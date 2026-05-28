<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/xp_system.php';
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

$xp         = getXP();
$levelTitle = getLevelTitle($xp);
$levelColor = getLevelColor($levelTitle);

// Techniques de défense
$techniques = [
    [
        'key'   => 'siem',
        'title' => 'SIEM',
        'sub'   => 'Security Information & Event Management',
        'desc'  => 'Centralisation, corrélation et analyse des logs de sécurité en temps réel. Détection des anomalies et réponse aux incidents.',
        'icon'  => 'fa-chart-line',
        'color' => '#3b82f6',
        'tools' => 'Splunk · IBM QRadar · Elastic SIEM · Wazuh',
        'url'   => 'defense/siem.php',
    ],
    [
        'key'   => 'ids_ips',
        'title' => 'IDS / IPS',
        'sub'   => 'Intrusion Detection & Prevention System',
        'desc'  => 'Surveillance du trafic réseau pour détecter et bloquer les intrusions par analyse de signatures et de comportements.',
        'icon'  => 'fa-shield-halved',
        'color' => '#10b981',
        'tools' => 'Snort · Suricata · Zeek',
        'url'   => 'defense/ids-ips.php',
    ],
    [
        'key'   => 'waf',
        'title' => 'WAF',
        'sub'   => 'Web Application Firewall',
        'desc'  => 'Filtrage du trafic HTTP/HTTPS pour bloquer les attaques applicatives : injection SQL, XSS, CSRF et autres vulnérabilités OWASP.',
        'icon'  => 'fa-fire-flame-curved',
        'color' => '#f59e0b',
        'tools' => 'ModSecurity · AWS WAF · Cloudflare WAF',
        'url'   => 'defense/waf.php',
    ],
    [
        'key'   => 'mfa',
        'title' => 'MFA',
        'sub'   => 'Multi-Factor Authentication',
        'desc'  => 'Authentification multi-facteurs pour contrer le vol d\'identifiants : quelque chose que tu sais, que tu as, que tu es.',
        'icon'  => 'fa-key',
        'color' => '#8b5cf6',
        'tools' => 'Google Authenticator · Authy · YubiKey FIDO2',
        'url'   => 'defense/mfa.php',
    ],
    [
        'key'   => 'password_policy',
        'title' => 'Politique MDP',
        'sub'   => 'Password Policy & Management',
        'desc'  => 'Gestion du cycle de vie des mots de passe, détection des compromissions et stockage sécurisé avec algorithmes adaptatifs.',
        'icon'  => 'fa-lock',
        'color' => '#ec4899',
        'tools' => 'HIBP API · zxcvbn · bcrypt · Argon2',
        'url'   => 'defense/password-policy.php',
    ],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Defense Center — Plateforme Cybersécurité</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
*{margin:0;padding:0;box-sizing:border-box;}
body{background:radial-gradient(circle at top left,#1e3a8a22,transparent 30%),radial-gradient(circle at bottom right,#10b98122,transparent 30%),#020617;color:white;min-height:100vh;font-family:'Inter',sans-serif;}
.topbar{background:#0f172a;border-bottom:1px solid rgba(255,255,255,.05);padding:16px 32px;display:flex;align-items:center;justify-content:space-between;}
.topbar .logo{font-size:18px;font-weight:800;color:white;}
.topbar .logo span{color:#3b82f6;}
.back-btn{display:inline-flex;align-items:center;gap:8px;color:#64748b;text-decoration:none;font-size:13px;padding:8px 16px;border-radius:10px;border:1px solid rgba(255,255,255,.06);transition:.2s;}
.back-btn:hover{color:white;background:rgba(255,255,255,.05);}
.xp-badge{display:flex;align-items:center;gap:8px;background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);padding:6px 16px;border-radius:20px;font-size:13px;}
.container-main{max-width:1100px;margin:0 auto;padding:50px 24px;}
.hero{text-align:center;margin-bottom:60px;}
.hero-icon{width:80px;height:80px;background:linear-gradient(135deg,#1e3a8a,#3b82f6);border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:36px;margin:0 auto 24px;}
.hero h1{font-size:42px;font-weight:800;margin-bottom:12px;}
.hero p{color:#64748b;font-size:16px;max-width:580px;margin:0 auto;}
.xp-notice{display:inline-flex;align-items:center;gap:8px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);padding:8px 18px;border-radius:20px;color:#34d399;font-size:13px;font-weight:600;margin-top:16px;}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:24px;}
.card-def{background:#0f172a;border:1px solid rgba(255,255,255,.06);border-radius:22px;padding:30px;text-decoration:none;color:white;display:block;transition:.25s;position:relative;overflow:hidden;}
.card-def::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:22px 22px 0 0;transition:.2s;}
.card-def:hover{transform:translateY(-6px);border-color:rgba(255,255,255,.12);box-shadow:0 20px 50px rgba(0,0,0,.4);color:white;}
.card-icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:18px;}
.card-title{font-size:22px;font-weight:800;margin-bottom:4px;}
.card-sub{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;margin-bottom:14px;}
.card-desc{color:#94a3b8;font-size:14px;line-height:1.65;margin-bottom:18px;}
.card-tools{display:flex;flex-wrap:wrap;gap:6px;}
.tool-tag{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);padding:4px 10px;border-radius:8px;font-size:11px;color:#64748b;}
.card-arrow{position:absolute;top:24px;right:24px;color:#1e293b;font-size:18px;transition:.2s;}
.card-def:hover .card-arrow{color:inherit;}
@media(max-width:768px){.hero h1{font-size:30px;}.grid{grid-template-columns:1fr;}}
</style>
</head>
<body>

<div class="topbar">
  <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
  <div class="logo">Cyber<span>Sec</span> — Defense Center</div>
  <div class="xp-badge">
    <i class="fas fa-star" style="color:#fbbf24;font-size:11px;"></i>
    <span><?= $xp ?> XP</span>
    <span style="font-weight:700;color:<?= $levelColor ?>;"><?= htmlspecialchars($levelTitle) ?></span>
  </div>
</div>

<div class="container-main">
  <div class="hero">
    <div class="hero-icon">🛡️</div>
    <h1>Defense Center</h1>
    <p>Maîtrise les cinq techniques de défense professionnelles utilisées par les experts en cybersécurité.</p>
    <div class="xp-notice">
      <i class="fas fa-bolt"></i>
      +5 XP par page visitée (une fois par jour)
    </div>
  </div>

  <div class="grid">
    <?php foreach ($techniques as $t): ?>
    <a href="<?= $t['url'] ?>" class="card-def" style="--c:<?= $t['color'] ?>;">
      <style>.card-def:nth-child(<?= array_search($t, $techniques) + 1 ?>)::before{background:<?= $t['color'] ?>;}</style>
      <i class="card-arrow fas fa-arrow-right" style="color:<?= $t['color'] ?>44;"></i>
      <div class="card-icon" style="background:<?= $t['color'] ?>22;color:<?= $t['color'] ?>;">
        <i class="fas <?= $t['icon'] ?>"></i>
      </div>
      <div class="card-title"><?= $t['title'] ?></div>
      <div class="card-sub" style="color:<?= $t['color'] ?>;"><?= $t['sub'] ?></div>
      <div class="card-desc"><?= $t['desc'] ?></div>
      <div class="card-tools">
        <?php foreach (explode(' · ', $t['tools']) as $tool): ?>
        <span class="tool-tag"><?= htmlspecialchars($tool) ?></span>
        <?php endforeach; ?>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once 'includes/chatbot_widget.php'; ?>
</body>
</html>
