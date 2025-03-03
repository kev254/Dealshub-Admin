<?php
session_start();
$vid = $_SESSION['vid'];
if(!isset($vid) || $vid === null || $vid === ""){
    header("Location: auth");
}
include("includes/data.php");
$branches = getBranches($vid);

// Fetch the current vendor and user data from the database based on the vendor ID
$vendor_query = "SELECT v.*, u.name as user_name, u.password FROM vendors v JOIN users u ON v.id = u.vendor_id WHERE v.id = ?";
$vendor_stmt = $conn->prepare($vendor_query);
$vendor_stmt->bind_param("i", $vid);
$vendor_stmt->execute();
$vendor_result = $vendor_stmt->get_result();

if ($vendor_result->num_rows == 0) {
    // If the vendor is not found
    header("Location: my_vendor");
    exit;
}

$data = $vendor_result->fetch_assoc();
$vendor_stmt->close();


// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include("includes/dbconnect.php");

    // Retrieve and sanitize input data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    $vendor_id = $vid;

    // Fetch the existing logo before updating
    $existing_logo = "";
    $get_logo_query = "SELECT logo FROM vendors WHERE id = ?";
    $stmt = $conn->prepare($get_logo_query);
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $stmt->bind_result($existing_logo);
    $stmt->fetch();
    $stmt->close();

    // Handle photo upload
    $photo = $existing_logo; // Default to the existing logo
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "/home/eyewitne/dealshub.co.ke/stores/";
        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $photo_name;

        if (!is_dir($target_dir)) {
            $errro ="Error: Target directory does not exist - $target_dir";
        }

        if ($_FILES["photo"]["error"] !== UPLOAD_ERR_OK) {
            $errro ="File Upload Error: " . $_FILES["photo"]["error"];
        }

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo = "stores/".$photo_name;
        } else {
            $errro ="Error: File could not be moved to $target_file. Check permissions.";
        }
    }

    // Update password if provided
    if (!empty($password)) {
        if ($password !== $confirm_password) {
            $errro = "Error: Passwords do not match.";
        } else {
            $hashed_password = md5($password);
            $update_password_query = "UPDATE users SET password = ? WHERE vendor_id = ?";
            $update_password_stmt = $conn->prepare($update_password_query);
            $update_password_stmt->bind_param("si", $hashed_password, $vendor_id);
            $update_password_stmt->execute();
        }
    }

    // Update vendor details without overriding the logo if not changed
    $update_vendor_query = "UPDATE vendors SET name = ?, email = ?, phone = ?, updated_at = NOW(), logo = ? WHERE id = ?";
    $update_vendor_stmt = $conn->prepare($update_vendor_query);
    $update_vendor_stmt->bind_param("ssssi", $first_name, $email, $phone, $photo, $vendor_id);
    $update_vendor_stmt->execute();

    // Update user email
    $update_user_query = "UPDATE users SET email = ? WHERE vendor_id = ?";
    $update_user_stmt = $conn->prepare($update_user_query);
    $update_user_stmt->bind_param("si", $email, $vendor_id);
    $update_user_stmt->execute();

    $success= "Vendor updated successfully.";
}

?>


<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php include("partials/head.php"); ?>

