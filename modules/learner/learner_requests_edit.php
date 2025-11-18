<?php
require_once '../../includes/session_check.php';
require_once '../../database/conf.php';

$learner_id = $_SESSION['user_id'] ?? 0;

if($_SERVER['REQUEST_METHOD']=='POST'){
    $request_id = intval($_POST['request_id']);
    $unit_id = intval($_POST['unit_id']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("
        UPDATE learning_requests 
        SET unit_id=?, description=? 
        WHERE request_id=? AND learner_id=? AND status='open'
    ");
    $stmt->bind_param("isii", $unit_id, $description, $request_id, $learner_id);
    $stmt->execute();

    header("Location: learner_requests.php");
    exit();
}
