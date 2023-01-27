<?php

namespace App\Controllers\aut;

use CodeIgniter\Controller;
use App\Models\CustomerModel;
use App\Models\TimerModel;
use App\Models\ChargeModel;
use App\Models\AdministrationModel;
use App\Models\UserModel;

class Timer extends Controller {

    protected $session;
    public $casscall;
    protected $helpers = ["url", "form", "general", "image", "cookie"];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->CustomerModel = new CustomerModel();
        $this->ChargeModel = new ChargeModel();
        $this->TimerModel = new TimerModel();
        $this->UserModel = new UserModel();
        $this->AdministrationModel = new AdministrationModel();
        if ($this->session->get('utc_offset') == 0) {
            $time_zone = intval(-180) / 60;
            $time_zone = '-0' . abs($time_zone) . ':00';
            $this->session->set(array('utc_offset' => $time_zone));
        }
        $pay_period = $this->session->has('pay_period');
        if (empty($pay_period)) {
            $this->session->set(array('pay_period' => 'monthly'));
        }
        $pay_period_start = $this->session->has('pay_period_start');
        if (empty($pay_period_start)) {
            $this->session->set(array('pay_period_start' => 1));
        }
        $this->session->remove(array('pay_periods', 'start_value'));
    }

    public function index() {
        set_cookie('start_date', '');
        set_cookie('end_date', '');
        set_cookie('pay_period', '');
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $this->session->remove('refURL');
            $dataStore["customer_data"] = $this->CustomerModel->get_customer();
            $count = 0;
            foreach ($dataStore["customer_data"] as $customer_value) {
                $dataStore["customer_data"][$count]["customer_id"] = base64_encode(encrypt($customer_value["customer_id"]));
                $count++;
            }
            $dataStore["chargetype_data"] = $this->ChargeModel->get_charge_type();
            $dataStore["item_data"] = $this->AdministrationModel->get_item();
            $count = 0;
            foreach ($dataStore["item_data"] as $customer_value) {
                $dataStore["item_data"][$count]["type"] = $this->CustomerModel->get_charge_type($dataStore["item_data"][$count]["type"]);
                $count++;
            }
            $this->TimerModel->set_graph();
            $dataStore['timer_data'] = $this->TimerModel->get_timer();
            $dataStore['calender_hours'] = $this->get_week_view();
            $dataStore['today_hours'] = $this->TimerModel->get_today_hours(date('Y-m-d'));
            foreach ($dataStore['today_hours'] as $key => $value) {
                $dataStore['today_hours'][$key]['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['today_hours'][$key]['time_start'], 'UTC');
                $dataStore['today_hours'][$key]['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['today_hours'][$key]['time_stop'], 'UTC');
                $dataStore['today_hours'][$key]['start'] = date('g:i A', strtotime($dataStore['today_hours'][$key]['time_start']));
                $dataStore['today_hours'][$key]['end'] = date('g:i A', strtotime($dataStore['today_hours'][$key]['time_stop']));
            }
            $dataStore['running_timers'] = $this->TimerModel->get_all_running_timer();
            foreach ($dataStore['running_timers'] as $key => $value) {
                $dataStore['running_timers'][$key]['customer_id'] = base64_encode(encrypt($value['customer_id']));
                $dataStore['running_timers'][$key]['time_start'] = convert_timezone($this->session->get('login_userid'), $value['time_start'], 'UTC');
                $dataStore['running_timers'][$key]['time_stop'] = convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC');
            }
            $dataStore['project_list'] = $this->TimerModel->get_timer_project();
            echo view('Views/aut/timer_view', $dataStore);
        } else {
            $redirect_url = base_url() . 'timer';
            $this->session->set(array('refURL' => base_url() . 'timer'));
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function get_week_view() {
        $weekCount = $this->request->getPost('weekcount');
        if ($weekCount == '') {
            $dataStore['calender_hours'] = $this->TimerModel->get_week_calender_hours();
        } else {
            $weekCount = $this->request->getPost('weekcount');
            $dataStore['calender_hours'] = $this->TimerModel->get_week_calender_hours($weekCount);
        }
        foreach ($dataStore['calender_hours'] as $key => $value) {
            $dataStore['calender_hours'][$key]['timer_id'] = base64_encode(encrypt($dataStore['calender_hours'][$key]['timer_id']));
            $dataStore['calender_hours'][$key]['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['calender_hours'][$key]['time_start'], 'UTC');
            $dataStore['calender_hours'][$key]['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['calender_hours'][$key]['time_stop'], 'UTC');
            $dataStore['calender_hours'][$key]['start'] = date('h:i A', strtotime($dataStore['calender_hours'][$key]['time_start']));
            $dataStore['calender_hours'][$key]['end'] = date('h:i A', strtotime($dataStore['calender_hours'][$key]['time_stop']));
            $color_dec = dechex($value['color_dec']);
            if (strlen($color_dec) < 6) {
                $dif = 6 - intval(strlen($color_dec));
                $add = '';
                for ($i = 0; $i < $dif; $i++) {
                    $add.='0';
                }
                $color_dec = $add . $color_dec;
            }
            $dataStore['calender_hours'][$key]['color_dec'] = $color_dec;
            $dataStore['calender_hours'][$key]['day'] = date('D', strtotime($dataStore['calender_hours'][$key]['time_start']));
        }
        if ($weekCount == '') {
            $calender_hours = $this->TimerModel->get_week_calender_stophours();
        } else {
            $calender_hours = $this->TimerModel->get_week_calender_stophours($weekCount);
        }
        if (isset($key)) {
            $new_key = $key + 1;
        } else {
            $new_key = 0;
        }
        $FirstDay = date("Y-m-d", strtotime('Monday this week'));
        $LastDay = date("Y-m-d", strtotime('Monday next week'));
        foreach ($calender_hours as $value) {
            $Date = date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC')));
            if ($Date >= $FirstDay && $Date <= $LastDay) {
                $dataStore['calender_hours'][$new_key]['timer_id'] = base64_encode(encrypt($value['timer_id']));
                if (date('l') == "Monday") {
                    $timestamp = date('Y-m-d ' . '00:00:00', strtotime('this Monday'));
                } else {
                    $timestamp = date('Y-m-d ' . '00:00:00', strtotime('last Monday'));
                }
                $dataStore['calender_hours'][$new_key]['time_start'] = $timestamp;
                $dataStore['calender_hours'][$new_key]['time_stop'] = convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC');
                $dataStore['calender_hours'][$new_key]['start'] = date('h:i A', strtotime($dataStore['calender_hours'][$new_key]['time_start']));
                $dataStore['calender_hours'][$new_key]['end'] = date('h:i A', strtotime($dataStore['calender_hours'][$new_key]['time_stop']));
                $color_dec = dechex($value['color_dec']);
                if (strlen($color_dec) < 6) {
                    $dif = 6 - intval(strlen($color_dec));
                    $add = '';
                    for ($i = 0; $i < $dif; $i++) {
                        $add.='0';
                    }
                    $color_dec = $add . $color_dec;
                }
                $dataStore['calender_hours'][$new_key]['color_dec'] = $color_dec;
                $dataStore['calender_hours'][$new_key]['day'] = date('D', strtotime($dataStore['calender_hours'][$new_key]['time_start']));
                $seconds = strtotime(convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC')) - strtotime($timestamp);
                $days = floor($seconds / 86400);
                $hours = floor(($seconds - ($days * 86400)) / 3600);
                $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
                $perMin = $minutes + ($hours * 60) + ($days * 1440);
                $dataStore['calender_hours'][$new_key]['dif_min'] = $perMin;
                $new_key++;
            }
        }
        $dataStore['week'] = $weekCount;
        $html = view('Views/aut/template/week_timer_view', $dataStore);
        if ($weekCount == '') {
            return $html;
        } else {
            echo $html;
        }
    }

    public function add_timer() {
        $dataStore = $this->TimerModel->add_timer(decrypt(base64_decode($this->request->getPost('customer_id'))), decrypt(base64_decode($this->request->getPost('timer_project_id'))));
        $dataStore = $this->TimerModel->get_timer($dataStore['timer_id']);
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['timer_id'] = base64_encode(encrypt($dataStore['timer_id']));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_stop'], 'UTC');
        sendTimerEvent($dataStore, $dataStore['name'] . ' Has been start timer.', 'Start-Timer', 'timer-' . $this->session->get('login_userid'));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function add_pin_timer() {
        $dataIn['customer_id'] = decrypt(base64_decode($this->request->getPost('customer_id')));
        $dataIn['user_id'] = $this->session->get('login_userid');
        $dataIn['item_id'] = decrypt(base64_decode($this->request->getPost('item_id')));
        $dataIn['ct_id'] = decrypt(base64_decode($this->request->getPost('ct_id')));
        $timer_id = $this->TimerModel->add_pin_timer($dataIn)['timer_id'];
        $dataStore = $this->TimerModel->get_timer($timer_id);
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['timer_id'] = base64_encode(encrypt($dataStore['timer_id']));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_stop'], 'UTC');
        sendTimerEvent($dataStore, $dataStore['name'] . ' Has been start timer.', 'Start-Timer', 'timer-' . $this->session->get('login_userid'));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function create_new_timer() {
        $dataIn = $this->request->getPost('form');
        $dataTimerIn['timer_project_id'] = decrypt(base64_decode($dataIn['new_timer_id']));
        $dataTimerIn['running'] = 0;
        $dataTimerIn['description'] = $dataIn['description'];
        $dataTimerIn['time_start'] = date('Y-m-d', strtotime($dataIn['time_start'])) . ' ' . date('H:i:s', strtotime($dataIn['start_time']));
        $dataTimerIn['time_stop'] = date('Y-m-d', strtotime($dataIn['time_start'])) . ' ' . date('H:i:s', strtotime($dataIn['end_time']));
        $dataTimerIn['time_start'] = datetimeconv_utc(date('Y-m-d H:i:s', strtotime($dataTimerIn['time_start'])), $this->session->get('login_userid'));
        $dataTimerIn['time_stop'] = datetimeconv_utc(date('Y-m-d H:i:s', strtotime($dataTimerIn['time_stop'])), $this->session->get('login_userid'));
        $dataStore = $this->TimerModel->create_new_timer($dataTimerIn);
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function timer_report() {
        if ($this->request->getPost('type') == "") {
            $type = 'week';
        } else {
            $type = $this->request->getPost('type');
        }
        if ($this->request->getPost('clear') == 1) {
            set_cookie('start_date', '');
            set_cookie('end_date', '');
            set_cookie('pay_period', '');
        }
        $timerReport = $this->TimerModel->get_report(date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), date('Y-m-d H:i:s'), 'UTC'))), $type, $this->request->getPost('count'));
        $totalMinute = 0;
        $today = array();
        if (count($timerReport['today_data']) > 0) {
            foreach ($timerReport['today_data'] as $key => $value) {
                $totalMinute = $totalMinute + intval($value['dif_min']);
            }
            foreach ($timerReport['today_data'] as $key => $value) {
                if (strlen(dechex($value['color_dec'])) < 6) {
                    $dif = 6 - intval(strlen(dechex($value['color_dec'])));
                    $add = '';
                    for ($i = 0; $i < $dif; $i++) {
                        $add.='0';
                    }
                    $value['color_dec'] = $add . dechex($value['color_dec']);
                } else {
                    $value['color_dec'] = dechex($value['color_dec']);
                }
                $timerReport['today_data'][$key]['color_dec'] = $value['color_dec'];
                $perMin = $value['dif_min'];
                $hours = intval($perMin / 60);
                $hours = ($hours < 10 ? "0" . $hours : $hours);
                $minutes = $perMin - (intval($perMin / 60) * 60);
                $minutes = ($minutes < 10 ? "0" . $minutes : $minutes);
                $timerReport['today_data'][$key]['per_min'] = number_format(($perMin * 100) / 480, 2);
                $today[$key]['name'] = $hours . ':' . $minutes;
                $today[$key]['y'] = number_format(($perMin * 100) / 480, 2);
                $today[$key]['color'] = $value['color_dec'];
            }
            if ($totalMinute == 0) {
                $minuteDiff = 480 - $totalMinute;
                if (intval($minuteDiff / 60) > 0) {
                    $perMin = intval(intval($minuteDiff / 60) * 60) + $minuteDiff - (intval($minuteDiff / 60) * 60);
                } else {
                    $perMin = $minuteDiff - (intval($minuteDiff / 60) * 60);
                }
                $hours = intval($minuteDiff / 60);
                $minutes = $minuteDiff - (intval($minuteDiff / 60) * 60);
                $timerReport['today_data'][$key + 1]['per_min'] = number_format(($perMin * 100) / 480, 2);
                $today[$key + 1]['name'] = '0:00';
                $today[$key + 1]['y'] = number_format((480 * 100) / 480, 2);
                $today[$key + 1]['color'] = '808080';
            }
        } else {
            $today[0]['name'] = '0:00';
            $today[0]['y'] = number_format((480 * 100) / 2400, 2);
            $today[0]['color'] = '808080';
        }

        $dataStore1 = $this->TimerModel->get_today_stoptimer();
        if (isset($key)) {
            $new_key = $key + 1;
        } else {
            $new_key = 0;
        }
        $new_s_min = 0;
        foreach ($dataStore1 as $value) {
            if (date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), date('Y-m-d H:i:s'), 'UTC'))) == date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC'))) && date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), date('Y-m-d H:i:s'), 'UTC'))) != date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_start'], 'UTC')))) {
                if (strlen(dechex($value['color_dec'])) < 6) {
                    $dif = 6 - intval(strlen(dechex($value['color_dec'])));
                    $add = '';
                    for ($i = 0; $i < $dif; $i++) {
                        $add.='0';
                    }
                    $value['color_dec'] = $add . dechex($value['color_dec']);
                } else {
                    $value['color_dec'] = dechex($value['color_dec']);
                }
                if (date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_start'], 'UTC'))) != date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), date('Y-m-d H:i:s'), 'UTC')))) {
                    $seconds = strtotime(convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC')) - strtotime(date('Y-m-d') . " 00:00:00");
                    $days = floor($seconds / 86400);
                    $hours = floor(($seconds - ($days * 86400)) / 3600);
                    $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
                    $today[$new_key]['name'] = $hours . ':' . $minutes;
                    $smin = $minutes + ($hours * 60);
                    $today[$new_key]['y'] = number_format(($smin * 100) / 480, 2);
                    $today[$new_key]['color'] = $value['color_dec'];
                    $x_min = $hours * 60;
                    $new_s_min = $new_s_min + $minutes + $x_min;
                    $new_key ++;
                }
            }
        }
        $totalMinute = $totalMinute + $new_s_min;

        $weekMinute = 0;
        if (count($timerReport['week_data']) > 0) {
            $arr = array();
            foreach ($timerReport['week_data'] as $key => $item) {
                $weekMinute = $weekMinute + intval($item['dif_min']);
                if (strlen(dechex($item['color_dec'])) < 6) {
                    $dif = 6 - intval(strlen(dechex($item['color_dec'])));
                    $add = '';
                    for ($i = 0; $i < $dif; $i++) {
                        $add.='0';
                    }
                    $item['color_dec'] = $add . dechex($item['color_dec']);
                } else {
                    $item['color_dec'] = dechex($item['color_dec']);
                }
                $timerReport['week_data'][$key]['color_dec'] = $item['color_dec'];
                $perMin = $item['dif_min'];
                $hours = intval($perMin / 60);
                $hours = ($hours < 10 ? "0" . $hours : $hours);
                $minutes = $perMin - (intval($perMin / 60) * 60);
                $minutes = ($minutes < 10 ? "0" . $minutes : $minutes);
                $timerReport['week_data'][$key]['per_min'] = number_format(($perMin * 100) / 480, 2);
                $week[$key]['name'] = $hours . ':' . $minutes;
                $week[$key]['y'] = number_format(($perMin * 100) / 2400, 2);
                $week[$key]['color'] = '#' . $item['color_dec'];
            }

            if ($weekMinute == 0) {
                $minuteDiff = 2400 - $weekMinute;
                if (intval($minuteDiff / 60) > 0) {
                    $perMin = intval(intval($minuteDiff / 60) * 60) + $minuteDiff - (intval($minuteDiff / 60) * 60);
                } else {
                    $perMin = $minuteDiff - (intval($minuteDiff / 60) * 60);
                }
                $hours = intval($minuteDiff / 60);
                $minutes = $minuteDiff - (intval($minuteDiff / 60) * 60);
                $week[$key + 1]['name'] = '0:00';
                $week[$key + 1]['y'] = number_format((2400 * 100) / 2400, 2);
                $week[$key + 1]['color'] = '#808080';
            }
        } else {
            $week[0]['name'] = '0:00';
            $week[0]['y'] = number_format((2400 * 100) / 2400, 2);
            $week[0]['color'] = '#808080';
        }

        $dataStore = $this->TimerModel->get_week_calender_stophours();
        if (isset($key)) {
            $week_key = $key + 1;
        } else {
            $week_key = 0;
        }
        $new_week_minute = 0;
        $FirstDay = date("Y-m-d", strtotime('Monday this week'));
        $LastDay = date("Y-m-d", strtotime('Monday next week'));
        foreach ($dataStore as $item) {
            $stop_date = date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $item['time_stop'], 'UTC')));
            if ($stop_date >= $FirstDay && $stop_date <= $LastDay) {
                if (strlen(dechex($item['color_dec'])) < 6) {
                    $dif = 6 - intval(strlen(dechex($item['color_dec'])));
                    $add = '';
                    for ($i = 0; $i < $dif; $i++) {
                        $add.='0';
                    }
                    $item['color_dec'] = $add . dechex($item['color_dec']);
                } else {
                    $item['color_dec'] = dechex($item['color_dec']);
                }
                $timerReport['week_data'][$week_key]['color_dec'] = $item['color_dec'];
                if (date('l') == "Monday") {
                    $timestamp = date('Y-m-d ' . '00:00:00', strtotime('this Monday'));
                } else {
                    $timestamp = date('Y-m-d ' . '00:00:00', strtotime('last Monday'));
                }
                $seconds = strtotime(convert_timezone($this->session->get('login_userid'), $item['time_stop'], 'UTC')) - strtotime($timestamp);
                $days = floor($seconds / 86400);
                $hours = floor(($seconds - ($days * 86400)) / 3600);
                $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
                $perMin = $minutes + ($hours * 60) + ($days * 1440);
                $hours = intval($perMin / 60);
                $hours = ($hours < 10 ? "0" . $hours : $hours);
                $minutes = $perMin - (intval($perMin / 60) * 60);
                $minutes = ($minutes < 10 ? "0" . $minutes : $minutes);
                $timerReport['week_data'][$week_key]['per_min'] = number_format(($perMin * 100) / 480, 2);
                $week[$week_key]['name'] = $hours . ':' . $minutes;
                $week[$week_key]['y'] = number_format(($perMin * 100) / 2400, 2);
                $week[$week_key]['color'] = '#' . $item['color_dec'];
                $new_week_minute = $new_week_minute + $perMin;
                $week_key++;
            }
        }
        $weekMinute = $weekMinute + $new_week_minute;
        $finalResult['today'] = $today;
        $finalResult['today_hours'] = intval($totalMinute / 60);
        $finalResult['today_minute'] = sprintf("%02d", $totalMinute - (intval($totalMinute / 60) * 60));
        $finalResult['week'] = $week;
        $finalResult['week_hours'] = intval($weekMinute / 60);
        $finalResult['week_minute'] = sprintf("%02d", $weekMinute - (intval($weekMinute / 60) * 60));
        echo \GuzzleHttp\json_encode($finalResult);
    }

    public function today_graph() {
        if ($this->request->getPost('date') == 'Today' || $this->request->getPost('date') == 'Yesterday') {
            $graph_date = $this->request->getPost('date');
        } else {
            $graph_date = $this->request->getPost('date') . ' , ' . date('Y');
        }
        if ($this->request->getPost('status') == "past") {
            $date = date('Y-m-d', strtotime("-1 day", strtotime($graph_date)));
        } else if ($this->request->getPost('status') == "future") {
            $date = date('Y-m-d', strtotime("+1 day", strtotime($graph_date)));
        } else {
            $date = date('Y-m-d', strtotime($graph_date));
        }
        $date1 = strtotime($date);
        $date2 = strtotime(date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), date('Y-m-d H:i:s'), 'UTC'))));
        $diff = abs($date2 - $date1) / 60 / 60 / 24;
        $timerReport = $this->TimerModel->get_report($date, 'week');
        $totalMinute = 0;
        $today = array();
        if (count($timerReport['today_data']) > 0) {
            foreach ($timerReport['today_data'] as $key => $value) {
                $totalMinute = $totalMinute + intval($value['dif_min']);
            }
            foreach ($timerReport['today_data'] as $key => $value) {
                if (strlen(dechex($value['color_dec'])) < 6) {
                    $dif = 6 - intval(strlen(dechex($value['color_dec'])));
                    $add = '';
                    for ($i = 0; $i < $dif; $i++) {
                        $add.='0';
                    }
                    $value['color_dec'] = $add . dechex($value['color_dec']);
                } else {
                    $value['color_dec'] = dechex($value['color_dec']);
                }
                $timerReport['today_data'][$key]['color_dec'] = $value['color_dec'];
                $perMin = $value['dif_min'];
                $hours = intval($perMin / 60);
                $hours = ($hours < 10 ? "0" . $hours : $hours);
                $minutes = $perMin - (intval($perMin / 60) * 60);
                $minutes = ($minutes < 10 ? "0" . $minutes : $minutes);
                $timerReport['today_data'][$key]['per_min'] = number_format(($perMin * 100) / 480, 2);
                $today[$key]['name'] = $hours . ':' . $minutes;
                $today[$key]['y'] = number_format(($perMin * 100) / 480, 2);
                $today[$key]['color'] = $value['color_dec'];
            }
            if ($totalMinute == 0) {
                $minuteDiff = 480 - $totalMinute;
                if (intval($minuteDiff / 60) > 0) {
                    $perMin = intval(intval($minuteDiff / 60) * 60) + $minuteDiff - (intval($minuteDiff / 60) * 60);
                } else {
                    $perMin = $minuteDiff - (intval($minuteDiff / 60) * 60);
                }
                $hours = intval($minuteDiff / 60);
                $minutes = $minuteDiff - (intval($minuteDiff / 60) * 60);
                $timerReport['today_data'][$key + 1]['per_min'] = number_format(($perMin * 100) / 480, 2);
                $today[$key + 1]['name'] = '0:00';
                $today[$key + 1]['y'] = number_format((480 * 100) / 480, 2);
                $today[$key + 1]['color'] = '808080';
            }
        } else {
            $today[0]['name'] = '0:00';
            $today[0]['y'] = number_format((480 * 100) / 2400, 2);
            $today[0]['color'] = '808080';
        }

        $dataStore1 = $this->TimerModel->get_today_stoptimer($date);
        if (isset($key)) {
            $new_key = $key + 1;
        } else {
            $new_key = 0;
        }
        $new_s_min = 0;
        foreach ($dataStore1 as $value) {
            if (date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC'))) != date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_start'], 'UTC')))) {
                if (date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_start'], 'UTC'))) != date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), date('Y-m-d H:i:s'), 'UTC')))) {
                    if (strlen(dechex($value['color_dec'])) < 6) {
                        $dif = 6 - intval(strlen(dechex($value['color_dec'])));
                        $add = '';
                        for ($i = 0; $i < $dif; $i++) {
                            $add.='0';
                        }
                        $value['color_dec'] = $add . dechex($value['color_dec']);
                    } else {
                        $value['color_dec'] = dechex($value['color_dec']);
                    }
                    $seconds = strtotime(convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC')) - strtotime(date('Y-m-d', strtotime($value['time_stop'])) . " 00:00:00");
                    $days = floor($seconds / 86400);
                    $hours = floor(($seconds - ($days * 86400)) / 3600);
                    $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
                    $today[$new_key]['name'] = $hours . ':' . $minutes;
                    $smin = $minutes + ($hours * 60);
                    $today[$new_key]['y'] = number_format(($smin * 100) / 480, 2);
                    $today[$new_key]['color'] = $value['color_dec'];
                    $x_min = $hours * 60;
                    $new_s_min = $new_s_min + $minutes + $x_min;
                    $new_key ++;
                }
            }
        }

        $totalMinute = $totalMinute + $new_s_min;
        $today = array_values($today);
        $startArray = array();
        foreach ($today as $key => $row) {
            $startArray[$key] = $row['color'];
        }
        array_multisort($startArray, SORT_ASC, $today);
        $dataStore['today'] = $today;
        $dataStore['today_hours'] = intval($totalMinute / 60);
        $dataStore['today_minute'] = sprintf("%02d", $totalMinute - (intval($totalMinute / 60) * 60));
        if ($diff == 1) {
            $dataStore['label'] = 'Yesterday';
        } else if ($diff == 0) {
            $dataStore['label'] = 'Today';
        } else {
            $dataStore['label'] = date('M j', strtotime($date));
        }
        $dataStore['date'] = date('D, F j', strtotime($date));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function week_graph() {
        $dataStore = array();
        if (strtolower($this->request->getPost('type')) == 'week') {
            $type = 'WEEk';
            if ($this->request->getPost('status') == "past") {
                if ($this->request->getPost('date') == "This Week") {
                    $start_week = strtotime("last monday", strtotime("-1 week +1 day"));
                    $dataStore['start_week'] = date("m/d", $start_week);
                    $dataStore['label'] = 'Week ' . $dataStore['start_week'];
                } else {
                    $start_week = strtotime("last monday", strtotime(explode(' ', $this->request->getPost('date'))[1] . '/' . date('Y')));
                    $dataStore['start_week'] = date("m/d", $start_week);
                    $dataStore['label'] = 'Week ' . $dataStore['start_week'];
                }
            } else if ($this->request->getPost('status') == "future") {
                $start_week = strtotime("next monday", strtotime(explode(' ', $this->request->getPost('date'))[1] . '/' . date('Y')));
                $FirstDay = date("Y-m-d", strtotime('monday this week'));
                $LastDay = date("Y-m-d", strtotime('sunday this week'));
                $dataStore['start_week'] = date("m/d", $start_week);
                if (date("Y-m-d", $start_week) >= $FirstDay && date("Y-m-d", $start_week) <= $LastDay) {
                    $dataStore['start_week'] = '';
                    $dataStore['label'] = 'This Week';
                } else {
                    $dataStore['label'] = 'Week ' . $dataStore['start_week'];
                }
            }
            $dataStore['new_date'] = date('Y-m-d');
        } else if (strtolower($this->request->getPost('type')) == 'month') {
            $type = 'MONTH';
            if ($this->request->getPost('status') == "past") {
                $dataStore['label'] = date('M', strtotime("-1 month", strtotime($this->request->getPost('date'))));
                $dataStore['new_date'] = date('Y-m-d', strtotime("-1 month", strtotime($this->request->getPost('date'))));
            } else if ($this->request->getPost('status') == "future") {
                $dataStore['label'] = date('M', strtotime("+1 month", strtotime($this->request->getPost('date'))));
                $dataStore['new_date'] = date('Y-m-d', strtotime("+1 month", strtotime($this->request->getPost('date'))));
                if (date('F-Y') == date('F-Y', strtotime($dataStore['new_date']))) {
                    $dataStore['label'] = 'This Month';
                }
            }
        } else if (strtolower($this->request->getPost('type')) == 'year') {
            $type = 'YEAR';
            if ($this->request->getPost('status') == "past") {
                $dataStore['label'] = date('Y', strtotime("-1 year", strtotime($this->request->getPost('date'))));
                $dataStore['new_date'] = date('Y-m-d', strtotime("-1 year", strtotime($this->request->getPost('date'))));
            } else if ($this->request->getPost('status') == "future") {
                $dataStore['label'] = date('Y', strtotime("+1 year", strtotime($this->request->getPost('date'))));
                $dataStore['new_date'] = date('Y-m-d', strtotime("+1 year", strtotime($this->request->getPost('date'))));
                if (date('Y') == date('Y', strtotime($dataStore['new_date']))) {
                    $dataStore['label'] = 'This Year';
                }
            }
        } else {
            $type = 'PAY';
            switch ($this->session->get('pay_period')) {
                case 'monthly':
                    $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                    $dataStore['new_date'] = date('Y-m-d', strtotime($this->request->getPost('count') . ' month', strtotime($start)));
                    $dataStore['label'] = 'Pay ' . date('m/d', strtotime($dataStore['new_date']));
                    if (date('F-Y') == date('F-Y', strtotime($dataStore['new_date']))) {
                        $dataStore['label'] = 'This Pay';
                    }
                    break;
                case 'weekly':
                case 'bi-weekly':
                    if (date('l') == getDayName($this->session->get('pay_period_start'))) {
                        $date1 = strtotime('this ' . getDayName($this->session->get('pay_period_start')));
                    } else {
                        $date1 = strtotime('last ' . getDayName($this->session->get('pay_period_start')));
                    }
                    $start = date('Y-m-d', $date1);
                    if ($this->session->get('pay_period') == 'weekly') {
                        if ($this->request->getPost('count') < 0) {
                            $start_week = strtotime(date('Y-m-d', strtotime($this->request->getPost('count') . ' week', strtotime($start))));
                        } else {
                            $start_week = strtotime(date('Y-m-d', strtotime('0 week 0 day', strtotime($start))));
                        }
                    } else {
                        if ($this->request->getPost('count') < 0) {
                            $start_week = strtotime(date('Y-m-d', strtotime($this->request->getPost('count') * 2 . ' week', strtotime($start))));
                        } else {
                            $start_week = strtotime(date('Y-m-d', strtotime('0 week 0 day', strtotime($start))));
                        }
                    }
                    $FirstDay = date("Y-m-d", strtotime('monday this week'));
                    $LastDay = date("Y-m-d", strtotime('sunday this week'));
                    $dataStore['start_week'] = date("m/d", $start_week);
                    if (date("Y-m-d", $start_week) >= $FirstDay && date("Y-m-d", $start_week) <= $LastDay) {
                        $dataStore['start_week'] = '';
                        $dataStore['label'] = 'This Pay';
                    } else {
                        $dataStore['label'] = 'Pay ' . $dataStore['start_week'];
                    }
                    $dataStore['new_date'] = date('Y-m-d');
                    break;
            }
        }

        $timerReport = $this->TimerModel->get_report(date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), date('Y-m-d H:i:s'), 'UTC'))), $type, $this->request->getPost('count'), $this->request->getPost('status'));
        if (strtolower($this->request->getPost('type')) == 'pay') {
            if ($this->session->get('pay_period') == 'semi-monthly') {
                if (null !== get_cookie('pay_period')) {
                    if ($this->request->getPost('status') == 'past') {
                        if (get_cookie('pay_period') == 1) {
                            $start = date('Y-m-d', strtotime('-1 month +1 day', strtotime(get_cookie('end_date'))));
                            $dataStore['label'] = 'Pay ' . date('m/d', strtotime($start));
                        } else {
                            $day1 = $this->session->get('pay_period_start');
                            if (strlen($day1) == 4) {
                                $day1 = substr($day1, 0, 2);
                            } else {
                                $day1 = substr($day1, 0, 1);
                            }
                            $start = get_cookie('start_date');
                            $start = date('Y-m', strtotime($start)) . '-' . $day1;
                            $dataStore['label'] = 'Pay ' . date('m/d', strtotime($start));
                        }
                    } else {
                        $start = date('Y-m-d', strtotime('+1 day', strtotime(get_cookie('end_date'))));
                        $dataStore['label'] = 'Pay ' . date('m/d', strtotime($start));
                        $end = date('Y-m-d', strtotime('+1 month -1 day', strtotime(get_cookie('start_date'))));
                        if (date("Y-m-d") >= $start && date("Y-m-d") <= $end) {
                            $dataStore['label'] = 'This Pay';
                        }
                    }
                    $dataStore['new_date'] = date('Y-m-d');
                }
            }
        }
        $weekMinute = 0;
        if (count($timerReport['week_data']) > 0) {
            $arr = array();
            foreach ($timerReport['week_data'] as $key => $item) {
                $weekMinute = $weekMinute + intval($item['dif_min']);
                if (strlen(dechex($item['color_dec'])) < 6) {
                    $dif = 6 - intval(strlen(dechex($item['color_dec'])));
                    $add = '';
                    for ($i = 0; $i < $dif; $i++) {
                        $add.='0';
                    }
                    $item['color_dec'] = $add . dechex($item['color_dec']);
                } else {
                    $item['color_dec'] = dechex($item['color_dec']);
                }
                $timerReport['week_data'][$key]['color_dec'] = $item['color_dec'];
                $perMin = $item['dif_min'];
                $hours = intval($perMin / 60);
                $hours = ($hours < 10 ? "0" . $hours : $hours);
                $minutes = $perMin - (intval($perMin / 60) * 60);
                $minutes = ($minutes < 10 ? "0" . $minutes : $minutes);
                $timerReport['week_data'][$key]['per_min'] = number_format(($perMin * 100) / 480, 2);
                $week[$key]['name'] = $hours . ':' . $minutes;
                $week[$key]['y'] = number_format(($perMin * 100) / 2400, 2);
                $week[$key]['color'] = '#' . $item['color_dec'];
            }
            if ($weekMinute == 0) {
                $minuteDiff = 2400 - $weekMinute;
                if (intval($minuteDiff / 60) > 0) {
                    $perMin = intval(intval($minuteDiff / 60) * 60) + $minuteDiff - (intval($minuteDiff / 60) * 60);
                } else {
                    $perMin = $minuteDiff - (intval($minuteDiff / 60) * 60);
                }
                $hours = intval($minuteDiff / 60);
                $minutes = $minuteDiff - (intval($minuteDiff / 60) * 60);
                $week[$key + 1]['name'] = '0:00';
                $week[$key + 1]['y'] = number_format((2400 * 100) / 2400, 2);
                $week[$key + 1]['color'] = '#808080';
            }
        } else {
            $week[0]['name'] = '0:00';
            $week[0]['y'] = number_format((2400 * 100) / 2400, 2);
            $week[0]['color'] = '#808080';
        }
        $weekStore = $this->TimerModel->get_week_calender_stophours($this->request->getPost('count'), $type, $this->request->getPost('status'));
        if (isset($key)) {
            $week_key = $key + 1;
        } else {
            $week_key = 0;
        }
        $new_week_minute = 0;
        $FirstDay = date("Y-m-d", strtotime('Monday this week'));
        $LastDay = date("Y-m-d", strtotime('Monday next week'));
        foreach ($weekStore as $item) {
            $stop_date = date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $item['time_stop'], 'UTC')));
            if ($stop_date >= $FirstDay && $stop_date <= $LastDay) {
                if (strlen(dechex($item['color_dec'])) < 6) {
                    $dif = 6 - intval(strlen(dechex($item['color_dec'])));
                    $add = '';
                    for ($i = 0; $i < $dif; $i++) {
                        $add.='0';
                    }
                    $item['color_dec'] = $add . dechex($item['color_dec']);
                } else {
                    $item['color_dec'] = dechex($item['color_dec']);
                }
                $timerReport['week_data'][$week_key]['color_dec'] = $item['color_dec'];
                if (date('l') == "Monday") {
                    $timestamp = date('Y-m-d ' . '00:00:00', strtotime('this Monday'));
                } else {
                    $timestamp = date('Y-m-d ' . '00:00:00', strtotime('last Monday'));
                }
                $seconds = strtotime(convert_timezone($this->session->get('login_userid'), $item['time_stop'], 'UTC')) - strtotime($timestamp);
                $days = floor($seconds / 86400);
                $hours = floor(($seconds - ($days * 86400)) / 3600);
                $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
                $perMin = $minutes + ($hours * 60) + ($days * 1440);
                $hours = intval($perMin / 60);
                $hours = ($hours < 10 ? "0" . $hours : $hours);
                $minutes = $perMin - (intval($perMin / 60) * 60);
                $minutes = ($minutes < 10 ? "0" . $minutes : $minutes);
                $timerReport['week_data'][$week_key]['per_min'] = number_format(($perMin * 100) / 480, 2);
                $week[$week_key]['name'] = $hours . ':' . $minutes;
                $week[$week_key]['y'] = number_format(($perMin * 100) / 2400, 2);
                $week[$week_key]['color'] = '#' . $item['color_dec'];
                $new_week_minute = $new_week_minute + $perMin;
                $week_key++;
            }
        }
        $weekMinute = $weekMinute + $new_week_minute;
        $dataStore['week'] = $week;
        $dataStore['week_hours'] = intval($weekMinute / 60);
        $dataStore['week_minute'] = sprintf("%02d", $weekMinute - (intval($weekMinute / 60) * 60));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function get_timer_report() {
        $dataStore = $this->TimerModel->get_all_running_timer();
        foreach ($dataStore as $value) {
            $seconds = strtotime(convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC')) - strtotime(convert_timezone($this->session->get('login_userid'), date('Y-m-d') . ' 00:00:00', 'UTC'));
            $days = floor($seconds / 86400);
            $hours = floor(($seconds - ($days * 86400)) / 3600);
            $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
        }
    }

    public function get_timer() {
//        echo decrypt(base64_decode($this->request->getPost('timer_id')));
        if (is_numeric($this->request->getPost('timer_id'))) {
            $dataStore = $this->TimerModel->get_timer($this->request->getPost('timer_id'));
        } else {
            $dataStore = $this->TimerModel->get_timer(decrypt(base64_decode($this->request->getPost('timer_id'))));
        }
        if ($dataStore['item_id'] != 0) {
            $dataStore["item_name"] = $this->AdministrationModel->get_item(base64_encode(encrypt($dataStore['item_id'])))['name'];
        } else {
            $dataStore['item_id'] = '';
        }
        if ($dataStore['ct_id'] == 0) {
            $dataStore["chargetype"] = '';
        } else {
            $dataStore["chargetype"] = $this->ChargeModel->get_charge_type($dataStore['ct_id'])['name'];
        }
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['time_frame_id'] = $dataStore['timer_id'];
        $dataStore['timer_id'] = base64_encode(encrypt($dataStore['timer_id']));
//        $dataStore['timer_project_id'] = base64_encode(encrypt($dataStore['timer_project_id']));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_stop'], 'UTC');
        if (!empty($dataStore['smart_start_time']))
            $dataStore['smart_start_time'] = convert_timezone($this->session->get('login_userid'), $dataStore['smart_start_time'], 'UTC');
        else
            $dataStore['smart_start_time'] = '';
        if (!empty($dataStore['smart_end_time']))
            $dataStore['smart_end_time'] = convert_timezone($this->session->get('login_userid'), $dataStore['smart_end_time'], 'UTC');
        else
            $dataStore['smart_end_time'] = '';
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function get_timer_project() {
        $dataStore = $this->TimerModel->get_timer_project(decrypt(base64_decode($this->request->getPost('timer_id'))));
        if ($dataStore["item_id"] == 0) {
            $dataStore["item_name"] = '';
        } else {
            $dataStore["item_name"] = $this->AdministrationModel->get_item(base64_encode(encrypt($dataStore['item_id'])))['name'];
        }
        if ($dataStore['ct_id'] == 0) {
            $dataStore["chargetype"] = '';
        } else {
            $dataStore["chargetype"] = $this->ChargeModel->get_charge_type($dataStore['ct_id'])['name'];
        }
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['timer_project_id'] = base64_encode(encrypt($dataStore['timer_project_id']));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function add_timer_customer() {
        $dataIn = $this->request->getPost('form');
        $dataIn['user_id_created'] = $this->session->get('login_userid');
        $dataIn['user_id_updated'] = $this->session->get('login_userid');
        $dataIn['biller_id'] = $this->session->get('biller_id');
        $dataStore = $this->CustomerModel->add_edit_biller_customer($dataIn);
        if ($dataStore['status'] == "success") {
            $dataStore['name'] = $dataIn['name'];
            $dataStore['identifier'] = $dataIn['identifier'];
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function add_timer_item() {
        $dataIn = $this->request->getPost('form');
        $label = $dataIn['ct_id'];
        $dataIn['ct_id'] = $this->CustomerModel->get_charge_type($dataIn['ct_id']);
        $dataIn['user_id_created'] = $this->session->get('login_userid');
        $dataIn['user_id_updated'] = $this->session->get('login_userid');
        $dataStore = $this->AdministrationModel->add_edit_item($dataIn);
        if ($dataStore['status'] == 'success') {
            $dataStore['name'] = $dataIn['name'];
            $dataStore['optgroup'] = str_replace(' ', '_', strtolower($label));
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function start_timer() {
        $dataStore = $this->TimerModel->get_timer(decrypt(base64_decode($this->request->getPost('timer_id'))));
        $mins = $this->TimerModel->get_minute_diffrence($this->session->get('login_userid'), decrypt(base64_decode($this->request->getPost('customer_id'))))['dif_min'];
        if ($mins > 1) {
            $this->TimerModel->stop_timer(decrypt(base64_decode($this->request->getPost('timer_id'))));
            $timer_id = $this->TimerModel->add_timer(decrypt(base64_decode($this->request->getPost('customer_id'))))['timer_id'];
        } else {
            $timer_id = decrypt(base64_decode($this->request->getPost('timer_id')));
        }
        $dataStore = $this->TimerModel->get_timer($timer_id);
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['timer_id'] = base64_encode(encrypt($dataStore['timer_id']));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
//        $dataStore['timer_project_id'] = base64_encode(encrypt($dataStore['timer_project_id']));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_stop'], 'UTC');
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function stop_timer() {
        $running_status = $this->TimerModel->get_running_timer_status(decrypt(base64_decode($this->request->getPost('customer_id'))));
        if ($running_status) {
            $dataStore = $this->TimerModel->pause_timer(decrypt(base64_decode($this->request->getPost('timer_id'))));
            echo \GuzzleHttp\json_encode($dataStore);
        } else {
            echo \GuzzleHttp\json_encode(array('alert' => 1));
        }
    }

    public function stop_all_timer() {
        $dataStore = $this->TimerModel->stop_timer(decrypt(base64_decode($this->request->getPost('timer_id'))));
        $dataStore['user'] = $this->TimerModel->get_timer(decrypt(base64_decode($this->request->getPost('timer_id'))));
        sendTimerEvent($dataStore, $dataStore['user']['name'] . ' Has been stop timer.', 'Stop-Timer', 'timer-' . $this->session->get('login_userid'));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function stop_timer_project() {
        if (is_numeric($this->request->getPost('timer_project_id'))) {
            $dataStore = $this->TimerModel->stop_timer_project($this->request->getPost('timer_project_id'));
        } else {
            $dataStore = $this->TimerModel->stop_timer_project(decrypt(base64_decode($this->request->getPost('timer_project_id'))));
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function update_time() {
        $diff = $this->request->getPost('diff');
        $dataStore = $this->TimerModel->get_timer(decrypt(base64_decode($this->request->getPost('timer_id'))));
        $diff_time = explode('GMT', $diff);
        if ($this->request->getPost('select_type') == 'start') {
            $dataIn['time_start'] = date('Y-m-d H:i:s', strtotime(rtrim($diff_time[0])));
            $dataIn['time_start'] = datetimeconv_utc($dataIn['time_start'], $this->session->get('login_userid'));
        } else {
            $dataIn['time_stop'] = date('Y-m-d H:i:s', strtotime(rtrim($diff_time[0])));
            $dataIn['time_stop'] = datetimeconv_utc($dataIn['time_stop'], $this->session->get('login_userid'));
        }
        if (strtotime($dataStore['time_start']) > strtotime($dataStore['time_stop'])) {
            $dataIn['time_stop'] = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($dataIn['time_stop'])));
        }
        $dataStore = $this->TimerModel->update_timer($dataIn, decrypt(base64_decode($this->request->getPost('timer_id'))));
        $dataStore = $this->TimerModel->get_timer(decrypt(base64_decode($this->request->getPost('timer_id'))));
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['timer_id'] = base64_encode(encrypt($dataStore['timer_id']));
        $dataStore['timer_project_id'] = base64_encode(encrypt($dataStore['timer_project_id']));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_stop'], 'UTC');
        sendTimerEvent($dataStore, $dataStore['name'] . ' Has been update timer time.', 'Edit-Timer', 'timer-' . $this->session->get('login_userid'));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function update_new_time() {
        $dataIn['timer_id'] = decrypt(base64_decode($this->request->getPost('timer_id')));
        $dataIn['time_start'] = date('Y-m-d H:i:s', strtotime($this->request->getPost('time_start')));
        $dataIn['time_stop'] = date('Y-m-d H:i:s', strtotime($this->request->getPost('time_stop')));
        $dataIn['time_start'] = datetimeconv_utc($dataIn['time_start'], $this->session->get('login_userid'));
        $dataIn['time_stop'] = datetimeconv_utc($dataIn['time_stop'], $this->session->get('login_userid'));
        if (strtotime($dataIn['time_start']) > strtotime($dataIn['time_stop'])) {
            $dataIn['time_stop'] = date('Y-m-d H:i:s', strtotime('+1 minutes', strtotime($dataIn['time_start'])));
        }
        $dataStore = $this->TimerModel->update_timer($dataIn, $dataIn['timer_id']);
        $dataStore = $this->TimerModel->get_timer(decrypt(base64_decode($this->request->getPost('timer_id'))));
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['timer_id'] = base64_encode(encrypt($dataStore['timer_id']));
//        $dataStore['timer_project_id'] = base64_encode(encrypt($dataStore['timer_project_id']));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_stop'], 'UTC');
        sendTimerEvent($dataStore, $dataStore['name'] . ' Has been update timer time.', 'Edit-Timer', 'timer-' . $this->session->get('login_userid'));
        if ($this->request->getPost('stop') == 1) {
            $dataStore = $this->TimerModel->stop_timer(decrypt(base64_decode($this->request->getPost('timer_id'))), $this->request->getPost('stop'));
            $dataStore['stop'] = $this->request->getPost('stop');
            echo \GuzzleHttp\json_encode($dataStore);
        } else {
            echo \GuzzleHttp\json_encode($dataStore);
        }
    }

    public function edit_timer() {
        $dataIn = $this->request->getPost('form');
        $dataStore = $this->TimerModel->get_timer(decrypt(base64_decode($dataIn['timer_id'])));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_stop'], 'UTC');
        if (date('Y-m-d', strtotime($dataIn['time_start'])) != date('Y-m-d', strtotime($dataStore['time_start']))) {
            $dataStore['time_start'] = date('Y-m-d H:i:s', strtotime("-" . date_diff(date_create(date('Y-m-d H:i:s', strtotime($dataIn['time_start']))), date_create(explode(" ", $dataStore['time_start'])[0]))->format("%R%a") . " day", strtotime($dataStore['time_start'])));
        }
        $dataIn['time_start'] = date('Y-m-d', strtotime($dataIn['time_start'])) . ' ' . date('H:i:s', strtotime($dataIn['start_time']));
        $dataIn['time_start'] = datetimeconv_utc($dataIn['time_start'], $this->session->get('login_userid'));
        $dataIn['time_stop'] = date('Y-m-d H:i:s', strtotime($dataIn['new_time_stop']));
        $mins = (strtotime($dataIn['time_stop']) - strtotime($dataStore['time_stop'])) / 60;
        if ($mins > 1) {
            $stop = 1;
        } else {
            $stop = 0;
        }
        $dataIn['time_stop'] = datetimeconv_utc($dataIn['time_stop'], $this->session->get('login_userid'));
        $dataIn['new_timerproject_id'] = decrypt(base64_decode($dataIn['new_timer_id']));
        $dataIn['timer_id'] = decrypt(base64_decode($dataIn['timer_id']));
        $dataIn['customer_id'] = decrypt(base64_decode($dataIn['customer_id']));
        if (strtotime($dataIn['time_start']) > strtotime($dataIn['time_stop'])) {
            $dataIn['time_stop'] = date('Y-m-d H:i:s', strtotime('+1 minutes', strtotime($dataIn['time_start'])));
        }
        $timer_id = $this->TimerModel->update_timerproject($dataIn);
        $dataStore = $this->TimerModel->get_timer($dataIn['timer_id']);
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['timer_id'] = base64_encode(encrypt($dataStore['timer_id']));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_stop'], 'UTC');
        sendTimerEvent($dataStore, $dataStore['name'] . ' Has been update timer time.', 'Edit-Timer', 'timer-' . $this->session->get('login_userid'));
        if ($stop == 1) {
            $dataStore = $this->TimerModel->stop_timer($dataIn['timer_id'], $stop);
            $dataStore['stop'] = $stop;
            echo \GuzzleHttp\json_encode($dataStore);
        } else {
            echo \GuzzleHttp\json_encode($dataStore);
        }
    }

    public function calender_edit_timer() {
        $dataIn = $this->request->getPost('form');
        $dataStore = $this->TimerModel->get_timer(decrypt(base64_decode($dataIn['timer_id'])));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_stop'], 'UTC');
        if (date('Y-m-d', strtotime($dataIn['time_start'])) != date('Y-m-d', strtotime($dataStore['time_start']))) {
            $dataStore['time_start'] = date('Y-m-d H:i:s', strtotime("-" . date_diff(date_create(date('Y-m-d H:i:s', strtotime($dataIn['time_start']))), date_create(explode(" ", $dataStore['time_start'])[0]))->format("%R%a") . " day", strtotime($dataStore['time_start'])));
        }
        $dataIn['time_start'] = date('Y-m-d', strtotime($dataIn['time_start'])) . ' ' . date('H:i:s', strtotime($dataIn['start_time']));
        $dataIn['time_start'] = datetimeconv_utc($dataIn['time_start'], $this->session->get('login_userid'));
        $dataIn['time_stop'] = date('Y-m-d H:i:s', strtotime($dataIn['stop_time_stop']));
        $dataIn['time_stop'] = datetimeconv_utc($dataIn['time_stop'], $this->session->get('login_userid'));
        $dataIn['new_timerproject_id'] = decrypt(base64_decode($dataIn['new_timer_id']));
        $dataIn['timer_id'] = decrypt(base64_decode($dataIn['timer_id']));
        $dataIn['customer_id'] = decrypt(base64_decode($dataIn['customer_id']));
        if (strtotime($dataIn['time_start']) > strtotime($dataIn['time_stop'])) {
            $dataIn['time_stop'] = date('Y-m-d H:i:s', strtotime('+1 minutes', strtotime($dataIn['time_start'])));
        }
        $timer_id = $this->TimerModel->update_timerproject($dataIn);
        $dataStore = $this->TimerModel->get_timer($dataIn['timer_id']);
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['timer_id'] = base64_encode(encrypt($dataStore['timer_id']));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_stop'], 'UTC');
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function delete_time() {
        $dataStore = $this->TimerModel->delete_timer(decrypt(base64_decode($this->request->getPost('timer_id'))));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function delete_timer_project() {
        $dataStore = $this->TimerModel->delete_timer_project(decrypt(base64_decode($this->request->getPost('timer_project_id'))));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function pin_timer_edit() {
        $dataIn = $this->request->getPost('form');
        $dataIn['timer_project_id'] = decrypt(base64_decode($dataIn['timer_project_id']));
        $dataIn['customer_id'] = decrypt(base64_decode($dataIn['customer_id']));
        $this->TimerModel->edit_pin_timer($dataIn);
        $dataStore = $this->TimerModel->get_timer_project($dataIn['timer_project_id']);
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));

        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function check_network() {
        echo \GuzzleHttp\json_encode(array('status' => 'success'));
    }

    public function update_running_timer() {
        $dataStore = $this->TimerModel->set_graph();
        $running_timer = $this->TimerModel->get_all_running_timer();
        $customer_ids = $customer_name = $timer_project_id = array();
        if (count($running_timer) != 0) {
            foreach ($running_timer as $value) {
                $customer_ids[] = $value['customer_id'];
                $customer_name[] = $value['name'];
                $timer_project_id[] = $value['timer_project_id'];
                if (strtotime($value['time_start']) > strtotime($value['time_stop'])) {
                    $dataIn['time_stop'] = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($value['time_stop'])));
                    $this->TimerModel->update_timer($dataIn, $value['timer_id']);
                }
            }
        }
        $dataStore['customer_ids'] = $customer_ids;
        $dataStore['customer_name'] = $customer_name;
        $dataStore['timer_project_id'] = $timer_project_id;
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function day_timer() {
        if ($this->request->getPost('status') == "past") {
            $date = date('Y-m-d', strtotime("-1 day", strtotime($this->request->getPost('date'))));
        } else if ($this->request->getPost('status') == "future") {
            $date = date('Y-m-d', strtotime("+1 day", strtotime($this->request->getPost('date'))));
        } else {
            $date = date('Y-m-d', strtotime($this->request->getPost('date')));
        }
        $dataStore['today_hours'] = $this->TimerModel->get_today_hours($date);
        foreach ($dataStore['today_hours'] as $key => $value) {
            if ($value['dif_min'] == 0) {
                unset($dataStore['today_hours'][$key]);
                continue;
            }
            if (strlen(dechex($value['color_dec'])) < 6) {
                $dif = 6 - intval(strlen(dechex($value['color_dec'])));
                $add = '';
                for ($i = 0; $i < $dif; $i++) {
                    $add.='0';
                }
                $dataStore['today_hours'][$key]['color_dec'] = '#' . $add . dechex($value['color_dec']);
            } else {
                $dataStore['today_hours'][$key]['color_dec'] = '#' . dechex($value['color_dec']);
            }
            $dataStore['today_hours'][$key]['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['today_hours'][$key]['time_start'], 'UTC');
            $dataStore['today_hours'][$key]['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['today_hours'][$key]['time_stop'], 'UTC');
            $dataStore['today_hours'][$key]['timer_id'] = base64_encode(encrypt($value['timer_id']));
            if (date('Y-m-d', strtotime($dataStore['today_hours'][$key]['time_start'])) != date('Y-m-d', strtotime($dataStore['today_hours'][$key]['time_stop']))) {
                $dataStore['today_hours'][$key]['show_sign'] = 'TRUE';
            } else {
                $dataStore['today_hours'][$key]['show_sign'] = 'FALSE';
            }
            if (date('Y-m-d', strtotime($dataStore['today_hours'][$key]['time_start'])) != date('Y-m-d', strtotime($dataStore['today_hours'][$key]['time_stop']))) {
                $dataStore['today_hours'][$key]['time_stop'] = date('Y-m-d', strtotime($dataStore['today_hours'][$key]['time_start'])) . ' 23:59:59';
                $value['dif_min'] = round((strtotime($dataStore['today_hours'][$key]['time_stop']) - strtotime($dataStore['today_hours'][$key]['time_start'])) / 60);
            }
            $dataStore['today_hours'][$key]['start'] = date('g:i A', strtotime($dataStore['today_hours'][$key]['time_start']));
            $dataStore['today_hours'][$key]['end'] = date('g:i A', strtotime($dataStore['today_hours'][$key]['time_stop']));
            $dataStore['today_hours'][$key]['dif_min_total'] = $value['dif_min'];
        }
        $dataStore2 = $this->TimerModel->get_today_stoptimer($date);
        if (isset($key)) {
            $new_key = $key + 1;
        } else {
            $new_key = 0;
        }
        $new_s_min = 0;
        foreach ($dataStore2 as $value) {
            if (date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC'))) != date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_start'], 'UTC')))) {
                if (date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_start'], 'UTC'))) != date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), date('Y-m-d H:i:s'), 'UTC')))) {
                    if (strlen(dechex($value['color_dec'])) < 6) {
                        $dif = 6 - intval(strlen(dechex($value['color_dec'])));
                        $add = '';
                        for ($i = 0; $i < $dif; $i++) {
                            $add.='0';
                        }
                        $dataStore['today_hours'][$new_key]['color_dec'] = '#' . $add . dechex($value['color_dec']);
                    } else {
                        $dataStore['today_hours'][$new_key]['color_dec'] = '#' . dechex($value['color_dec']);
                    }

                    $dataStore['today_hours'][$new_key]['time_start'] = date('Y-m-d', strtotime($value['time_stop'])) . ' 00:00:00';
                    $dataStore['today_hours'][$new_key]['time_stop'] = convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC');
                    $dataStore['today_hours'][$new_key]['timer_id'] = base64_encode(encrypt($value['timer_id']));
                    $dataStore['today_hours'][$new_key]['start'] = date('g:i A', strtotime($dataStore['today_hours'][$new_key]['time_start']));
                    $dataStore['today_hours'][$new_key]['end'] = date('g:i A', strtotime($dataStore['today_hours'][$new_key]['time_stop']));
                    $dataStore['today_hours'][$new_key]['identifier'] = $value['identifier'];
                    $dataStore['today_hours'][$new_key]['name'] = $value['name'];
                    $dataStore['today_hours'][$new_key]['po'] = $value['po'];
                    $dataStore['today_hours'][$new_key]['timer_project_id'] = $value['timer_project_id'];
                    $start = strtotime(convert_timezone($this->session->get('login_userid'), $value['time_start'], 'UTC'));
                    $end = strtotime(convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC'));
                    $mins = ($end - $start) / 60;
                    $dataStore['today_hours'][$new_key]['dif_min'] = floor($mins);
                    $startTotal = strtotime(date('Y-m-d', strtotime($value['time_stop'])) . ' 00:00:00');
                    $endTotal = strtotime(convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC'));
                    $minsTotal = ($endTotal - $startTotal) / 60;
                    $dataStore['today_hours'][$new_key]['dif_min_total'] = floor($minsTotal);
                    $dataStore['today_hours'][$new_key]['show_sign'] = 'TRUE';
                    $new_key ++;
                }
            }
        }
        $startArray = array();
        foreach ($dataStore['today_hours'] as $key => $row) {
            $startArray[$key] = $row['time_start'];
        }
        array_multisort($startArray, SORT_ASC, $dataStore['today_hours']);
        $dataStore['date'] = date('D, F j', strtotime($date));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function start_running_timer() {
        $dataStore = $this->TimerModel->add_timer($this->request->getPost('customer_id'), $this->request->getPost('timer_project_id'));
        $dataStore = $this->TimerModel->get_timer($dataStore['timer_id']);
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['timer_id'] = base64_encode(encrypt($dataStore['timer_id']));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore['timer_project_id'] = base64_encode(encrypt($dataStore['timer_project_id']));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_stop'], 'UTC');
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function get_running_timer() {
        $dataStore = $this->TimerModel->get_all_running_timer();
        foreach ($dataStore as $key => $value) {
            $dataStore[$key]['timer_id'] = base64_encode(encrypt($value['timer_id']));
            $dataStore[$key]['customer_id'] = base64_encode(encrypt($value['customer_id']));
            $dataStore[$key]['color_dec'] = dechex($value['color_dec']);
            if (strlen($dataStore[$key]['color_dec']) < 6) {
                $dif = 6 - intval(strlen(dechex($value['color_dec'])));
                $add = '';
                for ($i = 0; $i < $dif; $i++) {
                    $add.='0';
                }
                $dataStore[$key]['color_dec'] = $add . dechex($value['color_dec']);
            }
            $dataStore[$key]['time_start'] = convert_timezone($this->session->get('login_userid'), $value['time_start'], 'UTC');
            $dataStore[$key]['time_stop'] = convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC');
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function projectlist() {
        $dataStore['timer_data'] = $this->TimerModel->get_timer_project();
        $dataStore["customer_data"] = $this->CustomerModel->get_customer();
        $count = 0;
        foreach ($dataStore["customer_data"] as $customer_value) {
            $dataStore["customer_data"][$count]["customer_id"] = base64_encode(encrypt($customer_value["customer_id"]));
            $count++;
        }
        $dataStore["chargetype_data"] = $this->ChargeModel->get_charge_type();
        $dataStore["item_data"] = $this->AdministrationModel->get_item();
        $count = 0;
        foreach ($dataStore["item_data"] as $customer_value) {
            $dataStore["item_data"][$count]["type"] = $this->CustomerModel->get_charge_type($dataStore["item_data"][$count]["type"]);
            $count++;
        }
        $running_timer = $this->TimerModel->get_all_running_timer();
        $running_id = '';
        foreach ($running_timer as $value) {
            if ($value['locked'] == 0) {
                $running_id = $value['timer_project_id'];
            }
        }
        $dataStore['running_id'] = $running_id;
        echo view('Views/aut/project_list_view', $dataStore);
    }

    public function project_edit() {
        $dataIn[$this->request->getPost('key')] = $this->request->getPost('value');
        if (isset($dataIn['color_dec']) && !empty($dataIn['color_dec'])) {
            $dataIn['color_dec'] = hexdec($dataIn['color_dec']);
        }
        $dataIn['timer_project_id'] = decrypt(base64_decode($this->request->getPost('timer_project_id')));
        $dataStore = $this->TimerModel->edit_project($dataIn);
        $dataStore = $this->TimerModel->get_timer_project(decrypt(base64_decode($this->request->getPost('timer_project_id'))));
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function add_timer_project() {
        $dataIn = $this->request->getPost('form');
        $dataIn['customer_id'] = decrypt(base64_decode($dataIn['customer_id']));
        $dataIn['user_id'] = $this->session->get("login_userid");
        $dataIn['color_dec'] = hexdec(random_color());
        $dataIn['sticky_order'] = 0;
        $dataIn['locked'] = 0;
        $dataStore = $this->TimerModel->add_timer_project($dataIn);
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function get_today_timer() {
        if ($this->request->getPost('status') == "past") {
            $date = date('Y-m-d', strtotime("-1 day", strtotime($this->request->getPost('date'))));
        } else if ($this->request->getPost('status') == "future") {
            $date = date('Y-m-d', strtotime("+1 day", strtotime($this->request->getPost('date'))));
        } else {
            $date = date('Y-m-d', strtotime($this->request->getPost('date')));
        }
        $dataStore = $this->TimerModel->get_today_timer($date);
        foreach ($dataStore as $key => $value) {
            $dataStore[$key]['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore[$key]['time_start'], 'UTC');
            $dataStore[$key]['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore[$key]['time_stop'], 'UTC');
            $color_dec = dechex($value['color_dec']);
            if (strlen($color_dec) < 6) {
                $dif = 6 - intval(strlen($color_dec));
                $add = '';
                for ($i = 0; $i < $dif; $i++) {
                    $add.='0';
                }
                $color_dec = $add . $color_dec;
            }
            $dataStore[$key]['color_dec'] = $color_dec;
        }
        if (date('Y-m-d') == $date) {
            $dataStore2 = $this->TimerModel->get_today_stoptimer();
            if (isset($key)) {
                $new_key = $key + 1;
            } else {
                $new_key = 0;
            }
            $new_s_min = 0;
            foreach ($dataStore2 as $value) {
                if (date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), date('Y-m-d H:i:s'), 'UTC'))) == date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC'))) && date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), date('Y-m-d H:i:s'), 'UTC'))) != date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_start'], 'UTC')))) {
                    if (date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), $value['time_start'], 'UTC'))) != date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), date('Y-m-d H:i:s'), 'UTC')))) {
                        if (strlen(dechex($value['color_dec'])) < 6) {
                            $dif = 6 - intval(strlen(dechex($value['color_dec'])));
                            $add = '';
                            for ($i = 0; $i < $dif; $i++) {
                                $add.='0';
                            }
                            $dataStore[$new_key]['color_dec'] = $add . dechex($value['color_dec']);
                        } else {
                            $dataStore[$new_key]['color_dec'] = dechex($value['color_dec']);
                        }
                        $dataStore[$new_key]['time_start'] = convert_timezone($this->session->get('login_userid'), $value['time_start'], 'UTC');
                        $dataStore[$new_key]['time_stop'] = convert_timezone($this->session->get('login_userid'), $value['time_stop'], 'UTC');
                        $dataStore[$new_key]['timer_id'] = $value['timer_id'];
                        $new_key ++;
                    }
                }
            }
        }
        if ($this->request->getPost('sort') == 1) {
            $startArray = array();
            foreach ($dataStore as $key => $row) {
                $startArray[$key] = $row['time_start'];
            }
            array_multisort($startArray, SORT_ASC, $dataStore);
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function show_smart_running_timer() {
        $dataStore = $this->TimerModel->get_timer(decrypt(base64_decode($this->request->getPost('timer_id'))));
        if ($dataStore["item_id"] == 0) {
            $dataStore["item_name"] = '';
        } else {
            $dataStore["item_name"] = $this->AdministrationModel->get_item(base64_encode(encrypt($dataStore['item_id'])))['name'];
        }
        if ($dataStore['ct_id'] == 0) {
            $dataStore["chargetype"] = '';
        } else {
            $dataStore["chargetype"] = $this->ChargeModel->get_charge_type($dataStore['ct_id'])['name'];
        }
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['time_frame_id'] = $dataStore['timer_id'];
        $dataStore['timer_id'] = base64_encode(encrypt($dataStore['timer_id']));
        $dataStore['timer_project_id'] = base64_encode(encrypt($dataStore['timer_project_id']));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), gmdate("Y-m-d H:i:s"), 'UTC');
        if (!empty($dataStore['smart_start_time']))
            $dataStore['smart_start_time'] = convert_timezone($this->session->get('login_userid'), $dataStore['smart_start_time'], 'UTC');
        else
            $dataStore['smart_start_time'] = '';
        if (!empty($dataStore['smart_end_time']))
            $dataStore['smart_end_time'] = convert_timezone($this->session->get('login_userid'), $dataStore['smart_end_time'], 'UTC');
        else
            $dataStore['smart_end_time'] = '';
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function smart_ruuning_timer() {
        $dataIn['running'] = 1;
        $dataIn['time_start'] = datetimeconv_utc(date('Y-m-d H:i:s', strtotime($this->request->getPost('time_start'))), $this->session->get('login_userid'));
        $dataIn['time_stop'] = datetimeconv_utc(date('Y-m-d H:i:s', strtotime($this->request->getPost('time_stop'))), $this->session->get('login_userid'));
        $dataStore = $this->TimerModel->update_timer($dataIn, decrypt(base64_decode($this->request->getPost('timer_id'))));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function get_smart_timer() {
        $dataIn['time_start'] = date('Y-m-d H:i:s', strtotime($this->request->getPost('time_start')));
        $dataIn['time_stop'] = date('Y-m-d H:i:s', strtotime($this->request->getPost('time_stop')));
        $dataStore = $this->TimerModel->get_smart_timer($dataIn);
        if (!empty($dataStore['smart_start_time']))
            $dataStore['smart_start_time'] = convert_timezone($this->session->get('login_userid'), $dataStore['smart_start_time'], 'UTC');
        else
            $dataStore['smart_start_time'] = '';
        if (!empty($dataStore['smart_end_time']))
            $dataStore['smart_end_time'] = convert_timezone($this->session->get('login_userid'), $dataStore['smart_end_time'], 'UTC');
        else
            $dataStore['smart_end_time'] = '';
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function export_timer_data() {
        $dataStore = $this->TimerModel->get_export_timer($_REQUEST['count'], strtolower($_REQUEST['type']), 'start');
        $header = array('Duration', 'Date', 'Time', 'Description', 'Customer', 'Acct', 'Item', 'Type');
        $csv_array = array();
        foreach ($dataStore as $key => $value) {
            if (date('Y-m-d', strtotime($value['time_stop'])) != date('Y-m-d', strtotime($value['time_start']))) {
                $value['time_stop'] = date('Y-m-d', strtotime($value['time_start'])) . ' 23:59:59';
            }
            $seconds = strtotime($value['time_stop']) - strtotime($value['time_start']);
            if ($seconds > 60) {
                $csv_array[$key]['duration'] = round(($seconds / 60 / 60), 2);
                $csv_array[$key]['date'] = date('m/d/Y', strtotime($value['time_start']));
                $csv_array[$key]['time'] = date('h:i a', strtotime($value['time_start']));
                $datetime = $csv_array[$key]['date'] . $csv_array[$key]['time'];
                $csv_array[$key]['sort_date'] = date('Y-m-d H:i:s', strtotime($datetime));
                $csv_array[$key]['description'] = $value['description'];
                $csv_array[$key]['customer_name'] = $value['name'];
                $csv_array[$key]['acct'] = $value['identifier'];
                if ($value['item_id'] != '0') {
                    $csv_array[$key]["item"] = $this->AdministrationModel->get_item(base64_encode(encrypt($value['item_id'])))['name'];
                } else {
                    $csv_array[$key]["item"] = '';
                }
                $csv_array[$key]["type"] = $this->ChargeModel->get_charge_type($value['ct_id'])['name'];
            }
        }
        $old_key = $key + 1;
        foreach ($dataStore as $key => $value) {
            if (date('Y-m-d', strtotime($value['time_stop'])) != date('Y-m-d', strtotime($value['time_start']))) {
                $value['time_start'] = date('Y-m-d', strtotime($value['time_stop'])) . ' 00:00:00';
                if (date('Y-m-d', strtotime($value['time_start'])) == date('Y-m-d', strtotime('+1 day', strtotime(get_cookie('end_date'))))) {
                    continue;
                }
                $seconds1 = strtotime($value['time_stop']) - strtotime($value['time_start']);
                $csv_array[$old_key + $key]['duration'] = round(($seconds1 / 60 / 60), 2);
                $csv_array[$old_key + $key]['date'] = date('m/d/Y', strtotime($value['time_start']));
                $csv_array[$old_key + $key]['time'] = date('h:i a', strtotime($value['time_start']));
                $datetime = $csv_array[$old_key + $key]['date'] . $csv_array[$old_key + $key]['time'];
                $csv_array[$old_key + $key]['sort_date'] = date('Y-m-d H:i:s', strtotime($datetime));
                $csv_array[$old_key + $key]['description'] = $value['description'];
                $csv_array[$old_key + $key]['customer_name'] = $value['name'];
                $csv_array[$old_key + $key]['acct'] = $value['identifier'];
                if ($value['item_id'] != '0') {
                    $csv_array[$old_key + $key]["item"] = $this->AdministrationModel->get_item(base64_encode(encrypt($value['item_id'])))['name'];
                } else {
                    $csv_array[$old_key + $key]["item"] = '';
                }
                $csv_array[$old_key + $key]["type"] = $this->ChargeModel->get_charge_type($value['ct_id'])['name'];
            }
        }
        $csv_array = array_values($csv_array);
        $array_count = count($csv_array);
        $dataStore = $this->TimerModel->get_export_timer($_REQUEST['count'], strtolower($_REQUEST['type']), 'stop');
        $istop = 1;
        $csv_array1 = array();
        foreach ($dataStore as $value) {
            if (date('Y-m-d', strtotime(get_cookie('start_date'))) == date('Y-m-d', strtotime($value['time_stop']))) {
                if (date('Y-m-d', strtotime($value['time_start'])) != date('Y-m-d', strtotime($value['time_stop']))) {
                    $value['time_start'] = date('Y-m-d', strtotime($value['time_stop'])) . ' 00:00:00';
                    if (date('Y-m-d', strtotime($value['time_start'])) == date('Y-m-d', strtotime('+1 day', strtotime(get_cookie('end_date'))))) {
                        continue;
                    }
                    $seconds1 = strtotime($value['time_stop']) - strtotime($value['time_start']);
                    $csv_array1[$array_count + $istop]['duration'] = round(($seconds1 / 60 / 60), 2);
                    $csv_array1[$array_count + $istop]['date'] = date('m/d/Y', strtotime($value['time_start']));
                    $csv_array1[$array_count + $istop]['time'] = date('h:i a', strtotime($value['time_start']));
                    $datetime = $csv_array1[$array_count + $istop]['date'] . $csv_array1[$array_count + $istop]['time'];
                    $csv_array1[$array_count + $istop]['sort_date'] = date('Y-m-d H:i:s', strtotime($datetime));
                    $csv_array1[$array_count + $istop]['description'] = $value['description'];
                    $csv_array1[$array_count + $istop]['customer_name'] = $value['name'];
                    $csv_array1[$array_count + $istop]['acct'] = $value['identifier'];
                    if ($value['item_id'] != '0') {
                        $csv_array1[$array_count + $istop]["item"] = $this->AdministrationModel->get_item(base64_encode(encrypt($value['item_id'])))['name'];
                    } else {
                        $csv_array1[$array_count + $istop]["item"] = '';
                    }
                    $csv_array1[$array_count + $istop]["type"] = $this->ChargeModel->get_charge_type($value['ct_id'])['name'];
                    $istop ++;
                }
            }
        }
        $csv_array = array_merge($csv_array, $csv_array1);
        $csv_array = array_values($csv_array);
        $startArray = array();
        foreach ($csv_array as $key => $row) {
            $startArray[$key] = $row['sort_date'];
        }
        array_multisort($startArray, SORT_ASC, $csv_array);
        delete_col($csv_array, 'sort_date');
        exportCSV(ucfirst('timer_' . date('Y_m_d_H_i_s')), $csv_array, $header);
    }

    public function add_edit_description() {
        $dataIn = $this->request->getPost('form');
        $dataIn['timer_id'] = decrypt(base64_decode($dataIn['timer_id']));
        $dataStore = $this->TimerModel->update_timer($dataIn, $dataIn['timer_id']);
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function start_quick_timer() {
        $timer_id = $this->TimerModel->start_quick_timer();
        $dataStore = $this->TimerModel->get_timer($timer_id);
        $dataStore['color_dec'] = dechex($dataStore['color_dec']);
        if (strlen($dataStore['color_dec']) < 6) {
            $dif = 6 - intval(strlen($dataStore['color_dec']));
            $add = '';
            for ($i = 0; $i < $dif; $i++) {
                $add.='0';
            }
            $dataStore['color_dec'] = $add . $dataStore['color_dec'];
        }
        $dataStore['timer_id'] = base64_encode(encrypt($dataStore['timer_id']));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore['time_start'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_start'], 'UTC');
        $dataStore['time_stop'] = convert_timezone($this->session->get('login_userid'), $dataStore['time_stop'], 'UTC');
        sendTimerEvent($dataStore, $dataStore['name'] . ' Has been start timer.', 'Start-Timer', 'timer-' . $this->session->get('login_userid'));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function get_all_projects() {
        $dataProjectStore = array();
        $dataStore = $this->TimerModel->get_timer_project();
        foreach ($dataStore as $key => $value) {
            if ($value['name'] == 'Internal Tracking' && $value["identifier"] == 'internal' && $value['item_id'] == 0 && $value['ct_id'] == 5) {
                unset($dataStore[$key]);
                continue;
            }
            $dataProjectStore[$key]['customer_id'] = base64_encode(encrypt($value['customer_id']));
            $dataProjectStore[$key]['project_id'] = $value['timer_project_id'];
            $dataProjectStore[$key]['name'] = $value['name'];
            $dataProjectStore[$key]['timer_project_id'] = base64_encode(encrypt($value['timer_project_id']));
            $ide = $sep = $po = '';
            $ide = ($value["identifier"] == "" ? "" : $value["identifier"]);
            $sep = (($value["identifier"] == '' && $value["po"] == '') || ($value["identifier"] == '' || $value["po"] == '') ? "" : "/");
            $po = ($value["po"] == "" ? "" : $value["po"]);
            $dataProjectStore[$key]['identifier_po'] = $ide . $sep . $po;
            $dataStore[$key]['color_dec'] = dechex($value['color_dec']);
            if (strlen($dataStore[$key]['color_dec']) < 6) {
                $dif = 6 - intval(strlen($dataStore[$key]['color_dec']));
                $add = '';
                for ($i = 0; $i < $dif; $i++) {
                    $add.='0';
                }
                $dataProjectStore[$key]['color_dec'] = $add . $dataStore[$key]['color_dec'];
            } else {
                $dataProjectStore[$key]['color_dec'] = $dataStore[$key]['color_dec'];
            }
            if ($value['item_id'] == 0) {
                if ($value['ct_id'] == 0) {
                    $dataProjectStore[$key]['item'] = '';
                } else {
                    $dataProjectStore[$key]['item'] = dbQueryRows('charge_type', array('ct_id' => $value['ct_id']))[0]['name'];
                }
            }
            if ($value['item_id'] > 0) {
                $dataProjectStore[$key]['item'] = dbQueryRows('item', array('item_id' => $value['item_id']))[0]['name'];
            }
        }
        $dataProjectStore = array_values($dataProjectStore);
        echo \GuzzleHttp\json_encode($dataProjectStore);
    }

}
