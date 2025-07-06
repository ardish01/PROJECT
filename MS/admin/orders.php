<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}
$pdo = getDBConnection();

// Handle mark as paid
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid_order_id'])) {
    $orderId = intval($_POST['mark_paid_order_id']);
    $stmt = $pdo->prepare('UPDATE orders SET payment_reference = ?, status = ? WHERE id = ?');
    $stmt->execute(['CASH_PAID', 'completed', $orderId]);
    // Refresh to avoid resubmission
    header('Location: orders.php');
    exit();
}

// Get all orders (fetch payment_type and payment_reference too)
$stmt = $pdo->query('SELECT o.id, u.username, o.total_amount, o.status, o.order_date, o.payment_type, o.payment_reference FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC');
$orders = $stmt->fetchAll();

// Fetch order items for each order
$order_items_map = [];
if (!empty($orders)) {
    $order_ids = array_column($orders, 'id');
    $in = str_repeat('?,', count($order_ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT oi.order_id, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id IN ($in)");
    $stmt->execute($order_ids);
    foreach ($stmt->fetchAll() as $row) {
        $order_items_map[$row['order_id']][] = $row['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin - R.D.S Gears</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
        .admin-table th, .admin-table td { padding: 0.75rem 1rem; border: 1px solid #eee; text-align: left; }
        .admin-table th { background: #f8f9fa; }
        .admin-table tr:nth-child(even) { background: #f4f8fb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Orders</h1>
        <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['username'] ?? 'Guest'); ?></td>
                    <td><?php echo isset($order_items_map[$order['id']]) ? htmlspecialchars(implode(', ', $order_items_map[$order['id']])) : '-'; ?></td>
                    <td>NRS <?php echo number_format($order['total_amount'], 2); ?></td>
                    <td>
                        <?php
                        if (
                            isset($order['payment_type']) &&
                            $order['payment_type'] === 'khalti' &&
                            !empty($order['payment_reference'])
                        ) {
                            echo '<span style="color:green;font-weight:bold;">Paid</span>';
                        } elseif (
                            isset($order['payment_type']) &&
                            $order['payment_type'] === 'cod' &&
                            empty($order['payment_reference'])
                        ) {
                            // Show Mark as Paid button for COD
                            echo '<form method="POST" style="display:inline;">';
                            echo '<input type="hidden" name="mark_paid_order_id" value="' . $order['id'] . '">';
                            echo '<button type="submit" class="btn btn-sm btn-success" onclick="return confirm(\'Mark this order as paid?\')">Mark as Paid</button>';
                            echo '</form> ';
                            echo '<span style="color:orange;font-weight:bold;">Pending</span>';
                        } elseif (
                            isset($order['payment_type']) &&
                            $order['payment_type'] === 'cod' &&
                            $order['payment_reference'] === 'CASH_PAID'
                        ) {
                            echo '<span style="color:green;font-weight:bold;">Paid</span>';
                        } else {
                            echo '<span style="color:orange;font-weight:bold;">Pending</span>';
                        }
                        ?>
                    </td>
                    <td><?php echo $order['order_date']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 