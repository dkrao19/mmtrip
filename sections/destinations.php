<?php
$config = json_decode($sec['config'] ?? '{}', true);
$items = $config['items'] ?? [];
if(!empty($items)):
?>
<style>
    .dest-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }
    .dest-card {
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        height: 280px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: transform 0.3s;
    }
    .dest-card:hover {
        transform: translateY(-5px);
    }
    .dest-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }
    .dest-card:hover .dest-img {
        transform: scale(1.05);
    }
    /* Gradient Overlay */
    .dest-overlay {
        position: absolute;
        bottom: 0; left: 0; width: 100%;
        background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.5) 50%, transparent 100%);
        padding: 80px 20px 20px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }
    .dest-name {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .dest-sub {
        color: rgba(255,255,255,0.8);
        font-size: 13px;
        margin-top: 4px;
        font-weight: 500;
    }
    .dest-arrow {
        position: absolute;
        bottom: 20px; right: 20px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(4px);
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.3s;
    }
    .dest-card:hover .dest-arrow {
        opacity: 1;
        transform: translateX(0);
    }
</style>

<div class="section-wrap">
    <div class="section-header">
        <h2>🌍 Popular Destinations</h2>
        <span style="color:#64748b; font-size:14px; font-weight:500;">Explore top rated cities</span>
    </div>

    <div class="dest-grid">
        <?php foreach($items as $item): ?>
        <a href="/search?to=<?= urlencode($item['city']) ?>" class="dest-card">
            <?php if(!empty($item['image'])): ?>
                <img class="dest-img" src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['city']) ?>" onerror="this.src='https://placehold.co/400x500?text=Explore'">
            <?php else: ?>
                <img class="dest-img" src="https://placehold.co/400x500?text=<?= urlencode($item['city']) ?>" alt="City">
            <?php endif; ?>

            <div class="dest-overlay">
                <h3 class="dest-name"><?= htmlspecialchars($item['city']) ?></h3>
                <div class="dest-sub">Explore Flights & Hotels</div>
            </div>
            
            <div class="dest-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>