<?php
require_once('../../includes/session_check.php');
require_once('../../includes/logger.php');
require_once('../../database/conf.php');

if ($_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login.php");
    exit;
}

$tutor_id = $_SESSION['user_id'];
logActivity($tutor_id, "ACCESS", "Tutor Certificates", "Tutor opened certificates page");

// Fetch tutor certificates
$stmt = $conn->prepare("SELECT * FROM certificates WHERE tutor_id = ? ORDER BY issued_date DESC");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
$certificates = [];
while ($row = $result->fetch_assoc()) {
    $certificates[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Certificates - TeachMe</title>

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

    <h1 class="mb-4">My Certificates</h1>

    <?php if(empty($certificates)): ?>
        <div class="alert alert-info">You have not received any certificates yet.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach($certificates as $cert): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm tutor-card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($cert['certificate_type']) ?></h5>
                            <p class="card-text"><strong>Issued:</strong> <?= date("d M Y", strtotime($cert['issued_date'])) ?></p>
                            <!-- Optional: link to PDF or download -->
                            <?php if(!empty($cert['certificate_file'])): ?>
                                <a href="../../assets/certificates/<?= $cert['certificate_file'] ?>" target="_blank" class="btn btn-primary btn-sm">Download</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
