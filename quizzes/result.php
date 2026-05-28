<?php
session_start();

require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/xp_system.php';
require_once '../includes/n8n.php';

// ── Calcul du score ───────────────────────────────────────────────────────────
$score = 0;
$query = mysqli_query($conn, "SELECT * FROM quizzes");
$total_questions = mysqli_num_rows($query);

while ($quiz = mysqli_fetch_assoc($query)) {
    $question_id = $quiz['id'];
    $user_answer = $_POST['q' . $question_id] ?? '';
    if ($user_answer == $quiz['correct_answer']) {
        $score++;
    }
}

$user_id    = (int)$_SESSION['user_id'];
$percentage = ($total_questions > 0) ? round(($score / $total_questions) * 100) : 0;
$isPerfect  = ($percentage === 100);

// ── Calcul XP selon score ─────────────────────────────────────────────────────
$xpEarned = 0;
if ($percentage >= 80) {
    $xpEarned = 25;
} elseif ($percentage >= 50) {
    $xpEarned = 15;
} elseif ($percentage > 0) {
    $xpEarned = 10;
}
if ($isPerfect) {
    $xpEarned += 10; // Bonus score parfait
}

// ── Sauvegarder résultat en BDD ───────────────────────────────────────────────
$stmt = mysqli_prepare($conn,
    "INSERT INTO results (user_id, quiz_id, score) VALUES (?, 1, ?)"
);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $score);
mysqli_stmt_execute($stmt);

// ── Mettre à jour XP via xp_system ───────────────────────────────────────────
if ($xpEarned > 0) {
    addXP($xpEarned);
}

// ── Déclencher événements n8n ─────────────────────────────────────────────────
// Événement standard : quiz complété
triggerN8nEvent($user_id, 'quiz_completed', [
    'score'           => $score,
    'total_questions' => $total_questions,
    'percentage'      => $percentage,
    'xp_earned'       => $xpEarned,
    'total_xp'        => getXP(),
]);

// Événement spécial : score parfait
if ($isPerfect) {
    triggerN8nEvent($user_id, 'quiz_perfect', [
        'score'     => $score,
        'total'     => $total_questions,
        'xp_bonus'  => 10,
        'total_xp'  => getXP(),
    ]);
}

// ── Messages et couleurs selon performance ────────────────────────────────────
$message      = "Continue à t'entraîner !";
$messageColor = "#ef4444";
$messageIcon  = "fa-rotate-right";

if ($percentage === 100) {
    $message      = "Score Parfait ! 🏆";
    $messageColor = "#f59e0b";
    $messageIcon  = "fa-trophy";
} elseif ($percentage >= 80) {
    $message      = "Excellent Travail !";
    $messageColor = "#10b981";
    $messageIcon  = "fa-circle-check";
} elseif ($percentage >= 50) {
    $message      = "Bon Travail !";
    $messageColor = "#3b82f6";
    $messageIcon  = "fa-thumbs-up";
}

