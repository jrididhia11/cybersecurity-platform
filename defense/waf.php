<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/xp_system.php';
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

$xpAwarded = awardDefenseXP('waf');
$currentXP  = getXP();
$levelTitle = getLevelTitle($currentXP);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WAF — Defense Center</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{background:#020617;color:white;font-family:'Inter',sans-serif;}
.sidebar{position:fixed;width:270px;height:100vh;background:#0f172a;padding:28px 20px;border-right:1px solid rgba(255,255,255,.05);overflow-y:auto;z-index:100;}
.logo{font-size:22px;font-weight:700;margin-bottom:8px;}
.logo span{color:#3b82f6;}
.back-link{display:flex;align-items:center;gap:8px;color:#475569;font-size:13px;text-decoration:none;margin-bottom:28px;}
.back-link:hover{color:#94a3b8;}
.sidebar-label{font-size:11px;font-weight:700;color:#334155;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;padding-left:4px;}
.sidebar a{display:flex;align-items:center;gap:12px;color:#94a3b8;text-decoration:none;padding:11px 14px;border-radius:12px;margin-bottom:5px;font-size:14px;transition:.2s;}
.sidebar a:hover{background:rgba(255,255,255,.05);color:white;}
.sidebar a.active{background:linear-gradient(135deg,#78350f,#d97706);color:white;font-weight:600;}
.main{margin-left:270px;padding:40px;max-width:880px;}
.page-hero{background:linear-gradient(135deg,#1a1200,#0f172a);border:1px solid rgba(234,179,8,.2);border-radius:20px;padding:36px 40px;margin-bottom:32px;}
.page-hero .tag{display:inline-flex;align-items:center;gap:8px;background:rgba(234,179,8,.15);color:#fde047;padding:5px 16px;border-radius:20px;font-size:12px;font-weight:700;margin-bottom:14px;}
.page-hero h1{font-size:32px;font-weight:800;margin-bottom:10px;}
.page-hero p{color:#94a3b8;font-size:15px;line-height:1.75;margin:0;}
.section{background:#0f172a;border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:28px;margin-bottom:22px;}
.section h5{font-size:16px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px;}
.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px;}
.info-card{background:#111827;border-radius:12px;padding:16px;border-left:3px solid #d97706;}
.info-card h6{font-size:13px;font-weight:700;color:#fbbf24;margin-bottom:8px;}
.info-card p{font-size:13px;color:#64748b;line-height:1.6;margin:0;}
.reaction{background:#1a1200;border:1px solid rgba(234,179,8,.2);border-radius:14px;padding:20px 24px;}
.step{display:flex;align-items:flex-start;gap:14px;margin-bottom:14px;}
.step:last-child{margin-bottom:0;}
.step-num{min-width:30px;height:30px;border-radius:50%;background:rgba(234,179,8,.2);color:#fbbf24;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;}
.step p{font-size:14px;color:#94a3b8;margin:0;line-height:1.6;}
.code-block{background:#020617;border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:18px;font-family:monospace;font-size:12px;line-height:2;overflow-x:auto;}
.cmd{color:#86efac;} .cmt{color:#475569;} .val{color:#fde047;} .key{color:#93c5fd;}
.video-wrap{position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:14px;border:1px solid rgba(255,255,255,.08);}
.video-wrap iframe{position:absolute;top:0;left:0;width:100%;height:100%;}
.nav-footer{display:flex;justify-content:space-between;margin-top:32px;padding-top:24px;border-top:1px solid rgba(255,255,255,.06);}
.nav-btn{display:inline-flex;align-items:center;gap:10px;color:white;text-decoration:none;padding:12px 24px;border-radius:12px;font-size:14px;font-weight:600;transition:.2s;}
.nav-btn:hover{opacity:.85;color:white;}
.nav-btn.prev{background:#1e293b;}
.nav-btn.next{background:linear-gradient(135deg,#78350f,#d97706);}
@media(max-width:768px){.sidebar{display:none;}.main{margin-left:0;padding:20px;}}
</style>
</head>
<body>
<div class="sidebar">
    <div class="logo">Cyber<span>Sec</span></div>
    <a href="../dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    <div class="sidebar-label">Defense Techniques</div>
    <a href="siem.php"><i class="fas fa-file-alt"></i> SIEM / Log Analysis</a>
    <a href="ids-ips.php"><i class="fas fa-radar"></i> IDS / IPS</a>
    <a href="waf.php" class="active"><i class="fas fa-shield-alt"></i> WAF</a>
    <a href="mfa.php"><i class="fas fa-mobile-screen"></i> MFA</a>
    <a href="password-policy.php"><i class="fas fa-key"></i> Password Policy</a>
</div>

<div class="main">
    <div class="page-hero">
        <div class="tag"><i class="fas fa-shield-alt"></i> Technique 3 of 5</div>
        <h1>WAF — Web Application Firewall</h1>
        <p>A WAF sits between the internet and your web application. It inspects every HTTP request and blocks those that match known attack patterns — SQL injections, XSS, CSRF — before they reach your code or database.</p>
    </div>

    <div class="section">
        <h5 style="color:#fbbf24;"><i class="fas fa-play-circle"></i> Watch — WAF in action</h5>
        <div class="video-wrap">
            <iframe src="https://www.youtube-nocookie.com/embed/p8CQcF_9280" title="WAF Demo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>

    <div class="section">
        <h5 style="color:#fbbf24;"><i class="fas fa-info-circle"></i> Key Information</h5>
        <div class="info-grid">
            <div class="info-card">
                <h6><i class="fas fa-ban me-2"></i>What it blocks</h6>
                <p>SQL injection, XSS, CSRF, path traversal (../../etc/passwd), remote code execution, DDoS floods.</p>
            </div>
            <div class="info-card">
                <h6><i class="fas fa-tools me-2"></i>Common tools</h6>
                <p>ModSecurity (Apache/Nginx), Cloudflare WAF, AWS WAF, Imperva, Sucuri.</p>
            </div>
            <div class="info-card">
                <h6><i class="fas fa-layer-group me-2"></i>How it works</h6>
                <p>Rules-based: each request is matched against the OWASP Core Rule Set. If a rule fires, the request is blocked with a 403 error.</p>
            </div>
        </div>
    </div>

    <div class="section">
        <h5 style="color:#fbbf24;"><i class="fas fa-user-shield"></i> How the admin reacts — 4 steps</h5>
        <div class="reaction">
            <div class="step"><div class="step-num">1</div><p><strong style="color:white;">Configure the ruleset</strong> — deploy the OWASP Core Rule Set and tune it to match the application's legitimate traffic patterns.</p></div>
            <div class="step"><div class="step-num">2</div><p><strong style="color:white;">Monitor blocked requests</strong> — review WAF logs daily to identify recurring attack sources and new attack patterns.</p></div>
            <div class="step"><div class="step-num">3</div><p><strong style="color:white;">Fine-tune rules</strong> — add custom rules and whitelist legitimate requests that were falsely blocked.</p></div>
            <div class="step"><div class="step-num">4</div><p><strong style="color:white;">Permanent IP banning</strong> — repeated offenders are added to a deny list across all entry points of the infrastructure.</p></div>
        </div>
    </div>

    <div class="section">
        <h5 style="color:#fbbf24;"><i class="fas fa-terminal"></i> ModSecurity rule example</h5>
        <div class="code-block">
<span class="cmt"># Block SQL injection in GET/POST parameters</span>
<span class="key">SecRule</span> ARGS <span class="val">"@detectSQLi"</span> \
    <span class="val">"id:1001,phase:2,deny,status:403,log,msg:'SQL Injection Detected'"</span>

<span class="cmt"># Block XSS payloads</span>
<span class="key">SecRule</span> ARGS <span class="val">"@detectXSS"</span> \
    <span class="val">"id:1002,phase:2,deny,status:403,log,msg:'XSS Attack Detected'"</span>
        </div>
    </div>

    <div class="nav-footer">
        <a href="ids-ips.php" class="nav-btn prev"><i class="fas fa-arrow-left"></i> Previous : IDS / IPS</a>
        <a href="mfa.php" class="nav-btn next">Next : MFA <i class="fas fa-arrow-right"></i></a>
    </div>
</div>

<!-- XP Toast + Chatbot Widget -->
<style>
.xp-toast-def{position:fixed;top:20px;right:20px;background:linear-gradient(135deg,#14532d,#16a34a);border:1px solid rgba(34,197,94,.3);color:white;padding:12px 20px;border-radius:14px;font-size:14px;font-weight:600;z-index:9998;box-shadow:0 8px 24px rgba(0,0,0,.4);animation:slideIn .4s ease;}
@keyframes slideIn{from{transform:translateX(80px);opacity:0;}to{transform:translateX(0);opacity:1;}}
</style>
<?php if ($xpAwarded): ?>
<div class="xp-toast-def">&#9889; +5 XP &mdash; Defense Center visit&eacute; !</div>
<script>setTimeout(() => document.querySelector('.xp-toast-def')?.remove(), 3000);</script>
<?php endif; ?>
<?php require_once '../includes/chatbot_widget.php'; ?>
</body>
</html>
