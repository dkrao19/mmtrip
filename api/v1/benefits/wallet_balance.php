<?php
session_start();
require_once __DIR__."/../../config/database.php";

$userId = $_SESSION['user']['id'] ?? 0;

$stmt = $pdo->prepare("
  SELECT balance, credit_limit
  FROM wallets WHERE user_id=?
");
$stmt->execute([$userId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
  'balance' => (float)($row['balance'] ?? 0),
  'credit_limit' => (float)($row['credit_limit'] ?? 0)
]);
