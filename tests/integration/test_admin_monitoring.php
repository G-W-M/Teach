<?php
require_once __DIR__ . "/../../database/conf.php";

echo "==== INTEGRATION TEST: ADMIN MONITORING ====\n";

// 1. Count tutors
$tutors = $conn->query("SELECT COUNT(*) AS c FROM tutor")->fetch_assoc()['c'];
echo "✔ Tutors Counted: $tutors\n";

// 2. Count pending applications
$pending = $conn->query("SELECT COUNT(*) AS c FROM tutor_applications WHERE status='pending'")
                ->fetch_assoc()['c'];
echo "✔ Pending Tutor Applications: $pending\n";

// 3. Count sessions
$sessions = $conn->query("SELECT COUNT(*) AS c FROM sessions")->fetch_assoc()['c'];
echo "✔ Total Sessions: $sessions\n";

// 4. Count feedback entries
$feedback = $conn->query("SELECT COUNT(*) AS c FROM feedback")->fetch_assoc()['c'];
echo "✔ Feedback Records: $feedback\n";

echo "\n==== ADMIN MONITORING TEST COMPLETED ====\n";
