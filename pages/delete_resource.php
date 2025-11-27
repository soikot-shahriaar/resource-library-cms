<?php
/**
 * Delete Resource
 * Resource Library CMS
 */

require_once '../includes/init.php';
require_login();

$resource_id = (int)($_GET['id'] ?? 0);

if (!$resource_id) {
    add_flash_message('Resource not found.', 'error');
    redirect('dashboard.php');
}

// Get resource details to verify ownership
try {
    $sql = "SELECT file_path, title FROM resources WHERE id = ? AND user_id = ? AND is_active = 1";
    $resource = $db->fetch($sql, [$resource_id, get_current_user_id()]);
    
    if (!$resource) {
        add_flash_message('Resource not found or you do not have permission to delete it.', 'error');
        redirect('dashboard.php');
    }
} catch (Exception $e) {
    add_flash_message('Error loading resource.', 'error');
    redirect('dashboard.php');
}

// Process deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!validate_csrf_token($csrf_token)) {
        add_flash_message('Invalid security token. Please try again.', 'error');
        redirect('view_resource.php?id=' . $resource_id);
    }
    
    try {
        // Soft delete - mark as inactive
        $sql = "UPDATE resources SET is_active = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?";
        $affected_rows = $db->update($sql, [$resource_id, get_current_user_id()]);
        
        if ($affected_rows > 0) {
            // Optionally delete the physical file
            if ($resource['file_path'] && file_exists(UPLOAD_DIR . $resource['file_path'])) {
                unlink(UPLOAD_DIR . $resource['file_path']);
            }
            
            add_flash_message('Resource "' . $resource['title'] . '" has been deleted successfully.', 'success');
            redirect('my_resources.php');
        } else {
            add_flash_message('Failed to delete resource. Please try again.', 'error');
            redirect('view_resource.php?id=' . $resource_id);
        }
    } catch (Exception $e) {
        add_flash_message('Error deleting resource. Please try again.', 'error');
        redirect('view_resource.php?id=' . $resource_id);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Resource - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Delete Resource</h1>
            <p>Are you sure you want to delete this resource?</p>
        </div>
        
        <div class="confirmation-box">
            <div class="resource-preview">
                <h2><?php echo htmlspecialchars($resource['title']); ?></h2>
                <p><strong>Warning:</strong> This action cannot be undone. The resource will be permanently deleted.</p>
            </div>
            
            <form method="POST" action="">
                <?php echo csrf_token_field(); ?>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-danger">Yes, Delete Resource</button>
                    <a href="view_resource.php?id=<?php echo $resource_id; ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>

