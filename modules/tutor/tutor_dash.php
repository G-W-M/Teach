<?php
require_once('../../includes/session_check.php');
require_once('../../includes/logger.php');

if ($_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login.php");
    exit;
}

logActivity($_SESSION['user_id'], "ACCESS", "Tutor Dashboard", "Tutor opened dashboard");
require_once('../../database/conf.php');

$tid = $_SESSION['user_id'];

// Assigned Learners (from learning_requests)
$q = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM learning_requests 
    WHERE matched_tutor_id = ? AND status IN ('matched','completed')
");
$q->bind_param("i", $tid); 
$q->execute();
$result = $q->get_result();
$total_learners = $result->fetch_assoc()['total'] ?? 0;
$q->close();

// Competencies
$q2 = $conn->prepare("SELECT COUNT(*) AS total FROM tutor_profiles WHERE tutor_id = ?");
$q2->bind_param("i", $tid); 
$q2->execute();
$result2 = $q2->get_result();
$total_competencies = $result2->fetch_assoc()['total'] ?? 0;
$q2->close();

// Rating
$q3 = $conn->prepare("SELECT rating FROM tutor WHERE tutor_id = ?");
$q3->bind_param("i", $tid); 
$q3->execute();
$result3 = $q3->get_result();
$rating = number_format($result3->fetch_assoc()['rating'] ?? 0, 1);
$q3->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tutor Dashboard - TeachMe</title>

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

    <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>

    <!-- Stats Cards -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-primary">
                <div class="card-body">
                    <h5 class="card-title text-primary">Assigned Learners</h5>
                    <p class="card-text fs-2"><?php echo $total_learners; ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center shadow-sm border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">Your Competencies</h5>
                    <p class="card-text fs-2"><?php echo $total_competencies; ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center shadow-sm border-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning">Your Rating</h5>
                    <p class="card-text fs-2"><?php echo $rating; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-5 text-center">
        <a href="manage_competencies.php" class="btn btn-primary btn-lg me-2 mb-2">Manage Competencies</a>
        <a href="assigned_learners.php" class="btn btn-success btn-lg me-2 mb-2">View Learners</a>
        <a href="tutor_profile.php" class="btn btn-warning btn-lg mb-2">Edit Profile</a>
    </div>

</div>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
