<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require("includes/class/PHPMailer/src/Exception.php");
require("includes/class/PHPMailer/src/PHPMailer.php");
require("includes/class/PHPMailer/src/SMTP.php");

date_default_timezone_set("Etc/GMT-0"); //set date
        // $mail = new PHPMailer(false);
        // echo "<pre>";
        // print_r($mail);
        // die;

$logDate = date("Y-m-d H:i:s");
$logDate02 = date("Y-m-d H:i:s", strtotime('-1 hour', strtotime($logDate)));
$threeDayBack = date("Y-m-d", strtotime('-7 days', strtotime($logDate)));
$tomorrowDate = date("Y-m-d", strtotime('+1 days', strtotime($logDate)));
//ignore_user_abort(true); // run script in background until cron completes
ini_set('memory_limit', -1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
include_once("includes/data.php");
include("includes/function/func.php");
//include("includes/class/phpmailer/class.phpmailer.php");
define('ROOT_FOLDER', $RootFolder);
define('HOME_FOLDER', GetHomeDir() . "/");
define('IMAGE_BASE_FOLDER', str_replace(ROOT_FOLDER, "", HOME_FOLDER));
define('FULL_UPLOAD_FOLDER', HOME_FOLDER . "upload-files/");
define('LOGS_FOLDER', HOME_FOLDER . "_logs/");
define('SERVER_NAME', "client.tickletrain.com");
//error_reporting(E_ERROR);

$email_templete =  file_get_contents(HOME_FOLDER.'emails/bcc_tickle.html');
$reminder_task_templete =  file_get_contents(HOME_FOLDER.'emails/reminder_task.html');
$timing_array = [ 
            "two"  => ["1H","One Hour"],
            "three"=> ["2H","Two Hours"],
            "four" => ["3H","Three Hours"],
            "five" => ["1D","One Day"],
            "six"  => ["2D","Two Days"],
            "saven"=> ["3D","Three Days"],
            "eight"=> ["1W","One Week"],
            "nine"=> ["2W","Two Weeks"],
            "ten"  => ["1M","One Month"]
        ];

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

//Google Auth and Zend Mailer Files

function constructAuthString($email, $accessToken) {
    return base64_encode("user=$email\1auth=Bearer $accessToken\1\1");
}

if(!is_dir(__DIR__ . '/ticklelog/'.date('d-m-Y'))){
    @mkdir(__DIR__ . '/ticklelog/'.date('d-m-Y'));
}


$client = new Google_Client();
$client->setClientId('799405691032-er3cilvjgrqgtlfreuffllvkp2ouvrjb.apps.googleusercontent.com'); // paste the client id which you get from google API Console
$client->setClientSecret('QYmRweaDw20scMLTidBR8MRB'); // set the client secret

$bccCheck = array();
$logResult = array();
$sentTasks = array();
if(file_exists(__DIR__ . '/ticklelog/'.date('d-m-Y').'/cronmail.json'))
{
    $logJsonData = @file_get_contents(__DIR__ . '/ticklelog/'.date('d-m-Y').'/cronmail.json');
    $todayLogData = json_decode('['.$logJsonData.']',true);
    if(is_array($todayLogData)){
        foreach($todayLogData as $todayLogRes){

            if($todayLogRes['date'] >= $logDate02 && $todayLogRes['date'] <= $logDate){
                $logResult[] = $todayLogRes['TaskID'];
            }
            if($todayLogRes['ttresponse']=='BCC mail sent successfully'){
                $bccCheck[] = $todayLogRes['TaskID'];
            }        
            if($todayLogRes['ttresponse']=='sent successfully'){
                $sentTasks[] = $todayLogRes['TaskID'];
                $logResult[] = $todayLogRes['TaskID'];
            }
        }
    }
}else {
    $todayLogData = array('TickleID'=>"",'TaskID'=>"",'ttrequest'=>'','type'=>'sendtickle','date'=>"",'ttresponse'=>"");
    $fp = @fopen(__DIR__ . '/ticklelog/'.date('d-m-Y').'/cronmail.json', 'w');
    @fwrite($fp, json_encode($todayLogData));
    @fclose($fp);
}


 
function update_followups_time($MailID,$TaskID,$FollowTickleTrainID){
    global $db;
    //if($FollowTickleTrainID=='0')
    //{

//echo "SELECT ticklefollow.NoWeekend, tickle.NoWeekend as TickleNoWeekend,tickle.DailyDays,ticklefollow.DailyDaysFollow,ticklefollow.TickleTimeFollow,task.FollowTickleTrainID , task.TimeZone,task.TaskID,task.TaskGMDate,task.TaskInitiateDate from task left join ticklefollow ON ticklefollow.FollowTickleTrainID = task.FollowTickleTrainID left join tickle ON tickle.TickleTrainID = task.TickleTrainID  where task.MailID = '".$MailID."'  and task.Status != 'S' ORDER BY task.TaskID";

        $uQuery = mysqli_query($db->conn,"SELECT ticklefollow.NoWeekend, tickle.NoWeekend as TickleNoWeekend,tickle.DailyDays,ticklefollow.DailyDaysFollow,ticklefollow.TickleTimeFollow,task.FollowTickleTrainID , task.TimeZone,task.TaskID,task.TaskGMDate,task.TaskInitiateDate from task left join ticklefollow ON ticklefollow.FollowTickleTrainID = task.FollowTickleTrainID left join tickle ON tickle.TickleTrainID = task.TickleTrainID  where task.MailID = '".$MailID."'  and task.Status != 'S' ORDER BY task.TaskID");

        //and task.TaskID != '".$TaskID."'

        echo "<pre>";
        $iday='';
        while ($rowT = mysqli_fetch_assoc($uQuery)) {

            $rGMDate = strtotime($rowT['TaskGMDate']);
            $taskGMDateNew = date('Y-m-d H:i:s', mktime(date('H',$rGMDate), date('i',$rGMDate), date('s',$rGMDate), date('m',$rGMDate), date('d',$rGMDate) + 1, date('Y',$rGMDate)));
            
            $rIniDate = strtotime($rowT['TaskInitiateDate']);
            $TaskInitiateDateNew = date('Y-m-d H:i:s', mktime(date('H',$rIniDate), date('i',$rIniDate), date('s',$rIniDate), date('m',$rIniDate), date('d',$rIniDate) + 1, date('Y',$rIniDate)));
		
			/* Get Days*/
			if($rowT['FollowTickleTrainID'] == 0 && !empty($rowT['DailyDays'])):
				$days = $rowT['DailyDays'];
			else:
				$days = $rowT['DailyDaysFollow'];
			endif;
			
			
			if(empty($iday)):
				$iday=date('Y-m-d',strtotime($rowT['TaskInitiateDate']));
			endif;
			
			$iday = date("Y-m-d",strtotime('+'.$days.' days', strtotime($iday)));
			
			/* Check Week End */
			$NoWeekend = (isset($rowT['NoWeekend']) && !empty($rowT['NoWeekend']))?$rowT['NoWeekend']:$rowT['TickleNoWeekend'];
			//$NoWeekend = (isset($rowT['NoWeekend']) && !empty($rowT['NoWeekend']))?$rowT['NoWeekend']:'N';
			
			//echo "PreviousDate=".$iday; echo "<br/>";
			//echo "DaysAdd=".$days; echo "<br/>";
			//echo "Date=".$iday; echo "<br/>";
			/* For Weekend */
			if($NoWeekend=='Y'):
				if(date("D",strtotime($iday))=='Sat'):
					//echo "one day add to ".$iday."<br/>";
					$iday = date("Y-m-d",strtotime('+1 days', strtotime($iday)));
				endif;
				
				if(date("D",strtotime($iday))=='Sun'):
					//echo "one day add to ".$iday."<br/>";
					$iday = date("Y-m-d",strtotime('+1 days', strtotime($iday)));
				endif;
			endif;
			//echo "New Date=".$iday; echo "<br/>";
			//echo "<br/>"; echo "<hr/>";
			
					$gmdate = date('H:i:s',strtotime($rowT['TaskGMDate']));
					$taskGMDateNew = date('Y-m-d '.$gmdate.'', strtotime($iday));
					  $TaskInitiateDateNew = getlocaltime($taskGMDateNew, $rowT['TimeZone']);
			// echo "update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $rowT['TaskID'] . "' ";
          
			
			
           mysqli_query($db->conn,"update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $rowT['TaskID'] . "' ");
        }
    //} 
}


function update_google_token($refresh_token,$user_id)
    {
        global $client,$db;
        # code...
        try{
            $client->refreshToken($refresh_token);
            $getGoogleToken = $client->getAccessToken();
            $responce = json_decode($getGoogleToken, true);
            mysqli_query($db->conn,"update google_auth_tokens set access_token='" . $responce['access_token'] . "' , expires_in='" . $responce['expires_in'] . "' , created='" . $responce['created'] . "' where userid=".$user_id);
            return  $responce['access_token'];
        }catch(Exception $e){
            return false;
        }
    }


function create_logs($logData,$ttresponse){
    global $logDate; 
    global $logDate02;
    
    if(isset($logData['ttrequest']))
    {
        $ttrequest = $logData['ttrequest'];
    }else{
        $ttrequest = '';
    }
    
    if($ttresponse=='sending email failed reason up to 25 mails in one cron'){
        $data = array('TickleID'=>$logData['TickleID'],'TaskID'=>$logData['TaskID'],'ttrequest'=>$ttrequest,'type'=>'sendtickle','date'=>$logDate02,'ttresponse'=>$ttresponse);
    }
    else{
        $data = array('TickleID'=>$logData['TickleID'],'TaskID'=>$logData['TaskID'],'ttrequest'=>$ttrequest,'type'=>'sendtickle','date'=>$logDate,'ttresponse'=>$ttresponse);
    }
    WriteFile(__DIR__ . '/ticklelog/'.date('d-m-Y').'/cronmail.json', ','.json_encode($data), "a");
}

$logResult = array_unique($logResult);

$logResult  = empty($logResult)?"''":implode(',', $logResult);


$lastTime = 0;
if (file_exists(LOGS_FOLDER . "cronmail.tm")) {
    $lastTime = file_get_contents(LOGS_FOLDER . "cronmail.tm");
}


date_default_timezone_set("Etc/GMT-0"); //set date

$Cdate = gmdate("Y-m-d H:i:s");
//echo $Cdate;
echo ("Now: " . $Cdate);
echo "<br>";
$dofweek = intval(date('w'));


//if(file_exists(__DIR__ . '/ticklelog/cronmail.json'))
if(false)
{
    die('continue');
}else
{
    
    $fp01 = @fopen(__DIR__ . '/ticklelog/cronmail.json', 'w');
    @fwrite($fp01, 'cronrunning');
    @fclose($fp01);

    $userSmtp = array(); 
    $secUserSmtp = array();

    $uQuery = mysqli_query($db->conn,"SELECT TickleID from tickleuser where DMSmtpOff='1' and DMUse='1' ");
        while ($rowU = mysqli_fetch_assoc($uQuery)) {

            $userSmtp[] = $rowU['TickleID'];
        }
    $userSmtp  = empty($userSmtp)?"''" : implode(',', $userSmtp);


    $usecQuery = mysqli_query($db->conn,"SELECT id from secondaryEmail where DMSmtpOff='1' and DMUse='1' ");
    while ($rowSecU = mysqli_fetch_array($usecQuery)) {
        $secUserSmtp[] = $rowSecU['id'];
    }
    $secUserSmtp =  empty($secUserSmtp) ? "''" : implode(',', $secUserSmtp);


   $query = "SELECT tk.*,ticklefollow.NoWeekend, tickle.NoWeekend as TickleNoWeekend from task tk inner join user_mail um on um.MailID=tk.MailID   left join ticklefollow ON ticklefollow.FollowTickleTrainID = tk.FollowTickleTrainID left join tickle ON tickle.TickleTrainID = tk.TickleTrainID
   WHERE tk.TaskGMDate<='$Cdate' and tk.Status='Y' and tk.TaskID NOT IN (".$logResult.") and tk.TickleID NOT IN (".$userSmtp.") and tk.secondaryEmailId NOT IN (".$secUserSmtp.")  order by tk.TickleID limit 1000";

    //and tk.TickleID='1598'


   ///$query = "SELECT tk.* ,ticklefollow.NoWeekend, tickle.NoWeekend as TickleNoWeekend, um.message_id from task tk inner join user_mail um on um.MailID=tk.MailID left join ticklefollow ON ticklefollow.FollowTickleTrainID = tk.FollowTickleTrainID left join tickle ON tickle.TickleTrainID = tk.TickleTrainID WHERE tk.TaskID = 1265266 order by tk.TickleID limit 1000"; 


    //// and Approve='Y'
    //$query = "SELECT * from task WHERE TickleID=1176"; // and For testing
    $result =mysqli_query($db->conn,$query);
    $Mail = array();
    $Priority = array('1' => "1 (High)", '3' => "3 (Normal)", '5' => "5 (Low)");
    $tasks = array();
    $tickles = array();
    while ($row = mysqli_fetch_assoc($result)) {

        if($row['Pause']=='N'){
            $tasks[] = $row;
            $tickles[] = $row['TickleID'];       
        }else{
            // echo"<pre>";
            // print_r($row);
            update_followups_time($row['MailID'],$row['TaskID'],$row['FollowTickleTrainID']);
        }   

    }

    // echo"<pre>";
    // print_r($tasks);
   //die;
    //echo count($tasks);
    //die('ggdfgdf');
    mysqli_free_result($result);
    debug("Fetched " . count($tasks) . " tasks for sending");
    $contact_list = array();

    if (count($tasks) > 0) {
        $sql_cmail =mysqli_query($db->conn,"select * from contact_list where TickleID in (" . implode(",", $tickles) . ")");
        while ($row = mysqli_fetch_assoc($sql_cmail)) {
            $contact_list[$row['ContactID']] = $row;
        }
        mysqli_free_result($sql_cmail);
    }

    debug("Found " . count($contact_list) . " contacts");
    $ttclient = array();
    $ttclient['id'] = 0;
    $count = 1;
    $send_array = array();
    
    echo count($tasks) . ' task found <br>';

    for ($tt = 0; $tt < count($tasks); $tt++) {

        $logData = array();
        $oauthmain = false;
        $secAuth = false;
        $err = false;
        $row = $tasks[$tt];
        //print_r($row['secondaryEmailId']);die();continue;
        $MailID = $row['MailID'];
        $TickleID = $row['TickleID'];
        $TaskID = $row['TaskID'];
        $FollowTickleTrainID = $row['FollowTickleTrainID'];    
        $TickleTrainID = $row['TickleTrainID'];
        $CCMeFollow = 'N';
        
        //continue;
        $logData['TickleID'] = $row['TickleID'];
        $logData['MailID'] = $row['MailID'];
        $logData['TaskID'] = $row['TaskID'];
        //if($TickleID!=389)
        //continue;

        if ($ttclient['id'] != $TickleID) {
            $ttclient['id'] = $TickleID;
            $count = 1;
        } 
                
        //echo $count.'=='.$row['TickleID'].'<br>';

        $sql_mail =mysqli_query($db->conn,"select * from user_mail where MailID='$MailID' and TickleID='$TickleID'");

        //Auth Token Check
        $userToken = mysqli_fetch_array(mysqli_query($db->conn,"select access_token,refresh_token from google_auth_tokens where userid='" . $TickleID . "' "));
        if (!empty($userToken)) {
            $oauthmain = true;
        }
        //Auth Token check                    

        if (isset($rs_mail)) { unset($rs_mail); }

        $rs_mail = mysqli_fetch_assoc($sql_mail);
        mysqli_free_result($sql_mail);

        
        if (!is_array($rs_mail)) {
            $err = true;
        }

        if (!$err) {
            $sql_tickle =mysqli_query($db->conn,"select * from tickle where TickleTrainID='$TickleTrainID' and TickleID='$TickleID' and Status='Y'");
            $rs_tickle = mysqli_fetch_assoc($sql_tickle);
            mysqli_free_result($sql_tickle);
            if (!is_array($rs_tickle)) {
                $err = true;
            }
        }

        if (!$err) {
            $sql_user =mysqli_query($db->conn,"select * from tickleuser where TickleID='$TickleID' and Status='Y'");
            $rs_user = mysqli_fetch_assoc($sql_user);
            mysqli_free_result($sql_user);
            if (!is_array($rs_user)) {
                $err = true;
            }

            // if($_GET['test']){
                //  echo "<pre>";
                // print_r($rs_user);
            // }

        }

        if ($err) {
            echo "error delete";
            mysqli_query($db->conn,"delete from task where TaskID=" . @intval($TaskID));
            debug('Task ' . $TaskID . ' removed');
            continue;
        }

        
        
        $enable_alt = $rs_user['enable_alt'];
        $alt_email = $rs_user['alt_email'];
        $mail_type = $rs_user['mail_type'];
        
        $enable_alt_bcc = $rs_user['enable_alt_bcc'];
        $alt_email_bcc = $rs_user['alt_email_bcc'];
        
        $logData['Subject'] = $rs_mail['Subject'];
        $logData['ticklename'] = $rs_mail['XEnvelopeTo'];
        $logData['Subject'] = $rs_mail['Subject'];
        $logData['toaddress'] = $rs_mail['toaddress'];
        $logData['fromaddress'] = $rs_mail['fromaddress'];
        if($enable_alt_bcc=='1')
        {    
            $logData['bccaddress'] = $alt_email_bcc;
        }    
           
        
        $ContactID = $rs_mail['ContactID'];
        $rs_mail['ccaddress'] = trim($rs_mail['ccaddress']);
        debug('Task ' . $TaskID . ' started');
        //$FromEmailid = $rs_user['EmailID'];
        // $FromEmailid = extract_email_tt($rs_mail['fromaddress']);


        if ($rs_user['addon_hosting_id'] != '') {
            $postUrl = "https://secure.tickletrain.com/get_addon_info.php";
            $postdata = array(
                'get_addon_status' => true,
                'addon_hosting_id' => $rs_user['addon_hosting_id']
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $postUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POST, count($postdata));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $status = json_decode($response, 1);
        

            if ($rs_user['email_addon'] != '' && ($status['status'] == 'Active' || $status['status'] == 'Pending')) {

                if ($row['secondaryEmailId'] != '0') {
                    $oauthmain = false;
                    $secid = mysqli_fetch_assoc(mysqli_query($db->conn,'select * from secondaryEmail where id="' . $row['secondaryEmailId'] . '" and TickleID="' . $row['TickleID'] . '"'));
                  
                    if($secid){
                        $rs_user['FromEmail'] = $secid['FromEmail'];
                        $rs_user['EmailID'] = $secid['EmailID'];
                        $rs_user['DMUse'] = @intval($secid['DMUse']);
                        $rs_user['DMSmtp'] = @trim($secid['DMSmtp']);
                        $rs_user['DMPwd'] = @trim($secid['DMPwd']);
                        $port = @intval($secid['DMPort']);
                        $rs_user['DMPort'] = (($port > 0) ? $port : 25);
                        $rs_user['DMUser'] = @trim($secid['DMUser']);
                        $rs_user['DMSecure'] = @trim($secid['DMSecure']);
                        $rs_user['FirstName'] = @trim($secid['nickname']);  //add name
                        $rs_user['LastName'] = '';
                        $rs_user["signature"] = $secid['signature'];        // add sign
                        if ($secid['use_authtoken'] == '1') {
                            $secAuth = true;
                        }
                    }
                }
            }

        }

        //multiple emails 26-jan-2016
        //Starting of Code -- Date : 06 Jan 2015 -- Purpose : To remove custom subject duplicasy error
        if (isset($CustomSubject)) {
            unset($CustomSubject);
        }
        if (isset($Uattachments)) {
            unset($Uattachments);
        }
        //End of upper code

        $GetCustomSubject = GetCustomSubject($TaskID);
        if ($GetCustomSubject && $GetCustomSubject != "") {
            $CustomSubject = $GetCustomSubject;
        }

        if(!empty($rs_mail['CustomSubject'])) {
            $CustomSubject = $rs_mail['CustomSubject'];
        }

        //    if(isset($CustomSubject)){
        //    unset($CustomSubject);
        //    }
        //    $GetCustomSubjectQuery =mysqli_query($db->conn,"select * from  tickle_custom_subject where TickleTrainID='".$rs_mail['TickleTitleID']."'") or die(mysqli_error($db->conn). __LINE__);
        //    if(mysqli_num_rows($GetCustomSubjectQuery) > 0){
        //        $GetCustomSubjectRow = mysqli_fetch_assoc($GetCustomSubjectQuery);
        //        if($GetCustomSubjectRow['custom_subject'] != ""){
        //            $CustomSubject = $GetCustomSubjectRow['custom_subject'];
        //        }
        //    }

        $FromFirstName = $rs_user['FirstName'];
        $FromLastName = $rs_user['LastName'];
        $UserName = $rs_user['UserName'];
        $UserSign = @trim(urldecode($rs_user["signature"]));

        $additionatextmessage = '';
        if ($rs_user['Plan'] == 1) {
            $additionatextmessage = "<br/><div style=''> <a href='https://tickletrain.com/'>TickleTrain</a> - your free email assistant.</div>";
        } elseif ($rs_user['Plan'] != "1" && $rs_user['blueplanbarning'] == 1) {
            $additionatextmessage = "<br/><div style='color:green;'>-- Email Follow-up Made Easy! --<br/>&nbsp;&nbsp;&nbsp;&nbsp;it's free at <a href='https://tickletrain.com/'>TickleTrain</a></div>";
        }


        debug($UserName . " - " . $rs_user['Password']);

        $tickle_reply = "reply-" . $TaskID . "+" . $UserName . "@tickletrain.com";

        $CCMe = $rs_tickle['CCMe'];

        $FromAddress = $rs_mail['fromaddress'];

        //multiple emails 26-jan-2016
        // if ($rs_user['email_addon'] != '' && ($status['status'] == 'Active' || $status['status'] == 'Pending') && !empty($secid)) {
        //     $FromAddress = $secid['FromEmail'];
        // }
        //multiple emails 26-jan-2016
        //debug('readheader start');
        //$MailHeader = ReadHeader($rs_mail['MailHeader']);
        //debug('readheader end');
        //$rs_mail['ccaddress']=$rs_mail['ccaddress'].",".$rs_mail['fromaddress'];
        $Uccaddress = array();
        if ($rs_mail['ccaddress'] != '') {
            $Uccaddress = explode(",", $rs_mail['ccaddress']);
        }

        $USubject = $rs_mail['Subject'];
        $UMessage = $rs_mail['Message'];
        $UMessageHtml = $rs_mail['MessageHtml'];
        //Content Taken from Raw
        //    $UMessageRaw = $rs_mail['MessageRaw'];
        //    debug('body_decode start');
        //    if ($UMessageRaw != "") {
        //        $MailMessage = body_decode($UMessageRaw, @trim($MailHeader['Content-Transfer-Encoding']));
        //        if ($MailMessage['TEXT'] != "")
        //            $UMessage = $MailMessage['TEXT'];
        //
        //        if ($MailMessage['HTML'] != "")
        //            $UMessageHtml = stripslashes($MailMessage['HTML']);
        //    }
        //    debug('body_decode end');
        $Uattachments = '';
        $Uattachments = preg_split("/,/", @trim($rs_mail['attachments']), -1, PREG_SPLIT_NO_EMPTY);
        $TickleContact = $rs_tickle['TickleContact'];
        $TickleMailSubject = "RE: " . $USubject; //$rs_tickle['TickleMailSubject'];
        $TickleMailContent = $rs_tickle['TickleMailContent'];
        $EmailPriority = $rs_tickle['EmailPriority'];

        $sql_files =mysqli_query($db->conn,"select * from files where FileContext='tickle' and FileParentID='$TickleTrainID'");
        $TAttach = array();
        while ($rs_files = mysqli_fetch_array($sql_files)) {
            $fname = @trim($rs_files['FileName']);
            if ($fname != "" && file_exists(FULL_UPLOAD_FOLDER . $fname)) {
                $TAttach[] = FULL_UPLOAD_FOLDER . $fname;
            }
        }
        mysqli_free_result($sql_files);

        $TReceiptConfirm = $rs_tickle['TReceiptConfirm'];
        $AttachOriginalMessage = $rs_tickle['AttachOriginalMessage'];
        $CTickleName = $rs_tickle['CTickleName'];
        $NoWeekend = @trim($rs_tickle['NoWeekend']);
        $TApprove = @trim($rs_tickle['TApprove']);
        $DailyDays =  $rs_tickle['DailyDays'];

        if ($FollowTickleTrainID > 0) {
            $sql_FMail =mysqli_query($db->conn,"select * from ticklefollow where TickleTrainID='$TickleTrainID' and TickleID='$TickleID' and FollowTickleTrainID='$FollowTickleTrainID'");
            $rs_FMail = mysqli_fetch_array($sql_FMail);
                        mysqli_free_result($sql_FMail);

            $NoWeekend = @trim($rs_FMail['NoWeekend']);
            $TApprove = @trim($rs_FMail['TApprove']);
            $DailyDays =  $rs_FMail['DailyDaysFollow'];

            $TickleMailContent = $rs_FMail['TickleMailFollowContent'];
            $EmailPriority = $rs_FMail['EmailPriorityFollow'];
            $CCMeFollow = $rs_FMail['CCMeFollow'];
            $AttachOriginalMessage = $rs_FMail['AttachMessageFollow'];
            $sql_files =mysqli_query($db->conn,"select * from files where FileContext='ticklefollow' and FileParentID='" . $FollowTickleTrainID . "'");
            $TAttach = array();
            while ($rs_files = mysqli_fetch_array($sql_files)) {
                $fname = @trim($rs_files['FileName']);
                if ($fname != "" && file_exists(FULL_UPLOAD_FOLDER . $fname)) {
                    $TAttach[] = FULL_UPLOAD_FOLDER . $fname;
                }
            }
            mysqli_free_result($sql_files);
        }
        debug('Found ' . count($TAttach) . ' attachments');

  
        if ($NoWeekend == 'Y' && ($dofweek == 0 || $dofweek == 6)) { 
            update_followups_time($MailID,$TaskID,$row['FollowTickleTrainID']);
            create_logs($logData,'Tickle not send in weekend');             
            continue;
        }

        if ($TApprove == 'Y' && $row['Approve'] != 'Y') {
            update_followups_time($MailID,$TaskID,$row['FollowTickleTrainID']);
                create_logs($logData,'Task approve required. Continue');       
           continue;
        }
        if ($row['Pause'] != 'N') {
            update_followups_time($MailID,$TaskID,$row['FollowTickleTrainID']);
            create_logs($logData,'Task paused. Continue');
            continue;
        }

        if ($EmailPriority <= 0) {
            $EmailPriority = 3;
        }
        $EmailPriority = $Priority[$EmailPriority];

        $Cuserid = array();
        //BCC mail heading content
        $tot_tickle =mysqli_query($db->conn,"select count(*) as counts from task where TickleID='$TickleID' and MailID='$MailID'");
        $tot_arr = mysqli_fetch_array($tot_tickle);
        mysqli_free_result($tot_tickle);

        $rem_tickle =mysqli_query($db->conn,"select count(*) as counts from task where TickleID='$TickleID' and MailID='$MailID' and Status='Y'");
        $rem_arr = mysqli_fetch_array($rem_tickle);
        mysqli_free_result($rem_tickle);

        $of_tickle = ($tot_arr['counts'] - $rem_arr['counts']) + 1;

        $cmails = array();
        $sql_cuser =mysqli_query($db->conn,"select * from contact_list where TickleID='$TickleID' and CategoryID='$TickleContact' ");
        while ($rs_cuser = mysqli_fetch_array($sql_cuser)) {
            $cmails[strtolower(trim($rs_cuser['EmailID']))] = $rs_cuser;
        }
        mysqli_free_result($sql_cuser);
        /* $contact_mail=array();
          foreach ($Utoaddress as $KToadd => $vToadd) {
          if (isValidEmail(trim($vToadd)) && isset($cmails[trim($vToadd)])){
          $rs_cuser = $cmails[trim($vToadd)];
          $contact_mail[] = $rs_cuser['FirstName'] . " " . $rs_cuser['LastName'] . " [" . $vToadd . "]";
          }
          }
          debug('Found "'.implode(",", $contact_mail).'" contacts');
          $to_addr = implode(",", $contact_mail);
         */

        $protect = protect($TickleID . "-" . $TaskID);
        $TDeleteLink = "https://" . SERVER_NAME . Url_Create("unsubscribe", "act=" . rawurlencode($protect));
        $TDashboardLink = "https://" . SERVER_NAME . Url_Create("home", "act=" . rawurlencode($protect));

        $TimeZone = trim($rs_user['TimeZone']);
        date_default_timezone_set(gettimezone($TimeZone));

        $toheadercontact = "";
        if (isset($contact_list[$ContactID])) {
            $toaddress = $contact_list[$ContactID];
            $vx = strtolower($toaddress['EmailID']);
            $toaddressfirstname = $toaddress['FirstName'];
            $toaddresslastname = $toaddress['LastName'];
            if(!empty($toaddressfirstname) ||  !empty($toaddressfirstname) ){
                $toheadercontact = $toaddressfirstname . " " . $toaddresslastname . " <" . $vx . ">";
            }else{
                $toheadercontact = $vx;
            }
        } else {
            $toheadercontact = $rs_mail['toaddress'];
        }


        $emailid_for_extension =  (isset($contact_list[$ContactID])) ? strtolower($toaddress['EmailID']) : $rs_mail['toaddress'];

        // debug("toaddress: " . $ContactID);
        $Uattachments = array_filter($Uattachments);
        $Subject = $TickleMailSubject;

        if (isset($additionatextmessage) && $additionatextmessage != "") {
            $TickleMailContent = $TickleMailContent . $additionatextmessage;
        }

        if($mail_type != 'text'){
            $TickleMailContent = preg_replace('/\s+,/', ",", $TickleMailContent);
        }

        $TextMsg = strip_tags(str_ireplace(array("<br />", "<br>"), array("\n", "\n"), $TickleMailContent));
        $HTMLContent = $TickleMailContent . "<input type='hidden' name='emailid' value='$emailid_for_extension' id='chrome_extension'>";
        //$mail->AddReplyTo(trim($tickle_reply), "$FromFirstName $FromLastName");
        //$mail->AddReplyTo(trim($tickle_reply), "$FromFirstName $FromLastName");
        ///smtp password decrypt
        if (!empty($rs_user['DMPwd'])) {
            $rs_user['DMPwd'] = @trim(decryptIt($rs_user['DMPwd']));
            debug('pwd');
        }
    
        $mail = new PHPMailer(false);
       // echo $toaddress['EmailID'] .' ,'.  $rs_user['EmailID'] ;

        $reply_to = $rs_user['EmailID'];
        $FromEmailid = (!empty($rs_user['FromEmail'])) ? $rs_user['FromEmail'] :  $reply_to ;

        if( (!empty($rs_user['DMUser']) && !empty($rs_user['DMPwd'])) && ($rs_tickle['reminder_task'] != 'Y')) {

            $mail->IsSMTP();
            $mail->Mailer = "smtp";    
            $mail->Host = trim($rs_user['DMSmtp']);  //'secureus186.sgcpanel.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = $rs_user['DMAuth'];                               // Enable SMTP authentication
            $mail->Username = $rs_user['DMUser'];                 // SMTP username
            $mail->Password = trim($rs_user['DMPwd']);                           // SMTP password
            $mail->SMTPSecure = trim($rs_user['DMSecure']);    
            $mail->Port = intval($rs_user['DMPort']);
            $mail->Timeout = 60;
           // $mail->SMTPDebug = 2;
            $mail->setFrom($FromEmailid , trim($FromFirstName . ' ' . $FromLastName));
            $mail->Sender = $FromEmailid;
            $mail->AddReplyTo($FromEmailid);

        }else{
            $mail->IsSMTP();    // tell the class to use SMTP
            $mail->Mailer = "smtp";
            $mail->Host = "mail.tickletrain.com"; // SMTP server
            $mail->Port = '25'; // set the SMTP server port
            $mail->SMTPKeepAlive = false;                  // SMTP connection will not close after each email sent     
            $mail->Username = "ticklein@tickletrain.com";     // SMTP server username
            $mail->Password = 'change88q1w2e3r4';     // SMTP server password
            $mail->SMTPAuth = true;                  // enable SMTP authentication        
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;                        // Enable TLS encryption, `ssl` also accepted
           // $mail->SMTPDebug = 2;
            if(($rs_tickle['reminder_task'] == 'Y')) {
                $FromEmailid_name = (!empty($rs_tickle['reminder_task_name'])) ? utf8_decode($rs_tickle['reminder_task_name']) :  'YOUR TASK' ;
                $mail->setFrom('ticklein@tickletrain.com' , trim($FromEmailid_name));
            }else{
                $mail->setFrom($FromEmailid , trim($FromFirstName . ' ' . $FromLastName));
            }
          
            $mail->Sender = 'ticklein@tickletrain.com';

        }
  
        $Cc="";
        $AttMessageHeader = "<div style='border:none;border-top:solid #B5C4DF 1.0pt;padding:3.0pt 0in 0in 0in'><b>From:</b> $FromFirstName $FromLastName [" . $FromEmailid . "] \n <b>Sent:</b> " . $rs_mail['Date'] . " \n <b>To:</b>" . htmlspecialchars($toheadercontact);
           
        $AttMessageHeader.="\n <b>Subject:</b> " . $rs_mail['Subject'] . "</div>";
            $hdrTo = "\n <b>To:</b><span class='org-subject'>" . htmlspecialchars($toheadercontact)."</span>";

            if ($rs_mail['ccaddress'] != "") {
                $Cc="\n  <b>Cc: </b><span class='org-subject'>" . htmlspecialchars($rs_mail['ccaddress'])."</span>";
            }

            $AttMessage = " $AttMessageHeader \n" . $UMessage;

            $AttachOriginalMessage = ($rs_tickle['reminder_task'] == 'Y' ) ? 'A' : $AttachOriginalMessage;

            if ($AttachOriginalMessage != "N") {
                $Subject = $USubject; //$Subject;//." : ".$USubject;
                $TextMsg = $TextMsg . "\n\n" . str_replace("&nbsp;", " ", str_replace("\n", "\n> ", strip_tags($AttMessage)));
                if (trim($UMessageHtml) == "") {
                    $UMessageHtml = nl2br($UMessage);
                }
                $HTMLContent = str_replace("<p>", '<p style="padding-bottom:10px;">', $HTMLContent);
                //$HTMLContent=$HTMLContent."<br /><br /><blockquote type='cite'><div style='padding-left:10px;border-left:2px solid #0000FF;'>".nl2br($AttMessageHeader)."<br />".$UMessageHtml."</div></blockquote>";
                $HTMLContent = $HTMLContent . "<br /><br /><blockquote type='cite'>" . nl2br($AttMessageHeader) . "<br />" . $UMessageHtml . "</blockquote>";
            }

            if ($AttachOriginalMessage == 'A' && count($Uattachments)) {
                debug("AttachOriginalMessage: " . $AttachOriginalMessage . ", mail attachments: " . count($Uattachments));
                $basepath = preg_replace("/\.txt$/i", "/", $rs_mail['RawPath']);
                for ($at = 0; $at < count($Uattachments); $at++) {
                    if (file_exists($basepath . trim($Uattachments[$at]))) {
                        $TAttach[] = $basepath . trim($Uattachments[$at]);
                    }
                }
            }

            if (isset($CustomSubject) && $CustomSubject != "") {
                $mail->Subject = $CustomSubject;
            }else {
                $mail->Subject = "RE: " . $USubject;
            }
        
            $mail->WordWrap = 80; // set word wrap
            $mail->Priority = $EmailPriority;
            if ($TReceiptConfirm == 'Y') {
                $mail->ConfirmReadingTo = $FromEmailid;
                //$mailCc->ConfirmReadingTo = $FromEmailid;
                debug("Added ConfirmReadingTo = '$FromEmailid'");
            }
            $mail->isHTML(true); // send as HTML

            /*
              $mailCc->Subject = "RE: " . $USubject;
              $mailCc->WordWrap = 80; // set word wrap
              $mailCc->Priority = $EmailPriority;
              $mailCc->IsHTML(true); // send as HTML
            */

            if (count($TAttach)) {
                for ($f = 0; $f < count($TAttach); $f++) {
                    $mail->AddAttachment($TAttach[$f], utf8_basename($TAttach[$f]));
                    //$mailCc->AddAttachment($TAttach[$f], basename($TAttach[$f]));
                }
            }
            $to_address = "";

        $CCmekey = 1;
        $taskSent = true;
        debug('Sending to addresses start');
        if (is_array($toaddress)) {
            echo "<pre>";
            //$mail[]="";


            $ContinuousExist = false;
            $GetTaskDetailQuery =mysqli_query($db->conn,"select `MailID`,`FollowTickleTrainID`,`TickleTrainID` from `task` WHERE `TaskID`='$TaskID'");
            $GetTaskDetailsRow = mysqli_fetch_assoc($GetTaskDetailQuery);
            if ($GetTaskDetailsRow['FollowTickleTrainID'] == '0') {
                $GetTickleInformationQuery =mysqli_query($db->conn,"select `NoWeekend`,`DailyDays`,`EndAfter` from `tickle` where `TickleTrainID`='" . $GetTaskDetailsRow['TickleTrainID'] . "'");
                $GetTickleInfoRow = mysqli_fetch_assoc($GetTickleInformationQuery);
                if ($GetTickleInfoRow['EndAfter'] == '13') {
                    $ContinuousExist = true;
                }
            } else {
                $GetFollowupInformationQuery =mysqli_query($db->conn,"select `DailyDaysFollow`,`EndAfterFollow` from `ticklefollow` where `FollowTickleTrainID`='" . $GetTaskDetailsRow['FollowTickleTrainID'] . "'");
                $GetFollowupInformationRow = mysqli_fetch_assoc($GetFollowupInformationQuery);
                if ($GetFollowupInformationRow['EndAfterFollow'] == 13) {
                    $ContinuousExist = true;
                }
            }

            if ( (!empty($userToken) && $oauthmain && $row['secondaryEmailId'] == '0')  && $rs_tickle['reminder_task'] != 'Y'  ) {
                try {

                    //Google Auth Token
                    $sql_user =mysqli_query($db->conn,"select tu.* , gat.access_token ,gat.refresh_token from tickleuser tu left join google_auth_tokens gat on gat.userid = tu.TickleID where tu.TickleID='$TickleID' and tu.Status='Y'");
                    $rs_user = mysqli_fetch_assoc($sql_user);
                    $mail_type = $rs_user['mail_type'];
                    

                    //print_r($rs_user); 
                    if(!$accessToken  = update_google_token($rs_user['refresh_token'],$TickleID)){
                        continue;
                    }


                    $email = @trim($rs_user['EmailID']);
                    $token = trim($accessToken);
                    $smtpInitClientRequestEncoded = constructAuthString($email, $token);
                    $config = array('ssl' => 'ssl',
                        'port' => '465',
                        'auth' => 'xoauth',
                        'xoauth_request' => $smtpInitClientRequestEncoded);
                    //Google Auth Token
                    //Zend mailer
                    $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
                    Zend_Mail::setDefaultTransport($transport);
                    $message = new Zend_Mail('UTF-8');
                    //Zend Mailer


                    if (count($TAttach) != 0) {
                        for ($f = 0; $f < count($TAttach); $f++) {
                            $at = new Zend_Mime_Part(file_get_contents($TAttach[$f]));
                            $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                            $at->encoding = Zend_Mime::ENCODING_BASE64;
                            $at->filename = utf8_basename($TAttach[$f]);
                            $message->addAttachment($at);
                        }
                    }

                    $body = $HTMLContent;
                    $xmlBody = new DomDocument();
                    $xmlBody->loadHTML($body);
                    $imgs = $xmlBody->getElementsByTagName('img');
                    $I_data['atts'] = array();
                    foreach ($imgs as $img) {
                        $imgUrlRel = $img->getAttribute('src');
                        if (str_replace('http', '', $imgUrlRel) == $imgUrlRel) {
                            $body = str_replace($imgUrlRel, 'http://client.tickletrain.com' . $imgUrlRel, $body);
                        }
                    }
                    $UserSignGoogle = $UserSign;
                    $xmlBody = new DomDocument();
                    $xmlBody->loadHTML($UserSignGoogle);
                    $imgs = $xmlBody->getElementsByTagName('img');
                    foreach ($imgs as $img) {
                        $imgUrlRel = $img->getAttribute('src');                    
                        if (str_replace('http', '', $imgUrlRel) == $imgUrlRel) {
                            $UserSignGoogle = str_replace($imgUrlRel, 'http://client.tickletrain.com' . $imgUrlRel, $UserSignGoogle);
                        }
                    }

                    $body = RemoveBadChar($body);
                    $bodytxt = $TextMsg;

                    $vx = strtolower($toaddress['EmailID']);
                    $toaddressfirstname = str_replace("'", "", $toaddress['FirstName']);
                    $toaddresslastname = str_replace("'", "", $toaddress['LastName']);
                    $Sent_Email = $toaddressfirstname . " " . $toaddresslastname . " <" . $vx . ">";
                    if (@trim($toaddressfirstname) != "") {
                        $bodytxt = str_replace("[FirstName]", $toaddressfirstname, $bodytxt);
                        $bodytxt = str_replace("[firstname]", $toaddressfirstname, $bodytxt);
                        $body = str_replace("[FirstName]", $toaddressfirstname, $body);
                        $body = str_replace("[firstname]", $toaddressfirstname, $body);
                    }
                    if (@trim($toaddresslastname)) {
                        $bodytxt = str_replace("[LastName]", $toaddresslastname, $bodytxt);
                        $bodytxt = str_replace("[lastname]", $toaddresslastname, $bodytxt);
                        $body = str_replace("[LastName]", $toaddresslastname, $body);
                        $body = str_replace("[lastname]", $toaddresslastname, $body);
                    }
                    //$body = preg_replace("/\[signature\]/i", $UserSign, $body);
                    //$bodytxt = preg_replace('/\[signature\]/i', $UserSign, $bodytxt);
                    
                    $body = str_replace("[signature]", $UserSignGoogle, $body);
                    $bodytxt = str_replace('[signature]', $UserSignGoogle, $bodytxt);
                    
                    $bodytxt = str_replace('[FirstName]', "", $bodytxt);
                    $bodytxt = str_replace('[firstname]', "", $bodytxt);
                    $bodytxt = str_replace('[LastName]', "", $bodytxt);
                    $bodytxt = str_replace('[lastname]', "", $bodytxt);
                    if($mail_type!='text'){
                        $bodytxt = preg_replace('/((&nbsp;)*|\s*)*,((&nbsp;)*|\s*)*/i', ", ", $bodytxt);
                    }    
                    if($mail_type=='text'){
                        $bodytxt = nl2br($bodytxt);
                    }    
                    $body = str_replace('[FirstName]', "", $body);
                    $body = str_replace('[firstname]', "", $body);
                    $body = str_replace('[LastName]', "", $body);
                    $body = str_replace('[lastname]', "", $body);

                    if($rs_user['enable_email_traking'] == 1){
                       $unsubscribe_me = "https://" . SERVER_NAME ."/?action=mail_open&qrt=".rawurlencode($protect);
                       $link_text =  "<img src='".$unsubscribe_me."' width='0' height='0' border='0' style='border:none; width:0px !important; height:0px; overflow:hidden;opacity: 0;'  >";
                       $body .= $link_text;
                    }

                    if($mail_type!='text'){
                        $body = preg_replace('/(\&nbsp\;|\s)*\,(\&nbsp\;|\s)*/im', ", ", $body);                
                    }
                    if($mail_type=='text'){
                        $body = nl2br($body);
                    }
                    if (isset($CustomSubject) && $CustomSubject != "") {
                        $message->setSubject($CustomSubject);
                    } else {
                        $message->setSubject("RE: " . $USubject);
                    }
                    if ($mail->Host != '') {
                        $message->setFrom($FromEmailid, "$FromFirstName $FromLastName");
                    } else {
                        $message->setFrom($FromEmailid, "$FromFirstName $FromLastName");
                        //$mail->From = $UserName.'@tickletrain.com; on behalf of; '.$FromEmailid." <$FromFirstName $FromLastName>";//.SetFrom($FromEmailid, "$FromFirstName $FromLastName");
                        debug("Mailer from: " . $mail->From);
                    }
                    
                    if($enable_alt_bcc=='1' && !empty($alt_email_bcc))
                    {
                        $altEmailValues = explode(',', $alt_email_bcc);
                        foreach($altEmailValues as $altEmailValue){
                            if (isValidEmail($altEmailValue)) {
                                $message->addBcc($altEmailValue);
                            }
                        }
                    }
                    
                    
                    $bodytxt = RemoveBadChar($bodytxt);
                    $message->setBodyText($bodytxt);
                    $message->setBodyHtml($body, IMAGE_BASE_FOLDER);

                    /*
                      $mailCc->AltBody = $bodytxt;
                      $mailCc->CharSet="utf-8";
                      $mailCc->MsgHTML($body, IMAGE_BASE_FOLDER);
                     */

                    if (isValidEmail($vx)) {
                        $to_address = $vx;
                        //echo $v." : ";
                        debug("Mailto: " . $Sent_Email);
                        $message->addTo($vx, trim($toaddressfirstname . " " . $toaddresslastname));
                        //$mail->Sender = $v;
                        //$mail->AddReplyTo(trim($v));
                        if ($rs_mail['ccaddress'] != '') {
                            debug($rs_mail['ccaddress']);
                            $Uccaddress = MsgAddressParse($rs_mail['ccaddress']);
                            for ($jj = 0; $jj < count($Uccaddress); $jj++) {
                                $message->addCc($Uccaddress[$jj]['address'], $Uccaddress[$jj]['display']);
                            }
                        } 
                        if ($count <= 25) {
                            //echo $to_address.'<br>';
                            if (!in_array($TaskID, $sentTasks)) {
                                if (!in_array($TaskID, $send_array)) {    
                                    $sentTasks[] = $TaskID;
                                    $send_array[] = $TaskID;
                                    $ret = $message->Send();       
                                    mysqli_query($db->conn,"update task set SentDate=now() ,ToAddress='" . $to_address . "',Status='S' WHERE TaskID='" . $TaskID . "' and Status='Y'");
                                    $logData['ttrequest']= $TaskID.'=='. implode(',', $sentTasks);
                                    //create_logs($logData,'sent successfully');
                                }
                            }
                        } else {
                               //create_logs($logData,'sending email failed reason up to 25 mails in one cron');

                            continue;
                        }
                    } else {
                           $taskGMDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskGMDate']));
                           echo '66666'.$TaskInitiateDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskInitiateDate']));
						   echo "update task set Pause='Y' , TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $TaskID . "' ";
                           mysqli_query($db->conn,"update task set Pause='Y' , TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $TaskID . "' ");
                          // echo "if";
                           update_followups_time($MailID,$TaskID,$row['FollowTickleTrainID']);
                          
                           $logData['contact_email'] = $vx;
                           create_logs($logData,'receiver mail not valid');
                            continue;
                    }
                } catch (Exception $e) {
                       $taskGMDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskGMDate']));
                       echo '7777'.$TaskInitiateDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskInitiateDate']));
					   echo "update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $TaskID . "' ";
                       mysqli_query($db->conn,"update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $TaskID . "' ");
                       //echo "catch";
                       update_followups_time($MailID,$TaskID,$row['FollowTickleTrainID']);
                       create_logs($logData,'error in google account'.$e->getMessage());
                       continue;
                }
            } else if ($secAuth && $row['secondaryEmailId'] != '0'  && $rs_tickle['reminder_task'] != 'Y' ) {
                //Google Auth Token

                try {
                    
                    try{

                        $client->refreshToken($secid['refresh_token']);
                        $getGoogleToken = $client->getAccessToken();
                        $getGoogleToken02 = json_decode($getGoogleToken, true);
                        mysqli_query($db->conn,"update google_auth_tokens set access_token='" . $getGoogleToken02['access_token'] . "' , expires_in='" . $getGoogleToken02['expires_in'] . "' , created='" . $getGoogleToken02['created'] . "' where userid='" . $row['secondaryEmailId'] . "' ");

                        mysqli_query($db->conn,"update secondaryEmail set authtoken='" . $getGoogleToken02['access_token'] . "' where id='" . $row['secondaryEmailId'] . "'");
                        $accessToken = $getGoogleToken02['access_token'];
                    }catch(Exception $e){
                        continue;
                    }

                    $email = @trim($rs_user['EmailID']);
                    $token = trim($accessToken);
                    $smtpInitClientRequestEncoded = constructAuthString($email, $token);
                    $config = array('ssl' => 'ssl',
                        'port' => '465',
                        'auth' => 'xoauth',
                        'xoauth_request' => $smtpInitClientRequestEncoded);
                    //Google Auth Token
                    //Zend mailer
                    $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
                    Zend_Mail::setDefaultTransport($transport);
                    $message = new Zend_Mail('UTF-8');
                    //Zend Mailer

                    if (count($TAttach) != 0) {
                        for ($f = 0; $f < count($TAttach); $f++) {
                            $at = new Zend_Mime_Part(file_get_contents($TAttach[$f]));
                            $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                            $at->encoding = Zend_Mime::ENCODING_BASE64;
                            $at->filename = utf8_basename($TAttach[$f]);
                            $message->addAttachment($at);
                            debug('in attchment');
                        }
                    }

                    $body = $HTMLContent;
                    $xmlBody = new DomDocument();
                    $xmlBody->loadHTML($body);
                    $imgs = $xmlBody->getElementsByTagName('img');
                    $I_data['atts'] = array();
                    foreach ($imgs as $img) {
                        $imgUrlRel = $img->getAttribute('src');
                        if (str_replace('http', '', $imgUrlRel) == $imgUrlRel) {
                            $body = str_replace($imgUrlRel, 'https://client.tickletrain.com' . $imgUrlRel, $body);
                        }
                    }
                    $UserSignGoogle = $UserSign;
                    $xmlBody = new DomDocument();
                    $xmlBody->loadHTML($UserSignGoogle);
                    $imgs = $xmlBody->getElementsByTagName('img');
                    foreach ($imgs as $img) {
                        $imgUrlRel = $img->getAttribute('src');
                        if (str_replace('http', '', $imgUrlRel) == $imgUrlRel) {
                            $UserSignGoogle = str_replace($imgUrlRel, 'https://client.tickletrain.com' . $imgUrlRel, $UserSignGoogle);
                        }
                    }
                    
                    
                    $body = RemoveBadChar($body);
                    $bodytxt = $TextMsg;

                    $vx = strtolower($toaddress['EmailID']);
                    $toaddressfirstname = str_replace("'", "", $toaddress['FirstName']);
                    $toaddresslastname = str_replace("'", "", $toaddress['LastName']);
                    $Sent_Email = $toaddressfirstname . " " . $toaddresslastname . " [" . $vx . "]";
                    if (@trim($toaddressfirstname) != "") {
                        $bodytxt = str_replace("[FirstName]", $toaddressfirstname, $bodytxt);
                        $bodytxt = str_replace("[firstname]", $toaddressfirstname, $bodytxt);
                        $body = str_replace("[FirstName]", $toaddressfirstname, $body);
                        $body = str_replace("[firstname]", $toaddressfirstname, $body);
                    }
                    if (@trim($toaddresslastname)) {
                        $bodytxt = str_replace("[LastName]", $toaddresslastname, $bodytxt);
                        $bodytxt = str_replace("[lastname]", $toaddresslastname, $bodytxt);
                        $body = str_replace("[LastName]", $toaddresslastname, $body);
                        $body = str_replace("[lastname]", $toaddresslastname, $body);
                    }

                    if($rs_user['enable_email_traking'] == 1){
                        $unsubscribe_me = "https://" . SERVER_NAME ."/?action=mail_open&qrt=".rawurlencode($protect);
                        $link_text =  "<img src='".$unsubscribe_me."' width='0' height='0' border='0'style='border:none; width: 0px !important; height:0px; overflow:hidden;opacity: 0;'  >";
                        $body .= $link_text;
                    }
                    
                    
                    //$body = preg_replace("/\[signature\]/i", $UserSign, $body);
                    //$bodytxt = preg_replace('/\[signature\]/i', $UserSign, $bodytxt);
                    $body = str_replace("[signature]", $UserSignGoogle, $body);
                    $bodytxt = str_replace('[signature]', $UserSignGoogle, $bodytxt);
                    
                    $bodytxt = str_replace('[FirstName]', "", $bodytxt);
                    $bodytxt = str_replace('[firstname]', "", $bodytxt);
                    $bodytxt = str_replace('[LastName]', "", $bodytxt);
                    $bodytxt = str_replace('[lastname]', "", $bodytxt);
                    if($mail_type!='text'){
                        $bodytxt = preg_replace('/((&nbsp;)*|\s*)*,((&nbsp;)*|\s*)*/i', ", ", $bodytxt);
                    }
                    if($mail_type=='text'){
                        $bodytxt = nl2br($bodytxt);
                    }
                    $body = str_replace('[FirstName]', "", $body);
                    $body = str_replace('[firstname]', "", $body);
                    $body = str_replace('[LastName]', "", $body);
                    $body = str_replace('[lastname]', "", $body);
                    if($mail_type!='text'){
                        $body = preg_replace('/(\&nbsp\;|\s)*\,(\&nbsp\;|\s)*/im', ", ", $body);
                    }
                    if($mail_type=='text'){
                        $body = nl2br($body);
                    }    
                    if (isset($CustomSubject) && $CustomSubject != "") {
                        $message->setSubject($CustomSubject);
                    } else {
                        $message->setSubject("RE: " . $USubject);
                    }

                    if ($mail->Host != '') {
                        $message->setFrom($FromEmailid, "$FromFirstName $FromLastName");
                    } else {
                        $message->setFrom($FromEmailid, "$FromFirstName $FromLastName");
                        //$mail->From = $UserName.'@tickletrain.com; on behalf of; '.$FromEmailid." <$FromFirstName $FromLastName>";//.SetFrom($FromEmailid, "$FromFirstName $FromLastName");
                        debug("Mailer from: " . $mail->From);
                    }
                    
                    if($enable_alt_bcc=='1' && !empty($alt_email_bcc))
                    {    
                        $altEmailValues = explode(',', $alt_email_bcc);
                        foreach($altEmailValues as $altEmailValue){
                            if (isValidEmail($altEmailValue)) {
                                $message->addBcc($altEmailValue);
                            }
                        }
                    }
                    
                    
                    $bodytxt = RemoveBadChar($bodytxt);
                    $message->setBodyText($bodytxt);
                    $message->setBodyHtml($body, IMAGE_BASE_FOLDER);

                    /*
                      $mailCc->AltBody = $bodytxt;
                      $mailCc->CharSet="utf-8";
                      $mailCc->MsgHTML($body, IMAGE_BASE_FOLDER);
                     */

                    if (isValidEmail($vx)) {
                        $to_address = $vx;
                        //echo $v." : ";
                        debug("Mailto: " . $Sent_Email);
                        $message->addTo($vx, trim($toaddressfirstname . " " . $toaddresslastname));
                        //$mail->Sender = $v;
                        //$mail->AddReplyTo(trim($v));
                        if ($rs_mail['ccaddress'] != '') {
                            debug($rs_mail['ccaddress']);
                            $Uccaddress = MsgAddressParse($rs_mail['ccaddress']);
                            for ($jj = 0; $jj < count($Uccaddress); $jj++) {
                                $message->addCc($Uccaddress[$jj]['address'], $Uccaddress[$jj]['display']);
                            }
                        }

                        if ($count <= 25) {
                            //echo $to_address.'<br>';
                            if (!in_array($TaskID, $sentTasks)) {
                            if (!in_array($TaskID, $send_array)) {     
                                $sentTasks[] = $TaskID;
                                $send_array[] = $TaskID;
                                $ret = $message->Send();   
                                mysqli_query($db->conn,"update task set SentDate=now() ,ToAddress='" . $to_address . "',Status='S' WHERE TaskID='" . $TaskID . "' and Status='Y'");
                                $logData['ttrequest']= $TaskID.'=='. implode(',', $sentTasks);
                                //create_logs($logData,'sent successfully');
                            }    
                            }
                        } else {
                               //create_logs($logData,'sending email failed reason up to 25 mails in one cron');
                            continue;
                        }

                    } else {
                           $taskGMDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskGMDate']));
                           $TaskInitiateDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskInitiateDate']));
						   echo '*****';
						   echo "update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $TaskID . "' ";
                           mysqli_query($db->conn,"update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $TaskID . "' ");
                           update_followups_time($MailID,$TaskID,$row['FollowTickleTrainID']);
                           create_logs($logData,'receiver mail not valid');
                           continue;
                    }
                } catch (Exception $e) {
                       $taskGMDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskGMDate']));
                      echo '9999'. $TaskInitiateDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskInitiateDate']));
					  echo "update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $TaskID . "' ";
                       mysqli_query($db->conn,"update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $TaskID . "' ");
                       update_followups_time($MailID,$TaskID,$row['FollowTickleTrainID']);
                       create_logs($logData,'error in google account'.$e->getMessage());
                       continue;
                }
            } else {

                //echo"here";
                $body = $HTMLContent;
                $body = RemoveBadChar($body);
                $bodytxt = $TextMsg;

                $vx = strtolower($toaddress['EmailID']);
                $toaddressfirstname = str_replace("'", "", $toaddress['FirstName']);
                $toaddresslastname = str_replace("'", "", $toaddress['LastName']);
                

                if($rs_tickle['reminder_task'] == 'Y' ){

                    $EmailID = $rs_user['EmailID'];
                    $EmailID = (!empty($rs_user['FromEmail'])) ? $rs_user['FromEmail'] :  $EmailID ;
                    if(!empty($toaddressfirstname) || !empty($toaddresslastname)   ){
                        $full_name = $toaddressfirstname.' '.$toaddresslastname;
                        $mail->Subject = $mail->Subject . '  ['.trim($full_name).']'; 
                    }else{
                        $mail->Subject = $mail->Subject . '  ['.$vx.']'; 
                    }
                    $mail->Subject =  str_replace("RE:", "", $mail->Subject);
                }


                $Sent_Email = $toaddressfirstname . " " . $toaddresslastname . " [" . $vx . "]";
                if (@trim($toaddressfirstname) != "") {
                    $bodytxt = str_replace("[FirstName]", $toaddressfirstname, $bodytxt);
                    $bodytxt = str_replace("[firstname]", $toaddressfirstname, $bodytxt);
                    $body = str_replace("[FirstName]", $toaddressfirstname, $body);
                    $body = str_replace("[firstname]", $toaddressfirstname, $body);
                }
                if (@trim($toaddresslastname)) {
                    $bodytxt = str_replace("[LastName]", $toaddresslastname, $bodytxt);
                    $bodytxt = str_replace("[lastname]", $toaddresslastname, $bodytxt);
                    $body = str_replace("[LastName]", $toaddresslastname, $body);
                    $body = str_replace("[lastname]", $toaddresslastname, $body);
                }
                $body = str_replace("[signature]", $UserSign, $body);
                $bodytxt = str_replace('[signature]', $UserSign, $bodytxt);
                $bodytxt = str_replace('[FirstName]', "", $bodytxt);
                $bodytxt = str_replace('[firstname]', "", $bodytxt);
                $bodytxt = str_replace('[LastName]', "", $bodytxt);
                $bodytxt = str_replace('[lastname]', "", $bodytxt);

                          // add link to body
                if($rs_user['enable_email_traking'] == 1){
                        $unsubscribe_me = "https://" . SERVER_NAME ."/?action=mail_open&qrt=".rawurlencode($protect);
                        $link_text =  "<img src='".$unsubscribe_me."' width='0' height='0' border='0' style='border:none; width: 0px !important; height:0px; overflow:hidden;opacity: 0;' >";
                        $body .= $link_text;
                }

                if($mail_type!='text'){
                    $bodytxt = preg_replace('/((&nbsp;)*|\s*)*,((&nbsp;)*|\s*)*/i', ", ", $bodytxt);
                }
                if($mail_type=='text'){
                    $bodytxt = nl2br($bodytxt);
                }
                $body = str_replace('[FirstName]', "", $body);
                $body = str_replace('[firstname]', "", $body);
                $body = str_replace('[LastName]', "", $body);
                $body = str_replace('[lastname]', "", $body);
                $body = str_replace('"/upload-files/','"https://client.tickletrain.com/upload-files/',$body);
                if($mail_type!='text'){
                    $body = preg_replace('/(\&nbsp\;|\s)*\,(\&nbsp\;|\s)*/im', ", ", $body);
                }
                if($mail_type=='text'){
                    $body = nl2br($body);
                }
                $bodytxt = RemoveBadChar($bodytxt);

                if($rs_tickle['reminder_task'] == 'Y' ){

                    $Stage = ($ContinuousExist) ? $of_tickle . " of <b>∞<b>" : $of_tickle . " of " . $tot_arr['counts'];

                    $protect2 = protect($TickleID . "-" . $MailID);

                    $TaskComplete = "https://".SERVER_NAME.Url_Create("test","cptsk=".rawurlencode($protect)."&els=".rawurlencode(protect('yes'))); //Link for complete the task .. for owner


                    // add comments lik
                    $add_comment = "https://".SERVER_NAME.Url_Create("addcomments","cptsk=".rawurlencode($protect2)."&els=".rawurlencode(protect('yes')));

                    if($vx != $rs_user['EmailID']){ 
                        $TaskComplete = "https://" . SERVER_NAME . Url_Create("test", "cptsk=" . rawurlencode($protect)."&stg=".rawurlencode(protect($Stage))."&els=".rawurlencode(protect('no')));  // if user

                        $add_comment = "https://".SERVER_NAME.Url_Create("addcomments","cptsk=".rawurlencode($protect2)."&els=".rawurlencode(protect('no')));
                    }

                    $body =  str_replace("[message_body]", $body, $reminder_task_templete);
                    $body =  preg_replace("/\[To\]/i", htmlspecialchars($toheadercontact), $body);
                    $body =  preg_replace("/\[Subject\]/i", $mail->Subject, $body);
                    $body =  preg_replace("/\[title\]/i", $mail->Subject, $body);
                    $body =  preg_replace("/\[Date\]/i", date("D, j M Y H:i:s O(T)"), $body);

                    $TLink = "https://" . SERVER_NAME . Url_Create("test", "tskup=" . rawurlencode($protect)); //Link for Update the Date of task
                    $body =  preg_replace("/\[TaskComplete\]/i",'<a href="'.$TaskComplete.'" > Mark Complete</a>', $body);
                    $body =  preg_replace("/\[AddComment\]/i",'<a href="'.$add_comment.'" >Add Comments</a>', $body);


                    // Add comments to Emials template .. 
                    $comment_html='';
                    $comments  =  get_comments($MailID);
					
                    if(count($comments)){
                        $comment_html='<ul class="commentslist">';
                        foreach($comments as $comment):
						//echo 'ppppp'.html_entity_decode($comment['comment']);
					//echo 'sssss'.htmlspecialchars_decode($comment['comment']);
                            $comment_html.='<li>';
                            $comment_html.='<h3>'.get_comment_user($comment).'<span>'. getlocaltime($comment['created_at'],$TimeZone,'M d, Y').' at '. getlocaltime($comment['created_at'],$TimeZone,'h:i A') .'</span></h3>';
                            $comment_html.='<p>'.$comment['comment'].'</p>';
                            $comment_html.='</li>';
                         endforeach;
                        $comment_html.='</ul>';
                    }
                    $body =  str_replace("[CommentsList]",$comment_html, $body);
                    //// 


                    if(in_array($DailyDays,[1,2,3])){
                        $add_class_to = $DailyDays.'D';
                    }elseif(in_array($DailyDays,[7,14])){
                        $add_class_to = (array_search($DailyDays,[7,14])+1).'W';
                    }elseif(in_array($DailyDays,[28,29,30,31])){
                        $add_class_to = '1M';
                    }else{
                        $add_class_to = '';
                    }

                    $options = "";
                    foreach ($timing_array as $class => $value) {
                        if($add_class_to  == $value[0]){
                            $class = 'activeSpan';
                        }
                       $options.= '<span class="'.$class.'" title="'.$value[1].'" ><a href="'.$TLink.'&val='.rawurlencode(protect($value[0])).'&w='.rawurlencode(protect($NoWeekend)).'">'.$value[0].'</a></span>'; 
                    }
                    $body = preg_replace("/\[Options\]/i", $options, $body);
                    $body = preg_replace("/\[Stage\]/i", $Stage, $body);
                }

                $mail->AltBody = $bodytxt;
                $mail->CharSet = "utf-8";
                $mail->MsgHTML($body, IMAGE_BASE_FOLDER);
                /*
                  $mailCc->AltBody = $bodytxt;
                  $mailCc->CharSet="utf-8";
                  $mailCc->MsgHTML($body, IMAGE_BASE_FOLDER);
                 */
                
                if($enable_alt_bcc=='1' && !empty($alt_email_bcc))
                {
                    $altEmailValues = explode(',', $alt_email_bcc);
                    foreach($altEmailValues as $altEmailValue){
                        if (isValidEmail($altEmailValue)) {
                            $mail->addBcc($altEmailValue);
                        }
                    }
                }

                //echo $vx;
                if (isValidEmail($vx)) {
                    $to_address = $vx;
                    //echo $v." : ";
                   // $mail->AddAddress($vx, trim($toaddressfirstname . " " . $toaddresslastname));
                    $mail->AddAddress($vx);
                    //$mail->Sender = $v;
                    //$mail->AddReplyTo(trim($v));
                    if ($rs_mail['ccaddress'] != '') {
                        $Uccaddress = MsgAddressParse($rs_mail['ccaddress']);
                        for ($jj = 0; $jj < count($Uccaddress); $jj++) {
                            $mail->AddCC($Uccaddress[$jj]['address'], $Uccaddress[$jj]['display']);
                        }
                    }

                    if ($count <= 25) {
                        ob_start();
                        if (!in_array($TaskID, $send_array)) {     // duplicate message send 08/apr/2016  
                            if (!in_array($TaskID, $sentTasks)) {
                                $sentTasks[] = $TaskID;
                                $ret = $mail->Send();

                                if ($ret) {
                                    $send_array[] = $TaskID;       
                                    $logData['ttrequest']= $TaskID.'=='. implode(',', $sentTasks);
                                    //create_logs($logData,'sent successfully');
                                    mysqli_query($db->conn,"update task set SentDate=now() ,ToAddress='" . $to_address . "',Status='S' WHERE TaskID='" . $TaskID . "' and Status='Y'");
                                }else{
                                    echo $mail->ErrorInfo;
                                    mysqli_query($db->conn,"update task set Pause='Y' WHERE TaskID='" . $TaskID . "' and Status='Y'");
                                    create_logs($logData,'SMTP Error: Could not connect to SMTP host.');
                                    continue;
                                }
                            }
                        }
                        ob_flush();
                    } else {
                        //create_logs($logData,'sending email failed reason up to 25 mails in one cron');
                        continue;
                    }
                } else {
                    $taskGMDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskGMDate']));
                   echo '####'. $TaskInitiateDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskInitiateDate']));
				   echo "update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $TaskID . "' ";
                    mysqli_query($db->conn,"update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $TaskID . "' ");
                    update_followups_time($MailID,$TaskID,$row['FollowTickleTrainID']);
                    create_logs($logData,'receiver mail not valid');
                    continue;
                }

                if (!$ret) {
                    if (empty($userToken)) {

                        $taskSent = false;
                        debug("error on sending email - " . $mail->ErrorInfo);
                        $error01 = "error on sending email - " . $mail->ErrorInfo;
                       // create_logs($logData,$error01);
                        SendMail($FromEmailid, $TtSmtpReplyMail, "SMTP Error", "There was a problem sending out your Tickle called \"" . $TickleMailSubject . "\" to " . trim($toaddressfirstname . " " . $toaddresslastname) . " [$vx]. TickleTrain was not able to connect to your SMTP server, we recommend you log into your TickleTrain account and check your SMTP settings under the \"Settings\" tab.<br><br>Note: Verify your settings are correct by using \"Send Test Email\".<br>If this fails, please contact your email provider to help resolve the issue. You can switch back to your \"tickletrain\" email address in the meantime.
                            <br/><br/>Server Error: " . $mail->ErrorInfo . "
                            <br><br>Thank you,<br>TickleTrain<br>Send It. And Forget it.SM<br><a href=\"www.tickletrain.com\">www.tickletrain.com</a>");
                    }
                }
            }

        }//if
        //debug('Sending to addresses end');

        if (($CCMe == "Y" || $CCMeFollow == "Y") && $taskSent) {

            if ($TickleID == '994') {
                debug('me fillow section start robert');
            }

            $bccheadTxt = "<tr><td class='bodycopy bold'>[TO_ADDRESS] </td></tr>";

            if(!empty($Cc)){
                $bccheadTxt.= "<tr><td class='bodycopy bold'>".$Cc."</td></tr>";
            }
            $bccheadTxt.="<tr><td class='bodycopy bold'><b>Subject: </b><span class='org-subject'>" . $USubject ."</span></td></tr>
                        <tr><td class='bodycopy bold'><b>Date: </b><span class='org-subject'>" . date("D, j M Y H:i:s O(T)")."</span></td></tr>";

            if ($ContinuousExist) {
                $bccheadTxt.="<tr><td class='bodycopy bold'><b>Stage: </b><span class='org-subject'>".$of_tickle." of <b>∞<b></td></span></tr>";
            } else {
                $bccheadTxt.= "<tr><td class='bodycopy bold'><b>Stage: </b><span class='org-subject'>" .$of_tickle." of " .$tot_arr['counts']."</span></td></tr>";
            }
            $link_test ="Your Tickle has been sent! To manage remaining Tickles please <a href='".$TDashboardLink."' target='_blank'>Click Here</a>";
            
            $bccMailResponse = 'BCC mail sent successfully';

            if (!in_array($TaskID, $bccCheck)) {

                $template =  $email_templete;
                
                $mailBcc = new PHPMailer(false);
                ///added this code 10 Nov som - start
                $mailBcc->isSMTP();// Set mailer to use SMTP
                $mailBcc->Mailer = "smtp";
               // $mailBcc->SMTPDebug = 2;
                $mailBcc->Host = $TtSmtpHost;  // Specify main and backup SMTP servers
                $mailBcc->SMTPAuth = $TtSmtpAuth;// Enable SMTP authentication
                $mailBcc->Username = $TtSmtpUsername;// SMTP username
                $mailBcc->Password = $TtSmtpPassword;// SMTP password
                $mailBcc->SMTPSecure = $TtSmtpSecure;// Enable TLS encryption, `ssl` also accepted
                $mailBcc->Port = $TtSmtpPort;// TCP port to connect to
                $mailBcc->SMTPAutoTLS = false;
                $mailBcc->isHTML(true); ///added this code 10 Nov som - end
                
                $mailBcc->setFrom($TtSmtpReplyMail, "TickleTrain");
                if (isset($CustomSubject) && $CustomSubject != "") {
                    $mailBcc->Subject = "BCC'd on this tickle : " . $CustomSubject;
                    $title =  "BCC'd on this tickle : " . $CustomSubject;
                } else {
                    $mailBcc->Subject = "BCC'd on this tickle Re : " . $USubject;
                    $title = "BCC'd on this tickle Re : " . $USubject;
                }

                $mailBcc->ConfirmReadingTo = "";

                $template =  preg_replace("/\[TDashboardLink\]/i", $link_test, $template); // replace title

                $template =  preg_replace("/\[title\]/i", trim($title), $template); // replace title
                $bccheadTxt = str_replace("[TO_ADDRESS]", $hdrTo, $bccheadTxt);

                $template =  preg_replace("/\[From\]/i", $bccheadTxt, $template);

                $template =  preg_replace("/\[Reply_message\]/i", $body, $template);


                //$body2 = nl2br(str_replace("[TO_ADDRESS]", $hdrTo, $bccheadHTML)) . $body;
                $mailBcc->CharSet = "utf-8";
                $mailBcc->MsgHTML($template, IMAGE_BASE_FOLDER);

                $mailBcc->AddAddress($FromEmailid);
                $mailBcc->AddReplyTo($TtSmtpReplyMail);
                $mailBcc->Sender = $TtSmtpReplyMail;
                $mailBcc->Send();

                //$logDataJsonBcc = json_encode($logData);
                create_logs($logData,$bccMailResponse);
                $mailBcc->ClearAllRecipients();
                $mailBcc->ClearReplyTos();
            }


            //Alternate Email ID
            //debug('Alt email sending start');
            //if ($enable_alt > 0 && $alt_email != "") {
            //if (isValidEmail(trim($alt_email))) {
            //$mailBcc->AddAddress($alt_email);
            //$mailBcc->ConfirmReadingTo = "";
            //$mailBcc->AddReplyTo($TtSmtpReplyMail); //trim($alt_email));
            //$mailBcc->Sender = $TtSmtpReplyMail;
            //$mailBcc->Send();
            //
            //$mailBcc->ClearAllRecipients();
            //$mailBcc->ClearReplyTos();
            //}
            //}
           // debug('Alt email sending end');
            $CCmekey = 0;
            debug('Me follow end');
        }

        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->ClearAttachments();
        $mail->ClearCCs();

        //ccme
        if ($taskSent) {
           mysqli_query($db->conn,"update task set SentDate=now() ,ToAddress='$to_address',Status='S' WHERE TaskID='$TaskID' and Status='Y'");

            // Code set on 22/1/2014 to add continuous fetaure :

            $GetContiniousTickleID =mysqli_query($db->conn,"select `MailID`,`FollowTickleTrainID`,`TickleTrainID` from `task` WHERE `TaskID`='$TaskID'");
            $GetContinuousTickleRow = mysqli_fetch_assoc($GetContiniousTickleID);
          
            if ($GetContinuousTickleRow['FollowTickleTrainID'] == '0') {
                $GetTickleInfoQuery =mysqli_query($db->conn,"select `NoWeekend`,`DailyDays`,`EndAfter` from `tickle` where `TickleTrainID`='" . $GetContinuousTickleRow['TickleTrainID'] . "'");
                $GetTickleInfoRow = mysqli_fetch_assoc($GetTickleInfoQuery);

                if ($GetTickleInfoRow['EndAfter'] == '13') {
                    $LastTaskWithThisMessageQuery =mysqli_query($db->conn,"select * from `task` where `TickleTrainID`='" . $GetContinuousTickleRow['TickleTrainID'] . "' and
                      `MailID`='" . $GetContinuousTickleRow['MailID'] . "' and `Status`='Y' order by TaskID desc limit 1");
                    $LastTaskWithThisMessageRow = mysqli_fetch_assoc($LastTaskWithThisMessageQuery);
                    $NextTaskInitiateDate = $LastTaskWithThisMessageRow['TaskInitiateDate'];
                    $NextTaskInitiateDate = strtotime($NextTaskInitiateDate);
                    $NextTaskInitiateDate = strtotime("+$GetTickleInfoRow[DailyDays] day", $NextTaskInitiateDate);
                    $NextTaskInitiateDate = date('Y-m-d h:i:s', $NextTaskInitiateDate);
                    // $NextTaskGmDate = $LastTaskWithThisMessageRow['TaskGMDate'];
                    // $NextTaskGmDate = strtotime($NextTaskGmDate);
                    // $NextTaskGmDate = strtotime("+$GetTickleInfoRow[DailyDays] day", $NextTaskGmDate);
                    // $NextTaskGmDate = date('Y-m-d h:i:s', $NextTaskGmDate);

                    if ($GetTickleInfoRow['NoWeekend'] == 'Y') {
                        $DateInitiateTimeStamp = strtotime($NextTaskInitiateDate);
                        $NextTaskInitiateDateDay = date('D', $DateInitiateTimeStamp);
                        if ($NextTaskInitiateDateDay == 'Sat') {
                            $NextTaskInitiateDate = date('Y-m-d H:i:s', strtotime($NextTaskInitiateDate . ' + 2 day'));
                        } elseif ($NextTaskInitiateDateDay == 'Sun') {
                            $NextTaskInitiateDate = date('Y-m-d H:i:s', strtotime($NextTaskInitiateDate . ' + 1 day'));
                        }
                        // $DateTaskGmDateTimeStamp = strtotime($NextTaskGmDate);
                        // $NextTaskGmDateDay = date('D', $DateTaskGmDateTimeStamp);
                        // if ($NextTaskGmDateDay == 'Sat') {
                        //     $NextTaskGmDate = date('Y-m-d H:i:s', strtotime($NextTaskGmDate . ' + 2 day'));
                        // } elseif ($NextTaskGmDateDay == 'Sun') {
                        //     $NextTaskGmDate = date('Y-m-d H:i:s', strtotime($NextTaskGmDate . ' + 1 day'));
                        // }
                    }


                    $NextTaskGmDate = getgmdate($NextTaskInitiateDate, $LastTaskWithThisMessageRow['TimeZone']);
                    $InsertNewTaskQuery =mysqli_query($db->conn,"insert into task (MailID,TickleID,FollowTickleTrainID,TaskCretedDate,TaskInitiateDate,TickleTrainID,Status,SentDate,ToAddress,TaskGMDate,TimeZone,HasFollowup,Approve,Pause,DateID) values ('" . $LastTaskWithThisMessageRow['MailID'] . "','" . $LastTaskWithThisMessageRow['TickleID'] . "','" . $LastTaskWithThisMessageRow['FollowTickleTrainID'] . "','" . $LastTaskWithThisMessageRow['TaskCretedDate'] . "','" . $NextTaskInitiateDate . "','" . $LastTaskWithThisMessageRow['TickleTrainID'] . "','" . $LastTaskWithThisMessageRow['Status'] . "','" . $LastTaskWithThisMessageRow['SentDate'] . "','" . $LastTaskWithThisMessageRow['ToAddress'] . "','" . $NextTaskGmDate . "','" . $LastTaskWithThisMessageRow['TimeZone'] . "','" . $LastTaskWithThisMessageRow['HasFollowup'] . "','" . $LastTaskWithThisMessageRow['Approve'] . "','" . $LastTaskWithThisMessageRow['Pause'] . "','" . $LastTaskWithThisMessageRow['DateID'] . "')");
                }

            } else {

                $GetFollowupInfoQuery =mysqli_query($db->conn,"select `DailyDaysFollow`,`EndAfterFollow` , `NoWeekend` from `ticklefollow` where `FollowTickleTrainID`='" . $GetContinuousTickleRow['FollowTickleTrainID'] . "'");
                $GetFollowupInfoRow = mysqli_fetch_assoc($GetFollowupInfoQuery);

                if ($GetFollowupInfoRow['EndAfterFollow'] == '13') {

                    $LastTaskWithThisMessageQuery =mysqli_query($db->conn,"select * from `task` where `FollowTickleTrainID`='" . $GetContinuousTickleRow['FollowTickleTrainID'] . "' and
                  `MailID`='" . $GetContinuousTickleRow['MailID'] . "' and `Status`='Y' order by TaskID desc limit 1");
                    $LastTaskWithThisMessageRow = mysqli_fetch_assoc($LastTaskWithThisMessageQuery);
                    $NextTaskInitiateDate = $LastTaskWithThisMessageRow['TaskInitiateDate'];
                    $NextTaskInitiateDate = strtotime($NextTaskInitiateDate);
                    $NextTaskInitiateDate = strtotime("+$GetFollowupInfoRow[DailyDaysFollow] day", $NextTaskInitiateDate);
                    $NextTaskInitiateDate = date('Y-m-d h:i:s', $NextTaskInitiateDate);
                  
                    if ($GetFollowupInfoRow['NoWeekend'] == 'Y') {
                        $DateInitiateTimeStamp = strtotime($NextTaskInitiateDate);
                         $NextTaskInitiateDateDay = date('D', $DateInitiateTimeStamp);
                        if ($NextTaskInitiateDateDay == 'Sat') {
                            $NextTaskInitiateDate = date('Y-m-d H:i:s', strtotime($NextTaskInitiateDate . ' + 2 day'));
                        } elseif ($NextTaskInitiateDateDay == 'Sun') {
                            $NextTaskInitiateDate = date('Y-m-d H:i:s', strtotime($NextTaskInitiateDate . ' + 1 day'));
                        }
                        
                    }

                    $NextTaskGmDate = getgmdate($NextTaskInitiateDate, $LastTaskWithThisMessageRow['TimeZone']);

                    $InsertNewTaskQuery =mysqli_query($db->conn,"insert into task (MailID,TickleID,FollowTickleTrainID,TaskCretedDate,TaskInitiateDate,TickleTrainID,Status,SentDate,ToAddress,TaskGMDate,TimeZone,HasFollowup,Approve,Pause,DateID) values ('" . $LastTaskWithThisMessageRow['MailID'] . "','" . $LastTaskWithThisMessageRow['TickleID'] . "','" . $LastTaskWithThisMessageRow['FollowTickleTrainID'] . "','" . $LastTaskWithThisMessageRow['TaskCretedDate'] . "','" . $NextTaskInitiateDate . "','" . $LastTaskWithThisMessageRow['TickleTrainID'] . "','" . $LastTaskWithThisMessageRow['Status'] . "','" . $LastTaskWithThisMessageRow['SentDate'] . "','" . $LastTaskWithThisMessageRow['ToAddress'] . "','" . $NextTaskGmDate . "','" . $LastTaskWithThisMessageRow['TimeZone'] . "','" . $LastTaskWithThisMessageRow['HasFollowup'] . "','" . $LastTaskWithThisMessageRow['Approve'] . "','" . $LastTaskWithThisMessageRow['Pause'] . "','" . $LastTaskWithThisMessageRow['DateID'] . "')");
                }
            }
        // Code set on 22/1/2014 to add continuous fetaure :
        // End of Code for Conitnuos Fetaure
        }
      
        $count = $count + 1;
        debug('Task ' . $TaskID . ' finished');
    } //for loop


    @unlink(__DIR__ . '/ticklelog/cronmail.json');
    echo 'Done';
}

function isValidEmail($email) {
    return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email);
}

file_put_contents(LOGS_FOLDER . "cronmail.tm", time());
debug('Send daily report block finished');


function extract_email_tt($email_string) {    
    $MessageFromAddress = $email_string;    
    $startMailTag = strpos($MessageFromAddress,"<");
    $endMailTag = strpos($MessageFromAddress,">");
    if(!empty($startMailTag) && !empty($endMailTag))
    {    
        $limit = $endMailTag - $startMailTag;        
        $MessageFromAddress = substr($MessageFromAddress,$startMailTag+1,$limit-1);
        $MessageFromAddress = trim($MessageFromAddress);
        return strtolower($MessageFromAddress);
    }  else {
        return strtolower($MessageFromAddress);
    }
}

//function
function debug($msg) {
    //return false;
    //echo $msg."\n";
    //$fname = LOGS_FOLDER . 'cronmail.log';
    //WriteFile($fname, gmdate('d.m.Y H:i:s') . " > " . $msg . "\n", "a");
}


//end
?>
