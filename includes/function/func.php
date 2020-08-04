<?php
use PHPMailer\PHPMailer\PHPMailer;

function getUserIpAddr(){
    $ip = $_SERVER['REMOTE_ADDR'];
 
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
 
    return $ip;
}

// add on  10-03-2015
if (!function_exists('encryptIt')) {

    function encryptIt($q) {
        $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        $qEncoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), $q, MCRYPT_MODE_CBC, md5(md5($cryptKey))));
        return( $qEncoded );
    }
}

// decrypt password 
if (!function_exists('decryptIt')) {

    function decryptIt($q) {
        $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        $qDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), base64_decode($q), MCRYPT_MODE_CBC, md5(md5($cryptKey))), "\0");
        return( $qDecoded );
    }
}
/////////////////////////////////////////////////////
function GetHomeDir(){
    $homepath = dirname($_SERVER['SCRIPT_FILENAME']);
    if ($homepath[0] != '/') {
        if ($homepath == ".") {
            $homepath = "";
        } else {
            $homepath = "/" . $homepath;
        }
        $homepath = $_SERVER['PWD'] . $homepath;
    }
    return $homepath;
}

function GetRootFolder() {
    return "";
    $root = $_SERVER['DOCUMENT_ROOT'];
    $scr = $_SERVER['SCRIPT_FILENAME'];
    $scr = str_replace($root, "", $scr);
    $root = dirname($scr);
    $root = preg_split("/[\/\\\]/", $root, -1, PREG_SPLIT_NO_EMPTY);
    $root = @trim($root[0]);
    if ($root != '') {
        $root.="/";
    }
    return $root;
}

function GetVal($val, $repl = '') {
    $val = @trim($val);
    if (!strlen($val)) {
        return $repl;
    }
    return $val;
}

function GetIf($cond, $trueval, $falseval) {
    return ($cond ? $trueval : $falseval);
}

function Page_Create($template_file, $content) {
    if (file_exists($template_file)) {
        $fh = fopen($template_file, 'r');
        $theData = fread($fh, filesize($template_file));
        fclose($fh);
        $string = $theData;
        $patterns = array();
        $patterns['Title'] = '/<PHP:Title>/';
        $patterns['Head'] = '/<PHP:Head>/';
        $patterns['Header'] = '/<PHP:Header>/';
        $patterns['Theme_Path'] = '/<PHP:Theme_Path>/';
        $patterns['Root_Folder'] = '/<PHP:ROOT_FOLDER>/';
        $patterns['Left'] = '/<PHP:Left>/';
        $patterns['Content'] = '/<PHP:Content>/';
        $patterns['Right'] = '/<PHP:Right>/';
        $patterns['Footer'] = '/<PHP:Footer>/';
        $replacements = array();
        $replacements['Title'] = $content['Title'];
        $replacements['Head'] = $content['Head'];
        $replacements['Header'] = $content['Header'];
        $replacements['Theme_Path'] = $content['Theme_Path'];
        $replacements['Root_Folder'] = ROOT_FOLDER;
        $replacements['Left'] = $content['Left'];
        $replacements['Content'] = $content['Content'];
        $replacements['Right'] = $content['Right'];
        $replacements['Footer'] = $content['Footer'];
        return preg_replace($patterns, $replacements, $string);
    }
}

function GetMailAttachments($rawpath, $attachments) {
    $attsFiles = array();
    $atts = preg_split("/,/", @trim($attachments), -1, PREG_SPLIT_NO_EMPTY);
    $basepath = preg_replace("/\.txt$/i", "/", $rawpath);
    for ($at = 0; $at < count($atts); $at++) {
        if (@file_exists($basepath . $atts[$at])) {
            $attsFiles[] = $atts[$at];
        }
    }
    return $attsFiles;
}

function Block_Create($block_path, $content) {
    global $db, $Form, $system;
    if (file_exists($block_path)) {
        if (isset($content['FunctionVar'])) {
            foreach ($content['FunctionVar'] as $key => $val) {
                $$key = $val;
            }
        }
        ob_start();
        include($block_path);
        $content_block = ob_get_contents();
        ob_end_clean();
    }
    return $content_block;
}

function Url_Create($url_value, $query = "", $settings = "") {
    if (defined("ROOT_FOLDER")) {
        $root_folder = ROOT_FOLDER;
    } else {
        $root_folder = GetRootFolder();
    }
    $query = preg_replace("/^&/", "", $query);
    $sep = "?";
    if ($url_value == "home") {
        $url = "/" . $root_folder . "dashboard/";
    } else if ($url_value != "") {
        $url = "/" . $root_folder . $url_value . "/";
        //$sep="&";
    } else {
        $url = "/" . $root_folder . "dashboard/";
    }
    if ($query != "") {
        $url.=$sep . $query;
    }
    return $url;
}

//redirect to url
function redirect($url, $addparams = "") {
    $redirect_url = Url_Create($url, $addparams);
    header("location:" . $redirect_url);
    exit();
}

