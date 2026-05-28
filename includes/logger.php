<?php
require_once 'db.php';

function logAction($user_id, $action) {

    global $conn;

    $ip = $_SERVER['REMOTE_ADDR'];

    $stmt = $conn->prepare("INSERT INTO logs(user_id, action, ip_address) VALUES(?, ?, ?)");

    $stmt->bind_param("iss", $user_id, $action, $ip);

    $stmt->execute();
}
?>