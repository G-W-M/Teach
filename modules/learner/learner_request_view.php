<?php
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

$request_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("
    SELECT lr.request_id, lr.description, lr.status, lr.created_at, u.user_name AS tutor_name, lr.unit_id
    FROM learning_requests lr
    LEFT JOIN tutor t ON lr.matched_tutor_id = t.tutor_id
    LEFT JOIN users u ON t.tutor_id = u.user_id
    WHERE lr.request_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

if (!$request) {
    echo "Request not found.";
    exit;
}

// Fetch unit name
$unit_stmt = $conn->prepare("SELECT unit_name FROM units WHERE unit_id = ?");
$unit_stmt->bind_param("i", $request['unit_id']);
$unit_stmt->execute();
$unit_res = $unit_stmt->get_result()->fetch_assoc();
$unit_name = $unit_res['unit_name'] ?? '-';
?>

<div class="page-title">
    <h2>Request Details</h2>
</div>

<table class="request-details">
    <tr>
        <th>Request ID:</th>
        <td><?= $request['request_id'] ?></td>
    </tr>
    <tr>
        <th>Unit:</th>
        <td><?= htmlspecialchars($unit_name) ?></td>
    </tr>
    <tr>
        <th>Assigned Tutor:</th>
        <td><?= htmlspecialchars($request['tutor_name'] ?? '-') ?></td>
    </tr>
    <tr>
        <th>Status:</th>
        <td><?= htmlspecialchars(ucfirst($request['status'])) ?></td>
    </tr>
    <tr>
        <th>Description:</th>
        <td><?= nl2br(htmlspecialchars($request['description'])) ?></td>
    </tr>
    <tr>
        <th>Submitted At:</th>
        <td><?= $request['created_at'] ?></td>
    </tr>
</table>
