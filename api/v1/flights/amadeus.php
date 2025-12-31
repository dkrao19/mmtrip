<?php
function amadeusSearch($from,$to,$date,$adults){
  $token = getAmadeusToken();

  $url = "https://test.api.amadeus.com/v2/shopping/flight-offers?".
         http_build_query([
           'originLocationCode'=>$from,
           'destinationLocationCode'=>$to,
           'departureDate'=>$date,
           'adults'=>$adults,
           'currencyCode'=>'INR'
         ]);

  $ch = curl_init($url);
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_HTTPHEADER=>[
      "Authorization: Bearer $token"
    ]
  ]);

  return json_decode(curl_exec($ch),true);
}
