<?php

namespace App\Controllers\pub;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Libraries\Google;

class GoogleAuth extends Controller {

    protected $helpers = ["url", "form", 'general'];
    protected $UserModel;
    protected $google;
    protected $session;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        $this->UserModel = new UserModel();
        $this->google = new \Google();
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    public function oauth2callback() {
        $google_data = $this->google->validate();
        $dataUserIn = array(
            'name_display' => $google_data['displayName']
        );
        $dataUserAuthIn = array(
            'value' => $google_data["id"],
            'type' => 'google',
            'email_address' => $google_data['email_address']
        );
        $dataStore = $this->UserModel->login_via_google($dataUserIn, $dataUserAuthIn);
        if ($dataStore['already'] == 1) {
            $session_login_data = array('login_userid' => $dataStore["user_id"], 'display_name' => $dataStore["name_display"], 'biller_id' => $dataStore["biller_id"]);
            if ($dataStore["avatar_seed"] == 0) {
                $filename = 'profile-placeholder.jpg';
            } else {
                $filename = $dataStore["user_id"] . "-" . base_convert($dataStore["user_id"] + $dataStore["avatar_seed"], 10, 32) . '.jpg';
            }
            $session_profile_data = array('profileImage' => 'https://billex.s3.amazonaws.com/profile/' . $filename, 'profileImageSeed' => $dataStore["avatar_seed"]);

            $utc_offset = dbQueryRows('user', array('user_id' => $dataStore["user_id"]));
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
            return redirect()->to(base_url() . 'aut/dashboard');
        } else {
            $session_login_data = array('userid' => $dataStore["user_id"]);
            $this->session->set($session_login_data);
            return redirect()->to(base_url() . 'pub/setup');
        }
    }

    public function googlelogin() {
        $google_data = $this->google->validate();
        $dataUserIn = array(
            'name_display' => $google_data['displayName']
        );
        $dataUserAuthIn = array(
            'value' => $google_data["id"],
            'type' => 'google',
            'email_address' => $google_data['email_address']
        );
        $dataStore = $this->UserModel->login_via_google($dataUserIn, $dataUserAuthIn);
        $session_login_data = array('login_userid' => $dataStore["user_id"], 'display_name' => $dataStore["name_display"], 'biller_id' => $dataStore["biller_id"]);
        if ($dataStore["avatar_seed"] == 0) {
            $filename = 'profile-placeholder.jpg';
        } else {
            $filename = $dataStore["user_id"] . "-" . base_convert($dataStore["user_id"] + $dataStore["avatar_seed"], 10, 32) . '.jpg';
        }
        $session_profile_data = array('profileImage' => 'https://billex.s3.amazonaws.com/profile/' . $filename, 'profileImageSeed' => $dataStore["avatar_seed"]);
        $utc_offset = dbQueryRows('user', array('user_id' => $dataStore["user_id"]));
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
        if ($dataStore["biller_id"] == "") {
            return redirect()->to(base_url() . 'login?biller_status=' . encrypt(1));
        } else {
            if (!empty($this->session->has('refURL'))) {
                return redirect()->to($this->session->get('refURL'));
            } else {
                return redirect()->to(base_url() . 'aut/dashboard');
            }
        }
    }

    public function googleconnect() {
        $google_data = $this->google->validate();
        $dataUserIn = array(
            'name_display' => $google_data['displayName']
        );
        $dataUserAuthIn = array(
            'value' => $google_data["id"],
            'type' => 'google',
            'email_address' => $google_data['email_address']
        );
        $dataStore = $this->UserModel->login_via_google($dataUserIn, $dataUserAuthIn);
        return redirect()->to(base_url() . 'aut/dashboard/user_profile');
    }

}
