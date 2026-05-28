<?php
require_once '../includes/admin_auth.php';
require_once '../includes/db.php';

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");

$logs = $conn->query("
    SELECT logs.*, users.fullname
    FROM logs
    LEFT JOIN users ON logs.user_id = users.id
    ORDER BY logs.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>System Logs</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

<div class="container mt-5">

<h2 class="mb-4">System Logs</h2>

<table class="table table-dark table-bordered table-hover">

<thead>

<tr>
<th>ID</th>
<th>User</th>
<th>Action</th>
<th>IP Address</th>
<th>Date</th>
</tr>

</thead>

<tbody>

<?php while($log = $logs->fetch_assoc()): ?>

<tr>

<td><?= $log['id'] ?></td>

<td>
<?= htmlspecialchars($log['fullname'] ?? 'Unknown') ?>
</td>

<td>
<?= htmlspecialchars($log['action']) ?>
</td>

<td>
<?= htmlspecialchars($log['ip_address']) ?>
</td>

<td>
<?= htmlspecialchars($log['created_at']) ?>
</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</body>
</html>