# Admin Role Implementation Guide

## Overview
This implementation adds role-based access control (RBAC) to the Vintage Collection system, allowing admin users to edit and delete ALL items in the system, not just their own.

## Changes Made

### 1. Database Schema Updates
**File: `database/schema.sql`**
- Added `role` column to `users` table with default value 'user'
- Updated admin user insert with role = 'admin'
- Admin credentials: username: `admin`, password: `admin123`

### 2. Migration Script
**File: `database/add_admin_role.php`**
- Checks if `role` column exists in users table
- Adds `role` column if it doesn't exist
- Creates admin user if not exists
- Updates existing admin user's role to 'admin'
- Sets default 'user' role for all existing users without a role

### 3. Login System
**File: `login.php`**
- Already includes role column detection
- Sets `$_SESSION["role"]` during login
- Defaults to 'user' role if column doesn't exist

### 4. Registration System
**File: `register.php`**
- Updated to insert new users with 'user' role by default
- Includes backward compatibility check for role column

### 5. User Interface
**File: `includes/header.php`**
- Added red "ADMIN" badge next to admin usernames in navigation

### 6. Permission Checks
**Files: `update.php`, `delete.php`, `index.php`**
- Already include admin permission checks
- Admin users can edit/delete any item
- Regular users can only edit/delete their own items
- Permission check: `($owner_id == $_SESSION["id"] || $_SESSION["role"] == 'admin')`

## How It Works

### Admin Permissions
When a user logs in as admin (username: `admin`, password: `admin123`):
1. Session variable `$_SESSION["role"]` is set to 'admin'
2. All edit/delete buttons are visible for ALL items (not just owned items)
3. When attempting to edit/delete, permission check validates admin role
4. Admin badge is displayed in navigation bar

### Regular User Permissions
When a regular user logs in:
1. Session variable `$_SESSION["role"]` is set to 'user'
2. Only edit/delete buttons for their own items are visible
3. Attempting to edit/delete others' items results in permission error
4. No admin badge in navigation

## Installation Steps

### For New Installations
1. Import `database/schema.sql` into MySQL
2. Admin user is automatically created with role

### For Existing Installations
1. Run `database/add_admin_role.php` in browser
2. This will:
   - Add role column to users table
   - Create or update admin user
   - Set default roles for existing users

## Testing

### Test Admin Access
1. Login as admin (username: `admin`, password: `admin123`)
2. Navigate to home page
3. Verify you see edit/delete buttons for ALL items
4. Try editing an item you don't own - should succeed
5. Try deleting an item you don't own - should succeed
6. Verify "ADMIN" badge appears next to username in navigation

### Test Regular User Access
1. Register a new user or login as existing user
2. Navigate to home page
3. Verify you only see edit/delete buttons for your own items
4. Try editing another user's item directly via URL - should show permission error
5. Verify no "ADMIN" badge appears in navigation

## Security Features

1. **Password Hashing**: Admin password is hashed using `password_hash()`
2. **Session-Based Authentication**: Role stored in session after login
3. **Database Validation**: Permission checks query database to verify ownership
4. **Prepared Statements**: All database queries use prepared statements
5. **Permission Errors**: Clear error messages when unauthorized access attempted

## File Structure

```
alparslanproje/
├── database/
│   ├── schema.sql (Updated with role column)
│   ├── add_admin_role.php (Migration script - NEW)
│   └── add_user_id_column.php (Existing migration)
├── includes/
│   ├── header.php (Updated with admin badge)
│   ├── auth_check.php (Unchanged)
│   └── footer.php (Unchanged)
├── index.php (Already has admin checks)
├── update.php (Already has admin checks)
├── delete.php (Already has admin checks)
├── login.php (Already has role support)
├── register.php (Updated to set default role)
└── README.md (Updated with admin info)
```

## Default Admin Credentials

```
Username: admin
Password: admin123
```

⚠️ **IMPORTANT**: Change the admin password after first login in production!

## Notes

- The codebase already had admin role infrastructure in place
- `index.php`, `update.php`, and `delete.php` already had admin permission checks
- Only needed to add database support and update registration
- Backward compatible: works even if role column doesn't exist
