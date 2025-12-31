<?php
require_once __DIR__ . '/../../../config/api_keys.php';

function createRazorpayOrder($amount, $currency='INR') {
    $ch = curl_init("https://api.razorpay.com/v1/orders");
    curl_setopt_array($ch, [
        CURLOPT_USERPWD => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode([
            'amount' => $amount * 100,
            'currency' => $currency
        ])
    ]);
    return json_decode(curl_exec($ch), true);
}
