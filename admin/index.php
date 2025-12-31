<?php require_once __DIR__."/layout/header.php"; ?>
<?php require_once __DIR__."/layout/sidebar.php"; ?>

<div class="main">
<div class="topbar">
  <h2>Dashboard</h2>
  <div>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></div>
</div>

<div class="content">
  <div class="grid grid-auto">

    <div class="card">
      <h3>Total Bookings</h3>
      <h1>1,248</h1>
    </div>

    <div class="card">
      <h3>Revenue</h3>
      <h1>₹82.4L</h1>
    </div>

    <div class="card">
      <h3>Active Agents</h3>
      <h1>124</h1>
    </div>

  </div>

  <br>

  <div class="card">
    <h3>Bookings Trend</h3>
    <canvas id="chart"></canvas>
  </div>
</div>
</div>

<script>
new Chart(document.getElementById('chart'),{
  type:'line',
  data:{
    labels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
    datasets:[{
      label:'Bookings',
      data:[32,44,51,39,60,72,68],
      borderColor:'#0ea5e9',
      tension:.4
    }]
  }
});
</script>

<?php require_once __DIR__."/layout/footer.php"; ?>
