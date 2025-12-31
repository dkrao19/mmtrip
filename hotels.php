<div class="search-box">
  <div class="grid">
    <input id="city" placeholder="City (Bangkok)">
    <input type="date" id="checkin">
    <input type="date" id="checkout">
    <button onclick="searchHotels()">Search Hotels</button>
  </div>
</div>

<div id="hotelResults"></div>

<script>
function searchHotels(){
  const p = new URLSearchParams({
    city: city.value,
    checkin: checkin.value,
    checkout: checkout.value
  });

  fetch('/api/v1/hotels/search.php?'+p)
    .then(r=>r.json())
    .then(d=>{
      hotelResults.innerHTML='';
      d.forEach(h=>{
        hotelResults.innerHTML+=`
        <div class="flight">
          <div>
            <b>${h.hotel_name}</b><br>
            ⭐ ${h.star_rating}<br>
            ${h.room_type} - ${h.meal_plan}
          </div>
          <div>
            <div class="price">₹${h.price}</div>
            <button>Book</button>
          </div>
        </div>`;
      });
    });
}
</script>
