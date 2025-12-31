<?php
// =======================================
// PAYMENT PAGE – MMTRIPS
// =======================================

// Mock booking data (from session / DB)
$currency = "₹";
$booking = [
  'booking_id' => 'MMT123456',
  'total_amount' => 12249,
  'wallet_used' => 1200,
  'payable' => 11049
];

// Enabled gateways (from admin dashboard)
$gateways = [
  'RAZORPAY' => true,
  'STRIPE' => true,
  'WALLET_ONLY' => false
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment | MMTrips</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
  margin:0;
  font-family:'Inter',sans-serif;
  background:#f5f7fb;
  color:#1f2937;
}
.container{
  max-width:900px;
  margin:auto;
  padding:20px;
}
.card{
  background:#fff;
  border-radius:14px;
  padding:20px;
  box-shadow:0 8px 24px rgba(0,0,0,.1);
  margin-bottom:20px;
}
h1,h2{margin-top:0}

/* =============================
   SUMMARY
============================= */
.summary-line{
  display:flex;
  justify-content:space-between;
  margin:8px 0;
}
.total{
  font-size:22px;
  font-weight:700;
}

/* =============================
   PAYMENT OPTIONS
============================= */
.payment-option{
  border:1px solid #e5e7eb;
  padding:14px;
  border-radius:10px;
  margin-bottom:12px;
  cursor:pointer;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.payment-option.active{
  border-color:#0a2d4d;
  background:#f0f9ff;
}

/* =============================
   BUTTON
============================= */
.btn{
  width:100%;
  padding:16px;
  border:none;
  border-radius:12px;
  background:#0a2d4d;
  color:#fff;
  font-size:16px;
  font-weight:600;
  cursor:pointer;
}

/* =============================
   STATUS
============================= */
.status{
  text-align:center;
  padding:40px 20px;
}
.status.success{color:#15803d}
.status.fail{color:#b91c1c}

/* =============================
   SECURITY
============================= */
.secure{
  font-size:13px;
  color:#6b7280;
  margin-top:10px;
}
</style>
</head>

<body>

<div class="container">

<h1>💳 Complete Payment</h1>

<!-- BOOKING SUMMARY -->
<div class="card">
  <h2>Booking Summary</h2>

  <div class="summary-line">
    <span>Booking ID</span>
    <span><?= $booking['booking_id'] ?></span>
  </div>

  <div class="summary-line">
    <span>Total Amount</span>
    <span><?= $currency.number_format($booking['total_amount']) ?></span>
  </div>

  <?php if($booking['wallet_used']>0): ?>
  <div class="summary-line">
    <span>Wallet Used</span>
    <span>- <?= $currency.number_format($booking['wallet_used']) ?></span>
  </div>
  <?php endif; ?>

  <hr>

  <div class="summary-line total">
    <span>Payable Now</span>
    <span><?= $currency.number_format($booking['payable']) ?></span>
  </div>
</div>

<!-- PAYMENT METHODS -->
<div class="card">
  <h2>Select Payment Method</h2>

  <?php if($gateways['RAZORPAY']): ?>
  <div class="payment-option active" onclick="selectPay('RAZORPAY')" id="pay-razorpay">
    <span>UPI / NetBanking / Wallets</span>
    <strong>Razorpay</strong>
  </div>
  <?php endif; ?>

  <?php if($gateways['STRIPE']): ?>
  <div class="payment-option" onclick="selectPay('STRIPE')" id="pay-stripe">
    <span>Credit / Debit Card</span>
    <strong>Stripe</strong>
  </div>
  <?php endif; ?>

</div>

<!-- PAY -->
<button class="btn" onclick="makePayment()">Pay <?= $currency.number_format($booking['payable']) ?></button>

<div class="secure">
  🔒 Secure payment · PCI DSS compliant · Encrypted
</div>

<!-- STATUS (hidden initially) -->
<div id="paymentStatus" style="display:none"></div>

</div>

<script>
let selectedGateway = "RAZORPAY";

function selectPay(type){
  selectedGateway = type;
  document.querySelectorAll('.payment-option').forEach(p=>p.classList.remove('active'));
  document.getElementById("pay-"+type.toLowerCase()).classList.add('active');
}

function makePayment(){
  // Simulate payment flow
  document.body.scrollTop = document.documentElement.scrollTop = 0;

  document.getElementById("paymentStatus").style.display = "block";
  document.getElementById("paymentStatus").innerHTML = `
    <div class="card status">
      <h2>Processing Payment...</h2>
      <p>Please do not refresh</p>
    </div>
  `;

  setTimeout(()=>{
    paymentSuccess();
  },2000);
}

// SUCCESS
function paymentSuccess(){
  document.getElementById("paymentStatus").innerHTML = `
    <div class="card status success">
      <h2>✅ Payment Successful</h2>
      <p>Your booking is confirmed</p>
      <p><b>Booking ID:</b> <?= $booking['booking_id'] ?></p>
      <button class="btn" onclick="goToConfirmation()">View Booking</button>
    </div>
  `;
}

// FAILURE (for later real handling)
function paymentFail(){
  document.getElementById("paymentStatus").innerHTML = `
    <div class="card status fail">
      <h2>❌ Payment Failed</h2>
      <p>Something went wrong. Please try again.</p>
      <button class="btn" onclick="location.reload()">Retry</button>
    </div>
  `;
}

function goToConfirmation(){
  window.location.href = "my-bookings.php";
}
</script>

</body>
</html>
