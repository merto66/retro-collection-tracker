<?php
// Include auth check and config file
require_once "includes/auth_check.php";
require_once "config.php";

// Check existence of id parameter before processing further
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Get URL parameter
    $id = trim($_GET["id"]);

    // Prepare a select statement
    $sql = "SELECT * FROM items WHERE id = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);

        // Set parameters
        $param_id = $id;

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                // Retrieve individual field value
                $item_name = $row["item_name"];
                $category = $row["category"];
                $description = $row["description"];
                $estimated_value = $row["estimated_value"];
                $image_path = isset($row["image_path"]) ? $row["image_path"] : null;
            } else {
                // URL doesn't contain valid id. Redirect to error page
                header("location: index.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($link);
} else {
    // URL doesn't contain id parameter. Redirect to error page
    header("location: index.php");
    exit();
}

$page_title = "View Record";
include "includes/header.php";
?>

<div class="row">
    <div class="col-md-12">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">View Item</li>
            </ol>
        </nav>

        <h2 class="mt-5 mb-3">View Record</h2>
        <div class="form-group">
            <label>Item Name</label>
            <p><b><?php echo htmlspecialchars($item_name); ?></b></p>
        </div>
        <div class="form-group">
            <label>Category</label>
            <p><b><?php echo htmlspecialchars($category); ?></b></p>
        </div>
        <div class="form-group">
            <label>Description</label>
            <p><b><?php echo htmlspecialchars($description); ?></b></p>
        </div>
        <div class="form-group">
            <label>Estimated Value</label>
            <p><b>$<?php echo number_format($estimated_value, 2); ?></b></p>
        </div>
        <?php if(!empty($image_path)): ?>
        <div class="form-group">
            <label>Item Image</label>
            <br>
            <?php
            // Check if it's a URL or local file
            if (filter_var($image_path, FILTER_VALIDATE_URL)) {
                // It's a URL
                echo '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($item_name) . '" style="max-width: 500px; height: auto;" class="img-thumbnail">';
            } else {
                // It's a local file
                if (file_exists($image_path)) {
                    echo '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($item_name) . '" style="max-width: 500px; height: auto;" class="img-thumbnail">';
                } else {
                    echo '<p class="text-muted"><i class="fas fa-exclamation-triangle"></i> Image file not found.</p>';
                }
            }
            ?>
        </div>
        <?php endif; ?>
        <p><a href="index.php" class="btn btn-primary">Back</a></p>
    </div>
</div>

<?php include "includes/footer.php"; ?>


