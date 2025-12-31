<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . "/../../config/database.php";

$code   = trim($_GET['code'] ?? '');
$amount = (float)($_GET['amount'] ?? 0);

if(!$code || $amount <= 0){
  echo json_encode(['error'=>'INVALID_REQUEST']);
  exit;
}

/* FETCH COUPON */
$stmt = $pdo->prepare("
  SELECT * FROM coupons
  WHERE code = ?
    AND is_active = 1
    AND valid_from <= CURDATE()
    AND valid_to >= CURDATE()
  LIMIT 1
");
$stmt->execute([$code]);
$coupon = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$coupon){
  echo json_encode(['error'=>'INVALID_COUPON']);
  exit;
}

/* MIN AMOUNT CHECK */
if($amount < (float)$coupon['min_amount']){
  echo json_encode([
    'error'=>'MIN_AMOUNT_NOT_MET',
    'min_amount'=>$coupon['min_amount']
  ]);
  exit;
}

/* CALCULATE DISCOUNT */
if($coupon['discount_type'] === 'FLAT'){
  $discount = (float)$coupon['discount_value'];
}else{
  $discount = ($amount * (float)$coupon['discount_value']) / 100;
}

/* MAX DISCOUNT CAP */
if(!empty($coupon['max_discount']) && $discount > $coupon['max_discount']){
  $discount = (float)$coupon['max_discount'];
}

/* SAFETY */
if($discount > $amount){
  $discount = $amount;
}

/* OPTIONAL CONDITIONS (JSON – FUTURE READY) */
if(!empty($coupon['conditions'])){
  $conditions = json_decode($coupon['conditions'], true);

  // Example: user_role restriction
  if(isset($conditions['roles']) && isset($_SESSION['user'])){
    if(!in_array($_SESSION['user']['role_id'], $conditions['roles'])){
      echo json_encode(['error'=>'NOT_ELIGIBLE']);
      exit;
    }
  }
}

/* SUCCESS RESPONSE */
echo json_encode([
  'success'        => true,
  'code'           => $coupon['code'],
  'discount'       => round($discount,2),
  'payable_amount' => round($amount - $discount,2),
  'type'           => $coupon['discount_type']
]);
