<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require("includes/class/PHPMailer/src/Exception.php");
require("includes/class/PHPMailer/src/PHPMailer.php");
require("includes/class/PHPMailer/src/SMTP.php");


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

$delLogDate = date("Y-m-d H:i:s");
$fourDayBack = date("d-m-Y", strtotime('-4 days', strtotime($delLogDate)));
if(is_dir(__DIR__ . '/ticklelog/'.$fourDayBack)){
    $delFile = __DIR__ . '/ticklelog/'.$fourDayBack.'/crontrain.json';
    if(file_exists($delFile)){ unlink($delFile);  }
    $delFile = __DIR__ . '/ticklelog/'.$fourDayBack.'/instant_track_reply.json';
    if(file_exists($delFile)){ unlink($delFile);  }
    $delFile = __DIR__ . '/ticklelog/'.$fourDayBack.'/track_reply.json';
    if(file_exists($delFile)){ unlink($delFile);  }
    $delFile = __DIR__ . '/ticklelog/'.$fourDayBack.'/cronmail.json';
    if(file_exists($delFile)){ unlink($delFile);  }
    $delFile = __DIR__ . '/ticklelog/'.$fourDayBack.'/mastercron.json';
    if(file_exists($delFile)){ unlink($delFile);  }    
    rmdir(__DIR__ . '/ticklelog/'.$fourDayBack);
}


//Google Auth and Zend Mailer Files
$client = new Google_Client();
$client->setClientId('799405691032-er3cilvjgrqgtlfreuffllvkp2ouvrjb.apps.googleusercontent.com'); // paste the client id which you get from google API Console
$client->setClientSecret('QYmRweaDw20scMLTidBR8MRB'); // set the client secret


function constructAuthString($email, $accessToken) {
    return base64_encode("user=$email\1auth=Bearer $accessToken\1\1");
}



   $smtpSubject = "SMTP settings not configured properly";
   $smtpContent = 'Unfortunately the SMTP settings you entered are incorrect. TickleTrain will not be able to send your email. Please log into your TickleTrain account and click Settings to make changes. If you continue to have issues, please check with your email provider.';   

   $imapSubject = "Imap settings not configured properly";
   $imapContent = "Unfortunately the IMAP settings you entered are incorrect. TickleTrain will not be able to send your email.   Please log into your TickleTrain account and click Settings to make changes. If you continue to have issues, please check with your email provider.";   

   $googleSubject = "Google Auth settings not configured properly";
   $googleContent = "Unfortunately the Google Authentication has not verified so TickleTrain will not be able to send your email. Please log into your TickleTrain account and click Settings to make changes. If you continue to have issues, please check with your email provider.";


function sendAlertMessage($subject,$content,$email){
    global $TtSmtpHost;
    global $TtSmtpAuth;
    global $TtSmtpUsername;
    global $TtSmtpPassword;
    global $TtSmtpSecure;
    global $TtSmtpPort;
    global $TtSmtpReplyMail;
    
    $HTMLValue = $content;
    $mailreport = new PHPMailer(false); //New instance, with exceptions enabled    
    $mailreport->isSMTP();                                      // Set mailer to use SMTP
    $mailreport->Host = $TtSmtpHost;  // Specify main and backup SMTP servers
    $mailreport->SMTPAuth = $TtSmtpAuth;                               // Enable SMTP authentication
    $mailreport->Username = $TtSmtpUsername;                 // SMTP username
    $mailreport->Password = $TtSmtpPassword;                           // SMTP password
    $mailreport->SMTPSecure = $TtSmtpSecure;                            // Enable TLS encryption, `ssl` also accepted
    $mailreport->Port = $TtSmtpPort;    
    $mailreport->SetFrom($TtSmtpReplyMail, "TickleTrain");                
    $mailreport->Subject = $subject;
    $mailreport->AltBody = $HTMLValue;
    $mailreport->WordWrap = 80; // set word wrap
    $mailreport->Priority = $EmailPriority;
    $mailreport->CharSet = "utf-8";
    $mailreport->MsgHTML($HTMLValue, IMAGE_BASE_FOLDER);
    $mailreport->IsHTML(true); // send as HTML
    //$mailreport->AddReplyTo($TtSmtpReplyMail);
    $mailreport->Sender = $TtSmtpReplyMail;
    //$mailreport->AddAddress("tickletraincron@gmail.com"); //$Value['EMAIL']
    $mailreport->AddAddress($email); //$Value['EMAIL']    
    $mailreport->Send();
    $mailreport->ClearAddresses();
    $mailreport->ClearBCCs();
    $mailreport->ClearReplyTos();
    $mailreport->ClearAllRecipients();
    $mailreport->ClearCCs();
}


