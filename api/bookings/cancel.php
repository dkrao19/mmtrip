<?php
require_once "../../config/database.php";

$id = $_POST['booking_id'];

$pdo->prepare(
  "UPDATE bookings SET status='CANCELLED' WHERE id=?"
)->execute([$id]);

// trigger refund engine here

echo json_encode(["success"=>true]);
