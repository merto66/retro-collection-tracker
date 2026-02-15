<?php
/**
 * Create New Item
 * 
 * Allows authenticated users to add new collectible items to the database.
 * Handles form validation, image uploads, and database insertion.
 */

require_once "includes/auth_check.php";
require_once "config.php";

/**
 * Validates and uploads an image file
 * 
 * @param array $file The $_FILES array element for the uploaded file
 * @param string &$error Reference to error message variable
 * @return string|null Returns the file path on success, null on failure
 */
function upload_item_image($file, &$error) {
    $allowed_types = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!isset($file) || $file["error"] != 0) {
        return null;
    }
    
    $filename = $file["name"];
    $filetype = $file["type"];
    $filesize = $file["size"];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!array_key_exists($ext, $allowed_types)) {
        $error = "Invalid file format. Allowed formats: JPG, JPEG, GIF, PNG.";
        return null;
    }
    
    if ($filesize > $max_size) {
        $error = "File size exceeds the 5MB limit.";
        return null;
    }
    
    if (!in_array($filetype, $allowed_types)) {
        $error = "Invalid file type detected.";
        return null;
    }
    
    if (file_exists("uploads/" . $filename)) {
        $filename = time() . "_" . $filename;
    }
    
    if (move_uploaded_file($file["tmp_name"], "uploads/" . $filename)) {
        return "uploads/" . $filename;
    }
    
    $error = "Failed to upload the file. Please try again.";
    return null;
}

$item_name = $category = $description = $estimated_value = "";
$name_err = $category_err = $description_err = $value_err = $upload_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_name = trim($_POST["item_name"]);
    if (empty($input_name)) {
        $name_err = "Please enter an item name.";
    } else {
        $item_name = $input_name;
    }

    $input_category = trim($_POST["category"]);
    if (empty($input_category)) {
        $category_err = "Please enter a category.";
    } else {
        $category = $input_category;
    }

    $input_description = trim($_POST["description"]);
    if (empty($input_description)) {
        $description_err = "Please enter a description.";
    } else {
        $description = $input_description;
    }

    $input_value = trim($_POST["estimated_value"]);
    if (empty($input_value)) {
        $value_err = "Please enter the estimated value.";
    } elseif (!is_numeric($input_value) || $input_value < 0) {
        $value_err = "Please enter a valid positive number.";
    } else {
        $estimated_value = $input_value;
    }

    $image_path = upload_item_image($_FILES["item_image"], $upload_err);

    if (empty($name_err) && empty($category_err) && empty($description_err) && empty($value_err) && empty($upload_err)) {
        $sql = "INSERT INTO items (item_name, category, description, estimated_value, image_path, user_id) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssdsi", $item_name, $category, $description, $estimated_value, $image_path, $_SESSION["id"]);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_close($link);
                header("location: index.php");
                exit();
            } else {
                error_log("Database insert failed: " . mysqli_stmt_error($stmt));
                $upload_err = "Unable to save the item. Please try again.";
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Database prepare failed: " . mysqli_error($link));
            $upload_err = "Database error occurred. Please try again.";
        }
    }

    mysqli_close($link);
}

$page_title = "Create Record";
include "includes/header.php";
?>

<div class="row">
    <div class="col-md-12">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Item</li>
            </ol>
        </nav>

        <h2 class="mt-5">Create Record</h2>
        <p>Please fill this form and submit to add a collectible item to the database.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Item Name</label>
                <input type="text" name="item_name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $item_name; ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control <?php echo (!empty($category_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Select Category</option>
                    <option value="Electronics" <?php echo ($category == "Electronics") ? 'selected' : ''; ?>>Electronics</option>
                    <option value="Photography" <?php echo ($category == "Photography") ? 'selected' : ''; ?>>Photography</option>
                    <option value="Office Equipment" <?php echo ($category == "Office Equipment") ? 'selected' : ''; ?>>Office Equipment</option>
                    <option value="Furniture" <?php echo ($category == "Furniture") ? 'selected' : ''; ?>>Furniture</option>
                    <option value="Clothing" <?php echo ($category == "Clothing") ? 'selected' : ''; ?>>Clothing</option>
                    <option value="Other" <?php echo ($category == "Other") ? 'selected' : ''; ?>>Other</option>
                </select>
                <span class="invalid-feedback"><?php echo $category_err; ?></span>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                <span class="invalid-feedback"><?php echo $description_err; ?></span>
            </div>
            <div class="form-group">
                <label>Estimated Value</label>
                <input type="text" name="estimated_value" class="form-control <?php echo (!empty($value_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $estimated_value; ?>">
                <span class="invalid-feedback"><?php echo $value_err; ?></span>
            </div>
            <div class="form-group">
                <label>Item Image</label>
                <input type="file" name="item_image" class="form-control-file <?php echo (!empty($upload_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback d-block"><?php echo $upload_err; ?></span>
            </div>
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>


