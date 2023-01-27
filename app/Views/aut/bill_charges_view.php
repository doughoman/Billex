<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<style>
    @media only screen and (min-width: 320px) and (max-width: 575px) {
        .admin-contain-main-div {
            padding: 20px;
            background-color: #f4f5f7;
        }
    }
    @media only screen and (min-width: 576px) and (max-width: 767px) {
        .admin-contain-main-div {
            padding: 20px;
            background-color: #f4f5f7;
        }
    }
</style>
<div class="container-fluid">
    <div class="bill_charges_header">
        <div class="date_picker_div">
            <label>Include Charges Through</label>
            <div id="datepicker" class="input-group date cust_width" data-date-format="mm/dd/yyyy">
                <input type="hidden" id="selected_date" value="<?= (isset($_SESSION['bill_date']) ? $_SESSION['bill_date'] : ""); ?>">
                <input class="form-control" type="text" name="form[date_charge]" id="date_charge" autocomplete="off" value="<?= date('m/d/Y', strtotime("-1 days")); ?>"/>
                <span class="input-group-text input-group-addon dp"><i class="fas fa-calendar-alt"></i></span>
            </div>
        </div>
        <div class="preview_process_div">
            <button type="button" class="btn btn-submit prevoius_batches">Previous Batches</button>
            <button type="button" class="btn btn-submit batches_cancel" style="display: none;">Cancel</button>
        </div>
    </div>
    <div class="row charges-details-main">
        <div class="table-heading">
            <div class="charge-edittable-cols">
                <div class="custom-control custom-checkbox" style="cursor: pointer;">
                    <input type="checkbox" class="custom-control-input" id="customCheck_all" style="cursor: pointer;">
                    <label class="custom-control-label" for="customCheck_all" style="cursor: pointer;"></label>
                </div>
            </div>
            <div class="table-cols bill_charges_name">
                <p>Name</p>
                <p class="charges_id_po">ID / PO</p>
            </div> 
            <div class="table-cols bill_charges_ap no_mob no_ipad">
                <p>ID / PO</p>
            </div> 
            <div class="table-cols text-right charges_number_details">
                <p>Unbilled</p>
                <p class="charges_id_po">Balance</p>
            </div> 
            <div class="table-cols text-right no_mob bill_charges_count no_ipad">
                <p>Charge Count</p>
            </div> 
            <div class="table-cols text-right no_mob charges_number_details no_ipad">
                <p>Balance</p>
            </div> 
        </div>


        <div class="customer_listing_charge">
            <?php
            if (count($bill_charges) == 0) {
                ?>
                <div class="row1 blank_col_div">
                    <div class="table-col blank_col">
                        <p>No Bill Charges Available</p>
                    </div>
                </div>

                <?php
            }
            $blcount = 0;
            $unbill_charge = $charge_count = $current_balance = 0;
            foreach ($bill_charges as $value) {
                $unbill_charge = $unbill_charge + floatval($value['total']);
                $charge_count = $charge_count + $value['count'];
                $current_balance = $current_balance + floatval($value['balance']);
                ?>
                <div class="charge-tbl <?= ($blcount % 2 ? "bg-gray" : "bg-white1"); ?>">
                    <div class="results-data ">
                        <div class="charge-edittable-cols bill_charges_preview_div">
                            <div class="custom-control custom-checkbox" style="cursor: pointer;">
                                <input type="checkbox" class="custom-control-input deposit_select_checkbox" data-email="<?= $value['email_to_list']; ?>" data-count="<?= $value['count']; ?>" data-id="<?= base64_encode(encrypt($value['customer_id'])); ?>" id="customCheck<?= $blcount; ?>" style="cursor: pointer;">
                                <label class="custom-control-label" for="customCheck<?= $blcount; ?>" style="cursor: pointer;"></label>
                            </div>
                            <div>
                                <button type="button" class="edit_delete_btn preview_file_btn" data-custid="<?= base64_encode(encrypt($value['customer_id'])); ?>"><i class="far fa-file-alt"></i></button>
                                <button type="button" class="edit_delete_btn" data-toggle="tooltip" data-placement="top" title="<?= $value['email_to_list']; ?>" style="color: #327052;display:<?= ($value['email_to_list'] != "" ? "" : "none"); ?>"><i class="fas fa-at"></i></button>                            
                            </div>
                        </div>
                        <div class="table-cols bill_charges_name">
                            <a href="<?= base_url() ?>aut/customer/add_edit_customer/<?= base64_encode(encrypt($value['customer_id'])) ?>?bill=1" class="credit_edit1">
                                <p><?php echo($value['status'] == "active" ? "" : '<i class="fas fa-user-slash" data-toggle="tooltip" data-placement="top" title="Inactive"></i>&nbsp;'); ?><span data-toggle="tooltip" data-html="true" data-placement="top" title="<?= $value["address_mail_attention"]; ?><br><?= $value["address_mail_street"]; ?><br><?= $value["address_mail_city"]; ?>, <?= $value["address_mail_state"]; ?> <?= $value["address_mail_zip5"]; ?>"><?= $value['name']; ?></span></p></a>
                            <p class="charges_id_po"><?= ($value["identifier"] == "" ? "&nbsp;" : $value["identifier"]); ?></span> <?= (($value["identifier"] == '' && $value["po"] == '') || ($value["identifier"] == '' || $value["po"] == '') ? "" : "/") ?> <span><?= $value["po"]; ?></span></p>
                        </div> 
                        <div class="table-cols bill_charges_ap no_mob no_ipad">
                            <p><?= ($value["identifier"] == "" ? "&nbsp;" : $value["identifier"]); ?></span> <?= (($value["identifier"] == '' && $value["po"] == '') || ($value["identifier"] == '' || $value["po"] == '') ? "" : "/") ?> <span><?= $value["po"]; ?></span></p>
                        </div> 
                        <div class="table-cols text-right charges_number_details">
                            <?php
                            if ($value['status'] == 'active') {
                                ?>
                                <a href="JavaScript:void(0);" class="edit_delete_btn credit_edit unbill_charges_edit" data-custid="<?= base64_encode(encrypt($value['customer_id'])); ?>"><p><?= '$' . number_format($value['total'], 2); ?></p></a>
                                <?php
                            } else {
                                ?>
                                <p><?= '$' . number_format($value['total'], 2); ?></p>
                                <?php
                            }
                            ?>
                            <p class="charges_id_po"><?= '$' . number_format($value['balance'], 2); ?></p>
                        </div> 
                        <div class="table-cols text-right no_mob bill_charges_count no_ipad">
                            <p><?= $value['count']; ?></p>
                        </div> 
                        <div class="table-cols text-right no_mob charges_number_details no_ipad">
                            <p><?= '$' . number_format($value['balance'], 2); ?></p>
                        </div> 
                    </div>
                </div>
                <?php
                $blcount++;
            }
            ?>
        </div>
        <div class="charge_total" style="display: <?= (count($bill_charges) == 0 ? "none" : ""); ?>">
            <div class="charge-tbl charges_header_div">
                <div class="charge-edittable-cols no_mob">
                </div>
                <div class="table-cols bill_charges_name no_mob">
                    <label style="display:none;"><span class="total_selected">0</span> Selected</label>
                </div>
                <div class="table-cols bill_charges_ap">
                    <label>Total</label>
                </div>
                <div class="table-cols text-right charges_number_details total_balance_bill">
                    <label><?= '$' . number_format($unbill_charge, 2); ?></label>
                    <label class="charges_id_po"><?= '$' . number_format($current_balance, 2); ?></label>
                </div>
                <div class="table-cols text-right no_mob bill_charges_count no_ipad">
                    <label><?= $charge_count; ?></label>
                </div>
                <div class="table-cols text-right no_mob charges_number_details no_ipad">
                    <label><?= '$' . number_format($current_balance, 2); ?></label>
                </div>
            </div>
        </div>
    </div>
    <div class="action_buttons mt-2" style="width: 100%;display: <?= (count($bill_charges) == 0 ? "none" : ""); ?>">
        <button type="button" class="btn btn-submit customer_invoice_preview" disabled="">Preview Selected<i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>
        <button type="button" class="btn btn-submit" id="process_selected" data-toggle="modal" data-target="#invoice_process" disabled="">Process Selected<i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>
    </div>
    <div class="row deposit-details-main" style="display: none;">
        <div class="table-heading">
            <div class="table-cols">
                <p>Date</p>
            </div> 
            <div class="table-cols">
                <p>User</p>
            </div> 
            <div class="table-cols text-right">
                <p>Count</p>
            </div>
            <div class="deletetable-cols">
            </div>
        </div>
        <?php
        $blcount = 0;
        foreach ($batch_data as $value) {
            ?>
            <div class="charge-tbl <?= ($blcount % 2 ? "bg-gray" : "bg-white1"); ?> action_deposit" style="cursor: pointer;">
                <div class="results-data">
                    <div class="table-cols">
                        <p><?= date("m/d/Y", strtotime($value['time_created'])); ?></p>
                    </div> 
                    <div class="table-cols">
                        <p><?= $value['name_display']; ?></p>
                    </div> 
                    <div class="table-cols text-right">
                        <p><?= $value['invoice_count']; ?></p>
                    </div>
                    <div class="table-cols text-right">
                        <button type="button" class="edit_delete_btn invoice_file_btn deposit_pdf" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/batch_' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['ibf_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                    </div> 
                </div>
            </div>
            <?php
            $blcount++;
        }
        ?>
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
    <div class="modal fade" id="invoice_process" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog invoice_main_modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Invoice Option</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body invoice_option_body">
                    <div class="invoice_option_body">
                        <p>This will generate invoices for each selected customer's charges through <span class="invoice_date"><?= date('m/d/Y'); ?></span>. Select how you would like to deliver the invoices below.</p>
                    </div>
                    <div class="invoice_modal_option">
                        <div class="mb-3">
                            <p>Email (1 cent each)</p>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="email_cust" name="email_cust" <?= ($biller_data['invoice_email'] == 1 ? "checked" : ""); ?> class="custom-control-input chrges_invoice_option" data-type="email">
                                <label class="custom-control-label" for="email_cust">Deliver via email to customers with email address</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="nemail_cust" name="email_cust" <?= ($biller_data['invoice_email'] == 2 || $biller_data['invoice_email'] == 0 ? "checked" : ""); ?> class="custom-control-input chrges_invoice_option" data-type="email">
                                <label class="custom-control-label" for="nemail_cust">Do not deliver invoices via email</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p>USPS ($1 each)</p>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="usps_type" <?= ($biller_data['invoice_usps'] == 1 ? "checked" : ""); ?> name="USPS" class="custom-control-input chrges_invoice_option" data-type="usps">
                                <label class="custom-control-label" for="usps_type">Deliver via USPS to customers that did not receive an emailed invoice</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="ausps_cust" <?= ($biller_data['invoice_usps'] == 2 ? "checked" : ""); ?> name="USPS" class="custom-control-input chrges_invoice_option" data-type="usps">
                                <label class="custom-control-label" for="ausps_cust">Deliver invoices via USPS to all customers</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="nusps_cust" <?= ($biller_data['invoice_usps'] == 3 || $biller_data['invoice_usps'] == 0 ? "checked" : ""); ?> name="USPS" class="custom-control-input chrges_invoice_option" data-type="usps">
                                <label class="custom-control-label" for="nusps_cust">Do not deliver invoices via USPS</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p>PDF Batch Print File</p>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="print_invoice" <?= ($biller_data['invoice_pdf'] == 1 ? "checked" : ""); ?> name="pdf_batch" class="custom-control-input chrges_invoice_option" data-type="print">
                                <label class="custom-control-label" for="print_invoice">Print only invoices that were not delivered via email or USPS</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="aprint_invoice" <?= ($biller_data['invoice_pdf'] == 2 || $biller_data['invoice_pdf'] == 0 ? "checked" : ""); ?> name="pdf_batch" class="custom-control-input chrges_invoice_option" data-type="print">
                                <label class="custom-control-label" for="aprint_invoice">Print all invoices</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary customer_invoice_process">Run<i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="invoice_preview_html">

</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="<?php echo base_url(); ?>js/bill_charges.js"></script>