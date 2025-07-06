<?php
session_start();
require_once 'config/database.php';
$pdo = getDBConnection();

// Get cart items
$session_id = $_SESSION['session_id'] ?? uniqid();
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = $session_id;
}

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - R.D.S Gears</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .cart-page {
            padding: 2rem 0;
        }
        
        .cart-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        
        .cart-items {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .cart-item-info h3 {
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        
        .cart-item-price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .quantity-btn {
            background: #f8f9fa;
            border: 1px solid #ddd;
            width: 30px;
            height: 30px;
            border-radius: 3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-btn:hover {
            background: #e9ecef;
        }
        
        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 0.25rem;
        }
        
        .remove-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .remove-btn:hover {
            background: #c0392b;
        }
        
        .cart-summary {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
        }
        
        .summary-row.total {
            border-top: 2px solid #eee;
            padding-top: 1rem;
            font-weight: bold;
            font-size: 1.2rem;
            color: #e74c3c;
        }
        
        .checkout-btn {
            width: 100%;
            padding: 1rem;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .checkout-btn:hover {
            background: #229954;
        }
        
        .checkout-btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }
        
        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-cart i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1rem;
        }
        
        .continue-shopping {
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .cart-container {
                grid-template-columns: 1fr;
            }
            
            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 1rem;
            }
            
            .cart-item-image {
                width: 80px;
                height: 80px;
            }
            
            .quantity-controls,
            .remove-btn {
                grid-column: 1 / -1;
                justify-self: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <a href="index.php">
                        <img src="assets/images/Screenshot 2025-06-27 170834.png" alt="Logo" style="height:40px;vertical-align:middle;margin-right:10px;">
                        <span>R.D.S Gears</span>
                    </a>
                </div>
                
                <div class="nav-menu">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="products.php" class="nav-link">Products</a>
                    <a href="about.php" class="nav-link">About</a>
                    <a href="contact.php" class="nav-link">Contact</a>
                </div>
                
                <div class="nav-actions">
                    <div class="search-box">
                        <input type="text" placeholder="Search products..." id="searchInput">
                        <button type="button" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <div class="user-actions">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <span class="nav-link">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            </span>
                            <a href="logout.php" class="nav-link">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="nav-link">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                            <a href="register.php" class="nav-link">
                                <i class="fas fa-user-plus"></i>
                                <span>Register</span>
                            </a>
                        <?php endif; ?>
                        
                        <a href="cart.php" class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count" id="cartCount"><?php echo array_sum(array_column($cartItems, 'quantity')); ?></span>
                        </a>
                    </div>
                </div>
                
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Cart Page -->
    <div class="cart-page">
        <div class="container">
            <h1>Shopping Cart</h1>
            
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added any products to your cart yet.</p>
                    <div class="continue-shopping">
                        <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="cart-container">
                    <!-- Cart Items -->
                    <div class="cart-items">
                        <h2>Cart Items (<?php echo array_sum(array_column($cartItems, 'quantity')); ?>)</h2>
                        
                        <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item" data-product-id="<?php echo $item['product_id']; ?>">
                            <img src="assets/images/products/<?php echo htmlspecialchars($item['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 class="cart-item-image">
                            
                            <div class="cart-item-info">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <div class="cart-item-price">NRS <?php echo number_format($item['price'], 2); ?></div>
                                <?php if ($item['stock_quantity'] < $item['quantity']): ?>
                                    <div style="color: #e74c3c; font-size: 0.9rem;">
                                        Only <?php echo $item['stock_quantity']; ?> available
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, -1)">-</button>
                                <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" 
                                       min="1" max="<?php echo $item['stock_quantity']; ?>"
                                       onchange="updateQuantity(<?php echo $item['product_id']; ?>, this.value - <?php echo $item['quantity']; ?>)">
                                <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, 1)">+</button>
                            </div>
                            
                            <div class="cart-item-actions">
                                <div class="cart-item-subtotal">NRS <?php echo number_format($item['subtotal'], 2); ?></div>
                                <button class="remove-btn" onclick="removeFromCart(<?php echo $item['product_id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Cart Summary -->
                    <div class="cart-summary">
                        <h2>Order Summary</h2>
                        
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>NRS <?php echo number_format($total, 2); ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span><?php echo $total >= 50 ? 'Free' : 'NRS 5.99'; ?></span>
                        </div>
                        
                        <?php if ($total < 50): ?>
                        <div class="summary-row" style="color: #27ae60; font-size: 0.9rem;">
                            <span>Add NRS <?php echo number_format(50 - $total, 2); ?> more for free shipping</span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span>NRS <?php echo number_format($total + ($total >= 50 ? 0 : 5.99), 2); ?></span>
                        </div>
                        
                        <button class="checkout-btn" onclick="proceedToCheckout()" 
                                <?php echo !isset($_SESSION['user_id']) ? 'disabled' : ''; ?>>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                Proceed to Checkout
                            <?php else: ?>
                                Login to Checkout
                            <?php endif; ?>
                        </button>
                        
                        <?php if (!isset($_SESSION['user_id'])): ?>
                        <div style="text-align: center; margin-top: 1rem;">
                            <a href="login.php" class="btn btn-outline">Login</a>
                            <a href="register.php" class="btn btn-primary">Register</a>
                        </div>
                        <?php endif; ?>
                        
                        <div style="margin-top: 2rem;">
                            <a href="products.php" class="btn btn-outline" style="width: 100%;">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        function updateQuantity(productId, change) {
            const input = document.querySelector(`[data-product-id="${productId}"] .quantity-input`);
            const currentQuantity = parseInt(input.value);
            const newQuantity = currentQuantity + change;
            
            if (newQuantity < 1) {
                return;
            }
            
            fetch('api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update',
                    product_id: productId,
                    quantity: newQuantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showNotification(data.message || 'Error updating quantity', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error updating quantity', 'error');
            });
        }
        
        function removeFromCart(productId) {
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }
            
            fetch('api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'remove',
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showNotification(data.message || 'Error removing item', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error removing item', 'error');
            });
        }
        
        function proceedToCheckout() {
            <?php if (isset($_SESSION['user_id'])): ?>
                window.location.href = 'checkout.php';
            <?php else: ?>
                window.location.href = 'login.php';
            <?php endif; ?>
        }
    </script>
</body>
</html> 