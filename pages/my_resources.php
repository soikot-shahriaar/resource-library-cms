<?php
/**
 * My Resources Page
 * Resource Library CMS
 */

require_once '../includes/init.php';
require_login();

$current_user = get_current_user_data($db);

// Get search and filter parameters
$search = sanitize_input($_GET['search'] ?? '');
$category_filter = (int)($_GET['category'] ?? 0);
$type_filter = sanitize_input($_GET['type'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get categories for filter dropdown
try {
    $categories = $db->fetchAll("SELECT id, name FROM categories ORDER BY name");
} catch (Exception $e) {
    $categories = [];
}

// Build search query for user's resources
$where_conditions = ["r.user_id = ?", "r.is_active = 1"];
$params = [get_current_user_id()];

if (!empty($search)) {
    $where_conditions[] = "(r.title LIKE ? OR r.description LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($category_filter > 0) {
    $where_conditions[] = "r.category_id = ?";
    $params[] = $category_filter;
}

if (!empty($type_filter)) {
    $where_conditions[] = "r.resource_type = ?";
    $params[] = $type_filter;
}

$where_clause = implode(' AND ', $where_conditions);

// Get total count for pagination
try {
    $count_sql = "
        SELECT COUNT(*) as total 
        FROM resources r 
        WHERE $where_clause
    ";
    $total_resources = $db->fetch($count_sql, $params)['total'];
    $total_pages = ceil($total_resources / $per_page);
} catch (Exception $e) {
    $total_resources = 0;
    $total_pages = 1;
}

// Get user's resources
try {
    $sql = "
        SELECT r.*, c.name as category_name 
        FROM resources r 
        LEFT JOIN categories c ON r.category_id = c.id 
        WHERE $where_clause 
        ORDER BY r.updated_at DESC 
        LIMIT $per_page OFFSET $offset
    ";
    $resources = $db->fetchAll($sql, $params);
} catch (Exception $e) {
    $resources = [];
}

// Build query string for pagination
$query_params = [];
if (!empty($search)) $query_params['search'] = $search;
if ($category_filter > 0) $query_params['category'] = $category_filter;
if (!empty($type_filter)) $query_params['type'] = $type_filter;
$query_string = http_build_query($query_params);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Resources - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>My Resources</h1>
            <p>Manage your uploaded resources</p>
            <div class="page-actions">
                <a href="add_resource.php" class="btn btn-primary">➕ Add New Resource</a>
            </div>
        </div>
        
        <?php display_flash_messages(); ?>
        
        <!-- Search and Filter Form -->
        <div class="search-filters">
            <form method="GET" action="" class="filter-form">
                <div class="search-row">
                    <div class="search-input">
                        <input type="text" name="search" placeholder="Search your resources..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary">🔍 Search</button>
                    </div>
                </div>
                
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="category">Category:</label>
                        <select name="category" id="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="type">Type:</label>
                        <select name="type" id="type">
                            <option value="">All Types</option>
                            <option value="document" <?php echo $type_filter === 'document' ? 'selected' : ''; ?>>Document</option>
                            <option value="link" <?php echo $type_filter === 'link' ? 'selected' : ''; ?>>Link</option>
                            <option value="video" <?php echo $type_filter === 'video' ? 'selected' : ''; ?>>Video</option>
                            <option value="tutorial" <?php echo $type_filter === 'tutorial' ? 'selected' : ''; ?>>Tutorial</option>
                            <option value="other" <?php echo $type_filter === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-secondary">Apply Filters</button>
                        <a href="my_resources.php" class="btn btn-outline">Clear</a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Results Summary -->
        <div class="results-summary">
            <p>
                <?php if ($total_resources > 0): ?>
                    You have <?php echo $total_resources; ?> resource<?php echo $total_resources !== 1 ? 's' : ''; ?>
                    <?php if (!empty($search) || $category_filter > 0 || !empty($type_filter)): ?>
                        matching your criteria
                    <?php endif; ?>
                <?php else: ?>
                    You haven't added any resources yet
                    <?php if (!empty($search) || $category_filter > 0 || !empty($type_filter)): ?>
                        matching your criteria
                    <?php endif; ?>
                <?php endif; ?>
            </p>
        </div>
        
        <!-- Resources List -->
        <?php if (!empty($resources)): ?>
            <div class="resources-table">
                <table>
                    <thead>
                        <tr>
                            <th>Resource</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resources as $resource): ?>
                            <tr>
                                <td>
                                    <div class="resource-info">
                                        <div class="resource-icon-small">
                                            <?php echo get_resource_type_icon($resource['resource_type']); ?>
                                        </div>
                                        <div class="resource-details">
                                            <h4><a href="view_resource.php?id=<?php echo $resource['id']; ?>"><?php echo htmlspecialchars($resource['title']); ?></a></h4>
                                            <p><?php echo truncate_text(htmlspecialchars($resource['description']), 80); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="resource-type-badge"><?php echo ucfirst($resource['resource_type']); ?></span>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($resource['category_name'] ?? 'Uncategorized'); ?>
                                </td>
                                <td>
                                    <?php echo format_date($resource['updated_at'], 'M j, Y'); ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="view_resource.php?id=<?php echo $resource['id']; ?>" class="btn btn-small btn-outline">View</a>
                                        <a href="edit_resource.php?id=<?php echo $resource['id']; ?>" class="btn btn-small btn-primary">Edit</a>
                                        <a href="delete_resource.php?id=<?php echo $resource['id']; ?>" class="btn btn-small btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this resource?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo $query_string; ?>&page=<?php echo $page - 1; ?>" class="btn btn-outline">← Previous</a>
                    <?php endif; ?>
                    
                    <div class="page-numbers">
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        for ($i = $start_page; $i <= $end_page; $i++):
                        ?>
                            <a href="?<?php echo $query_string; ?>&page=<?php echo $i; ?>" 
                               class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-outline'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?<?php echo $query_string; ?>&page=<?php echo $page + 1; ?>" class="btn btn-outline">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-results">
                <div class="no-results-icon">📁</div>
                <h2>No resources found</h2>
                <p>
                    <?php if (!empty($search) || $category_filter > 0 || !empty($type_filter)): ?>
                        Try adjusting your search criteria or <a href="my_resources.php">view all your resources</a>.
                    <?php else: ?>
                        Start building your resource library by <a href="add_resource.php">adding your first resource</a>!
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>

