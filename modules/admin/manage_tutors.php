<?php 
session_start();
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

// Fetch tutors
$query = "
    SELECT 
        u.user_id, 
        u.user_name, 
        u.email, 
        t.status 
    FROM users u 
    LEFT JOIN tutor t 
        ON u.user_id = t.tutor_id 
    WHERE u.role = 'tutor'
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Tutors - Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/flatly/bootstrap.min.css">
    
    <!-- Optional custom CSS -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- Bootstrap Bundle (JS + Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

<?php include 'admin_nav.php'; ?>

<div class="table-wrapper">

    <h2>Manage Tutors</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['user_id']; ?></td>
                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo $row['status'] ?? 'N/A'; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>
