<?php
session_start();
require_once 'config/database.php';
$pdo = getDBConnection();

// Get categories for footer
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

$icons = [
    'Laptops' => 'fas fa-laptop',
    'Headphones' => 'fas fa-headphones',
    'Microphones' => 'fas fa-microphone',
    'Keyboards' => 'fas fa-keyboard',
    'Mouse' => 'fas fa-mouse'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - R.D.S Gears</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .about-page {
            padding: 2rem 0;
        }
        
        .about-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }
        
        .about-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .about-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .about-content {
            padding: 4rem 0;
        }
        
        .about-section {
            margin-bottom: 4rem;
        }
        
        .about-section h2 {
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .about-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .about-card i {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 1rem;
        }
        
        .about-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .about-card p {
            color: #6c757d;
            line-height: 1.6;
        }
        
        .team-section {
            background: #f8f9fa;
            padding: 4rem 0;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .team-member {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .team-member img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
        }
        
        .team-member h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .team-member .position {
            color: #3498db;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        
        .team-member p {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .stats-section {
            background: #2c3e50;
            color: white;
            padding: 4rem 0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }
        
        .stat-item h3 {
            font-size: 2.5rem;
            color: #3498db;
            margin-bottom: 0.5rem;
        }
        
        .stat-item p {
            font-size: 1.1rem;
            opacity: 0.9;
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
                    <a href="about.php" class="nav-link active">About</a>
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

    <!-- About Hero -->
    <section class="about-hero">
        <div class="container">
            <h1>About R.D.S Gears</h1>
            <p>Your trusted partner in premium computer accessories and electronics since 2020</p>
        </div>
    </section>

    <!-- About Content -->
    <div class="about-page">
        <div class="container">
            <!-- Mission & Vision -->
            <section class="about-section">
                <h2>Our Mission & Vision</h2>
                <div class="about-grid">
                    <div class="about-card">
                        <i class="fas fa-bullseye"></i>
                        <h3>Our Mission</h3>
                        <p>To provide high-quality computer accessories and electronics that enhance productivity, gaming, and creativity for our customers worldwide.</p>
                    </div>
                    
                    <div class="about-card">
                        <i class="fas fa-eye"></i>
                        <h3>Our Vision</h3>
                        <p>To become the leading destination for tech enthusiasts, professionals, and gamers seeking premium computer accessories and exceptional customer service.</p>
                    </div>
                    
                    <div class="about-card">
                        <i class="fas fa-heart"></i>
                        <h3>Our Values</h3>
                        <p>Quality, Innovation, Customer Satisfaction, and Integrity are the core values that drive everything we do at R.D.S Gears.</p>
                    </div>
                </div>
            </section>

            <!-- What We Offer -->
            <section class="about-section">
                <h2>What We Offer</h2>
                <div class="about-grid">
                    <div class="about-card">
                        <i class="fas fa-laptop"></i>
                        <h3>Premium Laptops</h3>
                        <p>High-performance laptops for gaming, business, and everyday use from top brands.</p>
                    </div>
                    
                    <div class="about-card">
                        <i class="fas fa-headphones"></i>
                        <h3>Audio Solutions</h3>
                        <p>Professional headphones and microphones for streaming, gaming, and content creation.</p>
                    </div>
                    
                    <div class="about-card">
                        <i class="fas fa-keyboard"></i>
                        <h3>Input Devices</h3>
                        <p>Mechanical keyboards, gaming mouse, and precision input devices for optimal performance.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>

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

    <script src="assets/js/main.js"></script>
</body>
</html> 