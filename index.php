<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CyberSec Learning Platform</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html{
    scroll-behavior:smooth;
}

body{
    background:
    radial-gradient(circle at top left,#2563eb22,transparent 25%),
    radial-gradient(circle at bottom right,#3b82f622,transparent 25%),
    #020617;
    color:white;
    font-family:'Inter',sans-serif;
    overflow-x:hidden;
}

/* NAVBAR */

.navbar{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    padding:22px 70px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:rgba(2,6,23,0.7);
    backdrop-filter:blur(14px);
    z-index:1000;
    border-bottom:1px solid rgba(255,255,255,0.05);
}

.logo{
    display:flex;
    align-items:center;
    gap:14px;
    font-size:28px;
    font-weight:700;
}

.logo i{
    color:#3b82f6;
}

.nav-links{
    display:flex;
    gap:35px;
}

.nav-links a{
    text-decoration:none;
    color:#cbd5e1;
    transition:0.3s;
    font-weight:500;
}

.nav-links a:hover{
    color:white;
}

/* HERO */

.hero{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:140px 70px 80px;
}

.hero-container{
    max-width:1400px;
    width:100%;
    display:grid;
    grid-template-columns:1.1fr 0.9fr;
    gap:50px;
    align-items:center;
}

/* LEFT */

.hero-left h1{
    font-size:72px;
    font-weight:800;
    line-height:1.1;
    margin-bottom:25px;
}

.hero-left h1 span{
    color:#3b82f6;
}

.hero-left p{
    font-size:20px;
    line-height:1.9;
    color:#94a3b8;
    margin-bottom:40px;
    max-width:700px;
}

.hero-buttons{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
    margin-bottom:50px;
}

.primary-btn{
    display:inline-flex;
    align-items:center;
    gap:12px;
    padding:18px 30px;
    border-radius:18px;
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    color:white;
    text-decoration:none;
    font-weight:600;
    transition:0.3s;
    box-shadow:0 10px 30px rgba(37,99,235,0.35);
}

.primary-btn:hover{
    transform:translateY(-4px);
}

.secondary-btn{
    display:inline-flex;
    align-items:center;
    gap:12px;
    padding:18px 30px;
    border-radius:18px;
    background:#111827;
    border:1px solid rgba(255,255,255,0.08);
    color:white;
    text-decoration:none;
    font-weight:600;
    transition:0.3s;
}

.secondary-btn:hover{
    border-color:#2563eb;
}

/* STATS */

.stats{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:20px;
}

.stat-card{
    background:rgba(15,23,42,0.9);
    border:1px solid rgba(255,255,255,0.05);
    border-radius:24px;
    padding:25px;
    transition:0.3s;
}

.stat-card:hover{
    transform:translateY(-6px);
    border-color:#2563eb;
}

.stat-number{
    font-size:42px;
    font-weight:700;
    color:#3b82f6;
    margin-bottom:10px;
}

.stat-text{
    color:#cbd5e1;
    line-height:1.6;
}

/* RIGHT PANEL */

.cyber-panel{
    background:rgba(15,23,42,0.92);
    border:1px solid rgba(255,255,255,0.05);
    border-radius:32px;
    padding:35px;
    position:relative;
    overflow:hidden;
    box-shadow:0 20px 60px rgba(0,0,0,0.4);
}

.cyber-panel::before{
    content:'';
    position:absolute;
    width:300px;
    height:300px;
    background:#2563eb22;
    border-radius:50%;
    top:-120px;
    right:-120px;
    filter:blur(60px);
    animation:floatGlow 8s ease-in-out infinite;
}

.panel-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
    position:relative;
    z-index:2;
}

.panel-title{
    font-size:22px;
    font-weight:700;
}

.status{
    display:flex;
    align-items:center;
    gap:10px;
    color:#22c55e;
    font-size:14px;
}

.status-dot{
    width:10px;
    height:10px;
    background:#22c55e;
    border-radius:50%;
    animation:pulse 1.5s infinite;
}

.terminal{
    background:black;
    border-radius:24px;
    padding:25px;
    border:1px solid #22c55e;
    font-family:monospace;
    margin-bottom:25px;
    position:relative;
    z-index:2;
}

.terminal-line{
    margin-bottom:12px;
}

.green{
    color:#22c55e;
}

.blue{
    color:#38bdf8;
}

.yellow{
    color:#facc15;
}

.red{
    color:#ef4444;
}

.panel-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
    position:relative;
    z-index:2;
}

