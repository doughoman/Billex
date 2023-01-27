$(document).ready(function () {

    $.extend($.expr[":"], {
        "containsIN": function (elem, i, match, array) {
            return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
        }
    });
    $(function () {
        $('#total_amount_customer').tooltip();
    });
    $(document).on("keyup", "#customer_search", function () {
        var searchText = $(this).val();
        $('.customer_payment').hide();
        $('.search_text_div:containsIN("' + searchText + '")').parents('.customer_payment').show();
        $(".customer_balnce_text").each(function () {
            if ($(this).val() != "") {
                $(this).parents('.customer_payment').show();
            }
        });
    });
    if ($(window).width() < 767) {
        $('.colpsclick1').on('click', function () {
            $(this).parent('.colpsclick').find('.recordhide').slideToggle(400);
        });
        $('.chargeclick1').on('click', function () {
            $(this).parent('.colpsclick').find('.recordhide').slideToggle(400);
        });
        $(document).on("click", ".colpsclick1", function () {
            $(this).parent('.colpsclick').find('.icon-rotate').toggleClass("rotate");
            $(this).parent('.colpsclick').find(".angle-righticon").toggleClass("on_click_icon");
        });
        $(document).on('click', '.customer_details_row', function () {
            $(this).parent().find('.customer_invoice_div').slideToggle();
        });
    }
    $(document).on("click", ".balance_down_icon", function () {
        $(this).parent(".cust_balance").find(".customer_amount_add").find(".customer_balnce_text").val(parseFloat($(this).find(".customer_balance").text().replace(/,/g, "").replace('$', '')).toFixed(2));
        $(".customer_balnce_text").each(function () {
            calculateSum();
        });
        var insum = 0;
        var total_balance = parseFloat($(this).parent(".cust_balance").find(".customer_amount_add").find(".customer_balnce_text").val());
        $($(this).parents('.customer_payment').find('.invoice_balance')).each(function () {
            insum += parseFloat($(this).text().replace(/,/g, "").replace('$', ''));
        });
        if (insum <= total_balance) {
            $($(this).parents('.customer_payment').find('.invoice_down_icon')).each(function () {
                if (insum >= parseFloat($(this).text().replace(/,/g, "").replace('$', ''))) {
                    $(this).trigger('click');
                }
                insum -= parseFloat($(this).text().replace(/,/g, "").replace('$', ''));
            });
        } else {
            $($(this).parents('.customer_payment').find('.invoice_down_icon')).each(function () {
                if (insum > parseFloat($(this).text().replace(/,/g, "").replace('$', ''))) {
                    $(this).trigger('click');
                }
                insum -= parseFloat($(this).text().replace(/,/g, "").replace('$', ''));
            });
        }
    });
    $(document).on("click", ".invoice_down_icon", function () {
        $(this).parent(".cust_balance").find(".customer_amount_add").find(".customer_invoice").val(parseFloat($(this).find(".customer_balance").text().replace(/,/g, "").replace('$', '')).toFixed(2));
        invoice_calculate($(this).attr('data-custid'));
    });
    $(".customer_balnce_text").each(function () {
        $(this).keyup(function () {
            calculateSum();
        });
    });
    $(".customer_invoice").each(function () {
        $(this).keyup(function () {
            invoice_calculate($(this).attr('data-custid'));
        });
    });
    $(".customer_balnce_text").each(function () {
        $(this).keyup(function () {
            invoice_calculate($(this).attr('data-id'));
        });
    });
    $.fn.digits = function () {
        return this.each(function () {
            $(this).text($(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
        });
    }
    var typingTimer;                //timer identifier
    var doneTypingInterval = 500;  //time in ms, 2 second for example
    var $input = $('#invoice_search');

//on keyup, start the countdown
    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

//on keydown, clear the countdown 
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });
    if ($(window).width() > 767) {
        $(document).on('click', '.customer_details_row', function () {
            $(this).parent().find('.customer_invoice_div').slideDown();
        });
    }
    $(document).on('click', '#cancel_payment_btn', function () {
        var customer_balance = [];
        $(".customer_balnce_text").each(function () {
            if ($(this).val() != "") {
                customer_balance.push([$(this).attr('data-id'), $(this).val().replace(/,/g, '')]);
            }
        });
        $.ajax({
            url: BASE_URL + "aut/customer/update_cuatomer_balance",
            type: "POST",
            data: {'customer_balance': customer_balance},
            dataType: "JSON",
            success: function (data)
            {
                if (data.status) {
                    if (getUrlVars()["hs"] == 1) {
                        window.location.href = BASE_URL + 'history';
                    } else {
                        window.location.href = BASE_URL + 'aut/customer/register/' + $('#customer_id').val();
                    }
                }
            }
        });
    });
});
function doneTyping() {
    var searchText = $('#invoice_search').val();
    if (searchText != "") {
        $.ajax({
            url: BASE_URL + "aut/postpayment/get_invoice_customer",
            type: "POST",
            data: {'invoice_number': searchText},
            dataType: "JSON",
            success: function (data)
            {
                $('.customer_payment').hide();
                $(".customer_balnce_text").each(function () {
                    if ($(this).val() != "") {
                        $(this).parents('.customer_payment').show();
                    }
                });
                $.each(data, function (i, output) {
                    $(".customer_invoice_" + output.customer_id).parents('.customer_payment').show();
                });
            }
        });
    } else {
        $('.customer_payment').show();
    }

}
function calculateSum() {
    var sum = 0;
    $(".customer_balnce_text").each(function () {
        if (!isNaN(this.value) && this.value.length != 0) {
            sum += parseFloat(this.value);
        }
    });
    $(".total_amount").text(sum.toFixed(2));
    $(".total_amount").digits();
    $("#total_amount_customer").val(sum.toFixed(2));
}
function invoice_calculate(cust_id) {
    var isum = 0;
    $(".customer_invoice_text_" + cust_id).each(function () {
        if (!isNaN(this.value) && this.value.length != 0) {
            isum += parseFloat(this.value);
        }
    });
    if ($(".cust_bal_" + cust_id).val() == "" || parseFloat($(".cust_bal_" + cust_id).val()) < isum) {
        $(".cust_bal_" + cust_id).val(isum.toFixed(2));
    }
    if (isum != 0) {
        $(".cust_bal_" + cust_id).removeClass('background-success');
        $(".cust_bal_" + cust_id).removeClass('background-danger');
        $(".cust_bal_" + cust_id).removeClass('background-warning');
        if (parseFloat($(".cust_bal_" + cust_id).val()) == isum) {
            $(".cust_bal_" + cust_id).addClass('background-success');
        } else if (parseFloat($(".cust_bal_" + cust_id).val()) > isum) {
            $(".cust_bal_" + cust_id).addClass('background-warning');
        } else if (parseFloat($(".cust_bal_" + cust_id).val()) < isum) {
            $(".cust_bal_" + cust_id).addClass('background-danger');
        }
    }
    calculateSum();
}
$(".customer_balnce_text").blur(function () {
    if ($(this).val() != "") {
        $(this).val(parseFloat($(this).val()).toFixed(2));
    }
});
$(".customer_invoice").blur(function () {
    if ($(this).val() != "") {
        $(this).val(parseFloat($(this).val()).toFixed(2));
    }
});
$(".customer_balnce_text").keyup(function () {
    var max = parseFloat($(this).attr('max'));
    if ($(this).val() > max)
    {
        $(this).val(max);
    }
});

