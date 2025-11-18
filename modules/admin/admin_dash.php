<?php
session_start();
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

// Example stats
$counts = [
    'users' => 10,
    'learners' => 5,
    'tutors' => 3,
    'sessions' => 8
];
$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - TeachMe</title>
</head>
<body style="margin:0; font-family:Arial,sans-serif; background:#f4f6f8;">

<!-- Navigation -->
<nav style="background:#003366; color:white; padding:1rem; display:flex; justify-content:space-between; align-items:center;">
    <div style="font-weight:bold;">Welcome, <?php echo htmlspecialchars($user_name); ?></div>
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

    <div style="background:linear-gradient(135deg,#667eea,#764ba2); color:white; padding:2.5rem; border-radius:15px; margin-bottom:2rem;">
        <h1 style="margin:0 0 .5rem; font-size:2.2rem;">Welcome back, <?php echo htmlspecialchars($user_name); ?></h1>
        <p>Your Admin Dashboard</p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:1.5rem;">
        <div style="background:white; padding:1.75rem; border-radius:12px; border-left:5px solid #3498db; text-align:center;">
            <h3>Total Users</h3>
            <span style="font-size:2.5rem; font-weight:bold; color:#2c3e50;"><?php echo $counts['users']; ?></span>
        </div>
        <div style="background:white; padding:1.75rem; border-radius:12px; border-left:5px solid #3498db; text-align:center;">
            <h3>Total Learners</h3>
            <span style="font-size:2.5rem; font-weight:bold; color:#2c3e50;"><?php echo $counts['learners']; ?></span>
        </div>
        <div style="background:white; padding:1.75rem; border-radius:12px; border-left:5px solid #3498db; text-align:center;">
            <h3>Total Tutors</h3>
            <span style="font-size:2.5rem; font-weight:bold; color:#2c3e50;"><?php echo $counts['tutors']; ?></span>
        </div>
        <div style="background:white; padding:1.75rem; border-radius:12px; border-left:5px solid #3498db; text-align:center;">
            <h3>Total Sessions</h3>
            <span style="font-size:2.5rem; font-weight:bold; color:#2c3e50;"><?php echo $counts['sessions']; ?></span>
        </div>
    </div>

</div>
</body>
</html>
