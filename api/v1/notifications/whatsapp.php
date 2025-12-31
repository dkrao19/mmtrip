<?php
function sendWhatsApp($mobile,$message){

  $token = "WHATSAPP_API_TOKEN";
  $phoneId = "PHONE_NUMBER_ID";

  $payload = [
    "messaging_product"=>"whatsapp",
    "to"=>$mobile,
    "type"=>"text",
    "text"=>["body"=>$message]
  ];

  $ch = curl_init("https://graph.facebook.com/v18.0/$phoneId/messages");
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_HTTPHEADER=>[
      "Authorization: Bearer $token",
      "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS=>json_encode($payload)
  ]);

  curl_exec($ch);
  curl_close($ch);
}
