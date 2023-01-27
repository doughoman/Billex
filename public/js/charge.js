var charge_id = '';
$(document).ready(function () {
    $("#datepicker").datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'mm/dd/yyyy'
    }).datepicker('update', new Date());
    $('.colpsclick').on('click', function () {
        $(this).toggleClass("active_row");
        $(this).next('.colleps-div').slideToggle(400);
        $(this).find('.icon-rotate').toggleClass("rotate");
    });
    if ($(window).width() < 767) {
        $('.colpsclick').on('click', function () {
            $(this).find('.recordhide').slideToggle(400);
        });
        $('.chargeclick').on('click', function () {
            $(this).find('.recordhide').slideToggle(400);
        });
        $(document).on("click", ".colpsclick,.chargeclick", function () {
            $(this).find('.icon-rotate').toggleClass("rotate");
            $(".angle-righticon").toggleClass("on_click_icon");
        });
        $('.action_click').click(function ()
        {
            $(this).toggleClass('overlay_div');
            $(this).find(".action_div").slideToggle(100);
        });
    }
    $(document).on('change', '#customer_listing', function () {
        $.ajax({
            url: BASE_URL + "aut/charges/get_customer_data/" + $(this).find(':selected').attr('data-id'),
            type: "POST",
            data: '',
            dataType: "JSON",
            success: function (data)
            {
                $(".select_customer_div").hide();
                $(".customer_details").css('display', 'flex');
                $(".customer_name").text(data.name);
                $("#customerlsiting").val(data.customer_id);
                $(".customer_identifier").text(data.identifier);
                $(".customer_po").text(data.po);
                $(".customer_attention").text(data.address_mail_attention);
                var address = data.address_mail_city + '<br>' + data.address_mail_city + ', ' + data.address_mail_state + ' ' + data.address_mail_zip5;
                $(".customer_address").html(address);
                $(".customer_contact_name").text(data.contact_name);
                $(".customer_contact_email").text(data.contact_email);
                $(".customer_contact_phone").text(data.contact_phone);
                location.reload();

            }
        });
    });
    $(document).on('click', '.view_all_list', function () {
        $('#invoice_preview').modal('show');
    });
    $(document).on('click', '#send_invoice', function () {
        $.ajax({
            url: BASE_URL + "aut/charges/preview_invoice",
            type: "POST",
            data: {'email': $("#send_invoice_email").val(), 'status': '1', 'customer_id': $("#charge_customer_id").val()},
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
            data: {'email': $("#send_invoice_email").val(), 'status': '1', 'customer_id': $("#charge_customer_id").val()},
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
    $(document).on("click", ".invoice_file_btn", function () {
        if ($(window).width() < 767) {
            $(".invoice_object_tag").attr('data', $(this).attr('data-url'));
            $(".preview_invoice_iframe").attr('src', 'https://docs.google.com/viewer?url=' + $(this).attr('data-url') + '&embedded=true');
        } else {
            $(".preview_invoice_iframe").attr('src', $(this).attr('data-url'));
        }
        $('#invoice_file_preview').modal('show');
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
    $(document).on("click", ".deposit_pdf", function () {
        $.ajax({
            url: BASE_URL + "aut/printdeposit/get_deposit_pdf",
            type: "POST",
            data: {'credit_ids': $(this).attr('data-id')},
            success: function (data)
            {
                var invoice_html = '<!DOCTYPE html>' +
                        '<html>' +
                        '<head>' +
                        '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' +
                        '<title></title>' +
                        '<style>@media print { @page { size: auto;  margin-top: 0mm;margin-bottom:0mm; } }</style>' +
                        '</head>' +
                        '<body class="invoice_body">';
                invoice_html += data;
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
    $(document).on("click", "#btn_invoice_code", function () {
        if ($("#invoice_code").val() == '') {
            $("#invoice_code").css('border', '1px solid red');
        } else {
            var invoice_code = $("#invoice_code").val().replace(/-/g, ''), asANumber = +invoice_code;
            $.ajax({
                url: BASE_URL + "aut/charges/check_invoice_code",
                type: "POST",
                data: {'code': invoice_code},
                dataType: "JSON",
                success: function (data)
                {
                    if (data == "0") {
                        window.location.href = BASE_URL + 'history/' + invoice_code;
                    } else {
                        $("#invoice_code").css('border', '1px solid red');
                        $(".email_error_message").show();
                    }
                }
            });
        }
    });
    $(document).on("click", "#mail_usps", function () {
        $.ajax({
            url: BASE_URL + "aut/billcharges/USPS_mail",
            type: "POST",
            data: {'customer_id': $('#charge_customer_id').val()},
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
    var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor) && !/OPR/.test(navigator.userAgent);
    $("#charge_qty_hrs").autocomplete = isChrome ? 'disabled' : 'off';
});
function printTrigger(status_print) {
    $(".s_n_preview_old").hide();
    $(".s_n_preview_new").show();
    $(".history_tr").show();
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
            data: {'email': $("#send_invoice_email").val(), 'status': '0', 'customer_id': $("#charge_customer_id").val()},
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
window.frames.onafterprint = function () {
    window.location.reload(true);
};
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

function add_download_charge() {
    $.ajax({
        url: BASE_URL + "aut/charges/ajax_add_download",
        type: "POST",
        data: {'charge_id': charge_id, 'info': $("#url_text").val(), 'email_to': $('#email_to').val()},
        dataType: "JSON",
        success: function (msg)
        {
            if (msg.status == "success") {
                if (getUrlVars()["bill"] == 1) {
                    window.location.href = BASE_URL + "aut/billcharges";
                } else {
                    location.reload();
                }
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
                        if (getUrlVars()["bill"] == 1) {
                            window.location.href = BASE_URL + "aut/billcharges";
                        } else {
                            location.reload();
                        }
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
        data: {'customer_id': $("#charge_customer_id").val()},
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
                data: {'email': $("#send_invoice_email").val(), 'status': '0', 'customer_id': $("#charge_customer_id").val(), 'myemail': 1, 'pdf_file_name': name},
                success: function (data)
                {
                    $('#send_invoice_my_email').find(".edit_customer_loder").hide();
                    if ($(window).width() < 767) {
                        window.location = $('#thickboxId').attr('href');
                    } else {
                        window.open(new_email_url);
                    }
                    location.reload();
                }
            });
        }
    });
});