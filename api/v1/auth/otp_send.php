<?php
require "../../../config/database.php";

$mobile=$_POST['mobile'];
$otp=rand(100000,999999);

$stmt=$pdo->prepare("SELECT id FROM users WHERE mobile=?");
$stmt->execute([$mobile]);
$user=$stmt->fetch();

if(!$user){ echo json_encode(['error'=>'User not found']); exit; }

$pdo->prepare("INSERT INTO user_otps (user_id,otp,expires_at)
VALUES (?,?,DATE_ADD(NOW(),INTERVAL 5 MINUTE))")
->execute([$user['id'],$otp]);

// send OTP via WhatsApp/SMS here
echo json_encode(['success'=>true]);
