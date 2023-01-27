<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<section>
    <div class="main-title setup_custom">

        <div class="container">
            <div class="row">
                <div class="col-md-12">             
                    <div class="setup-form">
                        <form method="post" id="user_setup_form">
                            <div class="important_field">
                                <div class="form-group">
                                    <label>Send Invoice As<span>*</span></label>
                                    <input type="text" id="send_invoice_as" name="form[name]" class="form-control settings" placeholder="Acme Inc" autocomplete="off" autofocus value="<?= (isset($name) ? $name : "") ?>">
                                </div>
                                <?php
                                $address = (isset($address) ? $address : "") . (isset($city) ? "\n" . $city : "") . (isset($state) && $state_pay != "" ? ", " . $state : "") . (isset($zip) && $zip != 0 ? " " . $zip : "");
                                $mail_address = (isset($address_pay) ? $address_pay : "") . (isset($city_pay) ? "\n" . $city_pay : "") . (isset($state_pay) && $state_pay != "" ? ", " . $state_pay : "") . (isset($zip_pay) && $zip_pay != 0 ? " " . $zip_pay : "");
                                ?>
                                <div class="settings_address form-group">
                                    <label for="addressHome">From Address<span>*</span></label>
                                    <div class="position-relative">
                                        <textarea id="addressHome" name="form[form_address]" autocomplete="off" class="form-control cass-address settings" rows="3" data-type="h" maxlength="150" placeholder="1423 Broadway St \nAnyplace, Fl 33600"><?= (isset($address) ? $address : "") ?></textarea>
                                        <span id="h_cass_icon" class="loder_spiner_mail" data-toggle="tooltip" data-placement="top"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="addressMail">Mail Payment to Address</label>
                                <div class="position-relative">
                                    <textarea id="setupAddressMail" name="form[mail_address]" autocomplete="off" class="form-control cass-address settings" rows="3" data-type="m" maxlength="150" placeholder="1423 Broadway St \nAnyplace, Fl 33600"><?= (isset($mail_address) ? $mail_address : "") ?></textarea>
                                    <span id="m_cass_icon" class="loder_spiner_mail" data-toggle="tooltip" data-placement="top"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Phone for Customer to Contact</label>
                                <input type="text" class="form-control settings" id="phone_number" name="form[phone_support]"autocomplete="off"  placeholder="813-555-7362" value="<?= (isset($phone_support) ? $phone_support : "") ?>">
                            </div>

                            <div class="form-group">
                                <label>Email for Customer to Contact</label>
                                <input type="email" class="form-control settings" id="user_email_address" autocomplete="off" name="form[email_support]" placeholder="support@acmeinc.com" value="<?= (isset($email_support) ? $email_support : "") ?>">
                            </div>
                            <div class="form-group">
                                <div class="change_header">
                                    <label for="profileImage" class="col-sm-3 control-label pl-0">Logo Image</label>
                                    <button class="change_image" id="change_pimage" type="button"><i class="fas fa-edit"></i>&nbsp;Change Image</button>
                                </div>
                                <div class="col-sm-12 pl-0">
                                    <input type="hidden" value="" id="social_img_url" name="social_img_url">
                                    <input type="hidden" value="" id="profileImage_str" name="profileImage_str">
                                    <div class="thumbnail thumb-select" id="profile_current" style="width: 150px;height: auto;margin-bottom: 5px;overflow: hidden;">
                                        <label class="photo" for="profile_current" style="width: 100%;height: auto;overflow: hidden;">
                                            <?php
                                            if (isset($logo_seed) && $logo_seed == 0) {
                                                $src = "https://billex.s3.amazonaws.com/biller_image/biller-placeholder.png";
                                            } else {
                                                $src = "https://billex.s3.amazonaws.com/biller_image/" . $biller_id . "-" . base_convert($biller_id + $logo_seed, 10, 32) . ".jpg";
                                            }
                                            ?>
                                            <img src="<?= $src; ?>" id="current_image" style="width: 100%;">
                                        </label>
                                        <div class="profile-images__box_inner upload-progress" style="display: none;">
                                            <span><i class="fas fa-spinner fa-spin"></i> Please Wait.....</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 pl-0 imagebtn-hide">

                                    <input type="hidden" role="uploadcare-uploader" name="content" data-images-only data-crop="">

                                </div>
                            </div>
                            <div class="form-group">
                                <div><label>Does logo image include return address?</label></div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="customRadioInline1" name="form[hide_address]" class="custom-control-input change_itemd" value="Yes" <?= ($hide_address == 1 ? "checked" : "") ?>>
                                    <label class="custom-control-label" for="customRadioInline1">Yes</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="customRadioInline2" name="form[hide_address]" class="custom-control-input change_itemd" value="No" <?= ($hide_address == 0 ? "checked" : "") ?>>
                                    <label class="custom-control-label" for="customRadioInline2">No</label>
                                </div>
                            </div>
                            <div class="setup-btn update_customer_btn">
                                <button type="button" class="btn btn-submit update_customer" onclick="edit_settings();" style="display:none;">Save Changes<i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>
                                <a href="#" class="btn cancle_form update_customer" style="display: none;" onclick="resetForm('user_setup_form');">Cancel</a>
                            </div>
                        </form>
                        <div class="connect_stripe_div">
                            <p>Accept online payments</p>
                        </div>
                        <div>
                            <a href="https://dashboard.stripe.com/express/oauth/authorize?response_type=code&client_id=<?php echo env("stripe.client_id"); ?>&scope=read_write" class="connect-button"><span>Connect with Stripe</span></a>
                            <?= (isset($processor) && $processor == "stripe" ? "(Connected)" : ""); ?>
                        </div>
                        <div class="payment_connect_option">Or</div>
                        <div>
                            <a href="https://stage.wepay.com/v2/oauth2/authorize?client_id=59962&redirect_uri=http%3A%2F%2Fhitesh.dev.billex.net%2Faut%2Fadministration%2Fsettings%3Fwepay%3D1&scope=manage_accounts%2Ccollect_payments%2Cview_user%2Csend_money%2Cpreapprove_payments%2Cmanage_subscriptions" class="connect-wepay"><span>Connect with Wepay</span></a>
                            <?= (isset($processor) && $processor == "wepay" ? "(Connected)" : ""); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="https://ucarecdn.com/libs/widget/3.x/uploadcare.full.min.js"></script>
