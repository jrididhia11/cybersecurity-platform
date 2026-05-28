<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/logger.php';

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");

if (isset($_SESSION['user_id'])) {
    logAction($_SESSION['user_id'], 'Logged out');
}

$_SESSION = [];

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

session_destroy();

header('Location: login.php');
exit();
?>