<?php
session_start();
require_once __DIR__."/../../config/database.php";

if(!isset($_SESSION['user']['id'])){
  http_response_code(401);
  exit;
}

$userId = (int)$_SESSION['user']['id'];
$amount = (float)($_POST['amount'] ?? 0);

if($amount <= 0){
  http_response_code(400);
  exit;
}

/* BUSINESS RULE */
$points = floor($amount / 100); // ₹100 = 1 point

$stmt = $pdo->prepare("
  INSERT INTO loyalty_points (user_id, points)
  VALUES (?, ?)
  ON DUPLICATE KEY UPDATE
    points = points + VALUES(points)
");

$stmt->execute([$userId, $points]);

echo json_encode([
  'success' => true,
  'points_credited' => $points
]);
