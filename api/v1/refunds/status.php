<?php
session_start();
require_once __DIR__."/../../config/database.php";

$bookingId = (int)($_GET['booking_id'] ?? 0);

$stmt = $pdo->prepare("
  SELECT status, refund_amount, retry_count
  FROM refunds WHERE booking_id=?
");
$stmt->execute([$bookingId]);

echo json_encode($stmt->fetchAll());
