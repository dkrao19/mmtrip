<?php
require "../config/database.php";

if($_POST){
  $pdo->prepare("
    UPDATE markup_rules
    SET platform_markup=?, agent_markup=?, corp_discount=?
  ")->execute([
    $_POST['platform'],
    $_POST['agent'],
    $_POST['corp']
  ]);
}

$r = $pdo->query("SELECT * FROM markup_rules LIMIT 1")->fetch();
?>

<h2>Markup Rules</h2>
<form method="post">
Platform Markup <input name="platform" value="<?= $r['platform_markup'] ?>"><br>
Agent Markup <input name="agent" value="<?= $r['agent_markup'] ?>"><br>
Corporate Discount <input name="corp" value="<?= $r['corp_discount'] ?>"><br>
<button>Save</button>
</form>
