<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<div class="container-fluid">
    <div class="row">   
        <div class="main-table-div">
            <h5 class="no_mob" id="add_charges_heading">Enter Charges</h5>
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
    <div class="row mt-3">
        <div class="col-md-8 col-lg-8">
            <form id="customer_charge">
                <input type="hidden" name="form[customer_id]" id="customer_id" value="<?= $customer_id; ?>">
                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left">Customer</label>
                    <div class="col-sm-9">
                        <div class="custom-dropdown big type_select_option">
                            <select class="selectpicker" data-live-search="true"  data-width="<?= (isset($_COOKIE['compact']) && $_COOKIE['compact'] == 1 ? "50%" : "100%"); ?>" title="Choose one of the item" id="customer_listing" disabled="">
                                <?php
                                foreach ($customer_data as $value) {
                                    echo '<option ' . ($value['customer_id'] == decrypt(base64_decode($customer_id)) ? "selected" : "") . '>' . $value['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-3 text-right mob-left">Date</label>
                    <div class="col-sm-9">
                        <div id="datepicker" class="input-group date cust_width" data-date-format="mm/dd/yyyy">
                            <input class="form-control" type="text" name="form[date_charge]" id="date_charge" autocomplete="off"/>
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
                            <select onchange="set_item_value(this);" class="selectpicker" data-live-search="true"  data-width="<?= (isset($_COOKIE['compact']) && $_COOKIE['compact'] == 1 ? "50%" : "100%"); ?>" title="Choose one of the item" id="item_listing">
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
                            <input type="hidden" id="item_id" name="form[item_id]">
                            <input type="hidden" id="charge_description_1" name="form[charge_description_1]">
                        </div>
                    </div>
                </div>
                <div class="form-group row extra_field" style="display: none;">
                    <label class="col-sm-3 text-right mob-left">Charge Type</label>
                    <div class="col-sm-9">
                        <div class="custom-dropdown big type_select_option cust_width">
                            <select class="selectpicker select_charge_type" name="form[ct_id]" data-live-search="true"  data-width="<?= (isset($_COOKIE['compact']) && $_COOKIE['compact'] == 1 ? "50%" : "100%"); ?>" title="Choose one of the item" id="chargetype_listing">
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
                    <div class="col-sm-9 cust_width">
                        <input type="number" class="form-control" name="form[rate]" id="charge_rate">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left">Description</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="charge_description" rows="3" name="form[charge_description_2]"></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left">Qty / Hrs</label>
                    <div class="col-sm-9 cust_width">
                        <input type="text" class="form-control" name="form[customer_charge_qua_hrs]" autocomplete="off" id="charge_qty_hrs">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left"></label>
                    <div class="col-sm-9">
                        <button class="btn btn-submit" type="button" id="add_edit_charge_btn" onclick="add_charge();">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="view_invoice_div">
        <a href="JavaScript:void(0);" class="view_all_list" style="display: <?= (count($charges_data) >= 1 ? "" : "none") ?>">Preview Invoice</a>
    </div>
    <div class="row charges-details-main customer_charge customer_charges_icon" style="display: <?= (count($charges_data) >= 1 ? "" : "none") ?>">
        <div class="table-heading">
            <div class="edittable-cols">

            </div> 
            <div class="table-cols datetable-cols">
                <p>Date</p>
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
        <div class="customer_listing_charge">
            <?php
            if (count($charges_data) == 0) {
                ?>
                <div class="row1 blank_col_div">
                    <div class="table-col blank_col">
                        <p>No Charges Available</p>
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
            foreach ($charges_data as $charges_vaule) {
                if ($charges_vaule["ct_id"] == "1") {
                    $billable_labor[] = $charges_vaule;
                    $qty_billable = $qty_billable + $charges_vaule['quantity'];
                    $billable_total_bill = $billable_total_bill + $charges_vaule['amount'];
                }
                if ($charges_vaule["ct_id"] == "2") {
                    $service[] = $charges_vaule;
                    $qty_service = $qty_service + $charges_vaule['quantity'];
                    $service_total_bill = $service_total_bill + $charges_vaule['amount'];
                }
                if ($charges_vaule["ct_id"] == "3") {
                    $product[] = $charges_vaule;
                    $qty_product = $qty_product + $charges_vaule['quantity'];
                    $product_total_bill = $product_total_bill + $charges_vaule['amount'];
                }
                if ($charges_vaule["ct_id"] == "4") {
                    $reimbursement[] = $charges_vaule;
                    $qty_reimbursement = $qty_reimbursement + $charges_vaule['quantity'];
                    $reimbursement_total_bill = $reimbursement_total_bill + $charges_vaule['amount'];
                }
                if ($charges_vaule["ct_id"] == "5") {
                    $unbillable_labor[] = $charges_vaule;
                    $qty_unbillable_labor = $qty_unbillable_labor + $charges_vaule['quantity'];
                    $unbillable_labor_total_bill = $unbillable_labor_total_bill + $charges_vaule['amount'];
                }
                if ($charges_vaule["ct_id"] == "6") {
                    $unbillable_other[] = $charges_vaule;
                    $qty_unbillable_other = $qty_unbillable_other + $charges_vaule['quantity'];
                    $unbillable_other_total_bill = $unbillable_other_total_bill + $charges_vaule['amount'];
                }
                if ($charges_vaule["ct_id"] == "7") {
                    $download[] = $charges_vaule;
                    $qty_download = $qty_download + $charges_vaule['quantity'];
                    $download_total_bill = $download_total_bill + $charges_vaule['amount'];
                }
            }
            $total_current_chrges = $billable_total_bill + $download_total_bill + $service_total_bill + $product_total_bill + $reimbursement_total_bill + $unbillable_labor_total_bill + $unbillable_other_total_bill;
            foreach ($billable_labor as $value) {
                if ($blcount == 0) {
                    ?>
                    <div class="charge-tbl charges_header_div">
                        <div class="charges_details_div1"><label><?= $value['name']; ?></label></div>
                        <div class="charges_details_div"><p><span class="no_mob">Qty :</span> <?= number_format($qty_billable, 2); ?></p></div>
                        <div class="charges_details_div"><p style="display:none;"><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($billable_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($blcount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit text-muted" disabled=""><i class="fas fa-lock"></i></button>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="table-cols datetable-cols">
                            <p class="no_mob"><?= date("m/d/Y", strtotime($value['date_charge'])); ?></p>
                            <p class="no_desktop"><?= date("m/d/y", strtotime($value['date_charge'])); ?></p>
                        </div> 
                        <div class="dtable-cols  no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['amount'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete no_mob" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn no_mob" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>

                        </div>
                    </div>
                    <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete action-margin" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn action-margin" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
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
                        <div class="charges_details_div"><p style="display:none;"><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($service_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($scount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit text-muted" disabled=""><i class="fas fa-lock"></i></button>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="table-cols datetable-cols">
                            <p class="no_mob"><?= date("m/d/Y", strtotime($value['date_charge'])); ?></p>
                            <p class="no_desktop"><?= date("m/d/y", strtotime($value['date_charge'])); ?></p>
                        </div> 
                        <div class="dtable-cols  no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['amount'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete no_mob" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn no_mob" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete action-margin" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn action-margin" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
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
                        <div class="charges_details_div"><p style="display:none;"><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($product_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($pcount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit text-muted" disabled=""><i class="fas fa-lock"></i></button>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="table-cols datetable-cols">
                            <p class="no_mob"><?= date("m/d/Y", strtotime($value['date_charge'])); ?></p>
                            <p class="no_desktop"><?= date("m/d/y", strtotime($value['date_charge'])); ?></p>
                        </div> 
                        <div class="dtable-cols  no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['amount'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete no_mob" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn no_mob" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete action-margin" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn action-margin" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
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
                        <div class="charges_details_div"><p style="display:none;"><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($download_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($dcount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit text-muted" disabled=""><i class="fas fa-lock"></i></button>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="table-cols datetable-cols">
                            <p class="no_mob"><?= date("m/d/Y", strtotime($value['date_charge'])); ?></p>
                            <p class="no_desktop"><?= date("m/d/y", strtotime($value['date_charge'])); ?></p>
                        </div> 
                        <div class="dtable-cols  no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['amount'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete no_mob" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn no_mob" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete action-margin" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn action-margin" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
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
                        <div class="charges_details_div"><p style="display:none;"><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($reimbursement_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($rcount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit text-muted" disabled=""><i class="fas fa-lock"></i></button>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="table-cols datetable-cols">
                            <p class="no_mob"><?= date("m/d/Y", strtotime($value['date_charge'])); ?></p>
                            <p class="no_desktop"><?= date("m/d/y", strtotime($value['date_charge'])); ?></p>
                        </div> 
                        <div class="dtable-cols  no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['amount'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete no_mob" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn no_mob" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete action-margin" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn action-margin" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
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
                        <div class="charges_details_div"><p style="display:none;"><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($unbillable_labor_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($ulcount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit text-muted" disabled=""><i class="fas fa-lock"></i></button>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="table-cols datetable-cols">
                            <p class="no_mob"><?= date("m/d/Y", strtotime($value['date_charge'])); ?></p>
                            <p class="no_desktop"><?= date("m/d/y", strtotime($value['date_charge'])); ?></p>
                        </div> 
                        <div class="dtable-cols  no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['amount'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete no_mob" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn no_mob" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete action-margin" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn action-margin" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
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
                        <div class="charges_details_div"><p style="display:none;"><span class="no_mob">Paid : </span>$0.00</p></div>
                        <div class="charges_details_div"><p><span class="no_mob">Billed : </span>$<?= number_format($unbillable_other_total_bill, 2); ?></p></div>
                    </div>
                    <?php
                }
                ?>
                <div class="charge-tbl <?= ($uocount % 2 ? "bg-gray" : "bg-white1"); ?> action_click main_hover_div">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-pencil-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn charge_edit text-muted" disabled=""><i class="fas fa-lock"></i></button>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="table-cols datetable-cols">
                            <p class="no_mob"><?= date("m/d/Y", strtotime($value['date_charge'])); ?></p>
                            <p class="no_desktop"><?= date("m/d/y", strtotime($value['date_charge'])); ?></p>
                        </div> 
                        <div class="dtable-cols  no_mob">
                            <p><?= $value['description']; ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= number_format($value['quantity'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['rate'], 2); ?></p>
                        </div> 
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['amount'], 2); ?></p>
                        </div> 
                        <div class="deletetable-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete no_mob" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn no_mob" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <?php
                            if ($value['invoice_id'] == 0) {
                                ?>
                                <button type="button" class="edit_delete_btn charge_delete action-margin" data-id="<?= base64_encode(encrypt($value['charge_id'])); ?>"><i class="fas fa-trash-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_file_btn action-margin" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            }
                            ?>
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
<div class="modal fade" id="invoice_file_preview" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered invoice_preview_modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Invoice Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div style="height: 80vh;">
                    <div style="height: 80vh;" class="no_desktop">
                        <object data="" class="invoice_object_tag" frameborder="0" width="100%" height="100%" type="application/pdf">
                            <iframe class="preview_invoice_iframe" src="" frameborder="0" width="100%" height="100%"></iframe>
                        </object>
                    </div>
                    <div style="height: 80vh;" class="no_mob">
                        <iframe class="preview_invoice_iframe" src="" frameborder="0" width="100%" height="100%"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="download_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered invoice_preview_modal" role="document">
        <div class="modal-content download_details_div">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <p>Downloads can email your customer a secret URL (or other text) upon their payment of the invoice. Complete the form to implement this feature.</p>
                    <div class="form-group">
                        <label for="url_text">URL/Text</label>
                        <textarea class="form-control download_textarea" id="url_text" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="email_to">Email to</label>
                        <textarea class="form-control download_textarea" id="email_to" rows="3"></textarea>
                    </div>
                    <div class="download_textarea text-center">
                        <button type="button" class="btn btn-link" id="btn_skip_download" onclick="location.reload();">Skip</button>
                        <button type="button" class="btn btn-primary" id="btn_save_download" disabled="" onclick="add_download_charge();">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include 'preview_invoice_view.php';
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="<?php echo base_url(); ?>js/customer.js"></script>