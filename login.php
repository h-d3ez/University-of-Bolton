<?php
session_start();
require_once 'config/database.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    try {
        $stmt = $conn->prepare("SELECT id, username, password, user_type, full_name FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Redirect to appropriate dashboard based on user type
            if ($user['user_type'] === 'teacher') {
                header('Location: teacher_dashboard.php');
            } else {
                header('Location: student_dashboard.php');
            }
            exit();
        } else {
            $message = 'Invalid username or password!';
            $message_type = 'error';
        }
    } catch (PDOException $e) {
        $message = 'Login failed. Please try again.';
        $message_type = 'error';
    }
}

$page_title = 'Login';
include 'components/header.php';
?>

<section class="section">
    <div class="container">
        <div class="form-container">
            <h2 style="text-align: center; color: #ffd700; margin-bottom: 2rem;"><i class="fas fa-sign-in-alt"></i> Login</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm" onsubmit="return validateForm('loginForm')">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username or Email</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">Login</button>
            </form>
            
            <p style="text-align: center; margin-top: 1rem;">
                Don't have an account? <a href="register.php" style="color: #ffd700;">Register here</a>
            </p>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>