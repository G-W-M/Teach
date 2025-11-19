<?php
require_once '../../database/conf.php';
require_once '../../includes/session_check.php';

// --- CHECK LOGIN ---
if (!is_logged_in()) {
    header("Location: ../../auth/login.php");
    exit();
}

// --- READ SESSION ID ---
$session_id = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;

// --- GET SESSION DETAILS ---
$stmt = $conn->prepare("
    SELECT s.*, u.username AS tutor_name
    FROM sessions s
    JOIN users u ON s.tutor_id = u.user_id
    WHERE s.session_id = ?
");
$stmt->bind_param("i", $session_id);
$stmt->execute();
$session = $stmt->get_result()->fetch_assoc();

if (!$session) {
    die("Session not found.");
}

// --- HANDLE SUBMISSION ---
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $comments = trim($_POST['comments']);
    $learner_id = $_SESSION['user_id'];
    $tutor_id = $session['tutor_id'];

    if ($rating < 1 || $rating > 5) {
        $message = "Invalid rating!";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO feedback (session_id, learner_id, tutor_id, rating, comments)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("iiiis", $session_id, $learner_id, $tutor_id, $rating, $comments);

        if ($stmt->execute()) {
            $message = "Feedback submitted successfully!";
        } else {
            $message = "Error saving feedback.";
        }
    }
}

// --- GET PAST FEEDBACK ---
$feedback_stmt = $conn->prepare("
    SELECT f.*, u.username AS learner_name
    FROM feedback f
    JOIN users u ON f.learner_id = u.user_id
    WHERE f.session_id = ?
    ORDER BY f.created_at DESC
");
$feedback_stmt->bind_param("i", $session_id);
$feedback_stmt->execute();
$feedback_list = $feedback_stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Session Feedback</title>
    <link rel="stylesheet" href="../../assets/css/tutor.css">
</head>
<body>

<div class="container">
    <h2>Feedback for Session: <?= htmlspecialchars($session['topic']) ?></h2>

    <?php if (!empty($message)): ?>
        <p style="color: green;"><?= $message ?></p>
    <?php endif; ?>

    <!-- SUBMIT FEEDBACK -->
    <div class="card">
        <h3>Leave Your Feedback</h3>

        <form method="POST">
            <label>Rating (1–5)</label>
            <select name="rating" required>
                <option value="">Select Rating</option>
                <?php for ($i=1; $i<=5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>

            <label>Comments</label>
            <textarea name="comments" placeholder="Say something helpful..." rows="4"></textarea>

            <button type="submit">Submit Feedback</button>
        </form>
    </div>

    <br>

    <!-- VIEW ALL FEEDBACK -->
    <div class="card">
        <h3>Previous Feedback</h3>

        <?php if ($feedback_list->num_rows > 0): ?>
            <?php while($fb = $feedback_list->fetch_assoc()): ?>
                <div class="feedback-item">
                    <strong><?= htmlspecialchars($fb['learner_name']) ?></strong>  
                    <span>(Rating: <?= $fb['rating'] ?>/5)</span>
                    <p><?= nl2br(htmlspecialchars($fb['comments'])) ?></p>
                    <small><?= $fb['created_at'] ?></small>
                </div>
                <hr>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No feedback yet for this session.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
