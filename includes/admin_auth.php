<?php

if (session_status() === PHP_SESSION_NONE) {

    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);

    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    ini_set('session.cookie_secure', $isSecure ? 1 : 0);

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'secure'   => $isSecure,
        'samesite' => 'Strict'
    ]);

    session_start();
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$timeout = 1800;

if (isset($_SESSION['LAST_ACTIVITY'])) {
    if ((time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
        session_unset();
        session_destroy();
        header("Location: /cybersecurity-platform/login.php?timeout=1");
        exit();
    }
}

$_SESSION['LAST_ACTIVITY'] = time();

$fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');

if (!isset($_SESSION['fingerprint'])) {
    $_SESSION['fingerprint'] = $fingerprint;
} else {
    if (!hash_equals($_SESSION['fingerprint'], $fingerprint)) {
        session_unset();
        session_destroy();
        header("Location: /cybersecurity-platform/login.php");
        exit();
    }
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /cybersecurity-platform/login.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die("ACCESS DENIED");
}
