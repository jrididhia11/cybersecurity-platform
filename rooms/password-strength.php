<?php
session_start();
include '../includes/xp_system.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['restart'])) {
    unset($_SESSION['pwd_step'], $_SESSION['pwd_score']);
    $_SESSION['pwd_replaying'] = true;
    header("Location: password-strength.php");
    exit();
}

if (!isset($_SESSION['pwd_step']))  $_SESSION['pwd_step']  = 1;
if (!isset($_SESSION['pwd_score'])) $_SESSION['pwd_score'] = 0;

$step     = $_SESSION['pwd_step'];
$feedback = null;

// Step 1: identify weak passwords
if (isset($_POST['step1']) && $step == 1) {
    $picks   = $_POST['weak_picks'] ?? [];
    $correct = ['A','C','E']; // les 3 faibles
    sort($picks);
    sort($correct);
    if ($picks === $correct) {
        $_SESSION['pwd_step'] = 2; $step = 2; $_SESSION['pwd_score']++;
        $feedback = ['right'=>true, 'msg'=>'Perfect! You correctly identified the 3 weak passwords.'];
    } else {
        $feedback = ['right'=>false, 'msg'=>'Not quite. The weak passwords are A (too short and common), C (predictable birth date), and E (dictionary word + simple numbers). B and D are strong because they are long, random, and complex.'];
    }
}

// Step 2: estimate crack time
if (isset($_POST['step2']) && $step == 2) {
    $answer = $_POST['crack_time'] ?? '';
    if ($answer === 'instant') {
        $_SESSION['pwd_step'] = 3; $step = 3; $_SESSION['pwd_score']++;
        $feedback = ['right'=>true, 'msg'=>'Correct! "123456" appears in every compromised password database. It is cracked in under a millisecond by a dictionary attack.'];
    } else {
        $feedback = ['right'=>false, 'msg'=>'"123456" is the most used password in the world. It is in every attack wordlist — cracked instantly, not in minutes or hours.'];
    }
}

// Step 3: build a strong password
if (isset($_POST['step3']) && $step == 3) {
    $pwd = $_POST['my_password'] ?? '';
    $score = 0;
    $checks = [
        'length'    => strlen($pwd) >= 12,
        'upper'     => (bool)preg_match('/[A-Z]/', $pwd),
        'lower'     => (bool)preg_match('/[a-z]/', $pwd),
        'digit'     => (bool)preg_match('/[0-9]/', $pwd),
        'special'   => (bool)preg_match('/[\W_]/', $pwd),
        'no_common' => !preg_match('/password|123456|azerty|qwerty|admin|letmein/i', $pwd),
    ];
    $score = array_sum($checks);
    if ($score >= 5) {
        $_SESSION['pwd_step'] = 4; $step = 4; $_SESSION['pwd_score']++;
        $xp = $_SESSION['pwd_score'] * 13;
        unset($_SESSION['pwd_replaying']);
        completeLab('password', $xp);
        $feedback = ['right'=>true, 'msg'=>'Excellent password! It meets all the best practices.', 'checks'=>$checks, 'score'=>$score];
    } else {
        $feedback = ['right'=>false, 'msg'=>'This password does not meet all the criteria yet.', 'checks'=>$checks, 'score'=>$score];
    }
}

