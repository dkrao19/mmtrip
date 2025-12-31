<?php
require "../../../config/api_keys.php";

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');
if(strlen($q) < 2){
  echo json_encode([]);
  exit;
}

/*
 Aviasales Places API
 Docs: https://travelpayouts.github.io/slate/
*/

$url = "https://autocomplete.travelpayouts.com/places2"
     . "?term=" . urlencode($q)
     . "&locale=en"
     . "&types[]=airport"
     . "&types[]=city";

$ch = curl_init($url);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 5
]);

$res = curl_exec($ch);
$data = json_decode($res, true);

$out = [];

foreach($data ?? [] as $p){
  $out[] = [
    'code'    => $p['code'],          // IATA
    'city'    => $p['name'],          // City
    'name'    => $p['airport_name'] ?? $p['name'],
    'country' => $p['country_name']
  ];
}

echo json_encode($out);
