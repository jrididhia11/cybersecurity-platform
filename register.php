<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/csrf.php';

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF verification
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    die("Invalid CSRF token. Please refresh and try again.");
}

    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $passwordRaw = $_POST['password'];

    // Validation serveur
    if (strlen($fullname) < 2 || strlen($fullname) > 100) {
        $message = "Full name must be between 2 and 100 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } else {

    $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {

        $message = "Email already exists";

    } else {

        $role = "student";

        $stmt = $conn->prepare("
            INSERT INTO users(fullname, email, password, role, status, xp)
            VALUES(?,?,?,?, 'active', 0)
        ");

        $stmt->bind_param(
            "ssss",
            $fullname,
            $email,
            $password,
            $role
        );

        if ($stmt->execute()) {

            $message = "Registration successful";

        } else {

            $message = "Registration failed";
        }
    }
    } // end validation else
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Create Account</title>

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

/* WRAPPER */

.register-wrapper{

    min-height:100vh;

    display:flex;

    align-items:center;

    justify-content:center;

    padding:30px;

    position:relative;

    z-index:2;
}

/* CONTAINER */

.register-container{

    width:100%;

    max-width:1250px;

    min-height:720px;

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

.register-box{

    width:100%;

    max-width:430px;
}

.register-title{

    font-size:42px;

    font-weight:700;

    color:white;

    margin-bottom:10px;
}

.register-subtitle{

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

/* PASSWORD STRENGTH */

.password-strength{

    margin-top:12px;
}

.strength-bar{

    width:100%;

    height:10px;

    border-radius:30px;

    background:#1e293b;

    overflow:hidden;
}

.strength-fill{

    height:100%;

    width:0%;

    transition:0.3s ease;

    border-radius:30px;
}

.strength-text{

    margin-top:8px;

    font-size:14px;

    color:#94a3b8;
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

.register-footer{

    margin-top:25px;

    text-align:center;

    color:#94a3b8;
}

.register-footer a{

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

    .register-container{
        min-height:auto;
    }

    .register-title{
        font-size:34px;
    }
}

</style>

</head>

<body>

<div class="register-wrapper">

<div class="register-container">

<div class="row g-0 h-100">

<!-- LEFT -->

<div class="col-lg-6 left-side">

<div class="brand-wrapper">

<div class="shield-icon">
<i class="fa-solid fa-user-shield"></i>
</div>

<h1 class="brand-title">
Cybersecurity<br>
Platform
</h1>

<p class="brand-subtitle">
Build your cybersecurity skills safely
</p>

</div>

</div>

<!-- RIGHT -->

<div class="col-lg-6 right-side">

<div class="register-box">

<h1 class="register-title">
Create Account
</h1>

<p class="register-subtitle">
Start your cybersecurity learning journey
</p>

<?php if($message === "Registration successful"): ?>

<div style="text-align:center;padding:20px 0;">
    <div style="font-size:60px;margin-bottom:16px;">✅</div>
    <h3 style="color:#22c55e;font-weight:700;margin-bottom:8px;">Account Created!</h3>
    <p style="color:#94a3b8;margin-bottom:24px;">Your account is ready. Redirecting to login in <span id="countdown">3</span>s...</p>
    <a href="login.php" style="display:inline-block;background:linear-gradient(135deg,#2563eb,#3b82f6);color:white;padding:14px 32px;border-radius:14px;text-decoration:none;font-weight:600;font-size:16px;">
        <i class="fa-solid fa-arrow-right-to-bracket" style="margin-right:8px;"></i>Go to Login
    </a>
</div>

<script>
let c = 3;
const el = document.getElementById('countdown');
const t = setInterval(() => {
    c--;
    if (el) el.textContent = c;
    if (c <= 0) { clearInterval(t); window.location.href = 'login.php'; }
}, 1000);
</script>

<?php elseif($message): ?>

<div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);border-radius:12px;padding:14px 18px;color:#fca5a5;margin-bottom:20px;">
    <i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
</div>

<?php endif; ?>

<?php if($message !== "Registration successful"): ?>
<form method="POST" action="">
<?= csrfInput() ?>

<div class="mb-4">

<label class="form-label">
Full Name
</label>

<div class="input-group">

<span class="input-group-text">
<i class="fa-solid fa-user"></i>
</span>

<input
type="text"
name="fullname"
class="form-control"
placeholder="Enter your full name"
required>

</div>

</div>

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
placeholder="Create a secure password"
required>

<span class="input-group-text password-toggle" onclick="togglePassword()">
<i class="fa-solid fa-eye" id="eyeIcon"></i>
</span>

</div>

<div class="password-strength">

<div class="strength-bar">

<div class="strength-fill" id="strengthFill"></div>

</div>

<div class="strength-text" id="strengthText">
Password strength
</div>

</div>

</div>

<button class="cyber-btn">

<i class="fa-solid fa-user-plus"></i>

Create Account

</button>

</form>
<?php endif; ?>

<div class="register-footer">

Already have an account?

<a href="login.php">
Login
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

/* PASSWORD STRENGTH */

const passwordInput = document.getElementById('password');

const strengthFill = document.getElementById('strengthFill');

const strengthText = document.getElementById('strengthText');

passwordInput.addEventListener('input', function(){

    const value = passwordInput.value;

    let strength = 0;

    if(value.length >= 6) strength++;
    if(value.match(/[A-Z]/)) strength++;
    if(value.match(/[0-9]/)) strength++;
    if(value.match(/[^A-Za-z0-9]/)) strength++;

    if(strength === 1){

        strengthFill.style.width = '25%';
        strengthFill.style.background = '#dc2626';
        strengthText.innerHTML = 'Weak password';

    }else if(strength === 2){

        strengthFill.style.width = '50%';
        strengthFill.style.background = '#f59e0b';
        strengthText.innerHTML = 'Medium password';

    }else if(strength === 3){

        strengthFill.style.width = '75%';
        strengthFill.style.background = '#3b82f6';
        strengthText.innerHTML = 'Strong password';

    }else if(strength === 4){

        strengthFill.style.width = '100%';
        strengthFill.style.background = '#22c55e';
        strengthText.innerHTML = 'Very strong password';

    }else{

        strengthFill.style.width = '0%';
        strengthText.innerHTML = 'Password strength';
    }
});

</script>

</body>
</html>