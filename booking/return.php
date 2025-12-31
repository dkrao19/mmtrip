<?php
session_start();

if(!isset($_SESSION['selected_offer'])){
  header("Location:/");
  exit;
}

$outbound = $_SESSION['selected_offer'];
$returnDate = $_GET['return_date'] ?? null;

if(!$returnDate){
  header("Location:/");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Select Return Flight – MMTrips</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{font-family:Inter,sans-serif;background:#f5f7fb}
.container{max-width:1000px;margin:30px auto}
.card{background:#fff;padding:16px;border-radius:12px;margin-bottom:12px;
display:grid;grid-template-columns:60px 2fr 1fr;gap:14px;align-items:center}
.price{font-size:18px;font-weight:700}
.cheapest{color:green;font-size:12px}
.btn{background:#ff7a00;color:#fff;padding:8px 12px;border-radius:8px;border:none}
.summary{background:#fff;padding:16px;border-radius:12px;margin-bottom:20px}
</style>
</head>
<body>

<div class="container">

<div class="summary">
<h3>Outbound Selected</h3>
<b><?= $outbound['origin'] ?> → <?= $outbound['destination'] ?></b><br>
<?= $outbound['airline'] ?> <?= $outbound['flight_number'] ?><br>
Price: ₹<?= $outbound['price'] ?>
</div>

<h3>Select Return Flight</h3>
<div id="returnResults">Loading return flights…</div>

</div>

<script>
const outbound = <?= json_encode($outbound) ?>;
const returnDate = "<?= $returnDate ?>";
let cheapest = Infinity;

fetch(`/api/v1/flights/search.php?from=${outbound.destination}&to=${outbound.origin}&date=${returnDate}`)
.then(r=>r.json())
.then(list=>{
  const wrap=document.getElementById('returnResults');
  wrap.innerHTML='';
  list.forEach((f,i)=>{
    if(f.price < cheapest) cheapest = f.price;
  });

  list.forEach(f=>{
    const total = outbound.price + f.price;
    wrap.innerHTML += `
    <div class="card">
      <img src="https://pics.avs.io/200/200/${f.airline}.png">
      <div>
        <b>${f.origin} → ${f.destination}</b><br>
        ${f.airline} ${f.flight_number}
      </div>
      <div>
        <div class="price">₹${total}</div>
        ${f.price === cheapest ? '<div class="cheapest">Cheapest return</div>' : ''}
        <button class="btn" onclick='selectReturn(${JSON.stringify(f)})'>Select</button>
      </div>
    </div>`;
  });
});

function selectReturn(f){
  fetch('/api/v1/session/store_return.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify(f)
  }).then(()=>{
    location.href='/booking/passengers.php';
  });
}
</script>

</body>
</html>
