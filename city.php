<?php
$city = htmlspecialchars($_GET['city']);
?>
<!DOCTYPE html>
<html>
<head>
<title>Cheap Flights & Hotels in <?= $city ?> | MMTrips</title>
<meta name="description" content="Book cheap flights and hotels in <?= $city ?> with MMTrips.">
</head>
<body>

<h1>Travel to <?= $city ?></h1>

<p>Book flights, hotels, holiday packages and activities in <?= $city ?>.</p>

<ul>
  <li>Flights to <?= $city ?></li>
  <li>Hotels in <?= $city ?></li>
  <li>Holiday Packages</li>
</ul>

</body>
</html>
