<?php
session_start();
include_once "../includes/connection.php";
include_once "../includes/functions.php";

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isAdmin($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$page_title = "Manage Bookings - Admin Dashboard";

// Handle booking status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $booking_id = $_POST['booking_id'];
        $new_status = $_POST['action'] === 'confirm' ? 'confirmed' : 'cancelled';
        
        $query = "UPDATE bookings SET status = ? WHERE booking_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $booking_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Booking status updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating booking status.";
        }
        
        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['delete_booking'])) {
        $booking_id = $_POST['booking_id'];
        
        // Delete the booking
        $delete_query = "DELETE FROM bookings WHERE booking_id = ?";
        $stmt = mysqli_prepare($con, $delete_query);
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Booking deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting booking.";
        }
        
        mysqli_stmt_close($stmt);
    }
    
    header("Location: manage-bookings.php");
    exit;
}

// Fetch all bookings with property and user details
$query = "SELECT b.*, p.property_title, u.full_name as user_name, u.email as user_email, b.phone_number 
          FROM bookings b 
          JOIN properties p ON b.property_id = p.property_id 
          JOIN users u ON b.user_id = u.user_id 
          ORDER BY b.created_at DESC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $page_title; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
        .booking-card {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
        }
        .booking-status {
            font-weight: bold;
        }
        .status-pending { color: #f0ad4e; }
        .status-confirmed { color: #5cb85c; }
        .status-cancelled { color: #d9534f; }
    </style>
</head>
<body>
    <?php include '../includes/nav.php'; ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Manage Bookings</h2>
                
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                    unset($_SESSION['success']);
                }
                ?>

                <div class="row">
                    <?php while ($booking = mysqli_fetch_assoc($result)): ?>
                        <div class="col-lg-6">
                            <div class="booking-card">
                                <h4><?php echo htmlspecialchars($booking['property_title']); ?></h4>
                                <p><strong>Booked by:</strong> <?php echo htmlspecialchars($booking['user_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['user_email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['phone_number'] ?? 'N/A'); ?></p>
                                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></p>
                                <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($booking['booking_time'])); ?></p>
                                <p><strong>Status:</strong> 
                                    <span class="booking-status status-<?php echo $booking['status']; ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </p>
                                <?php if ($booking['notes']): ?>
                                    <p><strong>Notes:</strong> <?php echo htmlspecialchars($booking['notes']); ?></p>
                                <?php endif; ?>
                                
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <button type="submit" name="action" value="confirm" class="btn btn-success">Confirm</button>
                                        <button type="submit" name="action" value="cancel" class="btn btn-danger">Cancel</button>
                                    </form>
                                <?php endif; ?>
                                
                                <form method="post" style="display: inline; margin-left: 5px;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                    <button type="submit" name="delete_booking" class="btn btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this booking? This action cannot be undone.');">
                                        <span class="glyphicon glyphicon-trash"></span> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/jquery-1.9.1.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.js"></script>
</body>
</html> 