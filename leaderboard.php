<?php

include 'includes/auth.php';
include 'config/db.php';
require_once 'includes/xp_system.php';

$currentUser = $_SESSION['fullname'];

$result = mysqli_query($conn,
    "SELECT fullname, xp, level
     FROM users
     ORDER BY xp DESC
     LIMIT 10"
);

$leaders = [];

while($row = mysqli_fetch_assoc($result)){
    $leaders[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cyber Leaderboard</title>

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

/* CONTAINER */

.leaderboard-container{

    max-width:1200px;

    margin:auto;

    padding:40px 20px;
}

/* HERO */

.hero-card{

    background:
    linear-gradient(135deg,#0f172a,#1e293b);

    border-radius:32px;

    padding:45px;

    margin-bottom:40px;

    border:1px solid rgba(255,255,255,0.05);

    position:relative;

    overflow:hidden;

    box-shadow:
    0 10px 40px rgba(0,0,0,0.4);
}

.hero-card::before{

    content:'';

    position:absolute;

    width:350px;
    height:350px;

    background:#f59e0b22;

    border-radius:50%;

    top:-140px;
    right:-140px;

    filter:blur(60px);
}

.hero-title{

    font-size:48px;

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

/* PODIUM */

.podium-row{

    margin-bottom:40px;
}

.podium-card{

    background:rgba(15,23,42,0.92);

    border-radius:28px;

    padding:30px;

    text-align:center;

    border:1px solid rgba(255,255,255,0.05);

    transition:0.35s ease;

    height:100%;
}

.podium-card:hover{

    transform:translateY(-8px);

    box-shadow:
    0 0 35px rgba(37,99,235,0.12);
}

.first-place{

    border:1px solid rgba(245,158,11,0.4);

    box-shadow:
    0 0 40px rgba(245,158,11,0.15);
}

.second-place{

    border:1px solid rgba(148,163,184,0.3);
}

.third-place{

    border:1px solid rgba(180,83,9,0.3);
}

.podium-rank{

    width:70px;
    height:70px;

    margin:auto;

    border-radius:22px;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:28px;

    font-weight:700;

    margin-bottom:20px;
}

.rank-1{
    background:linear-gradient(135deg,#f59e0b,#fbbf24);
    color:#111827;
}

.rank-2{
    background:linear-gradient(135deg,#94a3b8,#cbd5e1);
    color:#111827;
}

.rank-3{
    background:linear-gradient(135deg,#b45309,#d97706);
}

.podium-name{

    font-size:22px;

    font-weight:700;

    margin-bottom:12px;
}

.podium-xp{

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    padding:10px 18px;

    border-radius:14px;

    display:inline-block;

    font-weight:600;

    margin-bottom:12px;
}

.podium-level{

    color:#94a3b8;
}

/* TABLE */

.leaderboard-card{

    background:rgba(15,23,42,0.92);

    border-radius:30px;

    padding:30px;

    border:1px solid rgba(255,255,255,0.05);
}

.table{

    margin-bottom:0;
}

.table th{

    border:none !important;

    color:#94a3b8 !important;

    padding:20px !important;

    font-weight:600;
}

.table td{

    padding:20px !important;

    border-color:rgba(255,255,255,0.04) !important;

    vertical-align:middle;
}

.rank-badge{

    width:50px;
    height:50px;

    border-radius:16px;

    background:#1e293b;

    display:flex;

    align-items:center;

    justify-content:center;

    font-weight:700;
}

.user-row{

    transition:0.3s ease;
}

.user-row:hover{

    background:rgba(37,99,235,0.08);
}

.current-user{

    border:1px solid rgba(37,99,235,0.3);

    box-shadow:
    inset 0 0 20px rgba(37,99,235,0.08);
}

.user-name{

    font-weight:600;

    font-size:17px;
}

.xp-badge{

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    padding:10px 16px;

    border-radius:12px;

    display:inline-block;

    font-weight:600;
}

.level-badge{

    background:#1e293b;

    padding:10px 16px;

    border-radius:12px;

    display:inline-block;
}

/* BUTTON */

.back-btn{

    display:flex;

    align-items:center;

    justify-content:center;

    gap:12px;

    width:100%;

    margin-top:30px;

    padding:16px;

    border-radius:18px;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    text-decoration:none;

    color:white;

    font-weight:600;

    transition:0.3s;
}

.back-btn:hover{

    transform:translateY(-3px);

    box-shadow:
    0 15px 30px rgba(37,99,235,0.35);

    color:white;
}

/* RESPONSIVE */

@media(max-width:768px){

    .hero-title{
        font-size:34px;
    }

    .leaderboard-card{
        padding:20px;
    }
}

</style>

</head>

<body>

<div class="leaderboard-container">

<!-- HERO -->

<div class="hero-card">

<h1 class="hero-title">

<i class="fa-solid fa-trophy" style="color:#f59e0b;"></i>

Global Leaderboard

</h1>

<p class="hero-subtitle">

Compete with cybersecurity learners and climb the global rankings

</p>

</div>

<!-- PODIUM -->

<div class="row g-4 podium-row">

<?php if(isset($leaders[1])): ?>

<div class="col-lg-4">

<div class="podium-card second-place">

<div class="podium-rank rank-2">
#2
</div>

<div class="podium-name">
<?= htmlspecialchars($leaders[1]['fullname']) ?>
</div>

<div class="podium-xp">
<?= htmlspecialchars($leaders[1]['xp']) ?> XP
</div>

<div class="podium-level">
<?php
$_t = getLevelTitle((int)$leaders[1]['xp']);
$_c = getLevelColor($_t);
?>
<span class="title-badge" style="color:<?= $_c ?>;border-color:<?= $_c ?>44;background:<?= $_c ?>11;"><?= htmlspecialchars($_t) ?></span>
</div>

</div>

</div>

<?php endif; ?>

<?php if(isset($leaders[0])): ?>

<div class="col-lg-4">

<div class="podium-card first-place">

<div class="podium-rank rank-1">
👑
</div>

<div class="podium-name">
<?= htmlspecialchars($leaders[0]['fullname']) ?>
</div>

<div class="podium-xp">
<?= htmlspecialchars($leaders[0]['xp']) ?> XP
</div>

<div class="podium-level">
<?php
$_t = getLevelTitle((int)$leaders[0]['xp']);
$_c = getLevelColor($_t);
?>
<span class="title-badge" style="color:<?= $_c ?>;border-color:<?= $_c ?>44;background:<?= $_c ?>11;"><?= htmlspecialchars($_t) ?></span>
</div>

</div>

</div>

<?php endif; ?>

<?php if(isset($leaders[2])): ?>

<div class="col-lg-4">

<div class="podium-card third-place">

<div class="podium-rank rank-3">
#3
</div>

<div class="podium-name">
<?= htmlspecialchars($leaders[2]['fullname']) ?>
</div>

<div class="podium-xp">
<?= htmlspecialchars($leaders[2]['xp']) ?> XP
</div>

<div class="podium-level">
<?php
$_t = getLevelTitle((int)$leaders[2]['xp']);
$_c = getLevelColor($_t);
?>
<span class="title-badge" style="color:<?= $_c ?>;border-color:<?= $_c ?>44;background:<?= $_c ?>11;"><?= htmlspecialchars($_t) ?></span>
</div>

</div>

</div>

<?php endif; ?>

</div>

<!-- TABLE -->

<div class="leaderboard-card">

<div class="table-responsive">

<table class="table table-dark align-middle">

<thead>

<tr>

<th>Rang</th>
<th>Apprenant</th>
<th>XP</th>
<th>Titre</th>

</tr>

</thead>

<tbody>

<?php
$rank = 1;

foreach($leaders as $row):

$isCurrentUser = $row['fullname'] === $currentUser;
?>

<tr class="user-row <?= $isCurrentUser ? 'current-user' : '' ?>">

<td>

<div class="rank-badge">
#<?= $rank ?>
</div>

</td>

<td>

<div class="user-name">
<?= htmlspecialchars($row['fullname']) ?>
</div>

</td>

<td>

<div class="xp-badge">
<?= htmlspecialchars($row['xp']) ?> XP
</div>

</td>

<td>

<?php
$_tt = getLevelTitle((int)$row['xp']);
$_cc = getLevelColor($_tt);
?>
<span class="title-badge" style="color:<?= $_cc ?>;border-color:<?= $_cc ?>44;background:<?= $_cc ?>11;"><?= htmlspecialchars($_tt) ?></span>

</td>

</tr>

<?php
$rank++;
endforeach;
?>

</tbody>

</table>

</div>

<a href="dashboard.php" class="back-btn">

<i class="fa-solid fa-arrow-left"></i>

Back to Dashboard

</a>

</div>

</div>


<?php require_once 'includes/chatbot_widget.php'; ?>
</body>
</html>