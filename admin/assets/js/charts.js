fetch('/admin/pages/api_health_data.php')
.then(r=>r.json())
.then(d=>{
 new Chart(document.getElementById('healthChart'),{
  type:'bar',
  data:{labels:d.labels,datasets:[{label:'Failures',data:d.failures}]}
 });
});
