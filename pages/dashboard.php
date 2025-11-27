<?php
/**
 * Dashboard Page
 * Resource Library CMS
 */

require_once '../includes/init.php';
require_login();

$current_user = get_current_user_data($db);

// Get statistics
try {
    $total_resources = $db->fetch("SELECT COUNT(*) as count FROM resources WHERE is_active = 1")['count'];
    $user_resources = $db->fetch("SELECT COUNT(*) as count FROM resources WHERE user_id = ? AND is_active = 1", [get_current_user_id()])['count'];
    $total_categories = $db->fetch("SELECT COUNT(*) as count FROM categories")['count'];
    
    // Get recent resources
    $recent_resources = $db->fetchAll("
        SELECT r.*, c.name as category_name, u.username 
        FROM resources r 
        LEFT JOIN categories c ON r.category_id = c.id 
        LEFT JOIN users u ON r.user_id = u.id 
        WHERE r.is_active = 1 
        ORDER BY r.created_at DESC 
        LIMIT 10
    ");
    
    // Get user's recent resources
    $user_recent_resources = $db->fetchAll("
        SELECT r.*, c.name as category_name 
        FROM resources r 
        LEFT JOIN categories c ON r.category_id = c.id 
        WHERE r.user_id = ? AND r.is_active = 1 
        ORDER BY r.created_at DESC 
        LIMIT 5
    ", [get_current_user_id()]);
    
} catch (Exception $e) {
    $total_resources = 0;
    $user_resources = 0;
    $total_categories = 0;
    $recent_resources = [];
    $user_recent_resources = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($current_user['first_name']); ?>!</p>
        </div>
        
        <?php display_flash_messages(); ?>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-content">
                    <h3><?php echo $total_resources; ?></h3>
                    <p>Total Resources</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">👤</div>
                <div class="stat-content">
                    <h3><?php echo $user_resources; ?></h3>
                    <p>Your Resources</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🏷️</div>
                <div class="stat-content">
                    <h3><?php echo $total_categories; ?></h3>
                    <p>Categories</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="add_resource.php" class="btn btn-primary">
                    <span class="btn-icon">➕</span>
                    Add Resource
                </a>
                <a href="browse.php" class="btn btn-secondary">
                    <span class="btn-icon">🔍</span>
                    Browse Resources
                </a>
                <a href="my_resources.php" class="btn btn-secondary">
                    <span class="btn-icon">📋</span>
                    My Resources
                </a>
            </div>
        </div>
        
        <!-- Recent Resources -->
        <div class="dashboard-section">
            <h2>Recent Resources</h2>
            <?php if (!empty($recent_resources)): ?>
                <div class="resource-list">
                    <?php foreach ($recent_resources as $resource): ?>
                        <div class="resource-item">
                            <div class="resource-icon">
                                <?php echo get_resource_type_icon($resource['resource_type']); ?>
                            </div>
                            <div class="resource-content">
                                <h3><a href="view_resource.php?id=<?php echo $resource['id']; ?>"><?php echo htmlspecialchars($resource['title']); ?></a></h3>
                                <p><?php echo truncate_text(htmlspecialchars($resource['description']), 100); ?></p>
                                <div class="resource-meta">
                                    <span class="category"><?php echo htmlspecialchars($resource['category_name'] ?? 'Uncategorized'); ?></span>
                                    <span class="author">by <?php echo htmlspecialchars($resource['username']); ?></span>
                                    <span class="date"><?php echo format_date($resource['created_at'], 'M j, Y'); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="section-footer">
                    <a href="browse.php" class="btn btn-outline">View All Resources</a>
                </div>
            <?php else: ?>
                <p class="no-content">No resources found. <a href="add_resource.php">Add the first resource</a>!</p>
            <?php endif; ?>
        </div>
        
        <!-- Your Recent Resources -->
        <div class="dashboard-section">
            <h2>Your Recent Resources</h2>
            <?php if (!empty($user_recent_resources)): ?>
                <div class="resource-list">
                    <?php foreach ($user_recent_resources as $resource): ?>
                        <div class="resource-item">
                            <div class="resource-icon">
                                <?php echo get_resource_type_icon($resource['resource_type']); ?>
                            </div>
                            <div class="resource-content">
                                <h3><a href="view_resource.php?id=<?php echo $resource['id']; ?>"><?php echo htmlspecialchars($resource['title']); ?></a></h3>
                                <p><?php echo truncate_text(htmlspecialchars($resource['description']), 100); ?></p>
                                <div class="resource-meta">
                                    <span class="category"><?php echo htmlspecialchars($resource['category_name'] ?? 'Uncategorized'); ?></span>
                                    <span class="date"><?php echo format_date($resource['created_at'], 'M j, Y'); ?></span>
                                </div>
                            </div>
                            <div class="resource-actions">
                                <a href="edit_resource.php?id=<?php echo $resource['id']; ?>" class="btn btn-small">Edit</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="section-footer">
                    <a href="my_resources.php" class="btn btn-outline">View All Your Resources</a>
                </div>
            <?php else: ?>
                <p class="no-content">You haven't added any resources yet. <a href="add_resource.php">Add your first resource</a>!</p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>

