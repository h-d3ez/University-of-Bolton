<?php
session_start();
require_once 'config/database.php';

// Only teachers can delete events
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = (int)$_POST['event_id'];
    $user_id = $_SESSION['user_id'];
    $database = new Database();
    $conn = $database->getConnection();

    // Check if the event exists and is created by this teacher
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND created_by = ?");
    $stmt->execute([$event_id, $user_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        $_SESSION['error'] = "Event not found or you don't have permission to delete it.";
        header('Location: events.php');
        exit();
    }

    // Delete the event
    try {
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND created_by = ?");
        $stmt->execute([$event_id, $user_id]);
        $_SESSION['success'] = "Event deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "An error occurred while deleting the event.";
    }
    header('Location: events.php');
    exit();
} else {
    header('Location: events.php');
    exit();
}