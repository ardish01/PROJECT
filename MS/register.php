<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        try {
            $pdo = getDBConnection();
            
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username already exists';
            } else {
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Email already exists';
                } else {
                    // Create new user
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("
                        INSERT INTO users (username, email, password_hash, first_name, last_name) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$username, $email, $password_hash, $first_name, $last_name]);
                    
                    $success = 'Registration successful! You can now login.';
                    header("Refresh: 2; URL=login.php");
                }
            }
        } catch (Exception $e) {
            $error = 'Registration error occurred';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - R.D.S Gears</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
        }
        
        .auth-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 3rem;
            width: 100%;
            max-width: 500px;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h1 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .auth-header p {
            color: #6c757d;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .form-group input.error {
            border-color: #e74c3c;
        }
        
        .form-group .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }
        
        .auth-btn {
            width: 100%;
            padding: 1rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .auth-btn:hover {
            background: #2980b9;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }
        
        .auth-footer a {
            color: #3498db;
            text-decoration: none;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        
        .strength-weak {
            color: #e74c3c;
        }
        
        .strength-medium {
            color: #f39c12;
        }
        
        .strength-strong {
            color: #27ae60;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Create Account</h1>
                <p>Join R.D.S Gears and start shopping</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="register.php" id="registerForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" 
                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" 
                               value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    <div class="error-message" id="username-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    <div class="error-message" id="email-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required>
                    <div class="password-strength" id="password-strength"></div>
                    <div class="error-message" id="password-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <div class="error-message" id="confirm-password-error"></div>
                </div>
                
                <button type="submit" class="auth-btn">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign in here</a></p>
            </div>
        </div>
    </div>

    <script>
        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            let feedback = '';
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            if (strength < 2) {
                feedback = '<span class="strength-weak">Weak password</span>';
            } else if (strength < 4) {
                feedback = '<span class="strength-medium">Medium strength password</span>';
            } else {
                feedback = '<span class="strength-strong">Strong password</span>';
            }
            
            return feedback;
        }
        
        // Real-time password strength
        document.getElementById('password').addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            document.getElementById('password-strength').innerHTML = strength;
        });
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            let isValid = true;
            
            // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            
            // Username validation
            if (!username) {
                document.getElementById('username').classList.add('error');
                document.getElementById('username-error').textContent = 'Username is required';
                isValid = false;
            } else if (username.length < 3) {
                document.getElementById('username').classList.add('error');
                document.getElementById('username-error').textContent = 'Username must be at least 3 characters';
                isValid = false;
            }
            
            // Email validation
            if (!email) {
                document.getElementById('email').classList.add('error');
                document.getElementById('email-error').textContent = 'Email is required';
                isValid = false;
            } else if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                document.getElementById('email').classList.add('error');
                document.getElementById('email-error').textContent = 'Please enter a valid email';
                isValid = false;
            }
            
            // Password validation
            if (!password) {
                document.getElementById('password').classList.add('error');
                document.getElementById('password-error').textContent = 'Password is required';
                isValid = false;
            } else if (password.length < 6) {
                document.getElementById('password').classList.add('error');
                document.getElementById('password-error').textContent = 'Password must be at least 6 characters';
                isValid = false;
            }
            
            // Confirm password validation
            if (!confirmPassword) {
                document.getElementById('confirm_password').classList.add('error');
                document.getElementById('confirm-password-error').textContent = 'Please confirm your password';
                isValid = false;
            } else if (password !== confirmPassword) {
                document.getElementById('confirm_password').classList.add('error');
                document.getElementById('confirm-password-error').textContent = 'Passwords do not match';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        const inputs = ['username', 'email', 'password', 'confirm_password'];
        inputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
        
        function validateField(field) {
            const value = field.value.trim();
            const fieldName = field.name;
            const errorElement = document.getElementById(fieldName + '-error');
            
            if (!value) {
                field.classList.add('error');
                if (errorElement) errorElement.textContent = fieldName.replace('_', ' ') + ' is required';
                return false;
            }
            
            // Specific validations
            if (fieldName === 'email' && !value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                field.classList.add('error');
                errorElement.textContent = 'Please enter a valid email';
                return false;
            }
            
            if (fieldName === 'password' && value.length < 6) {
                field.classList.add('error');
                errorElement.textContent = 'Password must be at least 6 characters';
                return false;
            }
            
            if (fieldName === 'confirm_password') {
                const password = document.getElementById('password').value;
                if (value !== password) {
                    field.classList.add('error');
                    errorElement.textContent = 'Passwords do not match';
                    return false;
                }
            }
            
            field.classList.remove('error');
            if (errorElement) errorElement.textContent = '';
            return true;
        }
    </script>
</body>
</html> 