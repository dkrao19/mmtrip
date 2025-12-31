<?php
// Decode JSON Menus with Defaults
$companyLinks = json_decode($settings['footer_menu_company'] ?? '[]', true);
if (empty($companyLinks)) {
    $companyLinks = [
        ['label'=>'About Us', 'url'=>'/about'],
        ['label'=>'Careers', 'url'=>'/careers'],
        ['label'=>'Blog', 'url'=>'/blog']
    ];
}

$supportLinks = json_decode($settings['footer_menu_support'] ?? '[]', true);
if (empty($supportLinks)) {
    $supportLinks = [
        ['label'=>'Contact Us', 'url'=>'/contact'],
        ['label'=>'FAQs', 'url'=>'/faqs'],
        ['label'=>'Terms', 'url'=>'/terms']
    ];
}
?>

<style>
    .site-footer {
        background: var(--primary, #0a2d4d);
        color: #e2e8f0;
        padding: 70px 0 30px;
        margin-top: 100px;
        font-size: 14px;
    }
    .footer-inner {
        max-width: 1200px;
        margin: auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
        gap: 40px;
    }
    .footer-col h4 {
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .footer-links a {
        display: block;
        color: #cbd5e1;
        margin-bottom: 12px;
        transition: color 0.2s, padding-left 0.2s;
    }
    .footer-links a:hover {
        color: var(--secondary, #ff7a00);
        padding-left: 5px;
    }
    .footer-desc {
        line-height: 1.6;
        opacity: 0.8;
        margin-bottom: 20px;
    }
    .social-icons {
        display: flex;
        gap: 15px;
    }
    .social-icons a {
        width: 36px; height: 36px;
        background: rgba(255,255,255,0.1);
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%;
        transition: 0.3s;
        color: #fff;
    }
    .social-icons a:hover {
        background: var(--secondary);
        transform: translateY(-3px);
    }
    .copyright {
        text-align: center;
        margin-top: 60px;
        padding-top: 25px;
        border-top: 1px solid rgba(255,255,255,0.1);
        color: #94a3b8;
    }
    @media(max-width: 900px) { .footer-inner { grid-template-columns: 1fr 1fr; } }
    @media(max-width: 600px) { .footer-inner { grid-template-columns: 1fr; gap: 30px; } }
</style>

<footer class="site-footer">
    <div class="footer-inner">
        
        <div class="footer-col">
            <img src="<?= $settings['site_logo'] ?? '' ?>" style="height:40px; margin-bottom:20px; filter: brightness(0) invert(1);" alt="Logo" onerror="this.style.display='none'">
            <p class="footer-desc">
                <?= htmlspecialchars($settings['footer_desc'] ?? 'Your trusted travel partner.') ?>
            </p>
            <div class="social-icons">
                <?php if(!empty($settings['social_fb'])): ?> <a href="<?= $settings['social_fb'] ?>" target="_blank"><i class="fab fa-facebook-f"></i></a> <?php endif; ?>
                <?php if(!empty($settings['social_tw'])): ?> <a href="<?= $settings['social_tw'] ?>" target="_blank"><i class="fab fa-twitter"></i></a> <?php endif; ?>
                <?php if(!empty($settings['social_ig'])): ?> <a href="<?= $settings['social_ig'] ?>" target="_blank"><i class="fab fa-instagram"></i></a> <?php endif; ?>
                <?php if(!empty($settings['social_li'])): ?> <a href="<?= $settings['social_li'] ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a> <?php endif; ?>
            </div>
        </div>

        <div class="footer-col">
            <h4>Company</h4>
            <div class="footer-links">
                <?php foreach($companyLinks as $link): ?>
                    <a href="<?= htmlspecialchars($link['url']) ?>"><?= htmlspecialchars($link['label']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="footer-col">
            <h4>Support</h4>
            <div class="footer-links">
                <?php foreach($supportLinks as $link): ?>
                    <a href="<?= htmlspecialchars($link['url']) ?>"><?= htmlspecialchars($link['label']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="footer-col">
            <h4>Need Help?</h4>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
                <i class="fas fa-phone-alt" style="color:var(--secondary)"></i>
                <span style="font-size:16px; font-weight:700;">
                    <?= htmlspecialchars($settings['contact_phone'] ?? '+91 98765 43210') ?>
                </span>
            </div>

            <h4>We Accept</h4>
            <div style="display:flex; gap:15px; font-size:32px; color:#fff; opacity:0.8; margin-top:10px;">
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-amex"></i>
                <i class="fab fa-cc-paypal"></i>
            </div>
        </div>

    </div>

    <div class="copyright">
        &copy; <?= date('Y') ?> <?= htmlspecialchars($settings['footer_copyright'] ?? 'MMTrips. All rights reserved.') ?>
    </div>
</footer>