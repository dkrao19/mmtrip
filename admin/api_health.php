<?php require "../layout/header.php"; require "../layout/sidebar.php";
$rows=$pdo->query("SELECT * FROM api_circuit_breaker")->fetchAll(); ?>
<div class="card">
<h3>API Health</h3>
<table>
<tr><th>API</th><th>State</th><th>Failures</th></tr>
<?php foreach($rows as $r): ?>
<tr><td><?= $r['api_key'] ?></td><td><?= $r['state'] ?></td><td><?= $r['failure_count'] ?></td></tr>
<?php endforeach ?>
</table>
</div>
<?php require "../layout/footer.php"; ?>
