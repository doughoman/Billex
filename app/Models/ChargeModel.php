<?php

namespace App\Models;

use CodeIgniter\Model;

class ChargeModel extends Model {

    var $tblCustomer;
    var $tblcharge;
    var $tblChargeType;
    var $tblInvoice;
    var $tblDownload;
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
        $this->tblInvoice = $db->table('invoice');
        $this->tblDownload = $db->table('download');
        $this->tblCredit = $db->table('credit');
        $this->tblCreditDetail = $db->table('credit_detail');
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    /**
     * get Charge type 
     * 
     * @param 
     * @return array
     * */
    public function get_charge_type($ct_id = '') {
        if ($ct_id == '') {
            $dataStore = $this->tblChargeType
                    ->orderBy('display_order', 'ASC')
                    ->get()
                    ->getResultArray();
        } else {
            $dataStore = $this->tblChargeType
                    ->where(array('ct_id' => $ct_id))
                    ->orderBy('display_order', 'ASC')
                    ->get()
                    ->getRowArray();
        }
        return $dataStore;
    }

    /**
     * Add charge invoice
     * 
     * @param $dataIn,$customer_id
     * @return array
     * */
    public function add_invoice($dataIn, $customer_id) {
        $sql = "SELECT SUM(`quantity`*`rate`) as total 
                FROM charge 
                WHERE `customer_id`= :customer_id: AND invoice_id = 0 AND is_deleted = 0";
        $par = ['customer_id' => $customer_id];
        $userTotalInvoice = $this->db->query($sql, $par)->getRowArray();
        $dataIn['amount'] = $userTotalInvoice['total'];
        $dataIn['customer_id'] = $customer_id;
        $this->tblInvoice
                ->insert($dataIn);
        if ($this->db->insertID()) {
            $dataChargeIn['invoice_id'] = $this->db->insertID();
            $this->tblcharge
                    ->update($dataChargeIn, array('customer_id' => $customer_id, 'invoice_id' => 0, 'is_deleted' => 0));
        }
        $sql_update = 'UPDATE customer
                SET balance = balance + (' . $dataIn['amount'] . '), balance_time = CURRENT_TIMESTAMP()
                WHERE customer_id = :customer_id:';
        $this->db->query($sql_update, $par);
    }

    /**
     * Add charge download information
     * 
     * @param $dataIn
     * @return array
     * */
    public function add_download($dataIn) {
        $this->tblDownload
                ->insert($dataIn);
        if ($this->db->insertID()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Get download charge using invoice_id
     * 
     * @param array $invoice_id
     * @return array
     * */
    public function get_download_charge($invoice_ids) {
        $dataStore = $this->tblcharge
                ->where('ct_id', 7)
                ->whereIn('invoice_id', $invoice_ids)
                ->join('download', 'charge.charge_id=download.charge_id')
                ->get()
                ->getResultArray();
        return $dataStore;
    }

    /**
     * Get two recent credits for invoice preview
     * 
     * @param array $customerID
     * @return array
     * */
    public function get_recent_credits($customerID) {
        $dataStore = $this->tblCredit
                ->select('date_credit,type,reference,description,credit.amount')
                ->where(array('customer_id' => decrypt(base64_decode($customerID)), 'credit_detail.is_deleted' => 0, 'credit_detail.invoice_id!=' => 0))
                ->join('credit_detail', 'credit.credit_id = credit_detail.credit_id', 'INNER')
                ->groupBy('credit_detail.credit_id')
                ->orderBy('credit.date_credit,credit.credit_id', 'DESC')
                ->limit(2)
                ->get()
                ->getResultArray();
        return $dataStore;
    }

    /**
     * Get two recent invoices for invoice preview
     * 
     * @param array $customerID
     * @return array
     * */
    public function get_recent_invoices($customerID) {
        $dataStore = $this->tblInvoice
                ->where(array('customer_id' => decrypt(base64_decode($customerID)), 'is_deleted' => 0))
                ->orderBy('time_created,invoice_id', 'DESC')
                ->limit(2)
                ->get()
                ->getResultArray();
        return $dataStore;
    }

    /**
     * Get invoice for transaction history
     * 
     * @param array $invoice_id
     * @return array
     * */
    public function get_invoice($invoice_id) {
        $dataStore['invoice_data'] = $this->tblInvoice
                ->where(array('invoice_id' => $invoice_id, 'invoice.is_deleted' => 0))
                ->orderBy('invoice.time_created', 'ASC')
                ->get()
                ->getResultArray();
        $sql = 'SELECT credit.*,credit_detail.`credit_id`,COUNT(credit_detail_id) count,SUM(credit_detail.amount) total 
                FROM `credit_detail` 
                INNER JOIN credit ON credit.credit_id = credit_detail.credit_id 
                WHERE invoice_id=:invoice_id: AND credit_detail.is_deleted = 0
                GROUP BY credit_detail.credit_id';
        $par = ['invoice_id' => $invoice_id];
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

    /**
     * Get customer information for invoice history
     * 
     * @param array $invoice_id
     * @return array
     * */
    public function get_invoice_customer($invoice_id) {
        $dataStore = $this->tblInvoice
                ->where(array('invoice_id' => $invoice_id, 'invoice.is_deleted' => 0))
                ->join('customer', 'customer.customer_id = invoice.customer_id', 'INNER')
                ->orderBy('invoice.time_created', 'ASC')
                ->get()
                ->getRowArray();
        return $dataStore;
    }

}
