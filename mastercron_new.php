<?php
$logDate = date("Y-m-d H:i:s");
$logDate02 = date("Y-m-d H:i:s", strtotime('-1 hour', strtotime($logDate)));
$threeDayBack = date("Y-m-d", strtotime('-7 days', strtotime($logDate)));
$tomorrowDate = date("Y-m-d", strtotime('+1 days', strtotime($logDate)));
//ignore_user_abort(true); // run script in background until cron completes
ini_set('display_errors', 1);
ini_set('memory_limit', -1);
//error_reporting(E_ALL);
//error_reporting(E_STRICT);
include_once("includes/data.php");
include("includes/function/func.php");
include("includes/class/phpmailer/class.phpmailer.php");

$Cdate = gmdate("Y-m-d H:i:s");

define('ROOT_FOLDER', $RootFolder);
define('HOME_FOLDER', GetHomeDir() . "/");
define('IMAGE_BASE_FOLDER', str_replace(ROOT_FOLDER, "", HOME_FOLDER));
define('FULL_UPLOAD_FOLDER', HOME_FOLDER . "upload-files/");
define('LOGS_FOLDER', HOME_FOLDER . "_logs/");
define('SERVER_NAME', "client.tickletrain.com");
error_reporting(E_ERROR);


//Google Auth and Zend Mailer Files
set_include_path('/var/www/vhosts/client.tickletrain.com/httpdocs/google_auth2/');

// require_once('Zend/Loader/Autoloader.php');						   
require_once 'Zend/Mail/Transport/Smtp.php';
require_once 'Zend/Mail.php';
require_once 'Zend/Mime.php';

//$gfpath01 = str_replace('app','',__DIR__);
//$gfpath = $gfpath01.'/google_auth2/';
require_once 'src/Google_Client.php'; // include the required calss files for google login
require_once 'src/contrib/Google_PlusService.php';
require_once 'src/contrib/Google_Oauth2Service.php';



//echo $Cdate; die();




/* 11-02-2019 */
function update_followups_time($MailID,$TaskID,$FollowTickleTrainID){
    global $db;
    //if($FollowTickleTrainID=='0')
    //{
        $uQuery = mysqli_query($db->conn,"SELECT TaskID,TaskGMDate,TaskInitiateDate from task where MailID = '".$MailID."' and TaskID != '".$TaskID."' and Status != 'S' ");
        while ($rowT = mysqli_fetch_array($uQuery)) {
            //$taskGMDateNew = date("Y-m-d H:i:s", strtotime('+1 days', strtotime($rowT['TaskGMDate'])));
            //$TaskInitiateDateNew = date("Y-m-d H:i:s", strtotime('+1 days', strtotime($rowT['TaskInitiateDate'])));
            
            $rGMDate = strtotime($rowT['TaskGMDate']);
            $taskGMDateNew = date('Y-m-d H:i:s', mktime(date('H',$rGMDate), date('i',$rGMDate), date('s',$rGMDate), date('m',$rGMDate), date('d',$rGMDate) + 1, date('Y',$rGMDate)));
            
            $rIniDate = strtotime($rowT['TaskInitiateDate']);
            $TaskInitiateDateNew = date('Y-m-d H:i:s', mktime(date('H',$rIniDate), date('i',$rIniDate), date('s',$rIniDate), date('m',$rIniDate), date('d',$rIniDate) + 1, date('Y',$rIniDate)));
            
            
            mysqli_query($db->conn,"update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $rowT['TaskID'] . "' ");
        }           
    //} 
}


$userSmtp = array();
$uQuery = mysqli_query($db->conn,"SELECT TickleID from tickleuser where DMSmtpOff='1' and DMUse='1' ");
while ($rowU = mysqli_fetch_array($uQuery)) { $userSmtp[] = $rowU['TickleID']; }
if(empty($userSmtp)){ $userSmtp = "''"; }else{ $userSmtp = implode(',', $userSmtp); }

$secUserSmtp = array();
$usecQuery = mysqli_query($db->conn,"SELECT id from secondaryEmail where DMSmtpOff='1' and DMUse='1' ");
while ($rowSecU = mysqli_fetch_array($usecQuery)) { $secUserSmtp[] = $rowSecU['id']; }
if(empty($secUserSmtp)){ $secUserSmtp = "''"; }else{ $secUserSmtp = implode(',', $secUserSmtp); }

$query = "SELECT tk.* from task tk inner join user_mail um on um.MailID=tk.MailID WHERE tk.TaskGMDate<='$Cdate' and tk.Status='Y' and (tk.TickleID IN (".$userSmtp.") or tk.secondaryEmailId IN (".$secUserSmtp.")) and tk.TickleID = '1992'  order by tk.TickleID ";

$result =mysqli_query($db->conn,$query);
while($row = mysqli_fetch_array($result)) {
       
       //echo '<pre>'; print_r($row);       
        $taskGMDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskGMDate']));
        $TaskInitiateDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskInitiateDate']));
       // echo "update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $row['TaskID'] . "'<br>";
       mysqli_query($db->conn,"update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $row['TaskID'] . "' ");
        update_followups_time($row['MailID'],$row['TaskID'],$row['FollowTickleTrainID']);        
        

}












echo '<br> <br> <br>Cron Done 2';
?>
