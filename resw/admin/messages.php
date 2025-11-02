<?php
session_start();
include_once "../includes/connection.php";
include_once "../includes/functions.php";

// Check if user is admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

$isSubDirectory = true;
$page_title = "Messages - Admin Dashboard";

// Handle message deletion
if (isset($_POST['delete_message'])) {
    $message_id = mysqli_real_escape_string($con, $_POST['message_id']);
    $query = "DELETE FROM messages WHERE message_id = '$message_id'";
    if (mysqli_query($con, $query)) {
        $_SESSION['success_msg'] = "Message deleted successfully!";
    } else {
        $_SESSION['error_msg'] = "Error deleting message: " . mysqli_error($con);
    }
    header("Location: messages.php");
    exit();
}

// Handle marking message as read
if (isset($_POST['mark_read'])) {
    $message_id = mysqli_real_escape_string($con, $_POST['message_id']);
    $query = "UPDATE messages SET is_read = TRUE WHERE message_id = '$message_id'";
    mysqli_query($con, $query);
    header("Location: messages.php");
    exit();
}

// Get messages count
$count_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN is_read = FALSE THEN 1 ELSE 0 END) as unread
    FROM messages";
$count_result = mysqli_query($con, $count_query);
$counts = mysqli_fetch_assoc($count_result);

// Fetch all messages
$query = "SELECT * FROM messages ORDER BY created_at DESC";
$result = mysqli_query($con, $query);

include '../includes/nav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $page_title; ?></title>
    
    <style>
    :root {
        --primary-color: #572a00;
        --hover-color: #562700;
        --white: #ffffff;
        --light-bg: #f8f9fa;
        --shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    img {
        margin-top: 3px;
    }
    
    .inside-banner {
        background-color: var(--primary-color);
        color: var(--white);
        padding: 40px 0;
        margin-bottom: 40px;
    }
    
    .inside-banner h2 {
        margin: 0;
        color: var(--white);
    }
    
    .panel {
        border-radius: 10px;
        box-shadow: var(--shadow);
        border: none;
        margin-bottom: 30px;
    }
    
    .panel-heading {
        background-color: var(--primary-color) !important;
        color: var(--white) !important;
        border-radius: 10px 10px 0 0;
        padding: 15px 20px;
    }
    
    .btn-sm {
        margin: 2px;
    }
    
    .table > thead > tr > th {
        background-color: #f5f5f5;
    }
    
    .alert {
        margin-bottom: 20px;
    }
    
    .label {
        display: inline-block;
        padding: 4px 8px;
        font-size: 12px;
        border-radius: 3px;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .social-links img {
    width: 20px;
    height: 20px;
    margin-top: 7px;
} 
    .btn-primary:hover {
        background-color: var(--hover-color);
        border-color: var(--hover-color);
    }
    </style>
</head>
<body>

<!-- banner -->
<div class="inside-banner">
    <div class="container">
        <h2>Messages</h2>
    </div>
</div>
<!-- banner -->

<div class="container">
    <div class="properties-listing spacer">
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_msg'];
                unset($_SESSION['success_msg']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error_msg'];
                unset($_SESSION['error_msg']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Message Statistics -->
        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Total Messages</h3>
                    </div>
                    <div class="panel-body">
                        <h3><?php echo $counts['total']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h3 class="panel-title">Unread Messages</h3>
                    </div>
                    <div class="panel-body">
                        <h3><?php echo $counts['unread']; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages List -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">All Messages</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($message = mysqli_fetch_assoc($result)): ?>
                                <tr class="<?php echo !$message['is_read'] ? 'info' : ''; ?>">
                                    <td><?php echo date('Y-m-d H:i', strtotime($message['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($message['name']); ?></td>
                                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                                    <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($message['message']); ?></td>
                                    <td>
                                        <?php if (!$message['is_read']): ?>
                                            <span class="label label-warning">Unread</span>
                                        <?php else: ?>
                                            <span class="label label-default">Read</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!$message['is_read']): ?>
                                            <form action="" method="POST" style="display: inline-block;">
                                                <input type="hidden" name="message_id" value="<?php echo $message['message_id']; ?>">
                                                <button type="submit" name="mark_read" class="btn btn-success btn-sm">
                                                    <i class="glyphicon glyphicon-ok"></i> Mark as Read
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="" method="POST" style="display: inline-block;" 
                                              onsubmit="return confirm('Are you sure you want to delete this message?');">
                                            <input type="hidden" name="message_id" value="<?php echo $message['message_id']; ?>">
                                            <button type="submit" name="delete_message" class="btn btn-danger btn-sm">
                                                <i class="glyphicon glyphicon-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php';   ?>    

</body>
</html> 