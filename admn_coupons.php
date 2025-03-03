<?php 
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
}
include("includes/data.php");
$coupons = getCoupons();

if (isset($_GET['action'])) {
    $action = base64_decode($_GET['action']);
    $coupon_id = intval(base64_decode($_GET['coupon_id']));

    // Determine the new status
    $is_approved = ($action === "activate") ? 1 : 0;

    // Update the coupon status securely
    $stmt = $conn->prepare("UPDATE coupons SET is_approved = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_approved, $coupon_id);

    if ($stmt->execute()) {
        $success = "Coupon status updated successfully!";
        header("refresh:1; url=admn_coupons");
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
                        <div class="col-sm-12">
                            <div class="card card-table">
                                <div class="card-body">
                                    <div class="title-header option-title">
                                        <h5>Coupon List</h5>
                                        <div class="right-options">
                                            <!-- <ul>
                                                <li>
                                                    <a class="btn btn-solid" href="create_coupon">Create Coupon</a>
                                                </li>
                                            </ul> -->
                                        </div>
                                    </div>
                                    <div>
                                        <div class="table-responsive">
                                            <table class="table all-package coupon-list-table table-hover theme-table" id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <span class="form-check user-checkbox m-0 p-0">
                                                                <input class="checkbox_animated checkall" type="checkbox" value="">
                                                            </span>
                                                        </th>
                                                        <th>Title</th>
                                                        <th>Code</th>
                                                        <th>Discount</th>
                                                        <th>Status</th>
                                                        <th>Option</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($coupons as $coupon) {
                                                        echo '<tr>
                                                        <td>
                                                            <span class="form-check user-checkbox m-0 p-0">
                                                                <input class="checkbox_animated check-it" type="checkbox" value="">
                                                            </span>
                                                        </td>
                                                        <td>' . htmlspecialchars($coupon['title']) . '</td>
                                                        <td>' . htmlspecialchars($coupon['coupon_code']) . '</td>
                                                        <td class="theme-color">' . htmlspecialchars($coupon['discount_percentage']) . ' %</td>
                                                        <td class="menu-status">
                                                            <span class="' . ($coupon['is_approved'] == 1 ? 'success' : 'danger') . '">' . ($coupon['is_approved'] == 1 ? 'Active' : 'Pending') . '</span>
                                                        </td>
                                                        <td>
                                                            <ul>
                                                                <li>
                                                                    <a href="admn_edit_coupon?cid=' . base64_encode($coupon['id']) . '&&vcid='.base64_encode($coupon['vendor_id']).'">
                                                                        <i class="ri-pencil-line"></i>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="admn_coupons?action=' . base64_encode($coupon['is_approved'] == 1 ? 'deactivate' : 'activate') . '&coupon_id=' . base64_encode($coupon['id']) . '"
                                                                       onclick="return confirm(\'Are you sure you want to ' . ($coupon['is_approved'] == 1 ? 'deactivate' : 'activate') . ' this coupon?\')">
                                                                        <i class="ri-' . ($coupon['is_approved'] == 1 ? 'close-line text-danger' : 'check-line text-success') . '"></i>
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
                                <!-- Pagination End -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Container-fluid Ends-->

                <!-- footer start-->
                <?php include("partials/footer.php"); ?>
                <!-- footer end-->
            </div>
        </div>
    </div>
    <!-- page-wrapper End -->
    <?php include("partials/js.php"); ?>
</body>

</html>
