<?php
require_once "../../config/database.php";

$userId = 1; // from session / JWT

$stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$userId]);

echo json_encode($stmt->fetchAll());
