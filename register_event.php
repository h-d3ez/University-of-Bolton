<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $database = new Database();
    $conn = $database->getConnection();
    
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];
    
    try {
        // Check if event exists and is not full
        $stmt = $conn->prepare("
            SELECT e.*, COUNT(er.id) as registered_count
            FROM events e 
            LEFT JOIN event_registrations er ON e.id = er.event_id AND er.status = 'registered'
            WHERE e.id = ? AND e.event_date >= CURDATE()
            GROUP BY e.id
        ");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$event) {
            $_SESSION['error'] = "Event not found or has already passed.";
            header('Location: events.php');
            exit();
        }
        
        // Check if event is full
        if ($event['max_participants'] && $event['registered_count'] >= $event['max_participants']) {
            $_SESSION['error'] = "This event is full.";
            header('Location: events.php');
            exit();
        }
        
        // Check if user is already registered
        $stmt = $conn->prepare("
            SELECT id FROM event_registrations 
            WHERE event_id = ? AND user_id = ?
        ");
        $stmt->execute([$event_id, $user_id]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = "You are already registered for this event.";
            header('Location: events.php');
            exit();
        }
        
        // Register for the event
        $stmt = $conn->prepare("
            INSERT INTO event_registrations (event_id, user_id, status) 
            VALUES (?, ?, 'registered')
        ");
        $stmt->execute([$event_id, $user_id]);
        
        $_SESSION['success'] = "Successfully registered for the event!";
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "An error occurred while registering for the event.";
    }
    
    header('Location: events.php');
    exit();
} else {
    header('Location: events.php');
    exit();
}
?> 