function add_post_payment() {
    if ($("#payment_type").val() == "" && $("#check_ref").val() == "" && $("#total_amount_customer").val() == "") {
        $("#check_ref").css('border', '1px solid red');
        $(".bs-placeholder").css('border', '1px solid red');
        $("#total_amount_customer").css('border', '1px solid red');
    } else if ($("#payment_type").val() == "") {
        $(".bs-placeholder").css('border', '1px solid red');
    } else if ($("#check_ref").val() == "") {
        $("#check_ref").css('border', '1px solid red');
    } else if ($("#total_amount_customer").val() == "") {
        $("#total_amount_customer").css('border', '1px solid red');
    } else {
        $.ajax({
            url: BASE_URL + "aut/postpayment/add_post_payment",
            type: "POST",
            data: new FormData($('#post_payment_form')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            beforeSend: function (xhr) {
                $(".edit_customer_loder").show();
            },
            success: function (data)
            {
                if (data.status == "success") {
                    var customer_balance = [];
                    $(".customer_balnce_text").each(function () {
                        if ($(this).val() != "") {
                            customer_balance.push([$(this).attr('data-id'), $(this).val()]);
                        }
                    });
                    $.ajax({
                        url: BASE_URL + "aut/customer/update_cuatomer_balance",
                        type: "POST",
                        data: {'customer_balance': customer_balance},
                        dataType: "JSON",
                        success: function (data)
                        {
                            if (data.status) {
                                var customer_invoice = [];
                                $(".customer_payment").each(function () {
                                    $this = $(this);
                                    var cust_id = $this.attr('data-id');
                                    var total_balance = 0.00;
                                    var sum = 0;
                                    if ($this.find('.cust_bal_' + cust_id).val() != "") {
                                        total_balance = parseFloat($this.find('.cust_bal_' + cust_id).val());
                                        $this.find('.customer_invoice_text_' + cust_id).each(function () {
                                            $input = $(this);
                                            if ($input.val() != "") {
                                                sum = sum + parseFloat($input.val());
                                                customer_invoice.push([$input.attr('data-id'), $input.attr('data-custid'), $input.val()]);
                                            }
                                        });
                                        if (total_balance > sum) {
                                            customer_invoice.push(["0", cust_id, total_balance - sum]);
                                        }
                                    }
                                });
                                $.ajax({
                                    url: BASE_URL + "aut/postpayment/add_invoice_credit",
                                    type: "POST",
                                    data: {'customer_invoice': customer_invoice},
                                    dataType: "JSON",
                                    success: function (data)
                                    {
                                        if (data.status) {
                                            location.reload();
                                        }
                                    }
                                });
                            }
                        }
                    });
                }
            },
            processData: false,
            contentType: false
        });
    }
}
function edit_post_payment() {
    if ($("#payment_type").val() == "" && $("#check_ref").val() == "" && $("#total_amount_customer").val() == "") {
        $("#check_ref").css('border', '1px solid red');
        $(".bs-placeholder").css('border', '1px solid red');
        $("#total_amount_customer").css('border', '1px solid red');
    } else if ($("#payment_type").val() == "") {
        $(".bs-placeholder").css('border', '1px solid red');
    } else if ($("#check_ref").val() == "") {
        $("#check_ref").css('border', '1px solid red');
    } else if ($("#total_amount_customer").val() == "") {
        $("#total_amount_customer").css('border', '1px solid red');
    } else {
        $.ajax({
            url: BASE_URL + "aut/postpayment/add_post_payment",
            type: "POST",
            data: new FormData($('#post_payment_form')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            success: function (data)
            {
                if (data.status == "success") {
                    var customer_balance = [];
                    $(".customer_balnce_text").each(function () {
                        if ($(this).val() != "") {
                            customer_balance.push([$(this).attr('data-id'), $(this).val()]);
                        }
                    });
                    $.ajax({
                        url: BASE_URL + "aut/customer/update_cuatomer_balance",
                        type: "POST",
                        data: {'customer_balance': customer_balance},
                        dataType: "JSON",
                        success: function (data)
                        {
                            if (data.status) {
                                var customer_invoice = [];
                                $(".customer_payment").each(function () {
                                    $this = $(this);
                                    var cust_id = $this.attr('data-id');
                                    var total_balance = 0.00;
                                    var sum = 0;
                                    if ($this.find('.cust_bal_' + cust_id).val() != "") {
                                        total_balance = parseFloat($this.find('.cust_bal_' + cust_id).val());
                                        $this.find('.customer_invoice_text_' + cust_id).each(function () {
                                            $input = $(this);
                                            if ($input.val() != "") {
                                                sum = sum + parseFloat($input.val());
                                                customer_invoice.push([$input.attr('data-id'), $input.attr('data-custid'), $input.val()]);
                                            }
                                        });
                                        if (total_balance > sum) {
                                            customer_invoice.push(["0", cust_id, total_balance - sum]);
                                        }
                                    }
                                });
                                $.ajax({
                                    url: BASE_URL + "aut/postpayment/add_invoice_credit",
                                    type: "POST",
                                    data: {'customer_invoice': customer_invoice},
                                    dataType: "JSON",
                                    success: function (data)
                                    {
                                        if (data.status) {
                                            if (getUrlVars()["hs"] == 1) {
                                                window.location.href = BASE_URL + 'history';
                                            } else {
                                                window.location.href = BASE_URL + 'aut/customer/register/' + $('#customer_id').val();
                                            }
                                        }
                                    }
                                });
                            }
                        }
                    });
                }
            },
            processData: false,
            contentType: false
        });
    }
}
function select_type(input) {
    if ($(input).find(':selected').val() == "Adjustment" || $(input).find(':selected').val() == "Other") {
        $("#payment_description").css('background-color', '#ffffff');
        $(".post_payment_description").css('color', '#000000');
    } else {
        $("#payment_description").css('background-color', '#e9ecef');
        $(".post_payment_description").css('color', '#6c757d');
    }
}