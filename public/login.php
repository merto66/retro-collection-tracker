<?php
// Initialize the session
session_start();

// Check if the user is already logged in
$already_logged_in = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$logged_in_username = $already_logged_in ? $_SESSION["username"] : "";

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Check if role column exists in users table
        $role_column_exists = false;
        $result = mysqli_query($link, "SHOW COLUMNS FROM users LIKE 'role'");
        if ($result && mysqli_num_rows($result) > 0) {
            $role_column_exists = true;
        }
        
        // Prepare a select statement based on whether role column exists
        if ($role_column_exists) {
            $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        } else {
            $sql = "SELECT id, username, password FROM users WHERE username = ?";
        }

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    if ($role_column_exists) {
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role);
                    } else {
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                        $role = 'user'; // Default role if column doesn't exist
                    }
                    
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = $role;

                            // Redirect user to index page
                            header("location: index.php");
                            exit();
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else {
                $login_err = "Database error: " . mysqli_error($link);
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            $login_err = "Database error: " . mysqli_error($link);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        .wrapper {
            width: 400px;
            padding: 20px;
            margin: 100px auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
        
        <?php if ($already_logged_in): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> You are already logged in as <strong><?php echo htmlspecialchars($logged_in_username); ?></strong>
            </div>
            <div class="text-center">
                <a href="index.php" class="btn btn-primary"><i class="fas fa-home"></i> Go to Home Page</a>
                <a href="logout.php" class="btn btn-secondary"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        <?php else: ?>
            <p>Please fill in your credentials to login.</p>

            <?php
            if (!empty($login_err)) {
                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' . htmlspecialchars($login_err) . '</div>';
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
                <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>


