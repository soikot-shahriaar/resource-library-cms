<?php
/**
 * Preview File
 * Resource Library CMS
 */

require_once '../includes/init.php';
require_login();

$resource_id = (int)($_GET['id'] ?? 0);

if (!$resource_id) {
    add_flash_message('Resource not found.', 'error');
    redirect('dashboard.php');
}

// Get resource details
try {
    $sql = "SELECT file_path, title, mime_type FROM resources WHERE id = ? AND is_active = 1 AND file_path IS NOT NULL";
    $resource = $db->fetch($sql, [$resource_id]);
    
    if (!$resource) {
        add_flash_message('File not found.', 'error');
        redirect('dashboard.php');
    }
    
    $file_path = UPLOAD_DIR . $resource['file_path'];
    
    if (!file_exists($file_path)) {
        add_flash_message('File not found on server.', 'error');
        redirect('view_resource.php?id=' . $resource_id);
    }
    
    $file_extension = get_file_extension($resource['file_path']);
    
    // Only allow preview for images
    if (!in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
        add_flash_message('Preview not available for this file type.', 'error');
        redirect('view_resource.php?id=' . $resource_id);
    }
    
    // Set headers for image display
    header('Content-Type: ' . $resource['mime_type']);
    header('Content-Length: ' . filesize($file_path));
    header('Cache-Control: public, max-age=3600');
    
    // Output image
    readfile($file_path);
    exit;
    
} catch (Exception $e) {
    add_flash_message('Error previewing file.', 'error');
    redirect('dashboard.php');
}
?>

