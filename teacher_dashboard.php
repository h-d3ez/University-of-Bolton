<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

$page_title = 'Teacher Dashboard';
include 'components/header.php';
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-chalkboard-teacher"></i> Teacher Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! Manage your classes and communicate with students.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid grid-3">
            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>My Students</h3>
                <p>View and manage your student roster, track attendance, and monitor progress.</p>
                <a href="#" class="btn">View Students</a>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Messages</h3>
                <p>Communicate with students, send announcements, and respond to inquiries.</p>
                <a href="messages.php" class="btn">View Messages</a>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3>Assignments</h3>
                <p>Create, manage, and grade assignments for your courses.</p>
                <a href="#" class="btn">Manage Assignments</a>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3>Grade Book</h3>
                <p>Record grades, generate reports, and track student performance.</p>
                <a href="#" class="btn">Open Grade Book</a>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Schedule</h3>
                <p>View your teaching schedule, office hours, and upcoming events.</p>
                <a href="#" class="btn">View Schedule</a>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Guests Queries</h3>
                <p>Guests reaching out.</p>
                <a href="teacher_messages.php" class="btn">View Contact Inquiries</a>
            </div>          
            
            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Events</h3>
                <p>Create and manage events, monitor registrations, and track attendance.</p>
                <a href="events.php" class="btn">Manage Events</a>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-book"></i>
                </div>
                <h3>Course Materials</h3>
                <p>Upload and manage course materials, syllabi, and resources.</p>
                <a href="#" class="btn">Manage Materials</a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background-color: #111;">
    <div class="container">
        <h2 class="section-title">Quick Actions</h2>
        <div class="grid grid-2">
            <div class="quick-action-card">
                <h3><i class="fas fa-bullhorn"></i> Send Announcement</h3>
                <p>Quickly send an announcement to all your students.</p>
                <form class="quick-form">
                    <textarea placeholder="Type your announcement here..." rows="3"></textarea>
                    <button type="submit" class="btn">Send Announcement</button>
                </form>
            </div>

            <div class="quick-action-card">
                <h3><i class="fas fa-plus"></i> Create Assignment</h3>
                <p>Create a new assignment for your students.</p>
                <form class="quick-form">
                    <input type="text" placeholder="Assignment title">
                    <input type="date" placeholder="Due date">
                    <button type="submit" class="btn">Create Assignment</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>