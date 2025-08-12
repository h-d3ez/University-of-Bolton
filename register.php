<?php
require_once 'config/database.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_type = $_POST['user_type'];
    $full_name = trim($_POST['full_name']);
    
    if ($password !== $confirm_password) {
        $message = 'Passwords do not match!';
        $message_type = 'error';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, user_type, full_name) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password, $user_type, $full_name]);
            
            header('Location: register_success.php');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = 'Username or email already exists!';
            } else {
                $message = 'Registration failed. Please try again.';
            }
            $message_type = 'error';
        }
    }
}

$page_title = 'Register';
include 'components/header.php';
?>

<section class="section">
    <div class="container">
        <div class="form-container">
            <h2 style="text-align: center; color: #ffd700; margin-bottom: 2rem;"><i class="fas fa-user-plus"></i> Create Account</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="registerForm" onsubmit="return validateForm('registerForm')">
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="username"><i class="fas fa-at"></i> Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="user_type"><i class="fas fa-users"></i> User Type</label>
                    <select id="user_type" name="user_type" class="form-control" required>
                        <option value="">Select User Type</option>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">Register</button>
            </form>
            
            <p style="text-align: center; margin-top: 1rem;">
                Already have an account? <a href="login.php" style="color: #ffd700;">Login here</a>
            </p>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>