<?php
/**
 * Footer Include
 * Resource Library CMS
 */
?>
<footer class="main-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php echo SITE_NAME; ?></h3>
                <p>A simple and efficient resource library management system.</p>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="browse.php">Browse Resources</a></li>
                    <li><a href="add_resource.php">Add Resource</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Help</h4>
                <ul>
                    <li><a href="#" onclick="alert('Help documentation coming soon!')">Documentation</a></li>
                    <li><a href="#" onclick="alert('Support contact: admin@example.com')">Support</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Developed by <a href="https://rivertheme.com" target="_blank" rel="noopener noreferrer">Rivertheme</a>.</p>
        </div>
    </div>
</footer>

<script src="../assets/js/main.js"></script>

