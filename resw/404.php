<?php
include_once "includes/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>404 Not Found - Real Estate Management System</title>
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
                    <div class="alert alert-warning">
                        <h2>404 - Page Not Found</h2>
                        <p>The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
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