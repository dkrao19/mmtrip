<?php
// =======================================
// FLIGHT SEARCH RESULT PAGE – MMTRIPS
// =======================================

// Sample feature toggles (later from DB)
$features = [
  'PRICE_PREDICTION' => true,
  'PRICE_ALERT' => true,
  'AI_COUPON' => true,
  'UPSELL' => true,
];

// Currency
$currency = "₹";

// Mock search results (replace with API response)
$flights = [
  [
    'id'=>1,
    'airline'=>'Emirates',
    'flight'=>'EK-517',
    'from'=>'DEL',
    'to'=>'DXB',
    'depart'=>'09:30',
    'arrive'=>'12:10',
    'price'=>12499,
    'prediction'=>'UP',
    'prediction_value'=>1200
  ],
  [
    'id'=>2,
    'airline'=>'IndiGo',
    'flight'=>'6E-1401',
    'from'=>'DEL',
    'to'=>'DXB',
    'depart'=>'13:45',
    'arrive'=>'16:30',
    'price'=>10999,
    'prediction'=>'DOWN',
    'prediction_value'=>900
  ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Flight Results | MMTrips</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* =============================
   GLOBAL
============================= */
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
}
h1{margin-bottom:10px}

/* =============================
   FILTER BAR
============================= */
.filter-bar{
  background:#fff;
  padding:15px;
  border-radius:10px;
  box-shadow:0 6px 18px rgba(0,0,0,.08);
  display:flex;
  gap:15px;
  margin-bottom:20px;
}
.filter-bar select{
  padding:8px;
  border-radius:6px;
  border:1px solid #e5e7eb;
}

/* =============================
   PRICE ALERT
============================= */
.price-alert{
  background:#ecfeff;
  border:1px dashed #06b6d4;
  padding:12px;
  border-radius:10px;
  margin-bottom:20px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}

/* =============================
   COUPON SUGGESTION
============================= */
.coupon-suggest{
  background:#f0fdf4;
  border:1px solid #22c55e;
  padding:12px;
  border-radius:10px;
  margin-bottom:20px;
}

/* =============================
   FLIGHT CARD
============================= */
.flight-card{
  background:#fff;
  padding:20px;
  border-radius:14px;
  box-shadow:0 8px 22px rgba(0,0,0,.1);
  margin-bottom:20px;
  display:grid;
  grid-template-columns:2fr 1fr 1fr;
  gap:20px;
}
.flight-left h3{
  margin:0;
}
.flight-time{
  font-size:14px;
  color:#6b7280;
}

/* =============================
   PRICE
============================= */
.price{
  font-size:22px;
  font-weight:700;
}
.book-btn{
  background:#0a2d4d;
  color:#fff;
  border:none;
  padding:12px;
  border-radius:8px;
  font-weight:600;
  cursor:pointer;
}

/* =============================
   PRICE PREDICTION
============================= */
.prediction{
  margin-top:8px;
  padding:8px;
  font-size:13px;
  border-radius:6px;
}
.prediction.up{
  background:#fee2e2;
  color:#b91c1c;
}
.prediction.down{
  background:#dcfce7;
  color:#15803d;
}

/* =============================
   UPSELL
============================= */
.upsell{
  grid-column:1/4;
  background:#f8fafc;
  border-radius:10px;
  padding:12px;
  display:flex;
  gap:15px;
}
.upsell-card{
  background:#fff;
  border-radius:8px;
  padding:10px;
  box-shadow:0 4px 12px rgba(0,0,0,.08);
  font-size:13px;
}
.upsell-card button{
  margin-top:6px;
  padding:6px 10px;
  border:none;
  border-radius:6px;
  background:#0a2d4d;
  color:#fff;
  font-size:12px;
}

/* =============================
   RESPONSIVE
============================= */
@media(max-width:900px){
  .flight-card{
    grid-template-columns:1fr;
  }
  .upsell{
    flex-direction:column;
  }
}
</style>
</head>

<body>

<div class="container">
<h1>Flights from DEL to DXB</h1>

<!-- FILTER BAR -->
<div class="filter-bar">
  <select><option>Price</option></select>
  <select><option>Departure</option></select>
  <select><option>Airline</option></select>
</div>

<?php if($features['PRICE_ALERT']): ?>
<!-- PRICE ALERT -->
<div class="price-alert">
  <span>🔔 Get notified when prices drop</span>
  <button onclick="subscribeAlert()">Set Price Alert</button>
</div>
<?php endif; ?>

<?php if($features['AI_COUPON']): ?>
<!-- COUPON -->
<div class="coupon-suggest">
  💡 Best coupon for you: <b>WELCOME500</b> — Save <?= $currency ?>500
</div>
<?php endif; ?>

<!-- FLIGHT LIST --
