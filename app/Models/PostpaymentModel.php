<?php

namespace App\Models;

use CodeIgniter\Model;

class PostpaymentModel extends Model {

    var $tblCustomer;
    var $tblCredit;
    var $tblCreditDetail;
    var $tblInvoice;
    var $tblCharge;
    var $tblStripeTransaction;
    var $tblWepayTransaction;
    var $session;
    protected $helpers = ["general"];

    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
        $this->tblCustomer = $db->table('customer');
        $this->tblCredit = $db->table('credit');
        $this->tblCreditDetail = $db->table('credit_detail');
        $this->tblInvoice = $db->table('invoice');
        $this->tblStripeTransaction = $db->table('stripe_transaction');
        $this->tblWepayTransaction = $db->table('wepay_transaction');
        $this->tblCharge = $db->table('charge');
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    /**
     * get total Biller customer data
     * 
     * @param int $customer_id
     * 
     * @return array
     * */
    public function get_post_payment_customer($customer_id = '') {
        if ($customer_id == '') {
            $dataStore = $this->tblCustomer
                    ->where(array('biller_user.biller_id' => $this->session->get('biller_id'), 'customer.balance >' => 0, 'customer.is_deleted' => 0))
                    ->join('biller_user', 'customer.user_id_created=biller_user.user_id', 'left')
                    ->get()
                    ->getResultArray();
            foreach ($dataStore as $key => $value) {
                $sql = 'SELECT i.*, i.amount - COALESCE(SUM(c.amount),0) AS invoice_balance 
                        FROM invoice AS i 
                        LEFT JOIN credit_detail AS c ON i.invoice_id = c.invoice_id 
                        INNER JOIN charge AS ch ON i.invoice_id=ch.invoice_id
                        WHERE i.customer_id = :customer_id: AND i.paid = 0 AND i.is_deleted=0
                        GROUP BY i.invoice_id';
                $par = array('customer_id' => $value['customer_id']);
                $dataStore[$key]['invoice_data'] = $this->db->query($sql, $par)->getResultArray();
            }
            return $dataStore;
        } else {
            $dataStore = $this->tblCustomer
                    ->where(array('biller_user.biller_id' => $this->session->get('biller_id'), 'customer.balance >' => 0, 'customer.customer_id' => $customer_id, 'customer.is_deleted' => 0))
                    ->join('biller_user', 'customer.user_id_created=biller_user.user_id', 'left')
                    ->get()
                    ->getResultArray();
            foreach ($dataStore as $key => $value) {
                $sql = 'SELECT i.*, i.amount - COALESCE(SUM(c.amount),0) AS invoice_balance 
                        FROM invoice AS i 
                        LEFT JOIN credit_detail AS c ON i.invoice_id = c.invoice_id 
                        WHERE i.customer_id = :customer_id: AND i.paid = 0 AND i.is_deleted=0
                        GROUP BY i.invoice_id';
                $par = array('customer_id' => $value['customer_id']);
                $dataStore[$key]['invoice_data'] = $this->db->query($sql, $par)->getResultArray();
                $dataStore[$key]['invoice_data'] = $this->db->query($sql, $par)->getResultArray();
            }
            return $dataStore;
        }
    }

    /**
     * get customer based on invoice number
     * @param array $invoiceNumber
     * 
     * @return array
     * */
    public function get_invoice_customer($invoiceNumber) {
        $sql = "SELECT *
                FROM `invoice` 
                INNER JOIN customer USING(customer_id)
                WHERE invoice.biller_id = :biller_id: AND customer.balance > 0 AND customer.is_deleted = 0 AND invoice.invoice_number IN ('" . $invoiceNumber . "')";
        $par = ['biller_id' => $this->session->get('biller_id')];
        $dataStore = $this->db->query($sql, $par)->getResultArray();
        return $dataStore;
    }

