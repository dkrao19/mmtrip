use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function auth(){
  $h = getallheaders();
  if(!isset($h['Authorization'])) abort(401);

  $token = str_replace("Bearer ","",$h['Authorization']);
  return JWT::decode($token,new Key(JWT_SECRET,'HS256'));
}
