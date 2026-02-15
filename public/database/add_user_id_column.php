<?php
// This script adds the user_id column to the items table if it doesn't exist
require_once "../config.php";

echo "Checking if user_id column exists...\n";

// Check if column exists
$check_sql = "SHOW COLUMNS FROM items LIKE 'user_id'";
$result = mysqli_query($link, $check_sql);

if (mysqli_num_rows($result) == 0) {
    echo "user_id column does not exist. Adding it now...\n";
    
    // Add user_id column
    $alter_sql = "ALTER TABLE items ADD COLUMN user_id INT NOT NULL DEFAULT 1 AFTER estimated_value";
    
    if (mysqli_query($link, $alter_sql)) {
        echo "✓ user_id column added successfully!\n";
        
        // Add foreign key constraint
        $fk_sql = "ALTER TABLE items ADD CONSTRAINT fk_items_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE";
        
        if (mysqli_query($link, $fk_sql)) {
            echo "✓ Foreign key constraint added successfully!\n";
        } else {
            echo "⚠ Warning: Could not add foreign key constraint: " . mysqli_error($link) . "\n";
        }
        
        // Check if image_path column exists
        $check_image_sql = "SHOW COLUMNS FROM items LIKE 'image_path'";
        $image_result = mysqli_query($link, $check_image_sql);
        
        if (mysqli_num_rows($image_result) == 0) {
            echo "image_path column does not exist. Adding it now...\n";
            $image_sql = "ALTER TABLE items ADD COLUMN image_path VARCHAR(500) AFTER user_id";
            
            if (mysqli_query($link, $image_sql)) {
                echo "✓ image_path column added successfully!\n";
            } else {
                echo "✗ Error adding image_path column: " . mysqli_error($link) . "\n";
            }
        } else {
            echo "✓ image_path column already exists.\n";
        }
        
    } else {
        echo "✗ Error adding user_id column: " . mysqli_error($link) . "\n";
    }
} else {
    echo "✓ user_id column already exists.\n";
}

mysqli_close($link);
echo "\nMigration complete!\n";
?>
