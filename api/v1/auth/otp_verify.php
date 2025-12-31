<?php
session_start();
require "../../../config/database.php";

$otp=$_POST['otp'];

$stmt=$pdo->prepare("
SELECT u.* FROM user_otps o
JOIN users u ON u.id=o.user_id
WHERE o.otp=? AND o.expires_at>NOW()
");
$stmt->execute([$otp]);
$user=$stmt->fetch();

if($user){
  $_SESSION['user']=$user;
  echo json_encode(['success'=>true]);
}else{
  echo json_encode(['error'=>'Invalid OTP']);
}
