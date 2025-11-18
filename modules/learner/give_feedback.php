<?php
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

if ($_SESSION['role']!=='learner'){ header("Location: ../../index.php"); exit; }

$user_id = $_SESSION['user_id'];
$message = '';

// Fetch completed sessions for this learner where feedback hasn't been given yet
$sessions_stmt = $conn->prepare("
    SELECT s.session_id, u.user_name AS tutor_name, u.user_id AS tutor_id, lr.unit_id, un.unit_name
    FROM sessions s
    INNER JOIN learning_requests lr ON s.request_id = lr.request_id
    INNER JOIN tutor t ON s.tutor_id = t.tutor_id
    INNER JOIN users u ON t.tutor_id = u.user_id
    INNER JOIN units un ON lr.unit_id = un.unit_id
    LEFT JOIN feedback f ON f.session_id = s.session_id AND f.from_user = ?
    WHERE s.learner_id = ? AND s.status='completed' AND f.feedback_id IS NULL
    ORDER BY s.session_date DESC
");
$sessions_stmt->bind_param("ii", $user_id, $user_id);
$sessions_stmt->execute();
$sessions_result = $sessions_stmt->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['give_feedback'])) {
    $session_id = $_POST['session_id'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    // Get tutor_id for session
    $tutor_stmt = $conn->prepare("SELECT tutor_id FROM sessions WHERE session_id=?");
    $tutor_stmt->bind_param("i", $session_id);
    $tutor_stmt->execute();
    $tutor_stmt->bind_result($tutor_id);
    $tutor_stmt->fetch();
    $tutor_stmt->close();

    try {
        $insert_stmt = $conn->prepare("INSERT INTO feedback (session_id, from_user, to_user, rating, comments) VALUES (?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("iiiis", $session_id, $user_id, $tutor_id, $rating, $comments);
        $insert_stmt->execute();
        $insert_stmt->close();
        $message = "Feedback submitted successfully.";
    } catch(Exception $e){
        $message = "Error submitting feedback: " . $e->getMessage();
    }
}

?>

<?php include 'learner_nav.php'; ?>

<div class="main-content">
    <h2 class="page-title">Give Feedback</h2>

    <?php if($message): ?>
        <div class="alert-message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label for="session_id">Select Completed Session *</label>
                <select name="session_id" id="session_id" required>
                    <option value="">-- Choose a session --</option>
                    <?php while($session = $sessions_result->fetch_assoc()): ?>
                        <option value="<?= $session['session_id'] ?>">
                            <?= htmlspecialchars($session['unit_name'] . " with " . $session['tutor_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="rating">Rating *</label>
                <select name="rating" id="rating" required>
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Very Good</option>
                    <option value="3">3 - Good</option>
                    <option value="2">2 - Fair</option>
                    <option value="1">1 - Poor</option>
                </select>
            </div>

            <div class="form-group">
                <label for="comments">Comments</label>
                <textarea name="comments" id="comments" rows="4" placeholder="Write your feedback..."></textarea>
            </div>

            <button type="submit" name="give_feedback" class="btn-submit">Submit Feedback</button>
        </form>
    </div>
</div>

<style>
/* Use same styling as request form */
.main-content {
    max-width: 700px;
    margin: 2rem auto;
    padding: 0 1rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #2c3e50;
    text-align: center;
}

.alert-message {
    background-color: #dff0d8;
    color: #3c763d;
    padding: 1rem;
    border-radius: 5px;
    margin-bottom: 1.5rem;
    text-align: center;
}

.form-card {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 1.5rem;
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #34495e;
}

.form-group select,
.form-group textarea {
    padding: 0.6rem;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 0.95rem;
    width: 100%;
    resize: vertical;
    transition: all 0.2s;
}

.form-group select:focus,
.form-group textarea:focus {
    border-color: #1abc9c;
    outline: none;
    box-shadow: 0 0 5px rgba(26,188,156,0.3);
}

.btn-submit {
    background-color: #1abc9c;
    color: white;
    padding: 0.7rem 1.5rem;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-submit:hover {
    background-color: #16a085;
}
</style>
