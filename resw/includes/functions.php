<?php
// Check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin()
{
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Sanitize input
function sanitize($input)
{
    global $con;
    return mysqli_real_escape_string($con, trim($input));
}

// Format price
function formatPrice($price)
{
    return 'Rs' . number_format($price, 2);
}

// Get property status class
function getStatusClass($status)
{
    switch ($status) {
        case 'Sale':
            return 'sold';
        case 'Rent':
            return 'new';
        default:
            return '';
    }
}

// Get property type icon
function getPropertyTypeIcon($type)
{
    switch ($type) {
        case 'Apartment':
            return 'glyphicon-home';
        case 'Building':
            return 'glyphicon-building';
        case 'Office-Space':
            return 'glyphicon-briefcase';
        default:
            return 'glyphicon-home';
    }
}
?>