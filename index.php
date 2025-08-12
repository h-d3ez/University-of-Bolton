<?php
session_start();
require_once 'config/database.php';

// Initialize database
$database = new Database();
$database->getConnection();

// Redirect authenticated users to their dashboards
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] === 'teacher') {
        header('Location: teacher_dashboard.php');
    } else {
        header('Location: student_dashboard.php');
    }
    exit();
} else {
    // Redirect guests to guest dashboard
    header('Location: guest_dashboard.php');
    exit();
}
?>

<section class="hero">
    <div class="hero-content">
        <h1>Welcome to University of Bolton</h1>
        <p>Discover excellence in education with our world-class programs, innovative research, and vibrant campus community. Your journey to success starts here.</p>
        <div style="margin-top: 2rem;">
            <a href="explore.php" class="btn">Explore Programs</a>
            <a href="about.php" class="btn btn-secondary" style="margin-left: 1rem;">Learn More</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Why Choose Bolton?</h2>
        <div class="grid grid-3">
            <div class="card">
                <img src="Images/campus.png" alt="Modern Campus">
                <h3><i class="fas fa-university"></i> Modern Campus</h3>
                <p>State-of-the-art facilities with cutting-edge technology and comfortable learning environments designed for student success.</p>
            </div>
            <div class="card">
                <img src="Images/library.png" alt="Academic Excellence">
                <h3><i class="fas fa-graduation-cap"></i> Academic Excellence</h3>
                <p>Renowned faculty and innovative programs that prepare students for successful careers in their chosen fields.</p>
            </div>
            <div class="card">
                <img src="Images/garden.png" alt="Student Life">
                <h3><i class="fas fa-users"></i> Vibrant Student Life</h3>
                <p>Rich campus life with numerous clubs, activities, and opportunities for personal and professional growth.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background-color: #111;">
    <div class="container">
        <h2 class="section-title">Campus Highlights</h2>
        <div class="grid grid-2">
            <div class="card">
                <img src="Images/cafteria.png" alt="Cafeteria">
                <h3><i class="fas fa-utensils"></i> Modern Cafeteria</h3>
                <p>Enjoy delicious and nutritious meals in our spacious cafeteria with diverse dining options to suit all tastes and dietary requirements.</p>
            </div>
            <div class="card">
                <img src="Images/campus2.png" alt="Campus Grounds">
                <h3><i class="fas fa-tree"></i> Beautiful Grounds</h3>
                <p>Study and relax in our beautifully landscaped campus grounds, providing a peaceful environment for learning and recreation.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>