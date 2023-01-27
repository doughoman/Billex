<?php

namespace App\Controllers\pub;

require '../wepay.php';

use CodeIgniter\Controller;
use App\Models\ChargeModel;
use App\Models\CustomerModel;
use App\Models\BillerModel;
use App\Models\PostpaymentModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Hashids\Hashids;
use Stripe\Stripe;
use WePay;

class Makepayment extends Controller {

    protected $session;
    public $casscall;
    protected $helpers = ["url", "form", "general", "image"];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->casscall = new \Casscall();
        $this->ChargeModel = new ChargeModel();
        $this->CustomerModel = new CustomerModel();
        $this->BillerModel = new BillerModel();
        $this->PostpaymentModel = new PostpaymentModel();
        $this->dompdf = new Dompdf();
        $this->hashids = new Hashids(env('hashids.salt'), 16);
    }

    public function index($customer_id = "") {
        $this->session->set(array('invoice_customer_id' => $customer_id));
        $customer_id = $this->hashids->decode($customer_id)[0];
        $dataStore = $this->CustomerModel->get_customer(base64_encode(encrypt($customer_id)));
        $dataStore['invoice_data'] = $this->PostpaymentModel->get_credit_invoice($customer_id);
        if ($dataStore['biller_id'] == 0) {
            $dataStore['biller_id'] = $dataStore['invoice_data'][0]['biller_id'];
        }
        $this->session->set(array('invoice_biller_id' => $dataStore['biller_id']));
        $dataStore['biller_data'] = $this->BillerModel->get_biller_data($this->session->get('invoice_biller_id'));
        $session_profile_data = array('display_name' => $dataStore['biller_data']["name"]);
        $this->session->set($session_profile_data);
        echo view('Views/pub/make_payment_view', $dataStore);
    }

    public function add_payment_invoice() {
        $dataIn = $this->request->getPost('invoice');
        $dataStore = $this->PostpaymentModel->add_payment_invoice($dataIn);
        $dataStore['id'] = base64_encode(encrypt($dataStore['id']));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function add_wepay_invoice() {
        $dataIn = $this->request->getPost('invoice');
        $dataStore = $this->PostpaymentModel->add_wepay_invoice($dataIn);
        $dataStore['id'] = base64_encode(encrypt($dataStore['id']));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function payment() {
        $biller_data = $this->BillerModel->get_biller_data($this->session->get('invoice_biller_id'));
        if ($biller_data['processor'] == "stripe") {
            $stripe_user_id = $this->BillerModel->get_stripe_data()['stripe_user_id'];
            \Stripe\Stripe::setApiKey(env('stripe.secret_key'));
            try {
                if (!isset($_POST['stripeToken']))
                    throw new Exception("The Stripe Token was not generated correctly");
                $customer_data = $this->CustomerModel->get_customer(base64_encode(encrypt($this->hashids->decode($this->session->get('invoice_customer_id'))[0])));
                $customer = \Stripe\Customer::create([
                            "description" => "Customer for " . $this->session->get('display_name'),
                            "source" => $this->request->getPost('stripeToken'), // obtained with Stripe.js
                            "email" => $this->request->getPost('stripeEmail'),
                            "name" => $customer_data['name'],
                            "phone" => $customer_data['contact_phone']
                        ])->id;
                $cus = \Stripe\Charge::create([
                            "amount" => $this->request->getPost('stripeAmount') * 100,
                            "currency" => "usd",
                            "customer" => $customer,
                            "source" => $stripe_user_id,
                            "receipt_email" => $this->request->getPost('stripeEmail'),
                            "description" => $this->request->getPost('stripeEmail')
                        ])->id;
                $dataIn['type'] = 'Card';
                $dataIn['reference'] = $cus;
                $dataIn['description'] = 'Stripe online payment';
                $dataIn['amount'] = $this->request->getPost('stripeAmount');
                $dataIn['biller_id'] = $this->session->get('invoice_biller_id');
                $dataIn['date_credit'] = date('Y-m-d');
                $dataIn['customer_id'] = $customer_data['customer_id'];
                $this->PostpaymentModel->add_post_payment($dataIn);
                $dataCreditIn['customer_id'] = $this->request->getPost('customer_id');
                $dataCreditIn['amount'] = $this->request->getPost('stripeAmount');
                $dataCreditIn['user_id_created'] = 0;
                $dataInvoiceIn['transaction_id'] = $cus;
                $this->PostpaymentModel->add_payment_invoice($dataInvoiceIn, $this->request->getPost('stripeId'));
                $this->PostpaymentModel->add_credit_details($dataCreditIn);
                sendTransactionMessage($customer_data['contact_phone'], $this->request->getPost('stripeEmail'), 0);
                return redirect()->to(base_url() . 'pay/' . $this->session->get('invoice_customer_id') . '?status=success');
            } catch (Exception $e) {
                
            }
        } else {
            $customer_data = $this->CustomerModel->get_customer(base64_encode(encrypt($this->hashids->decode($this->session->get('invoice_customer_id'))[0])));
            $dataStoreInvoice = $this->PostpaymentModel->get_wepay_transaction('', decrypt(base64_decode($this->request->getPost('stripeId'))));
            $dataInvoice = \GuzzleHttp\json_decode($dataStoreInvoice['data'], true);
            $invoice_ids = array();

            foreach ($dataInvoice as $value) {
                $invoice_ids[] = $value[0];
            }
            $dataInvoiceSotre = $this->PostpaymentModel->get_invoices($invoice_ids);
            $total = 0.00;
            if (count($dataInvoiceSotre) != 0) {
                $html = 'Invoices<BR>';
                foreach ($dataInvoiceSotre as $key => $value) {
                    foreach ($dataInvoice as $ivalue) {
                        if ($value['invoice_id'] == $ivalue[0]) {
                            $total = $total + floatval($ivalue[2]);
                            $html.='Invoice Number: ' . $value['invoice_number'] . ' <span style="margin:0px 15px;">-</span> $' . $ivalue[2] . '<BR>';
                        }
                    }
                }
                $html.= '<br>Total: $' . $total;
            } else {
                $html = 'Extra Payment<BR>';
                foreach ($dataInvoice as $ivalue) {
                    $total = $total + floatval($ivalue[2]);
                    $html.='Extra Payment From ' . $customer_data['name'] . ': $' . $ivalue[2] . '<BR>';
                }
                $html.= '<br>Total: $' . $total;
            }

            $client_id = env('wepay.client_id');
            $client_secret = env('wepay.client_secret');
            $access_token = $stripe_user_id = $this->BillerModel->get_wepay_data($this->session->get('invoice_biller_id'))['access_token'];
            Wepay::useStaging($client_id, $client_secret);
            $wepay = new WePay($access_token);
            $account_id = $wepay->request('account/create/', array(
                        'name' => $this->session->get('display_name'),
                        'description' => "Customer for " . $this->session->get('display_name')
                    ))->account_id;
            $response = $wepay->request('checkout/create', array(
                'account_id' => $account_id,
                'amount' => $this->request->getPost('stripeAmount'),
                "short_description" => "Payment for seleted invoice",
                'currency' => 'USD',
                'type' => 'goods',
                'callback_uri' => 'http://hitesh.dev.billex.net/sys/wepaycallback?account_id=2057580846',
                "auto_release" => true,
                'hosted_checkout' => array(
                    'require_shipping' => false,
                    'redirect_uri' => base_url(),
                    "theme_object" => array(
                        "name" => "Billex",
                        "primary_color" => "327052",
                        "background_color" => "f9f9fc",
                        "button_color" => "40926a",
                        "secondary_color" => "607d8b"
                    ),
                ),
                'email_message' => array(
                    'to_payer' => $html,
                    'to_payee' => $html
                )
            ));
            $dataIn['type'] = 'Card';
            $dataIn['reference'] = $response->checkout_id;
            $dataIn['description'] = 'Stripe online payment';
            $dataIn['amount'] = $this->request->getPost('stripeAmount');
            $dataIn['biller_id'] = $this->session->get('invoice_biller_id');
            $dataIn['date_credit'] = date('Y-m-d');
            $dataIn['customer_id'] = $customer_data['customer_id'];
            $this->PostpaymentModel->add_post_payment($dataIn);
            $dataCreditIn['customer_id'] = $this->request->getPost('customer_id');
            $dataCreditIn['amount'] = $this->request->getPost('stripeAmount');
            $dataCreditIn['user_id_created'] = 0;
            $dataInvoiceIn['transaction_id'] = $response->checkout_id;
            $this->PostpaymentModel->add_wepay_invoice($dataInvoiceIn, $this->request->getPost('stripeId'));
            $this->PostpaymentModel->add_credit_details($dataCreditIn);
            return redirect()->to($response->hosted_checkout->checkout_uri);
        }
    }

}
