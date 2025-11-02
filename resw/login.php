<?php
session_start();
include_once "includes/connection.php";
include_once "includes/functions.php";

$error = '';
$success = '';

// Check if redirected from registration
if (isset($_GET['registration']) && $_GET['registration'] == 'success') {
    $success = "Registration successful! Please login with your credentials.";
}

// Check if form is submitted
if (isset($_POST['login'])) {
    $login = $_POST['email']; // This will accept either email or username
    $password = $_POST['password'];

    // First, get the user details
    $sql = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (int)$user['is_admin']; // Ensure it's an integer

            // Redirect based on user type
            if ($user['is_admin'] == 1) {
                header("Location: admin/admin.php");
                exit();
            } else {
                header("Location: index.php");
                exit();
            }
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login - Real Estate Management System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="assets/style.css" />
    <link rel="stylesheet" href="assets/navbar.css" />
    <style>
        .panel {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .panel-body {
            padding: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            height: 45px;
            padding: 10px 15px;
        }
        .btn-primary {
            padding: 10px 20px;
            margin-top: 10px;
            background-color: #FFE8D6;
            border-color: #FFE8D6;
        }
        .btn-primary:hover {
            background-color: #F9DFC9;
            border-color: #F9DFC9;
        }
        .checkbox {
            margin: 20px 0;
        }
        hr {
            margin: 25px 0;
        }
        /* Center row contents and add margins */
        .container1 > .row {
            display: flex;
            justify-content: center;
            margin: 50px 0;
        }
    </style>
    <script src="assets/jquery-1.9.1.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.js"></script>
    <script src="assets/script.js"></script>
</head>

<body>
<?php include 'includes/nav.php'; ?>

<!-- banner -->
<div class="inside-banner">
    <div class="container">
        <h2>Login</h2>
    </div>
</div>
<!-- banner -->

<div class="container1">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Login</h3>
                </div>
                <div class="panel-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label class="sr-only" for="email">Email or Username</label>
                            <input type="text" class="form-control" id="email" name="email"
                                placeholder="Enter email or username" required>
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password" required>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox"> Remember me
                            </label>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary">Login</button>
                    </form>
                    <hr>
                    <p>Don't have an account? <a style="color: black; border-bottom:  black 1px; " href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include 'includes/footer.php';   ?>    
</body>

</html>