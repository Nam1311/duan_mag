<?php

return [
    // ZaloPay Configuration
    'app_id' => env('ZALOPAY_APP_ID', '2553'),
    'key1' => env('ZALOPAY_KEY1', 'PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL'),
    'key2' => env('ZALOPAY_KEY2', 'kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz'),
    
    // Endpoints
    'sandbox' => [
        'create_order' => 'https://sandbox.zalopay.com.vn/v001/tpe/createorder',
        'get_status' => 'https://sandbox.zalopay.com.vn/v001/tpe/getstatusbyapptransid',
    ],
    
    'production' => [
        'create_order' => 'https://zalopay.com.vn/v001/tpe/createorder',
        'get_status' => 'https://zalopay.com.vn/v001/tpe/getstatusbyapptransid',
    ],
    
    // URLs
    'callback_url' => env('ZALOPAY_CALLBACK_URL', 'http://127.0.0.1:8080/payment/zalopay/callback'),
    'redirect_url' => env('ZALOPAY_REDIRECT_URL', 'http://127.0.0.1:8080/payment/zalopay/result'),
    
    // Environment (sandbox or production)
    'environment' => env('ZALOPAY_ENVIRONMENT', 'sandbox'),
];
