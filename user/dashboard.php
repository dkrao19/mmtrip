<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . "/../config/database.php";

/* ============================
   AUTH GUARD
============================ */
if (empty($_SESSION['user']) || $_SESSION['user']['role_id'] != 4) {
    header("Location:/");
    exit;
}

$user = $_SESSION['user'];

/* ============================
   FETCH WALLET
============================ */
$wallet = ['balance'=>0,'credit_limit'=>0];
$stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id=? LIMIT 1");
$stmt->execute([$user['id']]);
if ($row = $stmt->fetch()) {
    $wallet = $row;
}

/* ============================
   FETCH LOYALTY
============================ */
$points = 0;
$stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id=?");
$stmt->execute([$user['id']]);
if ($row = $stmt->fetch()) {
    $points = $row['points'];
}

/* ============================
   FETCH BOOKINGS
============================ */
$bookings = [];
$stmt = $pdo->prepare("
  SELECT *
  FROM bookings
  WHERE user_id=?
  ORDER BY created_at DESC
  LIMIT 20
");
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>User Dashboard – MMTrips</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
  margin:0;
  font-family:Inter,sans-serif;
  background:#f4f7fb;
}
.header{
  background:#0a2d4d;
  color:#fff;
  padding:15px 20px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.header a{color:#fff;text-decoration:none;font-weight:500}
.container{
  max-width:1200px;
  margin:30px auto;
  padding:0 20px;
}
.cards{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
  gap:20px;
  margin-bottom:30px;
}
.card{
  background:#fff;
  border-radius:14px;
  padding:18px;
  box-shadow:0 10px 25px rgba(0,0,0,.08);
}
.card h3{margin:0;font-size:16px;color:#6b7280}
.card p{font-size:22px;font-weight:700;margin:8px 0}
.table{
  background:#fff;
  border-radius:14px;
  padding:20px;
  box-shadow:0 10px 25px rgba(0,0,0,.08);
}
table{width:100%;border-collapse:collapse}
th,td{padding:12px;border-bottom:1px solid #eee;text-align:left}
th{color:#6b7280;font-size:13px}
.status{
  padding:4px 10px;
  border-radius:20px;
  font-size:12px;
  font-weight:600;
}
.CONFIRMED{background:#dcfce7;color:#166534}
.CANCELLED{background:#fee2e2;color:#991b1b}
.REFUNDED{background:#e0f2fe;color:#075985}
.btn{
  padding:6px 10px;
  border-radius:6px;
  background:#0a2d4d;
  color:#fff;
  text-decoration:none;
  font-size:13px;
}
.small{font-size:13px;color:#6b7280}
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
  <div>👋 Hello, <?= htmlspecialchars($user['name']) ?></div>
  <div>
    <a href="/">Home</a> |
    <a href="/logout.php">Logout</a>
  </div>
</div>

<div class="container">

  <!-- STATS -->
  <div class="cards">
    <div class="card">
      <h3>Wallet Balance</h3>
      <p>₹<?= number_format($wallet['balance'],2) ?></p>
    </div>
    <div class="card">
      <h3>Loyalty Points</h3>
      <p><?= (int)$points ?></p>
    </div>
    <div class="card">
      <h3>Total Bookings</h3>
      <p><?= count($bookings) ?></p>
    </div>
  </div>

  <!-- BOOKINGS -->
  <div class="table">
    <h3 style="margin-bottom:15px">Recent Bookings</h3>

    <?php if (!$bookings): ?>
      <p class="small">No bookings yet.</p>
    <?php else: ?>
    <table>
      <tr>
        <th>ID</th>
        <th>Service</th>
        <th>Price</th>
        <th>Status</th>
        <th>Date</th>
        <th>Action</th>
      </tr>
      <?php foreach($bookings as $b): ?>
      <tr>
        <td>#<?= $b['id'] ?></td>
        <td><?= $b['service_type'] ?></td>
        <td>₹<?= number_format($b['selling_price'],2) ?></td>
        <td>
          <span class="status <?= $b['status'] ?>">
            <?= $b['status'] ?>
          </span>
        </td>
        <td><?= date('d M Y',strtotime($b['created_at'])) ?></td>
        <td>
          <a class="btn" href="/booking/view.php?id=<?= $b['id'] ?>">View</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php endif; ?>
  </div>

</div>

</body>
</html>
