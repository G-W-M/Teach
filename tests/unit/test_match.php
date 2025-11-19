<?php
require_once "../../database/conf.php";

echo "==== UNIT TEST: TUTOR MATCHING ====\n";

// Step 1: Create a dummy learner request
$learner_id = 2; // must exist
$unit_id = 1;
$desc = "Need help with Unit Testing";

$stmt = $conn->prepare("
    INSERT INTO learning_requests (learner_id, unit_id, description)
    VALUES (?, ?, ?)
");
$stmt->bind_param("iis", $learner_id, $unit_id, $desc);
$stmt->execute();
$request_id = $stmt->insert_id;

// Step 2: Retrieve matching tutors
$q = $conn->query("
    SELECT t.tutor_id
    FROM tutor t
    JOIN tutor_competencies c ON t.tutor_id = c.tutor_id
    WHERE c.unit_id = $unit_id AND t.current_students < t.max_students
");

if ($q->num_rows > 0) {
    echo "✔ Tutor Match Found\n";
} else {
    echo "✘ No Tutor Found (Check competencies data)\n";
}

echo "===============================\n";
