<?php

namespace App\Controllers\pub;

require '../vendor/autoload.php';

use CodeIgniter\Controller;
use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Aws\Sns\SnsClient;

class Start extends Controller {

    protected $helpers = ["url", "form", "general"];
    protected $UserModel;
    protected $session;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->UserModel = new UserModel();
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    public function index() {
        echo view('Views/pub/home_view');
    }

    public function features() {
        echo view('Views/pub/features_view');
    }

    public function pricing() {
        echo view('Views/pub/pricing_view');
    }

    public function howto() {
        echo view('Views/pub/howto_view');
    }

    public function signup() {
        echo view('Views/pub/get_started_view');
    }

    public function registration_email() {
        $dataIn["email"] = $this->request->getPost("user_email_address");
        $dataIn["phone"] = "";
        $result = $this->UserModel->generate_verify_code($dataIn);
        $res = sendMail($dataIn["email"], $result["verification_code"]);
        $result["mail_status"] = $res;
        unset($result["verification_code"]);
        echo json_encode($result);
    }

    public function registration_phone() {
        $dataIn["phone"] = $this->request->getPost("user_phone_number");
        $dataIn["email"] = "";
        $result = $this->UserModel->generate_verify_code($dataIn);
        $res = sendMessage($dataIn["phone"], $result["verification_code"]);
        $result["message_status"] = $res;
        unset($result["verification_code"]);
        echo json_encode($result);
    }

    public function verify() {
        $dataIn["verify_id"] = $this->request->getPost("verify_id");
        $dataIn["verification_code"] = $this->request->getPost("user_verification_code");
        $result = $this->UserModel->verify_user($dataIn);
        if ($result["status"] == "Success") {
            if (strpos($_SERVER["HTTP_REFERER"], 'login') || $this->request->getPost("user_allready") == 1) {
                $session_login_data = array('login_userid' => $result["user_id"], 'display_name' => $result["display_name"], 'biller_id' => (isset($result["biller_id"]) ? $result["biller_id"] : ""));
                if ($result["avatar_seed"] == 0) {
                    $filename = 'profile-placeholder.jpg';
                } else {
                    $filename = $result["user_id"] . "-" . base_convert($result["user_id"] + $result["avatar_seed"], 10, 32) . '.jpg';
                }

                $session_profile_data = array('profileImage' => 'https://billex.s3.amazonaws.com/profile/' . $filename, 'profileImageSeed' => $result["avatar_seed"]);
                $this->session->set($session_profile_data);
            } else {
                $session_login_data = array('userid' => $result["user_id"]);
            }

            $this->session->set($session_login_data);
        }
        $utc_offset = dbQueryRows('user', array('user_id' => $result["user_id"]));
        if ($utc_offset[0]['utc_offset'] == 0) {
            $utc_offset[0]['utc_offset'] = -180;
        }
        $time_zone = intval($utc_offset[0]['utc_offset']) / 60;
        $time_zone = '-0' . abs($time_zone) . ':00';
        $this->session->set(array('utc_offset' => $time_zone));
        $this->session->set(array('pay_period' => $utc_offset[0]['pay_period']));
        $this->session->set(array('pay_period_start' => $utc_offset[0]['pay_period_start']));
        $result["biller_status"] = (isset($result["biller_id"]) && $result["biller_id"] != "" ? "1" : "0");
        unset($result["user_id"], $result["biller_id"]);
        echo json_encode($result);
    }

    public function login() {
        echo view("Views/pub/login_view");
    }

    public function email_login() {
        $dataIn["email"] = $this->request->getPost("user_email_address");
        $result = $this->UserModel->login_via_email($dataIn);
        $res = sendMail($dataIn["email"], $result["verification_code"]);

        unset($result["verification_code"]);
        unset($result["verification_type"]);
        echo json_encode($result);
    }

    public function phone_login() {
        $dataIn["phone"] = $this->request->getPost("user_phone_number");
        $result = $this->UserModel->login_via_phone($dataIn);
        $res = sendMessage($dataIn["phone"], $result["verification_code"]);

        unset($result["verification_code"]);
        unset($result["verification_type"]);
        echo json_encode($result);
    }

    public function user_check() {
        $dataIn["email"] = $this->request->getPost("user_email_address");
        $dataIn["phone"] = $this->request->getPost("phone");
        $result = $this->UserModel->user_check($dataIn);
        if ($result["status"] == "success") {
            $session_login_data = array('check_userid' => $result["user_id"]);
            $this->session->set($session_login_data);
        }
        unset($result["user_id"]);
        echo json_encode($result);
    }

    public function password_verification() {
        $dataIn["password"] = $this->request->getPost("password");
        $dataIn["user_id"] = $this->session->get('check_userid');
        $result = $this->UserModel->password_login($dataIn);
        if ($result["status"] == "success") {
            $session_login_data = array('login_userid' => $dataIn["user_id"], 'display_name' => $result["name_display"], 'biller_id' => (isset($result["biller_id"]) ? $result["biller_id"] : ""));
            if ($result["avatar_seed"] == 0) {
                $filename = 'profile-placeholder.jpg';
            } else {
                $filename = $dataIn["user_id"] . "-" . base_convert($dataIn["user_id"] + $result["avatar_seed"], 10, 32) . '.jpg';
            }

            $session_profile_data = array('profileImage' => 'https://billex.s3.amazonaws.com/profile/' . $filename, 'profileImageSeed' => $result["avatar_seed"]);
            $utc_offset = dbQueryRows('user', array('user_id' => $dataIn["user_id"]));
            if ($utc_offset[0]['utc_offset'] == 0) {
                $utc_offset[0]['utc_offset'] = -180;
            }
            $time_zone = intval($utc_offset[0]['utc_offset']) / 60;
            $time_zone = '-0' . abs($time_zone) . ':00';
            $this->session->set(array('utc_offset' => $time_zone));
            $this->session->set(array('pay_period' => $utc_offset[0]['pay_period']));
            $this->session->set(array('pay_period_start' => $utc_offset[0]['pay_period_start']));
            $this->session->set($session_login_data);
            $this->session->set($session_profile_data);
        }
        $result["biller_status"] = (isset($result["biller_id"]) && $result["biller_id"] != "" ? "1" : "0");
        unset($result["avatar_seed"], $result["name_display"], $result["biller_id"]);
        echo json_encode($result);
    }

    public function logout() {
        $session_items = array('userid', 'check_userid', 'utc_offset', 'pay_period_start', 'pay_period', 'login_userid', 'display_name', 'biller_id', 'profileImage', 'profileImageSeed', 'profileImageName', 'allready_user_id', 'customer_id', 'credit_id', 'invoice_id');
        $this->session->remove($session_items);
        return redirect()->to(base_url());
    }

}
