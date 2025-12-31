function openAuth(){
  document.getElementById("authModal").style.display="block";
}

function login(){
  fetch("/api/v1/auth/login.php",{
    method:"POST",
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({
      email:authEmail.value,
      password:authPassword.value
    })
  }).then(r=>r.json()).then(res=>{
    if(res.success) location.reload();
    else authError.innerText = res.error;
  });
}

function showOTP(){
  alert("OTP flow start");
}
