<?php

namespace App\Controllers\sys;

use CodeIgniter\Controller;
use App\Models\CustomerModel;
use App\Models\AdministrationModel;

class RecurringCron extends Controller {

    protected $helpers = ["url", "form", "general", "image"];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->AdministrationModel = new AdministrationModel();
        $this->CustomerModel = new CustomerModel();
    }

    public function index() {
        ini_set('max_execution_time', 0);
        $dataStore = $this->CustomerModel->get_all_recurring();
        $i = 0;
        foreach ($dataStore as $value) {
            if (date('Y-m-d') == $value["date_next"] || strtotime($value["date_next"]) < time()) {
                $dataIn['customer_id'] = $value['customer_id'];
                $dataIn['ct_id'] = $value['ct_id'];
                $dataIn['quantity'] = $value['quantity'];
                $dataIn['rate'] = $value['rate'];
                $dataIn['amount'] = floatval($value['quantity']) * floatval($value['rate']);
                $dataIn['user_id_created'] = $value['user_id_created'];
                $dataIn['user_id_updated'] = $value['user_id_updated'];
                $dataIn['item_id'] = $value['item_id'];
                if ($dataIn['item_id'] != 0) {
                    $item_data = $this->AdministrationModel->get_item(base64_encode(encrypt($dataIn['item_id'])));
                    $dataIn['description'] = ($item_data['name'] != "" ? $item_data['name'] . ": " : "") . $value['description'];
                    $dataIn['description'] = ($value['description'] == "" ? rtrim($value['description'], ': ') : $dataIn['description']);
                }
                if ($value['frequency'] == 'Weekly') {
                    $wdataRcUpdate['date_next'] = $value['date_next'];
                    do {
                        $dataIn['date_charge'] = $wdataRcUpdate['date_next'];
                        if (strtotime($dataIn['date_charge']) <= time()) {
                            $i++;
                            $dataStore = $this->CustomerModel->add_edit_charegs($dataIn);
                        }
                        $wdataRcUpdate['date_next'] = date('Y-m-d', strtotime("+7 day", strtotime($wdataRcUpdate['date_next'])));
                    } while (date('Y-m-d') == $wdataRcUpdate['date_next'] || strtotime($wdataRcUpdate['date_next']) < time());
                    $dataStore = $this->CustomerModel->add_edit_recuring($wdataRcUpdate, base64_encode(encrypt($value['rc_id'])));
                }
                if ($value['frequency'] == 'Monthly') {
                    $mdataRcUpdate['date_next'] = $value['date_next'];
                    do {
                        $dataIn['date_charge'] = $mdataRcUpdate['date_next'];
                        if (strtotime($dataIn['date_charge']) <= time()) {
                            $i++;
                            $dataStore = $this->CustomerModel->add_edit_charegs($dataIn);
                        }
                        $mdataRcUpdate['date_next'] = date('Y-m-d', strtotime("+1 month", strtotime($mdataRcUpdate['date_next'])));
                    } while (date('Y-m-d') == $mdataRcUpdate['date_next'] || strtotime($mdataRcUpdate['date_next']) < time());
                    $dataStore = $this->CustomerModel->add_edit_recuring($mdataRcUpdate, base64_encode(encrypt($value['rc_id'])));
                }
                if ($value['frequency'] == 'Quarterly') {
                    $qdataRcUpdate['date_next'] = $value['date_next'];
                    do {
                        $dataIn['date_charge'] = $qdataRcUpdate['date_next'];
                        if (strtotime($dataIn['date_charge']) <= time()) {
                            $i++;
                            $dataStore = $this->CustomerModel->add_edit_charegs($dataIn);
                        }
                        $qdataRcUpdate['date_next'] = date('Y-m-d', strtotime("+3 month", strtotime($qdataRcUpdate['date_next'])));
                    } while (date('Y-m-d') == $qdataRcUpdate['date_next'] || strtotime($qdataRcUpdate['date_next']) < time());
                    $dataStore = $this->CustomerModel->add_edit_recuring($qdataRcUpdate, base64_encode(encrypt($value['rc_id'])));
                }
                if ($value['frequency'] == 'Annually') {
                    $adataRcUpdate['date_next'] = $value['date_next'];
                    do {
                        $dataIn['date_charge'] = $adataRcUpdate['date_next'];
                        if (strtotime($dataIn['date_charge']) <= time()) {
                            $i++;
                            $dataStore = $this->CustomerModel->add_edit_charegs($dataIn);
                        }
                        $adataRcUpdate['date_next'] = date('Y-m-d', strtotime("+ 1 year", strtotime($adataRcUpdate['date_next'])));
                    } while (date('Y-m-d') == $adataRcUpdate['date_next'] || strtotime($adataRcUpdate['date_next']) < time());
                    $dataStore = $this->CustomerModel->add_edit_recuring($adataRcUpdate, base64_encode(encrypt($value['rc_id'])));
                }
            }
        }
        echo $i . ' Charges Added On ' . date('m-d-Y') . '.';
    }

}
