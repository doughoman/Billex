<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<!--
Required for browsers that do not yet support the latest technologies.
http://webcomponents.org/
https://github.com/webcomponents/webcomponentsjs/releases
-->
<link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<style>
    #exampleAccordion {
        display: none;
    }
    .timer_search_ico{
        display: block !important;
    }
    body.fixed-nav {
        padding-top: 0px;
    }
    @media (min-width: 992px){
        .content-wrapper {
            margin-left: 0px;
        }
    }
    #mainNav.fixed-top .sidenav-toggler {
        display: none !important;
    }
    @media (min-width: 992px){
        footer.sticky-footer {
            width: calc(100%);
        } 
    }
    .menu-left {
        left: -280px;
    }
    .menu-left.left-open {
        left: 0;
    }
    chart{
        display: block;
        width: 100%;
    }
    .menu {
        border-right: 1px solid #327052;
        background-color: #40926a;
        -webkit-transition: all 0.3s ease;
        -moz-transition: all 0.3s ease;
        transition: all 0.3s ease;
        position: fixed;
        top: 0;
        z-index: 1035;
        width: 250px;
        height: 100%;
    }
    .menu a {
        display: block;
        color: #fff;
        padding: 16px;

        text-decoration: none;
        position: relative;
        z-index: 11;
    }

    .menu a:hover, .menu a:active {
        color: #ffffff;
        background-color: #327052;
    }
    .backBtn {
        background-color: #327052;
        font-size: 16px;
        text-align: right;
    }

    .new-nav ul li a i {
        color: #fff;
    }
    .push {
        -webkit-transition: all 0.3s ease;
        -moz-transition: all 0.3s ease;
        transition: all 0.3s ease;
    }
    .push-left {
        left: -280px;
    }
    .push-left.pushleft-open {
        left: 0;
    }
    .push-toleft {
        left: 280px;
    }
    .fixed-top2 {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1030;
    }
    .timer_select_option .bootstrap-select>.dropdown-toggle:after{
        border: unset;
    }
    .chav_mobile_ico{
        display: none;
    }
    .bootbox-ok-button:hover {
        color: #ffffff !important;
    }
    .onoffswitch {
        position: relative; 
        width: 80px;
        -webkit-user-select:none; 
        -moz-user-select:none; 
        -ms-user-select: none;
    }
    .pin-chkbox .onoffswitch{
        width: 80px;
    }
    .onoffswitch-checkbox {
        display: none;
    }
    .onoffswitch-label {
        display: block; 
        overflow: hidden; 
        cursor: pointer;
        height: 27px;
        border-radius: 20px;
        margin-bottom: 0;
    }
    .onoffswitch-inner {
        display: block; 
        width: 200%; 
        margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }
    .onoffswitch-inner:before, .onoffswitch-inner:after {
        display: block; 
        float: left; 
        width: 50%; 
        height: 27px; 
        padding: 0; 
        line-height: 27px;
        font-size: 14px; 
        color: white; 
        font-family: Trebuchet, Arial, sans-serif; 
        font-weight: bold;
        box-sizing: border-box;
    }
    .onoffswitch-inner:before {
        content: "Multi";
        padding-left: 10px;
        background-color: #40926A; 
        color: #FFFFFF;
    }
    .onoffswitch-inner:after {
        content: "Single";
        padding-right: 10px;
        background-color: #b0abab; 
        color: #FFFFFF;
        text-align: right;
    }
    .rotary_time_type .onoffswitch-inner:before {
        content: "Hour";
    }
    .rotary_time_type .onoffswitch{
        width: 75px;
        margin-top: 20px;
        margin-left: 10px;
    }
    .rotary_time_type .onoffswitch-switch{
        right: 47px;
    }
    .rotary_time_type .onoffswitch-inner:after {
        content: "Min";
    }
    .pin-chkbox .onoffswitch-inner:before {
        content: "Pin";
    }
    .pin-chkbox .onoffswitch-inner:after {
        content: "Unpin";
    }
    .onoffswitch-switch {
        display: block; 
        width: 16px;
        margin: 3px;
        background: #FFFFFF;
        position: absolute; 
        top: 2px; 
        bottom: 0;
        right: 53px;
        height: 17px;
        border-radius: 20px;
        transition: all 0.3s ease-in 0s; 
    }
    .pin-chkbox .onoffswitch-switch{
        right: 50px;
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
        right: 7px; 
    }
    .dropdown-header {
        text-transform: capitalize;
    }
    @media only screen and (min-width: 320px) and (max-width: 575px) {
        .chav_mobile_ico,.main_title_div,.no_timer_page{
            display: none;
        }
        .no_other_page{
            display: block;
        }
        .new-nav>ul> li>a {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        #mainNav .navbar-collapse {
            max-height: 90vh;
        }
        .new-nav .sidenav-second-level{
            padding-left:30px !important;
            list-style: none;
        }
        .bg-darks {
            background-color: transparent;
            padding: 0;
        }
        .admin-contain-main-div_new{
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .footer-section{
            display: none;
        }
    }
    @media only screen and (min-width: 576px) and (max-width: 767px) {
        .chav_mobile_ico,.main_title_div,.no_timer_page{
            display: none;
        }
        .no_other_page{
            display: block;
        }
        .new-nav>ul> li>a {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        #mainNav .navbar-collapse {
            max-height: 90vh;
        }
        .new-nav .sidenav-second-level{
            padding-left:30px !important;
            list-style: none;
        }
        .bg-darks {
            background-color: transparent;
            padding: 0;
        }
        .admin-contain-main-div_new{
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .footer-section{
            display: none;
        }
    }
    @media only screen and (min-width: 768px) and (max-width: 991px) {
        .timer_search_ico{
            display: none !important;
        }
    }
    @media only screen and (max-width: 1440px) and (min-width: 1200px){}
    .no_other_page {
        display: block; 
    }
</style>
<div class="container-fluid timer_main_div">
    <div class="form-group row no_calender" style="display: <?= (isset($_REQUEST['list']) || isset($_REQUEST['calender']) ? "none" : ""); ?>">
        <div class="col-md-12 col-lg-4 col-sm-12 col-xl-4">
            <div class="select_customer" id="customer_timer">
                <div class="custom-dropdown big type_select_option timer_select_option">
                    <div class="dropdown bootstrap-select customer_timer_select show" style="width: 100%;">
                        <button type="button" class="btn dropdown-toggle btn-light bs-placeholder" id="show_project_list" role="button" title="New Timer">
                            <div class="filter-option">
                                <div class="filter-option-inner">
                                    <div class="filter-option-inner-inner">New Timer</div> 
                                </div> 
                            </div>
                        </button>
                    </div>
                    <i class="fas fa-search timer_search no_mob"></i>
                </div>
            </div>
        </div>
    </div>
    <button style="display: none;"><div class="add-ico"><i class="fas fa-plus"></i></div></button>
    <input type="hidden" class="timezone" value="<?= get_timezone($_SESSION['login_userid']); ?>">
    <div class="form-group row no_calender" style="display: <?= (isset($_REQUEST['list']) || isset($_REQUEST['calender']) ? "none" : ""); ?>">
        <div class="col-md-12 col-lg-4 col-sm-12 col-xl-4 order-two">
            <div class="timer_column">
                <div class="main-timer-div timer_running_new_div" data-stop="false" style="display: none;">
                    <input type="hidden" id="timer_id" class="timer_id">
                    <input type="hidden" id="timer_project_id" class="timer_project_id">
                    <input type="hidden" class="customer_id">
                    <div class="timer_card">
                        <div class="timer-header">
                            <div class="name-ofcust">
                                <h6 class="cursor-pointer"></h6>
                                <p></p>
                            </div>
                            <div class="time-count">   
                                <p class="cursor-pointer">
                                    <span id="hours" class="hours_sap running_hours">0</span>
                                    <span id="colon" class="hours_sap colon">:</span>
                                    <span id="minutes" style="font-size: 15px;font-weight: bold;" class="running_minutes">00</span>
                                </p>
                            </div>
                        </div>
                        <div class="timer-footer">
                            <div class="chat">
                                <i class="far fa-comment cursor-pointer add_timer_description" data-type="des"></i>
                                <i class="fas fa-pencil-alt cursor-pointer timer_edit_icon"></i>
                            </div>
                            <div class="play-action">
                                <div class="action">
                                    <i class="fas fa-play-circle cursor-pointer start" id="start" style="display: none;"></i>
                                    <i class="fas fa-pause-circle cursor-pointer stop" id="stop" ></i>
                                </div>
                            </div>
                            <div class="time">  
                                <p class="start_time cursor-pointer start_time_new" data-type="start">
                                    <span class="start_timer_hour start_timer_running_hour"></span>:<span class="start_timer_minute start_timer_running_minute"></span> -</p>&nbsp;
                                <p class="end_time cursor-pointer end_time_new" data-type="end">
                                    <span class="stop_timer_hour"></span>:<span class="stop_timer_minute"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="running_timer_list">
                </div>
                <div class="pin_timer_listing">
                    <?php
                    foreach ($timer_data as $value) {
                        if ($value['name'] == 'Internal Tracking' && $value["identifier"] == 'internal' && $value['item_id'] == 0 && $value['ct_id'] == 5) {
                            continue;
                        }
                        if (strlen(dechex($value['color_dec'])) < 6) {
                            $dif = 6 - intval(strlen(dechex($value['color_dec'])));
                            $add = '';
                            for ($i = 0; $i < $dif; $i++) {
                                $add.='0';
                            }
                            $value['color_dec'] = $add . dechex($value['color_dec']);
                        } else {
                            $value['color_dec'] = dechex($value['color_dec']);
                        }
                        ?>
                        <div class="main-timer-div-new new_main_box_<?= $value['timer_project_id']; ?>" data-id="<?= base64_encode(encrypt($value['customer_id'])); ?>" style="background-color: rgb(255, 255, 255);border: 3px solid #<?= $value['color_dec']; ?>;">
                            <input type="hidden" class="timer_project_id" value="<?= base64_encode(encrypt($value['timer_project_id'])); ?>">
                            <input type="hidden" class="customer_id" value="<?= $value['customer_id']; ?>">
                            <div class="timer-header" style="color:#000000;">
                                <div class="name-ofcust-new">
                                    <h6><?= $value['name']; ?></h6>
                                    <p><span><?= ($value["identifier"] == "" ? "&nbsp;" : $value["identifier"]); ?></span> <?= (($value["identifier"] == '' && $value["po"] == '') || ($value["identifier"] == '' || $value["po"] == '') ? "" : "/") ?> <span><?= $value["po"]; ?></span></p>
                                </div>
                                <div class="timer-footer" style="color:#007bff;">
                                    <div class="chat">
                                        <i class="fas fa-pencil-alt cursor-pointer timer_edit_icon" data-type="pin_timer" data-id="<?= base64_encode(encrypt($value['timer_project_id'])); ?>" id="timer_edit<?= $value['timer_project_id']; ?>"></i>
                                    </div>
                                    <div class="play-action">
                                        <div class="action">
                                            <i class="fas fa-play-circle cursor-pointer pin_timer_start" data-ctid="<?= base64_encode(encrypt($value['ct_id'])); ?>" data-itemid="<?= base64_encode(encrypt($value['item_id'])); ?>" id="start<?= $value['timer_project_id']; ?>"></i>
                                            <i class="fas fa-pause-circle cursor-pointer" id="stop<?= $value['timer_project_id']; ?>" style="display: none;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>

            </div>
        </div>
        <div class="col-md-12 col-lg-8 col-sm-12 col-xl-8 order-one">
            <div class="timer_csv_download">
                <a href="javascript:void(0)"><i class="fas fa-file-download cursor-pointer"></i></a>
            </div>
            <div class="timer_chart_main_div" style="display: none;">
                <div class="timer_chart_column today_chart">
                    <div class="show_total_timer">
                        <p class="mb-0 show_today today_graph_title cursor-pointer">Today</p>
                        <p class="show_today cursor-pointer"><span class="total_today_hours"></span>:<span class="total_today_minute"></span></p>
                        <div class="today_graph_ico">
                            <i class="fas fa-chevron-left day_graph_backward cursor-pointer"></i>
                            <i class="fas fa-chevron-right day_graph_forward cursor-pointer" style="display: none;"></i>
                        </div>
                    </div>
                    <div id="container" style="min-width: 200px; max-width: 200px;"></div>
                </div>
                <div class="timer_chart_column" style="">
                    <div class="show_total_timer">
                        <p class="mb-0 week_graph_title cursor-pointer">
                            <i class="fas fa-search-minus week_change_icon"></i>
                            <span class="show_weekly week_graph_type">This Week</span>
                        </p>
                        <p class="week_graph_total show_weekly cursor-pointer">
                            <span class="total_week_hours"></span>:<span class="total_week_minute"></span>
                        </p>
                        <div class="today_graph_ico">
                            <i class="fas fa-chevron-left week_graph_backward cursor-pointer"></i>
                            <i class="fas fa-chevron-right week_graph_forward cursor-pointer" style="display: none;"></i>
                        </div>
                        <input type="hidden" class="month_graph_date" value="<?= date('Y-m-d'); ?>">
                    </div>
                    <div id="container1" style="min-width: 200px; max-width: 200px;"></div>
                </div>
            </div>

        </div>
    </div>
    <div class="timer_profile_image no_desktop" style="display: <?= (isset($_REQUEST['list']) || isset($_REQUEST['calender']) ? "none" : ""); ?>">
        <ul class="navbar-nav">
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
    <!-- Calender Start -->
    <input type="hidden" value="0" class="week_count">
    <input type="hidden" value="0" class="graph_count">
    <div class="claender_main_div" style="display: none;">
    </div>
    <!-- Day Section Start-->
    <div class="today_timer" style="display: none;">
        <div class="add_project_div">
            <a href="JavaScript:void(0);" class="btn btn-submit no_mob add_day_timer"><i class="fas fa-user-plus"></i> &nbsp;Add</a>
            <button class="add_ico no_desktop add_day_timer"><div class="add-ico"><i class="fas fa-plus"></i></div></button>
        </div>
        <div class="back_today_div">
            <i class="fas fa-times back_today_graph cursor-pointer"></i>
        </div>
        <div class="calender_total_heading">
            <div class="month_div day_div">
                <input type="hidden" value="<?= date('D, F j', strtotime(convert_timezone($_SESSION['login_userid'], date('Y-m-d H:i:s'), 'UTC'))); ?>" class="today_new_date">
                <i class="fas fa-chevron-left date_backward cursor-pointer"></i>
                <h4 class="today_date"></h4>
                <i class="fas fa-chevron-right date_forward cursor-pointer"></i>
            </div>
            <div class="day-time">
                <p class="cursor-pointer">
                    <span id="hours1" class="hours_sap"></span>
                    <span id="colon1" class="hours_sap" style="visibility: visible;">:</span>
                    <span id="minutes1"></span></p>
            </div>
        </div>
        <div class="today_listing">

        </div>
    </div>
    <!-- Day Section End -->
</div>
<div class="modal fade" id="edit_detail_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="edit_popup_button">
                    <div class=""><a href="#" id="delete_timer" class="delete_link">Delete</a></div>
                    <div class=""><a href="#" data-dismiss="modal" aria-label="Close">Cancel</a></div>
                    <div class=""><input type="button" value="Save" class="btn btn-submit" id="save_timer" onclick="save_timer();"></div>
                </div>
            </div>  
            <div class="modal-body edit_detail_body">
                <form id='edit_detail'>
                    <input type="hidden" name="form[timer_id]" id="edit_timer_id">
                    <input type="hidden" name="form[timer_project_id]" id="edit_timer_project_id">
                    <input type="hidden" name="form[customer_id]" id="customer_id">
                    <input type="hidden" name="form[new_timer_id]" class="new_timer_id">
                    <input type="hidden" name="form[new_time_stop]" class="time_stop_new_value">
                    <input type="hidden" name="form[stop_time_stop]" class="stop_new_value">
                    <input type="hidden" name="form[color_dec]" id="pickcolor">
                    <div class="user-property-top">
                        <div class="property-title">
                        </div>
                    </div>
                    <div class="timer_project_header">
                        <div class="new_project_list cursor-pointer timer_project_box"  style="background-color: rgb(255, 255, 255);">
                            <div class="timer-header" style="color:#000000;">
                                <div class="name-ofcust-new">
                                    <h6 class="customer_name">Select Project</h6>
                                    <p class="id_po"></p>
                                    <p class="item_chargetype"></p>
                                </div>
                                <span class="set_edit_icon no_pin_timer">
                                    <i class="fas fa-pencil-alt" style="color: #007bff;"></i>
                                </span>
                            </div>
                        </div> 
                        <div class="property-color">
                            <div class="color-wrapper">
                                <div class="color-holder call-picker" style=""></div>
                                <div class="color-picker" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group no_pin_timer">
                        <label>Description</label>
                        <textarea class="form-control" id="timer_description1" rows="3" name="form[description]"></textarea>
                    </div>
                    <div class="no_pin_timer">
                        <p>Date</p>
                        <div id="datepicker" class="input-group date" data-date-format="mm/dd/yyyy">
                            <input class="form-control" type="text" name="form[time_start]" autocomplete="off" id="time_start"/>
                            <span class="input-group-text input-group-addon dp"><i class="fas fa-calendar-alt"></i></span>
                        </div><br/>
                        <div class="row">
                            <div class="col-sm-6">
                                <p>Start Time</p>
                                <div class="input-group mb-3">
                                    <input type="text" readonly="" class="form-control start_time" data-timetype="start" data-type="in_modal" name="form[start_time]" id="edit_start_timer">
                                    <input type="hidden" name="form[diff_start]" id="diff_start">
                                    <div class="input-group-append">
                                        <button type="button" class="btn-clock input-group-text edit_start_time start_time" data-timetype="start" data-type="in_modal"><i class="far fa-clock"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <p class="popup_end_label">End Time</p>
                                <div class="input-group mb-3">
                                    <input type="text" readonly="" class="form-control end_time" data-timetype="end" data-type="in_modal" name="form[end_time]" id="edit_end_timer">
                                    <input type="hidden" name="form[diff_end]" id="diff_end">
                                    <div class="input-group-append">
                                        <button type="button" class="btn-clock input-group-text end_start_time end_time" data-timetype="end" data-type="in_modal"><i class="far fa-clock"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lock-status">
                        <div class="lock-title">
                            <p style="margin: 0;">Multi-Timer</p>
                        </div>
                        <div class="lock-bn new_lock_unclock">
                            <i class="far fa-clock"></i>
                            <div class="onoffswitch">
                                <input type="hidden" name="form[locked]" class="locked_value" value="0">
                                <input type="checkbox" class="onoffswitch-checkbox" id="locked_unlocked" value="0">
                                <label class="onoffswitch-label" for="locked_unlocked">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                            <img src="<?php echo base_url(); ?>images/dual_clock.svg" class="dual_clock">
                        </div>
                    </div>
                    <p class="multi_timer_text" style="color: gray;">Enabling multi-timer allows the timer to continue running along with other timers.</p>
                    <div class="lock-status">
                        <div class="pin-title">
                            <p class="mb-0">Pin Project</p>
                        </div>
                        <div class="pin-chkbox">
                            <div class="onoffswitch">
                                <input type="hidden" name="form[sticky_order]" class="sticky_value" value="0">
                                <input type="checkbox" class="onoffswitch-checkbox" id="sticky_order" value="0">
                                <label class="onoffswitch-label" for="sticky_order">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div><i class="fas fa-map-pin"></i>
                        </div>
                    </div>
                    <p class="pin_timer_text" style="color: gray;">Pinning the project keeps it viewable for frequent access.</p>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit_description_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="edit_popup_button">
                    <div class=""><a href="#" id="delete_timer">Delete</a></div>
                    <div class=""><a href="#" data-dismiss="modal" aria-label="Close">Cancel</a></div>
                    <div class=""><input type="button" value="Save" class="btn btn-submit" onclick="save_desc();"></div>
                </div>
            </div>  
            <div class="modal-body edit_detail_body">
                <form id='edit_description'>
                    <input type="hidden" name="form[timer_id]" class="desc_timer_id">
                    <div class="form-group no_pin_timer">
                        <label>Description</label>
                        <textarea class="form-control" id="timer_description12" rows="3" name="form[description]"></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit_start_end_time" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered edit_startend_time" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="edit_popup_button">
                    <div class=""><a href="javaScript:void(0);" id="edit_time_cancel" data-dismiss="modal" aria-label="Close">Cancel</a></div>
                    <div class=""><button type="button" class="btn btn-sm btn-primary btn_save_time right">Done</button></div>
                </div>
            </div>  
            <div class="modal-body edit_startend_body">
                <input type="hidden" class="timer_id">
                <div class="rotary_time_type">
                    <div class="onoffswitch">
                        <input type="checkbox" class="onoffswitch-checkbox change_time_type" id="minute_hour" value="0">
                        <label class="onoffswitch-label" for="minute_hour">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <div class="rotary_switch_main_div">
                    <input type="text" class="rotarySwitch" value="0">    
                </div>
                <div class="input-bar">
                    <div class="input-date selected" id="new_start">
                        <div class="title">Start</div>
                        <div class="content">2:40 am</div>
                    </div>
                    <div class="duration_details">
                        Duration
                        <div class="time_duration"></div>
                    </div>
                    <div class="input-date" id="new_end">
                        <div class="title">End</div>
                        <div class="content">3:30 am</div>
                    </div>
                </div>
                <div class="navigation-bar">
                    <a href="#" class="nav-btn" id="prev"></a>
                    <a href="#" class="nav-btn" id="next"></a>
                </div>
                <div id="timeframe">
                    <div class="frame-number">1</div>
                    <div class="frame_time_show"><div class="frame"></div></div>
                    <div class="otherframe"></div>
                </div>
                <div class="form-group mb-0" style="display: none;">
                    <div class='date clockpicker-with-callbacks edit_running_time' id='datetimepicker3'>
                        <p class="mb-0 time_lable">Start Time</p>
                        <input type="text" pattern="[0-9]*" class="form-control" id="input-a" inputmode="numeric" inputmode="number" placeholder="Now">
                        <div class="onoffswitch">
                            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="ampm_switch" checked>
                            <label class="onoffswitch-label mb-0" for="ampm_switch">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="project_list" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered project_list_modal" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title">Projects</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body edit_detail_body">
                <div class="w-100" style="text-align: right;margin-bottom: 10px;">
                    <button class="btn btn-primary w-100" id="start_new_project" style="margin-bottom: 10px;">
                        <i class="far fa-clock" style="margin-right: 5px;"></i>Quick Start New Timer
                    </button>
                    <a href="JavaScript:void(0);" class="btn btn-success add_project w-100" id="add_new_quick_project">
                        <i class="fas fa-user-clock" style="margin-right: 5px;"></i> &nbsp;Add New Project
                    </a>
                </div>
                <div class="new_project_listing">
                    <?php
                    foreach ($project_list as $value) {
                        if ($value['name'] == 'Internal Tracking' && $value["identifier"] == 'internal' && $value['item_id'] == 0 && $value['ct_id'] == 5) {
                            continue;
                        }
                        if (strlen(dechex($value['color_dec'])) < 6) {
                            $dif = 6 - intval(strlen(dechex($value['color_dec'])));
                            $add = '';
                            for ($i = 0; $i < $dif; $i++) {
                                $add.='0';
                            }
                            $value['color_dec'] = $add . dechex($value['color_dec']);
                        } else {
                            $value['color_dec'] = dechex($value['color_dec']);
                        }
                        ?>
                        <div class="new_project_list cursor-pointer start_timer new_main_box_<?= $value['timer_project_id']; ?>" data-custid="<?= base64_encode(encrypt($value['customer_id'])); ?>" data-id="<?= base64_encode(encrypt($value['timer_project_id'])); ?>" style="background-color: rgb(255, 255, 255);border: 3px solid #<?= $value['color_dec']; ?>;">
                            <div class="timer-header" style="color:#000000;">
                                <div class="name-ofcust-new">
                                    <h6><?= $value['name']; ?></h6>
                                    <p>
                                        <?= ($value["identifier"] == "" ? "" : '<span>' . $value["identifier"] . '</span>'); ?> 
                                        <?= (($value["identifier"] == '' && $value["po"] == '') || ($value["identifier"] == '' || $value["po"] == '') ? "" : "/") ?> 
                                        <?= ($value["po"] == "" ? "" : '<span>' . $value["po"] . '</span>'); ?>
                                    </p>
                                    <?php
                                    if ($value['item_id'] == 0) {
                                        if ($value['ct_id'] == 0) {
                                            echo '';
                                        } else {
                                            $charge_type = dbQueryRows('charge_type', array('ct_id' => $value['ct_id']))[0]['name'];
                                            echo $charge_type;
                                        }
                                    }
                                    if ($value['item_id'] > 0) {
                                        $item = dbQueryRows('item', array('item_id' => $value['item_id']))[0]['name'];
                                        echo $item;
                                    }
                                    ?>
                                </div>
                                <span class="set_edit_icon no_pin_timer1" style="display: none;">
                                    <i class="fas fa-pencil-alt" style="color: #007bff;"></i>
                                </span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add_project_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Add Timer Project</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body edit_detail_body">
                <form id='add_timer_project'>
                    <div class="form-group">
                        <label>Customer</label>
                        <div class="custom-dropdown big type_select_option">
                            <select class="selectpicker customer_timer_select" data-live-search="true" onchange="set_customer_value(this);" name="form[customer_id]" data-width="<?= (isset($_COOKIE['compact']) && $_COOKIE['compact'] == 1 ? "50%" : "100%"); ?>" title="Select Customer" id="customer_listing">
                                <?php
                                echo '<option value="0">Add Customer</option>';
                                echo '<option data-divider="true"></option>';
                                foreach ($customer_data as $key => $value) {
                                    if ($value['status'] == "inactive") {
                                        continue;
                                    }
                                    echo '<option data-id="' . $value["customer_id"] . '" value="' . $value["customer_id"] . '" data-subtext="' . ($value["identifier"] == "" ? "&nbsp;" : $value["identifier"]) . (($value["identifier"] == '' && $value["po"] == '') || ($value["identifier"] == '' || $value["po"] == '') ? "" : "/") . $value["po"] . '">' . $value['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php
                    $download = $billable_labor = $service = $product = $reimbursement = $unbillable_labor = $unbillable_other = array();
                    foreach ($item_data as $item_value) {
                        if ($item_value["type"] == "1") {
                            $billable_labor[] = $item_value;
                        }
                        if ($item_value["type"] == "2") {
                            $service[] = $item_value;
                        }
                        if ($item_value["type"] == "3") {
                            $product[] = $item_value;
                        }
                        if ($item_value["type"] == "4") {
                            $reimbursement[] = $item_value;
                        }
                        if ($item_value["type"] == "5") {
                            $unbillable_labor[] = $item_value;
                        }
                        if ($item_value["type"] == "6") {
                            $unbillable_other[] = $item_value;
                        }
                        if ($item_value["type"] == "7") {
                            $download[] = $item_value;
                        }
                    }
                    ?>
                    <div class="form-group">
                        <label for="item_listing">Item</label>
                        <div class="custom-dropdown big type_select_option">
                            <select class="selectpicker select_charge_type" name="form[item_id]" onchange="set_item_value(this);" data-live-search="true"  data-width="100%" title="Choose one of the item" id="item_listing">
                                <option value="add_new">Add Item</option>
                                <option data-divider="true"></option>
                                <option value="0">Other Charge</option>
                                <?php
                                if (count($billable_labor) >= 1) {
                                    echo '<optgroup label="Billable Labor" id="billable_labor">';
                                    foreach ($billable_labor as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($service) >= 1) {
                                    echo '<optgroup label="Service" id="service">';
                                    foreach ($service as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($product) >= 1) {
                                    echo '<optgroup label="Product" id="product">';
                                    foreach ($product as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($download) >= 1) {
                                    echo '<optgroup label="Download" id="download">';
                                    foreach ($download as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($reimbursement) >= 1) {
                                    echo '<optgroup label="Reimbursement" id="reimbursement">';
                                    foreach ($reimbursement as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($unbillable_labor) >= 1) {
                                    echo '<optgroup label="Unbillable Labor" id="unbillable_labor">';
                                    foreach ($unbillable_labor as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }if (count($unbillable_other) >= 1) {
                                    echo '<optgroup label="Unbillable Other" id="unbillable_other">';
                                    foreach ($unbillable_other as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="item_listing">Charge Type</label>
                        <div class="custom-dropdown big type_select_option">
                            <select class="selectpicker select_charge_type" name="form[ct_id]" data-live-search="true"  data-width="100%" title="Choose one of the type" id="type_listing">
                                <?php
                                foreach ($chargetype_data as $value) {
                                    echo '<option value="' . $value['ct_id'] . '">' . $value['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="add_quick_timer_project" onclick="add_timer_project();">Add Timer Project</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add_customer_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Add Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id='add_customer'>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped" id="customer_name" name="form[name]" value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Job ID</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped" name="form[identifier]" value="">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="add_customer();">Save changes</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add_item_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Add Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id='add_item'>
                    <div>	
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control tooltipped change_item" name="form[name]" id="item_name">                                
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Bill Rate</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control tooltipped change_item" name="form[rate]" id="customer_discount">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Can Discount?</label>
                            <div class="col-sm-9 type_select_option">
                                <select class="form-control tooltipped change_itemd selectpicker"  data-width="100%" name="form[can_discount]">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Type</label>
                            <div class="col-sm-9 type_select_option">
                                <select class="form-control change_itemd selectpicker" name="form[ct_id]" data-live-search="true"  data-width="100%">
                                    <option value="Billable Labor">Billable Labor</option>
                                    <option value="Service">Service</option>
                                    <option value="Product">Product</option>
                                    <option value="Download">Download</option>
                                    <option value="Reimbursement">Reimbursement</option>
                                    <option value="Unbillable Labor">Unbillable Labor</option>
                                    <option value="Unbillable Other">Unbillable Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="add_item();">Save changes</button>
            </div>
        </div>
    </div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="https://js.pusher.com/5.1/pusher.min.js"></script>
<script src="<?php echo base_url(); ?>js/jquery.rotaryswitch.js" type="text/javascript" async=""></script>
<script src="<?php echo base_url(); ?>js/clock.js" type="text/javascript" async=""></script>
<script src="<?php echo base_url(); ?>js/timer.js"></script>
<script type="text/javascript">
                    // Enable pusher logging - don't include this in production
                    Pusher.logToConsole = false;

                    var pusher = new Pusher('<?php echo env('PUSHER.APP_KEY'); ?>', {
                        cluster: 'us2',
                        forceTLS: true,
                        activityTimeout: 60000
                    });

                    var channel = pusher.subscribe('timer-<?php echo $_SESSION['login_userid']; ?>');
                    channel.bind('Start-Timer', function (data) {
                        $.ajax({
                            url: BASE_URL + "aut/timer/get_running_timer",
                            type: "POST",
                            data: {},
                            dataType: "JSON",
                            success: function (data)
                            {
                                show_running_timer(data);
                            }
                        });
                    });

                    channel.bind('Stop-Timer', function (data) {
                        $.ajax({
                            url: BASE_URL + "aut/timer/get_running_timer",
                            type: "POST",
                            data: {},
                            dataType: "JSON",
                            success: function (data)
                            {
                                $('.pin_timer_listing').find('.main-timer-div-new').show();
                                $.each(data, function (i, value) {
                                    $('.pin_timer_listing').find(".new_main_box_" + value.timer_project_id).hide();
                                });
                            }
                        });
                        $.ajax({
                            url: BASE_URL + "aut/timer/get_running_timer",
                            type: "POST",
                            data: {},
                            dataType: "JSON",
                            success: function (data)
                            {
                                tab_focus_set(data);
                            }
                        });
                    });
                    channel.bind('Edit-Timer', function (data) {
                        $.ajax({
                            url: BASE_URL + "aut/timer/get_running_timer",
                            type: "POST",
                            data: {},
                            dataType: "JSON",
                            success: function (data)
                            {
                                tab_focus_set(data);
                            }
                        });
                    });

                    var colorList = ['990000', '993300', '333300', '003300', '003366', '000066', '333399', '333333',
                        '660000', 'FF6633', '666633', '336633', '336666', '0066FF', '666699', '666666', 'CC3333', 'FF9933', '99CC33', '669966', '66CCCC', '3366FF', '663366', '999999', 'CC66FF', 'FFCC33', 'FFFF66', '99FF66', '99CCCC', '993366', 'CCCCCC', 'FF99CC', 'CCffCC', '99CCFF', 'CC99FF'];
                    var picker = $('.color-picker');
                    for (var i = 0; i < colorList.length; i++) {
                        picker.append('<li class="color-item" data-hex="' + '#' + colorList[i] + '" style="background-color:' + '#' + colorList[i] + ';"></li>');
                    }
                    $('body').click(function () {
                        picker.fadeOut();
                    });
                    $('.call-picker').click(function (event) {
                        event.stopPropagation();
                        picker.fadeIn();
                        picker.children('li').click(function () {
                            codeHex = $(this).data('hex');
                            $('.color-holder').css('background-color', codeHex);
                            $('.timer_project_box').css('border', '3px solid ' + codeHex);
                            $('#pickcolor').val(codeHex);
                        });
                    });
</script>