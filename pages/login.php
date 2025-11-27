<?php
/**
 * Login Page
 * Resource Library CMS
 */

require_once '../includes/init.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!validate_csrf_token($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            // Check user credentials
            $sql = "SELECT id, username, password_hash, first_name, last_name FROM users WHERE username = ? AND is_active = 1";
            $user = $db->fetch($sql, [$username]);
            
            if ($user && verify_password($password, $user['password_hash'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                add_flash_message('Welcome back, ' . $user['first_name'] . '!', 'success');
                redirect('dashboard.php');
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (Exception $e) {
            $error = 'Login failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header .logo {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .login-header h1 {
            font-size: 2.5rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #718096;
            font-size: 1.1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2d3748;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 1.1rem;
        }
        
        .btn-login {
            width: 100%;
            padding: 0.875rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .demo-credentials {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }
        
        .demo-credentials h3 {
            color: #2d3748;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        
        .demo-credentials .credential-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            border: 1px solid #e2e8f0;
        }
        
        .demo-credentials .credential-item:last-child {
            margin-bottom: 0;
        }
        
        .credential-label {
            font-weight: 500;
            color: #4a5568;
        }
        
        .credential-value {
            font-family: 'Courier New', monospace;
            color: #667eea;
            font-weight: 600;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            color: #718096;
        }
        
        .auth-footer a {
            color: #667eea;
            text-decoration: none;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .copyright {
            text-align: center;
            margin-top: 1rem;
            color: #a0aec0;
            font-size: 0.875rem;
        }
        
        .copyright a {
            color: #667eea;
            text-decoration: none;
        }
        
        .copyright a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-form">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-book-open"></i>
                </div>
                <h1>Welcome Back</h1>
                <p>Sign in to access your resource library</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?php echo csrf_token_field(); ?>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" required 
                               placeholder="Enter your username"
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required
                               placeholder="Enter your password">
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>
            
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Create one here</a></p>
                <div class="copyright">
                    &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Developed by <a href="https://rivertheme.com" target="_blank" rel="noopener noreferrer">Rivertheme</a>.
                </div>
            </div>
        </div>
    </div>
</body>
</html>

