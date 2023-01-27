<?php

namespace App\Controllers\sys;

include '../wepay.php';

use CodeIgniter\Controller;
use App\Models\PostpaymentModel;
use App\Models\CustomerModel;
use App\Models\BillerModel;
use App\Models\ChargeModel;
use WePay;

class Wepaycallback extends Controller {

    protected $helpers = ["url", "form", "general", "image"];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->PostpaymentModel = new PostpaymentModel();
        $this->CustomerModel = new CustomerModel();
        $this->BillerModel = new BillerModel();
        $this->ChargeModel = new ChargeModel();
    }

    public function index() {
        $body = @file_get_contents('php://input');
        $reference = explode("=", $body)[1];
        $dataStore = $this->PostpaymentModel->get_credit($reference);
        $biller_data = $this->BillerModel->get_biller_data($dataStore['biller_id']);
        $dataStoreInvoice = $this->PostpaymentModel->get_wepay_transaction($reference);
        $dataInvoice = \GuzzleHttp\json_decode($dataStoreInvoice['data'], true);
        $client_id = env('wepay.client_id');
        $client_secret = env('wepay.client_secret');
        $access_token = $stripe_user_id = $this->BillerModel->get_wepay_data($dataStore['biller_id'])['access_token'];
        Wepay::useStaging($client_id, $client_secret);
        $wepay = new WePay($access_token);
        $response = $wepay->request('/checkout', array(
            'checkout_id' => $reference
        ));
        if ($response->payment_error == "") {
            if ($dataStore['reference'] == $response->checkout_id) {
                $this->CustomerModel->update_balance($dataStore['customer_id'], $dataStore['amount']);
                $dataIn['status'] = 'Success';
                $this->PostpaymentModel->update_payment_status($dataIn, $dataStore['credit_id']);
                $invoice_ids = array();
                foreach ($dataInvoice as $value) {
                    $dataCreditIn['invoice_id'] = $value[0];
                    $dataCreditIn['customer_id'] = $value[1];
                    $dataCreditIn['amount'] = $value[2];
                    $dataCreditIn['credit_id'] = $dataStore['credit_id'];
                    $dataCreditIn['user_id_created'] = 0;
                    $invoice_ids[] = $value[0];
                    $this->PostpaymentModel->add_invoice_credit($dataCreditIn);
                }
            }
        } else {
            $dataIn['status'] = 'Failed';
            $this->PostpaymentModel->update_payment_status($dataIn, $dataStore['credit_id']);
            log_message('info', 'Payment have be fail.!');
        }
    }

}
