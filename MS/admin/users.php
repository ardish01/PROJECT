<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}
$pdo = getDBConnection();
// Promote user to admin if requested
if (isset($_POST['promote_user_id'])) {
    $promote_id = intval($_POST['promote_user_id']);
    $stmt = $pdo->prepare('UPDATE users SET is_admin = 1 WHERE id = ?');
    $stmt->execute([$promote_id]);
}
// Demote admin to normal user if requested (cannot demote self)
if (isset($_POST['demote_user_id'])) {
    $demote_id = intval($_POST['demote_user_id']);
    if ($demote_id !== $_SESSION['user_id']) {
        $stmt = $pdo->prepare('UPDATE users SET is_admin = 0 WHERE id = ?');
        $stmt->execute([$demote_id]);
    }
}
// Remove user if requested (cannot remove self)
if (isset($_POST['remove_user_id'])) {
    $remove_id = intval($_POST['remove_user_id']);
    if ($remove_id !== $_SESSION['user_id']) {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$remove_id]);
    }
}
// Get all users
$stmt = $pdo->query('SELECT id, username, email, is_admin, created_at FROM users ORDER BY created_at DESC');
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin - R.D.S Gears</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
        .admin-table th, .admin-table td { padding: 0.75rem 1rem; border: 1px solid #eee; text-align: left; }
        .admin-table th { background: #f8f9fa; }
        .admin-table tr:nth-child(even) { background: #f4f8fb; }
        .promote-btn, .demote-btn, .remove-btn { background: #27ae60; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; margin-right: 0.5rem; }
        .demote-btn { background: #f39c12; }
        .remove-btn { background: #e74c3c; }
        .promote-btn[disabled], .demote-btn[disabled], .remove-btn[disabled] { background: #bdc3c7; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Users</h1>
        <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                    <td><?php echo $user['created_at']; ?></td>
                    <td>
                        <?php if (!$user['is_admin']): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="promote_user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="promote-btn">Make Admin</button>
                        </form>
                        <?php elseif ($user['is_admin'] && $user['id'] !== $_SESSION['user_id']): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="demote_user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="demote-btn">Make User</button>
                        </form>
                        <?php else: ?>
                        <button class="demote-btn" disabled>Admin (You)</button>
                        <?php endif; ?>
                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this user?');">
                            <input type="hidden" name="remove_user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="remove-btn">Remove</button>
                        </form>
                        <?php else: ?>
                        <button class="remove-btn" disabled>Remove (You)</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 