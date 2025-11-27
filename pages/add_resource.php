<?php
/**
 * Add Resource Page
 * Resource Library CMS
 */

require_once '../includes/init.php';
require_login();

$errors = [];
$form_data = [];

// Get categories for dropdown
try {
    $categories = $db->fetchAll("SELECT id, name FROM categories ORDER BY name");
} catch (Exception $e) {
    $categories = [];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data = [
        'title' => sanitize_input($_POST['title'] ?? ''),
        'description' => sanitize_input($_POST['description'] ?? ''),
        'resource_type' => sanitize_input($_POST['resource_type'] ?? ''),
        'category_id' => (int)($_POST['category_id'] ?? 0),
        'external_url' => sanitize_input($_POST['external_url'] ?? ''),
        'csrf_token' => $_POST['csrf_token'] ?? ''
    ];
    
    // Validate CSRF token
    if (!validate_csrf_token($form_data['csrf_token'])) {
        $errors[] = 'Invalid security token. Please try again.';
    }
    
    // Validate required fields
    if (empty($form_data['title'])) {
        $errors[] = 'Title is required.';
    }
    
    if (empty($form_data['description'])) {
        $errors[] = 'Description is required.';
    }
    
    if (empty($form_data['resource_type'])) {
        $errors[] = 'Resource type is required.';
    }
    
    // Validate resource type specific requirements
    $file_path = null;
    $file_size = null;
    $mime_type = null;
    
    if ($form_data['resource_type'] === 'link') {
        if (empty($form_data['external_url'])) {
            $errors[] = 'URL is required for link resources.';
        } elseif (!filter_var($form_data['external_url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Please enter a valid URL.';
        }
    } else {
        // Handle file upload
        if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] === UPLOAD_ERR_OK) {
            $uploaded_file = $_FILES['resource_file'];
            
            // Validate file size
            if ($uploaded_file['size'] > MAX_FILE_SIZE) {
                $errors[] = 'File size exceeds the maximum limit of ' . format_file_size(MAX_FILE_SIZE) . '.';
            }
            
            // Validate file type
            if (!is_allowed_file_type($uploaded_file['name'])) {
                $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', ALLOWED_FILE_TYPES);
            }
            
            if (empty($errors)) {
                // Generate unique filename and move file
                $unique_filename = generate_unique_filename($uploaded_file['name']);
                $upload_path = UPLOAD_DIR . $unique_filename;
                
                if (move_uploaded_file($uploaded_file['tmp_name'], $upload_path)) {
                    $file_path = $unique_filename;
                    $file_size = $uploaded_file['size'];
                    $mime_type = $uploaded_file['type'];
                } else {
                    $errors[] = 'Failed to upload file. Please try again.';
                }
            }
        } elseif ($form_data['resource_type'] !== 'link') {
            $errors[] = 'Please select a file to upload.';
        }
    }
    
    // Save resource if no errors
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO resources (title, description, resource_type, file_path, external_url, file_size, mime_type, category_id, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $resource_id = $db->insert($sql, [
                $form_data['title'],
                $form_data['description'],
                $form_data['resource_type'],
                $file_path,
                $form_data['external_url'] ?: null,
                $file_size,
                $mime_type,
                $form_data['category_id'] ?: null,
                get_current_user_id()
            ]);
            
            if ($resource_id) {
                add_flash_message('Resource added successfully!', 'success');
                redirect('view_resource.php?id=' . $resource_id);
            } else {
                $errors[] = 'Failed to add resource. Please try again.';
            }
        } catch (Exception $e) {
            $errors[] = 'Failed to add resource. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Resource - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Add New Resource</h1>
            <p>Share a valuable resource with the community</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="" enctype="multipart/form-data" id="resourceForm">
                <?php echo csrf_token_field(); ?>
                
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required 
                           value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="resource_type">Resource Type:</label>
                        <select id="resource_type" name="resource_type" required onchange="toggleResourceFields()">
                            <option value="">Select type...</option>
                            <option value="document" <?php echo ($form_data['resource_type'] ?? '') === 'document' ? 'selected' : ''; ?>>Document</option>
                            <option value="link" <?php echo ($form_data['resource_type'] ?? '') === 'link' ? 'selected' : ''; ?>>Link</option>
                            <option value="video" <?php echo ($form_data['resource_type'] ?? '') === 'video' ? 'selected' : ''; ?>>Video</option>
                            <option value="tutorial" <?php echo ($form_data['resource_type'] ?? '') === 'tutorial' ? 'selected' : ''; ?>>Tutorial</option>
                            <option value="other" <?php echo ($form_data['resource_type'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category:</label>
                        <select id="category_id" name="category_id">
                            <option value="">Select category...</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo ($form_data['category_id'] ?? 0) == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group" id="fileUploadGroup">
                    <label for="resource_file">Upload File:</label>
                    <input type="file" id="resource_file" name="resource_file" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif,.mp4,.avi,.mov">
                    <small>Maximum file size: <?php echo format_file_size(MAX_FILE_SIZE); ?>. Allowed types: <?php echo implode(', ', ALLOWED_FILE_TYPES); ?></small>
                </div>
                
                <div class="form-group" id="urlGroup" style="display: none;">
                    <label for="external_url">URL:</label>
                    <input type="url" id="external_url" name="external_url" 
                           value="<?php echo htmlspecialchars($form_data['external_url'] ?? ''); ?>"
                           placeholder="https://example.com">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Add Resource</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
        function toggleResourceFields() {
            const resourceType = document.getElementById('resource_type').value;
            const fileGroup = document.getElementById('fileUploadGroup');
            const urlGroup = document.getElementById('urlGroup');
            const fileInput = document.getElementById('resource_file');
            const urlInput = document.getElementById('external_url');
            
            if (resourceType === 'link') {
                fileGroup.style.display = 'none';
                urlGroup.style.display = 'block';
                fileInput.required = false;
                urlInput.required = true;
            } else {
                fileGroup.style.display = 'block';
                urlGroup.style.display = 'none';
                fileInput.required = true;
                urlInput.required = false;
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleResourceFields();
        });
    </script>
</body>
</html>

