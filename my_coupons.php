<?php 
session_start();
$vid = $_SESSION['vid'];
if(!isset($vid) || $vid===null || $vid===""){
    header("Location: auth");
}
include("includes/data.php");
$coupons = getCoupons($vid);

if (isset($_GET['del_id'])) {
    $coupon_id = intval($_GET['del_id']);

    // Delete the coupon securely
    $stmt = $conn->prepare("DELETE FROM coupons WHERE id = ? AND vendor_id = ?");
    $stmt->bind_param("ii", $coupon_id, $vid);

    if ($stmt->execute()) {
        $success = "Coupon added successfully!";
        header("refresh:1; url=my_coupons");
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
            <div class="page-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card card-table">
                                <div class="card-body">
                                    <div class="title-header option-title">
                                        <h5>Coupon List</h5>
                                        <div class="right-options">
                                            <ul>
                                                <li>
                                                    <a class="btn btn-solid" href="create_coupon">Create Coupon</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="table-responsive">
                                            <table class="table all-package coupon-list-table table-hover theme-table"
                                                id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <span class="form-check user-checkbox m-0 p-0">
                                                                <input class="checkbox_animated checkall"
                                                                    type="checkbox" value="">
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
                                                        $i = 0;

                                                        while($i <  count($coupons)){

                                                        $coupon = $coupons[$i];

                                                        echo '<tr><td>
                                                            <span class="form-check user-checkbox m-0 p-0">
                                                                <input class="checkbox_animated check-it"
                                                                    type="checkbox" value="">
                                                            </span>
                                                        </td>
                                                        <td>'.$coupon['title'].'</td>
                                                        <td>'.$coupon['coupon_code'].'</td>
                                                        <td class="theme-color">'.$coupon['discount_percentage'].' %</td>
                                                        <td class="menu-status">
                                                            <span class="' . ($coupon['is_approved'] == 1 ? 'success' : 'danger') . '">' . ($coupon['is_approved'] == 1 ? 'Active' : 'Pending') . '</span>
                                                        </td>
                                                        <td>
                                                            <ul>
                                                                <li>
                                                                    <a href="edit_coupon?cid='.$coupon['id'].'">
                                                                        <i class="ri-pencil-line"></i>
                                                                    </a>
                                                                </li>

                                                                <li>
                                                                    <a href="my_coupons?del_id='.$coupon['id'].'" >
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </td></tr>';
                                                        $i++;

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
    <!-- page-wrapper End -->

   

    

    <?php include("partials/js.php");?>
</body>

</html>