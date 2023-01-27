<?php

namespace App\Models;

use CodeIgniter\Model;

class PrintdepositModel extends Model {

    var $session;
    var $tblCredit;
    var $tblDeposit;
    var $tblCreditDetail;
    protected $helpers = ["general"];

    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->tblCredit = $db->table('credit');
        $this->tblDeposit = $db->table('deposit');
        $this->tblCreditDetail = $db->table('credit_detail');
    }

    /**
     * get print deposit amount with customer
     * 
     * 
     * @return array
     * */
    public function get_deposit_amount() {
        $sql = "SELECT credit.amount,credit_id, date_credit,type, reference, GROUP_CONCAT(customer.name SEPARATOR '<br>') customers
                FROM credit
                INNER JOIN credit_detail USING(credit_id)
                INNER JOIN customer USING(customer_id)
                WHERE credit.biller_id = :biller_id: AND credit.type IN ('Check','Cash') AND credit.deposit_id = 0 AND customer.is_deleted = 0 
                GROUP BY credit_id
                ORDER BY date_credit, credit_id";
        $par = ['biller_id' => $this->session->get('biller_id')];
        $dataStore = $this->db->query($sql, $par)->getResultArray();
        return $dataStore;
    }

    /**
     * Add deposit with total amount
     * @param $ids,$amount
     * 
     * @return array
     * */
    public function add_deposit($ids = '', $amount = '') {
        $dataIn['amount'] = $amount;
        $dataIn['user_id_created'] = $this->session->get('login_userid');
        $dataIn['biller_id'] = $this->session->get('biller_id');
        $this->tblDeposit
                ->insert($dataIn);
        if ($this->db->insertID()) {
            $usql = 'UPDATE credit 
                    SET deposit_id = :deposit_id:
                    WHERE biller_id = :biller_id: AND credit_id IN (' . $ids . ')';
            $par = ['biller_id' => $this->session->get('biller_id'), 'deposit_id' => $this->db->insertID()];
            $this->db->query($usql, $par);
            if ($this->db->affectedRows()) {
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
        } else {
            $dataStore["status"] = "fail";
        }

        return $dataStore;
    }

    /**
     * Get deposit credit
     * @param $ids
     * 
     * @return array
     * */
    public function get_deposit_credit($ids = '') {
        $sql = "SELECT credit.amount,credit_id, date_credit,type, reference, GROUP_CONCAT(customer.name SEPARATOR '<br>') customers
                FROM credit
                INNER JOIN credit_detail USING(credit_id)
                INNER JOIN customer USING(customer_id)
                WHERE credit.biller_id = :biller_id: AND credit.type IN ('Check','Cash') AND credit.credit_id IN (" . $ids . ")
                GROUP BY credit_id
                ORDER BY date_credit, credit_id";
        $par = ['biller_id' => $this->session->get('biller_id')];
        $dataStore = $this->db->query($sql, $par)->getResultArray();
        return $dataStore;
    }

    /**
     * Get deposit history with credit data
     * @param
     * 
     * @return array
     * */
    public function get_history_data() {
        $dataStore = $this->tblDeposit
                ->select('deposit.deposit_id,deposit.time_created,deposit.amount')
                ->where(array('deposit.biller_id' => $this->session->get('biller_id'), 'deposit.is_deleted' => 0))
                ->join('credit', 'credit.deposit_id=deposit.deposit_id')
                ->orderBy('deposit.time_created', 'DESC')
                ->groupBy('credit.deposit_id')
                ->get()
                ->getResultArray();
        foreach ($dataStore as $key1 => $value) {
            $dataStore[$key1]['credit_data'] = $this->tblCredit
                    ->where(array('deposit_id' => $value['deposit_id'], 'credit.is_deleted' => 0))
                    ->get()
                    ->getResultArray();
            foreach ($dataStore[$key1]['credit_data'] as $key2 => $cvalue) {
                $dataStore[$key1]['credit_data'][$key2]['credit_detail'] = $this->tblCreditDetail
                        ->select('customer.name,invoice.invoice_number,credit_detail.amount')
                        ->where(array('credit_detail.credit_id' => $cvalue['credit_id']))
                        ->join('invoice', 'credit_detail.invoice_id=invoice.invoice_id')
                        ->join('customer', 'credit_detail.customer_id=customer.customer_id')
                        ->get()
                        ->getResultArray();
            }
        }
        return $dataStore;
    }

    /**
     * Get deposit history with credit data
     * @param $dataIn,$deposit_id
     * 
     * @return array
     * */
    public function undo_deposit($dataIn, $deposit_id) {
        $this->tblCredit
                ->update($dataIn, array('deposit_id' => decrypt(base64_decode($deposit_id)), 'biller_id' => $this->session->get('biller_id')));
        $this->tblDeposit
                ->update(array('is_deleted' => 1), array('deposit_id' => decrypt(base64_decode($deposit_id)), 'biller_id' => $this->session->get('biller_id')));
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

}
