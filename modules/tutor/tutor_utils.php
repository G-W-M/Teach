<?php
require_once('../../database/conf.php');
require_once('logger.php');

/**
 * Get tutor's assigned learners count
 */
function getAssignedLearnersCount($tutor_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM learning_requests WHERE matched_tutor_id = ? AND status IN ('matched','completed')");
    $stmt->bind_param("i", $tutor_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result['total'] ?? 0;
}

/**
 * Get total competencies of a tutor
 */
function getTutorCompetenciesCount($tutor_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tutor_competencies WHERE tutor_id = ?");
    $stmt->bind_param("i", $tutor_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result['total'] ?? 0;
}

/**
 * Get tutor rating
 */
function getTutorRating($tutor_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT rating FROM tutor WHERE tutor_id = ?");
    $stmt->bind_param("i", $tutor_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return number_format($result['rating'] ?? 0, 1);
}

/**
 * Get upcoming sessions for a tutor
 */
function getTutorSessions($tutor_id, $limit = 50) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT s.session_id, s.session_date, s.start_time, s.end_time, u.user_name AS learner_name, sa.attended
        FROM sessions s
        JOIN session_attendance sa ON sa.session_id = s.session_id
        JOIN users u ON u.user_id = sa.user_id
        WHERE s.tutor_id = ?
        ORDER BY s.session_date DESC, s.start_time ASC
        LIMIT ?
    ");
    $stmt->bind_param("ii", $tutor_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $sessions = [];
    while($row = $result->fetch_assoc()) {
        $sessions[] = $row;
    }
    $stmt->close();
    return $sessions;
}

/**
 * Update attendance for a session
 * $attendanceData should be an associative array: [attendance_id => attended (1/0)]
 */
function updateAttendance($attendanceData) {
    global $conn;
    foreach ($attendanceData as $att_id => $attended) {
        $stmt = $conn->prepare("UPDATE session_attendance SET attended = ? WHERE attendance_id = ?");
        $stmt->bind_param("ii", $attended, $att_id);
        $stmt->execute();
        $stmt->close();
    }
}

/**
 * Get tutor profile details
 */
function getTutorProfile($tutor_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT t.*, u.user_name, u.email, u.phone FROM tutor t JOIN users u ON u.user_id = t.tutor_id WHERE t.tutor_id = ?");
    $stmt->bind_param("i", $tutor_id);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $profile;
}

/**
 * Update tutor profile
 */
function updateTutorProfile($tutor_id, $bio, $phone, $max_students, $status) {
    global $conn;
    $stmt = $conn->prepare("
        UPDATE tutor t
        JOIN users u ON u.user_id = t.tutor_id
        SET t.bio = ?, t.max_students = ?, t.status = ?, u.phone = ?
        WHERE t.tutor_id = ?
    ");
    $stmt->bind_param("sissi", $bio, $max_students, $status, $phone, $tutor_id);
    $success = $stmt->execute();
    $stmt->close();
    if ($success) logActivity($tutor_id, "UPDATE", "Tutor Profile", "Updated profile info");
    return $success;
}
