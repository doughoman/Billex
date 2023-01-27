$(document).ready(function () {
    UPLOADCARE_PUBLIC_KEY = 'ab84bc0ba2a1c3982f1b';
    UPLOADCARE_LOCAL = 'en';
//    UPLOADCARE_TABS = 'file camera url';
    UPLOADCARE_IMAGES_ONLY = true;
    UPLOADCARE_PREVIEW_STEP = true;

    var widget = uploadcare.Widget('[role=uploadcare-uploader]');
    widget.onChange(function (file) {
        if (file) {
            file.progress(function (fileInfo) {
                $('.upload-progress').show();
            });
        }
        ;
    });
    widget.onUploadComplete(function (info) {
        $("#btnSave").show();
        $("#current_image").attr('src', info.cdnUrl);
        $("#social_img_url").val("" + info.cdnUrl);
        setTimeout(function () {
            $.ajax({
                url: BASE_URL + 'aut/dashboard/save_profile',
                type: 'POST',
                dataType: 'json',
                data: {"social_img_url": info.cdnUrl},
                success: function (result) {
                    $(".user_message").remove();
                    $('.upload-progress').hide();
                    if (result.status == 'success') {
                        $('#profile-image').val('');
                        $('.header_profile_image').attr('src', info.cdnUrl);
                        $('#profile_current').after('<label class="success mt-1 user_message">' + result.msg + '</label>');
                        setTimeout(function () {
                            $('label.success').remove();
                        }, 1000);
                    } else {
                        $('#profile_current').after('<label class="error mt-1 user_message">' + result.msg + '</label>');
                        setTimeout(function () {
                            $('label.error').remove();
                        }, 1000);
                    }
                }
            });
        }, 3000);
    });
    $(document).on("click", "#change_pimage", function () {
        $(".uploadcare--widget__button_type_open").trigger("click");
    });
    $(":input").inputmask();
    $("#user_phone_number").inputmask({"mask": "999-999-9999"});

    $("#StrengthProgressBar").zxcvbnProgressBar({
        passwordInput: "#passwordInput"
    });
    $(document).on("focus", "#passwordInput", function () {
        $("#password_check_progress").show();
    });
    $(document).on("focusout", "#passwordInput", function () {
        $("#password_check_progress").hide();
    });
    $(document).on("change", "#hide_password_checkbox", function () {
        if ($(this).prop("checked") == true) {
            $("#passwordInput").attr("type", "password");
            $("#confirm_password_box").show();
        } else if ($(this).prop("checked") == false) {
            $("#passwordInput").attr("type", "text");
            $("#confirm_password_box").hide();
        }
    });
});
var value = '';
$('.name_edit').focus(function () {
    value = $(this).val();
});
$('.name_edit').focusout(function () {
    $this = $(this);
    var newValue = $this.val();
    if (value != newValue) {
        $this.next('.sucess_tick').html('<i class="fas fa-spinner fa-spin"></i>').show();
        $.ajax({
            url: BASE_URL + 'aut/dashboard/update_userprofile',
            type: 'POST',
            dataType: 'json',
            data: {'name': $this.attr('name'), 'value': newValue},
            success: function (result) {
                //$('label.success').css("visibility", "hidden");

                if (result.status == 'success') {
                    $this.next('.sucess_tick').html('<i class="fas fa-check"></i>');
                    $('.' + $this.attr("data-type") + '_user_message').css("visibility", "visible");
                    setTimeout(function () {
                        $('.' + $this.attr("data-type") + '_user_message').css("visibility", "hidden");
                        $this.next('.sucess_tick').hide();
                    }, 1000);
                } else {
                    $this.next('.sucess_tick').html('<i class="fas fa-times text-danger"></i>');
                    setTimeout(function () {
                        $('.' + $this.attr("data-type") + '_user_message').css("visibility", "hidden");
                        $this.next('.sucess_tick').hide();
                    }, 1000);
                }
            }
        });
    } else {
        $this.next('.sucess_tick').hide();
    }
});
function change_password() {
    var password = $("#passwordInput").val();

    var confirmPasword = $("#confirmPasswordInput").val();
    if (password != confirmPasword) {
        $("#result").text("Password does not match.!");
        return false;
    } else if (password == "" || password == "0") {
        $("#result").text("Please enter proper password.!");
        return false;
    } else {
        $.ajax({
            url: BASE_URL + "aut/dashboard/change_password",
            type: "POST",
            data: {"userPassword": password},
            dataType: "JSON",
            success: function (data)
            {
                if (data.status == "success") {
                    $("#result").text("Password Change Successfully.!");
                    $("#result").css("color", "green");
                    $("#passwordInput").val("");
                    $("#confirmPasswordInput").val("");
                } else {
                    $("#result").text("Password does not change.!");
                    $("#result").css("color", "red");
                }

            }

        });
    }

}
function getFormattedDate(date) {
    date = new Date(date);
    var year = date.getFullYear();
    var month = (1 + date.getMonth()).toString();
    month = month.length > 1 ? month : '0' + month;
    var day = date.getDate().toString();
    day = day.length > 1 ? day : '0' + day;
    return month + '/' + day + '/' + year;
}
function update_userprofile() {
    $.ajax({
        url: BASE_URL + "aut/dashboard/update_userprofile",
        type: "POST",
        data: new FormData($('#user_profile_form')[0]),
        dataType: 'JSON',
        enctype: 'multipart/form-data',
        success: function (data) {

        },
        processData: false,
        contentType: false
    });
}
function unlock(type) {
    if (type == "email") {
        $("#user_email_address").attr('readonly', false);
        var evalue = $("#user_email_address").val();
        $("#user_email_address").focus().val("");
        $("#email_btn_validate").html('<i class="fas fa-undo"></i>');
        $("#email_btn_validate").attr('onclick', 'lock("email","' + evalue + '")');
        $("#email_send").after('<button class="btn sendcode_email email_login_code" type="button" onclick="email_validate();" style="display:none;">send code</button>');
    } else {
        $("#user_phone_number").attr('readonly', false);
        var pvalue = $("#user_phone_number").val();
        $("#user_phone_number").focus().val("");
        $("#phone_btn_validate").html('<i class="fas fa-undo"></i>');
        $("#phone_btn_validate").attr('onclick', 'lock("phone","' + pvalue + '")');
        $("#phone_send").after('<button class="btn sendcode_email phone_login_code" type="button" onclick="phone_validate();" style="display:none;">send code</button>');
    }
}
function lock(type, evalue) {
    if (type == "email") {
        $("#user_email_address").val(evalue);
        $("#user_email_address").attr('readonly', true);
        $(".email_login_code").remove();
        $("#email_btn_validate").attr('onclick', 'unlock("email")');
        $("#email_btn_validate").html('<i class="fas fa-pencil-alt"></i>');
    } else {
        $("#user_phone_number").val(evalue);
        $("#user_phone_number").attr('readonly', true);
        $(".phone_login_code").remove();
        $("#phone_btn_validate").attr('onclick', 'unlock("phone")');
        $("#phone_btn_validate").html('<i class="fas fa-pencil-alt"></i>');
    }
}
function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!regex.test(email)) {
        return false;
    } else {
        return true;
    }
}
function verify() {
    if ($('.select_type').val() == "email") {
        form = "email_verification_form";
    } else {
        form = "phone_verification_form";
    }
    $.ajax({
        url: BASE_URL + "aut/dashboard/verify_email_phone",
        type: "POST",
        data: $('#' + form).serialize(),
        dataType: "JSON",
        success: function (data)
        {
            var status = data.status;
            if (status != "success") {
                $("." + $('.select_type').val() + "_error_massage").text("Enter the code sent to your " + $('.select_type').val() + ".");
                $(".phone_otp_verification").css('margin-top', '15px');
                $(".email_otp_verification").css('margin-top', '15px');
                $(".error_massage_div").addClass("alert_error");
                $(".error_massage_div").removeClass("alert_success");
            } else {

                $("." + $('.select_type').val() + "_error_massage").hide();
                $("#" + $('.select_type').val() + "_btn_validate").attr("disabled", false);
                $("#" + $('.select_type').val() + "_btn_validate").css("color", "#000000");
                $("#" + $('.select_type').val() + "_btn_validate").css("background-color", "#ffffff");
                $("#" + $('.select_type').val() + "_btn_validate").css("border-color", "#c1c1c1");
                $("." + $('.select_type').val() + "_login_code").remove();
                $("#" + $('.select_type').val() + "_btn_validate").attr('onclick', 'unlock(\"' + $('.select_type').val() + '\")');
                $("#" + $('.select_type').val() + "_btn_validate").html('<i class="fas fa-pencil-alt"></i>');
                $("#" + $('.select_type').val() + "_verification_form").remove();
            }
        }
    });
}
$(document).on("keyup", "#user_email_address", function () {
    var email = $("#user_email_address").val();
    if (IsEmail(email) == true) {
        $(".email_login_code").show();
    } else {
        $(".email_login_code").hide();
    }
});
$(document).on("keyup", "#user_phone_number", function () {
    var phone = $("#user_phone_number").val();
    str1 = phone.replace(/[^\d.]/g, '');
    total = parseInt(str1, 10);
    intRegex = /[0-9 -()+]+$/;
    if ((phone.length < 6) || (!intRegex.test(phone)))
    {
        $(".phone_login_code").hide();
    } else {
        $(".phone_login_code").show();
    }
});
var typingTimer;                //timer identifier
var doneTypingInterval = 500;  //time in ms, 2 second for example
var $input = $('.start_value_number');

