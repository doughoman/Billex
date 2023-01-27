<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>

<div class="container-fluid">
    <h4 id="add_charges_heading">Add User</h4>
    <div class="row mt-3">
        <div class="col-md-5 col-lg-5">
            <form id="add_biller_user">
                <div class="form-group row">
                    <div class="col-sm-9">
                        <label class="name_label" for="name_display">Name:</label>
                        <input type="text" class="form-control" name="form[name_display]" id="name_display">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="btn-group btn-group-toggle ml-3" data-toggle="buttons" role="group" id="options">
                        <label class="btn btn-outline-primary active invite_type_option">
                            <input type="radio" name="form[options]" id="email" autocomplete="off" checked value="email"> Email
                        </label>
                        <label class="btn btn-outline-primary invite_type_option">
                            <input type="radio" name="form[options]" id="phone" autocomplete="off" value="phone"> Phone
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-9">
                        <input type="text" class="form-control send_invite_option" name="form[email_address]" id="user_email_address">  
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-9">
                        <button class="btn btn-submit" type="button" id="add_edit_charge_btn" onclick="send_invite();">Send Invite<i class="fas fa-spinner fa-spin add_user_loder ml-1" style="display: none;"></i></button>
                    </div>
                </div>
                <p class="email_error_message" style="visibility: hidden;">Please enter valid email address.!</p>
                <p class="phone_error_message" style="visibility: hidden;">User already belong to this billex account.</p>
            </form>
        </div>
    </div>	
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="<?php echo base_url(); ?>js/administration.js"></script>