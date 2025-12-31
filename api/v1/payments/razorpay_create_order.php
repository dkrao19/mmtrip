<?php
require_once __DIR__."/../../../config/api_keys.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if(!isset($data['amount'])){
  http_response_code(400);
  echo json_encode(['error'=>'Amount missing']);
  exit;
}

$amount = (int)$data['amount'] * 100; // paise

$ch = curl_init("https://api.razorpay.com/v1/orders");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_USERPWD => RAZORPAY_KEY_ID . ":" . RAZORPAY_KEY_SECRET,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
  CURLOPT_POSTFIELDS => json_encode([
    'amount' => $amount,
    'currency' => 'INR',
    'receipt' => 'mmtrips_' . time(),
    'payment_capture' => 1
  ])
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if($httpCode !== 200){
  echo json_encode(['error'=>'Razorpay order failed','raw'=>$response]);
  exit;
}

$order = json_decode($response, true);

/* ✅ VERY IMPORTANT: return key */
echo json_encode([
  'id' => $order['id'],
  'amount' => $order['amount'],
  'currency' => $order['currency'],
  'key' => RAZORPAY_KEY_ID
]);
