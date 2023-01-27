<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<div class="container-fluid">
    <div class="add_header_line">
        <div><h4><?= (isset($item_id)) ? "Edit Item" : "Add Item"; ?></h4></div>
        <div class="onoffswitch">
            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" <?= (!isset($item_id)) ? "checked" : ""; ?><?= (isset($status) && $status == "active" ? "checked" : "") ?>>
            <label class="onoffswitch-label" for="myonoffswitch">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
            </label>
        </div>
    </div>
    <input type="hidden" id="item_id" value="<?php echo (isset($item_id) ? $item_id : "") ?>">
    <form id="add_item" method="post">
        <div class="row">
            <div class="col-md-12">
                <div class="identification-title">
                    <h4>Item</h4>
                </div>
                <div class="identification">	
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped change_item" name="form[name]" id="item_name" data-content="Prependeed to line item description on invoices." value="<?= (isset($name) ? $name : ""); ?>">
                            <input type="hidden" name="form[status]" id="item_status" value="<?= (!isset($item_id)) ? "active" : ""; ?><?= (isset($status) && $status == "active" ? "active" : "") ?>">

                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Description</label>
                        <div class="col-sm-9">
                            <textarea class="form-control tooltipped change_item" rows="5" name="form[description]"><?= (isset($description) ? $description : ""); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Bill Rate</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control tooltipped change_item" name="form[rate]" id="customer_discount" data-content="Charge this amount unless client has custom pricing defined." value="<?= (isset($rate) ? $rate : ""); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Can Discount?</label>
                        <div class="col-sm-9 type_select_option">
                            <select class="form-control tooltipped change_itemd selectpicker"  data-width="100%" name="form[can_discount]" data-content="Does a client`s discount percent apply to this item?">
                                <option value="1" <?= (isset($can_discount) && $can_discount == 1 ? "selected" : "") ?>>Yes</option>
                                <option value="0" <?= (isset($can_discount) && $can_discount == 0 ? "selected" : "") ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Type</label>
                        <div class="col-sm-9 type_select_option">
                            <select class="form-control change_itemd selectpicker" name="form[ct_id]" data-live-search="true"  data-width="100%">
                                <option value="Billable Labor" <?= (isset($ct_id) && $ct_id == "1" ? "selected" : "") ?>>Billable Labor</option>
                                <option value="Service" <?= (isset($ct_id) && $ct_id == "2" ? "selected" : "") ?>>Service</option>
                                <option value="Product" <?= (isset($ct_id) && $ct_id == "3" ? "selected" : "") ?>>Product</option>
                                <option value="Download" <?= (isset($ct_id) && $ct_id == "7" ? "selected" : "") ?>>Download</option>
                                <option value="Reimbursement" <?= (isset($ct_id) && $ct_id == "4" ? "selected" : "") ?>>Reimbursement</option>
                                <option value="Unbillable Labor" <?= (isset($ct_id) && $ct_id == "5" ? "selected" : "") ?>>Unbillable Labor</option>
                                <option value="Unbillable Other" <?= (isset($ct_id) && $ct_id == "6" ? "selected" : "") ?>>Unbillable Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" style="display: none;">
                        <label class="col-sm-3 col-form-label">Delete?</label>
                        <label class="checkbox_containers">
                            <input type="checkbox" id="hide_password_checkbox" class="tooltipped_delete change_itemd" data-content="Tax is only calculated for 'Product' items at the rate defined for the client. No other item tyes are taxed.'Unbillable' items are not show on the client`s invoice. To show clients items on their invoice without charging for them, create a billable item and set the rate to $0.">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="row mt-3">
                        <div class="btn-submitdiv text-right pr-3 update_customer_btn">
                            <?php
                            if (isset($item_id)) {
                                echo '<button type="button" class="btn btn-submit update_customer" onclick="edit_item();" style="display:none;">Save Changes<i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>';
                            } else {
                                echo '<button type="button" class="btn btn-submit update_customer" onclick="add_item();" style="display:none;">Save Changes<i class="fas fa-spinner fa-spin add_customer_loder" style="display: none;"></i></button>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
    <div class="btn-submitdiv pl-3 back_customer_btn">
        <a href="<?= base_url() ?>aut/administration/item" class="btn btn-adduser" id="back_customer_list">Back</a>
    </div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="<?php echo base_url(); ?>js/administration.js"></script>