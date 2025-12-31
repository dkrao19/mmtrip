<?php
$deals = json_decode(
  $pdo->query("SELECT content FROM homepage_sections WHERE section_key='deals'")
      ->fetchColumn(),
  true
) ?? [];
?>

<div class="section">
<h2>🔥 Deals</h2>
<div class="cards">
<?php foreach($deals as $d): ?>
  <div class="card">
    <b><?= htmlspecialchars($d['title']) ?></b><br>
    <?= htmlspecialchars($d['price']) ?>
  </div>
<?php endforeach; ?>
</div>
</div>
