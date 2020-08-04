<?php
//$encrypted = encryptIt($input);
//$decrypted = decryptIt($encrypted);
// encrypt password 

if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "") {
    $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //header("Location: $redirect");
}

if (isset($_POST['testsmtp'])) {

  if($_REQUEST['email_type']=='primary'){
	    if ($_REQUEST['imap_secure'] == 'none') {
		$_REQUEST['imap_secure'] = '';
	    }    
	    $imap_decrypt_pass=$_REQUEST['imap_passowrd'];
	    $connection = imap_open("{" . $_REQUEST['imap_host'] . ":" . $_REQUEST['imap_port'] . "/imap/" . $_REQUEST['imap_secure'] . "/novalidate-cert}INBOX", $_REQUEST['imap_userame'], $imap_decrypt_pass);
	    if ($connection) {
		echo "true";
	    } else {
		echo imap_last_error();
		die();
	    }
   }else{
	 if ($_REQUEST['sec_'.$_REQUEST['id'].'_imap_secure'] == 'none') {
		$_REQUEST['sec_'.$_REQUEST['id'].'_imap_secure'] = '';
	    }  
	
	  $imap_decrypt_pass=$_REQUEST['sec_'.$_REQUEST['id'].'_imap_passowrd'];
	    $connection = imap_open("{" . $_REQUEST['sec_'.$_REQUEST['id'].'_imap_host'] . ":" . $_REQUEST['sec_'.$_REQUEST['id'].'_imap_port'] . "/imap/" . $_REQUEST['sec_'.$_REQUEST['id'].'_imap_secure'] . "/novalidate-cert}INBOX", $_REQUEST['sec_'.$_REQUEST['id'].'_imap_username'], $imap_decrypt_pass);
	    if ($connection) {
		echo "true";
	    } else {
		echo imap_last_error();
		die();
	    }
   }
    exit;
}


