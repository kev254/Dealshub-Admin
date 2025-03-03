<?php 
session_start();
$vid = $_SESSION['vid'];
if(!isset($vid) || $vid===null || $vid===""){
    header("Location: auth");
}
include("includes/data.php");
$categories = getCategories();

if (isset($_GET['del_id'])) {
    $cat_id = base64_decode($_GET['del_id']);

    // Delete the coupon securely
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $cat_id);

    if ($stmt->execute()) {
        $success = "Category added successfully!";
        header("refresh:1; url=admn_categories");
    } 
}

if (isset($_GET['action'])) {
    $action = base64_decode($_GET['action']);
    $act_id = base64_decode($_GET['act_id']);

    // Set the status based on the action
    if ($action === "activate") {
        $is_approved = 1;
    } elseif ($action === "deactivate") {
        $is_approved = 0;
    }

    // Update the product's approval status securely
    $stmt = $conn->prepare("UPDATE categories SET is_approved = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_approved, $act_id);

    if ($stmt->execute()) {
        $success = "Category updated successfully!";
        header("refresh:1; url=admn_categories");
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php include("partials/head.php"); ?>


<body>
    <!-- tap on top start -->
    <div class="tap-top">
        <span class="lnr lnr-chevron-up"></span>
    </div>
    <!-- tap on tap end -->

    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header Start-->
        <?php include("partials/admn_header.php"); ?>

        <!-- Page Header Ends-->

        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            <!-- Page Sidebar Start-->
            <?php include("partials/admn_sidebar.php"); ?>
            <!-- Page Sidebar Ends-->

            <!-- index body start -->

            <!-- Container-fluid starts-->
            <div class="page-body">
                <!-- All User Table Start -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card card-table">
                                <div class="card-body">
                                    <div class="title-header option-title">
                                        <h5>All Categories</h5>
                                        <form class="d-inline-flex">
                                            <a href="admn_create_category"
                                                class="align-items-center btn btn-theme d-flex">
                                                <i data-feather="plus-square"></i>Create Category
                                            </a>
                                        </form>
                                    </div>

                                    <div class="table-responsive category-table">
                                        <div>
                                            <table class="table all-package theme-table" id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th>Category Name</th>
                                                        <th>Date created</th>
                                                        <th>Priority</th>
                                                        <th>Status</th>
                                                        <th>Option</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php 
                                                    $i = 0;
                                                    while($i < count($categories)){
                                                        $category = $categories[$i];

                                                    echo '<tr>
                                                        <td>'.$category['name'].'</td>
                                                        
                                                        <td>'.$category['created_at'].'</td>
                                                        <td>'.$category['priority'].'</td>
                                                        <td class="' . ($category['is_approved'] == 1 ? 'success' : 'danger') . '">
                                                            <span>' . htmlspecialchars($category['is_approved'] == 1 ? 'Active' : 'Pending') . '</span>
                                                        </td>

                                                        <td>
                                                            <ul>
                                                                <li>
                                                                    <a href="admn_edit_category?cid='.(base64_encode($category['id'])).'&&vcid='.base64_encode($category['vendor_id']).'">
                                                                        <i class="ri-pencil-line"></i>
                                                                    </a>
                                                                </li>

                                                                <li>
                                                                    <a href="?del_id='.base64_encode($category['id']).'&&vcid='.base64_encode($category['vendor_id']).'">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="admn_categories?action=' . base64_encode($category['is_approved'] == 1 ? 'deactivate' : 'activate') . '&act_id=' . base64_encode($category['id']) . '" 
                                                                    onclick="return confirm(\'Are you sure you want to ' . ($category['is_approved'] == 1 ? 'deactivate' : 'activate') . ' this Category?\')">
                                                                        <i class="ri-' . ($category['is_approved'] == 1 ? 'close-line text-danger' : 'check-line text-success') . '"></i>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>';
                                                    $i++;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- All User Table Ends-->

                <div class="container-fluid">
                    <!-- footer start-->
                    <?php include("partials/footer.php"); ?>
                    <!-- footer end-->
                </div>
            </div>
            <!-- Container-fluid end -->
        </div>
        <!-- Page Body End -->

        
    </div>

    <?php include("partials/js.php");?>
</body>

</html>