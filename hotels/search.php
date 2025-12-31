<!DOCTYPE html>
<html>
<head>
<title>Search Hotels – MMTrips</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{font-family:Inter;background:#f5f7fb}
.container{max-width:1100px;margin:30px auto}
.card{background:#fff;padding:16px;border-radius:12px;margin-bottom:14px;
display:grid;grid-template-columns:150px 2fr 1fr;gap:14px}
img{width:100%;border-radius:8px}
.price{font-size:20px;font-weight:700}
.btn{background:#ff7a00;color:#fff;padding:10px;border-radius:8px;border:none}
</style>
</head>
<body>

<div class="container">
<h2>Search Hotels</h2>

<input id="city" placeholder="City"><br><br>
<input type="date" id="checkin">
<input type="date" id="checkout"><br><br>

<button onclick="searchHotels()">Search</button>

<div id="results"></div>
</div>

<script>
function searchHotels(){
  const city=document.getElementById('city').value;
  const ci=document.getElementById('checkin').value;
  const co=document.getElementById('checkout').value;

  fetch(`/api/v1/hotels/search.php?city=${city}&checkin=${ci}&checkout=${co}`)
  .then(r=>r.json())
  .then(list=>{
    const res=document.getElementById('results');
    res.innerHTML='';
    list.forEach(h=>{
      res.innerHTML+=`
      <div class="card">
        <img src="${h.image}">
        <div>
          <b>${h.name}</b><br>
          ⭐ ${h.rating}<br>
          ${h.refundable?'Refundable':'Non-refundable'}
        </div>
        <div>
          <div class="price">₹${h.price}</div>
          <button class="btn" onclick='selectHotel(${JSON.stringify(h)})'>
            Select
          </button>
        </div>
      </div>`;
    });
  });
}

function selectHotel(h){
  fetch('/api/v1/session/store_hotel.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify(h)
  }).then(()=>location.href='/hotels/rooms.php');
}
</script>

</body>
</html>
