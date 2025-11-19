<?php
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';


$user_id = get_current_user_id();
$message = '';

// Check if student already has admission number
$check_query = "SELECT student_id FROM users WHERE user_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$user_data = $check_stmt->get_result()->fetch_assoc();
$check_stmt->close();

// If already has admission number, redirect to dashboard
if (!empty($user_data['student_id'])) {
    $_SESSION['student_id'] = $user_data['student_id'];
    header("Location: learner_dash.php");
    exit();
}

// Handle admission number setup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_profile'])) {
    $student_id = strtoupper(trim($_POST['student_id']));
    $phone = trim($_POST['phone']);
    $year_of_study = $_POST['year_of_study'];
    $faculty = $_POST['faculty'];

    // Validation
    if (empty($student_id)) {
        $message = "Admission number is required";
    } elseif (!preg_match('/^[A-Z0-9]{4,20}$/', $student_id)) {
        $message = "Admission number must be 4-20 characters (letters and numbers only)";
    } else {
        try {
            // Check if admission number is already taken
            $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE student_id = ? AND user_id != ?");
            $check_stmt->bind_param("si", $student_id, $user_id);
            $check_stmt->execute();
            
            if ($check_stmt->get_result()->num_rows > 0) {
                $message = "This admission number is already registered by another student";
            } else {
                // Update user with admission number
                $update_stmt = $conn->prepare("UPDATE users SET student_id = ?, phone = ? WHERE user_id = ?");
                $update_stmt->bind_param("ssi", $student_id, $phone, $user_id);
                
                if ($update_stmt->execute()) {
                    // Update session
                    $_SESSION['student_id'] = $student_id;
                    
                    // Create learner profile record
                    $profile_stmt = $conn->prepare("INSERT INTO learner_profiles (user_id, year_of_study, faculty) VALUES (?, ?, ?)");
                    $profile_stmt->bind_param("iss", $user_id, $year_of_study, $faculty);
                    $profile_stmt->execute();
                    $profile_stmt->close();
                    
                    $message = "success";
                    
                    // Redirect to dashboard
                    header("Location: learner_dash.php");
                    exit();
                } else {
                    $message = "Error updating profile. Please try again.";
                }
                $update_stmt->close();
            }
            $check_stmt->close();
        } catch (Exception $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile - TeachMe</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            height: 100vh;
            width: 100%;
            background: url('../../assets/img/bg.jpeg') no-repeat center center;
            background-size: 400px; 
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* WRAPPER FOR CENTERING AUTH FORMS */
        .setup-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* AUTH CONTAINER BOX */
        .setup-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px 50px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            text-align: center;
            width: 420px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .setup-box h2 {
            margin-bottom: 20px;
            color: #003366;
        }

        /* INPUT FIELDS */
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            font-size: 15px;
        }

        /* BUTTON */
        .form-button {
            display: inline-block;
            padding: 10px 20px;
            margin: 15px 0;
            background-color: #003366;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 500;
            border: none;
            width: 100%;
            cursor: pointer;
        }

        .form-button:hover {
            background-color: #0059b3;
        }

        /* ERROR MESSAGE */
        .error {
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
            text-align: center;
        }

        .setup-notice {
            background: #e7f3ff;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #1890ff;
            text-align: left;
        }

        .setup-notice h4 {
            margin: 0 0 10px 0;
            color: #1890ff;
            font-size: 14px;
        }

        .setup-notice ul {
            margin: 0;
            padding-left: 20px;
            font-size: 13px;
        }

        .setup-notice li {
            margin: 5px 0;
            color: #0056b3;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="setup-wrapper">
        <div class="setup-box">
            <h2>Complete Your Profile</h2>
            <p style="margin-bottom: 20px; color: #666;">Welcome to Strathmore University TeachMe</p>

            <?php if ($message && $message !== 'success'): ?>
                <p class="error"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="student_id">Admission Number *</label>
                    <input type="text" id="student_id" name="student_id" 
                           value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" 
                           placeholder="e.g., 123456, ABC123X" 
                           pattern="[A-Za-z0-9]{4,20}"
                           title="4-20 characters (letters and numbers only)"
                           required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                           placeholder="07XXXXXXXX"
                           required>
                </div>

                <div class="form-group">
                    <label for="year_of_study">Year of Study *</label>
                    <select id="year_of_study" name="year_of_study" required>
                        <option value="">Select year...</option>
                        <option value="1" <?php echo ($_POST['year_of_study'] ?? '') == '1' ? 'selected' : ''; ?>>Year 1</option>
                        <option value="2" <?php echo ($_POST['year_of_study'] ?? '') == '2' ? 'selected' : ''; ?>>Year 2</option>
                        <option value="3" <?php echo ($_POST['year_of_study'] ?? '') == '3' ? 'selected' : ''; ?>>Year 3</option>
                        <option value="4" <?php echo ($_POST['year_of_study'] ?? '') == '4' ? 'selected' : ''; ?>>Year 4</option>
                        <option value="5" <?php echo ($_POST['year_of_study'] ?? '') == '5' ? 'selected' : ''; ?>>Year 5</option>
                        <option value="postgraduate" <?php echo ($_POST['year_of_study'] ?? '') == 'postgraduate' ? 'selected' : ''; ?>>Postgraduate</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="faculty">Faculty/School *</label>
                    <select id="faculty" name="faculty" required>
                        <option value="">Select faculty...</option>
                        <option value="Business" <?php echo ($_POST['faculty'] ?? '') == 'Business' ? 'selected' : ''; ?>>Strathmore University Business School</option>
                        <option value="Law" <?php echo ($_POST['faculty'] ?? '') == 'Law' ? 'selected' : ''; ?>>Strathmore Law School</option>
                        <option value="IT" <?php echo ($_POST['faculty'] ?? '') == 'IT' ? 'selected' : ''; ?>>School of Computing and Engineering</option>
                        <option value="Hospitality" <?php echo ($_POST['faculty'] ?? '') == 'Hospitality' ? 'selected' : ''; ?>>School of Hospitality</option>
                        <option value="Humanities" <?php echo ($_POST['faculty'] ?? '') == 'Humanities' ? 'selected' : ''; ?>>School of Humanities</option>
                        <option value="Health" <?php echo ($_POST['faculty'] ?? '') == 'Health' ? 'selected' : ''; ?>>School of Health Sciences</option>
                        <option value="other" <?php echo ($_POST['faculty'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="setup-notice">
                    <h4>Important Notes:</h4>
                    <ul>
                        <li>Your admission number will be permanently linked to your account</li>
                        <li>This information helps us match you with the right tutors</li>
                        <li>You can update your phone number later in your profile</li>
                    </ul>
                </div>

                <button type="submit" name="setup_profile" class="form-button">
                    Complete Setup & Continue
                </button>
            </form>
        </div>
    </div>

    <script>
        // Convert admission number to uppercase
        document.getElementById('student_id').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });

        // Auto-format phone number
        document.getElementById('phone').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 0 && !this.value.startsWith('0')) {
                this.value = '0' + this.value;
            }
        });

        // Add loading state to button
        document.querySelector('form').addEventListener('submit', function() {
            const button = this.querySelector('button[type="submit"]');
            button.innerHTML = 'Setting up your profile...';
            button.disabled = true;
        });
    </script>
</body>
</html>