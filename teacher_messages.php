<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

$page_title = 'Contact Inquiries';
include 'components/header.php';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'bolton_university');

$result = $conn->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC");
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-comments"></i> Contact Inquiries</h1>
        <p>View and respond to messages submitted through the Contact Us form.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title"><i class="fas fa-envelope-open-text"></i> Guest Messages</h2>
        <?php if ($result->num_rows > 0): ?>
            <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="dashboard-card"
                    style="
                        width: 350px;
                        height: 260px;
                        border: 2px solid #FFD600;
                        border-radius: 10px;
                        margin-bottom: 1.5rem;
                        padding: 1.2rem;
                        text-align: left;
                        box-sizing: border-box;
                        display: flex;
                        flex-direction: column;
                        justify-content: flex-start;
                    ">
                    <div class="dashboard-icon" style="
                        margin-bottom: 0.5rem;
                        width: 48px;
                        height: 48px;
                        border-radius: 50%;
                        background: #000000ff;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    ">
                        <i class="fas fa-user-circle" style="font-size: 2em; color: #FFD600;"></i>
                    </div>
                    <h3 style="margin-bottom: 0.3rem; font-size: 1.1em;"><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p style="font-size: 0.92em; color: #888; margin-bottom: 0.4rem;">
                        <i class="fas fa-clock"></i> <?php echo htmlspecialchars($row['submitted_at']); ?>
                    </p>
                    <p style="font-size: 0.92em; margin-bottom: 0.3rem;">
                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?>
                    </p>
                    <p style="font-size: 0.92em; margin-bottom: 0.3rem;">
                        <i class="fas fa-tag"></i> <strong><?php echo htmlspecialchars(ucfirst($row['subject'])); ?></strong>
                    </p>
                    <div style="margin-bottom: 0.2em; color: #888; word-break: break-word; font-size: 0.97em; flex: 1 1 auto; overflow: auto;">
                        <i class="fas fa-comment"></i>
                        <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info" style="margin-top:2rem;">
                <i class="fas fa-info-circle"></i> No messages found.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'components/footer.php'; ?>