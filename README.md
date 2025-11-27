# 📚 Resource Library CMS

A modern, secure, and user-friendly Content Management System for organizing and sharing educational resources, documents, links, videos, and tutorials. Built with cutting-edge web technologies, featuring robust user authentication, file management, categorization, and advanced search functionality.

## 🚀 Technologies Used

- **Backend**: PHP 7.4+ with PDO for database operations
- **Database**: MySQL 5.7+ / MariaDB 10.2+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Styling**: Custom CSS with responsive design principles
- **Icons**: Font Awesome 6.0 for modern iconography
- **Security**: bcrypt password hashing, CSRF protection, XSS prevention
- **Server**: Apache/Nginx compatible with .htaccess support

## 📋 Project Overview

Resource Library CMS is designed to be a comprehensive solution for educational institutions, businesses, and organizations that need to manage and share various types of digital resources. The system provides an intuitive interface for users to upload, categorize, search, and access resources while maintaining strict security standards and user access controls.

### Key Benefits
- **Centralized Resource Management**: All resources in one organized location
- **Multi-User Support**: Role-based access control and user management
- **Flexible Resource Types**: Support for documents, links, videos, and more
- **Advanced Search**: Powerful filtering and search capabilities
- **Responsive Design**: Works seamlessly on all devices and screen sizes
- **Security First**: Enterprise-grade security with modern encryption

## ✨ Key Features

### 🔐 Authentication & Security
- Secure user registration and login system
- bcrypt password hashing with salt
- Session management with configurable timeout
- CSRF token protection on all forms
- Input sanitization and validation
- XSS protection through output escaping

### 📁 Resource Management
- **Multiple Resource Types**: Documents, links, videos, tutorials, and more
- **File Upload System**: Secure file handling with type validation
- **External Link Support**: Add and manage web resources
- **Category Organization**: Hierarchical resource categorization
- **Metadata Management**: Rich descriptions and tagging

### 🔍 Search & Discovery
- **Full-Text Search**: Search across titles and descriptions
- **Advanced Filtering**: Filter by category, type, and user
- **Smart Results**: Relevance-based search results
- **Quick Access**: Recent and popular resources

### 👥 User Experience
- **Personal Dashboard**: User-specific statistics and recent activity
- **Resource Ownership**: Users can manage their own resources
- **Profile Management**: Update personal information and passwords
- **Responsive Interface**: Mobile-first design approach

### 📊 Admin Features
- **User Management**: Monitor and manage user accounts
- **System Statistics**: Overview of system usage and resources
- **Content Moderation**: Review and approve user submissions
- **System Configuration**: Customize settings and parameters

## 👤 User Roles

### 🔑 Admin User
- **Full System Access**: Complete control over all features
- **User Management**: Create, edit, and delete user accounts
- **System Configuration**: Modify system settings and parameters
- **Content Moderation**: Review and approve all submissions
- **Analytics Access**: View comprehensive system statistics

### 👤 Regular User
- **Resource Management**: Upload, edit, and delete personal resources
- **Browse & Search**: Access all public resources in the system
- **Profile Management**: Update personal information and settings
- **Dashboard Access**: View personal statistics and recent activity

### 🚫 Guest User
- **Browse Only**: View public resources without authentication
- **Search Access**: Use search and filtering capabilities
- **No Uploads**: Cannot add or modify content

## 🏗️ Project Structure

```
resource-library-cms/
├── 📁 assets/                    # Frontend assets
│   ├── 📁 css/
│   │   └── style.css            # Main stylesheet with responsive design
│   ├── 📁 js/
│   │   └── main.js              # JavaScript functionality
│   └── 📁 images/               # Static images and icons
├── 📁 config/                    # Configuration files
│   └── database.php             # Database and system configuration
├── 📁 includes/                  # Core PHP files
│   ├── Database.php             # Database connection and operations class
│   ├── functions.php            # Utility functions and helpers
│   ├── init.php                 # Application initialization
│   ├── header.php               # Header template
│   └── footer.php               # Footer template
├── 📁 pages/                     # Application pages
│   ├── login.php                # User authentication
│   ├── register.php             # User registration
│   ├── dashboard.php            # Main user dashboard
│   ├── add_resource.php         # Resource creation
│   ├── edit_resource.php        # Resource editing
│   ├── view_resource.php        # Resource viewing
│   ├── delete_resource.php      # Resource deletion
│   ├── browse.php               # Resource browsing and search
│   ├── my_resources.php         # User's personal resources
│   ├── profile.php              # User profile management
│   ├── download.php             # File download handler
│   ├── preview.php              # File preview handler
│   └── logout.php               # User logout
├── 📁 sql/                       # Database files
│   ├── setup.sql                # Database schema and structure
│   └── sample_data.sql          # Sample data for testing
├── 📁 uploads/                   # File storage directory
├── index.php                     # Application entry point
└── README.md                     # This documentation
```

## ⚙️ Setup Instructions

### Prerequisites
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: Version 7.4 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.2+
- **Extensions**: PDO MySQL, GD, FileInfo, OpenSSL
- **Memory**: Minimum 512MB RAM, 1GB recommended
- **Storage**: 1GB+ disk space (more for file uploads)

