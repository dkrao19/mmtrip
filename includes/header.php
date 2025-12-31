<?php
// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentUser = $_SESSION['user'] ?? null;

/* ===============================
   LOAD LOCALIZATION DATA
================================ */
$countries = json_decode($settings['header_countries'] ?? '[]', true);
if(empty($countries)){
    $countries = [
        ['name'=>'Myanmar', 'code'=>'MM', 'flag'=>'https://flagcdn.com/w40/mm.png'],
        ['name'=>'India', 'code'=>'IN', 'flag'=>'https://flagcdn.com/w40/in.png'],
        ['name'=>'Global', 'code'=>'GL', 'flag'=>'https://flagcdn.com/w40/un.png']
    ];
}

$currencies = json_decode($settings['header_currencies'] ?? '[]', true);
if(empty($currencies)){
    $currencies = [
        ['code'=>'MMK', 'symbol'=>'Ks'],
        ['code'=>'USD', 'symbol'=>'$']
    ];
}
?>

<style>
/* Header specific styles */
.header-actions { display: flex; align-items: center; gap: 15px; }

/* Dropdown Container */
.dropdown-wrap { 
    position: relative; 
    cursor: pointer; 
    display: flex; 
    align-items: center; 
    gap: 6px; 
    font-size: 14px; 
    font-weight: 600;
    padding: 5px;
    border-radius: 6px;
    transition: background 0.2s;
    user-select: none;
}
.dropdown-wrap:hover { background: #f8fafc; }

/* Dropdown Menu (Hidden by default) */
.dropdown-menu { 
    display: none; 
    position: absolute; 
    top: 120%; 
    right: 0; 
    background: #fff; 
    box-shadow: 0 10px 25px rgba(0,0,0,0.15); 
    border: 1px solid #e2e8f0;
    border-radius: 8px; 
    padding: 8px 0; 
    min-width: 150px; 
    z-index: 1000; 
}

/* Show class for JS toggle */
.dropdown-menu.show { display: block; animation: fadeIn 0.2s ease-in-out; }

.dropdown-item { 
    padding: 10px 15px; 
    display: flex; 
    align-items: center; 
    gap: 10px; 
    color: #334155; 
    transition: 0.2s; 
    font-weight: 500;
}
.dropdown-item:hover { background: #f1f5f9; color: var(--primary); }

/* Flags */
.flag-icon { 
    width: 24px; 
    height: 24px; 
    border-radius: 50%; 
    object-fit: cover; 
    border: 1px solid #e2e8f0; 
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<header class="header">
    <div class="header-inner">
        <a href="/" class="logo">
            <img src="<?= $settings['site_logo'] ?? '/assets/img/logo.png' ?>" 
                 alt="Logo" 
                 style="height: <?= (int)($settings['logo_height'] ?? 42) ?>px; display: block;"
                 onerror="this.src='https://placehold.co/120x42?text=MMTrips'">
        </a>

        <div class="header-actions">
            <nav class="nav" style="margin-right: 15px; display: flex; gap: 20px;">
                <?php if(($settings['show_packages'] ?? 0) == 1): ?>
                    <a href="/packages"><i class="fas fa-suitcase"></i> Holidays</a>
                <?php endif; ?>
                <a href="/deals"><i class="fas fa-tags"></i> Deals</a>
                <a href="/support"><i class="fas fa-headset"></i> Support</a>
            </nav>

            <div class="dropdown-wrap" onclick="toggleDrop(event, 'countryMenu')">
                <img src="<?= $countries[0]['flag'] ?>" class="flag-icon">
                <span><?= $countries[0]['code'] ?></span>
                <i class="fas fa-chevron-down" style="font-size: 10px; opacity: 0.5;"></i>
                
                <div id="countryMenu" class="dropdown-menu">
                    <?php foreach($countries as $c): ?>
                    <div class="dropdown-item" onclick="selectItem('country', '<?= $c['name'] ?>')">
                        <img src="<?= $c['flag'] ?>" class="flag-icon">
                        <span><?= $c['name'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="dropdown-wrap" onclick="toggleDrop(event, 'currencyMenu')">
                <span><?= $currencies[0]['code'] ?></span>
                <i class="fas fa-chevron-down" style="font-size: 10px; opacity: 0.5;"></i>

                <div id="currencyMenu" class="dropdown-menu">
                    <?php foreach($currencies as $curr): ?>
                    <div class="dropdown-item" onclick="selectItem('currency', '<?= $curr['code'] ?>')">
                        <b style="width:20px; text-align:center"><?= $curr['symbol'] ?></b>
                        <span><?= $curr['code'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ($currentUser): ?>
                <a href="/?dashboard=1" style="font-weight: 700; color: var(--primary); margin-left: 5px; display:flex; align-items:center; gap:5px;">
                    <i class="fas fa-user-circle" style="font-size:20px;"></i> 
                    <?= htmlspecialchars(explode(' ', $currentUser['name'])[0]) ?>
                </a>
                <a href="/logout.php" style="color: #ef4444; margin-left: 10px;" title="Logout">
                    <i class="fas fa-sign-out-alt" style="font-size:18px;"></i>
                </a>
            <?php else: ?>
                <a href="javascript:void(0)" onclick="openLogin()" class="btn-login" 
                   style="background: <?= $settings['secondary_color'] ?? '#ff7a00' ?>; margin-left: 10px;">
                   Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
/**
 * Toggles a specific dropdown and closes others.
 * Stops propagation so the window click listener doesn't immediately close it.
 */
function toggleDrop(e, menuId) {
    e.stopPropagation(); // Prevent click from bubbling to window
    
    // Close all other menus first
    document.querySelectorAll('.dropdown-menu').forEach(el => {
        if (el.id !== menuId) el.classList.remove('show');
    });

    // Toggle the target menu
    const menu = document.getElementById(menuId);
    if (menu) {
        menu.classList.toggle('show');
    }
}

/**
 * Handle Item Selection (Placeholder logic)
 */
function selectItem(type, value) {
    // Here you would typically set a cookie or reload the page
    // alert(type + ' changed to: ' + value);
    console.log(type, value);
}

/**
 * Close all dropdowns when clicking anywhere else on the page
 */
window.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-menu').forEach(el => {
        el.classList.remove('show');
    });
});
</script>