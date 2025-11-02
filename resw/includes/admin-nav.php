<?php
// Get the base URL
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$admin_base = $base_url . "/resw-main/admin";

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="admin-navigation">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="admin-nav-buttons">
                    <a href="<?php echo $admin_base; ?>/admin.php" 
                       class="btn btn-primary <?php echo $current_page === 'admin.php' ? 'active' : ''; ?>">
                        Dashboard
                    </a>
                    <a href="<?php echo $admin_base; ?>/manage-properties.php" 
                       class="btn btn-info <?php echo $current_page === 'manage-properties.php' ? 'active' : ''; ?>">
                        Manage Properties
                    </a>
                    <a href="<?php echo $admin_base; ?>/manage-bookings.php" 
                       class="btn btn-info <?php echo $current_page === 'manage-bookings.php' ? 'active' : ''; ?>">
                        Manage Bookings
                    </a>
                    <a href="<?php echo $admin_base; ?>/manage-users.php" 
                       class="btn btn-info <?php echo $current_page === 'manage-users.php' ? 'active' : ''; ?>">
                        Manage Users
                    </a>
                    <a href="<?php echo $admin_base; ?>/manage-agents.php" 
                       class="btn btn-info <?php echo $current_page === 'manage-agents.php' ? 'active' : ''; ?>">
                        Manage Agents
                    </a>
                    <a href="<?php echo $admin_base; ?>/messages.php" 
                       class="btn btn-info <?php echo $current_page === 'messages.php' ? 'active' : ''; ?>">
                        Messages
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-navigation {
    background-color: #f8f9fa;
    padding: 15px 0;
    margin-bottom: 20px;
    border-bottom: 1px solid #dee2e6;
}

.admin-nav-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.admin-nav-buttons .btn {
    min-width: 150px;
}

.admin-nav-buttons .btn.active {
    background-color: #0056b3;
    border-color: #0056b3;
}
</style> 