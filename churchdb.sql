CREATE DATABASE church_attendance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE church_attendance;

-- 1. Churches
CREATE TABLE churches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(255),
    foundation_date DATE,
    image VARCHAR(255),
    description TEXT ,
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20)
);

-- 2. Roles (Functions like Usher, Pastor, Choir...)
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- 3. Users (Admins of the system)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Members (with added address and status fields)
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    birth_date DATE,
    phone VARCHAR(20),
    email VARCHAR(100),
    photo VARCHAR(255),
    address_line1 VARCHAR(255),
    address_line2 VARCHAR(255),
    city VARCHAR(100),
    status ENUM('active', 'inactive', 'transferred', 'deceased') DEFAULT 'active',
    role_id INT,
    church_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL,
    FOREIGN KEY (church_id) REFERENCES churches(id) ON DELETE CASCADE
);
 
-- 5. Weekly Schedules (recurring events)
CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    church_id INT NOT NULL,
    day_of_week ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
    event_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    start_time TIME,
    end_time TIME,
    FOREIGN KEY (church_id) REFERENCES churches(id) ON DELETE CASCADE
);

-- 6. One-time Events
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME,
    location VARCHAR(100),
    church_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (church_id) REFERENCES churches(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 7. Attendance Records
CREATE TABLE attendances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    event_id INT,
    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent', 'excused') NOT NULL DEFAULT 'present',
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    UNIQUE (member_id, attendance_date, event_id)
);

-- Members indexes
CREATE INDEX idx_member_name ON members(first_name, last_name);
CREATE INDEX idx_member_status ON members(status);

-- Events indexes
CREATE INDEX idx_event_date ON events(event_date);

-- Attendance indexes
CREATE INDEX idx_attendance_date ON attendances(attendance_date);