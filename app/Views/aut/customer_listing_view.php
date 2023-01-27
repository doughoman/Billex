<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<style>
    @media only screen and (min-width: 320px) and (max-width: 575px) {
        .admin-contain-main-div {
            padding: 15px;
            background-color: #f4f5f7;
        } 
    }
    @media only screen and (min-width: 576px) and (max-width: 767px) {
        .admin-contain-main-div {
            padding: 15px;
            background-color: #f4f5f7;
        }
    }

</style>

<div class="container-fluid">

    <div class="row">   
        <div class="main-table-div customer_filter_main">
            <div class="filter-main customer_filter">
                <div class="search-div listing_toggle">
                    <div class="customer_filter_text">
                        <div class="form-group search-box">
                            <input type="hidden" value="<?= (isset($_SESSION['search_key']) ? $_SESSION['search_key'] : ""); ?>" id="search_key">
                            <input type="text" class="form-control search_edit" id="search" placeholder="Search">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="onoffswitch1 no_desktop" style="left: 0;">
                            <input type="checkbox" name="onoffswitch1" class="onoffswitch1-checkbox" id="active_inactive_mob" checked>
                            <label class="onoffswitch1-label" for="active_inactive_mob"></label>
                        </div>
                    </div>
                    <div class="onoffswitch no_mob">
                        <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="active_inactive" checked="">
                        <label class="onoffswitch-label" for="active_inactive">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <div class="add_customer text-right add_item_link_top">
                    <a href="<?php echo base_url(); ?>aut/customer/add_edit_customer" class="btn btn-submit no_mob"><i class="fas fa-user-plus"></i> &nbsp;Add</a>
                    <a href="<?php echo base_url(); ?>aut/customer/add_edit_customer" class="add_ico no_desktop"><div class="add-ico"><i class="fas fa-plus"></i></div></a>
                </div>
            </div>
            <div class="table-heading no_mob">
                <div class="table-col">
                    <p>Name</p><p>ID / PO</p>
                </div>
                <div class="table-col">
                    <p>Attr</p>
                    <p>Address</p>
                </div>
                <div class="table-col contact-col">
                    <p>Contact</p>
                </div>
                <div class="table-col">
                </div>
            </div>

            <input type="hidden" id="customerId" value="<?= (isset($_SESSION['customerId']) ? $_SESSION['customerId'] : ""); ?>">
            <div class="mainrow-div customer_listing_main">
                <?php
                $count = 0;
                $cadd = 0;
                foreach ($customer_data as $customer_value) {
                    if ($customer_value['status'] == 'active') {
                        ?>
                        <div class="user_listing_main active" style="display: <?php echo ($customer_value["status"] != "active" ? "none" : "") ?>">
                            <div class="row1 colpsclick customer_action_<?= decrypt(base64_decode($customer_value["customer_id"])); ?>">
                                <div class="table-col search_text_div">
                                    <strong><span data-toggle="tooltip" data-html="true" data-placement="top" title="<?= $customer_value["address_mail_attention"]; ?><?= ($customer_value["address_mail_street"] != '' ? "<br>" . $customer_value["address_mail_street"] . '<br>' . $customer_value["address_mail_city"] . ',' . $customer_value["address_mail_state"] . ' ' . $customer_value["address_mail_zip5"] : ""); ?>"><?= $customer_value["name"]; ?></span></strong>
                                    <p><span><?= ($customer_value["identifier"] == "" ? "&nbsp;" : $customer_value["identifier"]); ?></span> <?= (($customer_value["identifier"] == '' && $customer_value["po"] == '') || ($customer_value["identifier"] == '' || $customer_value["po"] == '') ? "" : "/") ?> <span><?= $customer_value["po"]; ?></span></p>
                                </div>
                                <div class="table-col recordhide">
                                    <p><?= $customer_value["address_mail_attention"]; ?></p>
                                    <p style="display: <?= ($customer_value["address_mail_street"] == "" ? "none" : "") ?>"><?= $customer_value["address_mail_street"]; ?><br><?= $customer_value["address_mail_city"]; ?>, <?= $customer_value["address_mail_state"]; ?> <?= $customer_value["address_mail_zip5"]; ?></p>
                                </div>
                                <div class="table-col recordhide">
                                    <p><?= $customer_value["contact_name"]; ?></p>
                                    <p><?php echo '<a href="mailto:' . $customer_value["contact_email"] . '" target="_blank" class="customer_mail_phone">' . $customer_value["contact_email"] . '</a>'; ?></p>
                                    <p><?php
                                        if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $customer_value["contact_phone"], $matches)) {
                                            $phone_result = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                                            echo '<a href="tel:' . $phone_result . '" target="_blank" class="customer_mail_phone">' . $phone_result . '</a>';
                                        }
                                        ?></p>
                                </div>
                                <div class="angle-righticon-list">
                                    <div class="more">
                                        <button class="more-btn" data-id="<?php echo $customer_value["customer_id"]; ?>">
                                            <span class="more-dot"></span>
                                            <span class="more-dot"></span>
                                            <span class="more-dot"></span>
                                        </button>

                                        <div class="more-menu">
                                            <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                                <li class="more-menu-item" role="presentation">
                                                    <button type="button" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/charges/<?php echo $customer_value["customer_id"]; ?>" class="more-menu-btn customer_action_button" role="menuitem"><i class="fas fa-list action_icon"></i> Charges</button>
                                                </li>
                                                <li class="more-menu-item" role="presentation">
                                                    <button type="button" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/recuring/<?php echo $customer_value["customer_id"]; ?>" class="more-menu-btn customer_action_button" role="menuitem"><i class="fas fa-sync-alt action_icon"></i> Recuring</button>
                                                </li>
                                                <li class="more-menu-item" role="presentation">
                                                    <button type="button" class="more-menu-btn customer_action_button" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/register/<?php echo $customer_value["customer_id"]; ?>" role="menuitem"><i class="fas fa-book action_icon"></i> Register</button>
                                                </li>
                                                <li class="more-menu-item" role="presentation">
                                                    <button type="button" class="more-menu-btn customer_action_button" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/rates/<?php echo $customer_value["customer_id"]; ?>" role="menuitem"><i class="fas fa-tag action_icon"></i> Rates</button>
                                                </li>
                                                <li class="more-menu-item" role="presentation">
                                                    <button type="button" class="more-menu-btn customer_action_button" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/add_edit_customer/<?php echo $customer_value["customer_id"]; ?>" role="menuitem"><i class="fas fa-pencil-alt action_icon"></i> Edit</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="actions-icon no_mob">
                                    <div class="customer_action_icon">
                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Charges" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/charges/<?php echo $customer_value["customer_id"]; ?>" class="more-menu-btn customer_action_button1" role="menuitem"><i class="fas fa-list"></i></button>
                                    </div>
                                    <div class="customer_action_icon">
                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Recuring" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/recuring/<?php echo $customer_value["customer_id"]; ?>" class="more-menu-btn customer_action_button1" role="menuitem"><i class="fas fa-sync-alt"></i></button>
                                    </div>
                                    <div class="customer_action_icon">
                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Register" class="more-menu-btn customer_action_button1" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/register/<?php echo $customer_value["customer_id"]; ?>" role="menuitem"><i class="fas fa-book"></i></button>
                                    </div>
                                    <div class="customer_action_icon">
                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Rates" class="more-menu-btn customer_action_button1" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/rates/<?php echo $customer_value["customer_id"]; ?>" role="menuitem"><i class="fas fa-tag"></i></button>
                                    </div>
                                    <div class="customer_action_icon">
                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Edit" class="more-menu-btn customer_action_button1" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/add_edit_customer/<?php echo $customer_value["customer_id"]; ?>" role="menuitem"><i class="fas fa-pencil-alt"></i></button>
                                    </div>
                                </div> 
                            </div>
                            <div class="no_desktop customer_action_mob">
                                <div class="actions-icon">
                                    <div class="customer_action_icon">
                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Charges" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/charges/<?php echo $customer_value["customer_id"]; ?>" class="more-menu-btn customer_action_button1" role="menuitem"><i class="fas fa-list"></i></button>
                                    </div>
                                    <div class="customer_action_icon">
                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Recuring" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/recuring/<?php echo $customer_value["customer_id"]; ?>" class="more-menu-btn customer_action_button1" role="menuitem"><i class="fas fa-sync-alt"></i></button>
                                    </div>
                                    <div class="customer_action_icon">
                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Register" class="more-menu-btn customer_action_button1" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/register/<?php echo $customer_value["customer_id"]; ?>" role="menuitem"><i class="fas fa-book"></i></button>
                                    </div>
                                    <div class="customer_action_icon">
                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Rates" class="more-menu-btn customer_action_button1" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/rates/<?php echo $customer_value["customer_id"]; ?>" role="menuitem"><i class="fas fa-tag"></i></button>
                                    </div>
                                    <div class="customer_action_icon">
                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Edit" class="more-menu-btn customer_action_button1" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/add_edit_customer/<?php echo $customer_value["customer_id"]; ?>" role="menuitem"><i class="fas fa-pencil-alt"></i></button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <?php
                        $cadd++;
                    }
                    $count++;
                }
                $count = 0;
                foreach ($customer_data as $customer_value) {
                    if ($customer_value['status'] == 'inactive') {
                        ?>
                        <div class="inactive user_listing_main font_gray" style="display: <?php echo ($customer_value["status"] != "active" ? "none" : "") ?>">
                            <div class="row1 colpsclick customer_action_<?= decrypt(base64_decode($customer_value["customer_id"])); ?>">
                                <div class="table-col search_text_div">
                                    <strong><span data-toggle="tooltip" data-html="true" data-placement="top" title="<?= $customer_value["address_mail_attention"]; ?><?= ($customer_value["address_mail_street"] != '' ? "<br>" . $customer_value["address_mail_street"] . '<br>' . $customer_value["address_mail_city"] . ',' . $customer_value["address_mail_state"] . ' ' . $customer_value["address_mail_zip5"] : ""); ?>"><?= $customer_value["name"]; ?></span></strong>
                                    <p><span><?= ($customer_value["identifier"] == "" ? "&nbsp;" : $customer_value["identifier"]); ?></span> <?= (($customer_value["identifier"] == '' && $customer_value["po"] == '') || ($customer_value["identifier"] == '' || $customer_value["po"] == '') ? "" : "/") ?> <span><?= $customer_value["po"]; ?></span></p>
                                </div>
                                <div class="table-col recordhide">
                                    <p><?= $customer_value["address_mail_attention"]; ?></p>
                                    <p style="display: <?= ($customer_value["address_mail_street"] == "" ? "none" : "") ?>"><?= $customer_value["address_mail_street"]; ?><br><?= $customer_value["address_mail_city"]; ?>, <?= $customer_value["address_mail_state"]; ?> <?= $customer_value["address_mail_zip5"]; ?></p>
                                </div>
                                <div class="table-col recordhide">
                                    <p><?= $customer_value["contact_name"]; ?></p>
                                    <p><?php echo '<a href="mailto:' . $customer_value["contact_email"] . '" target="_blank" class="customer_mail_phone">' . $customer_value["contact_email"] . '</a>'; ?></p>
                                    <p><?php
                                        if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $customer_value["contact_phone"], $matches)) {
                                            $phone_result = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                                            echo '<a href="tel:' . $phone_result . '" target="_blank" class="customer_mail_phone">' . $phone_result . '</a>';
                                        }
                                        ?></p>
                                </div>
                                <div class="angle-righticon-list">
                                    <div class="more">
                                        <button class="more-btn">
                                            <span class="more-dot"></span>
                                            <span class="more-dot"></span>
                                            <span class="more-dot"></span>
                                        </button>
                                        <div class="more-menu">
                                            <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                                <li class="more-menu-item" role="presentation">
                                                    <button type="button" data-id="<?php echo $customer_value["customer_id"]; ?>" class="more-menu-btn customer_delete delete_customer" role="menuitem"><i class="fas fa-trash-alt action_icon"></i> Delete</button>
                                                </li>
                                                <li class="more-menu-item" role="presentation">
                                                    <button type="button" class="more-menu-btn customer_action_button" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/add_edit_customer/<?php echo $customer_value["customer_id"]; ?>" role="menuitem"><i class="fas fa-pencil-alt action_icon"></i> Edit</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="actions-icon">
                                    <div class="customer_action_icon">
                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Delete" data-id="<?php echo $customer_value["customer_id"]; ?>" class="more-menu-btn customer_delete delete_customer" role="menuitem"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                    <div class="customer_action_icon">
                                        <button type="button" data-toggle="tooltip" data-placement="top" title="Edit" class="more-menu-btn customer_action_button1" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/add_edit_customer/<?php echo $customer_value["customer_id"]; ?>" role="menuitem"><i class="fas fa-pencil-alt"></i></button>
                                    </div>
                                </div> 
                            </div>
                            <div class="colleps-div" style="display: none;">
                                <div class="icons">
                                    <div class="w-100" role='group'>
                                        <a href="javascript:void(0)" class="btn btn-default customer_delete delete_customer" data-id="<?= $customer_value["customer_id"]; ?>"><i class="fas fa-trash-alt"></i><p>Delete</p></a>
                                        <a href="javascript:void(0);" data-id="<?php echo $customer_value["customer_id"]; ?>" data-url="<?php echo base_url(); ?>aut/customer/add_edit_customer/<?php echo $customer_value["customer_id"]; ?>" class="btn btn-default customer_action_button"><i class="fas fa-pencil-alt"></i> <p>Edit</p></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                if (count($customer_data) == 0) {
                    ?>
                    <div class="row1 blank_col_div">
                        <div class="table-col blank_col">
                            <p>No Customers Available</p>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="text-right mt-3 no_mob add_item_link_bottom" style="display: <?= ($cadd > 3 ? "" : "none"); ?>">
                <a href="<?php echo base_url(); ?>aut/customer/add_edit_customer" class="btn btn-submit"><i class="fas fa-user-plus"></i> &nbsp;Add</a>
            </div>
        </div>
    </div>
</div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="<?php echo base_url(); ?>js/customer.js"></script>