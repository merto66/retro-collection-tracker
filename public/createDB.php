<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup</title>
    
</head>
<body>
<div class="container">
    <h2>Database Setup</h2>
    <?php
    /* Database credentials */
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'retro_koleksiyon');
    
    /* Attempt MySQL server connection without database (to create it) */
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

    // Check connection
    if ($link === false) {
        echo '<p class="error">❌ ERROR: Could not connect to MySQL server. ' . mysqli_connect_error() . '</p>';
        echo '<p class="error">Please make sure WAMP MySQL service is running.</p>';
        exit;
    }

    echo '<p class="success">✓ Connected to MySQL server successfully.</p>';

    // Check if database already exists
    $result = mysqli_query($link, "SHOW DATABASES LIKE '" . DB_NAME . "'");
    $db_exists = mysqli_num_rows($result) > 0;

    if ($db_exists) {
        echo '<p class="info">ℹ Database "' . DB_NAME . '" already exists.</p>';
    }

    // Attempt create database query execution
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;

    if (mysqli_query($link, $sql)) {
        if (!$db_exists) {
            echo '<p class="success">✓ Database "retro_koleksiyon" created successfully.</p>';
        }
        
        // Verify database exists
        $result = mysqli_query($link, "SHOW DATABASES LIKE '" . DB_NAME . "'");
        if (mysqli_num_rows($result) > 0) {
            echo '<p class="success">✓ Database verification: Database exists and is ready to use.</p>';
        } else {
            echo '<p class="warning">⚠ Warning: Database may not have been created properly.</p>';
        }
    } else {
        echo '<p class="error">❌ ERROR: Could not execute query. ' . mysqli_error($link) . '</p>';
    }

    // Close connection
    mysqli_close($link);
    ?>
    
    <hr style="margin: 20px 0;">
    <p><strong>Next Steps:</strong></p>
    <ul>
        <li><a href="createTable.php">Create Tables</a> - Tabloları oluşturmak için tıklayın</li>
        <li><a href="login.php">Go to Login</a> - Giriş sayfasına gitmek için tıklayın</li>
    </ul>
</div>
</body>
</html>


