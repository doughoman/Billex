<?php

namespace App\Controllers\aut;

use CodeIgniter\Controller;
use App\Models\BillerModel;
use App\Models\UserModel;
use App\Libraries\Casscall;
use Aws\S3\S3Client;
use App\Libraries\Name_Parser;

$client = \Config\Services::curlrequest();

class Biller extends Controller {

    public $casscall;
    protected $cass_helper;
    protected $helpers = ["url", "form", "general", "image"];
    protected $session;
    protected $s3Client;
    protected $name_parser;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->BillerModel = new BillerModel();
        $this->UserModel = new UserModel();
        $this->casscall = new \Casscall();

        $this->session = \Config\Services::session();
        $this->session->start();
        $this->s3Client = new S3Client([
            'region' => 'us-east-1',
            'version' => 'latest',
            'credentials' => array(
                'key' => getenv('aws.access_key_id'),
                'secret' => getenv('aws.secret_access_key'),
            ),
        ]);
    }

    public function cass_name() {
        $dataIn["name"] = $this->request->getPost("name");
        $dataIn["biller_id"] = $this->session->get('biller_id');
        $this->BillerModel->update_record($dataIn);
    }

    public function cass_phone() {
        $dataIn["phone_support"] = $this->request->getPost("phone");
        $dataIn["biller_id"] = $this->session->get('biller_id');
        $this->BillerModel->update_record($dataIn);
    }

    public function cass_email() {
        $dataIn["email_support"] = $this->request->getPost("email");
        $dataIn["biller_id"] = $this->session->get('biller_id');
        $this->BillerModel->update_record($dataIn);
    }

    public function cass_address($type = 'h', $address = '') {
        if (!empty($this->request->getPost("address"))) {
            $address = $this->request->getPost("address");
        } else {
            $address = trim($address);
        }
        $arr[0][$type] = explode("\n", $address); //create an array with key id of 0

        if (count($arr[0][$type]) < 2) {
            //not enough lines to submit for cass
            die(json_encode(array('address' => $address, 'cass_class' => 'danger', 'cass_icon' => get_cass_icon('21', 'html'), 'cass_errors' => 'Invalid Address')));
        }

        $rsCass = $this->casscall->send_bulk_cass($arr);

        $row = $rsCass[0][$type];
        $dataIn["address"] = $row["deliveryLine1"];
        $dataIn["city"] = ($row["cityLocal"] != "" ? $row["cityLocal"] : $row["city"]);
        $dataIn["state"] = $row["state"];
        $dataIn["zip"] = $row["zip"];
        $dataIn["zip4"] = $row["addOn"];
        $dataIn["biller_id"] = $this->session->get('biller_id');

        $errorText = get_cass_error($row['errorCodes']);
        $errorIcon = get_cass_icon($row['errorCodes'], 'html');
        $errorClass = get_cass_class($row['errorCodes']);
        if ($errorClass == "success" && !empty($this->request->getPost("address"))) {
            $this->BillerModel->update_record($dataIn);
        }
        if ($row['deliveryLine1'] != '')
            $arrAddress[] = $row['deliveryLine1'];
        if ($row['deliveryLine2'] != '')
            $arrAddress[] = $row['deliveryLine2'];
        if ($row['deliveryLine3'] != '')
            $arrAddress[] = local_csz($row['deliveryLine3'], $row['cityLocal']);
        $dataIn["cass_errors"] = get_cass_error($row['errorCodes']);
        $dataIn["cass_icon"] = get_cass_icon($row['errorCodes'], 'html');
        $dataIn["cass_class"] = get_cass_class($row['errorCodes']);
        $addressOut = implode("\n", $arrAddress);
        $dataIn["address"] = $addressOut;
        if (!empty($this->request->getPost("address"))) {
            echo json_encode(array('address' => $addressOut, 'cass_class' => $dataIn["cass_class"], 'cass_icon' => $dataIn["cass_icon"], 'cass_errors' => $dataIn["cass_errors"]));
        } else {
            return $dataIn;
        }
    }

    public function mail_cass_address($type = 'h', $address = '') {
        if (!empty($this->request->getPost("address"))) {
            $address = $this->request->getPost("address");
        } else {
            $address = trim($address);
        }
        $arr[0][$type] = explode("\n", $address); //create an array with key id of 0
        if (count($arr[0][$type]) < 2) {
            //not enough lines to submit for cass
            die(json_encode(array('address' => $address, 'cass_class' => 'danger', 'cass_icon' => get_cass_icon('21', 'html'), 'cass_errors' => 'Invalid Address')));
        }

        $rsCass = $this->casscall->send_bulk_cass($arr);

        $row = $rsCass[0][$type];

        $dataIn["address_pay"] = $row["deliveryLine1"];
        $dataIn["city_pay"] = ($row["cityLocal"] != "" ? $row["cityLocal"] : $row["city"]);
        $dataIn["state_pay"] = $row["state"];
        $dataIn["zip_pay"] = $row["zip"];
        $dataIn["zip4_pay"] = $row["addOn"];
        $dataIn["biller_id"] = $this->session->get('biller_id');
        $dataIn["recipient_pay"] = ($row["deliveryLine2"] != "" ? $row["deliveryLine2"] : "");

        $errorText = get_cass_error($row['errorCodes']);
        $errorIcon = get_cass_icon($row['errorCodes'], 'html');
        $errorClass = get_cass_class($row['errorCodes']);
        if ($errorClass == "success" && !empty($this->request->getPost("address"))) {
            $this->BillerModel->update_record($dataIn);
        }
        if ($row['deliveryLine1'] != '')
            $arrAddress[] = $row['deliveryLine1'];
        if ($row['deliveryLine2'] != '')
            $arrAddress[] = $row['deliveryLine2'];
        if ($row['deliveryLine3'] != '')
            $arrAddress[] = local_csz($row['deliveryLine3'], $row['cityLocal']);
        $dataIn["cass_errors"] = get_cass_error($row['errorCodes']);
        $dataIn["cass_icon"] = get_cass_icon($row['errorCodes'], 'html');
        $dataIn["cass_class"] = get_cass_class($row['errorCodes']);
        $addressOut = implode("\n", $arrAddress);
        $dataIn["address"] = $addressOut;
        if (!empty($this->request->getPost("address"))) {
            echo json_encode(array('address' => $addressOut, 'cass_class' => $dataIn["cass_class"], 'cass_icon' => $dataIn["cass_icon"], 'cass_errors' => $dataIn["cass_errors"]));
        } else {
            return $dataIn;
        }
    }

    public function name_display() {
        $dataIn["name_display"] = $this->request->getPost("name_display");
        $this->name_parser = new \Name_Parser($dataIn["name_display"]);
        $dataIn["name_first"] = ucfirst($this->name_parser->first);
        $dataIn["name_last"] = ucfirst($this->name_parser->last);
        $dataStore = $this->UserModel->update_user_data($dataIn);
        $session_login_data = array('display_name' => $dataIn["name_display"]);
        $this->session->set($session_login_data);
    }

    public function ajax_edit_settings() {
        $dataIn = $this->request->getPost('form');
        $saddress = array();
        $maddress = $this->cass_address('h', $dataIn['form_address']);
        if (isset($dataIn['mail_address']) && !empty($dataIn['mail_address'])) {
            $saddress = $this->mail_cass_address('m', $dataIn['mail_address']);
        } else {
            $dataIn["address_pay"] = "";
            $dataIn["city_pay"] = "";
            $dataIn["state_pay"] = "";
            $dataIn["zip_pay"] = "";
            $dataIn["zip4_pay"] = "";
        }
        unset($dataIn['form_address'], $dataIn['mail_address'], $maddress['address'], $maddress['biller_id'], $maddress['cass_errors'], $maddress['cass_icon'], $maddress['cass_class'], $saddress['address'], $saddress['biller_id'], $saddress['cass_errors'], $saddress['cass_icon'], $saddress['cass_class']);
        $dataIn['user_id_updated'] = $this->session->get('login_userid');
        if (strlen($this->request->getPost("social_img_url")) > 0) {
            $image = file_get_contents($this->request->getPost("social_img_url"));
            $imageSeed = rand(1, 255);
            $filename = $this->session->get('biller_id') . "-" . base_convert($this->session->get('biller_id') + $imageSeed, 10, 32) . '.jpg';
            $filepath = "/tmp/{$filename}";
            file_put_contents($filepath, $image);
            // Resize image and crop to square
//            fit_social_image($filepath);

            $this->s3Client->putObject(array(
                'Bucket' => 'billex',
                'Key' => 'biller_image/' . $filename,
                'SourceFile' => $filepath,
                'ACL' => 'public-read'
            ));
            $dataIn["logo_seed"] = $imageSeed;
        }
        if ($dataIn['hide_address'] == 'Yes') {
            $dataIn['hide_address'] = 1;
        } else {
            $dataIn['hide_address'] = 0;
        }
        $settingDataIn = array_merge($dataIn, $maddress, $saddress);
        $dataStore = $this->BillerModel->update_biller_data($settingDataIn);
        if ($dataStore["status"]) {
            $session_login_data = array('display_name' => $dataIn["name"]);
            $this->session->set($session_login_data);
        }
        $dataStore["name"] = $dataIn["name"];
        echo \GuzzleHttp\json_encode($dataStore);
    }
}
