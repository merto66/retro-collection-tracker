<?php
// Include auth check and config file
require_once "includes/auth_check.php";
require_once "config.php";

// Process delete operation after confirmation
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    // Prepare a delete statement
    $sql = "DELETE FROM items WHERE id = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);

        // Set parameters
        $param_id = trim($_POST["id"]);

        // Check ownership or admin
        $check_sql = "SELECT user_id FROM items WHERE id = ?";
        if($check_stmt = mysqli_prepare($link, $check_sql)){
            mysqli_stmt_bind_param($check_stmt, "i", $param_id);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_bind_result($check_stmt, $owner_id);
            mysqli_stmt_fetch($check_stmt);
            mysqli_stmt_close($check_stmt);

            if($owner_id != $_SESSION["id"] && $_SESSION["role"] != 'admin'){
                die("Error: YOU DO NOT HAVE PERMISSION TO DELETE THIS ITEM.");
            }
        }

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Records deleted successfully. Redirect to landing page
            header("location: index.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($link);
} else {
    // Check existence of id parameter
    if (empty(trim($_GET["id"]))) {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: index.php");
        exit();
    }
}

$page_title = "Delete Record";
include "includes/header.php";
?>

<div class="row">
    <div class="col-md-12">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Delete Item</li>
            </ol>
        </nav>

        <h2 class="mt-5 mb-3">Delete Record</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="alert alert-danger">
                <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                <p>Are you sure you want to delete this item record?</p>
                <p>
                    <input type="submit" value="Yes" class="btn btn-danger">
                    <a href="index.php" class="btn btn-secondary">No</a>
                </p>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>


