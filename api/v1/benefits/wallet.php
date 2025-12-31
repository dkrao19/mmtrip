<?php
require_once __DIR__.'/../../config/database.php';
session_start();

$uid=$_SESSION['user']['id'];
$amount=(float)$_POST['amount'];
$type=$_POST['type']; // credit / debit

$sql = $type=='credit'
  ? "UPDATE wallets SET balance=balance+? WHERE user_id=?"
  : "UPDATE wallets SET balance=balance-? WHERE user_id=?";

$pdo->prepare($sql)->execute([$amount,$uid]);
