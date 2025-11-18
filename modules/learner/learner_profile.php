<?php
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

if ($_SESSION['role'] !== 'learner') {
    header("Location: ../../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Get current user data with profile info
$user_query = "SELECT u.student_id, u.email, u.user_name, u.phone, u.date_joined,
                      lp.year_of_study, lp.faculty
               FROM users u
               LEFT JOIN learner_profiles lp ON u.user_id = lp.user_id
               WHERE u.user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $user_name = trim($_POST['user_name']);
        $phone = trim($_POST['phone']);
        $year_of_study = $_POST['year_of_study'];
        $faculty = $_POST['faculty'];
        
        // Handle admission number if not set
        $student_id = $user_data['student_id'];
        if (empty($student_id) && isset($_POST['student_id'])) {
            $new_student_id = strtoupper(trim($_POST['student_id']));
            
            // Validate new admission number
            if (!empty($new_student_id) && preg_match('/^[A-Z0-9]{4,20}$/', $new_student_id)) {
                // Check if admission number is available
                $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE student_id = ? AND user_id != ?");
                $check_stmt->bind_param("si", $new_student_id, $user_id);
                $check_stmt->execute();
                
                if ($check_stmt->get_result()->num_rows === 0) {
                    $student_id = $new_student_id;
                } else {
                    $message = "This admission number is already registered";
                }
                $check_stmt->close();
            }
        }

        if (empty($message)) {
            try {
                // Start transaction
                $conn->begin_transaction();
                
                // Update users table
                $update_user = "UPDATE users SET user_name = ?, phone = ?, student_id = ? WHERE user_id = ?";
                $user_stmt = $conn->prepare($update_user);
                $user_stmt->bind_param("sssi", $user_name, $phone, $student_id, $user_id);
                $user_stmt->execute();
                
                // Update or insert learner profile
                if ($user_data['year_of_study']) {
                    $update_profile = "UPDATE learner_profiles SET year_of_study = ?, faculty = ? WHERE user_id = ?";
                    $profile_stmt = $conn->prepare($update_profile);
                    $profile_stmt->bind_param("ssi", $year_of_study, $faculty, $user_id);
                } else {
                    $insert_profile = "INSERT INTO learner_profiles (user_id, year_of_study, faculty) VALUES (?, ?, ?)";
                    $profile_stmt = $conn->prepare($insert_profile);
                    $profile_stmt->bind_param("iss", $user_id, $year_of_study, $faculty);
                }
                $profile_stmt->execute();
                
                $conn->commit();
                
                // Update session
                $_SESSION['user_name'] = $user_name;
                if (!empty($student_id) && $student_id !== $user_data['student_id']) {
                    $_SESSION['student_id'] = $student_id;
                }
                
                $message = "success:Profile updated successfully!";
                
                // Refresh data
                $stmt = $conn->prepare($user_query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user_data = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                
            } catch (Exception $e) {
                $conn->rollback();
                $message = "Error updating profile: " . $e->getMessage();
            }
        }
    }
    
    // Handle password change separately
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $message = "All password fields are required";
        } elseif ($new_password !== $confirm_password) {
            $message = "New passwords don't match";
        } elseif (strlen($new_password) < 6) {
            $message = "New password must be at least 6 characters";
        } else {
            // Verify current password
            $check_stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
            $check_stmt->bind_param("i", $user_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result()->fetch_assoc();
            
            if (!password_verify($current_password, $result['password_hash'])) {
                $message = "Current password is incorrect";
            } else {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
                $update_stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($update_stmt->execute()) {
                    $message = "success:Password changed successfully!";
                } else {
                    $message = "Error changing password";
                }
                $update_stmt->close();
            }
            $check_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - TeachMe</title>
    <link rel="stylesheet" href="../../assets/css/learner.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
</head>
<body>
    <?php include 'learner_nav.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>My Profile</h1>
            <p>Manage your personal information and account settings</p>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, 'success:') === 0 ? 'alert-success' : 'alert-error'; ?>">
                <?php echo str_replace('success:', '', $message); ?>
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img src="../../assets/img/learner_icon.png" alt="Learner">
                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($user_data['user_name']); ?></h2>
                        <p class="profile-role">
                            <?php echo !empty($user_data['student_id']) ? 
                                'Student • ' . htmlspecialchars($user_data['student_id']) : 
                                'Learner (Profile Incomplete)'; ?>
                        </p>
                    </div>
                </div>

                <form method="POST" class="profile-form">
                    <div class="form-section">
                        <h3>📋 Personal Information</h3>
                        
                        <?php if (empty($user_data['student_id'])): ?>
                            <div class="form-group">
                                <label for="student_id">Admission Number *</label>
                                <input type="text" id="student_id" name="student_id" 
                                       value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" 
                                       placeholder="e.g., 123456, ABC123X" 
                                       pattern="[A-Za-z0-9]{4,20}"
                                       required>
                                <small class="form-help">Your Strathmore University admission number</small>
                            </div>
                        <?php else: ?>
                            <div class="form-group">
                                <label>Admission Number</label>
                                <input type="text" value="<?php echo htmlspecialchars($user_data['student_id']); ?>" 
                                       readonly class="readonly-field">
                                <small class="form-help">Your admission number is permanently set</small>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" 
                                   readonly class="readonly-field">
                        </div>

                        <div class="form-group">
                            <label for="user_name">Full Name *</label>
                            <input type="text" id="user_name" name="user_name" 
                                   value="<?php echo htmlspecialchars($user_data['user_name']); ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>"
                                   placeholder="07XXXXXXXX"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="year_of_study">Year of Study</label>
                            <select id="year_of_study" name="year_of_study">
                                <option value="">Select year...</option>
                                <option value="1" <?php echo ($user_data['year_of_study'] ?? '') == '1' ? 'selected' : ''; ?>>Year 1</option>
                                <option value="2" <?php echo ($user_data['year_of_study'] ?? '') == '2' ? 'selected' : ''; ?>>Year 2</option>
                                <option value="3" <?php echo ($user_data['year_of_study'] ?? '') == '3' ? 'selected' : ''; ?>>Year 3</option>
                                <option value="4" <?php echo ($user_data['year_of_study'] ?? '') == '4' ? 'selected' : ''; ?>>Year 4</option>
                                <option value="5" <?php echo ($user_data['year_of_study'] ?? '') == '5' ? 'selected' : ''; ?>>Year 5</option>
                                <option value="postgraduate" <?php echo ($user_data['year_of_study'] ?? '') == 'postgraduate' ? 'selected' : ''; ?>>Postgraduate</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="faculty">Faculty/School</label>
                            <select id="faculty" name="faculty">
                                <option value="">Select faculty...</option>
                                <option value="Business" <?php echo ($user_data['faculty'] ?? '') == 'Business' ? 'selected' : ''; ?>>Strathmore University Business School</option>
                                <option value="Law" <?php echo ($user_data['faculty'] ?? '') == 'Law' ? 'selected' : ''; ?>>Strathmore Law School</option>
                                <option value="IT" <?php echo ($user_data['faculty'] ?? '') == 'IT' ? 'selected' : ''; ?>>School of Computing and Engineering</option>
                                <option value="Hospitality" <?php echo ($user_data['faculty'] ?? '') == 'Hospitality' ? 'selected' : ''; ?>>School of Hospitality</option>
                                <option value="Humanities" <?php echo ($user_data['faculty'] ?? '') == 'Humanities' ? 'selected' : ''; ?>>School of Humanities</option>
                                <option value="Health" <?php echo ($user_data['faculty'] ?? '') == 'Health' ? 'selected' : ''; ?>>School of Health Sciences</option>
                                <option value="other" <?php echo ($user_data['faculty'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Member Since</label>
                            <input type="text" value="<?php echo date('F j, Y', strtotime($user_data['date_joined'])); ?>" 
                                   readonly class="readonly-field">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="update_profile" class="btn btn-primary">💾 Save Changes</button>
                        <a href="learner_dash.php" class="btn btn-outline">← Back to Dashboard</a>
                    </div>
                </form>

                <div class="form-section">
                    <h3>🔒 Change Password</h3>
                    <form method="POST" class="password-form">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password"
                                   placeholder="Enter current password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password"
                                   placeholder="At least 6 characters"
                                   minlength="6" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password"
                                   placeholder="Repeat new password" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="change_password" class="btn btn-secondary">🔄 Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <script>
        // Password validation
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');

            function validatePasswords() {
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity("Passwords don't match");
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }

            newPassword.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);

            // Convert admission number to uppercase
            const studentId = document.getElementById('student_id');
            if (studentId) {
                studentId.addEventListener('input', function(e) {
                    this.value = this.value.toUpperCase();
                });
            }

            // Auto-format phone number
            const phone = document.getElementById('phone');
            if (phone) {
                phone.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value.length > 0 && !this.value.startsWith('0')) {
                        this.value = '0' + this.value;
                    }
                });
            }
        });
    </script>
</body>
</html>