$query = "SELECT TickleID,EmailID,DMUse,DMSystem,DMSmtp,DMPort,DMUser,DMPwd,DMSecure,imap_host,imap_userame,imap_passowrd,imap_port,imap_secure,imapnotification,smtpnotification from tickleuser";
$result = mysqli_query($db->conn,$query);
$resultData = array();
while($row = mysqli_fetch_assoc($result))
{
    $row['DMPwd'] = @trim(decryptIt($row['DMPwd']));
    $row['imap_passowrd'] = @trim(decryptIt($row['imap_passowrd']));
    $googleaccess = mysqli_fetch_assoc(mysqli_query($db->conn,'select access_token,refresh_token from google_auth_tokens where userid="' . $row['TickleID'] . '" and refresh_token!="" '));
    if(!empty($googleaccess))
    {    
        $row['access_token'] = $googleaccess['access_token'];
        $row['refresh_token'] = $googleaccess['refresh_token'];
    }else{
        $row['access_token'] = '';
        $row['refresh_token'] = '';
    }    
    $resultData[] = $row;
}

//echo '<pre>';
//print_r($resultData); die();
if(!file_exists(__DIR__ . '/ticklelog/'.date('d-m-Y').'/mastercron.json'))
{
    $todayLogData = array('TickleID'=>"",'EmailID'=>"",'ttresponse'=>"");
    $fp = fopen(__DIR__ . '/ticklelog/'.date('d-m-Y').'/mastercron.json', 'w');
    fwrite($fp, json_encode($todayLogData));
    fclose($fp);
}

$smtpEmailIds = array();
$imapEmailIds = array();
$googleEmailIds = array();