if (isset($_POST['CustomSubjectCheckbox'])) {
    if (CheckCustomSubject($_POST['TickleTrainID'])) {
        $UpdateQuery = mysqli_query($db->conn,"update tickle_custom_subject set custom_subject
      = '" . mysqli_real_escape_string($db->conn,$_POST['TextAreaCustomSubject']) . "' where TickleTrainID='" . $_POST['TickleTrainID'] . "'")
                or die(mysqli_error($db->conn) . __LINE__);
    } else {
        $InsertQuery = mysqli_query($db->conn,"insert into tickle_custom_subject (custom_subject,
       TickleTrainID) values ('" . mysqli_real_escape_string($db->conn,$_POST['TextAreaCustomSubject']) . "','" . $_POST['TickleTrainID'] . "')")
                or die(mysqli_error($db->conn) . __LINE__);
    }
} elseif (!isset($_POST['CustomSubjectCheckbox']) && $_POST['TextAreaCustomSubject'] != "") {
    if (CheckCustomSubject($_POST['TickleTrainID'])) {
        $UpdateQuery = mysqli_query($db->conn,"update tickle_custom_subject set custom_subject
      = '' where TickleTrainID='" . $_POST['TickleTrainID'] . "'")
                or die(mysqli_error($db->conn) . __LINE__);
    }
}

function CheckCustomSubject($TickleTrainID) {
    global $db;
    $query = mysqli_query($db->conn,"select * from  tickle_custom_subject where TickleTrainID=
       '" . $TickleTrainID . "'") or die(mysqli_error($db->conn) . __LINE__);
    if (mysqli_num_rows($query) > 0) {
        return true;
    } else {
        return false;
    }
}

if (isset($_POST['checkfollowup'])) {
    if (isset($_POST['ticklemessage'])) {
        $GetFollowUpQuery = mysqli_query($db->conn,"select `FollowTickleTrainID` from `ticklefollow` where `TickleTrainID`='" . $_POST['tid'] . "'");
        if (mysqli_num_rows($GetFollowUpQuery)) {
            die("NotAvailable");
        } else {
            die("Available");
        }
    } elseif (isset($_POST['ticklefollowmessage'])) {

        if ($_POST['ticklefollowmessageid'] > 0) {
            $GetFollowUpQuery = mysqli_query($db->conn,"select `FollowTickleTrainID` from `ticklefollow` where `FollowTickleTrainID` > '" . $_POST['ticklefollowmessageid'] . "'
          and `TickleTrainID`='" . $_POST['tid'] . "'");
            if (mysqli_num_rows($GetFollowUpQuery)) {
                die("NotAvailable");
            } else {
                die("Available");
            }
        } else {
            die("Available");
        }
    } elseif (isset($_POST['AlreadyCheckedContinuousForFollowaUpMessage'])) {
        $AlreadyCheckedContinuousForFollowaUpMessageQuery = mysqli_query($db->conn,"select FollowTickleTrainID from  ticklefollow where EndAfterFollow='13' and TickleTrainID = '" . $_POST['tid'] . "'") or die(mysqli_error($db->conn) . __LINE__);
        if (mysqli_num_rows($AlreadyCheckedContinuousForFollowaUpMessageQuery)) {
            die("NotAvailable");
        } else {
            die("Available");
        }
    }
}
//echo "<pre>";
//print_r($_REQUEST);
//echo "</pre>";
//die();
$action = @trim($_REQUEST['action']);
$reload = @intval($_REQUEST['reload']);
//$spamcheck=@trim($_REQUEST['spamcheck']);

$CreatedDate = date("Y-m-d H:i:s");
if ($_REQUEST['TickleTrainID'] != "") {
    $TickleTrainID = $_REQUEST['TickleTrainID'];
} else {
    $TickleTrainID = $_REQUEST['NTickleTrainID'];
}
$TickleName = @strtolower(trim($_REQUEST['TickleName']));
$CTickleName = clearstr($TickleName);
$TickleMailSubject = @trim($_REQUEST['TickleMailSubject']);

$TickleMailContent = @trim($_REQUEST['TickleMailContent']);
$AttachOriginalMessage = @trim($_REQUEST['AttachOriginalMessage']);
$AttachOriginalMessageFiles = @trim($_REQUEST['AttachOriginalMessageFiles']);
if ($AttachOriginalMessage == "") {
    $AttachOriginalMessage = "N";
} else {
    $AttachOriginalMessage = "Y";
    if ($AttachOriginalMessageFiles == "Y") {
        $AttachOriginalMessage = "A";
    }
}
$CCMe = @trim($_REQUEST['CCMe']);
if ($CCMe == "") {
    $CCMe = "N";
}
$TickleTime = str_replace(".", ":", @trim($_REQUEST['TickleTime']));
$TickleTime_Time = preg_replace("/[^0-9:]/", '', $TickleTime);
$TickleTime_PM = trim(preg_replace("/[^a-z]/", '', strtolower($TickleTime)));
$TTime = explode(":", $TickleTime_Time);
if ($TickleTime_PM == "pm") {
    $TickleTime1 = $TTime[0] + 12;
    if ($TTime[0] == 12)
        $TickleTime1 = $TTime[0];

    $TickleTime = $TickleTime1 . ":" . $TTime[1] . ":00";
}else {
    if ($TTime[0] == 12)
        $TTime[0] = "00";
    $TickleTime = $TTime[0] . ":" . $TTime[1] . ":00";
}
$ContactID = @trim($_REQUEST['ContactID']);
$QuickTickle = @trim($_REQUEST['QuickTickle']);
$RecurrencePattern = @trim($_REQUEST['RecurrencePattern']);
$DailyDays = @intval($_REQUEST['DailyDays']);
$WeeklyWeeks = @intval($_REQUEST['WeeklyWeeks']);
$EmailPriority = @intval($_REQUEST['EmailPriority']);

$NoWeekend = ((@trim($_REQUEST['NoWeekend']) != "") ? "Y" : "N");
$TApprove = ((@trim($_REQUEST['TApprove']) != "") ? "Y" : "N");
$TReceiptConfirm = ((@trim($_REQUEST['TReceiptConfirm']) != "") ? "Y" : "N");

$delete_campaign_on_reply = ((@trim($_REQUEST['delete_campaign_on_reply']) != "") ? "1" : "0");
$notify_campaign_deleted = ((@trim($_REQUEST['notify_campaign_deleted']) != "") ? "1" : "0");
$notify_when_reply_received = ((@trim($_REQUEST['notify_when_reply_received']) != "") ? "1" : "0");
$do_not_track = ((@trim($_REQUEST['do_not_track']) != "") ? "1" : "0");

$TickleMailFollowContent = $_REQUEST['TickleMailFollowContent'];
$AttachMessageFollow = @trim($_REQUEST['AttachMessageFollow']);
$AttachMessageFollowFiles = @trim($_REQUEST['AttachMessageFollowFiles']);
if ($AttachMessageFollow == "") {
    $AttachMessageFollow = "N";
} else {
    $AttachMessageFollow = "Y";
    if ($AttachMessageFollowFiles == "Y") {
        $AttachMessageFollow = "A";
    }
}

$EmailPriorityFollow = @intval($_REQUEST['EmailPriorityFollow']);
$CCMeFollow = ((@trim($_REQUEST['CCMeFollow']) != "") ? "Y" : "N");
$DailyDaysFollow = $_REQUEST['DailyDaysFollow'];
$EndAfterFollow = $_REQUEST['EndAfterFollow'];
$TickleTimeFollow = date("H:i:s", strtotime($_REQUEST['TickleTimeFollow']));

for ($i = 1; $i <= 7; $i++) {
    $cbox = "box" . $i;
    if ($_REQUEST[$cbox] != "")
        $week_days[] = $_REQUEST[$cbox];
}
if (is_array($week_days)) {
    $WeekDays = implode(",", $week_days);
}
if ($_REQUEST['tsot'] == "Instantly") {
    $_REQUEST['EndAfter'] = '1';
    $EndAfter = $_REQUEST['EndAfter'];
}else{
    $EndAfter = $_REQUEST['EndAfter'];
}    

//$TickleTime=date("H:i:s");

$ModifyDate = $CreatedDate;
$sql_add = array();
/* $theValue=$_REQUEST['theValue'];
  for($i=1;$i<=$theValue;$i++)
  {
  $kvalue="Dates".$i;
  if($_REQUEST[$kvalue]>0)
  {
  $day[]=$_REQUEST[$kvalue];
  }
  }
  if(is_array($day)){
  sort($day);
  }
  for($i=0;$i<=9;$i++)
  {
  $ix=$i+1;
  $sql_add['Date'.$ix]=$day[$i];
  }
 */

$FollowTickleTrainID = @intval($_REQUEST['FollowTickleTrainID']);
if ($action == "CheckTickle") {
    if ($CTickleName == "") {
        echo "true";
        exit;
    }
    //echo " Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and CTickleName='$CTickleName'";exit;
    $tcheck = $db->select_to_array('tickle', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and CTickleName='$CTickleName' and TickleTrainID<>'$TickleTrainID'");
    if ($tcheck[0]['TickleTrainID'] != "") {
        echo "false";
        exit;
    }
    echo "true";
    exit;
}

if ($action == "CheckTickleGroup") {
    if ($TickleName == "") {
        echo "0";
        exit;
    }
    $tickle = $db->selectRow('tickle', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and TickleTrainID='$TickleTrainID'");
    $TickleContact = @intval($tickle['TickleContact']);
    $tcheck = $db->select_to_array('category', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and CategoryName='$TickleName' and CategoryID<>'$TickleContact'");
    if (@intval($tcheck[0]['CategoryID']) != 0) {
        echo @intval($tcheck[0]['CategoryID']);
        exit;
    }
    echo "0";
    exit;
}

if ($action == "AddTickle" && $_REQUEST['TickleTrainID'] == "") {
   

    if (isset($_REQUEST['CustomSubjectCheckbox']) && $_REQUEST['TextAreaCustomSubject'] != "") {
        $TickleCustomSubject = $_REQUEST['TextAreaCustomSubject'];
    }

    if ($_REQUEST['tsot'] == "Instantly") {
        $DailyDays = 0;
    }

    $tcheck = $db->select_to_array('tickle', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and CTickleName='$CTickleName'");
    if ($tcheck[0]['TickleTrainID'] != "") {
        redirect('tickle');
    }

    $CategoryName = $TickleName;
    $CategoryID = @intval($_REQUEST['TickleContact']);
    if (!$CategoryID) {
        $ids = $db->insert('category', array('CategoryName' => $CategoryName, 'ParentID' => 0, 'TickleID' => $_SESSION['TickleID'], 'Status' => 'Y'));
        $CategoryID = $db->last_insert_id();
    }

    if ($TickleMailContent != "" && $tcheck[0]['TickleTrainID'] == "") {

        $imap_decrypt_pass=encryptIt($_REQUEST['imap_passowrd']);

        $reminder_task =  (isset($_POST['reminder_task']) && $_POST['reminder_task'] == 'on' ) ? 'Y' : 'N' ; 
        $reminder_task_name =  ($reminder_task == 'Y' ) ? $_POST['reminder_task_name'] : '' ; 
        $AttachOriginalMessage =  ($reminder_task == 'Y' ) ? 'Y' : $AttachOriginalMessage ;



        $ids = $db->insert('tickle', array_merge(array('TickleTrainID' => $TickleTrainID, 'NoWeekend' => $NoWeekend, 'TApprove' => $TApprove, 'TReceiptConfirm' => $TReceiptConfirm, 'TickleTime' => $TickleTime, 'CreatedDate' => $CreatedDate, 'ModifyDate' => $ModifyDate, 'TickleName' => $TickleName, 'CTickleName' => $CTickleName, 'TickleMailSubject' => "$TickleMailSubject", 'reminder_task' => $reminder_task , 'reminder_task_name' => $reminder_task_name  , 'TickleMailContent' => "$TickleMailContent", 'EmailPriority' => "$EmailPriority", 'TickleMailFollowContent' => "$TickleMailFollowContent", 'AttachOriginalMessage' => "$AttachOriginalMessage", 'CCMe' => "$CCMe", 'TickleContact' => $CategoryID, 'TickleID' => $_SESSION['TickleID'], 'Status' => 'Y', 'QuickTickle' => "$QuickTickle", 'RecurrencePattern' => "$RecurrencePattern", 'DailyDays' => "$DailyDays", 'WeeklyWeeks' => "$WeeklyWeeks", 'WeekDays' => "$WeekDays", 'EndAfter' => "$EndAfter", 'FollowUp' => (($FollowUp != "") ? "Y" : "N"), 'AttachMessageFollow' => (($AttachMessageFollow != "") ? "Y" : "N"), 'EmailPriorityFollow' => "$EmailPriorityFollow", 'CCMeFollow' => (($CCMeFollow != "") ? "Y" : "N"), 'DailyDaysFollow' => @intval($DailyDaysFollow), 'EndAfterFollow' => @intval($EndAfterFollow), 'TickleTimeFollow' => "$TickleTimeFollow", 'MessageWait' => 0, 'custom_subject' => $TickleCustomSubject, 'delete_campaign_on_reply' => "$delete_campaign_on_reply", 'notify_when_reply_received' => "$notify_when_reply_received", 'do_not_track' => "$do_not_track", 'notify_campaign_deleted' => "$notify_campaign_deleted"), $sql_add)) ;
        updateFiles($TickleTrainID, "TAttach");
        $_SESSION['ticklenew'] = $CTickleName;
    }
    /*        if ($spamcheck=='yes') {
      redirect('addtickle', 'tid='.$TickleTrainID.'&action=Edit&spamcheck=yes');
      } */
    if ($reload) {
        redirect("addtickle", "tid=" . $TickleTrainID);
    }
    redirect("tickle");
}


if ($action == "EditTickle" && $TickleTrainID != "") {

    //print_r($_REQUEST['secarray']);
    //die("Is that triggering ????");

    $MCategoryID = @intval($_REQUEST['TickleContact']);
    $tcheck = $db->select_to_array('tickle', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and TickleName='$TickleName' and TickleTrainID<>'$TickleTrainID'");
    //$Taskcheck=$db->select_to_array('task',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='$TickleTrainID'");

    if ($tcheck[0]['TickleTrainID'] != "") {
        redirect('tickle');
    }

    if ($_REQUEST['tsot'] == "Instantly") {
        $DailyDays = 0;
    }

    if (isset($_REQUEST['CustomSubjectCheckbox']) && $_REQUEST['TextAreaCustomSubject'] != "") {
        $TickleCustomSubject = $_REQUEST['TextAreaCustomSubject'];
    } else {
        $TickleCustomSubject = "";
    }
    $tickle = $db->selectRow('tickle', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and TickleTrainID='$TickleTrainID'");
    if ($MCategoryID > 0 && $MCategoryID != intval($tickle['TickleContact'])) {
        $db->update_ignore('category_contact_list', array('CategoryID' => $MCategoryID), array("WHERE CategoryID=?", intval($tickle['TickleContact'])));
        $db->delete('category', array("WHERE  CategoryID= ?", intval($tickle['TickleContact'])));
        $sql_add['TickleContact'] = $MCategoryID;
    }

    if (isset($_REQUEST['TickleName']) && isset($_REQUEST['TickleMailContent'])) {

         $imap_decrypt_pass=encryptIt($_REQUEST['imap_passowrd']);
        if ($_REQUEST['imap_host'] && $_REQUEST['imap_userame'] && $_REQUEST['imap_passowrd'] && $_REQUEST['imap_connection_approved'] == 'yes') {
            $update_imap = mysqli_query($db->conn,"update tickleuser set imap_host='$_REQUEST[imap_host]',
                           imap_userame='$_REQUEST[imap_userame]',imap_passowrd='$imap_decrypt_pass',imap_port='$_REQUEST[imap_port]',
                           imap_secure='$_REQUEST[imap_secure]' where TickleID='" . $_SESSION['TickleID'] . "'") or die("Error Here");
        }


	// multiple emails settings start
	
	 if(isset($_REQUEST['secarray'])){
		foreach($_REQUEST['secarray'] as $secemails){
			
			
			   if($_REQUEST['sec_'.$secemails.'_delete_campaign_on_reply']=='on'){
					$dor = 1;
			    }
			   else{
					$dor = 0;
		            }

			    if($_REQUEST['sec_'.$secemails.'_notify_when_reply_received']=='on'){
					$dcr = 1;
			    }
			   else{
					$dcr = 0;
		            }
			    if($_REQUEST['sec_'.$secemails.'_do_not_track']=='on'){
					$dnt = 1;
			    }
			   else{
					$dnt = 0;
		            }  			
				mysqli_query($db->conn,'update secondaryEmail set delete_campaign_on_reply = "'.$dor.'" ,  notify_when_reply_received= "'.$dcr.'" , do_not_track = "'.$dnt.'" where id="'.$secemails.'"');
			    
			//}
		}
	 }


	//multiple emails settings end

        $reminder_task =  (isset($_POST['reminder_task']) && $_POST['reminder_task'] == 'on' ) ? 'Y' : 'N' ;
        $reminder_task_name =  ($reminder_task == 'Y' ) ? utf8_encode($_POST['reminder_task_name']) : '' ;
        $AttachOriginalMessage =  ($reminder_task == 'Y' ) ? 'Y' : $AttachOriginalMessage ;

       // echo  utf8_encode($reminder_task_name);
       // echo  utf8_decode(utf8_encode($reminder_task_name)); die; 
        $db->update('tickle', array_merge(array('ModifyDate' => $ModifyDate, 'NoWeekend' => $NoWeekend, 'TApprove' => $TApprove, 'TReceiptConfirm' => $TReceiptConfirm, 'TickleName' => $TickleName, 'TickleTime' => $TickleTime, 'CTickleName' => $CTickleName, 'TickleMailSubject' => "$TickleMailSubject", 'reminder_task' => $reminder_task ,'reminder_task_name' => $reminder_task_name , 'TickleMailContent' => "$TickleMailContent", 'TickleMailFollowContent' => "$TickleMailFollowContent", 'EmailPriority' => "$EmailPriority", 'AttachOriginalMessage' => $AttachOriginalMessage, 'CCMe' => "$CCMe", QuickTickle => "$QuickTickle", RecurrencePattern => "$RecurrencePattern", DailyDays => "$DailyDays", WeeklyWeeks => "$WeeklyWeeks", WeekDays => "$WeekDays", EndAfter => "$EndAfter", 'FollowUp' => (($FollowUp != "") ? "Y" : "N"), 'AttachMessageFollow' => (($AttachMessageFollow != "") ? "Y" : "N"), EmailPriorityFollow => "$EmailPriorityFollow", 'CCMeFollow' => (($CCMeFollow != "") ? "Y" : "N"), 'DailyDaysFollow' => @intval($DailyDaysFollow), 'EndAfterFollow' => @intval($EndAfterFollow), TickleTimeFollow => "$TickleTimeFollow", 'custom_subject' => $TickleCustomSubject, 'delete_campaign_on_reply' => "$delete_campaign_on_reply", 'notify_when_reply_received' => "$notify_when_reply_received", 'do_not_track' => "$do_not_track", 'notify_campaign_deleted' => "$notify_campaign_deleted"), $sql_add), array("WHERE  TickleTrainID= ?", $TickleTrainID)); //'TickleID'='".$_SESSION['TickleID']."' and        
        $db->update('category', array('CategoryName' => $TickleName), array("WHERE  CategoryID= ?", $tickle['TickleContact']));
        updateFiles($TickleTrainID, "TAttach");
        updateTickleTasks($TickleTrainID);
    }
    
    /*if ($reload) {
        redirect("addtickle", "tid=" . $TickleTrainID);
    }
    if ($spamcheck == 'yes') {
        redirect('addtickle', 'tid=' . $TickleTrainID . '&action=Edit&spamcheck=yes');
    }
    
    redirect("tickle");*/
    
	$surl = '?';
	foreach(json_decode(base64_decode($_POST['qstr'])) as $key => $redirectUrl01)
	{
		if($key!='u'){ $surl .= $key.'='.$redirectUrl01.'&';} 
	}
	header("location:https://client.tickletrain.com/tickle/".substr($surl,0,-1)."#".$_POST['hashtag']);
}

if ($action == "AddTickleFollow" && $FollowTickleTrainID == 0 && @trim($_REQUEST['TickleTrainID']) != "") {

    if (isset($_REQUEST['CustomSubjectCheckboxForFollowUp']) && $_REQUEST['TextAreaCustomSubjectForFollowup'] != "") {
        $TickleCustomSubject = $_REQUEST['TextAreaCustomSubjectForFollowup'];
    }

    $MVal['TickleTrainID'] = $TickleTrainID;
    $MVal['TickleID'] = $_SESSION['TickleID'];
    $MVal['Status'] = 'Y';
    $MVal['custom_subject'] = $TickleCustomSubject;
    $MVal['CreatedDate'] = $CreatedDate;
    $MVal['ModifyDate'] = $ModifyDate;
    $MVal['TickleMailFollowContent'] = $TickleMailFollowContent;
    $MVal['AttachMessageFollow'] = $AttachMessageFollow;
    $MVal['EmailPriorityFollow'] = $EmailPriorityFollow;
    $MVal['CCMeFollow'] = $CCMeFollow;
    $MVal['DailyDaysFollow'] = $DailyDaysFollow;
    $MVal['EndAfterFollow'] = $EndAfterFollow;
    $MVal['TickleTimeFollow'] = $TickleTimeFollow;
    $MVal['NoWeekend'] = $NoWeekend;
    $MVal['TApprove'] = $TApprove;
    $MVal['TReceiptConfirm'] = $TReceiptConfirm;
    $idx = $db->insert('ticklefollow', $MVal);
    $FollowTickleTrainID = $db->last_insert_id();
    updateFiles($FollowTickleTrainID, "TAttachFollow", "ticklefollow");
    header("location:https://client.tickletrain.com/addtickle/?tid=$TickleTrainID&action=Edit");
    exit;
}

if ($action == "EditTickleFollow" && $FollowTickleTrainID > 0) {
    //die("Is that triggering ?--");
    if (isset($_REQUEST['CustomSubjectCheckboxForFollowUp']) && $_REQUEST['TextAreaCustomSubjectForFollowup'] != "") {
        $TickleCustomSubject = $_REQUEST['TextAreaCustomSubjectForFollowup'];
    } else {
        $TickleCustomSubject = "";
    }
    $MVal['TickleTrainID'] = $TickleTrainID;
    $MVal['TickleID'] = $_SESSION['TickleID'];
    $MVal['Status'] = 'Y';
    $MVal['custom_subject'] = $TickleCustomSubject;
    $MVal['CreatedDate'] = $CreatedDate;
    $MVal['ModifyDate'] = $ModifyDate;
    $MVal['TickleMailFollowContent'] = $TickleMailFollowContent;
    $MVal['AttachMessageFollow'] = $AttachMessageFollow;
    $MVal['EmailPriorityFollow'] = $EmailPriorityFollow;
    $MVal['CCMeFollow'] = $CCMeFollow;
    $MVal['DailyDaysFollow'] = $DailyDaysFollow;
    $MVal['EndAfterFollow'] = $EndAfterFollow;
    $MVal['TickleTimeFollow'] = $TickleTimeFollow;
    $MVal['NoWeekend'] = $NoWeekend;
    $MVal['TApprove'] = $TApprove;
    $MVal['TReceiptConfirm'] = $TReceiptConfirm;
    $db->update('ticklefollow', $MVal, "Where TickleID='" . $_SESSION['TickleID'] . "' and FollowTickleTrainID='$FollowTickleTrainID'");
    updateFiles($FollowTickleTrainID, "TAttachFollow", "ticklefollow");
    //header("location:https://client.tickletrain.com/addtickle/?tid=$TickleTrainID&action=Edit");
}

if ($action == "LoadTickleFollow") {
    $tid = @trim($_GET['tid']);
    $ftid = @intval($_GET['ftid']);
    //  COde to remove DEFAULT TEXT ISSU !!!!! added on 19th feb 2015
    $CheckForFollowUpQuery = mysqli_query($db->conn,"select FollowTickleTrainID from ticklefollow where FollowTickleTrainID='" . $ftid . "' and
         TickleTrainID='" . $tid . "' and TickleID='" . $_SESSION['TickleID'] . "'");
    if (mysqli_num_rows($CheckForFollowUpQuery) > 0) {
    //if($ftid>0)
    //End of the code added on 19th feb 2015
        //die("Am i here ??");
        $tickle = $db->select_row('ticklefollow', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and FollowTickleTrainID='$ftid'");
        $Files = $db->select_to_array('files', '', " where FileContext='ticklefollow' and FileParentID='" . $ftid . "' ORDER by FileID ASC");
        $TickleTime = date("h:i A", strtotime($tickle['TickleTimeFollow']));
        $time = $TickleTime;
        if ($tickle['TickleTimeFollow'] == "") {
            $time = "12:00 PM";
        }
        $tickle['TickleTimeFollow'] = $time;
        $aFiles = array();
        for ($i = 0; $i < count($Files); $i++) {
            $frow = $Files[$i];
            $fname = @trim($frow['FileName']);
            //if ($fname == "" || !file_exists(FULL_UPLOAD_FOLDER . $fname)) {
            //    continue;
           // }
            $frow['FileNameLink'] = rawurlencode($fname);
            $aFiles[] = $frow;
        }
        $tickle['Files'] = $aFiles;
        echo json_encode($tickle);
    } else {
        $tickle['Files'] = null;        
        $tickle['TickleMailFollowContent'] = '<p style="font-size: 11px;">Type a different email message here.  This email will be on schedule after the previous one has been sent.</p>
<p style="text-align: left; margin-top:-5px">
                <strong style="font-size: 12px; "><em>For example:</em></strong></p>
<p>
                <span style="font-family:verdana,geneva,sans-serif;"><span style="font-size:12px;">Hi [firstname], I was just checking on this invoice again.  Did you receive it?  Are there any questions I can answer?  Please let me know.  Thanks in advance!</span></span></p>
<p>
                <span style="font-family:verdana,geneva,sans-serif;"><span style="font-size:12px;">[signature]</span></span></p>
<hr />
<p>
                <span style="font-size: 12px; ">Tip! &nbsp;Use the </span><img alt="" src="/' . ROOT_FOLDER . 'images/fn.PNG" style="font-size: 12px; border:1px solid black " /><span style="font-size: 12px; ">&nbsp;First Name and&nbsp;</span><img alt="" src="/' . ROOT_FOLDER . 'images/sig.PNG" style="font-size: 12px; border:1px solid black " /><span style="font-size: 11px; ">&nbsp;Signature shortcuts to insert these fields. &nbsp;</span></p>
<p>
                <span style="font-size:11px;">Go ahead and delete all of this text and enter your message here.</span></p>';
        ;
        $user_signature = tablerow('tickleuser', 'signature,mail_type', array("WHERE UserName ='" . $_SESSION['UserName'] . "' and TickleID='" . $_SESSION['TickleID'] . "'"));
        if(($ftid=='0' || $ftid=='') && $user_signature['mail_type']=='text')
        {
            $tickle['TickleMailFollowContent'] = '';
        }    
        echo json_encode($tickle);
    }
    exit;
}

if ($action == "DeleteFollow") {
    $tid = @trim($_GET['tid']);
    $ftid = @intval($_GET['ftid']);
    if ($ftid > 0) {
        $db->delete("ticklefollow", " where FollowTickleTrainID='" . $ftid . "'");
        $db->delete("task", " where FollowTickleTrainID='" . $ftid . "'");
    }
    exit;
}
if ($action == "FollowList") {
    $OptionsRow['Y'] = "Yes";
    $OptionsRow['N'] = "No";
    $EmailPrioritys['1'] = "High";
    $EmailPrioritys['5'] = "Low";
    $EmailPrioritys['3'] = "Normal";

    $sLimit = "";
    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $sLimit = "LIMIT " . mysqli_real_escape_string($db->conn,$_GET['iDisplayStart']) . ", " .
                mysqli_real_escape_string($db->conn,$_GET['iDisplayLength']);
    }
    $aColumns = array('FollowTickleTrainID', 'TickleMailFollowContent', 'DailyDaysFollow', 'EndAfterFollow', 'TickleTimeFollow', 'AttachMessageFollow', 'EmailPriorityFollow', 'CCMeFollow', 'FollowTickleTrainID');
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "FollowTickleTrainID";
    /* DB table to use */
    $sTable = "ticklefollow";
    $sWhere = "WHERE TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and TickleTrainID='" . $_REQUEST['TickleTrainID'] . "'";
    $sOrder = "ORDER BY " . $sIndexColumn . " asc";
    if (intval($_GET['iSortCol_0'])) {
        $sOrder = "ORDER BY  ";
        for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
            if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . "
	                                " . mysqli_real_escape_string($db->conn,$_GET['sSortDir_' . $i]) . ", ";
            }
        }

        $sOrder = substr_replace($sOrder, "", -2);
    }

    if ($_GET['sSearch'] != "") {
        $sWhere.= " and (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $sWhere .= $aColumns[$i] . " LIKE '%" . mysqli_real_escape_string($db->conn,$_GET['sSearch']) . "%' OR ";
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    }

    for ($i = 0; $i < count($aColumns); $i++) {
        if ($_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
            if ($sWhere == "") {
                $sWhere = "WHERE ";
            } else {
                $sWhere .= " AND ";
            }
            $sWhere .= $aColumns[$i] . " LIKE '%" . mysqli_real_escape_string($db->conn,$_GET['sSearch_' . $i]) . "%' ";
        }
    }

    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "
	        SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
	        FROM   $sTable
	        $sWhere
	        $sOrder
	        $sLimit
	";
    $rResult = mysqli_query($db->conn,$sQuery) or die(mysqli_error($db->conn)); //, $gaSql['link']

    /* Data set length after filtering */
    $sQuery = "
	        SELECT FOUND_ROWS()
	";
    $rResultFilterTotal = mysqli_query($db->conn,$sQuery) or die(mysqli_error($db->conn)); //, $gaSql['link']
    $aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];

    /* Total data set length */
    $sQuery = "
	        SELECT COUNT(" . $sIndexColumn . ")
	        FROM   $sTable
	";
    $rResultTotal = mysqli_query($db->conn,$sQuery) or die(mysqli_error($db->conn)); //, $gaSql['link']
    $aResultTotal = mysqli_fetch_array($rResultTotal);
    $iTotal = $aResultTotal[0];


    /*
     * Output
     */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );

    while ($aRow = mysqli_fetch_array($db->conn,$rResult)) {
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {
            if ($aColumns[$i] == "FollowTickleTrainID") {
                if ($i == 0) {
                    $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : '<input type="checkbox" name="FollowIDArr[]" class="selectedCheckbox" value="' . $aRow['FollowTickleTrainID'] . '"/>';
                } else {
                    /* Special output formatting for 'version' column */
                    $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : "<a href=\"javascript:EditFollow(" . $aRow['FollowTickleTrainID'] . ");\">Edit</a> || <a href=\"javascript:DeleteFollow('" . $aRow['FollowTickleTrainID'] . "');\" onclick=\"javascript:return confirm('Are You Sure want to delete?');\">Delete</a>";
                }
            } else if ($aColumns[$i] == 'TickleMailFollowContent') {
                $row[] = RemoveBadChar($aRow[$aColumns[$i]]);
            } else if ($aColumns[$i] == 'EndAfterFollow') {
                $row[] = intval($aRow[$aColumns[$i]]) - 1;
            } else if ($aColumns[$i] == 'TickleTimeFollow') {
                $row[] = date("h:i A", strtotime($aRow[$aColumns[$i]]));
            } else if ($aColumns[$i] == 'AttachMessageFollow') {
                $row[] = $OptionsRow[$aRow[$aColumns[$i]]];
            } else if ($aColumns[$i] == 'AttachMessageFollow') {
                $row[] = $OptionsRow[$aRow[$aColumns[$i]]];
            } else if ($aColumns[$i] == 'EmailPriorityFollow') {
                $row[] = $EmailPrioritys[$aRow[$aColumns[$i]]];
            } else if ($aColumns[$i] == 'CCMeFollow') {
                $row[] = $OptionsRow[$aRow[$aColumns[$i]]];
            } else if ($aColumns[$i] != ' ') {
                /* General output */
                $row[] = $aRow[$aColumns[$i]];
            }
        }
        $output['aaData'][] = $row;
    }

    echo json_encode($output);
    exit;
}

function updateFiles($parentId, $fldName, $ctx = 'tickle') {
    global $db;
    $append_date = strtotime(date('Y-m-d H:i:s'));
    for ($i = 0; $i < count($_FILES[$fldName]['tmp_name']); $i++) {
        $tname = $_FILES[$fldName]['tmp_name'][$i];
        $name = $_FILES[$fldName]['name'][$i];
        $name = str_replace(' ', '_', $name);
        $name = $append_date.'__'.$name;
        if ($tname != '' && move_uploaded_file($tname, FULL_UPLOAD_FOLDER . $name)) {
            chmod(FULL_UPLOAD_FOLDER . $name, 0755);
            $db->insert('files', array('FileContext' => $ctx, 'FileParentID' => $parentId, 'FileName' => $name));
        }
    }
}

function updateTickleTasks($TickleTrainID) {
    global $db;
    $AllTasks = $db->select_to_array('task', '', " where TickleTrainID='$TickleTrainID' and Status='Y' order by TaskID");
    $TMail = array();
    foreach ($AllTasks as $trow) {
        if (!isset($TMail[$trow['MailID']])) {
            $TMail[$trow['MailID']] = $trow;
        }
    }
    $CTickle = $db->select_to_array('tickle', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and TickleTrainID='" . $TickleTrainID . "'");
    $FTickle = $db->select_to_array('ticklefollow', '', " where TickleTrainID='" . $TickleTrainID . "' order by FollowTickleTrainID asc");
    $Tfollow = array();
    foreach ($FTickle as $trow) {
        $Tfollow[$trow['FollowTickleTrainID']] = $trow;
    }

    $rst = $CTickle[0];
    $getservertz = date_default_timezone_get();
    date_default_timezone_set('Etc/GMT-0');
    foreach ($TMail as $MailID => $Task) {
        $TaskID = $Task['TaskID'];
        $Tasks = $db->select_to_array('task', '', " Where MailID='$MailID' and TickleID='" . $_SESSION['TickleID'] . "' and TaskID>='$TaskID' and Status='Y' order by TaskID");
        $cnt = count($Tasks);
        $cdate = strtotime($Tasks[0]['TaskGMDate']);

        for ($j = 0; $j < $cnt; $j++) {
            $NoWeekend = @trim($CTickle[0]['NoWeekend']);
            $dailyDays = @intval($CTickle[0]['DailyDays']);
            if (intval($Tasks[$j]['FollowTickleTrainID'])) {
                $ftickle = $Tfollow[intval($Tasks[$j]['FollowTickleTrainID'])];
                $NoWeekend = @trim($ftickle['NoWeekend']);
                $dailyDays = @intval($ftickle['DailyDaysFollow']);
            }
            if ($j > 0) {
                $cdate+=3600 * 24 * $dailyDays;
                $ttime = strtotime($Tasks[$j]['TaskGMDate']);
                $cdate = mktime(intval(gmdate("H", $ttime)), intval(gmdate("i", $ttime)), intval(gmdate("s", $ttime)), intval(gmdate("m", $cdate)), intval(gmdate("d", $cdate)), intval(gmdate("Y", $cdate)));
            }
            $dofweek = intval(gmdate('w', $cdate));
            while ($NoWeekend == 'Y' && ($dofweek == 0 || $dofweek == 6)) {
                $cdate+=3600 * 24;
                $dofweek = intval(gmdate('w', $cdate));
            }

            $nday = gmdate("Y-m-d H:i:s", $cdate);
            //date_default_timezone_set($getservertz);

            $iday = getlocaltime($nday, $Tasks[$j]['TimeZone']);
            mysqli_query($db->conn,"update task set TaskInitiateDate='" . $iday . "', TaskGMDate='" . $nday . "' where TaskID=" . $Tasks[$j]['TaskID']);
        }
    }//foreach
    date_default_timezone_set($getservertz);
}

function setDefaults1(&$filter_post) {
    include_once "includes/mailsettings_inc.php";
    $msystem = $GLOBALS['server_settings_params'][$filter_post['DMSystem']];
    if (is_array($msystem) && $msystem['isdefault']) {
        if (isset($msystem['server'])) {
            $filter_post['DMSmtp'] = $msystem['server'];
        }
        if (isset($msystem['port'])) {
            $filter_post['DMPort'] = $msystem['port'];
        }
        if (isset($msystem['encryption'])) {
            $filter_post['DMSecure'] = $msystem['encryption'];
        }
    }
}

?>
