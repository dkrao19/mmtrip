<?php
require_once __DIR__.'/../../../config/api_keys.php';

function hotelSearch($city,$ci,$co,$rooms){
  $url="https://test.ean.com/v3/properties/availability".
       "?city=".$city.
       "&check_in=".$ci.
       "&check_out=".$co.
       "&rooms=".$rooms;

  $ch = curl_init($url);
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_USERPWD=>EXPEDIA_API_KEY.":".EXPEDIA_SECRET
  ]);

  $res = curl_exec($ch);
  curl_close($ch);

  return normalizeExpedia($res);
}

function normalizeExpedia($json){
  $data = json_decode($json,true);
  $out=[];
  foreach($data['properties'] ?? [] as $h){
    $out[]=[
      'hotel_id'=>$h['id'],
      'name'=>$h['name'],
      'rating'=>$h['rating'],
      'price'=>$h['price']['total'],
      'currency'=>$h['price']['currency'],
      'refundable'=>$h['refundability']
    ];
  }
  return json_encode($out);
}
