<?php
require_once __DIR__.'/../../../config/api_keys.php';

function hotelbedsHeaders(){
  return [
    'Api-key: '.HOTELBEDS_API_KEY,
    'X-Signature: '.hash('sha256', HOTELBEDS_API_KEY.HOTELBEDS_SECRET.time()),
    'Content-Type: application/json'
  ];
}

function hotelSearch($city,$ci,$co,$rooms){
  $payload = [
    "stay"=>["checkIn"=>$ci,"checkOut"=>$co],
    "occupancies"=>[["rooms"=>$rooms,"adults"=>2]],
    "destination"=>["code"=>$city]
  ];

  $ch = curl_init("https://api.test.hotelbeds.com/hotel-api/1.0/hotels");
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_HTTPHEADER=>hotelbedsHeaders(),
    CURLOPT_POSTFIELDS=>json_encode($payload)
  ]);

  $res = curl_exec($ch);
  curl_close($ch);

  return normalizeHotelbeds($res);
}

function normalizeHotelbeds($json){
  $data = json_decode($json,true);
  $out = [];
  foreach($data['hotels']['hotels'] ?? [] as $h){
    $out[] = [
      'hotel_id'=>$h['code'],
      'name'=>$h['name'],
      'rating'=>$h['categoryCode'],
      'price'=>$h['minRate'],
      'currency'=>$h['currency'],
      'refundable'=>true
    ];
  }
  return json_encode($out);
}
