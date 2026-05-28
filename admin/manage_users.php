<?php

require_once '../includes/admin_auth.php';
require_once '../includes/db.php';
require_once '../includes/csrf.php';
require_once '../includes/logger.php';

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");

if (isset($_GET['delete'])) {

    if (!validateCSRFToken($_GET['token'])) {
        die("Invalid CSRF Token");
    }

    $id = intval($_GET['delete']);

    if ($id == $_SESSION['user_id']) {
        die("You cannot delete yourself");
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        logAction($_SESSION['user_id'], "Deleted user ID $id");
    }

    header("Location: manage_users.php");
    exit();
}

$search = "";

if (isset($_GET['search'])) {

    $search = trim($_GET['search']);

    $stmt = $conn->prepare("
        SELECT * FROM users
        WHERE fullname LIKE CONCAT('%', ?, '%')
        OR email LIKE CONCAT('%', ?, '%')
        ORDER BY id DESC
    ");

    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();

    $users = $stmt->get_result();

} else {

    $users = $conn->query("SELECT * FROM users ORDER BY id DESC");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Users</title>

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

    background:
    radial-gradient(circle at top left,#2563eb22,transparent 25%),
    radial-gradient(circle at bottom right,#3b82f622,transparent 25%),
    #020617;

    min-height:100vh;

    font-family:'Inter',sans-serif;

    color:white;

    overflow-x:hidden;
}

/* MAIN */

.page-wrapper{

    padding:40px;
}

/* HEADER */

.top-card{

    background:
    linear-gradient(135deg,#0f172a,#1e293b);

    border-radius:30px;

    padding:35px;

    margin-bottom:35px;

    border:1px solid rgba(255,255,255,0.05);

    box-shadow:
    0 10px 40px rgba(0,0,0,0.4);
}

.page-title{

    font-size:42px;

    font-weight:700;

    margin-bottom:10px;
}

.page-subtitle{

    color:#94a3b8;
}

/* BUTTON */

.back-btn{

    display:inline-flex;

    align-items:center;

    gap:10px;

    padding:14px 24px;

    border-radius:16px;

    text-decoration:none;

    color:white;

    font-weight:600;

    background:
    linear-gradient(135deg,#2563eb,#3b82f6);

    transition:0.3s;
}

.back-btn:hover{

    transform:translateY(-3px);

    box-shadow:
    0 12px 25px rgba(37,99,235,0.35);

    color:white;
}

/* SEARCH */

.search-box{

    background:rgba(15,23,42,0.92);

    border-radius:24px;

    padding:25px;

    margin-bottom:30px;

    border:1px solid rgba(255,255,255,0.05);
}

.search-input{

    background:#0f172a !important;

    border:1px solid rgba(255,255,255,0.08) !important;

    color:white !important;

    padding:15px !important;

    border-radius:16px !important;
}

.search-input::placeholder{
    color:#64748b;
}

.search-input:focus{

    border-color:#3b82f6 !important;

    box-shadow:none !important;
}

/* TABLE CARD */

.table-card{

    background:rgba(15,23,42,0.92);

    border-radius:30px;

    padding:30px;

    border:1px solid rgba(255,255,255,0.05);

    overflow:hidden;
}

.table{

    margin:0;
}

.table thead{

    background:#0f172a;
}

.table th{

    border:none !important;

    padding:18px !important;

    color:#94a3b8 !important;

    font-weight:600;
}

.table td{

    border-color:rgba(255,255,255,0.04) !important;

    padding:18px !important;

    vertical-align:middle;
}

/* USER CARD EFFECT */

.user-row{

    transition:0.3s;
}

.user-row:hover{

    background:rgba(37,99,235,0.08);
}

/* BADGES */

.custom-badge{

    padding:8px 14px;

    border-radius:12px;

    font-size:13px;

    font-weight:600;
}

.role-admin{
    background:#2563eb;
}

.role-student{
    background:#475569;
}

.status-active{
    background:#16a34a;
}

.status-suspended{
    background:#dc2626;
}

/* ACTION BUTTONS */

.action-buttons{

    display:flex;

    flex-wrap:wrap;

    gap:8px;
}

.action-btn{

    width:40px;
    height:40px;

    border:none;

    border-radius:12px;

    display:flex;

    align-items:center;

    justify-content:center;

    text-decoration:none;

    color:white;

    transition:0.3s;
}

.action-btn:hover{

    transform:translateY(-2px);

    color:white;
}

.delete-btn{
    background:#dc2626;
}

.suspend-btn{
    background:#f59e0b;
}

.activate-btn{
    background:#16a34a;
}

.admin-btn{
    background:#2563eb;
}

.user-btn{
    background:#64748b;
}

/* RESPONSIVE */

@media(max-width:992px){

    .page-wrapper{
        padding:20px;
    }

    .page-title{
        font-size:32px;
    }

    .table-card{
        overflow-x:auto;
    }
}

</style>

</head>

<body>

<div class="page-wrapper">

<!-- HEADER -->

<div class="top-card d-flex justify-content-between align-items-center flex-wrap gap-3">

<div>

<h1 class="page-title">

<i class="fa-solid fa-users"></i>

Manage Users

</h1>

<p class="page-subtitle">
Manage accounts, permissions and platform users
</p>

</div>

<a href="admin_dashboard.php" class="back-btn">

<i class="fa-solid fa-arrow-left"></i>

Back Dashboard

</a>

</div>

<!-- SEARCH -->

<div class="search-box">

<form method="GET">

<input
type="text"
name="search"
class="form-control search-input"
placeholder="Search by name or email..."
value="<?= htmlspecialchars($search) ?>">

</form>

</div>

<!-- TABLE -->

<div class="table-card">

<div class="table-responsive">

<table class="table table-dark align-middle">

<thead>

<tr>

<th>ID</th>
<th>Full Name</th>
<th>Email</th>
<th>Role</th>
<th>Status</th>
<th>XP</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php while($row = $users->fetch_assoc()): ?>

<tr class="user-row">

<td>
<?= $row['id'] ?>
</td>

<td>
<?= htmlspecialchars($row['fullname']) ?>
</td>

<td>
<?= htmlspecialchars($row['email']) ?>
</td>

<td>

<?php if($row['role'] === 'admin'): ?>

<span class="custom-badge role-admin">
Admin
</span>

<?php else: ?>

<span class="custom-badge role-student">
Student
</span>

<?php endif; ?>

</td>

<td>

<?php if($row['status'] === 'active'): ?>

<span class="custom-badge status-active">
Active
</span>

<?php else: ?>

<span class="custom-badge status-suspended">
Suspended
</span>

<?php endif; ?>

</td>

<td>

<?= $row['xp'] ?>

</td>

<td>

<div class="action-buttons">

<!-- DELETE -->

<a
href="?delete=<?= $row['id'] ?>&token=<?= generateCSRFToken() ?>"
class="action-btn delete-btn"
onclick="return confirm('Delete this user?')"
title="Delete User">

<i class="fa-solid fa-trash"></i>

</a>

<!-- SUSPEND / ACTIVATE -->

<?php if($row['status'] === 'active'): ?>

<a
href="suspend_user.php?id=<?= $row['id'] ?>&token=<?= generateCSRFToken() ?>"
class="action-btn suspend-btn"
title="Suspend User">

<i class="fa-solid fa-ban"></i>

</a>

<?php else: ?>

<a
href="activate_user.php?id=<?= $row['id'] ?>&token=<?= generateCSRFToken() ?>"
class="action-btn activate-btn"
title="Activate User">

<i class="fa-solid fa-check"></i>

</a>

<?php endif; ?>

<!-- ADMIN -->

<a
href="change_role.php?id=<?= $row['id'] ?>&role=admin&token=<?= generateCSRFToken() ?>"
class="action-btn admin-btn"
title="Make Admin">

<i class="fa-solid fa-user-shield"></i>

</a>

<!-- USER -->

<a
href="change_role.php?id=<?= $row['id'] ?>&role=user&token=<?= generateCSRFToken() ?>"
class="action-btn user-btn"
title="Make User">

<i class="fa-solid fa-user"></i>

</a>

</div>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>

</div>

</body>
</html>