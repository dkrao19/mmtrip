<?php
function isApiEnabled($key){
  static $cache = [];

  if(isset($cache[$key])) return $cache[$key];

  require __DIR__."/database.php";

  $stmt = $pdo->prepare("
    SELECT is_enabled FROM api_settings WHERE api_key=?
  ");
  $stmt->execute([$key]);

  $enabled = (bool) $stmt->fetchColumn();
  $cache[$key] = $enabled;

  return $enabled;
}
