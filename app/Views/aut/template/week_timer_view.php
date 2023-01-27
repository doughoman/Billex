<!-- Calender Start -->

<?php
$arr = array();
foreach ($calender_hours as $key => $item) {
    $start = strtotime('00:00:00');
    $end = strtotime(date("G:i", strtotime($item['start'])));
    $mins = ($end - $start) / 60;
    $total = $mins + $item['dif_min'];
    if ($total > 1440) {
        $next_time1 = $total - 1440;
        $item['dif_min'] = $item['dif_min'] - $next_time1;
    }
    $arr[$item['day']][$key] = $item;
    if ($total > 1440) {
        $total_days = floor($total / 1440);
        $next_time = $total - 1440;
        for ($i = 0; $i < $total_days; $i++) {
            if ($total_days > 1) {
                for ($j = 0; $j < $total_days - 1; $j++) {
                    $day_plus = $j + 1;
                    $day = date('D', strtotime("+" . $day_plus . " day", strtotime('this ' . $item['day'])));
                    $next_day = 1440;
                    $item['dif_min'] = $next_day;
                    $arr[$day][$key] = $item;
                }
                $next_day1 = $total - (1440 * ($j + 1));
                $day = date('D', strtotime("+" . ($j + 1) . " day", strtotime('this ' . $item['day'])));
                $item['dif_min'] = $next_day1;
                $arr[$day][$key] = $item;
            } else {
                $day_plus = $i + 1;
                $day = date('D', strtotime("+" . $day_plus . " day", strtotime('this ' . $item['day'])));
                $next_day = $total - 1440;
                $item['dif_min'] = $next_day;
                $arr[$day][$key] = $item;
            }
        }
    }
}

ksort($arr, SORT_NUMERIC);
$mmin = 0;
$sumArray = array();
if ($week == "") {
    if (date('l') == "Monday") {
        $timestamp = strtotime('this Monday');
    } else {
        $timestamp = strtotime('last Monday');
    }
} else {
    if (date('l') == "Monday") {
        $timestamp = strtotime(date('Y-m-d', strtotime('this Monday', strtotime($week . ' week'))));
    } else {
        $timestamp = strtotime(date('Y-m-d', strtotime('last Monday', strtotime($week . ' week'))));
    }
}
$final_array = array();
for ($i = 0; $i < 7; $i++) {
    if (!array_key_exists(date('D', $timestamp), $arr)) {
        $arr[date('D', $timestamp)][0] = array(
            "start" => 0,
            "end" => 0,
            "dif_min" => 0,
            "color_dec" => "",
            "day" => date('D', $timestamp),
            "timer_id" => 0
        );
    }
    $final_array[date('D', $timestamp)] = $arr[date('D', $timestamp)];
    $timestamp = strtotime('+1 day', $timestamp);
}
foreach ($final_array as $k => $subArray) {
    $key = 'dif_min';
    $sumArray[$mmin] = array_sum(array_column(array_values($subArray), $key));
    $mmin++;
}
$total_minute = 0;
for ($i = 0; $i < 7; $i++) {
    if (isset($sumArray[$i])) {
        $total_minute = $total_minute + $sumArray[$i];
    }
}
$thours = intval($total_minute / 60);
$tminute = $total_minute - (intval($total_minute / 60) * 60);
$tminute = ($tminute < 10 ? "0" . $tminute : $tminute);
?>
<div class="calender_total_heading">
    <div class="month_div">
        <i class="fas fa-times back_graph cursor-pointer"></i>
    </div>
    <div class="month_div">
        <i class="fas fa-chevron-left cursor-pointer back_week"></i>
        <?php
        if ($week == "") {
            if (date('l') == "Monday") {
                $date = strtotime('this Monday');
            } else {
                $date = strtotime('last Monday');
            }
        } else {
            if (date('l') == "Monday") {
                $date = strtotime(date('Y-m-d', strtotime('this Monday', strtotime($week . ' week'))));
            } else {
                $date = strtotime(date('Y-m-d', strtotime('last Monday', strtotime($week . ' week'))));
            }
        }
        ?>
        <h4 class="calenderMonthName">
            <?php
            if (date('M', strtotime(date('Y-m-d', $date))) != date('M', strtotime(date('Y-m-d', $timestamp)))) {
                echo date('M', strtotime(date('Y-m-d', $date))) . ' / ' . date('M', strtotime(date('Y-m-d', $timestamp)));
            } else {
                echo date('F', strtotime(date('Y-m-d', $date)));
            }
            ?>
        </h4>
        <i class="fas fa-chevron-right cursor-pointer forward_week" style="display: none;"></i>
    </div>
    <div class="day-time">
        <p>
            <span id="hours" class="hours_sap"><?= $thours; ?></span>
            <span class="hours_sap" style="visibility: visible;">:</span>
            <span id="minutes"><?= $tminute; ?></span>
        </p>
    </div>
</div>

<div class="main_total_div">
    <div class="day_total">
    </div>           
    <?php
    for ($i = 0; $i < 7; $i++) {
        if (isset($sumArray[$i])) {
            if ($sumArray[$i] > 0) {
                $hours = intval($sumArray[$i] / 60);
                $minutes = $sumArray[$i] - (intval($sumArray[$i] / 60) * 60);
                $minutes = ($minutes < 10 ? "0" . $minutes : $minutes);
                echo '<div class="day_total">
                                  <label>' . $hours . ':' . $minutes . '</label>
                              </div>';
            } else {
                echo '<div class="day_total"></div>';
            }
        } else {
            echo '<div class="day_total"></div>';
        }
    }
    ?>