$levelTitle = getLevelTitle(getXP());
$levelColor = getLevelColor($levelTitle);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Résultat Quiz — Plateforme Cybersécurité</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
body{background:radial-gradient(circle at top left,#2563eb22,transparent 25%),radial-gradient(circle at bottom right,#3b82f622,transparent 25%),#020617;color:white;min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:'Inter',sans-serif;padding:30px;}
.result-card{width:100%;max-width:850px;background:linear-gradient(135deg,#1e293b,#0f172a);border-radius:30px;padding:55px;text-align:center;border:1px solid rgba(255,255,255,.05);box-shadow:0 15px 50px rgba(0,0,0,.45);position:relative;overflow:hidden;}
.result-card::before{content:'';position:absolute;width:320px;height:320px;background:#2563eb22;border-radius:50%;top:-120px;right:-120px;filter:blur(50px);}
.result-icon{width:120px;height:120px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#3b82f6);display:flex;align-items:center;justify-content:center;margin:auto;font-size:50px;box-shadow:0 0 40px rgba(37,99,235,.35);}
.result-title{font-size:46px;font-weight:700;margin:30px 0 10px;}
.result-subtitle{color:#94a3b8;font-size:18px;margin-bottom:45px;}
.score-circle{width:240px;height:240px;border-radius:50%;margin:auto;background:radial-gradient(circle,#0f172a,#020617);border:10px solid #2563eb;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 0 40px rgba(37,99,235,.25);}
.score-value{font-size:64px;font-weight:700;line-height:1;}
.score-total{color:#94a3b8;margin-top:10px;font-size:18px;}
.performance-message{margin-top:35px;font-size:34px;font-weight:700;}
.xp-details{margin-top:20px;display:flex;gap:14px;justify-content:center;flex-wrap:wrap;}
.xp-pill{padding:10px 20px;border-radius:14px;font-weight:600;font-size:15px;display:inline-flex;align-items:center;gap:8px;}
.xp-base{background:linear-gradient(135deg,#1e40af,#3b82f6);}
.xp-bonus{background:linear-gradient(135deg,#92400e,#f59e0b);color:#111;}
.title-pill{border:1px solid;padding:10px 20px;border-radius:14px;font-weight:700;font-size:15px;}
.actions{margin-top:50px;}
.action-btn{width:100%;border:none;padding:18px;border-radius:18px;background:linear-gradient(135deg,#2563eb,#3b82f6);color:white;font-weight:600;font-size:17px;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:12px;transition:.3s;box-shadow:0 10px 25px rgba(37,99,235,.3);}
.action-btn:hover{transform:translateY(-3px);box-shadow:0 15px 35px rgba(37,99,235,.4);color:white;}
.secondary-btn{background:#111827;border:1px solid rgba(255,255,255,.05);box-shadow:none;}
.secondary-btn:hover{background:#1e293b;}
/* Confetti pour score parfait */
.confetti-piece{position:fixed;width:10px;height:10px;top:-20px;border-radius:2px;animation:fall linear forwards;}
@keyframes fall{to{transform:translateY(110vh) rotate(720deg);opacity:0;}}
@media(max-width:768px){.result-card{padding:35px 25px;}.result-title{font-size:34px;}.score-circle{width:190px;height:190px;}.score-value{font-size:50px;}}
</style>
</head>
<body>

<div class="result-card">
  <div class="result-icon">
    <i class="fa-solid <?= $messageIcon ?>"></i>
  </div>
  <h1 class="result-title">Quiz Complété</h1>
  <p class="result-subtitle">Résultats de ton évaluation cybersécurité</p>

  <div class="score-circle">
    <div class="score-value"><?= htmlspecialchars($score) ?></div>
    <div class="score-total">/ <?= htmlspecialchars($total_questions) ?></div>
  </div>

  <div class="performance-message" style="color:<?= $messageColor ?>;">
    <?= $message ?>
  </div>

  <!-- XP détaillé -->
  <div class="xp-details">
    <?php if ($xpEarned > 0): ?>
    <div class="xp-pill xp-base">
      <i class="fas fa-bolt"></i>
      +<?= $isPerfect ? ($xpEarned - 10) : $xpEarned ?> XP (score <?= $percentage ?>%)
    </div>
    <?php endif; ?>
    <?php if ($isPerfect): ?>
    <div class="xp-pill xp-bonus">
      <i class="fas fa-star"></i>
      +10 XP Bonus Score Parfait !
    </div>
    <?php endif; ?>
    <div class="title-pill" style="color:<?= $levelColor ?>;border-color:<?= $levelColor ?>44;">
      <?= htmlspecialchars($levelTitle) ?> — <?= getXP() ?> XP
    </div>
  </div>

  <!-- Boutons -->
  <div class="actions">
    <div class="row g-4">
      <div class="col-md-4">
        <a href="quiz1.php" class="action-btn secondary-btn">
          <i class="fa-solid fa-rotate-right"></i> Réessayer
        </a>
      </div>
      <div class="col-md-4">
        <a href="../dashboard.php" class="action-btn">
          <i class="fa-solid fa-house"></i> Dashboard
        </a>
      </div>
      <div class="col-md-4">
        <a href="../leaderboard.php" class="action-btn secondary-btn">
          <i class="fa-solid fa-trophy"></i> Classement
        </a>
      </div>
    </div>
  </div>
</div>

<?php if ($isPerfect): ?>
<script>
// Confetti pour score parfait
const colors = ['#f59e0b','#3b82f6','#10b981','#ec4899','#8b5cf6'];
for (let i = 0; i < 60; i++) {
  const el = document.createElement('div');
  el.className = 'confetti-piece';
  el.style.cssText = `
    left:${Math.random()*100}vw;
    background:${colors[Math.floor(Math.random()*colors.length)]};
    animation-duration:${1.5 + Math.random()*2}s;
    animation-delay:${Math.random()*1.5}s;
    transform:rotate(${Math.random()*360}deg);
  `;
  document.body.appendChild(el);
  setTimeout(() => el.remove(), 4000);
}
</script>
<?php endif; ?>

<?php
if (file_exists(__DIR__ . '/../includes/chatbot_widget.php'))
    require_once __DIR__ . '/../includes/chatbot_widget.php';
?>
</body>
</html>
