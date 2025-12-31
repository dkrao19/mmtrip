function searchFlights(){
  const form = new FormData(document.getElementById("searchForm"));

  fetch("/api/flights/search.php",{
    method:"POST",
    body:form
  })
  .then(r=>r.json())
  .then(data=>{
    renderFlights(data);
  });
}

function book(f){
 fetch('/api/v1/session/store_offer.php',{
  method:'POST',
  headers:{'Content-Type':'application/json'},
  body:JSON.stringify(f)
 }).then(()=>location.href='/booking/passengers.php');
}


let allFlights = [];

function applyFilters(){
  const max = priceMax.value;
  const airline = airlineFilter.value;
  const time = timeFilter.value;

  const filtered = allFlights.filter(f=>{
    if(f.price > max) return false;
    if(airline && f.airline !== airline) return false;

    if(time){
      const h = new Date(f.departure_time).getHours();
      if(time === 'morning' && (h < 5 || h > 12)) return false;
      if(time === 'evening' && (h < 17 || h > 22)) return false;
    }
    return true;
  });

  renderFlights(filtered);
}

allFlights = data;
document.getElementById('filters').style.display = 'block';
renderFlights(data);

