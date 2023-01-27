
<?php
$page_title = "Login";
echo view('pub/common/header_view');
?>
<style>
    .billex_body{
        background-color: #f4f8fb;
    }
    .laptop_first{
        order: 1;
        display: flex;
        align-items: center;
    }
    .laptop_last{
        order: 12;
    }
    .top-bar .contact-info {
        text-align: right;
        line-height: 42px;
    }
    @media only screen and (min-width: 320px) and (max-width: 575px) {
        .laptop_first {
            padding-left: 5px;
            padding-right: 5px;
            width: 25%;
        }
        .laptop_first img {
            width: 100px;
        }
        .laptop_last{
            padding-left: 0;
            padding-right: 0;
        }
        .signup_signin_header{
            flex-wrap: unset;
            align-items: center;
        }
        .top-bar .contact-info span a {
            font-size: 12px;
        }
    }
    @media only screen and (min-width: 576px) and (max-width: 767px) {
        .laptop_first {
            padding-left: 5px;
            padding-right: 5px;
            width: 25%;
        }
        .laptop_first img {
            width: 100px;
        }
        .laptop_last{
            padding-left: 0;
            padding-right: 0;
        }
        
        .signup_signin_header{
            flex-wrap: unset;
            align-items: center;
        }
        .top-bar .contact-info span a {
            font-size: 15px;
        }
    }
</style>
<section>
    <div class="main-section main-title">
        <div class="container">
            <div class="row user_login_row">
                <div class="col-sm-6 sign_up_div sign_up_div2">
                    <div class="in_mobile_title">
                        <h2>User Sign In</h2>
                        <div class="seprator"></div>
                    </div>
                    <div class="second-title">
                        <h6>Choose how whould you like to login to the services</h6>
                        <div class="info-main-box">
                            <div class="info-box-login">
                                <a href="https://accounts.google.com/o/oauth2/auth?response_type=code&amp;redirect_uri=<?= base_url(); ?>pub/googleAuth/googlelogin&amp;client_id=<?= env("google.client_id"); ?>&amp;scope=https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile &amp;access_type=online&amp;approval_prompt=auto"  class="info-text signin_google">
                                    <i class="fab fa-google signin_ico"></i><span>Sign in with Google</span></a>
                            </div>
                            <div class="info-box-login">
                                <a href="#" id="email-box" class="info-text signin_email"><i class="fas fa-envelope signin_ico"></i><span>Sign in with Email</span></a>

                                <div class="phone-div-main" id="form-email">
                                    <span id="error_massage"></span>
                                    <p id="email_error_message" class="email_error_message"></p>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text color-white"><i class="far fa-envelope"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Enter Email Address" id="user_email_address" name="username">
                                    </div>

                                    <div class="sendcode-div mb-3">
                                        <div class="btn-sendcode">
                                            <button class="btn sendcode email_login_code" onclick="email_send_code();">Send Code</button>
                                        </div>
                                        <div class="or">
                                            <p>OR</p>
                                        </div>
                                        <div class="enterpassword-link">
                                            <button class="btn-enterpassword email_login_password" onclick="user_check();">Enter Password</button>
                                        </div>
                                    </div>
                                    <span class="email_code_password_lable"></span>
                                    <div class="input-group flex-nowrap email_add_password" style="display: none;">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text color-white" id="addon-wrapping"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="hidden" class="verify_id">
                                        <input type="text" class="form-control email_code_pasword">
                                        <div class="input-group-append">
                                            <button class="btn btn-success" type="button" id="btn_code_password">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="info-box-login">
                                <a href="#" id="phone-box" class="info-text signin_phone"><i class="fas fa-phone signin_ico"></i><span>Sign in with Phone</span></a>
                                <div class="phone-div-main" id="form-phone">
                                    <span id="error_massage"></span>
                                    <p id="phone_error_message" class="email_error_message"></p>

                                    <div class="phone-box">

                                        <div class="phone_otp_verification"></div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text color-white"><i class="fas fa-phone"></i></span>
                                            </div>
                                            <input type="tel" name="user_phone_number" value="" placeholder="Enter Phone Number" id="phone" class="form-control" type="tel" pattern="^\d{4}-\d{3}-\d{4}$" required>
                                        </div>

                                    </div>
                                    <div class="sendcode-div mb-3">
                                        <div class="btn-sendcode">
                                            <button class="btn sendcode phone_login_code" onclick="phone_code_verify()">Send Code</button>
                                        </div>
                                        <div class="or">
                                            <p>OR</p>
                                        </div>
                                        <div class="enterpassword-link">
                                            <button class="btn-enterpassword phone_login_password" onclick="phone_user_check()">Enter Password</button>
                                        </div>
                                    </div>
                                    <span class="phone_code_password_lable"></span>
                                    <div class="input-group flex-nowrap phone_add_password" style="display: none;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text color-white" id="addon-wrapping"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="hidden" class="verify_id">
                                        <input type="text" class="form-control phone_code_pasword">
                                        <div class="input-group-append">
                                            <button class="btn btn-success" type="button" id="phone_btn_code_password">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo view('pub/common/footer_view'); ?>
