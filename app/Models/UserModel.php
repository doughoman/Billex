<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model {

    var $tblUser;
    var $tblVerify;
    var $tblUserAuth;
    var $tblBiller;
    var $session;
    protected $helpers = ["general"];

    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();

        //Create the query builder instances for each table
        $this->tblUser = $db->table('user');
        $this->tblVerify = $db->table('verify');
        $this->tblUserAuth = $db->table('user_auth');
        $this->tblBiller = $db->table('biller');
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    /**
     * Adds a users verify record by phone number or email address
     * 
     * @param array $dataIn
     * @return array
     * */
    public function generate_verify_code($dataIn) {
        if ($dataIn["phone"] != "") {
            $wresult = $this->tblUserAuth
                    ->where(array('value' => $dataIn["phone"], 'user_id!=' => $this->session->get('login_userid')))
                    ->get()
                    ->getRowArray();
            $verification_type = "phone";
            $verification_address = $dataIn["phone"];
        } else {
            $verification_type = "email";
            $verification_address = $dataIn['email'];
            $wresult = $this->tblUserAuth
                    ->where('value', $dataIn['email'])
                    ->get()
                    ->getRowArray();
        }
        $random_number = mt_rand(10000, 99999);
        $dataVerify["verification_code"] = $random_number;
        $dataVerify["address"] = $verification_address;
        $dataVerify["verification_type"] = $verification_type;
        $this->tblVerify->insert($dataVerify);
        $dataVerify["verify_id"] = encrypt($this->db->insertID());
        if (!empty($wresult)) {
            $dataVerify["allready"] = 1;
            $this->session->set('allready_user_id', $wresult["user_id"]);
        } else {
            $dataVerify["allready"] = 0;
        }
        if ($dataVerify["verify_id"] != "") {
            $dataVerify["status"] = "success";
        } else {
            $dataVerify["status"] = "fail";
        }
        return $dataVerify;
    }

    /**
     * Verify the otp code and successfully verify create user as a phone number and email address
     * 
     * @param array $dataIn
     * @return array
     * $verifyResult["verification_type"] as a key for check user sign up using email address or phone number
     * $verifyResult["address"] as a value for check user already exsist or not
     * */
    public function verify_user($dataIn) {
        $dataIn['verify_id'] = decrypt($dataIn['verify_id']);

        $verifyResult = $this->tblVerify
                ->where([
                    'verify_id' => $dataIn['verify_id'],
                    'verification_code' => $dataIn["verification_code"]])
                ->get()
                ->getRowArray();
        if (empty($verifyResult["address"])) {
            $dataStored = array();
            $dataStored['data'] = array();
            $dataStored['status'] = 'Fail';
            $dataStored['message'] = 'Wrong Otp';
        } else {
            $dataStored = array();
            $data_verified_status = array('verified_status' => 1);
            $this->tblVerify->update($data_verified_status);
            $userResult = $this->tblUserAuth
                    ->where('value', $verifyResult["address"])
                    ->get()
                    ->getRowArray();

            if (!empty($userResult)) {
                $dataStored["user_id"] = $userResult["user_id"];
                $sql = 'SELECT b.name, b.biller_id, u.avatar_seed
                        FROM biller_user
                        INNER JOIN biller b USING(biller_id)
                        INNER JOIN user u USING(user_id)
                        WHERE user_id = :user_id:
                        AND expiration > CURRENT_DATE()
                        ORDER BY time_last_access
                        LIMIT 1';
                $par = ['user_id' => $dataStored["user_id"]];
                $userResultData = $this->db->query($sql, $par)->getRowArray();
                $dataStored["display_name"] = $userResultData["name"];
                $dataStored["biller_id"] = $userResultData["biller_id"];
                $dataStored["avatar_seed"] = $userResultData["avatar_seed"];
                $usql = 'UPDATE biller_user 
                    SET time_last_access = CURRENT_TIMESTAMP() 
                    WHERE user_id= :user_id:';
                $this->db->query($usql, $par);
            } else {
                $dataUser["password"] = password_hash(random_strings(), PASSWORD_DEFAULT);
                $this->tblUser->insert($dataUser);
                $dataStored["user_id"] = $this->db->insertID();
                $dataUserAuthIn["user_id"] = $this->db->insertID();
                $dataUserAuthIn["type"] = $verifyResult["verification_type"];
                $dataUserAuthIn["value"] = $verifyResult["address"];
                $this->tblUserAuth->insert($dataUserAuthIn);
            }
            $dataStored['data'] = $verifyResult;
            $dataStored['status'] = 'Success';
            $dataStored['message'] = 'Otp confirm';
        }

        return $dataStored;
    }

    /**
     * Users sign up or login using google
     * 
     * @param array $dataUserIn for add user data in be.user table
     * @param array $dataUserAuthIn for add user data in be.user_auth table
     * @return array
     * */
    public function login_via_google($dataUserIn, $dataUserAuthIn) {
        $googleResult = $this->tblUserAuth
                ->where('value', $dataUserAuthIn["value"])
                ->get()
                ->getRowArray();
        if (!empty($googleResult)) {
            $dataStored["user_id"] = $googleResult["user_id"];
            $dataStored['already'] = 1;
            $sql = 'SELECT b.name, b.biller_id, u.avatar_seed
                    FROM biller_user
                    INNER JOIN biller b USING(biller_id)
                    INNER JOIN user u USING(user_id)
                    WHERE user_id = :user_id:
                    AND expiration > CURRENT_DATE()
                    ORDER BY time_last_access
                    LIMIT 1';
            $par = ['user_id' => $dataStored["user_id"]];
            $userResultData = $this->db->query($sql, $par)->getRowArray();
            $dataStored["name_display"] = $userResultData["name"];
            $dataStored["biller_id"] = $userResultData["biller_id"];
            $dataStored["avatar_seed"] = $userResultData["avatar_seed"];
            $usql = 'UPDATE biller_user 
                    SET time_last_access = CURRENT_TIMESTAMP() 
                    WHERE user_id= :user_id:';
            $this->db->query($usql, $par);

            return $dataStored;
        } else {
            $googleEmailResult = $this->tblUserAuth
                    ->where('value', $dataUserAuthIn["email_address"])
                    ->get()
                    ->getRowArray();
            if (!empty($googleEmailResult)) {
                $dataUserAuthIn["user_id"] = $googleEmailResult["user_id"];
                unset($dataUserAuthIn["email_address"]);
                $this->tblUserAuth->insert($dataUserAuthIn);
            } else {
                $dataUserIn["password"] = password_hash(random_strings(), PASSWORD_DEFAULT);
                $this->tblUser->insert($dataUserIn);
                $dataStored["user_id"] = $this->db->insertID();
                $user_id = $this->db->insertID();
                $dataStored['already'] = 0;
                $sql = 'SELECT b.name, b.biller_id, u.avatar_seed
                        FROM biller_user
                        INNER JOIN biller b USING(biller_id)
                        INNER JOIN user u USING(user_id)
                        WHERE user_id = :user_id:
                        AND expiration > CURRENT_DATE()
                        ORDER BY time_last_access
                        LIMIT 1';
                $par = ['user_id' => $dataStored["user_id"]];
                $userResultData = $this->db->query($sql, $par)->getRowArray();
                $dataStored["name_display"] = $userResultData["name"];
                $dataStored["biller_id"] = $userResultData["biller_id"];
                $dataStored["avatar_seed"] = $userResultData["avatar_seed"];
                $dataUserAuthIn["user_id"] = $user_id;
                $dataUserAuthEmailIn["user_id"] = $user_id;
                $dataUserAuthEmailIn["type"] = "email";
                $dataUserAuthEmailIn["value"] = $dataUserAuthIn["email_address"];
                unset($dataUserAuthIn["email_address"]);
                $this->tblUserAuth->insert($dataUserAuthIn);
                $this->tblUserAuth->insert($dataUserAuthEmailIn);
                $usql = 'UPDATE biller_user 
                    SET time_last_access = CURRENT_TIMESTAMP() 
                    where user_id= :user_id:';
                $this->db->query($usql, $par);
                return $dataStored;
            }
        }
    }

    /**
     * Users login using phone using otp code option
     * 
     * @param array $dataIn
     * @return array
     * */
    public function login_via_phone($dataIn) {
        $dataStored = $this->tblUserAuth
                ->where('value', $dataIn["phone"])
                ->get()
                ->getRowArray();
        if (!empty($dataStored)) {
            $verification_type = "phone";
            $six_digit_random_number = mt_rand(10000, 99999);
            $dataVerify["verification_code"] = $six_digit_random_number;
            $dataVerify["verification_type"] = $verification_type;
            $dataVerify["address"] = $dataIn['phone'];
            $this->tblVerify->insert($dataVerify);
            $dataVerify["verify_id"] = encrypt($this->db->insertID());
            $dataVerify["status"] = "success";
        } else {
            $dataVerify["status"] = "fail";
        }
        return $dataVerify;
    }

    /**
     * Users login using email using otp code option
     * 
     * @param array $dataIn
     * @return array
     * */
    public function login_via_email($dataIn) {
        $dataStored = $this->tblUserAuth
                ->where('value', $dataIn['email'])
                ->get()
                ->getRowArray();
        if (!empty($dataStored)) {
            $six_digit_random_number = mt_rand(10000, 99999);
            $dataVerify["verification_code"] = $six_digit_random_number;
            $dataVerify["verification_type"] = "email";
            $dataVerify["address"] = $dataIn['email'];
            $this->tblVerify->insert($dataVerify);
            $dataVerify["verify_id"] = encrypt($this->db->insertID());
            $dataVerify["status"] = "success";
        } else {
            $dataVerify["status"] = "fail";
        }
        return $dataVerify;
    }

    /**
     * Users check already exists or not for login using password option select
     * 
     * @param array $dataIn
     * @return array
     * */
    public function user_check($dataIn) {
        if ($dataIn["phone"] != "") {
            $wresult = $this->tblUserAuth
                    ->where('value', $dataIn["phone"])
                    ->get()
                    ->getRowArray();
            if (!empty($wresult)) {
                $dataStored["status"] = "success";
                $dataStored["user_id"] = $wresult["user_id"];
            } else {
                $dataStored["status"] = "fail";
            }
            return $dataStored;
        } else {
            $wresult = $this->tblUserAuth
                    ->where('value', $dataIn['email'])
                    ->get()
                    ->getRowArray();
            if (!empty($wresult)) {
                $dataStored["status"] = "success";
                $dataStored["user_id"] = $wresult["user_id"];
            } else {
                $dataStored["status"] = "fail";
            }
            return $dataStored;
        }
    }

    /**
     * If users already exists at select password option for login check password
     * 
     * @param array $dataIn
     * @return array
     * */
    public function password_login($dataIn) {

        $wresult = $this->tblUser
                ->where(['user_id' => $dataIn['user_id']])
                ->get()
                ->getRowArray();
        if (password_verify($dataIn["password"], $wresult["password"])) {
            $dataStored["status"] = "success";
        } else {
            $dataStored["status"] = "fail";
        }
        $sql = 'SELECT b.name, b.biller_id, u.avatar_seed
                FROM biller_user
                INNER JOIN biller b USING(biller_id)
                INNER JOIN user u USING(user_id)
                WHERE user_id = :user_id:
                AND expiration > CURRENT_DATE()
                ORDER BY time_last_access
                LIMIT 1';
        $par = ['user_id' => $dataIn['user_id']];
        $userResultData = $this->db->query($sql, $par)->getRowArray();
        $dataStored["name_display"] = $userResultData["name"];
        $dataStored["biller_id"] = $userResultData["biller_id"];
        $dataStored["avatar_seed"] = $userResultData["avatar_seed"];
        $usql = 'UPDATE biller_user 
                    SET time_last_access = CURRENT_TIMESTAMP() 
                    where user_id= :user_id:';
        $this->db->query($usql, $par);
        return $dataStored;
    }

    /**
     * Update login user data like basic information and password
     * 
     * @param arrat $dataIn
     * @return array
     * */
    public function update_user_data($dataIn) {
        if ($this->session->has('login_userid')) {
            $user_id = $this->session->get('login_userid');
        } else {
            $user_id = $this->session->get('userid');
        }
        $this->tblUser->update($dataIn, array('user_id' => $user_id));
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
            $dataStore["msg"] = "Profile image Successfully changed.";
            $par = ['user_id' => $user_id];
            $usql = 'UPDATE user 
                    SET time_updated = CURRENT_TIMESTAMP() 
                    where user_id= :user_id:';
            $this->db->query($usql, $par);
        } else {
            $dataStore["status"] = "fail";
            $dataStore["msg"] = "Profile image not changed.";
        }
        return $dataStore;
    }

    /**
     * Get user profile record in dashboard for update general information
     * 
     * @param array $dataIn
     * @return array
     */
    public function get_user_profile_data($user_id) {
        $dataStore["user_aut_data"] = $this->tblUserAuth
                ->where('user_id', $user_id)
                ->get()
                ->getResultArray();
        $dataStore["name"] = $this->tblUser
                ->where(['user_id' => $user_id])
                ->get()
                ->getRowArray();
        return $dataStore;
    }

    /**
     * update user authentication data for email address and password after succefully verify
     * 
     * @param array $authDataIn
     * @return array
     */
    public function update_user_auth_data($authDataIn) {

        $verifyResult = $this->tblVerify
                ->where([
                    'verify_id' => decrypt($authDataIn['verify_id']),
                    'verification_code' => $authDataIn["verification_code"]])
                ->get()
                ->getRowArray();

        if (empty($verifyResult["address"])) {
            $dataStore = array();
            $dataStore['data'] = array();
            $dataStore['status'] = 'Fail';
            $dataStore['message'] = 'Wrong Otp';
        } else {
            $dataStore = array();
            $data_verified_status = array('verified_status' => 1);
            $this->tblVerify->update($data_verified_status);
            if ($this->session->has('allready_user_id')) {
                $this->db->table("biller_user")
                        ->where('user_id', $this->session->get('allready_user_id'))
                        ->update(array('user_id' => $authDataIn["user_id"]));
                $this->tblUserAuth
                        ->where('user_id', $this->session->get('allready_user_id'))
                        ->update(array('user_id' => $authDataIn["user_id"]));
                if ($this->db->affectedRows()) {
                    $dataStore["status"] = "success";
                    $this->session->remove('allready_user_id');
                } else {
                    $dataStore["status"] = "fail";
                }
            } else {
                if ($verifyResult["verification_type"] == "email") {
                    $userAuthResult = $this->tblUserAuth
                            ->where([
                                'user_id' => $authDataIn["user_id"],
                                'type' => 'email'])
                            ->get()
                            ->getRowArray();
                    if (!empty($userAuthResult)) {
                        $data_auth_update = array('value' => $authDataIn["auth_value"]);
                        $this->tblUserAuth
                                ->where([
                                    'user_id' => $authDataIn["user_id"],
                                    'type' => 'email'])
                                ->update($data_auth_update);
                        if ($this->db->affectedRows()) {
                            $dataStore["status"] = "success";
                        } else {
                            $dataStore["status"] = "fail";
                        }
                    } else {
                        $this->tblUserAuth
                                ->insert([
                                    'user_id' => $authDataIn["user_id"],
                                    'type' => 'email',
                                    'value' => $authDataIn["auth_value"]]);
                        if ($this->db->insertID()) {
                            $dataStore["status"] = "success";
                        } else {
                            $dataStore["status"] = "fail";
                        }
                    }
                } else {
                    $userAuthResult = $this->tblUserAuth
                            ->where(['value' => $authDataIn["auth_value"], 'type' => 'phone'])
                            ->get()
                            ->getRowArray();
                    if (!empty($userAuthResult)) {
                        $data_auth_update = array('value' => $authDataIn["auth_value"]);
                        $this->tblUserAuth
                                ->where([
                                    'user_id' => $authDataIn["user_id"],
                                    'type' => 'email'])
                                ->update($data_auth_update);
                        if ($this->db->affectedRows()) {
                            $dataStore["status"] = "success";
                        } else {
                            $dataStore["status"] = "fail";
                        }
                    } else {
                        $this->tblUserAuth
                                ->insert([
                                    'user_id' => $authDataIn["user_id"],
                                    'type' => 'phone',
                                    'value' => $authDataIn["auth_value"]]);
                        if ($this->db->insertID()) {
                            $dataStore["status"] = "success";
                        } else {
                            $dataStore["status"] = "fail";
                        }
                    }
                }
            }
        }
        return $dataStore;
    }

    /**
     * Remove the google account in billex
     * 
     * @param array $dataIn
     * @return array
     */
    public function disconnect_google($dataIn) {
        $this->tblUserAuth
                ->where([
                    'user_id' => $dataIn["user_id"],
                    'type' => 'google'])
                ->delete();
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

}
