<?php
header('Content-Type: application/json');
require_once __DIR__.'/adapters/'.strtolower(HOTEL_PROVIDER).'.php';

$city = $_GET['city'] ?? '';
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$rooms = $_GET['rooms'] ?? 1;

if(!$city || !$checkin || !$checkout){
  echo json_encode([]);
  exit;
}

echo hotelSearch($city, $checkin, $checkout, $rooms);
