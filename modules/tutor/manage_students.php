<?php
require_once('../../includes/session_check.php');
require_once('../../includes/logger.php');

if ($_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login.php");
    exit;
}

logActivity($_SESSION['user_id'], "ACCESS", "Manage Students", "Tutor opened manage students page");
require_once('../../database/conf.php');

$tid = $_SESSION['user_id'];

// Fetch assigned learners
$stmt = $conn->prepare("
    SELECT lr.request_id, u.user_name, u.email, u.student_id, u.phone, lr.unit_id, un.unit_name, lr.status
    FROM learning_requests lr
    JOIN users u ON lr.learner_id = u.user_id
    JOIN units un ON lr.unit_id = un.unit_id
    WHERE lr.matched_tutor_id = ? 
    ORDER BY lr.created_at DESC
");
$stmt->bind_param("i", $tid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students - TeachMe</title>

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
    <h1 class="mb-4">Your Assigned Learners</h1>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Unit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1; while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['unit_name']); ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No learners assigned yet.</div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
</html>

<?php $stmt->close(); ?>
