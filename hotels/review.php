<?php
session_start();
$hotel=$_SESSION['hotel'];
$room=$_SESSION['room'];
?>
<h2>Review Hotel Booking</h2>

<?= $hotel['name'] ?><br>
Room: <?= $room['name'] ?><br>
Price: ₹<?= $room['price'] ?><br>

<button onclick="location.href='/booking/review.php'">
Pay Now
</button>
