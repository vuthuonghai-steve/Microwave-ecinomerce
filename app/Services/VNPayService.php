<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Arr;
use RuntimeException;

class VNPayService
{
    public function generatePaymentUrl(Order $order, PaymentTransaction $transaction, string $clientIp, array $customer = [], array $card = []): array
    {
        $config = $this->config();
        foreach (['tmn_code', 'hash_secret', 'base_url', 'return_url'] as $key) {
            if (empty($config[$key])) {
                throw new RuntimeException("Thieu cau hinh VNPay: {$key}");
            }
        }

        $params = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => $config['tmn_code'],
            'vnp_Amount' => (int) round($transaction->amount * 100),
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $clientIp ?: request()->ip(),
            'vnp_Locale' => $config['locale'] ?? 'vn',
            'vnp_OrderInfo' => 'Thanh toan don hang '.$order->code,
            'vnp_OrderType' => $config['order_type'] ?? 'billpayment',
            'vnp_ReturnUrl' => $config['return_url'],
            'vnp_TxnRef' => $transaction->txn_ref,
        ];

        if (!empty($customer['customer_name'])) {
            $params['vnp_Bill_FullName'] = $customer['customer_name'];
        }
        if (!empty($customer['customer_email'])) {
            $params['vnp_Bill_Email'] = $customer['customer_email'];
        }
        if (!empty($customer['customer_phone'])) {
            $params['vnp_Bill_Mobile'] = $customer['customer_phone'];
        }

        if (!empty($card['number_masked'])) {
            $params['vnp_CardType'] = 'ATM';
            $params['vnp_Bill_CardNumber'] = $card['number_masked'];
        }
        if (!empty($card['expiry'])) {
            $params['vnp_Bill_ExpireDate'] = $card['expiry'];
        }

        ksort($params);
        $hashData = $this->buildHashData($params);
        $secureHash = $this->sign($hashData, $config['hash_secret']);
        $query = $this->buildQueryString($params);

        $url = rtrim($config['base_url'], '?').'?'.$query.'&vnp_SecureHash='.$secureHash;

        return [$url, $params, $secureHash];
    }

    public function verifySignature(array $payload): bool
    {
        $config = $this->config();
        $secureHash = Arr::get($payload, 'vnp_SecureHash');
        if (!$secureHash || empty($config['hash_secret'])) {
            return false;
        }

        $data = Arr::except($payload, ['vnp_SecureHash', 'vnp_SecureHashType']);
        ksort($data);
        $hashData = $this->buildHashData($data);
        $expected = $this->sign($hashData, $config['hash_secret']);
        return hash_equals($expected, $secureHash);
    }

    public function normalizedAmount(string $vnpAmount): float
    {
        if ($vnpAmount === '') {
            return 0.0;
        }
        return (float) (((int) $vnpAmount) / 100);
    }

    public function response(string $code, string $message): array
    {
        return [
            'RspCode' => $code,
            'Message' => $message,
        ];
    }

    protected function buildHashData(array $params): string
    {
        return collect($params)
            ->map(fn($value, $key) => $key.'='.rawurlencode((string) $value))
            ->implode('&');
    }

    protected function buildQueryString(array $params): string
    {
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    protected function sign(string $hashData, string $secret): string
    {
        return hash_hmac('sha512', $hashData, $secret);
    }

    protected function config(): array
    {
        return config('vnpay', []);
    }
}
