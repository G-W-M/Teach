<?php
require_once('../../includes/session_check.php');
require_once('../../includes/logger.php');
require_once('../../database/conf.php');

if ($_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login.php");
    exit;
}

$tutor_id = $_SESSION['user_id'];
logActivity($tutor_id, "ACCESS", "Tutor Feedback", "Tutor opened feedback page");

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['session_id'], $_POST['rating'], $_POST['comments'])) {
    $session_id = intval($_POST['session_id']);
    $rating = intval($_POST['rating']);
    $comments = trim($_POST['comments']);

    if ($rating >= 1 && $rating <= 5 && !empty($comments)) {
        $stmt = $conn->prepare("INSERT INTO feedback (session_id, learner_id, tutor_id, rating, comments) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiis", $session_id, $_POST['learner_id'], $tutor_id, $rating, $comments);
        if ($stmt->execute()) {
            $message = "Feedback submitted successfully!";
            logActivity($tutor_id, "FEEDBACK", "Tutor", "Submitted feedback for session_id: $session_id");
        } else {
            $error = "Failed to submit feedback.";
        }
        $stmt->close();
    } else {
        $error = "Invalid rating or empty comments.";
    }
}

// Fetch completed sessions for this tutor
$sess_stmt = $conn->prepare("
    SELECT s.session_id, s.session_date, u.user_id AS learner_id, u.user_name AS learner_name
    FROM sessions s
    JOIN users u ON u.user_id = s.learner_id
    WHERE s.tutor_id = ? AND s.status = 'completed'
    ORDER BY s.session_date DESC
");
$sess_stmt->bind_param("i", $tutor_id);
$sess_stmt->execute();
$sessions = $sess_stmt->get_result();
$sess_stmt->close();

// Fetch feedback given by this tutor
$fb_stmt = $conn->prepare("
    SELECT f.*, u.user_name AS learner_name
    FROM feedback f
    JOIN users u ON u.user_id = f.tutor_id
    WHERE f.learner_id = ?
    ORDER BY f.created_at DESC
");
$fb_stmt->bind_param("i", $tutor_id);
$fb_stmt->execute();
$feedbacks = $fb_stmt->get_result();
$fb_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tutor Feedback - TeachMe</title>

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

    <h1 class="mb-4">Submit Feedback</h1>

    <?php if(isset($message)) echo "<div class='alert alert-success'>$message</div>"; ?>
    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <!-- Feedback Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="session_id" class="form-label">Select Session</label>
                    <select class="form-select" name="session_id" required>
                        <option value="">-- Select Session --</option>
                        <?php while($s = $sessions->fetch_assoc()): ?>
                            <option value="<?= $s['session_id'] ?>" data-learner="<?= $s['learner_id'] ?>">
                                <?= $s['session_date'] ?> - <?= htmlspecialchars($s['learner_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <input type="hidden" name="learner_id" id="learner_id">

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating (1-5)</label>
                    <input type="number" class="form-control" name="rating" min="1" max="5" required>
                </div>

                <div class="mb-3">
                    <label for="comments" class="form-label">Comments</label>
                    <textarea class="form-control" name="comments" rows="4" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit Feedback</button>
            </form>
        </div>
    </div>

    <!-- Past Feedback -->
    <h2>Past Feedback Given</h2>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <?php if($feedbacks->num_rows > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Learner</th>
                            <th>Rating</th>
                            <th>Comments</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($f = $feedbacks->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($f['learner_name']) ?></td>
                                <td><?= $f['rating'] ?></td>
                                <td><?= htmlspecialchars($f['comments']) ?></td>
                                <td><?= $f['created_at'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No feedback submitted yet.</p>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
// Populate hidden learner_id when session changes
document.querySelector('select[name="session_id"]').addEventListener('change', function() {
    const selected = this.selectedOptions[0];
    document.getElementById('learner_id').value = selected.getAttribute('data-learner');
});
</script>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
