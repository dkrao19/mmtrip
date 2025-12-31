<?php
require_once "../config/database.php";
$rows = $pdo->query("
  SELECT b.*,u.name 
  FROM bookings b
  LEFT JOIN users u ON u.id=b.user_id
  ORDER BY b.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin – Bookings</title>
<style>
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border-bottom:1px solid #ddd}
.btn{padding:6px 10px;border-radius:6px;background:#0a2d4d;color:#fff}
.red{background:#dc2626}
</style>
</head>
<body>

<h2>All Bookings</h2>

<table>
<tr>
<th>ID</th><th>User</th><th>Type</th><th>Price</th><th>Status</th><th>Action</th>
</tr>
<?php foreach($rows as $r): ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= htmlspecialchars($r['name']) ?></td>
<td><?= $r['service_type'] ?></td>
<td><?= $r['selling_price'] ?></td>
<td><?= $r['status'] ?></td>
<td>
<?php if($r['status']=='CONFIRMED'): ?>
<button class="btn red" onclick="cancel(<?= $r['id'] ?>)">Cancel</button>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>

<script>
function cancel(id){
  if(!confirm('Cancel booking?')) return;
  fetch('/api/v1/flights/cancel.php',{
    method:'POST',
    body:JSON.stringify({
      booking_id:id,
      pnr:'NA',
      payment_mode:'RAZORPAY',
      refund_amount:1000
    })
  }).then(()=>location.reload());
}
</script>

</body>
</html>
