<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';
require_once '../includes/csrf.php';
require_once '../includes/logger.php';

if (
    !isset($_GET['token']) ||
    !validateCSRFToken($_GET['token'])
) {    die('Invalid CSRF Token');
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("UPDATE users SET status='active' WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    logAction($_SESSION['user_id'], "Activated user ID $id");
}

header('Location: manage_users.php');
exit();
?>