//outer redirect
function oredirect($redirect_url) {
    header("location
        :" . $redirect_url);
    exit();
}

#Table 
function tablelist($table, $select = "*", $where = "") {
    global $db, $system;
    $list = $db->select_to_array($table, $select, $where);
    return $list;
}

function tablerow($table, $select = "*", $where = "") {
    global $db, $system;
    $list = $db->select_row($table, $select, $where);
    return $list;
}

function selectrow($sql, $result_type = 'ASSOC') {
    global $db;
    $arr = $db->query_to_array($sql, $result_type);
    if (is_array($arr) && count($arr)) {
        return $arr[0];
    }
    return false;
}

function selectvalue($sql) {
    if ($row = selectrow($sql, 'BOTH')) {
        return $row[0];
    }
    return false;
}

//Function to filter posted values for database
function filterpost($check_array, $post_val) {
    $return = array();
    foreach ($check_array as $k => $v) {
        $return[$v] = $post_val[$v];
    }
    return $return;
}

function func($func) {
    global $db, $Form, $system;

    if (file_exists($func)) {
        include($func);
    }
    return $Variables;
}

//Check EmailID Already exist
function CheckUser($User, $Email) {
    global $db, $Form, $system;
    $check = $db->select_single_to_array('tickleuser', '*', array("WHERE UserName = ?", strtolower($User)));
    $check_email = $db->select_single_to_array('tickleuser', '*', array("WHERE EmailID = ?", $Email));
    $check_block = $db->select_single_to_array('block_register', '*', array("WHERE BlockTitle = ?", strtolower($User)));
    if ($check['TickleID'] > 0 || $check_email['TickleID'] > 0 || $check_block['BlockID'] > 0) {
        return true;
    } else {
        //if (whmcs_check_user(array('email' => $Email))) {
           // return true;
        //} else {
            return false;
        //}
    }
}


// checking email already exists
function CheckEmail($Email) {
    global $db, $Form, $system;
    $check_email = $db->select_single_to_array('tickleuser', '*', array("WHERE EmailID = ?", $Email));
    $check_block = $db->select_single_to_array('block_register', '*', array("WHERE BlockTitle = ?", strtolower($User)));
	//print_r($check_email);
    if ($check_email['TickleID'] > 0 || $check_block['BlockID'] > 0) {
        return true;
    } else {
            return false;
    }
}

function time24to12($time24) {
    return date("h:i A", strtotime($time24));
}

function time12to24($time12) {
    return date("H:i:s", strtotime($time12));
}

function convert_date($dates) {
    $date = explode(" ", $dates);
    $time = explode(":", $date[1]);
    $parts = explode("-", $date[0]);
    $thatis = mktime($time[0], $time[1], $time[2], $parts[1], $parts[2], $parts[0]);
    $nicedate = date("D, jS F Y", $thatis) . " " . $date[1];
    return $nicedate;
}

function mail_date($dates) {
    $date = $dates;
    // MAKE DANISH DATE DISPLAY
    list($dayName, $day, $month, $year, $time) = split(" ", $date);
    $time = substr($time, 0, 5);
    $date = $dayName . " " . $day . " " . $month . " " . $year . " " . $time;
    return $date;
}

function clearstr($string = "") {
    $special = array('/', '!', '&', '*', ' ', '	', '+', '.', '#', '~', '`', '@', '%', '^', '(', ')', '-', '=');
    $string = stripslashes($string);
    $string = strtolower($string);
    return str_replace($special, '', $string);
}

function gettimezonenames() {
    return array(
        'Pacific/Kwajalein' => '(GMT -12:00) Eniwetok, Kwajalein',
        'Pacific/Midway' => '(GMT -11:00) Midway Island, Samoa',
        'Pacific/Honolulu' => '(GMT -10:00) Hawaii',
        'US/Alaska' => '(GMT -9:00) Alaska',
        'US/Pacific' => '(GMT -8:00) Pacific Time (US and Canada)',
        'US/Mountain' => '(GMT -7:00) Mountain Time (US and Canada)',
        'America/Denver' => '(GMT -6:00) Central Time (US and Canada), Mexico City',
        'America/Chicago' => '(GMT -5:00) Eastern Time (US and Canada), Bogota, Lima',
        'America/New_York' => '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz',
        //'-3.5' => 'America/St_Johns', // an hour ahead
        'America/Buenos_Aires' => '(GMT -3:00) Brazil, Buenos Aires, Georgetown',
        'GMT -2.0' => '(GMT -2:00) Mid-Atlantic',
        '-1.0' => '(GMT -1:00 hour) Azores, Cape Verde Islands',
        '0.0' => '(GMT) Western Europe Time, London, Lisbon, Casablanca',
        '1.0' => '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris',
        '2.0' => '(GMT +2:00) Kaliningrad, South Africa',
        '3.0' => '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg',
        '3.5' => '(GMT +3:30) Tehran',
        '4.0' => '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi',
        '4.5' => '(GMT +4:30) Kabul',
        'Asia/Yekaterinburg' => '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
        '5.5' => '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi',
        '6.0' => '(GMT +6:00) Almaty, Dhaka, Colombo',
        '7.0' => '(GMT +7:00) Bangkok, Hanoi, Jakarta',
        '8.0' => '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong',
        '9.0' => '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
        '9.5' => '(GMT +9:30) Adelaide, Darwin',
        '10.0' => '(GMT +10:00) Eastern Australia, Guam, Vladivostok',
        '11.0' => '(GMT +11:00) Magadan, Solomon Islands, New Caledonia',
        '12.0' => '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka'
    );
}

function gettimezones() {
    return array(
        'Pacific/Kwajalein' => '(GMT -12:00) Eniwetok, Kwajalein',
        'Pacific/Midway' => '(GMT -11:00) Midway Island, Samoa',
        'Pacific/Honolulu' => '(GMT -10:00) Hawaii',
        'US/Alaska' => '(GMT -9:00) Alaska',
        'US/Pacific' => '(GMT -8:00) Pacific Time (US and Canada)',
        'US/Mountain' => '(GMT -7:00) Mountain Time (US and Canada)',
        'America/Denver' => '(GMT -6:00) Central Time (US and Canada), Mexico City',
        'America/Chicago' => '(GMT -5:00) Eastern Time (US and Canada), Bogota, Lima',
        'America/New_York' => '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz',
        //'-3.5' => 'America/St_Johns', // an hour ahead
        '-3.0' => '(GMT -3:00) Brazil, Buenos Aires, Georgetown',
        '-2.0' => '(GMT -2:00) Mid-Atlantic',
        '-1.0' => '(GMT -1:00 hour) Azores, Cape Verde Islands',
        '0.0' => '(GMT) Western Europe Time, London, Lisbon, Casablanca',
        '1.0' => '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris',
        '2.0' => '(GMT +2:00) Kaliningrad, South Africa',
        '3.0' => '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg',
        '3.5' => '(GMT +3:30) Tehran',
        '4.0' => '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi',
        '4.5' => '(GMT +4:30) Kabul',
        'Asia/Yekaterinburg' => '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
        '5.5' => '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi',
        '6.0' => '(GMT +6:00) Almaty, Dhaka, Colombo',
        '7.0' => '(GMT +7:00) Bangkok, Hanoi, Jakarta',
        '8.0' => '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong',
        '9.0' => '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
        '9.5' => '(GMT +9:30) Adelaide, Darwin',
        '10.0' => '(GMT +10:00) Eastern Australia, Guam, Vladivostok',
        '11.0' => '(GMT +11:00) Magadan, Solomon Islands, New Caledonia',
        '12.0' => '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka'
    );
}

function gettimezone($zonediff = '0.0') {
    return $zonediff;
    //$timezones = gettimezones();
    //return $timezones[$zonediff];
}

function getlocaltime($time, $zonediff = "0.0" , $format = 'Y-m-d H:i:s' ) {
    $getservertz = date_default_timezone_get();
    date_default_timezone_set('Etc/GMT-0');
    $tm = strtotime($time);
    date_default_timezone_set($zonediff);
    $gmdate = date($format, $tm);
    date_default_timezone_set($getservertz);
    return $gmdate;
}

function getgmdate($time, $zonediff = "0.0") {
    $getservertz = date_default_timezone_get();
    //date_default_timezone_set(gettimezone($zonediff));
    date_default_timezone_set($zonediff);
    $strtotime = strtotime($time);
   // $gdate = getdate($strtotime);
    //$gmdate = gmdate("Y-m-d H:i:s", mktime($gdate['hours'], $gdate['minutes'], $gdate['seconds'], $gdate['mon'], $gdate['mday'], $gdate['year']));
    $gmdate = gmdate("Y-m-d H:i:s", $strtotime);
    date_default_timezone_set($getservertz);
    return $gmdate;
}

function gettime() {
    //$tm = time();
    //$dt1 = mktime();
    //$dt2 = mktime();
    return time() + 3600;
}

function body_decode($email, $EncodeTyp = "") {
    $val = array();
    if (trim($EncodeTyp) == "7bit" || trim($EncodeTyp) == "8bit") {
        $val['TEXT'] = $email;
        $val['HTML'] = nl2br($email);
    } elseif (trim($EncodeTyp) == "quoted-printable") {
        $val['TEXT'] = quoted_printable_decode($email);
        $val['HTML'] = nl2br(quoted_printable_decode($email));
    } else {
        $mailBformating = "";
        $mdecode = 'mail/mimeDecode.php';
        if (defined('HOME_FOLDER') && @trim(HOME_FOLDER) != '') {
            $mdecode = HOME_FOLDER . $mdecode;
        }
        include_once($mdecode);
        $params['include_bodies'] = true;
        $params['decode_bodies'] = true;
        $params['decode_headers'] = true;
        $params['input'] = true;
        /*
          if(base64_decode($email))
          $email=base64_decode($email);
         */
       //echo $email;
        /* $email=str_replace("This is a multipart message in MIME format.

          ","",$email);
         */
        $email = trim(str_replace("This is a multipart message in MIME format.", "", trim($email)));
        $email = trim(str_replace("This is a multi-part message in MIME format.", "", trim($email)));


        $rs = iconv_mime_decode($email);
        //print_r($rs);
        //echo $email;
        $decoder1 = new Mail_mimeDecode($email);
        $structure1 = $decoder1->decode($params);

        if ($rs != "") {
            $ex = explode($rs, $email);
            foreach ($ex as $k => $value) {
                if ($value != "") {
                    //echo $value;
                    $decoder = new Mail_mimeDecode($value);
                    $structure = $decoder->decode($params);
                    if ($structure->ctype_secondary == "plain" && $structure->body != "") {
                        $val['TEXT'] = $structure->body;
                    }
                    if ($structure->ctype_secondary == "html" && $structure->body != "") {
                        $val['HTML'] = $structure->body;
                    }
                    /* if($structure->ctype_secondary=="alternative")
                      {
                      $Parts=$structure->parts;
                      if(is_array($Parts))
                      {
                      foreach($Parts as $K=>$V)
                      {
                      if($V->ctype_secondary=="plain"&&$V->body!="")
                      {
                      $val['TEXT']=$V->body;
                      $pattern = '/-*=_NextPart'."*".".*".'[A-Z0-9]/';
                      preg_match($pattern,$val['TEXT'],$matches);
                      if(is_array($matches)&&count($matches))
                      {
                      $match1=$matches[0];
                      $match12='Content-Type: multipart/alternative;
                      boundary="------=_NextPart_001_0C7B_01CBB63E.7888CD10"

                      ------=_NextPart_001_0C7B_01CBB63E.7888CD10
                      Content-Type: text/plain;
                      ';
                      $matcher=$match12.$val['TEXT'];
                      $decoder2 = new Mail_mimeDecode($val['TEXT']);
                      $structure2 = $decoder2->decode($params);
                      print_r($structure2);
                      }


                      exit;
                      }
                      if($V->ctype_secondary=="html"&&$V->body!="")
                      {
                      $val['HTML']=$V->body;

                      }
                      }
                      }
                      } */

                    $val[] = $structure;
                    //print_r($structure);
                }//if
            }

        //exit;
        }
    }//else main
    return $val;
}

function protect($string, $key = "TickleMailTrain") {
    $string_code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
    //$string_code = base64_encode($string);
    return $string_code;
}

function unprotect($string, $key = "TickleMailTrain") {
    $string_code = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
    //$string_code = base64_decode($string);
    return $string_code;
}


function reWriteId($string,$action='en') {
$output = false;

$encrypt_method = "AES-256-CBC";
//pls set your unique hashing key
$secret_key = 'TickleMailTrain';
$secret_iv = 'TickleMailTrainCron';

// hash
$key = hash('sha256', $secret_key);

// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
$iv = substr(hash('sha256', $secret_iv), 0, 16);

//do the encyption given text/string/number
if( $action == 'en' ) {
$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
$output = base64_encode($output);
}
else if( $action == 'de' ){
//decrypt the given text/string/number
$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
}

return $output;
}

function extract_emails_from($string) {
    preg_match_all("/[\._a-zA-Z0-9-\+]+@([_a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4}/i", $string, $matches);
    return $matches[0];
}

function parse_email_address($string) {
    $ret = array();
    $eml = extract_emails_from($string);
    if (!is_array($eml) || !count($eml)) {
        return $ret;
    }
    $ret['email'] = trim($eml[0]);
    $nm = str_replace($ret['email'], "", $string);
    $nm = preg_replace("/[\"'\(\)\<\>\[\]]/i", "", $nm);
    $nm = preg_replace("/[ ]{2,}/", ' ', $nm);
    list($fn, $ln) = explode(" ", $nm, 2);
    $ret['fname'] = @trim($fn);
    $ret['lname'] = @trim($ln);
    return $ret;
}

function RemoveBadChar($str) {
    return $str;
}

function ReadHeader($head) {
    $header = explode("\n", $head);
    $rhead = array();
    if (is_array($header) && count($header)) {
        $lsthead = "";
        foreach ($header as $line) {
            $line = @trim($line);
            if ($line == "") {
                continue;
            }
            if (preg_match("/^([^:]*): (.*)/i", $line, $arg)) {
                $rhead[$arg[1]] = $arg[2];
                $lsthead = $arg[1];
            } else {
                $rhead[$lsthead] = $rhead[$lsthead] . " " . $line;
            }
        }
    }
    return $rhead;
}

function SendMail($to, $from, $subj, $text, $method = 'mail', $isHtml = true, $bcc = array()) {

    $mail = new PHPMailer(false); //New instance, with exceptions enabled

    if ($method == 'smtp') {
 
        $mail->IsSMTP();    // tell the class to use SMTP
        $mail->Mailer = "smtp";                       
        $mail->Host = "mail.tickletrain.com"; // SMTP server
        $mail->Port = '25'; // set the SMTP server port
        $mail->SMTPKeepAlive = false;                  // SMTP connection will not close after each email sent
        $mail->SMTPAuth = false;                  // enable SMTP authentication        
        $mail->Username = "ticklein@tickletrain.com";     // SMTP server username
        $mail->Password = 'change88q1w2e3r4';     // SMTP server password
        $mail->SMTPAuth = true;                  // enable SMTP authentication        
        $mail->SMTPSecure = '';
        $mail->SMTPAutoTLS = false;                        // Enable TLS encryption, `ssl` also accepted

    }
    if ($method == 'sendmail') {
        $mail->IsSendmail();
    }
    if ($method == 'mail') {
        $mail->IsMail();
    }

    if ($method == 'qmail') {
        $mail->IsQmail();
    }

    $mail->AddAddress($to);
    $mail->SetFrom($from, "");
    foreach ($bcc as $email) {
        $mail->AddBCC($email);
    }
    $mail->Subject = $subj;
    $mail->IsHTML($isHtml);
    if (!$isHtml) {
        $mail->Body = $text;
    } else {
        $mail->MsgHTML($text);
    }
    $ret = $mail->Send();
    $mail->ClearAddresses();
    $mail->ClearBCCs();
    $mail->ClearReplyTos();
    $mail->ClearAllRecipients();
    $mail->ClearCCs();

    $mail = null;
    return $ret;
}

function WriteFile($file, $content, $key = "w", $rights = null) {
    if (@$fh = fopen($file, $key)) {
        fwrite($fh, $content);
        fclose($fh);

        if ($rights) {
            @chmod($file, $rights);
        }

        return true;
    }
    return false;
}

function debugF($msg) {
    if (defined('LOGS_FOLDER') && @trim(LOGS_FOLDER) != '') {
        $fname = LOGS_FOLDER . 'cronmail.log';
        WriteFile($fname, date('d.m.Y H:i:s') . " > " . $msg . "\n", "a");
    }
}

function prepareMailer($user, &$mail) {
    $FromEmailid = $user['UserName'] . '@tickletrain.com'; //$rs_user['EmailID'];
       //$mail->Sender = $FromEmailid;
    if (intval($user['DMUse']) && trim($user['DMSmtp']) != '') {
        $mail->IsSMTP();                       // tell the class to use SMTP
       // $mail->SMTPDebug = 2;
       // $mail->Debugoutput = 'html';

        $mail->Host = trim($user['DMSmtp']); // SMTP server
        $mail->Port = intval($user['DMPort']); // set the SMTP server port
        $mail->SMTPKeepAlive = false;                  // SMTP connection will not close after each email sent
        $mail->SMTPAuth = false;                  // enable SMTP authentication
        if (trim($user['DMUser']) != '' && trim($user['DMPwd']) != '') {
            $mail->Username = trim($user['DMUser']);     // SMTP server username
            $mail->Password = trim($user['DMPwd']);     // SMTP server password
            $mail->SMTPAuth = true;                  // enable SMTP authentication
        }
        $mail->SMTPSecure = trim($user['DMSecure']);
        $FromEmailid = $user['EmailID'];
        ///$mail->Sender = $FromEmailid;
        return;
    }
    else{
        $mail->IsSMTP();                           // tell the class to use SMTP
        $mail->Host = "mail.tickletrain.com"; // SMTP server
        $mail->Port = '25'; // set the SMTP server port
        $mail->SMTPKeepAlive = false;                  // SMTP connection will not close after each email sent
        $mail->SMTPAuth = false;                  // enable SMTP authentication        
        $mail->Username = "ticklein@tickletrain.com";     // SMTP server username
        $mail->Password = 'change88q1w2e3r4';     // SMTP server password
        $mail->SMTPAuth = true;                  // enable SMTP authentication        
        $mail->SMTPSecure = '';
        $FromEmailid = $user['EmailID'];
        $mail->Sender = $FromEmailid;
        return;
    }
    $mail->IsMail();                           // tell the class to use SMTP
    $mail->Host = ""; // SMTP server
    $mail->Port = 0; // set the SMTP server port
    $mail->Username = "";     // SMTP server username
    $mail->Password = "";     // SMTP server password
    $mail->SMTPAuth = false;                  // enable SMTP authentication
    $mail->SMTPKeepAlive = false;                  // SMTP connection will not close after each email sent
    $mail->SMTPSecure = "";
}

function callFb($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true
    ));

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function login($Username, $Password) {

   $check_login = tablelist('tickleuser', '', array("WHERE ( UserName ='$Username' or  EmailID='$Username') and Password='$Password'"));

   //$_COOKIE['sec_email'] = $check_login[0]['EmailID'];
   setcookie('hide_show_id', '', time() - 3600, "/myaccount/");

   unset($_COOKIE['hide_show_id']);

   setcookie('hide_show_id', $check_login[0]['EmailID'].'_mainemail', time() + (10*24*60*60*1000), "/myaccount/");

   //print_r($_COOKIE);die();

    if (count($check_login) == 1) {
        $ck_login = $check_login[0];
        if ($ck_login['TickleID'] > 0) {
        //            $postfields['action'] = "validatelogin";
        //            $postfields["email"] = "whmcsdeveloper@gmail.com";
        //            $postfields["password2"] = "developer";
        //            $loginToWhmcs = whmcs_callAPI($postfields);
        //            $postfield["action"] = "getclientpassword";
        //            $postfield["userid"] = $loginToWhmcs->userid;
        //            $apiResponse = whmcs_callAPI($postfield);
        //            pr($apiResponse);
        //            echo md5($loginToWhmcs->userid . $loginToWhmcs->password . $_SERVER['REMOTE_ADDR']);
        //            die();
        //            if ($loginToWhmcs->result == 'success') {
        //                $_SESSION['uid'] = $loginToWhmcs->userid;
        //                $_SESSION['upw'] = $loginToWhmcs->passwordhash;
        //            } else {
        //                return 3;
        //            }

            if ($ck_login['Status'] == 'Y') {
                $_SESSION['TickleID'] = $ck_login['TickleID'];
                $_SESSION['UserName'] = $ck_login['UserName'];
        //		$_SESSION['Password']=$ck_login['Password'];
                $_SESSION['EmailID'] = $ck_login['EmailID'];
                $_SESSION['FirstName'] = $ck_login['FirstName'];
                $_SESSION['LastName'] = $ck_login['LastName'];
                $_SESSION['TimeZone'] = $ck_login['TimeZone'];
                $_SESSION['mail_type'] = $ck_login['mail_type'];
                $_SESSION['signature'] = $ck_login['signature'];
                return 0;
            } else {
                return 1;
            }
        }
    }
    
    else{
    //whmcs login(jaswant)
    $pos = strpos($Username, '@');
    if ($pos == false) {
	$url = "http://secure.tickletrain.com/getcustomfileds.php"; # URL to WHMCS API file
        $postfields['user'] = $Username;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$email = curl_exec($ch);
	curl_close($ch);
        $Username =  $email;
    }


    $url = "http://secure.tickletrain.com/includes/api.php"; # URL to WHMCS API file
    //$admin = "manvinder"; # Admin username goes here
    //$adminpass = "TT2013WHMC$"; # Admin password goes here
    $admin = "TT"; # Admin username goes here
    $adminpass = "T!K|E"; # Admin password goes here
 
    $postfields["username"] = $admin;
    $postfields["password"] = md5($adminpass);
    $postfields["email"] = $Username;
    $postfields["password2"] = $Password;
    $postfields["action"] = "validatelogin"; #action performed by the [[API:Functions]]
 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
 
    $data = explode(";",$data);
    foreach ($data AS $temp) {
     $temp = explode("=",$temp);
     $results[$temp[0]] = $temp[1];
    }
     
   // print_r($results);
   // die();
    if ($results["result"]=="success") {
      return array('code' => 3 ,"mail" => $Username);
    } else {
      return 2;
    }
    //whmcs login(jaswant)
   }
    //return 2;
}

