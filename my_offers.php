<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
}
include("includes/data.php");
$offers = getFlyers($vid);

if (isset($_GET['del_id'])) {
    $cat_id = base64_decode($_GET['del_id']);

    // Delete the coupon securely
    $stmt = $conn->prepare("DELETE FROM flyers WHERE id = ? AND vendor_id = ?");
    $stmt->bind_param("ii", $cat_id, $vid);

    if ($stmt->execute()) {
        $success = "Offer deleted successfully!";
        header("refresh:1; url=my_offers");
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
                                    <div class="title-header option-title d-sm-flex d-block">
                                        <h5>Offers List</h5>
                                        <div class="right-options">
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">import</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">Export</a>
                                                </li>
                                                <li>
                                                    <a class="btn btn-solid" href="create_offer">Create Offer</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="table-responsive">
                                            <table class="table all-package theme-table table-product" id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th>Offer Image</th>
                                                        <th>Offfer Name</th>
                                                        <th>Valid until</th>
                                                        <th>Status</th>
                                                        <th>Option</th>
                                                    </tr>
                                                </thead>

                                                <tbody>


                                                    <?php
                                                    $a = 0;
                                                    while ($a < count($offers)) {
                                                        $offer = $offers[$a];
                                                        echo '<tr>
            <td>
                <div class="table-image">
                    <img src="' . $offer['thumbnail_image'] . '" class="img-fluid" alt="">
                </div>
            </td>
            <td>' . htmlspecialchars($offer['title']) . '</td>
            <td class="td-price">' . htmlspecialchars($offer['valid_until']) . '</td>
            <td class="' . ($offer['is_approved'] == 1 ? 'success' : 'danger') . '">
                <span>' . htmlspecialchars($offer['is_approved'] == 1 ? 'Active' : 'Pending') . '</span>
            </td>
            <td>
                <ul>
                    <li>
                        <a href="edit_offer?oid='.(base64_encode($offer['id'])).'">
                            <i class="ri-pencil-line"></i>
                        </a>
                    </li>
                    <li>
                        <a href="my_offers?del_id='.(base64_encode($offer['id'])).'">
                            <i class="ri-delete-bin-line"></i>
                        </a>
                    </li>
                </ul>
            </td>
        </tr>';
                                                        $a++;
                                                    }
                                                    ?>
                                                </tbody>






                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Container-fluid Ends-->

                <div class="container-fluid">
                    <!-- footer start-->
                    <?php include("partials/footer.php"); ?>

                    <body>
                </div>
            </div>
        </div>
    </div>
    <!-- page-wrapper End-->
    <?php include("partials/js.php"); ?>

</body>

</html>