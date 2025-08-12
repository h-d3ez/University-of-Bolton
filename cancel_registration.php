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
        // Check if user is registered for this event
        $stmt = $conn->prepare("
            SELECT er.*, e.event_date 
            FROM event_registrations er
            JOIN events e ON er.event_id = e.id
            WHERE er.event_id = ? AND er.user_id = ? AND er.status = 'registered'
        ");
        $stmt->execute([$event_id, $user_id]);
        $registration = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$registration) {
            $_SESSION['error'] = "You are not registered for this event.";
            header('Location: events.php');
            exit();
        }
        
        // Check if event has already passed
        if (strtotime($registration['event_date']) < strtotime('today')) {
            $_SESSION['error'] = "Cannot cancel registration for an event that has already passed.";
            header('Location: events.php');
            exit();
        }
        
        // Cancel the registration
        $stmt = $conn->prepare("
            UPDATE event_registrations 
            SET status = 'cancelled' 
            WHERE event_id = ? AND user_id = ?
        ");
        $stmt->execute([$event_id, $user_id]);
        
        $_SESSION['success'] = "Registration cancelled successfully.";
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "An error occurred while cancelling the registration.";
    }
    
    header('Location: events.php');
    exit();
} else {
    header('Location: events.php');
    exit();
}
?> 