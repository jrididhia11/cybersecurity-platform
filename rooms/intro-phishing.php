<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

if (isset($_POST['start'])) {
    $_SESSION['phishing_intro_done'] = true;
    header("Location: phishing_simulator.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Phishing — Introduction</title>
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
.intro-hero{background:linear-gradient(135deg,#052e1680,#0f172a);border:1px solid rgba(34,197,94,.2);border-radius:24px;padding:40px;margin-bottom:28px;text-align:center;}
.tag{display:inline-block;background:rgba(34,197,94,.15);color:#86efac;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;margin-bottom:16px;}
.section{background:#0f172a;border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:28px;margin-bottom:20px;}
.section h5{color:#86efac;margin-bottom:14px;font-size:16px;}
.how-step{display:flex;align-items:flex-start;gap:16px;margin-bottom:18px;}
.how-num{min-width:36px;height:36px;border-radius:50%;background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.3);color:#86efac;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;}
.impact-box{background:#111827;border-left:4px solid #ef4444;border-radius:0 12px 12px 0;padding:18px 22px;margin-bottom:12px;}
.impact-box h6{color:#f87171;margin-bottom:6px;font-size:14px;}
.fake-email{background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:22px;margin-top:12px;}
.fake-from{font-size:13px;color:#64748b;margin-bottom:4px;}
.fake-from span{color:#f87171;}
.fake-subject{font-weight:700;margin-bottom:14px;font-size:16px;}
.annotation{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);border-radius:8px;padding:8px 14px;font-size:12px;color:#fca5a5;margin-top:8px;}
.annotation i{margin-right:6px;}
.btn-start{background:linear-gradient(135deg,#16a34a,#22c55e);border:none;color:white;padding:16px 40px;border-radius:14px;font-weight:700;font-size:16px;cursor:pointer;transition:.2s;margin-top:8px;}
.btn-start:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(34,197,94,.35);}
.stat-box{background:#111827;border-radius:14px;padding:20px;text-align:center;}
.stat-num{font-size:32px;font-weight:700;color:#86efac;}
.stat-label{font-size:13px;color:#64748b;margin-top:4px;}
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">Cyber<span>Lab</span></div>
    <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="intro-phishing.php" class="active"><i class="fas fa-envelope"></i> Phishing Lab</a>
    <a href="intro-sql.php"><i class="fas fa-database"></i> SQL Injection</a>
    <a href="intro-password.php"><i class="fas fa-lock"></i> Password</a>
    <a href="intro-xss.php"><i class="fas fa-code"></i> XSS Lab</a>
    <a href="../leaderboard.php"><i class="fas fa-trophy"></i> Leaderboard</a>
</div>

<div class="main">

    <div class="intro-hero">
        <span class="tag"><i class="fas fa-envelope me-2"></i>Lab 1 — Phishing</span>
        <h1 style="font-size:36px;font-weight:800;margin-bottom:12px;">What is Phishing?</h1>
        <p style="color:#94a3b8;font-size:16px;max-width:600px;margin:0 auto;">An attack that exploits <strong style="color:white;">human trust</strong> rather than technical vulnerabilities. The most widespread attack in the world.</p>
    </div>

    <!-- Definition -->
    <div class="section">
        <h5><i class="fas fa-book-open me-2"></i>Definition</h5>
        <p style="color:#94a3b8;font-size:15px;line-height:1.8;">
            <strong style="color:white;">Phishing</strong> is a social engineering attack where an attacker impersonates a trusted entity — a bank, a company, a colleague — to <strong style="color:white;">steal sensitive information</strong>: passwords, card numbers, personal data.
        </p>
        <p style="color:#94a3b8;font-size:15px;line-height:1.8;margin-bottom:0;">
            Unlike technical attacks, phishing targets <strong style="color:white;">humans</strong> directly. No security system can stop a user who voluntarily hands over their information.
        </p>
    </div>

    <!-- VIDEO -->
    <div class="section">
        <h5><i class="fas fa-play-circle me-2"></i>Watch — How phishing attacks work</h5>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:16px;">See how a phishing campaign is built and how to spot fraudulent emails.</p>
        <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:14px;border:1px solid rgba(255,255,255,.08);">
            <iframe src="https://www.youtube-nocookie.com/embed/XBkzBrXlle0" title="Phishing Attack Demo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;border-radius:14px;"></iframe>
        </div>
    </div>

    <!-- Statistics -->
    <div class="section">
        <h5><i class="fas fa-chart-bar me-2"></i>Phishing by the numbers</h5>
        <div class="row g-3">
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">91%</div><div class="stat-label">of cyberattacks start with a phishing email</div></div></div>
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">3.4B</div><div class="stat-label">phishing emails sent every day worldwide</div></div></div>
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">1/3</div><div class="stat-label">of users click on a phishing link</div></div></div>
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">30s</div><div class="stat-label">average time before a victim clicks a malicious link</div></div></div>
        </div>
    </div>

    <!-- How it works -->
    <div class="section">
        <h5><i class="fas fa-gears me-2"></i>How it works — step by step</h5>
        <div class="how-step">
            <div class="how-num">1</div>
            <div><strong style="color:white;">The attacker creates a fake email</strong><br><span style="color:#94a3b8;font-size:14px;">They impersonate a well-known brand (PayPal, Microsoft, your bank) with a logo, layout, and professional tone.</span></div>
        </div>
        <div class="how-step">
            <div class="how-num">2</div>
            <div><strong style="color:white;">They create a sense of urgency</strong><br><span style="color:#94a3b8;font-size:14px;">"Your account will be closed in 24h", "Suspicious activity detected" — to make you act without thinking.</span></div>
        </div>
        <div class="how-step">
            <div class="how-num">3</div>
            <div><strong style="color:white;">They redirect to a fake site</strong><br><span style="color:#94a3b8;font-size:14px;">The link leads to a site that looks identical to the real one, but with a slightly different domain.</span></div>
        </div>
        <div class="how-step" style="margin-bottom:0;">
            <div class="how-num">4</div>
            <div><strong style="color:white;">The victim enters their credentials</strong><br><span style="color:#94a3b8;font-size:14px;">Password, card number — sent directly to the attacker in real time.</span></div>
        </div>
    </div>

    <!-- Annotated example -->
    <div class="section">
        <h5><i class="fas fa-magnifying-glass me-2"></i>Annotated phishing email example</h5>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:14px;">Here is a real example — spot the clues:</p>
        <div class="fake-email">
            <div class="fake-from">From: <span>security@paypa1-alerts.com</span></div>
            <div class="annotation"><i class="fas fa-triangle-exclamation"></i>The domain is <strong>paypa1-alerts.com</strong> — not paypal.com. The "l" is replaced by a "1".</div>
            <div class="fake-subject" style="margin-top:14px;">⚠️ URGENT: Your PayPal account has been limited</div>
            <div class="annotation"><i class="fas fa-triangle-exclamation"></i>Alarming subject with emoji to force attention.</div>
            <div style="color:#94a3b8;font-size:14px;margin-top:14px;line-height:1.8;">
                Dear Customer,<br><br>
                We have detected unusual activity on your account. To avoid permanent suspension, please verify your identity immediately.
                <span style="color:#38bdf8;text-decoration:underline;">Click here to secure your account →</span>
            </div>
            <div class="annotation"><i class="fas fa-triangle-exclamation"></i>"Dear Customer" — not your name. The link does not show the real URL.</div>
        </div>
    </div>

    <!-- Impact -->
    <div class="section">
        <h5><i class="fas fa-skull-crossbones me-2"></i>Consequences for the victim</h5>
        <div class="impact-box"><h6>💳 Financial theft</h6><p style="color:#94a3b8;font-size:14px;margin:0;">Access to bank accounts, fraudulent transfers, unauthorized purchases.</p></div>
        <div class="impact-box"><h6>🔑 Account compromise</h6><p style="color:#94a3b8;font-size:14px;margin:0;">The attacker gains access to all your services if you reuse the same password.</p></div>
        <div class="impact-box" style="border-color:#f59e0b;"><h6 style="color:#fbbf24;">🏢 Business impact</h6><p style="color:#94a3b8;font-size:14px;margin:0;">A single tricked employee can give access to the entire internal network (ransomware, espionage).</p></div>
    </div>

    <!-- CTA -->
    <div style="text-align:center;padding:20px 0 40px;">
        <p style="color:#64748b;font-size:14px;margin-bottom:20px;">You now understand how phishing works.</p>
        <form method="POST">
            <button type="submit" name="start" class="btn-start">
                <i class="fas fa-play me-2"></i>Start the Lab — Phishing Simulator
            </button>
        </form>
        <p style="color:#475569;font-size:13px;margin-top:14px;">3 rounds · up to +60 XP</p>
    </div>

</div>

<?php
if (file_exists(__DIR__ . '/../includes/chatbot_widget.php'))
    require_once __DIR__ . '/../includes/chatbot_widget.php';
?>
</body>
</html>
