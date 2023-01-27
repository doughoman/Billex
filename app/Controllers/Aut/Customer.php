<?php

namespace App\Controllers\aut;

use CodeIgniter\Controller;
use App\Libraries\Casscall;
use App\Models\CustomerModel;
use App\Models\AdministrationModel;
use App\Models\ChargeModel;
use App\Models\BillerModel;
use App\Models\PostpaymentModel;
use Dompdf\Dompdf;
use Hashids\Hashids;
use Jenssegers\Optimus\Optimus;

class Customer extends Controller {

    protected $session;
    public $casscall;
    protected $helpers = ["url", "form", "general", "image", "cookie"];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->AdministrationModel = new AdministrationModel();
        $this->casscall = new \Casscall();
        $this->CustomerModel = new CustomerModel();
        $this->PostpaymentModel = new PostpaymentModel();
        $this->ChargeModel = new ChargeModel();
        $this->BillerModel = new BillerModel();
        $this->dompdf = new Dompdf();
        $this->hashids = new Hashids(env('hashids.salt'), 16);
        $this->optimus = new Optimus(1580030173, 59260789, 1163945558);
    }

    public function index() {
        if (isset($_REQUEST['q']) && $_REQUEST['q'] == 'initialize') {
            $session_items = array('search_key', 'customerId');
            $this->session->remove($session_items);
        }
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore = $this->CustomerModel->get_customer();
            $dataStore["customer_data"] = $dataStore;
            $count = 0;
            foreach ($dataStore["customer_data"] as $customer_value) {
                $dataStore["customer_data"][$count]["customer_id"] = base64_encode(encrypt($customer_value["customer_id"]));
                $count++;
            }
            echo view('Views/aut/customer_listing_view', $dataStore);
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function search_key() {
        $search_key = $this->request->getPost('search_key');
        $session_search_data = array('search_key' => $search_key);
        $this->session->set($session_search_data);
        if ($search_key == "") {
            $session_items = array('customerId');
            $this->session->remove($session_items);
        }
    }

    public function set_customer() {
        $session_search_data = array('customerId' => decrypt(base64_decode($this->request->getPost('customer_id'))));
        $this->session->set($session_search_data);
    }

    public function add_edit_customer($id = '') {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            if ($id == "") {
                echo view('Views/aut/add_customer_view');
            } else {

                $dataStore = $this->CustomerModel->get_customer($id);
                if (empty($dataStore)) {
                    return redirect()->to(base_url() . 'aut/customer');
                } else {
                    $dataStore["customer_id"] = base64_encode(encrypt($dataStore["customer_id"]));
                    echo view('Views/aut/add_customer_view', $dataStore);
                }
            }
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function update_cuatomer_balance() {
        $dataIn = $this->request->getPost('customer_balance');
        foreach ($dataIn as $value) {
            $dataStore = $this->CustomerModel->update_balance($value[0], $value[1]);
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function mailling_address($type = 'h', $address = '', $saddress = '') {
        $mstatus = TRUE;
        if (!empty($this->request->getPost("address"))) {
            $address = $this->request->getPost("address");
        } else {
            $address = trim($address);
        }
        $arr[0][$type] = explode("\n", $address); //create an array with key id of 0
        if (count($arr[0][$type]) < 2) {
            //not enough lines to submit for cass
            if (!empty($this->request->getPost("address"))) {
                die(json_encode(array('address' => $address, 'cass_class' => 'danger', 'cass_icon' => get_cass_icon('21', 'html'), 'cass_errors' => 'Invalid Address', 'type' => 'h')));
            } else {
                return json_encode(array('address' => $address, 'cass_class' => 'danger', 'cass_icon' => get_cass_icon('21', 'html'), 'cass_errors' => 'Invalid Address', 'type' => 'h'));
                $mstatus = FALSE;
            }
        }
        if ($mstatus) {
            $rsCass = $this->casscall->send_bulk_cass($arr);
            $row = $rsCass[0][$type];
            $dataIn["address_mail_street"] = $row["deliveryLine1"];
            $dataIn["address_mail_city"] = ($row["cityLocal"] != "" ? $row["cityLocal"] : $row["city"]);
            $dataIn["address_mail_state"] = $row["state"];
            $dataIn["address_mail_zip5"] = $row["zip"];
            $dataIn["address_mail_zip4"] = $row["addOn"];
            $dataIn["cass_errors"] = get_cass_error($row['errorCodes']);
            $dataIn["cass_icon"] = get_cass_icon($row['errorCodes'], 'html');
            $dataIn["cass_class"] = get_cass_class($row['errorCodes']);
            $dataIn["type"] = "h";
            $errorText = get_cass_error($row['errorCodes']);
            $errorIcon = get_cass_icon($row['errorCodes'], 'html');
            $errorClass = get_cass_class($row['errorCodes']);
            if ($row['deliveryLine1'] != '')
                $arrAddress[] = $row['deliveryLine1'];
            if ($row['deliveryLine2'] != '')
                $arrAddress[] = $row['deliveryLine2'];
            if ($row['deliveryLine3'] != '')
                $arrAddress[] = local_csz($row['deliveryLine3'], $row['cityLocal']);
            $addressOut = implode("\n", $arrAddress);
            $dataIn["address"] = $addressOut;
            if (!empty($this->request->getPost("address"))) {
                echo json_encode(array('address' => $addressOut, 'cass_class' => $dataIn["cass_class"], 'cass_icon' => $dataIn["cass_icon"], 'cass_errors' => $dataIn["cass_errors"]));
            } else {
                return \GuzzleHttp\json_encode($dataIn);
            }
        }
    }

    public function service_address($type = 'm', $address = '', $maddress = '') {
        if (!empty($this->request->getPost("address"))) {
            $address = $this->request->getPost("address");
        } else {
            $address = trim($address);
        }
        $arr[0][$type] = explode("\n", $address); //create an array with key id of 0
        if (count($arr[0][$type]) < 2) {
            //not enough lines to submit for cass
            die(json_encode(array('address' => $address, 'cass_class' => 'danger', 'cass_icon' => get_cass_icon('21', 'html'), 'cass_errors' => 'Invalid Address', 'type' => 'm', 'm_address' => json_decode($maddress, true))));
        }

        $rsCass = $this->casscall->send_bulk_cass($arr);
        $row = $rsCass[0][$type];
        $dataIn["address_street"] = $row["deliveryLine1"];
        $dataIn["address_city"] = ($row["cityLocal"] != "" ? $row["cityLocal"] : $row["city"]);
        $dataIn["address_state"] = $row["state"];
        $dataIn["address_zip5"] = $row["zip"];
        $dataIn["address_zip4"] = $row["addOn"];
        $dataIn["cass_errors"] = get_cass_error($row['errorCodes']);
        $dataIn["cass_icon"] = get_cass_icon($row['errorCodes'], 'html');
        $dataIn["cass_class"] = get_cass_class($row['errorCodes']);
        if ($maddress != '') {
            $dataIn["m_address"] = \GuzzleHttp\json_decode($maddress, true);
        }
        $dataIn["type"] = "m";

        $errorText = get_cass_error($row['errorCodes']);
        $errorIcon = get_cass_icon($row['errorCodes'], 'html');
        $errorClass = get_cass_class($row['errorCodes']);
        if ($row['deliveryLine1'] != '')
            $arrAddress[] = $row['deliveryLine1'];
        if ($row['deliveryLine2'] != '')
            $arrAddress[] = $row['deliveryLine2'];
        if ($row['deliveryLine3'] != '')
            $arrAddress[] = local_csz($row['deliveryLine3'], $row['cityLocal']);
        $addressOut = implode("\n", $arrAddress);
        $dataIn["address"] = $addressOut;
        if ($maddress == '') {
            echo \GuzzleHttp\json_encode(array('address' => $addressOut, 'cass_class' => $dataIn["cass_class"], 'cass_icon' => $dataIn["cass_icon"], 'cass_errors' => $dataIn["cass_errors"]));
        } else {
            return \GuzzleHttp\json_encode($dataIn);
        }
    }

    public function ajax_add_edit_customer($id = "") {
        $dataIn = $this->request->getPost('form');
        $dataIn["contact_phone"] = ($dataIn["contact_phone"] != "" ? preg_replace("/[^0-9]/", '', $dataIn["contact_phone"]) : "");
        if ($dataIn['mailling_address'] != "") {
            $maddress = $this->mailling_address('h', $dataIn['mailling_address']);
            if ($dataIn['service_address'] == "") {
                echo $maddress;
                die;
            }
        } else {
            $maddress = array(
                'address_mail_street' => '',
                'address_mail_city' => '',
                'address_mail_state' => '',
                'address_mail_zip5' => 0,
                'address_mail_zip4' => 0
            );
        }

        if ($dataIn['service_address'] != "") {
            if ($dataIn['mailling_address'] != "") {
                $saddress = $this->service_address('h', $dataIn['service_address'], $maddress);
            } else {
                $saddress = $this->service_address('h', $dataIn['service_address'], \GuzzleHttp\json_encode($maddress));
            }
            if (isset(json_decode($saddress, true)['cass_class']) && json_decode($saddress, true)['cass_class'] != "success") {
                echo $saddress;
                die;
            }
        } else {
            $saddress = array(
                'address_street' => '',
                'address_city' => '',
                'address_state' => '',
                'address_zip5' => 0,
                'address_zip4' => 0
            );
        }
        $dataIn["biller_id"] = $this->session->get('biller_id');
        $dataIn["discount"] = ($dataIn["discount"] != "" ? $dataIn["discount"] * 100 : "");
        $dataIn["tax_state"] = ($dataIn["tax_state"] != "" ? $dataIn["tax_state"] * 100 : "");
        $dataIn["tax_county"] = ($dataIn["tax_county"] != "" ? $dataIn["tax_county"] * 100 : "");

        if ($dataIn['mailling_address'] != "") {
            $m_address = json_decode($maddress, true)["address"];
            $maddress = json_decode($maddress, true);
            $merror = $maddress["cass_errors"];
            $micon = $maddress["cass_icon"];
            unset($maddress["cass_errors"], $maddress["cass_icon"], $maddress["m_address"], $maddress["cass_class"], $maddress["type"], $maddress["address"]);
        }
        if ($dataIn['service_address'] != "") {
            $s_address = json_decode($saddress, true)["address"];
            $saddress = json_decode($saddress, true);
            $serror = $saddress["cass_errors"];
            $sicon = $saddress["cass_icon"];
            unset($saddress["m_address"], $saddress["cass_errors"], $saddress["address"], $saddress["type"], $saddress["cass_icon"], $saddress["cass_class"]);
        }
        unset($dataIn["mailling_address"]);
        unset($dataIn["service_address"]);
        $customerDataIn = array_merge($dataIn, $maddress, $saddress);
        if ($id == "") {
            $customerDataIn['user_id_created'] = $this->session->get('login_userid');
            $customerDataIn['user_id_updated'] = $this->session->get('login_userid');
            $dataStore = $this->CustomerModel->add_edit_biller_customer($customerDataIn);
        } else {
            $customerDataIn['user_id_updated'] = $this->session->get('login_userid');
            $dataStore = $this->CustomerModel->add_edit_biller_customer($customerDataIn, $id);
        }
        if ($maddress['address_mail_street'] != '') {
            $dataStore["m_address"] = $m_address;
            $dataStore["m_cass_icon"] = $micon;
            $dataStore["m_cass_errors"] = $merror;
        }
        if ($saddress['address_street'] != '') {
            $dataStore["s_address"] = $s_address;
            $dataStore["s_cass_icon"] = $sicon;
            $dataStore["s_cass_errors"] = $serror;
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function check_customer_transaction() {
        $customer_id = decrypt(base64_decode($this->request->getPost('customer_id')));
        $dataStore['charges'] = count(dbQueryRows('charge', array('customer_id' => $customer_id, 'is_deleted' => 0)));
        $dataStore['recurring_charge'] = count(dbQueryRows('recurring_charge', array('customer_id' => $customer_id, 'is_deleted' => 0)));
        $dataStore['invoice'] = count(dbQueryRows('invoice', array('customer_id' => $customer_id, 'is_deleted' => 0)));
        $dataStore['customer_item'] = count(dbQueryRows('customer_item', array('customer_id' => $customer_id)));
        $dataStore['credit_detail'] = count(dbQueryRows('credit_detail', array('customer_id' => $customer_id, 'is_deleted' => 0)));
        $timer_project = dbQueryRows('timer_project', array('customer_id' => $customer_id, 'status' => 'active'));
        $timer = 0;
        foreach ($timer_project as $value) {
            $timer = $timer + count(dbQueryRows('timer', array('timer_project_id' => $value['timer_project_id'])));
        }
        $dataStore['timer_project'] = count($timer_project);
        $dataStore['timer'] = $timer;
        $dataStore['status'] = '';
        foreach ($dataStore as $key => $value) {
            if ($value > 0) {
                $dataStore['status'] = 'fail';
                break;
            }
        }
        if ($dataStore['status'] == '') {
            $dataStore['status'] = 'success';
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function delete_customer() {
        $dataIn['is_deleted'] = 1;
        $dataStore = $this->CustomerModel->add_edit_biller_customer($dataIn, $this->request->getPost('customer_id'));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function charges($id = '') {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore = $this->CustomerModel->get_customer($id);
            if (empty($dataStore)) {
                return redirect()->to(base_url() . 'aut/customer');
            } else {
                $dataStore["customer_id"] = base64_encode(encrypt($dataStore["customer_id"]));
                $dataStore["item_data"] = $this->AdministrationModel->get_item();
                $count = 0;
                foreach ($dataStore["item_data"] as $customer_value) {
                    $dataStore["item_data"][$count]["type"] = $this->CustomerModel->get_charge_type($dataStore["item_data"][$count]["type"]);
                    $count++;
                }
                $dataStore["charges_data"] = $this->CustomerModel->get_charges($id);
                $dataStore["recent_charges"] = $this->ChargeModel->get_recent_credits($id);
                $dataStore["recent_invoices"] = $this->ChargeModel->get_recent_invoices($id);
                $dataStore["chargetype_data"] = $this->ChargeModel->get_charge_type();
                $dataStore["customer_data"] = $this->CustomerModel->get_customer();
                $dataStore["biller_data"] = $this->BillerModel->get_biller_data($this->session->get('biller_id'));
                $dataStore["charges_invoice_data"] = $this->CustomerModel->get_invoice_charges($dataStore["customer_id"]);
                $dataStore["invoice_data"] = $this->CustomerModel->get_invoice();
                $dataStore["invoice_code"] = number_format($this->optimus->encode(intval($dataStore["invoice_data"]['invoice_id']) + 1), 0, '', '-');
                $dataStore["invoice_data"]['invoice_id'] = $this->hashids->encode($dataStore["invoice_data"]['invoice_id'] + 1);
                echo view('Views/aut/customer_charges_view', $dataStore);
            }
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function ajax_add_charge($id = '') {
        $dataIn = $this->request->getPost('form');
        $dataIn['quantity'] = $dataIn['customer_charge_qua_hrs'];
        unset($dataIn['customer_charge_qua_hrs']);
        if (strpos($dataIn['quantity'], ':')) {
            $dataIn['quantity'] = convert_hrs_to_number($dataIn['quantity']);
        } else {
            $dataIn['quantity'] = $dataIn['quantity'];
        }
        if (is_numeric($dataIn['customer_id'])) {
            $dataIn['customer_id'] = $dataIn['customer_id'];
        } else {
            $dataIn['customer_id'] = decrypt(base64_decode($dataIn['customer_id']));
        }
        $dataIn['date_charge'] = date('Y-m-d', strtotime($dataIn['date_charge']));
        if (is_numeric($dataIn['ct_id'])) {
            $dataIn['ct_id'] = $dataIn['ct_id'];
        } else {
            $dataIn['ct_id'] = $this->CustomerModel->get_charge_type($dataIn['ct_id']);
        }
        $dataIn['amount'] = floatval($dataIn['quantity']) * floatval($dataIn['rate']);
        if ($id == '') {
            $dataIn['description'] = ($dataIn['charge_description_1'] != "" ? $dataIn['charge_description_1'] . ": " : "") . $dataIn['charge_description_2'];
            $dataIn['description'] = ($dataIn['charge_description_2'] == "" ? rtrim($dataIn['description'], ': ') : $dataIn['description']);
            unset($dataIn['charge_description_1'], $dataIn['charge_description_2']);
        } else {
            $dataIn['description'] = $dataIn['charge_description_2'];
            unset($dataIn['charge_description_1'], $dataIn['charge_description_2']);
        }
        if ($id == '') {
            $dataIn['user_id_created'] = $this->session->get('login_userid');
            $dataIn['user_id_updated'] = $this->session->get('login_userid');
            $dataStore = $this->CustomerModel->add_edit_charegs($dataIn);
            $dataStore["charge_id"] = base64_encode(encrypt($dataStore["charge_id"]));
        } else {
            $dataIn['user_id_updated'] = $this->session->get('login_userid');
            $dataStore = $this->CustomerModel->add_edit_charegs($dataIn, $id);
        }

        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function ajax_delete_charge() {
        $dataIn['is_deleted'] = 1;
        $dataStore = $this->CustomerModel->add_edit_charegs($dataIn, $this->request->getPost('id'));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function get_charge_edit_data() {
        $dataStore = $this->CustomerModel->get_charges('', $this->request->getPost('id'));
        $dataStore['charge_id'] = base64_encode(encrypt($dataStore["charge_id"]));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function recuring($id = '') {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore = $this->CustomerModel->get_customer($id);
            if (empty($dataStore)) {
                return redirect()->to(base_url() . 'aut/customer');
            } else {
                $dataStore["customer_id"] = base64_encode(encrypt($dataStore["customer_id"]));
                $dataStore["item_data"] = $this->AdministrationModel->get_item();
                $count = 0;
                foreach ($dataStore["item_data"] as $customer_value) {
                    $dataStore["item_data"][$count]["type"] = $this->CustomerModel->get_charge_type($dataStore["item_data"][$count]["type"]);
                    $count++;
                }
                $dataStore["recuring_data"] = $this->CustomerModel->get_recuring($id);
                $dataStore["chargetype_data"] = $this->ChargeModel->get_charge_type();
                $dataStore["customer_data"] = $this->CustomerModel->get_customer();
                echo view('Views/aut/customer_recuring_view', $dataStore);
            }
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function ajax_add_recuring($id = '') {
        $dataIn = $this->request->getPost('form');
        if (strpos($dataIn['quantity'], ':')) {
            $dataIn['quantity'] = convert_hrs_to_number($dataIn['quantity']);
        } else {
            $dataIn['quantity'] = $dataIn['quantity'];
        }
        if (is_numeric($dataIn['customer_id'])) {
            $dataIn['customer_id'] = $dataIn['customer_id'];
        } else {
            $dataIn['customer_id'] = decrypt(base64_decode($dataIn['customer_id']));
        }
        $dataIn['date_next'] = date('Y-m-d', strtotime($dataIn['date_next']));
        if (is_numeric($dataIn['ct_id'])) {
            $dataIn['ct_id'] = $dataIn['ct_id'];
        } else {
            $dataIn['ct_id'] = $this->CustomerModel->get_charge_type($dataIn['ct_id']);
        }
        if ($id == '') {
            $dataIn['user_id_created'] = $this->session->get('login_userid');
            $dataIn['user_id_updated'] = $this->session->get('login_userid');
            $dataStore["status"] = $this->CustomerModel->add_edit_recuring($dataIn);
        } else {
            $dataIn['user_id_updated'] = $this->session->get('login_userid');
            $dataStore["status"] = $this->CustomerModel->add_edit_recuring($dataIn, $id);
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function get_recuring_edit_data() {
        $dataStore = $this->CustomerModel->get_recuring('', $this->request->getPost('id'));
        $dataStore['rc_id'] = base64_encode(encrypt($dataStore["rc_id"]));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function ajax_delete_recurring() {
        $dataIn['is_deleted'] = 1;
        $dataStore = $this->CustomerModel->add_edit_recuring($dataIn, $this->request->getPost('id'));
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function rates($id = '') {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore = $this->CustomerModel->get_customer($id);
            if (empty($dataStore)) {
                return redirect()->to(base_url() . 'aut/customer');
            } else {
                $dataStore["customer_id"] = $dataStore["customer_id"];
                $dataStore["rates_data"] = $this->CustomerModel->get_rates($id);
                echo view('Views/aut/customer_rates_view', $dataStore);
            }
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function ajax_add_edit_rates() {
        $dataIn["customer_id"] = $this->request->getPost('cid');
        $dataIn["item_id"] = $this->request->getPost('itid');
        $dataIn["ci_rate"] = $this->request->getPost('rate');
        if ($dataIn["ci_rate"] != "") {
            if ($this->request->getPost('rate_id') != 0) {
                $dataStore = $this->CustomerModel->add_edit_rates($dataIn, $this->request->getPost('rate_id'));
            } else {
                $dataStore = $this->CustomerModel->add_edit_rates($dataIn);
            }
        } else {
            $dataStore = $this->CustomerModel->delete_rates($this->request->getPost('rate_id'));
        }
        echo \GuzzleHttp\json_encode($dataStore);
    }

    public function register($id = '') {
        $sess_id = $this->session->has('login_userid');
        if (!empty($sess_id)) {
            $dataStore = $this->CustomerModel->get_customer($id);
            if (empty($dataStore)) {
                return redirect()->to(base_url() . 'aut/customer');
            } else {
                $dataStore['register_data'] = $this->CustomerModel->get_register($id);
                echo view('Views/aut/customer_register_view', $dataStore);
            }
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'login?redirect_url=' . $redirect_url);
        }
    }

    public function undo_invoice() {
        $invoice_id = $this->request->getPost('invoice_id');
        $amount = $this->request->getPost('amount');
        $customer_id = $this->request->getPost('customer_id');
        $dataIn['invoice_id'] = 0;

        $dataStore = $this->CustomerModel->edit_charegs_invoice($dataIn, $invoice_id);
        $this->CustomerModel->update_balance(decrypt(base64_decode($customer_id)), $amount);
        echo \GuzzleHttp\json_encode($dataStore);
    }

}
