<?php
session_start();
header('Content-Type: application/json');

$_SESSION['payment_success'] = true;
header("Location: /booking/ticketing.php");
exit;

// 1️⃣ Credit loyalty
$_POST['amount'] = $paidAmount;
require '/api/v1/benefits/loyalty_credit.php';

// 2️⃣ Wallet cashback (optional)
// require '/api/v1/benefits/wallet_credit.php';


$data=json_decode(file_get_contents('php://input'),true);

$_SESSION['payment']=[
  'gateway'=>$data['gateway'],
  'reference'=>json_encode($data['payload']),
  'time'=>date('Y-m-d H:i:s')
];

echo json_encode(['status'=>'ok']);
