<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';
require_once '../includes/csrf.php';
require_once '../includes/logger.php';

if (!validateCSRFToken($_GET['token'])) {
    die('Invalid CSRF Token');
}

$id = intval($_GET['id']);
$role = $_GET['role'];

$allowed_roles = ['admin', 'user'];

if (!in_array($role, $allowed_roles)) {
    die('Invalid role');
}

$stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
$stmt->bind_param("si", $role, $id);

if ($stmt->execute()) {
    logAction($_SESSION['user_id'], "Changed role for user ID $id to $role");
}

header('Location: manage_users.php');
exit();
?>