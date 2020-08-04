
<?php

//error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_DEPRECATED));
ini_set('session.cookie_domain','.tickletrain.com');
header("Content-Type: text/html;charset=UTF-8");
error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));
if (get_magic_quotes_gpc()) { // Yes? Strip the added slashes 
    $_REQUEST = array_map('stripslashes', $_REQUEST);
    $_GET = array_map('stripslashes', $_GET);
    $_POST = array_map('stripslashes', $_POST);
    $_COOKIE = array_map('stripslashes', $_COOKIE);
}
include("function/func.php");
define('ROOT_FOLDER', GetRootFolder());
define('UPLOAD_FOLDER', '/' . ROOT_FOLDER . 'upload-files/');
define('FULL_UPLOAD_FOLDER', $_SERVER['DOCUMENT_ROOT'] . UPLOAD_FOLDER);
define('GLOBAL_RESOURCES', $_SERVER['DOCUMENT_ROOT'] . '/' . ROOT_FOLDER . 'GlobalResources/globals');
define('LOCAL_RESOURCES', $_SERVER['DOCUMENT_ROOT'] . '/' . ROOT_FOLDER . 'LocalResources/');
//include("class/class-db.php");
include("data.php");
include("lang.php");
define('PAGE_LIMIT', 20);
include("class/phpmailer/class.phpmailer.php");
//echo date("m/d/Y H:i:s A e ");
if ($_SESSION['TickleID'] > 0) {
    $user_det = tablelist('tickleuser', '', array("WHERE UserName ='" . $_SESSION['UserName'] . "' and TickleID='" . $_SESSION['TickleID'] . "'"));
    $UDetail = $user_det[0];
    if ($UDetail['TimeZone'] != "") {
        $timezones = array(
            '-12.0' => 'Etc/GMT+12',
            '-11.0' => 'Etc/GMT+11',
            '-10.0' => 'Etc/GMT+10',
            '-9.0' => 'Etc/GMT+9',
            '-8.0' => 'Etc/GMT+8',
            '-7.0' => 'Etc/GMT+7',
            '-6.0' => 'Etc/GMT+6',
            '-5.0' => 'Etc/GMT+5',
            '-4.0' => 'Etc/GMT+4',
            '-3.5' => 'America/St_Johns', // an hour ahead
            '-3.0' => 'Etc/GMT+3',
            '-2.0' => 'Etc/GMT+2',
            '-1.0' => 'Etc/GMT+1',
            '0.0' => 'Etc/GMT-0',
            '1.0' => 'Etc/GMT-1',
            '2.0' => 'Etc/GMT-2',
            '3.0' => 'Etc/GMT-3',
            '3.5' => 'Asia/Tehran',
            '4.0' => 'Etc/GMT-4',
            '4.5' => 'Asia/Kabul',
            '5.0' => 'Etc/GMT-5',
            '5.5' => 'Asia/Calcutta',
            '6.0' => 'Etc/GMT-6',
            '7.0' => 'Etc/GMT-7',
            '8.0' => 'Etc/GMT-8',
            '9.0' => 'Etc/GMT-9',
            '9.5' => 'Australia/Darwin',
            '10.0' => 'Etc/GMT-10',
            '11.0' => 'Etc/GMT-11',
            '12.0' => 'Etc/GMT-12'
        );

        $GMT = $UDetail['TimeZone'];
        date_default_timezone_set($timezones[$GMT]);
    }
}
include("class/class.FormValidation.php");
$facebookaccount->app = '128045707299309'; // facebook appID
$facebookaccount->secret = '70c502ad50123f8f94393c76cd123905';  // facebook appID secret
//$facebookaccount->nutrient = 'isagenix';
if (isset($_REQUEST['nutrient']) && $_REQUEST['nutrient'] == 'isagenix') {
    $facebookaccount->nutrient = 'isagenix';
}
$_SESSION["facebookaccount"] = $facebookaccount;
?>
