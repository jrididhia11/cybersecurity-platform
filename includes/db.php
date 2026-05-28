<?php

if (isset($conn)) return; // Already connected, do not reconnect

$host     = "localhost";
$user     = "root";
$password = "";
$database = "cybersecurity_platform";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
