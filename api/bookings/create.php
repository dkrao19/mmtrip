<?php
require_once "../../config/database.php";

$data = json_decode(file_get_contents("php://input"),true);

$sql = "INSERT INTO bookings
(user_id,type,amount,status)
VALUES(?,?,?,?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
  $data['user_id'],
  'FLIGHT',
  $data['amount'],
  'PENDING'
]);

echo json_encode([
  'booking_id'=>$pdo->lastInsertId()
]);
