<?php
session_start();
require_once __DIR__."/../../config/database.php";

$userId = $_SESSION['user']['id'] ?? 0;
$amount = (float)($_POST['amount'] ?? 0);

$stmt = $pdo->prepare("
  UPDATE wallets
  SET balance = balance + ?
  WHERE user_id = ?
");
$stmt->execute([$amount, $userId]);

echo json_encode(['success'=>true]);
