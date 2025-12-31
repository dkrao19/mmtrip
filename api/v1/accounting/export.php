<?php
require_once __DIR__."/../../config/database.php";

$type = $_GET['type'] ?? 'CSV'; // CSV / JSON

$q = $pdo->query("
  SELECT 
    b.id AS booking_id,
    b.reference,
    b.total_amount,
    b.currency,
    p.payment_mode,
    p.gateway_ref,
    p.created_at
  FROM bookings b
  JOIN payments p ON p.booking_id = b.id
");

$rows = $q->fetchAll(PDO::FETCH_ASSOC);

if($type === 'JSON'){
  header('Content-Type: application/json');
  echo json_encode($rows);
  exit;
}

/* CSV EXPORT */
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=mmtrips_accounting.csv');

$out = fopen('php://output', 'w');
fputcsv($out, array_keys($rows[0]));
foreach($rows as $r) fputcsv($out, $r);
fclose($out);
