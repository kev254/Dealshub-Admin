<?php 
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
}
include("includes/data.php");

$admins = getAdmins();

// Delete admin
if (isset($_GET['del_id'])) {
    $admin_id = base64_decode($_GET['del_id']);

    // Delete the admin securely
    $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);

    if ($stmt->execute()) {
        $success = "Admin deleted successfully!";
        header("refresh:1; url=admn_list");
    }
}

// Activate/Deactivate admin
if (isset($_GET['action'])) {
    $action = base64_decode($_GET['action']);
    $admin_id = base64_decode($_GET['act_id']);

    // Get the user ID linked to this admin
    $stmt = $conn->prepare("SELECT user_id FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $user_id = $admin['user_id'];

    if ($action === "activate") {
        $is_verified = 1;
    } elseif ($action === "deactivate") {
        $is_verified = 0;
    }

    // Update the admin's activation status securely
    $stmt = $conn->prepare("UPDATE users SET is_verified = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_verified, $user_id);

    if ($stmt->execute()) {
        $success = "Admin status updated successfully!";
        header("refresh:1; url=admn_list");
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php include("partials/head.php"); ?>

<body>
    <!-- Tap on top start -->
    <div class="tap-top">
        <span class="lnr lnr-chevron-up"></span>
    </div>
    <!-- Tap on top end -->

    <!-- Page wrapper Start -->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header Start -->
        <?php include("partials/admn_header.php"); ?>
        <!-- Page Header Ends -->

        <!-- Page Body Start -->
        <div class="page-body-wrapper">
            <!-- Page Sidebar Start -->
            <?php include("partials/admn_sidebar.php"); ?>
            <!-- Page Sidebar Ends -->

            <!-- Container-fluid starts -->
            <div class="page-body">
                <!-- All Admins Table Start -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card card-table">
                                <div class="card-body">
                                    <div class="title-header option-title">
                                        <h5>All Admins</h5>
                                        <form class="d-inline-flex">
                                            <a href="admn_create_admin"
                                                class="align-items-center btn btn-theme d-flex">
                                                <i data-feather="plus-square"></i>Create Admin
                                            </a>
                                        </form>
                                    </div>

                                    <div class="table-responsive admin-table">
                                        <table class="table all-package theme-table" id="table_id">
                                            <thead>
                                                <tr>
                                                    <th>Admin Name</th>
                                                    <th>Role</th>
                                                    <th>Date Created</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php 
                                                $i = 0;
                                                while ($i < count($admins)) {
                                                    $admin = $admins[$i];

                                                    echo '<tr>
                                                        <td>' . htmlspecialchars($admin['user_name']) . '</td>
                                                        <td>' . htmlspecialchars($admin['role_name']) . '</td>
                                                        <td>' . htmlspecialchars($admin['created_at']) . '</td>
                                                        <td class="' . ($admin['is_verified'] == 1 ? 'success' : 'danger') . '">
                                                            <span>' . ($admin['is_verified'] == 1 ? 'Active' : 'Inactive') . '</span>
                                                        </td>

                                                        <td>
                                                            <ul>
                                                                <li>
                                                                    <a href="admn_edit_admin?aid=' . base64_encode($admin['user_id']) . '">
                                                                        <i class="ri-pencil-line"></i>
                                                                    </a>
                                                                </li>

                                                                <li>
                                                                    <a href="?del_id=' . base64_encode($admin['id']) . '" onclick="return confirm(\'Are you sure you want to delete this admin?\')">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </a>
                                                                </li>

                                                                <li>
                                                                    <a href="admn_list?action=' . base64_encode($admin['is_verified'] == 1 ? 'deactivate' : 'activate') . '&act_id=' . base64_encode($admin['id']) . '" 
                                                                    onclick="return confirm(\'Are you sure you want to ' . ($admin['is_verified'] == 1 ? 'deactivate' : 'activate') . ' this admin?\')">
                                                                        <i class="ri-' . ($admin['is_verified'] == 1 ? 'close-line text-danger' : 'check-line text-success') . '"></i>
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
                <!-- All Admins Table Ends -->

                <div class="container-fluid">
                    <!-- Footer start -->
                    <?php include("partials/footer.php"); ?>
                    <!-- Footer end -->
                </div>
            </div>
            <!-- Container-fluid end -->
        </div>
        <!-- Page Body End -->
    </div>

    <?php include("partials/js.php"); ?>
</body>

</html>
