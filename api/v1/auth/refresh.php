use Firebase\JWT\JWT;

$data = json_decode(file_get_contents("php://input"),true);
$refresh = $data['refresh_token'];

$stmt = $pdo->prepare("
SELECT * FROM user_tokens
WHERE refresh_token=? AND expires_at>NOW()
");
$stmt->execute([$refresh]);
$token = $stmt->fetch();

if(!$token){
  http_response_code(401);
  exit;
}

$newAccess = JWT::encode([
  'uid'=>$token['user_id'],
  'exp'=>time()+ACCESS_TOKEN_TTL
], JWT_SECRET, 'HS256');

echo json_encode(['access_token'=>$newAccess]);
