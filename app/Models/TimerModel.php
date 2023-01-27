<?php

namespace App\Models;

use CodeIgniter\Model;

class TimerModel extends Model {

    var $tblTimer;
    var $tblTimerProject;
    var $session;
    protected $helpers = ["general"];

    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
        $this->tblTimer = $db->table('timer');
        $this->tblTimerProject = $db->table('timer_project');
        $this->tblCustomer = $db->table('customer');
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    /**
     * Add timer as per customer selected
     * 
     * @param int $customer_id,$timer_project_id
     * @return array
     * */
    public function add_timer($customer_id, $timer_project_id = '') {
        $timerData = "SELECT t.timer_id, tp.* 
                      FROM timer as t 
                      RIGHT JOIN timer_project as tp on t.timer_project_id=tp.timer_project_id 
                      WHERE tp.user_id = :user_id: 
                      AND tp.timer_project_id=:timer_project_id:";
        $par = ['timer_project_id' => $timer_project_id, 'user_id' => $this->session->get('login_userid')];
        $data = $this->db->query($timerData, $par)->getRowArray();
        $sql = "SELECT * FROM `timer` 
                INNER JOIN timer_project USING(timer_project_id) 
                WHERE timer_project.timer_project_id=:timer_project_id: AND running = 1 AND timer_project.user_id = :user_id:";
        $running_timer = $this->db->query($sql, $par)->getRowArray();
        if (empty($running_timer)) {
            if ($data['locked'] != '1') {
                $dataTimerStore = $this->tblTimerProject
                        ->select('timer_project.*')
                        ->where(array('timer_project.user_id' => $this->session->get('login_userid'), 'timer_project.timer_project_id' => $timer_project_id))
                        ->get()
                        ->getRowArray();
                $usql = 'UPDATE timer 
                    INNER JOIN timer_project USING(timer_project_id)
                    SET running = 0
                    WHERE user_id = :user_id: AND timer_project_id = :timer_project_id: AND running = 1 AND locked = 0';
                $this->db->query($usql, $par);
                $par = ['customer_id' => $customer_id, 'user_id' => $this->session->get('login_userid')];
                $dataTimerIn['timer_project_id'] = $dataTimerStore['timer_project_id'];
                $dataTimerIn['running'] = 1;
                $this->tblTimer->insert($dataTimerIn);
                if ($this->db->insertID()) {
                    $dataStore["timer_id"] = $this->db->insertID();
                    $dataStore["status"] = "success";
                } else {
                    $dataStore["status"] = "fail";
                }
            } else {
                $sql = 'UPDATE timer 
                    INNER JOIN timer_project USING(timer_project_id)
                    SET time_stop = CURRENT_TIMESTAMP()
                    WHERE user_id = :user_id: AND customer_id = :customer_id: AND running=1 AND time_stop < CURRENT_TIMESTAMP';
                $par = ['customer_id' => $customer_id, 'user_id' => $this->session->get('login_userid')];
                $this->db->query($sql, $par);
                $dataStore["timer_id"] = $data['timer_id']; // make timer id dynamic
                $dataStore["status"] = "success";
            }
        } else {
            $sql = 'UPDATE timer 
                    INNER JOIN timer_project USING(timer_project_id)
                    SET time_stop = CURRENT_TIMESTAMP()
                    WHERE user_id = :user_id: AND customer_id = :customer_id: AND timer_id= :timer_id: AND time_stop < CURRENT_TIMESTAMP';
            $par = ['customer_id' => $customer_id, 'user_id' => $this->session->get('login_userid'), 'timer_id' => $running_timer['timer_id']];
            $this->db->query($sql, $par);
            $dataStore["timer_id"] = $running_timer['timer_id']; // make timer id dynamic
            $dataStore["status"] = "success";
        }
        return $dataStore;
    }

