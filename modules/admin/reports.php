<?php
session_start();
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

// Fetch report data
$query = "
    SELECT 
        unit_id,
        COUNT(*) AS total_requests
    FROM learning_requests
    GROUP BY unit_id
";

$report_data = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports - Admin</title>
<!-- Flatly Bootswatch CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/flatly/bootstrap.min.css">
    
    <!-- Optional custom CSS -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- Bootstrap Bundle (JS + Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

<?php include 'admin_nav.php'; ?>

<div class="wrapper">

    <h2>Reports</h2>

    <table>
        <thead>
            <tr>
                <th>Unit ID</th>
                <th>Total Requests</th>
            </tr>
        </thead>

        <tbody>
        <?php while($row = $report_data->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['unit_id']; ?></td>
                <td><?php echo $row['total_requests']; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>

    </table>

</div>

</body>
</html>

