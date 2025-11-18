<?php
/**
 * Session Check and Role-based Access Control
 * Include this at the top of every page.
 */

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id'], $_SESSION['role']);
}

/**
 * Get current user ID
 * @return int|null
 */
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 * @return string|null
 */
function get_current_user_role() {
    return $_SESSION['role'] ?? null;
}

/**
 * Check if user has specific role
 * @param string $role
 * @return bool
 */
function has_role(string $role) {
    return is_logged_in() && $_SESSION['role'] === $role;
}

/**
 * Require user to be logged in
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit();
    }
}

/**
 * Require user to have a specific role
 * @param string $role
 */
function require_role(string $role) {
    require_login();
    if (!has_role($role)) {
        http_response_code(403);
        echo "Access denied. Required role: " . ucfirst($role);
        exit();
    }
}

/**
 * Redirect user based on role
 */
function redirect_by_role() {
    if (!is_logged_in()) return;

    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: admin/admin_dash.php');
            break;
        case 'tutor':
            header('Location: tutor/tutor_dash.php');
            break;
        case 'learner':
            if (empty($_SESSION['student_id'])) {
                header('Location: learner/learner_setup.php');
            } else {
                header('Location: learner/learner_dash.php');
            }
            break;
        default:
            header('Location: auth/login.php');
    }
    exit();
}

/**
 * Destroy session safely
 */
function safe_session_destroy() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }
}

/**
 * Regenerate session ID periodically for security
 */
function regenerate_session_id() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        if (!isset($_SESSION['last_regeneration']) || time() - $_SESSION['last_regeneration'] > 300) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
}

// Auto-run regeneration
regenerate_session_id();

// Redirect logic for pages
$allowed_without_login = ['login.php', 'signup.php', 'index.php'];
$current_page = basename($_SERVER['PHP_SELF']);

if (!is_logged_in() && !in_array($current_page, $allowed_without_login)) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
}

// Learner profile setup enforcement
if (is_logged_in() && $_SESSION['role'] === 'learner' && empty($_SESSION['student_id'])) {
    $allowed_pages = ['learner_setup.php', 'logout.php'];
    if (!in_array($current_page, $allowed_pages)) {
        header('Location: learner/learner_setup.php');
        exit();
    }
}
?>
