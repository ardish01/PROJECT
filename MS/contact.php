<?php
session_start();
require_once 'config/database.php';
$pdo = getDBConnection();

// Get categories for footer
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        try {
            // Save message to database
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            
            $success = 'Thank you for your message! We will get back to you soon.';
            
            // Clear form data
            $_POST = array();
        } catch (PDOException $e) {
            $error = 'Sorry, there was an error sending your message. Please try again later.';
        }
    }
}

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
    <title>Contact Us - R.D.S Gears</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .contact-page {
            padding: 2rem 0;
        }
        
        .contact-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }
        
        .contact-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .contact-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .contact-content {
            padding: 4rem 0;
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            margin-bottom: 4rem;
        }
        
        .contact-info {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .contact-info h2 {
            color: #2c3e50;
            margin-bottom: 2rem;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .contact-item i {
            font-size: 1.5rem;
            color: #3498db;
            margin-right: 1rem;
            width: 30px;
            text-align: center;
        }
        
        .contact-item-content h3 {
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        
        .contact-item-content p {
            color: #6c757d;
            margin: 0;
        }
        
        .contact-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .contact-form h2 {
            color: #2c3e50;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .submit-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .submit-btn:hover {
            background: #2980b9;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .faq-section {
            background: #f8f9fa;
            padding: 4rem 0;
        }
        
        .faq-section h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 3rem;
        }
        
        .faq-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }
        
        .faq-item {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .faq-item h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .faq-item h3 i {
            color: #3498db;
            margin-right: 0.5rem;
        }
        
        .faq-item p {
            color: #6c757d;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .faq-grid {
                grid-template-columns: 1fr;
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
                    <a href="contact.php" class="nav-link active">Contact</a>
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

    <!-- Contact Hero -->
    <section class="contact-hero">
        <div class="container">
            <h1>Contact Us</h1>
            <p>We're here to help! Get in touch with our team for any questions or support.</p>
        </div>
    </section>

    <!-- Contact Content -->
    <div class="contact-page">
        <div class="container">
            <div class="contact-content">
                <div class="contact-grid">
                    <!-- Contact Information -->
                    <div class="contact-info">
                        <h2>Get in Touch</h2>
                        
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="contact-item-content">
                                <h3>Address</h3>
                                <p>Mahalaxmi, Bhaktapur</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div class="contact-item-content">
                                <h3>Phone</h3>
                                <p>+9779800000000</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div class="contact-item-content">
                                <h3>Email</h3>
                                <p>info@rdsgears.com<br>support@rdsgears.com</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <div class="contact-item-content">
                                <h3>Business Hours</h3>
                                <p>Sunday To Friday - 9AM to 9PM<br>Saturday - Closed</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Form -->
                    <div class="contact-form">
                        <h2>Send us a Message</h2>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-error">
                                <i class="fas fa-exclamation-circle"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="contact.php" id="contactForm">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" required 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Subject *</label>
                                <select id="subject" name="subject" required>
                                    <option value="">Select a subject</option>
                                    <option value="General Inquiry" <?php echo ($_POST['subject'] ?? '') === 'General Inquiry' ? 'selected' : ''; ?>>General Inquiry</option>
                                    <option value="Product Support" <?php echo ($_POST['subject'] ?? '') === 'Product Support' ? 'selected' : ''; ?>>Product Support</option>
                                    <option value="Order Status" <?php echo ($_POST['subject'] ?? '') === 'Order Status' ? 'selected' : ''; ?>>Order Status</option>
                                    <option value="Returns & Refunds" <?php echo ($_POST['subject'] ?? '') === 'Returns & Refunds' ? 'selected' : ''; ?>>Returns & Refunds</option>
                                    <option value="Technical Support" <?php echo ($_POST['subject'] ?? '') === 'Technical Support' ? 'selected' : ''; ?>>Technical Support</option>
                                    <option value="Partnership" <?php echo ($_POST['subject'] ?? '') === 'Partnership' ? 'selected' : ''; ?>>Partnership</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Message *</label>
                                <textarea id="message" name="message" required placeholder="Please describe your inquiry in detail..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            </div>
                            
                            <button type="submit" class="submit-btn">
                                <i class="fas fa-paper-plane"></i>
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
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
                    <p><i class="fas fa-clock"></i> Sunday To Friday - 9AM to 9PM, Saturday - Closed</p>
                    <p><i class="fas fa-envelope"></i> info@rdsgears.com</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 R.D.S Gears. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script>
        // Form validation
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value.trim();
            
            let isValid = true;
            
            // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
            
            if (!name) {
                document.getElementById('name').classList.add('error');
                isValid = false;
            }
            
            if (!email) {
                document.getElementById('email').classList.add('error');
                isValid = false;
            } else if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                document.getElementById('email').classList.add('error');
                isValid = false;
            }
            
            if (!subject) {
                document.getElementById('subject').classList.add('error');
                isValid = false;
            }
            
            if (!message) {
                document.getElementById('message').classList.add('error');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields correctly');
            }
        });
    </script>
</body>
</html> 