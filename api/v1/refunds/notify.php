<?php
require_once __DIR__."/../../config/database.php";
require_once __DIR__."/../notifications/email.php";
require_once __DIR__."/../notifications/whatsapp.php";

$q = $pdo->query("
  SELECT r.*, u.email, u.mobile
  FROM refunds r
  JOIN bookings b ON b.id = r.booking_id
  JOIN users u ON u.id = b.user_id
  WHERE r.status IN ('SUCCESS','FAILED')
");

foreach($q as $r){

  $msg = $r['status']=='SUCCESS'
    ? "✅ Refund Successful\nAmount: {$r['refund_amount']} {$r['currency']}"
    : "⚠️ Refund Failed\nWe are retrying your refund.";

  // EMAIL
  sendBookingEmail(
    $r['email'],
    "Refund Update – MMTrips",
    nl2br($msg)
  );

  // WHATSAPP
  sendWhatsApp($r['mobile'], $msg);
}
