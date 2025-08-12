<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header('Location: login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $motivation_level = $_POST['motivation_level'];
    $notes = trim($_POST['notes']);
    $log_date = date('Y-m-d');
    
    try {
        $stmt = $conn->prepare("INSERT INTO motivation_logs (user_id, motivation_level, log_date, notes) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE motivation_level = ?, notes = ?");
        $stmt->execute([$_SESSION['user_id'], $motivation_level, $log_date, $notes, $motivation_level, $notes]);
        
        $message = 'Motivation logged successfully!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Failed to log motivation. Please try again.';
        $message_type = 'error';
    }
}

// Get today's motivation if exists
$today_motivation = null;
try {
    $stmt = $conn->prepare("SELECT * FROM motivation_logs WHERE user_id = ? AND log_date = ?");
    $stmt->execute([$_SESSION['user_id'], date('Y-m-d')]);
    $today_motivation = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error silently
}

// Get recent motivation logs
$recent_logs = [];
try {
    $stmt = $conn->prepare("SELECT * FROM motivation_logs WHERE user_id = ? ORDER BY log_date DESC LIMIT 7");
    $stmt->execute([$_SESSION['user_id']]);
    $recent_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error silently
}

$motivational_quotes = [
    ["The future belongs to those who believe in the beauty of their dreams.", "Eleanor Roosevelt"],
    ["Success is not final, failure is not fatal: it is the courage to continue that counts.", "Winston Churchill"],
    ["The only way to do great work is to love what you do.", "Steve Jobs"],
    ["Innovation distinguishes between a leader and a follower.", "Steve Jobs"],
    ["Your limitation—it's only your imagination.", "Unknown"],
    ["Push yourself, because no one else is going to do it for you.", "Unknown"],
    ["Great things never come from comfort zones.", "Unknown"],
    ["Dream it. Wish it. Do it.", "Unknown"],
    ["Success doesn't just find you. You have to go out and get it.", "Unknown"],
    ["The harder you work for something, the greater you'll feel when you achieve it.", "Unknown"],
    ["Dream bigger. Do bigger.", "Unknown"],
    ["Don't stop when you're tired. Stop when you're done.", "Unknown"],
    ["Wake up with determination. Go to bed with satisfaction.", "Unknown"],
    ["Do something today that your future self will thank you for.", "Sean Patrick Flanery"],
    ["Little things make big days.", "Unknown"],
    ["It's going to be hard, but hard does not mean impossible.", "Unknown"],
    ["Don't wait for opportunity. Create it.", "Unknown"],
    ["Sometimes we're tested not to show our weaknesses, but to discover our strengths.", "Unknown"],
    ["The key to success is to focus on goals, not obstacles.", "Unknown"],
    ["Believe you can and you're halfway there.", "Theodore Roosevelt"]
];

$page_title = 'Daily Motivation';
include 'components/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="section-title"><i class="fas fa-chart-line"></i> Daily Motivation Tracker</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-2">
            <div class="card">
                <h3><i class="fas fa-plus-circle"></i> Log Today's Motivation</h3>
                <form method="POST" id="motivationForm">
                    <div class="form-group">
                        <label for="motivation_level">Motivation Level (1-100)</label>
                        <input type="range" id="motivation_level" name="motivation_level" min="1" max="100" 
                               value="<?php echo $today_motivation ? $today_motivation['motivation_level'] : 50; ?>" 
                               class="form-control" oninput="updateMotivationDisplay(this.value)" required>
                        <div style="text-align: center; margin-top: 0.5rem;">
                            <span id="motivation-display"><?php echo $today_motivation ? $today_motivation['motivation_level'] : 50; ?></span>%
                        </div>
                        <div class="progress-container">
                            <div class="progress-bar" id="motivation-progress" 
                                 style="width: <?php echo $today_motivation ? $today_motivation['motivation_level'] : 50; ?>%;">
                                <?php echo $today_motivation ? $today_motivation['motivation_level'] : 50; ?>%
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes (Optional)</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3" 
                                  placeholder="How are you feeling today? What's motivating you?"><?php echo $today_motivation ? htmlspecialchars($today_motivation['notes']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn" style="width: 100%;">
                        <?php echo $today_motivation ? 'Update' : 'Log'; ?> Motivation
                    </button>
                </form>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-history"></i> Recent Logs</h3>
                <?php if (empty($recent_logs)): ?>
                    <p>No motivation logs yet. Start tracking your daily motivation!</p>
                <?php else: ?>
                    <?php foreach ($recent_logs as $log): ?>
                        <div class="message-container" style="margin-bottom: 1rem;">
                            <div class="message-header">
                                <span class="message-sender"><?php echo date('M j, Y', strtotime($log['log_date'])); ?></span>
                                <span class="message-time"><?php echo $log['motivation_level']; ?>%</span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-bar" style="width: <?php echo $log['motivation_level']; ?>%;">
                                    <?php echo $log['motivation_level']; ?>%
                                </div>
                            </div>
                            <?php if ($log['notes']): ?>
                                <p style="margin-top: 0.5rem; font-size: 0.9rem;"><?php echo htmlspecialchars($log['notes']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background-color: #111;">
    <div class="container">
        <h2 class="section-title"><i class="fas fa-quote-left"></i> Daily Motivation Quotes</h2>
        <div class="quotes-grid">
            <?php foreach ($motivational_quotes as $quote): ?>
                <div class="quote-card">
                    <div class="quote-text">"<?php echo htmlspecialchars($quote[0]); ?>"</div>
                    <div class="quote-author">- <?php echo htmlspecialchars($quote[1]); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>