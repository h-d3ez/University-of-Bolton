<?php
session_start();

// Redirect authenticated users to their respective dashboards
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] === 'teacher') {
        header('Location: teacher_dashboard.php');
    } else {
        header('Location: student_dashboard.php');
    }
    exit();
}

$page_title = 'Welcome to University of Bolton';
include 'components/header.php';
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-university"></i> Welcome to University of Bolton</h1>
        <p>Discover excellence in education. Join our community of learners and educators.</p>
        <div class="hero-buttons">
            <a href="register.php" class="btn btn-primary">Join Us Today</a>
            <a href="login.php" class="btn btn-secondary">Sign In</a>
            <div class="faculty-link-wrapper" style="margin-top: 2rem; display: flex; justify-content: center;">
                <a href="faculty.php" class="btn btn-accent faculty-btn">
                    <i class="fas fa-chalkboard-teacher"></i> View Faculty
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Why Choose University of Bolton?</h2>
        <div class="grid grid-3">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3>Academic Excellence</h3>
                <p>World-class education with experienced faculty and cutting-edge curriculum designed for success.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Vibrant Community</h3>
                <p>Join a diverse community of students and faculty from around the world.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3>Career Success</h3>
                <p>95% of our graduates find employment within 6 months of graduation.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background-color: #111;">
    <div class="container">
        <h2 class="section-title">Get Started Today</h2>
        <div class="grid grid-2">
            <div class="cta-card">
                <div class="cta-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>For Students</h3>
                <p>Access your courses, track progress, communicate with teachers, and stay motivated with our daily motivation tracker.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Course Management</li>
                    <li><i class="fas fa-check"></i> Grade Tracking</li>
                    <li><i class="fas fa-check"></i> Teacher Communication</li>
                    <li><i class="fas fa-check"></i> Daily Motivation</li>
                </ul>
                <a href="register.php?type=student" class="btn">Register as Student</a>
            </div>

            <div class="cta-card">
                <div class="cta-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h3>For Teachers</h3>
                <p>Manage your classes, communicate with students, create assignments, and track student progress efficiently.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Class Management</li>
                    <li><i class="fas fa-check"></i> Student Communication</li>
                    <li><i class="fas fa-check"></i> Assignment Creation</li>
                    <li><i class="fas fa-check"></i> Grade Management</li>
                </ul>
                <a href="register.php?type=teacher" class="btn">Register as Teacher</a>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Campus Highlights</h2>
        <div class="grid grid-3">
            <div class="highlight-card">
                <img src="Images/library.png" alt="Modern Library">
                <div class="highlight-content">
                    <h3>State-of-the-Art Library</h3>
                    <p>Access thousands of resources in our modern library facility.</p>
                </div>
            </div>

            <div class="highlight-card">
                <img src="Images/campus.png" alt="Beautiful Campus">
                <div class="highlight-content">
                    <h3>Beautiful Campus</h3>
                    <p>Study in a inspiring environment with modern facilities.</p>
                </div>
            </div>

            <div class="highlight-card">
                <img src="Images/cafteria.png" alt="Modern Cafeteria">
                <div class="highlight-content">
                    <h3>Modern Cafeteria</h3>
                    <p>Enjoy healthy meals and socialize with fellow students.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background-color: #111;">
    <div class="container">
        <div class="cta-section">
            <h2>Ready to Begin Your Journey?</h2>
            <p>Join thousands of students and faculty who have chosen University of Bolton for their academic success.</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-primary">Get Started</a>
                <a href="explore.php" class="btn btn-secondary">Learn More</a>
            </div>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>