<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db.php';
require_once 'includes/logger.php';
require_once 'includes/csrf.php';
require_once 'includes/n8n.php';

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");

$error = "";

if (isset($_GET['error']) && $_GET['error'] === 'session_expired') {
    $error = "Your session expired. Please try logging in again.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF verification
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        regenerateCSRFToken();
        header("Location: login.php?error=session_expired");
        exit();
    }
    regenerateCSRFToken();

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $ip = $_SERVER['REMOTE_ADDR'];

    $stmt = $conn->prepare("
        SELECT COUNT(*) as total 
        FROM failed_logins 
        WHERE ip_address=? 
        AND created_at > (NOW() - INTERVAL 10 MINUTE)
    ");

    $stmt->bind_param("s", $ip);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    if ($result['total'] >= 5) {
        // Alerte n8n — notifier l'administrateur
        triggerBruteForceAlert($ip, $email ?? 'unknown');
        die('Too many failed attempts. Try again later.');
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {

        if ($user['status'] === 'suspended') {
            die('Your account is suspended');
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];

        session_regenerate_id(true);

        logAction($_SESSION['user_id'], 'Logged in');

        if ($user['role'] === 'admin') {
            header("Location: admin/admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }

        exit();

    } else {

        $stmt = $conn->prepare("
            INSERT INTO failed_logins(email, ip_address)
            VALUES(?, ?)
        ");

        $stmt->bind_param("ss", $email, $ip);
        $stmt->execute();

        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CyberSec Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    min-height:100vh;

    overflow:hidden;

    background:
    radial-gradient(circle at top left,#2563eb22,transparent 25%),
    radial-gradient(circle at bottom right,#3b82f622,transparent 25%),
    #020617;

    font-family:'Inter',sans-serif;

    position:relative;
}

/* Animated Background */

body::before{

    content:'';

    position:absolute;

    width:600px;
    height:600px;

    background:#2563eb22;

    border-radius:50%;

    top:-200px;
    left:-200px;

    filter:blur(80px);

    animation:floatGlow 8s ease-in-out infinite;
}

body::after{

    content:'';

    position:absolute;

    width:500px;
    height:500px;

    background:#3b82f622;

    border-radius:50%;

    bottom:-200px;
    right:-200px;

    filter:blur(80px);

    animation:floatGlow 10s ease-in-out infinite;
}

@keyframes floatGlow{

    0%{
        transform:translateY(0px);
    }

    50%{
        transform:translateY(-20px);
    }

    100%{
        transform:translateY(0px);
    }
}

/* Wrapper */

.login-wrapper{

    min-height:100vh;

    display:flex;

    align-items:center;

    justify-content:center;

    padding:30px;

    position:relative;

    z-index:2;
}

/* Main Container */

.login-container{

    width:100%;

    max-width:1250px;

    min-height:700px;

    border-radius:32px;

    overflow:hidden;

    background:rgba(15,23,42,0.85);

    border:1px solid rgba(255,255,255,0.06);

    backdrop-filter:blur(22px);

    box-shadow:
    0 0 40px rgba(37,99,235,0.08),
    0 20px 60px rgba(0,0,0,0.55);
}

/* LEFT SIDE */

.left-side{

    background:
    linear-gradient(135deg,#0f172a,#111827);

    position:relative;

    display:flex;

    align-items:center;

    justify-content:center;

    overflow:hidden;

    padding:60px;
}

.left-side::before{

    content:'';

    position:absolute;

    width:320px;
    height:320px;

    background:#2563eb22;

    border-radius:50%;

    top:-100px;
    left:-100px;

    filter:blur(50px);
}

.left-side::after{

    content:'';

    position:absolute;

    width:250px;
    height:250px;

    background:#3b82f622;

    border-radius:50%;

    bottom:-100px;
    right:-100px;

    filter:blur(50px);
}

.brand-wrapper{

    position:relative;

    z-index:2;

    text-align:center;
}

.shield-icon{

    font-size:120px;

    color:#3b82f6;

    margin-bottom:30px;

    filter:drop-shadow(0 0 25px rgba(37,99,235,0.45));
}

.brand-title{

    font-size:68px;

    font-weight:700;

    line-height:1.1;

    color:white;
}

.brand-subtitle{

    color:#94a3b8;

    margin-top:20px;

    font-size:18px;
}

/* RIGHT SIDE */

.right-side{

    background:#020617;

    display:flex;

    align-items:center;

    justify-content:center;

    padding:50px 60px;
}

.login-box{

    width:100%;

    max-width:430px;
}

.login-title{

    font-size:42px;

    font-weight:700;

    color:white;

    margin-bottom:10px;
}

.login-subtitle{

    color:#94a3b8;

    margin-bottom:35px;
}

/* INPUTS */

.form-label{
    color:white;
    margin-bottom:10px;
}

.input-group{

    background:#0f172a;

    border-radius:16px;

    border:1px solid rgba(255,255,255,0.06);

    overflow:hidden;

    transition:0.3s ease;
}

.input-group:focus-within{

    border-color:#3b82f6;

    box-shadow:0 0 20px rgba(37,99,235,0.15);
}

.input-group-text{

    background:transparent !important;

    border:none !important;

    color:#64748b;
}

.form-control{

    background:transparent !important;

    border:none !important;

    color:white !important;

    padding:16px !important;
}

.form-control:focus{

    box-shadow:none !important;
}

.form-control::placeholder{
    color:#64748b;
}

/* BUTTON */

.cyber-btn{

    width:100%;

    border:none;

    padding:16px;

    border-radius:16px;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    color:white;

    font-weight:600;

    transition:0.3s ease;

    margin-top:10px;
}

.cyber-btn:hover{

    transform:translateY(-3px);

    box-shadow:0 12px 28px rgba(37,99,235,0.35);
}

/* PASSWORD TOGGLE */

.password-toggle{

    cursor:pointer;

    color:#94a3b8;

    transition:0.3s;
}

.password-toggle:hover{
    color:white;
}

/* ALERT */

.alert{

    border:none;

    border-radius:14px;
}

/* FOOTER */

.login-footer{

    margin-top:25px;

    text-align:center;

    color:#94a3b8;
}

.login-footer a{

    color:#3b82f6;

    text-decoration:none;

    font-weight:600;
}

/* RESPONSIVE */

@media(max-width:992px){

    body{
        overflow:auto;
    }

    .left-side{
        display:none;
    }

    .right-side{
        padding:40px 25px;
    }

    .login-container{
        min-height:auto;
    }

    .login-title{
        font-size:34px;
    }
}

</style>

</head>

<body>

<div class="login-wrapper">

<div class="login-container">

<div class="row g-0 h-100">

<!-- LEFT SIDE -->

<div class="col-lg-6 left-side">

<div class="brand-wrapper">

<div class="shield-icon">
<i class="fa-solid fa-shield-halved"></i>
</div>

<h1 class="brand-title">
Cybersecurity<br>
Platform
</h1>

<p class="brand-subtitle">
Train. Practice. Defend.
</p>

</div>

</div>

<!-- RIGHT SIDE -->

<div class="col-lg-6 right-side">

<div class="login-box">

<h1 class="login-title">
Welcome Back
</h1>

<p class="login-subtitle">
Login to continue your cybersecurity journey
</p>

<?php if($error): ?>

<div class="alert alert-danger">
<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
</div>

<?php endif; ?>

<form method="POST">

<?= csrfInput() ?>

<div class="mb-4">

<label class="form-label">
Email Address
</label>

<div class="input-group">

<span class="input-group-text">
<i class="fa-solid fa-envelope"></i>
</span>

<input
type="email"
name="email"
class="form-control"
placeholder="Enter your email"
required>

</div>

</div>

<div class="mb-4">

<label class="form-label">
Password
</label>

<div class="input-group">

<span class="input-group-text">
<i class="fa-solid fa-lock"></i>
</span>

<input
type="password"
name="password"
id="password"
class="form-control"
placeholder="Enter your password"
required>

<span class="input-group-text password-toggle" onclick="togglePassword()">
<i class="fa-solid fa-eye" id="eyeIcon"></i>
</span>

</div>

</div>

<button class="cyber-btn">

<i class="fa-solid fa-right-to-bracket"></i>

Login

</button>

</form>

<div class="login-footer">

Don't have an account?

<a href="register.php">
Create Account
</a>

</div>

</div>

</div>

</div>

</div>

</div>

<script>

function togglePassword(){

    const password = document.getElementById('password');

    const eyeIcon = document.getElementById('eyeIcon');

    if(password.type === 'password'){

        password.type = 'text';

        eyeIcon.classList.remove('fa-eye');

        eyeIcon.classList.add('fa-eye-slash');

    }else{

        password.type = 'password';

        eyeIcon.classList.remove('fa-eye-slash');

        eyeIcon.classList.add('fa-eye');
    }
}

</script>

</body>
</html>