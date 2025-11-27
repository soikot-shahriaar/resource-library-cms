/**
 * Resource Library CMS - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    
    if (mobileMenuToggle && mobileNav) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileNav.classList.toggle('active');
            
            // Update toggle icon
            if (mobileNav.classList.contains('active')) {
                mobileMenuToggle.textContent = '✕';
            } else {
                mobileMenuToggle.textContent = '☰';
            }
        });
        
        // Close mobile menu when clicking on a link
        const mobileNavLinks = mobileNav.querySelectorAll('a');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileNav.classList.remove('active');
                mobileMenuToggle.textContent = '☰';
            });
        });
    }
    
    // Auto-hide flash messages after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
    
    // Form validation enhancements
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        // Add loading state to submit buttons
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Processing...';
                
                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }, 10000);
            }
        });
        
        // Real-time validation for password confirmation
        const passwordField = form.querySelector('input[name="password"], input[name="new_password"]');
        const confirmPasswordField = form.querySelector('input[name="confirm_password"]');
        
        if (passwordField && confirmPasswordField) {
            function validatePasswordMatch() {
                if (confirmPasswordField.value && passwordField.value !== confirmPasswordField.value) {
                    confirmPasswordField.setCustomValidity('Passwords do not match');
                } else {
                    confirmPasswordField.setCustomValidity('');
                }
            }
            
            passwordField.addEventListener('input', validatePasswordMatch);
            confirmPasswordField.addEventListener('input', validatePasswordMatch);
        }
        
        // Email validation
        const emailFields = form.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            field.addEventListener('blur', function() {
                if (this.value && !isValidEmail(this.value)) {
                    this.setCustomValidity('Please enter a valid email address');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    });
    
    // File upload preview and validation
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // File size validation
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    alert('File size exceeds 10MB limit. Please choose a smaller file.');
                    this.value = '';
                    return;
                }
                
                // Show file info
                let infoDiv = this.parentNode.querySelector('.file-info-preview');
                if (!infoDiv) {
                    infoDiv = document.createElement('div');
                    infoDiv.className = 'file-info-preview';
                    this.parentNode.appendChild(infoDiv);
                }
                
                infoDiv.innerHTML = `
                    <small style="color: #28a745; margin-top: 0.5rem; display: block;">
                        Selected: ${file.name} (${formatFileSize(file.size)})
                    </small>
                `;
            }
        });
    });
    
    // Search form auto-submit on filter change
    const filterForm = document.querySelector('.filter-form');
    if (filterForm) {
        const filterSelects = filterForm.querySelectorAll('select');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                // Auto-submit form when filter changes
                filterForm.submit();
            });
        });
    }
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Confirmation dialogs for delete actions
    const deleteLinks = document.querySelectorAll('a[href*="delete"], .btn-danger');
    deleteLinks.forEach(link => {
        if (!link.onclick) { // Don't override existing onclick handlers
            link.addEventListener('click', function(e) {
                if (this.href && this.href.includes('delete')) {
                    const confirmed = confirm('Are you sure you want to delete this item? This action cannot be undone.');
                    if (!confirmed) {
                        e.preventDefault();
                    }
                }
            });
        }
    });
    
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
        
        // Initial resize
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    });
    
    // Copy to clipboard functionality
    const copyButtons = document.querySelectorAll('[data-copy]');
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const textToCopy = this.getAttribute('data-copy');
            navigator.clipboard.writeText(textToCopy).then(() => {
                const originalText = this.textContent;
                this.textContent = 'Copied!';
                setTimeout(() => {
                    this.textContent = originalText;
                }, 2000);
            });
        });
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K for search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
            }
        }
        
        // Escape to close modals/dropdowns
        if (e.key === 'Escape') {
            const activeDropdowns = document.querySelectorAll('.dropdown-menu:hover');
            activeDropdowns.forEach(dropdown => {
                dropdown.style.opacity = '0';
                dropdown.style.visibility = 'hidden';
            });
        }
    });
    
    // Lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    if (images.length > 0 && 'IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
    
    // Progress indicator for file uploads
    const uploadForms = document.querySelectorAll('form[enctype="multipart/form-data"]');
    uploadForms.forEach(form => {
        form.addEventListener('submit', function() {
            const fileInput = form.querySelector('input[type="file"]');
            if (fileInput && fileInput.files.length > 0) {
                showUploadProgress();
            }
        });
    });
    
    // Table sorting (basic)
    const sortableHeaders = document.querySelectorAll('th[data-sort]');
    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const column = this.cellIndex;
            const isAscending = !this.classList.contains('sort-asc');
            
            // Remove sort classes from all headers
            sortableHeaders.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
            
            // Add sort class to current header
            this.classList.add(isAscending ? 'sort-asc' : 'sort-desc');
            
            // Sort rows
            rows.sort((a, b) => {
                const aText = a.cells[column].textContent.trim();
                const bText = b.cells[column].textContent.trim();
                
                if (isAscending) {
                    return aText.localeCompare(bText);
                } else {
                    return bText.localeCompare(aText);
                }
            });
            
            // Reorder rows in DOM
            rows.forEach(row => tbody.appendChild(row));
        });
    });
});

// Utility functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function showUploadProgress() {
    // Create progress indicator
    const progressDiv = document.createElement('div');
    progressDiv.className = 'upload-progress';
    progressDiv.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; right: 0; background: #667eea; color: white; padding: 1rem; text-align: center; z-index: 9999;">
            <div>Uploading file... Please wait.</div>
            <div style="margin-top: 0.5rem; background: rgba(255,255,255,0.3); height: 4px; border-radius: 2px;">
                <div style="background: white; height: 100%; width: 0%; border-radius: 2px; animation: progressBar 3s ease-in-out infinite;"></div>
            </div>
        </div>
    `;
    
    document.body.appendChild(progressDiv);
    
    // Add CSS animation
    if (!document.querySelector('#progressBarStyle')) {
        const style = document.createElement('style');
        style.id = 'progressBarStyle';
        style.textContent = `
            @keyframes progressBar {
                0% { width: 0%; }
                50% { width: 70%; }
                100% { width: 100%; }
            }
        `;
        document.head.appendChild(style);
    }
}

// Theme toggle (for future enhancement)
function toggleTheme() {
    const body = document.body;
    const isDark = body.classList.contains('dark-theme');
    
    if (isDark) {
        body.classList.remove('dark-theme');
        localStorage.setItem('theme', 'light');
    } else {
        body.classList.add('dark-theme');
        localStorage.setItem('theme', 'dark');
    }
}

// Initialize theme from localStorage
function initializeTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
}

// Call theme initialization
initializeTheme();

