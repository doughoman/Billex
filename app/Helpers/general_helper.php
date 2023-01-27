<?php

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Aws\Sns\SnsClient;
use Aws\S3\S3Client;
use Hashids\Hashids;
use Pusher\Pusher;

function get_cass_error($errorCode = '') {
    if ('' == $errorCode)
        return '';
    $arrCodes = array(
        '31' => "Direct address match",
        '32' => "An address was found, but a more specific address could be found with more information",
        '10' => "Invalid input address",
        '11' => "Invalid 5-digit ZIP Code",
        '12' => "Invalid state abbreviation code",
        '13' => "Invalid city name",
        '21' => "No match found using input address",
        '22' => "Multiple addresses found and more specific information is required to select a single match",
        'A' => "Zip code corrected.",
        'B' => "City/State corrected",
        'C' => "Invalid City/State/Zip",
        'D' => "No ZIP+4 code assigned",
        'F' => "Address not found",
        'H' => "Missing secondary number (apartment/suite/lot etc)",
        'I' => "Insufficient or incorrect data",
        'J' => "Matched to PO Box portion of address",
        'K' => "Matched to non-PO Box portion of address",
        'L' => "An address component was changed in order to achieve a match",
        'M' => "Street name changed in order to achieve a match",
        'N' => "Address standardized",
        'Q' => "Unique zipcode",
        'R' => "New construction. Not yet a real address.",
        'S' => "Secondary number (apt/suite/lot etc) does not match the range found in the USPS",
        'T' => "Multiple address found",
        'V' => "Unverifiable city/state"
    );
    $arrErrors = explode(",", $errorCode);
    $arrText = array();
    foreach ($arrErrors as $code) {
        if (isset($arrCodes[$code])) {
            $arrText[] = $arrCodes[$code];
        }
    }
    $text = implode('. ', $arrText);
    $text.= ($text != '') ? '.' : '';
    return $text;
}

function get_cass_icon($errorCode = '', $html = '', $ttip = '') {
    if ('' == $errorCode)
        return '';
    $arrCodes = explode(",", $errorCode);
    switch ($arrCodes[0]) {
        case 31:
            $icon = "check";
            $class = "success";
            break;
        case 32:
            $icon = "info-circle";
            $class = "warning";
            break;
        default:
            $icon = 'exclamation-triangle';
            $class = 'danger';
    }
    if ($html != '') {
        $icon = '<i class="fas fa-lg fa-' . $icon . ' text-' . $class . ' ';
        if ($ttip != '') {
            $text = get_cass_error($errorCode);
            $icon .= 'ttip" title="' . $text;
        }
        $icon.= '"></i>';
    }
    return $icon;
}

function get_cass_class($errorCode = '') {
    $arrCodes = explode(",", $errorCode);
    switch ($arrCodes[0]) {
        case 31:
            $class = "success";
            break;
        case 32:
            $class = "warning";
            break;
        default:
            $class = 'danger';
    }
    return $class;
}

function get_csz($city, $state, $zip) {
    $csz = '';
    $city = trim($city);
    $state = trim($state);
    $zip = preg_replace('/\D/', '', $zip);
    if ('' != $zip) {
        if ($zip <= 5) {
            $zip = str_pad($zip, 5 - strlen($zip), "0", STR_PAD_LEFT);
        } else {
            $zip = str_pad($zip, 9 - strlen($zip), "0", STR_PAD_LEFT);
        }
    }
    if ($city) {
        if ($state) {
            $csz = $city . ', ' . $state . ' ';
        } else {
            $csz = $city . ' ';
        }
    } else {
        if ($state) {
            $csz = $state . ' ';
        }
    }
    return trim($csz . $zip);
}

function local_csz($csz, $localCity) { //if an address has a local city name, use it for the city state zip line
    if ($localCity != '') {
        return $localCity . substr($csz, strpos($csz, ","));
    } else
        return $csz;
}

function sendMessage($number, $otp) {
    $options = [
        'region' => 'us-west-2',
        'version' => 'latest',
        'signature_version' => 'v4',
        'credentials' => array(
            'key' => getenv('aws.access_key_id'),
            'secret' => getenv('aws.secret_access_key'),
        ),
    ];
    $SnsClient = new SnsClient($options);
    $message = $otp . " is your verification code.\n\rIf you did not request this code please report to support@billex.net.";
    $phone = '+1' . $number;
    try {
        $result = $SnsClient->publish([
            'Message' => $message,
            'PhoneNumber' => $phone,
        ]);
    } catch (AwsException $e) {
        // output error message if fails
        error_log($e->getMessage());
    }
}

