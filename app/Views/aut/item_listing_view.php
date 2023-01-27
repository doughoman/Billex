<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<div class="container-fluid">

    <div class="row">   
        <input type="hidden" value="<?= (isset($_SESSION['item_search_key']) ? $_SESSION['item_search_key'] : ""); ?>" id="search_key">
        <div class="main-table-div item_listing">
            <div class="filter-main customer_filter">
                <div class="search-div listing_toggle">
                    <div class="form-group search-box">
                        <input type="text" class="form-control search_edit" id="search" placeholder="Search">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="onoffswitch no_mob">
                        <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="active_inactive" checked="">

                        <label class="onoffswitch-label" for="active_inactive">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                    <div class="onoffswitch1 no_desktop">
                        <input type="checkbox" name="onoffswitch1" class="onoffswitch1-checkbox" id="active_inactive_mob" checked>
                        <label class="onoffswitch1-label" for="active_inactive_mob"></label>
                    </div>
                </div>
                <div class="add_customer text-right add_item_link_top">
                    <a href="<?php echo base_url(); ?>aut/administration/add_edit_item" class="btn btn-submit no_mob"><i class="fas fa-user-plus"></i> &nbsp;Add</a>
                    <a href="<?php echo base_url(); ?>aut/administration/add_edit_item" class="add_ico no_desktop"><div class="add-ico"><i class="fas fa-plus"></i></div></a>
                </div>
            </div>
            <div class="no_mob">
                <div class="table-heading no_mob">
                    <div class="table-col">
                        <p>Name</p><p>Description</p>
                    </div>

                    <div class="table-col text-right">
                        <p>Rate</p>
                    </div>
                    <div class="table-col text-right">
                        <p>Can Discount</p>
                    </div>
                    <div class="table-col text-right">
                    </div>
                </div>
                <div class="mainrow-div item_listing">
                    <?php
                    $scount = 0;
                    $blcount = 0;
                    $pcount = 0;
                    $rcount = 0;
                    $ulcount = 0;
                    $uocount = 0;
                    $down = 0;
                    $billable_labor = array();
                    $service = array();
                    $product = array();
                    $reimbursement = array();
                    $unbillable_labor = array();
                    $unbillable_other = array();
                    $download = array();
                    foreach ($item_data as $key => $item_value) {
                        if ($item_value["type"] == "Billable Labor") {
                            $billable_labor[] = $item_value;
                        }
                        if ($item_value["type"] == "Service") {
                            $service[] = $item_value;
                        }
                        if ($item_value["type"] == "Product") {
                            $product[] = $item_value;
                        }
                        if ($item_value["type"] == "Reimbursement") {
                            $reimbursement[] = $item_value;
                        }
                        if ($item_value["type"] == "Unbillable Labor") {
                            $unbillable_labor[] = $item_value;
                        }
                        if ($item_value["type"] == "Unbillable Other") {
                            $unbillable_other[] = $item_value;
                        }
                        if ($item_value["type"] == "Download") {
                            $download[] = $item_value;
                        }
                    }if (count($item_data) == 0) {
                        ?>
                        <div class="row1 blank_col_div">
                            <div class="table-col blank_col">
                                <p>No Items Available</p>
                            </div>
                        </div>

                        <?php
                    }

                    foreach ($billable_labor as $value) {
                        echo ($blcount == 0 ? '<div class="item_list_1"><h4 class="header_text">Billable Labor</h4>' : "");
                        ?>
                        <div class="row1 colpsclick user_listing_main <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($blcount % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div">
                                <strong><?= $value["name"]; ?></strong>
                                <p><?= $value["description"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= '$' . $value["rate"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></p>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                                <!--<button type="button" class=" more-menu-btn"><i class="fas fa-edit action_icon"></i> Edit</button>-->
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $blcount++;
                        echo (count($billable_labor) == $blcount ? "</div>" : "");
                    }
                    foreach ($service as $value) {
                        echo ($scount == 0 ? "<div class='item_list_2'><h4 class='header_text'>Service</h4>" : "");
                        ?>
                        <div class="row1 colpsclick user_listing_main <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($scount % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div">
                                <strong><?= $value["name"]; ?></strong>
                                <p><?= $value["description"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= '$' . $value["rate"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></p>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                                <!--<button type="button" class=" more-menu-btn"><i class="fas fa-edit action_icon"></i> Edit</button>-->
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $scount++;
                        echo (count($service) == $scount ? "</div>" : "");
                    }
                    foreach ($product as $value) {
                        echo ($pcount == 0 ? "<div class='item_list_3'><h4 class='header_text'>Product</h4>" : "");
                        ?>
                        <div class="row1 colpsclick user_listing_main <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($pcount % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div">
                                <strong><?= $value["name"]; ?></strong>
                                <p><?= $value["description"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= '$' . $value["rate"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></p>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                                <!--<button type="button" class=" more-menu-btn"><i class="fas fa-edit action_icon"></i> Edit</button>-->
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $pcount++;
                        echo (count($product) == $pcount ? "</div>" : "");
                    }
                    foreach ($download as $value) {
                        echo ($down == 0 ? "<div class='item_list_3'><h4 class='header_text'>Download</h4>" : "");
                        ?>
                        <div class="row1 colpsclick user_listing_main <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($down % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div">
                                <strong><?= $value["name"]; ?></strong>
                                <p><?= $value["description"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= '$' . $value["rate"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></p>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                                <!--<button type="button" class=" more-menu-btn"><i class="fas fa-edit action_icon"></i> Edit</button>-->
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $down++;
                        echo (count($download) == $down ? "</div>" : "");
                    }
                    foreach ($reimbursement as $value) {
                        echo ($rcount == 0 ? "<div class='item_list_4'><h4 class='header_text'>Reimbursement</h4>" : "");
                        ?>
                        <div class="row1 colpsclick user_listing_main <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($rcount % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div">
                                <strong><?= $value["name"]; ?></strong>
                                <p><?= $value["description"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= '$' . $value["rate"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></p>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                                <!--<button type="button" class=" more-menu-btn"><i class="fas fa-edit action_icon"></i> Edit</button>-->
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $rcount++;
                        echo (count($reimbursement) == $rcount ? "</div>" : "");
                    }
                    foreach ($unbillable_labor as $value) {
                        echo ($ulcount == 0 ? "<div class='item_list_5'><h4 class='header_text'>Unbillable Labor</h4>" : "");
                        ?>
                        <div class="row1 colpsclick user_listing_main <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($ulcount % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div">
                                <strong><?= $value["name"]; ?></strong>
                                <p><?= $value["description"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= '$' . $value["rate"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></p>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                                <!--<button type="button" class=" more-menu-btn"><i class="fas fa-edit action_icon"></i> Edit</button>-->
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $ulcount++;
                        echo (count($unbillable_labor) == $ulcount ? "</div>" : "");
                    }
                    foreach ($unbillable_other as $value) {
                        echo ($uocount == 0 ? "<div class='item_list_6'><h4 class='header_text'>Unbillable Other</h4>" : "");
                        ?>
                        <div class="row1 colpsclick user_listing_main <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($uocount % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div">
                                <strong><?= $value["name"]; ?></strong>
                                <p><?= $value["description"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= '$' . $value["rate"]; ?></p>
                            </div>
                            <div class="table-col recordhide text-right">
                                <p><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></p>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                                <!--<button type="button" class=" more-menu-btn"><i class="fas fa-edit action_icon"></i> Edit</button>-->
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $uocount++;
                        echo (count($unbillable_other) == $uocount ? "</div>" : "");
                    }
                    ?>
                </div>
                <div class="text-right add_item_link_bottom" style="display: <?= ($key > 2 ? "" : "none"); ?>">
                    <a href="<?php echo base_url(); ?>aut/administration/add_edit_item" class="btn btn-submit"><i class="fas fa-user-plus"></i> &nbsp;Add</a>
                </div>
            </div>

            <div class="no_desktop">
                <div class="mainrow-div item_listing_view">
                    <?php
                    $mscount = 0;
                    $mblcount = 0;
                    $mpcount = 0;
                    $mrcount = 0;
                    $mulcount = 0;
                    $muocount = 0;
                    $down = 0;
                    foreach ($billable_labor as $value) {
                        echo ($mblcount == 0 ? "<div class='item_list_1'><h4 class='header_text'>Billable Labor</h4>" : "");
                        ?>
                        <div class="row1 user_listing_main in_mobile_listing_view <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($mblcount % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div item_mobile_listing">
                                <div>
                                    <strong><?= $value["name"]; ?></strong>
                                    <p><?= (strlen($value["description"]) > 21 ? substr($value["description"], 0, 21) . '...' : $value["description"]); ?></p>
                                </div>
                                <div>
                                    <span><?= '$' . $value["rate"]; ?></span>
                                    <span><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></span>
                                </div>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $mblcount++;
                        echo (count($billable_labor) == $mblcount ? "</div>" : "");
                    }
                    foreach ($service as $value) {
                        echo ($mscount == 0 ? "<div class='item_list_2'><h4 class='header_text'>Service</h4>" : "");
                        ?>
                        <div class="row1 user_listing_main in_mobile_listing_view <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($mscount % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div item_mobile_listing">
                                <div>
                                    <strong><?= $value["name"]; ?></strong>
                                    <p><?= (strlen($value["description"]) > 21 ? substr($value["description"], 0, 21) . '...' : $value["description"]); ?></p>
                                </div>
                                <div>
                                    <span><?= '$' . $value["rate"]; ?></span>
                                    <span><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></span>
                                </div>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $mscount++;
                        echo (count($service) == $mscount ? "</div>" : "");
                    }
                    foreach ($product as $value) {
                        echo ($mpcount == 0 ? "<div class='item_list_3'><h4 class='header_text'>Product</h4>" : "");
                        ?>
                        <div class="row1 user_listing_main in_mobile_listing_view <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($mpcount % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div item_mobile_listing">
                                <div>
                                    <strong><?= $value["name"]; ?></strong>
                                    <p><?= (strlen($value["description"]) > 21 ? substr($value["description"], 0, 21) . '...' : $value["description"]); ?></p>
                                </div>
                                <div>
                                    <span><?= '$' . $value["rate"]; ?></span>
                                    <span><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></span>
                                </div>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $mpcount++;
                        echo (count($product) == $mpcount ? "</div>" : "");
                    }
                    foreach ($download as $value) {
                        echo ($down == 0 ? "<div class='item_list_3'><h4 class='header_text'>Download</h4>" : "");
                        ?>
                        <div class="row1 user_listing_main in_mobile_listing_view <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($down % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div item_mobile_listing">
                                <div>
                                    <strong><?= $value["name"]; ?></strong>
                                    <p><?= (strlen($value["description"]) > 21 ? substr($value["description"], 0, 21) . '...' : $value["description"]); ?></p>
                                </div>
                                <div>
                                    <span><?= '$' . $value["rate"]; ?></span>
                                    <span><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></span>
                                </div>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $down++;
                        echo (count($download) == $down ? "</div>" : "");
                    }
                    foreach ($reimbursement as $value) {
                        echo ($mrcount == 0 ? "<div class='item_list_4'><h4 class='header_text'>Reimbursement</h4>" : "");
                        ?>
                        <div class="row1 user_listing_main in_mobile_listing_view <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($mrcount % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div item_mobile_listing">
                                <div>
                                    <strong><?= $value["name"]; ?></strong>
                                    <p><?= (strlen($value["description"]) > 21 ? substr($value["description"], 0, 21) . '...' : $value["description"]); ?></p>
                                </div>
                                <div>
                                    <span><?= '$' . $value["rate"]; ?></span>
                                    <span><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></span>
                                </div>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $mrcount++;
                        echo (count($reimbursement) == $mrcount ? "</div>" : "");
                    }
                    foreach ($unbillable_labor as $value) {
                        echo ($mulcount == 0 ? "<div class='item_list_5'><h4 class='header_text'>Unbillable Labor</h4>" : "");
                        ?>
                        <div class="row1 user_listing_main in_mobile_listing_view <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($mulcount % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div item_mobile_listing">
                                <div>
                                    <strong><?= $value["name"]; ?></strong>
                                    <p><?= (strlen($value["description"]) > 21 ? substr($value["description"], 0, 21) . '...' : $value["description"]); ?></p>
                                </div>
                                <div>
                                    <span><?= '$' . $value["rate"]; ?></span>
                                    <span><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></span>
                                </div>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $mulcount++;
                        echo (count($unbillable_labor) == $mulcount ? "</div>" : "");
                    }
                    foreach ($unbillable_other as $value) {
                        echo ($muocount == 0 ? "<div class='item_list_6'><h4 class='header_text'>Unbillable Other</h4>" : "");
                        ?>
                        <div class="row1 user_listing_main in_mobile_listing_view <?php echo ($value["status"] != "active" ? "font_gray inactive" : "active") ?> <?= ($muocount % 2 ? "bg-gray" : ""); ?>" style="display: <?php echo ($value["status"] != "active" ? "none" : "") ?>">
                            <div class="table-col search_text_div item_mobile_listing">
                                <div>
                                    <strong><?= $value["name"]; ?></strong>
                                    <p><?= (strlen($value["description"]) > 21 ? substr($value["description"], 0, 21) . '...' : $value["description"]); ?></p>
                                </div>
                                <div>
                                    <span><?= '$' . $value["rate"]; ?></span>
                                    <span><?= ($value["can_discount"] == 1 ? "Y" : "N"); ?></span>
                                </div>
                            </div>
                            <div class="angle-righticon">
                                <div class="more">
                                    <button class="more-btn">
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                        <span class="more-dot"></span>
                                    </button>
                                    <div class="more-menu">
                                        <ul class="more-menu-items" tabindex="-1" role="menu" aria-labelledby="more-btn" aria-hidden="true">
                                            <li class="more-menu-item" role="presentation">
                                                <button type="button" class=" more-menu-btn" role="menuitem"><i class="fas fa-book action_icon"></i> Report</button>
                                            </li>
                                            <li class="more-menu-item" role="presentation">
                                                <a class=" more-menu-btn" href="<?php echo base_url(); ?>aut/administration/add_edit_item/<?php echo $value["item_id"]; ?>"><i class="fas fa-edit action_icon"></i> Edit</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $muocount++;
                        echo (count($unbillable_other) == $muocount ? "</div>" : "");
                    }
                    ?>
                </div>
            </div>
        </div>
    </div> 
</div>

<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="<?php echo base_url(); ?>js/administration.js"></script>