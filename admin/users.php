<?php
require "../config/database.php";
$users=$pdo->query("SELECT * FROM users")->fetchAll();
?>

<table border="1">
<tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>
<?php foreach($users as $u): ?>
<tr>
<td><?= $u['name'] ?></td>
<td><?= $u['email'] ?></td>
<td><?= $u['role_id'] ?></td>
<td><?= $u['is_active']?'Active':'Disabled' ?></td>
</tr>
<?php endforeach ?>
</table>
