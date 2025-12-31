<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function authenticate(){
  $headers = getallheaders();
  if(!isset($headers['Authorization'])){
    http_response_code(401); exit;
  }

  $token = str_replace("Bearer ","",$headers['Authorization']);
  return JWT::decode($token,new Key(JWT_SECRET,'HS256'));
}
