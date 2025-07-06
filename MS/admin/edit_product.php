<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}
$pdo = getDBConnection();
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php');
    exit();
}
$product_id = intval($_GET['id']);
// Get product
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if (!$product) {
    header('Location: products.php');
    exit();
}
// Get categories
$stmt = $pdo->query('SELECT id, name FROM categories ORDER BY name');
$categories = $stmt->fetchAll();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock_quantity = $_POST['stock_quantity'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $brand = $_POST['brand'] ?? '';
    $model = $_POST['model'] ?? '';
    $image_url = $product['image_url'];
    // Handle image upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $img_name = $_FILES['image_file']['name'];
        $img_tmp = $_FILES['image_file']['tmp_name'];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($img_ext, $allowed)) {
            $new_name = uniqid('prod_', true) . '.' . $img_ext;
            $dest = '../assets/images/products/' . $new_name;
            // Ensure the directory exists
            $dir = dirname($dest);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            if (move_uploaded_file($img_tmp, $dest)) {
                // Optionally delete old image
                if (!empty($product['image_url']) && file_exists('../assets/images/products/' . $product['image_url'])) {
                    unlink('../assets/images/products/' . $product['image_url']);
                }
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
    } elseif (!$error) {
        $stmt = $pdo->prepare('UPDATE products SET name=?, description=?, price=?, stock_quantity=?, category_id=?, brand=?, model=?, image_url=? WHERE id=?');
        $stmt->execute([$name, $description, $price, $stock_quantity, $category_id, $brand, $model, $image_url, $product_id]);
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
    <title>Edit Product - Admin - R.D.S Gears</title>
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
        .product-img-preview { max-width: 120px; max-height: 120px; margin-bottom: 1rem; border-radius: 6px; border: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="admin-form">
        <h1>Edit Product</h1>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="edit_product.php?id=<?php echo $product_id; ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price (NRS) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="stock_quantity">Stock Quantity *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $product['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>">
            </div>
            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($product['model']); ?>">
            </div>
            <div class="form-group">
                <label>Current Image</label><br>
                <?php if (!empty($product['image_url'])): ?>
                    <img src="../assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" class="product-img-preview">
                <?php else: ?>
                    <span>No image uploaded.</span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="image_file">Change Image (jpg, jpeg, png, gif)</label>
                <input type="file" id="image_file" name="image_file" accept="image/*">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Update Product</button>
                <a href="products.php" class="btn btn-outline" style="margin-left:1rem;">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html> 