<?php
$config = json_decode($sec['config'] ?? '{}', true);
$items = $config['items'] ?? [];
if(!empty($items)):
?>
<style>
    .deals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
        margin-top: 20px;
    }
    .deal-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        display: flex;
        flex-direction: column;
    }
    .deal-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 35px -5px rgba(0,0,0,0.15);
    }
    .deal-img-wrap {
        height: 200px;
        position: relative;
        overflow: hidden;
    }
    .deal-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }
    .deal-card:hover .deal-img-wrap img {
        transform: scale(1.1);
    }
    .deal-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: #ef4444;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 5px 10px;
        border-radius: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }
    .deal-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .deal-title {
        margin: 0 0 10px 0;
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
    }
    .deal-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f1f5f9;
        padding-top: 15px;
    }
    .deal-price {
        font-size: 20px;
        font-weight: 800;
        color: var(--secondary);
    }
    .deal-price small {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }
    .btn-deal {
        color: var(--primary);
        background: rgba(10, 45, 77, 0.05);
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        transition: 0.2s;
    }
    .btn-deal:hover {
        background: var(--primary);
        color: #fff;
    }
</style>

<div class="section-wrap">
    <div class="section-header">
        <div>
            <h2>🔥 Exclusive Deals</h2>
            <p style="margin:5px 0 0; color:#64748b;">Handpicked offers just for you</p>
        </div>
        <a href="/deals" style="color:var(--secondary); font-weight:600; font-size:14px;">View All Deals <i class="fas fa-arrow-right"></i></a>
    </div>

    <div class="deals-grid">
        <?php foreach($items as $item): ?>
        <div class="deal-card">
            <div class="deal-img-wrap">
                <?php if(!empty($item['image'])): ?>
                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" onerror="this.src='https://placehold.co/600x400?text=Hot+Deal'">
                <?php else: ?>
                    <img src="https://placehold.co/600x400?text=Offer" alt="Deal">
                <?php endif; ?>
                
                <span class="deal-badge">Limited Offer</span>
            </div>
            
            <div class="deal-body">
                <h3 class="deal-title"><?= htmlspecialchars($item['title']) ?></h3>
                <div class="deal-footer">
                    <div class="deal-price">
                        <small>From</small><br>
                        ₹<?= number_format((float)str_replace(',','',$item['price'])) ?>
                    </div>
                    <a href="/deals/view.php?id=<?= rand(100,999) ?>" class="btn-deal">Book Now</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>