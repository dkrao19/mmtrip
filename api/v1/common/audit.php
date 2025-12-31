<?php
require_once __DIR__."/../../config/database.php";

function auditLog($userId,$action,$ref,$meta=[]){
  global $pdo;

  $stmt = $pdo->prepare("
    INSERT INTO audit_logs
    (user_id, action, reference, ip_address, metadata)
    VALUES (?, ?, ?, ?, ?)
  ");
  $stmt->execute([
    $userId,
    $action,
    $ref,
    $_SERVER['REMOTE_ADDR'] ?? '',
    json_encode($meta)
  ]);
}
