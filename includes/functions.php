<?php
/**
 * Common Functions
 * Resource Library CMS
 */

/**
 * Sanitize input data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email address
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Generate secure password hash
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password against hash
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate random token
 */
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Redirect to a page
 */
function redirect($url) {
    // If URL is already absolute (starts with http:// or https://), use it as is
    if (preg_match('/^https?:\/\//', $url)) {
        // Remove port number from absolute URLs if present
        $url = preg_replace('/:8080(\/|$)/', '$1', $url);
        header("Location: $url");
        exit();
    }
    
    // For relative URLs, construct the full URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Remove port number from host (e.g., localhost:8080 -> localhost)
    if (strpos($host, ':') !== false) {
        $host = explode(':', $host)[0];
    }
    
    // For localhost, always use without port
    if ($host === 'localhost' || $host === '127.0.0.1') {
        $host = 'localhost';
    }
    
    // Get the current script directory
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $current_dir = dirname($script);
    
    // Remove trailing slash
    if ($current_dir === '/' || $current_dir === '\\') {
        $current_dir = '';
    }
    
    // Handle relative URL
    if (strpos($url, '/') === 0) {
        // Absolute path from root
        $full_url = $protocol . '://' . $host . $url;
    } else {
        // Relative path
        $full_url = $protocol . '://' . $host . $current_dir . '/' . ltrim($url, '/');
    }
    
    // Normalize the URL (remove double slashes except after protocol)
    $full_url = preg_replace('#([^:])//+#', '$1/', $full_url);
    
    header("Location: $full_url");
    exit();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function get_current_user_id() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current user data
 */
function get_current_user_data($db) {
    if (!is_logged_in()) {
        return null;
    }
    
    $sql = "SELECT id, username, email, first_name, last_name FROM users WHERE id = ? AND is_active = 1";
    return $db->fetch($sql, [get_current_user_id()]);
}

/**
 * Format file size
 */
function format_file_size($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

/**
 * Get file extension
 */
function get_file_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file type is allowed
 */
function is_allowed_file_type($filename) {
    $extension = get_file_extension($filename);
    return in_array($extension, ALLOWED_FILE_TYPES);
}

/**
 * Generate unique filename
 */
function generate_unique_filename($original_filename) {
    $extension = get_file_extension($original_filename);
    $basename = pathinfo($original_filename, PATHINFO_FILENAME);
    $basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $basename);
    return $basename . '_' . time() . '_' . uniqid() . '.' . $extension;
}

/**
 * Format date for display
 */
function format_date($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

/**
 * Truncate text
 */
function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get resource type icon
 */
function get_resource_type_icon($type) {
    $icons = [
        'document' => '📄',
        'link' => '🔗',
        'video' => '🎥',
        'tutorial' => '📚',
        'other' => '📁'
    ];
    return isset($icons[$type]) ? $icons[$type] : $icons['other'];
}

/**
 * Display flash messages
 */
function display_flash_messages() {
    if (isset($_SESSION['flash_messages'])) {
        foreach ($_SESSION['flash_messages'] as $message) {
            echo '<div class="alert alert-' . $message['type'] . '">' . $message['text'] . '</div>';
        }
        unset($_SESSION['flash_messages']);
    }
}

/**
 * Add flash message
 */
function add_flash_message($text, $type = 'info') {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = ['text' => $text, 'type' => $type];
}

/**
 * Validate CSRF token
 */
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generate_token();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Get CSRF token input field
 */
function csrf_token_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}
?>

