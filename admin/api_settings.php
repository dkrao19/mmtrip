<?php
require "../config/database.php";

if($_SERVER['REQUEST_METHOD']=='POST'){
  foreach($_POST['api'] as $key=>$val){
    $pdo->prepare("
      UPDATE api_settings SET is_enabled=?
      WHERE api_key=?
    ")->execute([$val,$key]);
  }
}

$apis = $pdo->query("SELECT * FROM api_settings")->fetchAll();
?>

<h2>API Settings</h2>

<form method="post">
<table border="1" cellpadding="10">
<tr><th>API</th><th>Status</th></tr>

<?php foreach($apis as $a): ?>
<tr>
<td><?= $a['api_key'] ?></td>
<td>
  <select name="api[<?= $a['api_key'] ?>]">
    <option value="1" <?= $a['is_enabled']?'selected':'' ?>>Enabled</option>
    <option value="0" <?= !$a['is_enabled']?'selected':'' ?>>Disabled</option>
  </select>
</td>
</tr>
<?php endforeach ?>

</table>
<br>
<button type="submit">Save Changes</button>
</form>
