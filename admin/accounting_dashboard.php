<?php
require_once "../config/database.php";
include "header.php";
include "sidebar.php";
?>

<div class="container">
<h2>📊 Accounting Dashboard</h2>

<!-- Summary -->
<?php
$summary = $pdo->query("
  SELECT
    SUM(total_amount) total_sales,
    SUM(CASE WHEN status='SUCCESS' THEN total_amount ELSE 0 END) paid
  FROM bookings
")->fetch();
?>

<div class="cards">
  <div class="card">Total Sales<br><b>₹<?= number_format($summary['total_sales'],2) ?></b></div>
  <div class="card">Paid Amount<br><b>₹<?= number_format($summary['paid'],2) ?></b></div>
</div>

<!-- Payments -->
<h3>💳 Payments</h3>
<table class="table">
<tr><th>Booking</th><th>Mode</th><th>Amount</th><th>Date</th></tr>
<?php
$q = $pdo->query("
  SELECT b.reference, p.payment_mode, p.amount, p.created_at
  FROM payments p
  JOIN bookings b ON b.id=p.booking_id
  ORDER BY p.created_at DESC
  LIMIT 50
");
foreach($q as $r){
  echo "<tr>
    <td>{$r['reference']}</td>
    <td>{$r['payment_mode']}</td>
    <td>₹{$r['amount']}</td>
    <td>{$r['created_at']}</td>
  </tr>";
}
?>
</table>

<!-- Refunds -->
<h3>💸 Refunds</h3>
<table class="table">
<tr><th>Booking</th><th>Mode</th><th>Amount</th><th>Status</th></tr>
<?php
$q = $pdo->query("
  SELECT b.reference, r.payment_mode, r.refund_amount, r.status
  FROM refunds r
  JOIN bookings b ON b.id=r.booking_id
  ORDER BY r.created_at DESC
  LIMIT 50
");
foreach($q as $r){
  echo "<tr>
    <td>{$r['reference']}</td>
    <td>{$r['payment_mode']}</td>
    <td>₹{$r['refund_amount']}</td>
    <td>{$r['status']}</td>
  </tr>";
}
?>
</table>

<a class="btn" href="/admin/accounting_export.php">⬇ Export Accounting</a>
</div>

<?php include "footer.php"; ?>
