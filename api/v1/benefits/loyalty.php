<?php
require_once __DIR__.'/../../config/database.php';
session_start();

$uid=$_SESSION['user']['id'];
$amount=$_POST['amount'];

$points = floor($amount / 100);
$pdo->prepare("INSERT INTO loyalty(user_id,points)
VALUES(?,?) ON DUPLICATE KEY UPDATE points=points+?")
->execute([$uid,$points,$points]);
