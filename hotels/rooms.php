<?php
session_start();
$hotel = $_SESSION['hotel'] ?? null;
if(!$hotel){ header("Location:/hotels/search.php"); exit; }
?>
<!DOCTYPE html>
<html>
<head><title>Select Room</title></head>
<body>

<h2><?= $hotel['name'] ?></h2>

<button onclick="selectRoom('Deluxe',5999)">Deluxe Room – ₹5999</button><br>
<button onclick="selectRoom('Suite',7999)">Suite – ₹7999</button>

<script>
function selectRoom(name,price){
  fetch('/api/v1/session/store_room.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({name,price})
  }).then(()=>location.href='/hotels/review.php');
}
</script>

</body>
</html>
