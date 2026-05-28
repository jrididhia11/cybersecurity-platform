<?php

require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/xp_system.php';
require_once 'includes/n8n.php';

header("X-Frame-Options: DENY");

// Bonus connexion quotidienne (+5 XP)
$dailyBonusAwarded = awardDailyLoginBonus();
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

/* QUIZ STATS */
$quizStmt = $conn->prepare("SELECT COUNT(*) as total FROM results WHERE user_id=?");
$quizStmt->bind_param("i", $user_id);
$quizStmt->execute();

$quizData = $quizStmt->get_result()->fetch_assoc();

$totalQuizzes = $quizData['total'] ?? 0;

/* XP */
$xp = getXP();
$level = getLevel();
$progress = ($xp % 100);
if($progress > 100) $progress = 100;
$scoreStmt = $conn->prepare("
SELECT score
FROM results
WHERE user_id=?
ORDER BY created_at DESC
LIMIT 1
");

$scoreStmt->bind_param("i", $user_id);
$scoreStmt->execute();

$scoreResult = $scoreStmt->get_result();

$latestScore = 0;

if($scoreResult->num_rows > 0){
    $scoreRow = $scoreResult->fetch_assoc();
    $latestScore = $scoreRow['score'];
}

/* LAST SCORE */

/* LABS */
$labs = [
    [
        "title" => "Phishing Simulator",
        "description" => "Identify phishing emails and suspicious links.",
        "difficulty" => "Easy",
        "xp" => 50,
        "icon" => "fa-envelope",
        "link" => "rooms/intro-phishing.php"
    ],
    [
        "title" => "SQL Injection Lab",
        "description" => "Understand how SQL Injection attacks work safely.",
        "difficulty" => "Medium",
        "xp" => 70,
        "icon" => "fa-database",
        "link" => "rooms/intro-sql.php"
    ],
    [
        "title" => "Password Security Lab",
        "description" => "Analyze password strength and security.",
        "difficulty" => "Easy",
        "xp" => 40,
        "icon" => "fa-key",
        "link" => "rooms/intro-password.php"
    ],
    [
        "title" => "XSS Lab",
        "description" => "Discover Cross-Site Scripting attacks and how to prevent them.",
        "difficulty" => "Hard",
        "xp" => 80,
        "icon" => "fa-code",
        "link" => "rooms/intro-xss.php"
    ]
];

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CyberSec Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    font-family:'Inter',sans-serif;
    overflow-x:hidden;
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
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    color:white;
    transform:translateX(5px);
}

/* MAIN CONTENT */

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

    box-shadow:
    0 0 30px rgba(37,99,235,0.08),
    0 10px 40px rgba(0,0,0,0.45);
}

.hero-title{
    font-size:44px;
    font-weight:700;
    margin-bottom:10px;
}

.hero-subtitle{
    color:#94a3b8;
    font-size:18px;
}

.level-badge{
    background:linear-gradient(135deg,#2563eb,#3b82f6);

    padding:14px 22px;

    border-radius:18px;

    font-weight:600;

    box-shadow:0 0 25px rgba(59,130,246,0.4);
}

/* STATS */

.stats-card{
    background:rgba(15,23,42,0.92);

    border:1px solid rgba(255,255,255,0.05);

    border-radius:28px;

    padding:30px;

    transition:0.35s ease;

    height:100%;
}

.stats-card:hover{
    transform:translateY(-8px);

    border-color:rgba(59,130,246,0.35);

    box-shadow:0 0 35px rgba(37,99,235,0.12);
}

.stats-icon{
    width:70px;
    height:70px;

    border-radius:20px;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:28px;

    margin-bottom:22px;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);
}

.stats-title{
    color:#94a3b8;
    font-size:16px;
    margin-bottom:12px;
}

.stats-number{
    font-size:42px;
    font-weight:700;
}

/* CHART CARD */

.chart-card{
    background:rgba(15,23,42,0.92);

    border-radius:30px;

    padding:35px;

    margin-top:35px;

    border:1px solid rgba(255,255,255,0.05);
}

/* PROGRESS */

.progress{
    height:22px;
    border-radius:30px;
    overflow:hidden;
    background:#1e293b;
}

.progress-bar{
    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    font-weight:600;
}

/* LABS */

.section-title{
    font-size:32px;
    font-weight:700;
    margin-top:55px;
    margin-bottom:25px;
}

.lab-card{
    background:rgba(15,23,42,0.92);

    border-radius:28px;

    padding:30px;

    border:1px solid rgba(255,255,255,0.05);

    transition:0.35s ease;

    height:100%;
}

.lab-card:hover{
    transform:translateY(-8px);

    border-color:#3b82f6;

    box-shadow:0 0 30px rgba(37,99,235,0.12);
}

.lab-icon{
    width:75px;
    height:75px;

    border-radius:22px;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:30px;

    margin-bottom:22px;
}

.lab-title{
    font-size:24px;
    font-weight:700;
}

.lab-description{
    color:#94a3b8;
    margin-top:12px;
    margin-bottom:20px;
}

.lab-meta{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:25px;
}

.badge-custom{
    background:#1e293b;

    padding:10px 14px;

    border-radius:12px;

    color:#cbd5e1;

    font-size:14px;
}

.start-btn{
    display:inline-block;

    text-decoration:none;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    color:white;

    padding:13px 24px;

    border-radius:14px;

    font-weight:600;

    transition:0.3s ease;
}

