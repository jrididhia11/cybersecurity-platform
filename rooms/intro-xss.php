<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }
if (isset($_POST['start'])) {
    $_SESSION['xss_intro_done'] = true;
    header("Location: xss-lab.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>XSS — Introduction</title>
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
.intro-hero{background:linear-gradient(135deg,#3b0764aa,#0f172a);border:1px solid rgba(168,85,247,.25);border-radius:24px;padding:40px;margin-bottom:28px;text-align:center;}
.tag{display:inline-block;background:rgba(168,85,247,.15);color:#d8b4fe;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;margin-bottom:16px;}
.section{background:#0f172a;border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:28px;margin-bottom:20px;}
.section h5{color:#d8b4fe;margin-bottom:14px;font-size:16px;}
.code-block{background:#020617;border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:18px;font-family:monospace;font-size:13px;line-height:2;margin:12px 0;}
.kw{color:#a78bfa;}.str{color:#fde047;}.vuln{color:#f87171;}.safe{color:#86efac;}.comment{color:#475569;}
.how-step{display:flex;align-items:flex-start;gap:16px;margin-bottom:18px;}
.how-num{min-width:36px;height:36px;border-radius:50%;background:rgba(168,85,247,.15);border:1px solid rgba(168,85,247,.3);color:#d8b4fe;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;}
.impact-box{background:#111827;border-left:4px solid #ef4444;border-radius:0 12px 12px 0;padding:18px 22px;margin-bottom:12px;}
.impact-box h6{color:#f87171;margin-bottom:6px;font-size:14px;}
.stat-box{background:#111827;border-radius:14px;padding:20px;text-align:center;}
.stat-num{font-size:30px;font-weight:700;color:#d8b4fe;}
.stat-label{font-size:13px;color:#64748b;margin-top:4px;}
.btn-start{background:linear-gradient(135deg,#7c3aed,#a855f7);border:none;color:white;padding:16px 40px;border-radius:14px;font-weight:700;font-size:16px;cursor:pointer;transition:.2s;}
.btn-start:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(168,85,247,.35);}
.compare-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:12px;}
.compare-card{background:#111827;border-radius:12px;padding:16px;}
.compare-card h6{font-size:13px;font-weight:700;margin-bottom:10px;}
.xss-types{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-top:12px;}
.type-card{background:#111827;border-radius:12px;padding:18px;border-top:3px solid #7c3aed;}
.type-card h6{color:#d8b4fe;font-size:14px;margin-bottom:8px;}
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">Cyber<span>Lab</span></div>
    <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="intro-phishing.php"><i class="fas fa-envelope"></i> Phishing Lab</a>
    <a href="intro-sql.php"><i class="fas fa-database"></i> SQL Injection</a>
    <a href="intro-password.php"><i class="fas fa-lock"></i> Password</a>
    <a href="intro-xss.php" class="active"><i class="fas fa-code"></i> XSS Lab</a>
    <a href="../leaderboard.php"><i class="fas fa-trophy"></i> Leaderboard</a>
</div>

<div class="main">

    <div class="intro-hero">
        <span class="tag"><i class="fas fa-code me-2"></i>Lab 4 — XSS</span>
        <h1 style="font-size:36px;font-weight:800;margin-bottom:12px;">What is Cross-Site Scripting?</h1>
        <p style="color:#94a3b8;font-size:16px;max-width:600px;margin:0 auto;">An attack that injects malicious JavaScript into websites trusted by users — ranked <strong style="color:white;">#3 in OWASP Top 10</strong>.</p>
    </div>

    <!-- Definition -->
    <div class="section">
        <h5><i class="fas fa-book-open me-2"></i>Definition</h5>
        <p style="color:#94a3b8;font-size:15px;line-height:1.8;">
            <strong style="color:white;">Cross-Site Scripting (XSS)</strong> occurs when an attacker injects malicious JavaScript into a web page that is then viewed by other users. Because the script runs in the victim's browser on a trusted site, it can <strong style="color:white;">steal cookies, hijack sessions, redirect users, or deface pages</strong>.
        </p>
        <p style="color:#94a3b8;font-size:15px;line-height:1.8;margin-bottom:0;">
            Unlike SQL Injection which attacks the server, XSS attacks the <strong style="color:white;">users</strong> of the application.
        </p>
    </div>

    <!-- VIDEO -->
    <div class="section">
        <h5><i class="fas fa-play-circle me-2"></i>Watch — XSS attack in action</h5>
        <p style="color:#94a3b8;font-size:14px;margin-bottom:16px;">See how a malicious script is injected into a web page and how htmlspecialchars() stops it.</p>
        <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:14px;border:1px solid rgba(255,255,255,.08);">
            <iframe src="https://www.youtube-nocookie.com/embed/EoaDgUgS6QA" title="XSS Attack Demo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;border-radius:14px;"></iframe>
        </div>
    </div>

    <!-- Stats -->
    <div class="section">
        <h5><i class="fas fa-chart-bar me-2"></i>XSS by the numbers</h5>
        <div class="row g-3">
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">#3</div><div class="stat-label">OWASP Top 10 most critical web vulnerabilities</div></div></div>
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">75%</div><div class="stat-label">of websites have been affected by XSS at some point</div></div></div>
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">2005</div><div class="stat-label">MySpace XSS worm infected 1 million accounts in 20 hours</div></div></div>
            <div class="col-md-3"><div class="stat-box"><div class="stat-num">$4M+</div><div class="stat-label">paid in XSS bug bounties by Google, Facebook, and others</div></div></div>
        </div>
    </div>

    <!-- 3 Types -->
    <div class="section">
        <h5><i class="fas fa-layer-group me-2"></i>The 3 types of XSS</h5>
        <div class="xss-types">
            <div class="type-card">
                <h6>⚡ Reflected XSS</h6>
                <p style="color:#94a3b8;font-size:13px;margin:0;">The payload is in the URL and reflected back immediately in the response. Requires tricking the victim into clicking a malicious link. <em style="color:#64748b;">— This lab covers this type.</em></p>
            </div>
            <div class="type-card">
                <h6>💾 Stored XSS</h6>
                <p style="color:#94a3b8;font-size:13px;margin:0;">The payload is saved in the database (e.g. a comment or profile field) and executes every time any user views that page. The most dangerous type.</p>
            </div>
            <div class="type-card">
                <h6>🔧 DOM-based XSS</h6>
                <p style="color:#94a3b8;font-size:13px;margin:0;">The payload is processed by client-side JavaScript directly in the browser, without ever being sent to the server. Harder to detect.</p>
            </div>
        </div>
    </div>

    <!-- How it works -->
    <div class="section">
        <h5><i class="fas fa-gears me-2"></i>How it works — step by step</h5>
        <div class="how-step">
            <div class="how-num">1</div>
            <div><strong style="color:white;">The attacker finds an input field</strong><br><span style="color:#94a3b8;font-size:14px;">A search bar, comment box, or URL parameter that reflects user input without sanitizing it.</span></div>
        </div>
        <div class="how-step">
            <div class="how-num">2</div>
            <div><strong style="color:white;">They inject a JavaScript payload</strong><br><span style="color:#94a3b8;font-size:14px;"><code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code> is the classic test — but real attacks steal cookies or redirect to phishing sites.</span></div>
        </div>
        <div class="how-step">
            <div class="how-num">3</div>
            <div><strong style="color:white;">The browser executes it</strong><br><span style="color:#94a3b8;font-size:14px;">Because the script is served from a trusted domain, the browser runs it with full access to the page's cookies and DOM.</span></div>
        </div>
        <div class="how-step" style="margin-bottom:0;">
            <div class="how-num">4</div>
            <div><strong style="color:white;">Session hijacking</strong><br><span style="color:#94a3b8;font-size:14px;"><code>document.cookie</code> sends the victim's session token to the attacker — who can now log in as them without any password.</span></div>
        </div>
    </div>

    <!-- Vulnerable vs Secure -->
    <div class="section">
        <h5><i class="fas fa-shield-halved me-2"></i>Vulnerable vs Secure — side by side</h5>
        <div class="compare-row">
            <div class="compare-card" style="border:1px solid rgba(239,68,68,.2);">
                <h6 style="color:#f87171;">❌ Vulnerable code</h6>
                <div class="code-block" style="font-size:12px;">
<span class="comment">// Input directly echoed — dangerous!</span>
<span class="kw">$search</span> = <span class="vuln">$_GET['q']</span>;
<span class="kw">echo</span> <span class="str">"Results for: <span class="vuln">$search</span>"</span>;

<span class="comment">// Attacker sends:</span>
<span class="comment">// ?q=&lt;script&gt;stealCookies()&lt;/script&gt;</span>
                </div>
            </div>
            <div class="compare-card" style="border:1px solid rgba(34,197,94,.2);">
                <h6 style="color:#86efac;">✅ Secure code</h6>
                <div class="code-block" style="font-size:12px;">
<span class="comment">// Input encoded before display</span>
<span class="kw">$search</span> = <span class="safe">htmlspecialchars</span>(
    <span class="kw">$_GET['q']</span>,
    <span class="str">ENT_QUOTES</span>,
    <span class="str">'UTF-8'</span>
);
<span class="kw">echo</span> <span class="str">"Results for: <span class="safe">$search</span>"</span>;
                </div>
            </div>
        </div>
    </div>

    <!-- Impact -->
    <div class="section">
        <h5><i class="fas fa-skull-crossbones me-2"></i>Real-world impact</h5>
        <div class="impact-box"><h6>🍪 Session hijacking</h6><p style="color:#94a3b8;font-size:14px;margin:0;">Steal the victim's session cookie and take over their account without knowing their password.</p></div>
        <div class="impact-box"><h6>🎣 In-browser phishing</h6><p style="color:#94a3b8;font-size:14px;margin:0;">Inject a fake login form into a trusted page — the victim types their credentials directly to the attacker.</p></div>
        <div class="impact-box" style="border-color:#f59e0b;"><h6 style="color:#fbbf24;">🐛 Self-replicating worms</h6><p style="color:#94a3b8;font-size:14px;margin:0;">Stored XSS can spread automatically — like the 2005 MySpace Samy worm that infected 1 million accounts in one day.</p></div>
    </div>

    <!-- CTA -->
    <div style="text-align:center;padding:20px 0 40px;">
        <p style="color:#64748b;font-size:14px;margin-bottom:20px;">You now understand how XSS works. Time to try it yourself.</p>
        <form method="POST">
            <button type="submit" name="start" class="btn-start">
                <i class="fas fa-play me-2"></i>Start the Lab — XSS
            </button>
        </form>
        <p style="color:#475569;font-size:13px;margin-top:14px;">2 steps · +80 XP</p>
    </div>

</div>

<?php
if (file_exists(__DIR__ . '/../includes/chatbot_widget.php'))
    require_once __DIR__ . '/../includes/chatbot_widget.php';
?>
</body>
</html>
