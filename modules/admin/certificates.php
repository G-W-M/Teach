<?php
session_start();
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

// Fetch certificate records
$query = "
    SELECT 
        c.certificate_id,
        c.certificate_type,
        c.issued_date,
        t.tutor_id,
        u.user_name AS tutor_name
    FROM certificates c
    JOIN tutor t ON c.tutor_id = t.tutor_id
    JOIN users u ON t.tutor_id = u.user_id
    ORDER BY c.issued_date DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Certificates - Admin</title>

<!-- Bootswatch Flatly theme -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/flatly/bootstrap.min.css">

<!-- Optional custom CSS -->
<link rel="stylesheet" href="../../assets/css/admin.css">

<!-- Bootstrap Bundle (JS + Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

<?php include 'admin_nav.php'; ?>

<div class="page-wrapper">

    <h2>Certificates</h2>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Certificate ID</th>
                <th>Tutor Name</th>
                <th>Certificate Type</th>
                <th>Issued Date</th>
            </tr>
        </thead>

        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['certificate_id']; ?></td>
                <td><?php echo htmlspecialchars($row['tutor_name']); ?></td>
                <td><?php echo htmlspecialchars($row['certificate_type']); ?></td>
                <td><?php echo date('F j, Y g:i A', strtotime($row['issued_date'])); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>
