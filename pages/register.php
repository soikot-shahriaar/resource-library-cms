<?php
/**
 * Registration Page
 * Resource Library CMS
 */

require_once '../includes/init.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('dashboard.php');
}

$errors = [];
$form_data = [];

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data = [
        'username' => sanitize_input($_POST['username'] ?? ''),
        'email' => sanitize_input($_POST['email'] ?? ''),
        'first_name' => sanitize_input($_POST['first_name'] ?? ''),
        'last_name' => sanitize_input($_POST['last_name'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'csrf_token' => $_POST['csrf_token'] ?? ''
    ];
    
    // Validate CSRF token
    if (!validate_csrf_token($form_data['csrf_token'])) {
        $errors[] = 'Invalid security token. Please try again.';
    }
    
    // Validate required fields
    if (empty($form_data['username'])) {
        $errors[] = 'Username is required.';
    } elseif (strlen($form_data['username']) < 3) {
        $errors[] = 'Username must be at least 3 characters long.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $form_data['username'])) {
        $errors[] = 'Username can only contain letters, numbers, and underscores.';
    }
    
    if (empty($form_data['email'])) {
        $errors[] = 'Email is required.';
    } elseif (!validate_email($form_data['email'])) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (empty($form_data['first_name'])) {
        $errors[] = 'First name is required.';
    }
    
    if (empty($form_data['last_name'])) {
        $errors[] = 'Last name is required.';
    }
    
    if (empty($form_data['password'])) {
        $errors[] = 'Password is required.';
    } elseif (strlen($form_data['password']) < PASSWORD_MIN_LENGTH) {
        $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long.';
    }
    
    if ($form_data['password'] !== $form_data['confirm_password']) {
        $errors[] = 'Passwords do not match.';
    }
    
    // Check if username or email already exists
    if (empty($errors)) {
        try {
            $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
            $existing_user = $db->fetch($sql, [$form_data['username'], $form_data['email']]);
            
            if ($existing_user) {
                $errors[] = 'Username or email already exists.';
            }
        } catch (Exception $e) {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
    
    // Create user if no errors
    if (empty($errors)) {
        try {
            $password_hash = hash_password($form_data['password']);
            
            $sql = "INSERT INTO users (username, email, password_hash, first_name, last_name) VALUES (?, ?, ?, ?, ?)";
            $user_id = $db->insert($sql, [
                $form_data['username'],
                $form_data['email'],
                $password_hash,
                $form_data['first_name'],
                $form_data['last_name']
            ]);
            
            if ($user_id) {
                // Auto-login after registration
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $form_data['username'];
                $_SESSION['user_name'] = $form_data['first_name'] . ' ' . $form_data['last_name'];
                
                add_flash_message('Registration successful! Welcome to ' . SITE_NAME . '.', 'success');
                redirect('dashboard.php');
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        } catch (Exception $e) {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-form">
            <h1>Register</h1>
            <p class="auth-subtitle">Create your account</p>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?php echo csrf_token_field(); ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" required 
                               value="<?php echo htmlspecialchars($form_data['first_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" required 
                               value="<?php echo htmlspecialchars($form_data['last_name'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>">
                    <small>Only letters, numbers, and underscores allowed</small>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <small>Minimum <?php echo PASSWORD_MIN_LENGTH; ?> characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">Register</button>
            </form>
            
            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>

