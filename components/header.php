<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>University of Bolton</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <img src="Images/bolton_logo.png" alt="University of Bolton" class="logo">
                    <span class="logo-text">University of Bolton</span>
                </div>
                <div class="nav-menu" id="nav-menu">
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'teacher'): ?>
                        <!-- Teacher Navigation -->
                        <a href="teacher_dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a href="my_students.php" class="nav-link"><i class="fas fa-users"></i> My Students</a>
                        <a href="messages.php" class="nav-link"><i class="fas fa-comments"></i> Messages</a>
                        <a href="events.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Events</a>
                        <a href="#" class="nav-link"><i class="fas fa-clipboard-list"></i> Assignments</a>
                        <a href="#" class="nav-link"><i class="fas fa-chart-bar"></i> Grades</a>
                        <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        <span class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <?php elseif (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'student'): ?>
                        <!-- Student Navigation -->
                        <a href="student_dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a href="motivation.php" class="nav-link"><i class="fas fa-chart-line"></i> Motivation</a>
                        <a href="messages.php" class="nav-link"><i class="fas fa-comments"></i> Messages</a>
                        <a href="events.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Events</a>
                        <a href="#" class="nav-link"><i class="fas fa-tasks"></i> Assignments</a>
                        <a href="#" class="nav-link"><i class="fas fa-star"></i> Grades</a>
                        <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        <span class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <?php else: ?>
                        <!-- Guest Navigation -->
                        <a href="guest_dashboard.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
                        <a href="about.php" class="nav-link"><i class="fas fa-info-circle"></i> About</a>
                        <a href="explore.php" class="nav-link"><i class="fas fa-compass"></i> Explore</a>
                        <a href="events.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Events</a>
                        <a href="news.php" class="nav-link"><i class="fas fa-newspaper"></i> News</a>
                        <a href="contact.php" class="nav-link"><i class="fas fa-envelope"></i> Contact</a>
                        <a href="login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="register.php" class="nav-link"><i class="fas fa-user-plus"></i> Register</a>
                    <?php endif; ?>
                </div>
                <div class="hamburger" id="hamburger">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </div>
        </nav>
    </header>
    <main class="main-content">