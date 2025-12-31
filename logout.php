<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* SAFE SESSION START */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* CLEAR SESSION */
$_SESSION = [];

/* DESTROY SESSION */
session_destroy();

/* DELETE SESSION COOKIE (IMPORTANT) */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* REDIRECT TO HOME */
header("Location: /");
exit;
