<?php
session_start();
include_once "includes/connection.php";
include_once "includes/functions.php";

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = mysqli_real_escape_string($con, $_POST['full_name']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);

    // Validate password match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if username or email already exists
        $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "Username or email already exists!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $query = "INSERT INTO users (username, email, password, full_name, phone) 
                     VALUES ('$username', '$email', '$hashed_password', '$full_name', '$phone')";

            if (mysqli_query($con, $query)) {
                // Redirect to login page after successful registration
                header("Location: login.php?registration=success");
                exit;
            } else {
                $error = "Error: " . mysqli_error($con);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register - Real Estate Management System</title>
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
            padding: 35px;  /* Slightly more padding for the registration form */
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            height: 45px;
            padding: 10px 15px;
        }
        .btn-primary {
            padding: 12px 25px;  /* Slightly larger button */
            margin-top: 15px;
            width: 100%;  /* Full width button */
            background-color: #FFE8D6;
            border-color: #FFE8D6;
        }
        .btn-primary:hover {
            background-color: #F9DFC9;
            border-color: #F9DFC9;
        }
        label {
            margin-bottom: 8px;
            font-weight: 500;
        }
        hr {
            margin: 25px 0;
        }
        /* Center row contents and add margins */
        .container > .row {
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
        <h2>Register</h2>
    </div>
</div>
<!-- banner -->

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Register</h3>
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
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password"
                                name="confirm_password" required>
                        </div>
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </form>
                    <hr>
                    <p>Already have an account? <a style="color: black; border-color: black; border-bottom: 1px" href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php';   ?>    
</body>

</html>