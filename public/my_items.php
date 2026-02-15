<?php
// Include auth check and config file
require_once "includes/auth_check.php";
require_once "config.php";

$page_title = "My Items";
include "includes/header.php";
?>

<div class="row">
    <div class="col-md-12">
        <div class="mt-5 mb-3 clearfix">
            <h2 class="pull-left">My Items</h2>
            <a href="create.php" class="btn btn-success pull-right">
                <i class="fas fa-plus"></i> Add New Item
            </a>
        </div>
        
        <?php
        $user_id = $_SESSION["id"];
        $sql = "SELECT * FROM items WHERE user_id = ? ORDER BY id DESC";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    echo '<table class="table table-bordered table-striped">';
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>#</th>";
                    echo "<th>Name</th>";
                    echo "<th>Category</th>";
                    echo "<th>Estimated Value</th>";
                    echo "<th>Action</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                        echo "<td>$" . number_format($row['estimated_value'], 2) . "</td>";
                        echo "<td>";
                        echo '<a href="read.php?id=' . $row['id'] . '" class="mr-3" title="View Record" data-toggle="tooltip"><span class="fa fa-eye"></span></a>';
                        echo '<a href="update.php?id=' . $row['id'] . '" class="mr-3" title="Update Record" data-toggle="tooltip"><span class="fa fa-pencil"></span></a>';
                        echo '<a href="delete.php?id=' . $row['id'] . '" title="Delete Record" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo '<div class="alert alert-info"><em>You haven\'t added any items yet.</em></div>';
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
        ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>
