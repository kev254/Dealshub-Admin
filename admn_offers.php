<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
}
include("includes/data.php");
$offers = getFlyers();

if (isset($_GET['action'])) {
    $action = base64_decode($_GET['action']);
    $act_id = base64_decode($_GET['act_id']);

    // Set the status based on the action
    if ($action === "activate") {
        $is_approved = 1;
    } elseif ($action === "deactivate") {
        $is_approved = 0;
    }

    // Update the offer's approval status securely
    $stmt = $conn->prepare("UPDATE flyers SET is_approved = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_approved, $act_id);

    if ($stmt->execute()) {
        $success = "Offer updated successfully!";
        header("refresh:1; url=admn_offers");
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
                                    <div class="title-header option-title d-sm-flex d-block">
                                        <h5>Offers List</h5>
                                        <div class="right-options">
                                            <ul>
                                               
                                            </ul>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="table-responsive">
                                            <table class="table all-package theme-table table-product" id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th>Offer Image</th>
                                                        <th>Offer Name</th>
                                                        <th> Vendor </th>
                                                        <th>Valid Until</th>
                                                        <th>Status</th>
                                                        <th>Option</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($offers as $offer) {
                                                        echo '<tr>
        <td>
            <div class="table-image">
                <img src="' . htmlspecialchars($offer['thumbnail_image']) . '" class="img-fluid" alt="">
            </div>
        </td>
        <td>' . htmlspecialchars($offer['title']) . '</td>
        <td class="td-price">' . htmlspecialchars($offer['vendor_name']) . '</td>
        <td class="td-price">' . htmlspecialchars($offer['valid_until']) . '</td>
        <td class="' . ($offer['is_approved'] == 1 ? 'success' : 'danger') . '">
            <span>' . ($offer['is_approved'] == 1 ? 'Active' : 'Pending') . '</span>
        </td>
        <td>
            <ul>
                <li>
                    <a href="admn_edit_offer?oid=' . base64_encode($offer['id']) . '">
                        <i class="ri-pencil-line"></i>
                    </a>
                </li>
                <li>
                    <a href="admn_offers?action=' . base64_encode($offer['is_approved'] == 1 ? 'deactivate' : 'activate') . '&act_id=' . base64_encode($offer['id']) . '" 
                       onclick="return confirm(\'Are you sure you want to ' . ($offer['is_approved'] == 1 ? 'deactivate' : 'activate') . ' this offer?\')">
                        <i class="ri-' . ($offer['is_approved'] == 1 ? 'close-line text-danger' : 'check-line text-success') . '"></i>
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
                    </div>
                </div>
                <!-- Container-fluid Ends-->

                <!-- Footer Start-->
                <?php include("partials/footer.php"); ?>
                <!-- Footer End-->
            </div>
        </div>
    </div>
    <!-- page-wrapper End-->
    <?php include("partials/js.php"); ?>
</body>

</html>
