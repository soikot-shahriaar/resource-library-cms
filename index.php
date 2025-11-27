<?php
/**
 * Main Index File
 * Resource Library CMS
 * Redirects to appropriate page based on login status
 */

require_once 'includes/init.php';

// Check if user is logged in
if (is_logged_in()) {
    // Redirect to dashboard
    redirect('pages/dashboard.php');
} else {
    // Redirect to login page
    redirect('pages/login.php');
}
?>

