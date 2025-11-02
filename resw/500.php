<?php
include_once "includes/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>500 Internal Server Error - Real Estate Management System</title>
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
                        <h2>500 - Internal Server Error</h2>
                        <p>Sorry, something went wrong on our end. Our team has been notified and is working to fix the issue.</p>
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