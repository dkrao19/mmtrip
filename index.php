<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . "/config/database.php";

/* ============================
   1. USER AUTH & REDIRECTS
============================ */
$user = $_SESSION['user'] ?? null;

if(isset($_GET['dashboard']) && $user){
    switch($user['role_id']){
        case 1: case 2: header("Location:/admin/index.php"); break;
        case 3: header("Location:/agent/index.php"); break;
        case 5: header("Location:/supplier/index.php"); break;
        default: header("Location:/user/dashboard.php");
    }
    exit;
}

/* ============================
   2. LOAD SETTINGS
============================ */
$settings = [
    'site_logo'=>'/uploads/logo.png',
    'logo_height'=>42,
    'primary_color'=>'#0a2d4d',
    'secondary_color'=>'#ff7a00',
    'homepage_title'=>'Book Flights, Hotels & Holidays',
    'homepage_subtitle'=>'Compare prices from multiple airlines & suppliers',
    'show_flights'=>1, 'show_hotels'=>1, 'show_bus'=>1, 'show_packages'=>1,
    'hero_bg'=>'#0a2d4d'
];

try{
    $rows = $pdo->query("SELECT `key`,`value` FROM site_settings")->fetchAll();
    foreach($rows as $r){
        $settings[$r['key']] = $r['value'];
    }
}catch(Exception $e){}

