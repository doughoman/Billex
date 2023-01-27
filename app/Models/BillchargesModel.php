<?php

namespace App\Models;

use CodeIgniter\Model;

class BillchargesModel extends Model {

    var $session;
    protected $helpers = ["general"];
    var $tblInvoiceBatchFile;
    var $tblcharge;
    var $tblInvoice;
    var $tblCredit;
    var $tblCreditDetail;

    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->tblInvoiceBatchFile = $db->table('invoice_batch_file');
        $this->tblcharge = $db->table('charge');
        $this->tblInvoice = $db->table('invoice');
        $this->tblCredit = $db->table('credit');
        $this->tblCreditDetail = $db->table('credit_detail');
    }

    /**
     * Get panding bill chares with customer
     * 
     * @param date $dataIn
     * 
     * @return array
     * */
    public function get_bill_charges($dateIn = '') {
        $sql = "SELECT cu.customer_id, cu.name, cu.identifier, cu.po, cu.balance,cu.status,cu.email_to_list,cu.address_mail_attention,cu.address_mail_street,cu.address_mail_city,cu.address_mail_state,cu.address_mail_zip5,
                SUM(ch.rate*ch.quantity) AS total, COUNT(ch.charge_id) AS count
                FROM charge AS ch
                INNER JOIN customer AS cu USING(customer_id)
                WHERE cu.biller_id = :biller_id: AND cu.is_deleted=0
                AND ch.is_deleted = 0
                AND ch.invoice_id = 0
                AND ch.date_charge <= :date_charge:
                GROUP BY cu.customer_id
                ORDER BY cu.name, cu.identifier";
        if ($dateIn == '') {
            $par = array('biller_id' => $this->session->get('biller_id'), 'date_charge' => date('Y-m-d', strtotime("-1 days")));
        } else {
            $par = array('biller_id' => $this->session->get('biller_id'), 'date_charge' => date('Y-m-d', strtotime($dateIn)));
        }
        $dataStore = $this->db->query($sql, $par)->getResultArray();
        return $dataStore;
    }

    /**
     * Get preview panding bill chares for selected customer
     * 
     * @param int $customer_ids
     * 
     * @return array
     * */
    public function get_preview_customer($customer_ids = '') {
        $sql = "SELECT cu.*,
                SUM(ch.rate*ch.quantity) AS total, COUNT(ch.charge_id) AS count
                FROM charge AS ch
                INNER JOIN customer AS cu USING(customer_id)
                WHERE cu.biller_id = :biller_id: AND cu.is_deleted=0
                AND ch.is_deleted = 0
                AND ch.invoice_id = 0
                AND cu.customer_id IN (" . $customer_ids . ")
                GROUP BY cu.customer_id
                ORDER BY cu.name, cu.identifier";
        $par = array('biller_id' => $this->session->get('biller_id'));
        $dataStore = $this->db->query($sql, $par)->getResultArray();
        foreach ($dataStore as $key => $value) {
            $dataStore[$key]['charges'] = $this->tblcharge
                    ->select('charge.charge_id,charge.customer_id,charge.amount,charge.date_charge,charge.quantity,charge.invoice_id,charge.description,charge.rate,charge.item_id,charge.is_deleted,charge_type.ct_id,charge_type.name')
                    ->where(array('customer_id' => $value['customer_id'], 'is_deleted' => 0, 'invoice_id' => 0))
                    ->join('charge_type', 'charge_type.ct_id=charge.ct_id')
                    ->orderBy('date_charge,charge_id', 'DESC')
                    ->limit(50)
                    ->get()
                    ->getResultArray();
            $customerID = base64_encode(encrypt($value['customer_id']));
            $dataStore[$key]['recent_charges'] = $this->tblCredit
                    ->select('date_credit,type,reference,description,credit.amount')
                    ->where(array('customer_id' => decrypt(base64_decode($customerID)), 'credit_detail.is_deleted' => 0, 'credit_detail.invoice_id!=' => 0))
                    ->join('credit_detail', 'credit.credit_id = credit_detail.credit_id', 'INNER')
                    ->groupBy('credit_detail.credit_id')
                    ->orderBy('credit.date_credit,credit.credit_id', 'DESC')
                    ->limit(2)
                    ->get()
                    ->getResultArray();
            $dataStore[$key]['recent_invoices'] = $this->tblInvoice
                    ->where(array('customer_id' => decrypt(base64_decode($customerID)), 'is_deleted' => 0))
                    ->orderBy('time_created,invoice_id', 'DESC')
                    ->limit(2)
                    ->get()
                    ->getResultArray();
        }
        return $dataStore;
    }

    /**
     * Add invoice batch file details
     * 
     * @param array $dataIn
     * 
     * @return array
     * */
    public function add_invoice_batch($dataIn) {
        $this->tblInvoiceBatchFile
                ->insert($dataIn);
        if ($this->db->insertID()) {
            $dataStore["status"] = "success";
            $dataStore["ifb_id"] = $this->db->insertID();
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Get batch table data 
     * 
     * @return array
     * */
    public function get_batch_data() {
        $dataStore = $this->tblInvoiceBatchFile
                ->where('biller_id', $this->session->get('biller_id'))
                ->join('user', 'user.user_id=invoice_batch_file.user_id_created')
                ->orderBy('invoice_batch_file.time_created', 'DESC')
                ->get()
                ->getResultArray();
        return $dataStore;
    }

}
