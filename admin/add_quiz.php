<?php

require_once '../includes/admin_auth.php';
require_once '../includes/db.php';
require_once '../includes/csrf.php';
require_once '../includes/logger.php';

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF Token");
    }

    $question = trim($_POST['question']);
    $option1 = trim($_POST['option1']);
    $option2 = trim($_POST['option2']);
    $option3 = trim($_POST['option3']);
    $option4 = trim($_POST['option4']);
    $correct_answer = trim($_POST['correct_answer']);

    $stmt = $conn->prepare("
        INSERT INTO quizzes(
            question,
            option1,
            option2,
            option3,
            option4,
            correct_answer
        )
        VALUES(?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "ssssss",
        $question,
        $option1,
        $option2,
        $option3,
        $option4,
        $correct_answer
    );

    if ($stmt->execute()) {

        logAction($_SESSION['user_id'], "Added new quiz");

        $message = "Quiz Added Successfully";
    }
}

$quizzes = $conn->query("SELECT * FROM quizzes ORDER BY id DESC");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Quiz</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/style.css">

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

.quiz-container{

    max-width:1400px;

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

/* FORM CARD */

.form-card{

    background:rgba(15,23,42,0.92);

    border-radius:30px;

    padding:35px;

    border:1px solid rgba(255,255,255,0.05);

    box-shadow:
    0 10px 35px rgba(0,0,0,0.3);

    margin-bottom:35px;
}

.section-title{

    font-size:28px;

    font-weight:600;

    margin-bottom:30px;
}

.form-label{

    color:#cbd5e1;

    font-weight:500;

    margin-bottom:10px;
}

.form-control{

    background:#0f172a !important;

    border:1px solid rgba(255,255,255,0.08) !important;

    color:white !important;

    border-radius:16px !important;

    padding:15px !important;

    transition:0.3s;
}

.form-control::placeholder{

    color:#64748b;
}

.form-control:focus{

    border-color:#3b82f6 !important;

    box-shadow:
    0 0 0 4px rgba(37,99,235,0.15) !important;
}

textarea.form-control{

    min-height:140px;

    resize:none;
}

/* BUTTON */

.cyber-btn{

    width:100%;

    border:none;

    padding:16px;

    border-radius:18px;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    color:white;

    font-weight:600;

    font-size:17px;

    transition:0.3s;

    box-shadow:
    0 10px 25px rgba(37,99,235,0.25);
}

.cyber-btn:hover{

    transform:translateY(-4px);

    box-shadow:
    0 15px 35px rgba(37,99,235,0.4);
}

/* ALERT */

.alert{

    border:none;

    border-radius:18px;

    padding:18px;

    font-weight:500;
}

/* TABLE */

.quiz-table-card{

    background:rgba(15,23,42,0.92);

    border-radius:30px;

    padding:35px;

    border:1px solid rgba(255,255,255,0.05);

    box-shadow:
    0 10px 35px rgba(0,0,0,0.3);
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

.quiz-row{

    transition:0.3s;
}

.quiz-row:hover{

    background:rgba(37,99,235,0.08);
}

.quiz-id{

    font-weight:700;

    color:#3b82f6;
}

.question-text{

    max-width:650px;

    font-weight:500;
}

/* ACTION BUTTONS */

.action-buttons{

    display:flex;

    gap:10px;

    flex-wrap:wrap;
}

.edit-btn,
.delete-btn{

    padding:10px 16px;

    border-radius:12px;

    text-decoration:none;

    color:white;

    font-size:14px;

    font-weight:600;

    transition:0.3s;
}

.edit-btn{

    background:
    linear-gradient(135deg,#f59e0b,#fbbf24);

    color:#111827;
}

.delete-btn{

    background:
    linear-gradient(135deg,#dc2626,#ef4444);
}

.edit-btn:hover,
.delete-btn:hover{

    transform:translateY(-2px);

    color:white;
}

/* BACK BUTTON */

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

    .form-card,
    .quiz-table-card{
        padding:22px;
    }

    .question-text{
        max-width:250px;
    }
}

</style>

</head>

<body>

<div class="quiz-container">

<!-- HERO -->

<div class="hero-card">

<h1 class="hero-title">

<i class="fa-solid fa-circle-plus"></i>

Quiz Management Center

</h1>

<p class="hero-subtitle">

Create and manage cybersecurity quizzes for students

</p>

</div>

<!-- FORM -->

<div class="form-card">

<h2 class="section-title">

Add New Quiz

</h2>

<?php if($message): ?>

<div class="alert alert-success">

<i class="fa-solid fa-circle-check"></i>

<?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>

</div>

<?php endif; ?>

<form method="POST">

<input
type="hidden"
name="csrf_token"
value="<?= generateCSRFToken() ?>">

<div class="mb-4">

<label class="form-label">

Quiz Question

</label>

<textarea
name="question"
class="form-control"
placeholder="Enter cybersecurity question..."
required></textarea>

</div>

<div class="row">

<div class="col-md-6 mb-4">

<label class="form-label">

Option 1

</label>

<input
type="text"
name="option1"
class="form-control"
placeholder="Enter option 1"
required>

</div>

<div class="col-md-6 mb-4">

<label class="form-label">

Option 2

</label>

<input
type="text"
name="option2"
class="form-control"
placeholder="Enter option 2"
required>

</div>

<div class="col-md-6 mb-4">

<label class="form-label">

Option 3

</label>

<input
type="text"
name="option3"
class="form-control"
placeholder="Enter option 3"
required>

</div>

<div class="col-md-6 mb-4">

<label class="form-label">

Option 4

</label>

<input
type="text"
name="option4"
class="form-control"
placeholder="Enter option 4"
required>

</div>

</div>

<div class="mb-4">

<label class="form-label">

Correct Answer

</label>

<input
type="text"
name="correct_answer"
class="form-control"
placeholder="Enter correct answer exactly"
required>

</div>

<button class="cyber-btn">

<i class="fa-solid fa-plus"></i>

Add Quiz

</button>

</form>

</div>

<!-- QUIZZES TABLE -->

<div class="quiz-table-card">

<h2 class="section-title">

All Quizzes

</h2>

<div class="table-responsive">

<table class="table table-dark align-middle">

<thead>

<tr>

<th>ID</th>
<th>Question</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php while($quiz = $quizzes->fetch_assoc()): ?>

<tr class="quiz-row">

<td>

<div class="quiz-id">

#<?= $quiz['id'] ?>

</div>

</td>

<td>

<div class="question-text">

<?= htmlspecialchars($quiz['question']) ?>

</div>

</td>

<td>

<div class="action-buttons">

<a
href="edit_quiz.php?id=<?= $quiz['id'] ?>"
class="edit-btn">

<i class="fa-solid fa-pen"></i>

Edit

</a>

<a
href="delete_quiz.php?id=<?= $quiz['id'] ?>&token=<?= generateCSRFToken() ?>"
class="delete-btn"
onclick="return confirm('Delete quiz?')">

<i class="fa-solid fa-trash"></i>

Delete

</a>

</div>

</td>

</tr>

<?php endwhile; ?>

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