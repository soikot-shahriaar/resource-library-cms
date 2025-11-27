<?php
/**
 * Initialization File
 * Resource Library CMS
 * Include this file at the top of every page
 */

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include configuration
require_once __DIR__ . '/../config/database.php';

// Include classes and functions
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/functions.php';

// Initialize database connection
try {
    $db = new Database();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// Set default timezone
date_default_timezone_set('UTC');

// Function to require login
function require_login() {
    if (!is_logged_in()) {
        add_flash_message('Please log in to access this page.', 'warning');
        redirect('/pages/login.php');
    }
}

// Function to require admin access (for future use)
function require_admin() {
    require_login();
    // Add admin check logic here if needed
}

// Function to get base URL
function get_base_url() {
    // Check if running from command line
    if (php_sapi_name() === 'cli') {
        return 'http://localhost';
    }
    
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Remove port number from host (e.g., localhost:8080 -> localhost)
    if (strpos($host, ':') !== false) {
        $host = explode(':', $host)[0];
    }
    
    // For localhost, always use without port
    if ($host === 'localhost' || $host === '127.0.0.1') {
        $host = 'localhost';
    }
    
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $path = dirname($script);
    
    // Remove trailing slash from path if it's just root
    if ($path === '/' || $path === '\\') {
        $path = '';
    }
    
    return $protocol . '://' . $host . $path;
}

// Set base URL constant
if (!defined('BASE_URL')) {
    define('BASE_URL', get_base_url());
}
?>

