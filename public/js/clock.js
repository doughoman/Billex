$(function () {
// Simone Note: initial values for start and end time and boolean value for which one selected
    var val_start = new Date(2020, 0, 17, 12, 0, 0);
    var val_end = new Date(2020, 0, 17, 13, 30, 0);
    var val_color = 'ebebeb';
    var val_selected = false; // false = start / true = end
    var val_isRunning = false;
    var selected_day_week;
    var new_val_start;
    var selected_time_type;
    var time_type;
    var old_timer_start;
    var old_timer_stop;
    var end;
    var time_frame_id;
    var smart_running_status;
    var timer_id;
    var rotarySwitch;
    // Simone Note: default array of mysql sample data set
    var val_array = [
        {startDate: new Date(2020, 3, 1, 10, 0, 0), endDate: new Date(2020, 3, 2, 10, 50, 0), color: 8750469},
        {startDate: new Date(2020, 3, 2, 10, 30, 0), endDate: new Date(2020, 3, 3, 11, 30, 0), color: 4565224},
        {startDate: new Date(2020, 3, 3, 14, 0, 0), endDate: new Date(2020, 3, 4, 15, 30, 0), color: 5025616}
    ];
    $(document).on("click", ".date_backward", function () {
        set_project_array($(this).parents('.month_div').find('h4').text(), 'past');
    });
    $(document).on("click", ".date_forward", function () {
        set_project_array($(this).parents('.month_div').find('h4').text(), 'future');
    });
    function set_project_array(date, status) {
        $.ajax({
            url: BASE_URL + "aut/timer/get_today_timer",
            type: "POST",
            data: {date: date, status: status},
            dataType: "JSON",
            success: function (data)
            {
                val_project_array = [];
                $.each(data, function (i, value) {
                    var start_time = new Date(value.time_start.replace(/-/g, '/'));
                    var end_time = new Date(value.time_stop.replace(/-/g, '/'));
                    var diffMs = (end_time - start_time); // milliseconds between now & Christmas
                    var diffMins = Math.round(diffMs / 60000); // minutes
                    if (diffMins > 1) {
                        var attrs = {startDate: new Date(value.time_start.replace(/-/g, '/')), endDate: new Date(value.time_stop.replace(/-/g, '/')), color: value.color_dec, timer_id: value.timer_id};
                        val_project_array.push(attrs);
                    }
                });
            }
        });
    }
    set_project_array($('.today_new_date').val(), 'nope');
    rotarySwitch = $('.rotarySwitch').rotaryswitch({
        themeClass: 'big',
        minimum: 0,
        maximum: 60,
        step: 1,
        hideInput: false
    });
    $(document).on("click", ".change_time_type", function () {
        if ($(this).val() == 0) {
            $('.rotaryswitchPlugin').remove();
            $('.rotary_switch_main_div').append('<input type="text" class="rotarySwitch" value="0">');
            rotarySwitch = $('.rotarySwitch').rotaryswitch({
                themeClass: 'big',
                minimum: 0,
                maximum: 12,
                step: 1,
                hideInput: false
            });

            $('.rotaryswitchPlugin .switch').css('background', 'url(' + BASE_URL + '/images/darkBigFront@2x2.png) no-repeat');
            $(this).prop('checked', true);
            $(this).val(1);
            if (val_selected) {
                switch_hour = val_end.toLocaleTimeString('en-US', {hour12: false, hour: 'numeric'});
                hour = val_end.toLocaleTimeString('en-US', {hour12: true, hour: 'numeric'});
            } else {
                switch_hour = val_start.toLocaleTimeString('en-US', {hour12: false, hour: 'numeric'});
                hour = val_start.toLocaleTimeString('en-US', {hour12: true, hour: 'numeric'});
            }
            hour = hour.split(' ')[0];
            rotarySwitchValue_current = hour;
            rotarySwitch.val(hour);
            rotarySwitch.on('change', function () {
                update();
            });
            switch_hour = parseInt(switch_hour) * 5;
            $('.rotaryswitchPlugin .switch').css('transform', 'rotate(' + (switch_hour * 6) + 'deg)');
        } else {
            $('.rotaryswitchPlugin').remove();
            $('.rotary_switch_main_div').append('<input type="text" class="rotarySwitch" value="0">');
            rotarySwitch = $('.rotarySwitch').rotaryswitch({
                themeClass: 'big',
                minimum: 0,
                maximum: 60,
                step: 1,
                hideInput: false
            });
            rotarySwitch.on('change', function () {
                update();
            });
            $('.rotaryswitchPlugin .switch').css('background', 'url(' + BASE_URL + '/images/darkBigFront@2x.png) no-repeat');
            $(this).prop('checked', false);
            $(this).val(0);
            if (val_selected) {
                $('#new_end').trigger('click');
            } else {
                $('#new_start').trigger('click');
            }
        }
    });
    function get_content_field(node) {
        return node.children(".content");
    }
    function formatAMPM(date) {
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0' + minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }
    function toColor(num) {
        num >>>= 0;
        var b = num & 0xFF,
                g = (num & 0xFF00) >>> 8,
                r = (num & 0xFF0000) >>> 16,
                a = 0.8;
        return "rgba(" + [r, g, b, a].join(",") + ")";
    }
    get_content_field($('#new_start')).text(formatAMPM(val_start));
    get_content_field($('#new_end')).text(formatAMPM(val_end));
    // current selected time
    function current_hours() {
        return val_selected ? val_end.getHours() : val_start.getHours();
    }
    function current_minutes() {
        return val_selected ? val_end.getMinutes() : val_start.getMinutes();
    }
    function current_time() {
        return val_selected ? val_end : val_start;
    }
    function findNearestValues() {
        var minVal = new Date(1970, 1, 1, 0, 0, 0), maxVal = new Date(2500, 1, 1, 0, 0, 0);
        $.each(val_array, function (i, value) {
            minVal = (value.startDate != "" ? new Date(value.startDate) : "");
            maxVal = (value.endDate != "" ? new Date(value.endDate) : "");
        });
        if (minVal != "") {
            if (minVal.getTime() === new Date(1970, 1, 1, 0, 0, 0).getTime()) {
                $('#prev').css("visibility", "hidden");
            } else {
                $('#prev').css("visibility", "visible");
                $('#prev').text(formatAMPM(minVal));
            }
        } else {
            $('#prev').css("visibility", "hidden");
        }
        if (maxVal != "") {
            if (maxVal.getTime() === new Date(2500, 1, 1, 0, 0, 0).getTime()) {
                $('#next').text('Running');
            } else {
                $('#next').css("visibility", "visible");
                $('#next').text(formatAMPM(maxVal));
            }
        } else {
            $('#next').css("visibility", "hidden");
        }
        if (maxVal == "" && smart_running_status == 1) {
            $('#next').css("visibility", "visible");
            $('#next').text('Running');
        }
    }
    function setNearestValue() {
        var minVal = new Date(1970, 1, 1, 0, 0, 0), maxVal = new Date(2500, 1, 1, 0, 0, 0);
        $.each(val_array, function (i, value) {
            minVal = new Date(value.startDate);
            maxVal = new Date(value.endDate);
        });
        if (minVal.getTime() !== new Date(1970, 1, 1, 0, 0, 0).getTime() && val_selected === false) {
            val_start = new Date(minVal);
            rotarySwitchValue_before = rotarySwitchValue_current = val_start.getMinutes();
        }

        if (val_selected) {
            if (maxVal.getTime() === new Date(2500, 1, 1, 0, 0, 0).getTime()) {
                val_end = new Date();
                val_isRunning = true;
            } else {
                val_end = new Date(maxVal);
                val_isRunning = false;
            }
            rotarySwitchValue_before = rotarySwitchValue_current = val_end.getMinutes();
        }

        rotarySwitch.val(rotarySwitchValue_current).change();
        update();
    }
    function rgbToHex(red, green, blue) {
        var out = '';
        for (var i = 0; i < 3; ++i) {
            var n = typeof arguments[i] == 'number' ? arguments[i] : parseInt(arguments[i]);
            if (isNaN(n) || n < 0 || n > 255) {
                return false;
            }
            out += (n < 16 ? '0' : '') + n.toString(16);
        }
        return out;
    }
    $(document).on("click", ".start_time,.end_time", function (e) {
        $('.rotaryswitchPlugin').remove();
        $('.rotary_switch_main_div').append('<input type="text" class="rotarySwitch" value="0">');
        rotarySwitch = $('.rotarySwitch').rotaryswitch({
            themeClass: 'big',
            minimum: 0,
            maximum: 60,
            step: 1,
            hideInput: false
        });
        rotarySwitch.on('change', function () {
            update();
        });
        $this = $(this);
        if ($(this).data('attr') == "stop") {
            timer_id = $(this).parents('.daybox-main').data('id');
            selected_day_week = $(this).parents('.daybox-footer').find(".calender_edit").data('type');
        } else {
            timer_id = $(this).parents('.timer_running_new_div').find('.timer_id').val();
        }
        if ($(this).data('type') == 'in_modal' && timer_id == null) {
            timer_id = $("#edit_detail_modal").find("#edit_timer_id").val();
            $("#edit_start_end_time").attr('data-pos', 'in_modal');
        }
        $("#edit_start_end_time").find('.timer_id').val(timer_id);
        if ($this.data('newtimetype') != "day" && $this.data('newtimetype') != "week") {
            time_type = $this.data('type');

            $.ajax({
                url: BASE_URL + "aut/timer/get_timer",
                type: "POST",
                data: {'timer_id': timer_id},
                dataType: "JSON",
                success: function (data)
                {
                    if ($this.data('type') == "in_modal") {
                        today_start = new Date(data.time_start.replace(/-/g, '/'));
                        time_start = today_start.getFullYear() + "-" + (today_start.getMonth() + 1) + "-" + today_start.getDate() + " " + $("#edit_start_timer").val();
                        val_start = new Date(time_start.replace(/-/g, '/'));
                        old_timer_start = time_start;
                        today_end = new Date(data.time_stop.replace(/-/g, '/'));
                        time_stop = today_end.getFullYear() + "-" + (today_end.getMonth() + 1) + "-" + today_end.getDate() + " " + $("#edit_end_timer").val();
                        val_end = new Date(data.time_stop.replace(/-/g, '/'));
                        old_timer_stop = time_stop;
                    } else {
                        val_start = new Date(data.time_start.replace(/-/g, '/'));
                        val_end = new Date(data.time_stop.replace(/-/g, '/'));
                        old_timer_start = data.time_start;
                        old_timer_stop = data.time_stop;
                    }
                    if ($this.data('type') == "start") {
                        $('#new_end').removeClass("selected");
                        $('#new_start').addClass("selected");
                        val_selected = false;
                        rotarySwitchValue_before = rotarySwitchValue_current = val_start.getMinutes();
                    } else {
                        if ($this.data('timetype') == "start") {
                            $('#new_end').removeClass("selected");
                            $('#new_start').addClass("selected");
                            val_selected = false;
                            rotarySwitchValue_before = rotarySwitchValue_current = val_start.getMinutes();
                            selected_time_type = "start";
                        } else {
                            $('#new_end').addClass("selected");
                            $('#new_start').removeClass("selected");
                            val_selected = true;
                            rotarySwitchValue_before = rotarySwitchValue_current = val_end.getMinutes();
                            selected_time_type = "end";
                        }
                    }
                    set_project_array($('.today_new_date').val(), 'nope');
                    smart_running_status = data.smart_running_time;
                    val_array = [];
                    var attrs = {startDate: (data.smart_start_time != '' ? new Date(data.smart_start_time.replace(/-/g, '/')) : ''), endDate: (data.smart_end_time != '' ? new Date(data.smart_end_time.replace(/-/g, '/')) : ''), color: data.color_dec};
                    val_array.push(attrs);
                    setTimeout(function () {
                        time_frame_id = data.time_frame_id;
                        $(".otherframe").css('top', '50px');
                        $(".timer_frame_" + data.time_frame_id).css('top', '15px');
                        $(".timer_frame_" + data.time_frame_id).css('background-color', '#' + data.color_dec);
                        val_color = data.color_dec;
                        update();
                    }, 200);
                    rotarySwitch.val(rotarySwitchValue_current).change();
                    $("#edit_start_end_time").modal("show");
                }
            });
        } else {
            val_flag = true;
            today = new Date($('#time_start').val().replace(/-/g, '/'));
            if ($this.data('newtimetype') == "day" && $("#edit_timer_id").val() == "") {
                time_start = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate() + " " + $("#edit_start_timer").val();
                time_stop = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate() + " " + $("#edit_end_timer").val();
                old_timer_start = time_start;
            } else {
                if ($("#edit_timer_id").val() == "") {
                    time_start = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate() + " " + $("#edit_start_timer").val();
                    start_hour = new Date(time_start.replace(/-/g, '/')).getHours();
                    time_start = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate() + " " + start_hour + ":00:00";
                    old_timer_start = time_start;
                    time_stop = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate() + " " + $("#edit_end_timer").val();
                    stop_hour = new Date(time_stop.replace(/-/g, '/')).getHours();
                    time_stop = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate() + " " + stop_hour + ":00:00";
                } else {
                    val_flag = false;
                    $.ajax({
                        url: BASE_URL + "aut/timer/get_timer",
                        type: "POST",
                        data: {'timer_id': $("#edit_detail_modal").find('#edit_timer_id').val()},
                        dataType: "JSON",
                        success: function (data)
                        {
                            today = new Date(data.time_start.replace(/-/g, '/'));
                            end = new Date(data.time_stop.replace(/-/g, '/'));
                            time_start = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate() + " " + $("#edit_start_timer").val();
                            start_hour = new Date(time_start.replace(/-/g, '/')).getHours();
                            time_start = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate() + " " + start_hour + ":" + $("#edit_start_timer").val().split(":")[1].split(" ")[0] + ":" + today.getSeconds();
                            old_timer_start = time_start;
                            time_stop = end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate() + " " + $("#edit_end_timer").val();
                            stop_hour = new Date(time_stop.replace(/-/g, '/')).getHours();
                            if (stop_hour == 0) {
                                stop_hour = stop_hour + "0";
                            }
                            time_stop = end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate() + " " + stop_hour + ":" + $("#edit_end_timer").val().split(":")[1].split(" ")[0] + ":" + end.getSeconds();
                            smart_running_status = data.smart_running_time;
                            val_array = [];
                            var attrs = {startDate: (data.smart_start_time != '' ? new Date(data.smart_start_time.replace(/-/g, '/')) : ''), endDate: (data.smart_end_time != '' ? new Date(data.smart_end_time.replace(/-/g, '/')) : ''), color: data.color_dec};
                            val_array.push(attrs);
                            setTimeout(function () {
                                time_frame_id = data.time_frame_id;
                                $(".otherframe").css('top', '50px');
                                $(".timer_frame_" + data.time_frame_id).css('top', '15px');
                                $(".timer_frame_" + data.time_frame_id).css('background-color', '#' + data.color_dec);
                                val_color = data.color_dec;
                                update();
                            }, 200);
                        }
                    });
                }
            }
            setTimeout(function () {
                set_project_array($('.today_new_date').val(), 'nope');
                val_start = new Date(time_start.replace(/-/g, '/'));
                val_end = new Date(time_stop.replace(/-/g, '/'));
                if (val_start.getHours() == '23' && val_start.getMinutes() == '00' && val_end.getHours() == '00' && val_end.getMinutes() == '00') {
                    val_end = dateAdd(val_end, 'day', 1);
                }
                val_color = $('.new_project_list').css('border-bottom-color');
                rgb = val_color.replace(/[^\d,]/g, '').split(',');
                val_color = rgbToHex(rgb[0], rgb[1], rgb[2]);
                if (val_flag) {
                    day_week_date = new Date($('#time_start').val().replace(/-/g, '/'));
                    create_time_start = day_week_date.getFullYear() + "-" + (day_week_date.getMonth() + 1) + "-" + day_week_date.getDate() + " "
                            + val_start.getHours() + ":" + val_start.getMinutes() + ":" + val_start.getSeconds();
                    create_time_stop = day_week_date.getFullYear() + "-" + (day_week_date.getMonth() + 1) + "-" + day_week_date.getDate() + " "
                            + val_end.getHours() + ":" + val_end.getMinutes() + ":" + val_end.getSeconds();
                    $.ajax({
                        url: BASE_URL + "aut/timer/get_smart_timer",
                        type: "POST",
                        data: {'time_start': create_time_start, 'time_stop': create_time_stop},
                        dataType: "JSON",
                        success: function (data)
                        {
                            smart_running_status = data.smart_running_time;
                            val_array = [];
                            var attrs = {startDate: (data.smart_start_time != '' ? new Date(data.smart_start_time.replace(/-/g, '/')) : ''), endDate: (data.smart_end_time != '' ? new Date(data.smart_end_time.replace(/-/g, '/')) : ''), color: data.color_dec};
                            val_array.push(attrs);
                        }
                    });
                }
            }, 500);
            setTimeout(function () {
                if ($this.data('timetype') == "start") {
                    $('#new_end').removeClass("selected");
                    $('#new_start').addClass("selected");
                    val_selected = false;
                    rotarySwitchValue_before = rotarySwitchValue_current = val_start.getMinutes();
                    selected_time_type = "start";
                } else {
                    $('#new_end').addClass("selected");
                    $('#new_start').removeClass("selected");
                    val_selected = true;
                    rotarySwitchValue_before = rotarySwitchValue_current = val_end.getMinutes();
                    selected_time_type = "end";
                }
                rotarySwitch.val(rotarySwitchValue_current).change();
                $("#edit_start_end_time").modal("show");
            }, 500);
        }
    });
    $('#prev').on('click', function () {
        $('#new_end').removeClass("selected");
        $('#new_start').addClass("selected");
        val_selected = false;
        rotarySwitchValue_before = rotarySwitchValue_current = val_start.getMinutes();
        rotarySwitch.val(rotarySwitchValue_current).change();
        setNearestValue();
        if ($(".change_time_type").val() == 1) {
            switch_hour = val_start.toLocaleTimeString('en-US', {hour12: false, hour: 'numeric'});
            hour = val_start.toLocaleTimeString('en-US', {hour12: true, hour: 'numeric'});
            hour = hour.split(' ')[0];
            rotarySwitchValue_current = hour;
            rotarySwitch.val(hour);
            switch_hour = parseInt(switch_hour) * 5;
            $('.rotaryswitchPlugin .switch').css('transform', 'rotate(' + (switch_hour * 6) + 'deg)');
        }
    });
    $('#next').on('click', function () {
        if ($(this).html() != "Running") {
            $('#new_end').addClass("selected");
            $('#new_start').removeClass("selected");
            val_selected = true;
            rotarySwitch.val(rotarySwitchValue_current).change();
            setNearestValue();
        }
        if ($(this).html() == "Running") {
            $.ajax({
                url: BASE_URL + "aut/timer/show_smart_running_timer",
                type: "POST",
                data: {'timer_id': $("#edit_start_end_time").find('.timer_id').val()},
                dataType: "JSON",
                success: function (data)
                {
                    val_start = new Date(data.time_start.replace(/-/g, '/'));
                    val_end = new Date(data.time_stop.replace(/-/g, '/'));
                    old_timer_start = data.time_start;
                    old_timer_stop = data.time_stop;
                    $('#new_end').addClass("selected");
                    $('#new_start').removeClass("selected");
                    val_selected = true;
                    rotarySwitchValue_before = rotarySwitchValue_current = val_end.getMinutes();
                    smart_running_status = data.smart_running_time;
                    val_array = [];
                    var attrs = {startDate: (data.smart_start_time != '' ? new Date(data.smart_start_time.replace(/-/g, '/')) : ''), endDate: (data.smart_end_time != '' ? new Date(data.smart_end_time.replace(/-/g, '/')) : ''), color: data.color_dec};
                    val_array.push(attrs);
                    setTimeout(function () {
                        time_frame_id = data.time_frame_id;
                        $(".otherframe").css('top', '50px');
                        $(".timer_frame_" + data.time_frame_id).css('top', '15px');
                        $(".timer_frame_" + data.time_frame_id).css('background-color', '#' + data.color_dec);
                        val_color = data.color_dec;
                        update();
                    }, 200);
                    rotarySwitch.val(rotarySwitchValue_current).change();
                    $("#save_new_value").attr('id', 'save_smart_running');
                }
            });
        }
    });
    $(document).on('click', '#save_smart_running', function () {
        var time_start = val_start.getFullYear() + "-" + (val_start.getMonth() + 1) + "-" + val_start.getDate() + " "
                + val_start.getHours() + ":" + val_start.getMinutes() + ":" + val_start.getSeconds();
        var time_stop = val_end.getFullYear() + "-" + (val_end.getMonth() + 1) + "-" + val_end.getDate() + " "
                + val_end.getHours() + ":" + val_end.getMinutes() + ":" + val_end.getSeconds();
        $.ajax({
            url: BASE_URL + "aut/timer/smart_ruuning_timer",
            type: "POST",
            data: {'timer_id': $("#edit_start_end_time").find('.timer_id').val(), 'time_start': time_start, 'time_stop': time_stop},
            dataType: "JSON",
            success: function (data)
            {
                location.reload();
            }
        });
    });
    $('#new_start').on('click', function () {
        $('#new_end').removeClass("selected");
        $('#new_start').addClass("selected");
        val_selected = false;
        rotarySwitchValue_before = rotarySwitchValue_current = val_start.getMinutes();
        rotarySwitch.val(rotarySwitchValue_current).change();
        if ($(".change_time_type").val() == 1) {
            switch_hour = val_start.toLocaleTimeString('en-US', {hour12: false, hour: 'numeric'});
            hour = val_start.toLocaleTimeString('en-US', {hour12: true, hour: 'numeric'});
            hour = hour.split(' ')[0];
            rotarySwitchValue_current = hour;
            rotarySwitch.val(hour);
            switch_hour = parseInt(switch_hour) * 5;
            $('.rotaryswitchPlugin .switch').css('transform', 'rotate(' + (switch_hour * 6) + 'deg)');
        }
    });
    $('#new_end').on('click', function () {
        $('#new_end').addClass("selected");
        $('#new_start').removeClass("selected");
        val_selected = true;
        rotarySwitchValue_before = rotarySwitchValue_current = val_end.getMinutes();
        rotarySwitch.val(rotarySwitchValue_current).change();
        if ($(".change_time_type").val() == 1) {
            switch_hour = val_end.toLocaleTimeString('en-US', {hour12: false, hour: 'numeric'});
            hour = val_end.toLocaleTimeString('en-US', {hour12: true, hour: 'numeric'});
            hour = hour.split(' ')[0];
            rotarySwitchValue_current = hour;
            rotarySwitch.val(hour);
            switch_hour = parseInt(switch_hour) * 5;
            $('.rotaryswitchPlugin .switch').css('transform', 'rotate(' + (switch_hour * 6) + 'deg)');
        }
    });
    function update() {
        if ($(".change_time_type").val() == 1) {
            if (rotarySwitch.val() == 0) {
                rotarySwitch.val(12);
            }
        }
        rotarySwitchValue_before = rotarySwitchValue_current;
        rotarySwitchValue_current = rotarySwitch.val();
        var offset_rotarySwitch;
        if ($(".change_time_type").val() == 1) {
            if (rotarySwitchValue_current == 0) {
                rotarySwitchValue_current = 12;
            }
        }
        if (Math.abs(rotarySwitchValue_current - rotarySwitchValue_before) > 30) {
            if (rotarySwitchValue_current < 30) {
                offset_rotarySwitch = (60 - (-1) * (rotarySwitchValue_current - rotarySwitchValue_before));
            } else {
                offset_rotarySwitch = (-1) * (60 - (rotarySwitchValue_current - rotarySwitchValue_before));
            }
        } else {
            offset_rotarySwitch = rotarySwitchValue_current - rotarySwitchValue_before;
        }
        if ($(".change_time_type").val() == 1) {
            if (offset_rotarySwitch == 11 || offset_rotarySwitch == -11) {
                offset_rotarySwitch = offset_rotarySwitch * -1;
            }
            if (offset_rotarySwitch > 0) {
                offset_rotarySwitch = 1;
            } else if (offset_rotarySwitch < 0) {
                offset_rotarySwitch = -1;
            } else {
                offset_rotarySwitch = 0;
            }
            offset_rotarySwitch = offset_rotarySwitch * 60;
        }
        if (val_selected === false) {
            val_start.setMinutes(val_start.getMinutes() + offset_rotarySwitch);
        } else {
            val_end.setMinutes(val_end.getMinutes() + offset_rotarySwitch);
        }
        if (offset_rotarySwitch !== 0 && val_selected) {
            val_isRunning = false;
        }

        //timeframe
        $('#timeframe').empty();
        width = 330;
        start = Math.floor(current_hours() * 60 + current_minutes() - width / 2);
        if (start < 0) {
            start += 1440;
        }
        for (i = 0; i < width; i++) {
            if ((start + i) % 60 === 0) {
                var ticket = document.createElement("div");
                ticket.className = "frame-number";
                ticket.style.left = i + 'px';
                var time = (start + i) / 60 > 23 ? (start + i) / 60 % 24 : (start + i) / 60;
                if (time === 0) {
                    time = 12 + 'a';
                } else if (time > 0 && time < 12) {
                    time = time + 'a';
                } else if (time === 12) {
                    time = time + 'p';
                } else {
                    time = time - 12 + 'p';
                }
                ticket.textContent = time;
                $('#timeframe').append(ticket);
            }
        }

        if (val_start > val_end && $(".change_time_type").val() == 0) {
            val_end = dateAdd(val_start, 'minute', 1);
            val_selected = false;
            $('#new_start').addClass("selected");
            $('#new_end').removeClass("selected");
        }
        if (val_start >= val_end && $(".change_time_type").val() == 1) {
            val_end = dateAdd(val_start, 'minute', 60);
            val_selected = false;
            $('#new_start').addClass("selected");
            $('#new_end').removeClass("selected");
        }
        timeframe_width = ((val_end - val_start) / 1000 / 60) << 0;
        time_frame = document.createElement("div");
        time_frame.className = "time_frame";
        time_frame.textContent = timeframe_width < 45 ? timeframe_width + ' mins' :
                (timeframe_width < 60 ? timeframe_width + ' mins ' : Math.floor(timeframe_width / 60) + ' hrs ' + timeframe_width % 60 + ' mins');
        $('.time_duration').html(time_frame);
        if (timeframe_width > width / 2) {
            timeframe_width = width / 2;
        }
        ticket_frame = document.createElement("div");
        ticket_frame.className = "frame";
        ticket_frame.style.width = timeframe_width + 'px';
        ticket_frame.style.background = '#' + val_color;
        if (!val_selected) {
            ticket_frame.style.left = '51%';
        } else {
            ticket_frame.style.right = '51%';
        }
        $('#timeframe').append(ticket_frame);
        left_time = new Date(current_time());
        left_time.setMinutes(left_time.getMinutes() - Math.floor(width / 2));
        right_time = new Date(current_time());
        right_time.setMinutes(right_time.getMinutes() + Math.floor(width / 2));
        $.each(val_project_array, function (i, value) {
            if (value.startDate < right_time && value.endDate > left_time) {
                var r_left = value.startDate > left_time ? value.startDate : left_time;
                var r_right = value.endDate > right_time ? right_time : value.endDate;
                var frame_width = Math.floor(((r_right - r_left) / 1000 / 60) << 0);
                var offset = ((r_left - left_time) / 1000 / 60) << 0;
                var new_frame = document.createElement("div");
                new_frame.className = "otherframe timer_frame_" + value.timer_id;
                if (time_frame_id == value.timer_id) {
                    new_frame.style = '';
                } else {
                    new_frame.style.width = frame_width + 'px';
                    new_frame.style.background = '#' + value.color;
                    new_frame.style.left = offset + 'px';
                    new_frame.style.top = '50px';
                }
                $('#timeframe').append(new_frame);
            }
        });
        get_content_field($('#new_start')).text(formatAMPM(val_start));
        get_content_field($('#new_end')).text(formatAMPM(val_end));
        findNearestValues();
    }

    rotarySwitch.on('change', function () {
        update();
    });

    $(document).on("click", "#set_new_value", function () {
        var today_date_time = new Date();
        var utc_date_time = today_date_time.getUTCFullYear() + '-' + (today_date_time.getUTCMonth() + 1) + '-' + today_date_time.getUTCDate() + ' ' + today_date_time.getUTCHours() + ':' + today_date_time.getUTCMinutes() + ':59';
        var time_start = val_start.getFullYear() + "-" + (val_start.getMonth() + 1) + "-" + val_start.getDate() + " "
                + val_start.getHours() + ":" + val_start.getMinutes() + ":" + val_start.getSeconds();
        var time_stop = val_end.getFullYear() + "-" + (val_end.getMonth() + 1) + "-" + val_end.getDate() + " "
                + val_end.getHours() + ":" + val_end.getMinutes() + ":00";
        if (Date.parse(time_stop) > Date.parse(dateAdd(new Date(utc_date_time.replace(/-/g, '/')), 'hour', $(".timezone").val())) && Date.parse(time_start) < Date.parse(dateAdd(new Date(utc_date_time.replace(/-/g, '/')), 'hour', $(".timezone").val()))) {
            bootbox.confirm({
                closeButton: false,
                className: "sign_up_alert",
                backdrop: true,
                onEscape: true,
                message: "The end time is set in the future. Do you want to save the timer with this end time or run the timer?",
                buttons: {
                    confirm: {
                        label: 'Keep End Time',
                        className: 'btn-primary bootbox-ok-button'
                    },
                    cancel: {
                        label: 'Run Timer',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        $(".time_stop_new_value").val(time_stop);
                        $(".stop_new_value").val(time_stop);
                        startTime = val_start.toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: 'numeric'});
                        if (startTime.split(':')[0].length == 1) {
                            startTime = '0' + startTime;
                        }
                        endTime = val_end.toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: 'numeric'});
                        if (endTime.split(':')[0].length == 1) {
                            endTime = '0' + endTime;
                        }
                        $("#edit_start_timer").val(startTime);
                        $("#edit_end_timer").val(endTime);
                        $("#edit_start_end_time").modal("hide");
                    } else {
                        $.ajax({
                            url: BASE_URL + "aut/timer/get_timer",
                            type: "POST",
                            data: {'timer_id': $("#edit_start_end_time").find('.timer_id').val()},
                            dataType: "JSON",
                            success: function (data)
                            {
                                val_end = new Date(data.time_stop.replace(/-/g, '/'));
                                time_stop1 = val_end.getFullYear() + "-" + (val_end.getMonth() + 1) + "-" + val_end.getDate() + " "
                                        + val_end.getHours() + ":" + val_end.getMinutes() + ":00";
                                $(".time_stop_new_value").val(time_stop1);
                                $(".stop_new_value").val(time_stop1);
                                startTime = val_start.toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: 'numeric'});
                                if (startTime.split(':')[0].length == 1) {
                                    startTime = '0' + startTime;
                                }
                                endTime = val_end.toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: 'numeric'});
                                if (endTime.split(':')[0].length == 1) {
                                    endTime = '0' + endTime;
                                }
                                $("#edit_start_timer").val(startTime);
                                $("#edit_end_timer").val(endTime);
                                $("#edit_start_end_time").modal("hide");
                            }
                        });
                    }
                }
            });
        } else if (Date.parse(time_stop) > Date.parse(dateAdd(new Date(utc_date_time.replace(/-/g, '/')), 'hour', $(".timezone").val())) && Date.parse(time_start) > Date.parse(dateAdd(new Date(utc_date_time.replace(/-/g, '/')), 'hour', $(".timezone").val()))) {
            bootbox.confirm({
                closeButton: false,
                className: "sign_up_alert",
                backdrop: true,
                onEscape: true,
                message: "The start and end times are set in the future. Do you want so save the timer with these values or reset the form?",
                buttons: {
                    confirm: {
                        label: 'Keep Values',
                        className: 'btn-primary bootbox-ok-button'
                    },
                    cancel: {
                        label: 'Reset Form',
                        className: 'btn-danger bootbox-ok-button'
                    }
                },
                callback: function (result) {
                    if (result) {
                        $(".time_stop_new_value").val(time_stop);
                        $(".stop_new_value").val(time_stop);
                        startTime = val_start.toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: 'numeric'});
                        if (startTime.split(':')[0].length == 1) {
                            startTime = '0' + startTime;
                        }
                        endTime = val_end.toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: 'numeric'});
                        if (endTime.split(':')[0].length == 1) {
                            endTime = '0' + endTime;
                        }
                        $("#edit_start_timer").val(startTime);
                        $("#edit_end_timer").val(endTime);
                        $("#edit_start_end_time").modal("hide");
                    } else {
                        $.ajax({
                            url: BASE_URL + "aut/timer/get_timer",
                            type: "POST",
                            data: {'timer_id': $("#edit_start_end_time").find('.timer_id').val()},
                            dataType: "JSON",
                            success: function (data)
                            {
                                val_start = new Date(data.time_start.replace(/-/g, '/'));
                                val_end = new Date(data.time_stop.replace(/-/g, '/'));
                                old_timer_start = data.time_start;
                                old_timer_stop = data.time_stop;
                                $('#new_end').removeClass("selected");
                                $('#new_start').addClass("selected");
                                val_selected = false;
                                rotarySwitchValue_before = rotarySwitchValue_current = val_start.getMinutes();
                                rotarySwitch.val(rotarySwitchValue_current).change();
                            }
                        });
                    }
                }
            });
        } else {
            $(".time_stop_new_value").val(time_stop);
            $(".stop_new_value").val(time_stop);
            startTime = val_start.toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: 'numeric'});
            if (startTime.split(':')[0].length == 1) {
                startTime = '0' + startTime;
            }
            endTime = val_end.toLocaleTimeString('en-US', {hour12: true, hour: '2-digit', minute: 'numeric'});
            if (endTime.split(':')[0].length == 1) {
                endTime = '0' + endTime;
            }
            $("#edit_start_timer").val(startTime);
            $("#edit_end_timer").val(endTime);
            $("#edit_start_end_time").modal("hide");
        }
    });
    $(document).on("click", "#save_new_value", function () {
        if (Date.parse(new Date(old_timer_stop.replace(/-/g, '/'))) != Date.parse(val_end)) {
            stop = 1;
        } else {
            stop = 0;
        }
        var time_start = val_start.getFullYear() + "-" + (val_start.getMonth() + 1) + "-" + val_start.getDate() + " "
                + val_start.getHours() + ":" + val_start.getMinutes() + ":" + val_start.getSeconds();
        var time_stop = val_end.getFullYear() + "-" + (val_end.getMonth() + 1) + "-" + val_end.getDate() + " "
                + val_end.getHours() + ":" + val_end.getMinutes() + ":00";
        var today_date_time = new Date();
        var utc_date_time = today_date_time.getUTCFullYear() + '-' + (today_date_time.getUTCMonth() + 1) + '-' + today_date_time.getUTCDate() + ' ' + today_date_time.getUTCHours() + ':' + today_date_time.getUTCMinutes() + ':59';
        if (Date.parse(time_stop) > Date.parse(dateAdd(new Date(utc_date_time.replace(/-/g, '/')), 'hour', $(".timezone").val())) && Date.parse(time_start) < Date.parse(dateAdd(new Date(utc_date_time.replace(/-/g, '/')), 'hour', $(".timezone").val()))) {
            bootbox.confirm({
                closeButton: false,
                className: "sign_up_alert",
                backdrop: true,
                onEscape: true,
                message: "The end time is set in the future. Do you want to save the timer with this end time or run the timer?",
                buttons: {
                    confirm: {
                        label: 'Keep End Time',
                        className: 'btn-primary bootbox-ok-button'
                    },
                    cancel: {
                        label: 'Run Timer',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        edit_time(time_start, time_stop, 1);
                    } else {
                        $.ajax({
                            url: BASE_URL + "aut/timer/get_timer",
                            type: "POST",
                            data: {'timer_id': $("#edit_start_end_time").find('.timer_id').val()},
                            dataType: "JSON",
                            success: function (data)
                            {
                                val_end = new Date(data.time_stop.replace(/-/g, '/'));
                                time_stop1 = val_end.getFullYear() + "-" + (val_end.getMonth() + 1) + "-" + val_end.getDate() + " "
                                        + val_end.getHours() + ":" + val_end.getMinutes() + ":00";
                                edit_time(time_start, time_stop1, 0);
                            }
                        });
                    }
                }
            });
        } else if (Date.parse(time_stop) > Date.parse(dateAdd(new Date(utc_date_time.replace(/-/g, '/')), 'hour', $(".timezone").val())) && Date.parse(time_start) > Date.parse(dateAdd(new Date(utc_date_time.replace(/-/g, '/')), 'hour', $(".timezone").val()))) {
            bootbox.confirm({
                closeButton: false,
                className: "sign_up_alert",
                backdrop: true,
                onEscape: true,
                message: "The start and end times are set in the future. Do you want so save the timer with these values or reset the form?",
                buttons: {
                    confirm: {
                        label: 'Keep Values',
                        className: 'btn-primary bootbox-ok-button'
                    },
                    cancel: {
                        label: 'Reset Form',
                        className: 'btn-danger bootbox-ok-button'
                    }
                },
                callback: function (result) {
                    if (result) {
                        edit_time(time_start, time_stop, 1);
                    } else {
                        $.ajax({
                            url: BASE_URL + "aut/timer/get_timer",
                            type: "POST",
                            data: {'timer_id': $("#edit_start_end_time").find('.timer_id').val()},
                            dataType: "JSON",
                            success: function (data)
                            {
                                val_start = new Date(data.time_start.replace(/-/g, '/'));
                                val_end = new Date(data.time_stop.replace(/-/g, '/'));
                                old_timer_start = data.time_start;
                                old_timer_stop = data.time_stop;
                                $('#new_end').removeClass("selected");
                                $('#new_start').addClass("selected");
                                val_selected = false;
                                rotarySwitchValue_before = rotarySwitchValue_current = val_start.getMinutes();
                                rotarySwitch.val(rotarySwitchValue_current).change();
                            }
                        });
                    }
                }
            });
        } else {
            edit_time(time_start, time_stop, stop);
        }
    });
    function edit_time(time_start, time_stop, stop) {
        $.ajax({
            url: BASE_URL + "aut/timer/update_new_time",
            type: "POST",
            data: {'timer_id': $("#edit_start_end_time").find('.timer_id').val(), 'time_start': time_start, 'time_stop': time_stop, 'stop': stop},
            dataType: "JSON",
            success: function (data)
            {
                if (data.stop == 1 && (selected_day_week == "" || typeof selected_day_week === "undefined")) {
                    location.reload();
                } else {
                    if (selected_day_week == "today") {
                        window.location.href = BASE_URL + 'timer?list=1&date=' + $('.day_div').find('.today_date').text().replace(/ /g, '');
                    } else {
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
                            sd = new Date(start_time);
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
                        $("#edit_start_end_time").modal("hide");
                        requestData();
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
                                    sd = new Date(start_time.replace(/-/g, '/'));
                                    start_time = dateAdd(sd, 'hour', diffHrs);
                                    hours = diffHrs;
                                }
                            }
                        });
                    }
                }
            }
        });
    }
});