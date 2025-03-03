<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");

// Fetch branches for the vendor
$branches = getBranches($vid);

// Handle branch deletion (if applicable)
if (isset($_GET['del_id'])) {
    $branch_id = base64_decode($_GET['del_id']);

    // Delete the branch securely
    $stmt = $conn->prepare("DELETE FROM branches WHERE id = ? AND vendor_id = ?");
    $stmt->bind_param("ii", $branch_id, $vid);

    if ($stmt->execute()) {
        $success = "Branch deleted successfully!";
        header("refresh:1; url=branches");

    }
}

// Handle branch deletion (if applicable)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $branch_id = base64_decode($_GET['bid']);
    if ($action === "deactivate") {
        $stmt = $conn->prepare("UPDATE branches SET status = 0 WHERE id = ? AND vendor_id = ?");
    } elseif ($action === "activate") {
        $stmt = $conn->prepare("UPDATE branches SET status = 1 WHERE id = ? AND vendor_id = ?");

    }

    $stmt->bind_param("ii", $branch_id, $vid);
    if ($stmt->execute()) {
        $success = "Branch updated successfully!";
        header("refresh:1; url=branches");

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
                               
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= $success ?></div>
                                    <?php endif; ?>
                                    <div class="title-header option-title d-sm-flex d-block">
                                        <h5>Branches List</h5>
                                        <div class="right-options">
                                            <ul>

                                                <li>
                                                    <a class="btn btn-solid" href="create_branch">Create Branch</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="table-responsive">
                                            <table class="table all-package theme-table table-product" id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th>Branch Name</th>
                                                        <th>Latitude</th>
                                                        <th>Longitude</th>
                                                        <th>Status</th>
                                                        <th>Option</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Loop through the branches and display them
                                                    foreach ($branches as $branch) {
                                                        echo '<tr>
                    <td>' . htmlspecialchars($branch['branch_name']) . '</td>
                    <td>' . htmlspecialchars($branch['latitude']) . '</td>
                    <td>' . htmlspecialchars($branch['longitude']) . '</td>
                    <td class="' . ($branch['status'] == 1 ? 'success' : 'danger') . '">
                        <span>' . ($branch['status'] == 1 ? 'Active' : 'Inactive') . '</span>
                    </td>
                    <td>
                        <ul>
                            <li>
                                <a href="edit_branch?bid=' . base64_encode($branch['id']) . '">
                                    <i class="ri-pencil-line"></i>
                                </a>
                            </li>
                            <li>
                                <a href="branches?del_id=' . base64_encode($branch['id']) . '">
                                    <i class="ri-delete-bin-line"></i>
                                </a>
                            </li>
                            <li>
                                ';

                                                        // Action for Deactivating or Activating the branch
                                                        if ($branch['status'] == 1) {
                                                            echo '<a href="branches?action=deactivate&bid=' . base64_encode($branch['id']) . '">
                                            <i class="ri-close-line text-success"></i>
                                          </a>';
                                                        } else {
                                                            echo '<a href="branches?action=activate&bid=' . base64_encode($branch['id']) . '">
                                            <i class="ri-check-line text-danger"></i> 
                                          </a>';
                                                        }

                                                        echo '</li>
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