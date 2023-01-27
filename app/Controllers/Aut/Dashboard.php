<?php

namespace App\Controllers\aut;

require '../vendor/autoload.php';

use CodeIgniter\Controller;
use App\Models\BillerModel;
use App\Models\UserModel;
use App\Models\DashboardModel;
use Aws\S3\S3Client;

class Dashboard extends Controller {

    protected $BillerModel;
    protected $session;
    protected $UserModel;
    protected $s3Client;
    protected $helpers = ["url", "form", "general", "image", "cookie"];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->BillerModel = new BillerModel();
        $this->UserModel = new UserModel();
        $this->DashboardModel = new DashboardModel();
        $this->session = \Config\Services::session();
        $this->session->start();

        $this->s3Client = new S3Client([
            'region' => 'us-east-1',
            'version' => 'latest',
            'credentials' => array(
                'key' => getenv('aws.access_key_id'),
                'secret' => getenv('aws.secret_access_key'),
            ),
        ]);
    }

    public function index() {
        $sess_id = $this->session->get('login_userid');
        if (!empty($sess_id)) {
            $dataStore['invoice_data'] = $this->DashboardModel->get_total_invoice();
            $dataStore['credit_data'] = $this->DashboardModel->get_total_credit();
            $dataStore['undeposited_items'] = $this->DashboardModel->get_undeposited_items();
            $dataStore['unbill_charges'] = $this->DashboardModel->get_unbill_charges();
            echo view('Views/aut/dashboard_view', $dataStore);
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function user_profile() {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore = $this->UserModel->get_user_profile_data($this->session->get('login_userid'));
            $dataStore["user_aut_data"] = $dataStore["user_aut_data"];
            $dataStore["user_data"] = $dataStore["name"];
            unset($dataStore["name"]);
            echo view('Views/aut/user_profile_view', $dataStore);
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function change_password() {
        $dataIn["password"] = password_hash($this->request->getPost("userPassword"), PASSWORD_DEFAULT);
        $dataIn["user_id"] = $this->session->get('login_userid');
        $dataStore = $this->UserModel->update_user_data($dataIn);
        echo json_encode($dataStore);
    }

    public function verify_email_phone() {
        $dataIn["verify_id"] = $this->request->getPost("verify_id");
        $dataIn["verification_code"] = $this->request->getPost("user_verification_code");
        $dataIn["auth_value"] = $this->request->getPost("auth_value");
        $dataIn["user_id"] = $this->session->get('login_userid');
        $result = $this->UserModel->update_user_auth_data($dataIn);
        echo \GuzzleHttp\json_encode($result);
    }

    public function update_userprofile() {
        $dataIn[$this->request->getPost('name')] = $this->request->getPost('value');
        $dataStore = $this->UserModel->update_user_data($dataIn);
        $name_display = dbQueryRows('user', array('user_id' => $this->session->get('login_userid')));
        $userDataIn['name_display'] = trim($name_display[0]['name_first'] . ' ' . $name_display[0]['name_last']);
        $dataStore = $this->UserModel->update_user_data($userDataIn);
        echo json_encode($dataStore);
    }

    public function save_profile() {
        if (strlen($this->request->getPost("social_img_url")) > 0) {
            $image = file_get_contents($this->request->getPost("social_img_url"));
            $imageSeed = rand(1, 255);
            $filename = $this->session->get('login_userid') . "-" . base_convert($this->session->get('login_userid') + $imageSeed, 10, 32) . '.jpg';
            $filepath = "/tmp/{$filename}";
            file_put_contents($filepath, $image);
            // Resize image and crop to square
            fit_social_image($filepath);

            $this->s3Client->putObject(array(
                'Bucket' => 'billex',
                'Key' => 'profile/' . $filename,
                'SourceFile' => $filepath,
                'ACL' => 'public-read'
            ));
            $dataProfileIn["avatar_seed"] = $imageSeed;
            $dataProfileIn["user_id"] = $this->session->get('login_userid');
            $dataStore = $this->UserModel->update_user_data($dataProfileIn);
            if ('' != $dataProfileIn["avatar_seed"]) {
                $session_profile_data = array('profileImage' => 'https://billex.s3.amazonaws.com/profile/' . $filename, 'profileImageName' => $filename, 'profileImageSeed' => $imageSeed);
                $this->session->set($session_profile_data);
            }
            echo json_encode($dataStore);
        }
    }

    public function disconnect_google() {
        $dataIn["user_id"] = $this->session->get('login_userid');
        $dataStore = $this->UserModel->disconnect_google($dataIn);
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function mobile_menu() {
        $sess_id = $this->session->get('login_userid');
        if (!empty($sess_id)) {
            echo view('Views/aut/mobile_menu_view');
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function set_compact() {
        if (get_cookie('compact') == 1) {
            set_cookie('compact', false, 31622400);
            set_cookie('sidenav_toggle', false, 31622400);
        } else {
            set_cookie('compact', true, 31622400);
            set_cookie('sidenav_toggle', true, 31622400);
        }
    }

    public function sidenav_toggle() {
        if (get_cookie('sidenav_toggle') == 1) {
            set_cookie('sidenav_toggle', false, 31622400);
        } else {
            set_cookie('sidenav_toggle', true, 31622400);
        }
    }

    public function set_timezone() {
        $dataIn["utc_offset"] = $this->request->getPost('utc_offset');
        $dataIn["user_id"] = $this->session->get('login_userid');
        $dataStore = $this->UserModel->update_user_data($dataIn);
        $utc_offset = dbQueryRows('user', array('user_id' => $this->session->get('login_userid')));
        if ($utc_offset[0]['utc_offset'] == 0) {
            $utc_offset[0]['utc_offset'] = -180;
        }
        $time_zone = intval($utc_offset[0]['utc_offset']) / 60;
        $time_zone = '-0' . abs($time_zone) . ':00';
        $this->session->set(array('utc_offset' => $time_zone));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function set_payperiods() {
        $dataIn["pay_period"] = $this->request->getPost('pay_period');
        if ($dataIn["pay_period"] == 'monthly') {
            $dataIn["pay_period_start"] = '1';
        } else if ($dataIn["pay_period"] == 'weekly' || $dataIn["pay_period"] == 'bi-weekly') {
            $dataIn["pay_period_start"] = '1';
        } else {
            $dataIn["pay_period_start"] = '116';
        }
        $dataIn["user_id"] = $this->session->get('login_userid');
        $dataStore = $this->UserModel->update_user_data($dataIn);
        $pay_periods = dbQueryRows('user', array('user_id' => $this->session->get('login_userid')));
        $this->session->set(array('pay_period' => $pay_periods[0]['pay_period']));
        $this->session->set(array('pay_period_start' => $pay_periods[0]['pay_period_start']));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function set_startvalue() {
        $dataIn["pay_period_start"] = $this->request->getPost('pay_period_start');
        $dataIn["user_id"] = $this->session->get('login_userid');
        $dataStore = $this->UserModel->update_user_data($dataIn);
        $pay_periods = dbQueryRows('user', array('user_id' => $this->session->get('login_userid')));
        $this->session->set(array('pay_period' => $pay_periods[0]['pay_period']));
        $this->session->set(array('pay_period_start' => $pay_periods[0]['pay_period_start']));
        echo \GuzzleHttp\json_encode($dataStore);
    }

}
