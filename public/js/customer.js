var item_id = '';
var type_rate_status = false;
var value = '';
var visible = false;
var cid = '';
$(document).ready(function () {
    $(function () {
        $('.customer_action_icon button').tooltip()
    });
    $(":input").inputmask();
    $("#customer_phone").inputmask({"mask": "999-999-9999"});
    $(document).on("change", "#myonoffswitch", function () {
        setTimeout(function () {
            $(".update_customer").show();
            $(".no_allform").css('visibility', 'hidden');
        }, 500);
        if ($(this).prop("checked") == true) {
            $("#customer_status").val('active');
        } else if ($(this).prop("checked") == false) {
            $("#customer_status").val('inactive');
        }
    });
    $(".inactive").slideUp();
    $(".active").slideDown();

    $(document).on("click", "#back_customer_list", function () {
        if (getUrlVars()["bill"] == 1) {
            window.location.href = BASE_URL + "aut/billcharges";
        } else {
            window.location.href = BASE_URL + "aut/customer";
        }
    });
    $(document).on("change", "#active_inactive,#active_inactive_mob", function () {
        if ($(this).prop("checked") == true) {
            $(".inactive").slideUp();
            $(".active").slideDown();
        } else if ($(this).prop("checked") == false) {
            $(".active").slideUp();
            $(".inactive").slideDown();
        }
    });
    var dvisiable = true;

    $(document).on("click", ".more-btn", function () {
        $(".more-btn").parent('.more').find('.more-menu').attr('aria-hidden', true);
        $(".more-btn").parent('.more').removeClass('show-more-menu');
        $(".more-btn").removeClass('active_action');
        if (cid != '' && cid == $(this).attr('data-id')) {
            visible = true;
        } else {
            visible = false;
        }
        cid = $(this).attr('data-id');
        if (!visible) {
            visible = true;
            $(this).parent('.more').find('.more-menu').attr('aria-hidden', false);
            $(this).parent('.more').addClass('show-more-menu');
            $(this).addClass('active_action');
            return false;
        } else {
            cid = '';
            visible = false;
            $(this).parent('.more').find('.more-menu').attr('aria-hidden', true);
            $(this).parent('.more').removeClass('show-more-menu');
            $(this).removeClass('active_action');
        }
    });
    $(document).click(function (e) {
        if (visible) {
            visible = false;
            $(".more-btn").parent('.more').find('.more-menu').attr('aria-hidden', true);
            $(".more-btn").parent('.more').removeClass('show-more-menu');
            $(".more-btn").removeClass('active_action');
        }
    });
    $(document).on('keyup', '#search', function () {
        if ($(this).val() == "") {
            if ($('.customer_action_' + $("#customerId").val()).find('.more').hasClass("show-more-menu")) {
                $('.customer_action_' + $("#customerId").val()).find(".more-btn").parent('.more').find('.more-menu').attr('aria-hidden', true);
                $('.customer_action_' + $("#customerId").val()).find(".more-btn").parent('.more').removeClass('show-more-menu');
                $('.customer_action_' + $("#customerId").val()).find(".more-btn").removeClass('active_action');
            }
        }
        $.ajax({
            url: BASE_URL + "aut/customer/search_key",
            type: "POST",
            data: {'search_key': $(this).val()},
            success: function (data)
            {

            }
        });
    });
    if ($("#search_key").val() != "") {
        setTimeout(function () {
            $('#search').val($("#search_key").val()).trigger('keyup');
        }, 0);
    }
    if ($("#customerId").val() != "") {
        $('.customer_action_' + $("#customerId").val()).find('.more-btn').trigger('click');

    }
    if ($(window).width() < 767) {
        $('.customer_action_' + $("#customerId").val()).find('.recordhide').slideToggle(400);
    }
    $(document).on("click", ".customer_action_button", function () {
        $this = $(this);
        $.ajax({
            url: BASE_URL + "aut/customer/set_customer",
            type: "POST",
            data: {'customer_id': $(this).attr('data-id')},
            success: function (data)
            {
                window.location.href = $this.attr('data-url');
            }
        });
    });
    $(document).on("click", ".customer_action_button1", function () {
        window.location.href = $(this).attr('data-url');
    });
    if ($(window).width() < 767) {
        $('.colpsclick,.chargeclick').on('click', function () {
            $(this).find('.recordhide').slideToggle(400);
            $(this).find('.angle-righticon').find('i').toggleClass("rotate");
            $(this).find(".angle-righticon").toggleClass("on_click_icon");
        });
        $('.action_click').click(function ()
        {
            $(this).toggleClass('overlay_div');
            $(this).children('.action_div').slideToggle(100);
            $(this).find('.register_reference').toggleClass('no_mob');
        });
        var dvisiable = false;
        $('.action_click1').click(function ()
        {
            $('.action_click1').removeClass('overlay_div');
            $('.action_click1').children('.action_div').slideUp(100);
            $('.register_reference').addClass('no_mob');
            if (cid != '' && cid == $(this).attr('data-count')) {
                dvisiable = true;
            } else {
                dvisiable = false;
            }
            cid = $(this).attr('data-count');
            if (!dvisiable) {
                dvisiable = true;
                $(this).addClass('overlay_div');
                $(this).children('.action_div').slideDown(100);
                $(this).find('.register_reference').removeClass('no_mob');
                return false;
            } else {
                cid = '';
                dvisiable = false;
                $(this).removeClass('overlay_div');
                $(this).children('.action_div').slideUp(100);
                $(this).find('.register_reference').addClass('no_mob');
            }

        });
    }
    $('.tooltipped').popover({placement: "top", trigger: 'focus'});
    $.extend($.expr[":"], {
        "containsIN": function (elem, i, match, array) {
            return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
        }
    });
    $(function () {
        $('strong span').tooltip();
    });
    $(document).on("keyup", "#search", function () {
        var searchText = $(this).val();
        $('.colpsclick').hide();
        $('.customer_action_mob').hide();
        $('.search_text_div:containsIN("' + searchText + '")').parent('.colpsclick').show();
        if ($(window).width() < 767) {
            $('.search_text_div:containsIN("' + searchText + '")').parent('.colpsclick').parent('.user_listing_main').find('.customer_action_mob').show();
        }
    });

    $('.customer_rates').focus(function () {
        value = $(this).val();
        item_id = $(this).attr('data-itemid');
    });
    var typingTimer;                //timer identifier
    var doneTypingInterval = 500;  //time in ms, 2 second for example
    var $input = $('.tooltipped');

//on keyup, start the countdown
    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

//on keydown, clear the countdown 
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });

    var typingTimer1;                //timer identifier
    var doneTypingInterval1 = 500;  //time in ms, 2 second for example
    var $input1 = $('.customer_rates');

