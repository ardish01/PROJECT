<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

$pdo = getDBConnection();

// Generate session ID if not exists
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid();
}

$session_id = $_SESSION['session_id'];

// Handle different actions
$action = $_GET['action'] ?? $_POST['action'] ?? '';
if (!$action) {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
}

switch ($action) {
    case 'add':
        handleAddToCart();
        break;
    case 'remove':
        handleRemoveFromCart();
        break;
    case 'update':
        handleUpdateCart();
        break;
    case 'count':
        handleGetCartCount();
        break;
    case 'get':
        handleGetCart();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function handleAddToCart() {
    global $pdo, $session_id;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $product_id = $input['product_id'] ?? null;
    $quantity = $input['quantity'] ?? 1;
    
    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        return;
    }
    
    try {
        // Check if product exists and has stock
        $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            return;
        }
        
        if ($product['stock_quantity'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
            return;
        }
        
        // Check if item already in cart
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$session_id, $product_id]);
        $cartItem = $stmt->fetch();
        
        if ($cartItem) {
            // Update quantity
            $newQuantity = $cartItem['quantity'] + $quantity;
            if ($newQuantity > $product['stock_quantity']) {
                echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                return;
            }
            
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->execute([$newQuantity, $cartItem['id']]);
        } else {
            // Add new item
            $stmt = $pdo->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$session_id, $product_id, $quantity]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Product added to cart']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error adding to cart']);
    }
}

function handleRemoveFromCart() {
    global $pdo, $session_id;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $product_id = $input['product_id'] ?? null;
    
    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$session_id, $product_id]);
        
        echo json_encode(['success' => true, 'message' => 'Product removed from cart']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error removing from cart']);
    }
}

function handleUpdateCart() {
    global $pdo, $session_id;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $product_id = $input['product_id'] ?? null;
    $quantity = $input['quantity'] ?? 1;
    
    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        return;
    }
    
    if ($quantity <= 0) {
        // Remove item if quantity is 0 or negative
        handleRemoveFromCart();
        return;
    }
    
    try {
        // Check stock availability
        $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            return;
        }
        
        if ($product['stock_quantity'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
            return;
        }
        
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$quantity, $session_id, $product_id]);
        
        echo json_encode(['success' => true, 'message' => 'Cart updated']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error updating cart']);
    }
}

function handleGetCartCount() {
    global $pdo, $session_id;
    
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart WHERE session_id = ?");
        $stmt->execute([$session_id]);
        $result = $stmt->fetch();
        
        echo json_encode(['success' => true, 'count' => $result['count'] ?? 0]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'count' => 0]);
    }
}

function handleGetCart() {
    global $pdo, $session_id;
    
    try {
        $stmt = $pdo->prepare("
            SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.image_url, p.stock_quantity
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.session_id = ?
        ");
        $stmt->execute([$session_id]);
        $cartItems = $stmt->fetchAll();
        
        $total = 0;
        foreach ($cartItems as &$item) {
            $item['subtotal'] = $item['price'] * $item['quantity'];
            $total += $item['subtotal'];
        }
        
        echo json_encode([
            'success' => true,
            'items' => $cartItems,
            'total' => $total,
            'count' => array_sum(array_column($cartItems, 'quantity'))
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error getting cart']);
    }
}
?> 