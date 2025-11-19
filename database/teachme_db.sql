-- Create database
CREATE DATABASE IF NOT EXISTS teachme;
USE teachme;

-- 1. USERS 
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    user_name VARCHAR(50),
    phone VARCHAR(20),
    role ENUM('learner','tutor','admin') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    date_joined DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. ADMIN 
CREATE TABLE admin (
    admin_id INT PRIMARY KEY,
    staff_number VARCHAR(50),
    FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 3. UNITS 
CREATE TABLE units (
    unit_id INT AUTO_INCREMENT PRIMARY KEY,
    unit_code VARCHAR(20) UNIQUE NOT NULL,
    unit_name VARCHAR(100) NOT NULL
);

-- 4. TUTOR APPLICATIONS 
CREATE TABLE tutor_applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    unit_id INT NOT NULL,
    test_score DECIMAL(5,2),
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    application_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id)
);

-- 5. TUTOR 
CREATE TABLE tutor (
    tutor_id INT PRIMARY KEY,
    bio TEXT,
    max_students INT DEFAULT 3,
    current_students INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    FOREIGN KEY (tutor_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 6. TUTOR AVAILABILITY
CREATE TABLE tutor_availability (
    availability_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    day ENUM('mon','tue','wed','thu','fri','sat','sun') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (tutor_id) REFERENCES tutor(tutor_id) ON DELETE CASCADE
);

-- 7. TUTOR COMPETENCIES 
CREATE TABLE tutor_competencies (
    competency_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    unit_id INT NOT NULL,
    FOREIGN KEY (tutor_id) REFERENCES tutor(tutor_id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id),
    UNIQUE (tutor_id, unit_id)
);

-- 8. LEARNING REQUESTS 
CREATE TABLE learning_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    learner_id INT NOT NULL,
    unit_id INT NOT NULL,
    description TEXT,
    status ENUM('open','matched','completed','cancelled') DEFAULT 'open',
    matched_tutor_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    preferred_schedule VARCHAR(100) AFTER description,
    urgency VARCHAR(50) AFTER preferred_schedule;
    FOREIGN KEY (learner_id) REFERENCES users(user_id),
    FOREIGN KEY (unit_id) REFERENCES units(unit_id),
    FOREIGN KEY (matched_tutor_id) REFERENCES tutor(tutor_id)
);

-- 9. SESSIONS 
CREATE TABLE sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    tutor_id INT NOT NULL,
    learner_id INT NOT NULL,
    session_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('scheduled','completed','cancelled') DEFAULT 'scheduled',
    FOREIGN KEY (request_id) REFERENCES learning_requests(request_id),
    FOREIGN KEY (tutor_id) REFERENCES tutor(tutor_id),
    FOREIGN KEY (learner_id) REFERENCES users(user_id)
);

-- 10. ATTENDANCE
CREATE TABLE session_attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    user_id INT NOT NULL,
    attended BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (session_id) REFERENCES sessions(session_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 11. FEEDBACK 
CREATE TABLE feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    learner_id INT NOT NULL,
    tutor_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comments TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(session_id),
    FOREIGN KEY (learner_id) REFERENCES users(user_id),
    FOREIGN KEY (tutor_id) REFERENCES users(user_id)
);

-- 12. CERTIFICATES 
CREATE TABLE certificates (
    certificate_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    certificate_type VARCHAR(50),
    issued_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tutor_id) REFERENCES tutor(tutor_id)
);

-- 13. SYSTEM LOGS 
CREATE TABLE system_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100),
    category VARCHAR(50) NULL,
    details TEXT NULL,
    time DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 14. NOTIFICATIONS
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    type VARCHAR(20) DEFAULT 'info',
    related_id INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

--15. TUTOR REQUESTS
CREATE TABLE tutoring_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    unit VARCHAR(255) NOT NULL,
    assigned_tutor VARCHAR(255) DEFAULT NULL,
    status VARCHAR(50) DEFAULT 'Open',
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    form_data TEXT NULL
);

--16.LEARNER PROFILES
CREATE TABLE IF NOT EXISTS learner_profiles (
    profile_id INT AUTO_INCREMENT PRIMARY KEY,
    learner_id INT NOT NULL,
    user_id INT NOT NULL,
    year_of_study VARCHAR(20),
    faculty VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (learner_id) REFERENCES users(user_id),
    UNIQUE KEY unique_learner (user_id)
);

--17. TEST QUESTIONS
CREATE TABLE test_questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    unit_id INT NOT NULL,
    question_text TEXT NOT NULL,
    option_1 VARCHAR(255) NOT NULL,
    option_2 VARCHAR(255) NOT NULL,
    option_3 VARCHAR(255) NOT NULL,
    option_4 VARCHAR(255) NOT NULL,
    correct_option INT NOT NULL CHECK (correct_option BETWEEN 1 AND 4),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id) ON DELETE CASCADE
);
--18. TUTOR TESTS

CREATE TABLE tutor_tests (
    test_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    unit_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    passed BOOLEAN NOT NULL,
    taken_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id) ON DELETE CASCADE
);

-- 19. TUTOR PROFILES
CREATE TABLE IF NOT EXISTS tutor_profiles (
    profile_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,               -- links to users.user_id
    bio TEXT,
    rating DECIMAL(3,2) DEFAULT 0,
    current_students INT DEFAULT 0,
    max_students INT DEFAULT 3,
    status ENUM('active','inactive','suspended') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tutor_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE (tutor_id)
);
CREATE TABLE IF NOT EXISTS tutor_competencies (
    competency_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    unit_id INT NOT NULL,
    FOREIGN KEY (tutor_id) REFERENCES tutor(tutor_id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id) ON DELETE CASCADE,
    UNIQUE (tutor_id, unit_id)
);

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE admin;
TRUNCATE TABLE tutor;
TRUNCATE TABLE tutor_profiles;
TRUNCATE TABLE tutor_availability;
TRUNCATE TABLE tutor_competencies;
TRUNCATE TABLE tutor_applications;
TRUNCATE TABLE tutor_tests;

TRUNCATE TABLE learner_profiles;

TRUNCATE TABLE learning_requests;
TRUNCATE TABLE sessions;
TRUNCATE TABLE session_attendance;
TRUNCATE TABLE feedback;
TRUNCATE TABLE certificates;
TRUNCATE TABLE system_logs;
TRUNCATE TABLE notifications;
TRUNCATE TABLE tutoring_requests;


TRUNCATE TABLE users;

SET FOREIGN_KEY_CHECKS = 1;
