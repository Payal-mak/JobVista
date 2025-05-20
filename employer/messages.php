<?php 
require_once '../includes/auth.php';
protectEmployerRoute();

$employer_id = $_SESSION['user_id'];
$messages = getUserMessages($employer_id);

// Mark message as read if viewing a specific message
if (isset($_GET['id'])) {
    $message_id = sanitize($_GET['id']);
    markMessageAsRead($message_id);
    header("Location: messages.php");
    exit();
}

include '../includes/header.php';
?>

<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Messages</h1>
            <p>Communicate with job applicants</p>
        </div>
        
        <div class="messages-container">
            <div class="messages-sidebar">
                <div class="messages-search">
                    <input type="text" placeholder="Search messages...">
                </div>
                
                <div class="messages-list">
                    <?php foreach($messages as $message): ?>
                        <a href="messages.php?applicant=<?= $message['sender_id'] == $employer_id ? $message['receiver_id'] : $message['sender_id'] ?>" class="message-preview <?= !$message['is_read'] && $message['receiver_id'] == $employer_id ? 'unread' : '' ?>">
                            <div class="message-sender">
                                <?= $message['sender_id'] == $employer_id ? 'You' : $message['sender_name'] ?>
                                <span class="message-time"><?= date('M d', strtotime($message['sent_at'])) ?></span>
                            </div>
                            <div class="message-subject">
                                <?= $message['subject'] ? $message['subject'] : '(No subject)' ?>
                            </div>
                            <div class="message-excerpt">
                                <?= substr($message['message'], 0, 50) ?>...
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="messages-content">
                <?php if (isset($_GET['applicant'])): 
                    $applicant_id = sanitize($_GET['applicant']);
                    $applicant = getUserById($applicant_id);
                    $job_id = isset($_GET['job']) ? sanitize($_GET['job']) : null;
                    
                    // Get conversation between employer and applicant
                    $stmt = $conn->prepare("SELECT m.*, 
                                          u1.name as sender_name, 
                                          u2.name as receiver_name,
                                          j.title as job_title
                                          FROM messages m
                                          JOIN users u1 ON m.sender_id = u1.id
                                          JOIN users u2 ON m.receiver_id = u2.id
                                          LEFT JOIN jobs j ON m.job_id = j.id
                                          WHERE (m.sender_id = ? AND m.receiver_id = ?)
                                          OR (m.sender_id = ? AND m.receiver_id = ?)
                                          ORDER BY m.sent_at ASC");
                    $stmt->execute([$employer_id, $applicant_id, $applicant_id, $employer_id]);
                    $conversation = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Mark all messages as read
                    $stmt = $conn->prepare("UPDATE messages SET is_read = TRUE 
                                          WHERE receiver_id = ? AND sender_id = ?");
                    $stmt->execute([$employer_id, $applicant_id]);
                ?>
                    <div class="conversation-header">
                        <h3>Conversation with <?= $applicant['name'] ?></h3>
                        <?php if ($job_id): 
                            $job = getJobById($job_id);
                        ?>
                            <p>Regarding: <?= $job['title'] ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="conversation-messages">
                        <?php foreach($conversation as $msg): ?>
                            <div class="message <?= $msg['sender_id'] == $employer_id ? 'sent' : 'received' ?>">
                                <div class="message-header">
                                    <span class="sender"><?= $msg['sender_id'] == $employer_id ? 'You' : $msg['sender_name'] ?></span>
                                    <span class="time"><?= date('M d, Y h:i A', strtotime($msg['sent_at'])) ?></span>
                                </div>
                                <div class="message-body">
                                    <?= nl2br($msg['message']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="reply-box">
                        <form method="post" action="../includes/functions.php">
                            <input type="hidden" name="sender_id" value="<?= $employer_id ?>">
                            <input type="hidden" name="receiver_id" value="<?= $applicant_id ?>">
                            <input type="hidden" name="job_id" value="<?= $job_id ?>">
                            
                            <div class="form-group">
                                <input type="text" name="subject" placeholder="Subject" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <textarea name="message" rows="4" placeholder="Type your message..." class="form-control" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="send_message" class="btn btn-primary">Send Message</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="select-conversation">
                        <i class="fas fa-comments"></i>
                        <p>Select a conversation to view messages</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>