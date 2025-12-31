<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../adapters/amadeus_token.php";

header('Content-Type: application/json');

if (empty($_SESSION['payment_success'])) {
  http_response_code(401);
  echo json_encode(['error'=>'PAYMENT_NOT_CONFIRMED']);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$offerId = $data['offer_id'] ?? null;
$passengers = $data['passengers'] ?? [];

if (!$offerId || empty($passengers)) {
  http_response_code(400);
  echo json_encode(['error'=>'INVALID_REQUEST']);
  exit;
}

/* ===============================
   REAL AMADEUS ORDER CREATE
=============================== */

try {

  $token = getAmadeusToken();

  // ---- Build Travelers ----
  $travelers = [];
  $i = 1;
  foreach ($passengers as $p) {
    $travelers[] = [
      "id" => (string)$i++,
      "dateOfBirth" => $p['dob'],
      "name" => [
        "firstName" => $p['first_name'],
        "lastName"  => $p['last_name']
      ],
      "gender" => $p['gender']
    ];
  }

  $payload = [
    "data" => [
      "type" => "flight-order",
      "flightOffers" => [[ "id" => $offerId ]],
      "travelers" => $travelers
    ]
  ];

  // ---- API CALL ----
  $url = ($_ENV['AMADEUS_ENV']==='production')
    ? "https://api.amadeus.com/v1/booking/flight-orders"
    : "https://test.api.amadeus.com/v1/booking/flight-orders";

  $ch = curl_init($url);
  curl_setopt_array($ch,[
    CURLOPT_POST=>true,
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_HTTPHEADER=>[
      "Authorization: Bearer $token",
      "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS=>json_encode($payload)
  ]);

  $res = curl_exec($ch);
  curl_close($ch);

  $order = json_decode($res,true);

  if (empty($order['data']['associatedRecords'][0]['reference'])) {
    throw new Exception($res);
  }

  $pnr = $order['data']['associatedRecords'][0]['reference'];
  $orderId = $order['data']['id'];

  /* ===============================
     STORE SESSION (CRITICAL)
  =============================== */
  $_SESSION['pnr'] = [
    'pnr'=>$pnr,
    'order_id'=>$orderId,
    'provider'=>'AMADEUS'
  ];

  /* ===============================
     INSERT INTO EXISTING bookings
  =============================== */
  $stmt = $pdo->prepare("
    INSERT INTO bookings
    (user_id, agent_id, service_type, supplier_net, selling_price, status, created_at)
    VALUES
    (:uid, :aid, 'FLIGHT', :net, :sell, 'CONFIRMED', NOW())
  ");

  $stmt->execute([
    ':uid'  => $_SESSION['user']['id'] ?? null,
    ':aid'  => $_SESSION['user']['agent_id'] ?? null,
    ':net'  => $_SESSION['supplier_net'] ?? 0,
    ':sell' => $_SESSION['payment_amount']
  ]);

  $bookingId = $pdo->lastInsertId();

  /* ===============================
     FLIGHT DETAILS
  =============================== */
  $stmt = $pdo->prepare("
    INSERT INTO booking_flights
    (booking_id, provider, pnr, order_id, raw_response)
    VALUES (?,?,?,?,?)
  ");
  $stmt->execute([
    $bookingId,'AMADEUS',$pnr,$orderId,json_encode($order)
  ]);

  /* ===============================
     PASSENGERS
  =============================== */
  foreach ($passengers as $p) {
    $stmt = $pdo->prepare("
      INSERT INTO booking_passengers
      (booking_id,type,first_name,last_name,gender,dob,passport_no,passport_expiry,nationality,seat,meal,baggage)
      VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->execute([
      $bookingId,$p['type'],$p['first_name'],$p['last_name'],$p['gender'],$p['dob'],
      $p['passport_no'],$p['passport_expiry'],$p['nationality'],
      $p['seat']??null,$p['meal']??null,$p['baggage']??null
    ]);
  }

  echo json_encode([
    'success'=>true,
    'pnr'=>$pnr
  ]);

} catch(Throwable $e){
  http_response_code(500);
  echo json_encode([
    'error'=>'ORDER_FAILED',
    'message'=>$e->getMessage()
  ]);
}
