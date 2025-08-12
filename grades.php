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

$message = '';
$message_type = '';
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

// Handle form submission for adding/editing grades
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $subject = trim($_POST['subject']);
    $assignment = trim($_POST['assignment']);
    $grade = (float)$_POST['grade'];
    $max_grade = (float)$_POST['max_grade'];
    $grade_id = isset($_POST['grade_id']) ? (int)$_POST['grade_id'] : 0;
    
    try {
        if ($grade_id > 0) {
            // Update existing grade
            $stmt = $conn->prepare("UPDATE grades SET subject = ?, assignment = ?, grade = ?, max_grade = ?, updated_at = NOW() WHERE id = ? AND teacher_id = ?");
            $stmt->execute([$subject, $assignment, $grade, $max_grade, $grade_id, $_SESSION['user_id']]);
            $message = 'Grade updated successfully!';
        } else {
            // Add new grade
            $stmt = $conn->prepare("INSERT INTO grades (student_id, teacher_id, subject, assignment, grade, max_grade, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$student_id, $_SESSION['user_id'], $subject, $assignment, $grade, $max_grade]);
            $message = 'Grade added successfully!';
        }
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Error saving grade. Please try again.';
        $message_type = 'error';
    }
}

// Handle grade deletion
if (isset($_GET['delete']) && isset($_GET['grade_id'])) {
    $grade_id = (int)$_GET['grade_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM grades WHERE id = ? AND teacher_id = ?");
        $stmt->execute([$grade_id, $_SESSION['user_id']]);
        $message = 'Grade deleted successfully!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Error deleting grade.';
        $message_type = 'error';
    }
}

// Get student information
$student = null;
if ($student_id > 0) {
    try {
        $stmt = $conn->prepare("SELECT id, full_name, email FROM users WHERE id = ? AND user_type = 'student'");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $student = null;
    }
}

// Get all students for dropdown
try {
    $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE user_type = 'student' ORDER BY full_name");
    $stmt->execute();
    $all_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $all_students = [];
}

// Get grades for selected student
$grades = [];
if ($student_id > 0) {
    try {
        $stmt = $conn->prepare("SELECT * FROM grades WHERE student_id = ? AND teacher_id = ? ORDER BY created_at DESC");
        $stmt->execute([$student_id, $_SESSION['user_id']]);
        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $grades = [];
    }
}

// Get grade for editing
$edit_grade = null;
if (isset($_GET['edit']) && isset($_GET['grade_id'])) {
    $grade_id = (int)$_GET['grade_id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM grades WHERE id = ? AND teacher_id = ?");
        $stmt->execute([$grade_id, $_SESSION['user_id']]);
        $edit_grade = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($edit_grade) {
            $student_id = $edit_grade['student_id'];
        }
    } catch (PDOException $e) {
        $edit_grade = null;
    }
}

