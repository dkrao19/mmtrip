<?php
function tboSearch($payload){
  $ch = curl_init("https://api.tektravels.com/BookingEngineService_Air/AirService.svc/rest/Search");
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_HTTPHEADER=>[
      "Content-Type: application/json",
      "Authorization: Basic ".base64_encode(TBO_USER.":".TBO_PASS)
    ],
    CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>json_encode($payload)
  ]);
  return json_decode(curl_exec($ch),true);
}