foreach ($resultData as $data)
{    
    echo '<br>'.$data['EmailID'];
    if($data['DMUse']=='1')
    {   
        if (empty($data['refresh_token']) && !empty($data['DMUser']) && !empty($data['DMSmtp']))
        { 
            $mail = new PHPMailer;
            $mail->IsSMTP();                                      // Set mailer to use SMTP
            $mail->Host = $data['DMSmtp'];                 // Specify main and backup server
            $mail->Port = $data['DMPort'];                                    // Set the SMTP port
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $data['DMUser'];                // SMTP username
            $mail->Password = $data['DMPwd'];                  // SMTP password
            $mail->SMTPSecure = $data['DMSecure'];                            // Enable encryption, 'ssl' also accepted
            $mail->From = $data['EmailID'];
            //$mail->FromName = 'Your From name';
            $mail->AddAddress('tickletraincron@gmail.com', '');  // Add a recipient//shine@123
            //$mail->AddAddress('ellen@example.com');               // Name is optional
            $mail->IsHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Test SMTP Connection';
            $mail->Body    = 'Test SMTP Connection';
            $mail->AltBody = 'Test SMTP Connection';
            if(!$mail->Send()) {                           
                mysqli_query($db->conn,"update tickleuser set DMSmtpOff='1' where TickleID='" . $data['TickleID'] . "'");            
                $logData = array('TickleID'=>$data['TickleID'],'EmailID'=>$data['EmailID'],'ttresponse'=>"SMTP Error: Could not connect to SMTP host.");
                echo " == SMTP Error:"; 
                $smtpEmailIds[] = $data['EmailID'];
                if(date("Y-m-d") > date("Y-m-d", strtotime("+7 days", strtotime($data['smtpnotification']))) || $data['smtpnotification']=='0000-00-00'){
                    mysqli_query($db->conn,"update tickleuser set smtpnotification='".date('Y-m-d')."' where TickleID='" . $data['TickleID'] . "'");
                    sendAlertMessage($smtpSubject,$smtpContent,$data['EmailID']);
                }    
                WriteFile(__DIR__ . '/ticklelog/'.date('d-m-Y').'/mastercron.json', ','.json_encode($logData), "a");            
            }
            else{            
                mysqli_query($db->conn,"update tickleuser set DMSmtpOff='0' where TickleID='" . $data['TickleID'] . "'");
            }
            $mail->ClearAllRecipients();
            $mail->ClearReplyTos();   
        }else{
            mysqli_query($db->conn,"update tickleuser set DMSmtpOff='0' where TickleID='" . $data['TickleID'] . "'");
        }    
    } 
    
    
    if (($data['imap_host'] == '' || $data['imap_userame'] == '' || $data['imap_passowrd'] == '')) {        
    }else{        
       $inbox = imap_open("{" . $data['imap_host'] . ":" . $data['imap_port'] . "/imap/" . $data['imap_secure'] . "}INBOX", $data['imap_userame'], $data['imap_passowrd']) or $inbox = imap_open("{" . $data['imap_host'] . ":" . $data['imap_port'] . "/imap/" . $data['imap_secure'] . "/novalidate-cert}INBOX", $data['imap_userame'], $data['imap_passowrd']);
       if($inbox){
           $chkInbox = (string) $inbox;
           $chkInbox = substr(strtolower(trim($chkInbox)),0,11);
           if($chkInbox=='resource id'){
                mysqli_query($db->conn,"update tickleuser set imapOff='0' where TickleID='" . $data['TickleID'] . "'");
                $res = 'Imap Authentication Yes';
           }
           else{
               mysqli_query($db->conn,"update tickleuser set imapOff='1' where TickleID='" . $data['TickleID'] . "'");
                $imapEmailIds[] = $data['EmailID'];
                $res = 'Imap Authentication No';
                if(date("Y-m-d") > date("Y-m-d", strtotime("+7 days", strtotime($data['imapnotification']))) || $data['imapnotification']=='0000-00-00'){
                    mysqli_query($db->conn,"update tickleuser set imapnotification='".date('Y-m-d')."' where TickleID='" . $data['TickleID'] . "'");
                    sendAlertMessage($imapSubject,$imapContent,$data['EmailID']);
                }
           }
       }
       else{           
           mysqli_query($db->conn,"update tickleuser set imapOff='1' where TickleID='" . $data['TickleID'] . "'");
           $imapEmailIds[] = $data['EmailID'];
           $res = 'Imap Authentication No';
           if(date("Y-m-d") > date("Y-m-d", strtotime("+7 days", strtotime($data['imapnotification']))) || $data['imapnotification']=='0000-00-00'){
              mysqli_query($db->conn,"update tickleuser set imapnotification='".date('Y-m-d')."' where TickleID='" . $data['TickleID'] . "'");
              sendAlertMessage($imapSubject,$imapContent,$data['EmailID']);
           }  
       }   
       echo ' == '.$res;
       $logData = array('TickleID'=>$data['TickleID'],'EmailID'=>$data['EmailID'],'ttresponse'=>$res);
       WriteFile(__DIR__ . '/ticklelog/'.date('d-m-Y').'/mastercron.json', ','.json_encode($logData), "a");     
       imap_close($inbox);        
    }
    
    if (!empty($data['refresh_token'])){                
        $refresh_token = $data['refresh_token'];
        try {
            $client->refreshToken($refresh_token);
            $getGoogleToken = $client->getAccessToken();
            mysqli_query($db->conn,"update tickleuser set imapOff='0' where TickleID='" . $data['TickleID'] . "'");
            if($data['DMUse']=='1')
            {
                mysqli_query($db->conn,"update tickleuser set DMSmtpOff='0' where TickleID='" . $data['TickleID'] . "'");
            }
            $res = 'Google Authentication Yes';
        } catch (Exception $e) {
            $ttresponse = $e->getMessage();            
            mysqli_query($db->conn,"update tickleuser set imapOff='1' where TickleID='" . $data['TickleID'] . "'");
            $googleEmailIds[] = $data['EmailID'];
            if($data['DMUse']=='1')
            {
                mysqli_query($db->conn,"update tickleuser set DMSmtpOff='1' where TickleID='" . $data['TickleID'] . "'");
            }
            if(date("Y-m-d") > date("Y-m-d", strtotime("+7 days", strtotime($data['smtpnotification']))) || $data['smtpnotification']=='0000-00-00'){
              mysqli_query($db->conn,"update tickleuser set smtpnotification='".date('Y-m-d')."' where TickleID='" . $data['TickleID'] . "'");
                sendAlertMessage($googleSubject,$googleContent,$data['EmailID']);
            }    
            $res = 'Google Authentication No';
        } 
        echo ' == '.$res;
        $logData = array('TickleID'=>$data['TickleID'],'EmailID'=>$data['EmailID'],'ttresponse'=>$res);
        WriteFile(__DIR__ . '/ticklelog/'.date('d-m-Y').'/mastercron.json', ','.json_encode($logData), "a");     
    }
    
    
}
 


