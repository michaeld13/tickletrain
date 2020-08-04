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

//Google Auth and Zend Mailer Files

function constructAuthString($email, $accessToken) {
    return base64_encode("user=$email\1auth=Bearer $accessToken\1\1");
}


function isValidEmail($email) {
    return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email);
}

$result =mysqli_query($db->conn,"SELECT TickleID,TimeZone,TimeDailyTickle,today_report_date from tickleuser where `Status`='Y'");
//and TickleID=575
$users = array();
//This is for Daily Mail Report
$getservertz = date_default_timezone_get();

echo 'Time Now =  '.date('Y-m-d h:s:i a').'<br>';

//debug('================= cron started on '. date('Y-m-d H:i:s').'================');
echo mysqli_num_rows($result).' users found';
//debug(mysqli_num_rows($result).' users found');
while ($row = mysqli_fetch_assoc($result)) {

	if(!empty($row['TimeZone'])){
		$TimeZone = $row['TimeZone'];
    	date_default_timezone_set($TimeZone);
	}
	
	$send_date = date('Y-m-d H:i',strtotime(date('Y-m-d') . ' ' . $row['TimeDailyTickle']));
	$now = strtotime(date('Y-m-d H:i'));

    //debug('------For TickleID '.$row['TickleID'].'---------');
        if ($now  == strtotime($send_date)) {
            debug('mail sent to this user');
        	//$Cdate = gmdate("Y-m-d", $now);
			echo $Cdate = date("Y-m-d", $now);
        	$ctime = strtotime(date("Y-m-d", $now) . " 23:59:59");
            //if ($lastTime < $timen && $timen <= time()) {
                //if ((date('His') >= 235459) && (date('His') <= 235959) )  {
              // mysqli_query($db->conn,"update tickleuser set today_report_date='" . date('Y-m-d') . "' where TickleID='" . $uid . "'");
               // debug('Daily report started');
                SendDailyUser_Report($Cdate, $row['TickleID'], $ctime);
           // }
       }
   //debug( 'now = '.date('Y-m-d H:i').' , senddate = '.$send_date . ', timezon= '. date_default_timezone_get());
   //debug('=================cron end on '. date('Y-m-d H:i:s').'================');
}
  echo "<br><b>Cron Done</b>";

mysqli_free_result($result);
date_default_timezone_set($getservertz);
//die();



