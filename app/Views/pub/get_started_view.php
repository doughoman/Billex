<?php
$page_title = "getStarted";
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
        .top-bar .contact-info {
            text-align: right;
            margin-right: 10px;
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
        .top-bar .contact-info {
            text-align: right;
            margin-right: 10px;
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
            <div class="row">
                <div class="sign_up_div col-sm-6 mob_last">
                    <div class="get-started-div">
                        <div class="no_mob">
                            <h1>Get Started</h1>
                            <div class="get_seprator"></div>
                        </div>
                        <p class="no_payment">
                            No payment information is required to use our basic services. Add payment info later when you decide to update to premium features.
                        </p>
                        <p class="no_mob">
                            Now let's set up you login and basic billing.
                        </p>
                    </div>
                </div>
                <div class="col-sm-6 sign_up_div mob_first sign_up_div2">
                    <div class="no_desktop get_startdiv">
                        <h1>Get Started</h1>
                        <div class="get_seprator"></div>
                    </div>
                    <div class="second-title">
                        <!--<h2>User Sign Up</h2>-->
                        <h6>Choose how whould you like to login to the services</h6>
                        <div class="info-main-box">
                            <div class="info-box">
                                <a href="https://accounts.google.com/o/oauth2/auth?response_type=code&amp;redirect_uri=<?= base_url(); ?>pub/googleAuth/oauth2callback&amp;client_id=<?= env("google.client_id"); ?>&amp;scope=https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile &amp;access_type=online&amp;approval_prompt=auto"
                                   class="info-text signin_google"><i class="fab fa-google"></i><span>Sign in with Google</span></a>
                            </div>
                            <div class="info-box">
                                <a href="#" id="email-box" class="info-text signin_email"><i class="fas fa-envelope"></i><span>Sign in with Email</span></a>
                                <div class="phone-div-main" id="form-email">

                                    <p id="email_error_message" class="email_error_message"></p>
                                    <div class="phone-box">

                                        <label>Your Email Address</label>
                                        <input type="email" name="user_email_address" id="user_email_address" value="" placeholder="Enter Email Address" class="form-control">

                                        <div class="email_otp_verification"></div>
                                        <input type="button" name="" value="Submit" onclick="user_reg_mail();" id="btn-email" class="btn btn-get-started">
                                    </div>
                                </div>
                            </div>
                            <div class="info-box">
                                <a href="#" id="phone-box" class="info-text signin_phone"><i class="fas fa-phone"></i><span>Sign in with Phone</span></a>
                                <div class="phone-div-main" id="form-phone">

                                    <p id="phone_error_messagep" class="phone_error_message"></p>

                                    <div class="phone-box">


                                        <label>Your Phone Number</label>
                                        <input type="tel" name="user_phone_number" value="" placeholder="Enter Phone Number" id="phone" class="form-control" type="tel" pattern="^\d{4}-\d{3}-\d{4}$" required>
                                        <div class="phone_otp_verification"></div>
                                        <input type="button" name="" onclick="user_reg_phone();" value="Submit" id="btn-phone" class="btn btn-get-started">
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
            $("#phone_display_name").focus();
        });

        $(document).on("click", "#email-box", function () {
            $('#form-email').slideDown();
            $('#form-phone').slideUp();
            $("#email_display_name").focus();
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
    });
    var uid;
    function otp_verify() {
        // ajax adding data to database
        if ($('.select_type').val() == "email") {
            form = "email_verification_form";
        } else {
            form = "phone_verification_form";
        }
        $.ajax({
            url: BASE_URL + "pub/start/verify",
            type: "POST",
            data: $('#' + form).serialize(),
            dataType: "JSON",
            success: function (data)
            {

                var status = data.status;
                if (status == "Success") {

                    //$(".error_massage").text("User Verification Successfully.");
                    //$(".error_massage_div").addClass("alert_success");
                    //$(".error_massage_div").removeClass("alert_error");
                    //$(".phone_otp_verification").css('margin-top', '15px');
                    //$(".email_otp_verification").css('margin-top', '15px');
                    if ($("#user_allready").val() == 1) {
                        bootbox.confirm({
                            closeButton: false,
                            className: "sign_up_alert",
                            backdrop: true,
                            onEscape: true,
                            message: "Your " + $('.select_type').val() + " already has an account so we are logging you into it.",
                            buttons: {
                                confirm: {
                                    label: 'Ok',
                                    className: 'btn-primary bootbox-ok-button'
                                },
                                cancel: {
                                    label: 'No',
                                    className: 'btn-danger bootbox-cancle-button'
                                }
                            },
                            callback: function (result) {
                                if (result) {
                                    window.location.href = BASE_URL + "aut/dashboard";
                                } else {
                                    location.reload();
                                }
                            }
                        });

                    } else {
                        window.location.href = BASE_URL + "pub/setup";
                    }

                } else {
                    $(".error_massage").text("Enter the code sent to your " + $('.select_type').val() + ".");
                    $(".phone_otp_verification").css('margin-top', '15px');
                    $(".email_otp_verification").css('margin-top', '15px');
                    $(".error_massage_div").addClass("alert_error");
                    $(".error_massage_div").removeClass("alert_success");
                }

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
            }
        });
    }

    function IsEmail(email) {
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!regex.test(email)) {
            return false;
        } else {
            return true;
        }
    }

    function user_reg_mail() {

        var email = $("#user_email_address").val();
        if (IsEmail(email) == false) {
            $("#email_error_message").text("Please enter a valid email address.");
            return false;
        }
        if (email == "" || email == null)
        {
            $("#user_email_address").addClass("error_color");
            $("#form-email").css("display", "block");
        } else
        {
            $.ajax({
                url: BASE_URL + "pub/start/registration_email",
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
                    //$('#form-email').slideUp();
                    $("#user_email_address").removeClass("error_color");
                    if (data.status == "success") {
                        //$('#verification_modal').modal('show');
                        $('#user_email_address').attr('readonly', true);
                        $('#email_display_name').attr('readonly', true);
                        var html = '<div class="error_massage_div"><p class="error_massage"></p></div><form action="" method="post" id="email_verification_form">' +
                                '<input type="hidden" name="verify_id" value="' + data.verify_id + '">' +
                                '<input type="hidden" class="select_type" value="email">' +
                                '<input type="hidden" name="user_allready" id="user_allready" value="' + data.allready + '">' +
                                '<div class="input-group mb-3">' +
                                '<input type="number" maxlength="5"  pattern="\d{3}[\-]\d{3}[\-]\d{4}" name="user_verification_code" placeholder="Verification code" id="user_verification_code" class="form-control">' +
                                '</div>' +
                                '</form>';
                        $('.email_otp_verification').append(html);
                        $("#btn-email").attr('onclick', 'otp_verify();');
                        $('#form-phone').slideUp();
                        //$('#form-email').slideUp();
                        $("#user_verification_code").focus();
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
    function user_reg_phone() {

        var phone = $("#phone").val();
        str1 = phone.replace(/[^\d.]/g, '');
        total = parseInt(str1, 10);

        intRegex = /[0-9 -()+]+$/;
        if ((phone.length < 6) || (!intRegex.test(phone)))
        {
            $("#phone_error_messagep").text("Please enter a valid phone number.");
            return false;

        }
        if (phone == "" || phone == null)
        {
            $("#phone").addClass("error_color");
            $("#form-phone").css("display", "block");
        } else
        {

            $.ajax({
                url: BASE_URL + "pub/start/registration_phone",
                type: "POST",
                data: {"user_phone_number": total},
                dataType: "JSON",
                beforeSend: function () {
                    $("#btn-phone").attr("disabled", true);
                },
                success: function (data)
                {
                    $(".phone_error_message").text("");
                    $("#btn-phone").attr("disabled", false);
                    $("#phone").removeClass("error_color");
                    $("#user_id").val(data.user_id);
                    $("#user_allready").val(data.allready);
                    if (data.status == "success") {

                        $('#phone').attr('readonly', true);
                        $('#phone_display_name').attr('readonly', true);
                        //$('#form-phone').slideUp();
                        var html = '<div class="error_massage_div"><p class="error_massage"></p></div><form action="" method="post" id="phone_verification_form">' +
                                '<input type="hidden" name="verify_id" value="' + data.verify_id + '">' +
                                '<input type="hidden" class="select_type" value="phone">' +
                                '<input type="hidden" name="user_allready" id="user_allready" value="' + data.allready + '">' +
                                '<div class="input-group mb-3">' +
                                '<input type="number" maxlength="5"  pattern="\d{3}[\-]\d{3}[\-]\d{4}" name="user_verification_code" placeholder="Verification code" id="user_verification_code" class="form-control">' +
                                '</div>' +
                                '</form>';
                        $('.phone_otp_verification').append(html);
                        $("#btn-phone").attr('onclick', 'otp_verify();');
                        $('#form-email').slideUp();
                        $("#user_verification_code").focus();
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
</script>