.mini-card{
    background:#111827;
    border-radius:22px;
    padding:20px;
    border:1px solid rgba(255,255,255,0.05);
}

.mini-card i{
    font-size:28px;
    color:#3b82f6;
    margin-bottom:15px;
}

.mini-card h4{
    margin-bottom:10px;
}

.mini-card p{
    color:#94a3b8;
    font-size:14px;
    line-height:1.7;
}

/* FEATURES */

.section{
    padding:100px 70px;
}

.section-title{
    text-align:center;
    font-size:48px;
    margin-bottom:20px;
}

.section-subtitle{
    text-align:center;
    color:#94a3b8;
    max-width:800px;
    margin:auto;
    line-height:1.8;
    margin-bottom:60px;
}

.features-grid{
    max-width:1400px;
    margin:auto;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:25px;
}

.feature-card{
    background:rgba(15,23,42,0.9);
    border:1px solid rgba(255,255,255,0.05);
    border-radius:28px;
    padding:35px;
    transition:0.3s;
}

.feature-card:hover{
    transform:translateY(-8px);
    border-color:#2563eb;
}

.feature-icon{
    width:75px;
    height:75px;
    border-radius:22px;
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:30px;
    margin-bottom:25px;
}

.feature-card h3{
    font-size:24px;
    margin-bottom:15px;
}

.feature-card p{
    color:#94a3b8;
    line-height:1.8;
}

/* FOOTER */

.footer{
    text-align:center;
    padding:40px;
    color:#64748b;
    border-top:1px solid rgba(255,255,255,0.05);
}

/* ANIMATIONS */

@keyframes pulse{

    0%{
        opacity:1;
    }

    50%{
        opacity:0.4;
    }

    100%{
        opacity:1;
    }

}

@keyframes floatGlow{

    0%{
        transform:translateY(0px);
    }

    50%{
        transform:translateY(20px);
    }

    100%{
        transform:translateY(0px);
    }

}

/* RESPONSIVE */

@media(max-width:1100px){

    .hero-container{
        grid-template-columns:1fr;
    }

    .hero-left{
        text-align:center;
    }

    .hero-left p{
        margin:auto auto 40px;
    }

    .hero-buttons{
        justify-content:center;
    }

}

@media(max-width:768px){

    .navbar{
        padding:20px;
        flex-direction:column;
        gap:15px;
    }

    .nav-links{
        gap:18px;
        flex-wrap:wrap;
        justify-content:center;
    }

    .hero{
        padding:180px 20px 60px;
    }

    .section{
        padding:80px 20px;
    }

    .hero-left h1{
        font-size:48px;
    }

    .hero-left p{
        font-size:18px;
    }

    .stats{
        grid-template-columns:1fr;
    }

    .panel-grid{
        grid-template-columns:1fr;
    }

}

</style>

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar">

<div class="logo">

<i class="fa-solid fa-shield-halved"></i>

CyberSec

</div>

<div class="nav-links">

<a href="#">Home</a>

<a href="#features">Features</a>

<a href="login.php">Login</a>

</div>

</nav>

<!-- HERO -->

<section class="hero">

<div class="hero-container">

<!-- LEFT -->

<div class="hero-left">

<h1>

Master <span>Cybersecurity</span><br>
Through Realistic Training

</h1>

<p>

CyberSec Learning Platform is an educational cybersecurity environment designed for BTS Réseau & Sécurité students. Practice phishing analysis, SQL Injection testing, password security, Linux fundamentals and real-world attack simulations through immersive hands-on labs.

</p>

<div class="hero-buttons">

<a href="login.php" class="primary-btn">

