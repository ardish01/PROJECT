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
$page_title = "Manage Properties - Admin Dashboard";

// Handle property deletion
if (isset($_POST['delete_property'])) {
    $property_id = mysqli_real_escape_string($con, $_POST['property_id']);
    
    // First check if the property exists
    $check_query = "SELECT property_id, property_img FROM properties WHERE property_id = '$property_id'";
    $check_result = mysqli_query($con, $check_query);
    
    if ($row = mysqli_fetch_assoc($check_result)) {
        // Delete property images
        if ($row['property_img']) {
            $images = explode(',', $row['property_img']);
            foreach ($images as $image) {
                if (file_exists('../' . $image)) {
                    unlink('../' . $image);
                }
            }
        }
        
        // Delete the property
        $query = "DELETE FROM properties WHERE property_id = '$property_id'";
        if (mysqli_query($con, $query)) {
            $_SESSION['success_msg'] = "Property deleted successfully!";
        } else {
            $_SESSION['error_msg'] = "Error deleting property: " . mysqli_error($con);
        }
    } else {
        $_SESSION['error_msg'] = "Property not found!";
    }
    
    header("Location: manage-properties.php");
    exit();
}

include '../includes/nav.php';

// Fetch all properties
$query = "SELECT * FROM properties ORDER BY property_id DESC";
$result = mysqli_query($con, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $page_title; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <style>
    :root {
        --primary-color: #572a00;
        --hover-color: #562700;
        --white: #ffffff;
        --light-bg: #f8f9fa;
        --shadow: 0 2px 4px rgba(0,0,0,0.1);
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

    .panel-body {
        padding: 20px;
        background: var(--white);
        border-radius: 0 0 10px 10px;
    }

    .btn {
        border-radius: 5px;
        padding: 8px 15px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover {
        background-color: var(--hover-color);
        border-color: var(--hover-color);
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    .table {
        margin-bottom: 0;
    }

    .table > thead > tr > th {
        background-color: var(--light-bg);
        border-bottom: 2px solid var(--primary-color);
        padding: 12px 8px;
    }

    .table > tbody > tr > td {
        vertical-align: middle;
        padding: 12px 8px;
    }

    .alert {
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .properties-listing {
        padding: 20px 0;
    }

    .table-responsive {
        border-radius: 10px;
    }

    img {
        border-radius: 5px;
        margin-top: 3px;
    }

    .btn-sm {
        padding: 5px 10px;
        margin: 2px;
    }

    form {
        margin: 0;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: flex-start;
        align-items: center;
    }
    .social-links img {
    width: 20px;
    height: 20px;
    margin-top: 7px;
}
    .action-buttons .btn {
        padding: 6px 12px;
        font-size: 13px;
        line-height: 1.5;
        border-radius: 4px;
        white-space: nowrap;
        min-width: 70px;
        text-align: center;
    }
    </style>
</head>
<body>

<!-- banner -->
<div class="inside-banner">
    <div class="container">
        <h2>Manage Properties</h2>
    </div>
</div>
<!-- banner -->

<div class="container">
    <div class="properties-listing spacer">
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_msg'];
                unset($_SESSION['success_msg']);
                ?>
            </div>
        <?php endif; ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">All Properties</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($property = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $property['property_id']; ?></td>
                                    <td><?php echo htmlspecialchars($property['property_title']); ?></td>
                                    <td>Rs<?php echo number_format($property['price']); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit-property.php?id=<?php echo $property['property_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                            <form action="" method="POST" style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete this property?');">
                                                <input type="hidden" name="property_id" value="<?php echo $property['property_id']; ?>">
                                                <button type="submit" name="delete_property" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html 