//on keyup, start the countdown
    $input1.on('keyup', function () {
        clearTimeout(typingTimer1);
        typingTimer1 = setTimeout(doneTyping1, doneTypingInterval1);
    });

//on keydown, clear the countdown 
    $input1.on('keydown', function () {
        clearTimeout(typingTimer1);
    });
    $(document).on('click', '.view_all_list', function () {
        $('#invoice_preview').modal('show');
    });
    $(document).on('click', '#send_invoice', function () {
        $.ajax({
            url: BASE_URL + "aut/charges/preview_invoice",
            type: "POST",
            data: {'email': $("#send_invoice_email").val(), 'status': '1', 'customer_id': $("#customer_id").val()},
            beforeSend: function (xhr) {
                $('#send_invoice').find(".edit_customer_loder").show();
            },
            success: function (data)
            {
                $('#send_invoice').find(".edit_customer_loder").hide();
                $('#invoice_preview').modal('hide');
                swal({title: "Invoice successfully sent on billing email address.!", text: "", icon: "success", timer: 3000}).then(function () {
                    location.reload();
                });
            }
        });
    });
    $(document).on('click', '#send_print_invoice', function () {
        $.ajax({
            url: BASE_URL + "aut/charges/preview_invoice",
            type: "POST",
            data: {'email': $("#send_invoice_email").val(), 'status': '1', 'customer_id': $("#customer_id").val()},
            beforeSend: function (xhr) {
                $('#send_print_invoice').find(".edit_customer_loder").show();
            },
            success: function (data)
            {
                $('#send_print_invoice').find(".edit_customer_loder").hide();
                $('#invoice_preview').modal('hide');
                printTrigger("1");
            }
        });
    });
    $(".make_paytment_link").attr("disabled", "disabled").off('click');
    if ($("#charge_count").val() == 0) {
        $("#send_invoice").attr("disabled", "disabled").off('click');
        $("#send_print_invoice").attr("disabled", "disabled").off('click');
        $("#print_invoice").attr("disabled", "disabled").off('click');
        $("#mail_usps").attr("disabled", "disabled").off('click');
    }
    $(document).on("keyup", "#send_invoice_email", function () {
        var email = $(this).val();
        if (IsEmail(email) != false) {
            $("#send_invoice").attr("disabled", false).on('click');
            $("#send_print_invoice").attr("disabled", false).on('click');
        }
    });
    $("#datepicker").datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'mm/dd/yyyy'
    }).datepicker('update', new Date());
    $(document).on("click", ".charge_delete", function () {
        var $this = $(this);
        bootbox.confirm({
            closeButton: false,
            className: "sign_up_alert",
            backdrop: true,
            onEscape: true,
            message: "Are you sure you want to delete this charge?",
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
                    $.ajax({
                        url: BASE_URL + "aut/customer/ajax_delete_charge",
                        type: "POST",
                        data: {'id': $this.attr("data-id")},
                        dataType: "JSON",
                        success: function (data)
                        {
                            if (data.status == "success") {
                                location.reload();
                            }
                        }
                    });
                }
            }
        });
    });
    $(document).on("click", ".recuring_delete", function () {
        var $this = $(this);
        bootbox.confirm({
            closeButton: false,
            className: "sign_up_alert",
            backdrop: true,
            onEscape: true,
            message: "Are you sure you want to delete this recurring charge?",
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
                    $.ajax({
                        url: BASE_URL + "aut/customer/ajax_delete_recurring",
                        type: "POST",
                        data: {'id': $this.attr("data-id")},
                        dataType: "JSON",
                        success: function (data)
                        {
                            if (data.status == "success") {
                                location.reload();
                            }
                        }
                    });
                }
            }
        });
    });
    $(document).on("click", ".charge_edit", function () {
        $('html, body').animate({
            scrollTop: $("#page-top").offset().top
        }, 200);
        $.ajax({
            url: BASE_URL + "aut/customer/get_charge_edit_data",
            type: "POST",
            data: {'id': $(this).attr("data-id")},
            dataType: "JSON",
            success: function (data)
            {
                $("#datepicker").datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    format: 'mm/dd/yyyy'
                }).datepicker('update', getFormattedDate(data.date_charge));
                $("#date_charge").val(getFormattedDate(data.date_charge));
                $("#item_listing").selectpicker('val', data.item_id);
                $("#item_id").val(data.item_id);
                if (data.item_id == 0) {
                    $(".extra_field").show();
                } else {
                    $(".extra_field").hide();
                }
                $(".select_charge_type").selectpicker('val', data.ct_id);
                $("#charge_rate").val(data.rate);
                $("#charge_description").val(data.description);
                $("#charge_qty_hrs").val(data.quantity);
                $("#add_edit_charge_btn").attr('onclick', "edit_charges('" + data.charge_id + "');");
                $("#add_charges_heading").html('Edit Charges');

            }
        });
    });
    $(document).on("click", ".recuring_edit", function () {
        $('html, body').animate({
            scrollTop: $("#page-top").offset().top
        }, 200);
        $.ajax({
            url: BASE_URL + "aut/customer/get_recuring_edit_data",
            type: "POST",
            data: {'id': $(this).attr("data-id")},
            dataType: "JSON",
            success: function (data)
            {
                $("#datepicker").datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    format: 'mm/dd/yyyy'
                }).datepicker('update', getFormattedDate(data.date_next));
                $("#date_charge").val(getFormattedDate(data.date_next));
                $("#item_listing").selectpicker('val', data.item_id);
                $("#frequency_listing").selectpicker('val', data.frequency);
                $("#item_id").val(data.item_id);
                if (data.item_id == 0) {
                    $(".extra_field").show();
                } else {
                    $(".extra_field").hide();
                }
                $(".select_charge_type").selectpicker('val', data.ct_id);
                $("#charge_rate").val(data.rate);
                $("#charge_description").val(data.description);
                $("#charge_qty_hrs").val(data.quantity);
                $("#add_edit_charge_btn").attr('onclick', "edit_recuring('" + data.rc_id + "');");
                $("#add_charges_heading").html('Edit Recurring Charges');
                $(".add_recuring_form").show();
                $(".customer_recuring").hide();
            }
        });
    });
    $(document).on("change", ".mailling_address", function () {
        var t = $('.mailling_address').data('type');
        if ($(this).val() != "") {
            setTimeout(function () {
                var $this = $(this);
                var t = $('.mailling_address').data('type');
                address = $('.mailling_address').val();
                $('#' + t + '_cass_icon').html('<i class="fa fa-spinner fa-spin fa-lg"></i>');
                //$('#' + t + '_cass_error').attr('class','');
                $.ajax({
                    url: BASE_URL + "aut/customer/mailling_address",
                    type: "POST",
                    data: {address: address},
                    dataType: "JSON",
                    success: function (data)
                    {
                        $('.mailling_address').val(data.address);
                        $('#' + t + '_cass_icon').html(data.cass_icon);
                        $('#' + t + '_cass_icon').attr('data-original-title', data.cass_errors).tooltip('show');
                    }
                });
            }, 1000);
        } else {
            $('#' + t + '_cass_icon').html('');
            $('#' + t + '_cass_icon').tooltip('hide');
        }
    });
    $(document).on("change", ".service_address", function () {
        var t = $(".service_address").data('type');
        if ($(this).val() != "") {
            setTimeout(function () {
                var $this = $(this);

                address = $(".service_address").val();
                $('#' + t + '_cass_icon').html('<i class="fa fa-spinner fa-spin fa-lg"></i>');
                //$('#' + t + '_cass_error').attr('class','');
                $.ajax({
                    url: BASE_URL + "aut/customer/service_address",
                    type: "POST",
                    data: {address: address},
                    dataType: "JSON",
                    success: function (data)
                    {

                        $(".service_address").val(data.address);
                        $('#' + t + '_cass_icon').html(data.cass_icon);
                        $('#' + t + '_cass_icon').attr('data-original-title', data.cass_errors).tooltip('show');
                    }
                });
            }, 1000);
        } else {
            $('#' + t + '_cass_icon').html('');
            $('#' + t + '_cass_icon').tooltip('hide');
        }
    });
    $(document).on("click", ".add_customer_recurring", function () {
        document.getElementById('customer_recuring').reset();
        $("#datepicker").datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'mm/dd/yyyy'
        }).datepicker('update', new Date());
        $('#frequency_listing').selectpicker('val', '');
        $("#item_listing").selectpicker('val', '');
        $("#chargetype_listing").selectpicker('val', '');
        $(".extra_field").hide();
        $(".add_recuring_form").show();
        $(".customer_recuring").hide();
    });
    $(document).on("click", ".cancel_form", function () {
        $(".add_recuring_form").hide();
        $(".customer_recuring").show();
    });
    if ($("#customer_rate").val() >= 1) {
        for (var i = 1; i <= 6; i++) {
            if ($(".item_list_" + i).find('.charge-tbl').length == $(".item_list_" + i).find('.un_set').length) {
                $(".item_list_" + i).children('.header_text').hide();
            }
        }
    }
    $(document).on("change", "#user_disable", function () {
        if ($(this).prop("checked") == true) {
            $(".un_set").slideUp();
            $(".set").slideDown();
            for (var i = 1; i <= 6; i++) {
                if ($(".item_list_" + i).find('.set').length >= 1) {
                    $(".item_list_" + i).children('.header_text').show();
                } else {
                    $(".item_list_" + i).children('.header_text').hide();
                }
            }
        } else if ($(this).prop("checked") == false) {
            $(".un_set").slideDown();
            $(".set").slideDown();
            for (var i = 1; i <= 6; i++) {
                if ($(".item_list_" + i).find('.un_set').length >= 1) {
                    $(".item_list_" + i).children('.header_text').show();
                }
            }
        }
    });
    $(".customer_rates").on("keypress keyup blur", function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
    $(document).on("click", ".invoice_file_btn", function () {
        if ($(window).width() < 767) {
            $(".invoice_object_tag").attr('data', $(this).attr('data-url'));
            $(".preview_invoice_iframe").attr('src', 'https://docs.google.com/viewer?url=' + $(this).attr('data-url') + '&embedded=true');
        } else {
            $(".preview_invoice_iframe").attr('src', $(this).attr('data-url'));
        }
        $('#invoice_file_preview').modal('show');
    });
    $(document).on("click", ".invoice_undo", function () {
        $this = $(this);
        bootbox.confirm({
            closeButton: false,
            className: "sign_up_alert invoice_up_alert",
            backdrop: true,
            onEscape: true,
            message: 'This will delete the invoice and place the charges back in the "to bill" state. Would you like to proceed?',
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-primary bootbox-ok-button'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-secondary'
                }
            },
            callback: function (result) {
                if (result) {
                    var invoice_id = $(this).attr('data-id');
                    $.ajax({
                        url: BASE_URL + "aut/customer/undo_invoice",
                        type: "POST",
                        data: {invoice_id: $this.attr('data-inid'), amount: $this.attr('data-amount'), customer_id: $('#customer_id').val()},
                        dataType: "JSON",
                        success: function (data)
                        {
                            if (data.status == "success") {
                                $('.remove_invoice_' + invoice_id).remove();
                                location.reload();
                            }
                        }
                    });
                }
            }
        });
    });
    $(document).on('click', '.delete_customer', function () {
        $this = $(this);
        $.ajax({
            url: BASE_URL + "aut/customer/check_customer_transaction",
            type: "POST",
            data: {customer_id: $this.attr('data-id')},
            dataType: "JSON",
            success: function (data)
            {
                if (data.status == 'fail') {

                    var msg = '';
                    if (data.charges > 0) {
                        msg += '<div>"' + data.charges.toLocaleString() + ' Charges"</div>';
                    }
                    if (data.recurring_charge > 0) {
                        msg += '<div>"' + data.recurring_charge.toLocaleString() + ' Recurring Charges"</div>';
                    }
                    if (data.invoice > 0) {
                        msg += '<div>"' + data.invoice.toLocaleString() + ' Invoices"</div>';
                    }
                    if (data.customer_item > 0) {
                        msg += '<div>"' + data.customer_item.toLocaleString() + ' Customer Items"</div>';
                    }
                    if (data.credit_detail > 0) {
                        msg += '<div>"' + data.credit_detail.toLocaleString() + ' Credits"</div>';
                    }
                    if (data.timer_project > 0) {
                        msg += '<div>"' + data.timer_project.toLocaleString() + ' Timer Projects"</div>';
                    }
                    if (data.timer > 0) {
                        msg += '<div>"' + data.timer.toLocaleString() + ' Timers"</div>';
                    }
                    bootbox.confirm({
                        closeButton: false,
                        className: "sign_up_alert",
                        backdrop: true,
                        onEscape: true,
                        message: "You can not delete customers that have linked transactions. This customer has the following transactions:" + msg,
                        buttons: {
                            confirm: {
                                label: 'Ok',
                                className: 'btn-primary bootbox-ok-button'
                            },
                            cancel: {
                                label: 'No',
                                className: 'btn-danger no_desktop no_mob no_ipad'
                            }
                        },
                        callback: function (result) {

                        }
                    });
                } else {
                    bootbox.confirm({
                        closeButton: false,
                        className: "sign_up_alert invoice_up_alert",
                        backdrop: true,
                        onEscape: true,
                        message: 'This will make this customer record inaccessible. Would you like to continue?',
                        buttons: {
                            confirm: {
                                label: 'Yes',
                                className: 'btn-primary bootbox-ok-button'
                            },
                            cancel: {
                                label: 'Cancel',
                                className: 'btn-secondary'
                            }
                        },
                        callback: function (result) {
                            if (result) {
                                $.ajax({
                                    url: BASE_URL + "aut/customer/delete_customer",
                                    type: "POST",
                                    data: {customer_id: $this.attr('data-id')},
                                    dataType: "JSON",
                                    success: function (data)
                                    {
                                        if (data.status == "success") {
                                            location.reload();
                                        }
                                    }
                                });
                            }
                        }
                    });
                }
            }
        });
    });
    $(document).on("keyup", ".download_textarea", function () {
        var info = $("#url_text").val();
        var email_to = $("#email_to").val();
        if (info != "" && email_to != "") {
            $("#btn_save_download").attr('disabled', false);
        } else {
            $("#btn_save_download").attr('disabled', true);
        }
    });
    $(document).on("click", "#mail_usps", function () {
        $.ajax({
            url: BASE_URL + "aut/billcharges/USPS_mail",
            type: "POST",
            data: {'customer_id': $('#customer_id').val()},
            beforeSend: function (xhr) {
                $('#mail_usps').find(".edit_customer_loder").show();
            },
            success: function (data)
            {
                $('#mail_usps').find(".edit_customer_loder").hide();
                $('#invoice_preview').modal('hide');
                swal({title: "Invoice successfully sent on billing email address.!", text: "", icon: "success", timer: 3000}).then(function () {
                    location.reload();
                });
            }
        });
    });
});
function printTrigger(status_print) {
    $(".s_n_preview_old").hide();
    $(".s_n_preview_new").show();
    if (status_print == "1") {
        $(".email_support").css('padding-left', '55px');
        $(".make_paytment_link").attr("disabled", false).on('click');
        var invoice_html = '<!DOCTYPE html>' +
                '<html>' +
                '<head>' +
                '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' +
                '<title>Invoice_' + getFormattedDate(Date()) + '</title>' +
                '<style>@media print { @page { size: auto;  margin-top: 0mm;margin-bottom:0mm; } }</style>' +
                '</head>' +
                '<body class="invoice_body" onafterprint="parent.location.reload();">';
        invoice_html += $(".invoice_table").html();
        invoice_html += '</body></html>';
        var frame1 = document.createElement('iframe');
        frame1.name = "frame1";
        frame1.id = "frame1";
        frame1.style.position = "absolute";
        frame1.style.top = "-1000000px";
        document.body.appendChild(frame1);
        var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
        frameDoc.document.open();
        frameDoc.document.write(invoice_html);
        frameDoc.document.close();
        window.frames["frame1"].focus();
        window.frames["frame1"].print();
    } else {
        $.ajax({
            url: BASE_URL + "aut/charges/preview_invoice",
            type: "POST",
            data: {'email': $("#send_invoice_email").val(), 'status': '0', 'customer_id': $("#customer_id").val()},
            beforeSend: function (xhr) {
                $('#print_invoice').find(".edit_customer_loder").show();
            },
            success: function (data)
            {
                $(".email_support").css('padding-left', '55px');
                $(".make_paytment_link").attr("disabled", false).on('click');
                var invoice_html = '<!DOCTYPE html>' +
                        '<html>' +
                        '<head>' +
                        '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' +
                        '<title>Invoice_' + getFormattedDate(Date()) + '</title>' +
                        '<style>@media print { @page { size: auto;  margin-top: 0mm;margin-bottom:0mm; } }</style>' +
                        '</head>' +
                        '<body class="invoice_body" onafterprint="parent.location.reload();">';
                invoice_html += $(".invoice_table").html();
                invoice_html += '</body></html>';
                var frame1 = document.createElement('iframe');
                frame1.name = "frame1";
                frame1.id = "frame1";
                frame1.style.position = "absolute";
                frame1.style.top = "-1000000px";
                document.body.appendChild(frame1);
                var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
                frameDoc.document.open();
                frameDoc.document.write(invoice_html);
                frameDoc.document.close();
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
            }
        });
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
function getFormattedDate(date) {
    date = new Date(date);
    var year = date.getFullYear();

    var month = (1 + date.getMonth()).toString();
    month = month.length > 1 ? month : '0' + month;

    var day = date.getDate().toString();
    day = day.length > 1 ? day : '0' + day;

    return month + '/' + day + '/' + year;
}
function resetForm(id) {
    document.getElementById(id).reset();
    $(".update_customer").hide();
    $(".no_allform").css('visibility', 'visible');
}
//user is "finished typing," do something
function doneTyping() {
    $(".update_customer").show();
    $(".no_allform").css('visibility', 'hidden');
}
function doneTyping1() {
    var cid1 = $("#item_rate_" + item_id).attr('data-custid');
    var itid1 = $("#item_rate_" + item_id).attr('data-itemid');
    var rate_id1 = $("#item_rate_" + item_id).attr('data-rateid');
    var rate1 = $("#item_rate_" + item_id).val();
    if (value != rate1) {
        type_rate_status = true;
        $("#item_rate_" + item_id).next('.sucess_tick').html('<i class="fas fa-spinner fa-spin"></i>').show();
        set_customer_rate(cid1, itid1, rate1, rate_id1);
    } else {
        $("#item_rate_" + item_id).next('.sucess_tick').hide();
    }
}

var autocomplete, autocomplete2;
var componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    postal_code: 'short_name'
};
function initAutocomplete() {
    autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('mailingAutoComplete'), {types: ['geocode']});
    autocomplete.setFields(['address_component']);
    autocomplete.addListener('place_changed', function () {
        fillInAddress(autocomplete, "mailling_address");
    });
    autocomplete2 = new google.maps.places.Autocomplete(
            document.getElementById('serviceAutoComplete'), {types: ['geocode']});
    autocomplete2.setFields(['address_component']);
    autocomplete2.addListener('place_changed', function () {
        fillInAddress(autocomplete2, "service_address");
    });
}
function fillInAddress(autocomplete, unique) {
    var placeSearch = [];
    var place = autocomplete.getPlace();

    for (var i = 0; i < place.address_components.length; i++) {
        var addressType = place.address_components[i].types[0];
        if (componentForm[addressType]) {
            var val = place.address_components[i][componentForm[addressType]];
            placeSearch.push(val);
        }
    }

    var newAddress = (placeSearch[0] ? placeSearch[0] : "") + (placeSearch[1] ? " " + placeSearch[1] : "") + (placeSearch[2] ? "\n" + placeSearch[2] : "") + (placeSearch[3] ? ", " + placeSearch[3] : "") + (placeSearch[5] ? " " + placeSearch[5] : "");
    $("." + unique).val(newAddress);
    if (unique == "mailling_address") {
        $("#serviceAutoComplete").focus();
    } else {
        $("#customer_discount").focus();
    }
}
function add_customer() {
    $.ajax({
        url: BASE_URL + "aut/customer/ajax_add_edit_customer",
        type: "POST",
        data: new FormData($('#add_customer')[0]),
        dataType: "JSON",
        enctype: 'multipart/form-data',
        beforeSend: function () {
            $(".add_customer_loder").show();
        },
        success: function (data)
        {
            $(".add_customer_loder").hide();
            var t = data.type;
            if (typeof (data.m_address) != "undefined" && data.m_address !== null) {
                var tm = data.m_address.type;
                $('#' + tm + '_cass_error').html(data.cass_errors);
                $('#' + tm + '_cass_error').addClass("address_" + data.cass_class);
                $('#' + tm + '_cass_icon').html(data.cass_icon);
                $('#' + tm + '_cass_icon').attr('data-original-title', data.cass_errors).tooltip('show');
            }
            if (data.cass_class != "success") {
                $('#' + t + '_cass_error').html(data.cass_errors);
                $('#' + t + '_cass_error').addClass("address_" + data.cass_class);
                $('#' + t + '_cass_icon').html(data.cass_icon);
                $('#' + t + '_cass_icon').attr('data-original-title', data.cass_errors).tooltip('show');
            }
            if (data.status == "success") {
                window.location.href = BASE_URL + "aut/customer";
            }
        },
        error: function () {
            $(".add_customer_loder").hide();
            $(".update_customer").hide();
            $(".no_allform").css('visibility', 'visible');
        },
        processData: false,
        contentType: false
    });
}
function edit_customer() {

    $.ajax({
        url: BASE_URL + "aut/customer/ajax_add_edit_customer/" + $("#customer_id").val(),
        type: "POST",
        data: new FormData($('#add_customer')[0]),
        dataType: "JSON",
        enctype: 'multipart/form-data',
        beforeSend: function () {
            $(".edit_customer_loder").show();
        },
        success: function (data)
        {
            $(".edit_customer_loder").hide();
            var t = data.type;
            if (data.status) {
                if (getUrlVars()["bill"] == 1) {
                    window.location.href = BASE_URL + "aut/billcharges";
                } else {
                    $(".update_customer").hide();
                    $(".no_allform").css('visibility', 'visible');
                }
            }
            if (typeof (data.m_address) != "undefined" && data.m_address !== null) {
                var tm = data.m_address.type;
                $('#' + tm + '_cass_error').html(data.cass_errors);
                $('#' + tm + '_cass_error').addClass("address_" + data.cass_class);
                $('#' + tm + '_cass_icon').html(data.cass_icon);
                $('#' + tm + '_cass_icon').attr('data-original-title', data.cass_errors).tooltip('show');
            }
            if (data.cass_class != "success") {
                $('#' + t + '_cass_error').html(data.cass_errors);
                $('#' + t + '_cass_error').addClass("address_" + data.cass_class);
                $('#' + t + '_cass_icon').html(data.cass_icon);
                $('#' + t + '_cass_icon').attr('data-original-title', data.cass_errors).tooltip('show');
            }

        },
        error: function () {
            $(".edit_customer_loder").hide();
            $(".update_customer").hide();
            $(".no_allform").css('visibility', 'visible');
        },
        processData: false,
        contentType: false
    });
}
function set_item_value(input) {
    if ($(input).find(':selected').val() == 0) {
        $(".extra_field").show();
    } else {
        $(".extra_field").hide();
    }
    $("#item_id").val($(input).find(':selected').attr('data-id'));
    $("#chargetype_listing").selectpicker('val', $(input).find(':selected').attr('data-type'));
    $("#charge_rate").val($(input).find(':selected').attr('data-rate'));
    $("#charge_description_1").val($(input).find(':selected').attr('data-name'));
}
function add_download_charge() {
    $.ajax({
        url: BASE_URL + "aut/charges/ajax_add_download",
        type: "POST",
        data: {'charge_id': charge_id, 'info': $("#url_text").val(), 'email_to': $('#email_to').val()},
        dataType: "JSON",
        success: function (msg)
        {
            if (msg.status == "success") {
                location.reload();
            }
        }
    });
}
function add_charge() {
    if ($("#date_charge").val() == "" && $("#item_listing").val() == "" && $("#chargetype_listing").val() == "") {
        $("#datepicker").css('border', '1px solid red');
        $("#chargetype_listing").css('border-radius', '5px');
        $("#chargetype_listing").css('border', '1px solid red');
        $("#datepicker").css('border-radius', '5px');
        $(".bs-placeholder").css('border', '1px solid red');
        $(".bs-placeholder").css('border-radius', '5px');
    } else if ($("#date_charge").val() == "") {
        $("#datepicker").css('border', '1px solid red');
        $("#datepicker").css('border-radius', '5px');
    } else if ($("#item_listing").val() == "") {
        $(".bs-placeholder").css('border', '1px solid red');
    } else if ($("#chargetype_listing").val() == "") {
        $(".bs-placeholder").css('border-radius', '5px');
        $(".bs-placeholder").css('border', '1px solid red');
    } else {
        $.ajax({
            url: BASE_URL + "aut/customer/ajax_add_charge",
            type: "POST",
            data: new FormData($('#customer_charge')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            success: function (data)
            {
                if (data.status == "success") {
                    if ($("#chargetype_listing").val() == 7) {
                        $("#download_popup").modal('show');
                        charge_id = data.charge_id;
                    } else {
                        location.reload();
                    }
                }
            },
            processData: false,
            contentType: false
        });

    }
}
function edit_charges(id) {
    if ($("#date_charge").val() == "" && $("#item_listing").val() == "" && $("#chargetype_listing").val() == "") {
        $("#datepicker").css('border', '1px solid red');
        $("#chargetype_listing").css('border-radius', '5px');
        $("#chargetype_listing").css('border', '1px solid red');
        $("#datepicker").css('border-radius', '5px');
        $(".bs-placeholder").css('border', '1px solid red');
        $(".bs-placeholder").css('border-radius', '5px');
    } else if ($("#date_charge").val() == "") {
        $("#datepicker").css('border', '1px solid red');
        $("#datepicker").css('border-radius', '5px');
    } else if ($("#item_listing").val() == "") {
        $(".bs-placeholder").css('border', '1px solid red');
    } else if ($("#chargetype_listing").val() == "") {
        $(".bs-placeholder").css('border-radius', '5px');
        $(".bs-placeholder").css('border', '1px solid red');
    } else {
        $.ajax({
            url: BASE_URL + "aut/customer/ajax_add_charge/" + id,
            type: "POST",
            data: new FormData($('#customer_charge')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            success: function (data)
            {
                if (data.status == "success") {
                    if ($("#chargetype_listing").val() == 7) {
                        if (data.download == 0) {
                            $("#download_popup").modal('show');
                            charge_id = id;
                        } else {
                            location.reload();
                        }
                    } else {
                        location.reload();
                    }

                }
            },
            processData: false,
            contentType: false
        });
    }
}
function add_recuring() {
    if ($("#date_charge").val() == "" && $("#item_listing").val() == "" && $("#chargetype_listing").val() == "") {
        $("#datepicker").css('border', '1px solid red');
        $("#chargetype_listing").css('border-radius', '5px');
        $("#chargetype_listing").css('border', '1px solid red');
        $("#datepicker").css('border-radius', '5px');
        $(".bs-placeholder").css('border', '1px solid red');
        $(".bs-placeholder").css('border-radius', '5px');
    } else if ($("#date_charge").val() == "") {
        $("#datepicker").css('border', '1px solid red');
        $("#datepicker").css('border-radius', '5px');
    } else if ($("#item_listing").val() == "") {
        $(".bs-placeholder").css('border', '1px solid red');
    } else if ($("#chargetype_listing").val() == "") {
        $(".bs-placeholder").css('border-radius', '5px');
        $(".bs-placeholder").css('border', '1px solid red');
    } else {
        $.ajax({
            url: BASE_URL + "aut/customer/ajax_add_recuring",
            type: "POST",
            data: new FormData($('#customer_recuring')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            success: function (data)
            {
                if (data.status.status == "success") {
                    $.ajax({
                        url: BASE_URL + "aut/customer/get_recuring_edit_data",
                        type: "POST",
                        data: {'id': data.status.rc_id},
                        dataType: "JSON",
                        success: function (data)
                        {
                            location.reload();
                        }
                    });
                }
            },
            processData: false,
            contentType: false
        });
    }
}
function edit_recuring(id) {
    if ($("#date_charge").val() == "" && $("#item_listing").val() == "" && $("#chargetype_listing").val() == "") {
        $("#datepicker").css('border', '1px solid red');
        $("#chargetype_listing").css('border-radius', '5px');
        $("#chargetype_listing").css('border', '1px solid red');
        $("#datepicker").css('border-radius', '5px');
        $(".bs-placeholder").css('border', '1px solid red');
        $(".bs-placeholder").css('border-radius', '5px');
    } else if ($("#date_charge").val() == "") {
        $("#datepicker").css('border', '1px solid red');
        $("#datepicker").css('border-radius', '5px');
    } else if ($("#item_listing").val() == "") {
        $(".bs-placeholder").css('border', '1px solid red');
    } else if ($("#chargetype_listing").val() == "") {
        $(".bs-placeholder").css('border-radius', '5px');
        $(".bs-placeholder").css('border', '1px solid red');
    } else {
        $.ajax({
            url: BASE_URL + "aut/customer/ajax_add_recuring/" + id,
            type: "POST",
            data: new FormData($('#customer_recuring')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            success: function (data)
            {
                if (data.status.status == "success") {
                    location.reload();
                }
            },
            processData: false,
            contentType: false
        });
    }
}
function set_customer_rate(cid, itid, rate, rate_id) {
    if (type_rate_status) {
        $.ajax({
            url: BASE_URL + "aut/customer/ajax_add_edit_rates",
            type: "POST",
            data: {'cid': cid, 'itid': itid, 'rate': rate, 'rate_id': rate_id},
            dataType: "JSON",
            success: function (data)
            {
                type_rate_status = false;
                if (rate == "") {
                    $("#item_rate_" + itid).attr('data-rateid', '0');
                    $("#item_rate_" + itid).next('.sucess_tick').html('<i class="fas fa-times text-danger"></i>');
                    setTimeout(function () {
                        $("#item_rate_" + itid).next('.sucess_tick').hide();
                    }, 2000);
                } else {
                    $("#item_rate_" + itid).next('.sucess_tick').html('<i class="fas fa-check"></i>');
                    $("#item_rate_" + itid).attr('data-rateid', data.rate_id);
                    setTimeout(function () {
                        $("#item_rate_" + itid).next('.sucess_tick').hide();
                    }, 2000);
                }
                $("#item_rate_" + itid).attr('data-original-title', data.msg, data.msg).tooltip('show');
                setTimeout(function () {
                    $("#item_rate_" + itid).tooltip('dispose', '2000');
                }, 2000);
            }
        });
    }
}
window.frames.onafterprint = function () {
    document.body.removeChild(frame1);
};
$(document).on('change', '#send_email_type', function () {
    if ($(this).val() == 'Send from My Email') {
        $('#send_invoice_my_email').show();
        $('#send_invoice').hide();
    } else {
        $('#send_invoice_my_email').hide();
        $('#send_invoice').show();
    }
});
$(document).on('click', '#send_invoice_my_email', function () {
    $.ajax({
        url: BASE_URL + "aut/charges/get_pdf_file",
        type: "POST",
        data: {'customer_id': $("#customer_id").val()},
        beforeSend: function (xhr) {
            $('#send_invoice_my_email').find(".edit_customer_loder").show();
        },
        success: function (name)
        {
            if ($(window).width() < 767) {
                new_email_url = $(".memail_body").val();
                new_email_url = new_email_url.replace('{link}', 'https://billex.s3.amazonaws.com/invoice/' + name);
                $("#thickboxId").attr('href', new_email_url);
            } else {
                new_email_url = $(".email_body").val();
                new_email_url = new_email_url.replace('{link}', 'https://billex.s3.amazonaws.com/invoice/' + name);
            }
            $.ajax({
                url: BASE_URL + "aut/charges/preview_invoice",
                type: "POST",
                data: {'email': $("#send_invoice_email").val(), 'status': '0', 'customer_id': $("#customer_id").val(), 'myemail': 1, 'pdf_file_name': name},
                success: function (data)
                {
                    $('#send_invoice_my_email').find(".edit_customer_loder").hide();
                    if ($(window).width() < 767) {
                        window.location = $('#thickboxId').attr('href');
                        location.reload();
                    } else {
                        window.open(new_email_url);
                        location.reload();
                    }
                }
            });
        }
    });
});