</div>
<div class="calender-main">
    <div class="calender-section">
        <div class="heading-row">
            <div class="heading-col">
            </div>
            <?php
            if ($week == "") {
                if (date('l') == "Monday") {
                    $timestamp = strtotime('this Monday');
                } else {
                    $timestamp = strtotime('last Monday');
                }
            } else {
                if (date('l') == "Monday") {
                    $timestamp = strtotime(date('Y-m-d', strtotime('this Monday', strtotime($week . ' week'))));
                } else {
                    $timestamp = strtotime(date('Y-m-d', strtotime('last Monday', strtotime($week . ' week'))));
                }
            }
            for ($i = 0; $i < 7; $i++) {
                echo '<div class="heading-col ' . date('l', $timestamp) . '">
                              <div class="heading">
                                  <label>' . date('D', $timestamp) . '</label>
                                  <p>' . date('d', $timestamp) . '</p>
                              </div> 
                          </div>';
                $timestamp = strtotime('+1 day', $timestamp);
            }
            ?>
        </div>
        <div class="fill-main">
            <div class="fill-column">
                <div class="col-time">
                    <div class="col-inner-time">12a</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">1a</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">2a</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">3a</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">4a</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">5a</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">6a</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">7a</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">8a</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">9a</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">10a</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">11a</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">12p</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">1p</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">2p</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">3p</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">4p</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">5p</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">6p</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">7p</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">8p</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">9p</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">10p</div>
                </div>
                <div class="col-time">
                    <div class="col-inner-time">11p</div>
                </div>
            </div>
            <div class="fill-column mon Monday">
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
            </div>
            <div class="fill-column tue Tuesday">
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
            </div>
            <div class="fill-column wed Wednesday">
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
            </div>
            <div class="fill-column thu Thursday">
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
            </div>
            <div class="fill-column fri Friday">
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
            </div>
            <div class="fill-column sat Saturday">
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
            </div>
            <div class="fill-column sun Sunday">
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
                <div class="col-time"></div>
            </div>
        </div>
    </div>
    <!-- End Calender -->
</div>
<script src="<?php echo base_url(); ?>js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>js/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url(); ?>js/jquery.rotaryswitch.js" type="text/javascript" async=""></script>
<?php
foreach ($calender_hours as $key => $value) {
    if ($value['dif_min'] > 0) {
        $start = strtotime('00:00:00');
        $end = strtotime(date("G:i", strtotime($value['start'])));
        $mins = ($end - $start) / 60;
        $total = $mins + $value['dif_min'];
        if ($total > 1440) {
            $total_days = floor($total / 1440);
            $next_time = $total - 1440;
            if ($total_days > 1) {
                for ($j = 0; $j < $total_days - 1; $j++) {
                    $day_plus = $j + 1;
                    $day = strtolower(date('D', strtotime("+" . $day_plus . " day", strtotime('this ' . $value['day']))));
                    $next_day = 1440;
                    ?>
                    <script>
                        var ehtml = '<div class="event_div calender_edit cursor-pointer" data-id="<?php echo $value['timer_id'] ?>" style="width:100%;height:<?php echo $next_day; ?>px;background-color:#<?php echo $value['color_dec'] ?>;top:0px;"></div>';
                        $(".<?php echo $day; ?>").append(ehtml);
                        ehtml = '';
                    </script>
                    <?php
                }
                $next_day1 = $total - (1440 * ($j + 1));
                $day = strtolower(date('D', strtotime("+" . ($j + 1) . " day", strtotime('this ' . $value['day']))));
                ?>
                <script>
                    var ehtml = '<div class="event_div calender_edit cursor-pointer" data-id="<?php echo $value['timer_id'] ?>" style="width:100%;height:<?php echo $next_day1 / 2; ?>px;background-color:#<?php echo $value['color_dec'] ?>;top:0px;"></div>';
                    $(".<?php echo $day; ?>").append(ehtml);
                    ehtml = '';
                </script>
                <?php
            } else {
                $day = strtolower(date('D', strtotime("+1 day", strtotime('this ' . $value['day']))));
                $next_day = $total - 1440;
                ?>
                <script>
                    var ehtml = '<div class="event_div calender_edit cursor-pointer" data-id="<?php echo $value['timer_id'] ?>" style="width:100%;height:<?php echo $next_day / 2; ?>px;background-color:#<?php echo $value['color_dec'] ?>;top:0px;"></div>';
                    $(".<?php echo $day; ?>").append(ehtml);
                    ehtml = '';
                </script>
                <?php
            }
            $value['dif_min'] = $value['dif_min'] - $next_time;
        }
        ?>
        <script>
            var html = '<div class="event_div calender_edit cursor-pointer" data-id="<?php echo $value['timer_id'] ?>" style="width:100%;height:<?php echo $value['dif_min'] / 2 ?>px;background-color:#<?php echo $value['color_dec'] ?>;top:<?php echo $mins / 2; ?>px;"></div>';
            $(".<?php echo strtolower($value['day']); ?>").append(html);
        </script>
        <?php
        $total = 0;
    }
}
?>