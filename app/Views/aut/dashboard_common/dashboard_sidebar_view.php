
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-darks fixed-top fixed-top2" id="mainNav">
    <div class="timer_search_ico cursor-pointer" style="display: none;">
        <i class="fas fa-bars" id="showLeft" style="display: <?= (isset($_REQUEST['list']) || isset($_REQUEST['calender']) ? "none" : ""); ?>"></i>
        <i class="fas fa-chevron-left back_graph cursor-pointer no_mob" style="display: none;" style="display: <?= (isset($_REQUEST['calender']) ? "none" : ""); ?>"></i>
    </div>
    <button class="navbar-toggler navbar-toggler-right ml-auto no_timer_page no_project_page" type="button" data-toggle="collapse" data-target="" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation" onclick='window.location.href = "<?php echo base_url(); ?>aut/dashboard/mobile_menu"'>
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">

            <li class="nav-item side_bar_menu_li" data-toggle="tooltip" data-placement="right" title="Dashboard">
                <a class="nav-link nav-link-collapse collapsed dashboard_link" data-toggle="collapse" href="#Dashboard" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/dashboard"'>
                    <i class="fas fa-tachometer-alt black fa-fw"></i>
                    <span class="nav-link-text">&nbsp;&nbsp;Dashboard</span>
                </a>
            </li>
            <li class="nav-item side_bar_menu_li" data-toggle="tooltip" data-placement="right" title="Timer">
                <a class="nav-link nav-link-collapse collapsed timer_link" target="billx-timer" data-parent="#exampleAccordion" onclick="window.open('<?php echo base_url(); ?>timer', 'menu_timer', 'location=yes,height=855,width=400,scrollbars=yes,status=yes'); return false;">
                    <i class="far fa-clock black fa-fw"></i>
                    <span class="nav-link-text">&nbsp;&nbsp;Timer</span>
                </a>
            </li>
            <li class="nav-item side_bar_menu_li" data-toggle="tooltip" data-placement="right" title="Enter Changes">
                <a class="nav-link nav-link-collapse collapsed charges_link" data-toggle="collapse" href="#changes" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/charges"'>
                    <i class="fas fa-keyboard skyblue fa-fw"></i>
                    <span class="nav-link-text">&nbsp;&nbsp;Enter Charges</span>
                </a>

            </li>
            <li class="nav-item side_bar_menu_li" data-toggle="tooltip" data-placement="right" title="Bill changes">
                <a class="nav-link nav-link-collapse collapsed bill_link" data-toggle="collapse" href="#billchange" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/billcharges?q=initialize"'>
                    <i class="far fa-file-alt brown fa-fw"></i>
                    <span class="nav-link-text">&nbsp;&nbsp;Bill Charges</span>
                </a>

            </li>
            <li class="nav-item side_bar_menu_li" data-toggle="tooltip" data-placement="right" title="Post Payment">
                <a class="nav-link nav-link-collapse collapsed postpayment_link" data-toggle="collapse" href="#postpayment" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/postpayment"'>
                    <i class="far fa-money-bill-alt green fa-fw"></i>
                    <span class="nav-link-text">&nbsp;&nbsp;Post Payment</span>
                </a>

            </li>
            <li class="nav-item side_bar_menu_li" data-toggle="tooltip" data-placement="right" title="Print Deposit">
                <a class="nav-link nav-link-collapse collapsed deposit_link" data-toggle="collapse" href="#print" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/printdeposit"'>
                    <i class="fas fa-print black fa-fw"></i>
                    <span class="nav-link-text">&nbsp;&nbsp;Print Deposit</span>
                </a>

            </li>
            <li class="nav-item side_bar_menu_li" data-toggle="tooltip" data-placement="right" title="Customers">
                <a class="nav-link nav-link-collapse collapsed customers_link" data-toggle="collapse" href="#customer" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/customer?q=initialize"'>
                    <i class="far fa-handshake gold fa-fw"></i>
                    <span class="nav-link-text">&nbsp;&nbsp;Customers</span>
                </a>

            </li>
            <li class="nav-item side_bar_menu_li" data-toggle="tooltip" data-placement="right" title="Administration">
                <a class="nav-link nav-link-collapse collapsed administration_link" data-toggle="collapse" href="#administration" data-parent="#exampleAccordion">
                    <i class="fas fa-cog gray fa-fw"></i>
                    <span class="nav-link-text">&nbsp;&nbsp;Administration</span>
                </a>
            </li>
            <li class="nav-item side_bar_menu_li">
                <ul class="sidenav-second-level collapse" id="administration">
                    <li data-toggle="tooltip" data-placement="right" title="Settings">
                        <a href="<?php echo base_url(); ?>aut/administration/settings" class="administration_settings"><i class="fas fa-sliders-h fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Settings</span></a>
                    </li>
                    <li data-toggle="tooltip" data-placement="right" title="Users">
                        <a href="<?php echo base_url(); ?>aut/administration/users" class="administration_users"><i class="fas fa-users fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Users</span></a>
                    </li>
                    <li data-toggle="tooltip" data-placement="right" title="Items">
                        <a href="<?php echo base_url(); ?>aut/administration/item?q=initialize" class="administration_item"><i class="fas fa-list fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Items</span></a>
                    </li>
                    <li data-toggle="tooltip" data-placement="right" title="Subscription">
                        <a href="<?php echo base_url(); ?>aut/administration/subscription" class="administration_subscription"><i class="fas fa-hand-pointer fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Subscription</span></a>
                    </li>
                    <li data-toggle="tooltip" data-placement="right" title="Import">
                        <a href="<?php echo base_url(); ?>aut/administration/import" class="administration_import"><i class="fas fa-file-import fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Import</span></a>
                    </li>
                    <li data-toggle="tooltip" data-placement="right" title="Export">
                        <a href="<?php echo base_url(); ?>aut/administration/export" class="administration_export"><i class="fas fa-file-export fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Export</span></a>
                    </li>
                </ul>
            </li>
            <?php
