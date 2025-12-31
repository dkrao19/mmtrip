<?php
session_start();
$hotel=$_SESSION['hotel'];
$room=$_SESSION['room'];
?>
<h2>🏨 Hotel Booking Confirmed</h2>
Hotel: <?= $hotel['name'] ?><br>
Room: <?= $room['name'] ?><br>
Voucher sent to email.
