<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';
require_once '../includes/csrf.php';
require_once '../includes/logger.php';

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");

if (!isset($_GET['id'])) {
    die('Quiz ID Missing');
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM quizzes WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$quiz = $stmt->get_result()->fetch_assoc();

if (!$quiz) {
    die('Quiz Not Found');
}

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
        UPDATE quizzes
        SET question=?, option1=?, option2=?, option3=?, option4=?, correct_answer=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "ssssssi",
        $question,
        $option1,
        $option2,
        $option3,
        $option4,
        $correct_answer,
        $id
    );

    if ($stmt->execute()) {

        logAction($_SESSION['user_id'], "Edited quiz ID $id");

        $message = "Quiz Updated Successfully";

        $stmt = $conn->prepare("SELECT * FROM quizzes WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $quiz = $stmt->get_result()->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Quiz</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

<div class="container mt-5">

<h2>Edit Quiz</h2>

<?php if($message): ?>

<div class="alert alert-success">
<?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
</div>

<?php endif; ?>

<form method="POST">

<input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

<div class="mb-3">

<label>Question</label>

<textarea name="question" class="form-control" required><?= htmlspecialchars($quiz['question']) ?></textarea>

</div>

<div class="mb-3">

<label>Option 1</label>

<input type="text"
name="option1"
class="form-control"
value="<?= htmlspecialchars($quiz['option1']) ?>"
required>

</div>

<div class="mb-3">

<label>Option 2</label>

<input type="text"
name="option2"
class="form-control"
value="<?= htmlspecialchars($quiz['option2']) ?>"
required>

</div>

<div class="mb-3">

<label>Option 3</label>

<input type="text"
name="option3"
class="form-control"
value="<?= htmlspecialchars($quiz['option3']) ?>"
required>

</div>

<div class="mb-3">

<label>Option 4</label>

<input type="text"
name="option4"
class="form-control"
value="<?= htmlspecialchars($quiz['option4']) ?>"
required>

</div>

<div class="mb-3">

<label>Correct Answer</label>

<input type="text"
name="correct_answer"
class="form-control"
value="<?= htmlspecialchars($quiz['correct_answer']) ?>"
required>

</div>

<button class="btn btn-warning">
Update Quiz
</button>

<a href="add_quiz.php" class="btn btn-secondary">
Back
</a>

</form>

</div>

</body>
</html>