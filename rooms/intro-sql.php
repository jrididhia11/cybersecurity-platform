<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }
if (isset($_POST['start'])) {
    $_SESSION['sql_intro_done'] = true;
    header("Location: sql-injection.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SQL Injection — Introduction</title>
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
.intro-hero{background:linear-gradient(135deg,#1e1b4b80,#0f172a);border:1px solid rgba(99,102,241,.25);border-radius:24px;padding:40px;margin-bottom:28px;text-align:center;}
.tag{display:inline-block;background:rgba(99,102,241,.15);color:#a5b4fc;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;margin-bottom:16px;}
.section{background:#0f172a;border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:28px;margin-bottom:20px;}
.section h5{color:#a5b4fc;margin-bottom:14px;font-size:16px;}
.code-block{background:#020617;border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:18px;font-family:monospace;font-size:13px;line-height:2;margin:12px 0;}
.kw{color:#818cf8;}.str{color:#fde047;}.vuln{color:#f87171;}.safe{color:#86efac;}.comment{color:#475569;}.ok{color:#86efac;}
.how-step{display:flex;align-items:flex-start;gap:16px;margin-bottom:18px;}
.how-num{min-width:36px;height:36px;border-radius:50%;background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.3);color:#a5b4fc;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;}
.impact-box{background:#111827;border-left:4px solid #ef4444;border-radius:0 12px 12px 0;padding:18px 22px;margin-bottom:12px;}
.impact-box h6{color:#f87171;margin-bottom:6px;font-size:14px;}
.stat-box{background:#111827;border-radius:14px;padding:20px;text-align:center;}
.stat-num{font-size:30px;font-weight:700;color:#a5b4fc;}
.stat-label{font-size:13px;color:#64748b;margin-top:4px;}
.btn-start{background:linear-gradient(135deg,#4338ca,#6366f1);border:none;color:white;padding:16px 40px;border-radius:14px;font-weight:700;font-size:16px;cursor:pointer;transition:.2s;}
.btn-start:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(99,102,241,.35);}
.compare-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:12px;}
.compare-card{background:#111827;border-radius:12px;padding:16px;}
.compare-card h6{font-size:13px;font-weight:700;margin-bottom:10px;}
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

    <div class="intro-hero">
        <span class="tag"><i class="fas fa-database me-2"></i>Lab 2 — SQL Injection</span>
        <h1 style="font-size:36px;font-weight:800;margin-bottom:12px;">What is SQL Injection?</h1>
        <p style="color:#94a3b8;font-size:16px;max-width:600px;margin:0 auto;">The most dangerous web vulnerability for 20 years — ranked <strong style="color:white;">#1 in the OWASP Top 10</strong>.</p>
    </div>

    <!-- Definition -->
    <div class="section">
        <h5><i class="fas fa-book-open me-2"></i>Definition</h5>
        <p style="color:#94a3b8;font-size:15px;line-height:1.8;">
            A <strong style="color:white;">SQL Injection (SQLi)</strong> happens when a web application directly inserts user input into a SQL query without validating it. The attacker can then <strong style="color:white;">alter the query's logic</strong> to bypass authentication, extract the entire database, or delete data.
        </p>
    </div>

    <!-- VIDEO -->
    <div class="section">
        <h5><i class="fas fa-play-circle me-2"></i>Watch — SQL Injection in action</h5>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:16px;">See how a SQL injection attack is carried out and how prepared statements stop it.</p>
        <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:14px;border:1px solid rgba(255,255,255,.08);">
            <iframe src="https://www.youtube-nocookie.com/embed/ciNHn38EyRc" title="SQL Injection Attack Demo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;border-radius:14px;"></iframe>
        </div>
    </div>

    <!-- Stats -->
    <div class="section">
        <h5><i class="fas fa-chart-bar me-2"></i>SQL Injection by the numbers</h5>
        <div class="row g-3">
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">#1</div><div class="stat-label">OWASP Top 10 web vulnerability for over 10 years</div></div></div>
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">65%</div><div class="stat-label">of websites have been vulnerable to SQLi at some point</div></div></div>
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">$4M</div><div class="stat-label">average cost of a data breach caused by SQLi</div></div></div>
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">LinkedIn</div><div class="stat-label">117M accounts stolen via SQL injection in 2012</div></div></div>
        </div>
    </div>

    <!-- Why it works -->
    <div class="section">
        <h5><i class="fas fa-gears me-2"></i>Why does it work?</h5>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:16px;">Imagine a login form. The PHP code builds this SQL query:</p>
        <div class="code-block">
<span class="comment">// What the developer wrote:</span>
<span class="kw">$query</span> = <span class="str">"SELECT * FROM users WHERE username='<span class="vuln">$username</span>' AND password='<span class="vuln">$password</span>'"</span>;

<span class="comment">// With username = admin  →  normal query:</span>
<span class="kw">SELECT</span> * <span class="kw">FROM</span> users <span class="kw">WHERE</span> username=<span class="str">'admin'</span> <span class="kw">AND</span> password=<span class="str">'secret'</span>

<span class="comment">// With username = admin'--  →  manipulated query:</span>
<span class="kw">SELECT</span> * <span class="kw">FROM</span> users <span class="kw">WHERE</span> username=<span class="str">'admin'</span><span class="comment">-- AND password='...'</span>
<span class="comment">// The -- comments out everything after → password completely ignored!</span>
        </div>
    </div>

    <!-- Steps -->
    <div class="section">
        <h5><i class="fas fa-list-ol me-2"></i>Steps of a SQLi attack</h5>
        <div class="how-step">
            <div class="how-num">1</div>
            <div><strong style="color:white;">Detecting the vulnerability</strong><br><span style="color:#94a3b8;font-size:14px;">The attacker types a simple apostrophe <code>'</code> in a field. If the app shows an SQL error, it is vulnerable.</span></div>
        </div>
        <div class="how-step">
            <div class="how-num">2</div>
            <div><strong style="color:white;">Injecting the payload</strong><br><span style="color:#94a3b8;font-size:14px;">They inject SQL code to alter the logic: <code>' OR '1'='1</code> makes the condition always true.</span></div>
        </div>
        <div class="how-step">
            <div class="how-num">3</div>
            <div><strong style="color:white;">Extracting data</strong><br><span style="color:#94a3b8;font-size:14px;">With UNION SELECT, they can dump any table: users, passwords, payment data.</span></div>
        </div>
        <div class="how-step" style="margin-bottom:0;">
            <div class="how-num">4</div>
            <div><strong style="color:white;">Destruction or takeover</strong><br><span style="color:#94a3b8;font-size:14px;"><code>DROP TABLE users</code> — complete wipe. Or creating a hidden admin account.</span></div>
        </div>
    </div>

    <!-- Vulnerable vs Secure -->
    <div class="section">
        <h5><i class="fas fa-shield-halved me-2"></i>Vulnerable vs Secure — side by side</h5>
        <div class="compare-row">
            <div class="compare-card" style="border:1px solid rgba(239,68,68,.2);">
                <h6 style="color:#f87171;">❌ Vulnerable code</h6>
                <div class="code-block" style="font-size:12px;">
<span class="vuln">$q</span> = <span class="str">"SELECT * FROM users
  WHERE user='<span class="vuln">$user</span>'
  AND pass='<span class="vuln">$pass</span>'"</span>;
<span class="vuln">mysqli_query</span>(<span class="kw">$conn</span>, <span class="vuln">$q</span>);
                </div>
            </div>
            <div class="compare-card" style="border:1px solid rgba(34,197,94,.2);">
                <h6 style="color:#86efac;">✅ Secure code</h6>
                <div class="code-block" style="font-size:12px;">
<span class="safe">$stmt</span> = <span class="ok">$conn->prepare</span>(
  <span class="str">"SELECT * FROM users
   WHERE user=? AND pass=?"</span>);
<span class="safe">$stmt</span>-><span class="ok">bind_param</span>(<span class="str">"ss"</span>,<span class="kw">$u</span>,<span class="kw">$p</span>);
<span class="safe">$stmt</span>-><span class="ok">execute</span>();
                </div>
            </div>
        </div>
    </div>

    <!-- Impact -->
    <div class="section">
        <h5><i class="fas fa-skull-crossbones me-2"></i>Real-world consequences</h5>
        <div class="impact-box"><h6>🔓 Authentication bypass</h6><p style="color:#94a3b8;font-size:14px;margin:0;">Log in without a password to any account, including admin.</p></div>
        <div class="impact-box"><h6>📦 Full database dump</h6><p style="color:#94a3b8;font-size:14px;margin:0;">All passwords, emails, and personal data exposed in one query.</p></div>
        <div class="impact-box" style="border-color:#f59e0b;"><h6 style="color:#fbbf24;">💣 Data destruction</h6><p style="color:#94a3b8;font-size:14px;margin:0;">DROP TABLE can wipe the entire database with a single command.</p></div>
    </div>

    <!-- CTA -->
    <div style="text-align:center;padding:20px 0 40px;">
        <p style="color:#64748b;font-size:14px;margin-bottom:20px;">You now understand how SQL Injection works. Time to try it yourself.</p>
        <form method="POST">
            <button type="submit" name="start" class="btn-start">
                <i class="fas fa-play me-2"></i>Start the Lab — SQL Injection
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
