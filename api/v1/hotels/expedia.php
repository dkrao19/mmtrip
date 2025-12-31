<?php
function expediaSearch($params){
  $ch = curl_init("https://api.ean.com/v3/properties/availability");
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_HTTPHEADER=>[
      "Authorization: Bearer ".EXPEDIA_TOKEN
    ]
  ]);
  return json_decode(curl_exec($ch),true);
}
