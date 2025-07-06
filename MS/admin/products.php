<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}
$pdo = getDBConnection();
// Get all products
$stmt = $pdo->query('SELECT id, name, price, stock_quantity, created_at FROM products ORDER BY created_at DESC');
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin - R.D.S Gears</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
        .admin-table th, .admin-table td { padding: 0.75rem 1rem; border: 1px solid #eee; text-align: left; }
        .admin-table th { background: #f8f9fa; }
        .admin-table tr:nth-child(even) { background: #f4f8fb; }
        .action-btn { background: #3498db; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; margin-right: 0.5rem; }
        .delete-btn { background: #e74c3c; }
        .add-btn { background: #27ae60; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Products</h1>
        <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
        <button class="add-btn action-btn" onclick="window.location.href='add_product.php'">Add New Product</button>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td>NRS <?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo $product['stock_quantity']; ?></td>
                    <td><?php echo $product['created_at']; ?></td>
                    <td>
                        <button class="action-btn" onclick="window.location.href='edit_product.php?id=<?php echo $product['id']; ?>'">Edit</button>
                        <button class="action-btn delete-btn" onclick="if(confirm('Delete this product?')) window.location.href='delete_product.php?id=<?php echo $product['id']; ?>'">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 