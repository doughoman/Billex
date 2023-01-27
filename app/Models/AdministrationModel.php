<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\Name_Parser;

class AdministrationModel extends Model {

    var $tblItem;
    var $tblUserAuth;
    var $tblUser;
    var $tblBiller;
    var $tblBillerUser;
    var $session;
    protected $helpers = ["general"];

    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
        $this->tblItem = $db->table('item');
        $this->tblUserAuth = $db->table('user_auth');
        $this->tblBiller = $db->table('biller');
        $this->tblBillerUser = $db->table('biller_user');
        $this->tblUser = $db->table('user');
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    /**
     * get total Biller customer data
     * 
     * @param array $item_id
     * @return array
     * */
    public function get_item($item_id = '') {
        if ($item_id == "") {
            $dataStore = $this->tblItem
                    ->select('item.item_id,item.status,item.name,item.description,item.rate,item.can_discount,item.ct_id,charge_type.name as type')
                    ->where('user_id_created', $this->session->get('login_userid'))
                    ->join('charge_type', 'charge_type.ct_id=item.ct_id')
                    ->orderBy('charge_type.display_order', 'ASC')
                    ->get()
                    ->getResultArray();
            return $dataStore;
        } else {
            $item_id = decrypt(base64_decode($item_id));
            $dataStore = $this->tblItem
                    ->where('item_id', $item_id)
                    ->get()
                    ->getRowArray();
            return $dataStore;
        }
    }

    /**
     * Biller customer data insert or update
     * 
     * @param array $dataIn,$item_id
     * @return array
     * */
    public function add_edit_item($dataIn, $item_id = '') {
        if ($item_id == "") {
            $this->tblItem->insert($dataIn);
            if ($this->db->insertID()) {
                $dataStore["status"] = "success";
                $dataStore['id'] = $this->db->insertID();
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        } else {
            $this->tblItem
                    ->update($dataIn, array('item_id' => decrypt(base64_decode($item_id))));
            if ($this->db->affectedRows()) {
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        }
    }

    /**
     * Get biller user record with user information
     * 
     * @param array $dataIn,$item_id
     * @return array
     * */
    public function get_biller_user() {
        $sql = 'SELECT user.*, biller_user.*
                FROM biller_user
                INNER JOIN user USING(user_id)
                WHERE biller_id = :biller_id:';
        $par = ['biller_id' => $this->session->get('biller_id')];
        $userResultData = $this->db->query($sql, $par)->getResultArray();
        return $userResultData;
    }

    /**
     * Send invitation to user for join this system
     * 
     * @param array $dataIn
     * @return array
     * */
    public function send_invite($dataIn) {
        if ($dataIn['options'] == 'phone') {
            $dataIn["phone"] = preg_replace("/[^0-9]/", '', $dataIn["phone"]);
            $userPhoneAuthResult = $this->tblUserAuth
                    ->where([
                        'value' => $dataIn['phone'],
                        'type' => 'phone'])
                    ->get()
                    ->getRowArray();
            if (empty($userPhoneAuthResult)) {
                $this->name_parser = new \Name_Parser($dataIn['name_display']);
                $userDataIn["name_first"] = ucfirst($this->name_parser->first);
                $userDataIn["name_last"] = ucfirst($this->name_parser->last);
                $userDataIn['name_display'] = trim($userDataIn["name_first"] . ' ' . $userDataIn["name_last"]);
                $userDataIn["password"] = password_hash(random_strings(), PASSWORD_DEFAULT);
                $this->tblUser
                        ->insert($userDataIn);
                $user_id = $this->db->insertID();
                $this->tblUserAuth
                        ->insert([
                            'user_id' => $this->db->insertID(),
                            'type' => 'phone',
                            'value' => $dataIn["phone"]]);
            } else {
                $user_id = $userPhoneAuthResult['user_id'];
            }
        } else {
            $userEmailAuthResult = $this->tblUserAuth
                    ->where([
                        'value' => $dataIn['email_address'],
                        'type' => 'email'])
                    ->get()
                    ->getRowArray();
            if (empty($userEmailAuthResult)) {
                $this->name_parser = new \Name_Parser($dataIn['name_display']);
                $userDataIn["name_first"] = ucfirst($this->name_parser->first);
                $userDataIn["name_last"] = ucfirst($this->name_parser->last);
                $userDataIn['name_display'] = trim($userDataIn["name_first"] . ' ' . $userDataIn["name_last"]);
                $userDataIn["password"] = password_hash(random_strings(), PASSWORD_DEFAULT);
                $this->tblUser
                        ->insert($userDataIn);
                $user_id = $this->db->insertID();
                $this->tblUserAuth
                        ->insert([
                            'user_id' => $this->db->insertID(),
                            'type' => 'email',
                            'value' => $dataIn["email_address"]]);
            } else {
                $user_id = $userEmailAuthResult['user_id'];
            }
        }
        $userBillerResult = $this->tblBillerUser
                ->where([
                    'user_id' => $user_id,
                    'biller_id' => $this->session->get('biller_id')])
                ->get()
                ->getRowArray();
        if (empty($userBillerResult)) {
            $this->tblBillerUser
                    ->insert([
                        'biller_id' => $this->session->get('biller_id'),
                        'user_id' => $user_id,
                        'expiration' => '9999-12-31',
                        'time_last_access' => '0000-00-00 00:00:00']);
            $user_name = $this->tblUser
                            ->where([
                                'user_id' => $this->session->get('login_userid')])
                            ->get()
                            ->getRowArray()['name_display'];

            $dataStore["status"] = "success";
            $dataStore["msg"] = "Successfully user invited.";
            $dataStore["user_name"] = $user_name;
        } else {
            $dataStore["status"] = "fail";
            $dataStore["msg"] = "User allready exsits.";
        }
        return $dataStore;
    }

}
