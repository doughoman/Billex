<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<div class="container-fluid">
    <div class="row">   
        <div class="main-table-div">
            <h5 class="no_mob" id="add_charges_heading"><?= (isset($charge_data['credit_id']) ? "Edit" : "Enter"); ?> Post Payment</h5>
        </div>
    </div>
    <div class="row mt-3 post_payment_main_div">
        <div class="col-md-8 col-lg-8">
            <form id="post_payment_form">
                <input type="hidden" name="form[credit_id]" value="<?= (isset($charge_data['credit_id']) ? base64_encode(encrypt($charge_data['credit_id'])) : ""); ?>">
                <input type="hidden" id="customer_id" name="form[customer_id]" value="<?= (isset($charge_data['customer_id']) ? base64_encode(encrypt($charge_data['customer_id'])) : ""); ?>">
                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left">Type</label>
                    <div class="col-sm-9 cust_width">
                        <div class="custom-dropdown big type_select_option">
                            <select class="selectpicker" name="form[type]" data-live-search="true"  data-width="100%" onchange="select_type(this);" title="Choose one of the type" id="payment_type">
                                <option value="Adjustment" <?= (isset($charge_data['type']) && $charge_data['type'] == "Adjustment" ? "selected" : ""); ?>>Adjustment</option>
                                <option value="Card" <?= (isset($charge_data['type']) && $charge_data['type'] == "Card" ? "selected" : ""); ?>>Card</option>
                                <option value="Cash" <?= (isset($charge_data['type']) && $charge_data['type'] == "Cash" ? "selected" : ""); ?>>Cash</option>
                                <option value="Check" <?= (isset($charge_data['type']) && $charge_data['type'] == "Check" ? "selected" : ""); ?>>Check</option>
                                <option value="PayPal" <?= (isset($charge_data['type']) && $charge_data['type'] == "PayPal" ? "selected" : ""); ?>>PayPal</option>
                                <option value="Other" <?= (isset($charge_data['type']) && $charge_data['type'] == "Other" ? "selected" : ""); ?>>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-3 text-right mob-left">Date</label>
                    <div class="col-sm-9 cust_width">
                        <div id="datepicker" class="input-group date" data-date-format="mm/dd/yyyy">

                            <input class="form-control" type="text" name="form[date_credit]" id="date_credit" autocomplete="off" value="<?= (isset($charge_data['date_credit']) ? date('m/d/Y', strtotime($charge_data['date_credit'])) : ""); ?>"/>
                            <span class="input-group-text input-group-addon dp"><i class="fas fa-calendar-alt"></i></span>
                            <!--  <span class="input-group-addon"></span> -->
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left">Check/Ref#</label>
                    <div class="col-sm-9 cust_width">
                        <input type="text" class="form-control" name="form[reference]" autocomplete="off" id="check_ref" value="<?= (isset($charge_data['reference']) ? $charge_data['reference'] : ""); ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left post_payment_description">Description</label>
                    <div class="col-sm-9">
                        <textarea class="form-control payment_description" id="payment_description" rows="2" name="form[description]"><?= (isset($charge_data['description']) ? $charge_data['description'] : ""); ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left text-muted">Amount</label>
                    <div class="col-sm-9 cust_width">
                        <input type="text" class="form-control" readonly="" value="<?= (isset($charge_data['amount']) ? $charge_data['amount'] : ""); ?>" name="form[amount]" data-toggle="tooltip" data-placement="top" title="Amount is calculated based on the amounts you post to each customer's account in the list below." autocomplete="off" id="total_amount_customer">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 text-right mob-left"></label>
                    <div class="col-sm-9">
                        <?php
                        if (isset($charge_data['credit_id'])) {
                            echo '<button class="btn btn-submit mr-2" type="button" id="add_edit_payment_btn" onclick="edit_post_payment()">Edit<i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>';
                            echo '<button class="btn btn-submit" type="button" id="cancel_payment_btn" >Cancel<i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>';
                        } else {
                            echo '<button class="btn btn-submit" type="button" id="add_edit_payment_btn" onclick="add_post_payment()">Save<i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>';
                        }
                        ?>

                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="filter-main customer_filter">
            <div class="listing_toggle">
                <div class="form-group search-box">
                    <input type="text" class="form-control search_edit" id="customer_search" placeholder="Search Customer">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            <div class="listing_toggle">
                <div class="form-group search-box">
                    <input type="text" class="form-control search_edit" id="invoice_search" placeholder="Search Invoice No.">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </div>
        <div class="main-table-div post_payment_customer">
            <div class="table-heading no_mob">
                <div class="cust_balance text-right">
                    <p>Balance</p>
                </div>
                <div class="cust_name">
                    <p>Name</p><p>ID / PO</p>
                </div>
                <div class="cust_contact">
                    <p>Contact</p>
                </div>
                <div class="cust_address">
                    <p>Attr</p>
                    <p>Address</p>
                </div>
            </div>
            <div class="mainrow-div">
                <div class="charge-tbl charges_header_div" style="display: none;">
                    <label>Amount: $ <span class="total_amount"></span></label>
                </div>
                <?php
                $post_payment = 0;
                $post_payment = isset($charge_data['amount']) ? $charge_data['amount'] : 0.00;
                $blcount = 0;
                $icount = 0;
                foreach ($customer_data as $key => $customer_value) {
                    ?>
                    <div class="customer_payment user_listing_main" data-id="<?= $customer_value['customer_id'] ?>">
                        <div class="row1 colpsclick customer_details_row customer_invoice_<?= $customer_value['customer_id'] ?>" >
                            <div class="cust_balance text-right">
                                <p class="balance_down_icon"><i class="fas fa-arrow-circle-down pr-2" style="color:#4e4ee8;"></i><span class="customer_balance"><?= '$' . number_format(floatval($customer_value["balance"]), 2); ?></span></p>
                                <p class="customer_amount_add"><input type="text" class="form-control customer_balnce_text text-right cust_bal_<?= $customer_value['customer_id'] ?>" value="<?= (isset($charge_data['amount']) ? number_format(floatval($charge_data['amount']), 2) : ""); ?>" data-id="<?= $customer_value['customer_id']; ?>" max="<?= $customer_value["balance"]; ?>"></p>
                            </div>
                            <div class="cust_name search_text_div">
                                <strong><?= $customer_value["name"]; ?></strong>
                                <p><span><?= $customer_value["identifier"]; ?></span> <?= (($customer_value["identifier"] == '' && $customer_value["po"] == '') || ($customer_value["identifier"] == '' || $customer_value["po"] == '') ? "" : "/") ?> <span><?= $customer_value["po"]; ?></span></p>
                            </div>
                            <div class="cust_contact recordhide">
                                <p><?= $customer_value["contact_name"]; ?></p>
                                <p><?php echo '<a href="mailto:' . $customer_value["contact_email"] . '" target="_blank" class="customer_mail_phone">' . $customer_value["contact_email"] . '</a>'; ?></p>
                                <p><?php
                                    if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $customer_value["contact_phone"], $matches)) {
                                        $phone_result = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                                        echo '<a href="tel:' . $phone_result . '" target="_blank" class="customer_mail_phone">' . $phone_result . '</a>';
                                    }
                                    ?></p>
                            </div>
                            <div class="cust_address recordhide">
                                <p><?= $customer_value["address_mail_attention"]; ?></p>
                                <p style="display: <?= ($customer_value["address_mail_street"] == "" ? "none" : "") ?>"><?= $customer_value["address_mail_street"]; ?><br><?= $customer_value["address_mail_city"]; ?>, <?= $customer_value["address_mail_state"]; ?> <?= $customer_value["address_mail_zip5"]; ?></p>
                            </div>
                            <div class="angle-righticon no_desktop colpsclick1">
                                <i class="fas fa-chevron-right icon-rotate"></i>
                            </div>
                        </div>
                        <?php if (count($customer_value['invoice_data']) >= 1) { ?>
                            <div class="customer_invoice customer_invoice_div" style="display: none;">
                                <?php
                                foreach ($customer_value['invoice_data'] as $invoice_value) {
                                    ?>
                                    <div class="post_payment_invoice">
                                        <div class="cust_balance text-right">
                                            <p class="invoice_down_icon" data-custid="<?= $customer_value['customer_id'] ?>"><i class="fas fa-arrow-circle-down pr-2" style="color:#4e4ee8;"></i><span class="customer_balance invoice_balance"><?= '$' . number_format(floatval($invoice_value['invoice_balance']), 2); ?></span></p>
                                            <p class="customer_amount_add"><input type="text" data-custid="<?= $customer_value['customer_id'] ?>" class="form-control customer_invoice customer_invoice_text_<?= $customer_value['customer_id'] ?> text-right" value="" data-id="<?= $invoice_value['invoice_id']; ?>" max="<?= $invoice_value['invoice_balance']; ?>"></p>
                                        </div>
                                        <div class="cust_name" style="margin-top: 14px;">
                                            <p>Invoice <?= $invoice_value["invoice_number"]; ?></p>
                                            <p><?= date("m/d/Y", strtotime($invoice_value['time_created'])); ?></p>
                                        </div>
                                    </div>
                                    <?php
                                    $icount++;
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    $blcount++;
                }
                if (count($customer_data) == 0) {
                    ?>
                    <div class="row1 blank_col_div">
                        <div class="table-col blank_col">
                            <p>No Post Payment Available</p>
                        </div>
                    </div>

                    <?php
                }
                ?>

            </div>
        </div>
    </div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="<?php echo base_url(); ?>js/post_payment.js"></script>
<?php
if (isset($charge_data['date_credit'])) {
    ?>
    <script type="text/javascript">
                                    $("#datepicker").datepicker({
                                        autoclose: true,
                                        todayHighlight: true,
                                        format: 'mm/dd/yyyy'
                                    }).datepicker('update', new Date("<?php echo date('m/d/Y', strtotime($charge_data['date_credit'])); ?>"));
    </script>

    <?php
} else {
    ?>
    <script type="text/javascript">
        $("#datepicker").datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'mm/dd/yyyy'
        }).datepicker('update', new Date());
    </script>

    <?php
}
?>