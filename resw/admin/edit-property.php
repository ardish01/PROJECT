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
$page_title = "Edit Property - Admin Dashboard";

// Get property details
if (isset($_GET['id'])) {
    $property_id = mysqli_real_escape_string($con, $_GET['id']);
    $query = "SELECT * FROM properties WHERE property_id = '$property_id'";
    $result = mysqli_query($con, $query);
    $property = mysqli_fetch_assoc($result);
    
    if (!$property) {
        header("Location: manage-properties.php");
        exit();
    }
} else {
    header("Location: manage-properties.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_title = sanitize($_POST['property_title']);
    $property_details = sanitize($_POST['property_details']);
    $price = sanitize($_POST['price']);
    $property_address = sanitize($_POST['property_address']);
    $floor_space = sanitize($_POST['floor_space']);
    $agent_id = sanitize($_POST['agent_id']);
    
    // Handle image upload
    $image_path = $property['property_img'];
    if (isset($_FILES['property_image']) && $_FILES['property_image']['size'] > 0) {
        $target_dir = "../images/properties/";
        $file_extension = strtolower(pathinfo($_FILES["property_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if file is an actual image
        if (getimagesize($_FILES["property_image"]["tmp_name"]) !== false) {
            // Delete old image if exists
            if ($property['property_img'] && file_exists('../' . $property['property_img'])) {
                unlink('../' . $property['property_img']);
            }
            
            // Upload new image
            if (move_uploaded_file($_FILES["property_image"]["tmp_name"], $target_file)) {
                $image_path = "images/properties/" . $new_filename;
            }
        }
    }
    
    // Update property
    $query = "UPDATE properties SET 
              property_title = '$property_title',
              property_details = '$property_details',
              price = '$price',
              property_address = '$property_address',
              floor_space = '$floor_space',
              agent_id = '$agent_id',
              property_img = '$image_path'
              WHERE property_id = '$property_id'";
              
    if (mysqli_query($con, $query)) {
        $_SESSION['success_msg'] = "Property updated successfully!";
        header("Location: manage-properties.php");
        exit();
    }
}

// Get all agents for the dropdown
$agents_query = "SELECT agent_id, agent_name FROM agent";
$agents_result = mysqli_query($con, $agents_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="../assets/style.css" />
    <link rel="stylesheet" href="../assets/navbar.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <style>
    :root {
        --primary-color: #563207;
        --hover-color: #3E2405;
        --white: #ffffff;
        --light-bg: #f8f9fa;
        --shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .navbar {
        min-height: 50px;
    }

    .navbar-brand {
        padding: 0 15px;
        height: 50px;
        line-height: 50px;
    }

    .navbar-brand img {
        display: inline-block;
        vertical-align: middle;
    }

    .navbar-nav>li>a {
        line-height: 50px;
        padding-top: 0;
        padding-bottom: 0;
    }

    @media (max-width: 767px) {
        .navbar-nav>li>a {
            line-height: normal;
            padding-top: 10px;
            padding-bottom: 10px;
        }
    }

    img {
        margin-top: 3px;
    }

    .inside-banner {
        background-color: var(--primary-color);
        color: var(--white);
        padding: 40px 0;
        margin-bottom: 40px;
    }

    .inside-banner h2 {
        margin: 0;
        color: var(--white);
    }

    .panel {
        border-radius: 10px;
        box-shadow: var(--shadow);
        border: none;
        margin-bottom: 30px;
    }

    .panel-heading {
        background-color: var(--primary-color) !important;
        color: var(--white) !important;
        border-radius: 10px 10px 0 0;
        padding: 15px 20px;
    }

    .panel-title {
        margin: 0;
        font-size: 18px;
        font-weight: 500;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 8px 12px;
        height: auto;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(87, 42, 0, 0.25);
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        padding: 8px 20px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: var(--hover-color);
        border-color: var(--hover-color);
    }

    .alert {
        border-radius: 4px;
        padding: 12px 20px;
        margin-bottom: 20px;
        border: none;
        box-shadow: var(--shadow);
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    </style>
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container">
    <div class="properties-listing spacer">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Edit Property Details</h3>
            </div>
            <div class="panel-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Property Title</label>
                                <input type="text" name="property_title" class="form-control" value="<?php echo htmlspecialchars($property['property_title']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Price</label>
                                <input type="number" name="price" class="form-control" value="<?php echo $property['price']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Property Address</label>
                                <input type="text" name="property_address" class="form-control" value="<?php echo htmlspecialchars($property['property_address']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Floor Space</label>
                                <input type="text" name="floor_space" class="form-control" value="<?php echo htmlspecialchars($property['floor_space']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Assign Agent</label>
                                <select name="agent_id" class="form-control" required>
                                    <option value="">Select Agent</option>
                                    <?php while($agent = mysqli_fetch_assoc($agents_result)): ?>
                                        <option value="<?php echo $agent['agent_id']; ?>" 
                                            <?php echo $property['agent_id'] == $agent['agent_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($agent['agent_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Property Details</label>
                                <textarea name="property_details" class="form-control" rows="5" required><?php echo htmlspecialchars($property['property_details']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Property Image</label>
                                <input type="file" name="property_image" class="form-control" accept="image/*">
                                <?php if ($property['property_img']): ?>
                                    <p class="help-block">Current image: 
                                        <img src="<?php echo '../' . $property['property_img']; ?>" 
                                             alt="Current Property Image"
                                             style="max-width: 200px; max-height: 150px; margin-top: 10px;">
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Property</button>
                        <a href="manage-properties.php" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html> 