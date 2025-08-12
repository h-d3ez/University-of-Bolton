<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$page_title = 'Login Successful';
include 'components/header.php';
?>

<section class="section">
    <div class="container">
        <div class="form-container" style="text-align: center;">
            <div style="font-size: 4rem; color: #ffd700; margin-bottom: 1rem;">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 style="color: #ffd700; margin-bottom: 1rem;">Welcome Back!</h2>
            <p style="margin-bottom: 2rem;">Hello <?php echo htmlspecialchars($_SESSION['full_name']); ?>! You have successfully logged in to your account.</p>
            
            <?php if ($_SESSION['user_type'] === 'student'): ?>
                <p style="margin-bottom: 2rem;">As a student, you now have access to motivation tracking, messaging with teachers, and all course materials.</p>
                <a href="motivation.php" class="btn">Track Motivation</a>
                <a href="messages.php" class="btn btn-secondary" style="margin-left: 1rem;">View Messages</a>
            <?php else: ?>
                <p style="margin-bottom: 2rem;">As a teacher, you can now communicate with students and access all teaching resources.</p>
                <a href="messages.php" class="btn">View Messages</a>
            <?php endif; ?>
            
            <div style="margin-top: 2rem;">
                <a href="index.php" class="btn btn-secondary">Go to Home</a>
            </div>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>