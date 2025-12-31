<?php
function sendSlack($webhook,$text){
  $payload=json_encode(['text'=>$text]);
  $ch=curl_init($webhook);
  curl_setopt_array($ch,[
    CURLOPT_POST=>1,
    CURLOPT_RETURNTRANSFER=>1,
    CURLOPT_HTTPHEADER=>['Content-Type: application/json'],
    CURLOPT_POSTFIELDS=>$payload
  ]);
  curl_exec($ch);
}

function sendEmail($to,$subject,$msg){
  @mail($to,$subject,$msg,"From: alerts@mmtrips.com");
}
