CREATE DATABASE IF NOT EXISTS unitrack_db;
USE unitrack_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS student_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    deadline DATE DEFAULT NULL,
    priority ENUM('High', 'Medium', 'Low') DEFAULT 'Medium',
    is_done TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS lectures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    day_name ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lectures_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS gpa_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject_name VARCHAR(200) NOT NULL,
    credits TINYINT UNSIGNED NOT NULL,
    grade_point DECIMAL(2,1) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_gpa_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Migration helpers for existing databases.
-- Re-run this file after feature updates so older schemas are upgraded.
ALTER TABLE lectures
    ADD COLUMN IF NOT EXISTS start_time TIME NOT NULL DEFAULT '00:00:00';

ALTER TABLE lectures
    ADD COLUMN IF NOT EXISTS end_time TIME NOT NULL DEFAULT '00:00:00';

ALTER TABLE gpa_subjects
    ADD COLUMN IF NOT EXISTS subject_name VARCHAR(200) NOT NULL DEFAULT '';

ALTER TABLE gpa_subjects
    ADD COLUMN IF NOT EXISTS credits TINYINT UNSIGNED NOT NULL DEFAULT 1;

ALTER TABLE gpa_subjects
    ADD COLUMN IF NOT EXISTS grade_point DECIMAL(2,1) NOT NULL DEFAULT 0.0;