function loginByTickle($TickleID) {
    $check_login = tablelist('tickleuser', '', array("WHERE TickleID='$TickleID'"));

    if (count($check_login) == 1) {
        $ck_login = $check_login[0];
        if ($ck_login['TickleID'] > 0) {
            if ($ck_login['Status'] == 'Y') {
                $_SESSION['TickleID'] = $ck_login['TickleID'];
                $_SESSION['UserName'] = $ck_login['UserName'];
                //$_SESSION['Password']=$ck_login['Password'];
                $_SESSION['EmailID'] = $ck_login['EmailID'];
                $_SESSION['FirstName'] = $ck_login['FirstName'];
                $_SESSION['LastName'] = $ck_login['LastName'];
                $_SESSION['TimeZone'] = $ck_login['TimeZone'];
                $_SESSION['mail_type'] = $ck_login['mail_type'];
                $_SESSION['signature'] = $ck_login['signature'];
                return 0;
            } else {
                return 1;
            }
        }
    }
    return 2;
}

function checkTickleDelete($tid) {
    global $db;
    if (!isset($GLOBALS['busyTickles'])) {
        $GLOBALS['busyTickles'] = array();
        $query = "select distinct TickleTrainID from task where Status='Y'";
        $res = mysqli_query($db->conn,$query);
        while ($row = mysqli_fetch_array($res)) {
            $GLOBALS['busyTickles'][$row['TickleTrainID']] = 1;
        }
    }
    return!isset($GLOBALS['busyTickles'][$tid]);
}

