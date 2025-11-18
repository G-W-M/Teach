<?php
require_once('../../includes/session_check.php');
require_once('../../includes/logger.php');
require_once('../../database/conf.php');

if ($_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login.php");
    exit;
}

$tutor_id = $_SESSION['user_id'];
logActivity($tutor_id, "ACCESS", "Tutor Attendance", "Tutor opened attendance page");

// Handle attendance update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance'])) {
    foreach ($_POST['attendance'] as $att_id => $att_value) {
        $attended = ($att_value === '1') ? 1 : 0;
        $stmt = $conn->prepare("UPDATE session_attendance SET attended = ? WHERE attendance_id = ?");
        $stmt->bind_param("ii", $attended, $att_id);
        $stmt->execute();
        $stmt->close();
    }
    $message = "Attendance updated successfully!";
}

// Fetch upcoming or recent sessions for this tutor
$sess_stmt = $conn->prepare("
    SELECT s.session_id, s.session_date, s.start_time, s.end_time, u.user_id AS learner_id, u.user_name, sa.attendance_id, sa.attended
    FROM sessions s
    JOIN session_attendance sa ON sa.session_id = s.session_id
    JOIN users u ON u.user_id = sa.user_id
    WHERE s.tutor_id = ?
    ORDER BY s.session_date DESC, s.start_time ASC
");
$sess_stmt->bind_param("i", $tutor_id);
$sess_stmt->execute();
$sessions = $sess_stmt->get_result();
$sess_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tutor Attendance - TeachMe</title>

    <!-- Flatly Bootswatch CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/flatly/bootstrap.min.css">
    
    <!-- Optional custom CSS -->
    <link rel="stylesheet" href="../../assets/css/tutor.css">

    <!-- Bootstrap Bundle (JS + Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php include 'tutor_nav.php'; ?>

<div class="container mt-4">

    <h1 class="mb-4">Manage Attendance</h1>

    <?php if(isset($message)) echo "<div class='alert alert-success'>$message</div>"; ?>

    <form method="POST">
        <table class="table table-striped table-bordered shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th>Session Date</th>
                    <th>Time</th>
                    <th>Learner</th>
                    <th>Attended</th>
                </tr>
            </thead>
            <tbody>
            <?php if($sessions->num_rows > 0): ?>
                <?php while($s = $sessions->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['session_date']) ?></td>
                        <td><?= htmlspecialchars($s['start_time']) ?> - <?= htmlspecialchars($s['end_time']) ?></td>
                        <td><?= htmlspecialchars($s['user_name']) ?></td>
                        <td class="text-center">
                            <input type="checkbox" name="attendance[<?= $s['attendance_id'] ?>]" value="1"
                                <?= $s['attended'] ? 'checked' : '' ?>>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No sessions found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php if($sessions->num_rows > 0): ?>
            <div class="text-center">
                <button type="submit" class="btn btn-primary mt-3">Update Attendance</button>
            </div>
        <?php endif; ?>
    </form>

</div>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
