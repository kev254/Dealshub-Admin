<?php 
session_start();
include "includes/data.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reponse='';
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $reponse=json_decode(login($email,$password), true);


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
                            <h3>Welcome To Deals Hub Kenya</h3>
                            <h4>Log In Your Account</h4>
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
                                        <input type="email" class="form-control" id="email" placeholder="Email Address" name="email">
                                        <label for="email">Email Address</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Password">
                                        <label for="password">Password</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="forgot-box">
                                        <div class="form-check ps-0 m-0 remember-box">
                                            <input class="checkbox_animated check-box" type="checkbox"
                                                id="flexCheckDefault">
                                            <label class="form-check-label" for="flexCheckDefault">Remember me</label>
                                        </div>
                                        <a href="forgot" class="forgot-password">Forgot Password?</a>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-animation w-100 justify-content-center">Log
                                        In</button>
                                    
                                </div>
                            </form>
                        </div>

                        <div class="other-log-in">
                            <h6>or</h6>
                        </div>

                        <div class="log-in-button">
                            <ul>
                                <li>
                                    <a href="tel:+254701451519" class="btn google-button w-100">
                                        <img src="../assets/images/inner-page/google.png" class="blur-up lazyload"
                                            alt=""> Contact Support
                                    </a>
                                </li>
                               
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- login section end -->

</body>

</html>