<script src="<?php echo base_url(); ?>js/administration.js"></script>
<script>
                                    var textAreas = document.getElementsByTagName('textarea');
                                    Array.prototype.forEach.call(textAreas, function (elem) {
                                        elem.placeholder = elem.placeholder.replace(/\\n/g, '\n');
                                    });
                                    $("#phone_number").inputmask({"mask": "999-999-9999"});
                                    UPLOADCARE_PUBLIC_KEY = 'ab84bc0ba2a1c3982f1b';
                                    UPLOADCARE_LOCAL = 'en';
//                                    UPLOADCARE_TABS = 'file camera url';
                                    UPLOADCARE_IMAGES_ONLY = true;
                                    UPLOADCARE_PREVIEW_STEP = true;

                                    var widget = uploadcare.Widget('[role=uploadcare-uploader]');
                                    widget.onChange(function (file) {
                                        if (file) {
                                            file.progress(function (fileInfo) {
                                                setTimeout(function () {
                                                    $(".update_customer").show();
                                                    $(".no_allform").css('visibility', 'hidden');
                                                }, 500);
                                            });
                                        }
                                        ;
                                    });
                                    widget.onUploadComplete(function (info) {
                                        $("#current_image").attr('src', info.cdnUrl);
                                        $("#social_img_url").val("" + info.cdnUrl);
                                    });
                                    var new_url = BASE_URL + "aut/administration/settings";
                                    window.history.pushState("data", "Title", new_url);
</script>