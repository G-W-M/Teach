<?php
session_start();
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

// Fetch sessions with tutor and learner names
$result = $conn->query("
    SELECT s.session_id, u.user_name AS learner, t.user_name AS tutor, s.session_date, s.start_time, s.end_time, s.status
    FROM sessions s
    JOIN users u ON s.learner_id = u.user_id
    JOIN tutor t1 ON s.tutor_id = t1.tutor_id
    JOIN users t ON t1.tutor_id = t.user_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Sessions - Admin</title>

<!-- Bootswatch Flatly CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/flatly/bootstrap.min.css">

<!-- Optional custom CSS -->
<link rel="stylesheet" href="../../assets/css/admin.css">

<!-- Bootstrap Bundle (JS + Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>

<?php include 'admin_nav.php'; ?>

<div class="table-wrapper">
    <h2>Manage Sessions</h2>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Session ID</th>
                <th>Learner</th>
                <th>Tutor</th>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['session_id']; ?></td>
                <td><?php echo htmlspecialchars($row['learner']); ?></td>
                <td><?php echo htmlspecialchars($row['tutor']); ?></td>
                <td><?php echo date('F j, Y', strtotime($row['session_date'])); ?></td>
                <td><?php echo date('g:i A', strtotime($row['start_time'])); ?></td>
                <td><?php echo date('g:i A', strtotime($row['end_time'])); ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
