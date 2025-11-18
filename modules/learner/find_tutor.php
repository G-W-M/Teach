<?php
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

if ($_SESSION['role']!=='learner'){ header("Location: ../../index.php"); exit; }

$user_id = $_SESSION['user_id'];
$message = '';

// Get units for dropdown
$units_result = $conn->query("SELECT * FROM units ORDER BY unit_name");

// Handle form submission
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['request_tutor'])){
    $unit_id = $_POST['unit_id'];
    $description = $_POST['description'];
    $preferred_schedule = $_POST['preferred_schedule'];
    $urgency = $_POST['urgency'] ?? 'medium';

    try {
        $stmt = $conn->prepare("INSERT INTO learning_requests (learner_id, unit_id, description, preferred_schedule, urgency, status, created_at) VALUES (?, ?, ?, ?, ?, 'open', NOW())");
        $stmt->bind_param("iisss", $user_id, $unit_id, $description, $preferred_schedule, $urgency);
        $stmt->execute();
        $stmt->close();
        $message = "Your request has been submitted successfully. You can view it in 'My Requests'.";
    } catch(Exception $e){
        $message = "Error submitting request: " . $e->getMessage();
    }
}
?>

<?php include 'learner_nav.php'; ?>

<div class="main-content">
    <h2 class="page-title">Request a Tutor</h2>

    <?php if($message): ?>
        <div class="alert-message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label for="unit_id">Unit *</label>
                <select name="unit_id" id="unit_id" required>
                    <option value="">Select unit...</option>
                    <?php while($unit=$units_result->fetch_assoc()): ?>
                        <option value="<?= $unit['unit_id'] ?>"><?= htmlspecialchars($unit['unit_code'].' - '.$unit['unit_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="urgency">Urgency</label>
                <select name="urgency" id="urgency">
                    <option value="low">Low - Within 2 weeks</option>
                    <option value="medium" selected>Medium - Within 1 week</option>
                    <option value="high">High - ASAP</option>
                </select>
            </div>

            <div class="form-group">
                <label for="preferred_schedule">Preferred Schedule</label>
                <select name="preferred_schedule" id="preferred_schedule">
                    <option value="weekday_morning">Weekday Morning (8-12)</option>
                    <option value="weekday_afternoon" selected>Weekday Afternoon (2-5)</option>
                    <option value="weekday_evening">Weekday Evening (6-8)</option>
                    <option value="weekend">Weekend</option>
                    <option value="flexible">Flexible</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea name="description" id="description" required rows="4" placeholder="Explain what you need help with..."></textarea>
            </div>

            <button type="submit" name="request_tutor" class="btn-submit">Submit Request</button>
        </form>
    </div>
</div>

<style>
/* Page and Form Styles */
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