/*Check User Secondary Email Imap/SMTP/Google Authentication settings*/
$query = "SELECT id,TickleID,EmailID,DMUse,DMSystem,DMSmtp,DMPort,DMUser,DMPwd,DMSecure,imap_host,imap_userame,imap_passowrd,imap_port,imap_secure,refresh_token,imapnotification,smtpnotification from secondaryEmail";
$result = mysqli_query($db->conn,$query);
$resultData = array();
while($row = mysqli_fetch_assoc($result))
{
    $row['DMPwd'] = @trim(decryptIt($row['DMPwd']));
    $row['imap_passowrd'] = @trim(decryptIt($row['imap_passowrd']));
    $resultData[] = $row;
}

foreach ($resultData as $data)
{
    echo '<br>Secondary Email = '.$data['EmailID'];
    if($data['DMUse']=='1')
    {
        if (empty($data['refresh_token']) && !empty($data['DMUser']) && !empty($data['DMSmtp']))
        {
            $mail = new PHPMailer(false);
            $mail->IsSMTP();                                      // Set mailer to use SMTP
            $mail->Host = $data['DMSmtp'];                 // Specify main and backup server
            $mail->Port = $data['DMPort'];                                    // Set the SMTP port
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $data['DMUser'];                // SMTP username
            $mail->Password = $data['DMPwd'];                  // SMTP password
            $mail->SMTPSecure = $data['DMSecure'];                            // Enable encryption, 'ssl' also accepted
            $mail->From = $data['EmailID'];
            //$mail->FromName = 'Your From name';
            $mail->AddAddress('tickletraincron@gmail.com', '');  // Add a recipient//shine@123
            //$mail->AddAddress('ellen@example.com');               // Name is optional
            $mail->IsHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Test SMTP Connection';
            $mail->Body    = 'Test SMTP Connection';
            $mail->AltBody = 'Test SMTP Connection';
            if(!$mail->Send()) {                     
                mysqli_query($db->conn,"update secondaryEmail set DMSmtpOff='1' where id='" . $data['id'] . "'");            
                $logData = array('TickleID'=>$data['TickleID'],'EmailID'=>$data['EmailID'],'ttresponse'=>"SMTP Error: Could not connect to SMTP host.");
                echo " == SMTP Error:"; 
                $smtpEmailIds[] = $data['EmailID'];
                if(date("Y-m-d") > date("Y-m-d", strtotime("+7 days", strtotime($data['smtpnotification']))) || $data['smtpnotification']=='0000-00-00'){
                    mysqli_query($db->conn,"update secondaryEmail set smtpnotification='".date('Y-m-d')."' where id='" . $data['id'] . "'");
                    sendAlertMessage($smtpSubject,$smtpContent,$data['EmailID']);
                }
                WriteFile(__DIR__ . '/ticklelog/'.date('d-m-Y').'/mastercron.json', ','.json_encode($logData), "a");            
            }
            else{            
                mysqli_query($db->conn,"update secondaryEmail set DMSmtpOff='0' where id='" . $data['id'] . "'");
            }
            $mail->ClearAllRecipients();
            $mail->ClearReplyTos();   
        }else{
            mysqli_query($db->conn,"update secondaryEmail set DMSmtpOff='0' where id='" . $data['id'] . "'");
        }    
    } 
    
    
    if (($data['imap_host'] == '' || $data['imap_userame'] == '' || $data['imap_passowrd'] == '')) {        
    }else{        
       $inbox = imap_open("{" . $data['imap_host'] . ":" . $data['imap_port'] . "/imap/" . $data['imap_secure'] . "}INBOX", $data['imap_userame'], $data['imap_passowrd']) or $inbox = imap_open("{" . $data['imap_host'] . ":" . $data['imap_port'] . "/imap/" . $data['imap_secure'] . "/novalidate-cert}INBOX", $data['imap_userame'], $data['imap_passowrd']);
       if($inbox){
           $chkInbox = (string) $inbox;
           $chkInbox = substr(strtolower(trim($chkInbox)),0,11);
           if($chkInbox=='resource id'){
                mysqli_query($db->conn,"update secondaryEmail set imapOff='0' where id='" . $data['id'] . "'");
                $res = 'Imap Authentication Yes';
           }else{
                mysqli_query($db->conn,"update secondaryEmail set imapOff='1' where id='" . $data['id'] . "'");
                $imapEmailIds[] = $data['EmailID'];
                $res = 'Imap Authentication No';
                if(date("Y-m-d") > date("Y-m-d", strtotime("+7 days", strtotime($data['imapnotification']))) || $data['imapnotification']=='0000-00-00'){
                    mysqli_query($db->conn,"update secondaryEmail set imapnotification='".date('Y-m-d')."' where id='" . $data['id'] . "'");
                    sendAlertMessage($imapSubject,$imapContent,$data['EmailID']);
                }    
           }     
       }
       else{           
           mysqli_query($db->conn,"update secondaryEmail set imapOff='1' where id='" . $data['id'] . "'");
           $imapEmailIds[] = $data['EmailID'];
           $res = 'Imap Authentication No';
           if(date("Y-m-d") > date("Y-m-d", strtotime("+7 days", strtotime($data['imapnotification']))) || $data['imapnotification']=='0000-00-00'){
                mysqli_query($db->conn,"update secondaryEmail set imapnotification='".date('Y-m-d')."' where id='" . $data['id'] . "'");
                sendAlertMessage($imapSubject,$imapContent,$data['EmailID']);
           }    
       }   
       echo ' == '.$res;
       $logData = array('TickleID'=>$data['TickleID'],'EmailID'=>$data['EmailID'],'ttresponse'=>$res);
       WriteFile(__DIR__ . '/ticklelog/'.date('d-m-Y').'/mastercron.json', ','.json_encode($logData), "a");     
       imap_close($inbox);        
    }
    
    if (!empty($data['refresh_token'])){                
        $refresh_token = $data['refresh_token'];
        try {
            $client->refreshToken($refresh_token);
            $getGoogleToken = $client->getAccessToken();
            mysqli_query($db->conn,"update secondaryEmail set imapOff='0' where id='" . $data['id'] . "'");
            if($data['DMUse']=='1')
            {
                mysqli_query($db->conn,"update secondaryEmail set DMSmtpOff='0' where id='" . $data['id'] . "'");
            }
            $res = 'Google Authentication Yes';
        } catch (Exception $e) {
            $ttresponse = $e->getMessage();            
            mysqli_query($db->conn,"update secondaryEmail set imapOff='1' where id='" . $data['id'] . "'");
            $googleEmailIds[] = $data['EmailID'];
            if($data['DMUse']=='1')
            {
                mysqli_query($db->conn,"update secondaryEmail set DMSmtpOff='1' where id='" . $data['id'] . "'");
            }
            $res = 'Google Authentication No';
            if(date("Y-m-d") > date("Y-m-d", strtotime("+7 days", strtotime($data['smtpnotification']))) || $data['smtpnotification']=='0000-00-00'){
                mysqli_query($db->conn,"update secondaryEmail set smtpnotification='".date('Y-m-d')."' where id='" . $data['id'] . "'");
                sendAlertMessage($googleSubject,$googleContent,$data['EmailID']);
            }    
        } 
        echo ' == '.$res;
        $logData = array('TickleID'=>$data['TickleID'],'EmailID'=>$data['EmailID'],'ttresponse'=>$res);
        WriteFile(__DIR__ . '/ticklelog/'.date('d-m-Y').'/mastercron.json', ','.json_encode($logData), "a");     
    }
}










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

$query = "SELECT tk.* from task tk inner join user_mail um on um.MailID=tk.MailID WHERE tk.TaskGMDate<='$Cdate' and tk.Status='Y' and (tk.TickleID IN (".$userSmtp.") or tk.secondaryEmailId IN (".$secUserSmtp.")) order by tk.TickleID ";

$result =mysqli_query($db->conn,$query);
while($row = mysqli_fetch_array($result)) {
       
       //echo '<pre>'; print_r($row);       
        $taskGMDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskGMDate']));
        $TaskInitiateDateNew = $tomorrowDate.' '.date('H:i:s', strtotime($row['TaskInitiateDate']));
       // echo "update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $row['TaskID'] . "'<br>";
       mysqli_query($db->conn,"update task set TaskGMDate='" . $taskGMDateNew . "',TaskInitiateDate='" . $TaskInitiateDateNew . "' where TaskID='" . $row['TaskID'] . "' ");
        update_followups_time($row['MailID'],$row['TaskID'],$row['FollowTickleTrainID']);        
}
echo '<br> <br> <br>Cron Done';
?>
