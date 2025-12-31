<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . "/../../config/database.php";

/* ===============================
   AUTH CHECK
================================ */
if (empty($_SESSION['user']) || $_SESSION['user']['role_id'] > 2) {
    header("Location: /admin/login.php");
    exit;
}

/* ===============================
   HANDLE AJAX REQUESTS
================================ */
$raw = file_get_contents("php://input");
if ($raw) {
    $json = json_decode($raw, true);
    if (is_array($json)) $_POST = array_merge($_POST, $json);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    header('Content-Type: application/json');
    try {
        switch ($_POST['action']) {

            // 1. SAVE ALL SETTINGS (Merged General + Footer + Header)
            case 'save_settings':
                if(!empty($_POST['settings'])){
                    foreach ($_POST['settings'] as $k => $v) {
                        $pdo->prepare("INSERT INTO site_settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)")
                            ->execute([$k, $v]);
                    }
                }
                echo json_encode(['success' => true]);
                exit;

            // 2. SAVE MODULES
            case 'save_modules':
                foreach ($_POST['modules'] as $k => $v) {
                    $pdo->prepare("INSERT INTO site_settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)")
                        ->execute([$k, (int)$v]);
                }
                echo json_encode(['success' => true]);
                exit;

            // 3. SAVE SECTIONS
            case 'save_sections':
                foreach ($_POST['sections'] as $s) {
                    $pdo->prepare("UPDATE homepage_sections SET sort_order=?, is_enabled=? WHERE section_key=?")
                        ->execute([(int)$s['order'], (int)$s['is_enabled'], $s['key']]);
                }
                echo json_encode(['success' => true]);
                exit;

            // 4. SAVE CONTENT (Deals/Destinations)
            case 'save_section_content':
                $pdo->prepare("UPDATE homepage_sections SET config=? WHERE section_key=?")
                    ->execute([json_encode($_POST['config'] ?? []), $_POST['section_key']]);
                echo json_encode(['success' => true]);
                exit;

            // 5. UPLOAD LOGO
            case 'upload_logo':
                if (!empty($_FILES['logo'])) {
                    $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                    $name = 'logo_' . time() . '.' . $ext;
                    $path = "/uploads/" . $name;
                    move_uploaded_file($_FILES['logo']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $path);
                    
                    $pdo->prepare("INSERT INTO site_settings (`key`,`value`) VALUES ('site_logo',?) ON DUPLICATE KEY UPDATE value=VALUES(value)")
                        ->execute([$path]);
                }
                echo json_encode(['success' => true]);
                exit;
        }
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

/* ===============================
   LOAD DATA
================================ */
$settings = [];
$stmt = $pdo->query("SELECT `key`, `value` FROM site_settings");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}

// Defaults for Countries if empty
if(empty($settings['header_countries'])) {
    $settings['header_countries'] = json_encode([
        ['name'=>'Myanmar', 'code'=>'MM', 'flag'=>'https://flagcdn.com/w40/mm.png'],
        ['name'=>'India', 'code'=>'IN', 'flag'=>'https://flagcdn.com/w40/in.png'],
        ['name'=>'Global', 'code'=>'GL', 'flag'=>'https://flagcdn.com/w40/un.png']
    ]);
}

// Defaults for Currencies if empty
if(empty($settings['header_currencies'])) {
    $settings['header_currencies'] = json_encode([
        ['code'=>'MMK', 'symbol'=>'Ks'],
        ['code'=>'USD', 'symbol'=>'$']
    ]);
}

// Default Footer Menus
if(empty($settings['footer_menu_company'])) {
    $settings['footer_menu_company'] = json_encode([
        ['label'=>'About Us', 'url'=>'/about'],
        ['label'=>'Careers', 'url'=>'/careers'],
        ['label'=>'Blog', 'url'=>'/blog']
    ]);
}
if(empty($settings['footer_menu_support'])) {
    $settings['footer_menu_support'] = json_encode([
        ['label'=>'Contact Us', 'url'=>'/contact'],
        ['label'=>'FAQs', 'url'=>'/faqs'],
        ['label'=>'Terms', 'url'=>'/terms']
    ]);
}

$sections = $pdo->query("SELECT * FROM homepage_sections ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

$dealsData = [];
$destData = [];
foreach ($sections as $s) {
    if ($s['section_key'] === 'deals' && !empty($s['config'])) $dealsData = json_decode($s['config'], true)['items'] ?? [];
    if ($s['section_key'] === 'destinations' && !empty($s['config'])) $destData = json_decode($s['config'], true)['items'] ?? [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Homepage Builder</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
    :root { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #3b82f6; --danger: #ef4444; }
    body { margin: 0; font-family: 'Inter', sans-serif; height: 100vh; display: flex; overflow: hidden; background: #000; }
    
    .sidebar { width: 440px; background: var(--bg); color: var(--text); padding: 20px; overflow-y: auto; border-right: 1px solid #334155; display: flex; flex-direction: column; gap: 15px; }
    
    .card { background: var(--card); padding: 15px; border-radius: 8px; border: 1px solid #334155; }
    h3 { margin: 0 0 10px 0; font-size: 13px; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; border-bottom: 1px solid #334155; padding-bottom: 8px; }
    
    label { display: block; margin-bottom: 4px; font-size: 12px; color: #cbd5e1; }
    input[type="text"], input[type="number"], input[type="color"], textarea { width: 100%; padding: 8px; background: #0f172a; border: 1px solid #475569; color: white; border-radius: 4px; margin-bottom: 10px; box-sizing: border-box; font-size: 13px; font-family: inherit; }
    textarea { resize: vertical; min-height: 60px; }
    
    .btn { background: var(--primary); color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; width: 100%; font-weight: 500; font-size: 13px; }
    .btn:hover { opacity: 0.9; }
    .btn-outline { background: transparent; border: 1px solid var(--primary); color: var(--primary); margin-bottom: 5px; }
    .btn-danger { background: transparent; border: 1px solid var(--danger); color: var(--danger); width: auto; padding: 4px 8px; font-size: 11px; }

    .preview { flex: 1; background: #f8fafc; position: relative; }
    iframe { width: 100%; height: 100%; border: none; }

    .section-item { display: flex; justify-content: space-between; align-items: center; background: #334155; padding: 10px; margin-bottom: 5px; border-radius: 4px; cursor: move; font-size: 13px; }
    .content-item { background: #0f172a; padding: 10px; margin-bottom: 8px; border-radius: 4px; border: 1px solid #334155; }

    #toast { position: fixed; bottom: 20px; right: 20px; background: #10b981; color: white; padding: 10px 20px; border-radius: 6px; display: none; font-size: 14px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); z-index: 9999; }
</style>
</head>
<body>

<div class="sidebar">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <h2 style="margin:0; font-size:18px;">🛠 Site Builder</h2>
        <button onclick="reloadPreview()" class="btn" style="width:auto; padding:5px 10px;">Refresh</button>
    </div>
    
    <div class="card">
        <h3>🎨 Branding</h3>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
            <div><label>Logo</label><input type="file" onchange="uploadLogo(this)"></div>
            <div><label>Height (px)</label><input type="number" id="logo_height" value="<?= $settings['logo_height'] ?? 42 ?>"></div>
        </div>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
            <div><label>Primary Color</label><input type="color" id="primary_color" value="<?= $settings['primary_color'] ?? '#0a2d4d' ?>" style="height:35px"></div>
            <div><label>Secondary Color</label><input type="color" id="secondary_color" value="<?= $settings['secondary_color'] ?? '#ff7a00' ?>" style="height:35px"></div>
        </div>
    </div>

    <div class="card">
        <h3>🌍 Header & Localization</h3>
        <label>Countries (JSON: Name, Code, Flag URL)</label>
        <textarea id="header_countries" style="font-family:monospace; font-size:11px; height:80px;"><?= $settings['header_countries'] ?></textarea>
        
        <label>Currencies (JSON: Code, Symbol)</label>
        <textarea id="header_currencies" style="font-family:monospace; font-size:11px; height:60px;"><?= $settings['header_currencies'] ?></textarea>
        
        <button class="btn" onclick="saveAllSettings()">Save Localization</button>
    </div>

    <div class="card">
        <h3>🦶 Footer & Contact</h3>
        <label>Footer Description</label>
        <textarea id="footer_desc"><?= htmlspecialchars($settings['footer_desc'] ?? 'Your trusted travel partner for flights, hotels, and holidays.') ?></textarea>
        
        <label>Contact Phone</label>
        <input type="text" id="contact_phone" value="<?= htmlspecialchars($settings['contact_phone'] ?? '+91 98765 43210') ?>">

        <label>Copyright Text</label>
        <input type="text" id="footer_copyright" value="<?= htmlspecialchars($settings['footer_copyright'] ?? 'MMTrips. All rights reserved.') ?>">

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
            <div><label>Facebook</label><input type="text" id="social_fb" value="<?= $settings['social_fb'] ?? '' ?>"></div>
            <div><label>Twitter</label><input type="text" id="social_tw" value="<?= $settings['social_tw'] ?? '' ?>"></div>
        </div>
        
        <label>Company Links (JSON)</label>
        <textarea id="footer_menu_company" style="font-family:monospace; font-size:11px; height:60px;"><?= $settings['footer_menu_company'] ?></textarea>
        
        <label>Support Links (JSON)</label>
        <textarea id="footer_menu_support" style="font-family:monospace; font-size:11px; height:60px;"><?= $settings['footer_menu_support'] ?></textarea>

        <button class="btn" onclick="saveAllSettings()">Save Footer</button>
    </div>

    <div class="card">
        <h3>🧩 Active Modules</h3>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:5px;">
            <?php foreach(['flights','hotels','bus','packages','insurance'] as $m): ?>
                <div style="background:#0f172a; padding:5px 8px; border-radius:4px; display:flex; justify-content:space-between;">
                    <span style="font-size:11px"><?= ucfirst($m) ?></span>
                    <input type="checkbox" id="show_<?= $m ?>" <?= ($settings["show_$m"] ?? 1) ? 'checked' : '' ?>>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="btn" onclick="saveModules()" style="margin-top:10px">Save Modules</button>
    </div>

    <div class="card">
        <h3>ss Layout Order</h3>
        <div id="sectionList">
            <?php foreach($sections as $s): ?>
                <div class="section-item" data-key="<?= $s['section_key'] ?>">
                    <span><?= ucfirst($s['section_key']) ?></span>
                    <input type="checkbox" class="section-toggle" <?= $s['is_enabled'] ? 'checked' : '' ?>>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="btn" onclick="saveSections()" style="margin-top:10px">Save Layout</button>
    </div>

    <div class="card">
        <h3>🔥 Deals</h3>
        <div id="dealsContainer"></div>
        <button class="btn btn-outline" onclick="addDealUI()">+ Add</button>
        <button class="btn" onclick="saveSectionContent('deals', getDealsData())">Save Deals</button>
    </div>
    
    <div class="card">
        <h3>🌍 Destinations</h3>
        <div id="destContainer"></div>
        <button class="btn btn-outline" onclick="addDestUI()">+ Add</button>
        <button class="btn" onclick="saveSectionContent('destinations', getDestData())">Save Destinations</button>
    </div>

</div>

<div class="preview">
    <iframe id="previewFrame" src="/"></iframe>
</div>
<div id="toast">Saved!</div>

<script>
// INIT
const deals = <?= json_encode($dealsData) ?>;
const dests = <?= json_encode($destData) ?>;
document.addEventListener('DOMContentLoaded', () => {
    if(deals.length > 0) deals.forEach(d => addDealUI(d)); else addDealUI();
    if(dests.length > 0) dests.forEach(d => addDestUI(d)); else addDestUI();
    Sortable.create(document.getElementById('sectionList'), { animation: 150 });
});

function showToast(msg) {
    const t = document.getElementById('toast');
    t.innerText = msg; t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 2000);
    reloadPreview();
}
function reloadPreview() { document.getElementById('previewFrame').contentWindow.location.reload(); }
function api(data) { return fetch('', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data)}).then(r=>r.json()); }

function saveAllSettings() {
    api({ action: 'save_settings', settings: {
        logo_height: document.getElementById('logo_height').value,
        primary_color: document.getElementById('primary_color').value,
        secondary_color: document.getElementById('secondary_color').value,
        
        // Header & Localization
        header_countries: document.getElementById('header_countries').value,
        header_currencies: document.getElementById('header_currencies').value,

        // Footer Fields
        footer_desc: document.getElementById('footer_desc').value,
        contact_phone: document.getElementById('contact_phone').value,
        footer_copyright: document.getElementById('footer_copyright').value,
        social_fb: document.getElementById('social_fb').value,
        social_tw: document.getElementById('social_tw').value,
        footer_menu_company: document.getElementById('footer_menu_company').value,
        footer_menu_support: document.getElementById('footer_menu_support').value
    }}).then(() => showToast('Settings Saved'));
}

function saveModules() {
    const m={}; ['flights','hotels','bus','packages','insurance'].forEach(x => m['show_'+x]=document.getElementById('show_'+x).checked?1:0);
    api({action:'save_modules', modules:m}).then(()=>showToast('Modules Saved'));
}

function saveSections() {
    const s=[]; document.querySelectorAll('#sectionList .section-item').forEach((el,i) => {
        s.push({ key:el.dataset.key, order:i, is_enabled:el.querySelector('.section-toggle').checked?1:0 });
    });
    api({action:'save_sections', sections:s}).then(()=>showToast('Layout Saved'));
}

function saveSectionContent(key, items) {
    api({action:'save_section_content', section_key:key, config:{items}}).then(()=>showToast(key+' Saved'));
}

function uploadLogo(inp) {
    if(!inp.files[0]) return;
    const fd=new FormData(); fd.append('action','upload_logo'); fd.append('logo',inp.files[0]);
    fetch('',{method:'POST', body:fd}).then(()=>showToast('Logo Uploaded'));
}

// UI HELPERS
function addDealUI(d={}) {
    document.getElementById('dealsContainer').insertAdjacentHTML('beforeend', 
    `<div class="content-item">
        <input class="d-title" placeholder="Title" value="${d.title||''}">
        <div style="display:flex; gap:5px"><input class="d-price" placeholder="Price" value="${d.price||''}"><input class="d-badge" placeholder="Badge" value="${d.badge||''}"></div>
        <input class="d-img" placeholder="Image URL" value="${d.image||''}">
        <button class="btn-danger" onclick="this.parentNode.remove()">Remove</button>
    </div>`);
}
function getDealsData() {
    return [...document.querySelectorAll('#dealsContainer .content-item')].map(el => ({
        title: el.querySelector('.d-title').value,
        price: el.querySelector('.d-price').value,
        badge: el.querySelector('.d-badge').value,
        image: el.querySelector('.d-img').value
    })).filter(x=>x.title);
}
function addDestUI(d={}) {
    document.getElementById('destContainer').insertAdjacentHTML('beforeend', 
    `<div class="content-item">
        <input class="dst-city" placeholder="City" value="${d.city||''}">
        <input class="dst-img" placeholder="Image URL" value="${d.image||''}">
        <button class="btn-danger" onclick="this.parentNode.remove()">Remove</button>
    </div>`);
}
function getDestData() {
    return [...document.querySelectorAll('#destContainer .content-item')].map(el => ({
        city: el.querySelector('.dst-city').value,
        image: el.querySelector('.dst-img').value
    })).filter(x=>x.city);
}
</script>
</body>
</html>