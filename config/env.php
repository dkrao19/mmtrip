<?php
/*
 | Load environment variables safely
 | Works on shared hosting + VPS + Docker
*/

function env($key, $default = null) {
    if (isset($_ENV[$key])) return $_ENV[$key];
    if (isset($_SERVER[$key])) return $_SERVER[$key];
    return $default;
}
