<?php
require_once "../../database/conf.php";

echo "==== UNIT TEST: FEEDBACK SYSTEM ====\n";

$session_id = 1; // must exist
$from = 2;
$to = 3;

$rating = 5;
$comment = "Excellent tutoring session";

$stmt = $conn->prepare("
    INSERT INTO feedback (session_id, from_user, to_user, rating, comments)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("iiiis", $session_id, $from, $to, $rating, $comment);

if ($stmt->execute()) {
    echo "✔ Feedback Insert Test Passed\n";
} else {
    echo "✘ Feedback Insert Test Failed\n";
}

$check = $conn->query("SELECT rating FROM feedback WHERE session_id = $session_id ORDER BY feedback_id DESC LIMIT 1");
$row = $check->fetch_assoc();

echo ($row['rating'] == 5)
    ? "✔ Feedback Retrieval Passed\n"
    : "✘ Feedback Retrieval Failed\n";

echo "==============================\n";
