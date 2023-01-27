<?php

namespace App\Models;

use CodeIgniter\Model;

class AlertModel extends Model {

    var $tblAlert;
    var $session;
    protected $helpers = ["general"];

    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
        $this->tblAlert = $db->table('alert');
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    /**
     * Add or edit alert
     * 
     * @param array $dataIn,$id
     * @return array
     * */
    public function add_edit_alert($dataIn, $id = '') {
        if ($id == "") {
            $this->tblAlert->insert($dataIn);
            if ($this->db->insertID()) {
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        } else {
            $this->tblAlert
                    ->update($dataIn, array('id' => decrypt(base64_decode($id))));
            if ($this->db->affectedRows()) {
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        }
    }

    /**
     * get alerts data
     * 
     * 
     * @return array
     * */
    public function get_alert($id = '') {
        if ($id == '') {
            $dataStore = $this->tblAlert
                    ->orderBy('time_stamp', 'DESC')
                    ->get()
                    ->getResultArray();
            return $dataStore;
        } else {
            $dataStore = $this->tblAlert
                    ->where('id', decrypt(base64_decode($id)))
                    ->get()
                    ->getRowArray();
            return $dataStore;
        }
    }

}
