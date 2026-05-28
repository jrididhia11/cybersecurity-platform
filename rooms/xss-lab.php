<?php
session_start();
include '../includes/xp_system.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['restart_xss'])) {
    unset($_SESSION['xss_step1'], $_SESSION['xss_step2']);
    $_SESSION['xss_replaying'] = true;
    header("Location: xss-lab.php");
    exit();
}

$step       = 1;
$success    = false;
$error      = false;
$message    = "";
$reflection = "";

// Step 1: Reflected XSS
if (isset($_POST['step1_submit'])) {
    $input = $_POST['search_input'] ?? '';
    // Simulates XSS payload detection (without executing it)
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
        $message = "✔ XSS Payload detected! The page would have executed your script.<br><strong>Step 1 complete.</strong>";
        $success = true;
        // Display encoded value (secure)
        $reflection = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    } else {
        $error   = true;
        $message = "✖ Try a classic XSS payload, e.g: <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code>";
        $step    = 1;
    }
}

// Step 2: identify the correct defense
if (isset($_POST['step2_answer'])) {
    $_SESSION['xss_step1'] = true;
    $answer = $_POST['defense'] ?? '';
    if ($answer === 'htmlspecialchars') {
        $_SESSION['xss_step2'] = true;
        unset($_SESSION['xss_replaying']);
        completeLab('xss', 80);
        $step    = 3;
        $success = true;
        $message = "✔ Correct! <code>htmlspecialchars()</code> encodes dangerous characters.<br><strong>+80 XP earned. Lab completed!</strong>";
    } else {
        $error   = true;
        $message = "✖ That is not the best defense against reflected XSS. Try again.";
        $step    = 2;
    }
} elseif (isset($_SESSION['xss_step1'])) {
    $step = 2;
}

if (isset($_SESSION['xss_step2'])) {
    $step = 3;
}

$alreadyDone = isLabCompleted('xss');
$xssReplaying = !empty($_SESSION['xss_replaying']);
if ($alreadyDone && !$xssReplaying) $step = 3;
?>
<!DOCTYPE html>
<html lang="en">
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
    <a href="intro-phishing.php"><i class="fas fa-envelope"></i> Phishing</a>
    <a href="intro-sql.php"><i class="fas fa-database"></i> SQL Injection</a>
    <a href="intro-password.php"><i class="fas fa-lock"></i> Password</a>
    <a href="intro-xss.php" class="active"><i class="fas fa-code"></i> XSS Lab</a>
    <a href="../leaderboard.php"><i class="fas fa-trophy"></i> Leaderboard</a>
</div>

