<?php
session_start();
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

$counts = [
    'users' => 10,
    'learners' => 5,
    'tutors' => 3,
    'sessions' => 8
];

$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - TeachMe</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/flatly/bootstrap.min.css">
    
    <!-- Optional custom CSS -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- Bootstrap Bundle (JS + Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body style="background:#f4f6f8;">

<?php include 'admin_nav.php'; ?>

<div class="container py-5">

    <div class="p-5 mb-4 text-white rounded-4" 
         style="background:linear-gradient(135deg,#667eea,#764ba2);">
        <h1 class="fw-bold">Welcome back, <?= htmlspecialchars($user_name) ?></h1>
        <p class="mb-0">Your Admin Dashboard</p>
    </div>

    <div class="row g-4">

        <?php
        $labels = [
            'users' => "Total Users",
            'learners' => "Total Learners",
            'tutors' => "Total Tutors",
            'sessions' => "Total Sessions",
        ];
        
        foreach ($labels as $key => $label):
        ?>
            <div class="col-md-3">
                <div class="bg-white p-4 text-center rounded shadow-sm border-start border-4 border-primary">
                    <h5><?= $label ?></h5>
                    <div class="display-5 fw-bold text-dark"><?= $counts[$key] ?></div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

</div>

</body>
</html>
