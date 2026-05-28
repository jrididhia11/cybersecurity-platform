<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>

<title>Password Checker</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="../assets/css/style.css">

</head>

<body>

<div class="container mt-5">

<div class="card card-dark p-5">

<h1>Password Strength Checker</h1>

<input type="password"
id="password"
class="form-control mt-4"
placeholder="Enter Password">

<h3 class="mt-4"
id="result"></h3>

</div>

</div>

<script>

const password =
document.getElementById('password');

const result =
document.getElementById('result');

password.addEventListener(
'keyup',()=>{

let value =
password.value;

if(value.length < 6){

result.innerHTML =
"Weak Password";

}else if(value.length < 10){

result.innerHTML =
"Medium Password";

}else{

result.innerHTML =
"Strong Password";

}

});

</script>


<?php
if (file_exists(__DIR__ . '/../includes/chatbot_widget.php'))
    require_once __DIR__ . '/../includes/chatbot_widget.php';
?>
</body>
</html>