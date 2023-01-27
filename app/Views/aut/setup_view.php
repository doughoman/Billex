
<?php
$page_title = "Setup";
echo view('pub/common/header_view');
?>
<section>
    <div class="main-section main-title setup_custom">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="get-started-div">
                        <h1>Setup</h1>
                        <div class="seprator-setup"></div>
                        <p>
                            We'll collect only the critical items needed to get your billing storted.More advanced setting can be controlled later.
                        </p>
                    </div>
                </div>
                <div class="col-md-8">             
                    <div class="setup-form">
                        <form method="post" id="user_setup_form">
                            <div class="important_field">
                                <div class="form-group">
                                    <label>Send Invoice As<span>*</span></label>
                                    <input type="text" id="send_invoice_as" name="send_invoice_as" class="form-control" placeholder="Acme Inc" autocomplete="off" autofocus>
                                </div>
                                <div class="form-group">
                                    <label for="addressHome">From Address<span>*</span><span id="h_cass_icon"></span></label>
                                    <textarea id="addressHome" name="setupAddressHome" autocomplete="off" class="form-control cass-address" rows="3" data-type="h" maxlength="150" placeholder="1423 Broadway St \nAnyplace, Fl 33600"></textarea>
                                    <div id="h_cass_error"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="addressMail">Mail Payment to Address<span id="m_cass_icon"></span></label>
                                <textarea id="setupAddressMail" name="setupAddressMail" autocomplete="off" class="form-control cass-address" rows="3" data-type="m" maxlength="150" placeholder="1423 Broadway St \nAnyplace, Fl 33600"></textarea>
                                <div id="m_cass_error"></div>
                            </div>

                            <div class="form-group">
                                <label>Phone for Customer to Contact</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_support"autocomplete="off"  placeholder="813-555-7362">
                            </div>

                            <div class="form-group">
                                <label>Email for Customer to Contact</label>
                                <input type="email" class="form-control" id="user_email_address" autocomplete="off" name="email_support" placeholder="support@acmeinc.com">
                            </div>
                            <div class="form-group">
                                <label>Your Name</label>
                                <input type="text" class="form-control" id="name_display" autocomplete="off" name="name_display" placeholder="John Smith">
                            </div>

                            <div class="setup-btn">
                                <button type="button" class="btn btn-primary" id="setup_btn_done"><i class="fas fa-play"></i>&nbsp;&nbsp;Done</button>
                                <a href="#" class="btn-text">Cancel</a> 
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php echo view('pub/common/footer_view'); ?>
<script src="<?php echo base_url(); ?>js/setup.js"></script>
