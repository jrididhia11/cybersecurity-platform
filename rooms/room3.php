<?php
session_start();
include '../includes/xp_system.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$step       = 1;
$success    = false;
$error      = false;
$message    = "";
$reflection = "";

// Étape 1 : XSS réfléchi
if (isset($_POST['step1_submit'])) {
    $input = $_POST['search_input'] ?? '';
    // Simule la détection d'un payload XSS (sans l'exécuter)
    $xss_patterns = ['<script', 'javascript:', 'onerror=', 'onload=', 'alert(', '<img', '<svg'];
    $detected = false;
    foreach ($xss_patterns as $p) {
        if (stripos($input, $p) !== false) {
            $detected = true;
            break;
        }
    }
    if ($detected) {
        $_SESSION['xss_step1'] = true;
        $step = 2;
        $message = "✔ Payload XSS détecté ! La page aurait affiché votre script.<br><strong>Étape 1 réussie.</strong>";
        $success = true;
        // Affiche la valeur encodée (sécurisé)
        $reflection = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    } else {
        $error   = true;
        $message = "✖ Essayez un payload XSS classique, ex: <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code>";
        $step    = 1;
    }
}

// Étape 2 : identifier la bonne contre-mesure
if (isset($_POST['step2_answer'])) {
    $_SESSION['xss_step1'] = true;
    $answer = $_POST['defense'] ?? '';
    if ($answer === 'htmlspecialchars') {
        $_SESSION['xss_step2'] = true;
        completeLab('xss', 80);
        $step    = 3;
        $success = true;
        $message = "✔ Correct ! <code>htmlspecialchars()</code> encode les caractères dangereux.<br><strong>+80 XP gagnés. Lab terminé !</strong>";
    } else {
        $error   = true;
        $message = "✖ Ce n'est pas la meilleure défense contre le XSS réfléchi. Réessayez.";
        $step    = 2;
    }
} elseif (isset($_SESSION['xss_step1'])) {
    $step = 2;
}

if (isset($_SESSION['xss_step2'])) {
    $step = 3;
}

