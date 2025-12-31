<?php
session_start();

if (empty($_SESSION['payment_success'])) {
    header("Location:/");
    exit;
}
if (empty($_SESSION['pnr'])) {
    $_SESSION['pnr'] = [
        'pnr' => 'TMP' . rand(100000,999999),
        'provider' => 'TEST'
    ];
}

header("Location:/booking/success.php");
exit;
?>
<!DOCTYPE html>
<html>
<head>
<title>Ticketing – MMTrips</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{text-align:center;font-family:Inter;background:#f5f7fb}
.card{background:#fff;padding:30px;margin:80px auto;max-width:420px;border-radius:12px}
</style>
</head>
<body>

<div class="card">
<h3>Issuing Tickets… ✈️</h3>
<p>Please do not refresh</p>
</div>

<script>
fetch('/api/v1/amadeus/order_create.php')
.then(r=>r.json())
.then(res=>{
  if(res.pnr){
    location.href='/booking/success.php';
  }else{
    document.body.innerHTML='<h3>Ticketing Failed. Support notified.</h3>';
  }
});
</script>

</body>
</html>
