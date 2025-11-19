<?php
require_once('../../includes/session_check.php');
require_once('../../includes/logger.php');
require_once('../../database/conf.php');

if ($_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login.php");
    exit;
}

$tutor_id = $_SESSION['user_id'];

// Log page access
logActivity($tutor_id, "PAGE_ACCESS", "TUTOR", "Accessed tutor test results page");

// Fetch test results
$stmt = $conn->prepare("
    SELECT tt.*, u.unit_name
    FROM tutor_tests tt
    JOIN units u ON u.unit_id = tt.unit_id
    WHERE tt.user_id = ?
    ORDER BY tt.taken_at DESC
");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$results = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Test Results - TeachMe</title>

    <!-- Bootswatch Flatly -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/flatly/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/tutor.css">
</head>

<body class="tutor-body">

<?php include 'tutor_nav.php'; ?>

<div class="container mt-4">

    <h1 class="mb-4 text-primary fw-bold">Competency Test Results</h1>

    <?php if ($results->num_rows === 0): ?>
        
        <div class="alert alert-info p-4 shadow-sm">
            <h4>No Test Attempts Yet 🚀</h4>
            <p>You haven't completed any competency tests so far. Once you finish a test, your results will appear here.</p>
        </div>

    <?php else: ?>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="mb-3">Your Attempts</h4>

                <table class="table table-hover table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Unit</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Status</th>
                            <th>Date Taken</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($r = $results->fetch_assoc()): 
                            $percent = number_format(($r['score'] / $r['total_questions']) * 100, 2);
                            $statusBadge = $r['passed']
                                ? "<span class='badge bg-success'>Passed</span>"
                                : "<span class='badge bg-danger'>Failed</span>";
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($r['unit_name']) ?></td>
                            <td><?= $r['score'] ?> / <?= $r['total_questions'] ?></td>
                            <td><?= $percent ?>%</td>
                            <td><?= $statusBadge ?></td>
                            <td><?= $r['taken_at'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

            </div>
        </div>

    <?php endif; ?>

</div>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
