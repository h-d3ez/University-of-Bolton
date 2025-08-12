<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

$message = '';
$message_type = '';
$selected_chat = isset($_GET['chat']) ? (int)$_GET['chat'] : 0;

// Handle AJAX message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    $receiver_id = (int)$_POST['receiver_id'];
    $message_text = trim($_POST['message']);
    
    try {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$_SESSION['user_id'], $receiver_id, $message_text]);
        
        echo json_encode(['success' => true, 'message' => 'Message sent!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
    exit();
}

// Get conversation partners
$conversations = [];
try {
    $stmt = $conn->prepare("
        SELECT DISTINCT 
            CASE 
                WHEN m.sender_id = ? THEN m.receiver_id 
                ELSE m.sender_id 
            END as partner_id,
            u.full_name, u.user_type,
            (SELECT message FROM messages m2 
             WHERE (m2.sender_id = ? AND m2.receiver_id = partner_id) 
                OR (m2.receiver_id = ? AND m2.sender_id = partner_id)
             ORDER BY m2.sent_at DESC LIMIT 1) as last_message,
            (SELECT sent_at FROM messages m2 
             WHERE (m2.sender_id = ? AND m2.receiver_id = partner_id) 
                OR (m2.receiver_id = ? AND m2.sender_id = partner_id)
             ORDER BY m2.sent_at DESC LIMIT 1) as last_message_time,
            (SELECT COUNT(*) FROM messages m2 
             WHERE m2.sender_id = partner_id AND m2.receiver_id = ? AND m2.is_read = 0) as unread_count
        FROM messages m
        JOIN users u ON u.id = CASE WHEN m.sender_id = ? THEN m.receiver_id ELSE m.sender_id END
        WHERE m.sender_id = ? OR m.receiver_id = ?
        ORDER BY last_message_time DESC
    ");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $conversations = [];
}

// Get all available users to start new conversations
$available_users = [];
try {
    if ($_SESSION['user_type'] === 'student') {
        $stmt = $conn->prepare("SELECT id, full_name, user_type FROM users WHERE user_type = 'teacher' ORDER BY full_name");
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, user_type FROM users WHERE user_type = 'student' ORDER BY full_name");
    }
    $stmt->execute();
    $available_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $available_users = [];
}

// Get messages for selected conversation
$chat_messages = [];
$chat_partner = null;
if ($selected_chat > 0) {
    try {
        // Get chat partner info
        $stmt = $conn->prepare("SELECT id, full_name, user_type FROM users WHERE id = ?");
        $stmt->execute([$selected_chat]);
        $chat_partner = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get messages
        $stmt = $conn->prepare("
            SELECT m.*, u.full_name as sender_name 
            FROM messages m 
            JOIN users u ON m.sender_id = u.id 
            WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.sent_at ASC
        ");
        $stmt->execute([$_SESSION['user_id'], $selected_chat, $selected_chat, $_SESSION['user_id']]);
        $chat_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Mark messages as read
        $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
        $stmt->execute([$selected_chat, $_SESSION['user_id']]);
    } catch (PDOException $e) {
        $chat_messages = [];
    }
}

$page_title = 'Messages';
include 'components/header.php';
?>

<section class="hero-small">
    <div class="hero-content">
        <h1><i class="fas fa-comments"></i> Messages</h1>
        <p>Connect and communicate with your academic community</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="chat-container">
            <!-- Sidebar with conversations -->
            <div class="chat-sidebar">
                <div class="sidebar-header">
                    <h3><i class="fas fa-comments"></i> Conversations</h3>
                    <button class="btn btn-small" onclick="toggleNewChatModal()">
                        <i class="fas fa-plus"></i> New
                    </button>
                </div>
                
                <div class="conversations-list">
                    <?php if (empty($conversations)): ?>
                        <div class="no-conversations">
                            <i class="fas fa-comment-slash"></i>
                            <p>No conversations yet</p>
                            <button class="btn btn-small" onclick="toggleNewChatModal()">
                                Start a conversation
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($conversations as $conv): ?>
                            <div class="conversation-item <?php echo ($conv['partner_id'] == $selected_chat) ? 'active' : ''; ?>" 
                                 onclick="window.location.href='messages.php?chat=<?php echo $conv['partner_id']; ?>'">
                                <div class="conversation-avatar">
                                    <i class="fas <?php echo $conv['user_type'] === 'teacher' ? 'fa-chalkboard-teacher' : 'fa-user-graduate'; ?>"></i>
                                </div>
                                <div class="conversation-info">
                                    <div class="conversation-name">
                                        <?php echo htmlspecialchars($conv['full_name']); ?>
                                        <?php if ($conv['unread_count'] > 0): ?>
                                            <span class="unread-badge"><?php echo $conv['unread_count']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="conversation-preview">
                                        <?php echo htmlspecialchars(substr($conv['last_message'], 0, 50)) . (strlen($conv['last_message']) > 50 ? '...' : ''); ?>
                                    </div>
                                    <div class="conversation-time">
                                        <?php echo timeAgo($conv['last_message_time']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Chat area -->
            <div class="chat-main">
                <?php if ($selected_chat > 0 && $chat_partner): ?>
                    <!-- Chat header -->
                    <div class="chat-header">
                        <div class="chat-partner-info">
                            <div class="partner-avatar">
                                <i class="fas <?php echo $chat_partner['user_type'] === 'teacher' ? 'fa-chalkboard-teacher' : 'fa-user-graduate'; ?>"></i>
                            </div>
                            <div class="partner-details">
                                <h4><?php echo htmlspecialchars($chat_partner['full_name']); ?></h4>
                                <span class="partner-role"><?php echo ucfirst($chat_partner['user_type']); ?></span>
                            </div>
                        </div>
                        <div class="chat-actions">
                            <button class="btn btn-small btn-secondary" onclick="refreshChat()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Messages area -->
                    <div class="chat-messages" id="chatMessages">
                        <?php if (empty($chat_messages)): ?>
                            <div class="no-messages">
                                <i class="fas fa-comment-dots"></i>
                                <p>No messages yet. Start the conversation!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($chat_messages as $msg): ?>
                                <div class="message <?php echo ($msg['sender_id'] == $_SESSION['user_id']) ? 'sent' : 'received'; ?>">
                                    <div class="message-content">
                                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                    </div>
                                    <div class="message-time">
                                        <?php echo date('g:i A', strtotime($msg['sent_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Message input -->
                    <div class="chat-input">
                        <form id="messageForm" onsubmit="sendMessage(event)">
                            <input type="hidden" id="receiverId" value="<?php echo $selected_chat; ?>">
                            <div class="input-group">
                                <textarea id="messageInput" placeholder="Type your message..." rows="1" 
                                         onkeypress="handleKeyPress(event)" maxlength="1000"></textarea>
                                <button type="submit" class="send-btn">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Welcome screen -->
                    <div class="chat-welcome">
                        <div class="welcome-content">
                            <i class="fas fa-comments"></i>
                            <h3>Welcome to Messages</h3>
                            <p>Select a conversation from the sidebar or start a new one to begin messaging.</p>
                            <button class="btn" onclick="toggleNewChatModal()">
                                <i class="fas fa-plus"></i> Start New Conversation
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- New Chat Modal -->
<div id="newChatModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Start New Conversation</h3>
            <button class="close-btn" onclick="toggleNewChatModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="users-list">
                <?php foreach ($available_users as $user): ?>
                    <div class="user-item" onclick="startNewChat(<?php echo $user['id']; ?>)">
                        <div class="user-avatar">
                            <i class="fas <?php echo $user['user_type'] === 'teacher' ? 'fa-chalkboard-teacher' : 'fa-user-graduate'; ?>"></i>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                            <div class="user-role"><?php echo ucfirst($user['user_type']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function timeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + 'm ago';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + 'h ago';
    if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + 'd ago';
    return date.toLocaleDateString();
}

function toggleNewChatModal() {
    const modal = document.getElementById('newChatModal');
    modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
}

function startNewChat(userId) {
    window.location.href = 'messages.php?chat=' + userId;
}

function sendMessage(event) {
    event.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const receiverId = document.getElementById('receiverId').value;
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    // Add message to chat immediately (optimistic update)
    addMessageToChat(message, true);
    messageInput.value = '';
    
    // Send via AJAX
    fetch('messages.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ajax=1&receiver_id=' + receiverId + '&message=' + encodeURIComponent(message)
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            // Remove the optimistic message and show error
            console.error('Failed to send message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function addMessageToChat(message, isSent) {
    const chatMessages = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = 'message ' + (isSent ? 'sent' : 'received');
    
    const now = new Date();
    const timeString = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    messageDiv.innerHTML = `
        <div class="message-content">${message.replace(/\n/g, '<br>')}</div>
        <div class="message-time">${timeString}</div>
    `;
    
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function handleKeyPress(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage(event);
    }
}

function refreshChat() {
    location.reload();
}

// Auto-scroll to bottom on page load
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
</script>

<?php
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time/60) . 'm ago';
    if ($time < 86400) return floor($time/3600) . 'h ago';
    if ($time < 604800) return floor($time/86400) . 'd ago';
    return date('M j', strtotime($datetime));
}