<?php
/**
 * Migration script to add role column and create admin user
 * Run this once to update your existing database
 */

// Include config file
require_once "../config.php";

echo "<h2>Database Migration: Adding Admin Role Support</h2>";
echo "<hr>";

// Step 1: Check if role column exists
echo "<h3>Step 1: Checking if 'role' column exists in users table...</h3>";
$result = mysqli_query($link, "SHOW COLUMNS FROM users LIKE 'role'");

if (mysqli_num_rows($result) == 0) {
    // Add role column
    echo "<p>Adding 'role' column to users table...</p>";
    $sql = "ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user' AFTER password";
    
    if (mysqli_query($link, $sql)) {
        echo "<p style='color: green;'>✓ Successfully added 'role' column to users table.</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding role column: " . mysqli_error($link) . "</p>";
        exit();
    }
} else {
    echo "<p style='color: blue;'>ℹ 'role' column already exists in users table.</p>";
}

// Step 2: Check if admin user exists
echo "<h3>Step 2: Checking if admin user exists...</h3>";
$check_sql = "SELECT id, username, role FROM users WHERE username = 'admin'";
$result = mysqli_query($link, $check_sql);

if (mysqli_num_rows($result) > 0) {
    // Admin user exists, update role
    $row = mysqli_fetch_assoc($result);
    echo "<p>Admin user exists (ID: " . $row['id'] . ", Current role: " . $row['role'] . ")</p>";
    
    if ($row['role'] != 'admin') {
        echo "<p>Updating admin user's role to 'admin'...</p>";
        $update_sql = "UPDATE users SET role = 'admin' WHERE username = 'admin'";
        
        if (mysqli_query($link, $update_sql)) {
            echo "<p style='color: green;'>✓ Successfully updated admin user's role.</p>";
        } else {
            echo "<p style='color: red;'>✗ Error updating role: " . mysqli_error($link) . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ Admin user already has 'admin' role.</p>";
    }
} else {
    // Create admin user
    echo "<p>Creating admin user...</p>";
    echo "<p>Username: admin</p>";
    echo "<p>Password: admin123</p>";
    
    // Hash the password (admin123)
    $hashed_password = password_hash("admin123", PASSWORD_DEFAULT);
    
    $insert_sql = "INSERT INTO users (username, password, role) VALUES ('admin', ?, 'admin')";
    
    if ($stmt = mysqli_prepare($link, $insert_sql)) {
        mysqli_stmt_bind_param($stmt, "s", $hashed_password);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<p style='color: green;'>✓ Successfully created admin user.</p>";
            echo "<p><strong>Login Credentials:</strong></p>";
            echo "<ul>";
            echo "<li>Username: <strong>admin</strong></li>";
            echo "<li>Password: <strong>admin123</strong></li>";
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>✗ Error creating admin user: " . mysqli_error($link) . "</p>";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "<p style='color: red;'>✗ Error preparing statement: " . mysqli_error($link) . "</p>";
    }
}

// Step 3: Update existing users without a role
echo "<h3>Step 3: Updating existing users without a role...</h3>";
$update_sql = "UPDATE users SET role = 'user' WHERE role IS NULL OR role = ''";
$result = mysqli_query($link, $update_sql);

if ($result) {
    $affected = mysqli_affected_rows($link);
    if ($affected > 0) {
        echo "<p style='color: green;'>✓ Updated " . $affected . " user(s) with default 'user' role.</p>";
    } else {
        echo "<p style='color: blue;'>ℹ No users needed updating.</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Error updating users: " . mysqli_error($link) . "</p>";
}

// Close connection
mysqli_close($link);

echo "<hr>";
echo "<h3>Migration Complete!</h3>";
echo "<p>Admin user can now edit and delete all items in the system.</p>";
echo "<p><a href='../index.php'>Go to Home Page</a> | <a href='../login.php'>Go to Login</a></p>";
?>
