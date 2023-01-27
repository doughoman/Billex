var start_time = '';
var toggle = toggle1 = !0;
var t;
var seconds = new_seconds = 0;
var minutes = 0;
var hours = 0;
var start = 0;
var select_type = '';
var first_time = '';
var input1;
var input2;
var codeHex = '';
var old_color = '';
var old_minute = weekcount = 0;
var clock = '';
var auto_timer;
var old = old_minute;
var new_time = '';
var hours_count = 0;
var a = graphcount = 0;
var input = $('#input-a');
var chart;
var chart1;
var running_status = false;
var select_time_type;
var old_running_time;
var not_stop = false;
var graph_type = '';
var timer_variable = [];
var pin_timer_project = false;

$('#showLeft').click(function () {
    $('.content-wrapper').toggleClass('push-toleft');
    $('.push-left').toggleClass('pushleft-open');
    return false;
});
$('.backBtn').click(function () {
    $('.content-wrapper').removeClass('push-toleft');
    $('.push-left').removeClass('pushleft-open');
});
$(document).on('click', '.content-wrapper', function (e) {
    if ($('.push-left').hasClass('pushleft-open')) {
        $('.content-wrapper').removeClass('push-toleft');
        $('.push-left').removeClass('pushleft-open');
        return false;
    }
});
if (getUrlVars()["calender"] == 1) {
    if ($(window).width() < 767) {
        $('.admin-contain-main-div').addClass('admin-contain-main-div_new');
        $('body.sticky-footer').css('margin-bottom', '15px');
    } else {
        $('body.sticky-footer').css('margin-bottom', '0px');
        $('.fixed-top2').hide();
        $('.timer_main_div').css('padding-top', '0px');
        $('.admin-contain-main-div').css('padding', '15px');
    }
    $('.calender-main').animate({scrollTop: '+=480px'}, 800);
    if (getUrlVars()["week"]) {
        $(".week_count").val(getUrlVars()["week"]);
    } else {
        $(".week_count").val(0);
    }
    $.ajax({
        url: BASE_URL + "aut/timer/get_week_view",
        type: "POST",
        data: {'weekcount': $(".week_count").val()},
        success: function (html)
        {
            loadWeekHtml(html);
        }
    });
    var new_url = BASE_URL + 'timer';
    window.history.pushState("data", "Title", new_url);
    $(".no_calender,.timer_search_ico i,.timer_profile_image").hide();
    $(".claender_main_div").show();
    $(".footer-section").hide();
}
if (getUrlVars()["list"] == 1) {
    if ($(window).width() < 767) {
        $('.admin-contain-main-div').addClass('admin-contain-main-div_new');
        $('body.sticky-footer').css('margin-bottom', '15px');
    } else {
        $('.timer_search_ico .back_graph').show();
    }
    if (getUrlVars()["date"]) {
        date = getUrlVars()["date"];
    } else {
        date = $('.today_date').text();
    }
    $.ajax({
        url: BASE_URL + "aut/timer/day_timer",
        type: "POST",
        data: {date: date, status: 'nope'},
        dataType: "JSON",
        success: function (data)
        {
            set_today_data(data);
        }
    });
    var new_url = BASE_URL + 'timer';
    window.history.pushState("data", "Title", new_url);
    $(".today_timer").show();
    $(".no_calender,.timer_search_ico #showLeft,.timer_profile_image").hide();
}
if (getUrlVars()["custid"]) {
    var custid = getUrlVars()["custid"];
    var projectid = getUrlVars()["projectid"];
    var running_id = getUrlVars()["running_id"];
    var new_url = BASE_URL + 'timer';
    window.history.pushState("data", "Title", new_url);
    $(".main-timer-div").hide();
    clearTimeout(t);
    $.ajax({
        url: BASE_URL + "aut/timer/stop_timer_project",
        type: "POST",
        data: {'timer_project_id': running_id},
        dataType: "JSON",
        success: function (data)
        {
            not_stop = false;
            $(".running_timer_list").html('');
        }
    });
    setTimeout(function () {
        $.ajax({
            url: BASE_URL + "aut/timer/add_timer",
            type: "POST",
            data: {'timer_project_id': projectid, 'customer_id': custid},
            dataType: "JSON",
            success: function (data)
            {
                set_new_timer(data);
            }
        });
    }, 500);
}
$(document).on("click", ".start_time,.end_time", function (e) {
    $(".change_time_type").prop('checked', false);
    $(".change_time_type").val(0);
    $('.rotaryswitchPlugin .switch').css('background', 'url(' + BASE_URL + '/images/darkBigFront@2x.png) no-repeat');
    old_color = $(".main-timer-div").css('background-color');
    selected_time_type = $(this).data('type');
    if ($(this).data('type') != "in_modal") {
        $(".btn_save_time").attr('id', 'save_new_value');
        select_type = $(this).attr('data-type');
        old_color = $(this).parents(".timer_running_new_div").css('background-color');
        color_project_id = $(this).parents('.timer_running_new_div').find(".timer_project_id").val();
        if (select_type != "start") {
            $(this).parents('.timer_running_new_div').find(".colon").addClass('fix_colon');
            $(this).parents('.timer_running_new_div').find(".start").show();
            $(this).parents('.timer_running_new_div').find(".stop").hide();
            $(this).parents('.timer_running_new_div').css('background-color', '#ffffff');
            $(this).parents(".main-timer-div").css('border', '3px solid #' + $(this).parents(".main-timer-div").css('background-color'));
            $(this).parents('.timer_running_new_div').find(".timer-footer").css('color', '#007bff');
            $(this).parents('.timer_running_new_div').find(".timer-header").css('color', '#000000');
            $(this).parents('.timer_running_new_div').find('.time').css('color', '#000000');
            $(this).parents('.timer_running_new_div').attr('data-stop', 'true');
        }
        clearTimeout(obj['t_' + color_project_id]);
        removeArrayValue(timer_variable, color_project_id);
    } else {
        $(".btn_save_time").attr('id', 'set_new_value');
        $(".btn_save_time").attr('data-pos', 'new_timer');
        select_time_type = $(this).parents('.input-group').find('.form-control').data('timetype');
    }
});

