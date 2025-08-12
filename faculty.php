<?php
session_start();
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Fetch all teachers
$stmt = $conn->prepare("SELECT full_name, email, username FROM users WHERE user_type = 'teacher' ORDER BY full_name");
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Faculty Directory';
include 'components/header.php';
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-chalkboard-teacher"></i> Faculty Directory</h1>
        <p>Meet our esteemed faculty members.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="faculty-list">
            <?php if (empty($teachers)): ?>
                <div class="no-faculty">
                    <i class="fas fa-user-slash"></i>
                    <h3>No faculty members found.</h3>
                    <p>Check back later for updates.</p>
                </div>
            <?php else: ?>
                <div class="faculty-grid">
                    <?php foreach ($teachers as $teacher): ?>
                        <div class="faculty-card">
                            <div class="faculty-avatar">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="faculty-info">
                                <h3><?php echo htmlspecialchars($teacher['full_name']); ?></h3>
                                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($teacher['email']); ?></p>
                                <p class="username">@<?php echo htmlspecialchars($teacher['username']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>