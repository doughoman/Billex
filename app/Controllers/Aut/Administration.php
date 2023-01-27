<?php

namespace App\Controllers\aut;

include '../wepay.php';

use CodeIgniter\Controller;
use App\Libraries\Casscall;
use App\Models\AdministrationModel;
use App\Models\BillerModel;
use App\Models\CustomerModel;
use WePay;

class Administration extends Controller {

    protected $session;
    public $casscall;
    protected $helpers = ["url", "form", "general", "image", "file"];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->casscall = new \Casscall();
        $this->AdministrationModel = new AdministrationModel();
        $this->BillerModel = new BillerModel();
        $this->CustomerModel = new CustomerModel();
    }

    public function item() {
        if (isset($_REQUEST['q']) && $_REQUEST['q'] == 'initialize') {
            $session_items = array('item_search_key');
            $this->session->remove($session_items);
        }
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore = $this->AdministrationModel->get_item();
            $dataStore["item_data"] = $dataStore;
            $count = 0;
            foreach ($dataStore["item_data"] as $customer_value) {
                $dataStore["item_data"][$count]["item_id"] = base64_encode(encrypt($customer_value["item_id"]));
                $count++;
            }

            echo view('Views/aut/item_listing_view', $dataStore);
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function search_key() {
        $search_key = $this->request->getPost('search_key');
        $session_search_data = array('item_search_key' => $search_key);
        $this->session->set($session_search_data);
    }

    public function add_edit_item($id = '') {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            if ($id == "") {
                echo view('Views/aut/add_item_view');
            } else {
                $dataStore = $this->AdministrationModel->get_item($id);
                if (empty($dataStore)) {
                    return redirect()->to(base_url() . 'aut/administration/item');
                } else {
                    $dataStore["item_id"] = base64_encode(encrypt($dataStore["item_id"]));
                    echo view('Views/aut/add_item_view', $dataStore);
                }
            }
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function ajax_add_edit_item($id = "") {
        $dataIn = $this->request->getPost('form');
        $dataIn['ct_id'] = $this->CustomerModel->get_charge_type($dataIn['ct_id']);
        if ($id == "") {
            $dataIn['user_id_created'] = $this->session->get('login_userid');
            $dataIn['user_id_updated'] = $this->session->get('login_userid');
            $dataStore = $this->AdministrationModel->add_edit_item($dataIn);
        } else {
            $dataIn['user_id_updated'] = $this->session->get('login_userid');
            $dataStore = $this->AdministrationModel->add_edit_item($dataIn, $id);
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function settings() {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {

            if (isset($_REQUEST['stripe']) && $_REQUEST['stripe'] == 1) {
                if (isset($_REQUEST['code'])) {

                    $stripeData = $this->BillerModel->get_stripe_data();
                    if (empty($stripeData)) {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'https://connect.stripe.com/oauth/token');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, "client_secret=" . env("stripe.secret_key") . "&code=" . $_REQUEST['code'] . "&grant_type=authorization_code");
                        curl_setopt($ch, CURLOPT_POST, 1);
                        $headers = array();
                        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $result = curl_exec($ch);
                        if (curl_errno($ch)) {
                            echo 'Error:' . curl_error($ch);
                        }
                        curl_close($ch);

                        $this->BillerModel->add_stripe_account(\GuzzleHttp\json_decode($result, TRUE));
                    } else {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'https://connect.stripe.com/oauth/token');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, "client_secret=" . env("stripe.secret_key") . "&refresh_token=" . $stripeData['refresh_token'] . "&grant_type=refresh_token");
                        curl_setopt($ch, CURLOPT_POST, 1);
                        $headers = array();
                        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $result = curl_exec($ch);
                        if (curl_errno($ch)) {
                            echo 'Error:' . curl_error($ch);
                        }
                        curl_close($ch);
                        $this->BillerModel->add_stripe_account(\GuzzleHttp\json_decode($result, TRUE));
                    }
                    $billerDataIn['processor'] = 'stripe';
                    $this->BillerModel->update_biller_data($billerDataIn);
                }
            }
            if (isset($_REQUEST['wepay']) && $_REQUEST['wepay'] == 1) {
                if (isset($_REQUEST['code'])) {
                    $client_id = env('wepay.client_id');
                    $client_secret = env('wepay.client_secret');
                    Wepay::useStaging($client_id, $client_secret);
                    $wepay = new WePay(NULL);
                    $response = WePay::getToken($_REQUEST['code'], base_url() . 'aut/administration/settings?wepay=1');
                    $dataIn['biller_id'] = $this->session->get('biller_id');
                    $dataIn['account_id'] = $response->user_id;
                    $dataIn['access_token'] = $response->access_token;
                    $this->BillerModel->add_wepay_account($dataIn);
                    $billerDataIn['processor'] = 'wepay';
                    $this->BillerModel->update_biller_data($billerDataIn);
                }
            }
            $dataStore = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
            echo view('Views/aut/settings_view', $dataStore);
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function users() {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore['biller_user'] = $this->AdministrationModel->get_biller_user();
            echo view('Views/aut/users_view', $dataStore);
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function update_biller_user() {
        $dataStore = $this->BillerModel->update_biller_user_data($this->request->getPost('user_id'), $this->request->getPost('status'));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function add_biller_user() {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            echo view('Views/aut/add_biller_user_view');
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function send_invite() {
        $dataIn = $this->request->getPost('form');
        $dataStore = $this->AdministrationModel->send_invite($dataIn);
        if ($dataStore["status"] == "success") {
            if ($dataIn["options"] == "phone") {
                sendInviteMessage($dataIn["phone"], $dataStore["user_name"], $this->session->get('display_name'));
            } else {
                sendInviteMail($dataIn["email_address"], $dataStore["user_name"], $this->session->get('display_name'));
            }
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function import() {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            echo view('Views/aut/import_view');
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    // Export data in TSV format 
    public function exportTSV($type, $data, $header = '') {
        // file name 
        $filename = $type . '.tsv';
        header("Content-Description: File Transfer");
        header('Content-type: application/tsv');
        header("Content-Disposition: attachment; filename=$filename");
        // file creation 
        $file = fopen('php://output', 'w');

        fputcsv($file, $header, chr(9));
        foreach ($data as $key => $line) {
            fputcsv($file, $line, chr(9));
        }
        fclose($file);
        exit;
    }

    public function export($type = '') {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            if ($type == "") {
                echo view('Views/aut/export_view');
            } else {
                if ($type == "customer") {
                    $dataStore = $this->CustomerModel->get_customer();
                    $header = array('ID', 'Name', 'Job ID', 'PO Number', 'Person', 'Email', 'Phone', 'Attn/Dpt', 'Mailing Address Line 1', 'Mailing Address Line 2', 'Service Address Line 1', 'Service Address Line 2', 'Discount', 'Minimum', 'State Tax', 'County Tax', 'Email Bill to', 'Notes');
                    $count = 0;
                    foreach ($dataStore as $customer_value) {
                        $dataStore[$count]["Mailing Address Line 1"] = $customer_value["address_mail_street"];
                        $dataStore[$count]["Mailing Address Line 2"] = $customer_value["address_mail_city"] . ',' . $customer_value["address_mail_state"] . ' ' . $customer_value["address_mail_zip5"];
                        $dataStore[$count]["Service Address Line 1"] = $customer_value["address_street"];
                        $dataStore[$count]["Service Address Line 2"] = $customer_value["address_city"] . ',' . $customer_value["address_state"] . ' ' . $customer_value["address_zip5"];
                        $dataStore[$count]["Discount"] = $customer_value["discount"] / 100;
                        $dataStore[$count]["Minimum"] = $customer_value["retainer"];
                        $dataStore[$count]["State Tax"] = $customer_value["tax_state"] / 100;
                        $dataStore[$count]["County Tax"] = $customer_value["tax_county"] / 100;
                        $dataStore[$count]["Email Bill to"] = $customer_value["email_to_list"];
                        $dataStore[$count]["Notes"] = $customer_value["notes"];
                        unset($dataStore[$count]["discount"], $dataStore[$count]["retainer"], $dataStore[$count]["email_to_list"], $dataStore[$count]["tax_state"], $dataStore[$count]["tax_county"], $dataStore[$count]["tax_county"], $dataStore[$count]["notes"]);
                        unset($dataStore[$count]["time_created"], $dataStore[$count]["address_mail_street"], $dataStore[$count]["address_street"], $dataStore[$count]["user_id_created"], $dataStore[$count]["time_updated"], $dataStore[$count]["user_id_updated"], $dataStore[$count]["biller_id"], $dataStore[$count]["status"]);
                        unset($dataStore[$count]["address_mail_city"], $dataStore[$count]["address_mail_state"], $dataStore[$count]["address_mail_zip5"], $dataStore[$count]["address_mail_zip4"], $dataStore[$count]["address_city"], $dataStore[$count]["address_state"], $dataStore[$count]["address_zip5"], $dataStore[$count]["address_zip4"]);
                        unset($dataStore[$count]["user_id"], $dataStore[$count]["expiration"], $dataStore[$count]["time_last_access"]);
                        $count++;
                    }
                }
                if ($type == "item") {
                    $dataStore = $this->AdministrationModel->get_item();
                    $header = array('ID', 'Name', 'Description', 'Bill Rate', 'Type', 'Can Discount?');
                    $count = 0;
                    foreach ($dataStore as $customer_value) {
                        $dataStore[$count]["Can Discount?"] = ($customer_value["can_discount"] == 1 ? 'Yes' : 'No');
                        unset($dataStore[$count]["status"], $dataStore[$count]["ct_id"], $dataStore[$count]["can_discount"], $dataStore[$count]["time_created"], $dataStore[$count]["user_id_created"], $dataStore[$count]["time_updated"], $dataStore[$count]["user_id_updated"]);
                        $count++;
                    }
                }
                $this->exportTSV(ucfirst($type), $dataStore, $header);
            }
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function subscription() {
        echo view('Views/aut/subscription_view');
    }

}
