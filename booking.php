<?php
// =======================================
// BOOKING REVIEW + PASSENGER PAGE – MMTRIPS
// =======================================

// Mock data (later from API/session)
$currency = "₹";
$flight = [
  'airline'=>'Emirates',
  'flight'=>'EK-517',
  'route'=>'DEL → DXB',
  'depart'=>'09:30',
  'arrive'=>'12:10',
  'base_fare'=>10500,
  'taxes'=>1999
];

$user = [
  'logged_in'=>true,
  'wallet_balance'=>1200,
  'loyalty_points'=>3200,
  'is_premium'=>true
];

// Payment gateways enabled (from admin)
$payments = [
  'STRIPE'=>true,
  'RAZORPAY'=>true,
  'WALLET'=>true
];

// Coupon suggestion
$suggestedCoupon = "WELCOME500";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Review & Pay | MMTrips</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
  margin:0;
  font-family:'Inter',sans-serif;
  background:#f5f7fb;
  color:#1f2937;
}
.container{
  max-width:1200px;
  margin:auto;
  padding:20px;
  display:grid;
  grid-template-columns:2fr 1fr;
  gap:20px;
}
h2{margin-top:0}

/* =============================
   CARD
============================= */
.card{
  background:#fff;
  padding:20px;
  border-radius:14px;
  box-shadow:0 8px 22px rgba(0,0,0,.1);
  margin-bottom:20px;
}

/* =============================
   PASSENGER FORM
============================= */
.form-row{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:15px;
}
input, select{
  padding:12px;
  border-radius:8px;
  border:1px solid #e5e7eb;
  width:100%;
}

/* =============================
   FARE
============================= */
.fare-line{
  display:flex;
  justify-content:space-between;
  margin:8px 0;
}
.total{
  font-size:20px;
  font-weight:700;
}

/* =============================
   TAGS / BOXES
============================= */
.tag{
  background:#ecfeff;
  color:#0369a1;
  padding:6px 10px;
  border-radius:6px;
  font-size:13px;
  display:inline-block;
  margin-bottom:8px;
}
.success{color:#15803d}
.link{color:#2563eb;cursor:pointer}

/* =============================
   BUTTON
============================= */
.btn{
  width:100%;
  padding:14px;
  border:none;
  border-radius:10px;
  background:#0a2d4d;
  color:#fff;
  font-size:16px;
  font-weight:600;
  cursor:pointer;
}

/* =============================
   RESPONSIVE
============================= */
@media(max-width:900px){
  .container{grid-template-columns:1fr}
}
</style>
</head>

<body>

<div class="container">

<!-- LEFT -->
<div>

  <!-- FLIGHT SUMMARY -->
  <div class="card">
    <h2>✈️ Flight Details</h2>
    <p><b><?= $flight['airline'] ?></b> <?= $flight['flight'] ?></p>
    <p><?= $flight['route'] ?> | <?= $flight['depart'] ?> → <?= $flight['arrive'] ?></p>
  </div>

  <!-- PASSENGER DETAILS -->
  <div class="card">
    <h2>👤 Passenger Details</h2>

    <div class="form-row">
      <input placeholder="First Name">
      <input placeholder="Last Name">
    </div><br>

    <div class="form-row">
      <select>
        <option>Male</option>
        <option>Female</option>
      </select>
      <input type="date">
    </div><br>

    <input placeholder="Email"><br><br>
    <input placeholder="Mobile Number">
  </div>

  <!-- UPSELL / BUNDLE -->
  <div class="card">
    <h2>🎁 Recommended for You</h2>

    <label>
      <input type="checkbox" checked>
      🛡️ Travel Insurance — <b>Save <?= $currency ?>250</b> with bundle
    </label><br><br>

    <label>
      <input type="checkbox">
      🧳 Extra Baggage (+<?= $currency ?>2,500)
    </label>
  </div>

</div>

<!-- RIGHT -->
<div>

  <!-- FARE SUMMARY -->
  <div class="card">
    <h2>💰 Fare Summary</h2>

    <div class="fare-line">
      <span>Base Fare</span>
      <span><?= $currency.$flight['base_fare'] ?></span>
    </div>

    <div class="fare-line">
      <span>Taxes & Fees</span>
      <span><?= $currency.$flight['taxes'] ?></span>
    </div>

    <div class="fare-line success">
      <span>Bundle Discount</span>
      <span>- <?= $currency ?>250</span>
    </div>

    <hr>

    <div class="fare-line total">
      <span>Total</span>
      <span><?= $currency.number_format($flight['base_fare']+$flight['taxes']-250) ?></span>
    </div>
  </div>

  <!-- COUPON -->
  <div class="card">
    <h2>🏷️ Coupon</h2>
    <div class="tag">Suggested: <?= $suggestedCoupon ?></div><br><br>
    <input placeholder="Enter coupon">
    <p class="link">Apply</p>
  </div>

  <!-- LOYALTY -->
  <div class="card">
    <h2>⭐ Loyalty Points</h2>
    <p>You have <b><?= $user['loyalty_points'] ?></b> points</p>
    <label>
      <input type="checkbox">
      Redeem 1,000 points (<?= $currency ?>100)
    </label>
  </div>

  <!-- WALLET -->
  <?php if($payments['WALLET']): ?>
  <div class="card">
    <h2>💼 Wallet</h2>
    <p>Balance: <?= $currency.$user['wallet_balance'] ?></p>
    <label>
      <input type="checkbox">
      Use wallet balance
    </label>
  </div>
  <?php endif; ?>

  <!-- PAYMENT -->
  <div class="card">
    <h2>💳 Payment Method</h2>

    <?php if($payments['RAZORPAY']): ?>
    <label><input type="radio" name="pay" checked> UPI / NetBanking</label><br>
    <?php endif; ?>

    <?php if($payments['STRIPE']): ?>
    <label><input type="radio" name="pay"> Credit / Debit Card</label><br>
    <?php endif; ?>
  </div>

  <!-- PAY -->
  <button class="btn">Proceed to Pay</button>

</div>

</div>

</body>
</html>
