<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';
require_once '../includes/csrf.php';
require_once '../includes/logger.php';

if (!isset($_GET['id']) || !isset($_GET['token'])) {
    die('Invalid Request');
}

if (!validateCSRFToken($_GET['token'])) {
    die('Invalid CSRF Token');
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM quizzes WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    logAction($_SESSION['user_id'], "Deleted quiz ID $id");
}

header('Location: add_quiz.php');
exit();
?>