### 1. Download and Extract
```bash
# Clone the repository
git clone <repository-url> resource-library-cms
cd resource-library-cms

# Or download and extract manually
wget <download-url>
tar -xzf resource-library-cms.tar.gz
cd resource-library-cms
```

### 2. Database Setup
```sql
-- Create database
CREATE DATABASE resource_library_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user (optional but recommended)
CREATE USER 'rlcms_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON resource_library_cms.* TO 'rlcms_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Import Database Schema
```bash
# Import main schema
mysql -u username -p resource_library_cms < sql/setup.sql

# Import sample data (optional)
mysql -u username -p resource_library_cms < sql/sample_data.sql
```

### 4. Configuration
Edit `config/database.php` with your settings:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'resource_library_cms');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_CHARSET', 'utf8mb4');

// System configuration
define('SITE_NAME', 'Resource Library CMS');
define('SITE_URL', 'http://localhost'); // Use 'http://localhost' for local development, or your domain for production
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('SESSION_TIMEOUT', 3600); // 1 hour
```

### 5. File Permissions
```bash
# Set proper permissions
chmod -R 755 resource-library-cms/
chmod -R 777 resource-library-cms/uploads/
chown -R www-data:www-data resource-library-cms/uploads/
```

### 6. Web Server Configuration

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
```

## 🎯 Usage

### 🚀 Getting Started
1. **Access the Application**: Navigate to `http://localhost/project_name` in a web browser (replace `project_name` with your actual project folder name)
2. **First Time Setup**: Use the demo credentials or create a new account
3. **Explore Dashboard**: Familiarize yourself with the main interface
4. **Add Resources**: Start building your resource library

**Note**: The application is configured to work with `http://localhost/project_name` (without port numbers). If you're using a different port, the system will automatically redirect to the correct URL without the port.

### 🔑 Demo Credentials
For testing and demonstration purposes, the following accounts are pre-configured:

#### Admin Account
- **Username**: `admin`
- **Password**: `admin123`
- **Access Level**: Full system administrator
- **Capabilities**: User management, system configuration, content moderation

#### Test User Accounts
- **Username**: `john_doe` | **Password**: `password123`
- **Username**: `jane_smith` | **Password**: `password123`
- **Username**: `mike_wilson` | **Password**: `password123`
- **Access Level**: Regular user
- **Capabilities**: Resource management, browsing, profile updates

### 📚 Adding Resources
1. **Navigate to Add Resource**: Click "Add Resource" from dashboard or navigation
2. **Fill Resource Details**:
   - **Title**: Descriptive and searchable resource name
   - **Description**: Detailed explanation of content and purpose
   - **Type**: Select appropriate category (document, link, video, tutorial, other)
   - **Category**: Choose relevant organizational category
   - **File/URL**: Upload file or enter external web address
3. **Submit**: Save and make resource available to users

### 🔍 Resource Discovery
- **Search Bar**: Quick search across all resource titles and descriptions
- **Category Filter**: Browse resources by organizational category
- **Type Filter**: Filter by resource type (document, link, video, etc.)
- **Advanced Search**: Combine multiple filters for precise results

### 👤 User Management
- **Profile Updates**: Modify personal information and contact details
- **Password Changes**: Secure password updates with current password verification
- **Resource Management**: View, edit, and delete personal resources
- **Activity Tracking**: Monitor resource usage and system activity

## 🎯 Intended Use

### 🏫 Educational Institutions
- **Course Materials**: Organize and distribute educational resources
- **Research Libraries**: Centralize academic papers and research materials
- **Student Portals**: Provide easy access to learning resources
- **Faculty Collaboration**: Share teaching materials and best practices

### 🏢 Business Organizations
- **Knowledge Management**: Centralize company documents and resources
- **Training Materials**: Organize employee training and development resources
- **Project Documentation**: Store and share project-related materials
- **Compliance Resources**: Maintain regulatory and compliance documentation

### 🏛️ Government & Non-Profits
- **Public Resources**: Share information with citizens and stakeholders
- **Internal Knowledge**: Organize internal documentation and procedures
- **Collaboration**: Enable cross-departmental resource sharing
- **Public Access**: Provide controlled access to public information

### 🏠 Personal & Small Business
- **Portfolio Management**: Organize and showcase work samples
- **Reference Library**: Maintain personal knowledge base
- **Client Resources**: Share materials with clients and customers
- **Project Archives**: Store completed project materials

## 📄 License

**License for RiverTheme**

RiverTheme makes this project available for demo, instructional, and personal use. You can ask for or buy a license from [RiverTheme.com](https://RiverTheme.com) if you want a pro website, sophisticated features, or expert setup and assistance. A Pro license is needed for production deployments, customizations, and commercial use.

**Disclaimer**

The free version is offered "as is" with no warranty and might not function on all devices or browsers. It might also have some coding or security flaws. For additional information or to get a Pro license, please get in touch with [RiverTheme.com](https://RiverTheme.com).
