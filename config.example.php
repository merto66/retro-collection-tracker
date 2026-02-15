<?php
/**
 * Database Configuration Template
 * 
 * SETUP INSTRUCTIONS:
 * 1. Copy this file to public/config.php
 * 2. Update the database credentials below with your actual values
 * 3. Make sure public/config.php is listed in .gitignore (it should be by default)
 * 
 * SECURITY NOTE:
 * Never commit config.php with real credentials to Git!
 */

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'YOUR_PASSWORD_HERE'); 
define('DB_NAME', 'retro_koleksiyon');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if ($link === false) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Unable to connect to the database. Please contact support if the problem persists.");
}

$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (!mysqli_query($link, $sql)) {
    error_log("Database creation failed: " . mysqli_error($link));
    die("Unable to initialize the database. Please contact support if the problem persists.");
}

mysqli_select_db($link, DB_NAME);
mysqli_set_charset($link, "utf8");
?>
