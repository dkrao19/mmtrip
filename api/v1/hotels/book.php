<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
if (session_status()===PHP_SESSION_NONE) session_start();

require_once __DIR__."/../../../config/database.php";

header('Content-Type: application/json');

if (empty($_SESSION['payment_success'])) {
  http_response_code(401);
  echo json_encode(['error'=>'PAYMENT_REQUIRED']);
  exit;
}

/*
Expected POST:
{
  hotel_id,
  provider: HOTELBEDS | EXPEDIA,
  checkin,
  checkout,
  guests,
  supplier_net,
  selling_price
}
*/

$data = json_decode(file_get_contents("php://input"), true);

try {

  // --- SIMULATED HOTEL CONFIRMATION ---
  $confirmation = strtoupper(substr(md5(time()),0,8));

  // --- STORE BOOKING ---
  $stmt = $pdo->prepare("
    INSERT INTO bookings
    (user_id, agent_id, service_type, supplier_net, selling_price, status, created_at)
    VALUES
    (:uid,:aid,'HOTEL',:net,:sell,'CONFIRMED',NOW())
  ");

  $stmt->execute([
    ':uid'=>$_SESSION['user']['id']??null,
    ':aid'=>$_SESSION['user']['agent_id']??null,
    ':net'=>$data['supplier_net'],
    ':sell'=>$data['selling_price']
  ]);

  $bookingId = $pdo->lastInsertId();

  // --- HOTEL DETAILS ---
  $stmt = $pdo->prepare("
    INSERT INTO booking_hotels
    (booking_id, provider, confirmation_no, hotel_id, checkin, checkout, raw_response)
    VALUES (?,?,?,?,?,?,?)
  ");

  $stmt->execute([
    $bookingId,
    $data['provider'],
    $confirmation,
    $data['hotel_id'],
    $data['checkin'],
    $data['checkout'],
    json_encode($data)
  ]);

  $_SESSION['pnr'] = ['pnr'=>$confirmation,'provider'=>$data['provider']];

  echo json_encode([
    'success'=>true,
    'confirmation'=>$confirmation
  ]);

} catch(Throwable $e){
  http_response_code(500);
  echo json_encode(['error'=>'HOTEL_BOOK_FAILED']);
}
