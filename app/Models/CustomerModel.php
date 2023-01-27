<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model {

    var $tblCustomer;
    var $tblcharge;
    var $tblChargeType;
    var $tblRecurringCharge;
    var $tblCustomerItem;
    var $tblItem;
    var $tblInvoice;
    var $tblCredit;
    var $tblCreditDetail;
    var $session;
    protected $helpers = ["general"];

    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
        $this->tblCustomer = $db->table('customer');
        $this->tblcharge = $db->table('charge');
        $this->tblChargeType = $db->table('charge_type');
        $this->tblRecurringCharge = $db->table('recurring_charge');
        $this->tblCustomerItem = $db->table('customer_item');
        $this->tblItem = $db->table('item');
        $this->tblInvoice = $db->table('invoice');
        $this->tblCredit = $db->table('credit');
        $this->tblCreditDetail = $db->table('credit_detail');
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    /**
     * get total Biller customer data
     * 
     * 
     * @return array
     * */
    public function get_customer($customerID = '') {
        if ($customerID == "") {
            $dataStore = $this->tblCustomer
                    ->where(array('biller_user.biller_id' => $this->session->get('biller_id'), 'customer.is_deleted' => 0))
                    ->join('biller_user', 'customer.user_id_created=biller_user.user_id', 'left')
                    ->orderBy('name', 'ASC')
                    ->get()
                    ->getResultArray();
            return $dataStore;
        } else {
            $dataStore = $this->tblCustomer
                    ->where('customer_id', decrypt(base64_decode($customerID)))
                    ->get()
                    ->getRowArray();
            return $dataStore;
        }
    }

    /**
     * Biller customer data insert or update
     * 
     * @param array $dataIn,$customer_id
     * @return array
     * */
    public function add_edit_biller_customer($dataIn, $customer_id = '') {
        if ($customer_id == "") {
            $this->tblCustomer->insert($dataIn);
            if ($this->db->insertID()) {
                $dataStore["status"] = "success";
                $dataStore['id'] = base64_encode(encrypt($this->db->insertID()));
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        } else {
            $this->tblCustomer
                    ->update($dataIn, array('customer_id' => decrypt(base64_decode($customer_id))));
            if ($this->db->affectedRows()) {
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        }
    }

    /**
     * Update customer balance as per post payment
     * 
     * @param array $customer_id,$balance
     * @return array
     * */
    public function update_balance($customer_id, $balance) {
        $sql_update = 'UPDATE customer
                SET balance = balance - (' . $balance . ')
                WHERE customer_id = :customer_id: AND is_deleted = 0';
        $par = ['customer_id' => $customer_id];
        $this->db->query($sql_update, $par);
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * add or edit Biller customer charges
     * 
     * @param array $dataIn
     * @return array
     * */
    public function add_edit_charegs($dataIn, $charge_id = '') {
        if ($charge_id == '') {
            $this->tblcharge->insert($dataIn);
            if ($this->db->insertID()) {
                $dataStore["status"] = "success";
                $dataStore["charge_id"] = $this->db->insertID();
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        } else {
            $this->tblcharge
                    ->update($dataIn, array('charge_id' => decrypt(base64_decode($charge_id))));
            if ($this->db->affectedRows()) {
                $sql = 'SELECT COUNT(*) AS total 
                        FROM download AS d 
                        INNER JOIN charge AS ch ON ch.charge_id=d.charge_id';
                $download = $this->db->query($sql)->getRowArray();
                if ($download['total'] >= 1) {
                    $dataStore['download'] = 1;
                } else {
                    $dataStore['download'] = 0;
                }
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        }
    }

    /**
     * Edit invoice_id for undo invoice
     * 
     * @param array $dataIn
     * @return array
     * */
    public function edit_charegs_invoice($dataIn, $invoice = '') {
        $this->tblcharge
                ->update($dataIn, array('invoice_id' => decrypt(base64_decode($invoice))));
        $this->tblInvoice
                ->update(array('is_deleted' => 1), array('invoice_id' => decrypt(base64_decode($invoice))));
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * get Biller customer charges
     * 
     * @param $customerID,$charge_id
     * @return array
     * */
    public function get_charges($customerID = '', $charge_id = '') {
        if ($charge_id == '') {
            $dataStore = $this->tblcharge
                    ->select('charge.charge_id,charge.customer_id,charge.amount,charge.date_charge,charge.invoice_id,charge.quantity,charge.description,charge.rate,charge.item_id,charge.is_deleted,charge_type.ct_id,charge_type.name')
                    ->where(array('charge.customer_id' => decrypt(base64_decode($customerID)), 'charge.is_deleted' => 0))
                    ->join('charge_type', 'charge_type.ct_id=charge.ct_id', 'INNER')
                    ->orderBy('date_charge,charge_id', 'DESC')
                    ->limit(50)
                    ->get()
                    ->getResultArray();
            return $dataStore;
        } else {
            $dataStore = $this->tblcharge
                    ->select('charge.charge_id,charge.customer_id,charge.amount,charge.date_charge,charge.quantity,charge.invoice_id,charge.description,charge.rate,charge.item_id,charge.is_deleted,charge_type.ct_id,charge_type.name')
                    ->where(array('is_deleted' => 0, 'charge_id' => decrypt(base64_decode($charge_id))))
                    ->join('charge_type', 'charge_type.ct_id=charge.ct_id')
                    ->orderBy('date_charge', 'DESC')
                    ->limit(50)
                    ->get()
                    ->getRowArray();
            return $dataStore;
        }
    }

    /**
     * get invoice charges as per customer id
     * 
     * @param $customerID
     * @return array
     * */
    public function get_invoice_charges($customerID = '') {
        $dataStore = $this->tblcharge
                ->select('charge.charge_id,charge.customer_id,charge.amount,charge.date_charge,charge.quantity,charge.invoice_id,charge.description,charge.rate,charge.item_id,charge.is_deleted,charge_type.ct_id,charge_type.name')
                ->where(array('customer_id' => decrypt(base64_decode($customerID)), 'is_deleted' => 0, 'invoice_id' => 0))
                ->join('charge_type', 'charge_type.ct_id=charge.ct_id')
                ->orderBy('date_charge,charge_id', 'DESC')
                ->limit(50)
                ->get()
                ->getResultArray();
        return $dataStore;
    }

    /**
     * get Charge type id from type
     * 
     * @param array $type
     * @return id
     * */
    public function get_charge_type($type) {
        $ct_id = $this->tblChargeType
                        ->where('name', $type)
                        ->get()
                        ->getRowArray()['ct_id'];
        return $ct_id;
    }

    /**
     * get Biller customer recuring
     * 
     * @param $customerID,$rc_id
     * @return array
     * */
    public function get_recuring($customerID = '', $rc_id = '') {
        if ($rc_id == '') {
            $dataStore = $this->tblRecurringCharge
                    ->select('recurring_charge.rc_id,recurring_charge.customer_id,recurring_charge.frequency,recurring_charge.date_next,recurring_charge.quantity,recurring_charge.description,recurring_charge.rate,recurring_charge.item_id,recurring_charge.is_deleted,charge_type.ct_id,charge_type.name')
                    ->where(array('customer_id' => decrypt(base64_decode($customerID)), 'is_deleted' => 0))
                    ->join('charge_type', 'charge_type.ct_id=recurring_charge.ct_id')
                    ->orderBy('date_next', 'DESC')
                    ->limit(50)
                    ->get()
                    ->getResultArray();
            return $dataStore;
        } else {
            $dataStore = $this->tblRecurringCharge
                    ->select('recurring_charge.rc_id,recurring_charge.customer_id,recurring_charge.frequency,recurring_charge.date_next,recurring_charge.quantity,recurring_charge.description,recurring_charge.rate,recurring_charge.item_id,recurring_charge.is_deleted,charge_type.ct_id,charge_type.name')
                    ->where(array('is_deleted' => 0, 'rc_id' => decrypt(base64_decode($rc_id))))
                    ->join('charge_type', 'charge_type.ct_id=recurring_charge.ct_id')
                    ->orderBy('date_next', 'DESC')
                    ->limit(50)
                    ->get()
                    ->getRowArray();
            return $dataStore;
        }
    }

    /**
     * add or edit Biller customer recuring
     * 
     * @param array $dataIn
     * @return array
     * */
    public function add_edit_recuring($dataIn, $rc_id = '') {
        if ($rc_id == '') {
            $this->tblRecurringCharge->insert($dataIn);
            if ($this->db->insertID()) {
                $dataStore["status"] = "success";
                $dataStore["rc_id"] = $this->db->insertID();
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        } else {
            $this->tblRecurringCharge
                    ->update($dataIn, array('rc_id' => decrypt(base64_decode($rc_id))));
            if ($this->db->affectedRows()) {
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        }
    }

    /**
     * get all customer recurring for cron
     * 
     *
     * @return array
     * */
    public function get_all_recurring() {
        $dataStore = $this->tblRecurringCharge
                ->select('recurring_charge.rc_id,recurring_charge.customer_id,recurring_charge.frequency,recurring_charge.date_next,recurring_charge.quantity,recurring_charge.description,recurring_charge.rate,recurring_charge.item_id,recurring_charge.user_id_created,recurring_charge.user_id_updated,recurring_charge.is_deleted,charge_type.ct_id,charge_type.name')
                ->where('is_deleted', 0)
                ->join('charge_type', 'charge_type.ct_id=recurring_charge.ct_id')
                ->orderBy('date_next', 'DESC')
                ->limit(50)
                ->get()
                ->getResultArray();
        return $dataStore;
    }

    /**
     * get all customer rates
     * 
     * @param $customerID,$id
     * @return array
     * */
    public function get_rates($customerID = '', $id = '') {
        if ($id == '') {
            $sql = 'SELECT i.item_id as item,i.*,ci.*
                    FROM item AS i 
                    LEFT JOIN customer_item AS ci 
                    ON i.item_id= ci.item_id 
                    AND (ci.customer_id IS NULL OR ci.customer_id=:customer_id:)
                    WHERE i.user_id_created=:user_id:';
            $par = ['customer_id' => decrypt(base64_decode($customerID)), 'user_id' => $this->session->get('login_userid')];
            $userResultData = $this->db->query($sql, $par)->getResultArray();
            return $userResultData;
        }
    }

    /**
     * add or edit Biller customer rate
     * 
     * @param array $dataIn
     * @return array
     * */
    public function add_edit_rates($dataIn, $id = '') {

        if ($id == '') {
            $this->tblCustomerItem
                    ->insert($dataIn);
            if ($this->db->insertID()) {
                $dataStore["status"] = "success";
                $dataStore["rate_id"] = $this->db->insertID();
                $dataStore["msg"] = 'Customer rate add successfully.';
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        } else {

            $this->tblCustomerItem
                    ->update($dataIn, array('id' => $id));
            if ($this->db->affectedRows()) {
                $dataStore["status"] = "success";
                $dataStore["msg"] = 'Customer rate updated successfully.';
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        }
    }

    /**
     * delete Biller customer rate
     * 
     * @param array $id
     * @return array
     * */
    public function delete_rates($id) {
        $this->tblCustomerItem
                ->where(['id' => $id])
                ->delete();
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
            $dataStore["msg"] = 'Customer rate removed successfully.';
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * get invoice details
     * 
     * @param 
     * @return array
     * */
    public function get_invoice() {
        $dataStore = $this->db->table('invoice')
                ->orderBy('invoice_id', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();
        return $dataStore;
    }

    /**
     * get customer register data
     * 
     * @param $customerID
     * @return array
     * */
    public function get_register($customerID = '') {
        $dataStore['invoice_data'] = $this->tblCustomer
                ->join('invoice', 'customer.customer_id=invoice.customer_id')
                ->where(array('invoice.is_deleted' => 0, 'customer.customer_id' => decrypt(base64_decode($customerID)), 'customer.is_deleted' => 0))
                ->orderBy('invoice.time_created', 'ASC')
                ->get()
                ->getResultArray();
        $sql = 'SELECT credit.*,credit_detail.`credit_id`,COUNT(credit_detail_id) count,SUM(credit_detail.amount) total 
                FROM `credit_detail` 
                INNER JOIN credit ON credit.credit_id = credit_detail.credit_id 
                WHERE customer_id=:customer_id: AND credit_detail.is_deleted = 0
                GROUP BY credit_detail.credit_id';
        $par = ['customer_id' => decrypt(base64_decode($customerID))];
        $dataStore['credit_data'] = $this->db->query($sql, $par)->getResultArray();
        $final_data = array_merge($dataStore['invoice_data'], $dataStore['credit_data']);
        usort($final_data, function( $a, $b ) {
            if ($a['time_created'] === $b['time_created']) {
                return 0;
            }
            return ( strtotime($a['time_created']) < strtotime($b['time_created']) ) ? 1 : -1;
        });
        return $final_data;
    }

}
