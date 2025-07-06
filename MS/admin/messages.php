<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$pdo = getDBConnection();

// Handle message actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $message_id = $_POST['message_id'] ?? 0;
        
        switch ($_POST['action']) {
            case 'mark_read':
                $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = TRUE WHERE id = ?");
                $stmt->execute([$message_id]);
                break;
            case 'mark_unread':
                $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = FALSE WHERE id = ?");
                $stmt->execute([$message_id]);
                break;
            case 'update_status':
                $status = $_POST['status'] ?? 'new';
                $stmt = $pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
                $stmt->execute([$status, $message_id]);
                break;
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
                $stmt->execute([$message_id]);
                break;
        }
        
        // Redirect to prevent form resubmission
        header('Location: messages.php');
        exit();
    }
}

// Get messages with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total count
$stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages");
$total_messages = $stmt->fetchColumn();
$total_pages = ceil($total_messages / $limit);

// Get messages
$stmt = $pdo->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$limit, $offset]);
$messages = $stmt->fetchAll();

// Get unread count
$stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = FALSE");
$unread_count = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages - Admin - R.D.S Gears</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .admin-header h1 {
            margin: 0;
            color: #2c3e50;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            margin: 0 0 0.5rem 0;
            color: #2c3e50;
        }
        
        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
            color: #3498db;
        }
        
        .messages-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table-header {
            background: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table-header h2 {
            margin: 0;
            color: #2c3e50;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .message-row {
            transition: background 0.2s;
        }
        
        .message-row:hover {
            background: #f8f9fa;
        }
        
        .message-row.unread {
            background: #fff3cd;
        }
        
        .message-row.unread:hover {
            background: #ffeaa7;
        }
        
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-new {
            background: #d4edda;
            color: #155724;
        }
        
        .status-in_progress {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-resolved {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .message-content {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        
        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            text-decoration: none;
            color: #007bff;
        }
        
        .pagination .current {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .pagination a:hover {
            background: #e9ecef;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .message-details h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        
        .message-meta {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .message-meta p {
            margin: 0.25rem 0;
        }
        
        .message-body {
            background: white;
            padding: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-envelope"></i> Manage Contact Messages</h1>
            <a href="dashboard.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Messages</h3>
                <div class="number"><?php echo $total_messages; ?></div>
            </div>
            <div class="stat-card">
                <h3>Unread Messages</h3>
                <div class="number"><?php echo $unread_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>New Messages</h3>
                <div class="number">
                    <?php 
                    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'");
                    echo $stmt->fetchColumn();
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Messages Table -->
        <div class="messages-table">
            <div class="table-header">
                <h2>Contact Messages</h2>
            </div>
            
            <?php if (empty($messages)): ?>
                <div style="padding: 2rem; text-align: center; color: #6c757d;">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <p>No messages found.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                        <tr class="message-row <?php echo !$message['is_read'] ? 'unread' : ''; ?>">
                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo htmlspecialchars($message['subject']); ?></td>
                            <td>
                                <div class="message-content">
                                    <?php echo htmlspecialchars(substr($message['message'], 0, 100)); ?>
                                    <?php if (strlen($message['message']) > 100): ?>...<?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $message['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $message['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-primary" onclick="viewMessage(<?php echo $message['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <?php if ($message['is_read']): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="mark_unread">
                                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="mark_read">
                                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="current"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Message View Modal -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="messageDetails"></div>
        </div>
    </div>
    
    <script>
        // Modal functionality
        const modal = document.getElementById('messageModal');
        const closeBtn = document.querySelector('.close');
        
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        function viewMessage(messageId) {
            // Fetch message details via AJAX
            fetch(`get_message.php?id=${messageId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const message = data.message;
                        document.getElementById('messageDetails').innerHTML = `
                            <div class="message-details">
                                <h3>${message.subject}</h3>
                                <div class="message-meta">
                                    <p><strong>From:</strong> ${message.name} (${message.email})</p>
                                    <p><strong>Date:</strong> ${new Date(message.created_at).toLocaleString()}</p>
                                    <p><strong>Status:</strong> 
                                        <span class="status-badge status-${message.status}">
                                            ${message.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                                        </span>
                                    </p>
                                </div>
                                <div class="message-body">${message.message}</div>
                            </div>
                        `;
                        modal.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading message details');
                });
        }
    </script>
</body>
</html> 