<?php
function hotelbedsSearch($payload){
  $apiKey = HOTELBEDS_KEY;
  $secret = HOTELBEDS_SECRET;
  $signature = hash('sha256',$apiKey.$secret.time());

  $ch = curl_init("https://api.test.hotelbeds.com/hotel-api/1.0/hotels");
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_HTTPHEADER=>[
      "Api-key:$apiKey",
      "X-Signature:$signature",
      "Content-Type:application/json"
    ],
    CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>json_encode($payload)
  ]);
  return json_decode(curl_exec($ch),true);
}
