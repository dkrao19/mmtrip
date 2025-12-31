<?php
require "../config/database.php";

$stats=$pdo->query("
SELECT api_key,
COUNT(*) calls,
SUM(success) success,
AVG(response_time_ms) avg_ms
FROM api_usage_logs
GROUP BY api_key
")->fetchAll();
?>

<h2>API Health Overview</h2>

<canvas id="apiChart"></canvas>

<script>
const data = {
  labels: <?= json_encode(array_column($stats,'api_key')) ?>,
  datasets: [{
    label:'Success Calls',
    data: <?= json_encode(array_column($stats,'success')) ?>
  }]
};
new Chart(document.getElementById('apiChart'),{
  type:'bar',
  data:data
});
</script>
