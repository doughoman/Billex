<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<style>
    @media only screen and (min-width: 320px) and (max-width: 575px) {
        body.sidenav-toggled .navbar-sidenav {
            width: unset; 
        }
        body.sidenav-toggled .navbar-sidenav .nav-item, body.sidenav-toggled .navbar-sidenav .nav-link {
            width:unset !important; 
        }
        body.sidenav-toggled .navbar-sidenav .nav-link-text {
            display: unset;
        }
        .admin-contain-main-div{
            padding: 0px;
            padding-left: 0px !important;
            padding-right: 0px !important;
        }
    }
    @media only screen and (min-width: 576px) and (max-width: 767px) {
        body.sidenav-toggled .navbar-sidenav {
            width: unset; 
        }
        body.sidenav-toggled .navbar-sidenav .nav-item, body.sidenav-toggled .navbar-sidenav .nav-link {
            width:unset !important; 
        }
        body.sidenav-toggled .navbar-sidenav .nav-link-text {
            display: unset;
        }
        .admin-contain-main-div{
            padding: 0px;
            padding-left: 0px !important;
            padding-right: 0px !important;
        }
    }
    @media only screen and (min-width: 768px) and (max-width: 991px) {
        body.sidenav-toggled .navbar-sidenav {
            width: unset; 
        }
        body.sidenav-toggled .navbar-sidenav .nav-item, body.sidenav-toggled .navbar-sidenav .nav-link {
            width:unset !important; 
        }
        body.sidenav-toggled .navbar-sidenav .nav-link-text {
            display: unset;
        }
        .admin-contain-main-div{
            padding: 0px;
            padding-left: 0px !important;
            padding-right: 0px !important;
        }
    } 
