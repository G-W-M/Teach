<?php
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

// Check if user is learner
if ($_SESSION['role'] !== 'learner') {
    header("Location: ../../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$student_id = $_SESSION['student_id'] ?? null;

// Stats
$stats = [
    'total_requests' => 0,
    'active_sessions' => 0,
    'completed_sessions' => 0,
    'feedback_given' => 0,
    'pending_requests' => 0
];

$upcoming_sessions = [];
$recent_activity = [];

try {
    // Total requests
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM learning_requests WHERE learner_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['total_requests'] = $stmt->get_result()->fetch_assoc()['count'];
    $stmt->close();

    // Active sessions
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM learning_requests WHERE learner_id = ? AND status = 'matched'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['active_sessions'] = $stmt->get_result()->fetch_assoc()['count'];
    $stmt->close();

    // Completed sessions
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM sessions s 
                           JOIN learning_requests lr ON s.request_id = lr.request_id 
                           WHERE lr.learner_id = ? AND s.status = 'completed'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['completed_sessions'] = $stmt->get_result()->fetch_assoc()['count'];
    $stmt->close();

    // Feedback given
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM feedback WHERE from_user = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['feedback_given'] = $stmt->get_result()->fetch_assoc()['count'];
    $stmt->close();

    // Pending requests
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM learning_requests WHERE learner_id = ? AND status = 'open'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['pending_requests'] = $stmt->get_result()->fetch_assoc()['count'];
    $stmt->close();

    // Upcoming sessions
    $sessions_query = "SELECT s.session_id, u.user_name as tutor_name, units.unit_name, 
                       s.session_date, s.start_time, s.end_time, s.status
                       FROM sessions s
                       JOIN tutor t ON s.tutor_id = t.tutor_id
                       JOIN users u ON t.tutor_id = u.user_id
                       JOIN learning_requests lr ON s.request_id = lr.request_id
                       JOIN units ON lr.unit_id = units.unit_id
                       WHERE lr.learner_id = ? 
                       AND s.session_date >= CURDATE() 
                       AND s.session_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                       AND s.status = 'scheduled'
                       ORDER BY s.session_date, s.start_time
                       LIMIT 5";

    $stmt = $conn->prepare($sessions_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $upcoming_sessions = $stmt->get_result();
    $stmt->close();

    // Recent activity
    $activity_query = "SELECT 'session' as type, s.session_date as date, 
                              CONCAT('Session with ', u.user_name) as description,
                              s.status
                       FROM sessions s
                       JOIN tutor t ON s.tutor_id = t.tutor_id
                       JOIN users u ON t.tutor_id = u.user_id
                       JOIN learning_requests lr ON s.request_id = lr.request_id
                       WHERE lr.learner_id = ?
                       UNION
                       SELECT 'request' as type, created_at as date,
                              CONCAT('Request for ', units.unit_name) as description,
                              status
                       FROM learning_requests lr
                       JOIN units ON lr.unit_id = units.unit_id
                       WHERE lr.learner_id = ?
                       ORDER BY date DESC
                       LIMIT 6";

    $stmt = $conn->prepare($activity_query);
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $recent_activity = $stmt->get_result();
    $stmt->close();

} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learner Dashboard - TeachMe</title>
    <link rel="stylesheet" href="../../assets/css/learner.css">
    <link rel="stylesheet" href="../../assets/css/main.css">

    <style>
        .dashboard-container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .welcome-section { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 2.5rem; border-radius: 15px; margin-bottom: 2rem; }
        .welcome-section h1 { margin: 0 0 0.5rem; font-size: 2.2rem; }
        .student-info { background: rgba(255,255,255,0.15); padding: 1.5rem; border-radius: 10px; margin-top: 1.5rem; }
        .student-info p { margin: .5rem 0; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap: 1.5rem; }
        .stat-card { background: white; padding: 1.75rem; border-radius: 12px; border-left: 5px solid #3498db; text-align: center; }
        .stat-number { font-size: 2.5rem; font-weight: bold; color: #2c3e50; }

        .quick-actions { background: white; padding: 2rem; border-radius: 12px; margin-top: 2rem; }
        .action-buttons { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 1rem; }

        .btn { padding: 1rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; text-align: center; }
        .btn-primary { background: #3498db; color: white; }
        .btn-secondary { background: #9b59b6; color: white; }
        .btn-success { background: #27ae60; color: white; }
        .btn-outline { border: 2px solid #3498db; color: #3498db; background: white; }

        .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem; }
        @media (max-width: 1024px) { .dashboard-grid { grid-template-columns: 1fr; } }

        .section-card { background: white; padding: 2rem; border-radius: 12px; }
        .session-item, .activity-item { padding: 1.25rem; border: 1px solid #e9ecef; border-radius: 8px; }
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; text-transform: uppercase; }

        .status-scheduled { background: #fff3cd; color: #856404; }
        .status-completed { background: #d1ecf1; color: #0c5460; }
        .status-open { background: #d1ecf1; color: #0c5460; }
        .status-matched { background: #d4edda; color: #155724; }

        .no-data { text-align: center; padding: 2rem; color: #7f8c8d; }
    </style>
</head>

<body>
<?php include 'learner_nav.php'; ?>

<div class="dashboard-container">

    <div class="welcome-section">
        <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?></h1>
        <p>Your personalized learning dashboard</p>

        <div class="student-info">
            <?php if (!empty($student_id)): ?>
                <p><strong>Admission Number:</strong> <?php echo htmlspecialchars($student_id); ?></p>
            <?php endif; ?>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p><strong>Role:</strong> Learner</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card"><h3>Total Requests</h3><span class="stat-number"><?php echo $stats['total_requests']; ?></span></div>
        <div class="stat-card"><h3>Active Sessions</h3><span class="stat-number"><?php echo $stats['active_sessions']; ?></span></div>
        <div class="stat-card"><h3>Completed Sessions</h3><span class="stat-number"><?php echo $stats['completed_sessions']; ?></span></div>
        <div class="stat-card"><h3>Feedback Given</h3><span class="stat-number"><?php echo $stats['feedback_given']; ?></span></div>
    </div>

    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-buttons">
            <a href="find_tutor.php" class="btn btn-primary">Find a Tutor</a>
            <a href="give_feedback.php" class="btn btn-secondary">Give Feedback</a>
            <a href="learner_profile.php" class="btn btn-success">My Profile</a>
            <a href="my_sessions.php" class="btn btn-outline">My Sessions</a>
        </div>
    </div>

    <div class="dashboard-grid">

        <div class="section-card">
            <h2>Upcoming Sessions</h2>
            <div class="sessions-list">
                <?php if ($upcoming_sessions->num_rows > 0): ?>
                    <?php while($s = $upcoming_sessions->fetch_assoc()): ?>
                        <div class="session-item">
                            <h4><?php echo htmlspecialchars($s['unit_name']); ?></h4>
                            <p><strong>Tutor:</strong> <?php echo htmlspecialchars($s['tutor_name']); ?></p>
                            <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($s['session_date'])); ?></p>
                            <p><strong>Time:</strong> 
                                <?php echo date('g:i A', strtotime($s['start_time'])); ?> - 
                                <?php echo date('g:i A', strtotime($s['end_time'])); ?>
                            </p>
                            <span class="status-badge status-<?php echo $s['status']; ?>">
                                <?php echo ucfirst($s['status']); ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-data">No upcoming sessions</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="section-card">
            <h2>Recent Activity</h2>
            <div class="activity-list">
                <?php if ($recent_activity->num_rows > 0): ?>
                    <?php while($a = $recent_activity->fetch_assoc()): ?>
                        <div class="activity-item">
                            <h4><?php echo htmlspecialchars($a['description']); ?></h4>
                            <p><?php echo date('M j, Y g:i A', strtotime($a['date'])); ?></p>
                            <span class="status-badge status-<?php echo $a['status']; ?>">
                                <?php echo ucfirst($a['status']); ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-data">No recent activity</div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<?php include '../../includes/footer.php'; ?>

</body>
</html>
