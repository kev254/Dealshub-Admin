<?php 
session_start();
$vid = $_SESSION['vid'];
if(!isset($vid) || $vid === null || $vid === ""){
    header("Location: auth");
}
include("includes/data.php");

// Modify the data fetching to use getSubCategories for fetching sub-categories
$sub_categories = getSubCategories();

// Check if the delete request is made
if (isset($_GET['del_id'])) {
    $sub_cat_id = base64_decode($_GET['del_id']);

    // Delete the sub-category securely
    $stmt = $conn->prepare("DELETE FROM sub_categories WHERE id = ?");
    $stmt->bind_param("i", $sub_cat_id);

    if ($stmt->execute()) {
        $success = "Sub-category deleted successfully!";
        header("refresh:1; url=admn_sub_categories");
    } 
}

// UPDATE Sub-category Status
if (isset($_GET['action']) && isset($_GET['act_id'])) {
    $action = base64_decode($_GET['action']);
    $act_id = base64_decode($_GET['act_id']);
    
    $is_approved = ($action === "activate") ? 1 : 0;

    $stmt = $conn->prepare("UPDATE sub_categories SET is_approved = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_approved, $act_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Sub-category status updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating sub-category status!";
    }
    header("Location: admn_sub_categories");
    exit();
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
                <!-- All Sub-categories Table Start -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card card-table">
                                <div class="card-body">
                                    <div class="title-header option-title">
                                        <h5>All Sub-Categories</h5>
                                        <form class="d-inline-flex">
                                            <a href="admn_create_subcategory"
                                                class="align-items-center btn btn-theme d-flex">
                                                <i data-feather="plus-square" disabled></i>Create Sub-Category
                                            </a>
                                        </form>
                                    </div>

                                    <div class="table-responsive category-table">
                                        <div>
                                            <table class="table all-package theme-table" id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th>Sub-category Name</th>
                                                        <th>Parent Category</th>
                                                        <th>Date Created</th>
                                                        <th>Status</th>
                                                        <th>Options</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php 
                                                    $i = 0;
                                                    while($i < count($sub_categories)){
                                                        $sub_category = $sub_categories[$i];

                                                    echo '<tr>
                                                        <td>'.$sub_category['name'].'</td>
                                                        <td>'.$sub_category['cat_name'].'</td>
                                                        <td>'.$sub_category['created_at'].'</td>
                                                        <td class="' . ($sub_category['is_approved'] == 1 ? 'success' : 'danger') . '">
                                                            <span>' . htmlspecialchars($sub_category['is_approved'] == 1 ? 'Active' : 'Pending') . '</span>
                                                        </td>

                                                        <td>
                                                            <ul>
                                                                <li>
                                                                    <a href="admn_edit_subcategory?sid='.(base64_encode($sub_category['id'])).'&&vsid='.(base64_encode($sub_category['vendor_id'])).'">
                                                                        <i class="ri-pencil-line"></i>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="admn_sub_categories?action=' . base64_encode($sub_category['is_approved'] == 1 ? 'deactivate' : 'activate') . '&act_id=' . base64_encode($sub_category['id']) . '" 
                                                                    onclick="return confirm(\'Are you sure you want to ' . ($sub_category['is_approved'] == 1 ? 'deactivate' : 'activate') . ' this Sub category?\')">
                                                                        <i class="ri-' . ($sub_category['is_approved'] == 1 ? 'close-line text-danger' : 'check-line text-success') . '"></i>
                                                                    </a>
                                                                </li>

                                                                <li>
                                                                    <a href="?del_id='.base64_encode($sub_category['id']).'">
                                                                        <i class="ri-delete-bin-line"></i>
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
                <!-- All Sub-categories Table Ends-->

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
