<?php
session_start();
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = 'Events';
include 'components/header.php';

// Get all events for different user types
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'teacher') {
    // Teachers: see all events (past and future)
    $stmt = $conn->prepare("
        SELECT e.*, u.full_name as created_by_name, 
               COUNT(er.id) as registered_count
        FROM events e 
        LEFT JOIN users u ON e.created_by = u.id 
        LEFT JOIN event_registrations er ON e.id = er.event_id AND er.status = 'registered'
        GROUP BY e.id 
        ORDER BY e.event_date DESC, e.event_time DESC
    ");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student') {
    // Students: see upcoming events and recently finished events (past 30 days)
    // Upcoming events
    $stmt = $conn->prepare("
        SELECT e.*, u.full_name as created_by_name, 
               COUNT(er.id) as registered_count
        FROM events e 
        LEFT JOIN users u ON e.created_by = u.id 
        LEFT JOIN event_registrations er ON e.id = er.event_id AND er.status = 'registered'
        WHERE e.event_date >= CURDATE()
        GROUP BY e.id 
        ORDER BY e.event_date ASC, e.event_time ASC
    ");
    $stmt->execute();
    $upcoming_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Recently finished events (past 30 days)
    $stmt = $conn->prepare("
        SELECT e.*, u.full_name as created_by_name, 
               COUNT(er.id) as registered_count
        FROM events e 
        LEFT JOIN users u ON e.created_by = u.id 
        LEFT JOIN event_registrations er ON e.id = er.event_id AND er.status = 'registered'
        WHERE e.event_date < CURDATE() AND e.event_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY e.id 
        ORDER BY e.event_date DESC, e.event_time DESC
    ");
    $stmt->execute();
    $recent_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Guests: see only upcoming events
    $stmt = $conn->prepare("
        SELECT e.*, u.full_name as created_by_name, 
               COUNT(er.id) as registered_count
        FROM events e 
        LEFT JOIN users u ON e.created_by = u.id 
        LEFT JOIN event_registrations er ON e.id = er.event_id AND er.status = 'registered'
        WHERE e.event_date >= CURDATE()
        GROUP BY e.id 
        ORDER BY e.event_date ASC, e.event_time ASC
    ");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get user's registered events if logged in
$user_registrations = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("
        SELECT event_id, status 
        FROM event_registrations 
        WHERE user_id = ? AND status IN ('registered', 'attended')
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user_registrations = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-calendar-alt"></i> University Events</h1>
        <p>Discover exciting events, workshops, and activities happening on campus.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'teacher'): ?>
            <!-- Teacher Events Management -->
            <div class="events-header">
                <h2 class="section-title">Manage Events</h2>
                <a href="create_event.php" class="btn"><i class="fas fa-plus"></i> Create New Event</a>
            </div>
            
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <div class="event-header">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <div class="event-actions">
                                <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-small">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="event_registrations.php?id=<?php echo $event['id']; ?>" class="btn btn-small btn-secondary">
                                    <i class="fas fa-users"></i> Registrations (<?php echo $event['registered_count']; ?>)
                                </a>
                                <form method="POST" action="delete_event.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                    <button type="submit" class="btn btn-small btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                </form>
                            </div>
                        </div>
                        <div class="event-details">
                            <p><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                            <p><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($event['event_time'])); ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                            <p><i class="fas fa-user"></i> Created by: <?php echo htmlspecialchars($event['created_by_name']); ?></p>
                        </div>
                        <div class="event-description">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </div>
                        <div class="event-stats">
                            <span class="stat-badge">
                                <i class="fas fa-users"></i> <?php echo $event['registered_count']; ?> registered
                            </span>
                            <?php if ($event['max_participants']): ?>
                                <span class="stat-badge">
                                    <i class="fas fa-user-plus"></i> <?php echo $event['max_participants']; ?> max
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($events)): ?>
                    <div class="no-events">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No events found</h3>
                        <p>Create the first event to get started!</p>
                        <a href="create_event.php" class="btn">Create Event</a>
                    </div>
                <?php endif; ?>
            </div>
            
        <?php elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student'): ?>
            <!-- Student Events View -->
            <div class="events-header">
                <h2 class="section-title">Upcoming Events</h2>
                <p>Browse and register for exciting university events and activities.</p>
            </div>
            <div class="events-grid">
                <?php foreach ($upcoming_events as $event): ?>
                    <div class="event-card <?php echo isset($user_registrations[$event['id']]) ? 'registered' : ''; ?>">
                        <div class="event-header">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <?php if (isset($user_registrations[$event['id']])): ?>
                                <span class="registered-badge">
                                    <i class="fas fa-check"></i> Registered
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="event-details">
                            <p><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                            <p><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($event['event_time'])); ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                        </div>
                        <div class="event-description">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </div>
                        <div class="event-stats">
                            <span class="stat-badge">
                                <i class="fas fa-users"></i> <?php echo $event['registered_count']; ?> registered
                            </span>
                            <?php if ($event['max_participants']): ?>
                                <span class="stat-badge">
                                    <i class="fas fa-user-plus"></i> <?php echo $event['max_participants']; ?> max
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="event-actions">
                            <?php if (!isset($user_registrations[$event['id']])): ?>
                                <?php if (!$event['max_participants'] || $event['registered_count'] < $event['max_participants']): ?>
                                    <form method="POST" action="register_event.php" style="display: inline;">
                                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                        <button type="submit" class="btn">
                                            <i class="fas fa-user-plus"></i> Register
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="btn btn-disabled">
                                        <i class="fas fa-user-times"></i> Full
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <form method="POST" action="cancel_registration.php" style="display: inline;">
                                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-user-minus"></i> Cancel Registration
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($upcoming_events)): ?>
                    <div class="no-events">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No upcoming events</h3>
                        <p>Check back later for new events!</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="events-header" style="margin-top: 2em;">
                <h2 class="section-title">Recently Finished Events</h2>
                <p>See what has recently happened on campus.</p>
            </div>
            <div class="events-grid">
                <?php foreach ($recent_events as $event): ?>
                    <div class="event-card">
                        <div class="event-header">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                        </div>
                        <div class="event-details">
                            <p><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                            <p><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($event['event_time'])); ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                        </div>
                        <div class="event-description">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </div>
                        <div class="event-stats">
                            <span class="stat-badge">
                                <i class="fas fa-users"></i> <?php echo $event['registered_count']; ?> registered
                            </span>
                            <?php if ($event['max_participants']): ?>
                                <span class="stat-badge">
                                    <i class="fas fa-user-plus"></i> <?php echo $event['max_participants']; ?> max
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($recent_events)): ?>
                    <div class="no-events">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No recently finished events</h3>
                        <p>Check back later for more events!</p>
                    </div>
                <?php endif; ?>
            </div>
            
        <?php else: ?>
            <!-- Guest Events View -->
            <div class="events-header">
                <h2 class="section-title">Upcoming Events</h2>
                <p>Discover exciting events, workshops, and activities happening on campus.</p>
            </div>
            
            <div class="guest-notice">
                <i class="fas fa-info-circle"></i>
                <p>Please <a href="login.php">login</a> or <a href="register.php">register</a> to register for events and access additional features.</p>
            </div>
            
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <div class="event-header">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                        </div>
                        <div class="event-details">
                            <p><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                            <p><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($event['event_time'])); ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                        </div>
                        <div class="event-description">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </div>
                        <div class="event-stats">
                            <span class="stat-badge">
                                <i class="fas fa-users"></i> <?php echo $event['registered_count']; ?> registered
                            </span>
                            <?php if ($event['max_participants']): ?>
                                <span class="stat-badge">
                                    <i class="fas fa-user-plus"></i> <?php echo $event['max_participants']; ?> max
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="event-actions">
                            <a href="login.php" class="btn">
                                <i class="fas fa-sign-in-alt"></i> Login to Register
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($events)): ?>
                    <div class="no-events">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No upcoming events</h3>
                        <p>Check back later for new events!</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'components/footer.php'; ?> 