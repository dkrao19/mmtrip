<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__."/../../config/database.php";
require_once __DIR__."/search.php"; // reuse your existing search logic

if(!isset($_GET['from'], $_GET['to'], $_GET['date'])){
  echo json_encode([]);
  exit;
}

/*
 Return flight is opposite direction
*/
$_GET['from'] = $_GET['to'];
$_GET['to']   = $_GET['from_original'] ?? $_GET['from'];
$_GET['date'] = $_GET['date'];

include __DIR__."/search.php";
