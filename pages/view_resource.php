<?php
/**
 * View Resource Page
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
    $sql = "
        SELECT r.*, c.name as category_name, u.username, u.first_name, u.last_name 
        FROM resources r 
        LEFT JOIN categories c ON r.category_id = c.id 
        LEFT JOIN users u ON r.user_id = u.id 
        WHERE r.id = ? AND r.is_active = 1
    ";
    $resource = $db->fetch($sql, [$resource_id]);
    
    if (!$resource) {
        add_flash_message('Resource not found.', 'error');
        redirect('dashboard.php');
    }
} catch (Exception $e) {
    add_flash_message('Error loading resource.', 'error');
    redirect('dashboard.php');
}

$is_owner = ($resource['user_id'] == get_current_user_id());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($resource['title']); ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="resource-view">
            <div class="resource-header">
                <div class="resource-title">
                    <div class="resource-icon-large">
                        <?php echo get_resource_type_icon($resource['resource_type']); ?>
                    </div>
                    <div class="title-content">
                        <h1><?php echo htmlspecialchars($resource['title']); ?></h1>
                        <div class="resource-meta">
                            <span class="resource-type"><?php echo ucfirst($resource['resource_type']); ?></span>
                            <?php if ($resource['category_name']): ?>
                                <span class="category"><?php echo htmlspecialchars($resource['category_name']); ?></span>
                            <?php endif; ?>
                            <span class="author">by <?php echo htmlspecialchars($resource['first_name'] . ' ' . $resource['last_name']); ?></span>
                            <span class="date"><?php echo format_date($resource['created_at'], 'M j, Y \a\t g:i A'); ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if ($is_owner): ?>
                    <div class="resource-actions">
                        <a href="edit_resource.php?id=<?php echo $resource['id']; ?>" class="btn btn-primary">Edit</a>
                        <a href="delete_resource.php?id=<?php echo $resource['id']; ?>" class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this resource?')">Delete</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="resource-content">
                <div class="resource-description">
                    <h2>Description</h2>
                    <p><?php echo nl2br(htmlspecialchars($resource['description'])); ?></p>
                </div>
                
                <div class="resource-access">
                    <h2>Access Resource</h2>
                    
                    <?php if ($resource['resource_type'] === 'link' && $resource['external_url']): ?>
                        <div class="resource-link">
                            <a href="<?php echo htmlspecialchars($resource['external_url']); ?>" 
                               target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-large">
                                🔗 Open Link
                            </a>
                            <p class="link-url"><?php echo htmlspecialchars($resource['external_url']); ?></p>
                        </div>
                    <?php elseif ($resource['file_path']): ?>
                        <div class="resource-file">
                            <div class="file-info">
                                <div class="file-details">
                                    <strong>File:</strong> <?php echo htmlspecialchars($resource['file_path']); ?><br>
                                    <?php if ($resource['file_size']): ?>
                                        <strong>Size:</strong> <?php echo format_file_size($resource['file_size']); ?><br>
                                    <?php endif; ?>
                                    <?php if ($resource['mime_type']): ?>
                                        <strong>Type:</strong> <?php echo htmlspecialchars($resource['mime_type']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="file-actions">
                                <a href="download.php?id=<?php echo $resource['id']; ?>" class="btn btn-primary">
                                    📥 Download
                                </a>
                                
                                <?php
                                $file_extension = get_file_extension($resource['file_path']);
                                if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])):
                                ?>
                                    <a href="preview.php?id=<?php echo $resource['id']; ?>" class="btn btn-secondary" target="_blank">
                                        👁️ Preview
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="no-access">No file or link available for this resource.</p>
                    <?php endif; ?>
                </div>
                
                <div class="resource-details">
                    <h2>Details</h2>
                    <table class="details-table">
                        <tr>
                            <td><strong>Resource Type:</strong></td>
                            <td><?php echo ucfirst($resource['resource_type']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Category:</strong></td>
                            <td><?php echo htmlspecialchars($resource['category_name'] ?? 'Uncategorized'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Added by:</strong></td>
                            <td><?php echo htmlspecialchars($resource['first_name'] . ' ' . $resource['last_name']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Date Added:</strong></td>
                            <td><?php echo format_date($resource['created_at'], 'F j, Y \a\t g:i A'); ?></td>
                        </tr>
                        <?php if ($resource['updated_at'] !== $resource['created_at']): ?>
                        <tr>
                            <td><strong>Last Updated:</strong></td>
                            <td><?php echo format_date($resource['updated_at'], 'F j, Y \a\t g:i A'); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            
            <div class="navigation-links">
                <a href="browse.php" class="btn btn-outline">← Back to Browse</a>
                <?php if ($is_owner): ?>
                    <a href="my_resources.php" class="btn btn-outline">My Resources</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>

