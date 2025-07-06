<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$pdo = getDBConnection();

// Get unread message count
$stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = FALSE");
$unread_messages = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - R.D.S Gears</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-dashboard { max-width: 800px; margin: 3rem auto; background: #fff; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); padding: 2rem; }
        .admin-dashboard h1 { text-align: center; margin-bottom: 2rem; }
        .admin-links { display: flex; flex-direction: column; gap: 1.5rem; }
        .admin-link { display: flex; align-items: center; gap: 1rem; background: #f8f9fa; padding: 1.5rem; border-radius: 8px; text-decoration: none; color: #2c3e50; font-size: 1.2rem; transition: background 0.2s; }
        .admin-link:hover { background: #e3eafc; }
        .admin-link i { font-size: 2rem; color: #3498db; }
        .message-badge { background: #e74c3c; color: white; border-radius: 50%; padding: 0.25rem 0.5rem; font-size: 0.8rem; margin-left: auto; }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <h1>Admin Dashboard</h1>
        <div class="admin-links">
            <a href="users.php" class="admin-link"><i class="fas fa-users"></i> Manage Users</a>
            <a href="products.php" class="admin-link"><i class="fas fa-box"></i> Manage Products</a>
            <a href="orders.php" class="admin-link"><i class="fas fa-shopping-cart"></i> Manage Orders</a>
            <a href="messages.php" class="admin-link">
                <i class="fas fa-envelope"></i> Manage Messages
                <?php if ($unread_messages > 0): ?>
                    <span class="message-badge"><?php echo $unread_messages; ?></span>
                <?php endif; ?>
            </a>
        </div>
        <div style="text-align:center; margin-top:2rem;">
            <a href="../index.php" class="btn btn-outline">Back to Store</a>
        </div>
    </div>
</body>
</html> 