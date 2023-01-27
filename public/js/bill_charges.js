var final = 0;
var typingTimer1;
var yesterday = '';
var customer_id;
$(document).ready(function () {
    if ($('#selected_date').val() != '') {
        yesterday = $('#selected_date').val();
    } else {
        today = new Date();
        yesterday = new Date(today);
        yesterday.setDate(today.getDate() - 1);
    }

    $("#datepicker").datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'mm/dd/yyyy'
    }).datepicker('update', getFormattedDate(yesterday))
            .on('changeDate', set_charges);
    if ($('#selected_date').val() != '') {
        $("#datepicker").trigger('changeDate');
    }
    $(document).on("click", "#customCheck_all", function () {
        $('.deposit_select_checkbox').not(this).prop('checked', this.checked);
        $('.total_selected').text($('.deposit_select_checkbox:checked').length);
        if ($('.deposit_select_checkbox:checked').length >= 1) {
            $('.total_selected').parent().show();
        } else {
            $('.total_selected').parent().hide();
        }
        if ($(this).prop("checked") == true) {
            $('.action_buttons button').attr('disabled', false);
        } else {
            $('.action_buttons button').attr('disabled', true);
        }
    });
    $(document).on("click", ".deposit_select_checkbox", function () {
        var process_count = $('.deposit_select_checkbox:checked').length;
        $('.total_selected').text($('.deposit_select_checkbox:checked').length);
        if ($('.deposit_select_checkbox:checked').length >= 1) {
            $('.total_selected').parent().show();
        } else {
            $('.total_selected').parent().hide();
        }
        if (process_count >= 1) {
            $('.action_buttons button').attr('disabled', false);
        } else {
            $('.action_buttons button').attr('disabled', true);
        }
    });
    $(document).on("click", ".customer_invoice_preview", function () {
        $this = $(this);
        $(this).find('.edit_customer_loder').show();
        var preview_array = [];
        $('.deposit_select_checkbox:checked').each(function () {
            preview_array.push($(this).attr('data-id'));
        });
        $.ajax({
            url: BASE_URL + "aut/billcharges/bill_charges_preview",
            type: "POST",
            data: {'customer_ids': preview_array},
            success: function (response)
            {
                $this.find('.edit_customer_loder').hide();
                $(".invoice_preview_html").html('');
                $(".invoice_preview_html").html(response);
                $(".make_paytment_link").attr("disabled", "disabled").off('click');
                $('#invoice_preview').modal('show');
            }
        });
    });
    $(document).on('hidden.bs.modal', '#invoice_preview', function () {
        $('input:checkbox').prop('checked', false);
        $('.total_selected').text($('.deposit_select_checkbox:checked').length);
        if ($('.deposit_select_checkbox:checked').length >= 1) {
            $('.total_selected').parent().show();
        } else {
            $('.total_selected').parent().hide();
            $('.action_buttons button').attr('disabled', true);
        }
    });
    $(document).on('hidden.bs.modal', '#invoice_process', function () {
        $('input:checkbox').prop('checked', false);
        $('.total_selected').text($('.deposit_select_checkbox:checked').length);
        if ($('.deposit_select_checkbox:checked').length >= 1) {
            $('.total_selected').parent().show();
        } else {
            $('.total_selected').parent().hide();
            $('.action_buttons button').attr('disabled', true);
        }
    });
    $(document).on("click", ".customer_invoice_process,#send_invoice", function () {
        var type = [];
        type.push($('input[name=email_cust]:checked').attr('id'));
        type.push($('input[name=USPS]:checked').attr('id'));
        type.push($('input[name=pdf_batch]:checked').attr('id'));
        var process_count = $('.deposit_select_checkbox:checked').length;
        var process_array = [];
        $('.deposit_select_checkbox:checked').each(function () {
            process_array.push($(this).attr('data-id'));
        });


        $.ajax({
            url: BASE_URL + "aut/billcharges/USPS_mail",
            type: "POST",
            data: {'customer_id': process_array, 'count': process_count, 'type': type, 'bill_charges': 1},
            beforeSend: function (xhr) {
                $('.customer_invoice_process').find(".edit_customer_loder").show();
            },
            success: function (data)
            {
                $(this).find("edit_customer_loder").hide();
                var total_link = [];
                data = JSON.parse(data);
                $.each(data[0], function (i, cvaule) {
                    total_link.push('https://billex.s3.amazonaws.com/invoice/' + cvaule);
                });
                if (total_link.length > 0) {
                    $.ajax({
                        url: BASE_URL + "aut/billcharges/get_html",
                        type: "POST",
                        data: {'url': total_link},
                        success: function (data, textStatus, jqXHR) {
                            $("#frame1").remove();
                            var invoice_html = data;
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
                            setTimeout(function () {
                                $('.customer_invoice_process').find(".edit_customer_loder").hide();
                                window.frames["frame1"].focus();
                                window.frames["frame1"].print();
                            }, 1000);
                        }
                    });
                } else {
                    location.reload();
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
        setTimeout(function () {
            $('#invoice_file_preview').modal('show');
        }, 500);

    });
    $(document).on('click', '.prevoius_batches', function () {
        $('.batches_cancel').show();
        $('.prevoius_batches').hide();
        $('.action_buttons').hide();
        $('.deposit-details-main').show();
        $('.charges-details-main').hide();
        $('.date_picker_div').css('visibility', 'hidden');
    });
    $(document).on('click', '.batches_cancel', function () {
        $('.batches_cancel').hide();
        $('.prevoius_batches').show();
        $('.action_buttons').show();
        $('.deposit-details-main').hide();
        $('.charges-details-main').show();
        $('.date_picker_div').css('visibility', 'visible');
    });
    $(document).on('click', '.preview_file_btn', function () {
        $this = $(this);
        customer_id = $(this).attr('data-custid');
        $.ajax({
            url: BASE_URL + "aut/billcharges/charge_preview",
            type: "POST",
            data: {'customer_id': $(this).attr('data-custid')},
            success: function (response)
            {
                $this.parents('.bill_charges_preview_div').find('.deposit_select_checkbox').prop('checked', true);
                $(".invoice_preview_html").html('');
                $(".invoice_preview_html").html(response);
                $(".make_paytment_link").attr("disabled", "disabled").off('click');
                $(".selectpicker").selectpicker();
                $('#invoice_preview').modal('show');
                $('.btn-send_invoice').tooltip();
            }
        });
    });
    $(document).on("keyup", "#send_invoice_email", function () {
        var email = $(this).val();
        if (IsEmail(email) != false) {
            $("#send_invoice").attr("disabled", false).on('click');
            $("#send_print_invoice").attr("disabled", false).on('click');
        }
    });
    $(document).on("click", ".unbill_charges_edit", function () {
        $.ajax({
            url: BASE_URL + "aut/billcharges/edit_unbill_charges",
            type: "POST",
            data: {'customer_id': $(this).attr('data-custid')},
            success: function (data)
            {
                window.location.href = BASE_URL + 'aut/charges?bill=1';
            }
        });
    });
    $.fn.digits = function () {
        return this.each(function () {
            $(this).text($(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
        });
    };
}
);
function set_charges(e) {
    $(".invoice_date").text(getFormattedDate($("#date_charge").val()));
    $.ajax({
        url: BASE_URL + "aut/billcharges/get_charge_on_date",
        type: "POST",
        data: {'date': $("#date_charge").val()},
        dataType: "JSON",
        success: function (response)
        {
            var new_url = BASE_URL + "aut/billcharges";
            window.history.pushState("data", "Title", new_url);
            var count = 0;
            var unbill_charge = charge_count = unbill_balance = 0;
            $(".customer_listing_charge").html('');
            $.each(response, function (i, cvaule) {
                var name = '';
                var unbill = '';
                if (i % 2) {
                    var cclass = "bg-gray";
                } else {
                    var cclass = "bg-white1";
                }
                if (cvaule.status == "active") {
                    if (cvaule.address_mail_zip5 != 0 || cvaule.address_mail_zip5 != '') {
                        zip5 = cvaule.address_mail_zip5;
                    } else {
                        zip5 = '';
                    }
                    name = '<p><span class="emails_field_' + count + '" data-toggle="tooltip" data-html="true" data-placement="top" title="' + cvaule.address_mail_attention + '<br>' + cvaule.address_mail_street + '<br>' + cvaule.address_mail_city + ',' + cvaule.address_mail_state + ' ' + zip5 + '">' + cvaule.name + '</span></p>';
                    unbill = '<a href="JavaScript:void(0);" class="edit_delete_btn credit_edit unbill_charges_edit" data-custid="' + cvaule.customer_id + '">$' + parseFloat(cvaule.total).toFixed(2) + '</a>';
                } else {
                    name = '<p><i class="fas fa-user-slash" data-toggle="tooltip" data-placement="top" title="Inactive"></i>&nbsp;<span class="emails_field_' + count + '" data-toggle="tooltip" data-html="true" data-placement="top" title="' + cvaule.address_mail_attention + '<br>' + cvaule.address_mail_street + '<br>' + cvaule.address_mail_city + ', ' + cvaule.address_mail_state + ' ' + cvaule.address_mail_zip5 + '">' + cvaule.name + '</span></p>';
                    unbill = '<span class="unbill_balance"><p>$' + parseFloat(cvaule.total).toFixed(2) + '</p></span>';
                }

                unbill_charge = unbill_charge + parseFloat(cvaule.total);
                charge_count = charge_count + parseInt(cvaule.count);
                unbill_balance = unbill_balance + parseFloat(cvaule.balance);
                if (cvaule.email_to_list != "") {
                    var display = "";
                } else {
                    var display = "none";
                }
                if ((cvaule.identifier == "" && cvaule.po == "") || (cvaule.identifier == "" || cvaule.po == "")) {
                    var sap = '';
                } else {
                    var sap = ' / ';
                }
                var html = '<div class="charge-tbl ' + cclass + '">' +
                        '<div class="results-data ">' +
                        '<div class="charge-edittable-cols bill_charges_preview_div">' +
                        '<div class="custom-control custom-checkbox" style="cursor: pointer;">' +
                        '<input type="checkbox" class="custom-control-input deposit_select_checkbox" data-email="' + cvaule.email_to_list + '" data-count="' + cvaule.count + '" data-id="' + cvaule.customer_id + '" id="customCheck' + i + '" style="cursor: pointer;">' +
                        '<label class="custom-control-label" for="customCheck' + i + '" style="cursor: pointer;"></label>' +
                        '</div>' +
                        '<div>' +
                        '<button type="button" class="edit_delete_btn preview_file_btn" data-custid="' + cvaule.customer_id + '"><i class="far fa-file-alt"></i></button>' +
                        '<button type="button" class="edit_delete_btn email_to_list_icon_' + count + '" data-toggle="tooltip" data-placement="top" title="' + cvaule.email_to_list + '" style="color: #327052;display:' + display + '"><i class="fas fa-at"></i></button>' +
                        '</div>' +
                        '</div>' +
                        '<div class="table-cols bill_charges_name">' +
                        '<a href="' + BASE_URL + 'aut/customer/add_edit_customer/' + cvaule.customer_id + '?bill=1" class="credit_edit1">' + name + '</a>' +
                        '<p class="charges_id_po">' + cvaule.identifier + '  ' + sap + '   ' + cvaule.po + '</p>' +
                        '</div>' +
                        '<div class="table-cols bill_charges_ap no_mob no_ipad">' +
                        '<p>' + cvaule.identifier + '' + sap + '' + cvaule.po + '</p>' +
                        '</div>' +
                        '<div class="table-cols text-right charges_number_details">' + unbill + '' +
                        '<p class="charges_id_po total_unbill_balance">$' + parseFloat(cvaule.balance).toFixed(2) + '</p>' +
                        '</div>' +
                        '<div class="table-cols text-right no_mob bill_charges_count no_ipad">' +
                        '<p>' + cvaule.count + '</p>' +
                        '</div>' +
                        '<div class="table-cols text-right no_mob charges_number_details no_ipad">' +
                        '<p class="unbill_balance total_unbill_balance">$' + parseFloat(cvaule.balance).toFixed(2) + '</p>' +
                        '</div> ' +
                        '</div>' +
                        '<div class="no_desktop mobile_charge_line2">' +
                        '<div class="table-cols bill_charges_ap">' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                $(".customer_listing_charge").append(html);
                $(".unbill_balance").digits();
                $(".unbill_charges_edit").digits();
                $('.email_to_list_icon_' + count + '').attr('data-original-title', cvaule.email_to_list).tooltip('enable');
                $('.emails_field_' + count + '').attr('data-original-title', cvaule.address_mail_attention + '<br>' + cvaule.address_mail_street + '<br>' + cvaule.address_mail_city + ', ' + cvaule.address_mail_state + ' ' + cvaule.address_mail_zip5).tooltip('enable');
                count = count + 1;
            });
            if (count != 0) {
                var total_html = '<div class="charge-tbl charges_header_div">' +
                        '<div class="charge-edittable-cols no_mob">' +
                        '</div>' +
                        '<div class="table-cols bill_charges_name no_mob">' +
                        '<label style="display:none;"><span class="total_selected">0</span> Selected</label>' +
                        '</div>' +
                        '<div class="table-cols bill_charges_ap">' +
                        '<label>Total</label>' +
                        '</div>' +
                        '<div class="table-cols text-right charges_number_details total_balance_bill">' +
                        '<label class="total_unbill_balance">$' + parseFloat(unbill_charge).toFixed(2) + '</label>' +
                        '<label class="charges_id_po total_unbill_balance">$' + parseFloat(unbill_balance).toFixed(2) + '</label>' +
                        '</div>' +
                        '<div class="table-cols text-right no_mob bill_charges_count no_ipad">' +
                        '<label>' + charge_count + '</label>' +
                        '</div>' +
                        '<div class="table-cols text-right no_mob charges_number_details no_ipad">' +
                        '<label class="total_unbill_balance">$' + parseFloat(unbill_balance).toFixed(2) + '</label>' +
                        '</div>' +
                        '</div>';
                $(".charge_total").html('');
                $(".charge_total").html(total_html);
                $(".charge_total").show();
                $(".action_buttons").show();
                $(".total_unbill_balance").digits();
                $('input:checkbox').prop('checked', false);
            } else {
                var mhtml = '<div class="row1 blank_col_div">' +
                        '<div class="table-col blank_col">' +
                        '<p>No Bill Charges Available</p>' +
                        '</div>' +
                        '</div>';
                $(".customer_listing_charge").append(mhtml);
                $(".charge_total").html('');
                $(".action_buttons").hide();
            }
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
function complete() {
    var checkedNum = $('.deposit_select_checkbox:checked').length;
    if (checkedNum == final) {
        clearTimeout(typingTimer1);
        location.reload();
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
function printTrigger(input) {
    $(".s_n_preview_old").hide();
    $(".s_n_preview_new").show();
    $(".history_tr").show();
    $(".customer_checkbox_" + input).attr('checked', true);
    var process_count = $('.deposit_select_checkbox:checked').length;
    var process_array = [];
    $('.deposit_select_checkbox:checked').each(function () {
        process_array.push($(this).attr('data-id'));
    });
    $.ajax({
        url: BASE_URL + "aut/billcharges/bill_charges_process",
        type: "POST",
        data: {'count': process_count, 'customer_ids': process_array},
        beforeSend: function (xhr) {
            $('#print_invoice').find(".edit_customer_loder").show();
        },
        success: function (response)
        {
            $('.deposit_select_checkbox:checked').each(function () {
                $.ajax({
                    url: BASE_URL + "aut/charges/preview_invoice",
                    type: "POST",
                    data: {'email': $(this).attr('data-email'), 'status': '1', 'customer_id': $(this).attr('data-id')},
                    success: function (data)
                    {
                        $('#print_invoice').find(".edit_customer_loder").hide();
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
            });
        }
    });
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
        data: {'customer_id': customer_id},
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
                data: {'email': $("#send_invoice_email").val(), 'status': '0', 'customer_id': customer_id, 'myemail': 1, 'pdf_file_name': name},
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