function sendInviteMessage($number, $name, $user_name) {
    $options = [
        'region' => 'us-west-2',
        'version' => 'latest',
        'signature_version' => 'v4',
        'credentials' => array(
            'key' => getenv('aws.access_key_id'),
            'secret' => getenv('aws.secret_access_key'),
        ),
    ];
    $SnsClient = new SnsClient($options);
    $message = "Welcome to BillEx for " . $user_name . "!,

You have been added as a user to the " . $user_name . " BillEx account by " . $name . ".

Please visit https://billex.net and sign in using phone number " . $number . ". No password is needed.

Thanks!
BillEx.net
support@billex.net";
    $phone = '+1' . $number;
    try {
        $result = $SnsClient->publish([
            'Message' => $message,
            'PhoneNumber' => $phone,
        ]);
    } catch (AwsException $e) {
        // output error message if fails
        error_log($e->getMessage());
    }
}

function sendTransactionMessage($number, $emailAddress, $status, $tranId = '') {
    $options = [
        'region' => 'us-west-2',
        'version' => 'latest',
        'signature_version' => 'v4',
        'credentials' => array(
            'key' => getenv('aws.access_key_id'),
            'secret' => getenv('aws.secret_access_key'),
        ),
    ];
    $SnsClient = new SnsClient($options);
    if ($status == 0) {
        $message = "Thank you for your payment. We will notify " . $emailAddress . " once the transaction completes.";
    } else {
        $message = "Thank you for your payment. Your payment have successfully.Your transaction Id " . $tranId . ".";
    }
    $phone = '+1' . $number;
    try {
        $result = $SnsClient->publish([
            'Message' => $message,
            'PhoneNumber' => $phone,
        ]);
    } catch (AwsException $e) {
        // output error message if fails
        error_log($e->getMessage());
    }
}

function sendMail($email, $otp) {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->setFrom(getenv("sent_from"), 'BillEx');
    $mail->addAddress($email, '');
    $mail->Username = getenv('aws.smtp_username');
    $mail->Password = getenv('aws.smtp_password');
    $mail->Host = getenv('aws.smtp_host');
    $mail->Subject = 'BillEx code';
    $mail->Body = $otp . " is your verification code.\n\rIf you did not request this code please report to support@billex.net.";
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->isHTML(true);
    if (!$mail->send()) {
        return false;
    } else {
        return true;
    }
}

function sendInviteMail($email, $name, $user_name) {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->setFrom(getenv("sent_from"), 'BillEx');
    $mail->addAddress($email, '');
    $mail->Username = getenv('aws.smtp_username');
    $mail->Password = getenv('aws.smtp_password');
    $mail->Host = getenv('aws.smtp_host');
    $mail->Subject = 'Welcome to BillEx for ' . $user_name . '!';
    $mail->Body = 'You have been added as a user to the ' . $user_name . ' BillEx account by ' . $name . '.<br><br>
Please visit https://billex.net and sign in using ' . $email . ' email. No password is needed.<br><br>
Thanks!<br>
BillEx.net<br>
support@billex.net';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->isHTML(true);
    if (!$mail->send()) {
        return false;
    } else {
        return true;
    }
}

function sendInvoice($file_name, $html, $email, $name) {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->setFrom(getenv("sent_from"), 'BillEx');
    $mail->addAddress($email, '');
    $mail->Username = getenv('aws.smtp_username');
    $mail->Password = getenv('aws.smtp_password');
    $mail->Host = getenv('aws.smtp_host');
    $mail->Subject = 'Invoice from ' . $name;
    $mail->Body = $html;
    $mail->addAttachment($file_name);
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->isHTML(true);
    if (!$mail->send()) {
        return false;
    } else {
        return true;
    }
}

function sendDownloadCharge($email, $html, $biller_name) {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->setFrom(getenv("sent_from"), 'BillEx');
    $mail->addAddress($email, '');
    $mail->Username = getenv('aws.smtp_username');
    $mail->Password = getenv('aws.smtp_password');
    $mail->Host = getenv('aws.smtp_host');
    $mail->Subject = 'Download info from ' . $biller_name;
    $mail->Body = $html;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->isHTML(true);
    if (!$mail->send()) {
        return false;
    } else {
        return true;
    }
}

function uploadInvoiceS3($pdf_file, $html_file) {
    $s3Client = new S3Client([
        'region' => 'us-east-1',
        'version' => 'latest',
        'credentials' => array(
            'key' => getenv('aws.access_key_id'),
            'secret' => getenv('aws.secret_access_key'),
        ),
    ]);
    $s3Client->putObject(array(
        'Bucket' => 'billex',
        'Key' => 'invoice/' . $pdf_file,
        'SourceFile' => $pdf_file,
        'ACL' => 'public-read'
    ));
    $s3Client->putObject(array(
        'Bucket' => 'billex',
        'Key' => 'invoice/' . $html_file,
        'SourceFile' => $html_file,
        'ACL' => 'public-read'
    ));
}

