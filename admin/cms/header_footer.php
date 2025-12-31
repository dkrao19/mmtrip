<?php
session_start();
require_once __DIR__.'/../../config/database.php';

if (empty($_SESSION['user']) || $_SESSION['user']['role_id'] > 2) {
  exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Content-Type: application/json');

  /* LOGO UPLOAD */
  if (!empty($_FILES['logo']['name'])) {
    $allowed = ['image/png','image/jpeg','image/webp'];
    if (!in_array($_FILES['logo']['type'], $allowed)) {
      echo json_encode(['error'=>'Invalid file type']); exit;
    }

    $name = 'logo_' . time() . '.' . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
    $path = '/uploads/logo/' . $name;
    move_uploaded_file($_FILES['logo']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$path);

    $pdo->prepare("INSERT INTO site_settings (`key`,`value`)
      VALUES ('site_logo',?)
      ON DUPLICATE KEY UPDATE value=VALUES(value)")
      ->execute([$path]);
  }

  /* LOGO HEIGHT */
  if (isset($_POST['logo_height'])) {
    $pdo->prepare("INSERT INTO site_settings (`key`,`value`)
      VALUES ('logo_height',?)
      ON DUPLICATE KEY UPDATE value=VALUES(value)")
      ->execute([(int)$_POST['logo_height']]);
  }

  /* FOOTER CONFIG */
  if (!empty($_POST['footer'])) {
    $pdo->prepare("INSERT INTO site_settings (`key`,`value`)
      VALUES ('footer_config',?)
      ON DUPLICATE KEY UPDATE value=VALUES(value)")
      ->execute([json_encode($_POST['footer'])]);
  }

  echo json_encode(['success'=>true]);
  exit;
}

/* ================= LOAD ================= */
$settings=[];
foreach($pdo->query("SELECT * FROM site_settings") as $s){
  $settings[$s['key']]=$s['value'];
}

function saveSetting($pdo,$k,$v){
  $pdo->prepare("
    INSERT INTO site_settings (`key`,`value`)
    VALUES (?,?)
    ON DUPLICATE KEY UPDATE value=VALUES(value)
  ")->execute([$k,$v]);
}

$menu = json_decode($settings['header_menu_json'] ?? '[]', true);
$footerCols = json_decode($settings['footer_columns_json'] ?? '[]', true);
?>
<!DOCTYPE html>
<html>
<head>
<title>Header & Footer Builder</title>
<link rel="stylesheet" href="/admin/assets/css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<style>
.wrap{display:flex;height:100vh}
.left{width:420px;background:#111827;color:#fff;padding:20px;overflow:auto}
.right{flex:1}
.card{background:#1f2937;padding:14px;border-radius:10px;margin-bottom:12px}
input,textarea{width:100%;padding:8px}
iframe{width:100%;height:100%;border:0}
</style>
</head>

<body>
<div class="wrap">

<!-- LEFT -->
<div class="left">

<h3>Header & Footer CMS</h3>

<div class="card">
<h4>Logo</h4>
<input type="file" id="logo">
<input type="number" id="logo_height" value="<?= $settings['logo_height'] ?? 42 ?>">
</div>

<div class="card">
<h4>Menu (Drag & Drop)</h4>
<div id="menu">
<?php foreach($menu as $m): ?>
<div class="card" data-title="<?= $m['title'] ?>" data-link="<?= $m['link'] ?>">
<?= $m['title'] ?>
</div>
<?php endforeach ?>
</div>
<button onclick="addMenu()">Add Menu</button>
</div>

<div class="card">
<h4>Footer Columns</h4>
<textarea id="footer_cols"><?= json_encode($footerCols,JSON_PRETTY_PRINT) ?></textarea>
</div>

<div class="card">
<h4>Footer Text</h4>
<textarea id="footer_text"><?= $settings['footer_text'] ?? '' ?></textarea>
</div>

<button onclick="saveAll()">Save All</button>

</div>

<!-- RIGHT -->
<div class="right">
<iframe id="preview" src="/"></iframe>
</div>

</div>

<script>
Sortable.create(menu,{animation:150});

function addMenu(){
  const t=prompt('Menu Title'); if(!t)return;
  const l=prompt('Menu Link'); if(!l)return;
  const d=document.createElement('div');
  d.className='card';
  d.dataset.title=t;
  d.dataset.link=l;
  d.innerText=t;
  menu.appendChild(d);
}

function saveAll(){
  const fd=new FormData();
  if(logo.files[0]) fd.append('logo',logo.files[0]);
  fd.append('logo_height',logo_height.value);

  let menuArr=[];
  document.querySelectorAll('#menu .card').forEach(m=>{
    menuArr.push({title:m.dataset.title,link:m.dataset.link});
  });

  fd.append('menu',JSON.stringify(menuArr));
  fd.append('footer_columns',footer_cols.value);
  fd.append('footer_text',footer_text.value);

  fetch('',{method:'POST',body:fd})
    .then(r=>r.json())
    .then(res=>{
      if(res.success){
        preview.contentWindow.location.reload();
        alert('Saved');
      }else alert(res.error);
    });
}
</script>

</body>
</html>
