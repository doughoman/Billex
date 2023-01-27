<?php

namespace App\Controllers\aut;

use CodeIgniter\Controller;
use App\Libraries\Casscall;
use App\Models\ChargeModel;
use App\Models\CustomerModel;
use App\Models\AdministrationModel;
use App\Models\BillerModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Hashids\Hashids;
use Jenssegers\Optimus\Optimus;
use Stripe\Stripe;
use App\Models\PostpaymentModel;

class Charges extends Controller {

    protected $session;
    public $casscall;
    protected $helpers = ["url", "form", "general", "image", "cookie"];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->AdministrationModel = new AdministrationModel();
        $this->casscall = new \Casscall();
        $this->ChargeModel = new ChargeModel();
        $this->CustomerModel = new CustomerModel();
        $this->PostpaymentModel = new PostpaymentModel();
        $this->BillerModel = new BillerModel();
        $this->dompdf = new Dompdf();
        $this->hashids = new Hashids(env('hashids.salt'), 16);
        $this->optimus = new Optimus(1580030173, 59260789, 1163945558);
    }

    public function index() {
//        set_cookie('invoice_link','');
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $cust_id = $this->session->has('customer_id');
            if (!empty($cust_id)) {
                $dataStore = $this->CustomerModel->get_customer(base64_encode(encrypt($this->session->get('customer_id'))));
                $dataStore["charges_data"] = $this->CustomerModel->get_charges(base64_encode(encrypt($this->session->get('customer_id'))));
                $dataStore["recent_charges"] = $this->ChargeModel->get_recent_credits(base64_encode(encrypt($this->session->get('customer_id'))));
                $dataStore["recent_invoices"] = $this->ChargeModel->get_recent_invoices(base64_encode(encrypt($this->session->get('customer_id'))));
                $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
            }
            $dataStore["item_data"] = $this->AdministrationModel->get_item();
            $count = 0;
            foreach ($dataStore["item_data"] as $customer_value) {
                $dataStore["item_data"][$count]["type"] = $this->CustomerModel->get_charge_type($dataStore["item_data"][$count]["type"]);
                $count++;
            }
            $dataStore["chargetype_data"] = $this->ChargeModel->get_charge_type();
            $dataStore["customer_data"] = $this->CustomerModel->get_customer();
            $count = 0;
            foreach ($dataStore["customer_data"] as $customer_value) {
                $dataStore["customer_data"][$count]["customer_id"] = base64_encode(encrypt($customer_value["customer_id"]));
                $count++;
            }
            $dataStore["charges_invoice_data"] = $this->CustomerModel->get_invoice_charges(base64_encode(encrypt($this->session->get('customer_id'))));
            $dataStore["invoice_data"] = $this->CustomerModel->get_invoice();
            $dataStore["invoice_code"] = number_format($this->optimus->encode(intval($dataStore["invoice_data"]['invoice_id']) + 1), 0, '', '-');
            $dataStore["invoice_data"]['invoice_id'] = $this->hashids->encode($dataStore["invoice_data"]['invoice_id'] + 1);
            $dataStore["biller_data"] = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
            echo view('Views/aut/charges_view', $dataStore);
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function get_customer_data($id = '') {
        $dataStore = $this->CustomerModel->get_customer($id);
        $dataStore["charges_data"] = $this->CustomerModel->get_charges($id);
        $this->session->set('customer_id', decrypt(base64_decode($id)));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function get_pdf_file() {
        $customer_id = decrypt(base64_decode($this->request->getPost('customer_id')));
        $dataStore["invoice_data"] = $this->CustomerModel->get_invoice();
        $invoice_id = $dataStore["invoice_data"]['invoice_id'] + 1;
        $pdf_file_name = $this->hashids->encode(intval($this->session->get('biller_id')), intval($invoice_id), intval($customer_id)) . '.pdf';
        echo $pdf_file_name;
    }

    public function preview_invoice($html = '', $filename = '', $stream = TRUE, $paper = 'Letter', $orientation = "portrait") {
        ini_set('max_execution_time', 0);
        $dataStore = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataIn['invoice_number'] = $dataStore['invoice_number'] + 1;
        $dataInvoice['biller_id'] = $this->session->get("biller_id");
        $dataInvoice['invoice_number'] = $dataIn['invoice_number'];
        $dataInvoice['user_id_created'] = $this->session->get('login_userid');
        $dataStatus = $this->BillerModel->update_biller_data($dataIn);
        $email = $this->request->getPost('email');
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
        $file_name = "Invoice_" . date('m-d-Y') . '.pdf';
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('debugKeepTemp', TRUE);
        $options->set('isHtml5ParserEnabled', true);
        $dataStore = $this->CustomerModel->get_customer($this->request->getPost('customer_id'));
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore["charges_data"] = $this->CustomerModel->get_invoice_charges($this->request->getPost('customer_id'));
        $customer_id = decrypt(base64_decode($this->request->getPost('customer_id')));
        $dataStore["invoice_data"] = $this->CustomerModel->get_invoice();
        $invoice_id = $dataStore["invoice_data"]['invoice_id'] + 1;
        if ($this->request->getPost('myemail') == 1) {
            $pdf_file_name = $this->request->getPost('pdf_file_name');
        } else {
            $pdf_file_name = $this->hashids->encode(intval($this->session->get('biller_id')), intval($invoice_id), intval($customer_id)) . '.pdf';
        }
        $dataStore["invoice_code"] = number_format($this->optimus->encode(intval($dataStore["invoice_data"]['invoice_id']) + 1), 0, '', '-');
        $dataStore["invoice_data"]['invoice_id'] = $this->hashids->encode($dataStore["invoice_data"]['invoice_id'] + 1);
        $dataStore["biller_data"] = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataStore["recent_charges"] = $this->ChargeModel->get_recent_credits(base64_encode(encrypt($customer_id)));
        $dataStore["recent_invoices"] = $this->ChargeModel->get_recent_invoices(base64_encode(encrypt($customer_id)));
        $this->ChargeModel->add_invoice($dataInvoice, $customer_id);
        $html = view('Views/aut/template/invoice_template_view', $dataStore);
        $this->dompdf->setOptions($options);
        $this->dompdf->load_html($html);
        $this->dompdf->set_paper($paper, $orientation);
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));
        $file = $this->dompdf->output();
        $html_file_name = $this->hashids->encode(intval($this->session->get('biller_id')), intval($invoice_id), intval($customer_id)) . '.html';
        file_put_contents($file_name, $file);
        file_put_contents($pdf_file_name, $file);
        file_put_contents($html_file_name, $html);
        if ($this->request->getPost('status') == 1) {
            foreach ($email_array as $value) {
                $status = sendInvoice($file_name, $html, $value, $this->session->get('display_name'));
            }
        }
        uploadInvoiceS3($pdf_file_name, $html_file_name);
        unlink($file_name);
        unlink($pdf_file_name);
        unlink($html_file_name);
    }

    public function make_charge_payment() {
        $stripeData = $this->BillerModel->get_stripe_data();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/charges');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "amount=1000&currency=USD&source=tok_visa");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, env("stripe.secret_key") . ':' . '');

        $headers = array();
        $headers[] = 'Stripe-Account: ' . $stripeData['stripe_user_id'];
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result1 = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        echo '<pre>';
        print_r(\GuzzleHttp\json_decode($result1, true));
        echo '</pre>';
    }

    public function ajax_add_download() {
        $dataIn['charge_id'] = decrypt(base64_decode($this->request->getPost('charge_id')));
        $dataIn['info'] = $this->request->getPost('info');
        $dataIn['email_to'] = $this->request->getPost('email_to');
        $dataStore = $this->ChargeModel->add_download($dataIn);
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function invoice_history($invoice_id = '') {
        if ($invoice_id != '') {
            if (!is_numeric($invoice_id)) {
                $dataStore['invoice_id'] = $invoice_id;
                $dataStore['code'] = $invoice_id;
                $dataStore['error'] = 1;
                echo view('Views/pub/invoice_history_view', $dataStore);
            } else {
                if (isset($invoice_id) && !empty($invoice_id)) {
                    $dataStore['invoice_id'] = $invoice_id;
                    $dataStore['code'] = $invoice_id;
                    $invoice_id = $this->optimus->decode($invoice_id);
                    $dataStore['history_data'] = $this->ChargeModel->get_invoice($invoice_id);
                    $dataStore['customer_data'] = $this->ChargeModel->get_invoice_customer($invoice_id);
                    if (count($dataStore['history_data']) == 0) {
                        $dataStore['error'] = 1;
                        unset($dataStore['history_data']);
                    }
                    echo view('Views/pub/invoice_history_view', $dataStore);
                }
            }
        } else {
            echo view('Views/pub/invoice_history_view');
        }
    }

    public function check_invoice_code() {
        $invoice_code = $this->request->getPost('code');
        $invoice_id = $this->optimus->decode($invoice_code);
        $dataStore = $this->ChargeModel->get_invoice($invoice_id);
        if (count($dataStore) == 0) {
            $error = 1;
        } else {
            $error = 0;
        }
        echo json_encode($error);
    }

}
