<?php
session_start();
require_once 'config/database.php';
$pdo = getDBConnection();

// Get featured products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     ORDER BY p.id DESC LIMIT 8");
$featured_products = $stmt->fetchAll();

// Get categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>R.D.S Gears - Your Computer Accessories Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                    <a href="index.php" class="nav-link active">Home</a>
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
                            <?php if (!empty($_SESSION['is_admin'])): ?>
                                <a href="admin/dashboard.php" class="nav-link btn btn-outline" style="margin-right: 1rem;">
                                    <i class="fas fa-tools"></i> Admin Dashboard
                                </a>
                            <?php endif; ?>
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
                            <span class="cart-count" id="cartCount">0</span>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to R.D.S Gears</h1>
            <p>Your one-stop shop for premium computer accessories and electronics</p>
            <div class="hero-buttons">
                <a href="products.php" class="btn btn-primary">Shop Now</a>
                <a href="#featured" class="btn btn-secondary">View Featured</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="assets/images/hero.png" alt="R.D.S Gears Hero">
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h2>Shop by Category</h2>
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <div class="category-icon">
                        <?php
                        $icons = [
                            'Laptops' => 'fas fa-laptop',
                            'Headphones' => 'fas fa-headphones',
                            'Microphones' => 'fas fa-microphone',
                            'Keyboards' => 'fas fa-keyboard',
                            'Mouse' => 'fas fa-mouse'
                        ];
                        $icon = $icons[$category['name']] ?? 'fas fa-box';
                        ?>
                        <i class="<?php echo $icon; ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p><?php echo htmlspecialchars($category['description']); ?></p>
                    <a href="products.php?category=<?php echo $category['id']; ?>" class="btn btn-outline">
                        Browse <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products" id="featured">
        <div class="container">
            <h2>Featured Products</h2>
            <div class="products-grid">
                <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="product-overlay">
                            <button class="btn-quick-view" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-add-cart" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <div class="product-price">
                            <span class="price">NRS <?php echo number_format($product['price'], 2); ?></span>
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <span class="stock in-stock">In Stock</span>
                            <?php else: ?>
                                <span class="stock out-of-stock">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <button class="btn btn-primary btn-sm add-to-cart" 
                                    data-product-id="<?php echo $product['id']; ?>"
                                    <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center">
                <a href="products.php" class="btn btn-primary">View All Products</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature">
                    <i class="fas fa-shipping-fast"></i>
                    <h3>Free Shipping</h3>
                    <p>Free shipping on orders over NRS 50</p>
                </div>
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Secure Payment</h3>
                    <p>100% secure payment processing</p>
                </div>
                <div class="feature">
                    <i class="fas fa-undo"></i>
                    <h3>Easy Returns</h3>
                    <p>30-day return policy</p>
                </div>
                <div class="feature">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Round the clock customer support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>R.D.S Gears</h3>
                    <p>Your trusted source for premium computer accessories and electronics.</p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Categories</h4>
                    <ul>
                        <?php foreach ($categories as $category): ?>
                        <li><a href="products.php?category=<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p><i class="fas fa-map-marker-alt"></i> Mahalaxmi, Bhaktapur</p>
                    <p><i class="fas fa-phone"></i> +9779800000000</p>
                    <p><i class="fas fa-envelope"></i> info@rdsgears.com</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 R.D.S Gears. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="quickViewContent"></div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html> 