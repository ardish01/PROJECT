<?php
session_start();
include_once __DIR__ . "/../includes/connection.php";
include_once __DIR__ . "/../includes/functions.php";

// Check if user is logged in first
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

// Check if user is admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

$isSubDirectory = true;
$page_title = "Admin Dashboard - Real Estate Management System";

// Get total properties
$query = "SELECT COUNT(*) as total FROM properties";
$result = mysqli_query($con, $query);
$total_properties = mysqli_fetch_assoc($result)['total'];

// Get total agents
$query = "SELECT COUNT(*) as total FROM agent";
$result = mysqli_query($con, $query);
$total_agents = mysqli_fetch_assoc($result)['total'];

// Get total users
$query = "SELECT COUNT(*) as total FROM users";
$result = mysqli_query($con, $query);
$total_users = mysqli_fetch_assoc($result)['total'];

// Set base path for admin section
$base_path = '../';

include __DIR__ . '/../includes/nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $page_title; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/style.css" />
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/navbar.css" />
    
    <style>
    :root {
        --primary-color: #563207;
        --hover-color: #3E2405;
        --white: #ffffff;
        --light-bg: #f8f9fa;
        --shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .dashboard-container {
        padding: 2rem 0;
        background-color: var(--light-bg);
    }

    .stats-card {
        background: var(--white);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow);
    }

    .action-card {
        background: var(--white);
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        text-align: center;
        box-shadow: var(--shadow);
    }

    .action-card:hover {
        box-shadow: var(--shadow);
    }

    .action-icon {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .action-title {
        color: #333;
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .action-description {
        color: #666;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .btn-action {
        background-color: var(--primary-color);
        color: var(--white);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s ease;
    }

    .btn-action:hover {
        background-color: var(--hover-color);
        color: var(--white);
    }

    .section-title {
        color: #333;
        margin-bottom: 2rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color);
    }

    /* Panel Styles */
    .panel {
        border: none;
        box-shadow: var(--shadow);
    }

    .panel-heading {
        background-color: var(--primary-color) !important;
        color: var(--white) !important;
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem !important;
    }

    .panel-primary {
        border-color: var(--primary-color);
    }

    .panel-green {
        border-color: #5cb85c;
    }

    .panel-yellow {
        border-color: #f0ad4e;
    }

    .panel-primary .panel-heading {
        background-color: var(--primary-color) !important;
    }

    .panel-green .panel-heading {
        background-color: #5cb85c !important;
    }

    .panel-yellow .panel-heading {
        background-color: #f0ad4e !important;
    }

    .huge {
        font-size: 2.5rem;
        font-weight: bold;
    }

    .dashboard-number {
        font-size: 3.5rem;
        font-weight: bold;
        color: var(--primary-color);
        margin: 1rem 0;
    }
    .social-links img {
    width: 20px;
    height: 20px;
    margin-top: 7px;
}
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .stats-card, .action-card {
            margin-bottom: 1rem;
        }
        
        .huge {
            font-size: 2rem;
        }
    }
    </style>
</head>
<body>

<!-- banner -->
<div class="inside-banner">
    <div class="container">
        <h2>Admin Dashboard</h2>
    </div>
</div>
<!-- banner -->

<div class="dashboard-container">
    <div class="container">
        <!-- Statistics Section -->
        <h3 class="section-title">Dashboard Overview</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="action-card">
                    <i class="fas fa-home action-icon"></i>
                    <h4 class="action-title">Total Properties</h4>
                    <div class="dashboard-number"><?php echo $total_properties; ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="action-card">
                    <i class="fas fa-users action-icon"></i>
                    <h4 class="action-title">Total Agents</h4>
                    <div class="dashboard-number"><?php echo $total_agents; ?></div>
                </div>
            </div>
        </div>

        <!-- Property Actions Section -->
        <h3 class="section-title">Property Management</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="action-card">
                    <i class="fas fa-plus-circle action-icon"></i>
                    <h4 class="action-title">Add Property</h4>
                    <p class="action-description">Add a new property listing to the system</p>
                    <a href="add-property.php" class="btn-action">Add New Property</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="action-card">
                    <i class="fas fa-tasks action-icon"></i>
                    <h4 class="action-title">Manage Properties</h4>
                    <p class="action-description">Edit or delete existing property listings</p>
                    <a href="manage-properties.php" class="btn-action">Manage Properties</a>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <h3 class="section-title">Quick Actions</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="action-card">
                    <i class="fas fa-user-tie action-icon"></i>
                    <h4 class="action-title">Manage Agents</h4>
                    <p class="action-description">Add, edit, or remove agents from the system</p>
                    <a href="manage-agents.php" class="btn-action">Manage Agents</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="action-card">
                    <i class="fas fa-calendar-check action-icon"></i>
                    <h4 class="action-title">Manage Bookings</h4>
                    <p class="action-description">View and manage property viewing bookings</p>
                    <a href="manage-bookings.php" class="btn-action">Manage Bookings</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include '../includes/footer.php';   ?>    
</body>
</html> 