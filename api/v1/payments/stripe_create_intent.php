<?php
require_once __DIR__."/../../../vendor/autoload.php";
require_once __DIR__."/../../../config/api_keys.php";

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$data=json_decode(file_get_contents('php://input'),true);

$session = \Stripe\Checkout\Session::create([
  'mode'=>'payment',
  'success_url'=>'https://mmtrips.com/booking/success.php',
  'cancel_url'=>'https://mmtrips.com/booking/review.php',
  'line_items'=>[[
    'price_data'=>[
      'currency'=>'inr',
      'product_data'=>['name'=>'MMTrips Booking'],
      'unit_amount'=>$data['amount']*100
    ],
    'quantity'=>1
  ]]
]);

echo json_encode(['checkout_url'=>$session->url]);
