<?php
require_once('../../includes/session_check.php');
require_role('tutor');  // Ensures only tutors can access

require_once('../../includes/logger.php');
require_once('../../database/conf.php');

// Log page access
logActivity($_SESSION['user_id'], 'PAGE_ACCESS', 'TUTOR', 'Accessed tutor profile');

// Get current tutor ID
$tid = get_current_user_id();

// Fetch tutor profile
$stmt = $conn->prepare("SELECT * FROM tutor_profiles WHERE tutor_id = ?");
$stmt->bind_param("i", $tid);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();

// Default placeholders if profile is missing
$bio = $profile['bio'] ?? '';
$max_students = 3;  // fixed
$status = 'Set by Admin';  // fixed

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tutor Profile - TeachMe</title>

    <!-- Bootswatch Flatly CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/flatly/bootstrap.min.css">

    <!-- Optional custom CSS -->
    <link rel="stylesheet" href="../../assets/css/tutor.css">

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="tutor-body">

<?php include 'tutor_nav.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Your Tutor Profile</h2>

    <div class="card tutor-card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Bio</h5>
            <p class="card-text"><?php echo htmlspecialchars($bio); ?></p>

            <hr>

            <p><strong>Max Students:</strong> <?php echo $max_students; ?></p>
            <p><strong>Status:</strong> <?php echo $status; ?></p>
        </div>
    </div>

<?php include '../../includes/footer.php'; ?>

</body>
</html>
