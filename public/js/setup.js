var textAreas = document.getElementsByTagName('textarea');
Array.prototype.forEach.call(textAreas, function (elem) {
    elem.placeholder = elem.placeholder.replace(/\\n/g, '\n');
});
$("#send_invoice_as").focus();
$(".setup_page_hide").hide();
$("#phone_number").inputmask({"mask": "999-999-9999"});
var showVerify = false;
function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!regex.test(email)) {
        return false;
    } else {
        return true;
    }
}
var class_s;
var address;
$(document).on("change", ".cass-address", function () {

    var $this = $(this);
    var t = $this.data('type');
    address = $this.val();
    $('#' + t + '_cass_icon').html('<i class="fa fa-spinner fa-spin fa-lg"></i>');
    $.ajax({
        url: BASE_URL + "aut/biller/cass_address",
        type: "POST",
        data: {address: $this.val()},
        dataType: "JSON",
        success: function (data)
        {

            $this.val(data.address);

            class_s = "address_" + data.cass_class;
            $('#' + t + '_cass_error').html(data.cass_errors);
            $('#' + t + '_cass_error').addClass("address_" + data.cass_class);
            $('#' + t + '_cass_icon').html(data.cass_icon);
            if (data.cass_class == "success") {
                $("#setupAddressMail").val($this.val());
            }
        }
    });
    $('#' + t + '_cass_error').removeClass(class_s);
    showVerify = false;
    $('#btnSave').removeClass('disabled');
    $('#btnSave').html('Save');
});
$(document).on("keyup", ".cass-address", function (e) {
    if (!showVerify) {
        if (e.keyCode != 9 && e.keyCode != 16) {
            showVerify = true;
            $('#btnSave').html("Verify");
            $('#btnsave').addClass('disabled');
            var $this = $(this);
            var t = $this.data('type');
            $("#" + t + 'cass_error').html('<a class="btn btn-sm btn-warning">Verify Address</a>');
        }
    }
});
function before_check_payment_address(payment_address) {

    $.ajax({
        url: BASE_URL + "aut/biller/mail_cass_address",
        type: "POST",
        data: {"address": payment_address},
        dataType: "JSON",
        success: function (data)
        {
            $("#setupAddressMail").val(data.address);
        }
    });
}

function after_check_payment_address() {
    var class_p;
    $(document).on("focusout", "#setupAddressMail", function () {
        var $this = $(this);
        var t = $this.data('type');
        $('#' + t + '_cass_icon').html('<i class="fa fa-spinner fa-spin fa-lg"></i>');
        $.ajax({
            url: BASE_URL + "aut/biller/mail_cass_address",
            type: "POST",
            data: {"address": $this.val()},
            dataType: "JSON",
            success: function (data)
            {

                $this.val(data.address);
                class_p = "address_" + data.cass_class;
                $('#' + t + '_cass_error').html(data.cass_errors);
                $('#' + t + '_cass_error').addClass("address_" + data.cass_class);
                $('#' + t + '_cass_icon').html(data.cass_icon);
            }
        });
        $('#' + t + '_cass_error').removeClass(class_p);
        showVerify = false;
        $('#btnSave').removeClass('disabled');
        $('#btnSave').html('Save');
    });
    $(document).on("keyup", "#setupAddressMail", function (e) {
        if (!showVerify) {
            if (e.keyCode != 9 && e.keyCode != 16) {
                showVerify = true;
                $('#btnSave').html("Verify");
                $('#btnsave').addClass('disabled');
                var $this = $(this);
                var t = $this.data('type');
                $("#" + t + 'cass_error').html('<a class="btn btn-sm btn-warning">Verify Address</a>');
            }
        }
    });
}
$(document).on("focusout", "#setupAddressMail", function () {
    if (address == $(this).val()) {
        before_check_payment_address($(this).val());
    } else {
        after_check_payment_address();
    }
});


$(document).on("change", "#send_invoice_as", function () {
    var name = $(this).val();
    $.ajax({
        url: BASE_URL + "aut/biller/cass_name",
        type: "POST",
        data: {"name": name},
        dataType: "JSON",
        success: function (data)
        {
        }
    });
});
$(document).on("change", "#phone_number", function () {
    var phone = $(this).val();
    
    total = parseInt(str1, 10);


    intRegex = /[0-9 -()+]+$/;
    if ((phone.length < 6) || (!intRegex.test(phone)))
    {
        $("#phone_error_messagep").show();
        $("#phone_error_messagep").text("Please requires 10 digits and reset the field to empty.");
        $(this).focus();
        return false;
    } else {
        $("#phone_error_messagep").hide();
        $.ajax({
            url: BASE_URL + "aut/biller/cass_phone",
            type: "POST",
            data: {"phone": total},
            dataType: "JSON",
            success: function (data)
            {


            }
        });
    }
});
$(document).on("change", "#user_email_address", function () {
    var email = $(this).val();
    if (IsEmail(email) == false) {
        $("#email_error_message").show();
        $("#email_error_message").text("Please enter a valid Email-id.");
        $(this).focus();
        return false;
    } else {
        $("#email_error_message").hide();
        $.ajax({
            url: BASE_URL + "aut/biller/cass_email",
            type: "POST",
            data: {"email": email},
            dataType: "JSON",
            success: function (data)
            {

            }
        });
    }
});
$(document).on("click", "#setup_btn_done", function () {
    if ($("#send_invoice_as").val() == "") {
        $("#addressHome").css("border", "3px solid #939598");
        $("#send_invoice_as").focus();
        $("#send_invoice_as").css("border", "3px solid red");
    } else if ($("#addressHome").val() == "") {
        $("#send_invoice_as").css("border", "3px solid #939598");
        $("#addressHome").focus();
        $("#addressHome").css("border", "3px solid red");
    } else {
        $.ajax({
            url: BASE_URL + "aut/biller/cass_address",
            type: "POST",
            data: {address: $("#addressHome").val()},
            dataType: "JSON",
            success: function (data)
            {
                window.location.href = BASE_URL + "aut/dashboard";
                $("#addressHome").val(data.address);
            }
        });
    }

});
$(document).on("change", "#name_display", function () {
    var name_display = $(this).val();
    $.ajax({
        url: BASE_URL + "aut/biller/name_display",
        type: "POST",
        data: {"name_display": name_display},
        dataType: "JSON",
        success: function (data)
        {
        }
    });
});