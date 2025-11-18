<?php
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

if ($_SESSION['role'] !== 'learner') {
    header("Location: ../../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch sessions for learner
$query = "
    SELECT s.session_id, u.user_name AS tutor_name, units.unit_name,
           s.session_date, s.start_time, s.end_time, s.status,
           lr.description
    FROM sessions s
    JOIN tutor t ON s.tutor_id = t.tutor_id
    JOIN users u ON t.tutor_id = u.user_id
    JOIN learning_requests lr ON s.request_id = lr.request_id
    JOIN units ON lr.unit_id = units.unit_id
    WHERE lr.learner_id = ?
    ORDER BY s.session_date DESC, s.start_time DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sessions = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sessions - TeachMe</title>

    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/learner.css">

    <style>
        /* --- Page Header --- */
        .page-header {
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .page-header h1 {
            font-size: 1.7rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: .3rem;
        }
        .page-header p {
            font-size: .95rem;
            color: #7f8c8d;
        }

        /* --- Container --- */
        .sessions-container {
            background: #ffffff;
            padding: 1.8rem;
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(0,0,0,0.06);
        }

        /* --- Table Styling --- */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6px;
            font-size: .95rem;
        }
        table thead th {
            background: #f5f7fa;
            padding: .9rem;
            color: #2c3e50;
            font-weight: 600;
            border-bottom: 2px solid #eaecee;
        }
        table tbody tr {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        table tbody td {
            padding: .9rem;
            border-bottom: none;
        }

        /* --- Status Badges --- */
        .status-badge {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: .8rem;
            font-weight: 600;
        }
        .status-scheduled { background: #e8f4fc; color: #1261a0; }
        .status-completed { background: #e7f8ec; color: #1e7d35; }
        .status-cancelled { background: #fdeaea; color: #c0392b; }

        /* --- Buttons & Links --- */
        .btn-link {
            color: #1a73e8;
            font-size: .9rem;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-link:hover {
            text-decoration: underline;
        }

        /* --- No Data --- */
        .no-data {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        .btn-primary {
            padding: .6rem 1.2rem;
            background: #1abc9c;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn-primary:hover {
            background: #16a085;
        }
    </style>
</head>

<body>

<?php include 'learner_nav.php'; ?>

<div class="container">

    <div class="page-header">
        <h1>My Tutoring Sessions</h1>
        <p>Review past sessions and manage your scheduled learning sessions.</p>
    </div>

    <div class="sessions-container">

        <?php if ($sessions->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Unit</th>
                        <th>Tutor</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Request Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php while ($row = $sessions->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['unit_name']); ?></td>

                        <td><?= htmlspecialchars($row['tutor_name']); ?></td>

                        <td>
                            <?= date('M j, Y', strtotime($row['session_date'])); ?><br>
                            <?= date('g:i A', strtotime($row['start_time'])); ?> –
                            <?= date('g:i A', strtotime($row['end_time'])); ?>
                        </td>

                        <td>
                            <span class="status-badge status-<?= $row['status']; ?>">
                                <?= ucfirst($row['status']); ?>
                            </span>
                        </td>

                        <td><?= nl2br(htmlspecialchars($row['description'])); ?></td>

                        <td>
                            <?php if ($row['status'] === 'completed'): ?>
                                <a class="btn-link"
                                    href="give_feedback.php?session_id=<?= $row['session_id']; ?>">
                                    Give Feedback
                                </a>

                            <?php elseif ($row['status'] === 'scheduled'): ?>
                                <span class="btn-link"
                                      onclick="alert('Session scheduled for <?= date('F j, Y', strtotime($row['session_date'])); ?>');">
                                    View Details
                                </span>

                            <?php else: ?>
                                <span style="color:#888;">No actions</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>

            </table>

        <?php else: ?>
            <div class="no-data">
                <p>No tutoring sessions have been recorded yet.</p>
                <p><a href="find_tutor.php" class="btn-primary">Request a Tutor</a></p>
            </div>
        <?php endif; ?>

    </div>

</div>

<?php include '../../includes/footer.php'; ?>

</body>
</html>
