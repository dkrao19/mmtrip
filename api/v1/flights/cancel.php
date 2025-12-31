<?php
if (session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__."/../../../config/database.php";
require_once __DIR__."/../adapters/amadeus_token.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"),true);
$pnr = $data['pnr'] ?? null;

if(!$pnr){
  http_response_code(400);
  echo json_encode(['error'=>'PNR_REQUIRED']);
  exit;
}

try{
  $token = getAmadeusToken();

  // REAL CALL PLACEHOLDER
  // DELETE https://api.amadeus.com/v1/booking/flight-orders/{id}

  $stmt = $pdo->prepare("
    UPDATE bookings SET status='CANCELLED'
    WHERE id=:id
  ");
  $stmt->execute([':id'=>$data['booking_id']]);

  // CREATE REFUND ENTRY
  $stmt = $pdo->prepare("
    INSERT INTO refunds
    (booking_id,payment_mode,refund_type,refund_amount,currency,status,retry_count)
    VALUES (?,?,?,?,?,'PENDING',0)
  ");
  $stmt->execute([
    $data['booking_id'],
    $data['payment_mode'],
    'FULL',
    $data['refund_amount'],
    'INR'
  ]);

  echo json_encode(['success'=>true]);

}catch(Throwable $e){
  http_response_code(500);
  echo json_encode(['error'=>'CANCEL_FAILED']);
}
