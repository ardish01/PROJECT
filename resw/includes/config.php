<?php
// Base URL configuration
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$app_path = "/resw-main";

// Get the current script path
$current_script = $_SERVER['SCRIPT_NAME'];
$is_admin = strpos($current_script, '/admin/') !== false;
$is_properties = strpos($current_script, '/properties/') !== false;

// Define all paths
define('BASE_URL', $base_url);
define('APP_PATH', $app_path);
define('ADMIN_PATH', $app_path . '/admin');
define('ASSETS_PATH', $app_path . '/assets');
define('INCLUDES_PATH', $app_path . '/includes');
define('PROPERTIES_PATH', $app_path . '/properties');

// Function to get full URL
function get_full_url($path) {
    // Remove any leading slash from the path
    $path = ltrim($path, '/');
    
    // If the path starts with http:// or https://, return as is
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    
    // If the path starts with the app path, use it as is
    if (strpos($path, APP_PATH) === 0) {
        return BASE_URL . $path;
    }
    
    // Otherwise, prepend the app path
    return BASE_URL . APP_PATH . '/' . $path;
}

// Function to get relative path
function get_relative_path($path) {
    global $is_admin, $is_properties;
    
    // Remove any leading slash from the path
    $path = ltrim($path, '/');
    
    // Handle admin paths
    if (strpos($path, 'admin/') === 0 && !$is_admin) {
        return '../' . $path;
    }
    
    // Handle properties paths
    if (strpos($path, 'properties/') === 0 && !$is_properties) {
        return '../' . $path;
    }
    
    // Handle includes paths
    if (strpos($path, 'includes/') === 0) {
        return $is_admin ? '../' . $path : $path;
    }
    
    // Handle assets paths
    if (strpos($path, 'assets/') === 0) {
        return $is_admin ? '../' . $path : $path;
    }
    
    return $path;
}

// Function to include files with proper path
function include_file($file) {
    $path = get_relative_path($file);
    if (file_exists($path)) {
        include_once $path;
    } else {
        error_log("File not found: " . $path);
        include_once 'includes/error.php';
    }
}

// Function to get the correct file path
function get_file_path($path) {
    $base_path = $_SERVER['DOCUMENT_ROOT'] . '/resw-main';
    return $base_path . '/' . ltrim($path, '/');
}
?> 