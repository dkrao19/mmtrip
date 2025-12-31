<?php
$banners = $pdo->query("
  SELECT * FROM homepage_banners
  WHERE is_active=1
  ORDER BY sort_order
")->fetchAll();
?>

<div class="banner-slider">
<?php foreach($banners as $b): ?>
  <a href="<?= htmlspecialchars($b['link']) ?>">
    <img src="<?= htmlspecialchars($b['image']) ?>" alt="">
    <div class="caption">
      <h3><?= htmlspecialchars($b['title']) ?></h3>
      <p><?= htmlspecialchars($b['subtitle']) ?></p>
    </div>
  </a>
<?php endforeach; ?>
</div>