<i class="fa-solid fa-right-to-bracket"></i>

Get Started

</a>

<a href="register.php" class="secondary-btn">

<i class="fa-solid fa-user-plus"></i>

Create Account

</a>

</div>

<div class="stats">

<div class="stat-card">

<div class="stat-number">
120+
</div>

<div class="stat-text">
Students registered on the learning platform
</div>

</div>

<div class="stat-card">

<div class="stat-number">
50+
</div>

<div class="stat-text">
Cybersecurity labs and simulations completed
</div>

</div>

<div class="stat-card">

<div class="stat-number">
1000+
</div>

<div class="stat-text">
Quiz attempts and security assessments
</div>

</div>

<div class="stat-card">

<div class="stat-number">
24/7
</div>

<div class="stat-text">
Secure learning environment accessibility
</div>

</div>

</div>

</div>

<!-- RIGHT -->

<div class="cyber-panel">

<div class="panel-header">

<div class="panel-title">

Security Operations Panel

</div>

<div class="status">

<div class="status-dot"></div>

System Online

</div>

</div>

<div class="terminal">

<div class="terminal-line green">
[SYSTEM] Initializing cyber environment...
</div>

<div class="terminal-line blue">
[INFO] Secure educational sandbox active
</div>

<div class="terminal-line yellow">
[WARNING] Phishing simulation loaded
</div>

<div class="terminal-line green">
[SUCCESS] SQL Injection lab deployed
</div>

<div class="terminal-line red">
[ALERT] Attack simulation detected
</div>

<div class="terminal-line blue">
[INFO] Student monitoring enabled
</div>

</div>

<div class="panel-grid">

<div class="mini-card">

<i class="fa-solid fa-shield"></i>

<h4>Interactive Labs</h4>

<p>

Hands-on cybersecurity exercises and attack simulations.

</p>

</div>

<div class="mini-card">

<i class="fa-solid fa-terminal"></i>

<h4>Linux Training</h4>

<p>

Practice essential commands and system security operations.

</p>

</div>

<div class="mini-card">

<i class="fa-solid fa-bug"></i>

<h4>Threat Analysis</h4>

<p>

Analyze vulnerabilities and understand attack techniques.

</p>

</div>

<div class="mini-card">

<i class="fa-solid fa-graduation-cap"></i>

<h4>Skill Assessment</h4>

<p>

Evaluate your cybersecurity knowledge through quizzes.

</p>

</div>

</div>

</div>

</div>

</section>

<!-- FEATURES -->

<section class="section" id="features">

<h2 class="section-title">

Platform Features

</h2>

<p class="section-subtitle">

An immersive cybersecurity educational platform combining practical labs, realistic attack simulations, security quizzes and gamified learning for BTS Réseau & Sécurité students.

</p>

<div class="features-grid">

<div class="feature-card">

<div class="feature-icon">
<i class="fa-solid fa-shield-halved"></i>
</div>

<h3>Cybersecurity Labs</h3>

<p>

Practice phishing detection, password auditing, SQL Injection testing and secure authentication concepts.

</p>

</div>

<div class="feature-card">

<div class="feature-icon">
<i class="fa-solid fa-network-wired"></i>
</div>

<h3>Network Security</h3>

<p>

Understand packet analysis, system monitoring and modern network defense strategies.

</p>

</div>

<div class="feature-card">

<div class="feature-icon">
<i class="fa-solid fa-user-secret"></i>
</div>

<h3>Attack Simulations</h3>

<p>

Experience realistic cybersecurity scenarios inspired by real-world threats and vulnerabilities.

</p>

</div>

<div class="feature-card">

<div class="feature-icon">
<i class="fa-solid fa-ranking-star"></i>
</div>

<h3>Gamified Learning</h3>

<p>

Earn XP, complete quizzes and track your cybersecurity progression interactively.

</p>

</div>

</div>

</section>

<!-- FOOTER -->

<footer class="footer">

© 2026 CyberSec Learning Platform • BTS Réseau & Sécurité

</footer>

</body>
</html>