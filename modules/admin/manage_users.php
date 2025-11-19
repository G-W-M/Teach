<?php
session_start();
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';
require_once '../../includes/logger.php'; // logActivity()

$message = "";

// Delete user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Delete user record
    if ($conn->query("DELETE FROM users WHERE user_id = $id")) {
        $message = "User deleted successfully.";

        // Log activity
        logActivity($_SESSION['user_id'], 'DELETE_USER', 'User Management', "Deleted user with ID $id");
    } else {
        $message = "Failed to delete user.";
    }
}

// Handle search and filter
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';

$query = "SELECT * FROM users WHERE 1";
$params = [];
$types = '';

if ($search) {
    $query .= " AND (user_name LIKE ? OR email LIKE ?)";
    $searchParam = "%$search%";
    $params[] = &$searchParam;
    $params[] = &$searchParam;
    $types .= 'ss';
}

if ($role_filter) {
    $query .= " AND role = ?";
    $params[] = &$role_filter;
    $types .= 's';
}

$query .= " ORDER BY date_joined DESC";

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$users = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users | Admin</title>

<!-- Bootswatch Flatly theme -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/flatly/bootstrap.min.css">

<!-- Optional custom CSS -->
<link rel="stylesheet" href="../../assets/css/admin.css">

<!-- Bootstrap Bundle (JS + Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">
<?php include 'admin_nav.php'; ?>

<main class="container my-5 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">Manage Users</h3>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success shadow-sm"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Search & Filter -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name or email..." class="form-control">
                </div>
                <div class="col-md-4">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="learner" <?= $role_filter === 'learner' ? 'selected' : '' ?>>Learner</option>
                        <option value="tutor" <?= $role_filter === 'tutor' ? 'selected' : '' ?>>Tutor</option>
                        <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-success fw-semibold">
                        <i class="bi bi-funnel-fill me-1"></i>Apply Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-wrapper shadow-sm rounded bg-white p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-primary m-0">User List</h5>
            <a href="add_user.php" class="btn btn-primary btn-sm">
                <i class="bi bi-person-plus-fill me-1"></i>Add New
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Date Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users->num_rows > 0): ?>
                        <?php while ($u = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?= $u['user_id'] ?></td>
                                <td><?= htmlspecialchars($u['user_name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : ($u['role']==='tutor' ? 'info' : 'success') ?>">
                                        <?= ucfirst($u['role']) ?>
                                    </span>
                                </td>
                                <td><?= date('F j, Y', strtotime($u['date_joined'])) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit_user.php?id=<?= $u['user_id'] ?>" class="btn btn-warning">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="?delete=<?= $u['user_id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this user?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="bg-primary text-white text-center py-3 mt-auto">
    <small>&copy; <?= date('Y') ?> TeachMe | Admin Panel</small>
</footer>
</body>
</html>
