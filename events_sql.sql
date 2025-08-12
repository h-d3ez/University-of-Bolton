-- Events System Database Schema
-- Run this SQL to create the necessary tables for the events functionality

-- Create events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    max_participants INT DEFAULT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Create event_registrations table
CREATE TABLE IF NOT EXISTS event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('registered', 'attended', 'cancelled') DEFAULT 'registered',
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_event_user (event_id, user_id)
);

-- Sample data for testing (optional)
-- Insert sample events (replace created_by with actual user IDs from your users table)

-- Sample Event 1
INSERT INTO events (title, description, event_date, event_time, location, max_participants, created_by) 
VALUES (
    'Campus Career Fair',
    'Join us for our annual career fair featuring top companies from various industries. Network with recruiters, learn about job opportunities, and get your resume reviewed by professionals.',
    '2024-03-15',
    '10:00:00',
    'Main Auditorium',
    200,
    1
);

-- Sample Event 2
INSERT INTO events (title, description, event_date, event_time, location, max_participants, created_by) 
VALUES (
    'Student Leadership Workshop',
    'Develop your leadership skills through interactive workshops, team-building exercises, and guest speaker presentations. Perfect for students interested in student government or leadership roles.',
    '2024-03-20',
    '14:00:00',
    'Student Center Room 101',
    50,
    1
);

-- Sample Event 3
INSERT INTO events (title, description, event_date, event_time, location, max_participants, created_by) 
VALUES (
    'Academic Excellence Seminar',
    'Learn effective study strategies, time management techniques, and academic success tips from experienced faculty members and top-performing students.',
    '2024-03-25',
    '16:00:00',
    'Library Conference Room',
    NULL,
    1
);

-- Sample registrations (replace user_id with actual student user IDs)
-- INSERT INTO event_registrations (event_id, user_id, status) VALUES (1, 2, 'registered');
-- INSERT INTO event_registrations (event_id, user_id, status) VALUES (1, 3, 'registered');
-- INSERT INTO event_registrations (event_id, user_id, status) VALUES (2, 2, 'registered');
-- INSERT INTO event_registrations (event_id, user_id, status) VALUES (3, 3, 'attended'); 