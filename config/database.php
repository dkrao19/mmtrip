<?php
/**
 * MMTRIPS - Database Configuration
 * Used by:
 *  - B2C Website
 *  - Admin Dashboard
 *  - API (v1)
 *  - Flutter App APIs
 *
 * PHP 8+
 */

/* =====================================
   ENVIRONMENT
===================================== */

define('APP_ENV', 'production'); // local | staging | production

/* =====================================
   DATABASE SETTINGS
===================================== */

define('DB_HOST', 'localhost');
define('DB_NAME', 'impokpxc_mmtripscom');
define('DB_USER', 'impokpxc_ys1');     // CHANGE
define('DB_PASS', 'luckyLUCKY@123');     // CHANGE
define('DB_CHARSET', 'utf8mb4');

/* =====================================
   JWT / SECURITY
===================================== */

define('JWT_SECRET', 'CHANGE_THIS_TO_LONG_RANDOM_SECRET');
define('ACCESS_TOKEN_TTL', 900);        // 15 minutes
define('REFRESH_TOKEN_TTL', 2592000);   // 30 days

/* =====================================
   TIMEZONE
===================================== */

date_default_timezone_set('Asia/Yangon');

/* =====================================
   PDO CONNECTION
===================================== */

$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
  PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".DB_CHARSET
];

try {
  $pdo = new PDO(
    "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
    DB_USER,
    DB_PASS,
    $options
  );
} catch (PDOException $e) {
  if (APP_ENV !== 'production') {
    die("❌ Database Connection Failed: " . $e->getMessage());
  } else {
    error_log($e->getMessage());
    http_response_code(500);
    die("Database connection error");
  }
}

/* =====================================
   COMMON HELPER FUNCTIONS
===================================== */

/**
 * Safe JSON response
 */
function jsonResponse($data, int $code = 200): void {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}

/**
 * Get logged-in user (Session)
 */
function currentUser(): ?array {
  return $_SESSION['user'] ?? null;
}

/**
 * Require login (Session-based)
 */
function requireLogin(): void {
  if (!currentUser()) {
    http_response_code(401);
    exit('Unauthorized');
  }
}

/**
 * Require role (Admin / Agent)
 */
function requireRole(array $roles): void {
  $user = currentUser();
  if (!$user || !in_array($user['role_id'], $roles)) {
    http_response_code(403);
    exit('Forbidden');
  }
}

/* =====================================
   API FEATURE FLAG CHECK
===================================== */

function isApiEnabled(string $apiKey): bool {
  global $pdo;
  static $cache = [];

  if (isset($cache[$apiKey])) {
    return $cache[$apiKey];
  }

  $stmt = $pdo->prepare("
    SELECT is_enabled
    FROM api_settings
    WHERE api_key = ?
    LIMIT 1
  ");
  $stmt->execute([$apiKey]);

  $enabled = (bool) $stmt->fetchColumn();
  $cache[$apiKey] = $enabled;

  return $enabled;
}

/* =====================================
   API LOGGING (USAGE + COST)
===================================== */

function logApiUsage(
  string $apiKey,
  string $endpoint,
  int $responseMs,
  bool $success,
  ?string $error = null
): void {
  global $pdo;

  $pdo->prepare("
    INSERT INTO api_usage_logs
    (api_key, endpoint, response_time_ms, success, error_message)
    VALUES (?,?,?,?,?)
  ")->execute([
    $apiKey,
    $endpoint,
    $responseMs,
    $success ? 1 : 0,
    $error
  ]);
}

/* =====================================
   CIRCUIT BREAKER
===================================== */

function isCircuitOpen(string $apiKey): bool {
  global $pdo;

  $stmt = $pdo->prepare("
    SELECT state
    FROM api_circuit_breaker
    WHERE api_key=?
  ");
  $stmt->execute([$apiKey]);

  return $stmt->fetchColumn() === 'OPEN';
}

function registerApiSuccess(string $apiKey): void {
  global $pdo;

  $pdo->prepare("
    UPDATE api_circuit_breaker
    SET failure_count=0, state='CLOSED'
    WHERE api_key=?
  ")->execute([$apiKey]);
}

function registerApiFailure(string $apiKey): void {
  global $pdo;

  $pdo->prepare("
    INSERT INTO api_circuit_breaker
      (api_key, failure_count, state, last_failure)
    VALUES (?,1,'OPEN',NOW())
    ON DUPLICATE KEY UPDATE
      failure_count = failure_count + 1,
      state = IF(failure_count + 1 >= 3, 'OPEN', 'HALF_OPEN'),
      last_failure = NOW()
  ")->execute([$apiKey]);
}

/* =====================================
   COST TRACKING
===================================== */

function logApiCost(string $apiKey): void {
  global $pdo;

  $stmt = $pdo->prepare("
    SELECT cost_per_call
    FROM api_costs
    WHERE api_key=?
  ");
  $stmt->execute([$apiKey]);

  $cost = (float) $stmt->fetchColumn();

  if ($cost > 0) {
    $pdo->prepare("
      INSERT INTO api_cost_logs (api_key, cost)
      VALUES (?,?)
    ")->execute([$apiKey, $cost]);
  }
}

/* =====================================
   END OF FILE
===================================== */

