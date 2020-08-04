<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require("includes/class/PHPMailer/src/Exception.php");
require("includes/class/PHPMailer/src/PHPMailer.php");
require("includes/class/PHPMailer/src/SMTP.php");

ignore_user_abort(true); // run script in background until cron completes

//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//error_reporting(E_STRICT);
include_once("includes/data.php");
include("includes/function/func.php");
define('ROOT_FOLDER', $RootFolder);
define('HOME_FOLDER', GetHomeDir() . "/");
define('IMAGE_BASE_FOLDER', str_replace(ROOT_FOLDER, "", HOME_FOLDER));
define('FULL_UPLOAD_FOLDER', HOME_FOLDER . "upload-files/");
define('LOGS_FOLDER', HOME_FOLDER . "_logs/");
define('SERVER_NAME', "client.tickletrain.com");
//error_reporting(E_ERROR);


$timezones = gettimezones();
$lastTime = 0;
if (file_exists(LOGS_FOLDER . "cronmail.tm")) {
    $lastTime = file_get_contents(LOGS_FOLDER . "cronmail.tm");
}

$mail = new PHPMailer(false); //New instance, with exceptions enabled
/*
  $mailCc = new PHPMailer(false);
  $mailCc->IsMail();
 */
$mailBcc = new PHPMailer(false);
$mailBcc->IsMail();
//date_default_timezone_set("Etc/GMT-0"); //set date
//echo $Cdate = gmdate("Y-m-d H:i:s");die();

$Cdate = "2016-04-07 16:00:00";
//echo $Cdate;
$dofweek = intval(date('w'));
$query = "SELECT * from task WHERE TaskGMDate='$Cdate' and Status='S' and TickleID=994"; // and Approve='Y'
//$query = "SELECT * from task WHERE TickleID=1176"; // and For testing
$result = mysqli_query($db->conn,$query);
$Mail = array();
$Priority = array('1' => "1 (High)", '3' => "3 (Normal)", '5' => "5 (Low)");
$tasks = array();
$tickles = array();
while ($row = mysqli_fetch_array($result)) {
    $tasks[] = $row;
    $tickles[] = $row['TickleID'];
}

//echo count($tasks);
//die('ggdfgdf');
mysqli_free_result($result);
$contact_list = array();
if (count($tasks) > 0) {
    $sql_cmail = mysqli_query($db->conn,"select * from contact_list where TickleID in (" . implode(",", $tickles) . ")");
    while ($row = mysqli_fetch_array($sql_cmail)) {
        $contact_list[$row['ContactID']] = $row;
    }
    mysqli_free_result($sql_cmail);
}


$ttclient = array();
$ttclient['id'] = 0;
$count = 1;

//echo"<pre>";
$send_array = array();
//print_r($tasks);die();
for ($tt = 0; $tt < count($tasks); $tt++) {
	$row = $tasks[$tt];
	$TaskID = $row['TaskID'];
	if (!in_array($TaskID, $send_array)){     // duplicate message send 08/apr/2016
		echo $TaskID.'<br>';
	}
	 array_push($send_array,$TaskID);

}


?>
