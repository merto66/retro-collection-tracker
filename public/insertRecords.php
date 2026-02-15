<?php
/* Database credentials */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'mert456');
define('DB_NAME', 'retro_koleksiyon');

/* Attempt MySQL server connection with database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Attempt insert query execution for users
$hashed_password = password_hash("admin123", PASSWORD_DEFAULT);
$sql = "INSERT INTO users (username, password) VALUES ('admin', '" . mysqli_real_escape_string($link, $hashed_password) . "')";

if (mysqli_query($link, $sql)) {
    echo "Admin user added successfully.<br>";
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link) . "<br>";
}

// Attempt insert query execution for items
$sql = "INSERT INTO items (item_name, category, description, estimated_value) VALUES
    ('Vintage Vinyl Record Player', 'Electronics', '1950s turntable in excellent working condition', 450.00),
    ('Retro Typewriter', 'Office Equipment', '1960s manual typewriter, fully functional', 320.00),
    ('Classic Camera Collection', 'Photography', 'Set of 3 vintage cameras from 1970s', 850.00)";

if (mysqli_query($link, $sql)) {
    echo "Sample items added successfully.";
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}

// Close connection
mysqli_close($link);
?>


