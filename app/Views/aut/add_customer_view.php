<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>

<div class="container-fluid">

    <div class="add_header_line">
        <div><h4><?= (isset($customer_id)) ? "Edit Customer" : "Add Customer"; ?></h4></div>
        <div class="onoffswitch">
            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox tooltipped" id="myonoffswitch" <?= (!isset($customer_id)) ? "checked" : ""; ?> <?= (isset($status) && $status == "active" ? "checked" : "") ?>>

            <label class="onoffswitch-label" for="myonoffswitch">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
            </label>
        </div>
    </div>
    <input type="hidden" id="customer_id" value="<?php echo (isset($customer_id) ? $customer_id : "") ?>">
    <form id="add_customer" method="post">
        <div class="row">
            <div class="col-md-6">
                <div class="identification-title">
                    <h4>Identification</h4>
                </div>
                <div class="identification">	
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped" name="form[name]" data-content="Please enter identification name." value="<?= (isset($name) ? $name : ""); ?>">
                            <input type="hidden" name="form[status]" id="customer_status" value="<?= (!isset($customer_id)) ? "active" : ""; ?><?= (isset($status) && $status == "active" ? "active" : "") ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Job ID</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped" name="form[identifier]" data-content="Please enter your Job ID." value="<?= (isset($identifier) ? $identifier : ""); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">PO Number</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped" name="form[po]" data-content="Please enter your PO Number." value="<?= (isset($po) ? $po : ""); ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="identification-title">
                    <h4>Contact</h4>
                </div>
                <div class="identification">	
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Person</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped" name="form[contact_name]" value="<?= (isset($contact_name) ? $contact_name : ""); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control tooltipped" name="form[contact_email]" value="<?= (isset($contact_email) ? $contact_email : ""); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Phone</label>
                        <div class="col-sm-9">
                            <input type="tel" class="form-control tooltipped" id="customer_phone" name="form[contact_phone]" value="<?= (isset($contact_phone) && $contact_phone != 0 ? $contact_phone : ""); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="identification-title">
                    <h4>Address</h4>
                </div>
                <div class="identification">	
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Attn/Dpt</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped" name="form[address_mail_attention]" value="<?= (isset($address_mail_attention) ? $address_mail_attention : ""); ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php
                        $mail_address = (isset($address_mail_street) && $address_mail_street != '' ? $address_mail_street : "") . (isset($address_mail_city) && $address_mail_city != '' ? "\n" . $address_mail_city : "") . (isset($address_mail_state) && $address_mail_state != '' ? ", " . $address_mail_state : "") . (isset($address_mail_zip5) && $address_mail_zip5 != 0 ? " " . $address_mail_zip5 : "");
                        $service_address = (isset($address_street) && $address_street != '' ? $address_street : "") . (isset($address_city) && $address_city != '' ? "\n" . $address_city : "") . (isset($address_state) && $address_state != '' ? ", " . $address_state : "") . (isset($address_zip5) && $address_mail_zip5 != 0 ? " " . $address_zip5 : "");
                        ?>
                        <label class="col-sm-3 col-form-label">Mailing Address</label>

                        <div class="col-sm-9 position-relative">
                            <textarea class="form-control mailling_address tooltipped h_address" name="form[mailling_address]" rows="3" id="mailingAutoComplete" data-type="h"><?= (isset($mail_address) ? $mail_address : "") ?></textarea>
                            <span id="h_cass_icon" class="loder_spiner_mail" data-toggle="tooltip" data-placement="top"></span>
                        </div>
                    </div>
                    <i class="fas fa-spinner fa-spin loder_spiner_service" style="display: none;"></i>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Service Address</label>
                        <div class="col-sm-9">
                            <textarea class="form-control service_address tooltipped m_address" name="form[service_address]" rows="3" id="serviceAutoComplete" data-type="m"><?= (isset($service_address) ? $service_address : "") ?></textarea>
                            <span id="m_cass_icon" class="loder_spiner_mail" data-toggle="tooltip" data-placement="top"></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-6">
                <div class="identification-title">
                    <h4>Billing Info</h4>
                </div>
                <div class="identification">	
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Discount</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control tooltipped" name="form[discount]" id="customer_discount" data-content="Please enter your Billing Discount." value="<?= (isset($discount) && $discount != 0 ? $discount / 100 : ""); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Minimum</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control tooltipped" name="form[retainer]" data-content="Please enter Billing Minimum Discount." value="<?= (isset($retainer) && $retainer != 0 ? $retainer : ""); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">State Tax</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control tooltipped" name="form[tax_state]" value="<?= (isset($tax_state) && $tax_state != 0 ? $tax_state / 100 : ""); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">County Tax</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control tooltipped" name="form[tax_county]" value="<?= (isset($tax_county) && $tax_state != 0 ? $tax_county / 100 : ""); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Email Bill to</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped" name="form[email_to_list]" data-content="Please enter Billing email address." value="<?= (isset($email_to_list) ? $email_to_list : ""); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="form-group col-md-12">
                <label for="">Notes</label>
                <textarea class="form-control tooltipped" rows="5" name="form[notes]"><?= (isset($notes) ? $notes : ""); ?></textarea>
            </div>
            <div class="btn-submitdiv pl-3 update_customer_btn">
                <?php
                if (isset($customer_id)) {
                    echo '<button type="button" class="btn btn-submit update_customer" onclick="edit_customer();" style="display:none;">Save Changes<i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>';
                } else {
                    echo '<button type="button" class="btn btn-submit update_customer" onclick="add_customer();" style="display:none;">Save Changes<i class="fas fa-spinner fa-spin add_customer_loder" style="display: none;"></i></button>';
                }
                ?>
            </div>
            <div class="btn-submitdiv pl-3 back_customer_btn">
                <button type="button" class="btn btn-adduser" id="back_customer_list">Back</button>
            </div>
        </div>
    </form>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= env('google.place_api'); ?>&libraries=places&callback=initAutocomplete" async defer></script>
<script src="<?php echo base_url(); ?>js/customer.js"></script>