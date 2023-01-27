<?php

namespace App\Controllers\aut;

use CodeIgniter\Controller;
use App\Models\PostpaymentModel;
use App\Models\CustomerModel;
use App\Models\ChargeModel;

class PostPayment extends Controller {

    protected $session;
    public $casscall;
    protected $helpers = ["url", "form", "general", "image"];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->PostpaymentModel = new PostpaymentModel();
        $this->CustomerModel = new CustomerModel();
        $this->ChargeModel = new ChargeModel();
        $this->session->start();
    }

    public function index() {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore['customer_data'] = $this->PostpaymentModel->get_post_payment_customer();
            echo view('Views/aut/post_payment_view', $dataStore);
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function add_post_payment() {
        $dataIn = $this->request->getPost('form');
        $dataIn['biller_id'] = $this->session->get('biller_id');
        $dataIn['user_id_created'] = $this->session->get('login_userid');
        $dataIn['date_credit'] = date('Y-m-d', strtotime($dataIn['date_credit']));
        if (isset($dataIn['credit_id']) && !empty($dataIn['credit_id'])) {
            $dataStore = $this->PostpaymentModel->add_post_payment($dataIn, $dataIn['credit_id']);
            $this->CustomerModel->update_balance(decrypt(base64_decode($dataIn['customer_id'])), $dataIn['amount']);
        } else {
            $dataStore = $this->PostpaymentModel->add_post_payment($dataIn);
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function add_invoice_credit() {
        $dataIn = $this->request->getPost('customer_invoice');
        $invoice_ids = array();
        foreach ($dataIn as $value) {
            $dataCreditIn['invoice_id'] = $value[0];
            $dataCreditIn['customer_id'] = $value[1];
            $dataCreditIn['amount'] = $value[2];
            $dataStore = $this->PostpaymentModel->add_invoice_credit($dataCreditIn);
            $invoice_ids[] = $value[0];
        }
        $dataStoreDownload = $this->ChargeModel->get_download_charge($invoice_ids);
        foreach ($dataStoreDownload as $cvalue) {
            $email = $cvalue['email_to'];
            $email_array = array();
            if (strpos($email, ';') !== false) {
                $email_array = explode(';', $email);
            } else {
                $email_array[] = $email;
            }
            if (strpos($email, ',') !== false) {
                $email_array = explode(',', $email);
            } else {
                if (!count($email_array) >= 1) {
                    $email_array[] = $email;
                }
            }
            $html = view('Views/aut/template/download_charge_template_view', $cvalue);
            foreach ($email_array as $evalue) {
                sendDownloadCharge($evalue, $html, $this->session->get('display_name'));
            }
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function edit($credit_id = '') {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore['charge_data'] = $this->PostpaymentModel->get_payment_credit($credit_id);
            $this->CustomerModel->update_balance($dataStore['charge_data']['customer_id'], -$dataStore['charge_data']['amount']);
            $dataStore['customer_data'] = $this->PostpaymentModel->get_post_payment_customer($dataStore['charge_data']['customer_id']);
            echo view('Views/aut/post_payment_view', $dataStore);
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function get_invoice_customer() {
        $invoiceNumber = $this->request->getPost('invoice_number');
        $dataStore = $this->PostpaymentModel->get_invoice_customer($invoiceNumber);
        echo \GuzzleHttp\json_encode($dataStore);
    }

}
