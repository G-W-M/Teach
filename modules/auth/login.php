<?php
session_start();
require_once '../../database/conf.php';
require_once '../../includes/logger.php';

$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $selected_role = trim($_POST["role"] ?? '');

    if (empty($email) || empty($password) || empty($selected_role)) {
        $error_msg = "All fields are required.";
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
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (!$user['is_active']) {
                    $error_msg = "Please verify your email first.";
                } elseif ($selected_role !== $user['role']) {
                    $error_msg = "Role mismatch!";
                } elseif (password_verify($password, hash: $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_name'] = $user['user_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['email'] = $user['email'];

                    logActivity($user['user_id'], "LOGIN", "Authentication", "Successful login");

                    // Redirect by role
                    switch ($user['role']) {
                        case "admin": header("Location: ../admin/admin_dash.php"); break;
                        case "tutor": header("Location: ../tutor/tutor_dash.php"); break;
                        default: header("Location: ../learner/learner_dash.php"); break;
                    }
                    exit;
                } else {
                    $error_msg = "Incorrect password.";
                }
            } else {
                $error_msg = "Account not found.";
            }
            $stmt->close();
        } else {
            $error_msg = "Database error. Please try again.";
        }
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

        <?php if (!empty($error_msg)): ?>
            <div class="error" style="color:red; margin-bottom:10px; padding:10px; background:#f8d7da; border-radius:5px;">
                <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            <input type="password" name="password" placeholder="Password" required>

            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="learner" <?php echo (($_POST['role'] ?? '')=='learner')?'selected':''; ?>>Learner</option>
                <option value="tutor" <?php echo (($_POST['role'] ?? '')=='tutor')?'selected':''; ?>>Tutor</option>
                <option value="admin" <?php echo (($_POST['role'] ?? '')=='admin')?'selected':''; ?>>Admin</option>
            </select>

            <button type="submit" class="form-button">Login</button>
        </form>

        <p class="login-link">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </p>
    </div>
</div>
</body>
</html>
