<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../config/database.php";

header('Content-Type: application/json');

/* =========================
   READ INPUT (JSON + POST)
========================= */
$raw = file_get_contents("php://input");
$input = json_decode($raw, true);
if (!is_array($input)) {
    $input = $_POST;
}

$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Email / Mobile and password required'
    ]);
    exit;
}

/* =========================
   FETCH USER (FIXED QUERY)
========================= */
$sql = "
    SELECT *
    FROM users
    WHERE email = :email
       OR mobile = :mobile
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':email'  => $email,
    ':mobile' => $email
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid credentials'
    ]);
    exit;
}

/* =========================
   CHECK ACTIVE
========================= */
if ((int)$user['is_active'] !== 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Account disabled'
    ]);
    exit;
}

/* =========================
   VERIFY PASSWORD
========================= */
if (!password_verify($password, $user['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid credentials'
    ]);
    exit;
}

/* =========================
   LOGIN SUCCESS
========================= */
$_SESSION['user'] = [
    'id'       => $user['id'],
    'role_id'  => $user['role_id'],
    'name'     => $user['name'],
    'email'    => $user['email'],
    'mobile'   => $user['mobile'],
    'agent_id' => $user['agent_id'] ?? null
];

session_regenerate_id(true);

echo json_encode([
    'success' => true,
    'role_id' => (int)$user['role_id']
]);
exit;
