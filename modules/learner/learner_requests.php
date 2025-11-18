<?php
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

$learner_id = $_SESSION['user_id'] ?? 0;

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // Only allow deletion of open requests
    $del_stmt = $conn->prepare("DELETE FROM learning_requests WHERE request_id = ? AND learner_id = ? AND status='open'");
    $del_stmt->bind_param("ii", $delete_id, $learner_id);
    $del_stmt->execute();
    header("Location: learner_requests.php");
    exit();
}

// Fetch learner requests
$stmt = $conn->prepare("
    SELECT lr.request_id, lr.unit_id, lr.description, lr.status, lr.created_at, t.tutor_id, u.user_name AS tutor_name
    FROM learning_requests lr
    LEFT JOIN tutor t ON lr.matched_tutor_id = t.tutor_id
    LEFT JOIN users u ON t.tutor_id = u.user_id
    WHERE lr.learner_id = ?
    ORDER BY lr.created_at DESC
");
$stmt->bind_param("i", $learner_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all units for editing
$unit_res = $conn->query("SELECT unit_id, unit_name FROM units ORDER BY unit_name ASC");
$units = [];
while($row = $unit_res->fetch_assoc()) {
    $units[$row['unit_id']] = $row['unit_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Requests & Recent Activity</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f9; padding:2rem; }
        h2 { border-bottom:2px solid #3498db; padding-bottom:5px; color:#2c3e50; }
        table { width:100%; border-collapse: collapse; background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-top:1rem; }
        th, td { padding:12px 15px; text-align:left; border-bottom:1px solid #ddd; }
        th { background:#3498db; color:white; font-weight:600; }
        tr:nth-child(even) { background:#f9f9f9; }
        tr:hover { background:#e8f0fe; }
        .btn { padding:6px 12px; border-radius:4px; text-decoration:none; color:white; margin-right:5px; transition:0.3s; }
        .view-btn { background:#2c3e50; } .view-btn:hover { background:#3498db; }
        .edit-btn { background:#f39c12; } .edit-btn:hover { background:#e67e22; }
        .del-btn { background:#e74c3c; } .del-btn:hover { background:#c0392b; }
        /* Modal */
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); justify-content:center; align-items:center; }
        .modal-content { background:white; padding:20px; border-radius:8px; width:400px; max-width:90%; }
        .modal-content h3 { margin-top:0; }
        .modal-content label { display:block; margin-top:10px; font-weight:500; }
        .modal-content input, .modal-content textarea, .modal-content select { width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:4px; }
        .modal-content button { margin-top:15px; padding:8px 12px; border:none; border-radius:4px; cursor:pointer; }
        .modal-close { background:#e74c3c; color:white; float:right; }
    </style>
</head>
<body>

<h2>My Requests & Recent Activity</h2>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Unit</th>
            <th>Description</th>
            <th>Assigned Tutor</th>
            <th>Status</th>
            <th>Submitted At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['request_id'] ?></td>
            <td><?= htmlspecialchars($units[$row['unit_id']] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= htmlspecialchars($row['tutor_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td>
                <a href="learner_request_view.php?id=<?= $row['request_id'] ?>" class="btn view-btn">View</a>
                <?php if($row['status']=='open'): ?>
                    <button class="btn edit-btn" onclick="openEditModal(<?= $row['request_id'] ?>, <?= $row['unit_id'] ?>, '<?= htmlspecialchars(addslashes($row['description'])) ?>')">Edit</button>
                    <a href="?delete_id=<?= $row['request_id'] ?>" class="btn del-btn" onclick="return confirm('Are you sure you want to delete this request?')">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<!-- Edit Modal -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()">X</button>
        <h3>Edit Request</h3>
        <form method="post" action="learner_requests_edit.php">
            <input type="hidden" name="request_id" id="modal_request_id">
            <label for="unit">Unit</label>
            <select name="unit_id" id="modal_unit" required>
                <?php foreach($units as $uid => $uname): ?>
                    <option value="<?= $uid ?>"><?= htmlspecialchars($uname) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="description">Description</label>
            <textarea name="description" id="modal_description" rows="4" required></textarea>
            <button type="submit" style="background:#3498db;color:white;">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, unit_id, description){
    document.getElementById('modal_request_id').value = id;
    document.getElementById('modal_unit').value = unit_id;
    document.getElementById('modal_description').value = description;
    document.getElementById('editModal').style.display = 'flex';
}
function closeModal(){
    document.getElementById('editModal').style.display = 'none';
}
</script>
</body>
</html>