//            $superadmin = FALSE;
//            $email_id = dbQueryRows('user_auth', array('user_id' => $_SESSION['login_userid'], 'type' => 'email'));
//            $superadmin_array = array('doughoman@gmail.com');
//            if (isset($email_id[0]['value']) && in_array($email_id[0]['value'], $superadmin_array)) {
//                $superadmin = TRUE;
//            }
            ?>
            <li class="nav-item side_bar_menu_li" data-toggle="tooltip" data-placement="right" title="Super Admin" style="display: <?= (isset($superadmin) ? "" : "none"); ?>">
                <a class="nav-link nav-link-collapse collapsed superadmin_link" data-toggle="collapse" href="#superadmin" data-parent="#exampleAccordion">
                    <i class="fas fa-cog gray fa-fw"></i>
                    <span class="nav-link-text">&nbsp;&nbsp;Super Admin</span>
                </a>
                <ul class="sidenav-second-level collapse" id="superadmin">
                    <li>
                        <a href="<?php echo base_url(); ?>aut/alert" class="superadmin_alert"><i class="far fa-bell fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Alerts</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown no_desktop">
                <a class="nav-link nv dropdown-toggle mr-lg-2 remove_down_icon" id="messagesDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?= (isset($_SESSION['profileImage']) ? $_SESSION['profileImage'] : ""); ?>" class="header_profile_image">
                </a>
                <div class="dropdown-menu dm" aria-labelledby="messagesDropdown">
                    <a class="dropdown-item" href="<?php echo base_url(); ?>aut/dashboard/user_profile">
                        My Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">
                        Subscription
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo base_url(); ?>logout">
                        SIgn Out
                    </a>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav sidenav-toggler">
            <li class="nav-item">
                <a class="nav-link nv text-center" id="sidenavToggler">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        </ul>
        <ul class="user_display_name no_mob">
            <li class="nav-item dropdown">
                <a class="nav-link nv dropdown-toggle mr-lg-2 no_allform display_name_color" id="messagesDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="login_user_name"><?= (isset($_SESSION["display_name"]) ? $_SESSION["display_name"] : ""); ?></span>
                </a>
                <div class="dropdown-menu display_name_dropdown no_allform" aria-labelledby="messagesDropdown">
                    <a class="dropdown-item" href="#">
                        Change Biller
                    </a>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav no_mob">
            <li class="nav-item dropdown ">
                <a class="nav-link nv dropdown-toggle mr-lg-2 remove_down_icon no_allform" id="alertsDropdown" href="<?php echo base_url(); ?>aut/alert/user_alerts">
                    <i class="far fa-bell"></i>
                    <span class="d-lg-none">Alerts
                        <span class="badge badge-pill badge-warning">6 New</span>
                    </span>
                </a>
            </li>
            <li class="nav-item dropdown ">
                <a class="nav-link nv dropdown-toggle mr-lg-2 remove_down_icon no_allform" id="messagesDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?= (isset($_SESSION['profileImage']) ? $_SESSION['profileImage'] : ""); ?>" class="header_profile_image">
                </a>
                <div class="dropdown-menu dm no_allform" aria-labelledby="messagesDropdown">
                    <a class="dropdown-item" href="<?php echo base_url(); ?>aut/dashboard/user_profile">
                        My Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo base_url(); ?>aut/administration/subscription">
                        Subscription
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" id="comapct_view" href="javascript:void(0)">
                        <?php
                        if (isset($_COOKIE['compact']) && $_COOKIE['compact'] == 1) {
                            echo 'Default View';
                        } else {
                            echo 'Compact View';
                        }
                        ?>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo base_url(); ?>logout">
                        Sign Out
                    </a>
                </div>
            </li>

        </ul>
    </div>
    <div class="no_desktop chav_mobile_ico">
        <a href="#" class="chav-left-menu-click">
            <i class="fas fa-chevron-left"></i>
        </a>
    </div>
</nav>
<nav class="menu push push-left no_other_page" id="mainNav">
    <a href="#" class="backBtn"><i class="fas fa-arrow-left"></i></a>
    <div class="new-nav navbar-collapse">
        <ul class="navbar-nav navbar-sidenav" id="exampleAccordion1">
            <li class="nav-item side_bar_menu_li" data-toggle="tooltip" data-placement="right" title="Customers">
                <a class="nav-link nav-link-collapse collapsed customers_link" data-toggle="collapse" href="#customer" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/customer?q=initialize"'>
                    <i class="far fa-handshake gold fa-fw"></i>
                    <span class="nav-link-text">&nbsp;&nbsp;Customers</span>
                </a>
            </li>
            <li class="nav-item side_bar_menu_li" data-toggle="tooltip" data-placement="right" title="Projects">
                <a class="nav-link nav-link-collapse collapsed deposit_link" data-toggle="collapse" href="#Projects" data-parent="#exampleAccordion" onclick='window.location.href = "<?php echo base_url(); ?>aut/timer/projectlist"'>
                    <i class="fas fa-tasks fa-fw"></i>
                    <span class="nav-link-text">&nbsp;&nbsp;Projects</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
<div class="content-wrapper admin-contain-main-div">