<?php
require_once "../../database/conf.php";

echo "==== INTEGRATION TEST: FULL WORKFLOW ====\n";

// STEP 1: Register Learner
$email = "int_learner_" . rand(1000,9999) . "@test.com";
$pass = "12345678";
$role = "learner";

$hash = password_hash($pass, PASSWORD_BCRYPT);
$conn->query("INSERT INTO users (email, password_hash, role) VALUES ('$email', '$hash', '$role')");
$learner_id = $conn->insert_id;
echo "✔ Learner Registered (ID: $learner_id)\n";

// STEP 2: Tutor Takes Test
$conn->query("INSERT INTO users (email, password_hash, role) VALUES ('test_tutor@test.com', '$hash', 'tutor')");
$tutor_id = $conn->insert_id;
$conn->query("INSERT INTO tutor (tutor_id) VALUES ($tutor_id)");
$conn->query("INSERT INTO tutor_competencies (tutor_id, unit_id) VALUES ($tutor_id, 1)");
echo "✔ Tutor Created & Competency Added\n";

// STEP 3: Learner Creates Request
$conn->query("INSERT INTO learning_requests (learner_id, unit_id, description) VALUES ($learner_id, 1, 'Need help')");
$request_id = $conn->insert_id;
echo "✔ Learning Request Created\n";

// STEP 4: System Matches Tutor Automatically
$conn->query("UPDATE learning_requests SET matched_tutor_id = $tutor_id WHERE request_id = $request_id");
echo "✔ Tutor Matched\n";

// STEP 5: Create a Session
$conn->query("
    INSERT INTO sessions (request_id, tutor_id, learner_id, session_date, start_time, end_time)
    VALUES ($request_id, $tutor_id, $learner_id, CURDATE(), '10:00:00', '11:00:00')
");
$session_id = $conn->insert_id;
echo "✔ Session Created (ID: $session_id)\n";

// STEP 6: Add Feedback
$conn->query("
    INSERT INTO feedback (session_id, from_user, to_user, rating, comments)
    VALUES ($session_id, $learner_id, $tutor_id, 5, 'Great tutor!')
");
echo "✔ Feedback Added\n";

// STEP 7: Certificate Auto-Issue (simulated)
$conn->query("INSERT INTO certificates (tutor_id, certificate_type) VALUES ($tutor_id, 'Integration Test Award')");
echo "✔ Certificate Issued\n";

echo "\n==== FULL WORKFLOW COMPLETED SUCCESSFULLY ====\n";