//on keyup, start the countdown
$input.on('keyup', function () {

});

//on keydown, clear the countdown 
$input.on('keydown', function () {
    clearTimeout(typingTimer);
});
$(document).on('keyup', '#time_start', function () {
    var day = $(this).val();
    if (day >= 1 && day <= 28) {
        $(this).css('border', '1px solid #ced4da');
        $(this).parents('.form-group').find('.startvalue_message').text('Start value successfully updated.');
        $(this).parents('.form-group').find('.startvalue_message').addClass('success').removeClass('email_error_message');
        $(this).parents('.form-group').find('.startvalue_message').css('visibility', 'hidden');
        clearTimeout(typingTimer);
        typingTimer = setTimeout(set_start_value, doneTypingInterval);
    } else {
        $(this).css('border', '1px solid red');
        $(this).parents('.form-group').find('.startvalue_message').text('Start value must be 1 - 28.');
        $(this).parents('.form-group').find('.startvalue_message').removeClass('success').addClass('email_error_message');
        $(this).parents('.form-group').find('.startvalue_message').css('visibility', 'visible');
    }
});
$(document).on('keyup', '#time_start1', function () {
    var day = $(this).val();
    if (day >= 1 && day <= 28) {
        $(this).css('border', '1px solid #ced4da');
        $('.startday1_message').text('Start value successfully updated.');
        $('.startday1_message').addClass('success').removeClass('email_error_message');
        $('.startday1_message').css('visibility', 'hidden');
    } else {
        $(this).css('border', '1px solid red');
        $('.startday1_message').text('Start value must be 1 - 28.');
        $('.startday1_message').removeClass('success').addClass('email_error_message');
        $('.startday1_message').css('visibility', 'visible');
    }
});
$(document).on('keyup', '#time_start2', function () {
    var day = $(this).val();
    if (day >= 1 && day <= 28) {
        $(this).css('border', '1px solid #ced4da');
        $('.startday2_message').text('Start value successfully updated.');
        $('.startday2_message').addClass('success').removeClass('email_error_message');
        $('.startday2_message').css('visibility', 'hidden');

    } else {
        $(this).css('border', '1px solid red');
        $('.startday2_message').text('Start value must be 1 - 28.');
        $('.startday2_message').removeClass('success').addClass('email_error_message');
        $('.startday2_message').css('visibility', 'visible');
    }
});
$(document).on('keyup', '#time_start1,#time_start2', function () {
    day1 = parseInt($('#time_start1').val());
    day2 = parseInt($('#time_start2').val());
    if (day1 < day2) {
        $('.semi_monthly_message').text('Start value successfully updated.');
        $('.semi_monthly_message').addClass('success').removeClass('email_error_message');
        $('.semi_monthly_message').css('visibility', 'hidden');
        if (day2.length == 1) {
            $('#time_start2').val('0' + $('#time_start2').val());
        }
        if (day2.length != 0) {
            set_start_value();
        }
    } else {
        $('.semi_monthly_message').text('start day 1 is less then start day 2.');
        $('.semi_monthly_message').removeClass('success').addClass('email_error_message');
        $('.semi_monthly_message').css('visibility', 'visible');
    }
});
function email_validate(type) {
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
                $("#email_btn_validate").attr("disabled", true);
            },
            success: function (data)
            {
                $(".email_error_message").text("");
                $("#email_btn_validate").attr("disabled", false);
                //$('#form-email').slideUp();
                $("#user_email_address").removeClass("error_color");
                if (data.status == "success") {
                    //$('#verification_modal').modal('show');
                    $('#user_email_address').attr('readonly', true);
                    $('#email_display_name').attr('readonly', true);
                    if (data.allready == 1) {
                        $("#email_error_message").text("Email address already exists on another account.Confirming email address will combine the accounts.");
                    }
                    var html = '<form action="" method="post" id="email_verification_form">' +
                            '<input type="hidden" name="verify_id" value="' + data.verify_id + '">' +
                            '<input type="hidden" class="select_type" value="email">' +
                            '<input type="hidden" name="auth_value" value="' + $("#user_email_address").val() + '">' +
                            '<input type="hidden" name="user_allready" id="user_allready" value="' + data.allready + '">' +
                            '<div class="input-group mb-3">' +
                            '<input type="number" maxlength="5"  pattern="\d{3}[\-]\d{3}[\-]\d{4}" name="user_verification_code" placeholder="Verification code" id="user_verification_code" class="form-control">' +
                            '<div class="input-group-append"><button class="btn btn-primary" id="email_btn_validate" type="button" onclick="verify(\'email\')">Verify</button></div>' +
                            '</div>' +
                            '</form>';
                    $('.email_otp_verification').append(html);


                    $("#user_verification_code").focus();
                }
            }
        });
    }
}
function phone_validate(type) {
    var phone = $("#user_phone_number").val();
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
        $("#user_phone_number").addClass("error_color");
        $("#form-phone").css("display", "block");
    } else
    {

        $.ajax({
            url: BASE_URL + "pub/start/registration_phone",
            type: "POST",
            data: {"user_phone_number": total},
            dataType: "JSON",
            beforeSend: function () {
                $("#phone_btn_validate").attr("disabled", true);
            },
            success: function (data)
            {
                $(".phone_error_message").text("");
                $("#phone_btn_validate").attr("disabled", false);
                $("#user_phone_number").removeClass("error_color");
                if (data.status == "success") {
                    $('#user_phone_number').attr('readonly', true);
                    $('#phone_display_name').attr('readonly', true);
                    if (data.allready == 1) {
                        $("#phone_error_messagep").text("Phone number already exists on another account.Confirming number will combine the accounts.");
                    }

                    var html = '<form action="" method="post" id="phone_verification_form">' +
                            '<input type="hidden" name="verify_id" value="' + data.verify_id + '">' +
                            '<input type="hidden" class="select_type" value="phone">' +
                            '<input type="hidden" name="auth_value" value="' + total + '">' +
                            '<input type="hidden" name="user_allready" id="user_allready" value="' + data.allready + '">' +
                            '<div class="input-group mb-3">' +
                            '<input type="number" maxlength="5"  pattern="\d{3}[\-]\d{3}[\-]\d{4}" name="user_verification_code" placeholder="Verification code" id="user_verification_code" class="form-control">' +
                            '<div class="input-group-append"><button class="btn btn-primary" id="phone_btn_validate" type="button" onclick="verify(\'phone\')">Verify</button></div>' +
                            '</div>' +
                            '</form>';
                    $('.phone_otp_verification').append(html);
                    $("#user_verification_code").focus();
                }
            }
        });
    }
}
function disconnect_google() {
    $.ajax({
        url: BASE_URL + "aut/dashboard/disconnect_google",
        type: "post",
        data: {},
        dataType: "JSON",
        success: function (data) {
            if (data.status == "success") {
                location.reload();
            } else {

            }
        }
    });
}
function set_time_zone(input) {
    $.ajax({
        url: BASE_URL + "aut/dashboard/set_timezone",
        type: "post",
        data: {'utc_offset': $(input).val()},
        dataType: "JSON",
        success: function (data) {
            if (data.status == "success") {
                $('.timezone_message').css("visibility", "visible");
                setTimeout(function () {
                    $('.timezone_message').css("visibility", "hidden");
                }, 1000);
            }
        }
    });
}
function set_value_type() {
    $(".value_label").show();
    switch ($("#payperiods_listing").val()) {
        case 'monthly':
            var html = '<input class="form-control start_value_number" min="1" max="28" type="number" autocomplete="off" id="time_start" value="' + $(".start_value").val() + '"/>';
            $(".start_value_type").html(html);
            break;
        case 'weekly':
        case 'bi-weekly':
            var html = '<div class="custom-dropdown big type_select_option" style="width: 100%;">' +
                    '<select class="selectpicker" data-live-search="true"  data-width="100%" title="Choose one of the start day" onchange="set_start_value(this)">' +
                    '<option value="1" selected>Monday</option>' +
                    '<option value="2" ' + ($(".start_value").val() == "2" ? "selected" : "") + '>Tuesday</option>' +
                    '<option value="3" ' + ($(".start_value").val() == "3" ? "selected" : "") + '>Wednesday</option>' +
                    '<option value="4" ' + ($(".start_value").val() == "4" ? "selected" : "") + '>Thursday</option>' +
                    '<option value="5" ' + ($(".start_value").val() == "5" ? "selected" : "") + '>Friday</option>' +
                    '<option value="6" ' + ($(".start_value").val() == "6" ? "selected" : "") + '>Saturday</option>' +
                    '<option value="7" ' + ($(".start_value").val() == "7" ? "selected" : "") + '>Sunday</option>' +
                    '</select>' +
                    '</div>';
            $(".start_value_type").html(html);
            $('.selectpicker').selectpicker();
            break;
        case 'semi-monthly':
            if ($(".start_value").val().length != 3 && $(".start_value").val().length != 4) {
                $(".start_value").val('115');
            }
            if ($(".start_value").val().length == 3) {
                start = $(".start_value").val().substring(0, 1);
            } else {
                start = $(".start_value").val().substring(0, 2);
            }
            var html = '<div class="form-group mb-0 startday1">' +
                    '<label>Start Day 1</label>' +
                    '<input class="form-control" type="number" autocomplete="off" id="time_start1" value="' + start + '"/>' +
                    '<span class="success startday1_message" style="visibility: hidden;">Start Value successfully updated.</span>' +
                    '</div>' +
                    '<div class="form-group mb-0 startday2">' +
                    '<label>Start Day 2</label>' +
                    '<input class="form-control" type="number" autocomplete="off" id="time_start2" value="' + $(".start_value").val().slice(-2) + '"/>' +
                    '<span class="success startday2_message" style="visibility: hidden;">Start Value successfully updated.</span>' +
                    '</div>';
            $(".start_value_type").html(html);
            $(".value_label").hide();
            break;
    }
}
set_value_type();
function set_pay_periods(input) {
    $(".start_value").val('');
    set_value_type();
    $.ajax({
        url: BASE_URL + "aut/dashboard/set_payperiods",
        type: "post",
        data: {'pay_period': $(input).val()},
        dataType: "JSON",
        success: function (data) {
            if (data.status == "success") {
                $('.payperiods_message').css("visibility", "visible");
                setTimeout(function () {
                    $('.payperiods_message').css("visibility", "hidden");
                }, 1000);
            }
        }
    });
}
function set_start_value(input) {
    var values = '';
    switch ($("#payperiods_listing").val()) {
        case 'monthly':
            values = $("#time_start").val();
            break;
        case 'weekly':
            values = $(input).val();
            break;
        case 'bi-weekly':
            values = $(input).val();
            break;
        case 'semi-monthly':
            values = $("#time_start1").val() + $("#time_start2").val();
            break;
    }
    $.ajax({
        url: BASE_URL + "aut/dashboard/set_startvalue",
        type: "post",
        data: {'pay_period_start': values},
        dataType: "JSON",
        success: function (data) {
            if (data.status == "success") {
                $('.startvalue_message').css("visibility", "visible");
                setTimeout(function () {
                    $('.startvalue_message').css("visibility", "hidden");
                }, 1000);
            }
        }
    });
}