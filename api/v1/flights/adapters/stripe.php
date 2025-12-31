<?php
require_once __DIR__ . '/../../../config/api_keys.php';

function createStripeIntent($amount, $currency='usd') {
    $ch = curl_init("https://api.stripe.com/v1/payment_intents");
    curl_setopt_array($ch, [
        CURLOPT_USERPWD => STRIPE_SECRET_KEY . ':',
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'amount' => $amount * 100,
            'currency' => $currency
        ])
    ]);
    return json_decode(curl_exec($ch), true);
}
