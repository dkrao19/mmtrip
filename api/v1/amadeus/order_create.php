<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__."/helpers.php";

if(!isset($_SESSION['selected_offer'], $_SESSION['passengers'])){
  http_response_code(400);
  echo json_encode(['error'=>'Missing booking data']);
  exit;
}

$offer = $_SESSION['selected_offer'];
$passengers = $_SESSION['passengers'];

$travelers = [];
foreach($passengers as $i=>$p){
  $travelers[] = [
    'id' => (string)($i+1),
    'dateOfBirth' => $p['dob'],
    'name' => [
      'firstName' => $p['first_name'],
      'lastName'  => $p['last_name']
    ],
    'gender' => strtoupper($p['gender'][0]),
    'contact' => [
      'emailAddress' => $_SESSION['user']['email'] ?? 'support@mmtrips.com'
    ]
  ];
}

$payload = [
  'data'=>[
    'type'=>'flight-order',
    'flightOffers'=>[$offer['raw']],   // 🔑 IMPORTANT
    'travelers'=>$travelers
  ]
];

$ch = curl_init("https://test.api.amadeus.com/v1/booking/flight-orders");
curl_setopt_array($ch,[
  CURLOPT_RETURNTRANSFER=>true,
  CURLOPT_POST=>true,
  CURLOPT_HTTPHEADER=>amadeusHeaders(),
  CURLOPT_POSTFIELDS=>json_encode($payload)
]);

$response = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if($http !== 201){
  echo json_encode(['error'=>'ORDER_CREATE_FAILED','raw'=>$response]);
  exit;
}

$order = json_decode($response, true);

// Store PNR
$_SESSION['pnr'] = [
  'order_id'=>$order['data']['id'],
  'pnr'=>$order['data']['associatedRecords'][0]['reference']
];

echo json_encode($_SESSION['pnr']);
