var stepped = 0, chunks = 0, rows = 0;
var start, end;
var parser;
var pauseChecked = false;
var printStepChecked = false;
var results = [];
var complete_url;
$('#exampleModalCenter').modal({backdrop: 'static', keyboard: false});
$('#exampleModalCenter').modal('show');
var option = [];
var type = '';
var select_vaule = [];
var count = 0;
var total_count;
var rcount = 1;
var add_customer_status = false;
var maddress;
$(document).ready(function () {
    $(document).on("click", "#btn_customer", function () {
        $("#text_paste_area").show();
        $("#exampleModalCenter").modal('hide');
        $("#csv").focus();
        type = 'Customer';
        complete_url = BASE_URL + 'aut/customer';
        option.push('Name', 'Job ID', 'PO Number', 'Person', 'Email', 'Phone', 'Attn/Dpt', 'Mailing Address Line 1', 'Mailing Address Line 2', 'Mailing Address Line 3', 'Service Address Line 1', 'Service Address Line 2', 'Service Address Line 3', 'Discount', 'Minimum', 'State Tax', 'County Tax', 'Email Bill to', 'Notes');
    });
    $(document).on("click", "#btn_item", function () {
        $("#text_paste_area").show();
        $("#exampleModalCenter").modal('hide');
        $("#csv").focus();
        type = 'Item';
        option.push('Name', 'Description', 'Bill Rate', 'Can Discount?', 'Type');
        complete_url = BASE_URL + 'aut/administration/item';
    });
    $(document).on('click', '#btn_undo', function () {
        $("#text_paste_area").show();
        $("#csv").focus().val('');
        $(".output_div").hide();
    });
    $(document).on('click', '.btn_next_step', function () {
        $("#import_heading").hide();
        $('#btn_undo').hide();
        $(".btn_next_step").hide();
        $('#map_heading').show();
        $('.select_option').show();
        $(".btn_map_next").show();
    });
    $(document).on('click', '.btn_next_form', function () {
        if (type == 'Item') {
            $(".selectpicker1").each(function (index, element) {
                select_vaule.push([$(element).val(), index]);
            });
            $('.output_div').hide();
            $(".btn_map_next").hide();
            $('.item_form_div').show();
            $(".total_number_record").text(results.data.length);
            total_count = results.data.length;
            next_item(count);
        } else {
            $(".selectpicker1").each(function (index, element) {
                select_vaule.push([$(element).val(), index]);
            });
            $('.output_div').hide();
            $(".btn_map_next").hide();
            $('.customer_form_div').show();
            $(".total_number_record").text(results.data.length);
            total_count = results.data.length;
            $(":input").inputmask();
            $("#customer_phone").inputmask({"mask": "999-999-9999"});
            $('.tooltipped').popover({placement: "top", trigger: 'focus'});
            $(".total_number_record").text(results.data.length);
            next_customer(count);
        }
    });
    $(document).on('click', '#btn_item_skip', function () {
        if (total_count != rcount) {
            next_item(rcount);
            rcount = rcount + 1;
            $(".count_record").text(rcount);
        } else {
            if (total_count + 1 == rcount + 1) {
                final_respone('item_form_div', 'Item');
            }
        }
    });
    $(document).on('click', '#btn_item_save', function () {
        add_item();
        if (total_count != rcount) {
            next_item(rcount);
            rcount = rcount + 1;
            $(".count_record").text(rcount);
        } else {
            if (total_count == rcount) {
                final_respone('item_form_div', 'Item');
            }
        }
    });
    $(document).on('click', '#btn_customer_skip', function () {
        if (total_count != rcount) {
            next_customer(rcount);
            rcount = rcount + 1;
            $(".count_record").text(rcount);
        } else {
            if (total_count == rcount) {
                final_respone('customer_form_div', 'Customer');
            }
        }
    });
    $(document).on('click', '#btn_customer_save', function () {
        $.ajax({
            url: BASE_URL + "aut/customer/ajax_add_edit_customer",
            type: "POST",
            data: new FormData($('#add_customer')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            beforeSend: function (xhr) {
                $('#h_cass_icon').html('<i class="fa fa-spinner fa-spin fa-lg"></i>').show();
                $('#m_cass_icon').html('<i class="fa fa-spinner fa-spin fa-lg"></i>').show();
                $("#btn_customer_save").html('<i class="fas fa-spinner fa-spin"></i>');
            },
            success: function (data, msg)
            {
                maddress = data.m_address;
                if (typeof (maddress.address) != "undefined" && maddress.address !== null && maddress.address != "") {
                    var tm = maddress.type;
                    $("#mailingAutoComplete").val(maddress.address);
                    address_response(tm);
                } else {
                    var tm = maddress.type1;
                    $("#mailingAutoComplete").val(maddress.address);
                    address_response(tm);
                }
                var t = data.type;
                $("." + t + "_address").val(data.address);
                $('#' + t + '_cass_icon').html(data.cass_icon);
                $('#' + t + '_cass_icon').attr('data-original-title', data.cass_errors).tooltip('show');
                setTimeout(function () {
                    $('#' + t + '_cass_icon').hide();
                    $('#' + t + '_cass_icon').tooltip('hide', '2000');
                }, 2000);
                if (data.status == "success") {
                    $(".h_address").val(data.m_address);
                    $(".m_address").val(data.s_address);
                    $('#m_cass_icon').html(data.s_cass_icon);
                    $('#m_cass_icon').attr('data-original-title', data.s_cass_errors).tooltip('show');
                    $('#h_cass_icon').html(data.m_cass_icon);
                    $('#h_cass_icon').attr('data-original-title', data.m_cass_errors).tooltip('show');
                    setTimeout(function () {
                        $('#m_cass_icon').hide();
                        $('#m_cass_icon').tooltip('dispose', '2000');
                        $('#h_cass_icon').hide();
                        $('#h_cass_icon').tooltip('dispose', '2000');
                    }, 2000);
                    $("#btn_customer_save").css('border', '2px solid #264c12');
                    $("#btn_customer_save").css('color', '#264c12');
                    $("#btn_customer_save").html('<i class="fas fa-check"></i>');
                    setTimeout(function () {
                        $("#btn_customer_save").html('Save');
                        if (total_count != rcount) {
                            next_customer(rcount);
                            rcount = rcount + 1;
                            $(".count_record").text(rcount);
                        } else {
                            if (total_count == rcount) {
                                final_respone('customer_form_div', 'Customer');
                            }
                        }
                    }, 1000);
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
    });
    $("#csv").bind('paste', function (e) {
        var self = this;
        setTimeout(function (e) {
            var excelData = document.getElementById('csv').value;
            // split into rows
            excelRow = excelData.split(String.fromCharCode(10));

            // split rows into columns
            for (i = 0; i < excelRow.length; i++) {
                excelRow[i] = excelRow[i].split(String.fromCharCode(9));
            }
            if (excelRow.length >= 2) {
                // start to create the HTML table
                var myTable = document.createElement("table");
                myTable.setAttribute('class', 'table');
                var myTbody = document.createElement("tbody");
                var selectRow = document.createElement("tr");
                selectRow.setAttribute("class", "select_option");
                // Loop over the rows
                for (i = 0; i < excelRow.length; i++) {
                    // create a row in the HTML table
                    var myRow = document.createElement("tr");
                    // Loop over the columns and add TD to the TR
                    for (j = 0; j < excelRow[i].length; j++) {
                        // Loop over the row columns
                        var myCell = document.createElement("td");
                        var selectcell = document.createElement("td");
                        myCell.innerHTML = excelRow[i][j];
                        var select = document.createElement("select");
                        select.setAttribute('class', 'form-control selectpicker1');
                        select.setAttribute('data-rcount', j);
                        select.setAttribute('name', 'selectpicker[]');
                        select.setAttribute('data-live-search', 'true');
                        select.setAttribute('data-size', '5');
                        select.setAttribute('data-width', '100%');
                        select.setAttribute('title', 'Map');
                        $.each(option, function (i) {
                            var myoption = document.createElement("option");
                            myoption.innerHTML = option[i];
                            myoption.setAttribute('value', option[i]);
                            select.appendChild(myoption);
                        });
                        if (i == 0) {
                            selectcell.appendChild(select);
                        }
                        myRow.appendChild(myCell);
                        if (i == 0) {
                            selectRow.appendChild(selectcell);
                        }
                    }
                    myTbody.appendChild(myRow);
                    if (i == 0) {
                        myTbody.appendChild(selectRow);
                    }
                }
                myTable.appendChild(myTbody);
                $(".output").html('');
                $(".output").append(myTable);
                $(".output_div").show();
                $("#text_paste_area").hide();
                $('.selectpicker1').selectpicker();
                $('.select_option').hide();
                if (excelRow.length <= 3) {
                    myTable.setAttribute('class', 'mb-6');
                }
            } else {
                $(".error_message").show();
            }
        }, 0);
    });

    $(function () {
        $("#csv").bind('paste', function (e) {
            var self = this;
            setTimeout(function (e) {
                stepped = 0;
                chunks = 0;
                rows = 0;
                var txt = $('#csv').val();
                var localChunkSize = $('#localChunkSize').val();
                var remoteChunkSize = $('#remoteChunkSize').val();
                var config = buildConfig();
                // NOTE: Chunk size does not get reset if changed and then set back to empty/default value
                if (localChunkSize)
                    Papa.LocalChunkSize = localChunkSize;
                if (remoteChunkSize)
                    Papa.RemoteChunkSize = remoteChunkSize;
                pauseChecked = $('#step-pause').prop('checked');
                printStepChecked = $('#print-steps').prop('checked');
                start = performance.now();
                results = Papa.parse(txt, config);
                results.data.splice(0, 1);
            }, 0);
        });
    });
    $(document).on("click", ".view_all_list", function () {
        $(".output_div").slideToggle(800);
        $(".select_option").hide();
        $("#map_heading").text(type + " Listing");
        $(this).find('i').toggleClass("rotate-180");
    });
});
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
                $("#btn_item_save").html('<i class="fas fa-spinner fa-spin"></i>');
            },
            success: function (data)
            {
                $("#btn_item_save").html('<i class="fas fa-check"></i>');
                setTimeout(function () {
                    $("#btn_item_save").html('Save');
                }, 1000);
            },
            processData: false,
            contentType: false
        });
    }
}
function buildConfig() {
    return {
        delimiter: '\t',
        newline: '',
        header: false,
        dynamicTyping: false,
        step: false,
        worker: false,
        complete: completeFn,
        error: errorFn,
        download: false,
        fastMode: false,
        skipEmptyLines: true,
        chunk: false,
        beforeFirstChunk: undefined,
    };
}
function errorFn(error, file) {
    console.log("ERROR:", error, file);
}
function completeFn() {
    end = performance.now();
    if (!$('#stream').prop('checked')
            && !$('#chunk').prop('checked')
            && arguments[0]
            && arguments[0].data)
        rows = arguments[0].data.length;
}
function next_customer(increment) {
    var maddress_array = [];
    var saddress_array = [];

    $.each(select_vaule, function (i, item) {
        if (item[0] == "Name") {
            $("#customer_name").val(results.data[increment][item[1]]);
        } else if (item[0] == "Job ID") {
            $("#job_id").val(results.data[increment][item[1]]);
        } else if (item[0] == "PO Number") {
            $("#po_number").val(results.data[increment][item[1]]);
        } else if (item[0] == "Person") {
            $("#person_name").val(results.data[increment][item[1]]);
        } else if (item[0] == "Email") {
            $("#person_email").val(results.data[increment][item[1]]);
        } else if (item[0] == "Phone") {
            $("#customer_phone").val(parseInt(results.data[increment][item[1]].replace(/[^\d.]/g, ''), 10));
        } else if (item[0] == "Attn/Dpt") {
            $("#attn_dpt").val(results.data[increment][item[1]]);
        } else if (item[0] == "Mailing Address Line 1" || item[0] == "Mailing Address Line 2" || item[0] == "Mailing Address Line 3") {
            if (results.data[increment][item[1]] != "") {
                maddress_array.push(results.data[increment][item[1]]);
            }
        } else if (item[0] == "Service Address Line 1" || item[0] == "Service Address Line 2" || item[0] == "Service Address Line 3") {
            if (results.data[increment][item[1]] != "") {
                saddress_array.push(results.data[increment][item[1]]);
            }
        } else if (item[0] == "Discount") {
            $("#customer_discount").val(parseFloat(results.data[increment][item[1]]));
        } else if (item[0] == "Minimum") {
            $("#minimum").val(parseInt(results.data[increment][item[1]]));
        } else if (item[0] == "State Tax") {
            $("#state_tax").val(parseFloat(results.data[increment][item[1]]));
        } else if (item[0] == "County Tax") {
            $("#country_tax").val(parseFloat(results.data[increment][item[1]]));
        } else if (item[0] == "Email Bill to") {
            $("#email_bill").val(results.data[increment][item[1]]);
        } else if (item[0] == "Notes") {
            $("#notes").val(results.data[increment][item[1]]);
        }
    });
    var m1, m2, m3;
    $.each(maddress_array, function (i, output) {
        if (i == 0) {
            m1 = output;
        } else if (i == 1) {
            m2 = output;
        } else if (i == 2) {
            m3 = output;
        }
    });
    var s1, s2, s3;
    $.each(saddress_array, function (i, output) {
        if (i == 0) {
            s1 = output;
        } else if (i == 1) {
            s2 = output;
        } else if (i == 2) {
            s3 = output;
        }
    });
    $("#mailingAutoComplete").val((m1 ? m1 : "") + (m2 ? "\n" + m2 : "") + (m3 ? "\n" + m3 : ""));
    $("#serviceAutoComplete").val((s1 ? s1 : "") + (s2 ? "\n" + s2 : "") + (s3 ? "\n" + s3 : ""));
    mailing_address();
    service_address();
}
function next_item(increment) {
    $.each(select_vaule, function (i, item) {
        if (item[0] == "Name") {
            $("#item_name").val(results.data[increment][item[1]]);
        } else if (item[0] == "Description") {
            $("#description").val(results.data[increment][item[1]]);
        } else if (item[0] == "Bill Rate") {
            $("#bill_rate").val(results.data[increment][item[1]]);
        } else if (item[0] == "Can Disrcount?") {
            $("#can_discount").selectpicker('val', (results.data[increment][item[1]] == 'Y' || results.data[increment][item[1]] == 'y' ? '1' : '0'));
        } else if (item[0] == "Type") {
            $("#item_type").selectpicker('val', results.data[increment][item[1]]);
        }
    });
}
function address_response(tm) {
    $('#' + tm + '_cass_icon').html(maddress.cass_icon1);
    $('#' + tm + '_cass_icon').attr('data-original-title', maddress.cass_errors1).tooltip('show');
    $("#btn_customer_save").html('<i class="fas fa-times"></i>');
    $("#btn_customer_save").css('color', 'red');
    $("#btn_customer_save").css('border', '2px solid #ff0000');
    setTimeout(function () {
        $('#' + tm + '_cass_icon').hide();
        $('#' + tm + '_cass_icon').tooltip('hide', '2000');
        $("#btn_customer_save").css('color', '#264c12');
        $("#btn_customer_save").css('border', '2px solid #264c12');
        $("#btn_customer_save").html('Save');
    }, 2000);
}
function final_respone(hide, type) {
    $('.' + hide).hide();
    swal({title: type + " successfully imported.!", text: "", type:
                "success"}).then(function () {
        location.reload();
    });
}
function mailing_address() {
    if ($('.mailling_address').val() != "") {
        $('#h_cass_icon').html('<i class="fa fa-spinner fa-spin fa-lg"></i>').show();
        setTimeout(function () {
            var t = $('.mailling_address').data('type');
            address = $('.mailling_address').val();

            //$('#' + t + '_cass_error').attr('class','');
            $.ajax({
                url: BASE_URL + "aut/customer/mailling_address",
                type: "POST",
                data: {address: address},
                dataType: "JSON",
                success: function (data)
                {
                    $('.mailling_address').val(data.address);
                    if (data.hasOwnProperty('cass_class1')) {
                        $('#' + t + '_cass_icon').html(data.cass_icon1);
                        $('#' + t + '_cass_icon').attr('data-original-title', data.cass_errors1).tooltip('show');
                    } else {
                        $('#' + t + '_cass_icon').html(data.cass_icon);
                        $('#' + t + '_cass_icon').attr('data-original-title', data.cass_errors).tooltip('show');
                    }
                    $('#' + t + '_cass_icon').html(data.cass_icon);
                    $('#' + t + '_cass_icon').attr('data-original-title', data.cass_errors).tooltip('show');
                    setTimeout(function () {
                        $('#' + t + '_cass_icon').hide();
                        $('#' + t + '_cass_icon').tooltip('hide', '2000');
                    }, 2000);
                }
            });
        }, 1000);
    }
}
function service_address() {
    if ($(".service_address").val() != "") {
        $('#m_cass_icon').html('<i class="fa fa-spinner fa-spin fa-lg"></i>').show();
        setTimeout(function () {
            var t = $(".service_address").data('type');
            address = $(".service_address").val();

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
                    setTimeout(function () {
                        $('#' + t + '_cass_icon').hide();
                        $('#' + t + '_cass_icon').tooltip('hide', '2000');
                    }, 2000);
                }
            });
        }, 1000);
    }
}