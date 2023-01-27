<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<style>
    .output tr:first-child td {
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }
    .admin-contain-main-div{
        padding: 30px;
    }
    .csv_heading_line{
        font-size: 17px;
    }
</style>

<div class="form-group" id="text_paste_area" style="display: none;">
    <h4 class="add_charges_heading">Import</h4>
    <p class="mb-3 csv_heading_line">Copy and paste your data from a spreadsheet program below. Be sure to include field names in the first row.</p>
    <textarea class="form-control" id="csv" rows="3"></textarea>
    <p class="email_error_message error_message" style="display: none;">Please paste more than one row with header.</p>
</div>
<div class="output_div" style="display: none;">
    <h4 class="add_charges_heading select_import_heading" id="import_heading">Import</h4>
    <h4 class="add_charges_heading select_import_heading" id="map_heading" style="display: none;">Map</h4><br>
    <div class="text-right mb-2"><button type="button" class="btn btn-submit" id="btn_undo">Undo</button></div>
    <div class="output table-responsive mb-3"></div> 
    <div class="text-right mt-2 btn_next_step"><button href="#" class="btn btn-submit btn_next_step" id="submit-parse">Next</button></div>
    <div class="text-right mt-2 btn_map_next" style="display: none;"><button href="#" class="btn btn-submit btn_next_form">Next</button></div>
</div>
<div class="item_form_div" style="display: none;">

    <h4 id="add_charges_heading">Import Items</h4>
    <div class="item_box_heading">
        <span class="count_record">1</span> of <span class="total_number_record"></span>
        <a href="JavaScript:void(0);" class="view_all_list">View List <i class="fas fa-chevron-down"></i></a>
    </div>
    <form id="add_item" method="post">
        <div class="row item_add_form_main">
            <div class="col-md-7">
                <div class="identification mt-3">	
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped change_item" name="form[name]" id="item_name" data-content="Prependeed to line item description on invoices." value="">
                            <input type="hidden" name="form[status]" id="item_status" value="active">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Description</label>
                        <div class="col-sm-9">
                            <textarea class="form-control tooltipped change_item" rows="5" name="form[description]" id="description"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Bill Rate</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control tooltipped change_item" name="form[rate]" id="bill_rate" data-content="Charge this amount unless client has custom pricing defined." value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Can Discount?</label>
                        <div class="col-sm-9 type_select_option">
                            <select class="form-control tooltipped change_itemd selectpicker" id="can_discount" data-width="100%" name="form[can_discount]" data-content="Does a client`s discount percent apply to this item?">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Type</label>
                        <div class="col-sm-9 type_select_option">
                            <select class="form-control change_itemd selectpicker" id="item_type" name="form[ct_id]" data-live-search="true"  data-width="100%">
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
                    <div class="form-group row" style="display: none;">
                        <label class="col-sm-3 col-form-label">Delete?</label>
                        <label class="checkbox_containers">
                            <input type="checkbox" id="hide_password_checkbox" class="tooltipped_delete change_itemd" data-content="Tax is only calculated for 'Product' items at the rate defined for the client. No other item tyes are taxed.'Unbillable' items are not show on the client`s invoice. To show clients items on their invoice without charging for them, create a billable item and set the rate to $0.">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="row mt-3 save_skip_btn">
                        <button type="button" class="btn btn-submit mr-2" id="btn_item_skip">Skip</button>
                        <button type="button" class="btn btn-submit" id="btn_item_save">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="customer_form_div position-relative" style="display: none;">
    <h4 id="add_charges_heading">Import Customer</h4>
    <div class="item_box_heading">
        <span class="count_record">1</span> of <span class="total_number_record"></span>
        <a href="JavaScript:void(0);" class="view_all_list">View List <i class="fas fa-chevron-down"></i></a>
    </div>
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
                            <input type="text" class="form-control tooltipped" name="form[name]" data-content="Please enter identification name." value="" id="customer_name">
                            <input type="hidden" name="form[status]" id="customer_status" value="active">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Job ID</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped" name="form[identifier]" data-content="Please enter your Job ID." value="" id="job_id">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">PO Number</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped" name="form[po]" data-content="Please enter your PO Number." value="" id="po_number">
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
                            <input type="text" class="form-control tooltipped" name="form[contact_name]" value="" id="person_name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control tooltipped" name="form[contact_email]" value="" id="person_email">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Phone</label>
                        <div class="col-sm-9">
                            <input type="tel" class="form-control tooltipped" id="customer_phone" name="form[contact_phone]" value="">
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
                            <input type="text" class="form-control tooltipped" name="form[address_mail_attention]" value="" id="attn_dpt">
                        </div>
                    </div>

                    <div class="form-group row">

                        <label class="col-sm-3 col-form-label">Mailing Address</label>

                        <div class="col-sm-9 position-relative">
                            <textarea class="form-control mailling_address tooltipped h_address" onchange="mailing_address();" name="form[mailling_address]" rows="3" id="mailingAutoComplete" data-type="h"></textarea>
                            <span id="h_cass_icon" class="loder_spiner_mail" data-toggle="tooltip" data-placement="top"></span>
                        </div>
                    </div>
                    <i class="fas fa-spinner fa-spin loder_spiner_service" style="display: none;"></i>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Service Address</label>
                        <div class="col-sm-9">
                            <textarea class="form-control service_address tooltipped m_address" onchange="service_address();" name="form[service_address]" rows="3" id="serviceAutoComplete" data-type="m"></textarea>
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
                            <input type="number" class="form-control tooltipped" name="form[discount]" id="customer_discount" data-content="Please enter your Billing Discount." value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Minimum</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control tooltipped" name="form[retainer]" data-content="Please enter Billing Minimum Discount." value="" id="minimum">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">State Tax</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control tooltipped" name="form[tax_state]" value="" id="state_tax">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">County Tax</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control tooltipped" name="form[tax_county]" value="" id="country_tax">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Email Bill to</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control tooltipped" name="form[email_to_list]" data-content="Please enter Billing email address." value="" id="email_bill">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="form-group col-md-12">
                <label for="">Notes</label>
                <textarea class="form-control tooltipped" rows="5" name="form[notes]" id="notes"></textarea>
            </div>
            <div class="save_skip_btn">
                <button type="button" class="btn btn-submit1 mr-2" id="btn_customer_skip">Skip</button>
                <button type="button" class="btn btn-submit1" id="btn_customer_save">Save</button>
            </div>
        </div>
    </form>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Import</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.history.back();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <p style="font-size:16px;">Click on the button, which you want to like to import data.</p>
                    <p><button type="button" class="btn btn-primary" id="btn_customer"><i class="far fa-handshake fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Customers</span></button></p>
                    <p class="mb-0"><button type="button" class="btn btn-primary btn_item_import" id="btn_item"><i class="fas fa-list fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Items</span></button></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= env('google.place_api'); ?>&libraries=places&callback=initAutocomplete" async defer></script>
<script src="<?php echo base_url(); ?>js/import.js"></script>