function checkTickleFollowDelete($ftid) {
    global $db;
    if (!isset($GLOBALS['busyFollowTickles'])) {
        $GLOBALS['busyFollowTickles'] = array();
        $query = "select distinct FollowTickleTrainID from task where Status='Y' and FollowTickleTrainID>0";
        $res = mysqli_query($db->conn,$query);
        while ($row = mysqli_fetch_array($res)) {
            $GLOBALS['busyFollowTickles'][$row['FollowTickleTrainID']] = 1;
        }
    }
    return!isset($GLOBALS['busyFollowTickles'][$ftid]);
}

function checkContactDelete($cid, $gid = 0) {
    global $db;
    if (!isset($GLOBALS['busyContacts'])) {
        $GLOBALS['busyContacts'] = array();
        //$query = "select distinct ccat.ContactID, TickleContact as GroupID from task inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) inner join category_contact_list ccat on (TickleContact=ccat.CategoryID) where task.Status='Y'";
        $query = "select distinct ContactID from task inner join user_mail on (task.MailID=user_mail.MailID) where task.Status='Y'";
        $res = mysqli_query($db->conn,$query);
        while ($row = mysqli_fetch_array($res)) {
            //$GLOBALS['busyContacts'][$row['ContactID'] . '_' . $row['GroupID']] = 1;
            $GLOBALS['busyContacts'][$row['ContactID']] = 1;
        }
    }
    return (!isset($GLOBALS['busyContacts'][$cid]));
    /*    if ($gid) {
      return !isset($GLOBALS['busyContacts'][$cid . '_' . $gid]);
      }
      return true; */
}

