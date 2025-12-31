<?php
session_start();
require_once __DIR__."/../../config/database.php";

$userId = $_SESSION['user']['id'] ?? 0;
$amount = (float)($_POST['amount'] ?? 0);

if($amount <= 0){
  http_response_code(400);
  exit;
}

/* FETCH WALLET */
$stmt = $pdo->prepare("
  SELECT balance, credit_limit
  FROM wallets WHERE user_id=?
  FOR UPDATE
");
$stmt->execute([$userId]);
$w = $stmt->fetch(PDO::FETCH_ASSOC);

$totalAvailable = ($w['balance'] ?? 0) + ($w['credit_limit'] ?? 0);

if($amount > $totalAvailable){
  echo json_encode(['error'=>'INSUFFICIENT_WALLET']);
  exit;
}

/* DEDUCT */
$deductBalance = min($amount, $w['balance']);
$remaining = $amount - $deductBalance;

$newBalance = $w['balance'] - $deductBalance;
$newCredit  = $w['credit_limit'] - $remaining;

$upd = $pdo->prepare("
  UPDATE wallets
  SET balance=?, credit_limit=?
  WHERE user_id=?
");
$upd->execute([$newBalance, $newCredit, $userId]);

echo json_encode([
  'success'=>true,
  'wallet_used'=>$amount
]);
