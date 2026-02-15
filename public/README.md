# Vintage & Retro Collection Tracking System

A PHP/MySQL web application for tracking vintage and retro collectibles.

## About This Project

This project was developed as part of a **WEB PROGRAMMING** course assignment. It demonstrates practical implementation of:

- PHP backend development with MySQLi
- MySQL database design and management
- User authentication and authorization systems
- CRUD operations with prepared statements
- File upload handling and validation
- Security best practices (SQL injection prevention, XSS protection, password hashing)
- Responsive web design with Bootstrap

The project is shared publicly for educational purposes and portfolio demonstration.

## Features

- **Authentication System**: User login and registration with password hashing
- **Role-Based Access Control**: Admin users can edit/delete all items, regular users can only manage their own items
- **CRUD Operations**: Create, Read, Update, Delete items
- **Search Functionality**: Search items by name
- **Pagination**: Browse items with pagination (10 items per page)
- **Bootstrap UI**: Modern, responsive design with Bootstrap 4.5
- **Security**: MySQLi prepared statements to prevent SQL injection

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB
- Web server (Apache/Nginx) or WAMP/XAMPP/MAMP

### Setup Instructions

1. **Clone or Download the Repository**:
   ```bash
   git clone https://github.com/YOUR_USERNAME/retro-collection-tracker.git
   cd retro-collection-tracker
   ```

2. **Configure Database Connection**:
   - Copy `config.example.php` to `public/config.php`:
     ```bash
     cp config.example.php public/config.php
     ```
   - Edit `public/config.php` with your database credentials:
     ```php
     define('DB_SERVER', 'localhost');
     define('DB_USERNAME', 'root');
     define('DB_PASSWORD', 'your_password');
     define('DB_NAME', 'retro_koleksiyon');
     ```

3. **Setup Database**:
   
   **Option A - Automatic (Recommended)**:
   - Run `public/createDB.php` to create the database
   - Run `public/createTable.php` to create the tables
   - Run `public/insertRecords.php` to insert sample data
   - Run `public/database/add_user_id_column.php` to update schema
   - Run `public/database/add_admin_role.php` to add admin support
   
   **Option B - Manual**:
   - Import `public/database/schema.sql` into your MySQL database
   - This creates all tables and adds sample data automatically

4. **Set Upload Directory Permissions**:
   ```bash
   chmod 755 public/uploads
   ```
   (On Windows, ensure the uploads folder has write permissions)

5. **Access the Application**:
   - Local development: `http://localhost/retro-collection-tracker/public/index.php`
   - Or configure your web server to point to the `public/` directory
   
6. **Default Login Credentials**:
   - **Admin User**:
     - Username: `admin`
     - Password: `admin123`
   - Admin users can edit and delete ALL items in the system
   - **Important**: Change the default admin password after first login!

### For Existing Installations (Migration)

If you already have an existing database, run these migrations in order:
1. Open: `http://localhost/YOUR_PATH/public/database/add_user_id_column.php`
2. Open: `http://localhost/YOUR_PATH/public/database/add_admin_role.php`

## File Structure

```
retro-collection-tracker/
├── .gitignore                   # Git ignore rules (protects config.php and uploads)
├── config.example.php           # Database configuration template
├── README.md                    # This file
└── public/                      # Main application directory
    ├── config.php               # Database configuration (NOT in Git)
    ├── index.php                # Main listing page with slider, pagination, search
    ├── login.php                # User login page
    ├── register.php             # User registration page
    ├── create.php               # Add new collectible form
    ├── read.php                 # View single item details
    ├── update.php               # Edit item form
    ├── delete.php               # Delete item with confirmation
    ├── logout.php               # Session destroy and redirect
    ├── my_items.php             # User's own items listing
    ├── createDB.php             # Database creation script
    ├── createTable.php          # Table creation script
    ├── insertRecords.php        # Sample data insertion script
    ├── assets/
    │   ├── css/                 # Custom styles
    │   └── js/                  # Custom JavaScript
    ├── includes/
    │   ├── header.php           # Common header with navigation
    │   ├── footer.php           # Common footer
    │   └── auth_check.php       # Session validation helper
    ├── database/
    │   ├── schema.sql           # Complete database schema
    │   ├── add_user_id_column.php
    │   └── add_admin_role.php
    └── uploads/                 # User uploaded images (NOT in Git)
        └── .gitkeep             # Keeps directory in Git
```

## Technologies Used

- PHP 7.4+
- MySQL/MariaDB
- MySQLi Extension
- Bootstrap 4.5
- FontAwesome 6.4.2
- jQuery 3.5.1

## Security Features

- **MySQLi Prepared Statements**: Prevents SQL injection attacks
- **Password Hashing**: Uses `password_hash()` with bcrypt algorithm
- **Session-Based Authentication**: Secure session management
- **Input Validation**: Server-side validation on all forms
- **XSS Prevention**: Output escaping with `htmlspecialchars()`
- **Role-Based Access Control**: Admin and regular user permissions
- **Protected Configuration**: `config.php` excluded from Git repository
- **Secure File Uploads**: File type and size validation for images

### Security Notes

- **IMPORTANT**: The `config.php` file containing database credentials is NOT included in the repository for security reasons
- Always use `config.example.php` as a template and create your own `config.php` locally
- The `uploads/` directory is also excluded from Git - actual uploaded images stay local
- Change default admin password (`admin123`) immediately after installation
- Use strong passwords for database and admin accounts in production

## Usage

1. **Login**: Use the login page to access the system
2. **View Items**: Browse all items on the main page
3. **Search**: Use the search bar to filter items by name
4. **Add Item**: Click "Add New Item" to create a new collectible
5. **View Details**: Click the eye icon to view item details
6. **Edit**: Click the pencil icon to edit an item
7. **Delete**: Click the trash icon to delete an item

## Notes

- All database operations use MySQLi prepared statements
- The application follows secure coding practices
- All `mysql_*` functions have been updated to `mysqli_*`
- Proper error handling and validation implemented throughout
- Uploaded images are stored locally and not tracked by Git

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open source and available for educational purposes.

## Roadmap

See [PROJECT_ISSUES.md](public/PROJECT_ISSUES.md) for planned improvements and known issues.



