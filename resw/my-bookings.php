<?php
session_start();
include_once "includes/connection.php";
include_once "includes/functions.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = "My Bookings - Real Estate Management System";
$user_id = $_SESSION['user_id'];

// Fetch user's bookings with agent information
$query = "SELECT b.*, p.property_title, p.property_address, p.price, a.agent_name, a.agent_contact, a.agent_email 
          FROM bookings b 
          JOIN properties p ON b.property_id = p.property_id 
          JOIN agent a ON p.agent_id = a.agent_id 
          WHERE b.user_id = ? 
          ORDER BY b.booking_date DESC, b.booking_time DESC";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    // Verify that the booking belongs to the current user
    $check_booking = "SELECT booking_id FROM bookings WHERE booking_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($con, $check_booking);
    mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        // Delete the booking
        $delete_query = "DELETE FROM bookings WHERE booking_id = ?";
        $stmt = mysqli_prepare($con, $delete_query);
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Booking deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting booking.";
        }
    } else {
        $_SESSION['error'] = "Invalid booking or unauthorized action.";
    }
    
    header("Location: my-bookings.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $page_title; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="assets/style.css" />
    <style>
        .booking-card {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .booking-status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .status-pending { 
            color: #f0ad4e;
            background-color: #fff3cd;
        }
        .status-confirmed { 
            color: #5cb85c;
            background-color: #d4edda;
        }
        .status-cancelled { 
            color: #d9534f;
            background-color: #f8d7da;
        }
        .property-info {
            margin-bottom: 15px;
        }
        .booking-date {
            font-size: 1.1em;
            margin-bottom: 10px;
        }
        .no-bookings {
            text-align: center;
            padding: 40px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- banner -->
    <div class="inside-banner">
        <div class="container">
            <h2>My Bookings</h2>
        </div>
    </div>
    <!-- banner -->

    <div class="container">
        <div class="spacer">
            <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <div class="row">
                <div class="col-lg-12">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($booking = mysqli_fetch_assoc($result)): ?>
                            <div class="booking-card">
                                <div class="property-info">
                                    <h4><?php echo htmlspecialchars($booking['property_title']); ?></h4>
                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($booking['property_address']); ?></p>
                                    <p><strong>Price:</strong> $<?php echo number_format($booking['price']); ?></p>
                                </div>
                                
                                <div class="booking-date">
                                    <strong>Viewing Date:</strong> <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?>
                                    <strong>Time:</strong> <?php echo date('g:i A', strtotime($booking['booking_time'])); ?>
                                </div>
                                
                                <div class="booking-status status-<?php echo $booking['status']; ?>">
                                    Status: <?php echo ucfirst($booking['status']); ?>
                                </div>

                                <?php if ($booking['status'] === 'confirmed'): ?>
                                    <div class="agent-info" style="margin: 15px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
                                        <h5>Your Assigned Agent</h5>
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['agent_name']); ?></p>
                                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($booking['agent_contact']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['agent_email']); ?></p>
                                        <div class="alert alert-info" style="margin-top: 10px;">
                                            <strong>Note:</strong> Your assigned agent will contact you soon to confirm the viewing details and answer any questions you may have.
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($booking['notes']): ?>
                                    <div class="booking-notes">
                                        <strong>Your Notes:</strong> <?php echo htmlspecialchars($booking['notes']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="booking-meta">
                                    <small>Booked on: <?php echo date('F j, Y g:i A', strtotime($booking['created_at'])); ?></small>
                                </div>
                                
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <form method="post" style="margin-top: 10px;">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <button type="submit" name="delete_booking" class="btn btn-danger btn-sm">
                                            <span class="glyphicon glyphicon-trash"></span> Delete Booking
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-bookings">
                            <h3>No Bookings Found</h3>
                            <p>You haven't made any property viewing bookings yet.</p>
                            <a href="index.php" class="btn btn-primary">Browse Properties</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/jquery-1.9.1.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.js"></script>
</body>
</html> 