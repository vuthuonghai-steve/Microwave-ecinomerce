<?php

return [
    'tmn_code' => env('VNPAY_TMN_CODE'),
    'hash_secret' => env('VNPAY_HASH_SECRET'),
    'base_url' => env('VNPAY_BASE_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'return_url' => env('VNPAY_RETURN_URL', rtrim(env('APP_URL', 'http://localhost'), '/').'/payment/vnpay/return'),
    'order_type' => env('VNPAY_ORDER_TYPE', 'billpayment'),
    'locale' => env('VNPAY_LOCALE', 'vn'),
];
