istatus = false;
$(document).ready(function () {
    if ($("#search_key").val() != "") {
        setTimeout(function () {
            $('#search').val($("#search_key").val()).trigger('keyup');
        }, 500);
    }
    $(document).on("click", "#change_pimage", function () {
        $(".uploadcare--widget__button_type_open").trigger("click");
    });
    $(document).on("change", "#myonoffswitch", function () {
        setTimeout(function () {
            $(".update_customer").show();
            $(".no_allform").css('visibility', 'hidden');
        }, 500);
        if ($(this).prop("checked") == true) {
            $("#item_status").val('active');
        } else if ($(this).prop("checked") == false) {
            $("#item_status").val('inactive');
        }
    });
    $(document).on('keyup', '#search', function () {
        $.ajax({
            url: BASE_URL + "aut/administration/search_key",
            type: "POST",
            data: {'search_key': $(this).val()},
            success: function (data)
            {

            }
        });
    });
    var visible = true;
    $(document).on("click", ".more-btn", function () {
        $(".more-btn").parent('.more').find('.more-menu').attr('aria-hidden', true);
        $(".more-btn").parent('.more').removeClass('show-more-menu');
        $(".more-btn").removeClass('active_action');
        if (visible) {
            visible = false;
            $(this).parent('.more').find('.more-menu').attr('aria-hidden', false);
            $(this).parent('.more').addClass('show-more-menu');
            $(this).addClass('active_action');
        } else {
            visible = true;
            $(this).parent('.more').find('.more-menu').attr('aria-hidden', true);
            $(this).parent('.more').removeClass('show-more-menu');
            $(this).removeClass('active_action');
        }
    });
    $(document).on("change", ".change_itemd", function () {
        setTimeout(function () {
            $(".update_customer").show();
            $(".no_allform").css('visibility', 'hidden');
        }, 500);
    });
    for (var i = 1; i <= 6; i++) {
        if ($(".item_list_" + i).find('.colpsclick').length == $(".item_list_" + i).find('.inactive').length) {
            $(".item_list_" + i).children('.header_text').hide();
        }
    }
    $(document).on("change", "#active_inactive,#active_inactive_mob", function () {
        if ($(this).prop("checked") == true) {
            $(".inactive").slideUp();
            $(".active").slideDown();
            for (var i = 1; i <= 6; i++) {
                if ($(".item_list_" + i).find('.active').length > 1) {
                    $(".item_list_" + i).children('.header_text').show();
                } else {
                    $(".item_list_" + i).children('.header_text').hide();
                }
            }
        } else if ($(this).prop("checked") == false) {
            $(".active").slideUp();
            $(".inactive").slideDown();
            for (var i = 1; i <= 6; i++) {
                if ($(".item_list_" + i).find('.inactive').length > 1) {
                    $(".item_list_" + i).children('.header_text').show();
                } else {
                    $(".item_list_" + i).children('.header_text').hide();
                }
            }
        }
    });
    if ($(window).width() < 767) {
        $('.colpsclick').on('click', function () {
            $(this).toggleClass("active_row");
            $(this).next('.colleps-div').slideToggle(400);
            $(this).find('.icon-rotate').toggleClass("rotate");
            $(this).toggleClass('mb-0');
        });
    }
    $('.tooltipped').popover({placement: "top", trigger: 'focus'});
    $('.tooltipped_delete').popover({placement: "top"});
    $.extend($.expr[":"], {
        "containsIN": function (elem, i, match, array) {
            return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
        }
    });
    $(document).on("keyup", "#search", function () {
        var searchText = $(this).val();
        $('.colpsclick').hide();
        $('.header_text').hide();
        $('.search_text_div:containsIN("' + searchText + '")').parent('.colpsclick').show();
        $('.search_text_div:containsIN("' + searchText + '")').parent('.colpsclick').prev('.header_text').show();
    });

    var typingTimer;                //timer identifier
    var doneTypingInterval = 500;  //time in ms, 2 second for example
    var $input = $('.change_item,.settings');

//on keyup, start the countdown
    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

//on keydown, clear the countdown 
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });
    $(document).on("change", "#addressHome", function () {
        var $this = $(this);
        var t = $('#addressHome').data('type');
        address = $('#addressHome').val();
        $('#' + t + '_cass_icon').html('<i class="fa fa-spinner fa-spin fa-lg"></i>');
        //$('#' + t + '_cass_error').attr('class','');
        $.ajax({
            url: BASE_URL + "aut/biller/cass_address",
            type: "POST",
            data: {address: address},
            dataType: "JSON",
            success: function (data)
            {
                $('#addressHome').val(data.address);
                $('#' + t + '_cass_icon').html(data.cass_icon);
                $('#' + t + '_cass_icon').attr('data-original-title', data.cass_errors).tooltip('show');
            }
        });
    });
    $(document).on("change", "#setupAddressMail", function () {
        var $this = $(this);
        var t = $('#setupAddressMail').data('type');
        address = $('#setupAddressMail').val();
        $('#' + t + '_cass_icon').html('<i class="fa fa-spinner fa-spin fa-lg"></i>');
        //$('#' + t + '_cass_error').attr('class','');
        $.ajax({
            url: BASE_URL + "aut/biller/mail_cass_address",
            type: "POST",
            data: {address: address},
            dataType: "JSON",
            success: function (data)
            {
                $('#setupAddressMail').val(data.address);
                $('#' + t + '_cass_icon').html(data.cass_icon);
                $('#' + t + '_cass_icon').attr('data-original-title', data.cass_errors).tooltip('show');
            }
        });
    });
    $(document).on("change", ".user_disable", function ()
    {
        if ($(this).prop("checked") == false)
        {
            $.ajax({
                url: BASE_URL + "aut/administration/update_biller_user",
                type: "POST",
                data: {'user_id': $(this).attr('data-id'), 'status': 0},
                dataType: "JSON",
                success: function (data)
                {

                }
            });
        }
        if ($(this).prop("checked") == true) {
            $.ajax({
                url: BASE_URL + "aut/administration/update_biller_user",
                type: "POST",
                data: {'user_id': $(this).attr('data-id'), 'status': 1},
                dataType: "JSON",
                success: function (data)
                {

                }
            });
        }
    });
    
    $(document).on("change", "#user_phone", function ()
    {
        var phone = $(this).val();
        str1 = phone.replace(/[^\d.]/g, '');
        total = parseInt(str1, 10);
        intRegex = /[0-9 -()+]+$/;
        if ((phone.length < 6) || (!intRegex.test(phone)))
        {
            $(".email_error_message").text("Please requires 10 digits.");
            $(".email_error_message").css("visibility","visible");
            $(this).css('border', '1px solid red');
            $(this).css('border', '1px solid red');
            $(this).focus();
            return false;
        } else {
            $(".email_error_message").css("visibility","hidden");
            $(this).css('border', '1px solid #ced4da');
            istatus = true;
        }
    });
    $(document).on("change", "#user_email_address", function () {
        var email = $(this).val();
        if (IsEmail(email) == false) {
            $(".email_error_message").text("Please enter valid email address.!");
            $(".email_error_message").css("visibility","visible");
            $(this).css('border', '1px solid red');
            $(this).focus();
            return false;
        }
        else {
            $(".email_error_message").css("visibility","hidden");
            $(this).css('border', '1px solid #ced4da');
            istatus = true;
        }
    });
    $(document).on("change", "#options :input", function () {
        $(".email_error_message").css("visibility","hidden");
        $(".send_invite_option").css('border','1px solid #ced4da');
        $(".send_invite_option").val('');
        $(".phone_error_message").css('visibility','hidden');
        $(".add_user_loder").hide();
        if ($(this).val() == "email") {
            $(".send_invite_option").attr('name', 'form[email_address]');
            $(".send_invite_option").attr('id', 'user_email_address');
            $("#user_email_address").inputmask("remove");
        } else {
            $(".send_invite_option").attr('name', 'form[phone]');
            $(".send_invite_option").attr('id', 'user_phone');
            $(":input").inputmask();
            $("#user_phone").inputmask({"mask": "999-999-9999"}); 
        }
    });
});
function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!regex.test(email)) {
        return false;
    } else {
        return true;
    }
}
function resetForm(id) {
    document.getElementById(id).reset();
    $(".update_customer").hide();
    $(".no_allform").css('visibility', 'visible');
}
function doneTyping() {
    $(".update_customer").show();
    $(".no_allform").css('visibility', 'hidden');
}
function add_item() {
    if ($("#item_name").val() == "") {
        $("#item_name").focus();
        $("#item_name").css("border", "1px solid red");
    } else {
        $.ajax({
            url: BASE_URL + "aut/administration/ajax_add_edit_item",
            type: "POST",
            data: new FormData($('#add_item')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            beforeSend: function (xhr) {
                $(".add_customer_loder").show();
            },
            success: function (data)
            {
                $(".add_customer_loder").hide();
                if (data.status == "success") {
                    window.location.href = BASE_URL + "aut/administration/item";
                }
            },
            processData: false,
            contentType: false
        });
    }
}
function edit_item() {
    $.ajax({
        url: BASE_URL + "aut/administration/ajax_add_edit_item/" + $("#item_id").val(),
        type: "POST",
        data: new FormData($('#add_item')[0]),
        dataType: "JSON",
        enctype: 'multipart/form-data',
        beforeSend: function (xhr) {
            $(".edit_customer_loder").show();
        },
        success: function (data)
        {
            $(".edit_customer_loder").hide();
            var t = data.type;
            $(".h_address").val(data.m_address);
            $(".m_address").val(data.s_address);
            if (data.status) {
                window.location.href = BASE_URL + "aut/administration/item";
            }
            if (data.cass_class != "success") {
                $('#' + t + '_cass_error').html(data.cass_errors);
                $('#' + t + '_cass_error').addClass("address_" + data.cass_class);
                $('#' + t + '_cass_icon').html(data.cass_icon);
            }
        },
        processData: false,
        contentType: false
    });
}
function edit_settings() {
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
            url: BASE_URL + "aut/biller/ajax_edit_settings",
            type: "POST",
            data: new FormData($('#user_setup_form')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            beforeSend: function (xhr) {
                $(".edit_customer_loder").show();
            },
            success: function (data)
            {
                $(".edit_customer_loder").hide();
                $(".login_user_name").text(data.name);
                location.reload();
            },
            processData: false,
            contentType: false
        });
    }
}
function send_invite() {
    console.log(istatus);
    if ($("#name_display").val() == "" && $(".send_invite_option").val() == "") {
        $("#name_display").css('border', '1px solid red');
        $(".send_invite_option").css('border', '1px solid red');
    } else if ($("#name_display").val() == "") {
        $("#name_display").css('border', '1px solid red');
    } else if ($(".send_invite_option").val() == "") {
        $("#name_display").css('border', '1px solid #ced4da');
        $(".send_invite_option").css('border', '1px solid red');
    } else if(istatus) {
        $.ajax({
            url: BASE_URL + "aut/administration/send_invite",
            type: "POST",
            data: new FormData($('#add_biller_user')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            beforeSend: function (xhr) {
                $(".add_user_loder").show();
            },
            success: function (data)
            {
                $(".add_user_loder").hide();
                if (data.status == "success") {
                    window.location.href = BASE_URL + "aut/administration/users";
                }
                else{
                    $(".phone_error_message").css('visibility','visible');
                }
            },
            processData: false,
            contentType: false
        });
    }
}