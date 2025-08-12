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

// Get event ID from URL
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$event_id) {
    header('Location: events.php');
    exit();
}

// Get event details
$stmt = $conn->prepare("
    SELECT * FROM events 
    WHERE id = ?
");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    $_SESSION['error'] = "Event not found.";
    header('Location: events.php');
    exit();
}

// Handle attendance marking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registration_id']) && isset($_POST['action'])) {
    $registration_id = (int)$_POST['registration_id'];
    $action = $_POST['action'];
    
    if ($action === 'mark_attended' || $action === 'mark_cancelled') {
        $status = $action === 'mark_attended' ? 'attended' : 'cancelled';
        
        try {
            $stmt = $conn->prepare("
                UPDATE event_registrations 
                SET status = ? 
                WHERE id = ? AND event_id = ?
            ");
            $stmt->execute([$status, $registration_id, $event_id]);
            
            $_SESSION['success'] = "Registration status updated successfully!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "An error occurred while updating the registration.";
        }
        
        header('Location: event_registrations.php?id=' . $event_id);
        exit();
    }
}

// Get registrations for this event
$stmt = $conn->prepare("
    SELECT er.*, u.full_name, u.email, u.user_type
    FROM event_registrations er
    JOIN users u ON er.user_id = u.id
    WHERE er.event_id = ?
    ORDER BY er.registration_date ASC
");
$stmt->execute([$event_id]);
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count registrations by status
$registered_count = 0;
$attended_count = 0;
$cancelled_count = 0;

foreach ($registrations as $reg) {
    switch ($reg['status']) {
        case 'registered':
            $registered_count++;
            break;
        case 'attended':
            $attended_count++;
            break;
        case 'cancelled':
            $cancelled_count++;
            break;
    }
}

$page_title = 'Event Registrations';
include 'components/header.php';
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-users"></i> Event Registrations</h1>
        <p>Manage registrations for: <?php echo htmlspecialchars($event['title']); ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="event-summary">
            <h2 class="section-title">Event Summary</h2>
            <div class="event-details">
                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($event['event_time'])); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                <p><strong>Max Participants:</strong> <?php echo $event['max_participants'] ? $event['max_participants'] : 'Unlimited'; ?></p>
            </div>
            
            <div class="registration-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $registered_count; ?></div>
                    <div class="stat-label">Registered</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $attended_count; ?></div>
                    <div class="stat-label">Attended</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $cancelled_count; ?></div>
                    <div class="stat-label">Cancelled</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($registrations); ?></div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
        </div>
        
        <div class="registrations-list">
            <h3>Registration Details</h3>
            
            <?php if (empty($registrations)): ?>
                <div class="no-registrations">
                    <i class="fas fa-users-slash"></i>
                    <h4>No registrations yet</h4>
                    <p>Students will appear here once they register for this event.</p>
                </div>
            <?php else: ?>
                <div class="registrations-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $reg): ?>
                                <tr class="registration-row status-<?php echo $reg['status']; ?>">
                                    <td><strong><?php echo htmlspecialchars($reg['full_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($reg['email']); ?></td>
                                    <td>
                                        <span class="role-badge role-<?php echo $reg['user_type']; ?>">
                                            <?php echo ucfirst($reg['user_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($reg['registration_date'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $reg['status']; ?>">
                                            <?php echo ucfirst($reg['status']); ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <?php if ($reg['status'] === 'registered'): ?>
                                            <div class="button-group">
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="registration_id" value="<?php echo $reg['id']; ?>">
                                                    <input type="hidden" name="action" value="mark_attended">
                                                    <button type="submit" class="btn btn-small btn-success">
                                                        <i class="fas fa-check"></i> Mark Attended
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="registration_id" value="<?php echo $reg['id']; ?>">
                                                    <input type="hidden" name="action" value="mark_cancelled">
                                                    <button type="submit" class="btn btn-small btn-secondary">
                                                        <i class="fas fa-times"></i> Mark Cancelled
                                                    </button>
                                                </form>
                                            </div>
                                        <?php elseif ($reg['status'] === 'attended'): ?>
                                            <span class="btn btn-small btn-success disabled">
                                                <i class="fas fa-check"></i> Attended
                                            </span>
                                        <?php elseif ($reg['status'] === 'cancelled'): ?>
                                            <span class="btn btn-small btn-secondary disabled">
                                                <i class="fas fa-times"></i> Cancelled
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-actions">
            <a href="events.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Events
            </a>
            <a href="edit_event.php?id=<?php echo $event_id; ?>" class="btn">
                <i class="fas fa-edit"></i> Edit Event
            </a>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?> 