$page_title = 'Grade Management';
include 'components/header.php';
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-star"></i> Grade Management</h1>
        <p>Add, edit, and manage student grades efficiently.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="grades-layout">
            <!-- Student Selection -->
            <div class="student-selector">
                <h3><i class="fas fa-user-graduate"></i> Select Student</h3>
                <form method="GET" class="student-form">
                    <select name="student_id" onchange="this.form.submit()" class="form-select">
                        <option value="">Choose a student...</option>
                        <?php foreach ($all_students as $s): ?>
                            <option value="<?php echo $s['id']; ?>" <?php echo ($s['id'] == $student_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            
            <?php if ($student): ?>
                <!-- Grade Form -->
                <div class="grade-form-section">
                    <h3>
                        <i class="fas fa-plus"></i> 
                        <?php echo $edit_grade ? 'Edit Grade' : 'Add New Grade'; ?> for <?php echo htmlspecialchars($student['full_name']); ?>
                    </h3>
                    
                    <form method="POST" class="grade-form">
                        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                        <?php if ($edit_grade): ?>
                            <input type="hidden" name="grade_id" value="<?php echo $edit_grade['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" required 
                                       value="<?php echo $edit_grade ? htmlspecialchars($edit_grade['subject']) : ''; ?>"
                                       placeholder="e.g., Mathematics, Computer Science">
                            </div>
                            
                            <div class="form-group">
                                <label for="assignment">Assignment</label>
                                <input type="text" id="assignment" name="assignment" required 
                                       value="<?php echo $edit_grade ? htmlspecialchars($edit_grade['assignment']) : ''; ?>"
                                       placeholder="e.g., Midterm Exam, Project 1">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="grade">Grade</label>
                                <input type="number" id="grade" name="grade" step="0.1" min="0" required 
                                       value="<?php echo $edit_grade ? $edit_grade['grade'] : ''; ?>"
                                       placeholder="85.5">
                            </div>
                            
                            <div class="form-group">
                                <label for="max_grade">Max Grade</label>
                                <input type="number" id="max_grade" name="max_grade" step="0.1" min="0" required 
                                       value="<?php echo $edit_grade ? $edit_grade['max_grade'] : '100'; ?>"
                                       placeholder="100">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 
                                <?php echo $edit_grade ? 'Update Grade' : 'Add Grade'; ?>
                            </button>
                            <?php if ($edit_grade): ?>
                                <a href="grades.php?student_id=<?php echo $student_id; ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <!-- Grades List -->
                <div class="grades-list-section">
                    <h3><i class="fas fa-list"></i> Current Grades</h3>
                    
                    <?php if (empty($grades)): ?>
                        <div class="no-grades">
                            <i class="fas fa-clipboard"></i>
                            <p>No grades recorded for this student yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="grades-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Assignment</th>
                                        <th>Grade</th>
                                        <th>Percentage</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($grades as $grade): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($grade['subject']); ?></td>
                                            <td><?php echo htmlspecialchars($grade['assignment']); ?></td>
                                            <td><?php echo $grade['grade'] . '/' . $grade['max_grade']; ?></td>
                                            <td>
                                                <span class="percentage <?php echo ($grade['grade']/$grade['max_grade']*100 >= 70) ? 'good' : (($grade['grade']/$grade['max_grade']*100 >= 50) ? 'average' : 'poor'); ?>">
                                                    <?php echo number_format(($grade['grade']/$grade['max_grade'])*100, 1); ?>%
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($grade['created_at'])); ?></td>
                                            <td class="actions">
                                                <a href="grades.php?student_id=<?php echo $student_id; ?>&edit=1&grade_id=<?php echo $grade['id']; ?>" 
                                                   class="btn btn-small btn-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="grades.php?student_id=<?php echo $student_id; ?>&delete=1&grade_id=<?php echo $grade['id']; ?>" 
                                                   class="btn btn-small btn-danger"
                                                   onclick="return confirm('Are you sure you want to delete this grade?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Grade Statistics -->
                        <div class="grade-stats">
                            <?php 
                            $total_points = array_sum(array_column($grades, 'grade'));
                            $total_max = array_sum(array_column($grades, 'max_grade'));
                            $overall_percentage = $total_max > 0 ? ($total_points / $total_max) * 100 : 0;
                            ?>
                            <div class="stat-card">
                                <h4>Overall Performance</h4>
                                <div class="overall-grade">
                                    <span class="grade-number"><?php echo number_format($overall_percentage, 1); ?>%</span>
                                    <span class="grade-letter">
                                        <?php 
                                        if ($overall_percentage >= 90) echo 'A';
                                        elseif ($overall_percentage >= 80) echo 'B';
                                        elseif ($overall_percentage >= 70) echo 'C';
                                        elseif ($overall_percentage >= 60) echo 'D';
                                        else echo 'F';
                                        ?>
                                    </span>
                                </div>
                                <p><?php echo count($grades); ?> assignments graded</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>