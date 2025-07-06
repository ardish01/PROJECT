<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}
$pdo = getDBConnection();
// Get categories
$stmt = $pdo->query('SELECT id, name FROM categories ORDER BY name');
$categories = $stmt->fetchAll();
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock_quantity = $_POST['stock_quantity'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $brand = $_POST['brand'] ?? '';
    $model = $_POST['model'] ?? '';
    $image_url = '';
    // Handle image upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $img_name = $_FILES['image_file']['name'];
        $img_tmp = $_FILES['image_file']['tmp_name'];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($img_ext, $allowed)) {
            $new_name = uniqid('prod_', true) . '.' . $img_ext;
            $dest = '../assets/images/products/' . $new_name;
            if (move_uploaded_file($img_tmp, $dest)) {
                $image_url = $new_name;
            } else {
                $error = 'Failed to upload image.';
            }
        } else {
            $error = 'Invalid image file type. Only jpg, jpeg, png, gif allowed.';
        }
    }
    if (!$name || !$price || !$stock_quantity || !$category_id) {
        $error = 'Please fill in all required fields.';
    } elseif (!$image_url) {
        $error = 'Please select or upload a product image.';
    } elseif (!$error) {
        $stmt = $pdo->prepare('INSERT INTO products (name, description, price, stock_quantity, category_id, brand, model, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $description, $price, $stock_quantity, $category_id, $brand, $model, $image_url]);
        $success = 'Product added successfully!';
        header('Location: products.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin - R.D.S Gears</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-form { max-width: 600px; margin: 2rem auto; background: #fff; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); padding: 2rem; }
        .admin-form h1 { text-align: center; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: #2c3e50; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .form-group textarea { min-height: 80px; }
        .form-actions { text-align: center; }
        .btn-primary { background: #3498db; color: #fff; border: none; padding: 0.8rem 2rem; border-radius: 5px; font-size: 1.1rem; cursor: pointer; }
        .btn-primary:hover { background: #2980b9; }
        .alert { padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="admin-form">
        <h1>Add Product</h1>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="add_product.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price (NRS) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label for="stock_quantity">Stock Quantity *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" min="0" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" id="brand" name="brand">
            </div>
            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" id="model" name="model">
            </div>
            <div class="form-group">
                <label for="image_file">Product Image (jpg, jpeg, png, gif) *</label>
                <input type="file" id="image_file" name="image_file" accept="image/*" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Add Product</button>
                <a href="products.php" class="btn btn-outline" style="margin-left:1rem;">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html> 