function checkGroupDelete($gid) {
    global $db;
    if (!isset($GLOBALS['busyGroups'])) {
        $GLOBALS['busyGroups'] = array();
        $query = "select distinct TickleContact as GroupID from tickle where Status='Y'";
        $res = mysqli_query($db->conn,$query);
        while ($row = mysqli_fetch_array($res)) {
            $GLOBALS['busyGroups'][$row['GroupID']] = 1;
        }
    }
    return!isset($GLOBALS['busyGroups'][$gid]);
}

function GetMessagePart($res, $msg, $part) {
    $sec = mailparse_msg_get_part($res, $part);
    ob_start();
    mailparse_msg_extract_part($sec, $msg);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

function MsgAddressParse($addr) {
    $arr = mailparse_rfc822_parse_addresses($addr);
    for ($i = 0; $i < count($arr); $i++) {
        $arr[$i]['display'] = MimeHeaderDecode($arr[$i]['display']);
    }
    return $arr;
}

function MimeHeaderDecode($hdr) {
    $ret = "";
    $elms = imap_mime_header_decode($hdr);
    for ($i = 0; $i < count($elms); $i++) {
        $txt = $elms[$i]->text;
        if ($elms[$i]->charset != 'default') {
            $txt = iconv($elms[0]->charset, "utf-8", $txt);
        }
        $ret.=$txt;
    }
	return $ret;
   // return str_replace("'", "", $ret); comment by manvinder 10-10-2014
}

function MsgHeadersParse($hdrs) {
    $arr = (array) imap_rfc822_parse_headers($hdrs);
    $arr['subject'] = $arr['Subject'] = MimeHeaderDecode($arr['subject']);

    $addrs = array("to", "from", "reply_to", "sender", "cc");
    for ($j = 0; $j < count($addrs); $j++) {
        $adrfield = $addrs[$j] . "address";
        $arr[$adrfield] = "";
        for ($i = 0; $i < count($arr[$addrs[$j]]); $i++) {
            $row = (array) ($arr[$addrs[$j]][$i]);
            $row['personal'] = MimeHeaderDecode($row['personal']);
            $row['email'] = $row['mailbox'] . '@' . $row['host'];
            $arr[$addrs[$j]][$i] = $row;
            if ($arr[$adrfield] != "") {
                $arr[$adrfield] = $arr[$adrfield] . ", ";
            }
            $arr[$adrfield] = $arr[$adrfield] . $row['personal'] . " <" . $row['email'] . ">";
        }
    }
    return $arr;
}

function TextMsgParse($msg) {
    $msgstruct = array();
    $mime = mailparse_msg_create();
    mailparse_msg_parse($mime, $msg);
    $struct = mailparse_msg_get_structure($mime);
     
    /* print a choice of sections */
    foreach ($struct as $st) {
        /* get a handle on the message resource for a subsection */
        $section = mailparse_msg_get_part($mime, $st);
        /* get content-type, encoding and header information for that section */
        $info = mailparse_msg_get_part_data($section);
        $attach = isset($info["disposition-filename"]);
        if ($info["content-type"] == "text/plain" && !$attach) {
            //echo $info["charset"];
            $msgstruct['text'] = iconv($info["charset"], "utf-8", GetMessagePart($mime, $msg, $st));
        }
        if ($info["content-type"] == "text/html" && !$attach) {
            $msgstruct['html'] = iconv($info["charset"], "utf-8", GetMessagePart($mime, $msg, $st));
        }
        if ($attach) {
            if (!isset($msgstruct['attachments'])) {
                $msgstruct['attachments'] = array();
            }
            $attach = array();
            $attach['filename'] = MimeHeaderDecode($info["disposition-filename"]);
            $attach['content'] = GetMessagePart($mime, $msg, $st);
            $attach['content-disposition'] = MimeHeaderDecode($info["content-disposition"]);
            array_push($msgstruct['attachments'], $attach);
        }
        if (isset($info['content-id'])) {
            if (!isset($msgstruct['images'])) {
                $msgstruct['images'] = array();
            }
            $attach = array();
            if(isset($info["content-name"])){
                $attach['filename'] = MimeHeaderDecode($info["content-name"]);
            }else{
                $attach['filename'] = '';
            }
            $attach['content'] = GetMessagePart($mime, $msg, $st);
            $msgstruct['images'][$info['content-id']] = $attach;
        }
        //echo "<td>" . $info["charset"] . "</td>\n";
    }
    mailparse_msg_free($mime);
    return $msgstruct;
}

function MsgParse($mFile, $showpart) {

    $mime = mailparse_msg_parse_file($mFile);
    /* return an array of message parts - this contsists of the names of the parts
     * only */
    $struct = mailparse_msg_get_structure($mime);

    echo "<table>\n";
    /* print a choice of sections */
    foreach ($struct as $st) {
        echo "<tr>\n";
        echo "<td><a href=\"$PHP_SELF?showpart=$st\">$st</a></td>\n";
        /* get a handle on the message resource for a subsection */
        $section = mailparse_msg_get_part($mime, $st);
        /* get content-type, encoding and header information for that section */
        $info = mailparse_msg_get_part_data($section);
        //print_r($info);
        echo "\n";
        echo "<td>" . $info["content-type"] . "</td>\n";
        echo "<td>" . $info["content-disposition"] . "</td>\n";
        echo "<td>" . $info["disposition-filename"] . "</td>\n";
        echo "<td>" . $info["charset"] . "</td>\n";
        echo "</tr>";
    }
    echo "</table>";
    /* if we were called to display a part, do so now */
    if ($showpart) {
        /* get a handle on the message resource for the desired part */
        $sec = mailparse_msg_get_part($mime, $showpart);
        echo "<table border=1><tr><th>Section $showpart</th></tr><tr><td>";
        ob_start();
        /* extract the part from the message file and dump it to the output buff
          er
         * */
        mailparse_msg_extract_part_file($sec, $mFile);
        $contents = ob_get_contents();
        ob_end_clean();
        /* quote the message for safe display in a browser */
        echo nl2br(htmlentities($contents)) . "</td></tr></table>";
        ;
    }
    mailparse_msg_free($mime);
}

function Translate($lang, $key, $bname = false) {
    $bname = ($bname !== false) ? strtolower(trim($bname)) : strtolower(trim($GLOBALS['upage']));
    $lang = strtolower($lang);
    $checkresources = array();
    if ($lang != "") {
        $checkresources[] = LOCAL_RESOURCES . $bname . "." . $lang . ".resx";
        $checkresources[] = GLOBAL_RESOURCES . "." . $lang . ".resx";
    }
    $checkresources[] = LOCAL_RESOURCES . $bname . ".resx";
    $checkresources[] = GLOBAL_RESOURCES . ".resx";
    foreach ($checkresources as $resfile) {
        if (!file_exists($resfile)) {
            continue;
        }
        if (!isset($GLOBALS[$resfile])) {
            $GLOBALS[$resfile] = parse_ini_file($resfile);
        }
        if (isset($GLOBALS[$resfile][strtolower($key)])) {
            return $GLOBALS[$resfile][strtolower($key)];
        }
    }
    return $key;
}

function utf8_basename($filename) {
    // We always check for forward slash AND backward slash
    // because they could be mixed or "sneaked" in. ;)
    // You know, never trust user input...
    if (mb_strpos($filename, '/') !== false) {
        $filename = mb_substr($filename, mb_strrpos($filename, '/') + 1);
    }

    if (mb_strpos($filename, '\\') !== false) {
        $filename = mb_substr($filename, mb_strrpos($filename, '\\') + 1);
    }

    return $filename;
}

function GetDomain($eml) {
    $dd = trim(preg_replace('/[^@]+@/i', '', $eml));
    return preg_replace('/\.[^\.]+$/', '', $dd);
}

//Custom functions for whmcs
function whmcs_check_user($params) {
    if (!isset($params['email'])) {
        return array('error' => 'Please Enter email');
    } else {
        $postfields['action'] = 'getclientsdetails';
        $postfields['email'] = $params['email'];
        $apiResponse = whmcs_callAPI($postfields);
        if ($apiResponse->result == 'success') {
            return array('error' => 'User Already registered with this email . Please try another');
        } else {
            return false;
        }
    }
}

function whmcs_register($params) {
    if (empty($params['EmailID'])) {
        return array('error' => 'Please Enter email');
    } else {
        
        $checkmail = whmcs_checkmail($params);
        if(isset ($checkmail['error']) && $checkmail['error'] == 'ok'){
            
      //  echo '<pre>';    
       // print_r($params);
       // echo '</pre>';
      //  die();
        #Prepare Registrarion postfield array
        $postfields['action'] = 'addclient';
        $postfields['firstname'] = $params['FirstName'];
        $postfields['lastname'] = $params['LastName'];
        $postfields['email'] = $params['EmailID'];
        
        $postfields['phonenumber'] = $params['Phone'];
        $postfields['address1'] = $params['Address'];
        $postfields['city'] = $params['City'];
        $postfields['postcode'] = $params['PostCode'];
        $postfields['country'] = $params['country'];
        $postfields['state'] = $params['State'];
        
        if(isset($params['nutrient'])){
          $SpecialTickle = $params['nutrient'];  
        }
        
        $postfields['password2'] = $params['Password'];
        $postfields["customfields"] = base64_encode(serialize(array("2"=>"$params[Username]","3"=>"$params[Password]",
            "7"=>"$params[Timezone]","5"=>"$params[Plan]","8"=>"$SpecialTickle")));
        
        $postfields['noemail'] = true;
        if($params['Plan'] == '1'){
        $postfields['skipvalidation'] = true;
        }
        
        $response = whmcs_callAPI($postfields);
        
      //  echo '<pre>';
      //  print_r($response);
      //  echo '</pre>';
         if ($response->result == 'success') {
            $postfields = array();
            $postfields['clientid'] = $response->clientid;
            $postfields['status'] = 'active'; // change on 19-10-2014
            $apiResponse = whmcs_updateClient($postfields);
            if ($apiResponse['error']) {
                return $apiResponse;
            } else {
                
                return true;
            }
        } else {
            return array("error" => 'Error while registration');
        }
    }
    else{
        return $checkmail;
    }
    }
}

function whmcs_updateClient($postfields){
    if (!empty($postfields['clientid']) || !empty($postfields['email'])) {
        $postfields['action'] = 'updateclient';
        $apiresponse = whmcs_callAPI($postfields);
      
        if ($apiresponse->result == 'success') {
            return array('error' => false);
        } else {
            return array('error' => 'Error while updating client');
        }
    } else {
        return array('error' => 'One of clientid or email is required');
    }
}

function whmcs_checkmail($params){
	
    if (!empty($params['EmailID'])) {
        $postfields['action'] = 'getclientsdetails';
        $postfields['email'] = $params['EmailID'];
        $apiresponse = whmcs_callAPI($postfields);
	    //echo"test";
        //print_r($apiresponse);die();
        if ($apiresponse->result == 'success') {
            
            return array('error' => false, 'userid' => $apiresponse->userid,'passowrd'=>$apiresponse->customfields3); 
        } else {
            return array('error' => 'ok'); 
            
        }
    } else {
        return array('error' => 'One of clientid or email is required');
    }
}

function whmcs_getorder($params){
    if (!empty($params)) {
        $postfields['action'] = 'getorders';
        $postfields['userid'] = $params;
        $apiresponse = whmcs_callAPI($postfields);
    
        if ($apiresponse->result == 'success' && $apiresponse->orders->order[0]->userid!="") {
           
            foreach($apiresponse->orders->order as $order){
                $invoiceid = $order->invoiceid;
                $amount = $order->amount;
                $status = $order->status;
                $userid = $order->userid;
            }
            return array('invoiceid' => $invoiceid, 'amount' => $amount, 'status' => $status,'userid'=>$userid); 
        } else {
            return array('error' => 'no order'); 
            
        }
    } else {
        return array('error' => 'One of clientid is required');
    }
}


function whmcs_getproducdetails($params) {
    if (!empty($params)) {
        $postfields['action'] = 'getclientsproducts';
        $postfields['clientid'] = $params;
        $apiresponse = whmcs_callAPI($postfields);
        
        if ($apiresponse->result == 'success') {
            
            return $apiresponse; 
        } else {
            return array('error' => 'no order'); 
            
        }
    } else {
        return array('error' => 'One of clientid is required');
    }
}

function whmcs_getannouncements(){
    
        $postfields['action'] = 'getannouncements';
        $apiresponse = whmcs_callAPI($postfields);
        if ($apiresponse->result == 'success') {
        return $apiresponse; 
        } else {
        return array('error' => 'No news'); 
        }     
}

//Login Check and do Login
function whmcs_validate_login($postfields){
    //Required variables email,password2.
    $postfields["action"] = "validatelogin";
    return whmcs_callAPI($postfields);
}

function whmcs_getPlans() {
    $response = array();
    $postfields["action"] = "getproducts";
    $postfields["gid"] = "1";
    $apiResponse = whmcs_callAPI($postfields);
    if ($apiResponse->result == 'success' && $apiResponse->totalresults > 0) {
        foreach ($apiResponse->products->product as $key => $product) {
            $response[$key]['pid'] = $product->pid;
            $response[$key]['name'] = $product->name;
        }
    }
    return $response;
}

function whmcs_callAPI($params){

    //This function will call the whmcs external api , Required params are action and required params specific to the api to be called
    if (!isset($params['action'])) {
        return "Invalid Method";
    }
    $url = "https://secure.tickletrain.com/includes/api.php"; # URL to WHMCS API file
    $username = "TT"; # Admin username goes here
    $password = "T!K|E"; # Admin password goes here
    $username = "manvinder"; # Admin username goes here
    $password = "TT2013WHMC$"; # Admin password goes here

    $params["username"] = $username;
    $params["password"] = md5($password);
    $params["responsetype"] = "json";
    $query_string = "";
    foreach ($params AS $k => $v)
        $query_string .= "$k=" . urlencode($v) . "&";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $jsondata = curl_exec($ch);
	
    if (curl_error($ch))
        die("Connection Error: " . curl_errno($ch) . ' - ' . curl_error($ch));
    curl_close($ch);
    return json_decode($jsondata); # Decode JSON String
}

function pr($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

function preText($txt, $max_len=250, $pref=" "){
    $txt = trim($txt);
    if (strlen($txt) <= $max_len) {
        return $txt;
    }
    //$txt = preg_replace("/[\r\n]+/"," ",$txt);
    $ret = array();
    $cp = $txt;
    while (strlen($txt) > $max_len) {
        $txt = trim($txt);
        $txt = substr($txt, 0, $max_len);
        $ind = strrpos($txt, $pref);
        if (!$ind) {
            $ind = strrpos($txt, " ");
        }
        if ($ind) {
            $ret[] = trim(substr($txt, 0, $ind + 1));
            $txt = substr($cp, $ind + 1);
        } else {
            $ret[] = $txt;
            $txt = substr($cp, $max_len);
        }
        $cp = $txt;
    }
    $ret[] = $txt;
    return nl2br(join("\n", $ret));
}

function whmcs_unsuspend($serviceid){
  if($serviceid!=""){
      
       $postfields['action'] = 'moduleunsuspend';
       $postfields['accountid'] = $serviceid;
       $apiresponse = whmcs_callAPI($postfields);
      
       if ($apiresponse->result == 'success'){
            return success;
        }else{
            return 'error'; 
        }
  }else{
      return 'error';
  }  
}


function whmcs_upgrade($serviceid,$clientid){
  if($serviceid!=""){
      
       $postfields["action"] = "upgradeproduct";
       $postfields["clientid"] = $clientid;
       $postfields["serviceid"] = $serviceid;
       $postfields["type"] = "product";
       $postfields["newproductid"] = "1";
       $postfields["newproductbillingcycle"] = "monthly";
       $postfields["paymentmethod"] = "paypal";
      
       $apiResponse = whmcs_callAPI($postfields);
       if ($apiResponse->result == 'success'){
            return 'success';
        }else{
            return 'error'; 
        }
  }else{
      return 'error';
  }  
}


function whmcs_upgrade_new_check($serviceid1,$userid,$npid){
  if($serviceid1!=""){
      
       $postfields["action"] = "upgradeproduct";
       $postfields["clientid"] = $userid;
       $postfields["serviceid"] = $serviceid1;
       $postfields["type"] = "product";
       $postfields["newproductid"] = $npid;
       $postfields["newproductbillingcycle"] = "monthly";
       $postfields["paymentmethod"] = "paypal";
      
       $apiResponse = whmcs_callAPI($postfields);
       echo '<pre>';
       print_r($apiResponse);
       die();
       if ($apiResponse->result == 'success'){
            return 'success';
        }else{
            return 'error'; 
        }
  }else{
      return 'error';
  }  
}

function GetCustomSubject($TaskID){
    global $db;
    $query = mysqli_query($db->conn,"select FollowTickleTrainID,TickleTrainID from task where TaskID='".$TaskID."'") or die(mysqli_error($db->conn). __LINE__);
    $row = mysqli_fetch_assoc($query);

    $FollowTickleTrainID = $row['FollowTickleTrainID'];
    $TickleTrainID = $row['TickleTrainID'];

    if($FollowTickleTrainID == 0){
    $GetTickleCustomSubjectQuery = mysqli_query($db->conn,"select custom_subject from tickle where TickleTrainID='".$TickleTrainID."'");
    $GetTickleCustomSubjectRow = mysqli_fetch_assoc($GetTickleCustomSubjectQuery);
    $CustomSubject = $GetTickleCustomSubjectRow['custom_subject'];
    }else{
     $GetTickleFollowCustomSubjectQuery = mysqli_query($db->conn,"select custom_subject from ticklefollow where FollowTickleTrainID='".$FollowTickleTrainID."'");   
     $GetTickleFollowCustomSubjectRow = mysqli_fetch_assoc($GetTickleFollowCustomSubjectQuery);
     $CustomSubject = $GetTickleFollowCustomSubjectRow['custom_subject'];
    }
    
    if(isset($CustomSubject) && $CustomSubject != ""){
        return $CustomSubject;
    }else{
        return false;
    }
}


function get_comments($MailID){
   global $db;
   $query = "select tickleuser.FirstName, tickleuser.LastName, contact_list.FirstName as CFN, contact_list.LastName as CLN , contact_list.EmailID as CEID ,comments.id,comments.comment,comments.comment_by,DATE_FORMAT(comments.created_at, '%b %d , %Y') as created_at, DATE_FORMAT(comments.created_at, '%h:%i %p') as created_time from comments inner join tickleuser ON (comments.TickleID=tickleuser.TickleID) inner join user_mail ON (comments.MailID=user_mail.MailID) inner join contact_list on (contact_list.ContactID=user_mail.ContactID) where comments.MailID='".$MailID."'";
   return  $db->query_to_array($query);
}


function get_comment_user($comment){

    if($comment['comment_by'] == 'owner'){
        return $comment['FirstName'].' '.$comment['LastName'];
    }else{
        if(!empty($comment['CFN'])  || !empty($comment['CLN'])  ){
             return trim($comment['CFN'].' '.$comment['CLN']);
        }else{
             return '['.trim($comment['CEID']).']';
        }
    }
}

?>