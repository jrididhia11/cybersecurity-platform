<?php

include '../config/db.php';
include '../includes/auth.php';

$query = $conn->query("SELECT * FROM quizzes ORDER BY RAND()");

$totalQuestions = $query->num_rows;

$categoryNames = [
    1 => 'General Cybersecurity',
    2 => 'Passwords & Auth',
    3 => 'Network Security',
    4 => 'Attacks & Threats',
    5 => 'Cryptography',
];

?>

<!DOCTYPE html>
<html>

<head>

<title>Cybersecurity Quiz</title>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/style.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

body{
    background:
    radial-gradient(circle at top left,#2563eb22,transparent 25%),
    radial-gradient(circle at bottom right,#3b82f622,transparent 25%),
    #020617;
    color:white;
    min-height:100vh;
    font-family:'Inter',sans-serif;
}

.quiz-container{
    max-width:1000px;
    margin:auto;
    padding:50px 20px;
}

.quiz-header{
    background:
    linear-gradient(135deg,#1e293b,#0f172a);
    border-radius:28px;
    padding:40px;
    margin-bottom:35px;
    border:1px solid rgba(255,255,255,0.05);
    box-shadow:0 10px 40px rgba(0,0,0,0.4);
    position:relative;
    overflow:hidden;
}

.quiz-header::before{
    content:'';
    position:absolute;
    width:300px;
    height:300px;
    background:#2563eb22;
    border-radius:50%;
    top:-120px;
    right:-120px;
    filter:blur(50px);
}

.quiz-title{
    font-size:42px;
    font-weight:700;
    margin-bottom:10px;
}

.quiz-subtitle{
    color:#94a3b8;
    font-size:18px;
}

.quiz-stats{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
    margin-top:25px;
}

.stat-badge{
    background:#111827;
    padding:12px 18px;
    border-radius:14px;
    color:#cbd5e1;
    font-size:15px;
    border:1px solid rgba(255,255,255,0.05);
}

.progress-section{
    background:#0f172a;
    border-radius:22px;
    padding:25px;
    margin-bottom:30px;
}

.progress{
    height:20px;
    border-radius:20px;
    background:#1e293b;
    overflow:hidden;
}

.progress-bar{
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    width:100%;
}

.question-card{
    background:rgba(15,23,42,0.92);
    border:1px solid rgba(255,255,255,0.05);
    border-radius:24px;
    padding:35px;
    margin-bottom:30px;
    box-shadow:0 10px 30px rgba(0,0,0,0.3);
    transition:0.3s;
}

.question-card:hover{
    transform:translateY(-5px);
    border-color:#2563eb55;
}

.question-number{
    width:50px;
    height:50px;
    border-radius:14px;
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    margin-bottom:20px;
    font-size:18px;
}

.question-text{
    font-size:24px;
    font-weight:600;
    margin-bottom:25px;
    line-height:1.5;
}

.category-badge{
    display:inline-block;
    background:#1e293b;
    color:#38bdf8;
    padding:8px 14px;
    border-radius:12px;
    font-size:14px;
    margin-bottom:20px;
}

.option-label{
    display:flex;
    align-items:center;
    gap:15px;
    background:#0f172a;
    border:1px solid rgba(255,255,255,0.05);
    padding:18px;
    border-radius:16px;
    margin-bottom:15px;
    cursor:pointer;
    transition:0.3s;
}

.option-label:hover{
    border-color:#2563eb;
    background:#111827;
    transform:translateX(5px);
}

.option-label input{
    accent-color:#2563eb;
    transform:scale(1.2);
}

.submit-btn{
    width:100%;
    border:none;
    padding:18px;
    border-radius:18px;
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    color:white;
    font-weight:600;
    font-size:18px;
    transition:0.3s;
    box-shadow:0 10px 25px rgba(37,99,235,0.3);
}

.submit-btn:hover{
    transform:translateY(-3px);
}

.back-btn{
    display:inline-flex;
    align-items:center;
    gap:10px;
    color:#94a3b8;
    text-decoration:none;
    margin-bottom:25px;
    transition:0.3s;
}

.back-btn:hover{
    color:white;
}

.terminal{
    background:black;
    border-radius:22px;
    padding:25px;
    margin-top:35px;
    border:1px solid #22c55e;
    font-family:monospace;
}

.terminal-line{
    color:#22c55e;
    margin-bottom:12px;
}

@media(max-width:768px){

    .quiz-title{
        font-size:32px;
    }

    .question-text{
        font-size:20px;
    }

    .question-card{
        padding:25px;
    }

}

</style>

</head>

<body>

<div class="quiz-container">

<a href="../dashboard.php" class="back-btn">

<i class="fa-solid fa-arrow-left"></i>

Back to Dashboard

</a>

<div class="quiz-header">

<h1 class="quiz-title">

Cybersecurity Knowledge Assessment

</h1>

<p class="quiz-subtitle">

Evaluate your cybersecurity knowledge through interactive security challenges.

</p>

<div class="quiz-stats">

<div class="stat-badge">
<i class="fa-solid fa-list"></i>
<?= $totalQuestions ?> Questions
</div>

<div class="stat-badge">
<i class="fa-solid fa-bolt"></i>
+100 XP Reward
</div>

<div class="stat-badge">
<i class="fa-solid fa-shield-halved"></i>
Medium Difficulty
</div>

<div class="stat-badge">
<i class="fa-solid fa-clock"></i>
Estimated Time: 5 min
</div>

</div>

</div>

<div class="progress-section">

<h5 class="mb-3">Quiz Progress</h5>

<div class="progress">

<div class="progress-bar"></div>

</div>

</div>

<form action="result.php" method="POST">

<?php 
$count = 1;

while($quiz = $query->fetch_assoc()):
?>

<div class="question-card">

<div class="question-number">

<?= $count ?>

</div>

<div class="category-badge">

<?= htmlspecialchars($categoryNames[$quiz['course_id']] ?? 'Cybersecurity') ?>

</div>

<div class="question-text">

<?= htmlspecialchars($quiz['question']) ?>

</div>

<label class="option-label">

<input
type="radio"
name="q<?= $quiz['id'] ?>"
value="<?= htmlspecialchars($quiz['option1']) ?>"
required>

<span>

<?= htmlspecialchars($quiz['option1']) ?>

</span>

</label>

<label class="option-label">

<input
type="radio"
name="q<?= $quiz['id'] ?>"
value="<?= htmlspecialchars($quiz['option2']) ?>">

<span>

<?= htmlspecialchars($quiz['option2']) ?>

</span>

</label>

<label class="option-label">

<input
type="radio"
name="q<?= $quiz['id'] ?>"
value="<?= htmlspecialchars($quiz['option3']) ?>">

<span>

<?= htmlspecialchars($quiz['option3']) ?>

</span>

</label>

<label class="option-label">

<input
type="radio"
name="q<?= $quiz['id'] ?>"
value="<?= htmlspecialchars($quiz['option4']) ?>">

<span>

<?= htmlspecialchars($quiz['option4']) ?>

</span>

</label>

</div>

<?php 
$count++;
endwhile; 
?>

<button class="submit-btn">

<i class="fa-solid fa-paper-plane"></i>

Submit Quiz

</button>

</form>

<div class="terminal">

<div class="terminal-line">
> Quiz session initialized...
</div>

<div class="terminal-line">
> Monitoring answer integrity...
</div>

<div class="terminal-line">
> Secure evaluation engine active...
</div>

<div class="terminal-line">
> Awaiting candidate submission...
</div>

</div>

</div>


<?php
if (file_exists(__DIR__ . '/../includes/chatbot_widget.php'))
    require_once __DIR__ . '/../includes/chatbot_widget.php';
?>
</body>
</html>