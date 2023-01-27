<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model {

    var $session;
    protected $helpers = ["general"];
    var $tblCharge;

    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->tblCharge = $db->table('charge');
    }

    /**
     * Get total invoice amount month wise
     * 
     * @return array
     * */
    public function get_total_invoice() {
        $filename = WRITEPATH . 'cache/' . $this->session->get('biller_id') . '_total_invoice';
        if (file_exists($filename)) {
            if (date("m/d/y") != date("m/d/Y", filemtime($filename))) {
                unlink(WRITEPATH . 'cache/' . $this->session->get('biller_id') . '_total_invoice');
            }
        }
        if (!$foo = cache($this->session->get('biller_id') . '_total_invoice')) {
            $sql = "SELECT SUM(IF(month = '1', total, 0)) AS '1',
                SUM(IF(month = '2', total, 0)) AS '2', 
                SUM(IF(month = '3', total, 0)) AS '3', 
                SUM(IF(month = '4', total, 0)) AS '4', 
                SUM(IF(month = '5', total, 0)) AS '5', 
                SUM(IF(month = '6', total, 0)) AS '6', 
                SUM(IF(month = '7', total, 0)) AS '7', 
                SUM(IF(month = '8', total, 0)) AS '8', 
                SUM(IF(month = '9', total, 0)) AS '9', 
                SUM(IF(month = '10', total, 0)) AS '10', 
                SUM(IF(month = '11', total, 0)) AS '11', 
                SUM(IF(month = '12', total, 0)) AS '12' 
                FROM ( SELECT SUM(amount) AS total, MONTH(time_created) AS month 
                FROM invoice 
                WHERE biller_id = :biller_id: AND is_deleted = 0 
                GROUP BY MONTH(time_created)
                ORDER BY month ASC ) as invoice";
            $par = array('biller_id' => $this->session->get('biller_id'));
            $dataStore = $this->db->query($sql, $par)->getResultArray();
            cache()->save($this->session->get('biller_id') . '_total_invoice', $dataStore, 86400);
        } else {
            $cache = \Config\Services::cache();
            $dataStore = $cache->get($this->session->get('biller_id') . '_total_invoice');
        }

        return $dataStore;
    }

    /**
     * Get total credit amount month wise
     * 
     * @return array
     * */
    public function get_total_credit() {
        $filename = WRITEPATH . 'cache/' . $this->session->get('biller_id') . '_total_credit';
        if (file_exists($filename)) {
            if (date("m/d/y") != date("m/d/Y", filemtime($filename))) {
                unlink(WRITEPATH . 'cache/' . $this->session->get('biller_id') . '_total_credit');
            }
        }
        if (!$foo = cache($this->session->get('biller_id') . '_total_credit')) {
            $sql = "SELECT SUM(IF(month = '1', total, 0)) AS '1',
                SUM(IF(month = '2', total, 0)) AS '2', 
                SUM(IF(month = '3', total, 0)) AS '3', 
                SUM(IF(month = '4', total, 0)) AS '4', 
                SUM(IF(month = '5', total, 0)) AS '5', 
                SUM(IF(month = '6', total, 0)) AS '6', 
                SUM(IF(month = '7', total, 0)) AS '7', 
                SUM(IF(month = '8', total, 0)) AS '8', 
                SUM(IF(month = '9', total, 0)) AS '9', 
                SUM(IF(month = '10', total, 0)) AS '10', 
                SUM(IF(month = '11', total, 0)) AS '11', 
                SUM(IF(month = '12', total, 0)) AS '12' 
                FROM ( SELECT SUM(amount) AS total, MONTH(time_created) AS month 
                FROM credit 
                WHERE biller_id = :biller_id: AND is_deleted = 0 
                GROUP BY MONTH(time_created)
                ORDER BY month ASC ) as credit";
            $par = array('biller_id' => $this->session->get('biller_id'));
            $dataStore = $this->db->query($sql, $par)->getResultArray();
            cache()->save($this->session->get('biller_id') . '_total_credit', $dataStore, 86400);
        } else {
            $cache = \Config\Services::cache();
            $dataStore = $cache->get($this->session->get('biller_id') . '_total_credit');
        }
        return $dataStore;
    }

    /**
     * Get total undeposited items total
     * 
     * @return array
     * */
    public function get_undeposited_items() {
        $sql = "SELECT credit.amount
                FROM credit
                INNER JOIN credit_detail USING(credit_id)
                INNER JOIN customer USING(customer_id)
                WHERE credit.biller_id = :biller_id: AND credit.type IN ('Check','Cash') AND credit.deposit_id = 0 AND customer.is_deleted = 0 
                GROUP BY credit_id
                ORDER BY date_credit, credit_id";
        $par = array('biller_id' => $this->session->get('biller_id'));
        $dataStore = $this->db->query($sql, $par)->getResultArray();

        return $dataStore;
    }

    /**
     * Get total unbill charges
     * 
     * @return array
     * */
    public function get_unbill_charges() {
        $dataStore = $this->tblCharge
                ->selectSum('charge.amount')
                ->where(array('customer.biller_id' => $this->session->get('biller_id'), 'charge.invoice_id' => 0, 'customer.is_deleted' => 0, 'charge.is_deleted' => 0, 'customer.status' => 'active'))
                ->join('customer', 'charge.customer_id=customer.customer_id', 'inner')
                ->get()
                ->getRowArray();
        return $dataStore;
    }

}
