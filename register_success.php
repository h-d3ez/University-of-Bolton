<?php
$page_title = 'Registration Successful';
include 'components/header.php';
?>

<section class="section">
    <div class="container">
        <div class="form-container" style="text-align: center;">
            <div style="font-size: 4rem; color: #ffd700; margin-bottom: 1rem;">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 style="color: #ffd700; margin-bottom: 1rem;">Registration Successful!</h2>
            <p style="margin-bottom: 2rem;">Thank you for registering with University of Bolton. Your account has been created successfully.</p>
            <p style="margin-bottom: 2rem;">You can now log in to access your dashboard and explore all the features available to you.</p>
            <a href="login.php" class="btn">Login Now</a>
            <a href="index.php" class="btn btn-secondary" style="margin-left: 1rem;">Go to Home</a>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>