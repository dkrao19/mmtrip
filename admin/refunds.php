<?php
require_once "../config/database.php";
$q = $pdo->query("
  SELECT r.*, b.reference
  FROM refunds r
  LEFT JOIN bookings b ON b.id=r.booking_id
  ORDER BY r.created_at DESC
");
?>
<h2>Refund Management</h2>

<table border="1" cellpadding="8">
<tr>
<th>Booking</th>
<th>Mode</th>
<th>Amount</th>
<th>Status</th>
<th>Retry</th>
<th>SLA</th>
</tr>

<?php foreach($q as $r): ?>
<tr>
<td><?= $r['reference'] ?></td>
<td><?= $r['payment_mode'] ?></td>
<td><?= $r['refund_amount'] ?> <?= $r['currency'] ?></td>
<td><?= $r['status'] ?></td>
<td><?= $r['retry_count'] ?>/3</td>
<td>
<?php
$hrs = (time()-strtotime($r['created_at']))/3600;
echo $hrs<24?'🟢':($hrs<72?'🟡':'🔴');
?>
</td>
</tr>
<?php endforeach; ?>
</table>
