<?php
session_start();

if(!isset($_SESSION['selected_offer'], $_SESSION['passengers'])){
  header("Location:/");
  exit;
}

$outbound = $_SESSION['selected_offer'];
$return   = $_SESSION['return_offer'] ?? null;
$passengers = $_SESSION['passengers'];

$total = (float)$outbound['price'];
if($return){
  $total += (float)$return['price'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Review Booking – MMTrips</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
  font-family:Inter,sans-serif;
  background:#f4f7fb;
  margin:0;
}
.container{
  max-width:900px;
  margin:30px auto;
  padding:0 15px;
}
.card{
  background:#fff;
  padding:18px;
  border-radius:12px;
  margin-bottom:16px;
  box-shadow:0 4px 14px rgba(0,0,0,.08);
}
.row{
  display:flex;
  justify-content:space-between;
  margin:6px 0;
}
h3{margin-top:0}
.btn{
  width:100%;
  padding:14px;
  border:none;
  border-radius:10px;
  font-weight:600;
  font-size:15px;
  cursor:pointer;
}
.btn-razor{
  background:#ff7a00;
  color:#fff;
}
.btn-stripe{
  background:#6772e5;
  color:#fff;
  margin-top:10px;
}
.small{
  font-size:13px;
  color:#555;
}
.total{
  font-size:20px;
  font-weight:700;
}
</style>
</head>

<body>

<div class="container">

<!-- FLIGHT SUMMARY -->
<div class="card">
<h3>Flight Summary</h3>

<div class="row">
  <span><?= $outbound['origin'] ?> → <?= $outbound['destination'] ?></span>
  <span>₹<?= number_format($outbound['price']) ?></span>
</div>

<?php if($return): ?>
<div class="row">
  <span><?= $return['origin'] ?> → <?= $return['destination'] ?></span>
  <span>₹<?= number_format($return['price']) ?></span>
</div>
<?php endif; ?>

<hr>
<div class="row total">
  <span>Total</span>
  <span>₹<?= number_format($total) ?></span>
</div>
</div>

<!-- PASSENGERS -->
<div class="card">
<h3>Passengers</h3>
<?php foreach($passengers as $p): ?>
  <div class="small">
    <?= htmlspecialchars($p['first_name']." ".$p['last_name']) ?>
    (<?= htmlspecialchars($p['type']) ?>)
  </div>
<?php endforeach; ?>
</div>

<!-- PAYMENT -->
<div class="card">
<h3>Payment</h3>

<button class="btn btn-razor" onclick="payRazorpay()">
  Pay ₹<?= number_format($total) ?> with Razorpay
</button>

<button class="btn btn-stripe" onclick="payStripe()">
  Pay with Stripe
</button>

<div class="small" id="payMsg"></div>
</div>

</div>

<!-- Razorpay -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
const TOTAL_AMOUNT = <?= (int)$total ?>;

/* ===============================
   RAZORPAY PAYMENT
================================ */
function payRazorpay(){
  document.getElementById('payMsg').innerText = 'Opening Razorpay...';

  fetch('/api/v1/payments/razorpay_create_order.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({amount:TOTAL_AMOUNT})
  })
  .then(r => r.json())
  .then(order => {

    if(!order || !order.id){
      alert('Unable to create Razorpay order');
      return;
    }

    const options = {
      key: order.key,
      amount: order.amount,
      currency: "INR",
      name: "MMTrips",
      description: "Flight Booking",
      order_id: order.id,
      handler: function (response){
        finalizePayment('razorpay', response);
      },
      theme: {
        color: "#ff7a00"
      }
    };

    const rzp = new Razorpay(options);
    rzp.open();
  })
  .catch(err => {
    console.error(err);
    alert('Razorpay failed to load');
  });
}

/* ===============================
   STRIPE PAYMENT
================================ */
function payStripe(){
  document.getElementById('payMsg').innerText = 'Redirecting to Stripe...';

  fetch('/api/v1/payments/stripe_create_intent.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({amount:TOTAL_AMOUNT})
  })
  .then(r => r.json())
  .then(res => {
    if(res.checkout_url){
      window.location.href = res.checkout_url;
    }else{
      alert('Stripe session error');
    }
  })
  .catch(err => {
    console.error(err);
    alert('Stripe error');
  });
}

/* ===============================
   FINALIZE PAYMENT
================================ */
function finalizePayment(gateway, payload){
  fetch('/api/v1/payments/payment_success.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({
      gateway: gateway,
      payload: payload
    })
  })
  .then(() => {
    window.location.href = '/booking/success.php';
  });
}
</script>

</body>
</html>
