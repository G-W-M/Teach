<?php
/**
 * Session Check and Role-based Access Control
 * Central session management file - should be included first in all pages
 */

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Redirect to login if not logged in
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit();
    }
}

/**
 * Check if user has specific role
 */
function has_role($required_role) {
    return is_logged_in() && $_SESSION['role'] === $required_role;
}

/**
 * Require specific role for access
 */
function require_role($required_role) {
    require_login();
    
    if (!has_role($required_role)) {
        http_response_code(403);
        echo "Access denied. Required role: " . ucfirst($required_role);
        exit();
    }
}

/**
 * Redirect based on user role
 */
function redirect_by_role() {
    if (!is_logged_in()) {
        return;
    }
    
    $role = $_SESSION['role'];
    switch ($role) {
        case 'admin':
            header('Location: admin_dash.php');
            break;
        case 'tutor':
            header('Location: tutor_dash.php');
            break;
        case 'learner':
            // Check if learner needs to complete profile setup
            if (empty($_SESSION['student_id'])) {
                header('Location: learner_setup.php');
            } else {
                header('Location: learner_dash.php');
            }
            break;
        default:
            header('Location: login.php');
    }
    exit();
}

/**
 * Get current user ID
 */
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function get_current_user_role() {
    return $_SESSION['role'] ?? null;
}

/**
 * Check if user is active
 */
function is_user_active() {
    return isset($_SESSION['is_active']) && $_SESSION['is_active'] === true;
}

/**
 * Safe session destroy with logging
 */
function safe_session_destroy() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }
}

/**
 * Regenerate session ID for security
 */
function regenerate_session() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

// Main session check logic
if (!is_logged_in()) {
    // Allow access to login and signup pages without redirect
    $allowed_pages = ['login.php', 'signup.php', 'index.php', ''];
    $current_page = basename($_SERVER['PHP_SELF']);
    
    if (!in_array($current_page, $allowed_pages)) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit();
    }
} else {
    // For logged-in users, check if learner needs profile setup
    if ($_SESSION['role'] === 'learner' && empty($_SESSION['student_id'])) {
        $current_page = basename($_SERVER['PHP_SELF']);
        $allowed_pages = ['learner_setup.php', 'logout.php'];
        
        if (!in_array($current_page, $allowed_pages)) {
            header("Location: learner_setup.php");
            exit();
        }
    }
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['last_regeneration']) || time() - $_SESSION['last_regeneration'] > 300) {
        regenerate_session();
        $_SESSION['last_regeneration'] = time();
    }
}
?>