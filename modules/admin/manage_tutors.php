<?php
session_start();
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

// Fetch tutors
$result = $conn->query("SELECT u.user_id, u.user_name, u.email, t.status FROM users u LEFT JOIN tutor t ON u.user_id = t.tutor_id WHERE u.role='tutor'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Tutors - Admin</title>
</head>
<body style="margin:0; font-family:Arial,sans-serif; background:#f4f6f8;">

<!-- Navigation -->
<nav style="background:#003366; color:white; padding:1rem; display:flex; justify-content:space-between; align-items:center;">
    <div style="font-weight:bold;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
    <div>
        <a href="admin_dash.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Dashboard</a>
        <a href="manage_tutors.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Manage Tutors</a>
        <a href="manage_sessions.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Sessions</a>
        <a href="manage_feedback.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Feedback</a>
        <a href="reports.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Reports</a>
        <a href="certificates.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Certificates</a>
        <a href="system_logs.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">System Logs</a>
        <a href="logout.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Logout</a>
    </div>
</nav>

<div style="max-width:1400px; margin:2rem auto; padding:2rem;">

    <h2 style="color:#0059b3;">Manage Tutors</h2>

    <table style="width:100%; border-collapse:collapse; margin-top:20px; background:white; border-radius:12px; overflow:hidden;">
        <thead>
            <tr style="background:#3498db; color:white;">
                <th style="padding:10px;">ID</th>
                <th style="padding:10px;">Name</th>
                <th style="padding:10px;">Email</th>
                <th style="padding:10px;">Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr style="text-align:center; border-bottom:1px solid #eee;">
                <td style="padding:10px;"><?php echo $row['user_id']; ?></td>
                <td style="padding:10px;"><?php echo htmlspecialchars($row['user_name']); ?></td>
                <td style="padding:10px;"><?php echo htmlspecialchars($row['email']); ?></td>
                <td style="padding:10px;"><?php echo $row['status'] ?? 'N/A'; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>
</body>
</html>