function SendDailyUser_Report($Cdate, $uid, $ctime) {
    global $db;
    global $TtSmtpHost;  // Specify main and backup SMTP servers
    global $TtSmtpAuth;                               // Enable SMTP authentication
    global $TtSmtpUsername;                 // SMTP username
    global $TtSmtpPassword;                           // SMTP password
    global $TtSmtpSecure;                            // Enable TLS encryption, `ssl` also accepted
    global $TtSmtpPort; 
    global $TtSmtpReplyMail;
    
    
    $allowed_compaign =mysqli_query($db->conn,"select Allowe_campaign,warningthresold	from Compaign where TickleID=$uid");
    while ($allowcamp = mysqli_fetch_assoc($allowed_compaign)) {
        $campaignallowed = $allowcamp['Allowe_campaign'];
        $allowecampaign = $allowcamp['Allowe_campaign'];
        $warningthresold = $allowcamp['warningthresold'];
    }

    $start_date =  $Cdate;
    $end_date =  date('Y-m-d', strtotime('+1 day', strtotime($start_date)));

    $dselect = "select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where tickle.reminder_task = 'N' and  task.TickleID='$uid' and task.Status='Y'";
    $mselect = "select distinct task.MailID, task.TaskID, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where tickle.reminder_task = 'N' and task.TickleID='$uid' and task.Status='Y'";
    $dselect.=" order by TaskDate";

    $i = 0;
    $checkquery =mysqli_query($db->conn,$dselect);
    while ($checrow = mysqli_fetch_assoc($checkquery)) {
        $newcheckarray[$i] = $checrow;
        $i++;
    }

    $dates = array();
    $mArr = $newcheckarray;

    foreach ($mArr as $row) {
        $dates[$row['TaskDate']] = 1;
    }
    $Variables['dates'] = $dates;
    $j = 0;

    $checkqueryagain =mysqli_query($db->conn,$mselect);
    while ($checrowagain = mysqli_fetch_assoc($checkqueryagain)) {
        $newcheckarrayagain[$j] = $checrowagain;
        $j++;
    }

    $mArr = $newcheckarrayagain;
    $mails = array();
    $sMails = array();
    foreach ($mArr as $row) {
        if (!isset($sMails[$row['MailID']])) {
            $sMails[$row['MailID']] = $row['TaskDateTime'];
        }
    }
    if ($sfld == 5 && !$sord) {
        asort($sMails);
    }
    if ($sfld == 5 && $sord) {
        arsort($sMails);
    }

    foreach ($sMails as $mid => $val) {
        $mails[] = $mid;
    }

    $currentcampaign = count($mails);

    //die();
    $percentagecampaign1 = ($allowecampaign / 100) * $warningthresold;
    $percentagecampaig = floor($allowecampaign - $percentagecampaign1);
    //die();
    $EmailID =mysqli_query($db->conn,"select EmailID,Plan,blueplanbarning from tickleuser where TickleID='" . $uid . "'");
    while ($mailcheck = mysqli_fetch_assoc($EmailID)) {
        $email = $mailcheck['EmailID'];
        $plan = $mailcheck['Plan'];
        $blueplanbarning = $mailcheck['blueplanbarning'];
    }


    if ($currentcampaign >= $percentagecampaig) {
        $params['EmailID'] = $email;
        $checkmail = whmcs_checkmail($params);
        if (isset($checkmail) && $checkmail['userid'] != "") {
            $userid = $checkmail['userid'];
            $getorder = whmcs_getorder($checkmail['userid']);
            $getserviceid = whmcs_getproducdetails($getorder['userid']);
            if ($getserviceid->products->product[0]->id != "") {
                $serviceid = $getserviceid->products->product[0]->id;
                $productid = $getserviceid->products->product[0]->pid;
            }
        }
    }
    
    $mailreport = new PHPMailer(false); //New instance, with exceptions enabled
    //$mailreport->IsMail();
    $mailreport->isSMTP();                                      // Set mailer to use SMTP
    $mailreport->Host = $TtSmtpHost;  // Specify main and backup SMTP servers
    $mailreport->SMTPAuth = $TtSmtpAuth;                               // Enable SMTP authentication
    $mailreport->Username = $TtSmtpUsername;                 // SMTP username
    $mailreport->Password = $TtSmtpPassword;                           // SMTP password
    $mailreport->SMTPSecure = $TtSmtpSecure;                            // Enable TLS encryption, `ssl` also accepted
    $mailreport->Port = $TtSmtpPort;   
    
    $query = "SELECT distinct *, TA.TaskGMDate as task_tm, TU.EmailID as ReportEmailID, TU.TimeZone as user_timezone from tickleuser as TU , task as TA, tickle as TT, user_mail as UM, contact_list as CL  WHERE  TA.TaskInitiateDate>='$start_date' and  TA.TaskInitiateDate<'$end_date'  and TA.Status='Y' and TU.TickleID=TA.TickleID and TA.TickleTrainID = TT.TickleTrainID and TT.reminder_task= 'N' and TA.TickleID=TT.TickleID and TA.MailID=UM.MailID and UM.ContactID=CL.ContactID and TU.TickleID=$uid order by TT.TickleName asc, TA.TaskGMDate asc";

    $result =mysqli_query($db->conn,$query);
    $followsArr = array();

    $i = 0;
    $tusers = array();
    $tmails = array();
    $tickles = array();
    $minDay = 0;
    $maxDay = 0;
    $tzone = date_default_timezone_get();
    date_default_timezone_set("UTC");
    $utzone = "UTC";
    echo "<pre>";
    while ($row = mysqli_fetch_assoc($result)) {
        $followsArr[] = @intval($row['FollowTickleTrainID']);
        if (!isset($tmails[$row['MailID']])) {
            $tmails[$row['MailID']] = 1;
        }
        $tickles[] = $row['TickleID'];
        $tm = strtotime($row['task_tm']);
        if (!$minDay || $tm < $minDay) {
            $minDay = $tm;
        }
        if (!$maxDay || $tm > $maxDay) {
            $maxDay = $tm;
        }
        $row['TaskUNIXTime'] = $tm;
        $utzone = $row["user_timezone"];
        $tusers[] = $row;
    }
    mysqli_free_result($result);
    date_default_timezone_set($utzone);
    $tmails = array_keys($tmails);
    $result =mysqli_query($db->conn,"SELECT distinct MailID, SUM(IF(STATUS =  'S', 1, 0)) AS sent, SUM(IF(STATUS =  'Y', 1, 0)) AS nsent FROM `task` where MailID in (" . implode(',', $tmails) . ") GROUP BY MailID ORDER BY MailID");
    $tstages = array();
    while ($trow = mysqli_fetch_array($result)) {
        $tstages[intval($trow['MailID'])] = $trow;
    }

    /* SELECT MailID, SUM(IF(STATUS =  'S', 1, 0)) AS sent, SUM(IF(STATUS =  'Y', 1, 0)) AS nsent FROM  `task` GROUP BY MailID ORDER BY MailID LIMIT 0 , 30 */
    debug("Found " . count($tusers) . ' tasks for daily report sent');
    $contact_list = array();
    if (count($tusers) > 0) {
        $sql_cmail =mysqli_query($db->conn,"select distinct * from contact_list where TickleID in (" . implode(",", $tickles) . ")");
        while ($row = mysqli_fetch_array($sql_cmail)) {
            $contact_list[$row['ContactID']] = $row;
        }
        mysqli_free_result($sql_cmail);
    }

    $result =mysqli_query($db->conn,"select distinct * from tickleuser as TU, ticklefollow TF where TU.TickleID=TF.TickleID and TU.TickleID=$uid and TF.FollowTickleTrainID in (" . join(",", $followsArr) . ")");
    while ($trow = mysqli_fetch_array($result)) {
        $tfollow[intval($trow['FollowTickleTrainID'])] = $trow;
    }
    mysqli_free_result($result);
    $TickleArr = array();
    $TickleMail = array();
    $ddd = 0;
    $usedMails = array();
    $act_link  = '' ;  

    for ($hhh = 0; $hhh < count($tusers); $hhh++) {
        $row = $tusers[$hhh];

        if (isset($usedMails[$row['MailID']])) {
            continue;
        }
        // $today = date('N', strtotime($row['TaskGMDate']));
        // if(in_array($today,[6,7]) ){ // skip saturday and sunday
        //     continue;
        // }


        $usedMails[$row['MailID']] = 1;
        $TApprove = $row['TApprove'];
        if (@intval($row['FollowTickleTrainID'])) {
            $ftickle = $tfollow[intval($row['FollowTickleTrainID'])];
            $TApprove = $ftickle['TApprove'];
        }

        $enable_alt = $row['enable_alt'];
        $alt_email = $row['alt_email'];
        $imps = implode(",", $row);
        $contacts = array();
        $EmailID = $row['ReportEmailID'];
        $TickleID = $row['TickleID'];
        $TickleMail[$TickleID]['view_more'] ='';

        if($hhh > 50 ){
           $TickleMail[$TickleID]['view_more']  ='To view more, please';
           break;
        }

        if (!isset($TickleArr[$TickleID])) {
            $TickleArr[$TickleID] = array();
            $TickleMail[$TickleID] = array();
            $TickleMail[$TickleID]['TEXT'] = '';
            $TickleMail[$TickleID]['HTML'] = '';
            $ddd = 0;
        } else {
            $ddd = count($TickleArr[$TickleID]);
        }
        $TickleArr[$TickleID][$ddd] = array();
        $TickleArr[$TickleID][$ddd]['content'] = $row;
        //$TickleArr[$TickleID][]['Content'] = $row;
        $protect = protect($row['TickleID'] . "-" . $row['TaskID']);

        $act_link = rawurlencode($protect);

        $MailID = $row['MailID'];
        $TickleTrainID = $row['TickleTrainID'];
        $TickleMail[$TickleID]['TDashboardLink'] = "https://" . SERVER_NAME . Url_Create("home", "act=".$act_link);
        $TickleMail[$TickleID]['Tdirectupgradelink'] = $TickleMail[$TickleID]['TDashboardLink'] . '&dierctserviceid=' . $serviceid;
        $TickleMail[$TickleID]['serviceid'] = $serviceid;
        $TickleMail[$TickleID]['plan'] = $plan;
        $TickleMail[$TickleID]['blueplanbarning'] = $blueplanbarning;
        //die();
        $TickleContact = $row['TickleContact'];
        $querycontact =mysqli_query($db->conn,"select * from category where CategoryID='" . $TickleContact . "' and TickleID='" . $TickleID . "'");
        $rsc = mysqli_fetch_array($querycontact);
        mysqli_free_result($querycontact);
        if (isset($contact_list[$row['ContactID']])) {
            $toaddress = $contact_list[$row['ContactID']];
            $vx = strtolower($toaddress['EmailID']);
            $fname = $toaddress['FirstName'];
            $lname = $toaddress['LastName'];
            $Contact = "To: " . htmlspecialchars($fname . " " . $lname . " <" . $vx . ">");
        } else {
            $Contact = "To: " . htmlspecialchars($row['toaddress']);
        }

        if (trim($row['ccaddress']) != "") {
            $ccMail = explode('@', htmlspecialchars($row['ccaddress']));
            if(isset($ccMail[1]) && $ccMail[1]!='tickletrain.com')
            {    
                $Contact.="<br>CC: " . htmlspecialchars($row['ccaddress']);
            }    
        }

        // Code to add Contionuos feature on Todays' Tickle Mail , Added on 24/1/2014
      
        $ContinuousExist = false;
        $GetTaskDetailQuery =mysqli_query($db->conn,"select `MailID`,`FollowTickleTrainID`,`TickleTrainID` from `task` WHERE 
         `MailID`='" . $tstages[$MailID][MailID] . "' order by TaskID desc limit 1");
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
        if ($ContinuousExist) {
            $Stage = (@intval($tstages[$MailID]['sent']) + 1) . " of &infin;";
        } else {
            $Stage = (@intval($tstages[$MailID]['sent']) + 1) . " of " . (@intval($tstages[$MailID]['nsent']) + @intval($tstages[$MailID]['sent']));
        }

        // End of Code
        //  die();

        $names = $contact_list[$row['ContactID']];
        $TaskInitiateDate = date("M jS, Y h:ia", $row['TaskUNIXTime']);
        $TickleArr[$TickleID][$ddd]['Subject'] = $row['Subject'];
        $TickleArr[$TickleID][$ddd]['TickleContact'] = $rsc['CategoryName'];
        if ($TApprove == 'Y' && $row['Approve'] == 'N') {
            $TickleArr[$TickleID][$ddd]['ApproveLink'] = "https://".SERVER_NAME . Url_Create("approve", "act=" .$act_link);
        } else {
            if ($row['Pause'] == 'N') {
                $TickleArr[$TickleID][$ddd]['PauseLink'] = "https://".SERVER_NAME.Url_Create("pause", "act=".$act_link);
            } else {
                $TickleArr[$TickleID][$ddd]['UnPauseLink'] = "https://".SERVER_NAME.Url_Create("unpause", "act=".$act_link);
            }
        }
        $TickleArr[$TickleID][$ddd]['DeleteLink'] = "https://" . SERVER_NAME . Url_Create("unsubscribe", "act=".$act_link);

        $TickleMail[$TickleID]['NAME'] = $row['FirstName'] . " " . $row['LastName'];
        $TickleMail[$TickleID]['EMAIL'] = $EmailID;
        //Alternate Email ID
        if ($enable_alt > 0 && $alt_email != "") {
            if (isValidEmail(trim($alt_email)))
                $TickleMail[$TickleID]['ALTEMAIL'] = $alt_email;
        }

        //Starting of Code to set custom Subject on Today's Tickle Mails. Set on 26/2/2014
        if (isset($CustomSubjectForTodayTickleMail) && isset($FollowTickleTrainIdForCustomSubject)) {
            unset($CustomSubjectForTodayTickleMail);
            unset($FollowTickleTrainIdForCustomSubject);
            if (isset($CustomSubjectForTodayTickleMail)):
                unset($CustomSubjectForTodayTickleMail);
            endif;
        }
        $FollowTickleTrainIdForCustomSubject = $row['FollowTickleTrainID'];
        if ($FollowTickleTrainIdForCustomSubject == 0) {
            $TickleTrainIdForCustomSubject = $row['TickleTrainID'];
            $GetTickleCustomSubjectQuery =mysqli_query($db->conn,"select custom_subject from tickle where TickleTrainID='" . $TickleTrainIdForCustomSubject . "'");
            $GetTickleCustomSubjectRow = mysqli_fetch_assoc($GetTickleCustomSubjectQuery);
            $CustomSubjectForTodayTickleMail = $GetTickleCustomSubjectRow['custom_subject'];
        } else {
            $GetTickleFollowCustomSubjectQuery = mysqli_query
                    ("select custom_subject from ticklefollow where FollowTickleTrainID='" . $FollowTickleTrainIdForCustomSubject . "'");
            $GetTickleFollowCustomSubjectRow = mysqli_fetch_assoc($GetTickleFollowCustomSubjectQuery);
            $CustomSubjectForTodayTickleMail = $GetTickleFollowCustomSubjectRow['custom_subject'];
        }
        if (isset($CustomSubjectForTodayTickleMail) && $CustomSubjectForTodayTickleMail != "") {
            $row['Subject'] = $CustomSubjectForTodayTickleMail;
        }

        //End of Code to set custom Subject on Today's Tickle Mails. Set on 26/2/2014


        $TickleMail[$TickleID]['TEXT'].='
            Tickle Name : ' . $row['TickleName'] . '
            To : ' . $Contact . '
            Subject : ' . $row['Subject'] . '
            Date : ' . $TaskInitiateDate . '
            Delete Link : ' . $TickleArr[$TickleID][$ddd]['DeleteLink'];
        if (isset($TickleArr[$TickleID][$ddd]['ApproveLink'])) {
            $TickleMail[$TickleID]['TEXT'].='Approve Link : ' . $TickleArr[$TickleID][$ddd]['ApproveLink'];
        }
        if (isset($TickleArr[$TickleID][$ddd]['PauseLink'])) {
            $TickleMail[$TickleID]['TEXT'].='Pause Link : ' . $TickleArr[$TickleID][$ddd]['PauseLink'];
        }
        if (isset($TickleArr[$TickleID][$ddd]['UnPauseLink'])) {
            $TickleMail[$TickleID]['TEXT'].='UnPause Link : ' . $TickleArr[$TickleID][$ddd]['UnPauseLink'];
        }

        $TickleMail[$TickleID]['TEXT'].='-----------------------------';
        if ($ddd % 2) {
            $bgcolor = 'bgcolor="#FFFFFF"';
        } else {
            $bgcolor = 'bgcolor="#DFEDFD"';
        }
        $time = $row['TaskUNIXTime'];
        $TickleTime = date("h:i A", $time);

        $subject = "https://" . SERVER_NAME . Url_Create("previewmail", "act=" . rawurlencode($protect) . "&MailID=" . $MailID . "&Mails=Mail");
        $subject = '<a href="' . $subject . '" style="white-space:nowrap">' . htmlspecialchars($row['Subject']) . '</a>';
        $TaskTime = "https://" . SERVER_NAME . Url_Create("edittask", "act=" . rawurlencode($protect) . "&MailID=" . $MailID);
        $TaskTime = '<a href="' . $TaskTime . '">' . $TickleTime . '</a>';
        ob_start();
        include "tpl/mailrow.php";
        $htmlrow = ob_get_contents();
        ob_end_clean();
        $TickleMail[$TickleID]['HTML'].=$htmlrow;
        $Contact = "";
    }
    $time1 = $ctime; //mktime(date('H'), date('i') + 68, date('s'), date('m'), date('d'), date('Y'));
    $mindt = date("Ymd", $minDay);
    $maxdt = $mindt; //date("Ymd",$maxDay);
    $reportDate = date("M jS, Y", $minDay);
    if ($mindt != $maxdt) {
        $reportDate.= " - " . date("M jS, Y", $maxDay);
    }

 $todaydate = date('Y-m-d');
    $reportDate = date("M jS, Y",strtotime($todaydate));
	
    // die();
    // if(isset($serviceid)){
    // $txtstart = "Today's Tickles scheduled for " . $reportDate . ":\n";    
    //  }else{
    $txtstart = "Today's Tickles scheduled for " . $reportDate . ":\n";
    // }
    //$htmlstart = "Today's Tickles scheduled for " . date("M jS, Y", $time1) . ':<br /><br /><table bgcolor="#CCCCCC" cellpadding="3" cellspacing="1"><tr bgcolor="#EFEFEF"><td><b>Name</b></td><td><b>E-mail</b></td><td><b>Subject</b></td><td><b>Tickle</b></td><td><b>Time</b></td><td><b>Stage</b></td><td><b>Actions</b></td></tr>';

    if (count($TickleMail) > 0) { 
        foreach ($TickleMail as $Key => $Value) { 

            if (count($Value) > 0 && $Value['EMAIL'] != "") {
                $EmailPriority = 3;
                ob_start();
                include "tpl/mail.php";
                $HTMLValue = ob_get_contents();
                ob_end_clean();
                //debug($HTMLValue);
                //$HTMLValue = $htmlstart . $Value['HTML'] . '</table>Click <a href="' . $Value['TDashboardLink'] . '">here</a> to login to Tickle Train.';
                //$mailreport->SetFrom($Value['EMAIL'], $Value['NAME']);
                $mailreport->SetFrom($TtSmtpReplyMail, "TickleTrain");
                // if(isset($serviceid)){
                //  $mailreport->Subject = "Today's Tickles scheduled for " . $reportDate;
                //  }else{
                $mailreport->Subject = "Today's Tickles scheduled for " . $reportDate;
                //  }
                $mailreport->AltBody = $mailcontent['TEXT'] . $Value['TEXT'];
                $mailreport->WordWrap = 80; // set word wrap
                $mailreport->Priority = $EmailPriority;
                $mailreport->CharSet = "utf-8";
                $mailreport->MsgHTML($HTMLValue, IMAGE_BASE_FOLDER);
                $mailreport->IsHTML(true); // send as HTML
                //$mailreport->AddReplyTo($TtSmtpReplyMail);
                $mailreport->Sender = $TtSmtpReplyMail;
                $mailreport->AddAddress($Value['EMAIL']); //$Value['EMAIL']
				//$mailreport->AddCC('anjali.shinedezign@gmail.com');
                $mailreport->Send();
                debug("Today's Tickles has been sent for '" . $Value['EMAIL'] . "'");
                // Clear all addresses and attachments for next loop
                $mailreport->ClearAddresses();
                $mailreport->ClearBCCs();
                $mailreport->ClearReplyTos();
                $mailreport->ClearAllRecipients();
                $mailreport->ClearCCs();

                if ($Value['ALTEMAIL'] != "") {
                    try {
                            $mailreport->AddReplyTo($TtSmtpReplyMail);
                            $mailreport->Sender = $TtSmtpReplyMail;
                            $mailreport->AddAddress($Value['ALTEMAIL']);
							$mailreport->AddCC('anjali.shinedezign@gmail.com');
                            $mailreport->Send();
                            // Clear all addresses and attachments for next loop
                            $mailreport->ClearAddresses();
                            $mailreport->ClearBCCs();
                            $mailreport->ClearReplyTos();
                            $mailreport->ClearAllRecipients();
                            $mailreport->ClearCCs();
                      }
                        catch(Exception $e) {
                        //echo 'Message: ' .$e->getMessage();
                      }                        
                }
            }//if
        }//foreach
    }//if
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
