<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header('Location: login.php');
    exit();
}

$page_title = 'Student Dashboard';
include 'components/header.php';
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-graduation-cap"></i> Student Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! Track your progress and stay motivated.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="faculty-link-wrapper" style="margin-bottom: 2rem;">
            <a href="faculty.php" class="btn btn-accent faculty-btn">
                <i class="fas fa-chalkboard-teacher"></i> View Faculty
            </a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid grid-3">
            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Daily Motivation</h3>
                <p>Track your daily motivation levels and get inspired with motivational quotes.</p>
                <a href="motivation.php" class="btn">View Motivation</a>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Messages</h3>
                <p>Communicate with your teachers and get help when you need it.</p>
                <a href="messages.php" class="btn">View Messages</a>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3>Assignments</h3>
                <p>View your assignments, due dates, and submit your work.</p>
                <a href="#" class="btn">View Assignments</a>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3>Grades</h3>
                <p>Check your grades and track your academic progress.</p>
                <a href="#" class="btn">View Grades</a>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Schedule</h3>
                <p>View your class schedule, exam dates, and important events.</p>
                <a href="#" class="btn">View Schedule</a>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Events</h3>
                <p>Browse and register for upcoming university events and activities.</p>
                <a href="events.php" class="btn">View Events</a>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3>Course Materials</h3>
                <p>Access course materials, lecture notes, and study resources.</p>
                <a href="#" class="btn">Access Materials</a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background-color: #111;">
    <div class="container">
        <h2 class="section-title">Quick Stats</h2>
        <div class="grid grid-4">
            <div class="stat-card">
                <div class="stat-number">4.2</div>
                <div class="stat-label">GPA</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">12</div>
                <div class="stat-label">Credits</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">3</div>
                <div class="stat-label">Pending Assignments</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">85%</div>
                <div class="stat-label">Attendance</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Recent Activity</h2>
        <div class="activity-feed">
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="activity-content">
                    <h4>New Assignment Posted</h4>
                    <p>Mathematics - Calculus Problem Set 5</p>
                    <span class="activity-time">2 hours ago</span>
                </div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="activity-content">
                    <h4>Assignment Submitted</h4>
                    <p>Computer Science - Database Design Project</p>
                    <span class="activity-time">1 day ago</span>
                </div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="activity-content">
                    <h4>Grade Received</h4>
                    <p>English Literature - Essay Analysis (A-)</p>
                    <span class="activity-time">2 days ago</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>