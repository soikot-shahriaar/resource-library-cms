<?php
/**
 * Logout Page
 * Resource Library CMS
 */

require_once '../includes/init.php';

// Destroy session and redirect
session_unset();
session_destroy();
session_start();

add_flash_message('You have been logged out successfully.', 'info');
redirect('login.php');
?>

