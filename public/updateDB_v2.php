<?php
require_once "config.php";

// 1. Add 'role' column to 'users' table if not exists
$sql = "ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user'";
if (mysqli_query($link, $sql)) {
    echo "Added 'role' column to 'users' table.<br>";
} else {
    echo "Info: " . mysqli_error($link) . "<br>";
}

// 2. Add 'user_id' column to 'items' table if not exists
$sql = "ALTER TABLE items ADD COLUMN user_id INT DEFAULT NULL";
if (mysqli_query($link, $sql)) {
    echo "Added 'user_id' column to 'items' table.<br>";
} else {
    echo "Info: " . mysqli_error($link) . "<br>";
}

// 3. Update existing items (1, 2, 3, 4) with image paths and user_id (assuming user_id 1 is admin/default)
// We will assign them to user_id=1. If user 1 doesn't exist, it might be null, but that's okay for now.

$updates = [
    1 => "https://images.unsplash.com/photo-1603048588665-791ca8aea617?q=80&w=600",
    2 => "https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=600",
    3 => "https://images.unsplash.com/photo-1520690214124-2405c5217036?q=80&w=600",
    4 => "https://images.unsplash.com/photo-1550989460-0adf9ea622e2?q=80&w=600"
];

foreach ($updates as $id => $url) {
    $sql = "UPDATE items SET image_path = '$url', user_id = 1 WHERE id = $id";
    if (mysqli_query($link, $sql)) {
        echo "Updated item $id with image and user_id 1.<br>";
    } else {
        echo "Error updating item $id: " . mysqli_error($link) . "<br>";
    }
}

// 4. Make existing user ID 1 an 'admin' if it exists
$sql = "UPDATE users SET role = 'admin' WHERE id = 1";
mysqli_query($link, $sql);

mysqli_close($link);
?>
