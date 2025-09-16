<?php

namespace App\Services;

use App\Models\Order;

class VNPayService
{
    /**
     * Generate the VNPAY payment URL.
     *
     * @param Order $order
     * @param string|null $ipAddress
     * @return string
     */
    public function generatePaymentUrl(Order $order, ?string $ipAddress): string
    {
        $vnp_TmnCode = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_Url = config('vnpay.base_url');
        $vnp_Returnurl = route('payment.vnpay.return'); // We will create this route later

        $vnp_TxnRef = $order->code; // Order code
        $vnp_OrderInfo = "Thanh toan don hang {$order->code}";
        $vnp_OrderType = config('vnpay.order_type');
        $vnp_Amount = $order->grand_total * 100; // Amount in VND * 100
        $vnp_Locale = config('vnpay.locale');
        $vnp_IpAddr = $ipAddress ?? '127.0.0.1';

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now()->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        // Sort data by key
        ksort($inputData);
        
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return $vnp_Url;
    }
}