.start-btn:hover{
    transform:scale(1.05);
    color:white;
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

<div class="sidebar">

<div class="logo">
Cyber<span>Sec</span>
</div>

<a href="dashboard.php">
<i class="fa-solid fa-chart-line"></i>
Dashboard
</a>

<a href="profile.php">
<i class="fa-solid fa-user"></i>
Profile
</a>

<a href="leaderboard.php">
<i class="fa-solid fa-trophy"></i>
Leaderboard
</a>

<a href="defense.php">
<i class="fa-solid fa-shield-halved"></i>
Defense Center
</a>

<a href="chatbot.php" style="background:linear-gradient(135deg,rgba(30,64,175,.3),rgba(59,130,246,.2));border-color:rgba(59,130,246,.3);color:#93c5fd;">
<i class="fa-solid fa-robot"></i>
CyberBot IA
</a>

<a href="#labs">
<i class="fa-solid fa-flask"></i>
Labs
</a>

<a href="quizzes/quiz1.php">
<i class="fa-solid fa-file-circle-question"></i>
Quiz
</a>

<a href="logout.php">
<i class="fa-solid fa-right-from-bracket"></i>
Logout
</a>

</div>

<div class="main-content">

<div class="hero-card">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

<div>

<h1 class="hero-title">
Welcome Back, <?= htmlspecialchars($_SESSION['fullname']) ?>
</h1>

<p class="hero-subtitle">
Master cybersecurity through immersive labs and real-world security challenges.
</p>

</div>

<div class="level-badge">
Level <?= $level ?>
</div>

</div>

</div>

<div class="row g-4">

<div class="col-md-4">

<div class="stats-card">

<div class="stats-icon">
<i class="fa-solid fa-check"></i>
</div>

<div class="stats-title">
Completed Quizzes
</div>

<div class="stats-number">
<?= $totalQuizzes ?>
</div>

</div>

</div>

<div class="col-md-4">

<div class="stats-card">

<div class="stats-icon">
<i class="fa-solid fa-bolt"></i>
</div>

<div class="stats-title">
Total XP
</div>

<div class="stats-number">
<?= $xp ?>
</div>

</div>

</div>

<div class="col-md-4">

<div class="stats-card">

<div class="stats-icon">
<i class="fa-solid fa-chart-simple"></i>
</div>

<div class="stats-title">
Latest Score
</div>

<div class="stats-number">
<?= $latestScore ?>
</div>

</div>

</div>

</div>

<div class="chart-card">

<h3 class="mb-4">
Learning Statistics
</h3>

<canvas id="statsChart"></canvas>

</div>

<div class="chart-card">

<h3 class="mb-4">
XP Progress
</h3>

<div class="progress">

<div class="progress-bar" style="width: <?= $progress ?>%">
<?= round($progress) ?>%
</div>

</div>

</div>

<h2 class="section-title" id="labs">
Interactive Cybersecurity Labs
</h2>

<div class="row g-4">

<?php foreach($labs as $lab): ?>

<div class="col-lg-4 col-md-6">

<div class="lab-card">

<div class="lab-icon">
<i class="fa-solid <?= $lab['icon'] ?>"></i>
</div>

<div class="lab-title">
<?= $lab['title'] ?>
</div>

<div class="lab-description">
<?= $lab['description'] ?>
</div>

<div class="lab-meta">

<div class="badge-custom">
<?= $lab['difficulty'] ?>
</div>

<div class="badge-custom">
+<?= $lab['xp'] ?> XP
</div>

</div>

<a href="<?= $lab['link'] ?>" class="start-btn">
Start Lab
</a>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

<script>

const ctx = document.getElementById('statsChart');

new Chart(ctx, {

type: 'bar',

data: {

labels: ['Quizzes', 'XP', 'Score'],

datasets: [{

label: 'Statistics',

data: [
<?= $totalQuizzes ?>,
<?= $xp ?>,
<?= $latestScore ?>
],

backgroundColor: [
'#2563eb',
'#0ea5e9',
'#38bdf8'
],

borderRadius:10

}]

},

options: {

responsive:true,

plugins:{
legend:{
labels:{
color:'white'
}
}
},

scales:{

x:{
ticks:{
color:'white'
},
grid:{
color:'rgba(255,255,255,0.05)'
}
},

y:{
ticks:{
color:'white'
},
grid:{
color:'rgba(255,255,255,0.05)'
}
}

}

}

});

</script>


<!-- Daily XP Toast -->
<?php if ($dailyBonusAwarded ?? false): ?>
<style>.daily-toast{position:fixed;top:20px;right:20px;background:linear-gradient(135deg,#1e3a8a,#3b82f6);border:1px solid rgba(59,130,246,.3);color:white;padding:14px 20px;border-radius:14px;font-size:14px;font-weight:600;z-index:9998;box-shadow:0 8px 24px rgba(0,0,0,.4);animation:slideIn .4s ease;}@keyframes slideIn{from{transform:translateX(80px);opacity:0;}to{transform:translateX(0);opacity:1;}}</style>
<div class="daily-toast">🌟 Bonus connexion quotidienne : +5 XP !</div>
<script>setTimeout(() => document.querySelector('.daily-toast')?.remove(), 4000);</script>
<?php endif; ?>
<?php require_once 'includes/chatbot_widget.php'; ?>
</body>
</html>