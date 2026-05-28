<?php

require_once '../includes/admin_auth.php';
require_once '../config/db.php';

$query = "
SELECT 
    users.fullname,
    MAX(results.score) as score,
    MAX(results.created_at) as created_at
FROM results
INNER JOIN users ON users.id = results.user_id
GROUP BY users.id
ORDER BY score DESC
";

$result = mysqli_query($conn, $query);

$results = [];

while($row = mysqli_fetch_assoc($result)){
    $results[] = $row;
}

$totalResults = count($results);

$averageScore = 0;

if($totalResults > 0){

    $sum = 0;

    foreach($results as $r){
        $sum += $r['score'];
    }

    $averageScore = round($sum / $totalResults);
}

$topScore = 0;
$topStudent = "No Data";

foreach($results as $r){

    if($r['score'] > $topScore){

        $topScore = $r['score'];
        $topStudent = $r['fullname'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Quiz Results</title>

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

.results-container{

    max-width:1300px;

    margin:auto;

    padding:40px 20px;
}

/* HERO */

.hero-card{

    background:
    linear-gradient(135deg,#0f172a,#1e293b);

    border-radius:32px;

    padding:45px;

    margin-bottom:35px;

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

    background:#2563eb22;

    border-radius:50%;

    top:-150px;
    right:-150px;

    filter:blur(60px);
}

.hero-title{

    font-size:42px;

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

    padding:30px;

    border:1px solid rgba(255,255,255,0.05);

    height:100%;

    transition:0.3s;

    box-shadow:
    0 10px 35px rgba(0,0,0,0.25);
}

.stats-card:hover{

    transform:translateY(-5px);

    border-color:#2563eb55;
}

.stats-icon{

    width:70px;
    height:70px;

    border-radius:20px;

    display:flex;

    align-items:center;

    justify-content:center;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    font-size:28px;

    margin-bottom:22px;
}

.stats-title{

    color:#94a3b8;

    margin-bottom:12px;
}

.stats-value{

    font-size:40px;

    font-weight:700;
}

/* TABLE */

.results-card{

    margin-top:35px;

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

.result-row{

    transition:0.3s;
}

.result-row:hover{

    background:rgba(37,99,235,0.08);
}

.student-name{

    font-weight:600;

    font-size:16px;
}

.score-badge{

    padding:10px 18px;

    border-radius:14px;

    font-weight:600;

    display:inline-block;
}

.score-high{

    background:
    linear-gradient(135deg,#16a34a,#22c55e);
}

.score-medium{

    background:
    linear-gradient(135deg,#f59e0b,#fbbf24);

    color:#111827;
}

.score-low{

    background:
    linear-gradient(135deg,#dc2626,#ef4444);
}

.date-text{

    color:#94a3b8;
}

/* BUTTON */

.back-btn{

    display:flex;

    align-items:center;

    justify-content:center;

    gap:12px;

    width:100%;

    margin-top:30px;

    padding:18px;

    border-radius:20px;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    color:white;

    text-decoration:none;

    font-weight:600;

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

    .hero-title{
        font-size:34px;
    }

    .results-card{
        padding:20px;
    }

    .stats-value{
        font-size:32px;
    }
}

</style>

</head>

<body>

<div class="results-container">

<!-- HERO -->

<div class="hero-card">

<h1 class="hero-title">

<i class="fa-solid fa-chart-column"></i>

Quiz Analytics Dashboard

</h1>

<p class="hero-subtitle">

Monitor student performance and cybersecurity learning progress

</p>

</div>

<!-- STATS -->

<div class="row g-4">

<div class="col-md-4">

<div class="stats-card">

<div class="stats-icon">

<i class="fa-solid fa-file-circle-check"></i>

</div>

<div class="stats-title">

Total Students

</div>

<div class="stats-value">

<?= $totalResults ?>

</div>

</div>

</div>

<div class="col-md-4">

<div class="stats-card">

<div class="stats-icon">

<i class="fa-solid fa-chart-line"></i>

</div>

<div class="stats-title">

Average Score

</div>

<div class="stats-value">

<?= $averageScore ?>

</div>

</div>

</div>

<div class="col-md-4">

<div class="stats-card">

<div class="stats-icon">

<i class="fa-solid fa-trophy"></i>

</div>

<div class="stats-title">

Top Performer

</div>

<div class="stats-value" style="font-size:24px;">

<?= htmlspecialchars($topStudent) ?>

</div>

</div>

</div>

</div>

<!-- TABLE -->

<div class="results-card">

<div class="table-responsive">

<table class="table table-dark align-middle">

<thead>

<tr>

<th>Rank</th>
<th>Student</th>
<th>Best Score</th>
<th>Last Activity</th>

</tr>

</thead>

<tbody>

<?php
$rank = 1;

foreach($results as $row):

$scoreClass = 'score-low';

if($row['score'] >= 80){
    $scoreClass = 'score-high';
}
elseif($row['score'] >= 50){
    $scoreClass = 'score-medium';
}
?>

<tr class="result-row">

<td>

<div class="student-name">

#<?= $rank ?>

</div>

</td>

<td>

<div class="student-name">

<?= htmlspecialchars($row['fullname']) ?>

</div>

</td>

<td>

<div class="score-badge <?= $scoreClass ?>">

<?= htmlspecialchars($row['score']) ?>

</div>

</td>

<td>

<div class="date-text">

<?= htmlspecialchars($row['created_at']) ?>

</div>

</td>

</tr>

<?php
$rank++;
endforeach;
?>

</tbody>

</table>

</div>

<a href="admin_dashboard.php" class="back-btn">

<i class="fa-solid fa-arrow-left"></i>

Back to Dashboard

</a>

</div>

</div>

</body>
</html>