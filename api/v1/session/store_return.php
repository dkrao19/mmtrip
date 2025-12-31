<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if(!$data){
  http_response_code(400);
  echo json_encode(['error'=>'Invalid data']);
  exit;
}

$_SESSION['return_offer'] = $data;

echo json_encode(['success'=>true]);