<div class="main">

    <div class="lab-header">
        <div class="d-flex align-items-center gap-3 mb-3">
            <span class="badge-diff">Hard</span>
            <span class="xp-badge"><i class="fas fa-star me-1"></i>+80 XP</span>
            <?php if($alreadyDone): ?>
            <span style="background:rgba(34,197,94,.15);color:#86efac;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;">
                <i class="fas fa-check me-1"></i>Completed
            </span>
            <?php endif; ?>
        </div>
        <h1 style="font-size:32px;font-weight:700;margin-bottom:10px;">
            <i class="fas fa-code" style="color:#a78bfa;margin-right:12px;"></i>
            XSS — Cross-Site Scripting
        </h1>
        <p style="color:#94a3b8;max-width:680px;">
            XSS allows an attacker to inject malicious JavaScript into a web page viewed by other users. Discover how it works and how to defend against it.
        </p>
    </div>

    <!-- Step 1 -->
    <div class="step-card <?= $step >= 1 ? ($step > 1 || $success && $step==1 ? 'step-done' : 'step-active') : 'step-locked' ?>">
        <h5 style="margin-bottom:16px;">
            <span class="step-number <?= $step > 1 || ($success && $step==1) ? 'step-done' : 'step-active' ?>">
                <?= ($step > 1) ? '✓' : '1' ?>
            </span>
            Inject a reflected XSS payload
        </h5>

        <?php if($step == 1): ?>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:20px;">
            This fictional site displays your search input without validation. Inject an XSS payload into the search field.
            <br><strong style="color:#f1f5f9;">Example:</strong>
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
                <p style="color:#94a3b8;font-size:13px;margin-bottom:14px;">Results for: <span style="color:#f87171;" id="reflectPreview">[your input]</span></p>
                <form method="POST">
                    <input type="text" name="search_input" class="vuln-input" placeholder="Type your payload here..." id="xssInput" autocomplete="off">
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
            XSS payload successfully injected. The page would have executed your script.
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Step 2 -->
    <div class="step-card <?= $step < 2 ? 'step-locked' : ($step > 2 ? 'step-done' : 'step-active') ?>">
        <h5 style="margin-bottom:16px;">
            <span class="step-number <?= $step < 2 ? 'step-locked' : ($step > 2 ? 'step-done' : 'step-active') ?>">
                <?= $step > 2 ? '✓' : '2' ?>
            </span>
            Choose the right defense
        </h5>

        <?php if($step == 2): ?>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:20px;">
            Which PHP function should you use to <strong style="color:white;">safely display</strong> user input in an HTML page?
        </p>
        <?php if($error): ?>
        <div class="alert-danger-custom mb-3"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST">
            <button type="submit" name="step2_answer" value="1" class="defense-btn" onclick="document.getElementById('def').value='htmlspecialchars'">
                <code style="color:#a78bfa;">htmlspecialchars($input, ENT_QUOTES, 'UTF-8')</code>
                <span style="float:right;font-size:12px;">Encodes HTML characters</span>
            </button>
            <button type="submit" name="step2_answer" value="1" class="defense-btn" onclick="document.getElementById('def').value='strip_tags'">
                <code style="color:#a78bfa;">strip_tags($input)</code>
                <span style="float:right;font-size:12px;">Removes HTML tags</span>
            </button>
            <button type="submit" name="step2_answer" value="1" class="defense-btn" onclick="document.getElementById('def').value='addslashes'">
                <code style="color:#a78bfa;">addslashes($input)</code>
                <span style="float:right;font-size:12px;">Escapes quotes</span>
            </button>
            <button type="submit" name="step2_answer" value="1" class="defense-btn" onclick="document.getElementById('def').value='trim'">
                <code style="color:#a78bfa;">trim($input)</code>
                <span style="float:right;font-size:12px;">Removes whitespace</span>
            </button>
            <input type="hidden" name="defense" id="def" value="">
        </form>

        <?php elseif($step >= 3): ?>
        <div class="alert-success-custom">
            <i class="fas fa-check-circle me-2"></i>
            <code>htmlspecialchars()</code> is the correct defense against reflected XSS.
        </div>
        <?php else: ?>
        <p style="color:#475569;font-size:14px;">Complete step 1 to unlock.</p>
        <?php endif; ?>
    </div>

    <!-- Step 3: summary -->
    <?php if($step >= 3): ?>
    <div class="step-card" style="border-color:rgba(34,197,94,.25);">
        <h5 style="margin-bottom:20px;color:#86efac;">
            <span class="step-number step-done">✓</span>
            Lab Completed — Summary
        </h5>
        <?php if(!$alreadyDone): ?>
        <div class="alert-success-custom mb-3"><?= $message ?></div>
        <?php endif; ?>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:16px;">What you learned:</p>
        <ul style="color:#94a3b8;font-size:14px;line-height:2;">
            <li>Reflected XSS injects code via a URL or unvalidated form input.</li>
            <li><code style="color:#a78bfa;">htmlspecialchars()</code> converts <code>&lt;</code>, <code>&gt;</code>, <code>&amp;</code>, <code>"</code> into harmless HTML entities.</li>
            <li>There is also stored XSS (in the database) and DOM-based XSS.</li>
            <li>Content Security Policy (CSP) headers add an extra layer of defense.</li>
        </ul>
        <div style="margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;">
            <a href="../dashboard.php" class="btn-attack" style="text-decoration:none;">
                <i class="fas fa-home me-2"></i>Back to Dashboard
            </a>
            <a href="../leaderboard.php" style="background:#0f172a;border:1px solid rgba(255,255,255,.08);color:#94a3b8;padding:10px 24px;border-radius:10px;text-decoration:none;font-size:14px;">
                <i class="fas fa-trophy me-2"></i>View Leaderboard
            </a>
            <form method="POST">
                <input type="hidden" name="restart_xss" value="1">
                <button type="submit" style="background:linear-gradient(135deg,#4338ca,#6366f1);border:none;color:white;padding:10px 24px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;">
                    <i class="fas fa-rotate-right me-2"></i>Try Again
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>


<?php
if (file_exists(__DIR__ . '/../includes/chatbot_widget.php'))
    require_once __DIR__ . '/../includes/chatbot_widget.php';
?>
</body>
</html>
