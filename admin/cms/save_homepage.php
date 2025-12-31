<?php
session_start();
require_once __DIR__."/../../config/database.php";

$data=json_decode(file_get_contents("php://input"),true);

foreach($data as $s){
  $stmt=$pdo->prepare("
    INSERT INTO homepage_sections (section_key,position)
    VALUES (?,?)
    ON DUPLICATE KEY UPDATE position=VALUES(position)
  ");
  $stmt->execute([$s['key'],$s['position']]);
}
