<?php
include "includes/data.php";

// Check if reset code is provided in URL
if (isset($_GET['cuuid'])) {
    $reset_code = mysqli_real_escape_string($conn, $_GET['cuuid']);
    $reset_code = base64_decode($reset_code);
    
    // Check if the reset code exists in the database
    $sql = "SELECT id, email FROM users WHERE reset_password_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $reset_code);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // If the reset code is found, show the reset form
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
            $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

            // Check if passwords match
            if ($new_password !== $confirm_password) {
                $error_message = "Passwords do not match.";
            } else {
                // Hash the new password
                $hashed_password = md5($new_password);

                // Update the password and set reset_password_code to NULL
                $update_sql = "UPDATE users SET password = ?, reset_password_code = NULL WHERE reset_password_code = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $hashed_password, $reset_code);
                if ($update_stmt->execute()) {
                    $success_message = "Your password has been changed successfully!";
                    header("refresh:1; url=auth");
                } else {
                    $error_message = "An error occurred while updating the password. Please try again!";
                }
            }
        }
    } else {
        $error_message = "Invalid or expired reset token.";
    }
} else {
    $error_message = "No reset code provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("partials/head.php"); ?>

<body>

    <!-- login section start -->
    <section class="log-in-section section-b-space">
        <a href="" class="logo-login"><img src="assets/images/logo/logo1.png" class="img-fluid"></a>
        <div class="container w-100">
            <div class="row">

                <div class="col-xl-5 col-lg-6 me-auto">
                    <div class="log-in-box">
                        <div class="log-in-title">
                            <h3>Welcome To Deals Hub</h3>
                            <h4>Reset Your Password</h4>
                        </div>

                        <!-- Display messages (success or error) -->
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success text-center">
                                <?php echo $success_message; ?>
                            </div>
                        <?php elseif (isset($error_message)): ?>
                            <div class="alert alert-danger text-center">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Reset password form -->
                        <div class="input-box">
                            <form class="row g-4" method="POST" action="">
                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" required>
                                        <label for="new_password">New Password</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                                        <label for="confirm_password">Confirm Password</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-animation w-100 justify-content-center">Reset Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- login section end -->

</body>
</html>
