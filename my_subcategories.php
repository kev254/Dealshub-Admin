<?php 
session_start();
$vid = $_SESSION['vid'];
if(!isset($vid) || $vid === null || $vid === ""){
    header("Location: auth");
}
include("includes/data.php");

// Modify the data fetching to use getSubCategories for fetching sub-categories
$sub_categories = getSubCategories($vid);

// Check if the delete request is made
if (isset($_GET['del_id'])) {
    $sub_cat_id = base64_decode($_GET['del_id']);

    // Delete the sub-category securely
    $stmt = $conn->prepare("DELETE FROM sub_categories WHERE id = ? AND vendor_id = ?");
    $stmt->bind_param("ii", $sub_cat_id, $vid);

    if ($stmt->execute()) {
        $success = "Sub-category deleted successfully!";
        header("refresh:1; url=sub_categories");
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
        <?php include("partials/header.php"); ?>

        <!-- Page Header Ends-->

        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            <!-- Page Sidebar Start-->
            <?php include("partials/sidebar.php"); ?>
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
                                            <a href="create_subcategory"
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
    while ($i < count($sub_categories)) {
        $sub_category = $sub_categories[$i];
        $isApproved = $sub_category['is_approved'] == 1;

        echo '<tr>
            <td>' . htmlspecialchars($sub_category['name']) . '</td>
            <td>' . htmlspecialchars($sub_category['cat_name']) . '</td>
            <td>' . htmlspecialchars($sub_category['created_at']) . '</td>
            <td class="' . ($isApproved ? 'success' : 'danger') . '">
                <span>' . ($isApproved ? 'Active' : 'Pending') . '</span>
            </td>
            <td>
                <ul>
                    <li>
                        <a href="' . (!$isApproved ? 'edit_subcategory?sid=' . base64_encode($sub_category['id']) : '#') . '" 
                           class="' . ($isApproved ? 'disabled' : '') . '">
                            <i class="ri-pencil-line"></i>
                        </a>
                    </li>

                    <li>
                        <a href="' . (!$isApproved ? '?del_id=' . base64_encode($sub_category['id']) : '#') . '" 
                           class="' . ($isApproved ? 'disabled' : '') . '" 
                           onclick="return ' . ($isApproved ? 'false' : 'confirm(\'Are you sure?\')') . ';">
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