/* ============================
   3. LOAD SECTIONS (CMS)
============================ */
$homepageSections = [];
try {
    $stmt = $pdo->query("SELECT section_key, is_enabled, config FROM homepage_sections ORDER BY sort_order ASC");
    $homepageSections = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

if (!$homepageSections) {
    // Fallback if DB is empty
    $homepageSections = [
        ['section_key'=>'hero','is_enabled'=>1],
        ['section_key'=>'search','is_enabled'=>1],
        ['section_key'=>'deals','is_enabled'=>1],
        ['section_key'=>'destinations','is_enabled'=>1],
        ['section_key'=>'loyalty','is_enabled'=>1],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>MMTrips – <?= htmlspecialchars($settings['homepage_title']) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?= htmlspecialchars($settings['homepage_subtitle']) ?>">

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root{
        --primary: <?= $settings['primary_color'] ?>;
        --secondary: <?= $settings['secondary_color'] ?>;
        --text: #1e293b;
        --bg: #f8fafc;
    }
    * { box-sizing: border-box; outline: none; }
    body { margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); }
    a { text-decoration: none; color: inherit; transition: 0.2s; }
    
    /* GLOBAL LAYOUT */
    .section-wrap { max-width: 1200px; margin: 60px auto; padding: 0 20px; }
    .section-header { margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end; }
    .section-header h2 { margin: 0; font-size: 28px; color: var(--text); }
    
    /* HEADER & FOOTER OVERRIDES */
    .header { background: #fff; position: sticky; top: 0; z-index: 999; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .header-inner { max-width: 1200px; margin: auto; display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; }
    .nav { display: flex; gap: 20px; align-items: center; font-weight: 600; font-size: 14px; }
    .nav a:hover { color: var(--secondary); }
    .btn-login { background: var(--secondary); color: #fff !important; padding: 10px 20px; border-radius: 8px; }

    /* FLIGHT RESULTS STYLING */
    .results-area { max-width: 1140px; margin: 20px auto; padding: 0 15px; display: none; }
    .flight-card { 
        background: #fff; border-radius: 12px; padding: 20px; margin-bottom: 15px; 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); display: grid; 
        grid-template-columns: 80px 2fr 1fr; gap: 20px; align-items: center; 
    }
    .flight-card img { width: 50px; }
    .flight-info b { font-size: 18px; color: var(--text); }
    .flight-price-box { text-align: right; }
    .flight-price { font-size: 22px; font-weight: 700; color: var(--text); display: block; margin-bottom: 5px; }
    .btn-book { background: var(--primary); color: #fff; border: none; padding: 8px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; }

    /* CARD GRIDS (For Deals/Destinations) */
    .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px; }
    .deal-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: 0.3s; display: block; }
    .deal-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .deal-img { height: 180px; background: #cbd5e1; position: relative; overflow: hidden; }
    .deal-img img { width: 100%; height: 100%; object-fit: cover; }
    .deal-content { padding: 16px; }
    .deal-price { float: right; font-weight: 700; color: var(--secondary); font-size: 18px; }

    /* FOOTER */
    .footer { background: var(--primary); color: #cbd5e1; padding: 60px 20px; margin-top: 80px; }
    .footer h4 { color: #fff; }
</style>
</head>
<body>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<div id="homepageContent">
    <?php
    foreach ($homepageSections as $sec) {
        if ((int)$sec['is_enabled'] !== 1) continue;
        
        // Load specific section file
        $file = __DIR__ . '/sections/' . $sec['section_key'] . '.php';
        if (file_exists($file)) {
            include $file;
        }
    }
    ?>
</div>

<div id="flightResults" class="results-area">
    <button onclick="location.reload()" style="margin-bottom:20px; background:none; border:none; color:var(--primary); font-weight:600; cursor:pointer;">
        <i class="fas fa-arrow-left"></i> Back to Search
    </button>
    <div id="resultsList"></div>
</div>

<div id="fareModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;">
    <div style="background:#fff; width:90%; max-width:500px; padding:25px; border-radius:12px; position:relative;">
        <span onclick="document.getElementById('fareModal').style.display='none'" style="position:absolute; top:15px; right:20px; cursor:pointer; font-size:20px;">&times;</span>
        <h3>Fare Rules & Baggage</h3>
        <div id="fareContent">Loading...</div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<div id="loginModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:#fff; width:360px; padding:30px; border-radius:12px; position:relative;">
        <span onclick="closeLogin()" style="position:absolute; top:15px; right:15px; cursor:pointer;">&times;</span>
        <h3 style="margin-top:0">Login</h3>
        <input id="loginEmail" placeholder="Email" style="width:100%; padding:10px; margin-bottom:10px; border:1px solid #ddd; border-radius:6px;">
        <input id="loginPassword" type="password" placeholder="Password" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">
        <button onclick="doLogin()" style="width:100%; padding:10px; background:var(--primary); color:#fff; border:none; border-radius:6px; cursor:pointer;">Login</button>
        <p id="loginError" style="color:red; font-size:12px; margin-top:10px;"></p>
    </div>
</div>

<script>
/* ===============================
   1. PASSENGER & TAB LOGIC
================================ */
// Match passenger IDs from search.php
let pax = { a: 1, c: 0, i: 0 };

function chg(type, val) {
    pax[type] = Math.max(0, pax[type] + val);
    if(type === 'a' && pax.a < 1) pax.a = 1; // Minimum 1 Adult
    if(pax.i > pax.a) pax.i = pax.a; // Infants cannot exceed adults

    // Update the number inside the popup
    const el = document.getElementById(type);
    if(el) el.innerText = pax[type];

    // Update the main input text
    const textEl = document.getElementById('paxText');
    if(textEl) {
        textEl.value = `${pax.a} Adult${pax.c ? `, ${pax.c} Child` : ''}${pax.i ? `, ${pax.i} Infant` : ''}`;
    }
}

// Return Date Logic
const tripRadios = document.querySelectorAll('input[name="trip"]');
const returnInput = document.getElementById('return');
if(tripRadios && returnInput) {
    tripRadios.forEach(r => {
        r.addEventListener('change', () => {
            returnInput.disabled = (r.value === 'ONEWAY');
        });
    });
}

// Tab Switching Logic
function switchTab(tab) {
    // Visual update for tabs
    document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active'));
    event.currentTarget.classList.add('active');
    
    // Logic to show/hide forms can go here if you expand hotels/bus later
    if(tab !== 'flights') {
        alert(tab.charAt(0).toUpperCase() + tab.slice(1) + ' booking coming soon!');
    }
}


/* ===============================
   2. AUTOCOMPLETE
================================ */
function initAutocomplete(inputId, suggestId) {
    const inp = document.getElementById(inputId);
    const sug = document.getElementById(suggestId);
    if(!inp || !sug) return;

    inp.dataset.code = ''; // Store IATA code here

    inp.addEventListener('input', () => {
        const q = inp.value.trim();
        if(q.length < 2) {
            sug.style.display = 'none';
            return;
        }

        // Using Airport API
        fetch('/api/v1/common/airports.php?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(data => {
                sug.innerHTML = '';
                if(data.length > 0) {
                    sug.style.display = 'block';
                    data.forEach(a => {
                        const div = document.createElement('div');
                        div.style.padding = '10px';
                        div.style.cursor = 'pointer';
                        div.style.borderBottom = '1px solid #f1f5f9';
                        div.innerHTML = `<b>${a.city}</b> (${a.code}) <small style="color:#64748b">${a.name}</small>`;
                        
                        div.onclick = () => {
                            inp.value = `${a.city} (${a.code})`;
                            inp.dataset.code = a.code; // Important: Store Code
                            sug.style.display = 'none';
                        };
                        div.onmouseover = () => div.style.background = '#f8fafc';
                        div.onmouseout = () => div.style.background = '#fff';
                        
                        sug.appendChild(div);
                    });
                } else {
                    sug.style.display = 'none';
                }
            })
            .catch(() => sug.style.display = 'none');
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
        if(!inp.contains(e.target) && !sug.contains(e.target)) {
            sug.style.display = 'none';
        }
    });
}

// Initialize Autocomplete
initAutocomplete('from', 'fromSug');
initAutocomplete('to', 'toSug');


/* ===============================
   3. SEARCH FUNCTION
================================ */
function searchFlights() {
    const fromEl = document.getElementById('from');
    const toEl = document.getElementById('to');
    const departEl = document.getElementById('depart');
    
    // Use stored code if available, else use raw value (fallback)
    const from = fromEl.dataset.code || fromEl.value;
    const to = toEl.dataset.code || toEl.value;
    const date = departEl.value;

    if(!from || !to || !date) {
        alert("Please select origin, destination and departure date.");
        return;
    }

    // UI Toggle
    document.getElementById('homepageContent').style.display = 'none';
    document.getElementById('flightResults').style.display = 'block';
    const list = document.getElementById('resultsList');
    list.innerHTML = '<div style="text-align:center; padding:50px;"><i class="fas fa-spinner fa-spin fa-3x" style="color:var(--primary)"></i><p>Searching best fares...</p></div>';

    // API Call
    const params = new URLSearchParams({ from, to, date });
    
    fetch('/api/v1/flights/search.php?' + params.toString())
        .then(r => r.json())
        .then(data => {
            list.innerHTML = '';
            
            if(!Array.isArray(data) || data.length === 0) {
                list.innerHTML = '<div style="text-align:center; padding:30px"><h3>No flights found</h3><button class="btn-book" onclick="location.reload()">Try Again</button></div>';
                return;
            }

            // Find cheapest for highlighting
            const cheapest = Math.min(...data.map(f => parseFloat(f.price)));

            data.forEach(f => {
                const isCheap = parseFloat(f.price) === cheapest;
                
                list.innerHTML += `
                <div class="flight-card">
                    <img src="https://pics.avs.io/200/200/${f.airline}.png" alt="${f.airline}">
                    <div class="flight-info">
                        <b>${f.origin} <i class="fas fa-arrow-right" style="font-size:12px; opacity:0.5"></i> ${f.destination}</b><br>
                        <span style="color:#64748b">${f.airline} | ${f.flight_number}</span><br>
                        <small>${formatTime(f.departure_time)} – ${formatTime(f.arrival_time)}</small>
                    </div>
                    <div class="flight-price-box">
                        <span class="flight-price">₹${f.price}</span>
                        ${isCheap ? '<small style="color:green; font-weight:bold">Best Price</small><br>' : ''}
                        <button class="btn-book" onclick='selectFlight(${JSON.stringify(f)})'>Select</button>
                    </div>
                </div>`;
            });
        })
        .catch(err => {
            console.error(err);
            list.innerHTML = '<p style="text-align:center; color:red">Search Error. Please try again.</p>';
        });
}

function selectFlight(flight) {
    // Store selected flight in session
    fetch('/api/v1/session/store_offer.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(flight)
    }).then(() => {
        // Redirect logic based on OneWay vs Return
        const isReturn = !document.getElementById('return').disabled;
        if(isReturn && document.getElementById('return').value) {
            location.href = `/booking/return.php?date=${document.getElementById('return').value}`;
        } else {
            location.href = '/booking/passengers.php';
        }
    });
}

function formatTime(dt) {
    if(!dt) return '';
    return new Date(dt).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

/* ===============================
   4. LOGIN LOGIC
================================ */
function openLogin(){ document.getElementById('loginModal').style.display='flex'; }
function closeLogin(){ document.getElementById('loginModal').style.display='none'; }

function doLogin(){
    const e = document.getElementById('loginEmail').value;
    const p = document.getElementById('loginPassword').value;
    
    fetch('/api/v1/auth/login.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({email:e, password:p})
    })
    .then(r => r.json())
    .then(res => {
        if(res.success) location.href='/?dashboard=1';
        else document.getElementById('loginError').innerText = res.message || 'Login Failed';
    })
    .catch(() => document.getElementById('loginError').innerText = 'System Error');
}
</script>

</body>
</html>