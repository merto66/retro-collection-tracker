<?php
// Include auth check and config file
require_once "includes/auth_check.php";
require_once "config.php";

// Define variables
$search_term = "";
$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// Handle search
if (isset($_GET["search"]) && !empty(trim($_GET["search"]))) {
    $search_term = trim($_GET["search"]);
}

// Prepare a select statement for count
if (!empty($search_term)) {
    $count_sql = "SELECT COUNT(*) as total FROM items WHERE item_name LIKE ?";
} else {
    $count_sql = "SELECT COUNT(*) as total FROM items";
}

$total_records = 0;
if ($stmt = mysqli_prepare($link, $count_sql)) {
    if (!empty($search_term)) {
        $search_param = "%" . $search_term . "%";
        mysqli_stmt_bind_param($stmt, "s", $search_param);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $total_records = $row["total"];
    mysqli_stmt_close($stmt);
}

$total_pages = ceil($total_records / $records_per_page);

// Prepare a select statement
// Note: LIMIT and OFFSET are safe here as they are cast to integers
$limit = (int)$records_per_page;
$offset_val = (int)$offset;

if (!empty($search_term)) {
    $sql = "SELECT * FROM items WHERE item_name LIKE ? ORDER BY id DESC LIMIT $limit OFFSET $offset_val";
} else {
    $sql = "SELECT * FROM items ORDER BY id DESC LIMIT $limit OFFSET $offset_val";
}

$page_title = "Vintage Collection - Dashboard";
include "includes/header.php";
?>

<div class="row">
    <div class="col-md-12">
        <!-- Hero Section with Carousel -->
        <div id="carouselExampleIndicators" class="carousel slide mb-4" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="d-block w-100 text-white text-center" style="height: 400px; background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1603048588665-791ca8aea617?q=80&w=1920'); background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <h2 class="display-4"><i class="fas fa-record-vinyl"></i> Vintage Vinyl Records</h2>
                        <p class="lead">Discover rare and classic vinyl collections</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="d-block w-100 text-white text-center" style="height: 400px; background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=1920'); background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <h2 class="display-4"><i class="fas fa-camera-retro"></i> Retro Cameras</h2>
                        <p class="lead">Classic photography equipment from the golden age</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="d-block w-100 text-white text-center" style="height: 400px; background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1520690214124-2405c5217036?q=80&w=1920'); background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <h2 class="display-4"><i class="fas fa-keyboard"></i> Vintage Typewriters</h2>
                        <p class="lead">Mechanical typewriters and office classics</p>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Collection Items</li>
            </ol>
        </nav>

        <div class="mt-5 mb-3 clearfix">
            <h2 class="pull-left">Collection Items</h2>
            <a href="create.php" class="btn btn-success pull-right">
                <i class="fas fa-plus"></i> Add New Item
            </a>
        </div>

        <!-- Search Form -->
        <form method="GET" action="index.php" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by item name..." value="<?php echo htmlspecialchars($search_term); ?>">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i> Search</button>
                    <?php if (!empty($search_term)): ?>
                    <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i> Clear</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <?php
        if ($stmt = mysqli_prepare($link, $sql)) {
            if (!empty($search_term)) {
                $search_param = "%" . $search_term . "%";
                mysqli_stmt_bind_param($stmt, "s", $search_param);
            }

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    echo '<table class="table table-bordered table-striped">';
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>#</th>";
                    echo "<th>Image</th>";
                    echo "<th>Name</th>";
                    echo "<th>Category</th>";
                    echo "<th>Estimated Value</th>";
                    echo "<th>Action</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $image_path = isset($row['image_path']) && !empty($row['image_path']) ? $row['image_path'] : null;
                        
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>";
                        if ($image_path) {
                            // Check if it's a URL or local file
                            if (filter_var($image_path, FILTER_VALIDATE_URL)) {
                                // It's a URL
                                echo '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($row['item_name']) . '" style="max-width: 80px; max-height: 80px; object-fit: cover;" class="img-thumbnail">';
                            } else {
                                // It's a local file
                                if (file_exists($image_path)) {
                                    echo '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($row['item_name']) . '" style="max-width: 80px; max-height: 80px; object-fit: cover;" class="img-thumbnail">';
                                } else {
                                    echo '<span class="text-muted"><i class="fas fa-image"></i> No image</span>';
                                }
                            }
                        } else {
                            echo '<span class="text-muted"><i class="fas fa-image"></i> No image</span>';
                        }
                        echo "</td>";
                        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                        echo "<td>$" . number_format($row['estimated_value'], 2) . "</td>";
                        echo "<td>";
                        echo '<a href="read.php?id=' . $row['id'] . '" class="mr-3" title="View Record" data-toggle="tooltip"><span class="fa fa-eye"></span></a>';
                        
                        // Show edit/delete only for owner or admin
                        $item_user_id = isset($row['user_id']) ? $row['user_id'] : null;
                        if(isset($_SESSION["id"]) && ($item_user_id == $_SESSION["id"] || (isset($_SESSION["role"]) && $_SESSION["role"] == 'admin'))) {
                            echo '<a href="update.php?id=' . $row['id'] . '" class="mr-3" title="Update Record" data-toggle="tooltip"><span class="fa fa-pencil"></span></a>';
                            echo '<a href="delete.php?id=' . $row['id'] . '" title="Delete Record" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
                        }
                        echo "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody>";
                    echo "</table>";

                    // Free result set
                    mysqli_free_result($result);

                    // Pagination
                    if ($total_pages > 1) {
                        echo '<nav aria-label="Page navigation">';
                        echo '<ul class="pagination justify-content-center">';
                        
                        // Previous button
                        if ($page > 1) {
                            $prev_page = $page - 1;
                            $search_param = !empty($search_term) ? "&search=" . urlencode($search_term) : "";
                            echo '<li class="page-item"><a class="page-link" href="index.php?page=' . $prev_page . $search_param . '">Previous</a></li>';
                        }

                        // Page numbers
                        for ($i = 1; $i <= $total_pages; $i++) {
                            $search_param = !empty($search_term) ? "&search=" . urlencode($search_term) : "";
                            $active = ($i == $page) ? "active" : "";
                            echo '<li class="page-item ' . $active . '"><a class="page-link" href="index.php?page=' . $i . $search_param . '">' . $i . '</a></li>';
                        }

                        // Next button
                        if ($page < $total_pages) {
                            $next_page = $page + 1;
                            $search_param = !empty($search_term) ? "&search=" . urlencode($search_term) : "";
                            echo '<li class="page-item"><a class="page-link" href="index.php?page=' . $next_page . $search_param . '">Next</a></li>';
                        }

                        echo '</ul>';
                        echo '</nav>';
                    }
                } else {
                    echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close connection
        mysqli_close($link);
        ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>

