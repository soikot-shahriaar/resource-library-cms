<?php
/**
 * User Profile Page
 * Resource Library CMS
 */

require_once '../includes/init.php';
require_login();

$current_user = get_current_user_data($db);
$errors = [];
$success = false;

// Process profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data = [
        'first_name' => sanitize_input($_POST['first_name'] ?? ''),
        'last_name' => sanitize_input($_POST['last_name'] ?? ''),
        'email' => sanitize_input($_POST['email'] ?? ''),
        'current_password' => $_POST['current_password'] ?? '',
        'new_password' => $_POST['new_password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'csrf_token' => $_POST['csrf_token'] ?? ''
    ];
    
    // Validate CSRF token
    if (!validate_csrf_token($form_data['csrf_token'])) {
        $errors[] = 'Invalid security token. Please try again.';
    }
    
    // Validate required fields
    if (empty($form_data['first_name'])) {
        $errors[] = 'First name is required.';
    }
    
    if (empty($form_data['last_name'])) {
        $errors[] = 'Last name is required.';
    }
    
    if (empty($form_data['email'])) {
        $errors[] = 'Email is required.';
    } elseif (!validate_email($form_data['email'])) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    // Check if email is already taken by another user
    if (empty($errors)) {
        try {
            $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
            $existing_user = $db->fetch($sql, [$form_data['email'], get_current_user_id()]);
            
            if ($existing_user) {
                $errors[] = 'Email address is already in use by another account.';
            }
        } catch (Exception $e) {
            $errors[] = 'Error checking email availability.';
        }
    }
    
    // Handle password change
    $update_password = false;
    if (!empty($form_data['new_password'])) {
        if (empty($form_data['current_password'])) {
            $errors[] = 'Current password is required to set a new password.';
        } else {
            // Verify current password
            try {
                $sql = "SELECT password_hash FROM users WHERE id = ?";
                $user_data = $db->fetch($sql, [get_current_user_id()]);
                
                if (!verify_password($form_data['current_password'], $user_data['password_hash'])) {
                    $errors[] = 'Current password is incorrect.';
                }
            } catch (Exception $e) {
                $errors[] = 'Error verifying current password.';
            }
        }
        
        if (strlen($form_data['new_password']) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'New password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long.';
        }
        
        if ($form_data['new_password'] !== $form_data['confirm_password']) {
            $errors[] = 'New passwords do not match.';
        }
        
        if (empty($errors)) {
            $update_password = true;
        }
    }
    
    // Update profile if no errors
    if (empty($errors)) {
        try {
            if ($update_password) {
                $password_hash = hash_password($form_data['new_password']);
                $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                $params = [$form_data['first_name'], $form_data['last_name'], $form_data['email'], $password_hash, get_current_user_id()];
            } else {
                $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                $params = [$form_data['first_name'], $form_data['last_name'], $form_data['email'], get_current_user_id()];
            }
            
            $affected_rows = $db->update($sql, $params);
            
            if ($affected_rows > 0) {
                // Update session data
                $_SESSION['user_name'] = $form_data['first_name'] . ' ' . $form_data['last_name'];
                
                $success = true;
                add_flash_message('Profile updated successfully!', 'success');
                
                // Refresh current user data
                $current_user = get_current_user_data($db);
            } else {
                $errors[] = 'No changes were made to your profile.';
            }
        } catch (Exception $e) {
            $errors[] = 'Failed to update profile. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>My Profile</h1>
            <p>Manage your account information</p>
        </div>
        
        <?php display_flash_messages(); ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="profile-container">
            <div class="profile-info">
                <h2>Account Information</h2>
                
                <form method="POST" action="">
                    <?php echo csrf_token_field(); ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first_name" name="first_name" required 
                                   value="<?php echo htmlspecialchars($current_user['first_name']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" required 
                                   value="<?php echo htmlspecialchars($current_user['last_name']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" value="<?php echo htmlspecialchars($current_user['username']); ?>" disabled>
                        <small>Username cannot be changed</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($current_user['email']); ?>">
                    </div>
                    
                    <hr>
                    
                    <h3>Change Password</h3>
                    <p class="form-note">Leave password fields empty if you don't want to change your password.</p>
                    
                    <div class="form-group">
                        <label for="current_password">Current Password:</label>
                        <input type="password" id="current_password" name="current_password">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password">
                            <small>Minimum <?php echo PASSWORD_MIN_LENGTH; ?> characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            
            <div class="profile-stats">
                <h2>Your Statistics</h2>
                
                <?php
                try {
                    $user_stats = $db->fetch("
                        SELECT 
                            COUNT(*) as total_resources,
                            COUNT(CASE WHEN resource_type = 'document' THEN 1 END) as documents,
                            COUNT(CASE WHEN resource_type = 'link' THEN 1 END) as links,
                            COUNT(CASE WHEN resource_type = 'video' THEN 1 END) as videos,
                            COUNT(CASE WHEN resource_type = 'tutorial' THEN 1 END) as tutorials,
                            COUNT(CASE WHEN resource_type = 'other' THEN 1 END) as others
                        FROM resources 
                        WHERE user_id = ? AND is_active = 1
                    ", [get_current_user_id()]);
                } catch (Exception $e) {
                    $user_stats = [
                        'total_resources' => 0,
                        'documents' => 0,
                        'links' => 0,
                        'videos' => 0,
                        'tutorials' => 0,
                        'others' => 0
                    ];
                }
                ?>
                
                <div class="stats-list">
                    <div class="stat-item">
                        <span class="stat-label">Total Resources:</span>
                        <span class="stat-value"><?php echo $user_stats['total_resources']; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Documents:</span>
                        <span class="stat-value"><?php echo $user_stats['documents']; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Links:</span>
                        <span class="stat-value"><?php echo $user_stats['links']; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Videos:</span>
                        <span class="stat-value"><?php echo $user_stats['videos']; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Tutorials:</span>
                        <span class="stat-value"><?php echo $user_stats['tutorials']; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Other:</span>
                        <span class="stat-value"><?php echo $user_stats['others']; ?></span>
                    </div>
                </div>
                
                <div class="profile-actions">
                    <a href="my_resources.php" class="btn btn-outline">Manage Resources</a>
                    <a href="add_resource.php" class="btn btn-primary">Add Resource</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>

