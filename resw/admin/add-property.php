<?php
session_start();
include_once "../includes/connection.php";
include_once "../includes/functions.php";

// Check if user is admin


if (!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

$isSubDirectory = true;
$page_title = "Add Property - Real Estate Management System";

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $property_title = sanitize($_POST['property_title']);
    $property_details = sanitize($_POST['property_details']);
    $price = floatval($_POST['price']);
    $property_address = sanitize($_POST['property_address']);
    $floor_space = sanitize($_POST['floor_space']);
    $agent_id = intval($_POST['agent_id']);
    $user_id = $_SESSION['user_id']; // Get the current user's ID from session

    // Handle main property image upload
    $target_dir = "../images/properties/";
    $property_images = array();
    
    // Handle multiple image uploads
    if (isset($_FILES["property_images"]) && !empty($_FILES["property_images"]["name"][0])) {
        foreach ($_FILES["property_images"]["tmp_name"] as $key => $tmp_name) {
            if ($_FILES["property_images"]["error"][$key] == 0) {
                $image_path = "images/properties/" . basename($_FILES["property_images"]["name"][$key]);
                if (move_uploaded_file($tmp_name, "../" . $image_path)) {
                    $property_images[] = $image_path;
                }
            }
        }
    }

    // Combine all image paths with commas
    $property_img = implode(',', $property_images);

    // Validate numeric fields
    $errors = array();

    if ($price <= 0) {
        $errors[] = "Price must be greater than 0";
    }

    // If there are no errors, proceed with the insert
    if (empty($errors)) {
        // Insert into properties table
        $query = "INSERT INTO properties (property_title, property_details, price, property_address, property_img, floor_space, agent_id, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $con->prepare($query);
        $stmt->bind_param(
            "ssdsssii",
            $property_title,
            $property_details,
            $price,
            $property_address,
            $property_img,
            $floor_space,
            $agent_id,
            $user_id
        );

        if ($stmt->execute()) {
            $message = "Property added successfully!";
        } else {
            $message = "Error adding property: " . $con->error;
        }
    } else {
        // Display errors
        $message = "<div class='alert alert-danger'><ul>";
        foreach ($errors as $error) {
            $message .= "<li>$error</li>";
        }
        $message .= "</ul></div>";
    }
}

// Get agents for dropdown
$agents_query = "SELECT agent_id, agent_name FROM agent";
$agents_result = mysqli_query($con, $agents_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Property - Real Estate Management System</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="../assets/style.css" />
    <link rel="stylesheet" href="../assets/navbar.css" />
    <style>
        .btn-primary {
            background-color: #563207;
            border-color: #563207;
        }
        
        .btn-primary:hover {
            background-color: #3E2405;
            border-color: #3E2405;
        }
        
        img {
            margin-top: 3px;
        }

        /* More specific selectors to avoid conflicts */
        .property-form .form-group {
            margin-bottom: 25px;
        }
        .row{
            margin-top: 10px;
        }
        .property-form .control-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
        }

        .property-form .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .property-form textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .property-form .button-group {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }

        .property-form .button-group .btn {
            padding: 8px 20px;
        }

        /* Add spacing between columns */
        @media (min-width: 768px) {
            .property-form .right-column {
                padding-left: 30px;
            }
        }

        .property-form .help-block {
            color: #666;
            margin-top: 5px;
            font-size: 13px;
        }

        .property-form input[type="file"] {
            padding: 3px;
        }
    </style>
    <script src="../assets/jquery-1.9.1.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.js"></script>
    <script src="../assets/script.js"></script>

    <!-- Owl stylesheet -->
    <link rel="stylesheet" href="../assets/owl-carousel/owl.carousel.css">
    <link rel="stylesheet" href="../assets/owl-carousel/owl.theme.css">
    <script src="../assets/owl-carousel/owl.carousel.js"></script>
    <!-- Owl stylesheet -->

    <!-- slitslider -->
    <link rel="stylesheet" type="text/css" href="../assets/slitslider/css/style.css" />
    <link rel="stylesheet" type="text/css" href="../assets/slitslider/css/custom.css" />
    <script type="text/javascript" src="../assets/slitslider/js/modernizr.custom.79639.js"></script>
    <script type="text/javascript" src="../assets/slitslider/js/jquery.ba-cond.min.js"></script>
    <script type="text/javascript" src="../assets/slitslider/js/jquery.slitslider.js"></script>
</head>

<body>
    <?php include '../includes/nav.php'; ?>

    <!-- banner -->
    <div class="inside-banner">
        <div class="container">
            <h2>Add New Property</h2>
        </div>
    </div>
    <!-- banner -->

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" action="" enctype="multipart/form-data" class="property-form">
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Property Title</label>
                                        <input type="text" name="property_title" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Property Details</label>
                                        <textarea name="property_details" class="form-control" required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Price</label>
                                        <input type="number" name="price" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Property Address</label>
                                        <input type="text" name="property_address" class="form-control" required>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6 right-column">
                                    <div class="form-group">
                                        <label class="control-label">Floor Space</label>
                                        <input type="text" name="floor_space" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Agent</label>
                                        <select name="agent_id" class="form-control" required>
                                            <option value="">Select Agent</option>
                                            <?php while ($agent = mysqli_fetch_assoc($agents_result)): ?>
                                                <option value="<?php echo $agent['agent_id']; ?>">
                                                    <?php echo $agent['agent_name']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Property Images</label>
                                        <input type="file" name="property_images[]" class="form-control" multiple required>
                                        <p class="help-block">You can select multiple images</p>
                                    </div>

                                    <div class="button-group">
                                        <button type="submit" class="btn btn-primary">Add Property</button>
                                        <a href="admin.php" class="btn btn-default">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <!-- Add JavaScript validation -->
    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            const price = parseFloat(document.querySelector('input[name="price"]').value);
            
            let errors = [];

            if (price <= 0) {
                errors.push("Price must be greater than 0");
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert(errors.join("\n"));
            }
        });
    </script>
</body>

</html>