<script type="text/javascript">
    $(document).ready(function () {

        $(document).on("click", "#phone-box", function () {
            $('#form-email').slideUp();
            $('#form-phone').slideDown();
            $("#phone").focus();
        });

        $(document).on("click", "#email-box", function () {
            $('#form-email').slideDown();
            $('#form-phone').slideUp();
            $("#user_email_id").focus();
        });
        // $(document).on("click", "#btn-email", function () {
        //     $('#form-email').slideUp();
        // });
        $(document).on("click", "#dropdownMenuButton", function () {

            $('#form-phone').slideUp();
            $('#form-email').slideUp();
        });
        $(":input").inputmask();

        $("#phone").inputmask({"mask": "999-999-9999"});
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            $(".basic_billing").hide();
        }
<?php
if (isset($_REQUEST["biller_status"]) && !empty($_REQUEST["biller_status"]) && decrypt($_REQUEST["biller_status"]) == 1) {
    ?>
            bootbox.confirm({
                closeButton: false,
                className: "sign_up_alert",
                backdrop: true,
                onEscape: true,
                message: "This is your first time signing in and you have not created an account. Would you like to get started with a free account so that you can bill your customers?",
                buttons: {
                    confirm: {
                        label: 'Yes',
                        className: 'btn-primary bootbox-ok-button'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        window.location.href = BASE_URL + "pub/setup";
                    } else {
                        window.location.href = BASE_URL + "logout";

                    }
                }
            });
    <?php
}
?>
    });

    $(document).on("click", ".email_login_code", function () {
        $(".email_code_pasword").attr("id", "email_code");
        $(".email_code_pasword").attr("type", "tel");
        $(".email_code_pasword").attr("placeholder", "Enter Code");
    });
    $(document).on("click", ".email_login_password", function () {
        $(".email_code_pasword").attr("type", "password");
        $(".email_code_pasword").attr("id", "email_password");
        $(".email_code_pasword").attr("placeholder", "Enter Password");

    });
    $(document).on("click", ".phone_login_code", function () {
        $(".phone_code_pasword").attr("type", "tel");
        $(".phone_code_pasword").attr("id", "email_code");
        $(".phone_code_pasword").attr("placeholder", "Enter Code");

    });
    $(document).on("click", ".phone_login_password", function () {
        $(".phone_code_pasword").attr("type", "password");
        $(".phone_code_pasword").attr("id", "email_password");
        $(".phone_code_pasword").attr("placeholder", "Enter Password");
    });
    function IsEmail(email) {
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!regex.test(email)) {
            return false;
        } else {
            return true;
        }
    }
    function email_send_code() {
        var email = $("#user_email_address").val();
        if (IsEmail(email) == false) {
            $("#email_error_message").text("Please enter a valid Email-id.");
            return false;
        }
        if (email == "" || email == null)
        {
            $("#user_email_address").addClass("error_color");
            $("#form-email").css("display", "block");
        } else
        {
            $.ajax({
                url: BASE_URL + "pub/start/email_login",
                type: "POST",
                data: {"user_email_address": $("#user_email_address").val()},
                dataType: "JSON",
                beforeSend: function () {
                    $("#btn-email").attr("disabled", true);
                },
                success: function (data)
                {
                    $(".email_error_message").text("");
                    $("#btn-email").attr("disabled", false);
                    $("#user_email_address").removeClass("error_color");
                    if (data.status == "success") {
                        $('#user_email_address').attr('readonly', true);
                        $(".email_login_code").attr("disabled", true);
                        $('#form-phone').slideUp();
                        $("#user_verification_code").focus();
                        $(".email_add_password").show();
                        $(".email_code_password_lable").text("Enter Code");
                        $(".verify_id").val(data.verify_id);
                        $("#btn_code_password").attr('onclick', 'code_verify();');
                    } else {
                        $("#email_error_message").text("Not Register Email Id.");
                        $("#btn-email").attr("disabled", false);
                        $("#user_email_address").removeClass("error_color");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#btn-email").attr("disabled", false);
                    alert('Error adding / update data');
                }
            });
        }
    }
    function code_verify() {
        if ($(".verify_id").val() != "") {
            id = "email_code";
        } else {
            id = "email_password";
        }
        $.ajax({
            url: BASE_URL + "pub/start/verify",
            type: "POST",
            data: {"user_verification_code": $("#" + id).val(), "verify_id": $(".verify_id").val()},
            dataType: "JSON",
            success: function (data)
            {

                var status = data.status;
                if (status == "Success") {
                    if (data.biller_status == 1) {
<?php
if (isset($_REQUEST["redirect_url"]) && !empty($_REQUEST["redirect_url"])) {
    ?>
                            window.location.href = "<?php echo $_REQUEST["redirect_url"] ?>";
    <?php
} else {
    ?>
                            window.location.href = BASE_URL + "aut/dashboard";
    <?php
}
?>
                    } else {
                        bootbox.confirm({
                            closeButton: false,
                            className: "sign_up_alert",
                            backdrop: true,
                            onEscape: true,
                            message: "This is your first time signing in and you have not created an account. Would you like to get started with a free account so that you can bill your customers?",
                            buttons: {
                                confirm: {
                                    label: 'Yes',
                                    className: 'btn-primary bootbox-ok-button'
                                },
                                cancel: {
                                    label: 'No',
                                    className: 'btn-danger'
                                }
                            },
                            callback: function (result) {
                                if (result) {
                                    window.location.href = BASE_URL + "pub/setup";
                                } else {
                                    window.location.href = BASE_URL + "logout";
                                }
                            }
                        });
                    }
                } else {
                    $("#email_error_message").text("Verification code is invalid.");
                    $("#phone_error_message").text("Verification code is invalid.");
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
            }
        });
    }
    function user_check() {
        var email = $("#user_email_address").val();
        if (IsEmail(email) == false) {
            $("#email_error_message").text("Please enter a valid Email-id.");
            return false;
        }
        if (email == "" || email == null)
        {
            $("#user_email_address").addClass("error_color");
            $("#form-email").css("display", "block");
        } else
        {
            $.ajax({
                url: BASE_URL + "pub/start/user_check",
                type: "POST",
                data: {"user_email_address": $("#user_email_address").val(), "phone": ""},
                dataType: "JSON",
                beforeSend: function () {
                    $("#btn-email").attr("disabled", true);
                },
                success: function (data)
                {
                    $(".email_error_message").text("");
                    $("#btn-email").attr("disabled", false);
                    $("#user_email_address").removeClass("error_color");
                    if (data.status == "success") {
                        $('#user_email_address').attr('readonly', true);
                        $(".email_login_code").attr("disabled", true);
                        $('#form-phone').slideUp();
                        $("#user_verification_code").focus();
                        $(".email_add_password").show();
                        $(".email_code_password_lable").text("Enter Password");
                        $("#btn_code_password").attr('onclick', 'password_verification();');
                    } else {
                        $("#email_error_message").text("Not Register Email Id.");
                        $("#btn-email").attr("disabled", false);
                        $("#user_email_address").removeClass("error_color");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#btn-email").attr("disabled", false);
                    alert('Error adding / update data');
                }
            });
        }
    }
    function password_verification() {
        $.ajax({
            url: BASE_URL + "pub/start/password_verification",
            type: "POST",
            data: {"password": $("#email_password").val()},
            dataType: "JSON",
            beforeSend: function () {
                $("#btn-email").attr("disabled", true);
            },
            success: function (data)
            {
                $(".email_error_message").text("");
                $("#btn-email").attr("disabled", false);
                $("#user_email_address").removeClass("error_color");
                if (data.status == "success") {
                    if (data.biller_status == 1) {
<?php
if (isset($_REQUEST["redirect_url"]) && !empty($_REQUEST["redirect_url"])) {
    ?>
                            window.location.href = "<?php echo $_REQUEST["redirect_url"] ?>";
    <?php
} else {
    ?>
                            window.location.href = BASE_URL + "aut/dashboard";
    <?php
}
?>
                    } else {
                        bootbox.confirm({
                            closeButton: false,
                            className: "sign_up_alert",
                            backdrop: true,
                            onEscape: true,
                            message: "This is your first time signing in and you have not created an account. Would you like to get started with a free account so that you can bill your customers?",
                            buttons: {
                                confirm: {
                                    label: 'Yes',
                                    className: 'btn-primary bootbox-ok-button'
                                },
                                cancel: {
                                    label: 'No',
                                    className: 'btn-danger'
                                }
                            },
                            callback: function (result) {
                                if (result) {
                                    window.location.href = BASE_URL + "pub/setup";
                                } else {
                                    window.location.href = BASE_URL + "logout";
                                }
                            }
                        });
                    }

                } else {
                    $("#email_error_message").text("Password is invalid.");
                    $("#phone_error_message").text("Password is invalid.");
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $("#btn-email").attr("disabled", false);
                alert('Error adding / update data');
            }
        });
    }
    function phone_code_verify() {
        var phone = $("#phone").val();
        str1 = phone.replace(/[^\d.]/g, '');
        total = parseInt(str1, 10);

        intRegex = /[0-9 -()+]+$/;
        if ((phone.length < 6) || (!intRegex.test(phone)))
        {
            $("#phone_error_message").text("Please enter a valid phone number.");
            $("#phone").focus();
            return false;

        }
        if (phone == "" || phone == null)
        {
            $("#phone").addClass("error_color");
            $("#form-phone").css("display", "block");
        } else
        {
            $.ajax({
                url: BASE_URL + "pub/start/phone_login",
                type: "POST",
                data: {"user_phone_number": total},
                dataType: "JSON",
                beforeSend: function () {
                    $("#btn-phone").attr("disabled", true);
                },
                success: function (data)
                {
                    //$(".phone_error_message").text("");
                    $("#btn-phone").attr("disabled", false);
                    $("#phone").removeClass("error_color");


                    if (data.status == "success") {
                        $(".phone_login_code").attr("disabled", true);
                        $('#phone').attr('readonly', true);
                        $(".phone_code_password_lable").text("Enter Code");
                        $("#phone_btn_code_password").attr('onclick', 'code_verify();');
                        $('#form-email').slideUp();
                        $(".phone_code_pasword").focus();
                        $(".phone_add_password").show();
                        $(".verify_id").val(data.verify_id);
                    } else {
                        $("#phone_error_message").text("Not Register Phone.");
                        $("#btn-email").attr("disabled", false);
                        $("#user_email_address").removeClass("error_color");
                        $("#phone").focus();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#btn-phone").attr("disabled", false);
                    alert('Error adding / update data');
                }
            });
        }
    }
    function phone_user_check() {
        var phone = $("#phone").val();
        str1 = phone.replace(/[^\d.]/g, '');
        total = parseInt(str1, 10);

        intRegex = /[0-9 -()+]+$/;
        if ((phone.length < 6) || (!intRegex.test(phone)))
        {
            $("#phone_error_message").text("Please enter a valid phone number.");
            $("#phone").focus();
            return false;

        }
        if (phone == "" || phone == null)
        {
            $("#phone").addClass("error_color");
            $("#form-phone").css("display", "block");
        } else
        {
            $.ajax({
                url: BASE_URL + "pub/start/user_check",
                type: "POST",
                data: {"phone": total, "user_email_address": ""},
                dataType: "JSON",
                beforeSend: function () {
                    $("#btn-email").attr("disabled", true);
                },
                success: function (data)
                {
                    $(".email_error_message").text("");
                    $("#btn-email").attr("disabled", false);
                    $("#user_email_address").removeClass("error_color");
                    if (data.status == "success") {
                        $('#user_email_address').attr('readonly', true);
                        $(".email_login_code").attr("disabled", true);
                        $("#email_password").focus();
                        $(".phone_add_password").show();
                        $(".email_code_password_lable").text("Enter Password");
                        $("#phone_btn_code_password").attr('onclick', 'password_verification();');
                    } else {
                        $("#phone_error_message").text("Not Register phone.");
                        $("#btn-email").attr("disabled", false);
                        $("#phone").focus();
                        $("#user_email_address").removeClass("error_color");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#btn-email").attr("disabled", false);
                    alert('Error adding / update data');
                }
            });
        }
    }
</script>