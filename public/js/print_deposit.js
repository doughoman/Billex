
$(document).ready(function () {
    $('.action_click').on('click', function () {
        $(this).parents('.charge-tbl').toggleClass("active_row");
        $(this).parents('.charge-tbl').next('.colleps-div').slideToggle(400);
        $(this).find('.icon-rotate').toggleClass("rotate");
        $(this).find(".angle-righticon").toggleClass("on_click_icon");
    });
    if ($(window).width() < 767) {
        $('.action_deposit').click(function () {
            $(this).toggleClass('overlay_div');
            $(this).find('.action_div').slideToggle(100);
        });
    }
    $(document).on("change", ".deposit_select_checkbox", function () {
        var total = 0;
        $('.deposit_select_checkbox:checked').each(function () {
            total += isNaN(parseFloat($(this).attr('data-amount'))) ? 0 : parseFloat($(this).attr('data-amount'));
        });
        if (total) {
            $("#btn_print_save").show();
            $(".deposit_his_div").hide();
            $(".deposit_total").show();
        } else {
            $("#btn_print_save").hide();
            $(".deposit_his_div").show();
            $(".deposit_total").hide();
        }
        $(".total_amount").text(total.toFixed(2));
        $(".total_amount").digits();

    });
    $(document).on('click', '.history_show', function () {
        $('.history_cancel').show();
        $('.history_show').hide();
        $('.deposit-details-main').show();
        $('.print_deposit').hide();
    });
    $(document).on('click', '.history_cancel', function () {
        $('.history_cancel').hide();
        $('.history_show').show();
        $('.deposit-details-main').hide();
        $('.print_deposit').show();
    });
    $.fn.digits = function () {
        return this.each(function () {
            $(this).text($(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
        });
    }
    $('.ferdfe').filter(function () {
        return this.childNodes.length > 5
    }).addClass('twoColumns');
    $(document).on("click", "#btn_print_save", function () {
        var credit_id = [];
        $(".deposit_select_checkbox:checked").each(function () {
            credit_id.push(["id", $(this).attr('data-id')]);
        });
        credit_id.push(["amount", parseFloat($('.total_amount').text().replace(/,/g, "")).toFixed(2)]);
        $.ajax({
            url: BASE_URL + "aut/printdeposit/add_deposit",
            type: "POST",
            data: {'credit_ids': credit_id},
            success: function (data)
            {
                $("#frame1").remove();
                var invoice_html = '<!DOCTYPE html>' +
                        '<html>' +
                        '<head>' +
                        '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' +
                        '<title></title>' +
                        '<style>@media print { @page { size: auto;  margin-top: 0mm;margin-bottom:0mm; } }</style>' +
                        '</head>' +
                        '<body class="invoice_body" onafterprint="parent.location.reload();">';
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
    $(document).on("click", ".deposit_undo", function () {
        $this = $(this);
        bootbox.confirm({
            closeButton: false,
            className: "sign_up_alert invoice_up_alert",
            backdrop: true,
            onEscape: true,
            message: 'This will delete the deposit and place the items back in the "to deposit" state. Would you like to proceed?',
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
                    var deposit_id = $this.attr('data-id');
                    $.ajax({
                        url: BASE_URL + "aut/printdeposit/undo_deposit",
                        type: "POST",
                        data: {deposit_id: deposit_id},
                        dataType: "JSON",
                        success: function (data)
                        {
                            if (data.status == "success") {
                                $this.parents('.charge-tbl').remove();
                                if ($(".deposit-details-main").find('.action_deposit').length == 0) {
                                    var html = '<div class="row1 blank_col_div">' +
                                            '<div class="table-col blank_col">' +
                                            '<p>No Deposit History Available</p>' +
                                            '</div>' +
                                            '</div>';
                                    $(".blank_history_div").append(html);

                                }
                                location.reload();
                            }
                        }
                    });
                }
            }
        });

    });
    $(document).on("click", ".deposit_pdf", function () {
        $.ajax({
            url: BASE_URL + "aut/printdeposit/get_deposit_pdf",
            type: "POST",
            data: {'credit_ids': $(this).attr('data-id')},
            success: function (data)
            {
                $("#frame1").remove();
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
});
window.frames.onafterprint = function () {
    document.body.removeChild(frame1);
};