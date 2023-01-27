<?php 

/**
 * CodeIgniter Casscall Class
 *
 * Uses web services provided on casscall.com server
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Doug Homan
 */
use App\Libraries\Curl;
class Casscall {

    protected $_ci;                 // CodeIgniter instance
    protected $cass_url;                 // URL of the session
    public $error_code;             // Error code returned as an int
    public $error_string;           // Error message returned as a string
    public $info;                   // Returned after request (elapsed time, etc)
    public $debug = FALSE;

    function __construct()
    {
        log_message('debug', 'Casscall Class Initialized');
        //$this->_ci = & get_instance();
        $this->cass_url = 'http://casscall.com/';

        // Load Phil's cURL library
        //$this->_ci->load->library('curl');
        $this->_ci = new \Curl();
    }

    public function send_bulk_cass($arr) //array format should be recordId => type => street, 2nd Line, City-State-Zip
    {
        $fracs = array('Â¼','Â½','Â¾');
        $fracs_rep = array('1/4','1/2','3/4');

        $page = "cassBulk.asp";
        $cass = array();

        $timeOut = count($arr) / 50; //set timeout equal to 50 records per second
        if($timeOut < 30) $timeOut = 30; //give a minimum 10 seconds for the overhead
        if($timeOut > 100) $timeOut = 100; //we have more then 5000 records so set timeout for 5000 record batches

        //build string to submit - break it down to batches of 5000 since posting to external service
        $arrStr = array(0 => '');
        $batch = $count = 0;
        foreach($arr as $id => $arrId) {
            foreach($arrId as $type => $fields) { //build string of tab delimited values
                $original = $fields;
                $fields = array_values(array_filter($fields));//remove empty values from array
                $countFields = count($fields);
                if($countFields > 2) {
                    //they submitted 3 address lines so use all of them
                    $arrStr[$batch] .= "\n" . $id . "\t" . $type . "\t" . $fields[0] . "\t" . $fields[1] . "\t" . $fields[2];
                    $cass[$id][$type] = array('address1' => $fields[0], 'address2' => $fields[1], 'csz' => $fields[2]);
                    $count++;
                } else if($countFields > 1){
                    //only 2 address lines submitted so must be address1 and CSZ so skip using address2
                    $arrStr[$batch] .= "\n" . $id . "\t" . $type . "\t" . $fields[0] . "\t" . '' . "\t" . $fields[1];
                    $cass[$id][$type] = array('address1' => $fields[0], 'address2' => '', 'csz' => $fields[1]);
                    $count++;
                } else if(isset($fields[0])) {
                    //they did not supply enough lines to cass so don't submit to cass
                    $cass[$id][$type] = array('address1' => isout($original[0]), 'address2' => isout($original[1]), 'csz' => isout($original[3]));
                } else {
                    $cass[$id][$type] = array('address1' => '', 'address2' => '', 'csz' => '');
                }
                if($count > 5000) {
                    $batch++;
                    $arrStr[$batch] = '';
                    $count = 0;
                }
            }
        }

        //process the batches of strings
        foreach($arrStr as $str) {
            if('' == $str) {
                return $cass;
            }

            $str = substr($str,1);

            $params = array("d" => $str);
            $failCount = 0;
            while($failCount++ < 5) {
                //echo "\nsending cass";
                $response = $this->_api_request($page,$params,array(CURLOPT_TIMEOUT => $timeOut));
                if($response) break;
                if($failCount > 3) die('something is wrong with casscall');
            }
            $count = 0;
            $result = explode("\n", $response);

            foreach ($result as $row) {
                if (! trim($row)) {
                    continue;
                }

                $field = explode("\t", $row);

                $rowId = array_shift($field);
                $type = array_shift($field);

                if( ! isset($field[9])) {
                    die("result=".print_r($result)."row=".$row."<br/> id=".$rowId.print_r($field));
                }
                if($field[9] != '') { //if cass returns an entry for address 2, then use it
                    $deliveryLine2 = str_replace($fracs,$fracs_rep,$field[9]);
                } else { //otherwise address 2 was used for apt and included in line 1
                    $deliveryLine2 = '';
                }

                //some people enter only a zip, so if that doesn't cass lets prevent from returning a poorly formated partial CSZ
                if($field[11] != '') {
                    $deliveryLine3 = $field[11];
                    if($field[12] != '') $deliveryLine3 .=', '.$field[12];
                    if($field[13] != '') $deliveryLine3 .= ' '.$field[13];
                } else if($field[12] != '') {
                    $deliveryLine3 = trim($field[12] .' '.$field[13]);
                } else {
                    $deliveryLine3 = $field[13];
                }


                $cityLocal = ($field[11] == $field[22] ? '' : $field[22]); //if cityLocal value is same as city, set it to nothing

                //add cass fields to each request row
                $cass[$rowId][$type]['cert'] = $field[0];
                $cass[$rowId][$type]['streetNumber'] = $field[1];
                $cass[$rowId][$type]['preDirectional'] = $field[2];
                $cass[$rowId][$type]['streetName'] = $field[3];
                $cass[$rowId][$type]['streetSuffix'] = $field[4];
                $cass[$rowId][$type]['postDirectional'] = $field[5];
                $cass[$rowId][$type]['secondaryDesignation'] = $field[6];
                $cass[$rowId][$type]['secondaryNumber'] = $field[7];
                $cass[$rowId][$type]['city'] = $field[11];
                $cass[$rowId][$type]['state'] = $field[12];
                $cass[$rowId][$type]['zip'] = $field[13];
                $cass[$rowId][$type]['addOn'] = $field[14];
                $cass[$rowId][$type]['dpcCheckDigit'] = $field[15];
                $cass[$rowId][$type]['lotNumber'] = $field[16].$field[17];
                $cass[$rowId][$type]['carrierRoute'] = $field[18];
                $cass[$rowId][$type]['countyNumber'] = $field[19];
                $cass[$rowId][$type]['errorCodes'] = $field[20];
                $cass[$rowId][$type]['errorText'] = $field[21];
                $cass[$rowId][$type]['cityLocal'] = $cityLocal;
                $cass[$rowId][$type]['recordType'] = $field[23];
                $cass[$rowId][$type]['deliveryLine1'] = str_replace($fracs,$fracs_rep,$field[8]);
                $cass[$rowId][$type]['deliveryLine2'] = $deliveryLine2;
                $cass[$rowId][$type]['deliveryLine3'] = $deliveryLine3;
            }
        }

        return $cass;
    }


    /**
     * API request
     *
     * Send a request using Phil's cURL lib.
     * @param arra		query parameters that have to be added
     * @param bool		whether to return the actual response
     * @return mixed
     */
    private function _api_request($page, $params, $options = array(), $return = TRUE)
    {

        // Set the endpoint
        $this->_ci->create($this->cass_url.$page);
        $this->_ci->post($params,$options);


        // Show headers when in debug mode
        if($this->debug === TRUE)
        {
            $this->_ci->option(CURLOPT_FAILONERROR, FALSE);
            $this->_ci->option(CURLINFO_HEADER_OUT, TRUE);
        }

        $response = $this->_ci->execute();

        // Return the actual response when in debug or if requested specifically
        if($this->debug === TRUE OR $return === TRUE)
        {
            return $response;
        }

        // Check if everything went okay
        if ($response === FALSE)
        {
            log_message('debug', 'API request failed.');
            return FALSE;
        }

        return TRUE;

    }


}

/* End of file Curl.php */
/* Location: ./application/libraries/Curl.php */