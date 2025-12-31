<?php
function ndcCall($xml){
  $ch = curl_init(NDC_ENDPOINT);
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
      "Content-Type: application/xml",
      "Authorization: Bearer ".NDC_API_KEY
    ],
    CURLOPT_POSTFIELDS => $xml
  ]);
  return curl_exec($ch);
}
