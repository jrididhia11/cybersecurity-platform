<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/xp_system.php';
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

$xpAwarded = awardDefenseXP('password_policy');
$currentXP  = getXP();
$levelTitle = getLevelTitle($currentXP);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Password Policy — Defense Center</title>
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
.sidebar a.active{background:linear-gradient(135deg,#3b0764,#7c3aed);color:white;font-weight:600;}
.main{margin-left:270px;padding:40px;max-width:880px;}
.page-hero{background:linear-gradient(135deg,#0e0a1a,#0f172a);border:1px solid rgba(168,85,247,.2);border-radius:20px;padding:36px 40px;margin-bottom:32px;}
.page-hero .tag{display:inline-flex;align-items:center;gap:8px;background:rgba(168,85,247,.15);color:#d8b4fe;padding:5px 16px;border-radius:20px;font-size:12px;font-weight:700;margin-bottom:14px;}
.page-hero h1{font-size:32px;font-weight:800;margin-bottom:10px;}
.page-hero p{color:#94a3b8;font-size:15px;line-height:1.75;margin:0;}
.section{background:#0f172a;border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:28px;margin-bottom:22px;}
.section h5{font-size:16px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px;}
.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px;}
.info-card{background:#111827;border-radius:12px;padding:16px;border-left:3px solid #7c3aed;}
.info-card h6{font-size:13px;font-weight:700;color:#c084fc;margin-bottom:8px;}
.info-card p{font-size:13px;color:#64748b;line-height:1.6;margin:0;}
.reaction{background:#0e0a1a;border:1px solid rgba(168,85,247,.2);border-radius:14px;padding:20px 24px;}
.step{display:flex;align-items:flex-start;gap:14px;margin-bottom:14px;}
.step:last-child{margin-bottom:0;}
.step-num{min-width:30px;height:30px;border-radius:50%;background:rgba(168,85,247,.2);color:#c084fc;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;}
.step p{font-size:14px;color:#94a3b8;margin:0;line-height:1.6;}
.code-block{background:#020617;border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:18px;font-family:monospace;font-size:12px;line-height:2;overflow-x:auto;}
.cmd{color:#86efac;} .cmt{color:#475569;} .val{color:#fde047;} .key{color:#93c5fd;}
.video-wrap{position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:14px;border:1px solid rgba(255,255,255,.08);}
.video-wrap iframe{position:absolute;top:0;left:0;width:100%;height:100%;}
.done-box{background:linear-gradient(135deg,#0e0a1a,#1a0f2a);border:1px solid rgba(168,85,247,.3);border-radius:16px;padding:32px;text-align:center;margin-top:32px;}
.done-box h4{font-size:22px;font-weight:700;margin-bottom:10px;}
.done-box p{color:#94a3b8;font-size:14px;margin-bottom:20px;}
.done-btn{display:inline-flex;align-items:center;gap:10px;background:#1e293b;color:white;text-decoration:none;padding:12px 24px;border-radius:12px;font-size:14px;font-weight:600;}
.done-btn:hover{background:#334155;color:white;}
.nav-footer{display:flex;justify-content:flex-start;margin-top:32px;padding-top:24px;border-top:1px solid rgba(255,255,255,.06);}
.nav-btn{display:inline-flex;align-items:center;gap:10px;color:white;text-decoration:none;padding:12px 24px;border-radius:12px;font-size:14px;font-weight:600;transition:.2s;background:#1e293b;}
.nav-btn:hover{opacity:.85;color:white;}
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
    <a href="waf.php"><i class="fas fa-shield-alt"></i> WAF</a>
    <a href="mfa.php"><i class="fas fa-mobile-screen"></i> MFA</a>
    <a href="password-policy.php" class="active"><i class="fas fa-key"></i> Password Policy</a>
</div>

<div class="main">
    <div class="page-hero">
        <div class="tag"><i class="fas fa-key"></i> Technique 5 of 5</div>
        <h1>Password Policy & Credential Management</h1>
        <p>A password policy defines rules enforced at the system level for acceptable passwords. Combined with a password manager and regular audits, it dramatically reduces the attack surface for brute force and credential stuffing attacks.</p>
    </div>

    <div class="section">
        <h5 style="color:#c084fc;"><i class="fas fa-play-circle"></i> Watch — Password security explained</h5>
        <div class="video-wrap">
            <iframe src="https://www.youtube-nocookie.com/embed/aEmXedmMBzM" title="Password Security" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>

    <div class="section">
        <h5 style="color:#c084fc;"><i class="fas fa-info-circle"></i> Key Information</h5>
        <div class="info-grid">
            <div class="info-card">
                <h6><i class="fas fa-check-circle me-2"></i>NIST 2024 guidelines</h6>
                <p>Minimum 8 characters (15 recommended), no forced rotation unless compromised, check against known breached password lists.</p>
            </div>
            <div class="info-card">
                <h6><i class="fas fa-tools me-2"></i>Tools for admins</h6>
                <p>Have I Been Pwned API, Bitwarden (team password manager), Active Directory password policy, Specops Password Auditor.</p>
            </div>
            <div class="info-card">
                <h6><i class="fas fa-database me-2"></i>Secure storage</h6>
                <p>Never store plain text. Use bcrypt (cost ≥12), Argon2id, or scrypt. Salt each password individually before hashing.</p>
            </div>
        </div>
    </div>

    <div class="section">
        <h5 style="color:#c084fc;"><i class="fas fa-user-shield"></i> How the admin enforces password policy — 4 steps</h5>
        <div class="reaction">
            <div class="step"><div class="step-num">1</div><p><strong style="color:white;">Define and enforce the policy</strong> — configure minimum length, complexity rules, and lockout after failed attempts at Active Directory or application level.</p></div>
            <div class="step"><div class="step-num">2</div><p><strong style="color:white;">Audit against breached password lists</strong> — regularly check company passwords against the HaveIBeenPwned database and force resets for compromised ones.</p></div>
            <div class="step"><div class="step-num">3</div><p><strong style="color:white;">Deploy a password manager</strong> — provide a team password manager (Bitwarden, 1Password Teams) so employees stop reusing weak passwords.</p></div>
            <div class="step"><div class="step-num">4</div><p><strong style="color:white;">Store passwords securely</strong> — audit the application code to ensure all passwords are hashed with bcrypt and never stored in plain text or MD5.</p></div>
        </div>
    </div>

    <div class="section">
        <h5 style="color:#c084fc;"><i class="fas fa-terminal"></i> Secure password hashing in PHP</h5>
        <div class="code-block">
<span class="cmt"># Correct password hashing with bcrypt (cost factor 12)</span>
<span class="key">$hash</span> = <span class="cmd">password_hash</span>(<span class="val">$password</span>, PASSWORD_BCRYPT, [<span class="val">'cost'</span> => 12]);

<span class="cmt"># Verifying at login</span>
<span class="key">if</span> (<span class="cmd">password_verify</span>(<span class="val">$input</span>, <span class="val">$hash</span>)) {
    <span class="cmt">// login success</span>
}

<span class="cmt"># Check if password needs rehashing (cost upgrade)</span>
<span class="key">if</span> (<span class="cmd">password_needs_rehash</span>(<span class="val">$hash</span>, PASSWORD_BCRYPT, [<span class="val">'cost'</span> => 12])) {
    <span class="key">$hash</span> = <span class="cmd">password_hash</span>(<span class="val">$input</span>, PASSWORD_BCRYPT, [<span class="val">'cost'</span> => 12]);
}
        </div>
    </div>

    <!-- Completion box -->
    <div class="done-box">
        <div style="font-size:48px;margin-bottom:16px;">🎉</div>
        <h4>You've completed the Defense Center!</h4>
        <p>You now know the 5 key defense techniques used by network administrators to detect and respond to cyber attacks.</p>
        <a href="../dashboard.php" class="done-btn"><i class="fas fa-home me-2"></i> Back to Dashboard</a>
    </div>

    <div class="nav-footer">
        <a href="mfa.php" class="nav-btn"><i class="fas fa-arrow-left"></i> Previous : MFA</a>
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
