<?php
/**
 * Update Item
 * 
 * Allows item owners and admins to edit existing collectible items.
 * Handles form validation, permission checks, image uploads, and database updates.
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

/**
 * Checks if user has permission to modify an item
 * 
 * @param mysqli $link Database connection
 * @param int $item_id Item ID to check
 * @param int $user_id Current user ID
 * @param string $user_role Current user role
 * @return bool True if user has permission, false otherwise
 */
function has_edit_permission($link, $item_id, $user_id, $user_role) {
    $sql = "SELECT user_id FROM items WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $item_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $owner_id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        return ($owner_id == $user_id || $user_role == 'admin');
    }
    return false;
}

$item_name = $category = $description = $estimated_value = "";
$name_err = $category_err = $description_err = $value_err = $upload_err = "";

if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $id = $_POST["id"];

    if (!has_edit_permission($link, $id, $_SESSION["id"], $_SESSION["role"])) {
        mysqli_close($link);
        die('<div class="alert alert-danger mt-3">You do not have permission to edit this item. <a href="index.php">Go Back</a></div>');
    }

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

    if (empty($name_err) && empty($category_err) && empty($description_err) && empty($value_err)) {
        $new_image_path = upload_item_image($_FILES["item_image"], $upload_err);

        if (empty($upload_err)) {
            if ($new_image_path) {
                $sql = "UPDATE items SET item_name=?, category=?, description=?, estimated_value=?, image_path=? WHERE id=?";
            } else {
                $sql = "UPDATE items SET item_name=?, category=?, description=?, estimated_value=? WHERE id=?";
            }

            if ($stmt = mysqli_prepare($link, $sql)) {
                if ($new_image_path) {
                    mysqli_stmt_bind_param($stmt, "sssdsi", $item_name, $category, $description, $estimated_value, $new_image_path, $id);
                } else {
                    mysqli_stmt_bind_param($stmt, "sssdi", $item_name, $category, $description, $estimated_value, $id);
                }

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    mysqli_close($link);
                    header("location: index.php");
                    exit();
                } else {
                    error_log("Database update failed: " . mysqli_stmt_error($stmt));
                    $upload_err = "Unable to update the item. Please try again.";
                }
                mysqli_stmt_close($stmt);
            } else {
                error_log("Database prepare failed: " . mysqli_error($link));
                $upload_err = "Database error occurred. Please try again.";
            }
        }
    }

    mysqli_close($link);
} else {
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id = trim($_GET["id"]);
        $sql = "SELECT * FROM items WHERE id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    if (!has_edit_permission($link, $id, $_SESSION["id"], $_SESSION["role"])) {
                        mysqli_stmt_close($stmt);
                        mysqli_close($link);
                        echo '<div class="alert alert-danger mt-3">You do not have permission to edit this item. <a href="index.php">Go Back</a></div>';
                        include "includes/footer.php";
                        exit();
                    }

                    $item_name = $row["item_name"];
                    $category = $row["category"];
                    $description = $row["description"];
                    $estimated_value = $row["estimated_value"];
                    $image_path = $row["image_path"];
                } else {
                    mysqli_stmt_close($stmt);
                    mysqli_close($link);
                    header("location: index.php");
                    exit();
                }
            } else {
                error_log("Database query failed: " . mysqli_stmt_error($stmt));
                echo '<div class="alert alert-danger mt-3">Unable to retrieve item. Please try again. <a href="index.php">Go Back</a></div>';
            }
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
    } else {
        header("location: index.php");
        exit();
    }
}

$page_title = "Update Record";
include "includes/header.php";
?>

<div class="row">
    <div class="col-md-12">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update Item</li>
            </ol>
        </nav>

        <h2 class="mt-5">Update Record</h2>
        <p>Please edit the input values and submit to update the item record.</p>
        <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post" enctype="multipart/form-data">
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
                <label>Current Image</label><br>
                <?php if(!empty($image_path)): ?>
                    <img src="<?php echo $image_path; ?>" alt="Current Image" style="max-width: 200px; margin-bottom: 10px;" class="img-thumbnail">
                <?php else: ?>
                    <p>No image uploaded.</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Change Image (Leave blank to keep current)</label>
                <input type="file" name="item_image" class="form-control-file <?php echo (!empty($upload_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback d-block"><?php echo $upload_err; ?></span>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>


