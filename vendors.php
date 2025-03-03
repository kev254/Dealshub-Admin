<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");

// Fetch vendors for the logged-in user
$vendors = getVendors();

// Handle status toggle action (activate or deactivate)
if (isset($_GET['action']) && isset($_GET['bid'])) {
    $action = $_GET['action'];
    $vendor_id = base64_decode($_GET['bid']);
    // Determine the new status
    $status = ($action === "activate") ? "Approved" : "Pending";

    // Fetch vendor details for registration (when activating)
    if ($action === "activate") {
        $stmtFetch = $conn->prepare("SELECT name, email FROM vendors WHERE id = ?");
        $stmtFetch->bind_param("i", $vendor_id);
        $stmtFetch->execute();
        $result = $stmtFetch->get_result();
        $vendorDetails = $result->fetch_assoc();

        $name = $vendorDetails['name'];
        $email = $vendorDetails['email'];
        $password = generate_random_password(); // Generate a secure random password
        $role_id = 2; // Assuming role_id 2 represents vendors
    }

    // Update the vendor status securely
    $stmtUpdate = $conn->prepare("UPDATE vendors SET status = ? WHERE id = ?");
    $stmtUpdate->bind_param("si", $status, $vendor_id);
    
    if ($stmtUpdate->execute()) {
        // Register vendor only when activating
        if ($action === "activate") {
            register_vendor($name, $email, $password, $role_id, $vendor_id);
        }
        $success = "Vendor status updated successfully!";
        header("refresh:1; url=vendors");
    }
}

/**
 * Function to generate a random password.
 */
function generate_random_password($length = 6) {
    return bin2hex(random_bytes($length / 2)); 
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
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= $success ?></div>
                                    <?php endif; ?>
                                    <div class="title-header option-title d-sm-flex d-block">
                                        <h5>Vendors List</h5>
                                    </div>
                                    <div>
                                        <div class="table-responsive">
                                            <table class="table all-package theme-table table-product" id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th>Logo</th>
                                                        <th>Vendor Name</th>
                                                        <th>Business Type</th>
                                                        <th>Representative</th>
                                                        <th>Phone</th>
                                                        <th>Email</th>
                                                        <th>Status</th>
                                                        <th>Option</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($vendors as $vendor) {
                                                        echo '<tr>
                                                        <td>
                                                            <div class="table-image">
                                                                <img src="' . $images_base_url . htmlspecialchars($vendor['logo']) . '" class="img-fluid" alt="">
                                                            </div>
                                                        </td>
                                                        <td>' . htmlspecialchars($vendor['vendor_name']) . '</td>
                                                        <td>' . htmlspecialchars($vendor['bus_name']) . '</td>
                                                        <td>' . htmlspecialchars($vendor['rep_name']) . '</td>
                                                        <td>' . htmlspecialchars($vendor['phone']) . '</td>
                                                        <td>' . htmlspecialchars($vendor['email']) . '</td>
                                                        <td class="' . ($vendor['status'] == "Approved" ? 'success' : 'danger') . '">
                                                            <span>' . ($vendor['status'] == "Approved" ? 'Active' : 'Inactive') . '</span>
                                                        </td>
                                                        <td>
                                                            <ul>
                                                               
                                                                <li>
                                                                    <a href="vendors?action=' . ($vendor['status'] == "Approved" ? 'deactivate' : 'activate') . '&bid=' . base64_encode($vendor['vendor_id']) . '"
                                                                       onclick="return confirm(\'Are you sure you want to ' . ($vendor['status'] == "Approved" ? 'deactivate' : 'activate') . ' this vendor?\')">
                                                                        <i class="ri-' . ($vendor['status'] == "Approved" ? 'close-line text-danger' : 'check-line text-success') . '"></i>
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
                    <!-- Container-fluid Ends-->

                    <div class="container-fluid">
                        <!-- footer start-->
                        <?php include("partials/footer.php"); ?>
                        <!-- footer end-->
                    </div>
                </div>
            </div>
        </div>
        <!-- page-wrapper End-->
        <?php include("partials/js.php"); ?>
</body>

</html>
