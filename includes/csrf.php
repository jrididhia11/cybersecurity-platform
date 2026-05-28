<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generateCSRFToken()
{
    // ALWAYS generate token if missing
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token)
{
    // Check token exists
    if (
        !isset($_SESSION['csrf_token']) ||
        empty($token)
    ) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

function regenerateCSRFToken()
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrfInput()
{
    return '
        <input type="hidden" name="csrf_token" value="' .
        htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8') .
        '">
    ';
}
?>