$('#edit_start_end_time').on('hidden.bs.modal', function () {
    if ($(this).data('pos') != 'in_modal') {
        $(".running_timer_project_" + color_project_id).css('background-color', old_color);
        $(".running_timer_project_" + color_project_id).css('border', '3px solid ' + old_color);
        $(".running_timer_project_" + color_project_id + " .timer-footer").css('color', '#ffffff');
        $(".running_timer_project_" + color_project_id + " .timer-header").css('color', '#ffffff');
        $(".running_timer_project_" + color_project_id + " .time").css('color', '#ffffff');
        $(".running_timer_project_" + color_project_id + " .start").hide();
        $(".running_timer_project_" + color_project_id + " .stop").show();
        $(".running_timer_project_" + color_project_id + " .colon").removeClass('fix_colon');
        $(".running_timer_project_" + color_project_id).attr('data-stop', 'false');
        timer_variable.push(color_project_id);
        $.each(timer_variable, function (i, value) {
            clearTimeout(obj['t_' + value]);
        });
        if (selected_time_type != "in_modal") {
            new_add();
        }
    }
});
$(document).on("click", "#start,.start", function (e) {
    new_add();
    $(this).parents('.timer_running_new_div').find("#start").hide();
    $(this).parents('.timer_running_new_div').find("#stop").show();
    timer1();
    $.ajax({
        url: BASE_URL + "aut/timer/start_timer",
        type: "POST",
        data: {'timer_id': $(this).parents('.timer_running_new_div').find(".timer_id").val(), 'customer_id': $(this).parents('.timer_running_new_div').find(".customer_id").val(), 'timer_project_id': $(this).parents('.timer_running_new_div').find('.timer_project_id').val()},
        dataType: "JSON",
        success: function (data)
        {
            $(".running_timer_project_" + data.timer_project_id).css('background-color', '#' + data.color_dec);
            $(".running_timer_project_" + data.timer_project_id).css('border', '3px solid #' + data.color_dec);
            $(".running_timer_project_" + data.timer_project_id + " .timer-footer").css('color', '#ffffff');
            $(".running_timer_project_" + data.timer_project_id + " .timer-header").css('color', '#ffffff');
            $(".running_timer_project_" + data.timer_project_id + " .time").css('color', '#ffffff');
            $(".running_timer_project_" + data.timer_project_id + " .start").hide();
            $(".running_timer_project_" + data.timer_project_id + " .stop").show();
            $(".running_timer_project_" + data.timer_project_id + " .colon").removeClass('fix_colon');
            $(".running_timer_project_" + data.timer_project_id).attr('data-stop', 'false');
            timer_variable.push(data.timer_project_id);
            $.each(timer_variable, function (i, value) {
                clearTimeout(obj['t_' + value]);
            });
            new_add();
        }
    });
});
$(document).on("click", ".stop", function () {
    $this = $(this);
    $.ajax({
        url: BASE_URL + "aut/timer/stop_all_timer",
        type: "POST",
        data: {'timer_id': $(this).parents('.timer_running_new_div').find('.timer_id').val(), 'customer_id': $(this).parents('.timer_running_new_div').find('.customer_id').val(), 'timer_project_id': $(this).parents('.timer_running_new_div').find('.timer_project_id').val()},
        dataType: "JSON",
        success: function (data)
        {
            if (data.alert == 1) {
                bootbox.confirm({
                    closeButton: false,
                    className: "sign_up_alert",
                    backdrop: true,
                    onEscape: true,
                    message: "Timer was already stopped.",
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
                        location.reload();
                    }
                });
            }

            $this.parents('.timer_running_new_div').remove();
            if ($(".running_timer_list").find('.timer_running_new_div').length == 0) {
                timer_variable = [];
            }
        }
    });
});
$(document).on("click", "#stop", function () {
    old_color = $(".main-timer-div").css('background-color');
    $("#colon").css('visibility', 'visible');
    clearTimeout(t);
    $this = $(this);
    running_status = true;
    $.ajax({
        url: BASE_URL + "aut/timer/stop_all_timer",
        type: "POST",
        data: {'timer_id': $(this).parents('.timer_running_new_div').find('.timer_id').val(), 'customer_id': $(this).parents('.timer_running_new_div').find('.customer_id').val(), 'timer_project_id': $(this).parents('.timer_running_new_div').find('.timer_project_id').val()},
        dataType: "JSON",
        success: function (data)
        {
            $(".new_main_box_" + $('#timer_project_id').val()).show();
            $.ajax({
                url: BASE_URL + "aut/timer/update_running_timer",
                type: "POST",
                data: {},
                dataType: "JSON",
                success: function (data)
                {
                    if (data.status == "success") {
                        if (data.customer_ids.length > 0) {
                            if ($(".pin_timer_start_new").hasClass("customer_running_timer_" + data.customer_ids[0])) {
                                $(".customer_running_timer_" + data.customer_ids[0]).trigger('click');
                            } else {
                                $.ajax({
                                    url: BASE_URL + "aut/timer/start_running_timer",
                                    type: "POST",
                                    data: {'customer_id': data.customer_ids[0], 'timer_project_id': data.timer_project_id[0]},
                                    dataType: "JSON",
                                    success: function (data)
                                    {
                                        $.ajax({
                                            url: BASE_URL + "aut/timer/get_running_timer",
                                            type: "POST",
                                            data: {},
                                            dataType: "JSON",
                                            success: function (data)
                                            {
                                                show_running_timer(data);
                                            }
                                        });
                                        set_new_timer(data);
                                    }
                                });
                            }
                        } else {
                            $this.parents('.main-timer-div').hide();
                        }
                        requestData();
                    }
                    if (data.customer_ids.length == 0) {
                        running_status = false;
                        clearTimeout(auto_timer);
                        $this.parents('.main-timer-div').hide();
                        location.reload();
                    }
                },
                error: function () {
                    location.reload();
                }
            });
        }
    });
});
$(document).on("click", ".name-ofcust h6,.time-count p", function (e) {
    color_project_id = $(this).parents('.timer_running_new_div').find(".timer_project_id").val();
    clearTimeout(obj['t_' + color_project_id]);
    removeArrayValue(timer_variable, color_project_id);
    old_color = $(".main-timer-div").css('background-color');
    clearTimeout(auto_timer);
    $(this).parents('.timer_running_new_div').find(".colon").addClass('fix_colon');
    $(this).parents('.timer_running_new_div').find(".start").show();
    $(this).parents('.timer_running_new_div').find(".stop").hide();
    $(this).parents('.timer_running_new_div').css('background-color', '#ffffff');
    $(this).parents(".main-timer-div").css('border', '3px solid #' + $(this).parents(".main-timer-div").css('background-color'));
    $(this).parents('.timer_running_new_div').find(".timer-footer").css('color', '#007bff');
    $(this).parents('.timer_running_new_div').find(".timer-header").css('color', '#000000');
    $(this).parents('.timer_running_new_div').find('.time').css('color', '#000000');
    $(this).parents('.timer_running_new_div').attr('data-stop', 'true');
    running_status = true;
    $.ajax({
        url: BASE_URL + "aut/timer/stop_timer",
        type: "POST",
        data: {'timer_id': $(this).parents('.timer_running_new_div').find('.timer_id').val(), 'customer_id': $(this).parents('.timer_running_new_div').find('.customer_id').val(), 'timer_project_id': $(this).parents('.timer_running_new_div').find('.timer_project_id').val()},
        dataType: "JSON",
        success: function (data)
        {
            if (data.alert == 1) {
                bootbox.confirm({
                    closeButton: false,
                    className: "sign_up_alert",
                    backdrop: true,
                    onEscape: true,
                    message: "Timer was already stopped.",
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
                        location.reload();
                    }
                });
            } else {
                requestData();
            }
        }
    });
});
$("#input-a").inputmask({"mask": "99:99"});
$("#edit_start_timer,#edit_end_timer").inputmask({"mask": "99:99 AA"});
$(document).on("click", ".calender_edit", function () {
    if ($(this).data('type') == "today") {
        select_list_type = "today";
    } else {
        select_list_type = "week";
    }
    $(".no_pin_timer").show();
    $('.property-color').hide();
    $(".timer_project_box").css('pointer-events', 'unset');
    $this = $(this);
    $('#save_timer').attr('onclick', 'save_calender_timer();');
    $.ajax({
        url: BASE_URL + "aut/timer/get_timer",
        type: "POST",
        data: {'timer_id': $(this).attr('data-id')},
        dataType: "JSON",
        success: function (data)
        {
            start_time = data.time_start;
            first_time = data.time_start;
            last_time = data.time_stop;
            var start = new Date(data.time_start.replace(/-/g, '/'));
            var end = new Date(data.time_stop.replace(/-/g, '/'));
            var date_diff_indays = function (date1, date2) {
                dt1 = new Date(date1);
                dt2 = new Date(date2);
                return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate())) / (1000 * 60 * 60 * 24));
            };
            $('.popup_end_label').text((date_diff_indays(start, end) > 0 ? "End Time (+" + date_diff_indays(start, end) + " day)" : "End Time"));
            $(".invite_type_option").removeClass('active');
            $("#edit_timer_id").val(data.timer_id);
            $("#customer_id").val(data.customer_id);
            $("#pickcolor").val('#' + data.color_dec);
            $(".color-holder").css('background', '#' + data.color_dec);
            $("#timer_project_id").val(data.timer_project_id);
            if ((data.identifier == "" && data.po == "") || (data.identifier == "" || data.po == "")) {
                var sap = '';
            } else {
                var sap = ' / ';
            }
            $(".new_timer_id").val('');
            $(".customer_name").text(data.name);
            $(".id_po").text(data.identifier + '' + sap + '' + data.po);
            $(".timer_project_box").css('border', '3px solid #' + data.color_dec);
            if (data.item_id > 0) {
                $(".item_chargetype").text(data.item_name);
            }
            if (data.item_id == 0) {
                $(".item_chargetype").text(data.chargetype);
            }
            $("#timer_description1").val(data.description);
            var asdate = new Date(data.time_start.replace(/-/g, '/')).toString();
            var sdate = asdate.split(' ');
            var diffDays = Math.ceil(Math.abs(new Date() - new Date(data.time_start)) / (1000 * 60 * 60 * 24));
            $("#datepicker").datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'mm/dd/yyyy',
                endDate: "today",
                startDate: '-' + (diffDays - 1) + 'd'
            }).datepicker('update', getFormattedDate(data.time_start)).on('changeDate', set_charges);
            $("#time_start").val(sdate[0] + ', ' + sdate[1] + ' ' + sdate[2] + ', ' + sdate[3]);
            d = new Date(data.time_start.replace(/-/g, '/'));
            datetext = d.toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
            datetext1 = datetext.split(' ')[0];
            start_hours_set = datetext1.split(':')[0];
            start_minute_set = datetext1.split(':')[1];
            $('#edit_start_timer').val((datetext1.split(':')[0] ? (datetext1.split(':')[0] > 9 ? datetext1.split(':')[0] : "0" + datetext1.split(':')[0]) : "0") + datetext1.split(':')[1] + ' ' + datetext.split(' ')[1]);
            $('#edit_start_timer').parent().find('.edit_start_time').attr('data-default', datetext.split(':')[0] + ':' + datetext.split(':')[1] + ' ' + datetext.split(' ')[1]);
            d = new Date(data.time_stop.replace(/-/g, '/'));
            datetext = d.toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
            datetext2 = datetext.split(' ')[0];
            end_hours_set = datetext2.split(':')[0];
            end_minute_set = datetext2.split(':')[0];
            $('#edit_end_timer').val((datetext2.split(':')[0] ? (datetext2.split(':')[0] > 9 ? datetext2.split(':')[0] : "0" + datetext2.split(':')[0]) : "0") + datetext2.split(':')[1] + ' ' + datetext.split(' ')[1]);
            $('#edit_end_timer').parent().find('.end_start_time').attr('data-default', datetext.split(':')[0] + ':' + datetext.split(':')[1] + ' ' + datetext.split(' ')[1]);
            $('#edit_start_timer').attr('data-newtimetype', 'day');
            $('#edit_start_timer').parent().find('.edit_start_time').attr('data-newtimetype', 'day');
            $('#edit_end_timer').attr('data-newtimetype', 'day');
            $('#edit_end_timer').parent().find('.end_start_time').attr('data-newtimetype', 'day');
            var time_stop = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " " + datetext;
            $(".stop_new_value").val(time_stop);
            if (data.locked == 1) {

                $("#locked_unlocked").prop("checked", true);
            } else {

                $("#locked_unlocked").prop("checked", false);
            }
            $('.locked_value').val(data.locked);
            if (data.sticky_order == 1) {

                $("#sticky_order").prop('checked', true);
            } else {

                $("#sticky_order").prop('checked', false);
            }
            $('.sticky_value').val(data.sticky_order);
            $("#delete_timer").parent('div').show();
            $("#edit_detail_modal").modal('show');
        }
    });
});
$(document).on("click", ".add_day_timer", function () {
    $.ajax({
        url: BASE_URL + "aut/timer/get_today_timer",
        type: "POST",
        data: {date: $('.today_new_date').val(), 'sort': 1},
        dataType: "JSON",
        success: function (data)
        {
            new_date = new Date($('.today_new_date').val() + '-' + new Date().getFullYear());
            end_time_array = [];
            $.each(data, function (i, value) {
                var start_time = new Date(value.time_start.replace(/-/g, '/'));
                var end_time = new Date(value.time_stop.replace(/-/g, '/'));
                if (new_date.getDate() != end_time.getDate()) {
                    end_time = start_time.getFullYear() + '-' + (start_time.getMonth() + 1) + '-' + start_time.getDate() + ' 23:59:59';
                    end_time = new Date(end_time);
                }
                var diffMs = (end_time - start_time); // milliseconds between now & Christmas
                var diffMins = Math.round(diffMs / 60000); // minutes
                if (diffMins > 1) {
                    var attrs = {endDate: new Date(end_time)};
                    end_time_array.push(attrs);
                }
            });
            if (end_time_array.length != 0) {
                lastEl = end_time_array[end_time_array.length - 1]['endDate'];
                end_time = dateAdd(lastEl, 'hour', 1);
                start_time = lastEl.toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: 'numeric'});
                end_time = end_time.toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: 'numeric'});
            } else {
                start_time = '12:00 PM';
                end_time = '01:00 PM';
            }
            $('.property-color').hide();
            select_list_type = "today";
            $(".color-holder").css('background', '#000000');
            $(".new_timer_id").val('');
            $(".customer_name").text('Select Project');
            $(".id_po").text('');
            $(".timer_project_box").css('border', '3px solid #000000');
            $("#timer_description1").val('');
            $(".item_chargetype").text('');
            asdate = new Date($(".today_date").text() + " " + new Date().getFullYear()).toString();
            var sdate = asdate.split(' ');
            $("#time_start").val(sdate[0] + ', ' + sdate[1] + ' ' + sdate[2] + ', ' + sdate[3]);
            $('#edit_start_timer').val(start_time);
            $('#edit_start_timer').parent().find('.edit_start_time').attr('data-default', start_time);
            $('#edit_end_timer').val(end_time);
            $('#edit_start_timer').attr('data-newtimetype', 'day');
            $('#edit_start_timer').parent().find('.edit_start_time').attr('data-newtimetype', 'day');
            $('#edit_end_timer').attr('data-newtimetype', 'day');
            $('#edit_end_timer').parent().find('.end_start_time').attr('data-newtimetype', 'day');
            $('#edit_end_timer').parent().find('.end_start_time').attr('data-default', end_time);
            asdate = new Date($(".today_date").text());
            var time_stop = asdate.getFullYear() + "-" + (asdate.getMonth() + 1) + "-" + asdate.getDate() + " " + end_time;
            $(".stop_new_value").val(time_stop);
            $("#unlocked").attr("checked", true);
            $("#unlocked").parent().addClass('active');
            $("#sticky_order").prop('checked', true);
            $("#delete_timer").parent('div').hide();
            $('#save_timer').attr('onclick', 'create_new_timer();');
            $(".new_project_list").show();
            $(".timer_project_box").html('<div class="timer-header" style="color:#000000;"><div class="name-ofcust-new"><h6 class="customer_name">Select Project</h6><p class="id_po"></p><p class="item_chargetype"></p></div></div>');
            $("#edit_detail_modal").modal('show');
        }
    });
});
$(document).on("click", ".col-time", function () {
    $('.property-color').hide();
    var calenderTime = ($(this).index() ? ($(this).index() > 9 ? $(this).index() : "0" + $(this).index()) : "0");
    if (calenderTime == 0) {
        calenderTime = "0" + calenderTime;
    }
    today = new Date();
    myToday = new Date(today.getFullYear(), today.getMonth(), today.getDate(), calenderTime, "00", "00");
    startTime = myToday.toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: 'numeric'});
    if (startTime.split(':')[0].length == 1) {
        startTime = '0' + startTime;
    }
    calenderTime = parseInt(calenderTime) + 1;
    calenderTime = (calenderTime ? (calenderTime > 9 ? calenderTime : "0" + calenderTime) : "0");
    if (calenderTime == 24) {
        calenderTime = "00";
    }
    myToday = new Date(today.getFullYear(), today.getMonth(), today.getDate(), calenderTime, "00", "00");
    endTime = myToday.toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: 'numeric'});
    if (endTime.split(':')[0].length == 1) {
        endTime = '0' + endTime;
    }
    var className = $(this).parent('.fill-column').attr('class').split(" ")[2];
    var calenderDate = $("." + className).find('.heading p').text();
    var calenderMonth = $(".calenderMonthName").text();
    if (calenderMonth.indexOf("/") != -1) {
        if (calenderDate >= 1 && calenderDate <= 7) {
            calenderMonth = calenderMonth.split('/')[1].replace(/\s/g, '');
        } else {
            calenderMonth = calenderMonth.split('/')[0].replace(/\s/g, '');
        }
    }
    startDate = calenderDate + "," + calenderMonth + "," + today.getFullYear();
    $(".color-holder").css('background', '#000000');
    $(".new_timer_id").val('');
    $(".customer_name").text('Select Project');
    $(".id_po").text('');
    $(".timer_project_box").css('border', '3px solid #000000');
    $("#timer_description1").val('');
    $(".item_chargetype").text('');
    asdate = new Date(startDate.replace(/\s/g, '')).toString();
    var sdate = asdate.split(' ');
    $("#time_start").val(sdate[0] + ', ' + sdate[1] + ' ' + sdate[2] + ', ' + sdate[3]);
    $('#edit_start_timer').val(startTime);
    $('#edit_start_timer').parent().find('.edit_start_time').attr('data-default', startTime);
    $('#edit_end_timer').val(endTime);
    $('#edit_end_timer').parent().find('.end_start_time').attr('data-default', endTime);
    $('#edit_start_timer').attr('data-newtimetype', 'week');
    $('#edit_start_timer').parent().find('.edit_start_time').attr('data-newtimetype', 'week');
    $('#edit_end_timer').attr('data-newtimetype', 'week');
    $('#edit_end_timer').parent().find('.end_start_time').attr('data-newtimetype', 'week');
    asdate = new Date(startDate.replace(/\s/g, '').replace(/-/g, '/'));
    var time_stop = asdate.getFullYear() + "-" + (asdate.getMonth() + 1) + "-" + asdate.getDate() + " " + endTime;
    $(".stop_new_value").val(time_stop);
    $("#unlocked").attr("checked", true);
    $("#unlocked").parent().addClass('active');
    $("#sticky_order").prop('checked', true);
    $("#delete_timer").parent('div').hide();
    $('#save_timer').attr('onclick', 'create_new_timer();');
    $(".new_project_list").show();
    $(".timer_project_box").html('<div class="timer-header" style="color:#000000;"><div class="name-ofcust-new"><h6 class="customer_name">Select Project</h6><p class="id_po"></p><p class="item_chargetype"></p></div></div>');
    select_list_type = "week";
    $("#edit_detail_modal").modal('show');

});
$(document).ready(function () {
    $('.v-input').on("focus blur", function () {
        $(this).toggleClass('primary--text');
        $(this).parents('.v-text-field__slot').find('.v-label').toggleClass('primary--text');
    });
});
$(document).on("click", ".add_timer_description", function () {
    $.ajax({
        url: BASE_URL + "aut/timer/get_timer",
        type: "POST",
        data: {'timer_id': $(this).parents('.timer_running_new_div').find('.timer_id').val()},
        dataType: "JSON",
        success: function (data)
        {
            $(".desc_timer_id").val(data.timer_id);
            $("#timer_description12").val(data.description);
            setTimeout(function () {
                $("#timer_description12").focus();
            }, 500);
            $("#delete_timer").parent('div').show();
            $("#edit_description_modal").modal('show');
        }
    });
});
$(document).on("click", ".timer_edit_icon", function () {
    $this = $(this);
    if ($(this).data('type') == 'pin_timer') {
        $('#save_timer').attr('onclick', 'save_pin_timer();');
        $(".no_pin_timer").hide();
        $(".no_pin_timer1").hide();
        $(".timer_project_box").css('pointer-events', 'none');
        $('.property-color').show();
        url = BASE_URL + "aut/timer/get_timer_project";
        timer_id = $(this).parents('.main-timer-div-new').find('.timer_project_id').val();
        $('.delete_link').attr('id', 'delete_timer_project');
    } else {
        $('.property-color').hide();
        $('#save_timer').attr('onclick', 'save_timer();');
        $(".no_pin_timer").show();
        $(".timer_project_box").css('pointer-events', 'unset');
        url = BASE_URL + "aut/timer/get_timer";
        timer_id = $(this).parents('.timer_running_new_div').find('.timer_id').val();
        $('.delete_link').attr('id', 'delete_timer');
    }

    $.ajax({
        url: url,
        type: "POST",
        data: {'timer_id': timer_id},
        dataType: "JSON",
        success: function (data)
        {
            start_time = data.time_start;
            first_time = data.time_start;
            last_time = data.time_stop;
            if ($this.data('type') != 'pin_timer') {
                var start = new Date(data.time_start.replace(/-/g, '/'));
                var end = new Date(data.time_stop.replace(/-/g, '/'));
            }
            var date_diff_indays = function (date1, date2) {
                dt1 = new Date(date1);
                dt2 = new Date(date2);
                return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate())) / (1000 * 60 * 60 * 24));
            };
            $('.popup_end_label').text((date_diff_indays(start, end) > 0 ? "End Time (+" + date_diff_indays(start, end) + " day)" : "End Time"));
            $(".invite_type_option").removeClass('active');
            $("#edit_timer_id").val(data.timer_id);
            $("#customer_id").val(data.customer_id);
            $("#pickcolor").val('#' + data.color_dec);
            $(".color-holder").css('background', '#' + data.color_dec);
            $("#timer_project_id").val(data.timer_project_id);
            $("#edit_timer_project_id").val(data.timer_project_id);
            if ((data.identifier == "" && data.po == "") || (data.identifier == "" || data.po == "")) {
                var sap = '';
            } else {
                var sap = ' / ';
            }
            $(".new_timer_id").val('');
            $(".customer_name").text(data.name);
            $(".id_po").text(data.identifier + '' + sap + '' + data.po);
            $(".timer_project_box").css('border', '3px solid #' + data.color_dec);
            if (data.item_id > 0) {
                $(".item_chargetype").text(data.item_name);
            }
            if (data.item_id == 0) {
                $(".item_chargetype").text(data.chargetype);
            }
            if ($this.attr('data-type') == 'des') {
                setTimeout(function () {
                    $("#timer_description1").val(data.description).focus();
                }, 500);
            } else {
                $("#timer_description1").val(data.description);
            }
            if ($this.data('type') != 'pin_timer') {
                var asdate = new Date(data.time_start.replace(/-/g, '/')).toString();
                var sdate = asdate.split(' ');
                var diffDays = Math.ceil(Math.abs(new Date() - new Date(data.time_start)) / (1000 * 60 * 60 * 24));
                $("#datepicker").datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    format: 'mm/dd/yyyy',
                    endDate: "today",
                    startDate: '-' + (diffDays - 1) + 'd'
                }).datepicker('update', getFormattedDate(data.time_start)).on('changeDate', set_charges);
                $("#time_start").val(sdate[0] + ', ' + sdate[1] + ' ' + sdate[2] + ', ' + sdate[3]);
                $('#edit_start_timer').val(new Date(data.time_start.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: '2-digit'}));
                $('#edit_start_timer').parent().find('.edit_start_time').attr('data-default', new Date(data.time_start.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: '2-digit'}));
                $('#edit_end_timer').val(new Date(data.time_stop.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: '2-digit'}));
                $('#edit_end_timer').parent().find('.end_start_time').attr('data-default', new Date(data.time_stop.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: '2-digit'}));
                asdate = new Date(data.time_stop.replace(/-/g, '/'));
                var time_stop = asdate.getFullYear() + "-" + (asdate.getMonth() + 1) + "-" + asdate.getDate() + " " + new Date(data.time_stop.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: '2-digit'});
                $(".time_stop_new_value").val(time_stop);
            }

            if (data.locked == 1) {
                $("#locked_unlocked").prop("checked", true);
            } else {
                $("#locked_unlocked").prop("checked", false);
            }
            $('.locked_value').val(data.locked);
            if (data.sticky_order == 1) {
                $("#sticky_order").prop('checked', true);
                pin_timer_project = true;
            } else {
                $("#sticky_order").prop('checked', false);
                pin_timer_project = false;
            }
            $('.sticky_value').val(data.sticky_order);

            $("#delete_timer").parent('div').show();
            $("#edit_detail_modal").modal('show');
        }
    });
});
$(document).on("click", ".timer_project_box", function () {
    $(".new_project_list").attr('data-modal', 'in');
    $("#start_new_project").hide();
    $("#add_new_quick_project").attr('data-pos', 'in_modal');
    $("#project_list").modal('show');
});
$(document).on("click", "#delete_timer", function () {
    $.ajax({
        url: BASE_URL + "aut/timer/delete_time",
        type: "POST",
        data: {'timer_id': $(this).parents('#edit_detail_modal').find("#edit_timer_id").val()},
        dataType: "JSON",
        success: function (data)
        {
            if (data.status == "success") {
                if (typeof (select_list_type) != "undefined" && select_list_type !== null) {
                    if (select_list_type == "week") {
                        window.location.href = BASE_URL + 'timer?calender=1&week=' + $(".week_count").val();
                    } else if (select_list_type == "today") {
                        window.location.href = BASE_URL + 'timer?list=1&date=' + $('.day_div').find('.today_date').text().replace(/ /g, '');
                    }
                } else {
                    location.reload();
                }
            }
        }
    });
});
$(document).on("click", "#delete_timer_project", function () {
    $this = $(this);
    msg = 'This will remove the project from future use. All existing timer records linked to the project will remain.';
    if (pin_timer_project) {
        msg += 'If you are trying to remove from your pinned list, use the Pin toggle instead.';
    }
    bootbox.confirm({
        closeButton: false,
        className: "sign_up_alert",
        backdrop: true,
        onEscape: true,
        message: msg,
        buttons: {
            confirm: {
                label: 'Delete',
                className: 'btn-primary bootbox-ok-button'
            },
            cancel: {
                label: 'Cancel',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    url: BASE_URL + "aut/timer/delete_timer_project",
                    type: "POST",
                    data: {'timer_project_id': $this.parents('#edit_detail_modal').find("#edit_timer_project_id").val()},
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
var day_options = {
    chart: {
        plotBackgroundColor: '#f9f9fc',
        plotBorderWidth: '0',
        plotShadow: false,
        type: 'pie',
        marginTop: 0,
        marginBottom: 0,
        marginLeft: 0,
        marginRight: 0,
        zIndex: 9999,
        events: {
            load: requestData
        }
    },
    title: {
        text: ''
    },
    tooltip: {
        pointFormat: '<b>{point.percentage:.1f}%</b>',
        shared: true,
        useHTML: true,
        formatter: function () {
            return '<b>' + this.key + '</b>';
        },
        valueDecimals: 2
    },
    exporting: {enabled: false},
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            zIndex: 9999,
            dataLabels: {
                enabled: false,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
            },
            point: {
                events: {
                    click: function () {

                        if ($(window).width() < 767) {
                            $('.admin-contain-main-div').addClass('admin-contain-main-div_new');
                            $('body.sticky-footer').css('margin-bottom', '15px');
                        } else {
                            $('.timer_search_ico .back_graph').show();
                        }
                        $(".today_date").text('');
                        $(".today_date").text($(".today_new_date").val());
                        $.ajax({
                            url: BASE_URL + "aut/timer/day_timer",
                            type: "POST",
                            data: {date: $('.today_date').text(), status: 'nope'},
                            dataType: "JSON",
                            success: function (data)
                            {
                                set_today_data(data);
                            }
                        });

                        $(".today_timer").show();
                        $(".no_calender,.timer_search_ico #showLeft,.timer_profile_image").hide();
                    }
                }
            }
        },
        series: {
            animation: false
        }
    },
    responsive: {
        rules: [{
                condition: {
                    maxWidth: 600
                },
                chartOptions: {
                    legend: {
                        enabled: false
                    }
                }
            }]
    },
    series: [{
            data: [],
            zIndex: 9999
        }]
};
var week_options = {
    chart: {
        plotBackgroundColor: '#f9f9fc',
        plotBorderWidth: '0',
        plotShadow: false,
        type: 'pie',
        marginTop: 0,
        marginBottom: 0,
        marginLeft: 0,
        marginRight: 0,
        zIndex: 9999,
        events: {
            load: requestData
        }
    },
    title: {
        text: ''
    },
    tooltip: {
        pointFormat: '<b>{point.percentage:.1f}%</b>',
        shared: true,
        useHTML: true,
        formatter: function () {
            return '<b>' + this.key + '</b>';
        },
        valueDecimals: 2
    },
    exporting: {enabled: false},
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
            },
            point: {
                events: {
                    click: function () {
                        $.ajax({
                            url: BASE_URL + "aut/timer/get_week_view",
                            type: "POST",
                            data: {'weekcount': 0},
                            success: function (html)
                            {
                                loadWeekHtml(html);
                            }
                        });
                        if ($(window).width() < 767) {
                            $('.admin-contain-main-div').addClass('admin-contain-main-div_new');
                            $('body.sticky-footer').css('margin-bottom', '15px');
                        } else {
                            $('body.sticky-footer').css('margin-bottom', '0px');
                            $('.fixed-top2').hide();
                            $('.timer_main_div').css('padding-top', '0px');
                            $('.admin-contain-main-div').css('padding', '15px');
                        }
                        $(".no_calender,.timer_search_ico i,.timer_profile_image").hide();
                        $(".claender_main_div").show();
                        $(".footer-section").hide();
                    }
                }
            }
        },
        series: {
            animation: false
        }
    },
    responsive: {
        rules: [{
                condition: {
                    maxWidth: 600
                },
                chartOptions: {
                    legend: {
                        enabled: false
                    }
                }
            }]
    },
    series: [{
            data: [],
            zIndex: 9999
        }]
};
$(document).on("click", ".show_today", function () {
    if ($(window).width() < 767) {
        $('.admin-contain-main-div').addClass('admin-contain-main-div_new');
        $('body.sticky-footer').css('margin-bottom', '15px');
    } else {
        $('.timer_search_ico .back_graph').show();
    }
    $(".today_date").text('');
    $(".today_date").text($(".today_new_date").val());
    $.ajax({
        url: BASE_URL + "aut/timer/day_timer",
        type: "POST",
        data: {date: $('.today_date').text(), status: 'nope'},
        dataType: "JSON",
        success: function (data)
        {
            set_today_data(data);
        }
    });
    $(".today_timer").show();
    $(".no_calender,.timer_search_ico #showLeft,.timer_profile_image").hide();
});
$(document).on("click", ".show_weekly", function () {
    $.ajax({
        url: BASE_URL + "aut/timer/get_week_view",
        type: "POST",
        data: {'weekcount': 0},
        success: function (html)
        {
            loadWeekHtml(html);
        }
    });
    if ($(window).width() < 767) {
        $('.admin-contain-main-div').addClass('admin-contain-main-div_new');
        $('body.sticky-footer').css('margin-bottom', '15px');
        $('.calender-main').css('height', $(window).height() - 120 + 'px');
    } else {
        $('body.sticky-footer').css('margin-bottom', '0px');
        $('.fixed-top2').hide();
        $('.timer_main_div').css('padding-top', '0px');
        $('.admin-contain-main-div').css('padding', '15px');
    }
    $(".claender_main_div").show();
    $(".no_calender,.timer_search_ico i,.timer_profile_image").hide();
    $(".footer-section").hide();
});
$(document).on("click", ".back_graph,.back_today_graph", function () {
    $('.timer_search_ico .back_graph').hide();
    if ($(window).width() < 767) {
        $(".no_calender,.timer_search_ico #showLeft,.timer_profile_image").show();
        $(".claender_main_div").hide();
        $(".today_timer").hide();
    } else {
        $(".no_calender,.timer_search_ico #showLeft").show();
        $(".claender_main_div").hide();
        $(".today_timer").hide();
        $('body.sticky-footer').css('margin-bottom', '56px');
        $('.fixed-top2').show();
        $('.timer_main_div').css('padding-top', '40px');
        $('.admin-contain-main-div').css('padding', '30px');
        $(".footer-section").show();
    }
    $('.week_count').val(0);
    $('.admin-contain-main-div').removeClass('admin-contain-main-div_new');
});
$(window).resize(function () {
    if ($(window).width() < 767) {
        if ($(window).height() - 90 > 764) {
            height = 764;
        } else {
            height = $(window).height() - 90;
        }
        $('.calender-main').css('height', height + 'px');
    } else {
        $('.calender-main').css('height', '764px');
    }
});
if ($(window).width() < 767) {
    if ($(window).height() - 90 > 764) {
        height = 764;
    } else {
        height = $(window).height() - 90;
    }
    $('.calender-main').css('height', height + 'px');
} else {
    $('.calender-main').css('height', '764px');
}
if ($(window).width() < 767) {
    $("#container").css('height', '100px');
    $("#container").css('min-width', '100px');
    $("#container").css('max-width', '100px');
    $("#container1").css('height', '100px');
    $("#container1").css('min-width', '100px');
    $("#container1").css('max-width', '100px');
} else {
    $("#container").css('height', '200px');
    $("#container1").css('height', '200px');
}
chart = Highcharts.chart('container', day_options);
chart1 = Highcharts.chart('container1', week_options);
$(document).on('click', '.pin_timer_start_new', function () {
    $this = $(this);
    if (running_status) {
        $('.timer_card').slideUp(500);
    }
    $(this).parents('.main-timer-div-new').hide();
    setTimeout(function () {
        $.ajax({
            url: BASE_URL + "aut/timer/add_pin_timer",
            type: "POST",
            data: {'customer_id': $this.parents('.main-timer-div-new').data('id'), 'ct_id': $this.data('ctid'), 'item_id': $this.data('itemid')},
            dataType: "JSON",
            success: function (data)
            {
                set_new_timer(data);
            }
        });
    }, 500);
});
$(document).on('click', '.pin_timer_start', function () {
    var cnt = 0;
//    $('.timer_card').slideUp(500);
//    $('.main-timer-div-new').show();
//    $(this).parents('.main-timer-div-new').hide();
    $this = $(this);

    if (timer_variable.length > 0) {
        $.each($('.timer_running_new_div'), function () {
            if ($(this).find('.timer_project_id').val() != '') {
                $.ajax({
                    url: BASE_URL + "aut/timer/stop_timer_project",
                    type: "POST",
                    data: {'timer_project_id': $(this).find('.timer_project_id').val()},
                    dataType: "JSON",
                    success: function (data)
                    {

                    }
                });
            }
        });
    }
    $.ajax({
        url: BASE_URL + "aut/timer/add_pin_timer",
        type: "POST",
        data: {'customer_id': $this.parents('.main-timer-div-new').data('id'), 'ct_id': $this.data('ctid'), 'item_id': $this.data('itemid')},
        dataType: "JSON",
        success: function (data)
        {
            set_new_timer(data);
        }
    });
});

var timer1 = function () {
    auto_timer = setInterval(function () {
        if (document.hidden == false) {
            $.ajax({
                url: BASE_URL + "aut/timer/check_network",
                type: "POST",
                data: {},
                dataType: "JSON",
                success: function (data)
                {
                    if (data.status == "success") {
                        $.ajax({
                            url: BASE_URL + "aut/timer/update_running_timer",
                            type: "POST",
                            data: {},
                            dataType: "JSON",
                            success: function (data)
                            {
                                if (data.customer_ids.length > 0) {
                                    not_stop = false;
                                    if (running_status != false) {
                                        if ($(".pin_timer_start_new").hasClass("customer_running_timer_" + data.customer_ids[0])) {
                                            $(".customer_running_timer_" + data.customer_ids[0]).trigger('click');
                                        } else {
                                            if (running_status) {
                                                $.ajax({
                                                    url: BASE_URL + "aut/timer/start_running_timer",
                                                    type: "POST",
                                                    data: {'customer_id': data.customer_ids[0], 'timer_project_id': data.timer_project_id[0]},
                                                    dataType: "JSON",
                                                    success: function (data)
                                                    {
                                                        set_new_timer(data);
                                                    }
                                                });
                                            }
                                            $.ajax({
                                                url: BASE_URL + "aut/timer/get_running_timer",
                                                type: "POST",
                                                data: {},
                                                dataType: "JSON",
                                                success: function (data)
                                                {
                                                    show_running_timer(data);
                                                }
                                            });
                                        }
                                    }
                                }
                                requestData();
                                if (data.customer_ids.length == 0) {
                                    not_stop = false;
                                    running_status = false;
                                    clearTimeout(auto_timer);
                                    $('.main-timer-div').hide();
                                }
                            },
                            error: function () {
                                clearTimeout(auto_timer);
                            }
                        });
                    } else {
                        clearTimeout(auto_timer);
                    }
                },
                error: function () {
                    clearTimeout(auto_timer);
                }
            });
        }
    }, 60 * 1000);
};
timer1();
$.ajax({
    url: BASE_URL + "aut/timer/update_running_timer",
    type: "POST",
    data: {},
    dataType: "JSON",
    success: function (data)
    {
        if (data.status == "success") {
            $(".customer_running_timer_" + data.customer_ids[0]).trigger('click');
            requestData();
        }
    }
});
var graph_status;
$(document).on("click", ".date_backward", function () {
    $.ajax({
        url: BASE_URL + "aut/timer/day_timer",
        type: "POST",
        data: {date: $(this).parents('.month_div').find('h4').text(), status: 'past'},
        dataType: "JSON",
        success: function (data)
        {
            graph_status = 'past';
            set_today_data(data);
        }
    });
});
$(document).on("click", ".date_forward", function () {
    $.ajax({
        url: BASE_URL + "aut/timer/day_timer",
        type: "POST",
        data: {date: $(this).parents('.month_div').find('h4').text(), status: 'future'},
        dataType: "JSON",
        success: function (data)
        {
            graph_status = 'future';
            set_today_data(data);
        }
    });
});
$(document).on("click", ".day_graph_backward", function () {
    $.ajax({
        url: BASE_URL + "aut/timer/today_graph",
        type: "POST",
        data: {date: $('.today_graph_title').text(), status: 'past'},
        dataType: "JSON",
        success: function (data)
        {
            graph_data(data, 'day');
        }
    });
});
$(document).on("click", ".day_graph_forward", function () {
    $.ajax({
        url: BASE_URL + "aut/timer/today_graph",
        type: "POST",
        data: {date: $('.today_graph_title').text(), status: 'future'},
        dataType: "JSON",
        success: function (data)
        {
            graph_data(data, 'day');
        }
    });
});
$(document).on("click", ".week_graph_backward", function () {
    if (graph_type == '') {
        graph_type = 'Week';
    }
    if (graph_type.toLowerCase() == 'week') {
        date = $('.week_graph_type').text();
    } else {
        date = $('.month_graph_date').val();
    }
    graphcount = parseInt($(".graph_count").val());
    graphcount = graphcount - 1;
    $(".graph_count").val(graphcount);
    $.ajax({
        url: BASE_URL + "aut/timer/week_graph",
        type: "POST",
        data: {date: date, 'type': graph_type, status: 'past', 'count': graphcount},
        dataType: "JSON",
        success: function (data)
        {
            graph_data(data, 'week');
        }
    });
});
$(document).on("click", ".week_graph_forward", function () {
    if (graph_type.toLowerCase() == 'week') {
        date = $('.week_graph_type').text();
    } else {
        date = $('.month_graph_date').val();
    }
    graphcount = parseInt($(".graph_count").val());
    graphcount = graphcount + 1;
    $(".graph_count").val(graphcount);
    $.ajax({
        url: BASE_URL + "aut/timer/week_graph",
        type: "POST",
        data: {date: date, 'type': graph_type, status: 'future', 'count': graphcount},
        dataType: "JSON",
        success: function (data)
        {
            graph_data(data, 'week');
        }
    });
});
$(document).on('click', '.timer_csv_download a', function () {
    if (graph_type == '') {
        graph_type = 'Week';
    }
    graphcount = parseInt($(".graph_count").val());
    window.location.href = BASE_URL + 'aut/timer/export_timer_data?count=' + graphcount + '&type=' + graph_type;
});
$(document).on("click", "#show_project_list", function () {
    $("#start_new_project").show();
    $(".set_edit_icon").hide();
    $("#project_list").modal('show');
});
$(document).on("click", ".start_timer", function () {
    var cnt = 0;
    if ($(this).data('modal') == "in") {
        $(".new_timer_id").val($(this).data('id'));
        $("#project_list").modal('hide');
        $(".timer_project_box").html($(this).html());
        $(".timer_project_box").css('border', $(this).css('border'));
        $(".timer_project_box").find('.set_edit_icon').show();
    } else {
        $('#project_list').delay(1000).fadeOut('slow');
        $this = $(this);
        if (timer_variable.length > 0) {
            $.each($('.timer_running_new_div'), function () {
                if ($(this).find('.timer_project_id').val() != '') {
                    $.ajax({
                        url: BASE_URL + "aut/timer/stop_timer_project",
                        type: "POST",
                        data: {'timer_project_id': $(this).find('.timer_project_id').val()},
                        dataType: "JSON",
                        success: function (data)
                        {
                            cnt = cnt + 1;
                            if (timer_variable.length == cnt) {
//                                $(".running_timer_list").html('');
//                                $(".main-timer-div").hide();
                                clearTimeout(t);
                                $.ajax({
                                    url: BASE_URL + "aut/timer/add_timer",
                                    type: "POST",
                                    data: {'timer_project_id': $this.data('id'), 'customer_id': $this.data('custid')},
                                    dataType: "JSON",
                                    success: function (data)
                                    {
                                        set_new_timer(data);
                                    }
                                });
                            }
                        }
                    });
                }
            });
        } else {
//            $(".running_timer_list").html('');
//            $(".main-timer-div").hide();
            clearTimeout(t);
            $.ajax({
                url: BASE_URL + "aut/timer/add_timer",
                type: "POST",
                data: {'timer_project_id': $this.data('id'), 'customer_id': $this.data('custid')},
                dataType: "JSON",
                success: function (data)
                {
                    set_new_timer(data);
                }
            });
        }

    }
});
$(document).on("click", ".forward_week", function () {
    weekcount = parseInt($(".week_count").val());
    weekcount = weekcount + 1;
    $(".week_count").val(weekcount);
    $.ajax({
        url: BASE_URL + "aut/timer/get_week_view",
        type: "POST",
        data: {'weekcount': weekcount},
        success: function (html)
        {
            loadWeekHtml(html);
        }
    });
});
$(document).on("click", ".back_week", function () {
    weekcount = parseInt($(".week_count").val());
    weekcount = weekcount - 1;
    $(".week_count").val(weekcount);
    $.ajax({
        url: BASE_URL + "aut/timer/get_week_view",
        type: "POST",
        data: {'weekcount': weekcount},
        success: function (html)
        {
            loadWeekHtml(html);
        }
    });
});
$(document).on("click", ".week_change_icon", function () {
    if (graph_type.toLowerCase() == 'week') {
        $(this).parent('p').find('.show_weekly').text('This Week');
    } else if (graph_type.toLowerCase() == 'pay') {
        $(this).parent('p').find('.show_weekly').text('This Pay');
    } else if (graph_type.toLowerCase() == 'month') {
        $(this).parent('p').find('.show_weekly').text('This Month');
    } else if (graph_type.toLowerCase() == 'year') {
        $(this).parent('p').find('.show_weekly').text('This Year');
    }
    $('.week_graph_forward').hide();
    var type = $(this).parent('p').find('.show_weekly').text();
    if (type == "This Week") {
        type = "This Pay";
        graph_type = 'pay';
        $(this).attr('class', 'fas fa-search-minus week_change_icon');
    } else if (type == "This Pay") {
        type = "This Month";
        graph_type = 'month';
        $(this).attr('class', 'fas fa-search-minus week_change_icon');
    } else if (type == "This Month") {
        type = "This Year";
        graph_type = 'year';
        $(this).attr('class', 'fas fa-search-plus week_change_icon');
    } else {
        type = "This Week";
        graph_type = 'week';
        $(this).attr('class', 'fas fa-search-minus week_change_icon');
    }
    $(this).parent('p').find('.show_weekly').text(type);
    $(".graph_count").val(0);
    $.ajax({
        url: BASE_URL + "aut/timer/timer_report",
        type: "post",
        data: {'type': graph_type, 'count': 0, 'clear': 1},
        dataType: "JSON",
        success: function (data) {
            $.each(data.week, function (i, value) {
                data.week[i].y = parseFloat(value.y);
            });
            $(".total_week_hours").text(data.week_hours);
            $(".total_week_minute").text(data.week_minute);
            chart1.series[0].setData(data.week);
            $(".timer_chart_main_div").show();
        },
        cache: false
    });
});
$(document).on('click', '#locked_unlocked', function () {
    if ($(this).prop("checked") == true) {
        $('.locked_value').val('1');
    } else {
        $('.locked_value').val('0');
    }
});
$(document).on('click', '#sticky_order', function () {
    if ($(this).prop("checked") == true) {
        $('.sticky_value').val('1');

    } else {
        $('.sticky_value').val('0');
    }
});
$(document).on('click', '#start_new_project', function () {
    $.ajax({
        url: BASE_URL + "aut/timer/start_quick_timer",
        type: "POST",
        data: {},
        dataType: "JSON",
        success: function (data)
        {
            set_new_timer(data);
        }
    });
});
$(document).on("click", ".add_project", function () {
    $('.backBtn').trigger('click');
    $("#add_project_modal").modal("show");
    $("#add_quick_timer_project").attr('data-pos', 'in_modal');
});
document.addEventListener('visibilitychange', function () {
    if (document.hidden == false) {
        if (timer_variable.length != 0) {
            $.ajax({
                url: BASE_URL + "aut/timer/get_running_timer",
                type: "POST",
                data: {},
                dataType: "JSON",
                success: function (data)
                {
                    tab_focus_set(data);
                }
            });
        }
    }
});
window.addEventListener("focus", function (event) {
    if (document.hidden == false) {
        if (timer_variable.length != 0) {
            $.ajax({
                url: BASE_URL + "aut/timer/get_running_timer",
                type: "POST",
                data: {},
                dataType: "JSON",
                success: function (data)
                {
                    tab_focus_set(data);
                }
            });
        }
    }
}, false);
function graph_data(data, type) {
    if (type == 'day') {
        $('.today_graph_title').text(data.label);
        if (data.label == 'Today') {
            $(".day_graph_forward").hide();
        } else {
            $(".day_graph_forward").show();
        }
        $.each(data.today, function (i, value) {
            data.today[i].y = parseFloat(value.y);
            data.today[i].color = '#' + value.color;
        });
        $(".total_today_hours").text(data.today_hours);
        $(".total_today_minute").text(data.today_minute);
        chart.series[0].setData(data.today);
        $(".timer_chart_main_div").show();
        $(".today_new_date").val(data.date);
    } else {
        $('.week_graph_type').text(data.label);
        if ($('.week_graph_type').text().indexOf('This') != -1) {
            $('.week_graph_forward').hide();
        } else {
            $('.week_graph_forward').show();
        }
        $('.month_graph_date').val(data.new_date);
        $.each(data.week, function (i, value) {
            data.week[i].y = parseFloat(value.y);
        });
        $(".total_week_hours").text(data.week_hours);
        $(".total_week_minute").text(data.week_minute);
        chart1.series[0].setData(data.week);
    }
}
function loadWeekHtml(html) {
    $(".claender_main_div").html('').html(html);
    if ($(window).width() < 767) {
        if ($(window).height() - 90 > 764) {
            height = 764;
        } else {
            height = $(window).height() - 90;
        }
        $('.calender-main').css('height', height + 'px');
    } else {
        $('.calender-main').css('height', '764px');
    }
    if (weekcount == 0) {
        $('.forward_week').hide();
    } else {
        $('.forward_week').show();
    }
}
function set_today_data(data) {
    $(".day_div").find('h4').text(data.date);
    var total_miutes = 0;
    $.each(data.today_hours, function (i, value) {
        total_miutes = total_miutes + parseInt(value.dif_min_total);
    });
    var hours = Math.floor(total_miutes / 60);
    var minutes = total_miutes % 60;
    $(".day-time #hours1").text(hours);
    $(".day-time #minutes1").text((minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00"));
    $(".today_listing").html('');
    $.each(data.today_hours, function (i, tvalue) {
        if (parseInt(tvalue.dif_min) > 0) {
            ehours = Math.floor(tvalue.dif_min / 60);
            eminutes = tvalue.dif_min % 60;
            eminutes = (eminutes ? (eminutes > 9 ? eminutes : "0" + eminutes) : "00");
            if ((tvalue.identifier == "" && tvalue.po == "") || (tvalue.identifier == "" || tvalue.po == "")) {
                var sap = '';
            } else {
                var sap = ' / ';
            }
            var html = '<div class="daybox-main" data-type="today" data-id="' + tvalue.timer_id + '" style="border: 3px solid ' + tvalue.color_dec + ';">' +
                    '<div class="day-box-section">' +
                    '<div class="daybox-header calender_edit cursor-pointer" data-type="today" data-id="' + tvalue.timer_id + '">' +
                    '<div class="dayname_new">' +
                    '<h6 class="">' + tvalue.name + '</h6>' +
                    '</div>' +
                    '<div class="day-time_new">' +
                    '<p class=""><span class="not_today_timer" style="display:' + (tvalue.show_sign == "TRUE" ? "" : "none;") + '"><i class="fas fa-asterisk"></i></span><span class="hours_sap">' + ehours + '</span><span class="hours_sap" style="visibility: visible;">:</span><span>' + eminutes + '</span></p>' +
                    '</div>' +
                    '</div>' +
                    '<div class="daybox-footer">' +
                    '<div class="day-count calender_edit cursor-pointer" data-type="today" data-id="' + tvalue.timer_id + '"><p>' + tvalue.identifier + '' + sap + '' + tvalue.po + '</p></div>' +
                    '<div class="day-sectime cursor-pointer"><span class="not_today_timer" style="display:' + (tvalue.show_sign == "TRUE" ? "" : "none;") + '"><i class="fas fa-asterisk"></i></span><p class="start_time" data-type="start" data-attr="stop">' + tvalue.start + ' -</p>&nbsp;<p class="end_time" data-type="end" data-attr="stop">' + tvalue.end + '</p></div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            $(".today_listing").append(html);
        }
    });
    if (data.today_hours.length == 0) {
        var html = '<h4 class="today_listing_msg">No any timer started on this date.</h4>';
        $(".today_listing").append(html);
    }
    $.ajax({
        url: BASE_URL + "aut/timer/today_graph",
        type: "POST",
        data: {date: $('.today_date').text(), status: 'nope'},
        dataType: "JSON",
        success: function (data)
        {
            graph_data(data, 'day');
            if (data.label == 'Today') {
                $('.date_forward').hide();
            } else {
                $('.date_forward').show();
            }
        }
    });
}
function tab_focus_set(data) {
    $.each(data, function (i, data) {
        if ($('.running_timer_project_' + data.timer_project_id).data('stop') == true) {
            return;
        }
        show_minutes = 0;
        show_hours = 0;
        var start = new Date(data.time_start.replace(/-/g, '/'));
        var end = new Date(data.time_stop.replace(/-/g, '/'));
        var diffMs = (end - start);
        var diffDays = Math.floor(diffMs / 86400000);
        var diffHrs = Math.floor((diffMs % 86400000) / 3600000);
        var diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000);
        if (diffMins < 0) {
            show_minutes = 0;
        } else {
            show_minutes = diffMins;
        }
        if (diffDays > 0) {
            diffHrs = diffHrs + diffDays * 24;
        }
        if (diffHrs > 0) {
            show_hours = diffHrs;
        }
        if (timer_variable.length == 1) {
            hours = show_hours;
            minutes = show_minutes;
        }
        $('.running_timer_project_' + data.timer_project_id).find('.running_hours').text(show_hours);
        $('.running_timer_project_' + data.timer_project_id).find('.running_minutes').text((show_minutes ? (show_minutes > 9 ? show_minutes : "0" + show_minutes) : "00"));
        var d = new Date(data.time_start.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
        var parts = d.split(":");
        $('.running_timer_project_' + data.timer_project_id).find(".start_timer_hour").text(parts[0]);
        $('.running_timer_project_' + data.timer_project_id).find(".start_timer_minute").text(parts[1]);
        $('.running_timer_project_' + data.timer_project_id).find(".start_timer_running_hour").text(parts[0]);
        $('.running_timer_project_' + data.timer_project_id).find(".start_timer_running_minute").text(parts[1]);
        var ds = new Date(data.time_stop.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
        $('.running_timer_project_' + data.timer_project_id).find(".stop_timer_hour").text(ds.split(":")[0]);
        $('.running_timer_project_' + data.timer_project_id).find(".stop_timer_minute").text(ds.split(":")[1]);
    });
    requestData();
    $('.pin_timer_listing').find('.main-timer-div-new').show();
    $.each(data, function (i, value) {
        $('.pin_timer_listing').find(".new_main_box_" + value.timer_project_id).hide();
    });
    if (data.length == 0) {
        $(".main-timer-div").hide();
        $(".running_timer_list").html('');
    }
}
function set_new_timer(data) {
    seconds = 0;
    minutes = 0;
    hours = 0;
    start_time = data.time_start;
    first_time = data.time_start;
    last_time = data.time_stop;
    var start = new Date(data.time_start.replace(/-/g, '/'));
    var end = new Date(data.time_stop.replace(/-/g, '/'));
    var diffMs = (end - start);
    var diffDays = Math.floor(diffMs / 86400000);
    var diffHrs = Math.floor((diffMs % 86400000) / 3600000);
    var diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000);
    if (diffMins < 0) {
        minutes = 0;
    } else {
        minutes = diffMins;
    }
    if (diffDays > 0) {
        diffHrs = diffHrs + diffDays * 24;
    }
    if (diffHrs > 0) {
        sd = new Date(start_time.replace(/-/g, '/'));
        start_time = dateAdd(sd, 'hour', diffHrs);
        hours = diffHrs;
    }
    $(".main-timer-div").addClass('running_timer_project_' + data.timer_project_id);
    $("#timer_id").val(data.timer_id);
    $("#timer_project_id").val(data.timer_project_id);
    $("#add_description_timer").val(data.timer_id);
    $(".main-timer-div").css('background-color', '#' + data.color_dec);
    $(".main-timer-div").css('border', '3px solid #' + data.color_dec);
    $(".main-timer-div .timer-footer").css('color', '#ffffff');
    $(".main-timer-div .timer-header").css('color', '#ffffff');
    $('.main-timer-div .time').css('color', '#ffffff');
    $(".name-ofcust h6").text(data.name);
    if ((data.identifier == "" && data.po == "") || (data.identifier == "" || data.po == "")) {
        var sap = '';
    } else {
        var sap = ' / ';
    }
    $(".name-ofcust p").text(data.identifier + '' + sap + '' + data.po);
    $(".main-timer-div").find("#timer_edit").attr('data-id', data.timer_id);
    $(".add_timer_description").attr('data-id', data.timer_id);
    $("#start").hide();
    $("#stop").show();
    clearTimeout(t);
    add();
    var d = new Date(first_time.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
    var parts = d.split(":");
    $(".start_timer_hour").text(parts[0]);
    $(".start_timer_minute").text(parts[1]);
    var ds = new Date(data.time_stop.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
    $(".stop_timer_hour").text(ds.split(":")[0]);
    $(".stop_timer_minute").text(ds.split(":")[1]);
    $('.timer_card').slideDown(500);
    $(".main-timer-div").show();
    $('.running_timer_project_' + data.timer_project_id).find(".customer_id").val(data.customer_id);
    $('#project_list').modal('hide');
    requestData();
    clearTimeout(auto_timer);
    timer1();

}
function add() {
    seconds++;
    running_status = false;
    if (seconds >= 60) {
        seconds = 0;
        nd = new Date(start_time.replace(/-/g, '/'));
        minutes++;
        if (minutes >= 60) {
            sd = new Date(start_time.replace(/-/g, '/'));
            start_time = dateAdd(sd, 'hour', 1);
            minutes = 0;
            hours++;
        }
    }
    $('#hours').text(hours);
    $('#minutes').text((minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00"));
    timer();
}
function timer() {
    t = setTimeout(add, 1000);
}
function requestData() {
    $.ajax({
        url: BASE_URL + "aut/timer/timer_report",
        type: "post",
        data: {'customer_id': $('#customer_id').val()},
        dataType: "JSON",
        success: function (data) {
            $.each(data.today, function (i, value) {
                data.today[i].y = parseFloat(value.y);
                data.today[i].color = '#' + value.color;
            });
            $.each(data.week, function (i, value) {
                data.week[i].y = parseFloat(value.y);
            });
            if ($('.today_graph_title').text() == 'Today') {
                $(".total_today_hours").text(data.today_hours);
                $(".total_today_minute").text(data.today_minute);
                chart.series[0].setData(data.today);
            }
            if ($('.week_graph_type').text() == 'This Week') {
                $(".total_week_hours").text(data.week_hours);
                $(".total_week_minute").text(data.week_minute);
                chart1.series[0].setData(data.week);
            }
            $(".timer_chart_main_div").show();
        },
        cache: false
    });
}
function save_desc() {
    $.ajax({
        url: BASE_URL + "aut/timer/add_edit_description",
        type: "POST",
        data: new FormData($('#edit_description')[0]),
        dataType: "JSON",
        enctype: 'multipart/form-data',
        success: function (data)
        {
            if (data.status == "success") {
                $("#edit_description_modal").modal("hide");
            }
        },
        processData: false,
        contentType: false
    });
}
function save_timer() {
    var start_diff = (new Date("1970-1-1 " + $('#edit_start_timer').val()) - new Date("1970-1-1 " + $(".edit_start_time").attr('data-default'))) / 1000 / 60;
    var end_diff = (new Date("1970-1-1 " + $('#edit_end_timer').val()) - new Date("1970-1-1 " + $(".end_start_time").attr('data-default'))) / 1000 / 60;
    $("#diff_start").val(start_diff);
    $("#diff_end").val(end_diff);
    $.ajax({
        url: BASE_URL + "aut/timer/edit_timer",
        type: "POST",
        data: new FormData($('#edit_detail')[0]),
        dataType: "JSON",
        enctype: 'multipart/form-data',
        success: function (data)
        {
            if (data.stop == 1) {
                location.reload();
            } else {
                $(".running_timer_project_" + data.timer_project_id).css('background-color', '#' + data.color_dec);
                $(".running_timer_project_" + data.timer_project_id).css('border', '3px solid #' + data.color_dec);
                $(".running_timer_project_" + data.timer_project_id + " .timer-footer").css('color', '#ffffff');
                $(".running_timer_project_" + data.timer_project_id + " .timer-header").css('color', '#ffffff');
                $(".running_timer_project_" + data.timer_project_id + " .time").css('color', '#ffffff');
                start_time = data.time_start;
                first_time = data.time_start;
                last_time = data.time_stop;
                var start = new Date(data.time_start.replace(/-/g, '/'));
                var end = new Date(data.time_stop.replace(/-/g, '/'));
                var diffMs = (end - start);
                var diffDays = Math.floor(diffMs / 86400000);
                var diffHrs = Math.floor((diffMs % 86400000) / 3600000);
                var diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000);
                if (diffMins < 0) {
                    update_minutes = 0;
                } else {
                    update_minutes = diffMins;
                }
                if (diffDays > 0) {
                    diffHrs = diffHrs + diffDays * 24;
                }
                if (diffHrs >= 0) {
                    sd = new Date(start_time.replace(/-/g, '/'));
                    start_time = dateAdd(sd, 'hour', diffHrs);
                    update_hours = diffHrs;
                }
                $('.running_timer_project_' + data.timer_project_id + ' .timer_id').val(data.timer_id);
                $('.running_timer_project_' + data.timer_project_id + ' .running_hours').text(update_hours);
                $('.running_timer_project_' + data.timer_project_id + ' .running_minutes').text((update_minutes ? (update_minutes > 9 ? update_minutes : "0" + update_minutes) : "00"));
                var d = new Date(data.time_start.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
                var parts = d.split(":");
                $(".running_timer_project_" + data.timer_project_id).find(".start_timer_running_hour").text(parts[0]);
                $(".running_timer_project_" + data.timer_project_id).find(".start_timer_running_minute").text(parts[1]);
                var ds = new Date(data.time_stop.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
                $(".running_timer_project_" + data.timer_project_id + " .stop_timer_hour").text(ds.split(":")[0]);
                $(".running_timer_project_" + data.timer_project_id + " .stop_timer_minute").text(ds.split(":")[1]);
                $("#edit_detail_modal").modal("hide");
                requestData();
                if ($(".new_timer_id").val() != "") {
                    location.reload();
                }
                $.ajax({
                    url: BASE_URL + "aut/timer/get_timer",
                    type: "POST",
                    data: {'timer_id': $("#timer_id").val()},
                    dataType: "JSON",
                    success: function (data)
                    {
                        var start = new Date(data.time_start.replace(/-/g, '/'));
                        var end = new Date(data.time_stop.replace(/-/g, '/'));
                        var diffMs = (end - start);
                        var diffDays = Math.floor(diffMs / 86400000);
                        var diffHrs = Math.floor((diffMs % 86400000) / 3600000);
                        var diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000);
                        if (diffMins < 0) {
                            minutes = 0;
                        } else {
                            minutes = diffMins;
                        }
                        if (diffDays > 0) {
                            diffHrs = diffHrs + diffDays * 24;
                        }
                        if (diffHrs >= 0) {
                            sd = new Date(start_time);
                            start_time = dateAdd(sd, 'hour', diffHrs);
                            hours = diffHrs;
                        }
                    }
                });
            }

        },
        processData: false,
        contentType: false
    });
}
$.ajax({
    url: BASE_URL + "aut/timer/get_running_timer",
    type: "POST",
    data: {},
    dataType: "JSON",
    success: function (data)
    {
        show_running_timer(data);
    }
});
function show_running_timer(data) {
    $(".running_timer_list").html('');

    $.each(data, function (i, value) {
        new_hours = 0;
        new_minutes = 0;
        if ($('.name-ofcust h6').text() == value.name) {
            return;
        }
        if ((value.identifier == "" && value.po == "") || (value.identifier == "" || value.po == "")) {
            var sap = '';
        } else {
            var sap = ' / ';
        }
        var start = new Date(value.time_start.replace(/-/g, '/'));
        var end = new Date(value.time_stop.replace(/-/g, '/'));
        var diffMs = (end - start);
        var diffDays = Math.floor(diffMs / 86400000);
        var diffHrs = Math.floor((diffMs % 86400000) / 3600000);
        var diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000);
        if (diffMins < 0) {
            new_minutes = 0;
        } else {
            new_minutes = diffMins;
        }
        if (diffDays > 0) {
            diffHrs = diffHrs + diffDays * 24;
        }
        if (diffHrs >= 0) {
            new_hours = diffHrs;
        }
        var d = new Date(value.time_start.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
        var parts = d.split(":");
        var ds = new Date(value.time_stop.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
        var html = '<div class="main-timer-div-new timer_running_new_div running_timer_project_' + value.timer_project_id + '" data-stop="false" data-id="' + value.customer_id + '" style="background-color: #' + value.color_dec + ';border: 3px solid #' + value.color_dec + ';">' +
                '<input type="hidden" class="timer_id" value="' + value.timer_id + '">' +
                '<input type="hidden" class="timer_project_id" value="' + value.timer_project_id + '">' +
                '<input type="hidden" class="customer_id" value="' + value.customer_id + '">' +
                '<div class="timer-header" style="color: #ffffff;">' +
                '<div class="name-ofcust-new">' +
                '<h6>' + value.name + '</h6>' +
                '<p>' + value.identifier + '' + sap + '' + value.po + '</p>' +
                '</div>' +
                '<div class="time-count">' +
                '<p class="cursor-pointer">' +
                '<span class="hours_sap running_hours">' + new_hours + '</span>' +
                '<span class="hours_sap colon">:</span>' +
                '<span style="font-size: 15px;font-weight: bold;" class="running_minutes">' + (new_minutes ? (new_minutes > 9 ? new_minutes : "0" + new_minutes) : "00") + '</span>' +
                '</p>' +
                '</div>' +
                '</div>' +
                '<div class="timer-footer running_timer_footer" style="color: #ffffff;">' +
                '<div class="chat" >' +
                '<i class="far fa-comment cursor-pointer add_timer_description" data-type="des"></i>' +
                '<i class="fas fa-pencil-alt cursor-pointer timer_edit_icon" data-id="' + value.timer_id + '" id="timer_edit' + value.timer_project_id + '"></i>' +
                '</div>' +
                '<div class="play-action" >' +
                '<div class="action">' +
                '<i class="fas fa-play-circle cursor-pointer start" style="display:none;"></i>' +
                '<i class="fas fa-pause-circle cursor-pointer stop"></i>' +
                '</div>' +
                '</div>' +
                '<div class="time" style="color: #ffffff;">' +
                '<p class="start_time cursor-pointer start_time_new" data-type="start">' +
                '<span class="start_timer_running_hour">' + parts[0] + '</span>:<span class="start_timer_running_minute">' + parts[1] + '</span> -' +
                '</p>' +
                '&nbsp;' +
                '<p class="end_time cursor-pointer end_time_new" data-type="end">' +
                '<span class="stop_timer_hour">' + ds.split(':')[0] + '</span>:<span class="stop_timer_minute">' + ds.split(':')[1] + '</span>' +
                '</p>' +
                '</div>' +
                '</div>' +
                ' </div>';
        $(".running_timer_list").append(html);
    });
    $(".new_project_list").show();
    $('.pin_timer_listing').find('.main-timer-div-new').show();
    $.each(data, function (i, value) {
        $('.pin_timer_listing').find(".new_main_box_" + value.timer_project_id).hide();
    });
    timer_variable = [];
    $.each($('.timer_running_new_div:visible'), function (i) {
        timer_variable.push($(this).find('.timer_project_id').val());
    });
    obj = [];
    new_add();
}
function new_timer() {
    var varname = '';
    for (i = 0; i < timer_variable.length; i++) {
        varname = 't_' + timer_variable[i];
        clearTimeout(obj[varname]);
        obj[varname] = setTimeout(new_add, 1000);
    }
}
function new_add() {
    new_seconds++;
    if (new_seconds >= 60) {
        new_seconds = 0;
        nd = new Date(start_time);
        if (document.hidden == false) {
            $.ajax({
                url: BASE_URL + "aut/timer/get_running_timer",
                type: "POST",
                data: {},
                dataType: "JSON",
                success: function (data)
                {
                    $.each(data, function (i, data) {
                        if ($('.running_timer_project_' + data.timer_project_id).attr('data-stop') == 'false') {
                            running_minutes = 0;
                            running_hours = 0;
                            var start = new Date(data.time_start.replace(/-/g, '/'));
                            var end = new Date(data.time_stop.replace(/-/g, '/'));
                            var diffMs = (end - start);
                            var diffDays = Math.floor(diffMs / 86400000);
                            var diffHrs = Math.floor((diffMs % 86400000) / 3600000);
                            var diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000);
                            if (diffMins < 0) {
                                running_minutes = 0;
                            } else {
                                running_minutes = diffMins;
                            }
                            if (diffDays > 0) {
                                diffHrs = diffHrs + diffDays * 24;
                            }
                            if (diffHrs >= 0) {
                                running_hours = diffHrs;
                            }
                            $('.running_timer_project_' + data.timer_project_id).find('.running_hours,.running_minutes').text('');
                            $('.running_timer_project_' + data.timer_project_id).find('.running_hours').text(running_hours);
                            $('.running_timer_project_' + data.timer_project_id).find('.running_minutes').text((running_minutes ? (running_minutes > 9 ? running_minutes : "0" + running_minutes) : "0"));
                            var d = new Date(data.time_start.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
                            var parts = d.split(":");
                            $('.running_timer_project_' + data.timer_project_id).find(".start_timer_hour").text(parts[0]);
                            $('.running_timer_project_' + data.timer_project_id).find(".start_timer_minute").text(parts[1]);
                            var ds = new Date(data.time_stop.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
                            $('.running_timer_project_' + data.timer_project_id).find(".stop_timer_hour").text(ds.split(":")[0]);
                            $('.running_timer_project_' + data.timer_project_id).find(".stop_timer_minute").text(ds.split(":")[1]);
                        }
                    });

                    $('.pin_timer_listing').find('.main-timer-div-new').show();
                    $.each(data, function (i, value) {
                        $('.pin_timer_listing').find(".new_main_box_" + value.timer_project_id).hide();
                    });
                    if (data.length == 0) {
                        $(".main-timer-div").hide();
                        $(".running_timer_list").html('');
                    }
                }
            });
        }
    }
    $(".colon").css({visibility: toggle1 ? "visible" : "hidden"});
    toggle1 = !toggle1;
    new_timer();
}
function removeArrayValue(arr) {
    var what, a = arguments, L = a.length, ax;
    while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax = arr.indexOf(what)) !== -1) {
            arr.splice(ax, 1);
        }
    }
    return arr;
}
function create_new_timer() {
    if ($(".new_timer_id").val() == "") {
        $(".timer_project_box").css('border', '3px solid red');
    } else {
        $.ajax({
            url: BASE_URL + "aut/timer/create_new_timer",
            type: "POST",
            data: new FormData($('#edit_detail')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            success: function (data)
            {
                if (data.status == "success") {
                    if (select_list_type == "week") {
                        window.location.href = BASE_URL + 'timer?calender=1&week=' + $(".week_count").val();
                    } else {
                        window.location.href = BASE_URL + 'timer?list=1&date=' + $('.day_div').find('.today_date').text().replace(/ /g, '');
                    }
                }
            },
            processData: false,
            contentType: false
        });
    }
}
function save_calender_timer() {
    var start_diff = (new Date("1970-1-1 " + $('#edit_start_timer').val()) - new Date("1970-1-1 " + $(".edit_start_time").attr('data-default'))) / 1000 / 60;
    var end_diff = (new Date("1970-1-1 " + $('#edit_end_timer').val()) - new Date("1970-1-1 " + $(".end_start_time").attr('data-default'))) / 1000 / 60;
    $("#diff_start").val(start_diff);
    $("#diff_end").val(end_diff);
    $.ajax({
        url: BASE_URL + "aut/timer/calender_edit_timer",
        type: "POST",
        data: new FormData($('#edit_detail')[0]),
        dataType: "JSON",
        enctype: 'multipart/form-data',
        success: function (data)
        {
            $(".main-timer-div").css('background-color', '#' + data.color_dec);
            $(".main-timer-div").css('border', '3px solid #' + data.color_dec);
            $(".main-timer-div .timer-footer").css('color', '#ffffff');
            $(".main-timer-div .timer-header").css('color', '#ffffff');
            $('.main-timer-div .time').css('color', '#ffffff');
            start_time = data.time_start;
            first_time = data.time_start;
            last_time = data.time_stop;
            var start = new Date(data.time_start.replace(/-/g, '/'));
            var end = new Date(data.time_stop.replace(/-/g, '/'));
            var diffMs = (end - start);
            var diffDays = Math.floor(diffMs / 86400000);
            var diffHrs = Math.floor((diffMs % 86400000) / 3600000);
            var diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000);
            if (diffMins < 0) {
                minutes = 0;
            } else {
                minutes = diffMins;
            }
            if (diffDays > 0) {
                diffHrs = diffHrs + diffDays * 24;
            }
            if (diffHrs >= 0) {
                sd = new Date(start_time.replace(/-/g, '/'));
                start_time = dateAdd(sd, 'hour', diffHrs);
                hours = diffHrs;
            }
            var d = new Date(first_time.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
            var parts = d.split(":");
            $(".start_timer_hour").text(parts[0]);
            $(".start_timer_minute").text(parts[1]);
            var ds = new Date(data.time_stop.replace(/-/g, '/')).toLocaleTimeString('en-US', {hour12: true, hour: 'numeric', minute: 'numeric'});
            $(".stop_timer_hour").text(ds.split(":")[0]);
            $(".stop_timer_minute").text(ds.split(":")[1]);
            $("#edit_detail_modal").modal("hide");
            if (select_list_type == "week") {
                window.location.href = BASE_URL + 'timer?calender=1&week=' + $(".week_count").val();
            } else {
                window.location.href = BASE_URL + 'timer?list=1&date=' + $('.day_div').find('.today_date').text().replace(/ /g, '');
            }
        },
        processData: false,
        contentType: false
    });
}
function save_pin_timer() {
    $.ajax({
        url: BASE_URL + "aut/timer/pin_timer_edit",
        type: "POST",
        data: new FormData($('#edit_detail')[0]),
        dataType: "JSON",
        enctype: 'multipart/form-data',
        success: function (data)
        {
            $(".running_timer_project_" + data.timer_project_id).css('background-color', '#' + data.color_dec);
            $(".running_timer_project_" + data.timer_project_id).css('border', '3px solid #' + data.color_dec);
            $(".new_main_box_" + data.timer_project_id).css('border', '3px solid #' + data.color_dec);
            $("#edit_detail_modal").modal("hide");
            requestData();
        },
        processData: false,
        contentType: false
    });
}
function add_customer() {
    if ($("#customer_name").val() == "") {
        $("#customer_name").css('border', '1px solid red');
    } else {
        $.ajax({
            url: BASE_URL + "aut/timer/add_timer_customer",
            type: "POST",
            data: new FormData($('#add_customer')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            success: function (data)
            {
                if (data.status == "success") {
                    $("#customer_listing").append('<option data-id="' + data.id + '" value="' + data.id + '" data-subtext="' + data.identifier + '" selected>' + data.name + '</option>');
                    $("#customer_listing").selectpicker("refresh");
                    $("#add_customer_modal").modal('hide');
                }
            },
            processData: false,
            contentType: false
        });
    }
}
function set_customer_value(input) {
    if ($(input).find(':selected').val() == 0) {
        $("#add_customer_modal").modal('show');
    }
}
function add_item() {
    if ($("#item_name").val() == "") {
        $("#item_name").css('border', '1px solid red');
    } else {
        $.ajax({
            url: BASE_URL + "aut/timer/add_timer_item",
            type: "POST",
            data: new FormData($('#add_item')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            success: function (data)
            {
                if (data.status == "success") {
                    if ($('#' + data.optgroup).parents('html').length > 0) {
                        $('#' + data.optgroup).append('<option data-id="' + data.id + '"  value="' + data.id + '" selected>' + data.name + '</option>');
                    } else {
                        var html = '<optgroup label="' + data.optgroup.replace(/_/g, ' ') + '" id="' + data.optgroup + '">' +
                                '<option data-id="' + data.id + '"  value="' + data.id + '" selected>' + data.name + '</option>' +
                                '</optgroup>';
                        $("#item_listing").append(html);
                    }
                    $("#item_listing").selectpicker("refresh");
                    $("#add_item_modal").modal('hide');
                }
            },
            processData: false,
            contentType: false
        });

    }
}
function set_item_value(input) {
    if ($(input).find(':selected').val() == 'add_new') {
        $("#add_item_modal").modal('show');
    }
}
function set_charges() {
    var asedate = new Date($("#time_start").val().replace(/-/g, '/')).toString();
    var sedate = asedate.split(' ');
    setTimeout(function () {
        $("#time_start").val(sedate[0] + ', ' + sedate[1] + ' ' + sedate[2] + ', ' + sedate[3]);
    }, 0);
}
function getFormattedDate(date) {
    date = new Date(date.replace(/-/g, '/'));
    var year = date.getFullYear();
    var month = (1 + date.getMonth()).toString();
    month = month.length > 1 ? month : '0' + month;
    var day = date.getDate().toString();
    day = day.length > 1 ? day : '0' + day;
    return month + '/' + day + '/' + year;
}
function add_timer_project() {
    if ($("#customer_listing").val() == "" && $("#item_listing").val() == "" && $("#type_listing").val() == "") {
        $('#add_timer_project').find('.dropdown-toggle').css('border', '1px solid red');
    } else {
        $.ajax({
            url: BASE_URL + "aut/timer/add_timer_project",
            type: "POST",
            data: new FormData($('#add_timer_project')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            success: function (data)
            {
                if ($("#add_quick_timer_project").data('pos') == 'in_modal') {
                    $.ajax({
                        url: BASE_URL + "aut/timer/get_all_projects",
                        type: "POST",
                        data: {},
                        dataType: "JSON",
                        success: function (data)
                        {
                            $('.new_project_listing').html('');
                            $.each(data, function (i, value) {
                                var html = '<div class="new_project_list cursor-pointer start_timer new_main_box_' + value.project_id + '" data-custid="' + value.customer_id + '" data-id="' + value.timer_project_id + '" style="background-color: rgb(255, 255, 255);border: 3px solid #' + value.color_dec + ';" data-modal="in">' +
                                        '<div class="timer-header" style="color:#000000;">' +
                                        '<div class="name-ofcust-new">' +
                                        '<h6>' + value.name + '</h6>' +
                                        '<p>' + value.identifier_po + '</p>' +
                                        '' + value.item + '</div>' +
                                        '<span class="set_edit_icon" style="display: none;"><i class="fas fa-pencil-alt" style="color: #007bff;"></i></span>' +
                                        '</div>' +
                                        '</div>';
                                $('.new_project_listing').append(html);
                            });
                            $("#add_quick_timer_project").removeAttr('data-pos');
                            $("#add_project_modal").modal('hide');
                        }
                    });
                } else {
                    location.reload();
                }
            },
            processData: false,
            contentType: false
        });
    }
}