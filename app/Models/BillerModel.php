<?php

namespace App\Models;

use CodeIgniter\Model;

class BillerModel extends Model {

    var $tblBiller;
    var $tblBillerUser;
    var $tblUser;
    var $tblStripe;
    var $tblWepay;
    var $session;
    protected $helpers = ["general"];

    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
        $this->tblBiller = $db->table('biller');
        $this->tblUser = $db->table('user');
        $this->tblBillerUser = $db->table('biller_user');
        $this->tblStripe = $db->table('stripe');
        $this->tblWepay = $db->table('wepay');
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    /**
     * Get the biller user data based on user id
     * 
     * @param $id
     * @return array
     * */
    public function get_biller_data($id) {
        $dataStore = $this->tblBiller
                ->where('biller_id', $id)
                ->get()
                ->getRowArray();
        $dataStore["your_name"] = $this->tblUser
                        ->where('user_id', $this->session->get('login_userid'))
                        ->get()
                        ->getRowArray()['name_display'];
        return $dataStore;
    }

    /**
     * Biller users data insert if not yet exsist and if exists update the user data
     * login_userid generate after user successfully login
     * And userid generate after user new signup and set data in biller account
     * @param array $dataIn
     * @return array
     * */
    public function update_record($dataIn) {
        $billerResult = $this->tblBiller
                ->where('biller_id', $dataIn["biller_id"])
                ->get()
                ->getRowArray();
        if ($this->session->has('login_userid')) {
            $dataIn["user_id_created"] = $this->session->get('login_userid');
        } else {
            $dataIn["user_id_created"] = $this->session->get('userid');
        }
        $dataIn['user_id_updated'] = $dataIn["user_id_created"];
        if (!empty($billerResult)) {
            $this->tblBiller
                    ->update($dataIn, array('biller_id' => $dataIn["biller_id"]));
        } else {
            $dataIn['user_id_updated'] = $dataIn["user_id_created"];
            $this->tblBiller->insert($dataIn);
            $filename = 'profile-placeholder.jpg';
            $session_profile_data = array('profileImage' => 'https://billex.s3.amazonaws.com/profile/' . $filename);
            $this->session->set($session_profile_data);
            $this->session->set('biller_id', $this->db->insertID());
            $session_login_data = array('login_userid' => $dataIn["user_id_created"]);
            $this->session->set($session_login_data);
            $expiration_data["biller_id"] = $this->session->get('biller_id');
            $expiration_data['user_id'] = $dataIn["user_id_created"];
            $expiration_data['expiration'] = '9999-12-31';
            $this->db->table('biller_user')->insert($expiration_data);
        }
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * update the selected biller record
     * 
     * @param array $dataIn
     * @return array
     * */
    public function update_biller_data($dataIn) {
        $this->tblBiller
                ->update($dataIn, array('biller_id' => $this->session->get('biller_id')));
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * update the selected biller_user record
     * 
     * @param array $user_id
     * @return array
     * */
    public function update_biller_user_data($user_id, $status) {
        if ($status == 0) {
            $usql = 'UPDATE biller_user 
                    SET expiration = CURDATE() 
                    WHERE user_id= :user_id:';
        } else {
            $usql = 'UPDATE biller_user 
                    SET expiration = \'9999-12-31\'
                    WHERE user_id= :user_id:';
        }

        $par = ['user_id' => $user_id];
        $this->db->query($usql, $par);
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Add stripe details after connect
     * 
     * @param array $dataIn
     * @return array
     * */
    public function add_stripe_account($dataIn) {
        $dataIn['biller_id'] = $this->session->get('biller_id');
        $userstipe = $this->tblStripe
                ->where([
                    'biller_id' => $this->session->get('biller_id')])
                ->get()
                ->getRowArray();
        if (empty($userstipe)) {
            $this->tblStripe
                    ->insert($dataIn);
        } else {
            $this->tblStripe
                    ->update($dataIn, array('biller_id' => $this->session->get('biller_id')));
        }
    }

    /**
     * Get stripe account data
     * 
     * @param 
     * @return array
     * */
    public function get_stripe_data() {
        $userstipe = $this->tblStripe
                ->where([
                    'biller_id' => $this->session->get('biller_id')])
                ->get()
                ->getRowArray();
        return $userstipe;
    }

    /**
     * Add wepay details after connect
     * 
     * @param array $dataIn
     * @return array
     * */
    public function add_wepay_account($dataIn) {
        $dataIn['biller_id'] = $this->session->get('biller_id');
        $userstipe = $this->tblWepay
                ->where([
                    'biller_id' => $this->session->get('biller_id')])
                ->get()
                ->getRowArray();
        if (empty($userstipe)) {
            $this->tblWepay
                    ->insert($dataIn);
        } else {
            $this->tblWepay
                    ->update($dataIn, array('biller_id' => $this->session->get('biller_id')));
        }
    }

    /**
     * Get wepay account data
     * 
     * @param 
     * @return array
     * */
    public function get_wepay_data($biller_id) {
        $userstipe = $this->tblWepay
                ->where([
                    'biller_id' => $biller_id])
                ->get()
                ->getRowArray();
        return $userstipe;
    }

}
