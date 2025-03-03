<?php 
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
}
include("includes/data.php");
$products = getProducts();

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
    $stmt = $conn->prepare("UPDATE products SET is_approved = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_approved, $act_id);

    if ($stmt->execute()) {
        $success = "Product updated successfully!";
        header("refresh:1; url=admn_products");
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
             <div class="page-body">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Best Selling Product Start -->
                        <div class="col-xl-12 col-md-12">
                            <div class="card o-hidden card-hover">
                                <div class="card-header card-header-top card-header--2 px-0 pt-0">
                                    <div class="card-header-title">
                                        <h4>Recent Products</h4>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div>
                                        <div class="table-responsive">
                                            <table class="table all-package theme-table table-product" id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th>Product Image</th>
                                                        <th>Product Name</th>
                                                        <th>Category</th>
                                                        <th>Vendor</th>
                                                        <th>Price</th>
                                                        <th>Status</th>
                                                        <th>Option</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php
                                                    foreach ($products as $product) {
                                                        echo '<tr>
        <td>
            <div class="table-image">
                <img src="' . htmlspecialchars($product['thumbnail_image']) . '" class="img-fluid" alt="">
            </div>
        </td>
        <td>' . htmlspecialchars($product['name']) . '</td>
        <td>' . htmlspecialchars($product['cat_name']) . '</td>
        <td>' . htmlspecialchars($product['vendor_name']) . '</td>
        <td class="td-price">Ksh.' . number_format($product['price'], 2) . '</td>
        <td class="' . ($product['is_approved'] == 1 ? 'success' : 'danger') . '">
            <span>' . ($product['is_approved'] == 1 ? 'Active' : 'Pending') . '</span>
        </td>
        <td>
            <ul>
                <li>
                    <a href="admn_edit_product?pid=' . base64_encode($product['id']) . '&&pvid='. base64_encode($product['vendor_id']) .'">
                        <i class="ri-pencil-line"></i>
                    </a>
                </li>
                <li>
                    <a href="admn_products?action=' . base64_encode($product['is_approved'] == 1 ? 'deactivate' : 'activate') . '&act_id=' . base64_encode($product['id']) . '" 
                       onclick="return confirm(\'Are you sure you want to ' . ($product['is_approved'] == 1 ? 'deactivate' : 'activate') . ' this product?\')">
                        <i class="ri-' . ($product['is_approved'] == 1 ? 'close-line text-danger' : 'check-line text-success') . '"></i>
                    </a>
                </li>
            </ul>
        </td>
    </tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Best Selling Product End -->
                    </div>
                </div>
                <!-- Container-fluid Ends-->

                <!-- footer start-->
                <?php include("partials/footer.php"); ?>
                <!-- footer End-->
            </div>
            <!-- index body end -->
        </div>
    </div>
    <!-- page-wrapper End-->

    <?php include("partials/js.php"); ?>
</body>

</html>
