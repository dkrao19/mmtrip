<?php
session_start();

if (!isset($_SESSION['selected_offer'])) {
  header("Location: /");
  exit;
}

$offer = $_SESSION['selected_offer'];

// pax count from offer or search
$adults = $_SESSION['pax']['a'] ?? 1;
$children = $_SESSION['pax']['c'] ?? 0;
$infants = $_SESSION['pax']['i'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $_SESSION['passengers'] = $_POST['passengers'];
  header("Location: /booking/review.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Passenger Details – MMTrips</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{font-family:Inter,sans-serif;background:#f5f7fb}
.container{max-width:900px;margin:30px auto;background:#fff;padding:20px;border-radius:12px}
.card{border:1px solid #eee;border-radius:10px;padding:15px;margin-bottom:15px}
h3{margin-top:0}
input,select{width:100%;padding:10px;margin:6px 0}
.btn{background:#ff7a00;color:#fff;padding:12px;border:none;border-radius:8px;width:100%;font-weight:600}
</style>
</head>
<body>

<div class="container">
<h2>Passenger Details</h2>
<p><b><?= $offer['origin'] ?> → <?= $offer['destination'] ?></b></p>

<form method="POST">

<?php
$index = 0;
for ($i=1; $i <= $adults; $i++, $index++) {
  echo passengerForm("Adult $i", $index, "ADT");
}
for ($i=1; $i <= $children; $i++, $index++) {
  echo passengerForm("Child $i", $index, "CHD");
}
for ($i=1; $i <= $infants; $i++, $index++) {
  echo passengerForm("Infant $i", $index, "INF");
}

function passengerForm($label, $i, $type){
  return "
  <div class='card'>
    <h3>$label</h3>
    <input name='passengers[$i][type]' value='$type' type='hidden'>
    <input name='passengers[$i][first_name]' placeholder='First Name' required>
    <input name='passengers[$i][last_name]' placeholder='Last Name' required>
    <select name='passengers[$i][gender]' required>
      <option value=''>Gender</option>
      <option>Male</option>
      <option>Female</option>
    </select>
    <input type='date' name='passengers[$i][dob]' required>
    <input name='passengers[$i][nationality]' placeholder='Nationality'>
    <input name='passengers[$i][passport]' placeholder='Passport Number'>
  </div>";
}
?>

<button class="btn">Continue to Review</button>
</form>
</div>

</body>
</html>
