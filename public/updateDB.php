<?php
require_once "config.php";

// Attempt to add image_path column
$sql = "ALTER TABLE items ADD COLUMN image_path VARCHAR(255) DEFAULT NULL";

if (mysqli_query($link, $sql)) {
    echo "Successfully added 'image_path' column to 'items' table.";
} else {
    // Check if error is because column already exists
    if (strpos(mysqli_error($link), "Duplicate column") !== false) {
        echo "'image_path' column already exists.";
    } else {
        echo "Error upgrading database: " . mysqli_error($link);
    }
}

mysqli_close($link);
?>
