<?php

include 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT fullname, email, xp
FROM users
WHERE id=?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$xp = $user['xp'] ?? 0;

$level = floor($xp / 500) + 1;

$progress = ($xp % 500) / 5;

$badges = [];

if($xp >= 100){
    $badges[] = [
        "icon" => "fa-shield-halved",
        "title" => "Security Rookie"
    ];
}

if($xp >= 500){
    $badges[] = [
        "icon" => "fa-user-secret",
        "title" => "Cyber Agent"
    ];
}

if($xp >= 1000){
    $badges[] = [
        "icon" => "fa-trophy",
        "title" => "Elite Defender"
    ];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cyber Profile</title>

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

    font-family:'Inter',sans-serif;

    overflow-x:hidden;
}

.profile-container{

    max-width:1100px;

    margin:auto;

    padding:45px 20px;
}

/* HERO */

.profile-hero{

    background:
    linear-gradient(135deg,#0f172a,#1e293b);

    border-radius:34px;

    padding:50px;

    border:1px solid rgba(255,255,255,0.05);

    box-shadow:
    0 15px 50px rgba(0,0,0,0.45);

    position:relative;

    overflow:hidden;

    margin-bottom:35px;
}

.profile-hero::before{

    content:'';

    position:absolute;

    width:350px;
    height:350px;

    background:#2563eb22;

    border-radius:50%;

    top:-150px;
    right:-150px;

    filter:blur(60px);
}

.profile-avatar{

    width:140px;
    height:140px;

    border-radius:50%;

    margin:auto;

    display:flex;

    align-items:center;

    justify-content:center;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    font-size:60px;

    box-shadow:
    0 0 40px rgba(37,99,235,0.35);

    position:relative;

    z-index:2;
}

.profile-name{

    font-size:46px;

    font-weight:700;

    margin-top:30px;

    margin-bottom:10px;

    position:relative;

    z-index:2;
}

.profile-email{

    color:#94a3b8;

    font-size:18px;

    position:relative;

    z-index:2;
}

/* STATS */

.stats-card{

    background:rgba(15,23,42,0.92);

    border-radius:28px;

    padding:30px;

    border:1px solid rgba(255,255,255,0.05);

    height:100%;

    transition:0.35s ease;

    box-shadow:
    0 10px 35px rgba(0,0,0,0.25);
}

.stats-card:hover{

    transform:translateY(-6px);

    border-color:#2563eb55;
}

.stats-icon{

    width:70px;
    height:70px;

    border-radius:20px;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:26px;

    margin-bottom:22px;
}

.stats-title{

    color:#94a3b8;

    margin-bottom:12px;

    font-size:16px;
}

.stats-value{

    font-size:42px;

    font-weight:700;
}

/* PROGRESS */

.progress-card{

    margin-top:35px;

    background:rgba(15,23,42,0.92);

    border-radius:30px;

    padding:35px;

    border:1px solid rgba(255,255,255,0.05);
}

.progress-title{

    font-size:26px;

    font-weight:700;

    margin-bottom:20px;
}

.progress{

    height:24px;

    border-radius:30px;

    overflow:hidden;

    background:#111827;
}

.progress-bar{

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    font-weight:600;
}

/* BADGES */

.badges-card{

    margin-top:35px;

    background:rgba(15,23,42,0.92);

    border-radius:30px;

    padding:35px;

    border:1px solid rgba(255,255,255,0.05);
}

.badges-title{

    font-size:26px;

    font-weight:700;

    margin-bottom:25px;
}

.badge-box{

    background:#111827;

    border-radius:22px;

    padding:25px;

    text-align:center;

    border:1px solid rgba(255,255,255,0.05);

    transition:0.3s;
}

.badge-box:hover{

    transform:translateY(-5px);

    border-color:#2563eb55;
}

.badge-icon{

    width:70px;
    height:70px;

    margin:auto;

    border-radius:20px;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:28px;

    margin-bottom:18px;
}

.badge-title{

    font-weight:600;

    font-size:18px;
}

/* BUTTON */

.back-btn{

    width:100%;

    margin-top:35px;

    padding:18px;

    border-radius:20px;

    text-decoration:none;

    color:white;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    display:flex;

    align-items:center;

    justify-content:center;

    gap:12px;

    font-weight:600;

    font-size:18px;

    transition:0.3s;
}

.back-btn:hover{

    transform:translateY(-4px);

    box-shadow:
    0 15px 35px rgba(37,99,235,0.35);

    color:white;
}

/* RESPONSIVE */

@media(max-width:768px){

    .profile-hero{
        padding:35px 25px;
    }

    .profile-name{
        font-size:34px;
    }

    .stats-value{
        font-size:32px;
    }
}

</style>

</head>

<body>

<div class="profile-container">

<!-- HERO -->

<div class="profile-hero text-center">

<div class="profile-avatar">

<i class="fa-solid fa-user-secret"></i>

</div>

<h1 class="profile-name">

<?= htmlspecialchars($user['fullname']) ?>

</h1>

<p class="profile-email">

<?= htmlspecialchars($user['email']) ?>

</p>

</div>

<!-- STATS -->

<div class="row g-4">

<div class="col-md-6">

<div class="stats-card">

<div class="stats-icon">

<i class="fa-solid fa-layer-group"></i>

</div>

<div class="stats-title">

Current Level

</div>

<div class="stats-value">

<?= $level ?>

</div>

</div>

</div>

<div class="col-md-6">

<div class="stats-card">

<div class="stats-icon">

<i class="fa-solid fa-bolt"></i>

</div>

<div class="stats-title">

Total XP

</div>

<div class="stats-value">

<?= $xp ?>

</div>

</div>

</div>

</div>

<!-- PROGRESS -->

<div class="progress-card">

<h3 class="progress-title">

Learning Progress

</h3>

<div class="progress">

<div
class="progress-bar"
style="width:<?= $progress ?>%">

<?= round($progress) ?>%

</div>

</div>

</div>

<!-- BADGES -->

<div class="badges-card">

<h3 class="badges-title">

Achievements

</h3>

<div class="row g-4">

<?php if(count($badges) > 0): ?>

<?php foreach($badges as $badge): ?>

<div class="col-md-4">

<div class="badge-box">

<div class="badge-icon">

<i class="fa-solid <?= $badge['icon'] ?>"></i>

</div>

<div class="badge-title">

<?= $badge['title'] ?>

</div>

</div>

</div>

<?php endforeach; ?>

<?php else: ?>

<div class="col-12 text-center text-secondary">

No achievements unlocked yet

</div>

<?php endif; ?>

</div>

</div>

<a href="dashboard.php" class="back-btn">

<i class="fa-solid fa-arrow-left"></i>

Back to Dashboard

</a>

</div>


<?php require_once 'includes/chatbot_widget.php'; ?>
</body>
</html>