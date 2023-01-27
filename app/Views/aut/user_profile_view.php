<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="personal-info-box-main">
                <h4>Personal information</h4>
                <div class="personal-form-box">
                    <form id="user_profile_form" method="post" >
                        <div class="form-group position-relative mb-0">
                            <label>First Name</label>
                            <input type="text" class="form-control name_edit" name="name_first" data-type="first" placeholder="Enter First Name" value="<?= $user_data["name_first"]; ?>">
                            <a href="#" class="sucess_tick text-success" style="display: none;"><i class="fas fa-check"></i></a>
                            <span class="success first_user_message" style="visibility: hidden;">First name successfully updated.</span>
                        </div>
                        <div class="form-group position-relative mb-0">
                            <label>Last Name</label>
                            <input type="text" class="form-control name_edit" name="name_last" data-type="last" placeholder="Enter Last Name" value="<?= $user_data["name_last"]; ?>">
                            <a href="#" class="sucess_tick text-success" style="display: none;"><i class="fas fa-check"></i></a>
                            <span class="success last_user_message" style="visibility: hidden;">Last name successfully updated.</span>
                        </div>
                        <?php
                        $phone = "";
                        $email_address = "";
                        foreach ($user_aut_data as $user_aut_value) {
                            if ($user_aut_value["type"] == "phone") {
                                $phone = $user_aut_value["value"];
                            }
                            if ($user_aut_value["type"] == "email") {
                                $email_address = $user_aut_value["value"];
                            }
                            if ($user_aut_value["type"] == "google") {
                                $google_id = $user_aut_value["value"];
                            }
                        }
                        ?>
                        <label>Email</label>
                        <p id="email_error_message" class="email_error_message"></p>
                        <div class="input-group form-group" id="email_send">
                            <input type="email" id="user_email_address" <?php echo($email_address == "" ? "" : "readonly"); ?> name="user_email_address" class="form-control" placeholder="Enter Email" value="<?= $email_address; ?>">
                            <?php
                            if ($email_address != "") {
                                ?>
                                <div class="input-group-append">
                                    <button class="btn btn_validate" id="email_btn_validate" type="button" onclick="unlock('email')"><i class="fas fa-pencil-alt"></i></button>
                                </div>
                            <?php } ?>
                        </div>
                        <?php
                        if ($email_address == "") {
                            echo '<button class="btn sendcode_email email_login_code" type="button" onclick="email_validate();" style="display:none;">send code</button>';
                        }
                        ?>
                        <div class="email_otp_verification">
                            <p class="email_error_massage"></p>
                        </div>
                        <label>Phone</label>
                        <p id="phone_error_messagep" class="email_error_message"></p>
                        <div class="input-group form-group" id="phone_send">
                            <input type="tel" name="user_phone_number"  <?php echo($phone == "" ? "" : "readonly"); ?> value="<?php echo $phone; ?>" placeholder="Enter Phone Number" id="user_phone_number" class="form-control" type="tel" pattern="^\d{4}-\d{3}-\d{4}$" required>
                            <?php
                            if ($phone != "") {
                                ?>
                                <div class="input-group-append">
                                    <button class="btn btn_validate" id="phone_btn_validate" type="button" onclick="unlock('phone')"><i class="fas fa-pencil-alt"></i></button>
                                </div>
                            <?php } ?>
                        </div>
                        <?php
                        if ($phone == "") {
                            echo '<button class="btn sendcode_email phone_login_code" type="button" onclick="phone_validate();" style="display:none;">send code</button>';
                        }
                        ?>
                        <div class="phone_otp_verification">
                            <p class="phone_error_massage"></p>
                        </div>
                        <div class="form-group mb-0">
                            <label>Time Zone</label>
                            <div class="custom-dropdown big type_select_option" style="width: 100%;">
                                <select class="selectpicker" data-live-search="true"  data-width="100%" title="Choose one of the timezone" onchange="set_time_zone(this)" id="timezone_listing">
                                    <option value="-180" <?php echo($user_data["utc_offset"] == "-180" ? "selected" : ""); ?>>Atlantic Time UTC -3</option>
                                    <option value="-240" <?php echo($user_data["utc_offset"] == "-240" ? "selected" : ""); ?>>Eastern Time UTC -4</option>
                                    <option value="-300" <?php echo($user_data["utc_offset"] == "-300" ? "selected" : ""); ?>>Central Time UTC -5</option>
                                    <option value="-360" <?php echo($user_data["utc_offset"] == "-360" ? "selected" : ""); ?>>Mountain Time UTC -6</option>
                                    <option value="-420" <?php echo($user_data["utc_offset"] == "-420" ? "selected" : ""); ?>>Pacific Time UTC -7</option>
                                    <option value="-480" <?php echo($user_data["utc_offset"] == "-480" ? "selected" : ""); ?>>Alaska Time UTC -8</option>
                                    <option value="-540" <?php echo($user_data["utc_offset"] == "-540" ? "selected" : ""); ?>>Hawaii Time UTC -9</option>
                                </select>
                            </div>
                            <span class="success timezone_message" style="visibility: hidden;">Time Zone successfully updated.</span>
                        </div>
                        <div class="form-group mb-0">
                            <label>Pay Periods</label>
                            <input type="hidden" value="<?php echo($user_data["pay_period_start"]); ?>" data-type="<?php echo($user_data["pay_period"]); ?>" class="start_value">
                            <div class="custom-dropdown big type_select_option" style="width: 100%;">
                                <select class="selectpicker" data-live-search="false"  data-width="100%" title="Choose one of the pay periods" onchange="set_pay_periods(this)" id="payperiods_listing">
                                    <option value="monthly" <?php echo($user_data["pay_period"] == "monthly" ? "selected" : ""); ?>>Monthly</option>
                                    <option value="weekly" <?php echo($user_data["pay_period"] == "weekly" ? "selected" : ""); ?>>Weekly</option>
                                    <option value="bi-weekly" <?php echo($user_data["pay_period"] == "bi-weekly" ? "selected" : ""); ?>>Bi-weekly</option>
                                    <option value="semi-monthly" <?php echo($user_data["pay_period"] == "semi-monthly" ? "selected" : ""); ?>>Semi-monthly</option>
                                </select>
                            </div>
                            <span class="success payperiods_message" style="visibility: hidden;">Pay Periods successfully updated.</span>
                        </div>
                        <div class="form-group mb-0">
                            <label class="value_label">Start Day</label>
                            <div class="start_value_type">
                                <input class="form-control start_value_number" type="number" min="1" max="28" autocomplete="off" id="time_start"/>
                            </div>
                            <span class="success startvalue_message semi_monthly_message" style="visibility: hidden;">Start Value successfully updated.</span>
                        </div>
                        <div class="form-group" style="display: none;">
                            <label>Location</label>
                            <div class="location">
                                <p>1423 Broadway , NY 10018</p>
                                <span><a href="#"><i class="fas fa-edit"></i>&nbsp;Change Location</a></span>
                            </div>
                        </div>
                        <div class="form-group" style="display: none;">
                            <label>Photo</label>
                            <div class="profile-img mb-3"></div>
                            <a href="#"><i class="fas fa-edit"></i>&nbsp;Change Image</a>
                        </div>
                        <div class="form-group">
                            <div class="change_header">
                                <label for="profileImage" class="col-sm-3 control-label pl-0">Photo</label>
                                <button class="change_image" id="change_pimage" type="button"><i class="fas fa-edit"></i>&nbsp;Change Image</button>
                            </div>
                            <div class="col-sm-12 pl-0">
                                <input type="hidden" value="" id="social_img_url" name="social_img_url">
                                <input type="hidden" value="" id="profileImage_str" name="profileImage_str">
                                <div class="thumbnail thumb-select" id="profile_current" style="width: 150px;height: 150px;margin-bottom: 5px;overflow: hidden;">
                                    <label class="photo" for="profile_current" style="width: 100%;height: 150px;overflow: hidden;">
                                        <img src="<?= $_SESSION['profileImage']; ?>" id="current_image" style="width: 100%;">
                                    </label>
                                    <div class="profile-images__box_inner upload-progress" style="display: none;">
                                        <span><i class="fas fa-spinner fa-spin"></i> Please Wait.....</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 pl-0">

                                <input type="hidden" role="uploadcare-uploader" name="content" data-images-only data-crop="128x128 upscale">

                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <form method="post">
                <div class="personal-info-box-main">
                    <div class="change_password_header">
                        <div><h4>Change Password</h4></div>
                        <div>
                            <label class="checkbox_containers">Hide Text
                                <input type="checkbox" id="hide_password_checkbox">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                    <p id="result" class="email_error_message"></p>
                    <div class="password-form">
                        <div class="form-group">
                            <label>Password</label>
                            <input type="text" id="passwordInput" class="form-control result" placeholder="Enter password name" autocomplete="off" name="fjdghvbshjdfv">
                        </div>
                        <div class="progress mb-2" id="password_check_progress" style="display: none;">
                            <div id="StrengthProgressBar" class="progress-bar"></div>
                        </div>
                        <div class="form-group" id="confirm_password_box" style="display: none;">
                            <label>Password Confirmation</label>
                            <input type="password" id="confirmPasswordInput" class="form-control"  placeholder="Enter password confirmation">
                        </div>
                        <button type="button" class="btn btn-submit" onclick="change_password();">Change Password</button>
                    </div>
                </div>
            </form>

            <div class="personal-info-box-main mt-4">
                <h4>Third Party Connection</h4>
                <div class="thirdparty-form">
                    <div class="t-main mt-4" style="display: none;">
                        <div class="text-div">
                            <p class="fb-conncet"><i class="fab fa-facebook-square"></i> Your Account not link with facebook.</p>
                        </div>
                        <div class="btn-div">
                            <button class="btn btn-success">Connect</button>
                        </div>
                    </div>
                    <div class="t-main mt-4">
                        <div class="text-div">
                            <p class="fb-conncet"><i class="fab fa-google"></i> Your Account link with Google.</p>
                        </div>
                        <div class="btn-div">
                            <?php
                            if (isset($google_id) && !empty($google_id)) {
                                echo '<button class="btn btn-warning" onclick="disconnect_google()">Disconnect</button>';
                            } else {
                                echo '<a href="https://accounts.google.com/o/oauth2/auth?response_type=code&amp;redirect_uri=' . base_url() . 'pub/googleAuth/googleconnect&amp;client_id=' . env("google.client_id") . '&amp;scope=https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile &amp;access_type=online&amp;approval_prompt=auto" class="btn btn-success">Connect</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="https://ucarecdn.com/libs/widget/3.x/uploadcare.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/1.0/zxcvbn.min.js"></script>
<script src="<?php echo base_url(); ?>js/passwordStrength.js"></script>
<script src="<?php echo base_url(); ?>js/user_profile.js"></script>