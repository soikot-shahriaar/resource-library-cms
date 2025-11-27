<?php
/**
 * Header Include
 * Resource Library CMS
 */

$current_user = get_current_user_data($db);
?>
<header class="main-header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="dashboard.php">
                    <h1><?php echo SITE_NAME; ?></h1>
                </a>
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="browse.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'browse.php' ? 'active' : ''; ?>">Browse</a></li>
                    <li><a href="add_resource.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add_resource.php' ? 'active' : ''; ?>">Add Resource</a></li>
                    <li><a href="my_resources.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'my_resources.php' ? 'active' : ''; ?>">My Resources</a></li>
                </ul>
            </nav>
            
            <div class="user-menu">
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($current_user['first_name'] . ' ' . $current_user['last_name']); ?></span>
                    <div class="user-dropdown">
                        <button class="dropdown-toggle">⚙️</button>
                        <div class="dropdown-menu">
                            <a href="profile.php">Profile</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile menu toggle -->
            <button class="mobile-menu-toggle">☰</button>
        </div>
    </div>
</header>

<!-- Mobile navigation -->
<nav class="mobile-nav">
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="browse.php">Browse</a></li>
        <li><a href="add_resource.php">Add Resource</a></li>
        <li><a href="my_resources.php">My Resources</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

