<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename=mmtrips_accounting.csv');

require_once "../config/database.php";

$q = $pdo->query("
  SELECT
    b.reference,
    b.total_amount,
    b.currency,
    p.payment_mode,
    p.gateway_ref,
    p.created_at
  FROM bookings b
  JOIN payments p ON p.booking_id=b.id
");

$out = fopen("php://output", "w");
fputcsv($out, ['Booking','Amount','Currency','Mode','Gateway Ref','Date']);

foreach($q as $r){
  fputcsv($out, $r);
}
fclose($out);
