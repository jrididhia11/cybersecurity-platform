<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }
if (isset($_POST['start'])) {
    $_SESSION['pwd_intro_done'] = true;
    header("Location: password-strength.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Password Security — Introduction</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
body{background:#020617;color:white;font-family:'Inter',sans-serif;}
.sidebar{position:fixed;width:260px;height:100vh;background:#0f172a;padding:30px 20px;border-right:1px solid rgba(255,255,255,.05);overflow-y:auto;}
.sidebar-logo{font-size:24px;font-weight:700;margin-bottom:40px;}
.sidebar-logo span{color:#3b82f6;}
.sidebar a{display:flex;align-items:center;gap:12px;color:#94a3b8;text-decoration:none;padding:12px 16px;border-radius:12px;margin-bottom:6px;font-size:14px;transition:.2s;}
.sidebar a:hover,.sidebar a.active{background:linear-gradient(135deg,#2563eb,#3b82f6);color:white;}
.main{margin-left:260px;padding:40px;max-width:900px;}
.intro-hero{background:linear-gradient(135deg,#17213280,#0f172a);border:1px solid rgba(251,191,36,.2);border-radius:24px;padding:40px;margin-bottom:28px;text-align:center;}
.tag{display:inline-block;background:rgba(251,191,36,.15);color:#fde047;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;margin-bottom:16px;}
.section{background:#0f172a;border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:28px;margin-bottom:20px;}
.section h5{color:#fbbf24;margin-bottom:14px;font-size:16px;}
.stat-box{background:#111827;border-radius:14px;padding:20px;text-align:center;}
.stat-num{font-size:30px;font-weight:700;color:#fde047;}
.stat-label{font-size:13px;color:#64748b;margin-top:4px;}
.pwd-row{display:flex;align-items:center;justify-content:space-between;background:#111827;border-radius:10px;padding:14px 18px;margin-bottom:10px;font-family:monospace;}
.pwd-val{font-size:16px;font-weight:600;}
.crack-time{font-size:13px;padding:4px 12px;border-radius:20px;font-family:'Inter',sans-serif;}
.t-instant{background:rgba(239,68,68,.15);color:#f87171;}
.t-minutes{background:rgba(234,179,8,.15);color:#fde047;}
.t-hours{background:rgba(251,146,60,.15);color:#fb923c;}
.t-years{background:rgba(34,197,94,.15);color:#86efac;}
.attack-card{background:#111827;border-radius:12px;padding:18px;margin-bottom:12px;}
.attack-card h6{font-size:14px;font-weight:700;margin-bottom:8px;}
.rule-item{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:14px;}
.rule-icon{width:32px;height:32px;border-radius:8px;background:rgba(251,191,36,.1);display:flex;align-items:center;justify-content:center;color:#fbbf24;font-size:14px;flex-shrink:0;}
.btn-start{background:linear-gradient(135deg,#b45309,#f59e0b);border:none;color:#000;padding:16px 40px;border-radius:14px;font-weight:700;font-size:16px;cursor:pointer;transition:.2s;}
.btn-start:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(245,158,11,.35);}
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

    <div class="intro-hero">
        <span class="tag"><i class="fas fa-lock me-2"></i>Lab 3 — Password Security</span>
        <h1 style="font-size:36px;font-weight:800;margin-bottom:12px;">Why are weak passwords dangerous?</h1>
        <p style="color:#94a3b8;font-size:16px;max-width:600px;margin:0 auto;">An attacker can test <strong style="color:white;">billions of combinations per second</strong>. The strength of your password is your only defense.</p>
    </div>

    <!-- VIDEO -->
    <div class="section">
        <h5><i class="fas fa-play-circle me-2"></i>Watch — How brute force attacks work</h5>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:16px;">See how weak passwords are cracked in seconds and why complexity is essential.</p>
        <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:14px;border:1px solid rgba(255,255,255,.08);">
            <iframe src="https://www.youtube-nocookie.com/embed/aEmXedmMBzM" title="Password Brute Force Demo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;border-radius:14px;"></iframe>
        </div>
    </div>

    <!-- Stats -->
    <div class="section">
        <h5><i class="fas fa-chart-bar me-2"></i>By the numbers</h5>
        <div class="row g-3">
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">81%</div><div class="stat-label">of data breaches involve a weak or stolen password</div></div></div>
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">123456</div><div class="stat-label">most used password in the world in 2024</div></div></div>
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">65%</div><div class="stat-label">of users reuse the same password across multiple sites</div></div></div>
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">24B</div><div class="stat-label">email/password pairs exposed on the dark web in 2022</div></div></div>
        </div>
    </div>

    <!-- Crack time -->
    <div class="section">
        <h5><i class="fas fa-stopwatch me-2"></i>How long to crack your password?</h5>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:16px;">With a modern GPU (RTX 4090) testing ~100 billion combinations/second:</p>
        <div class="pwd-row"><span class="pwd-val" style="color:#f87171;">123456</span><span class="crack-time t-instant">Instant</span></div>
        <div class="pwd-row"><span class="pwd-val" style="color:#f87171;">password</span><span class="crack-time t-instant">Instant</span></div>
        <div class="pwd-row"><span class="pwd-val" style="color:#fb923c;">Pierre1990</span><span class="crack-time t-hours">~3 minutes</span></div>
        <div class="pwd-row"><span class="pwd-val" style="color:#fde047;">Pierre1990!</span><span class="crack-time t-minutes">~2 hours</span></div>
        <div class="pwd-row"><span class="pwd-val" style="color:#86efac;">xK#9mP$2qL!v</span><span class="crack-time t-years">~400 years</span></div>
        <div class="pwd-row"><span class="pwd-val" style="color:#86efac;">correct-horse-battery-staple</span><span class="crack-time t-years">Several million years</span></div>
    </div>

    <!-- Attack types -->
    <div class="section">
        <h5><i class="fas fa-gears me-2"></i>How attackers crack passwords</h5>
        <div class="attack-card">
            <h6 style="color:#f87171;"><i class="fas fa-book me-2"></i>Dictionary Attack</h6>
            <p style="color:#94a3b8;font-size:14px;margin:0;">Tests millions of common words and known variants. "password", "azerty123", "admin2025" — all present in these lists.</p>
        </div>
        <div class="attack-card">
            <h6 style="color:#fb923c;"><i class="fas fa-repeat me-2"></i>Brute-force</h6>
            <p style="color:#94a3b8;font-size:14px;margin:0;">Tests every possible combination. 6 digits = cracked in under a second. 8 alphanumeric characters = a few hours.</p>
        </div>
        <div class="attack-card">
            <h6 style="color:#fbbf24;"><i class="fas fa-table me-2"></i>Rainbow Tables</h6>
            <p style="color:#94a3b8;font-size:14px;margin:0;">Pre-computed tables of millions of hashes — allows recovering a password from its stored hash in seconds.</p>
        </div>
        <div class="attack-card" style="margin-bottom:0;">
            <h6 style="color:#a5b4fc;"><i class="fas fa-list me-2"></i>Credential Stuffing</h6>
            <p style="color:#94a3b8;font-size:14px;margin:0;">Uses millions of email/password pairs stolen in previous breaches. If you reuse passwords, one hacked site compromises all your accounts.</p>
        </div>
    </div>

    <!-- Golden rules -->
    <div class="section">
        <h5><i class="fas fa-star me-2"></i>Rules for a strong password</h5>
        <div class="rule-item"><div class="rule-icon"><i class="fas fa-ruler"></i></div><div><strong style="color:white;">Minimum 12 characters</strong> — each added character multiplies crack time by 70+</div></div>
        <div class="rule-item"><div class="rule-icon"><i class="fas fa-font"></i></div><div><strong style="color:white;">Mix character types</strong> — uppercase, lowercase, numbers, special characters (!@#$...)</div></div>
        <div class="rule-item"><div class="rule-icon"><i class="fas fa-ban"></i></div><div><strong style="color:white;">Avoid personal info</strong> — first name, birth date, pet name = easily guessable</div></div>
        <div class="rule-item"><div class="rule-icon"><i class="fas fa-copy"></i></div><div><strong style="color:white;">One unique password per site</strong> — use a password manager (Bitwarden, 1Password)</div></div>
        <div class="rule-item" style="border:none;"><div class="rule-icon"><i class="fas fa-mobile"></i></div><div><strong style="color:white;">Enable MFA</strong> — even if the password is stolen, the attacker cannot get in without the 2nd factor</div></div>
    </div>

    <!-- CTA -->
    <div style="text-align:center;padding:20px 0 40px;">
        <p style="color:#64748b;font-size:14px;margin-bottom:20px;">You now understand why password strength is critical. Time to test your skills.</p>
        <form method="POST">
            <button type="submit" name="start" class="btn-start">
                <i class="fas fa-play me-2"></i>Start the Lab — Password Security
            </button>
        </form>
        <p style="color:#475569;font-size:13px;margin-top:14px;">3 steps · up to +70 XP</p>
    </div>

</div>

<?php
if (file_exists(__DIR__ . '/../includes/chatbot_widget.php'))
    require_once __DIR__ . '/../includes/chatbot_widget.php';
?>
</body>
</html>
