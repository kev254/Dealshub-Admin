<?php
$vendor = getVendors($vid)[0];
$loggedbtid=$vendor['business_type_id'];
$notificatins=getNotificatiosn($vid);
?>
<div class="page-header">
    <div class="header-wrapper m-0">
        <div class="header-logo-wrapper p-0">
            <div class="logo-wrapper">
                <a href="home.php">
                    <img class="img-fluid main-logo" src="assets/images/logo/logo1.png" alt="logo">
                    <img class="img-fluid white-logo" src="assets/images/logo/logo2.png" alt="logo">
                </a>
            </div>
            <div class="toggle-sidebar">
                <i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
                <a href="home.php">
                    <img src="assets/images/logo/logo1.png" class="img-fluid" alt="">
                </a>
            </div>
        </div>


        <div class="nav-right col-6 pull-right right-header p-0">
            <ul class="nav-menus">
                <li>
                    <span class="header-search">
                        <i class="ri-search-line"></i>
                    </span>
                </li>
                <li class="onhover-dropdown">
                    <div class="notification-box">
                        <i class="ri-notification-line"></i>
                        <span class="badge rounded-pill badge-theme"><?php echo count($notificatins);?></span>
                    </div>
                    <ul class="notification-dropdown onhover-show-div">
                        <li>
                            <i class="ri-notification-line"></i>
                            <h6 class="f-18 mb-0">Notitications</h6>
                        </li>
                        <?php
                        $k = 0;
                        while ($k<count($notificatins)){
                            $notification = $notificatins[$k];
                            echo '<li>
                            <p>
                                <i class="fa fa-circle me-2 font-primary"></i>'.$notification['title'].' <span
                                    class="pull-right text-sm">'.$notification['created_at'].'</span>
                            </p>
                        </li>';
                        $k++;

                        }
                        ?>
                        

                    </ul>
                </li>

                <li>
                    <div class="mode">
                        <i class="ri-moon-line"></i>
                    </div>
                </li>
                <li class="profile-nav onhover-dropdown pe-0 me-0">
                    <div class="media profile-media">
                        <img class="user-profile rounded-circle" src="<?php echo $images_base_url.$vendor['logo'];?>" alt="">
                        <div class="user-name-hide media-body">
                            <span><?php echo $vendor['vendor_name'];?></span>
                            <p class="mb-0 font-roboto"><?php echo $vendor['bus_name'];?><i class="middle ri-arrow-down-s-line"></i></p>
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">

                        <li>
                            <a href="support_ticket">
                                <i data-feather="phone"></i>
                                <span>Spports Tickets</span>
                            </a>
                        </li>
                        <li>
                            <a href="settings">
                                <i data-feather="settings"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li>
                            <a data-bs-toggle="modal" data-bs-target="#staticBackdrop" href="">
                                <i data-feather="log-out"></i>
                                <span>Log out</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>


 <!-- Modal Start -->
 <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="modal-title" id="staticBackdropLabel">Logging Out</h5>
                    <p>Are you sure you want to log out?</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="button-box">
                        <button type="button" class="btn btn--no" data-bs-dismiss="modal">No</button>
                        <button type="button" onclick="location.href = 'logout.php';"
                            class="btn  btn--yes btn-primary">Yes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal End -->