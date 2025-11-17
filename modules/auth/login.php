<?php
ini_set('session.cookie_path', '/');
session_start();

require_once '../../database/conf.php';
require_once '../../includes/logger.php';

$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $selected_role = trim($_POST["role"]);

    if (empty($email) || empty($password) || empty($selected_role)) {
        $error_msg = "All fields are required.";
    } else {

        // Fetch user
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
            } elseif ($selected_role !== $user['role']) {
                $error_msg = "Role mismatch! You selected '{$selected_role}' but registered as '{$user['role']}'.";
            } elseif (password_verify($password, $user['password_hash'])) {

                // Session
                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['email']     = $user['email'];

                logActivity($user['user_id'], "LOGIN", "Authentication", "Successful login");

                // Redirect
                if ($user['role'] === "admin") {
                    header("Location: ../admin/admin_dash.php");
                } elseif ($user['role'] === "tutor") {
                    header("Location: ../tutor/tutor_dash.php");
                } else {
                    header("Location: ../learner/learner_dash.php");
                }
                exit;
            } else {
                $error_msg = "Incorrect password.";
            }

        } else {
            $error_msg = "Account not found.";
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
        <p class="error"><?php echo htmlspecialchars($error_msg); ?></p>
      <?php endif; ?>

      <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <!-- NEW ROLE DROPDOWN -->
        <select name="role" required>
          <option value="">-- Select Role --</option>
          <option value="learner">Learner</option>
          <option value="tutor">Tutor</option>
          <option value="admin">Admin</option>
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
