<?php
require_once __DIR__."/../config/database.php";

$rows = $pdo->query("
  SELECT * FROM refunds 
  WHERE status='FAILED' AND retry_count < 3
")->fetchAll();

foreach($rows as $r){
  $pdo->prepare("
    UPDATE refunds SET retry_count=retry_count+1,status='PENDING'
    WHERE id=?
  ")->execute([$r['id']]);
}
