<?php
require_once(__DIR__ . '/../database/conf.php');

function logActivity($user_id, $action, $module, $description) {
    global $conn;

    if (!$conn) {
        error_log("DB connection missing in logger.php");
        return false;
    }

    // Handle NULL / anonymous user
    $user_id = (empty($user_id) || $user_id == 0) ? null : $user_id;

    // If user_id is NULL → separate SQL (MySQL can't bind NULL for integer)
    if ($user_id === null) {
        $stmt = $conn->prepare("
            INSERT INTO system_logs (user_id, action, category, details, time)
            VALUES (NULL, ?, ?, ?, NOW())
        ");

        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }

        $stmt->bind_param("sss", $action, $module, $description);

    } else {
        $stmt = $conn->prepare("
            INSERT INTO system_logs (user_id, action, category, details, time)
            VALUES (?, ?, ?, ?, NOW())
        ");

        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }

        $stmt->bind_param("isss", $user_id, $action, $module, $description);
    }

    $result = $stmt->execute();

    if (!$result) {
        error_log("Execute failed: " . $stmt->error);
    }

    $stmt->close();
    return $result;
}
?>
