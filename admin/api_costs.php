<?php require "../layout/header.php"; require "../layout/sidebar.php";
$rows=$pdo->query("
SELECT api_key,SUM(cost) total FROM api_cost_logs GROUP BY api_key
")->fetchAll(); ?>
<div class="card">
<h3>API Cost</h3>
<table>
<tr><th>API</th><th>Total Cost</th></tr>
<?php foreach($rows as $r): ?>
<tr><td><?= $r['api_key'] ?></td><td><?= number_format($r['total'],2) ?></td></tr>
<?php endforeach ?>
</table>
</div>
<?php require "../layout/footer.php"; ?>
