<?php

namespace App\Controllers\aut;

use CodeIgniter\Controller;
use App\Libraries\Casscall;
use App\Models\AlertModel;

class Alert extends Controller {

    protected $session;
    public $casscall;
    protected $helpers = ["url", "form", "general", "image"];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->AlertModel = new AlertModel();
    }

    public function index() {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore['alert_data'] = $this->AlertModel->get_alert();
            $count = 0;
            foreach ($dataStore["alert_data"] as $alert_value) {
                $dataStore["alert_data"][$count]["id"] = base64_encode(encrypt($alert_value["id"]));
                $count++;
            }
            $superadmin = FALSE;
            $email_id = dbQueryRows('user_auth', array('user_id' => $_SESSION['login_userid'], 'type' => 'email'));
            $superadmin_array = array('doughoman@gmail.com');
            if (isset($email_id[0]['value']) && in_array($email_id[0]['value'], $superadmin_array)) {
                $superadmin = TRUE;
            }
            if ($superadmin) {
                echo view('Views/aut/alert_view', $dataStore);
            }
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function ajax_add_edit_alert($id = '') {
        $dataIn = $this->request->getPost('form');
        if ($dataIn['time_stamp'] == '') {
            unset($dataIn['time_stamp']);
        } else {
            $dataIn['time_stamp'] = date('Y-m-d H:i:s', strtotime($dataIn['time_stamp']));
        }
        if ($id == '') {
            $dataStore = $this->AlertModel->add_edit_alert($dataIn);
        } else {
            $dataStore = $this->AlertModel->add_edit_alert($dataIn, $id);
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function get_alert_edit_data() {
        $dataStore = $this->AlertModel->get_alert($this->request->getPost('id'));
        $dataStore['id'] = base64_encode(encrypt($dataStore["id"]));
        $dataStore['time_stamp'] = date('m/d/Y H:i:s', strtotime($dataStore['time_stamp']));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function user_alerts() {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore['alert_data'] = $this->AlertModel->get_alert();
            echo view('Views/aut/user_alerts_view', $dataStore);
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

}
