<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session and include necessary files
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';
require_once '../../includes/logger.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to apply as a tutor.");
}

$user_id = $_SESSION['user_id'];

// Log page access
logActivity($user_id, 'PAGE_ACCESS', 'TUTOR', 'Accessed tutor apply page');

// Redirect if already a tutor
$checkTutor = $conn->prepare("SELECT * FROM tutor WHERE tutor_id = ?");
if (!$checkTutor) die("Prepare failed: " . $conn->error);

$checkTutor->bind_param("i", $user_id);
$checkTutor->execute();
$tutorExists = $checkTutor->get_result()->num_rows > 0;

if ($tutorExists) {
    echo "<div class='alert alert-info text-center mt-4'>You are already a tutor.</div>";
    exit;
}

// Fetch available units
$units = $conn->query("SELECT * FROM units ORDER BY unit_name ASC");
if (!$units) die("Failed to fetch units: " . $conn->error);

// Handle application submission
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $unit_id = $_POST['unit_id'] ?? null;

    if (!$unit_id) {
        $error = "Please select a unit before submitting.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tutor_applications (user_id, unit_id) VALUES (?, ?)");
        if (!$stmt) die("Prepare failed: " . $conn->error);

        $stmt->bind_param("ii", $user_id, $unit_id);
        if ($stmt->execute()) {
            logActivity($user_id, 'APPLICATION_SUBMITTED', 'TUTOR', "Applied for tutor role in unit ID: $unit_id");
            header("Location: take_test.php?unit=$unit_id");
            exit;
        } else {
            $error = "Failed to submit application: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tutor Application</title>
    <link rel="stylesheet" href="../../assets/css/tutor.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="tutor-body">
<?php include 'tutor_nav.php'; ?>

<div class="container mt-4">
    <div class="tutor-header">Apply as Tutor</div>
    <div class="tutor-card">
        <h3>Select Unit You Want to Teach</h3>
        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST">
            <label class="form-label">Choose Subject / Unit</label>
            <select name="unit_id" class="form-select mb-3" required>
                <option value="">-- Select Unit --</option>
                <?php while ($u = $units->fetch_assoc()) { ?>
                    <option value="<?= $u['unit_id'] ?>"><?= htmlspecialchars($u['unit_name']) ?></option>
                <?php } ?>
            </select>

            <button type="submit" class="tutor-btn tutor-btn-primary tutor-btn-block"> Take Test</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
