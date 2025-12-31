<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../api/v1/notifications/email.php";
require_once __DIR__ . "/../api/v1/notifications/whatsapp.php";

/* ============================
   SESSION RECOVERY (CRITICAL)
============================ */

// Recover PNR from URL if session lost (gateway redirect issue)
if (!empty($_GET['pnr']) && empty($_SESSION['pnr'])) {
    $_SESSION['pnr'] = [
        'pnr' => preg_replace('/[^A-Z0-9]/', '', $_GET['pnr'])
    ];
}

// Allow page if EITHER payment OR PNR exists
if (empty($_SESSION['pnr']) && empty($_SESSION['payment_success'])) {
    header("Location: /");
    exit;
}

/* SAFE USER FALLBACK */
$user = $_SESSION['user'] ?? [
    'name'   => 'Guest',
    'email'  => '',
    'mobile' => ''
];

$pnr = $_SESSION['pnr'] ?? ['pnr' => 'PENDING'];

/* ============================
   GENERATE INVOICE (ONCE)
============================ */
if (empty($_SESSION['invoice'])) {
    try {
        require_once __DIR__ . "/../api/v1/invoice/generate.php";
        // generate.php should set $_SESSION['invoice']
    } catch (Throwable $e) {
        error_log("Invoice error: " . $e->getMessage());
    }
}

$invoice = $_SESSION['invoice'] ?? null;

/* ============================
   PREVENT DUPLICATE NOTIFICATIONS
============================ */
if (empty($_SESSION['notifications_sent'])) {

    /* EMAIL */
    try {
        if (!empty($user['email']) && $invoice) {
            sendBookingEmail(
                $user['email'],
                "Your Booking is Confirmed – MMTrips",
                "
                <b>Your booking is confirmed</b><br><br>
                PNR: <b>{$pnr['pnr']}</b><br>
                Thank you for booking with MMTrips.<br>
                <br>
                <a href='https://{$_SERVER['HTTP_HOST']}/assets/invoices/{$invoice}'>
                  Download Invoice
                </a>
                ",
                __DIR__ . "/../assets/invoices/" . $invoice
            );
        }
    } catch (Throwable $e) {
        error_log("Email error: " . $e->getMessage());
    }

    /* WHATSAPP */
    try {
        if (!empty($user['mobile'])) {
            sendWhatsApp(
                $user['mobile'],
                "✈️ Booking Confirmed!\nPNR: {$pnr['pnr']}\nInvoice sent to your email."
            );
        }
    } catch (Throwable $e) {
        error_log("WhatsApp error: " . $e->getMessage());
    }

    $_SESSION['notifications_sent'] = true;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Booking Confirmed</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{
  text-align:center;
  font-family:Inter, Arial, sans-serif;
  background:#f4f7fb;
}
.card{
  background:#fff;
  margin:80px auto;
  padding:30px;
  max-width:420px;
  border-radius:14px;
  box-shadow:0 10px 25px rgba(0,0,0,.1);
}
.btn{
  display:inline-block;
  margin-top:15px;
  padding:10px 18px;
  background:#0a2d4d;
  color:#fff;
  border-radius:8px;
  text-decoration:none;
}
.small{
  font-size:13px;
  color:#6b7280;
}
</style>
</head>
<body>

<div class="card">
  <h2>🎉 Booking Confirmed</h2>

  <p>
    PNR: <b><?= htmlspecialchars($pnr['pnr']) ?></b>
  </p>

  <?php if ($invoice): ?>
    <p>Invoice generated successfully</p>
    <a class="btn" href="/assets/invoices/<?= htmlspecialchars($invoice) ?>" target="_blank">
      Download Invoice
    </a>
  <?php else: ?>
    <p class="small">Invoice will be emailed shortly</p>
  <?php endif; ?>

  <br><br>
  <a href="/" class="small">Back to Home</a>
</div>

</body>
</html>