    /**
     * Add post payment data
     * @param array $dataIn
     * 
     * @return array
     * */
    public function add_post_payment($dataIn, $credit_id = '') {
        $customer_id = $dataIn['customer_id'];
        unset($dataIn['customer_id']);
        if ($credit_id == '') {
            $this->tblCredit->insert($dataIn);
            if ($this->db->insertID()) {
                $this->session->set(array('credit_id' => $this->db->insertID()));
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        } else {
            $dataIn['credit_id'] = decrypt(base64_decode($credit_id));
            $this->tblCredit
                    ->update($dataIn, array('credit_id' => decrypt(base64_decode($credit_id))));
            if ($this->db->affectedRows()) {
                $dataStore["status"] = "success";
                $dataCreditIn['amount'] = $dataIn['amount'];
                $this->tblCreditDetail
                        ->update($dataCreditIn, array('credit_id' => decrypt(base64_decode($credit_id)), 'customer_id' => decrypt(base64_decode($customer_id))));
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        }
    }

    /**
     * Add credit details value
     * @param array $dataIn
     * 
     * @return array
     * */
    public function add_credit_details($dataIn) {
        $dataIn['credit_id'] = $this->session->get('credit_id');
        if (isset($dataIn['user_id_created'])) {
            $dataIn['user_id_created'] = 0;
        } else {
            $dataIn['user_id_created'] = $this->session->get('login_userid');
        }

        $this->tblCreditDetail->insert($dataIn);
        if ($this->db->insertID()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Add invoice credit details value
     * @param array $dataIn
     * 
     * @return array
     * */
    public function add_invoice_credit($dataIn) {
        if (!isset($dataIn['credit_id']) && empty($dataIn['credit_id'])) {
            $dataIn['credit_id'] = $this->session->get('credit_id');
            $dataIn['user_id_created'] = $this->session->get('login_userid');
        }
        $this->tblCreditDetail
                ->insert($dataIn);
        if ($this->db->insertID()) {
            $dataCredit = $this->tblCreditDetail
                    ->where(array('invoice_id' => $dataIn['invoice_id']))
                    ->get()
                    ->getResultArray();
            $total_invoice = 0;
            foreach ($dataCredit as $value) {
                $total_invoice = $total_invoice + floatval($value['amount']);
            }
            $sql = "UPDATE `invoice`
                    SET `paid`= 1
                    WHERE invoice_id=:invoice_id: AND amount <= :amount:";
            $par = array('invoice_id' => $dataIn['invoice_id'], 'amount' => $total_invoice);
            $this->db->query($sql, $par);
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
        return $dataStore;
    }

    /**
     * Get credit and credit_details information using refernce
     * 
     * @param array $reference
     * 
     * @return array
     * */
    public function get_credit($reference) {
        $dataStore = $this->tblCredit
                ->where(array('credit.is_deleted' => 0, 'credit.reference' => $reference))
                ->join('credit_detail', 'credit_detail.credit_id=credit.credit_id')
                ->join('customer', 'credit_detail.customer_id=customer.customer_id')
                ->get()
                ->getRowArray();
        return $dataStore;
    }

    /**
     * Get credit and credit_details information using credit id
     * 
     * @param array $credit_id
     * 
     * @return array
     * */
    public function get_payment_credit($credit_id = '') {
        $dataStore = $this->tblCredit
                ->where(array('credit.is_deleted' => 0, 'credit.credit_id' => decrypt(base64_decode($credit_id))))
                ->join('credit_detail', 'credit_detail.credit_id=credit.credit_id')
                ->join('customer', 'credit_detail.customer_id=customer.customer_id')
                ->get()
                ->getRowArray();
        return $dataStore;
    }

    /**
     * Update payment status on web hook.
     * 
     * @param array $status,$credit_id
     * 
     * @return array
     * */
    public function update_payment_status($status, $credit_id) {
        $this->tblCredit
                ->update($status, array('credit_id' => $credit_id));
        if ($this->db->affectedRows()) {
            $dataStore["status"] = "success";
        } else {
            $dataStore["status"] = "fail";
        }
    }

    /**
     * Get invoice using customer id
     * 
     * @param string $customer_id
     * 
     * @return array
     * */
    public function get_credit_invoice($customer_id = '') {
        $sql = 'SELECT i.*, i.amount - COALESCE(SUM(c.amount),0) AS invoice_balance 
                        FROM invoice AS i 
                        LEFT JOIN credit_detail AS c ON i.invoice_id = c.invoice_id 
                        WHERE i.customer_id = :customer_id: AND i.paid = 0 AND i.is_deleted=0
                        GROUP BY i.invoice_id';
        $par = array('customer_id' => $customer_id);
        $dataStore = $this->db->query($sql, $par)->getResultArray();
        return $dataStore;
    }

    /**
     * Add Invoice for webhook
     * 
     * @param array $dataIn
     * 
     * @return array
     * */
    public function add_payment_invoice($dataIn, $id = '') {
        if ($id == '') {
            $stripeDataIn['data'] = \GuzzleHttp\json_encode($dataIn);
            $stripeDataIn['transaction_id'] = random_strings();
            $this->tblStripeTransaction->insert($stripeDataIn);
            if ($this->db->insertID()) {
                $dataStore['id'] = $this->db->insertID();
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        } else {
            $this->tblStripeTransaction
                    ->update($dataIn, array('id' => decrypt(base64_decode($id))));
            if ($this->db->affectedRows()) {
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
        }
    }

    /**
     * Add Invoice for webhook
     * 
     * @param array $dataIn
     * 
     * @return array
     * */
    public function add_wepay_invoice($dataIn, $id = '') {
        if ($id == '') {
            $stripeDataIn['data'] = \GuzzleHttp\json_encode($dataIn);
            $stripeDataIn['transaction_id'] = random_strings();
            $this->tblWepayTransaction->insert($stripeDataIn);
            if ($this->db->insertID()) {
                $dataStore['id'] = $this->db->insertID();
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
            return $dataStore;
        } else {
            $this->tblWepayTransaction
                    ->update($dataIn, array('id' => decrypt(base64_decode($id))));
            if ($this->db->affectedRows()) {
                $dataStore["status"] = "success";
            } else {
                $dataStore["status"] = "fail";
            }
        }
    }

    /**
     * Get Stripe Transaction for add invoice in credit details
     * 
     * @param array $transaction_id
     * 
     * @return array
     * */
    public function get_stripe_transaction($transaction_id) {
        $dataStore = $this->tblStripeTransaction
                ->where(array('transaction_id' => $transaction_id))
                ->get()
                ->getRowArray();
        return $dataStore;
    }

    /**
     * Get wepay Transaction for add invoice in credit details
     * 
     * @param array $transaction_id
     * 
     * @return array
     * */
    public function get_wepay_transaction($transaction_id = '', $wepay_id = '') {
        if ($wepay_id == "") {
            $dataStore = $this->tblWepayTransaction
                    ->where(array('transaction_id' => $transaction_id))
                    ->get()
                    ->getRowArray();
            return $dataStore;
        } else {
            $dataStore = $this->tblWepayTransaction
                    ->where(array('id' => $wepay_id))
                    ->get()
                    ->getRowArray();
            return $dataStore;
        }
    }

    /**
     * Get invoice details for to_payer and to_payee email
     * 
     * @param array $invoice_id
     * 
     * @return array
     * */
    public function get_invoices($invoice_ids) {
        $dataStore = $this->tblInvoice
                ->whereIn('invoice_id', $invoice_ids)
                ->get()
                ->getResultArray();
        return $dataStore;
    }

}
