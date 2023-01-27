<?php

namespace App\Controllers\aut;

use CodeIgniter\Controller;
use App\Models\PrintdepositModel;

class Printdeposit extends Controller {

    protected $session;
    public $casscall;
    protected $helpers = ["url", "form", "general", "image"];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->PrintdepositModel = new PrintdepositModel();
        $this->session->start();
    }

    public function index() {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore['customer_data'] = $this->PrintdepositModel->get_deposit_amount();
            $dataStore['deposit_data'] = $this->PrintdepositModel->get_history_data();
            echo view('Views/aut/print_deposit_view', $dataStore);
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function add_deposit() {
        $dataIn = $this->request->getPost('credit_ids');
        $count = 0;
        $amount = 0.00;
        $id = '';
        foreach ($dataIn as $value) {
            if ($value[0] == "id") {
                $dataIn['credit_ids'][$count] = decrypt(base64_decode($value[1]));
                $id .= $dataIn['credit_ids'][$count] . ',';
                $count++;
            }
            if ($value[0] == "amount") {
                $amount = $value[1];
            }
        }
        $ids = rtrim($id, ',');
        $dataStore = $this->PrintdepositModel->add_deposit($ids, $amount);
        $dataCustomer['customer_data'] = $this->PrintdepositModel->get_deposit_credit($ids);
        $html = view('Views/aut/template/deposit_template_view', $dataCustomer);
        echo $html;
    }

    public function undo_deposit() {
        $deposit_id = $this->request->getPost('deposit_id');
        $dataIn['deposit_id'] = 0;
        $dataStore = $this->PrintdepositModel->undo_deposit($dataIn, $deposit_id);
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function get_deposit_pdf() {
        $ids = $this->request->getPost('credit_ids');
        $dataCustomer['customer_data'] = $this->PrintdepositModel->get_deposit_credit($ids);
        $html = view('Views/aut/template/deposit_template_view', $dataCustomer);
        echo $html;
    }

}
