<?php
if(session_status()===PHP_SESSION_NONE) session_start();
if(empty($_SESSION['user']) || $_SESSION['user']['role_id']>2){
  header("Location:/");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin – MMTrips</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/admin/assets/css/admin.css">
<script src="/admin/assets/js/admin.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="layout">
