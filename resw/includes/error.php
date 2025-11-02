<?php
// Log the error
error_log("Error: " . $_SERVER['REQUEST_URI']);

// Display a user-friendly error message
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Error - Real Estate Management System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?php echo get_full_url('assets/bootstrap/css/bootstrap.css'); ?>" />
    <link rel="stylesheet" href="<?php echo get_full_url('assets/style.css'); ?>" />
</head>
<body>
    <div class="container">
        <div class="spacer">
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-danger">
                        <h2>Oops! Something went wrong.</h2>
                        <p>We're sorry, but there was an error processing your request.</p>
                        <p>Please try again later or contact the administrator if the problem persists.</p>
                        <a href="<?php echo get_full_url('index.php'); ?>" class="btn btn-primary">Return to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
exit;
?> 