<?php
session_start();
if($_SESSION['user']['role_id']!=3) die("Access denied");
?>

<h2>Agent Dashboard</h2>

<ul>
  <li><a href="bookings.php">My Bookings</a></li>
  <li><a href="ledger.php">Ledger & Credit</a></li>
</ul>
