<?php
require_once __DIR__ . "/../../database/conf.php";


echo "==== UNIT TEST: USER REGISTRATION & LOGIN ====\n";

function registerUser($conn, $email, $password, $role) {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)");
    return $stmt->bind_param("sss", $email, $hash, $role) && $stmt->execute();
}

// TEST 1: Registration
$testEmail = "student_test_" . rand(1000,9999) . "@test.com";
$result = registerUser($conn, $testEmail, "12345678", "learner");

echo $result ? "✔ Registration Test Passed\n" : "✘ Registration Test Failed\n";

// TEST 2: Login
$stmt = $conn->prepare("SELECT password_hash FROM users WHERE email = ?");
$stmt->bind_param("s", $testEmail);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($hash);
$stmt->fetch();

if (password_verify("12345678", $hash)) {
    echo "✔ Login Test Passed\n";
} else {
    echo "✘ Login Test Failed\n";
}

echo "===============================\n";
