<?php

namespace App\Controllers\aut;

include '../c2mAPIRest.php';

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
use App\Models\PostpaymentModel;
use App\Models\BillchargesModel;
use c2mAPIRest;
use App\Libraries\Name_Parser;

class Billcharges extends Controller {

    protected $session;
    public $casscall;
    protected $helpers = ["url", "form", "general", "image"];

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
        $this->hashids = new Hashids(env('hashids.salt'), 16);
        $this->BillchargesModel = new BillchargesModel();
        $this->optimus = new Optimus(1580030173, 59260789, 1163945558);
        $this->c2m = new c2mAPIRest();
        $this->c2m->c2mAPIRestMain(env('click2mail.username'), env('click2mail.password'), "stage"); //Change stage to live for production
    }

    public function index($s = '') {
        if (isset($_REQUEST['q']) && $_REQUEST['q'] == 'initialize') {
            $session_items = array('bill_date');
            $this->session->remove($session_items);
        }
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore['bill_charges'] = $this->BillchargesModel->get_bill_charges();
            $dataStore['batch_data'] = $this->BillchargesModel->get_batch_data();
            $dataStore["biller_data"] = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
            echo view('Views/aut/bill_charges_view', $dataStore);
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function get_charge_on_date() {
        $dateIn = $this->request->getPost('date');
        $session_search_data = array('bill_date' => $dateIn);
        $this->session->set($session_search_data);
        $dataStore = $this->BillchargesModel->get_bill_charges($dateIn);
        $count = 0;
        foreach ($dataStore as $customer_value) {
            $dataStore[$count]["customer_id"] = base64_encode(encrypt($customer_value["customer_id"]));
            $count++;
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function bill_charges_preview() {
        $dataIn = $this->request->getPost('customer_ids');
        $ids = '';
        foreach ($dataIn as $value) {
            $ids .=decrypt(base64_decode($value)) . ',';
        }
        $ids = rtrim($ids, ',');
        $dataStore["biller_data"] = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataStore["invoice_data"] = $this->CustomerModel->get_invoice();
        $dataStore["invoice_code"] = number_format($this->optimus->encode(intval($dataStore["invoice_data"]['invoice_id']) + 1), 0, '', '-');
        $dataStore["invoice_data"]['invoice_id'] = $this->hashids->encode($dataStore["invoice_data"]['invoice_id'] + 1);
        $dataStore["customer_data"] = $this->BillchargesModel->get_preview_customer($ids);

        $html = view('Views/aut/preview_charges_invoice_view', $dataStore);
        echo $html;
    }

    public function charge_preview() {
        $customer_id = $this->request->getPost('customer_id');
        $dataStore = $this->CustomerModel->get_customer($customer_id);
        $dataStore["customer_id"] = base64_encode(encrypt($dataStore["customer_id"]));
        $dataStore["recent_charges"] = $this->ChargeModel->get_recent_credits($customer_id);
        $dataStore["recent_invoices"] = $this->ChargeModel->get_recent_invoices($customer_id);
        $dataStore["charges_data"] = $this->CustomerModel->get_charges($customer_id);
        $dataStore["charges_invoice_data"] = $this->CustomerModel->get_invoice_charges($customer_id);
        $dataStore["invoice_data"] = $this->CustomerModel->get_invoice();
        $dataStore["invoice_code"] = number_format($this->optimus->encode(intval($dataStore["invoice_data"]['invoice_id']) + 1), 0, '', '-');
        $dataStore["invoice_data"]['invoice_id'] = $this->hashids->encode($dataStore["invoice_data"]['invoice_id'] + 1);
        $dataStore["biller_data"] = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataStore["preview"] = 1;
        $html = view('Views/aut/preview_invoice_view', $dataStore);
        echo $html;
    }

    public function bill_charges_process($customer_ids = array(), $unsend_customer_ids = array(), $status = true, $count = '', $html = '', $filename = '', $stream = TRUE, $paper = 'Letter', $orientation = "portrait") {
        $html_files_name = array();
        ini_set('max_execution_time', 0);
        $dataIn['biller_id'] = $this->session->get('biller_id');
        if ($count == "") {
            $dataIn['invoice_count'] = $this->request->getPost('count');
        } else {
            $dataIn['invoice_count'] = $count;
        }
        $dataIn['invoice_count'] = $this->request->getPost('count');
        $dataIn['user_id_created'] = $this->session->get('login_userid');
        if ($status == true && $count != '') {
            $batch_id = $this->BillchargesModel->add_invoice_batch($dataIn)['ifb_id'];
        }
        $dataStore = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataIn1['invoice_number'] = $dataStore['invoice_number'] + 1;
        $dataInvoice['biller_id'] = $this->session->get("biller_id");
        $dataInvoice['invoice_number'] = $dataIn1['invoice_number'];
        $dataInvoice['user_id_created'] = $this->session->get('login_userid');
        $ids = '';
        if (count($customer_ids) == 0) {
            $customerIds = $this->request->getPost('customer_ids');
        } else {
            $customerIds = $customer_ids;
        }
        foreach ($customerIds as $value) {
            $ids .=decrypt(base64_decode($value)) . ',';
        }
        $ids = rtrim($ids, ',');
        $dataStore = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataStore["biller_data"] = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataStore["invoice_data"] = $this->CustomerModel->get_invoice();
        $dataStore["invoice_code"] = number_format($this->optimus->encode(intval($dataStore["invoice_data"]['invoice_id']) + 1), 0, '', '-');
        $dataStore["invoice_op_id"] = $dataStore["invoice_data"]['invoice_id'];
        $dataStore["invoice_data"]['invoice_id'] = $this->hashids->encode($dataStore["invoice_data"]['invoice_id'] + 1);
        $dataStore["customer_data"] = $this->BillchargesModel->get_preview_customer($ids);
        $invoice_id = intval($dataStore["invoice_data"]['invoice_id']) + 1;
        $file_name = "Batch_Invoice_" . date('m-d-Y') . '.pdf';
        $html2 = view('Views/aut/template/charges_invoice_template_view', $dataStore);
        if (count($unsend_customer_ids) != 0) {
            foreach ($unsend_customer_ids as $value) {
                $file_name = "invoice";
                $billerData = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
                $dataStatus = $this->BillerModel->update_biller_data(array('invoice_number' => $billerData['invoice_number'] + 1));
                $dataIn['invoice_number'] = $billerData['invoice_number'] + 1;
                $dataInvoice['biller_id'] = $this->session->get("biller_id");
                $dataInvoice['invoice_number'] = $dataIn['invoice_number'];
                $dataInvoice['user_id_created'] = $this->session->get('login_userid');
                $dataStore = $this->CustomerModel->get_customer($value);
                $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
                $dataStore["charges_data"] = $this->CustomerModel->get_invoice_charges($value);
                $customer_id = decrypt(base64_decode($value));
                $dataStore["invoice_data"] = $this->CustomerModel->get_invoice();
                $invoice_id = $dataStore["invoice_data"]['invoice_id'] + 1;
                $dataStore["invoice_code"] = number_format($this->optimus->encode(intval($dataStore["invoice_data"]['invoice_id']) + 1), 0, '', '-');
                $dataStore["invoice_data"]['invoice_id'] = $this->hashids->encode($dataStore["invoice_data"]['invoice_id'] + 1);
                $dataStore["biller_data"] = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
                $dataStore["recent_charges"] = $this->ChargeModel->get_recent_credits(base64_encode(encrypt($customer_id)));
                $dataStore["recent_invoices"] = $this->ChargeModel->get_recent_invoices(base64_encode(encrypt($customer_id)));
                $dataStore["page_break"] = 1;
                $this->ChargeModel->add_invoice($dataInvoice, decrypt(base64_decode($value)));
                $html = view('Views/aut/template/invoice_template_view', $dataStore);
                $dataStore["page_break"] = 0;
                $pdf_html = view('Views/aut/template/invoice_template_view', $dataStore);
                $this->dompdf = new Dompdf();
                $options = new Options();
                $options->set('isRemoteEnabled', TRUE);
                $options->set('debugKeepTemp', FALSE);
                $options->set('isHtml5ParserEnabled', true);
                $this->dompdf->setOptions($options);
                $this->dompdf->load_html($pdf_html);
                $this->dompdf->set_paper($paper, $orientation);
                $this->dompdf->render();
                $file = $this->dompdf->output();
                $pdf_file_name2 = $this->hashids->encode(intval($this->session->get('biller_id')), intval($invoice_id), intval($customer_id)) . '.pdf';
                $html_file_name2 = $this->hashids->encode(intval($this->session->get('biller_id')), intval($invoice_id), intval($customer_id)) . '.html';
                $html_files_name[] = $html_file_name2;
                file_put_contents($file_name, $file);
                file_put_contents($pdf_file_name2, $file);
                file_put_contents($html_file_name2, $html);
                uploadInvoiceS3($pdf_file_name2, $html_file_name2);
                unlink($file_name);
                unlink($pdf_file_name2);
                unlink($html_file_name2);
                unset($this->dompdf, $options);
            }
        }
        unset($dataStore);
        if ($status == true && $count != '') {
            $this->dompdf = new Dompdf();
            $options = new Options();
            $options->set('isRemoteEnabled', TRUE);
            $options->set('debugKeepTemp', FALSE);
            $options->set('isHtml5ParserEnabled', true);
            $this->dompdf->setOptions($options);
            $this->dompdf->load_html($html2);
            $this->dompdf->set_paper($paper, $orientation);
            $this->dompdf->render();
            $file = $this->dompdf->output();
            if ($status == true && $count != '') {
                $pdf_file_name = 'batch_' . $this->hashids->encode(intval($this->session->get('biller_id')), intval($batch_id)) . '.pdf';
                $html_file_name = $batch_html_file = 'batch_' . $this->hashids->encode(intval($this->session->get('biller_id')), intval($batch_id)) . '.html';
                file_put_contents($file_name, $file);
                file_put_contents($pdf_file_name, $file);
                file_put_contents($html_file_name, $html2);
                uploadInvoiceS3($pdf_file_name, $html_file_name);
            }
            unlink($file_name);
            unlink($pdf_file_name);
            unlink($html_file_name);
            unset($this->dompdf);
            if (count($html_files_name) == 0) {
                $html_files_name[] = $batch_html_file;
                return $html_files_name;
            } else {
                return $html_files_name;
            }
        }
    }

    public function send_invoice_email($customerID = '', $html = '', $filename = '', $stream = TRUE, $paper = 'Letter', $orientation = "portrait") {
        $this->dompdf = new Dompdf();
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('debugKeepTemp', FALSE);
        $options->set('isHtml5ParserEnabled', true);
        $dataStore = array();
        $billerData = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataIn['invoice_number'] = $billerData['invoice_number'] + 1;
        $dataInvoice['biller_id'] = $this->session->get("biller_id");
        $dataInvoice['invoice_number'] = $dataIn['invoice_number'];
        $dataInvoice['user_id_created'] = $this->session->get('login_userid');
        $dataStore = $this->CustomerModel->get_customer($customerID);
        $dataStatus = $this->BillerModel->update_biller_data($dataIn);
        $file_name = "Invoice_" . date('m-d-Y') . '.pdf';
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore["charges_data"] = $this->CustomerModel->get_invoice_charges($customerID);
        $customer_id = decrypt(base64_decode($customerID));
        $dataStore["invoice_data"] = $this->CustomerModel->get_invoice();
        $invoice_id = $dataStore["invoice_data"]['invoice_id'] + 1;
        $dataStore["invoice_code"] = number_format($this->optimus->encode(intval($dataStore["invoice_data"]['invoice_id']) + 1), 0, '', '-');
        $dataStore["invoice_data"]['invoice_id'] = $this->hashids->encode($dataStore["invoice_data"]['invoice_id'] + 1);
        $dataStore["biller_data"] = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataStore["recent_charges"] = $this->ChargeModel->get_recent_credits(base64_encode(encrypt($customer_id)));
        $dataStore["recent_invoices"] = $this->ChargeModel->get_recent_invoices(base64_encode(encrypt($customer_id)));
        if (isset($dataStore['email_to_list']) && $dataStore['email_to_list'] != "") {
            $this->ChargeModel->add_invoice($dataInvoice, $customer_id);
        }
        $html = view('Views/aut/template/invoice_template_view', $dataStore);
        $this->dompdf->setOptions($options);
        $this->dompdf->load_html($html);
        $this->dompdf->set_paper($paper, $orientation);
        $this->dompdf->render();
        $file = $this->dompdf->output();
        $pdf_file_name = $this->hashids->encode(intval($this->session->get('biller_id')), intval($invoice_id), intval($customer_id)) . '.pdf';
        $html_file_name = $html_file_name2 = $this->hashids->encode(intval($this->session->get('biller_id')), intval($invoice_id), intval($customer_id)) . '.html';
        unset($this->dompdf);
        if (isset($dataStore['email_to_list']) && $dataStore['email_to_list'] != "") {
            file_put_contents($pdf_file_name, $file);
            file_put_contents($html_file_name, $html);
            file_put_contents($file_name, $file);
            $email = $dataStore['email_to_list'];
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
            foreach ($email_array as $value) {
                $status = sendInvoice($file_name, $html, $value, $this->session->get('display_name'));
            }
            uploadInvoiceS3($pdf_file_name, $html_file_name);
            unlink($pdf_file_name);
            unlink($html_file_name);
            unlink($file_name);
        } else {
            return $customerID;
        }
    }

    public function send_invoice_USPS($customerID = '', $add_invoice = 0, $html = '', $filename = '', $stream = TRUE, $paper = 'Letter', $orientation = "portrait") {
        $this->dompdf = new Dompdf();
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('debugKeepTemp', FALSE);
        $options->set('isHtml5ParserEnabled', true);
        $dataStore = array();
        $billerData = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataIn['invoice_number'] = $billerData['invoice_number'] + 1;
        $dataInvoice['biller_id'] = $this->session->get("biller_id");
        $dataInvoice['invoice_number'] = $dataIn['invoice_number'];
        $dataInvoice['user_id_created'] = $this->session->get('login_userid');
        $dataStore = $this->CustomerModel->get_customer($customerID);
        $dataStatus = $this->BillerModel->update_biller_data($dataIn);
        $file_name = "Invoice_" . date('m-d-Y') . '.pdf';
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore["charges_data"] = $this->CustomerModel->get_invoice_charges($customerID);
        $customer_id = decrypt(base64_decode($customerID));
        $dataStore["invoice_data"] = $this->CustomerModel->get_invoice();
        $invoice_id = $dataStore["invoice_data"]['invoice_id'] + 1;
        $dataStore["invoice_code"] = number_format($this->optimus->encode(intval($dataStore["invoice_data"]['invoice_id']) + 1), 0, '', '-');
        $dataStore["invoice_data"]['invoice_id'] = $this->hashids->encode($dataStore["invoice_data"]['invoice_id'] + 1);
        $dataStore["biller_data"] = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataStore["recent_charges"] = $this->ChargeModel->get_recent_credits(base64_encode(encrypt($customer_id)));
        $dataStore["recent_invoices"] = $this->ChargeModel->get_recent_invoices(base64_encode(encrypt($customer_id)));
        if ($dataStore["address_mail_street"] != "") {
            if ($add_invoice != 0) {
                $this->ChargeModel->add_invoice($dataInvoice, $customer_id);
            }
        }
        $html = view('Views/aut/template/invoice_template_view', $dataStore);
        $this->dompdf->setOptions($options);
        $this->dompdf->load_html($html);
        $this->dompdf->set_paper($paper, $orientation);
        $this->dompdf->render();
        $file = $this->dompdf->output();
        $pdf_file_name = $this->hashids->encode(intval($this->session->get('biller_id')), intval($invoice_id), intval($customer_id)) . '.pdf';
        $html_file_name = $html_file_name2 = $this->hashids->encode(intval($this->session->get('biller_id')), intval($invoice_id), intval($customer_id)) . '.html';
        unset($this->dompdf);
        $this->name_parser = new \Name_Parser($dataStore['name']);
        if (isset($dataStore["address_mail_street"]) && $dataStore["address_mail_street"] != "") {
            $this->c2m->addAddress(ucfirst($this->name_parser->first), ucfirst($this->name_parser->last), $this->session->get('display_name'), $dataStore["address_mail_street"], "", $dataStore['address_mail_city'], $dataStore['address_mail_state'], $dataStore['address_mail_zip5'], "USA");
            $output = $this->c2m->runAll("Letter 8.5 x 11", "Address on First Page", "Next Day", "#10 Double Window", "Black and White", "White 24#", "Printing One side", $pdf_file_name, $this->c2m->createAddressList());
            $finalData['statusUrl'] = $output->statusUrl;
            $finalData['id'] = $output->id;
            $finalData['address_list_id'] = $this->c2m->addressListId;
            $finalData['job_id'] = $this->c2m->jobId;
            $this->c2m->clearJob();
            file_put_contents($pdf_file_name, $file);
            file_put_contents($html_file_name, $html);
            file_put_contents($file_name, $file);
            uploadInvoiceS3($pdf_file_name, $html_file_name);
            unlink($file_name);
            unlink($pdf_file_name);
            unlink($html_file_name);
        } else {
            return $customerID;
        }
    }

    public function send_invoice_pdf($customerID = '', $status = 0, $html = '', $filename = '', $stream = TRUE, $paper = 'Letter', $orientation = "portrait") {
        $this->dompdf = new Dompdf();
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('debugKeepTemp', FALSE);
        $options->set('isHtml5ParserEnabled', true);
        $dataStore = array();
        $billerData = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataIn['invoice_number'] = $billerData['invoice_number'] + 1;
        $dataInvoice['biller_id'] = $this->session->get("biller_id");
        $dataInvoice['invoice_number'] = $dataIn['invoice_number'];
        $dataInvoice['user_id_created'] = $this->session->get('login_userid');
        $dataStore = $this->CustomerModel->get_customer($customerID);
        $dataStatus = $this->BillerModel->update_biller_data($dataIn);
        $file_name = "Invoice_" . date('m-d-Y') . '.pdf';
        $dataStore['customer_id'] = base64_encode(encrypt($dataStore['customer_id']));
        $dataStore["charges_data"] = $this->CustomerModel->get_invoice_charges($customerID);
        $customer_id = decrypt(base64_decode($customerID));
        $dataStore["invoice_data"] = $this->CustomerModel->get_invoice();
        $invoice_id = $dataStore["invoice_data"]['invoice_id'] + 1;
        $dataStore["invoice_code"] = number_format($this->optimus->encode(intval($dataStore["invoice_data"]['invoice_id']) + 1), 0, '', '-');
        $dataStore["invoice_data"]['invoice_id'] = $this->hashids->encode($dataStore["invoice_data"]['invoice_id'] + 1);
        $dataStore["biller_data"] = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
        $dataStore["recent_charges"] = $this->ChargeModel->get_recent_credits(base64_encode(encrypt($customer_id)));
        $dataStore["recent_invoices"] = $this->ChargeModel->get_recent_invoices(base64_encode(encrypt($customer_id)));
        if ($status == 0) {
            $this->ChargeModel->add_invoice($dataInvoice, $customer_id);
        }
        $html = view('Views/aut/template/invoice_template_view', $dataStore);
        $this->dompdf->setOptions($options);
        $this->dompdf->load_html($html);
        $this->dompdf->set_paper($paper, $orientation);
        $this->dompdf->render();
        $file = $this->dompdf->output();
        $pdf_file_name = $this->hashids->encode(intval($this->session->get('biller_id')), intval($invoice_id), intval($customer_id)) . '.pdf';
        $html_file_name = $html_file_name2 = $this->hashids->encode(intval($this->session->get('biller_id')), intval($invoice_id), intval($customer_id)) . '.html';
        file_put_contents($pdf_file_name, $file);
        file_put_contents($html_file_name, $html);
        file_put_contents($file_name, $file);
        uploadInvoiceS3($pdf_file_name, $html_file_name);
        unlink($file_name);
        unlink($pdf_file_name);
        unlink($html_file_name);
        unset($this->dompdf);
        return $html_file_name2;
    }

    public function USPS_mail($html = '', $filename = '', $stream = TRUE, $paper = 'Letter', $orientation = "portrait") {
        ini_set('max_execution_time', 0);
        if (isset($_REQUEST['bill_charges']) && $_REQUEST['bill_charges'] == 1) {
            $type = $this->request->getPost('type');
            $customer_ids = $this->request->getPost('customer_id');
            if ($type[0] == "email_cust") {
                $dataIn['invoice_email'] = 1;
            }
            if ($type[0] == "nemail_cust") {
                $dataIn['invoice_email'] = 2;
            }
            if ($type[1] == "usps_type") {
                $dataIn['invoice_usps'] = 1;
            }
            if ($type[1] == "ausps_cust") {
                $dataIn['invoice_usps'] = 2;
            }
            if ($type[1] == "nusps_cust") {
                $dataIn['invoice_usps'] = 3;
            }
            if ($type[2] == "print_invoice") {
                $dataIn['invoice_pdf'] = 1;
            }
            if ($type[2] == "aprint_invoice") {
                $dataIn['invoice_pdf'] = 2;
            }
            $this->BillerModel->update_biller_data($dataIn);
            $status = 0;
            $html_file = array();
            $unsend_customer_ids = array();
            if ($type[0] == "nemail_cust" && $type[1] == "nusps_cust" && $type[2] == "aprint_invoice") {
                $html_file[] = $this->bill_charges_process($this->request->getPost('customer_id'), $this->request->getPost('customer_id'), true, $this->request->getPost('count'));
            } else {
                if ($type[2] == "aprint_invoice") {
                    $html_file[] = $this->bill_charges_process($this->request->getPost('customer_id'), array(), true, $this->request->getPost('count'));
                }
                if ($type[0] == "email_cust") {
                    foreach ($this->request->getPost('customer_id') as $value) {
                        $unsend_emailids[] = $this->send_invoice_email($value);
                    }
                    if ($type[1] == "ausps_cust") {
                        foreach ($this->request->getPost('customer_id') as $uscustid) {
                            $unsend_customer_ids[] = $uspsids[] = $this->send_invoice_USPS($uscustid, 1);
                        }
                        if (count(array_unique($uspsids)) != 0) {
                            if ($type[2] == "print_invoice") {
                                $status = 1;
                                $uspsids = array_filter(array_unique($uspsids));
                                foreach ($uspsids as $upid) {
                                    $html_file[] = $this->send_invoice_pdf($upid);
                                }
                            }
                        }
                        $unsend_emailids = array();
                    }
                    if (count($unsend_emailids) != 0) {
                        if ($type[1] == "usps_type") {
                            $unsend_emailids = array_filter($unsend_emailids);
                            foreach ($unsend_emailids as $unid) {
                                $unsend_customer_ids[] = $unsend_uspsids[] = $this->send_invoice_USPS($unid, 1);
                            }
                            if (count(array_unique($unsend_uspsids)) != 0) {
                                if ($type[2] == "print_invoice") {
                                    $unsend_uspsids = array_filter(array_unique($unsend_uspsids));
                                    foreach ($unsend_uspsids as $upid) {
                                        $html_file[] = $this->send_invoice_pdf($upid);
                                    }
                                }
                            }
                        } else {
                            if (count(array_unique($unsend_emailids)) != 0) {
                                if ($type[2] == "print_invoice") {
                                    $unsend_emailids = array_filter(array_unique($unsend_emailids));
                                    foreach ($unsend_emailids as $ueid) {
                                        if ($status == 0) {
                                            $html_file[] = $this->send_invoice_pdf($ueid);
                                        } else {
                                            $html_file[] = $this->send_invoice_pdf($ueid, 0);
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if ($type[1] == "usps_type" || $type[1] == "ausps_cust") {
                        foreach ($this->request->getPost('customer_id') as $usid) {
                            $unsend_customer_ids[] = $uspsids[] = $this->send_invoice_USPS($usid, 1);
                        }
                        if (count(array_unique($uspsids)) != 0) {
                            if ($type[2] == "print_invoice") {
                                $uspsids = array_filter(array_unique($uspsids));
                                foreach ($uspsids as $upid) {
                                    $html_file[] = $this->send_invoice_pdf($upid);
                                }
                            }
                        }
                    }
                }
                if ($type[2] == "aprint_invoice") {
                    if ($type[0] == "email_cust" || ($type[1] == "usps_type" || $type[1] == "ausps_cust")) {
                        $this->bill_charges_process($this->request->getPost('customer_id'), array_filter(array_unique($unsend_customer_ids)), false);
                    }
                }
            }
            echo \GuzzleHttp\json_encode($html_file);
        } else {
            $this->send_invoice_USPS($this->request->getPost('customer_id'), 1);
        }
    }

    public function edit_unbill_charges() {
        $customer_id = decrypt(base64_decode($this->request->getPost('customer_id')));
        $this->session->set('customer_id', $customer_id);
    }

    public function get_html() {
        $urls = $this->request->getPost('url');
        $response1 = '';
        foreach ($urls as $url) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Accept: */*",
                    "Accept-Encoding: gzip, deflate",
                    "Cache-Control: no-cache",
                    "Connection: keep-alive",
                    "Host: billex.s3.amazonaws.com",
                    "User-Agent: PostmanRuntime/7.17.1",
                    "cache-control: no-cache"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                $response1.=$response;
            }
            unset($curl);
        }
        echo $response1;
    }

}
