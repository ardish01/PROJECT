<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php');
    exit();
}
$pdo = getDBConnection();
$product_id = intval($_GET['id']);
// Get image filename
$stmt = $pdo->prepare('SELECT image_url FROM products WHERE id = ?');
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if ($product) {
    // Delete product
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$product_id]);
    // Delete image file if exists and not empty
    if (!empty($product['image_url'])) {
        $img_path = '../assets/images/products/' . $product['image_url'];
        if (file_exists($img_path)) {
            unlink($img_path);
        }
    }
}
header('Location: products.php');
exit(); 