$alreadyDone = isLabCompleted('xss');
if ($alreadyDone) $step = 3;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>XSS Lab</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
body { background:#020617; color:white; font-family:'Inter',sans-serif; overflow-x:hidden; }
.sidebar { position:fixed; width:260px; height:100vh; background:#0f172a; padding:30px 20px; border-right:1px solid rgba(255,255,255,0.05); overflow-y:auto; }
.sidebar-logo { font-size:24px; font-weight:700; margin-bottom:40px; }
.sidebar-logo span { color:#3b82f6; }
.sidebar a { display:flex; align-items:center; gap:12px; color:#94a3b8; text-decoration:none; padding:12px 16px; border-radius:12px; margin-bottom:6px; font-size:14px; transition:.2s; }
.sidebar a:hover, .sidebar a.active { background:rgba(59,130,246,.12); color:white; }
.main { margin-left:260px; padding:40px; }
.lab-header { background:linear-gradient(135deg,#7c3aed22,#0f172a); border:1px solid rgba(124,58,237,.2); border-radius:20px; padding:32px; margin-bottom:32px; }
.badge-diff { background:rgba(239,68,68,.15); color:#f87171; padding:6px 14px; border-radius:20px; font-size:13px; font-weight:600; }
.step-card { background:#0f172a; border:1px solid rgba(255,255,255,.06); border-radius:16px; padding:28px; margin-bottom:24px; }
.step-number { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:50%; font-weight:700; font-size:14px; margin-right:12px; }
.step-active .step-number { background:#7c3aed; color:white; }
.step-done .step-number { background:#22c55e; color:white; }
.step-locked .step-number { background:#1e293b; color:#64748b; }
.fake-browser { background:#1e293b; border-radius:12px; overflow:hidden; margin:20px 0; }
.fake-browser-bar { background:#334155; padding:10px 16px; display:flex; align-items:center; gap:10px; }
.fake-dot { width:12px; height:12px; border-radius:50%; }
.fake-url { background:#0f172a; border-radius:6px; padding:4px 14px; font-size:13px; color:#94a3b8; flex:1; }
.fake-content { padding:20px; }
.vuln-input { background:#0f172a; border:1px solid rgba(255,255,255,.1); border-radius:10px; color:white; padding:10px 14px; width:100%; font-size:14px; }
.vuln-input:focus { outline:none; border-color:#7c3aed; }
.btn-attack { background:linear-gradient(135deg,#7c3aed,#a855f7); border:none; color:white; padding:10px 24px; border-radius:10px; font-weight:600; cursor:pointer; transition:.2s; }
.btn-attack:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(124,58,237,.35); }
.code-block { background:#020617; border:1px solid rgba(255,255,255,.08); border-radius:10px; padding:16px; font-family:monospace; font-size:13px; color:#a78bfa; margin:12px 0; }
.alert-success-custom { background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.25); border-radius:12px; padding:16px 20px; color:#86efac; }
.alert-danger-custom  { background:rgba(239,68,68,.1);  border:1px solid rgba(239,68,68,.25);  border-radius:12px; padding:16px 20px; color:#fca5a5; }
.defense-btn { background:#0f172a; border:1px solid rgba(255,255,255,.08); border-radius:12px; padding:14px 20px; color:#94a3b8; cursor:pointer; transition:.2s; width:100%; text-align:left; margin-bottom:10px; font-size:14px; }
.defense-btn:hover { border-color:#7c3aed; color:white; background:rgba(124,58,237,.1); }
.xp-badge { background:rgba(34,197,94,.15); color:#86efac; padding:6px 14px; border-radius:20px; font-size:13px; font-weight:600; }
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">Cyber<span>Lab</span></div>
    <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="../rooms/phishing_simulator.php"><i class="fas fa-envelope"></i> Phishing</a>
    <a href="../rooms/sql-injection.php"><i class="fas fa-database"></i> SQL Injection</a>
    <a href="../rooms/password-strength.php"><i class="fas fa-lock"></i> Password</a>
    <a href="room3.php" class="active"><i class="fas fa-code"></i> XSS Lab</a>
    <a href="../leaderboard.php"><i class="fas fa-trophy"></i> Leaderboard</a>
</div>

<div class="main">

    <div class="lab-header">
        <div class="d-flex align-items-center gap-3 mb-3">
            <span class="badge-diff">Hard</span>
            <span class="xp-badge"><i class="fas fa-star me-1"></i>+80 XP</span>
            <?php if($alreadyDone): ?>
            <span style="background:rgba(34,197,94,.15);color:#86efac;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;">
                <i class="fas fa-check me-1"></i>Complété
            </span>
            <?php endif; ?>
        </div>
        <h1 style="font-size:32px;font-weight:700;margin-bottom:10px;">
            <i class="fas fa-code" style="color:#a78bfa;margin-right:12px;"></i>
            XSS — Cross-Site Scripting
        </h1>
        <p style="color:#94a3b8;max-width:680px;">
            Le XSS permet à un attaquant d'injecter du code JavaScript malveillant dans une page web
            vue par d'autres utilisateurs. Découvrez comment ça fonctionne et comment s'en protéger.
        </p>
    </div>

    <!-- Étape 1 -->
    <div class="step-card <?= $step >= 1 ? ($step > 1 || $success && $step==1 ? 'step-done' : 'step-active') : 'step-locked' ?>">
        <h5 style="margin-bottom:16px;">
            <span class="step-number <?= $step > 1 || ($success && $step==1) ? 'step-done' : 'step-active' ?>">
                <?= ($step > 1) ? '✓' : '1' ?>
            </span>
            Injecter un payload XSS réfléchi
        </h5>

        <?php if($step == 1): ?>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:20px;">
            Ce site fictif affiche votre recherche sans validation. Injectez un payload XSS dans le champ de recherche.
            <br><strong style="color:#f1f5f9;">Exemple :</strong>
        </p>
        <div class="code-block">&lt;script&gt;alert('XSS')&lt;/script&gt;</div>

        <div class="fake-browser">
            <div class="fake-browser-bar">
                <div class="fake-dot" style="background:#ef4444;"></div>
                <div class="fake-dot" style="background:#f59e0b;"></div>
                <div class="fake-dot" style="background:#22c55e;"></div>
                <div class="fake-url">http://vuln-site.local/search?q=...</div>
            </div>
            <div class="fake-content">
                <p style="color:#94a3b8;font-size:13px;margin-bottom:14px;">Résultats pour : <span style="color:#f87171;" id="reflectPreview">[votre saisie]</span></p>
                <form method="POST">
                    <input type="text" name="search_input" class="vuln-input" placeholder="Tapez votre payload ici..." id="xssInput" autocomplete="off">
                    <div style="margin-top:12px;">
                        <button type="submit" name="step1_submit" class="btn-attack">
                            <i class="fas fa-search me-2"></i>Rechercher
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if($error): ?>
        <div class="alert-danger-custom mt-3"><?= $message ?></div>
        <?php endif; ?>

        <?php elseif($step >= 2): ?>
        <div class="alert-success-custom">
            <i class="fas fa-check-circle me-2"></i>
            <?php if($step == 2): echo $message; else: ?>
            Payload XSS injecté avec succès. La page aurait exécuté votre script.
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Étape 2 -->
    <div class="step-card <?= $step < 2 ? 'step-locked' : ($step > 2 ? 'step-done' : 'step-active') ?>">
        <h5 style="margin-bottom:16px;">
            <span class="step-number <?= $step < 2 ? 'step-locked' : ($step > 2 ? 'step-done' : 'step-active') ?>">
                <?= $step > 2 ? '✓' : '2' ?>
            </span>
            Choisir la bonne défense
        </h5>

        <?php if($step == 2): ?>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:20px;">
            Quelle fonction PHP faut-il utiliser pour afficher <strong style="color:white;">en toute sécurité</strong> une valeur venant de l'utilisateur dans une page HTML ?
        </p>
        <?php if($error): ?>
        <div class="alert-danger-custom mb-3"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST">
            <button type="submit" name="step2_answer" value="1" class="defense-btn" onclick="document.getElementById('def').value='htmlspecialchars'">
                <code style="color:#a78bfa;">htmlspecialchars($input, ENT_QUOTES, 'UTF-8')</code>
                <span style="float:right;font-size:12px;">Encode les caractères HTML</span>
            </button>
            <button type="submit" name="step2_answer" value="1" class="defense-btn" onclick="document.getElementById('def').value='strip_tags'">
                <code style="color:#a78bfa;">strip_tags($input)</code>
                <span style="float:right;font-size:12px;">Supprime les balises</span>
            </button>
            <button type="submit" name="step2_answer" value="1" class="defense-btn" onclick="document.getElementById('def').value='addslashes'">
                <code style="color:#a78bfa;">addslashes($input)</code>
                <span style="float:right;font-size:12px;">Échappe les guillemets</span>
            </button>
            <button type="submit" name="step2_answer" value="1" class="defense-btn" onclick="document.getElementById('def').value='trim'">
                <code style="color:#a78bfa;">trim($input)</code>
                <span style="float:right;font-size:12px;">Supprime les espaces</span>
            </button>
            <input type="hidden" name="defense" id="def" value="">
        </form>

        <?php elseif($step >= 3): ?>
        <div class="alert-success-custom">
            <i class="fas fa-check-circle me-2"></i>
            <code>htmlspecialchars()</code> est la défense correcte contre le XSS réfléchi.
        </div>
        <?php else: ?>
        <p style="color:#475569;font-size:14px;">Complétez l'étape 1 pour débloquer.</p>
        <?php endif; ?>
    </div>

    <!-- Étape 3 : récap -->
    <?php if($step >= 3): ?>
    <div class="step-card" style="border-color:rgba(34,197,94,.25);">
        <h5 style="margin-bottom:20px;color:#86efac;">
            <span class="step-number step-done">✓</span>
            Lab terminé — Résumé
        </h5>
        <?php if(!$alreadyDone): ?>
        <div class="alert-success-custom mb-3"><?= $message ?></div>
        <?php endif; ?>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:16px;">Ce que vous avez appris :</p>
        <ul style="color:#94a3b8;font-size:14px;line-height:2;">
            <li>Le XSS réfléchi injecte du code via l'URL ou un formulaire non validé.</li>
            <li><code style="color:#a78bfa;">htmlspecialchars()</code> convertit <code>&lt;</code>, <code>&gt;</code>, <code>&amp;</code>, <code>"</code> en entités HTML inoffensives.</li>
            <li>Il existe aussi le XSS stocké (en base) et le XSS DOM-based.</li>
            <li>Les Content Security Policy (CSP) ajoutent une couche de défense supplémentaire.</li>
        </ul>
        <div style="margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;">
            <a href="../dashboard.php" class="btn-attack" style="text-decoration:none;">
                <i class="fas fa-home me-2"></i>Retour au dashboard
            </a>
            <a href="../leaderboard.php" style="background:#0f172a;border:1px solid rgba(255,255,255,.08);color:#94a3b8;padding:10px 24px;border-radius:10px;text-decoration:none;font-size:14px;">
                <i class="fas fa-trophy me-2"></i>Voir le classement
            </a>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
const input = document.getElementById('xssInput');
const preview = document.getElementById('reflectPreview');
if (input && preview) {
    input.addEventListener('input', function() {
        // Montre la valeur brute (simulant un site vulnérable)
        preview.textContent = input.value || '[votre saisie]';
    });
}
</script>

<?php
if (file_exists(__DIR__ . '/../includes/chatbot_widget.php'))
    require_once __DIR__ . '/../includes/chatbot_widget.php';
?>
</body>
</html>
