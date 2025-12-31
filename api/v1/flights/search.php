<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

/*
  DO NOT load api_keys.php here
  Adapters will load config themselves
*/

require_once __DIR__ . '/adapters/amadeus.php';
require_once __DIR__ . '/normalizer.php';

try {

  if(empty($_GET['from']) || empty($_GET['to']) || empty($_GET['date'])){
    echo json_encode([]);
    exit;
  }

  $params = [
    'from' => $_GET['from'],
    'to'   => $_GET['to'],
    'date' => $_GET['date'],
    'return_date' => $_GET['return_date'] ?? ''
  ];

  $raw = amadeusSearch($params);

  if(isset($raw['errors'])){
    http_response_code(400);
    echo json_encode([
      'error' => 'AMADEUS_ERROR',
      'details' => $raw['errors']
    ]);
    exit;
  }

  $data = normalizeAmadeus($raw);

    /*
      ALWAYS return an array
      If single object, wrap it
    */
    if (!is_array($data) || (isset($data['id']))) {
        $data = [$data];
    }

  echo json_encode($data);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'error' => 'SERVER_ERROR',
    'message' => $e->getMessage()
  ]);
}
