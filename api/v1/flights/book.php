<?php
require "adapters/amadeus.php";

$offerId = $_POST['offer_id'];
$pax = $_POST['passengers'];

$payload = [
  "data" => [
    "type" => "flight-order",
    "flightOffers" => [ $_POST['offer_raw'] ],
    "travelers" => $pax
  ]
];

$token = getAmadeusToken();

$ch = curl_init("https://api.amadeus.com/v1/booking/flight-orders");
curl_setopt_array($ch, [
  CURLOPT_POST=>1,
  CURLOPT_RETURNTRANSFER=>1,
  CURLOPT_HTTPHEADER=>[
    "Authorization: Bearer $token",
    "Content-Type: application/json"
  ],
  CURLOPT_POSTFIELDS=>json_encode($payload)
]);

echo curl_exec($ch);