<body>
    <!-- tap on top start-->
    <div class="tap-top">
        <span class="lnr lnr-chevron-up"></span>
    </div>
    <!-- tap on tap end-->

    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header Start-->
        <?php include("partials/header.php");?>

        <!-- Page Header Ends-->

        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            <!-- Page Sidebar Start-->
            <?php include("partials/sidebar.php");?>
            <!-- Page Sidebar Ends-->

            <!-- Settings Section Start -->
            <div class="page-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <!-- Details Start -->
                                    <div class="card">
                                    <div class="card-body">
    <div class="title-header option-title">
        <h5>Profile Setting</h5>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success mt-3"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
    </div>
    <form class="theme-form theme-form-2 mega-form" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="mb-4 row align-items-center">
                <label class="form-label-title col-sm-2 mb-0">First Name</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="first_name" placeholder="Enter Your First Name" value="<?= htmlspecialchars($data['name'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label class="form-label-title col-sm-2 mb-0">Representative Name</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="rep_name" placeholder="Enter Representative Name" value="<?= htmlspecialchars($data['rep_name'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label class="form-label-title col-sm-2 mb-0">Your Phone Number</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="phone" placeholder="Enter Your Number" value="<?= htmlspecialchars($data['phone'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label class="form-label-title col-sm-2 mb-0">Enter Email Address</label>
                <div class="col-sm-10">
                    <input class="form-control" type="email" name="email" placeholder="Enter Your Email Address" value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label class="form-label-title col-sm-2 mb-0">Address</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="address" placeholder="Enter Address" value="<?= htmlspecialchars($data['address'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label class="col-sm-2 col-form-label form-label-title">Photo</label>
                <div class="col-sm-10">
                    <input class="form-control form-choose" type="file" name="photo" id="formFileMultiple" multiple>
                    <p class="mt-2">Current Logo: <img src="<?= $images_base_url.htmlspecialchars($data['logo'] ?? '') ?>" alt="Logo" style="max-width: 100px; height: auto;"></p>
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label class="form-label-title col-sm-2 mb-0">Password</label>
                <div class="col-sm-10">
                    <input class="form-control" type="password" name="password" placeholder="Enter Your Password">
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label class="form-label-title col-sm-2 mb-0">Confirm Password</label>
                <div class="col-sm-10">
                    <input class="form-control" type="password" name="confirm_password" placeholder="Enter Your Confirm Password">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <button class="btn btn-primary" type="submit">Update Profile</button>
            </div>
        </div>
    </form>
</div>

</div>

                                    <!-- Details End -->

                                    <!-- Address Start -->
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-header-2 mb-3">
                                                <h5>Branches</h5>
                                            </div>

                                            <div class="save-details-box">
                                                <div class="row g-4">
                                                    <?php 
                                                    $i = 0;
                                                    while ($i < count($branches)) {
                                                        $branch = $branches[$i]; // Accessing current branch from the array
                                                        
                                                        echo '<div class="col-xl-4 col-md-6">
                                                                <div class="save-details">
                                                                    <div class="save-name">
                                                                        <h5>' . htmlspecialchars($branch['branch_name']) . '</h5>
                                                                    </div>

                                                                    <div class="save-position">
                                                                        <h6>' . htmlspecialchars($branch['latitude'] ." ".$branch['longitude']) . '</h6> 
                                                                    </div>

                                                                    <div class="save-address">
                                                                        <p>' . htmlspecialchars($branch['longitude']) . '</p>
                                                                        <p>' . htmlspecialchars($branch['branch_name']) . '</p>
                                                                        <p>' . htmlspecialchars($branch['created_at']) . '</p>
                                                                    </div>

                                                                    <div class="mobile">
                                                                        <p class="mobile">Mobile No. ' . htmlspecialchars($branch['phone']) . '</p> <!-- Assuming mobile is a key -->
                                                                    </div>

                                                                    <div class="button">
                                                                        <a href="edit_branch.php?bid=' . (base64_encode($branch['id'])) . '" class="btn btn-sm">Edit</a>
                                                                        <a href="#=' . $branch['id'] . '" class="btn btn-sm">View on Map</a>
                                                                    </div>
                                                                </div>
                                                            </div>';
                                                        
                                                        $i++; // Increment the loop counter
                                                    }
                                                    ?>
                                                </div>
                                            </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- Address End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Settings Section End -->
        </div>
        <!-- Page Body End-->

        <!-- footer start-->
        <?php include("partials/footer.php");?>

        <!-- footer End-->
    </div>
    <!-- page-wrapper End-->

    <?php include("partials/js.php");?>

</body>

</html>