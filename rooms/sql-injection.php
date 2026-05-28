<?php
session_start();
include '../includes/xp_system.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Reset
if (isset($_POST['restart'])) {
    unset($_SESSION['sql_step'], $_SESSION['sql_score']);
    $_SESSION['sql_replaying'] = true;
    header("Location: sql-injection.php");
    exit();
}

if (!isset($_SESSION['sql_step']))  $_SESSION['sql_step']  = 1;
if (!isset($_SESSION['sql_score'])) $_SESSION['sql_score'] = 0;

$step     = $_SESSION['sql_step'];
$feedback = null;

// Step 1: identify the vulnerable query
if (isset($_POST['step1']) && $step == 1) {
    $answer = $_POST['vuln_query'] ?? '';
    if ($answer === 'B') {
        $_SESSION['sql_step'] = 2;
        $step = 2;
        $_SESSION['sql_score']++;
        $feedback = ['right'=>true, 'msg'=>'Correct! Query B directly concatenates the variable into the SQL string with no protection — that is the definition of SQL injection.'];
    } else {
        $feedback = ['right'=>false, 'msg'=>'Incorrect. Query A uses bound parameters (prepare/bind_param) which neutralize any injection. Query B is the vulnerable one — it directly concatenates $username into the SQL.'];
    }
}

// Step 2: bypass the login
if (isset($_POST['step2']) && $step == 2) {
    $payload = trim($_POST['username'] ?? '');
    // Accepte toutes les variantes classiques
    $patterns = ["'", "OR", "1=1", "--", "#", "/*", "admin'", "' or", "or '", "1'"];
    $found = false;
    foreach ($patterns as $p) {
        if (stripos($payload, $p) !== false) { $found = true; break; }
    }
    if ($found) {
        $_SESSION['sql_step'] = 3;
        $step = 3;
        $_SESSION['sql_score']++;
        $feedback = ['right'=>true, 'msg'=>'Injection successful! The resulting query ignores the password and returns all users — the first being the admin.'];
    } else {
        $feedback = ['right'=>false, 'msg'=>'Payload not detected. Try a classic payload like <code>\' OR \'1\'=\'1</code> or <code>admin\'--</code>. The goal is to break the SQL syntax to alter its logic.'];
    }
}

// Step 3: choose the right defense
if (isset($_POST['step3']) && $step == 3) {
    $answer = $_POST['defense'] ?? '';
    if ($answer === 'prepared') {
        $_SESSION['sql_step'] = 4;
        $step = 4;
        $_SESSION['sql_score']++;
        unset($_SESSION['sql_replaying']);
        $xp = $_SESSION['sql_score'] * 23; // max 69 ~= 70
        completeLab('sql', $xp);
        $feedback = ['right'=>true, 'msg'=>'Perfect! Prepared statements separate SQL code from data — injection becomes impossible because parameters are never interpreted as code.'];
    } else {
        $feedback = ['right'=>false, 'msg'=>'Not quite. <code>mysqli_real_escape_string</code> helps but can be bypassed. <code>htmlspecialchars</code> protects against XSS, not SQLi. Prepared statements are the only reliable defense.'];
    }
}

