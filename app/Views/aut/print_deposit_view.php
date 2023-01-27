<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<style>
    .admin-contain-main-div{
        padding: 20px;
    }
</style>
<div class="container-fluid">
    <div class="row">   
        <div class="main-table-div add_header_line">
            <div class="print_deposite_header no_mob">
                <h5 class="">Print Deposit</h5>
            </div>
            <div class="print_save_div">
                <div class="deposit_his_div mb-2">
                    <button type="button" class="btn btn-submit history_show">Deposit History</button>
                    <button type="button" class="btn btn-submit history_cancel" style="display: none;">Cancel</button>
                </div>
                <div class="deposit_total" style="display: none;">
                    <h6 style="font-weight: 600;" class="mr-2">Deposit Amount: $<span class="total_amount">0.00</span></h6>
                    <button class="btn btn-submit mb-2" id="btn_print_save" style="display: none;">Print & Save Deposit</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row charges-details-main print_deposit">
        <div class="table-heading">
            <div class="checktable-cols">

            </div> 
            <div class="table-cols">
                <p>Amount</p>
            </div> 
            <div class="table-cols">
                <p>Date</p>
            </div> 
            <div class="ref-table-cols">
                <p class="mb-0">Reference</p>
            </div> 
            <div class="dtable-cols no_mob">
                <p>Customer</p>
            </div> 
        </div>
        <div class="customer_listing_charge">
            <?php
            $blcount = 0;
            foreach ($customer_data as $value) {
                ?>
                <div class="charge-tbl <?= ($blcount % 2 ? "bg-gray" : "bg-white1"); ?>">
                    <div class="results-data ">
                        <div class="checktable-cols">
                            <div class="custom-control custom-checkbox" style="cursor: pointer;">
                                <input type="checkbox" class="custom-control-input deposit_select_checkbox" data-id='<?= base64_encode(encrypt($value['credit_id'])); ?>' data-amount="<?= $value['amount']; ?>" id="customCheck<?= $blcount; ?>" style="cursor: pointer;">
                                <label class="custom-control-label" for="customCheck<?= $blcount; ?>" style="cursor: pointer;"></label>
                            </div>
                        </div>
                        <div class="table-cols">
                            <p><?= '$' . number_format($value['amount'], 2); ?></p>
                        </div>
                        <div class="table-cols datetable-cols">
                            <p><?= date("m/d/Y", strtotime($value['date_credit'])); ?></p>
                        </div> 
                        <div class="ref-table-cols datetable-cols">
                            <p><?= $value['reference']; ?></p>
                        </div> 
                        <div class="dtable-cols no_mob">
                            <p><?= $value['customers']; ?></p>
                        </div>
                    </div>
                    <p class="no_desktop mob_des"><?= $value['customers']; ?></p>
                </div>

                <?php
                $blcount++;
            }
            if (count($customer_data) == 0) {
                ?>
                <div class="row1 blank_col_div">
                    <div class="table-col blank_col">
                        <p>No Bill Charges Available</p>
                    </div>
                </div>

                <?php
            }
            ?>
        </div>
    </div>
    <div class="row deposit-details-main" style="display: none;">
        <div class="table-heading">
            <div class="edittable-cols no_mob">
            </div> 
            <div class="table-cols deposit_date_col">
                <p>Date</p>
            </div> 
            <div class="table-cols deposit_item_col text-right">
                <p>Items</p>
            </div> 
            <div class="table-cols deposit_amount_col text-right">
                <p>Amount</p>
            </div>
            <div class="deletetable-cols no_mob">
            </div> 
            
        </div>
        <div class="blank_history_div"></div>
        <?php
        $blcount = 0;
        foreach ($deposit_data as $value) {
            ?>
        <div class="w-100 <?= ($blcount % 2 ? "bg-gray" : "bg-white1"); ?>">
        
        
            <div class="charge-tbl <?= ($blcount % 2 ? "bg-gray" : "bg-white1"); ?> action_deposit" style="cursor: pointer;">
                <div class="results-data">
                    <div class="edittable-cols no_mob">
                        <button type="button" class="edit_delete_btn deposit_undo" data-id="<?= base64_encode(encrypt($value['deposit_id'])); ?>"><i class="fas fa-undo"></i></button>
                    </div> 
                    <div class="table-cols deposit_date_col">
                        <p><?= date("m/d/Y", strtotime($value['time_created'])); ?></p>
                    </div> 
                    <div class="table-cols deposit_item_col text-right">
                        <p><?= count($value['credit_data']); ?></p>
                    </div> 
                    <div class="table-cols deposit_amount_col text-right">
                        <p><?= '$' . number_format($value['amount'], 2); ?></p>
                    </div>
                    <div class="deletetable-cols no_mob">
                        <?php
                        $credit_id = '';
                        foreach ($value['credit_data'] as $civalue) {
                            $credit_id.=$civalue['credit_id'] . ',';
                        }
                        $credit_id = rtrim($credit_id, ',');
                        ?>
                        <button type="button" class="edit_delete_btn invoice_file_btn deposit_pdf" data-id="<?= $credit_id; ?>"><i class="far fa-file-alt"></i></button>
                    </div> 
                    <div class="action_click">
                        <div class="angle-righticon">
                            <i class="fas fa-chevron-right icon-rotate"></i>
                        </div>
                    </div>
                </div>
                <div class="action_div" style="display: none;">
                    <div class="action-cols">
                        <button type="button" class="edit_delete_btn deposit_undo" data-id="<?= base64_encode(encrypt($value['deposit_id'])); ?>"><i class="fas fa-undo"></i></button>
                        <button type="button" class="edit_delete_btn invoice_file_btn deposit_pdf" data-id="<?= $credit_id; ?>"><i class="far fa-file-alt"></i></button>
                    </div>
                </div>
            </div>
            <div class="colleps-div colleps_deposit" style="display: none;">
                <?php
                $credit_count = 0;
                $detail_count = 0;
                foreach ($value['credit_data'] as $cvalue) {
                    if ($credit_count == 0) {
                        ?>
                        <div class="table-heading under_deposite_header">
                            <div class="table-cols deposit_credit_type">
                                <p>Type</p>
                            </div> 
                            <div class="table-cols no_mob">
                                <p>Reference</p>
                            </div> 
                            <div class="table-cols deposit_credit_date">
                                <p>Date</p>
                            </div>
                            <div class="dtable-cols no_mob">
                                <p>Description</p>
                            </div>
                            <div class="table-cols deposit_credit_amount">
                                <p>Amount</p>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="results-data">
                        <div class="table-cols deposit_credit_type">
                            <p><?= $cvalue['type']; ?></p>
                        </div>
                        <div class="table-cols no_mob">
                            <p><?= $cvalue['reference']; ?></p>
                        </div>
                        <div class="table-cols deposit_credit_date">
                            <p><?= date("m/d/Y", strtotime($cvalue['date_credit'])); ?></p>
                        </div>
                        <div class="dtable-cols no_mob">
                            <p><?= $cvalue['description']; ?></p>
                        </div>
                        <div class="table-cols deposit_credit_amount">
                            <p><?= '$' . number_format($cvalue['amount'], 2); ?></p>
                        </div>
                    </div>
                    <div class="table-col1">
                        <p class="no_desktop"><?= $cvalue['reference']; ?></p>
                        <p class="no_desktop"><?= $cvalue['description']; ?></p>
                    </div>
                    <?php if (count($cvalue['credit_detail']) >= 1) { ?>
                        <div class="customer_invoice deposit_invoice_div">
                            <?php
                            foreach ($cvalue['credit_detail'] as $invoice_value) {
                                if ($detail_count == 0) {
                                    ?>
                                    <div class="table-heading under_invoice_header">
                                        <div class="table-cols deposit_cust_name">
                                            <p>Name</p>
                                        </div> 
                                        <div class="table-cols deposit_invoice_number">
                                            <p>Invoice Number</p>
                                        </div> 
                                        <div class="table-cols deposit_cust_name">
                                            <p>Amount</p>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="post_payment_invoice">
                                    <div class="table-cols deposit_cust_name">
                                        <p><?= $invoice_value['name']; ?></p>
                                    </div>
                                    <div class="table-cols deposit_invoice_number">
                                        <p>Invoice <?= $invoice_value["invoice_number"]; ?></p>

                                    </div>
                                    <div class="table-cols deposit_cust_name">
                                        <p><?= '$' . number_format($invoice_value['amount'], 2); ?></p>
                                    </div>
                                </div>
                                <?php
                                $detail_count++;
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    $credit_count++;
                }
                ?>
            </div>
            </div>
            <?php
            $blcount++;
        }

        if (count($deposit_data) == 0) {
            ?>
            <div class="row1 blank_col_div">
                <div class="table-col blank_col">
                    <p>No Deposit History Available</p>
                </div>
            </div>

            <?php
        }
        ?>
    </div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="<?php echo base_url(); ?>js/print_deposit.js"></script>