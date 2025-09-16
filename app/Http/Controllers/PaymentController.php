<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.hash_secret');
        $inputData = $request->all();
        
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);

        // Sort data by key
        ksort($inputData);

        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            $orderCode = $inputData['vnp_TxnRef'];
            $order = Order::where('code', $orderCode)->first();

            if ($order) {
                if ($inputData['vnp_ResponseCode'] == '00') {
                    // Transaction successful
                    $order->payment_status = 'paid';
                    $order->save();
                    // Optionally update Payment model as well
                    $order->payment()->update(['status' => 'succeeded', 'txn_code' => $inputData['vnp_TransactionNo']]);

                    return redirect()->route('orders.show', $order->id)->with('status', 'Thanh toán đơn hàng thành công!');
                } else {
                    // Transaction failed
                    $order->payment_status = 'failed';
                    $order->save();
                    $order->payment()->update(['status' => 'failed']);

                    return redirect()->route('orders.show', $order->id)->withErrors('Thanh toán thất bại. Vui lòng thử lại.');
                }
            } else {
                Log::error('VNPay Return: Order not found', ['order_code' => $orderCode]);
                return redirect()->route('home')->withErrors('Không tìm thấy đơn hàng.');
            }
        } else {
            Log::error('VNPay Return: Invalid signature', ['data' => $inputData]);
            return redirect()->route('home')->withErrors('Chữ ký không hợp lệ.');
        }
    }
}
