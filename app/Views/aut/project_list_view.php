<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
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

    .timer_select_option .bootstrap-select>.dropdown-toggle:after{
        border: unset;
    }
    .bootbox-ok-button:hover {
        color: #ffffff !important;
    }
    .onoffswitch {
        position: relative; 
        width: 62px;
        -webkit-user-select:none; 
        -moz-user-select:none; 
        -ms-user-select: none;
    }
    .onoffswitch-checkbox {
        display: none;
    }
    .onoffswitch-label {
        display: block; 
        overflow: hidden; 
        cursor: pointer;
        border: 2px solid #40926A; 
        border-radius: 20px;
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
        height: 23px; 
        padding: 0; 
        line-height: 23px;
        font-size: 14px; 
        color: white; 
        font-family: Trebuchet, Arial, sans-serif; 
        font-weight: bold;
        box-sizing: border-box;
    }
    .onoffswitch-inner:before {
        content: "AM";
        padding-left: 10px;
        background-color: #40926A; 
        color: #FFFFFF;
    }
    .onoffswitch-inner:after {
        content: "PM";
        padding-right: 10px;
        background-color: #40926A; 
        color: #FFFFFF;
        text-align: right;
    }
    .onoffswitch-switch {
        display: block; 
        width: 20px;
        margin: 3px;
        background: #FFFFFF;
        position: absolute; 
        top: 0; 
        bottom: 0;
        right: 35px;
        border: 2px solid #40926A; 
        border-radius: 20px;
        transition: all 0.3s ease-in 0s; 
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
        right: 0px; 
    }
    .color-holder {
        width: 33px;
        height: 23px;
    }
    .dropdown-header {
        text-transform: capitalize;
    }
    @media only screen and (min-width: 320px) and (max-width: 575px) {
        .timer_main_div {
            padding-top: 40px;
        }
        .no_project_page{
            visibility: hidden;
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
        .admin-contain-main-div_new{
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .footer-section{
            display: none;
        }
        .chav_mobile_ico{
            display: none;
        }
        .timer_search_ico i{
            color: #ffffff;
            font-size: 22px;
            position: relative;
            top: 0px;
            left: 0px;
        }
    }
    @media only screen and (min-width: 576px) and (max-width: 767px) {
        .timer_main_div {
            padding-top: 40px;
        }
        .no_project_page{
            visibility: hidden;
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
        .admin-contain-main-div_new{
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .footer-section{
            display: none;
        }
        .chav_mobile_ico{
            display: none;
        }
        .timer_search_ico i{
            color: #ffffff;
            font-size: 22px;
            position: relative;
            top: 0px;
            left: 0px;
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
    .back_graph{
        display: block !important;
    }
    #showLeft{
        display: none;
    }
</style>
<div class="container-fluid timer_main_div">
    <div class="add_project_div">
        <a href="JavaScript:void(0);" class="btn btn-submit no_mob add_project">
            <i class="fas fa-user-plus"></i> &nbsp;Add Project
        </a>
        <button class="add_ico no_desktop add_timer_project"><div class="add-ico"><i class="fas fa-plus"></i></div></button>
    </div>
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
        <div class="new_project_list new_main_box_<?= $value['timer_project_id']; ?>" data-id="<?= base64_encode(encrypt($value['timer_project_id'])); ?>" style="background-color: rgb(255, 255, 255);border: 3px solid #<?= $value['color_dec']; ?>;">
            <div class="timer-header" style="color:#000000;">
                <div class="name-ofcust-new">
                    <h6><?= $value['name']; ?></h6>
                    <p><?= ($value["identifier"] == "" ? "<span style='visibility: hidden;'>212</span>" : '<span>' . $value["identifier"] . '</span>'); ?> <?= (($value["identifier"] == '' && $value["po"] == '') || ($value["identifier"] == '' || $value["po"] == '') ? "" : "/") ?> <?= ($value["po"] == "" ? "" : '<span>' . $value["po"] . '</span>'); ?></p>
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
                <div class="property-color">
                    <div class="color-wrapper">
                        <div class="color-holder call-picker" style="background-color: #<?= $value['color_dec']; ?>"></div>
                        <div class="color-picker" style="display: none"></div>
                    </div>
                </div>
            </div>
            <div class="timer-footer new_timer_footer" style="color:#007bff;">
                <div class="action">
                    <i class="fas fa-play-circle cursor-pointer pin_timer_start" data-ctid="<?= base64_encode(encrypt($value['ct_id'])); ?>" data-itemid="<?= base64_encode(encrypt($value['item_id'])); ?>" data-projectid="<?= base64_encode(encrypt($value['timer_project_id'])); ?>" data-custid="<?= base64_encode(encrypt($value['customer_id'])); ?>" id="start<?= $value['timer_project_id']; ?>"></i>
                </div>
                <div class="action new_project_action">
                    <?php
                    if ($value['sticky_order'] == 1) {
                        echo '<i class="fas fa-map-pin pin_project cursor-pointer" style="color: gold;" data-type="pin"></i>';
                    } else {
                        echo '<i class="fas fa-map-pin pin_project cursor-pointer" data-type="unpin" style="color: #b0abab;"></i>';
                    }
                    if ($value['locked'] == 0) {
                        echo '<i class="far fa-clock lock_project cursor-pointer" data-type="unlock" style="color: #b0abab;"></i>';
                    } else {
                        echo '<svg id="Layer_1" class="lock_project cursor-pointer" data-name="Layer 1" data-type="lock" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 18"><defs><style>.cls-1{fill:gold;}.cls-1,.cls-2{stroke:gold;}.cls-1,.cls-2,.cls-3{stroke-linecap:round;stroke-linejoin:round;}.cls-2,.cls-3{fill:none;}.cls-3{stroke:#fff;}</style></defs><title>radio</title><rect class="cls-1" x="0.5" y="3.5" width="27" height="14" rx="0.85"/><line class="cls-2" x1="5.5" y1="0.5" x2="8.5" y2="0.5"/><line class="cls-2" x1="7" y1="3.5" x2="7" y2="0.5"/><line class="cls-2" x1="22.5" y1="0.5" x2="19.5" y2="0.5"/><line class="cls-2" x1="21" y1="3.5" x2="21" y2="0.5"/><circle class="cls-3" cx="7.5" cy="10.5" r="5"/><circle class="cls-3" cx="20.5" cy="10.5" r="5"/><polyline class="cls-3" points="5 12.5 7.5 10.5 10 10.5"/><polyline class="cls-3" points="23 12 20.5 10.5 22.5 7.5"/></svg>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <div class="add_project_div bottom_add_project_div" style="display: none;">
        <a href="JavaScript:void(0);" class="btn btn-submit no_mob add_project"><i class="fas fa-user-plus"></i> &nbsp;Add Project</a>
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
                                    echo '<optgroup label="Billable Labor">';
                                    foreach ($billable_labor as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($service) >= 1) {
                                    echo '<optgroup label="Service">';
                                    foreach ($service as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($product) >= 1) {
                                    echo '<optgroup label="Product">';
                                    foreach ($product as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($download) >= 1) {
                                    echo '<optgroup label="Download">';
                                    foreach ($download as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($reimbursement) >= 1) {
                                    echo '<optgroup label="Reimbursement">';
                                    foreach ($reimbursement as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($unbillable_labor) >= 1) {
                                    echo '<optgroup label="Unbillable Labor">';
                                    foreach ($unbillable_labor as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-name="' . $value["name"] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }if (count($unbillable_other) >= 1) {
                                    echo '<optgroup label="Unbillable Other">';
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
                <button type="button" class="btn btn-primary" onclick="add_timer_project();">Add Timer Project</button>
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
<script type="text/javascript">
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) { // 300px from top
            $('.bottom_add_project_div').fadeIn();
        } else {
            $('.bottom_add_project_div').fadeOut();
        }
    });
    if ($(window).width() < 767) {
        $('.selectpicker').selectpicker({
            size: 3
        });
    } else {
        $('.selectpicker').selectpicker({
            size: 10
        });
    }
    if ($(window).width() == 500) {
        $('.selectpicker').selectpicker({
            size: 10
        });
    }
    $('#showLeft').click(function () {
        $('.content-wrapper').toggleClass('push-toleft');
        $('.push-left').toggleClass('pushleft-open');
        return false;
    });
    $('.backBtn').click(function () {
        $('.content-wrapper').removeClass('push-toleft');
        $('.push-left').removeClass('pushleft-open');
    });
    $(document).on('click', '.content-wrapper', function (e) {
        if ($('.push-left').hasClass('pushleft-open')) {
            $('.content-wrapper').removeClass('push-toleft');
            $('.push-left').removeClass('pushleft-open');
            return false;
        }
    });
    var colorList = ['990000', '993300', '333300', '003300', '003366', '000066', '333399', '333333',
        '660000', 'FF6633', '666633', '336633', '336666', '0066FF', '666699', '666666', 'CC3333', 'FF9933', '99CC33', '669966', '66CCCC', '3366FF', '663366', '999999', 'CC66FF', 'FFCC33', 'FFFF66', '99FF66', '99CCCC', '993366', 'CCCCCC', 'FF99CC', 'CCffCC', '99CCFF', 'CC99FF'];
    var picker = $(".color-picker");
    var picker1 = $("#color-picker1");
    for (var i = 0; i < colorList.length; i++) {
        picker.append('<li class="color-item" data-hex="' + '#' + colorList[i] + '" style="background-color:' + '#' + colorList[i] + ';"></li>');
        picker1.append('<li class="color-item1" data-hex="' + '#' + colorList[i] + '" style="background-color:' + '#' + colorList[i] + ';"></li>');
    }
    $('body').click(function () {
        picker.fadeOut();
        picker1.fadeOut();
    });
    $(document).on("click", ".call-picker,.call-picker1", function (event) {
        event.stopPropagation();
        $this = $(this);
        $(this).parents('.color-wrapper').find('.color-picker').fadeIn();
        $(this).parents('.color-wrapper').find('.color-picker').children('li').hover(function () {
            codeHex = $(this).data('hex');
            $this.css('background-color', codeHex);
        });
    });
    $(document).on("click", ".color-holder1", function (event) {
        event.stopPropagation();
        $this = $(this);
        $(this).parents('.color-wrapper').find('.color-picker1').fadeIn();
        $(this).parents('.color-wrapper').find('.color-picker1').children('li').hover(function () {
            codeHex = $(this).data('hex');
            $this.css('background-color', codeHex);
            $('#pickcolor').val(codeHex);
        });
    });
    $(document).on("click", ".color-item", function () {
        $this = $(this);
        $.ajax({
            url: BASE_URL + "aut/timer/project_edit",
            type: "POST",
            data: {'key': 'color_dec', 'value': $(this).data('hex').substring(1), 'timer_project_id': $(this).parents('.new_project_list').data('id')},
            dataType: "JSON",
            success: function (data)
            {
                if (data.timer_project_id) {
                    $this.parents('.new_project_list').css('border', '3px solid #' + data.color_dec);
                }
            }
        });
    });
    $(document).on("click", ".back_graph", function () {
        window.history.back();
    });
    $(document).on("click", ".pin_project", function () {
        if ($(this).data('type') == "pin") {
            val = 0;
            svg = '<i class="fas fa-map-pin pin_project cursor-pointer" data-type="unpin" style="color: #b0abab;"></i>';
        } else {
            val = 1;
            svg = '<i class="fas fa-map-pin pin_project cursor-pointer" style="color: gold;" data-type="pin"></i>';
        }
        $this = $(this);
        edit_project($this, svg, val, 'sticky_order');
    });
    $(document).on("click", ".lock_project", function () {
        if ($(this).data('type') == "lock") {
            val = 0;
            icon = '<i class="far fa-clock lock_project cursor-pointer" data-type="unlock" style="color: #b0abab;"></i>';
        } else {
            val = 1;
            icon = '<svg id="Layer_1" class="lock_project cursor-pointer" data-name="Layer 1" data-type="lock" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 18"><defs><style>.cls-1{fill:gold;}.cls-1,.cls-2{stroke:gold;}.cls-1,.cls-2,.cls-3{stroke-linecap:round;stroke-linejoin:round;}.cls-2,.cls-3{fill:none;}.cls-3{stroke:#fff;}</style></defs><title>radio</title><rect class="cls-1" x="0.5" y="3.5" width="27" height="14" rx="0.85"/><line class="cls-2" x1="5.5" y1="0.5" x2="8.5" y2="0.5"/><line class="cls-2" x1="7" y1="3.5" x2="7" y2="0.5"/><line class="cls-2" x1="22.5" y1="0.5" x2="19.5" y2="0.5"/><line class="cls-2" x1="21" y1="3.5" x2="21" y2="0.5"/><circle class="cls-3" cx="7.5" cy="10.5" r="5"/><circle class="cls-3" cx="20.5" cy="10.5" r="5"/><polyline class="cls-3" points="5 12.5 7.5 10.5 10 10.5"/><polyline class="cls-3" points="23 12 20.5 10.5 22.5 7.5"/></svg>';
        }
        $this = $(this);
        edit_project($this, icon, val, 'locked');
    });
    $(document).on("click", ".add_project,.add_timer_project", function () {
        $("#add_project_modal").modal("show");
    });
    $(document).on('click', '.pin_timer_start', function () {
        window.location.href = BASE_URL + 'timer?custid=' + $(this).data('custid') + '&projectid=' + $(this).data('projectid') + '&running_id=<?php echo $running_id; ?>';
    });
    function edit_project($this, icon, val, key) {
        $.ajax({
            url: BASE_URL + "aut/timer/project_edit",
            type: "POST",
            data: {'key': key, 'value': val, 'timer_project_id': $this.parents('.new_project_list').data('id')},
            dataType: "JSON",
            success: function (data)
            {
                if (data.timer_project_id) {
                    if (key == "sticky_order") {
                        $this.parents('.new_project_action').prepend(icon);
                        $this.remove();
                    } else {
                        $this.before(icon);
                        $this.remove();
                    }
                }
            }
        });
    }
    function add_timer_project() {
        if ($("#customer_listing").val() == "" && $("#item_listing").val() == "" && $("#type_listing").val() == "") {
            $('#add_timer_project').find('.dropdown-toggle').css('border', '1px solid red');
        } else {
            $.ajax({
                url: BASE_URL + "aut/timer/add_timer_project",
                type: "POST",
                data: new FormData($('#add_timer_project')[0]),
                dataType: "JSON",
                enctype: 'multipart/form-data',
                success: function (data)
                {
                    location.reload();
                },
                processData: false,
                contentType: false
            });
        }
    }
    function set_customer_value(input) {
        if ($(input).find(':selected').val() == 0) {
            $("#add_customer_modal").modal('show');
        }
    }
    function add_customer() {
        if ($("#customer_name").val() == "") {
            $("#customer_name").css('border', '1px solid red');
        } else {
            $.ajax({
                url: BASE_URL + "aut/timer/add_timer_customer",
                type: "POST",
                data: new FormData($('#add_customer')[0]),
                dataType: "JSON",
                enctype: 'multipart/form-data',
                success: function (data)
                {
                    if (data.status == "success") {
                        $("#customer_listing").append('<option data-id="' + data.id + '" value="' + data.id + '" data-subtext="' + data.identifier + '" selected>' + data.name + '</option>');
                        $("#customer_listing").selectpicker("refresh");
                        $("#add_customer_modal").modal('hide');
                    }
                },
                processData: false,
                contentType: false
            });
        }
    }
    function add_item() {
        if ($("#item_name").val() == "") {
            $("#item_name").css('border', '1px solid red');
        } else {
            $.ajax({
                url: BASE_URL + "aut/timer/add_timer_item",
                type: "POST",
                data: new FormData($('#add_item')[0]),
                dataType: "JSON",
                enctype: 'multipart/form-data',
                success: function (data)
                {
                    if (data.status == "success") {
                        if ($('#' + data.optgroup).parents('html').length > 0) {
                            $('#' + data.optgroup).append('<option data-id="' + data.id + '"  value="' + data.id + '" selected>' + data.name + '</option>');
                        } else {
                            var html = '<optgroup label="' + data.optgroup.replace(/_/g, ' ') + '" id="' + data.optgroup + '">' +
                                    '<option data-id="' + data.id + '"  value="' + data.id + '" selected>' + data.name + '</option>' +
                                    '</optgroup>';
                            $("#item_listing").append(html);
                        }
                        $("#item_listing").selectpicker("refresh");
                        $("#add_item_modal").modal('hide');
                    }
                },
                processData: false,
                contentType: false
            });

        }
    }
    function set_item_value(input) {
        if ($(input).find(':selected').val() == 'add_new') {
            $("#add_item_modal").modal('show');
        }
    }
</script>