    /**
     * Add timer from day and calender view
     * 
     * @param array $dataIn
     * @return array
     * */
    public function create_new_timer($dataIn) {
        $this->tblTimer->insert($dataIn);
        if ($this->db->insertID()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Add pin project timer as per timer customer and timer project
     * 
     * @param array $dataIn
     * @return array
     * */
    public function add_pin_timer($dataIn) {
        $dataTimerStore = $this->tblTimerProject
                ->select('timer.*,timer_project.*')
                ->where(array('timer_project.user_id' => $dataIn['user_id'], 'timer_project.customer_id' => $dataIn['customer_id'], 'timer_project.item_id' => $dataIn['item_id'], 'timer_project.ct_id' => $dataIn['ct_id']))
                ->join('timer', 'timer.timer_project_id=timer_project.timer_project_id')
                ->get()
                ->getRowArray();
        $sql = "SELECT * FROM `timer` 
                INNER JOIN timer_project USING(timer_project_id) 
                WHERE timer_project.customer_id=:customer_id: AND running = 1 AND timer_project.user_id = :user_id:";
        $par = ['customer_id' => $dataIn['customer_id'], 'user_id' => $dataIn['user_id']];
        $running_timer = $this->db->query($sql, $par)->getRowArray();
        if (empty($running_timer)) {
            if (empty($dataTimerStore)) {
                $sql = 'INSERT INTO timer_project SET
                    user_id = :user_id:,
                    customer_id = :customer_id:,
                    item_id = 0,
                    ct_id = 5,
                    color_dec = CONV("' . random_color() . '",16,10),
                    sticky_order = 0';
                $this->db->query($sql, $par);
                $dataTimerIn['timer_project_id'] = $this->db->insertID();
                $dataTimerIn['running'] = 1;
                $this->tblTimer->insert($dataTimerIn);
                if ($this->db->insertID()) {
                    $dataStore["timer_id"] = $this->db->insertID();
                    $dataStore["status"] = "success";
                } else {
                    $dataStore["status"] = "fail";
                }
            } else {
                $dataTimerIn['timer_project_id'] = $dataTimerStore['timer_project_id'];
                $dataTimerIn['running'] = 1;
                $this->tblTimer->insert($dataTimerIn);
                if ($this->db->insertID()) {
                    $dataStore["timer_id"] = $this->db->insertID();
                    $dataStore["status"] = "success";
                } else {
                    $dataStore["status"] = "fail";
                }
            }
        } else {
            $sql = 'UPDATE timer 
                    INNER JOIN timer_project USING(timer_project_id)
                    SET time_stop = CURRENT_TIMESTAMP()
                    WHERE user_id = :user_id: AND customer_id = :customer_id: AND timer_id = :timer_id: AND time_stop < CURRENT_TIMESTAMP';
            $par = ['customer_id' => $dataIn['customer_id'], 'user_id' => $this->session->get('login_userid'), 'timer_id' => $running_timer['timer_id']];
            $this->db->query($sql, $par);
            $dataStore["timer_id"] = $running_timer['timer_id']; // make timer id dynamic
            $dataStore["status"] = "success";
        }
        return $dataStore;
    }

    /**
     * On page load on other device set the graph new value
     * 
     * */
    public function set_graph() {
        $usql = 'UPDATE timer 
                INNER JOIN timer_project USING(timer_project_id)
                SET time_stop = CURRENT_TIMESTAMP()
                WHERE user_id = :user_id: AND running = 1 AND time_stop <= CURRENT_TIMESTAMP';
        $par = ['user_id' => $this->session->get('login_userid')];
        $this->db->query($usql, $par);
        $dataStore["status"] = "success";
        return $dataStore;
    }

    /**
     * Get timer as per customer selected
     * 
     * @param int $timer_id
     * @return array
     * */
    public function get_timer($timer_id = '') {
        if ($timer_id != '') {
            $dataStore = $this->tblTimerProject
                    ->select('timer.*,timer_project.*,customer.name,customer.identifier,customer.po')
                    ->where(array('timer.timer_id' => $timer_id))
                    ->join('timer', 'timer.timer_project_id=timer_project.timer_project_id')
                    ->join('customer', 'timer_project.customer_id=customer.customer_id')
                    ->get()
                    ->getRowArray();
            $smart_start_time = 'SELECT time_stop AS smart_start_time
                    FROM timer 
                    INNER JOIN timer_project ON timer.timer_project_id=timer_project.timer_project_id 
                    WHERE running = 0 
                    AND time_stop <= :time_start: 
                    AND timer_id != :timer_id: 
                    AND timer_project.user_id = :user_id: 
                    AND TIMESTAMPDIFF(MINUTE,time_start,time_stop) > 0 
                    ORDER BY time_stop DESC 
                    LIMIT 1';
            $par = ['time_start' => $dataStore['time_start'], 'timer_id' => $timer_id, 'user_id' => $this->session->get('login_userid')];
            $smart_start = $this->db->query($smart_start_time, $par)->getRowArray();
            $dataStore['smart_start_time'] = isset($smart_start['smart_start_time']) ? $smart_start['smart_start_time'] : "";
            if ($dataStore['smart_start_time'] != "") {
                $starthourdiff = (strtotime($dataStore['time_start']) - strtotime($dataStore['smart_start_time'])) / ( 60 * 60 );
                if ($starthourdiff > 12) {
                    $dataStore['smart_start_time'] = '';
                }
            }
            $smart_end_time = 'SELECT time_start AS smart_end_time
                    FROM timer 
                    INNER JOIN timer_project ON timer.timer_project_id=timer_project.timer_project_id 
                    WHERE running = 0 
                    AND time_start >= :time_stop: 
                    AND timer_id != :timer_id: 
                    AND timer_project.user_id = :user_id: 
                    AND TIMESTAMPDIFF(MINUTE,time_start,time_stop) > 0 
                    ORDER BY time_stop ASC
                    LIMIT 1';
            $par = ['time_stop' => $dataStore['time_stop'], 'timer_id' => $timer_id, 'user_id' => $this->session->get('login_userid')];
            $smart_end = $this->db->query($smart_end_time, $par)->getRowArray();
            $dataStore['smart_end_time'] = isset($smart_end['smart_end_time']) ? $smart_end['smart_end_time'] : "";
            if ($dataStore['smart_end_time'] != "") {
                $endhourdiff = (strtotime($dataStore['time_stop']) - strtotime($dataStore['smart_end_time'])) / ( 60 * 60 );
                if ($endhourdiff > 12) {
                    $dataStore['smart_end_time'] = '';
                }
            } else {
                $endhourdiff = (strtotime(gmdate("Y-m-d H:i:s")) - strtotime($dataStore['time_stop'])) / ( 60 * 60 );
                if ($endhourdiff < 12 && $endhourdiff > 0) {
                    $dataStore['smart_end_time'] = '';
                    $dataStore['smart_running_time'] = '1';
                }
            }
            return $dataStore;
        } else {
            $dataStore = $this->tblTimerProject
                    ->select('timer_project.*,customer.name,customer.identifier,customer.po')
                    ->where(array('sticky_order > ' => 0, 'user_id' => $this->session->get('login_userid'), 'timer_project.status' => 'active', 'customer.status' => 'active'))
                    ->join('customer', 'timer_project.customer_id=customer.customer_id')
                    ->orderBy('sticky_order', 'ASC')
                    ->get()
                    ->getResultArray();
            return $dataStore;
        }
    }

    /**
     * Get smart timer hours for day or week view
     * @param array $dataIn
     * @return array
     * */
    public function get_smart_timer($dataIn) {
        $smart_start_time = 'SELECT time_stop AS smart_start_time
                    FROM timer 
                    INNER JOIN timer_project ON timer.timer_project_id=timer_project.timer_project_id 
                    WHERE running = 0 
                    AND time_stop <= :time_start: 
                    AND timer_project.user_id = :user_id: 
                    AND TIMESTAMPDIFF(MINUTE,time_start,time_stop) > 0 
                    ORDER BY time_stop DESC 
                    LIMIT 1';
        $par = ['time_start' => $dataIn['time_start'], 'user_id' => $this->session->get('login_userid')];
        $smart_start = $this->db->query($smart_start_time, $par)->getRowArray();
        $dataStore['smart_start_time'] = isset($smart_start['smart_start_time']) ? $smart_start['smart_start_time'] : "";
        if ($dataStore['smart_start_time'] != "") {
            $starthourdiff = (strtotime($dataIn['time_start']) - strtotime($dataStore['smart_start_time'])) / ( 60 * 60 );
            if ($starthourdiff > 12) {
                $dataStore['smart_start_time'] = '';
            }
        }
        $smart_end_time = 'SELECT time_start AS smart_end_time
                    FROM timer 
                    INNER JOIN timer_project ON timer.timer_project_id=timer_project.timer_project_id 
                    WHERE running = 0 
                    AND time_start >= :time_stop: 
                    AND timer_project.user_id = :user_id: 
                    AND TIMESTAMPDIFF(MINUTE,time_start,time_stop) > 0 
                    ORDER BY time_stop 
                    DESC LIMIT 1';
        $par = ['time_stop' => $dataIn['time_stop'], 'user_id' => $this->session->get('login_userid')];
        $smart_end = $this->db->query($smart_end_time, $par)->getRowArray();
        $dataStore['smart_end_time'] = isset($smart_end['smart_end_time']) ? $smart_end['smart_end_time'] : "";
        if ($dataStore['smart_end_time'] != "") {
            $endhourdiff = (strtotime($dataIn['time_stop']) - strtotime($dataStore['smart_end_time'])) / ( 60 * 60 );
            if ($endhourdiff > 12) {
                $dataStore['smart_end_time'] = '';
            }
        } else {
            $endhourdiff = (strtotime(gmdate("Y-m-d H:i:s")) - strtotime($dataIn['time_stop'])) / ( 60 * 60 );
            if ($endhourdiff < 12 && $endhourdiff > 0) {
                $dataStore['smart_end_time'] = '';
                $dataStore['smart_running_time'] = '1';
            }
        }
        return $dataStore;
    }

    /**
     * Get timer project data for edit
     * 
     * @param int $timer_project_id
     * @return array
     * */
    public function get_timer_project($timer_project_id = '') {
        if ($timer_project_id != "") {
            $dataStore = $this->tblTimerProject
                    ->select('timer_project.*,customer.name,customer.identifier,customer.po')
                    ->where(array('timer_project.timer_project_id' => $timer_project_id, 'timer_project.status' => 'active', 'customer.status' => 'active'))
                    ->join('customer', 'timer_project.customer_id=customer.customer_id')
                    ->get()
                    ->getRowArray();
        } else {
            $dataStore = $this->tblTimerProject
                    ->select('timer_project.*,customer.name,customer.identifier,customer.po')
                    ->where(array('timer_project.user_id' => $this->session->get('login_userid'), 'timer_project.status' => 'active', 'customer.status' => 'active'))
                    ->join('customer', 'timer_project.customer_id=customer.customer_id')
                    ->orderBy('customer.name', 'ASC')
                    ->get()
                    ->getResultArray();
        }
        return $dataStore;
    }

    /**
     * Pause timer as per timer selected
     * 
     * @param int $timer_id
     * @return array
     * */
    public function pause_timer($timer_id) {
        $sql = 'UPDATE timer
                INNER JOIN timer_project USING(timer_project_id)
                SET time_stop = CURRENT_TIMESTAMP()
                WHERE timer_id = :timer_id: AND user_id = :user_id: AND running = 1 AND time_stop < CURRENT_TIMESTAMP';
        $par = ['timer_id' => $timer_id, 'user_id' => $this->session->get('login_userid')];
        $this->db->query($sql, $par);
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Get minute diffrence for check new timer craete or not
     * 
     * @param int $user_id,int $customer_id
     * @return array
     * */
    public function get_minute_diffrence($user_id, $customer_id) {
        $ssql = "SELECT 
                 TIMESTAMPDIFF(MINUTE,time_stop,CURRENT_TIMESTAMP()) AS dif_min
                 from timer 
                 INNER JOIN timer_project USING(timer_project_id)
                 WHERE user_id=:user_id: AND customer_id=:customer_id: AND running=1";
        $par = ['user_id' => $user_id, 'customer_id' => $customer_id];
        $dataStore = $this->db->query($ssql, $par)->getRowArray();
        return $dataStore;
    }

    /**
     * Get running timer status
     * 
     * @param int $customer_id
     * @return array
     * */
    public function get_running_timer_status($customer_id) {
        $sql = "SELECT * FROM `timer` 
                INNER JOIN timer_project USING(timer_project_id) 
                WHERE timer_project.customer_id=:customer_id: AND running = 1 AND timer_project.user_id = :user_id:";
        $par = ['customer_id' => $customer_id, 'user_id' => $this->session->get('login_userid')];
        $running_timer = $this->db->query($sql, $par)->getRowArray();
        if (empty($running_timer)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Get all running timers
     *
     * @return array
     * */
    public function get_all_running_timer() {
        $sql = 'UPDATE timer
                INNER JOIN timer_project USING(timer_project_id)
                SET time_stop = CURRENT_TIMESTAMP()
                WHERE user_id = :user_id: AND running = 1 AND time_stop < CURRENT_TIMESTAMP';
        $par = ['user_id' => $this->session->get('login_userid')];
        $this->db->query($sql, $par);
        $sql = "SELECT timer.*,timer_project.*,customer.name,customer.po,customer.identifier FROM `timer` 
                INNER JOIN timer_project USING(timer_project_id) 
                INNER JOIN customer ON timer_project.customer_id = customer.customer_id
                WHERE running = 1 AND timer_project.user_id = :user_id: 
                ORDER BY time_stop ASC";
        $par = ['user_id' => $this->session->get('login_userid')];
        $dataStore = $this->db->query($sql, $par)->getResultArray();
        return $dataStore;
    }

    /**
     * Stop timer as per timer selected
     * 
     * @param int $timer_id,$stop
     * @return array
     * */
    public function stop_timer($timer_id, $stop = '') {
        $sql = 'UPDATE timer
                INNER JOIN timer_project USING(timer_project_id)
                SET running = 0,
                locked = 0 
                WHERE timer_id = :timer_id: AND user_id = :user_id: AND running = 1';
        $par = ['timer_id' => $timer_id, 'user_id' => $this->session->get('login_userid')];
        $this->db->query($sql, $par);
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        if ($stop != 1) {
            $usql = 'UPDATE timer
                INNER JOIN timer_project USING(timer_project_id)
                SET time_stop = CURRENT_TIMESTAMP()
                WHERE timer_id = :timer_id: AND user_id = :user_id: AND time_stop < CURRENT_TIMESTAMP';
            $this->db->query($usql, $par);
        }
        return $dataStore;
    }

    /**
     * Stop all unlock timer as per timer project
     * 
     * @param int $timer_project_id
     * @return array
     * */
    public function stop_timer_project($timer_project_id) {
        $sql = 'UPDATE timer
                INNER JOIN timer_project USING(timer_project_id)
                SET running = 0,
                locked = 0 
                WHERE timer_project_id = :timer_project_id: AND user_id = :user_id: AND running = 1 AND locked = 0';
        $par = ['timer_project_id' => $timer_project_id, 'user_id' => $this->session->get('login_userid')];
        $this->db->query($sql, $par);
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * update start or end time
     * 
     * @param array $dataIn,int $timer_id
     * @return array
     * */
    public function update_timer($dataIn, $timer_id) {
        $this->tblTimer
                ->update($dataIn, array('timer_id' => $timer_id));
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Update timer and timer project data
     * 
     * @param array $dataIn
     * @return array
     * */
    public function update_timerproject($dataIn) {
        $sql = 'UPDATE timer_project 
                INNER JOIN timer USING(timer_project_id)
                SET locked = :locked:,
                sticky_order = :sticky_order: 
                WHERE user_id = :user_id: AND 
                customer_id = :customer_id: AND 
                timer_id=:timer_id:';
        $par = ['customer_id' => $dataIn['customer_id'], 'timer_id' => $dataIn['timer_id'], 'user_id' => $this->session->get('login_userid'), 'locked' => $dataIn['locked'], 'sticky_order' => (isset($dataIn['sticky_order']) ? $dataIn['sticky_order'] : 0)];
        $this->db->query($sql, $par);
        $timerDataIn['time_start'] = $dataIn['time_start'];
        $timerDataIn['time_stop'] = $dataIn['time_stop'];
        $timerDataIn['description'] = $dataIn['description'];
        if (isset($dataIn['new_timerproject_id']) && !empty($dataIn['new_timerproject_id'])) {
            $timerDataIn['timer_project_id'] = $dataIn['new_timerproject_id'];
        }
        $this->tblTimer
                ->update($timerDataIn, array('timer_id' => $dataIn['timer_id']));
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Update timer project data using list view
     * 
     * @param array $dataIn
     * @return array
     * */
    public function edit_project($dataIn) {
        $this->tblTimerProject
                ->update($dataIn, array('timer_project_id' => $dataIn['timer_project_id']));
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Delete only selectd customer timer
     * 
     * @param int $timer_id
     * @return array
     * */
    public function delete_timer($timer_id) {
        $this->tblTimer
                ->where(['timer_id' => $timer_id])
                ->delete();
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Delete only selectd customer timer
     * 
     * @param int $timer_project_id
     * @return array
     * */
    public function delete_timer_project($timer_project_id) {
        $dataTimerStore = $this->tblTimer
                ->where(array('timer_project_id' => $timer_project_id))
                ->get()
                ->getResultArray();
        if (count($dataTimerStore) > 0) {
            $this->tblTimerProject
                    ->update(array('status' => 'inactive'), array('timer_project_id' => $timer_project_id));
        } else {
            $this->tblTimerProject
                    ->where(['timer_project_id' => $timer_project_id])
                    ->delete();
        }
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Update only pin timer project data
     * 
     * @param array $dataIn
     * @return array
     * */
    public function edit_pin_timer($dataIn) {
        $sql = 'UPDATE timer_project 
                    INNER JOIN timer USING(timer_project_id)
                    SET locked = :locked:,
                    sticky_order = :sticky_order:,
                    color_dec = CONV("' . ltrim($dataIn['color_dec'], '#') . '",16,10)
                    WHERE user_id = :user_id: AND 
                    customer_id = :customer_id: AND 
                    timer_project_id=:timer_project_id:';
        $par = ['customer_id' => $dataIn['customer_id'], 'timer_project_id' => $dataIn['timer_project_id'], 'user_id' => $this->session->get('login_userid'), 'locked' => $dataIn['locked'], 'sticky_order' => (isset($dataIn['sticky_order']) ? $dataIn['sticky_order'] : 0)];
        $this->db->query($sql, $par);
        $timerDataIn['description'] = $dataIn['description'];
        $this->tblTimer
                ->update($timerDataIn, array('timer_id' => $dataIn['timer_id']));
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Get the today and current week data for graph
     * 
     * @param string $date,string $type
     * @return array
     * */
    public function get_report($date = '', $type = '', $diff = '', $status = '') {
        if ($date == '') {
            $ssql = "SELECT SUM(TIMESTAMPDIFF(MINUTE,time_start,time_stop)) AS dif_min,
                 timer_project.color_dec
                 from timer 
                 INNER JOIN timer_project USING(timer_project_id)
                 INNER JOIN customer ON timer_project.customer_id = customer.customer_id 
                 WHERE DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = DATE(NOW()) AND 
                 timer_project.user_id=:user_id:
                 AND DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = DATE(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) 
                 GROUP BY timer_project_id";
            $par = ['user_id' => $this->session->get('login_userid')];
        } else {
            $ssql = "SELECT SUM(TIMESTAMPDIFF(MINUTE,time_start,time_stop)) AS dif_min,
                 timer_project.color_dec
                 from timer 
                 INNER JOIN timer_project USING(timer_project_id) 
                 INNER JOIN customer ON timer_project.customer_id = customer.customer_id 
                 WHERE DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = :date: AND 
                 timer_project.user_id=:user_id:
                 AND DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = DATE(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) 
                 GROUP BY timer_project_id";
            $par = ['user_id' => $this->session->get('login_userid'), 'date' => $date];
        }
        $dataStore['today_data'] = $this->db->query($ssql, $par)->getResultArray();
        if ($date == '') {
            $ssql = "SELECT SUM(TIMESTAMPDIFF(MINUTE,CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "'),CONCAT(DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')),' 23:59:59'))) AS dif_min,
                 timer_project.color_dec
                 from timer 
                 INNER JOIN timer_project USING(timer_project_id) 
                 INNER JOIN customer ON timer_project.customer_id = customer.customer_id 
                 WHERE DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = DATE(NOW()) AND 
                 timer_project.user_id=:user_id:
                 AND DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) != DATE(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) 
                 GROUP BY timer_project_id";
            $par = ['user_id' => $this->session->get('login_userid')];
        } else {
            $ssql = "SELECT SUM(TIMESTAMPDIFF(MINUTE,CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "'),CONCAT(DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')),' 23:59:59'))) AS dif_min,
                 timer_project.color_dec
                 from timer 
                 INNER JOIN timer_project USING(timer_project_id) 
                 INNER JOIN customer ON timer_project.customer_id = customer.customer_id 
                 WHERE DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = :date: AND 
                 timer_project.user_id=:user_id:
                 AND DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) != DATE(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) 
                 GROUP BY timer_project_id";
            $par = ['user_id' => $this->session->get('login_userid'), 'date' => $date];
        }
        if (count($dataStore['today_data']) == 0) {
            $dataStore['today_data'] = $this->db->query($ssql, $par)->getResultArray();
        } else {
            $new_key = count($dataStore['today_data']) + 1;
            $extra_hours = $this->db->query($ssql, $par)->getResultArray();
            foreach ($extra_hours as $key => $value) {
                $dataStore['today_data'][$new_key + $key] = $value;
            }
        }
        $dataStore['today_data'] = array_values($dataStore['today_data']);
        $where = '';
        if ($diff == '') {
            $diff = 0;
        }
        if (strtolower($type) == 'week') {
            if ($diff < 0) {
                $where = ' YEARWEEK(CONVERT_TZ(time_start,"+00:00","' . $this->session->get('utc_offset') . '"),1) = YEARWEEK(NOW() - INTERVAL ' . abs($diff) . ' WEEK,1) ';
            }
            if ($diff == 0) {
                $where = ' YEARWEEK(CONVERT_TZ(time_start,"+00:00","' . $this->session->get('utc_offset') . '"),1) = YEARWEEK(NOW(),1) ';
            }
        } else if (strtolower($type) == 'month') {
            if ($diff < 0) {
                $where = " YEAR(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = YEAR(CURRENT_DATE - INTERVAL " . abs($diff) . " MONTH)
                          AND MONTH(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = MONTH(CURRENT_DATE - INTERVAL " . abs($diff) . " MONTH) ";
            }
            if ($diff == 0) {
                $where = "CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "') >= LAST_DAY(CURRENT_DATE) "
                        . "+ INTERVAL 1 DAY - INTERVAL 1 MONTH AND "
                        . "CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "') "
                        . "< LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY ";
            }
        } else if (strtolower($type) == 'year') {
            if ($diff < 0) {
                $where = ' YEAR(CONVERT_TZ(time_start,"+00:00","' . $this->session->get('utc_offset') . '")) = YEAR(CURDATE() - INTERVAL ' . abs($diff) . ' YEAR) ';
            }
            if ($diff == 0) {
                $where = "YEAR(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = YEAR(CURDATE())";
            }
        } else {
            switch ($this->session->get('pay_period')) {
                case 'monthly':
                    $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                    $end = date("Y-m-t");
                    if ($diff < 0) {
                        $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                        $start = date('Y-m-d', strtotime($diff . ' month', strtotime($start)));
                        $end = date("Y-m-t", strtotime($diff . ' month'));
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
                    $end = date('Y-m-d', strtotime('+1 week -1 day', strtotime($start)));
                    if ($this->session->get('pay_period') == 'bi-weekly') {
                        $end = date('Y-m-d', strtotime('+2 week -1 day', strtotime($start)));
                    }
                    if ($diff < 0 && $this->session->get('pay_period') == 'weekly') {
                        $start = date('Y-m-d', strtotime($diff . ' week', strtotime($start)));
                        $end = date('Y-m-d', strtotime($diff . ' week', strtotime($end)));
                    }
                    if ($diff < 0 && $this->session->get('pay_period') == 'bi-weekly') {
                        $start = date('Y-m-d', strtotime($diff * 2 . ' week', strtotime($start)));
                        $end = date('Y-m-d', strtotime($diff * 2 . ' week', strtotime($end)));
                    }
                    break;
                case 'semi-monthly':
                    $start = $this->session->get('pay_period_start');
                    if (strlen($start) == 4) {
                        $day1 = $start = substr($start, 0, 2);
                    } else {
                        $day1 = $start = substr($start, 0, 1);
                    }
                    $end = $this->session->get('pay_period_start');
                    $end = substr($end, -2);
                    $start = date('Y-m') . '-' . $start;
                    $end = date('Y-m') . '-' . $end;
                    $start = date('Y-m-d', strtotime($start));
                    $end = date('Y-m-d', strtotime($end));
                    if (null == get_cookie('pay_period') || get_cookie('pay_period') == '') {
                        $start2 = date('Y-m-d', strtotime('-1 day', strtotime($end)));
                        $today = date('Y-m-d', strtotime(convert_timezone($this->session->get('login_userid'), gmdate("Y-m-d H:i:s"), 'UTC')));
                        if ($today >= $start && $today <= $start2) {
                            $end = date('Y-m-d', strtotime('-1 day', strtotime($end)));
                            $half = '1';
                        } else if ($today <= date('Y-m-d', strtotime('+1month', strtotime($start))) && $today >= $end) {
                            $half = '2';
                            $end1 = date('Y-m-d', strtotime('+1month -1 day', strtotime($start)));
                            $start = $end;
                            $end = $end1;
                        }
                        set_cookie('pay_period', $half, 86400);
                        set_cookie('start_date', $start, 86400);
                        set_cookie('end_date', $end, 86400);
                    } else {
                        if ($status == 'past') {
                            if (get_cookie('pay_period') == 1) {
                                $start = date('Y-m-d', strtotime('-1 month +1 day', strtotime(get_cookie('end_date'))));
                                $end = date('Y-m-d', strtotime('-1 day', strtotime(get_cookie('start_date'))));
                                set_cookie('pay_period', 2, 86400);
                                set_cookie('start_date', $start, 86400);
                                set_cookie('end_date', $end, 86400);
                            } else {
                                $start = get_cookie('start_date');
                                $start = date('Y-m', strtotime($start)) . '-' . $day1;
                                $end = date('Y-m-d', strtotime('-1 day', strtotime(get_cookie('start_date'))));
                                set_cookie('pay_period', 1, 86400);
                                set_cookie('start_date', $start, 86400);
                                set_cookie('end_date', $end, 86400);
                            }
                        } else {
                            $start = date('Y-m-d', strtotime('+1 day', strtotime(get_cookie('end_date'))));
                            $end = date('Y-m-d', strtotime('+1 month -1 day', strtotime(get_cookie('start_date'))));
                            set_cookie('pay_period', 1, 86400);
                            set_cookie('start_date', $start, 86400);
                            set_cookie('end_date', $end, 86400);
                        }
                    }
                    break;
                default:
                    $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                    $end = date("Y-m-t");
                    if ($diff < 0) {
                        $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                        $start = date('Y-m-d', strtotime($diff . ' month', strtotime($start)));
                        $end = date("Y-m-t", strtotime($diff . ' month'));
                    }
                    break;
            }
            $where = " DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) >= '" . $start . "' AND DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) <= '" . $end . "' ";
        }
        $sql = "SELECT SUM(TIMESTAMPDIFF(MINUTE,time_start,time_stop)) AS dif_min,
                timer_project.color_dec
                FROM timer
                INNER JOIN timer_project USING(timer_project_id) 
                INNER JOIN customer ON timer_project.customer_id = customer.customer_id 
                WHERE " . $where . "
                AND timer_project.user_id=:user_id:
                GROUP BY timer_project_id";
        $dataStore['week_data'] = $this->db->query($sql, $par)->getResultArray();
        return $dataStore;
    }

    /**
     * Get all today stop timer for show in pie graph
     * 
     * 
     * @return array
     * */
    public function get_today_stoptimer($date = '') {
        if ($date == '') {
            $ssql = "SELECT timer.*,timer_project.*,customer.name,customer.po,customer.identifier FROM `timer` 
                INNER JOIN timer_project USING(timer_project_id) 
                INNER JOIN customer ON timer_project.customer_id = customer.customer_id
                WHERE DATE(time_stop) = DATE(NOW()) AND timer_project.user_id = :user_id:
                ORDER BY time_start";
            $par = ['user_id' => $this->session->get('login_userid')];
        } else {
            $ssql = "SELECT timer.*,timer_project.*,customer.name,customer.po,customer.identifier FROM `timer` 
                INNER JOIN timer_project USING(timer_project_id) 
                INNER JOIN customer ON timer_project.customer_id = customer.customer_id
                WHERE DATE(time_stop) = DATE(:date:) AND timer_project.user_id = :user_id:
                ORDER BY time_start";
            $par = ['user_id' => $this->session->get('login_userid'), 'date' => $date];
        }
        $dataStore = $this->db->query($ssql, $par)->getResultArray();
        return $dataStore;
    }

    /**
     * Get all today timer as per start time with project details
     * 
     * @param string $date
     * @return array
     * */
    public function get_today_timer($date = '') {
        if ($date == '') {
            $sql = "SELECT timer.time_start,timer.time_stop,timer_project.color_dec,timer.timer_id 
                    from timer 
                    INNER JOIN timer_project USING(timer_project_id) 
                    INNER JOIN customer ON timer_project.customer_id = customer.customer_id 
                    WHERE DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = DATE(NOW()) AND 
                    timer_project.user_id=:user_id: 
                    ORDER BY time_start";
            $par = ['user_id' => $this->session->get('login_userid')];
            $dataStore = $this->db->query($sql, $par)->getResultArray();
        } else {
            $sql = "SELECT timer.time_start,timer.time_stop,timer_project.color_dec,timer.timer_id 
                    from timer 
                    INNER JOIN timer_project USING(timer_project_id) 
                    INNER JOIN customer ON timer_project.customer_id = customer.customer_id 
                    WHERE DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = :date: AND 
                    timer_project.user_id=:user_id: 
                    ORDER BY time_start";
            $par = ['user_id' => $this->session->get('login_userid'), 'date' => $date];
            $dataStore = $this->db->query($sql, $par)->getResultArray();
        }
        return $dataStore;
    }

    /**
     * Get week hours and minute for show project in calendar
     * 
     * @param int $week
     * @return array
     * */
    public function get_week_calender_hours($week = '') {
        if ($week == '') {
            $sql = "SELECT TIME_FORMAT( `time_start`,'%r') AS start,
                TIME_FORMAT( `time_stop`,'%r') AS end,
                time_start,time_stop,
                TIMESTAMPDIFF(MINUTE,time_start,time_stop) AS dif_min,
                timer_project.color_dec,
                SUBSTRING(DAYNAME(time_start),1,3) as day,
                timer_id
                FROM timer 
                INNER JOIN timer_project USING(timer_project_id) 
                INNER JOIN customer ON timer_project.customer_id = customer.customer_id  
                WHERE YEARWEEK(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "'),1) = YEARWEEK(NOW(),1) 
                AND timer_project.user_id=:user_id: ";
        } else {
            $where = '';
            if ($week > 0) {
                $where .= ' YEARWEEK(CONVERT_TZ(time_start,"+00:00","' . $this->session->get('utc_offset') . '"),1) = YEARWEEK(NOW() + INTERVAL ' . abs($week) . ' WEEK,1) ';
            } else {
                $where .= ' YEARWEEK(CONVERT_TZ(time_start,"+00:00","' . $this->session->get('utc_offset') . '"),1) = YEARWEEK(NOW() - INTERVAL ' . abs($week) . ' WEEK,1) ';
            }
            if ($week == 0) {
                $where = ' YEARWEEK(CONVERT_TZ(time_start,"+00:00","' . $this->session->get('utc_offset') . '"),1) = YEARWEEK(NOW(),1) ';
            }
            $sql = "SELECT TIME_FORMAT( `time_start`,'%r') AS start,
                TIME_FORMAT( `time_stop`,'%r') AS end,
                time_start,time_stop,
                TIMESTAMPDIFF(MINUTE,time_start,time_stop) AS dif_min,
                timer_project.color_dec,
                SUBSTRING(DAYNAME(time_start),1,3) as day,
                timer_id
                FROM timer 
                INNER JOIN timer_project USING(timer_project_id) 
                INNER JOIN customer ON timer_project.customer_id = customer.customer_id  
                WHERE " . $where . " 
                AND timer_project.user_id=:user_id:";
        }
        $par = ['user_id' => $this->session->get('login_userid')];
        $dataStore = $this->db->query($sql, $par)->getResultArray();
        return $dataStore;
    }

    /**
     * Get all week hours as per time stop in current week for calender
     * 
     * @param int $week,string $type
     * @return array
     * */
    public function get_week_calender_stophours($week = '', $type = '') {
        if ($week == '') {
            $where = '';
            if ($type == 'week') {
                $where = "YEARWEEK(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "'),1) = YEARWEEK(NOW(),1) ";
            } else if ($type == 'month') {
                $where = "CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "') >= LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL 1 MONTH AND CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "') < LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY ";
            } else {
                $where = "YEAR(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) = YEAR(CURDATE())";
            }
            $sql = "SELECT TIME_FORMAT( `time_start`,'%r') AS start,
                TIME_FORMAT( `time_stop`,'%r') AS end,
                time_start,time_stop,
                TIMESTAMPDIFF(MINUTE,time_start,time_stop) AS dif_min,
                timer_project.color_dec,
                SUBSTRING(DAYNAME(time_start),1,3) as day,
                timer_id
                FROM timer 
                INNER JOIN timer_project USING(timer_project_id) 
                INNER JOIN customer ON timer_project.customer_id = customer.customer_id  
                WHERE " . $where . " 
                AND timer_project.user_id= :user_id: 
                AND YEARWEEK(time_start,1) != YEARWEEK(NOW(),1)";
        } else {
            $where = '';
            if ($week == '') {
                $week = 0;
            }
            if (strtolower($type) == 'week') {
                if ($week < 0) {
                    $where = ' YEARWEEK(CONVERT_TZ(time_stop,"+00:00","' . $this->session->get('utc_offset') . '"),1) = YEARWEEK(NOW() - INTERVAL ' . abs($week) . ' WEEK,1) ';
                }
                if ($week == 0) {
                    $where = ' YEARWEEK(CONVERT_TZ(time_stop,"+00:00","' . $this->session->get('utc_offset') . '"),1) = YEARWEEK(NOW(),1) ';
                }
            } else if (strtolower($type) == 'month') {
                if ($week < 0) {
                    $where = " YEAR(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) = YEAR(CURRENT_DATE - INTERVAL " . abs($week) . " MONTH)
                          AND MONTH(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) = MONTH(CURRENT_DATE - INTERVAL " . abs($week) . " MONTH) ";
                }
                if ($week == 0) {
                    $where = "CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "') >= LAST_DAY(CURRENT_DATE) "
                            . "+ INTERVAL 1 DAY - INTERVAL 1 MONTH AND "
                            . "CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "') "
                            . "< LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY ";
                }
            } else if (strtolower($type) == 'year') {
                if ($week < 0) {
                    $where = ' YEAR(CONVERT_TZ(time_stop,"+00:00","' . $this->session->get('utc_offset') . '")) = YEAR(CURDATE() - INTERVAL ' . abs($week) . ' YEAR) ';
                }
                if ($week == 0) {
                    $where = "YEAR(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) = YEAR(CURDATE())";
                }
            } else {
                switch ($this->session->get('pay_period')) {
                    case 'monthly':
                        $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                        $end = date("Y-m-t");
                        if ($week < 0) {
                            $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                            $start = date('Y-m-d', strtotime($week . ' month', strtotime($start)));
                            $end = date("Y-m-t", strtotime($week . ' month'));
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
                        $end = date('Y-m-d', strtotime('+1 week -1 day', strtotime($start)));
                        if ($this->session->get('pay_period') == 'bi-weekly') {
                            $end = date('Y-m-d', strtotime('+2 week -1 day', strtotime($start)));
                        }
                        if ($week < 0 && $this->session->get('pay_period') == 'weekly') {
                            $start = date('Y-m-d', strtotime($week . ' week', strtotime($start)));
                            $end = date('Y-m-d', strtotime($week . ' week', strtotime($end)));
                        }
                        if ($week < 0 && $this->session->get('pay_period') == 'bi-weekly') {
                            $start = date('Y-m-d', strtotime($week * 2 . ' week', strtotime($start)));
                            $end = date('Y-m-d', strtotime($week * 2 . ' week', strtotime($end)));
                        }
                        break;
                    case 'semi-monthly':
                        $start = date('Y-m-d', strtotime(get_cookie('start_date')));
                        $end = date('Y-m-d', strtotime(get_cookie('end_date')));
                        break;
                    default:
                        $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                        $end = date("Y-m-t");
                        if ($week < 0) {
                            $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                            $start = date('Y-m-d', strtotime($week . ' month', strtotime($start)));
                            $end = date("Y-m-t", strtotime($week . ' month'));
                        }
                        break;
                }
                $where = " DATE(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) >= '" . $start . "' AND DATE(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) <= '" . $end . "' ";
            }
            $sql = "SELECT TIME_FORMAT( `time_start`,'%r') AS start,
                TIME_FORMAT( `time_stop`,'%r') AS end,
                time_start,time_stop,
                TIMESTAMPDIFF(MINUTE,time_start,time_stop) AS dif_min,
                timer_project.color_dec,
                SUBSTRING(DAYNAME(time_start),1,3) as day,
                timer_id
                FROM timer 
                INNER JOIN timer_project USING(timer_project_id) 
                INNER JOIN customer ON timer_project.customer_id = customer.customer_id  
                WHERE " . $where . " 
                AND timer_project.user_id= :user_id: 
                AND YEARWEEK(time_start,1) != YEARWEEK(NOW(),1)";
        }
        $par = ['user_id' => $this->session->get('login_userid')];
        $dataStore = $this->db->query($sql, $par)->getResultArray();
        return $dataStore;
    }

    /**
     * Get today hours and minute for show project in listing
     * 
     * @param string $date
     * 
     * @return array
     * */
    public function get_today_hours($date = '') {
        if ($date == '') {
            $sql = "SELECT TIMESTAMPDIFF(MINUTE,time_start,time_stop) AS dif_min,
                    time_start,time_stop,
                    timer_project_id,
                    timer_project.color_dec,
                    customer.name,customer.po,customer.identifier,
                    timer_id
                    from timer 
                    INNER JOIN timer_project USING(timer_project_id) 
                    INNER JOIN customer ON timer_project.customer_id=customer.customer_id
                    WHERE DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = DATE(NOW()) AND 
                    timer_project.user_id=:user_id: 
                    ORDER BY time_start";
            $par = ['user_id' => $this->session->get('login_userid')];
            $dataStore = $this->db->query($sql, $par)->getResultArray();
        } else {
            $sql = "SELECT TIMESTAMPDIFF(MINUTE,time_start,time_stop) AS dif_min,
                    time_start,time_stop,
                    timer_project_id,
                    timer_project.color_dec,
                    customer.name,customer.po,customer.identifier,
                    timer_id
                    from timer 
                    INNER JOIN timer_project USING(timer_project_id) 
                    INNER JOIN customer ON timer_project.customer_id=customer.customer_id
                    WHERE DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = :date: AND 
                    timer_project.user_id=:user_id: 
                    ORDER BY time_start";
            $par = ['user_id' => $this->session->get('login_userid'), 'date' => $date];
            $dataStore = $this->db->query($sql, $par)->getResultArray();
        }
        return $dataStore;
    }

    /**
     * Add new timer project using custom,item and charge type
     * 
     * @param array $dataIn
     * @return array
     * */
    public function add_timer_project($dataIn) {
        $dataTimerStore = $this->tblTimerProject
                ->select('timer_project.*')
                ->where(array('timer_project.user_id' => $this->session->get('login_userid'), 'timer_project.customer_id' => $dataIn['customer_id'], 'timer_project.item_id' => $dataIn['item_id'], 'timer_project.ct_id' => $dataIn['ct_id']))
                ->get()
                ->getRowArray();
        if (empty($dataTimerStore)) {
            $this->tblTimerProject->insert($dataIn);
            if ($this->db->insertID()) {
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
        } else {
            $this->tblTimerProject
                    ->update(array('status' => 'active'), array('timer_project_id' => $dataTimerStore['timer_project_id']));
            $dataStore["status"] = "success";
//            $dataStore["alert"] = 1;
        }

        return $dataStore;
    }

    /**
     * Get graph CSV data for export
     * 
     * @param int $diff,string $type.string $time_type
     * @return array
     * */
    public function get_export_timer($diff = '', $type = '', $time_type = '') {
        $where = '';
        if ($diff == '') {
            $diff = 0;
        }
        if (strtolower($type) == 'week') {
            if ($time_type == 'start') {
                if ($diff < 0) {
                    $where = ' YEARWEEK(CONVERT_TZ(time_start,"+00:00","' . $this->session->get('utc_offset') . '"),1) = YEARWEEK(NOW() - INTERVAL ' . abs($diff) . ' WEEK,1) ';
                }
                if ($diff == 0) {
                    $where = ' YEARWEEK(CONVERT_TZ(time_start,"+00:00","' . $this->session->get('utc_offset') . '"),1) = YEARWEEK(NOW(),1) ';
                }
            } else {
                if ($diff < 0) {
                    $where = ' YEARWEEK(CONVERT_TZ(time_stop,"+00:00","' . $this->session->get('utc_offset') . '"),1) = YEARWEEK(NOW() - INTERVAL ' . abs($diff) . ' WEEK,1) ';
                }
                if ($diff == 0) {
                    $where = ' YEARWEEK(CONVERT_TZ(time_stop,"+00:00","' . $this->session->get('utc_offset') . '"),1) = YEARWEEK(NOW(),1) ';
                }
            }
        } else if (strtolower($type) == 'month') {
            if ($time_type == 'start') {
                if ($diff < 0) {
                    $where = " YEAR(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = YEAR(CURRENT_DATE - INTERVAL " . abs($diff) . " MONTH)
                           AND MONTH(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = MONTH(CURRENT_DATE - INTERVAL " . abs($diff) . " MONTH) ";
                }
                if ($diff == 0) {
                    $where = "CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "') >= LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL 1 MONTH AND 
                          CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "') < LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY ";
                }
            } else {
                if ($diff < 0) {
                    $where = " YEAR(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) = YEAR(CURRENT_DATE - INTERVAL " . abs($diff) . " MONTH)
                           AND MONTH(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) = MONTH(CURRENT_DATE - INTERVAL " . abs($diff) . " MONTH) ";
                }
                if ($diff == 0) {
                    $where = "CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "') >= LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL 1 MONTH AND 
                          CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "') < LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY ";
                }
            }
        } else if (strtolower($type) == 'year') {
            if ($time_type == 'start') {
                if ($diff < 0) {
                    $where = ' YEAR(CONVERT_TZ(time_start,"+00:00","' . $this->session->get('utc_offset') . '")) = YEAR(CURDATE() - INTERVAL ' . abs($diff) . ' YEAR) ';
                }
                if ($diff == 0) {
                    $where = "YEAR(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) = YEAR(CURDATE())";
                }
            } else {
                if ($diff < 0) {
                    $where = ' YEAR(CONVERT_TZ(time_stop,"+00:00","' . $this->session->get('utc_offset') . '")) = YEAR(CURDATE() - INTERVAL ' . abs($diff) . ' YEAR) ';
                }
                if ($diff == 0) {
                    $where = "YEAR(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) = YEAR(CURDATE())";
                }
            }
        } else {
            switch ($this->session->get('pay_period')) {
                case 'monthly':
                    $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                    $end = date("Y-m-t");
                    if ($diff < 0) {
                        $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                        $start = date('Y-m-d', strtotime($diff . ' month', strtotime($start)));
                        $end = date("Y-m-t", strtotime($diff . ' month'));
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
                    $end = date('Y-m-d', strtotime('+1 week -1 day', strtotime($start)));
                    if ($this->session->get('pay_period') == 'bi-weekly') {
                        $end = date('Y-m-d', strtotime('+2 week -1 day', strtotime($start)));
                    }
                    if ($diff < 0 && $this->session->get('pay_period') == 'weekly') {
                        $start = date('Y-m-d', strtotime($diff . ' week', strtotime($start)));
                        $end = date('Y-m-d', strtotime($diff . ' week', strtotime($end)));
                    }
                    if ($diff < 0 && $this->session->get('pay_period') == 'bi-weekly') {
                        $start = date('Y-m-d', strtotime($diff * 2 . ' week', strtotime($start)));
                        $end = date('Y-m-d', strtotime($diff * 2 . ' week', strtotime($end)));
                    }
                    break;
                case 'semi-monthly':
                    $start = date('Y-m-d', strtotime(get_cookie('start_date')));
                    $end = date('Y-m-d', strtotime(get_cookie('end_date')));
                    break;
                default:
                    $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                    $end = date("Y-m-t");
                    if ($diff < 0) {
                        $start = date('Y-m') . '-' . $this->session->get('pay_period_start');
                        $start = date('Y-m-d', strtotime($diff . ' month', strtotime($start)));
                        $end = date("Y-m-t", strtotime($diff . ' month'));
                    }
                    break;
            }
            if ($time_type == 'start') {
                $where = " DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) >= '" . $start . "' AND DATE(CONVERT_TZ(time_start,'+00:00','" . $this->session->get('utc_offset') . "')) <= '" . $end . "' ";
            } else {
                $where = " DATE(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) >= '" . $start . "' AND DATE(CONVERT_TZ(time_stop,'+00:00','" . $this->session->get('utc_offset') . "')) <= '" . $end . "' ";
            }
        }
        $sql = "SELECT CONVERT_TZ(timer.time_start,'+00:00','" . $this->session->get('utc_offset') . "') AS time_start,
                CONVERT_TZ(timer.time_stop,'+00:00','" . $this->session->get('utc_offset') . "') AS time_stop,
                timer.description,timer_project.item_id,timer_project.ct_id,
                customer.name,customer.po,customer.identifier FROM `timer` 
                INNER JOIN timer_project USING(timer_project_id) 
                INNER JOIN customer ON timer_project.customer_id = customer.customer_id
                WHERE " . $where . " 
                AND timer_project.user_id=:user_id: 
                ORDER BY timer.time_start ASC";
        $par = ['user_id' => $this->session->get('login_userid')];
        $dataStore = $this->db->query($sql, $par)->getResultArray();
        return $dataStore;
    }

    public function start_quick_timer() {
        $dataStore = array();
        $dataStore = $this->tblCustomer
                ->where(array('biller_id' => $this->session->get('biller_id'), 'identifier' => 'internal'))
                ->get()
                ->getRowArray();
        if (!isset($dataStore['customer_id']) && empty($dataStore['customer_id'])) {
            $dataIn['biller_id'] = $this->session->get('biller_id');
            $dataIn['name'] = 'Internal Tracking';
            $dataIn['identifier'] = 'internal';
            $dataIn['notes'] = 'This entry is used for recording internal transactions that are not assigned to a specific customer.';
            $this->tblCustomer->insert($dataIn);
            if ($this->db->insertID()) {
                $customer_id = $this->db->insertID();
            }
        } else {
            $customer_id = $dataStore['customer_id'];
        }
        $dataTimerStore = $this->tblTimerProject
                ->select('timer_project.*')
                ->where(array('timer_project.user_id' => $this->session->get('login_userid'), 'timer_project.customer_id' => $customer_id, 'timer_project.item_id' => 0, 'timer_project.ct_id' => 5))
                ->get()
                ->getRowArray();
        if (empty($dataTimerStore)) {
            $dataProjectIn['user_id'] = $this->session->get("login_userid");
            $dataProjectIn['customer_id'] = $customer_id;
            $dataProjectIn['item_id'] = 0;
            $dataProjectIn['ct_id'] = 5;
            $dataProjectIn['color_dec'] = hexdec(random_color());
            $dataProjectIn['sticky_order'] = 0;
            $dataProjectIn['locked'] = 0;
            $this->tblTimerProject->insert($dataProjectIn);
            if ($this->db->insertID()) {
                $timer_project_id = $this->db->insertID();
            }
        } else {
            $timer_project_id = $dataTimerStore['timer_project_id'];
        }
        $dataTimerIn['timer_project_id'] = $timer_project_id;
        $dataTimerIn['running'] = 1;
        $this->tblTimer->insert($dataTimerIn);
        return $this->db->insertID();
    }

}
