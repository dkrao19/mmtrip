<?php

require_once __DIR__."/../../config/api_keys.php";

function amadeusToken(){
  static $token = null;
  static $expiry = 0;

  if($token && time() < $expiry){
    return $token;
  }

  $ch = curl_init("https://test.api.amadeus.com/v1/security/oauth2/token");
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_HTTPHEADER=>['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_POSTFIELDS=>http_build_query([
      'grant_type'=>'client_credentials',
      'client_id'=>AMADEUS_CLIENT_ID,
      'client_secret'=>AMADEUS_CLIENT_SECRET
    ])
  ]);

  $res = json_decode(curl_exec($ch), true);
  curl_close($ch);

  $token = $res['access_token'];
  $expiry = time() + $res['expires_in'] - 60;

  return $token;
}

function amadeusHeaders(){
  return [
    'Authorization: Bearer '.amadeusToken(),
    'Content-Type: application/json'
  ];
}
