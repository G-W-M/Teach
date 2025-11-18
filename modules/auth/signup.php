<?php
session_start();
require_once '../../database/conf.php';
require_once '../../includes/logger.php';

$success_msg = '';
$error_msg = '';

if (isset($_POST['signup'])) {
    $user_name = trim($_POST['user_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'learner';

    // Validation
    if (empty($user_name) || empty($email) || empty($password) || empty($role)) {
        $error_msg = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Invalid email format.";
    } elseif (!preg_match('/@strathmore\.edu$/', $email)) {
        $error_msg = "Only Strathmore University emails are allowed.";
    } elseif (strlen($password) < 6) {
        $error_msg = "Password must be at least 6 characters long.";
    } else {

        // Check if email exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $exists = $stmt->get_result();
        $stmt->close();

        if ($exists->num_rows > 0) {
            $error_msg = "Email already registered.";
        } else {

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt2 = $conn->prepare("INSERT INTO users (user_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("ssss", $user_name, $email, $password_hash, $role);

            if ($stmt2->execute()) {
                $user_id = $stmt2->insert_id;

                // Generate Student ID
                $student_id = "STU" . str_pad($user_id, 6, '0', STR_PAD_LEFT);

                $stmt3 = $conn->prepare("UPDATE users SET student_id = ? WHERE user_id = ?");
                $stmt3->bind_param("si", $student_id, $user_id);
                $stmt3->execute();
                $stmt3->close();

                logActivity($user_id, "REGISTER", "Authentication", "New user registered: $email");

                $success_msg = "Signup successful! Your Student ID: $student_id";
            } else {
                $error_msg = "Signup failed. Try again.";
            }
            $stmt2->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - TeachMe</title>
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>

<div class="signup-wrapper">
    <div class="signup-box">
        <h2>Sign Up</h2>

        <?php if ($success_msg): ?>
            <div class="success-box"><?php echo $success_msg; ?></div>
            <a href="login.php" class="form-button">Login</a>
        <?php else: ?>

            <?php if ($error_msg): ?>
                <div class="error-box"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="user_name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>

                <!-- ✅ ROLE DROPDOWN MOVED HERE -->
                <select name="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="learner">Learner</option>
                    <option value="tutor">Tutor</option>
                </select>

                <button type="submit" name="signup" class="form-button">Sign Up</button>
            </form>

            <p class="signup-link">
                Already have an account? <a href="login.php">Login</a>
            </p>

        <?php endif; ?>
    </div>
</div>

</body>
</html>
