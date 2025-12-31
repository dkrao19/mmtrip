<?php
session_start();
require_once __DIR__."/../../config/database.php";

$userId = $_SESSION['user']['id'] ?? 0;

$stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id=?");
$stmt->execute([$userId]);

echo json_encode([
  'points' => (int)($stmt->fetchColumn() ?? 0)
]);
