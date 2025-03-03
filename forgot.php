<?php
include "includes/data.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reponse='';
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $reponse=json_decode(reset_pass($email), true);


}
?>
<!DOCTYPE html>
<html lang="en">

<?php include("partials/head.php");?>


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
                            <h4>Request Password Reset</h4>
                        </div>
                        <?php if (isset($reponse)): ?>
                            <div class="alert <?php echo $reponse['success'] ? 'alert-success' : 'alert-success'; ?> text-center">
                                <?php echo $reponse['message']; ?>
                            </div>
                            
                        <?php endif; ?>

                        <div class="input-box">
                            <form class="row g-4" method="POST" action="">
                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email Address">
                                        <label for="email">Email Address</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-animation w-100 justify-content-center">Send
                                        link</button>
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