$alreadyDone  = isLabCompleted('password');
$pwdReplaying = !empty($_SESSION['pwd_replaying']);
if ($alreadyDone && !$pwdReplaying && $step < 4) { $step = 4; $_SESSION['pwd_step'] = 4; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Password Security Lab</title>
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
.lab-header{background:linear-gradient(135deg,#17213222,#0f172a);border:1px solid rgba(251,191,36,.15);border-radius:20px;padding:32px;margin-bottom:28px;}
.badge-easy{background:rgba(34,197,94,.15);color:#86efac;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;}
.xp-badge{background:rgba(251,191,36,.15);color:#fde047;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;}
.theory{background:#0f172a;border-left:4px solid #f59e0b;border-radius:0 12px 12px 0;padding:24px 28px;margin-bottom:28px;}
.theory h5{color:#fbbf24;margin-bottom:12px;}
.step-card{background:#0f172a;border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:28px;margin-bottom:20px;}
.step-num{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:50%;font-weight:700;font-size:13px;margin-right:10px;}
.s-active{background:#f59e0b;color:#000;}
.s-done{background:#22c55e;color:white;}
.s-lock{background:#1e293b;color:#475569;}
.fb-ok{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);border-radius:12px;padding:16px 20px;color:#86efac;margin-bottom:16px;}
.fb-err{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);border-radius:12px;padding:16px 20px;color:#fca5a5;margin-bottom:16px;}
.pwd-card{background:#111827;border:2px solid rgba(255,255,255,.06);border-radius:12px;padding:16px 20px;margin-bottom:10px;cursor:pointer;transition:.2s;display:flex;align-items:center;gap:14px;}
.pwd-card:hover{border-color:#f59e0b;}
.pwd-label{font-family:monospace;font-size:16px;font-weight:600;flex:1;}
.pwd-hint{font-size:12px;color:#64748b;}
.cyber-input{width:100%;background:#111827;border:1px solid rgba(255,255,255,.08);color:white;padding:12px 16px;border-radius:10px;font-family:monospace;font-size:16px;letter-spacing:2px;margin-bottom:12px;}
.cyber-input:focus{outline:none;border-color:#f59e0b;}
.btn-check{background:linear-gradient(135deg,#b45309,#f59e0b);border:none;color:#000;padding:11px 24px;border-radius:10px;font-weight:700;cursor:pointer;transition:.2s;}
.btn-check:hover{transform:translateY(-2px);}
.check-row{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:14px;color:#94a3b8;}
.check-ok{color:#86efac;}.check-fail{color:#f87171;}
.strength-bar{height:8px;border-radius:4px;margin:8px 0;transition:.4s;}
.crack-opt{background:#111827;border:2px solid rgba(255,255,255,.06);border-radius:12px;padding:14px 18px;width:100%;text-align:left;color:#94a3b8;cursor:pointer;margin-bottom:10px;font-size:14px;transition:.2s;}
.crack-opt:hover{border-color:#f59e0b;color:white;}
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">Cyber<span>Lab</span></div>
    <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="intro-phishing.php"><i class="fas fa-envelope"></i> Phishing Lab</a>
    <a href="intro-sql.php"><i class="fas fa-database"></i> SQL Injection</a>
    <a href="intro-password.php" class="active"><i class="fas fa-lock"></i> Password</a>
    <a href="intro-xss.php"><i class="fas fa-code"></i> XSS Lab</a>
    <a href="../leaderboard.php"><i class="fas fa-trophy"></i> Leaderboard</a>
</div>

<div class="main">

    <div class="lab-header">
        <div class="d-flex gap-2 mb-3">
            <span class="badge-easy">Easy</span>
            <span class="xp-badge"><i class="fas fa-star me-1"></i>Up to +40 XP</span>
            <?php if($step>=4): ?><span style="background:rgba(34,197,94,.15);color:#86efac;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;"><i class="fas fa-check me-1"></i>Completed</span><?php endif; ?>
        </div>
        <h1 style="font-size:30px;font-weight:700;margin-bottom:8px;"><i class="fas fa-shield-halved me-2" style="color:#fbbf24;"></i>Password Security Lab</h1>
        <p style="color:#94a3b8;margin:0;">3 steps — identify, analyze, and create a strong password.</p>
    </div>

    <!-- Theory -->
    <div class="theory">
        <h5><i class="fas fa-book-open me-2"></i>Why are weak passwords dangerous?</h5>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:12px;">An attacker can test <strong style="color:white;">billions of combinations per second</strong> with modern hardware. A 6-digit password is cracked in under a second.</p>
        <div class="row g-3" style="font-size:13px;">
            <div class="col-md-4"><div style="background:#111827;border-radius:10px;padding:14px;"><div style="color:#f87171;font-weight:700;margin-bottom:6px;">🔴 Weak</div><code>123456</code>, <code>password</code><br><span style="color:#64748b;">Cracked instantly</span></div></div>
            <div class="col-md-4"><div style="background:#111827;border-radius:10px;padding:14px;"><div style="color:#fbbf24;font-weight:700;margin-bottom:6px;">🟡 Medium</div><code>Pierre2001!</code><br><span style="color:#64748b;">Cracked in minutes</span></div></div>
            <div class="col-md-4"><div style="background:#111827;border-radius:10px;padding:14px;"><div style="color:#86efac;font-weight:700;margin-bottom:6px;">🟢 Strong</div><code>xK#9mP$2qL!vR</code><br><span style="color:#64748b;">Cracked in ~400 years</span></div></div>
        </div>
    </div>

    <!-- Step 1 -->
    <div class="step-card">
        <h5 style="margin-bottom:18px;">
            <span class="step-num <?= $step>1?'s-done':($step==1?'s-active':'s-lock') ?>"><?= $step>1?'✓':'1' ?></span>
            Identify the weak passwords
        </h5>

        <?php if($step == 1): ?>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:16px;">Select the <strong style="color:white;">weak passwords</strong> from the list, then click <strong style="color:white;">Validate</strong> to see if you are right.</p>

        <?php if($feedback && isset($_POST['step1']) && !$feedback['right']): ?>
        <div class="fb-err" style="margin-bottom:16px;">
            ❌ Not quite — <strong style="color:white;">A</strong> (pass123), <strong style="color:white;">C</strong> (Pierre1990!) and <strong style="color:white;">E</strong> (football2025) are the weak ones. Try again !
        </div>
        <?php endif; ?>

        <form method="POST">
            <?php
            $pwds = [
                'A' => ['pwd'=>'pass123',               'hint'=>'8 chars, very common'],
                'B' => ['pwd'=>'mK#8xP!2qL$vR9',       'hint'=>'15 chars, random'],
                'C' => ['pwd'=>'Pierre1990!',            'hint'=>'First name + birth year'],
                'D' => ['pwd'=>'correct-horse-battery',  'hint'=>'Long passphrase'],
                'E' => ['pwd'=>'football2025',           'hint'=>'Dictionary word + year'],
            ];
            foreach($pwds as $k=>$p): ?>
            <div class="pwd-card" style="cursor:pointer;" onclick="var c=document.getElementById('chk<?=$k?>');c.checked=!c.checked;">
                <input type="checkbox" name="weak_picks[]" value="<?=$k?>" id="chk<?=$k?>" style="width:18px;height:18px;accent-color:#f59e0b;flex-shrink:0;">
                <div>
                    <div class="pwd-label"><?= htmlspecialchars($p['pwd']) ?></div>
                    <div class="pwd-hint"><?= $p['hint'] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            <button type="submit" name="step1" style="display:block;width:100%;margin-top:16px;background:linear-gradient(135deg,#b45309,#f59e0b);border:none;color:#000;padding:14px 24px;border-radius:12px;font-weight:700;font-size:15px;cursor:pointer;">
                <i class="fas fa-check-circle" style="margin-right:8px;"></i>Validate my answers
            </button>
        </form>

        <?php elseif($step >= 2): ?>
        <div class="fb-ok">✅ Correct — <code>pass123</code>, <code>Pierre1990!</code> and <code>football2025</code> are the 3 weak passwords.</div>

        <?php endif; ?>
    </div>

    <!-- Step 2 -->
    <div class="step-card">
        <h5 style="margin-bottom:18px;">
            <span class="step-num <?= $step>2?'s-done':($step==2?'s-active':'s-lock') ?>"><?= $step>2?'✓':'2' ?></span>
            How long to crack "123456"?
        </h5>
        <?php if($step==2): ?>
        <?php if($feedback && isset($_POST['step2'])): ?><div class="<?= $feedback['right']?'fb-ok':'fb-err' ?>"><?= $feedback['msg'] ?></div><?php endif; ?>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:18px;">An attacker uses a <strong style="color:white;">dictionary attack</strong> on the hash of "123456". What is the estimated time?</p>
        <form method="POST">
            <button type="submit" name="step2" value="1" class="crack-opt" onclick="document.getElementById('ct').value='days'"><i class="fas fa-clock me-2"></i>Several days</button>
            <button type="submit" name="step2" value="1" class="crack-opt" onclick="document.getElementById('ct').value='hours'"><i class="fas fa-clock me-2"></i>A few hours</button>
            <button type="submit" name="step2" value="1" class="crack-opt" onclick="document.getElementById('ct').value='minutes'"><i class="fas fa-clock me-2"></i>A few minutes</button>
            <button type="submit" name="step2" value="1" class="crack-opt" onclick="document.getElementById('ct').value='instant'"><i class="fas fa-bolt me-2"></i>Instant (&lt; 1ms)</button>
            <input type="hidden" name="crack_time" id="ct" value="">
        </form>
        <?php elseif($step>=3): ?>
        <div class="fb-ok">✅ Instant — "123456" has been in every wordlist for 20 years.</div>
        <?php else: ?>
        <p style="color:#475569;font-size:14px;">Complete step 1 first.</p>
        <?php endif; ?>
    </div>

    <!-- Step 3 -->
    <div class="step-card">
        <h5 style="margin-bottom:18px;">
            <span class="step-num <?= $step>3?'s-done':($step==3?'s-active':'s-lock') ?>"><?= $step>3?'✓':'3' ?></span>
            Create a strong password
        </h5>
        <?php if($step==3): ?>
        <?php if($feedback && isset($_POST['step3'])): ?>
        <div class="<?= $feedback['right']?'fb-ok':'fb-err' ?>"><?= $feedback['msg'] ?></div>
        <?php if(isset($feedback['checks'])): ?>
        <div style="margin-bottom:16px;">
            <?php
            $labels = ['length'=>'12+ characters','upper'=>'Uppercase letter','lower'=>'Lowercase letter','digit'=>'Number','special'=>'Special character (!@#...)','no_common'=>'Not a common word'];
            foreach($feedback['checks'] as $k=>$v): ?>
            <div class="check-row"><span class="<?=$v?'check-ok':'check-fail'?>"><?=$v?'✅':'❌'?></span><?=$labels[$k]?></div>
            <?php endforeach; ?>
            <div style="margin-top:10px;">
                <div style="display:flex;justify-content:space-between;font-size:12px;color:#64748b;"><span>Force</span><span><?=$feedback['score']?>/6</span></div>
                <?php $pct = round($feedback['score']/6*100); ?>
                <div class="strength-bar" style="background:linear-gradient(90deg,<?=$pct>=83?'#22c55e':($pct>=50?'#f59e0b':'#ef4444')?> <?=$pct?>%,#1e293b <?=$pct?>%);"></div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:16px;">Create a password that meets <strong style="color:white;">all the criteria</strong> below:</p>
        <ul style="color:#94a3b8;font-size:13px;line-height:2;margin-bottom:18px;padding-left:20px;">
            <li>At least 12 characters</li>
            <li>Uppercase + lowercase + number + special character</li>
            <li>Not a dictionary word</li>
        </ul>
        <form method="POST">
            <input type="text" name="my_password" class="cyber-input" placeholder="Type your password here..." id="pwdInput" autocomplete="off">
            <div id="liveBar" style="height:6px;border-radius:4px;background:#1e293b;margin-bottom:12px;transition:.3s;"></div>
            <button type="submit" name="step3" class="btn-check">Analyze my password</button>
        </form>
        <?php elseif($step>=4): ?>
        <div class="fb-ok">✅ Strong password validated — it would withstand a brute-force attack.</div>
        <?php else: ?>
        <p style="color:#475569;font-size:14px;">Complete the previous steps first.</p>
        <?php endif; ?>
    </div>

    <!-- Summary -->
    <?php if($step>=4): ?>
    <div class="step-card" style="border-color:rgba(34,197,94,.25);text-align:center;">
        <div style="font-size:48px;margin-bottom:12px;">🔐</div>
        <h4 style="color:#86efac;margin-bottom:8px;">Lab Completed!</h4>
        <p style="color:#94a3b8;margin-bottom:20px;">You now understand the basics of password security.</p>
        <ul style="text-align:left;color:#94a3b8;font-size:14px;line-height:2;max-width:560px;margin:0 auto 24px;">
            <li>Common passwords are cracked <strong style="color:white;">instantly</strong> by dictionary attack.</li>
            <li>A good password: 12+ characters, mixed, no common words.</li>
            <li>Use a <strong style="color:white;">password manager</strong> (Bitwarden, 1Password).</li>
            <li>Enable <strong style="color:white;">MFA</strong> on all your important accounts.</li>
        </ul>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <form method="POST"><button name="restart" style="background:linear-gradient(135deg,#b45309,#f59e0b);border:none;color:#000;padding:12px 28px;border-radius:12px;font-weight:700;cursor:pointer;"><i class="fas fa-rotate-right me-2"></i>Try Again</button></form>
            <a href="../dashboard.php" style="display:inline-block;background:#111827;border:1px solid rgba(255,255,255,.08);color:#94a3b8;padding:12px 28px;border-radius:12px;text-decoration:none;font-size:14px;"><i class="fas fa-home me-2"></i>Dashboard</a>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
// Real-time strength bar
const input = document.getElementById('pwdInput');
const bar   = document.getElementById('liveBar');
if(input && bar){
    input.addEventListener('input', function(){
        const v = this.value;
        let s = 0;
        if(v.length>=12) s++;
        if(/[A-Z]/.test(v)) s++;
        if(/[a-z]/.test(v)) s++;
        if(/[0-9]/.test(v)) s++;
        if(/[\W_]/.test(v)) s++;
        if(!/password|123456|azerty|qwerty|admin/i.test(v) && v.length>0) s++;
        const pct = Math.round(s/6*100);
        const col = pct>=83?'#22c55e':(pct>=50?'#f59e0b':'#ef4444');
        bar.style.background = `linear-gradient(90deg,${col} ${pct}%,#1e293b ${pct}%)`;
    });
}
</script>


<?php
if (file_exists(__DIR__ . '/../includes/chatbot_widget.php'))
    require_once __DIR__ . '/../includes/chatbot_widget.php';
?>
</body>
</html>
