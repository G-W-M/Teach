<?php

// admin_nav.php
?>
<style>
body { font-family: Arial, sans-serif; background-color: #f4f7fa; margin:0; }
.navbar { background-color: #0059b3; color: white; padding: 15px; display:flex; align-items:center; }
.navbar a { color: white; margin-right: 20px; text-decoration: none; font-weight: 600; }
.navbar a:hover { text-decoration: underline; }
.container { padding: 20px; }
table { border-collapse: collapse; width: 100%; background:white; }
table th, table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
table th { background-color: #0059b3; color: white; }
button { padding: 5px 10px; cursor: pointer; background-color:#0059b3; color:white; border:none; border-radius: 3px; }
button:hover { background-color: #003f7f; }
input, select { padding:5px; }
.alert { padding: 10px; margin:10px 0; border-radius:3px; }
.alert-success { background:#d4edda; color:#155724; }
.alert-error { background:#f8d7da; color:#721c24; }
</style>
<div style="background:#003366; color:white; padding:1rem; display:flex; justify-content:space-between; align-items:center;">
    <div style="font-weight:bold;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
    <div>
        <a href="admin_dash.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Dashboard</a>
        <a href="manage_tutors.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Tutors</a>
        <a href="manage_sessions.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Sessions</a>
        <a href="manage_feedback.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Feedback</a>
        <a href="certificates.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Certificates</a>
        <a href="reports.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Reports</a>
        <a href="system_logs.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Logs</a>
        <a href="user_management.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Users</a>
        <a href="../../modules/auth/logout.php" style="color:white; margin:0 10px; text-decoration:none; font-weight:600;">Logout</a>
    </div>
</div>

<div class="container">
<?php require_once "../../includes/admin_footer.php"; ?>