function random_strings() {
    $characterPool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    return substr(str_shuffle($characterPool), 0, 10);
}

function encrypt($payload) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($payload, 'aes-256-cbc', 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=', 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

function decrypt($garble) {
    try {
        list($encrypted_data, $iv) = explode('::', base64_decode($garble), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=', 0, $iv);
    } catch (\Exception $ex) {
        $error_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if (strpos(explode("?", $error_url)[0], "administration")) {
            $redirect = base_url() . 'aut/administration/item';
        } else if (strpos(explode("?", $error_url)[0], "customer")) {
            $redirect = base_url() . 'aut/customer';
        }
    }
}

function convert_hrs_to_number($hrs) {
    $hrs_array = explode(":", $hrs);
    $minute = number_format($hrs_array[1] / 60, 2, '.', '');
    $total = $hrs_array[0] + $minute;
    return $total;
}

// GET ALL TABLE DATA USING WHERE CONDITION
function dbQueryRows($table, $whereArray = array(), $notWhereArray = array()) {
    $db = \Config\Database::connect();
    $table = $db->table($table);
    $dataStore = $table
            ->where($whereArray)
            ->get()
            ->getResultArray();
    return $dataStore;
}

function getAllAlerts() {
    $db = \Config\Database::connect();
    $table = $db->table('alert');
    $dataStore = $table
            ->orderBy('time_stamp', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
    return $dataStore;
}

function encodeHashids($value = '') {
//    print_r($value);
    $hashids = new Hashids(env('hashids.salt'), 16);
    return $hashids->encode($value);
}

function random_color() {
    $rgbColor = array();
    foreach (array('r', 'g', 'b') as $color) {
        $rgbColor[$color] = mt_rand(0, 255);
    }
    return sprintf("%02x%02x%02x", $rgbColor['r'], $rgbColor['g'], $rgbColor['b']);
}

function convert_timezone($user_id, $time = "", $fromTz = '') {
    $dataStore = dbQueryRows('user', array('user_id' => $user_id));
    if ($dataStore[0]['utc_offset'] == 0) {
        $dataStore[0]['utc_offset'] = -180;
    }
    return date('Y-m-d H:i:s', strtotime($time) + (60 * $dataStore[0]['utc_offset']));
}

function datetimeconv_utc($datetime, $user_id) {
    $dataStore = dbQueryRows('user', array('user_id' => $user_id));
    if ($dataStore[0]['utc_offset'] == 0) {
        $dataStore[0]['utc_offset'] = -180;
    }
    return date('Y-m-d H:i:s', strtotime($datetime) - (60 * $dataStore[0]['utc_offset']));
}

function get_timezone($user_id) {
    $dataStore = dbQueryRows('user', array('user_id' => $user_id));
    if ($dataStore[0]['utc_offset'] == 0) {
        $dataStore[0]['utc_offset'] = -180;
    }
    $time_zone = intval($dataStore[0]['utc_offset']) / 60;
    return $time_zone;
}

// Export data in CSV format 
function exportCSV($name, $data, $header = '') {
    // file name 
    $filename = $name . '.csv';
    header("Content-Description: File Transfer");
    header('Content-type: application/csv');
    header("Content-Disposition: attachment; filename=$filename");
    // file creation 
    $file = fopen('php://output', 'w');

    fputcsv($file, $header);
    foreach ($data as $key => $line) {
        fputcsv($file, $line);
    }
    fclose($file);
    exit;
}

function sendTimerEvent($userDetails = array(), $message = '', $event = '', $channel = '') {
    $options = array(
        'cluster' => 'us2',
        'useTLS' => true
    );
    $pusher = new Pusher(
            env('PUSHER.APP_KEY'), env('PUSHER.APP_SECRET'), env('PUSHER.APP_ID'), $options
    );
    $data['message'] = $message;
    $pusher->trigger($channel, $event, $data);
}

function getDayName($value) {
    $day_name = '';
    switch ($value) {
        case 1:
            $day_name = 'Monday';
            break;
        case 2:
            $day_name = 'Tuesday';
            break;
        case 3:
            $day_name = 'Wednesday';
            break;
        case 4:
            $day_name = 'Thursday';
            break;
        case 5:
            $day_name = 'Friday';
            break;
        case 6:
            $day_name = 'Saturday';
            break;
        case 7:
            $day_name = 'Sunday';
            break;
    }
    return $day_name;
}

function delete_col(&$array, $key) {
    return array_walk($array, function (&$v) use ($key) {
        unset($v[$key]);
    });
}