</style>
<div class="mobile-menu-main" id="mobnav">
    <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">

        <li class="nav-item mobile-link">
            <a class="nav-link nav-link-collapse collapsed " data-toggle="collapse" href="#changes" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/dashboard"'>
                <i class="fas fa-tachometer-alt fa-fw"></i>
                <span class="nav-link-text">&nbsp;&nbsp;Dashboard</span>
            </a>

        </li>
        <li class="nav-item mobile-link">
            <a class="nav-link nav-link-collapse collapsed " data-toggle="collapse" href="#changes" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/Charges"'>
                <i class="fas fa-keyboard echng fa-fw"></i>
                <span class="nav-link-text">&nbsp;&nbsp;Enter Charges</span>
            </a>

        </li>
        <li class="nav-item mobile-link">
            <a class="nav-link nav-link-collapse collapsed " data-toggle="collapse" href="#changes" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/billcharges"'>
                <i class="far fa-file-alt bchange fa-fw"></i>
                <span class="nav-link-text">&nbsp;&nbsp;Bill Charges</span>
            </a>

        </li>
        <li class="nav-item mobile-link">
            <a class="nav-link nav-link-collapse collapsed " data-toggle="collapse" href="#changes" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/postpayment"'>
                <i class="far fa-money-bill-alt pchange fa-fw"></i>
                <span class="nav-link-text">&nbsp;&nbsp;Post Payment</span>
            </a>

        </li>
        <li class="nav-item mobile-link">
            <a class="nav-link nav-link-collapse collapsed " data-toggle="collapse" href="#changes" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/printdeposit"'>
                <i class="fas fa-print pdeposit fa-fw"></i>
                <span class="nav-link-text">&nbsp;&nbsp;Print Deposit</span>
            </a>

        </li>
        <li class="nav-item mobile-link">
            <a class="nav-link nav-link-collapse collapsed " data-toggle="collapse" href="#changes" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/customer"'>
                <i class="far fa-handshake cust fa-fw"></i>
                <span class="nav-link-text">&nbsp;&nbsp;Customers</span>
            </a>
        </li>
        <li class="nav-item mobile-link">
            <a class="nav-link nav-link-collapse collapsed " data-toggle="collapse" href="#administrations" data-parent="#exampleAccordion">
                <i class="fas fa-cog adminst fa-fw"></i>
                <span class="nav-link-text">&nbsp;&nbsp;Administration</span>
            </a>
            <ul class="sidenav-second-level collapse mob-sub" id="administrations">
                <li>
                    <a href="<?php echo base_url(); ?>aut/administration/settings" class="administration_settings"><i class="fas fa-sliders-h fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Settings</span></a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?>aut/administration/users" class="administration_users"><i class="fas fa-users fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Users</span></a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?>aut/administration/item" class="administration_item"><i class="fas fa-list fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Items</span></a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?>aut/administration/subscription" class="administration_subscription"><i class="fas fa-hand-pointer fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Subscription</span></a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?>aut/administration/import" class="administration_import"><i class="fas fa-file-import fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Import</span></a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?>aut/administration/export" class="administration_export"><i class="fas fa-file-export fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Export</span></a>
                </li>
            </ul>
        </li>
        <?php
        $superadmin = FALSE;
        $email_id = dbQueryRows('user_auth', array('user_id' => $_SESSION['login_userid'], 'type' => 'email'));
        $superadmin_array = array('doughoman@gmail.com');
        if (isset($email_id[0]['value']) && in_array($email_id[0]['value'], $superadmin_array)) {
            $superadmin = TRUE;
        }
        ?>
        <li class="nav-item mobile-link" style="display: <?= ($superadmin ? "" : "none"); ?>">
            <a class="nav-link nav-link-collapse collapsed " data-toggle="collapse" href="#superadmin" data-parent="#exampleAccordion">
                <i class="fas fa-cog adminst fa-fw"></i>
                <span class="nav-link-text">&nbsp;&nbsp;Super Admin</span>
            </a>
            <ul class="sidenav-second-level collapse mob-sub" id="superadmin">
                <li>
                    <a href="<?php echo base_url(); ?>aut/alert" class="superadmin_alert"><i class="far fa-bell fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Alerts</span></a>
                </li>
            </ul>
        </li>

        <li class="nav-item mobile-link">
            <a class="nav-link nav-link-collapse collapsed" href="#alert" onclick='window.location.href = "<?php echo base_url(); ?>aut/alert/user_alerts"'>
                <i class="far fa-bell adminst fa-fw"></i>
                <span class="nav-link-text">&nbsp;&nbsp;Alerts</span>
            </a>

        </li>
        <li class="nav-item mobile-link">
            <a class="dropdown-item nav-link-collapse collapsed" href="#" onclick='window.location.href = "<?php echo base_url(); ?>aut/dashboard/user_profile"'>
                <img src="<?= $_SESSION['profileImage']; ?>" class="header_profile_image mob-img">
                &nbsp;My Profile</a>
        </li>
        <li class="nav-item mobile-link">
            <a class="nav-link nav-link-collapse collapsed" href="#" onclick='window.location.href = "<?php echo base_url(); ?>timer"'>
                <i class="far fa-clock adminst fa-fw"></i>
                <span class="nav-link-text">&nbsp;&nbsp;Timer</span>
            </a>

        </li>
        <li class="p-0 nav-item mobile-link">
            <a class="dropdown-item nav-link-collapse collapsed" href="#" onclick='window.location.href = "<?php echo base_url(); ?>aut/administration/subscription"'><i class="fas fa-donate bchange fa-fw"></i>&nbsp;&nbsp;<span class="nav-link-text">Subscription</span></a>
        </li>
        <li class="p-0 nav-item mobile-link">
            <a class="dropdown-item nav-link-collapse collapsed" id="comapct_view" href="javascript:void(0)"><i class="fas fa-crop fa-fw compact_icon"></i>&nbsp;&nbsp;<span class="nav-link-text"><?php
                    if (isset($_COOKIE['compact']) && $_COOKIE['compact'] == 1) {
                        echo 'Default View';
                    } else {
                        echo 'Compact View';
                    }
                    ?></span></a>
        </li>
        <li class="p-0 nav-item mobile-link">
            <a class="dropdown-item" href="#" onclick='window.location.href = "<?php echo base_url(); ?>logout"'>
                <i class="fas fa-sign-out-alt"></i><span class="nav-link-text">&nbsp;&nbsp;&nbsp;Sign Out</span></a>
        </li>
    </ul>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script type="text/javascript">
    $(document).ready(function () {
        if (!window.matchMedia("(max-width: 991px)").matches) {
            window.location.href = BASE_URL + "aut/dashboard";
        }
    });
</script>