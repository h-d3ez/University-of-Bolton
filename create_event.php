<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = trim($_POST['location']);
    $max_participants = !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : null;
    
    $errors = [];
    
    // Validation
    if (empty($title)) {
        $errors[] = "Event title is required.";
    }
    
    if (empty($description)) {
        $errors[] = "Event description is required.";
    }
    
    if (empty($event_date)) {
        $errors[] = "Event date is required.";
    } elseif (strtotime($event_date) < strtotime('today')) {
        $errors[] = "Event date cannot be in the past.";
    }
    
    if (empty($event_time)) {
        $errors[] = "Event time is required.";
    }
    
    if (empty($location)) {
        $errors[] = "Event location is required.";
    }
    
    if ($max_participants !== null && $max_participants <= 0) {
        $errors[] = "Maximum participants must be a positive number.";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO events (title, description, event_date, event_time, location, max_participants, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $description, $event_date, $event_time, $location, $max_participants, $_SESSION['user_id']]);
            
            $_SESSION['success'] = "Event created successfully!";
            header('Location: events.php');
            exit();
            
        } catch (PDOException $e) {
            $errors[] = "An error occurred while creating the event.";
        }
    }
}

$page_title = 'Create Event';
include 'components/header.php';
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-plus"></i> Create New Event</h1>
        <p>Schedule a new event for students to register and attend.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="form-container">
            <h2 class="section-title">Event Details</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="event-form">
                <div class="form-group">
                    <label for="title">Event Title *</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="description">Event Description *</label>
                    <textarea id="description" name="description" class="form-control" rows="5" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="event_date">Event Date *</label>
                        <input type="date" id="event_date" name="event_date" class="form-control" 
                               value="<?php echo isset($_POST['event_date']) ? htmlspecialchars($_POST['event_date']) : ''; ?>" 
                               min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="event_time">Event Time *</label>
                        <input type="time" id="event_time" name="event_time" class="form-control" 
                               value="<?php echo isset($_POST['event_time']) ? htmlspecialchars($_POST['event_time']) : ''; ?>" 
                               required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="location">Event Location *</label>
                    <input type="text" id="location" name="location" class="form-control" 
                           value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>" 
                           placeholder="e.g., Main Hall, Room 101, Library" required>
                </div>
                
                <div class="form-group">
                    <label for="max_participants">Maximum Participants (Optional)</label>
                    <input type="number" id="max_participants" name="max_participants" class="form-control" 
                           value="<?php echo isset($_POST['max_participants']) ? htmlspecialchars($_POST['max_participants']) : ''; ?>" 
                           min="1" placeholder="Leave empty for unlimited">
                    <small>Leave empty to allow unlimited registrations</small>
                </div>
                
                <div class="form-actions">
                    <a href="events.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Create Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?> 