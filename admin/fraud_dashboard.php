<?php
require_once "../config/database.php";
include "header.php";
include "sidebar.php";
?>

<div class="container">
<h2>🚨 Fraud Monitoring Dashboard</h2>

<!-- Refund Abuse -->
<h3>🔁 Excessive Refunds (Last 7 Days)</h3>
<table class="table">
<tr><th>User ID</th><th>Refund Count</th></tr>
<?php
$q = $pdo->query("
  SELECT b.user_id, COUNT(*) cnt
  FROM refunds r
  JOIN bookings b ON b.id = r.booking_id
  WHERE r.created_at > NOW() - INTERVAL 7 DAY
  GROUP BY b.user_id
  HAVING cnt > 3
");
foreach($q as $r){
  echo "<tr><td>{$r['user_id']}</td><td>{$r['cnt']}</td></tr>";
}
?>
</table>

<!-- Coupon Abuse -->
<h3>🎟️ Coupon Abuse</h3>
<table class="table">
<tr><th>User</th><th>Coupons Used (24h)</th></tr>
<?php
$q = $pdo->query("
  SELECT user_id, COUNT(*) cnt
  FROM audit_logs
  WHERE action='COUPON_APPLIED'
    AND created_at > NOW() - INTERVAL 1 DAY
  GROUP BY user_id
  HAVING cnt > 5
");
foreach($q as $r){
  echo "<tr><td>{$r['user_id']}</td><td>{$r['cnt']}</td></tr>";
}
?>
</table>

<!-- Wallet Abuse -->
<h3>💳 Wallet Misuse</h3>
<table class="table">
<tr><th>User</th><th>Balance</th><th>Credit Limit</th></tr>
<?php
$q = $pdo->query("
  SELECT user_id,balance,credit_limit
  FROM wallets
  WHERE balance < 0
");
foreach($q as $r){
  echo "<tr><td>{$r['user_id']}</td><td>{$r['balance']}</td><td>{$r['credit_limit']}</td></tr>";
}
?>
</table>

<!-- Audit Logs -->
<h3>📜 Audit Logs (Latest)</h3>
<table class="table">
<tr><th>User</th><th>Action</th><th>Reference</th><th>IP</th><th>Time</th></tr>
<?php
$q = $pdo->query("
  SELECT * FROM audit_logs
  ORDER BY created_at DESC
  LIMIT 50
");
foreach($q as $r){
  echo "<tr>
    <td>{$r['user_id']}</td>
    <td>{$r['action']}</td>
    <td>{$r['reference']}</td>
    <td>{$r['ip_address']}</td>
    <td>{$r['created_at']}</td>
  </tr>";
}
?>
</table>
</div>

<?php include "footer.php"; ?>
