<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__."/../../config/database.php";

$bookingId = (int)($_POST['booking_id'] ?? 0);
$refundAmount = (float)($_POST['refund_amount'] ?? 0);
$refundType = $_POST['refund_type'] ?? 'FULL';
$paymentMode = $_POST['payment_mode'] ?? 'STRIPE';
$currency = $_POST['currency'] ?? 'INR';

if(!$bookingId || $refundAmount <= 0){
  echo json_encode(['error'=>'INVALID_REQUEST']);
  exit;
}

$stmt = $pdo->prepare("
  INSERT INTO refunds
  (booking_id, payment_mode, refund_type, refund_amount, currency, status)
  VALUES (?, ?, ?, ?, ?, 'PENDING')
");

$stmt->execute([
  $bookingId,
  $paymentMode,
  $refundType,
  $refundAmount,
  $currency
]);

echo json_encode([
  'success' => true,
  'message' => 'Refund initiated'
]);
