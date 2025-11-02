<?php
session_start();
include_once "../includes/connection.php";
include_once "../includes/functions.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['property_id'];
    $user_id = $_SESSION['user_id'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];
    $phone_number = $_POST['phone_number'];
    $notes = $_POST['notes'];

    // Validate input
    if (empty($property_id) || empty($booking_date) || empty($booking_time) || empty($phone_number)) {
        $_SESSION['error'] = "Please fill in all required fields, including phone number";
        header("Location: property-detail.php?id=" . $property_id);
        exit;
    }

    // Basic validation for phone number format (10 digits)
    if (!preg_match('/^[0-9]{10}$/', $phone_number)) {
        $_SESSION['error'] = "Please enter a valid 10-digit phone number.";
        header("Location: property-detail.php?id=" . $property_id);
        exit;
    }

    // Check if user already has a booking for this property
    $check_booking = "SELECT booking_id FROM bookings WHERE property_id = ? AND user_id = ? AND status != 'cancelled'";
    $stmt = mysqli_prepare($con, $check_booking);
    mysqli_stmt_bind_param($stmt, "ii", $property_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error'] = "You have already booked a viewing for this property. Please check your bookings.";
        header("Location: property-detail.php?id=" . $property_id);
        exit;
    }
    mysqli_stmt_close($stmt);

    // Verify that the user exists
    $user_check = "SELECT user_id FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $user_check);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        $_SESSION['error'] = "Invalid user account. Please log in again.";
        header("Location: ../login.php");
        exit;
    }
    mysqli_stmt_close($stmt);

    // Verify that the property exists
    $property_check = "SELECT property_id FROM properties WHERE property_id = ?";
    $stmt = mysqli_prepare($con, $property_check);
    mysqli_stmt_bind_param($stmt, "i", $property_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        $_SESSION['error'] = "Invalid property selected.";
        header("Location: ../index.php");
        exit;
    }
    mysqli_stmt_close($stmt);

    // Insert booking into database
    $query = "INSERT INTO bookings (property_id, user_id, booking_date, booking_time, phone_number, notes) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "iissss", $property_id, $user_id, $booking_date, $booking_time, $phone_number, $notes);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Booking request submitted successfully!";
    } else {
        $_SESSION['error'] = "Error submitting booking request: " . mysqli_error($con);
    }
    
    mysqli_stmt_close($stmt);
    header("Location: property-detail.php?id=" . $property_id);
    exit;
} else {
    header("Location: ../index.php");
    exit;
}
?> 