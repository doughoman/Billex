<?php

namespace App\Controllers\sys;

use CodeIgniter\Controller;
use App\Models\PostpaymentModel;
use App\Models\CustomerModel;
use App\Models\BillerModel;
use Stripe\Stripe;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\ChargeModel;

class Stripecallback extends Controller {

    protected $helpers = ["url", "form", "general", "image"];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->PostpaymentModel = new PostpaymentModel();
        $this->CustomerModel = new CustomerModel();
        $this->BillerModel = new BillerModel();
        $this->ChargeModel = new ChargeModel();
        Stripe::setApiKey(env('stripe.secret_key'));
        $this->dompdf = new Dompdf();
    }

    public function index() {
        $body = @file_get_contents('php://input');
        $event_json = json_decode($body);
        $event_data = $event_json->data;
        $transction_details = $event_data->object;
        $reference = $transction_details->id;
        $dataStore = $this->PostpaymentModel->get_credit($reference);
        $biller_data = $this->BillerModel->get_biller_data($dataStore['biller_id']);
        $dataStoreInvoice = $this->PostpaymentModel->get_stripe_transaction($reference);
        $dataInvoice = \GuzzleHttp\json_decode($dataStoreInvoice['data'], true);
        if ($transction_details->status == "succeeded") {
            if ($dataStore['reference'] == $transction_details->id) {
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
                        sendDownloadCharge($evalue, $html, $biller_data['name']);
                    }
                }
                $email = $transction_details->description;
                $invoice = $transction_details->receipt_url;
                sendTransactionMessage($dataStore['contact_phone'], '', 1, $transction_details->id);
                $file_name = "Invoice.pdf";
                $options = new Options();
                $options->set('isRemoteEnabled', TRUE);
                $options->set('debugKeepTemp', TRUE);
                $options->set('isHtml5ParserEnabled', true);
                $html = $this->getUrlContent($invoice);
                $this->dompdf->setOptions($options);
                $this->dompdf->load_html($html);
                $this->dompdf->set_paper('Letter', 'portrait');
                $this->dompdf->render();
                $this->dompdf->stream($file_name, array("Attachment" => false));
                $file = $this->dompdf->output();
                file_put_contents($file_name, $file);
                sendInvoice($file_name, $html, $email, "Billex");
                unlink($file_name);
                unlink("cookies.txt");
            }
        } else {
            $dataIn['status'] = 'Failed';
            $this->PostpaymentModel->update_payment_status($dataIn, $dataStore['credit_id']);
            log_message('info', 'Payment have be fail.!');
        }
    }

    function getUrlContent($url) {
        fopen("cookies.txt", "w");
        $parts = parse_url($url);
        $host = $parts['host'];
        $ch = curl_init();
        $header = array('GET /1575051 HTTP/1.1',
            "Host: {$host}",
            'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language:en-US,en;q=0.8',
            'Cache-Control:max-age=0',
            'Connection:keep-alive',
            'Host:adfoc.us',
            'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36',
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);

        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}
