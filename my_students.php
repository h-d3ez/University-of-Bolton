<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Get selected student for detailed view
$selected_student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;

// Get all students and their motivation statistics
try {
    $stmt = $conn->prepare("
        SELECT DISTINCT u.id, u.full_name, u.email, u.username, u.created_at as registration_date,
               COUNT(m.id) as total_logs,
               AVG(m.motivation_level) as avg_motivation,
               MAX(m.created_at) as last_log_date,
               MIN(m.created_at) as first_log_date
        FROM users u 
        LEFT JOIN motivation_logs m ON u.id = m.user_id 
        WHERE u.user_type = 'student'
        GROUP BY u.id, u.full_name, u.email, u.username, u.created_at
        ORDER BY u.full_name
    ");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent motivations for each student (last 30 days)
    foreach ($students as &$student) {
        $stmt = $conn->prepare("
            SELECT m.motivation_level, m.created_at, m.notes
            FROM motivation_logs m
            WHERE m.user_id = ? AND m.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ORDER BY m.created_at DESC
            LIMIT 10
        ");
        $stmt->execute([$student['id']]);
        $student['recent_motivations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get weekly averages for the last 4 weeks
        $stmt = $conn->prepare("
            SELECT 
                WEEK(m.created_at) as week_num,
                YEAR(m.created_at) as year_num,
                AVG(m.motivation_level) as avg_level,
                COUNT(m.id) as log_count,
                DATE(MIN(m.created_at)) as week_start
            FROM motivation_logs m
            WHERE m.user_id = ? AND m.created_at >= DATE_SUB(NOW(), INTERVAL 4 WEEK)
            GROUP BY WEEK(m.created_at), YEAR(m.created_at)
            ORDER BY year_num DESC, week_num DESC
        ");
        $stmt->execute([$student['id']]);
        $student['weekly_stats'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (PDOException $e) {
    $students = [];
    $error_message = "Error loading student data: " . $e->getMessage();
}

// Get detailed motivation logs for selected student
$detailed_logs = [];
if ($selected_student_id) {
    try {
        $stmt = $conn->prepare("
            SELECT m.motivation_level, m.created_at, m.notes,
                   DATE(m.created_at) as log_date,
                   TIME(m.created_at) as log_time
            FROM motivation_logs m
            WHERE m.user_id = ?
            ORDER BY m.created_at DESC
        ");
        $stmt->execute([$selected_student_id]);
        $detailed_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get selected student info
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'student'");
        $stmt->execute([$selected_student_id]);
        $selected_student = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $detailed_logs = [];
        $selected_student = null;
    }
}

$page_title = 'My Students';
include 'components/header.php';
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-users"></i> My Students</h1>
        <p>Monitor your students' daily motivation levels and progress.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($selected_student_id && $selected_student): ?>
            <!-- Detailed Student View -->
            <div class="student-detail-header">
                <a href="my_students.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to All Students
                </a>
                <div class="student-detail-info">
                    <div class="student-avatar-large">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="student-details">
                        <h2><?php echo htmlspecialchars($selected_student['full_name']); ?></h2>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($selected_student['email']); ?></p>
                        <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($selected_student['username']); ?></p>
                        <p><i class="fas fa-calendar"></i> Registered: <?php echo date('M j, Y', strtotime($selected_student['created_at'])); ?></p>
                    </div>
                    <div class="student-actions">
                        <a href="grades.php?student_id=<?php echo $selected_student['id']; ?>" class="btn">
                            <i class="fas fa-star"></i> Manage Grades
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="motivation-logs-container">
                <h3><i class="fas fa-chart-line"></i> Complete Motivation Log History</h3>
                
                <?php if (empty($detailed_logs)): ?>
                    <div class="no-logs">
                        <i class="fas fa-clipboard-list"></i>
                        <h4>No Motivation Logs Found</h4>
                        <p>This student hasn't logged any motivation data yet.</p>
                    </div>
                <?php else: ?>
                    <div class="logs-summary">
                        <div class="summary-stats">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo count($detailed_logs); ?></div>
                                <div class="stat-label">Total Logs</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?php echo number_format(array_sum(array_column($detailed_logs, 'motivation_level')) / count($detailed_logs), 1); ?></div>
                                <div class="stat-label">Average Level</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?php echo date('M j', strtotime($detailed_logs[0]['created_at'])); ?></div>
                                <div class="stat-label">Last Log</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?php echo date('M j', strtotime(end($detailed_logs)['created_at'])); ?></div>
                                <div class="stat-label">First Log</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detailed-logs">
                        <?php 
                        $current_date = '';
                        foreach ($detailed_logs as $log): 
                            $log_date = date('Y-m-d', strtotime($log['created_at']));
                            if ($log_date !== $current_date):
                                $current_date = $log_date;
                        ?>
                            <div class="log-date-header">
                                <h4><?php echo date('l, F j, Y', strtotime($log['created_at'])); ?></h4>
                            </div>
                        <?php endif; ?>
                        
                        <div class="log-entry">
                            <div class="log-time">
                                <?php echo date('g:i A', strtotime($log['created_at'])); ?>
                            </div>
                            <div class="log-content">
                                <div class="motivation-level-display">
                                    <?php 
                                    $level_out_of_10 = round($log['motivation_level'] / 10);
                                    $level_class = max(1, min(10, $level_out_of_10));
                                    ?>
                                    <div class="level-indicator level-<?php echo $level_class; ?>">
                                        <?php echo $log['motivation_level']; ?>%
                                    </div>
                                    <div class="level-bar-full">
                                        <div class="level-fill" style="width: <?php echo $log['motivation_level']; ?>%"></div>
                                    </div>
                                </div>
                                <?php if ($log['notes']): ?>
                                    <div class="log-quote">
                                        <i class="fas fa-sticky-note"></i>
                                        <blockquote>
                                            <?php echo htmlspecialchars($log['notes']); ?>
                                        </blockquote>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
        <?php else: ?>
            <!-- Students Overview -->
            <div class="students-overview">
                <div class="overview-header">
                    <h2><i class="fas fa-users"></i> Students Overview</h2>
                    <p>Click on any student to view their detailed motivation logs</p>
                </div>
                
                <?php if (empty($students)): ?>
                    <div class="no-students">
                        <i class="fas fa-user-graduate"></i>
                        <h3>No Students Found</h3>
                        <p>No students have registered yet.</p>
                    </div>
                <?php else: ?>
                    <div class="students-grid">
                        <?php foreach ($students as $student): ?>
                            <div class="student-card" onclick="window.location.href='my_students.php?student_id=<?php echo $student['id']; ?>'">
                                <div class="student-header">
                                    <div class="student-avatar">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div class="student-info">
                                        <h3><?php echo htmlspecialchars($student['full_name']); ?></h3>
                                        <p><?php echo htmlspecialchars($student['email']); ?></p>
                                        <p class="username">@<?php echo htmlspecialchars($student['username']); ?></p>
                                    </div>
                                    <div class="student-actions">
                                        <a href="grades.php?student_id=<?php echo $student['id']; ?>" class="btn btn-small" onclick="event.stopPropagation();">
                                            <i class="fas fa-star"></i> Grades
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="motivation-summary">
                                    <h4><i class="fas fa-chart-line"></i> Motivation Summary</h4>
                                    <div class="summary-stats-grid">
                                        <div class="summary-stat">
                                            <span class="stat-number"><?php echo $student['total_logs']; ?></span>
                                            <span class="stat-label">Total Logs</span>
                                        </div>
                                        <div class="summary-stat">
                                            <span class="stat-number"><?php echo $student['avg_motivation'] ? number_format($student['avg_motivation'], 1) : '0'; ?></span>
                                            <span class="stat-label">Avg Level</span>
                                        </div>
                                        <div class="summary-stat">
                                            <span class="stat-number"><?php echo $student['last_log_date'] ? date('M j', strtotime($student['last_log_date'])) : 'Never'; ?></span>
                                            <span class="stat-label">Last Log</span>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($student['recent_motivations'])): ?>
                                        <div class="recent-trend">
                                            <h5>Recent Trend (Last 10 logs)</h5>
                                            <div class="trend-chart">
                                                <?php foreach (array_reverse(array_slice($student['recent_motivations'], 0, 10)) as $motivation): ?>
                                                    <div class="trend-bar" style="height: <?php echo ($motivation['motivation_level'] * 0.4); ?>px;" 
                                                         title="<?php echo $motivation['motivation_level']; ?>% on <?php echo date('M j', strtotime($motivation['created_at'])); ?>"></div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="no-recent-data">
                                            <p><i class="fas fa-info-circle"></i> No recent motivation data</p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($student['weekly_stats'])): ?>
                                        <div class="weekly-progress">
                                            <h5>Weekly Progress</h5>
                                            <div class="weekly-stats">
                                                <?php foreach ($student['weekly_stats'] as $week): ?>
                                                    <div class="week-stat">
                                                        <div class="week-avg"><?php echo number_format($week['avg_level'], 1); ?></div>
                                                        <div class="week-label">Week of <?php echo date('M j', strtotime($week['week_start'])); ?></div>
                                                        <div class="week-count"><?php echo $week['log_count']; ?> logs</div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-footer">
                                    <span class="view-details">Click to view detailed logs <i class="fas fa-arrow-right"></i></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'components/footer.php'; ?>