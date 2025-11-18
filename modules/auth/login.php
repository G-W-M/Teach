<?php
session_start();
require_once '../../database/conf.php';
require_once '../../includes/logger.php';

$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if (empty($email) || empty($password)) {
        $error_msg = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Invalid email format.";
    } elseif (!preg_match('/@strathmore\.edu$/', $email)) {
        $error_msg = "Only Strathmore University emails are allowed.";
    } else {

        $stmt = $conn->prepare("
            SELECT user_id, user_name, email, password_hash, role, is_active
            FROM users
            WHERE email = ?
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (!$user['is_active']) {
                $error_msg = "Please verify your email first.";
            } elseif (password_verify($password, $user['password_hash'])) {

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];

                logActivity($user['user_id'], "LOGIN", "Authentication", "Successful login");

                // Redirect by role
                if ($user['role'] === "admin") {
                    header("Location: ../admin/admin_dash.php");
                } elseif ($user['role'] === "tutor") {
                    header("Location: ../tutor/tutor_dash.php");
                } else {
                    header("Location: ../learner/learner_dash.php");
                }
                exit();
            } else {
                $error_msg = "Incorrect password.";
            }
        } else {
            $error_msg = "Account not found.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - TeachMe</title>
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">
        <h2>Login</h2>

        <?php if ($error_msg): ?>
            <div class="error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <!-- ❌ REMOVED ROLE DROPDOWN -->
            <!-- LOGIN auto-detects role from DB now -->

            <button type="submit" class="form-button">Login</button>
        </form>

        <p class="login-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
</div>
</body>
</html>
