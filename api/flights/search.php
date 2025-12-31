<?php
require_once "../../config/database.php";

$from = $_POST['from'] ?? '';
$to   = $_POST['to'] ?? '';
$date = $_POST['date'] ?? '';

if (!$from || !$to || !$date) {
  echo json_encode([]);
  exit;
}

/*
  Later:
  1. Call Amadeus / TBO / NDC
  2. Normalize results
*/

// TEMP: DB sample
$sql = "SELECT * FROM flight_cache
        WHERE origin=? AND destination=? AND travel_date=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$from,$to,$date]);

echo json_encode($stmt->fetchAll());
