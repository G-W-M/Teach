<?php
require_once('../../includes/session_check.php');
require_role('tutor'); // only tutors allowed

require_once('../../includes/logger.php');
require_once('../../database/conf.php');

// Log page access
logActivity($_SESSION['user_id'], 'PAGE_ACCESS', 'TUTOR', 'Accessed tutor profile');

// Current tutor ID
$tid = get_current_user_id();

// Fetch tutor profile
$stmt = $conn->prepare("
    SELECT bio 
    FROM tutor_profiles 
    WHERE tutor_id = ?
");
$stmt->bind_param("i", $tid);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();

// Defaults
$bio = $profile['bio'] ?? 'No bio added yet.';
$max_students = 3; 
$status = 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tutor Profile - TeachMe</title>

    <!-- Bootswatch Flatly -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/flatly/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/tutor.css">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="tutor-body">

<?php include 'tutor_nav.php'; ?>

<div class="container mt-5">

    <h2 class="mb-4 fw-bold text-primary">Your Tutor Profile</h2>

    <div class="card shadow-sm tutor-card">
        <div class="card-body">

            <h5 class="card-title mb-3">Bio</h5>
            <p class="card-text">
                <?= nl2br(htmlspecialchars($bio)) ?>
            </p>

            <hr>

            <p><strong>Max Students:</strong> <?= $max_students ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($status) ?></p>

        </div>
    </div>

</div>

<?php include '../../includes/footer.php'; ?>

</body>
</html>
