<?php
// =======================================
// MY BOOKINGS PAGE – MMTRIPS
// =======================================

// Mock user bookings (later from API)
$currency = "₹";
$bookings = [
  [
    'id'=>'MMT123456',
    'type'=>'Flight',
    'route'=>'DEL → DXB',
    'date'=>'20 Feb 2025',
    'amount'=>12249,
    'status'=>'CONFIRMED'
  ],
  [
    'id'=>'MMT123457',
    'type'=>'Hotel',
    'route'=>'Bangkok Hotel',
    'date'=>'10 Mar 2025',
    'amount'=>8999,
    'status'=>'REFUNDED'
  ],
  [
    'id'=>'MMT123458',
    'type'=>'Flight',
    'route'=>'SIN → DEL',
    'date'=>'28 Jan 2025',
    'amount'=>15499,
    'status'=>'CANCELLED'
  ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Bookings | MMTrips</title>
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
  max-width:1100px;
  margin:auto;
  padding:20px;
}
h1{margin-bottom:20px}

/* =============================
   BOOKING CARD
============================= */
.booking-card{
  background:#fff;
  border-radius:14px;
  padding:20px;
  box-shadow:0 8px 22px rgba(0,0,0,.1);
  margin-bottom:20px;
}
.booking-top{
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.booking-id{
  font-weight:600;
}
.status{
  padding:6px 12px;
  border-radius:20px;
  font-size:13px;
  font-weight:600;
}
.status.CONFIRMED{
  background:#dcfce7;
  color:#15803d;
}
.status.CANCELLED{
  background:#fee2e2;
  color:#b91c1c;
}
.status.REFUNDED{
  background:#e0f2fe;
  color:#0369a1;
}

/* =============================
   DETAILS
============================= */
.details{
  margin-top:15px;
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
  gap:10px;
  font-size:14px;
}

/* =============================
   ACTIONS
============================= */
.actions{
  margin-top:15px;
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}
.actions button{
  padding:8px 14px;
  border-radius:8px;
  border:1px solid #e5e7eb;
  background:#fff;
  cursor:pointer;
  font-weight:500;
}
.actions button.primary{
  background:#0a2d4d;
  color:#fff;
  border:none;
}
.actions button.danger{
  background:#fee2e2;
  color:#b91c1c;
  border:none;
}

/* =============================
   WHATSAPP
============================= */
.whatsapp{
  background:#dcfce7;
  border:1px dashed #22c55e;
  padding:15px;
  border-radius:12px;
  margin-top:30px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.whatsapp button{
  background:#22c55e;
  color:#fff;
  border:none;
  padding:10px 16px;
  border-radius:8px;
  font-weight:600;
}

/* =============================
   RESPONSIVE
============================= */
@media(max-width:768px){
  .booking-top{
    flex-direction:column;
    align-items:flex-start;
    gap:8px;
  }
}
</style>
</head>

<body>

<div class="container">

<h1>📄 My Bookings</h1>

<?php foreach($bookings as $b): ?>
<div class="booking-card">

  <div class="booking-top">
    <div class="booking-id">
      <?= $b['type'] ?> Booking · <?= $b['id'] ?>
    </div>
    <div class="status <?= $b['status'] ?>">
      <?= $b['status'] ?>
    </div>
  </div>

  <div class="details">
    <div><b>Route</b><br><?= $b['route'] ?></div>
    <div><b>Travel Date</b><br><?= $b['date'] ?></div>
    <div><b>Amount</b><br><?= $currency.number_format($b['amount']) ?></div>
  </div>

  <div class="actions">
    <button onclick="downloadTicket('<?= $b['id'] ?>')">🎫 Ticket</button>
    <button onclick="downloadInvoice('<?= $b['id'] ?>')">🧾 Invoice</button>

    <?php if($b['status']=='CONFIRMED'): ?>
      <button class="danger" onclick="cancelBooking('<?= $b['id'] ?>')">
        ❌ Cancel & Refund
      </button>
    <?php endif; ?>

    <?php if($b['status']=='REFUNDED'): ?>
      <button>💼 Wallet Refunded</button>
    <?php endif; ?>
  </div>

</div>
<?php endforeach; ?>

<!-- WHATSAPP SUPPORT -->
<div class="whatsapp">
  <div>
    <h3>Need Help?</h3>
    <p>Chat with MMTrips support on WhatsApp</p>
  </div>
  <button onclick="openWhatsApp()">WhatsApp Support</button>
</div>

</div>

<script>
function downloadTicket(id){
  alert("Downloading ticket for " + id);
  // window.location = "/api/ticket/download?id="+id;
}

function downloadInvoice(id){
  alert("Downloading invoice for " + id);
  // window.location = "/api/invoice/download?id="+id;
}

function cancelBooking(id){
  if(confirm("Are you sure you want to cancel this booking?")){
    alert("Cancellation requested for " + id);
    // API: /api/bookings/cancel
  }
}

function openWhatsApp(){
  window.open("https://wa.me/911234567890?text=Hello%20MMTrips%20Support");
}
</script>

</body>
</html>
