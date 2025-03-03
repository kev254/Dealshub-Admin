<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");

// Fetch the current user data from the database based on the vendor ID
$user_query = "SELECT u.name as user_name, u.email, u.password FROM users u WHERE u.vendor_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $vid);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows == 0) {
    // If the user is not found
    header("Location: my_vendor");
    exit;
}

$data = $user_result->fetch_assoc();
$user_stmt->close();

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $error = '';
    $success = '';

    // Validate password match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Update user's email in the users table
        $update_user_query = "UPDATE users SET email = ? WHERE vendor_id = ?";
        $update_user_stmt = $conn->prepare($update_user_query);
        $update_user_stmt->bind_param("si", $email, $vid);

        // Update password if provided
        if (!empty($password)) {
            $update_password_query = "UPDATE users SET password = ? WHERE vendor_id = ?";
            $update_password_stmt = $conn->prepare($update_password_query);
            $hashed_password = md5($password); // Hash the password
            $update_password_stmt->bind_param("si", $hashed_password, $vid);
            $update_password_stmt->execute();
            $update_password_stmt->close();
        }

        // Execute the user update
        if ($update_user_stmt->execute()) {
            $success = "Profile updated successfully!";
            header("refresh:1; url=admn_settings");
        } else {
            $error = "Error: " . $update_user_stmt->error;
        }

        $update_user_stmt->close();
    }
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
        <?php include("partials/admn_header.php"); ?>

        <!-- Page Header Ends-->

        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            <!-- Page Sidebar Start-->
            <?php include("partials/admn_sidebar.php"); ?>
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
                                                    <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (isset($success)): ?>
                                                    <div class="alert alert-success mt-3"><?= htmlspecialchars($success) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <form class="theme-form theme-form-2 mega-form" method="POST"
                                                enctype="multipart/form-data">
                                                <div class="row">


                                                    <div class="mb-4 row align-items-center">
                                                        <label class="form-label-title col-sm-2 mb-0">Enter Email
                                                            Address</label>
                                                        <div class="col-sm-10">
                                                            <input class="form-control" type="email" name="email"
                                                                placeholder="Enter Your Email Address"
                                                                value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                                                        </div>
                                                    </div>





                                                    <div class="mb-4 row align-items-center">
                                                        <label class="form-label-title col-sm-2 mb-0">Password</label>
                                                        <div class="col-sm-10">
                                                            <input class="form-control" type="password" name="password"
                                                                placeholder="Enter Your Password">
                                                        </div>
                                                    </div>

                                                    <div class="mb-4 row align-items-center">
                                                        <label class="form-label-title col-sm-2 mb-0">Confirm
                                                            Password</label>
                                                        <div class="col-sm-10">
                                                            <input class="form-control" type="password"
                                                                name="confirm_password"
                                                                placeholder="Enter Your Confirm Password">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <button class="btn btn-primary" type="submit">Update
                                                            Profile</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                    </div>


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
        <?php include("partials/footer.php"); ?>

        <!-- footer End-->
    </div>
    <!-- page-wrapper End-->

    <?php include("partials/js.php"); ?>

</body>

</html>