$alreadyDone  = isLabCompleted('sql');
$sqlReplaying = !empty($_SESSION['sql_replaying']);
$sqlRestarted = !empty($_SESSION['sql_restarted']);
if ($sqlRestarted) unset($_SESSION['sql_restarted']);
if ($alreadyDone && !$sqlReplaying && $step < 4) { $step = 4; $_SESSION['sql_step'] = 4; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SQL Injection Lab</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
body{background:#020617;color:white;font-family:'Inter',sans-serif;}
.sidebar{position:fixed;width:260px;height:100vh;background:#0f172a;padding:30px 20px;border-right:1px solid rgba(255,255,255,.05);overflow-y:auto;}
.sidebar-logo{font-size:24px;font-weight:700;margin-bottom:40px;}
.sidebar-logo span{color:#3b82f6;}
.sidebar a{display:flex;align-items:center;gap:12px;color:#94a3b8;text-decoration:none;padding:12px 16px;border-radius:12px;margin-bottom:6px;font-size:14px;transition:.2s;}
.sidebar a:hover,.sidebar a.active{background:linear-gradient(135deg,#2563eb,#3b82f6);color:white;}
.main{margin-left:260px;padding:40px;max-width:1000px;}
.lab-header{background:linear-gradient(135deg,#1e1b4b22,#0f172a);border:1px solid rgba(99,102,241,.2);border-radius:20px;padding:32px;margin-bottom:28px;}
.badge-med{background:rgba(234,179,8,.15);color:#fde047;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;}
.xp-badge{background:rgba(99,102,241,.15);color:#a5b4fc;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;}
.theory{background:#0f172a;border-left:4px solid #6366f1;border-radius:0 12px 12px 0;padding:24px 28px;margin-bottom:28px;}
.theory h5{color:#818cf8;margin-bottom:12px;}
.step-card{background:#0f172a;border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:28px;margin-bottom:20px;}
.step-num{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:50%;font-weight:700;font-size:13px;margin-right:10px;}
.s-active{background:#6366f1;color:white;}
.s-done{background:#22c55e;color:white;}
.s-lock{background:#1e293b;color:#475569;}
.code-block{background:#020617;border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:16px;font-family:monospace;font-size:13px;margin:12px 0;line-height:1.8;}
.vuln{color:#f87171;}.safe{color:#86efac;}.kw{color:#818cf8;}.str{color:#fde047;}.comment{color:#475569;}
.cyber-input{width:100%;background:#111827;border:1px solid rgba(255,255,255,.08);color:white;padding:12px 16px;border-radius:10px;font-family:monospace;font-size:14px;margin-bottom:12px;}
.cyber-input:focus{outline:none;border-color:#6366f1;}
.btn-inject{background:linear-gradient(135deg,#4338ca,#6366f1);border:none;color:white;padding:11px 24px;border-radius:10px;font-weight:600;cursor:pointer;transition:.2s;}
.btn-inject:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(99,102,241,.35);}
.fb-ok{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);border-radius:12px;padding:16px 20px;color:#86efac;margin-bottom:16px;}
.fb-err{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);border-radius:12px;padding:16px 20px;color:#fca5a5;margin-bottom:16px;}
.query-choice{background:#111827;border:2px solid rgba(255,255,255,.06);border-radius:12px;padding:18px;margin-bottom:12px;cursor:pointer;transition:.2s;}
.query-choice:hover{border-color:#6366f1;}
.db-row{background:#0f172a;border:1px solid rgba(34,197,94,.2);border-radius:8px;padding:10px 16px;font-family:monospace;font-size:13px;color:#86efac;margin-bottom:6px;}
.defense-opt{background:#111827;border:2px solid rgba(255,255,255,.06);border-radius:12px;padding:14px 18px;width:100%;text-align:left;color:#94a3b8;cursor:pointer;transition:.2s;margin-bottom:10px;font-size:14px;}
.defense-opt:hover{border-color:#6366f1;color:white;background:rgba(99,102,241,.08);}
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">Cyber<span>Lab</span></div>
    <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="intro-phishing.php"><i class="fas fa-envelope"></i> Phishing Lab</a>
    <a href="intro-sql.php" class="active"><i class="fas fa-database"></i> SQL Injection</a>
    <a href="intro-password.php"><i class="fas fa-lock"></i> Password</a>
    <a href="intro-xss.php"><i class="fas fa-code"></i> XSS Lab</a>
    <a href="../leaderboard.php"><i class="fas fa-trophy"></i> Leaderboard</a>
</div>

<div class="main">

    <div class="lab-header">
        <div class="d-flex gap-2 mb-3">
            <span class="badge-med">Medium</span>
            <span class="xp-badge"><i class="fas fa-star me-1"></i>Up to +70 XP</span>
            <?php if($step>=4): ?><span style="background:rgba(34,197,94,.15);color:#86efac;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;"><i class="fas fa-check me-1"></i>Completed</span><?php endif; ?>
        </div>
        <h1 style="font-size:30px;font-weight:700;margin-bottom:8px;"><i class="fas fa-database me-2" style="color:#818cf8;"></i>SQL Injection Lab</h1>
        <p style="color:#94a3b8;margin:0;">3 steps — understand, exploit, then defend.</p>
    </div>

    <!-- Theory -->
    <div class="theory">
        <h5><i class="fas fa-book-open me-2"></i>How does SQL Injection work?</h5>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:12px;">When an application directly concatenates user input into a SQL query, an attacker can <strong style="color:white;">modify the query's logic</strong> by injecting SQL code.</p>
        <div class="code-block">
<span class="comment">// Vulnerable query:</span>
<span class="kw">SELECT</span> * <span class="kw">FROM</span> users <span class="kw">WHERE</span> username = <span class="str">'</span><span class="vuln">$username</span><span class="str">'</span> <span class="kw">AND</span> password = <span class="str">'$password'</span>

<span class="comment">// If username = admin'-- then the query becomes:</span>
<span class="kw">SELECT</span> * <span class="kw">FROM</span> users <span class="kw">WHERE</span> username = <span class="str">'admin'</span><span class="comment">--' AND password = '...'</span>
<span class="comment">// The -- comments out the rest → password is ignored!</span>
        </div>
    </div>

    <!-- Step 1 -->
    <div class="step-card">
        <h5 style="margin-bottom:18px;">
            <span class="step-num <?= $step>1?'s-done':($step==1?'s-active':'s-lock') ?>"><?= $step>1?'✓':'1' ?></span>
            Identify the vulnerable query
        </h5>
        <?php if($step==1): ?>
        <?php if($feedback): ?><div class="<?= $feedback['right']?'fb-ok':'fb-err' ?>"><?= $feedback['msg'] ?></div><?php endif; ?>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:18px;">Which of these two PHP queries is vulnerable to SQL Injection?</p>
        <form method="POST">
            <div class="query-choice" onclick="document.getElementById('qA').click()">
                <label style="cursor:pointer;width:100%;"><input type="radio" name="vuln_query" id="qA" value="A" style="margin-right:10px;">
                <strong style="color:#86efac;">Query A</strong>
                <div class="code-block" style="margin-top:10px;">
<span class="kw">$stmt</span> = <span class="safe">$conn->prepare</span>(<span class="str">"SELECT * FROM users WHERE username=? AND password=?"</span>);
<span class="kw">$stmt</span>-><span class="safe">bind_param</span>(<span class="str">"ss"</span>, <span class="kw">$username</span>, <span class="kw">$password</span>);
                </div></label>
            </div>
            <div class="query-choice" onclick="document.getElementById('qB').click()">
                <label style="cursor:pointer;width:100%;"><input type="radio" name="vuln_query" id="qB" value="B" style="margin-right:10px;">
                <strong style="color:#f87171;">Query B</strong>
                <div class="code-block" style="margin-top:10px;">
<span class="kw">$query</span> = <span class="str">"SELECT * FROM users WHERE username='<span class="vuln">$username</span>' AND password='<span class="vuln">$password</span>'"</span>;
<span class="kw">$result</span> = <span class="vuln">mysqli_query</span>(<span class="kw">$conn</span>, <span class="kw">$query</span>);
                </div></label>
            </div>
            <button type="submit" name="step1" class="btn-inject mt-2">Submit my answer</button>
        </form>
        <?php elseif($step>=2): ?>
        <div class="fb-ok">✅ Query B — it directly concatenates variables with no protection.</div>
        <?php endif; ?>
    </div>

    <!-- Step 2 -->
    <div class="step-card">
        <h5 style="margin-bottom:18px;">
            <span class="step-num <?= $step>2?'s-done':($step==2?'s-active':'s-lock') ?>"><?= $step>2?'✓':'2' ?></span>
            Bypasser le login
        </h5>
        <?php if($step==2): ?>
        <?php if($feedback && isset($_POST['step2'])): ?><div class="<?= $feedback['right']?'fb-ok':'fb-err' ?>"><?= $feedback['msg'] ?></div><?php endif; ?>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:16px;">This form uses the vulnerable query B. Inject an SQL payload in the username field to log in <strong style="color:white;">without knowing the password.</strong></p>
        <div class="code-block" style="margin-bottom:18px;">
<span class="comment">// Exemple de payload :</span>
<span class="str">' OR '1'='1</span>   <span class="comment">→ WHERE username='' OR '1'='1' (toujours vrai)</span>
<span class="str">admin'--</span>       <span class="comment">→ WHERE username='admin'-- ... (ignore le mdp)</span>
        </div>
        <div style="background:#111827;border:1px solid rgba(255,255,255,.06);border-radius:14px;padding:24px;">
            <p style="color:#64748b;font-size:13px;margin-bottom:16px;"><i class="fas fa-building me-2"></i>SecureBank Admin Panel</p>
            <form method="POST">
                <input type="text" name="username" class="cyber-input" placeholder="Username" autocomplete="off">
                <input type="password" name="password" class="cyber-input" placeholder="Password" value="anything">
                <button type="submit" name="step2" class="btn-inject w-100">Login →</button>
            </form>
        </div>
        <?php elseif($step>=3): ?>
        <div class="fb-ok">✅ Authentication bypass successful — the payload altered the SQL logic.</div>
        <?php else: ?>
        <p style="color:#475569;font-size:14px;">Complete step 1 first.</p>
        <?php endif; ?>
    </div>

    <!-- Step 3 -->
    <div class="step-card">
        <h5 style="margin-bottom:18px;">
            <span class="step-num <?= $step>3?'s-done':($step==3?'s-active':'s-lock') ?>"><?= $step>3?'✓':'3' ?></span>
            Choose the right defense
        </h5>
        <?php if($step==3): ?>
        <?php if($feedback && isset($_POST['step3'])): ?><div class="<?= $feedback['right']?'fb-ok':'fb-err' ?>"><?= $feedback['msg'] ?></div><?php endif; ?>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:18px;">Which technique <strong style="color:white;">permanently eliminates</strong> the risk of SQL injection?</p>
        <form method="POST">
            <button type="submit" name="step3" value="1" class="defense-opt" onclick="document.getElementById('def').value='prepared'">
                <code style="color:#818cf8;">$stmt = $conn->prepare("SELECT * FROM users WHERE username=?")</code><br>
                <span style="font-size:12px;margin-top:4px;display:block;">Prepared Statements</span>
            </button>
            <button type="submit" name="step3" value="1" class="defense-opt" onclick="document.getElementById('def').value='escape'">
                <code style="color:#818cf8;">$u = mysqli_real_escape_string($conn, $username)</code><br>
                <span style="font-size:12px;margin-top:4px;display:block;">Escape special characters</span>
            </button>
            <button type="submit" name="step3" value="1" class="defense-opt" onclick="document.getElementById('def').value='html'">
                <code style="color:#818cf8;">$u = htmlspecialchars($username)</code><br>
                <span style="font-size:12px;margin-top:4px;display:block;">Encode HTML entities</span>
            </button>
            <input type="hidden" name="defense" id="def" value="">
        </form>
        <?php elseif($step>=4): ?>
        <div class="fb-ok">✅ Prepared statements — they separate SQL code from user data.</div>
        <?php else: ?>
        <p style="color:#475569;font-size:14px;">Complete the previous steps first.</p>
        <?php endif; ?>
    </div>

    <!-- Final summary -->
    <?php if($step>=4): ?>
    <div class="step-card" style="border-color:rgba(34,197,94,.25);text-align:center;">
        <div style="font-size:48px;margin-bottom:12px;">🛡️</div>
        <h4 style="color:#86efac;margin-bottom:8px;">Lab Completed!</h4>
        <p style="color:#94a3b8;margin-bottom:20px;">You earned <strong style="color:#fde047;"><?= ($_SESSION['sql_score']??3)*23 ?> XP</strong>. Here is what you learned:</p>
        <ul style="text-align:left;color:#94a3b8;font-size:14px;line-height:2;max-width:560px;margin:0 auto 24px;">
            <li>Vulnerable queries directly concatenate variables into the SQL string.</li>
            <li>An attacker can alter SQL logic using characters like <code>'</code>, <code>--</code>, <code>OR</code>.</li>
            <li>Use <strong style="color:white;">prepared statements</strong> — the only fully reliable defense.</li>
            <li>Never trust user input inside a SQL query.</li>
        </ul>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <form method="POST"><button name="restart" style="background:linear-gradient(135deg,#4338ca,#6366f1);border:none;color:white;padding:12px 28px;border-radius:12px;font-weight:600;cursor:pointer;"><i class="fas fa-rotate-right me-2"></i>Try Again</button></form>
            <a href="../dashboard.php" style="display:inline-block;background:#111827;border:1px solid rgba(255,255,255,.08);color:#94a3b8;padding:12px 28px;border-radius:12px;text-decoration:none;font-size:14px;"><i class="fas fa-home me-2"></i>Dashboard</a>
        </div>
    </div>
    <?php endif; ?>


<?php
if (file_exists(__DIR__ . '/../includes/chatbot_widget.php'))
    require_once __DIR__ . '/../includes/chatbot_widget.php';
?>
</body>
</html>
