<?php

require_once '../includes/admin_auth.php';
require_once '../config/db.php';

$totalUsers = 0;
$totalQuizzes = 0;
$totalResults = 0;

$userQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
if($userQuery){
    $totalUsers = mysqli_fetch_assoc($userQuery)['total'];
}

$quizQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM quizzes");
if($quizQuery){
    $totalQuizzes = mysqli_fetch_assoc($quizQuery)['total'];
}

$resultQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM results");
if($resultQuery){
    $totalResults = mysqli_fetch_assoc($resultQuery)['total'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cyber Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    background:
    radial-gradient(circle at top left,#2563eb22,transparent 25%),
    radial-gradient(circle at bottom right,#3b82f622,transparent 25%),
    #020617;

    color:white;

    min-height:100vh;

    overflow-x:hidden;

    font-family:'Inter',sans-serif;
}

/* SIDEBAR */

.sidebar{

    position:fixed;

    left:0;
    top:0;

    width:280px;

    height:100vh;

    background:rgba(15,23,42,0.92);

    border-right:1px solid rgba(255,255,255,0.05);

    backdrop-filter:blur(18px);

    padding:30px 20px;

    z-index:1000;
}

.logo{

    font-size:32px;

    font-weight:700;

    margin-bottom:50px;
}

.logo span{
    color:#3b82f6;
}

.sidebar a{

    display:flex;

    align-items:center;

    gap:14px;

    text-decoration:none;

    color:#cbd5e1;

    padding:16px 18px;

    border-radius:18px;

    margin-bottom:15px;

    transition:0.3s ease;
}

.sidebar a:hover{

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    color:white;

    transform:translateX(5px);
}

/* MAIN */

.main-content{

    margin-left:280px;

    padding:40px;
}

/* HERO */

.hero-card{

    background:
    linear-gradient(135deg,#0f172a,#1e293b);

    border-radius:32px;

    padding:45px;

    border:1px solid rgba(255,255,255,0.05);

    margin-bottom:35px;

    position:relative;

    overflow:hidden;

    box-shadow:
    0 0 30px rgba(37,99,235,0.08),
    0 10px 40px rgba(0,0,0,0.45);
}

.hero-card::before{

    content:'';

    position:absolute;

    width:320px;
    height:320px;

    background:#2563eb22;

    border-radius:50%;

    top:-120px;
    right:-120px;

    filter:blur(50px);
}

.hero-title{

    font-size:46px;

    font-weight:700;

    margin-bottom:12px;

    position:relative;

    z-index:2;
}

.hero-subtitle{

    color:#94a3b8;

    font-size:18px;

    position:relative;

    z-index:2;
}

/* STATS */

.stats-card{

    background:rgba(15,23,42,0.92);

    border-radius:28px;

    padding:32px;

    border:1px solid rgba(255,255,255,0.05);

    transition:0.35s ease;

    height:100%;
}

.stats-card:hover{

    transform:translateY(-8px);

    border-color:rgba(59,130,246,0.35);

    box-shadow:0 0 35px rgba(37,99,235,0.12);
}

.stats-icon{

    width:75px;
    height:75px;

    border-radius:22px;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:30px;

    margin-bottom:22px;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);
}

.stats-title{

    color:#94a3b8;

    margin-bottom:12px;

    font-size:16px;
}

.stats-number{

    font-size:44px;

    font-weight:700;
}

/* SECTION */

.section-card{

    background:rgba(15,23,42,0.92);

    border-radius:30px;

    padding:35px;

    margin-top:35px;

    border:1px solid rgba(255,255,255,0.05);
}

.section-title{

    font-size:30px;

    font-weight:700;

    margin-bottom:30px;
}

/* ACTION BUTTONS */

.action-btn{

    display:flex;

    align-items:center;

    justify-content:center;

    gap:14px;

    width:100%;

    padding:20px;

    border-radius:20px;

    text-decoration:none;

    color:white;

    font-weight:600;

    font-size:17px;

    transition:0.3s ease;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    box-shadow:
    0 10px 25px rgba(37,99,235,0.25);
}

.action-btn:hover{

    transform:translateY(-5px);

    box-shadow:
    0 15px 35px rgba(37,99,235,0.4);

    color:white;
}

/* ACTIVITY */

.activity-item{

    display:flex;

    align-items:center;

    gap:16px;

    padding:18px;

    border-radius:18px;

    background:#0f172a;

    margin-bottom:16px;

    border:1px solid rgba(255,255,255,0.04);
}

.activity-icon{

    width:55px;
    height:55px;

    border-radius:16px;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:20px;
}

.activity-title{

    font-weight:600;

    margin-bottom:4px;
}

.activity-text{

    color:#94a3b8;

    font-size:14px;
}

/* RESPONSIVE */

@media(max-width:992px){

    .sidebar{
        width:100%;
        height:auto;
        position:relative;
    }

    .main-content{
        margin-left:0;
        padding:25px;
    }

    .hero-title{
        font-size:34px;
    }
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

<div class="logo">
Cyber<span>Admin</span>
</div>

<a href="admin_dashboard.php">
<i class="fa-solid fa-chart-line"></i>
Dashboard
</a>

<a href="manage_users.php">
<i class="fa-solid fa-users"></i>
Manage Users
</a>

<a href="add_quiz.php">
<i class="fa-solid fa-circle-plus"></i>
Add Quiz
</a>

<a href="view_results.php">
<i class="fa-solid fa-chart-column"></i>
Results
</a>

<a href="../dashboard.php">
<i class="fa-solid fa-house"></i>
Main Dashboard
</a>

<a href="../logout.php">
<i class="fa-solid fa-right-from-bracket"></i>
Logout
</a>

</div>

<!-- MAIN -->

<div class="main-content">

<!-- HERO -->

<div class="hero-card">

<h1 class="hero-title">
Security Control Center
</h1>

<p class="hero-subtitle">
Manage users, quizzes and monitor cybersecurity platform activity
</p>

</div>

<!-- STATS -->

<div class="row g-4">

<div class="col-lg-4 col-md-6">

<div class="stats-card">

<div class="stats-icon">
<i class="fa-solid fa-users"></i>
</div>

<div class="stats-title">
Total Users
</div>

<div class="stats-number">
<?= $totalUsers ?>
</div>

</div>

</div>

<div class="col-lg-4 col-md-6">

<div class="stats-card">

<div class="stats-icon">
<i class="fa-solid fa-clipboard-question"></i>
</div>

<div class="stats-title">
Total Quizzes
</div>

<div class="stats-number">
<?= $totalQuizzes ?>
</div>

</div>

</div>

<div class="col-lg-4 col-md-12">

<div class="stats-card">

<div class="stats-icon">
<i class="fa-solid fa-chart-column"></i>
</div>

<div class="stats-title">
Total Results
</div>

<div class="stats-number">
<?= $totalResults ?>
</div>

</div>

</div>

</div>

<!-- QUICK ACTIONS -->

<div class="section-card">

<h3 class="section-title">
Quick Actions
</h3>

<div class="row g-4">

<div class="col-md-6">

<a href="manage_users.php" class="action-btn">

<i class="fa-solid fa-users"></i>

Manage Users

</a>

</div>

<div class="col-md-6">

<a href="add_quiz.php" class="action-btn">

<i class="fa-solid fa-circle-plus"></i>

Add Quiz

</a>

</div>

<div class="col-md-6">

<a href="view_results.php" class="action-btn">

<i class="fa-solid fa-chart-column"></i>

View Results

</a>

</div>

<div class="col-md-6">

<a href="../dashboard.php" class="action-btn">

<i class="fa-solid fa-house"></i>

Main Dashboard

</a>

</div>

</div>

</div>

<!-- ACTIVITY -->

<div class="section-card">

<h3 class="section-title">
Recent Activity
</h3>

<div class="activity-item">

<div class="activity-icon">
<i class="fa-solid fa-user-plus"></i>
</div>

<div>

<div class="activity-title">
New users registered
</div>

<div class="activity-text">
Platform activity is being monitored in real time
</div>

</div>

</div>

<div class="activity-item">

<div class="activity-icon">
<i class="fa-solid fa-shield-halved"></i>
</div>

<div>

<div class="activity-title">
Security systems active
</div>

<div class="activity-text">
Authentication and protection layers are operational
</div>

</div>

</div>

<div class="activity-item">

<div class="activity-icon">
<i class="fa-solid fa-chart-line"></i>
</div>

<div>

<div class="activity-title">
Learning statistics updated
</div>

<div class="activity-text">
Quiz and results metrics refreshed successfully
</div>

</div>

</div>

</div>

</div>

</body>
</html>