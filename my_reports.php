<?php
session_start();
$vid = $_SESSION['vid'];
if(!isset($vid) || $vid===null || $vid===""){
    header("Location: auth");
}
include("includes/data.php");
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php include("partials/head.php");?>

<body>
    <!-- tap on top start -->
    <div class="tap-top">
        <span class="lnr lnr-chevron-up"></span>
    </div>
    <!-- tap on tap end -->

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

            <!-- index body start -->
            <div class="page-body">
                <!-- Container-fluid starts-->
                <div class="container-fluid">
                    <div class="row">
                        <!-- Salery Summy star-->
                        <div class="col-xl-12 col-lg-12 col-md-6">
                            <div class="card o-hidden">
                                <div class="card-header border-0 pb-1">
                                    <div class="card-header-title">
                                        <h4>Sales Summary</h4>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="saler-summary"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Salery Summy end-->

                        

                        

                       

                   

                    </div>
                </div>
                <!-- Container-fluid Ends-->

                <!-- footer start-->
                <?php include("partials/footer.php");?>
            <!-- Reports Section End -->
        </div>
        <!-- Page Body End-->
    </div>
    <!-- page-wrapper End-->

    <?php include("partials/js.php");?>
</body>

</html>