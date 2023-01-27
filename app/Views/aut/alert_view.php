<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<style>
    .admin-contain-main-div{
        padding: 30px;
    }
</style>
<div class="container-fluid p-0">
    <div class="add_recuring_div m-0">
        <div><h4 class="add_charges_heading mb-0">Alerts</h4></div>
        <div>
            <a href="JavaScript:void(0)" class="btn btn-submit no_mob btn-addlert"><i class="fas fa-user-plus"></i> &nbsp;Add</a>
            <a href="JavaScript:void(0)" class="add_ico no_desktop btn-addlert"><i class="fas fa-plus-circle"></i></a>
        </div>
    </div>
    <div class="add_alert_form" style="display: <?= (count($alert_data) >= 1 ? "none" : "") ?>">
        <form id="add_alert" method="post">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Message</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="form[message]" id="alert_message">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Type</label>
                        <div class="col-sm-9 type_select_option">
                            <select class="form-control selectpicker" name="form[class]" data-live-search="true" title="Choose one of the type" data-width="100%" id="class_type">
                                <option class="alert-primary mb-1 mt-1" value="primary">Primary</option>
                                <option class="alert-secondary mb-1" value="secondary">Secondary</option>
                                <option class="alert-success mb-1" value="success">Success</option>
                                <option class="alert-danger mb-1" value="danger">Danger</option>
                                <option class="alert-warning mb-1" value="warning">Warning</option>
                                <option class="alert-info mb-1" value="info">Info</option>
                                <option class="alert-light mb-1" value="light">Light</option>
                                <option class="alert-dark" value="dark">Dark</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Time Stamp</label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <div class="input-group date" id="id_1">
                                    <input type="text" value="" class="form-control" id="time_stamp" name="form[time_stamp]" autocomplete="off">
                                    <div class="input-group-addon input-group-append">
                                        <div class="input-group-text">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mt-3">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-9 btn-submitdiv pr-3">
                            <button type="button" class="btn btn-submit update_customer" onclick="add_alert();">Add Alert<i class="fas fa-spinner fa-spin add_customer_loder" style="display: none;"></i></button>
                            <button class="btn btn-submit cancel_form" type="button">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row mt-3 customer_alert" style="display: <?= (count($alert_data) >= 1 ? "" : "none") ?>">   
        <div class="main-table-div alerts_listing">
            <div class="table-heading">
                <div class="table-col_message">
                    <p>Message</p>
                </div>
                <div class="table-col_class">
                    <p>Class Type</p>
                </div>
                <div class="add_user_div">

                </div>
            </div>
            <div class="mainrow-div" style="background-color: #FFFFFF;">
                <?php
                $blcount = 1;
                foreach ($alert_data as $value) {
                    ?>
                    <div class="charge-tbl action_click" style="<?= (count($alert_data) == $blcount ? "" : "border-bottom: 1px solid #c8c8c8;"); ?>">
                        <div class="results-data ">
                            <div class="table-col table-col_message">
                                <p class="<?= ($value['class'] == 'light' ? "alert" : "text"); ?>-<?= $value['class']; ?>"><?= $value['message']; ?></p>
                            </div> 
                            <div class="table-col table-col_class">
                                <p><?= ucfirst($value['class']); ?></p>
                            </div> 
                            <div class="table-col table-col_edit position-relative no_mob">
                                <button type="button" class="edit_delete_btn alert_edit text-info" data-id="<?= $value['id']; ?>"><i class="fas fa-pencil-alt"></i></button>
                            </div> 
                        </div>
                        <div class="action_div" style="display: none;">
                            <div class="action-cols-alert">
                                <button type="button" class="edit_delete_btn alert_edit text-info action-margin" data-id="<?= $value['id']; ?>"><i class="fas fa-pencil-alt"></i></button>
                            </div>
                        </div>
                    </div>
                    <?php
                    $blcount++;
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script type="text/javascript">
    $(document).ready(function () {

        (function ($) {
            $(function () {
                $('#id_1').datetimepicker({
                    "allowInputToggle": true,
                    "showClose": true,
                    "showClear": true,
                    "showTodayButton": true,
                    "format": "MM/DD/YYYY HH:mm:ss",
                    "useCurrent": false,
                    icons: {
                        time: 'far fa-clock',
                        date: 'fas fa-calendar-alt',
                        up: 'fas fa-chevron-up',
                        down: 'fas fa-chevron-down',
                        previous: 'fas fa-chevron-left',
                        next: 'fas fa-chevron-right',
                        today: 'fas fa-calendar-week',
                        clear: 'fas fa-trash',
                        close: 'fas fa-times'
                    },
                });
            });
        })(jQuery);

        $(document).on("click", ".alert_edit", function () {
            $('html, body').animate({
                scrollTop: $("#page-top").offset().top
            }, 200);
            $.ajax({
                url: BASE_URL + "aut/alert/get_alert_edit_data",
                type: "POST",
                data: {'id': $(this).attr("data-id")},
                dataType: "JSON",
                success: function (data)
                {
                    $('#datetimepicker').datetimepicker('update', data.time_stamp);
                    $(".selectpicker").selectpicker('val', data.class);
                    $("#time_stamp").val(data.time_stamp);

                    $("#alert_message").val(data.message);
                    $(".update_customer").attr('onclick', "edit_alert('" + data.id + "');");
                    $(".update_customer").text('Edit Alert');
                    $(".add_alert_form").show();
                    $(".customer_alert").hide();
                }
            });
        });
        $(document).on("click", ".btn-addlert", function () {
            document.getElementById('add_alert').reset();
            $('#id_1').datetimepicker({
                "allowInputToggle": true,
                "showClose": true,
                "showClear": true,
                "showTodayButton": true,
                "format": "MM/DD/YYYY HH:mm:ss",
                "useCurrent": false,
                icons: {
                    time: 'far fa-clock',
                    date: 'fas fa-calendar-alt',
                    up: 'fas fa-chevron-up',
                    down: 'fas fa-chevron-down',
                    previous: 'fas fa-chevron-left',
                    next: 'fas fa-chevron-right',
                    today: 'fas fa-calendar-week',
                    clear: 'fas fa-trash',
                    close: 'fas fa-times'
                },
            });
            $('#class_type').selectpicker('val', '');
            $(".add_alert_form").show();
            $(".customer_alert").hide();
        });
        $(document).on("click", ".cancel_form", function () {
            $(".add_alert_form").hide();
            $(".customer_alert").show();
        });
        if ($(window).width() < 767) {
            $('.action_click').click(function () {
                $(this).toggleClass('overlay_div');
                $(this).children('.action_div').slideToggle(100);
            });
        }
    });
    function add_alert() {
        if ($("#alert_message").val() == "" && $(".selectpicker").val() == "") {
            $("#alert_message").css('border', '1px solid red');
            $(".bs-placeholder").css('border', '1px solid red');
        } else {
            $.ajax({
                url: BASE_URL + "aut/alert/ajax_add_edit_alert",
                type: "POST",
                data: new FormData($('#add_alert')[0]),
                dataType: "JSON",
                enctype: 'multipart/form-data',
                beforeSend: function () {
                    $(".add_customer_loder").show();
                },
                success: function (data)
                {
                    if (data.status == "success") {
                        location.reload();
                    }
                },
                processData: false,
                contentType: false
            });
        }
    }
    function edit_alert(id) {
        $.ajax({
            url: BASE_URL + "aut/alert/ajax_add_edit_alert/" + id,
            type: "POST",
            data: new FormData($('#add_alert')[0]),
            dataType: "JSON",
            enctype: 'multipart/form-data',
            beforeSend: function () {
                $(".edit_customer_loder").show();
            },
            success: function (data)
            {
                if (data.status == "success") {
                    location.reload();
                }
            },
            processData: false,
            contentType: false
        });
    }
</script>