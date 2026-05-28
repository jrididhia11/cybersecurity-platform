<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

header("Content-Type: application/json");

$user_id = $_SESSION['user_id'];

$quizStmt = $conn->prepare("
SELECT COUNT(*) as total
FROM results
WHERE user_id=?
");

$quizStmt->bind_param("i", $user_id);
$quizStmt->execute();

$quizData = $quizStmt->get_result()->fetch_assoc();

$totalQuizzes = $quizData['total'] ?? 0;

$xp = $totalQuizzes * 100;

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

echo json_encode([
    'quizzes' => $totalQuizzes,
    'xp' => $xp,
    'latest_score' => $latestScore
]);

?>