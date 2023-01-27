<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<div class="container-fluid">
    <div class="row mb-3">   
        <div class="main-table-div">
            <div class="add_recuring_div" >
                <div><h5 class="no_mob" id="add_charges_heading">Recurring Charges</h5></div>
                <div>
                    <button type="button" class="btn btn-submit no_mob add_customer_recurring"><i class="fas fa-user-plus"></i> &nbsp;Add</button>
                    <a href="JavaScript:Void(0)" class="add_ico no_desktop add_customer_recurring"><i class="fas fa-plus-circle"></i></a>
                </div>
            </div>
            <div class="mainrow-div">
                <div class="row1 chargeclick active_row">
                    <div class="table-col search_text_div">
                        <strong class="customer_name"><?= $name; ?></strong>
                        <p><span class="customer_identifier"><?= $identifier; ?></span> <?= (($identifier == '' && $po == '') || ($identifier == '' || $po == '') ? "" : "/") ?> <span><?= $po; ?></span></p>
                    </div>
                    <div class="table-col recordhide">
                        <p class="customer_attention"><?= $address_mail_attention; ?></p>
                        <p class="customer_address" style="display: <?= ($address_mail_street == "" ? "none" : "") ?>"><?= $address_mail_street; ?><br><?= $address_mail_city; ?>, <?= $address_mail_state; ?> <?= $address_mail_zip5; ?></p>
                    </div>
                    <div class="table-col recordhide">
                        <p><?= $contact_name; ?></p>
                        <p><?php echo '<a href="mailto:' . $contact_email . '" target="_blank" class="customer_mail_phone">' . $contact_email . '</a>'; ?></p>
                        <p><?php
                            if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $contact_phone, $matches)) {
                                $phone_result = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                                echo '<a href="tel:' . $phone_result . '" target="_blank" class="customer_mail_phone">' . $phone_result . '</a>';
                            }
                            ?></p>
                    </div>
                    <div class="angle-righticon no_desktop">
                        <i class="fas fa-chevron-right icon-rotate"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row add_recuring_form" style="display: <?= (count($recuring_data) >= 1 ? "none" : "") ?>">
        <div class="col-md-8 col-lg-8">
            <form id="customer_recuring">
                <input type="hidden" name="form[customer_id]" value="<?= $customer_id; ?>">
                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left">Frequency</label>
                    <div class="col-sm-9">
                        <div class="custom-dropdown big type_select_option">
                            <select class="selectpicker" data-live-search="true"  data-width="100%" name="form[frequency]" title="Choose one of the item" id="frequency_listing">
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Annually">Annually</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-3 text-right mob-left">Next Date</label>
                    <div class="col-sm-9">
                        <div id="datepicker" class="input-group date" data-date-format="mm/dd/yyyy">
                            <input class="form-control" type="text" name="form[date_next]" id="date_next" autocomplete="off"/>
                            <span class="input-group-text input-group-addon dp"><i class="fas fa-calendar-alt"></i></span>
                            <!--  <span class="input-group-addon"></span> -->
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left">Item</label>
                    <div class="col-sm-9">
                        <div class="custom-dropdown big type_select_option">
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
                            <select onchange="set_item_value(this);" class="selectpicker" data-live-search="true"  data-width="100%" title="Choose one of the item" id="item_listing">
                                <option value="0">Other Charge</option>
                                <?php
                                if (count($billable_labor) >= 1) {
                                    echo '<optgroup label="Billable Labor">';
                                    foreach ($billable_labor as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($service) >= 1) {
                                    echo '<optgroup label="Service">';
                                    foreach ($service as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($product) >= 1) {
                                    echo '<optgroup label="Product">';
                                    foreach ($product as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($download) >= 1) {
                                    echo '<optgroup label="Download">';
                                    foreach ($download as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($reimbursement) >= 1) {
                                    echo '<optgroup label="Reimbursement">';
                                    foreach ($reimbursement as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                if (count($unbillable_labor) >= 1) {
                                    echo '<optgroup label="Unbillable Labor">';
                                    foreach ($unbillable_labor as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }if (count($unbillable_other) >= 1) {
                                    echo '<optgroup label="Unbillable Other">';
                                    foreach ($unbillable_other as $value) {
                                        echo '<option data-id="' . $value['item_id'] . '" data-rate="' . $value['rate'] . '" data-type="' . $value['type'] . '" value="' . $value['item_id'] . '">' . $value["name"] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                ?>
                            </select>
                            <input type="hidden" id="item_id" name="form[item_id]">
                        </div>
                    </div>
                </div>
                <div class="form-group row extra_field" style="display: none;">
                    <label class="col-sm-3 text-right mob-left">Charge Type</label>
                    <div class="col-sm-9">
                        <div class="custom-dropdown big type_select_option">
                            <select class="selectpicker select_charge_type" name="form[ct_id]" data-live-search="true"  data-width="100%" title="Choose one of the item" id="chargetype_listing">
                                <?php
                                foreach ($chargetype_data as $value) {
                                    echo '<option value="' . $value['ct_id'] . '">' . $value['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row extra_field" style="display: none;">
                    <label class="col-sm-3 text-right mob-left">Rate</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" name="form[rate]" id="charge_rate">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left">Description</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="charge_description" rows="3" name="form[description]"></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left">Qty / Hrs</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[quantity]" id="charge_qty_hrs">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left"></label>
                    <div class="col-sm-9">
                        <button class="btn btn-submit" type="button" id="add_edit_charge_btn" onclick="add_recuring();">Save</button>
                        <button class="btn btn-submit cancel_form" type="button">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row charges-details-main customer_recuring" style="display: <?= (count($recuring_data) >= 1 ? "" : "none") ?>">
        <div class="table-heading">
            <div class="edittable-cols">

            </div> 
            <div class="table-cols datetable-cols">
                <p>Frequency</p>
            </div>
            <div class="table-cols datetable-cols no_mob">
                <p>Next Date</p>
            </div>
            <div class="dtable-cols no_mob">
                <p>Description</p>
            </div> 
            <div class="table-cols">
                <p>Qty</p>
            </div> 
            <div class="table-cols">
                <p>Rate</p>
            </div> 
            <div class="table-cols">
                <p>Total</p>
            </div> 
            <div class="deletetable-cols">

            </div> 
        </div>
        <div class="customer_listing_recuring w-100">
            <?php
            if (count($recuring_data) == 0) {
                ?>
                <div class="row1 blank_col_div">
                    <div class="table-col blank_col">
                        <p>No Recurring Available</p>
                    </div>
                </div>

                <?php
            }
            ?>
            <?php
            $download = $billable_labor = $service = $product = $reimbursement = $unbillable_labor = $unbillable_other = array();
            $product_total_bill = $qty_product = $billable_total_bill = $qty_billable = $service_total_bill = $qty_service = $blcount = $scount = $pcount = $rcount = $ulcount = $uocount = 0;
            $unbillable_other_total_bill = $qty_unbillable_other = $unbillable_labor_total_bill = $qty_unbillable_labor = $reimbursement_total_bill = $qty_reimbursement = 0;
            $dcount = $qty_download = $download_total_bill = 0;
            foreach ($recuring_data as $recuring_value) {
                if ($recuring_value["ct_id"] == "1") {
                    $billable_labor[] = $recuring_value;
                    $qty_billable = $qty_billable + $recuring_value['quantity'];
                    $billable_total_bill = $billable_total_bill + ($recuring_value['quantity'] * $recuring_value['rate']);
                }
                if ($recuring_value["ct_id"] == "2") {
                    $service[] = $recuring_value;
                    $qty_service = $qty_service + $recuring_value['quantity'];
                    $service_total_bill = $service_total_bill + ($recuring_value['quantity'] * $recuring_value['rate']);
                }
                if ($recuring_value["ct_id"] == "3") {
                    $product[] = $recuring_value;
                    $qty_product = $qty_product + $recuring_value['quantity'];
                    $product_total_bill = $product_total_bill + ($recuring_value['quantity'] * $recuring_value['rate']);
                }
                if ($recuring_value["ct_id"] == "4") {
                    $reimbursement[] = $recuring_value;
                    $qty_reimbursement = $qty_reimbursement + $recuring_value['quantity'];
                    $reimbursement_total_bill = $reimbursement_total_bill + ($recuring_value['quantity'] * $recuring_value['rate']);
                }
                if ($recuring_value["ct_id"] == "5") {
                    $unbillable_labor[] = $recuring_value;
                    $qty_unbillable_labor = $qty_unbillable_labor + $recuring_value['quantity'];
                    $unbillable_labor_total_bill = $unbillable_labor_total_bill + ($recuring_value['quantity'] * $recuring_value['rate']);
                }
                if ($recuring_value["ct_id"] == "6") {
                    $unbillable_other[] = $recuring_value;
                    $qty_unbillable_other = $qty_unbillable_other + $recuring_value['quantity'];
                    $unbillable_other_total_bill = $unbillable_other_total_bill + ($recuring_value['quantity'] * $recuring_value['rate']);
                }
                if ($recuring_value["ct_id"] == "7") {
                    $download[] = $recuring_value;
                    $qty_download = $qty_download + $recuring_value['quantity'];
                    $download_total_bill = $download_total_bill + ($recuring_value['quantity'] * $recuring_value['rate']);
                }
            }
            foreach ($billable_labor as $value) {
                if ($blcount == 0) {
                    ?>
                    <div class="charge-tbl charges_header_div">
                        <div class="charges_details_div1"><label><?= $value['name']; ?></label></div>
                        <div class="charges_details_div"><p><span class="no_mob">Qty :</span> <?= number_format($qty_billable, 2); ?></p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($billable_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($blcount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <button type="button" class="edit_delete_btn recuring_edit" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                        </div>
                        <div class="table-cols datetable-cols">
                            <p><?= $value['frequency']; ?></p>
                        </div> 
                        <div class="table-cols datetable-cols no_mob">
                            <p><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>

                        </div> 
                        <div class="dtable-cols no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <button type="button" class="edit_delete_btn recuring_delete" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    <div style="display: flex;">
                        <p class="no_desktop mob_des"><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>

                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <button type="button" class="edit_delete_btn recuring_edit action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                            <button type="button" class="edit_delete_btn recuring_delete action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>
                <?php
                $blcount++;
            }
            foreach ($service as $value) {
                if ($scount == 0) {
                    ?>
                    <div class="charge-tbl charges_header_div">
                        <div class="charges_details_div1"><label><?= $value['name']; ?></label></div>
                        <div class="charges_details_div"><p><span class="no_mob">Qty :</span> <?= number_format($qty_service, 2); ?></p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($service_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($scount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <button type="button" class="edit_delete_btn recuring_edit" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                        </div>
                        <div class="table-cols datetable-cols">
                            <p><?= $value['frequency']; ?></p>
                        </div> 
                        <div class="table-cols datetable-cols no_mob">
                            <p><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>

                        </div> 
                        <div class="dtable-cols no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <button type="button" class="edit_delete_btn recuring_delete" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    <div style="display: flex;">
                        <p class="no_desktop mob_des"><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>

                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <button type="button" class="edit_delete_btn recuring_edit action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                            <button type="button" class="edit_delete_btn recuring_delete action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>
                <?php
                $scount++;
            }
            foreach ($product as $value) {
                if ($pcount == 0) {
                    ?>
                    <div class="charge-tbl charges_header_div">
                        <div class="charges_details_div1"><label><?= $value['name']; ?></label></div>
                        <div class="charges_details_div"><p><span class="no_mob">Qty :</span> <?= number_format($qty_product, 2); ?></p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($product_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($pcount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <button type="button" class="edit_delete_btn recuring_edit" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                        </div>
                        <div class="table-cols datetable-cols">
                            <p><?= $value['frequency']; ?></p>
                        </div> 
                        <div class="table-cols datetable-cols no_mob">
                            <p><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>

                        </div> 
                        <div class="dtable-cols no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <button type="button" class="edit_delete_btn recuring_delete" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    <div style="display: flex;">
                        <p class="no_desktop mob_des"><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>

                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <button type="button" class="edit_delete_btn recuring_edit action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                            <button type="button" class="edit_delete_btn recuring_delete action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>
                <?php
                $pcount++;
            }
            foreach ($download as $value) {
                if ($dcount == 0) {
                    ?>
                    <div class="charge-tbl charges_header_div">
                        <div class="charges_details_div1"><label><?= $value['name']; ?></label></div>
                        <div class="charges_details_div"><p><span class="no_mob">Qty :</span> <?= number_format($qty_download, 2); ?></p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($download_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($dcount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <button type="button" class="edit_delete_btn recuring_edit" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                        </div>
                        <div class="table-cols datetable-cols">
                            <p><?= $value['frequency']; ?></p>
                        </div> 
                        <div class="table-cols datetable-cols no_mob">
                            <p><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>

                        </div> 
                        <div class="dtable-cols no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <button type="button" class="edit_delete_btn recuring_delete" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    <div style="display: flex;">
                        <p class="no_desktop mob_des"><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>

                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <button type="button" class="edit_delete_btn recuring_edit action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                            <button type="button" class="edit_delete_btn recuring_delete action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>
                <?php
                $dcount++;
            }
            foreach ($reimbursement as $value) {
                if ($rcount == 0) {
                    ?>
                    <div class="charge-tbl charges_header_div">
                        <div class="charges_details_div1"><label><?= $value['name']; ?></label></div>
                        <div class="charges_details_div"><p><span class="no_mob">Qty :</span> <?= number_format($qty_reimbursement, 2); ?></p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($reimbursement_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($rcount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <button type="button" class="edit_delete_btn recuring_edit" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                        </div>
                        <div class="table-cols datetable-cols">
                            <p><?= $value['frequency']; ?></p>
                        </div> 
                        <div class="table-cols datetable-cols no_mob">
                            <p><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>

                        </div> 
                        <div class="dtable-cols no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <button type="button" class="edit_delete_btn recuring_delete" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    <div style="display: flex;">
                        <p class="no_desktop mob_des"><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>

                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <button type="button" class="edit_delete_btn recuring_edit action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                            <button type="button" class="edit_delete_btn recuring_delete action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>
                <?php
                $rcount++;
            }
            foreach ($unbillable_labor as $value) {
                if ($ulcount == 0) {
                    ?>
                    <div class="charge-tbl charges_header_div">
                        <div class="charges_details_div1"><label><?= $value['name']; ?></label></div>
                        <div class="charges_details_div"><p><span class="no_mob">Qty :</span> <?= number_format($qty_unbillable_labor, 2); ?></p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($unbillable_labor_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($ulcount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <button type="button" class="edit_delete_btn recuring_edit" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                        </div>
                        <div class="table-cols datetable-cols">
                            <p><?= $value['frequency']; ?></p>
                        </div> 
                        <div class="table-cols datetable-cols no_mob">
                            <p><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>

                        </div> 
                        <div class="dtable-cols no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <button type="button" class="edit_delete_btn recuring_delete" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    <div style="display: flex;">
                        <p class="no_desktop mob_des"><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>

                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <button type="button" class="edit_delete_btn recuring_edit action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                            <button type="button" class="edit_delete_btn recuring_delete action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>
                <?php
                $ulcount++;
            }
            foreach ($unbillable_other as $value) {
                if ($uocount == 0) {
                    ?>
                    <div class="charge-tbl charges_header_div">
                        <div class="charges_details_div1"><label><?= $value['name']; ?></label></div>
                        <div class="charges_details_div"><p><span class="no_mob">Qty :</span> <?= number_format($qty_unbillable_other, 2); ?></p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($unbillable_other_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($uocount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <button type="button" class="edit_delete_btn recuring_edit" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                        </div>
                        <div class="table-cols datetable-cols">
                            <p><?= $value['frequency']; ?></p>
                        </div> 
                        <div class="table-cols datetable-cols no_mob">
                            <p><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>

                        </div> 
                        <div class="dtable-cols no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <button type="button" class="edit_delete_btn recuring_delete" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    <div style="display: flex;">
                        <p class="no_desktop mob_des"><?= date("m/d/Y", strtotime($value['date_next'])); ?></p>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>

                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <button type="button" class="edit_delete_btn recuring_edit action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                            <button type="button" class="edit_delete_btn recuring_delete action-margin" data-id="<?= base64_encode(encrypt($value['rc_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>
                <?php
                $uocount++;
            }
            ?>
        </div>
    </div>
    <div class="btn-submitdiv pl-3 back_customer_btn">
        <button type="button" class="btn btn-adduser" id="back_customer_list">Back</button>
    </div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="<?php echo base_url(); ?>js/customer.js"></script>