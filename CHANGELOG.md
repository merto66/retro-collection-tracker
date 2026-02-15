# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-02-15

### Added
- User authentication system with login and registration
- Role-based access control (Admin and Regular users)
- CRUD operations for collectible items
- Image upload functionality with validation (JPG, PNG, GIF - max 5MB)
- Search functionality to filter items by name
- Pagination system (10 items per page)
- Bootstrap 4.5 responsive UI with modern design
- MySQLi prepared statements for SQL injection prevention
- Password hashing with bcrypt algorithm
- Session-based authentication
- User can view and manage only their own items
- Admin users can edit/delete all items in the system
- Database auto-creation and schema setup
- Sample data insertion scripts

### Security
- Input validation and sanitization on all forms
- XSS prevention with htmlspecialchars()
- Protected configuration file (excluded from Git)
- Secure file upload validation
- Prepared statements for database queries

### Documentation
- Complete README with installation instructions
- Database schema documentation
- Configuration template file
- Project issues and roadmap documented

## [Unreleased]

### Planned Features
- Password reset functionality
- Email verification on registration
- User profile page
- Advanced filtering and sorting
- Export to PDF/Excel
- Dashboard with statistics
- Item comments and history
- Multiple images per item

---

For detailed issue tracking, see [PROJECT_ISSUES.md](public/PROJECT_ISSUES.md)
