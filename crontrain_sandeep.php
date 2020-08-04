<?php



include_once("includes/data.php");
include_once("includes/function/func.php");
ini_set('memory_limit', '-1');
define('HOME_FOLDER', GetHomeDir() . "/");
define('LOGS_FOLDER', HOME_FOLDER . "_logs/");
//echo mkdir( GetHomeDir() . "/mail/testingfolder3/"); exit();  

    // if(isset($_GET['test']))
    // {
    //     echo  getgmdate('2019-12-22 06:00:00', 'America/New_York');
    //     echo "<br>";
    //     echo date_default_timezone_get();
    //     echo "<br>";

    //     // echo  date('Y-m-d H:i:s',strtotime('2019-12-22 08:00:00'));
    //     // echo "<br>";

    //     die();
    // }


function create_logs($logData,$ttresponse){
    global $db;
    $logDate = date("Y-m-d H:i:s");//gmdate("Y-m-d H:i:s");
    $logDate01 = date("Y-m-d");
    $logDate01 = $logDate01.' 00:00:00';
    if(file_exists(__DIR__ . '/ticklelog/'.date('d-m-Y').'/crontrain.json'))
    {        
        $logJsonData = file_get_contents(__DIR__ . '/ticklelog/'.date('d-m-Y').'/crontrain.json');
        $todayLogData = json_decode($logJsonData,true);    
    }else {
        $todayLogData = array();
    }
    
    $data = array('TickleID'=>$logData['TickleID'],'MailID'=>$logData['MailID'],'ttrequest'=>'','type'=>'createcampaign','date'=>$logDate,'ttresponse'=>$ttresponse);
    
    $todayLogData[] = $data;
    if(!is_dir(__DIR__ . '/ticklelog/'.date('d-m-Y'))){
        mkdir(__DIR__ . '/ticklelog/'.date('d-m-Y'));    
    }
    $fp = fopen(__DIR__ . '/ticklelog/'.date('d-m-Y').'/crontrain.json', 'w');
    fwrite($fp, json_encode($todayLogData));
    fclose($fp);
    
}


function remove_special_characters_from_string($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
}

error_reporting(E_ERROR);
$RawTEmail = array();
$msgCount = 0;
$mbox = imap_open($ImapServerName, $ImapUserName, $ImapPassWord) or die(SendMail('ourinternet@outlook.com', 'noreply@tickletrain.com', 'Crictical Error', print_r(imap_errors())));
debug("mbox: " . $mbox);
$hdr = imap_check($mbox);


if ($hdr) {
    $msgCount = $hdr->Nmsgs;
    // $RecentMessage =  $hdr->Recent; // Added on 17/1/2014 (Actually it was fetching overview of all mails in the mailbox, so its for getting just recent messages)
    // $MessageIndex = $msgCount - $RecentMessage;
}

debug("imap check: " . $msgCount);

$MN = $msgCount;
$size = 0;
if ($msgCount) {
    $overview = imap_fetch_overview($mbox, "1:$MN", 0); // Modified on 17/1/2014 to get overview of just recent messages
    //print_r($overview);
    $overview = array_values(array_filter($overview, "unread")); // Changed after issue of repeated campaign's  13 April 2013 Issue was that we were getting all emails rather unread emails.  

    $size = sizeof($overview);
}
echo "msgs count: " . $size."<br>";

debug("msgs count: " . $size);
for ($i = $size - 1; $i >= 0; $i--) {
    $val = $overview[$i];
    $msg = $val->msgno;
    $headers = imap_fetchheader($mbox, $msg);
    //debug($headers);
    $headers = preg_replace('/X-Original-To:(\s*[0-9]+-){0,1}/i', 'X-Envelope-To:', $headers);

    $header = explode("\n", $headers);

    //$header = explode("\n", imap_fetchheader($mbox, $msg));
    // browse array for additional headers
    if (is_array($header) && count($header)) {
        $head = array();
        foreach ($header as $line) {
            // is line with additional header?
            if (preg_match("/^X-/i", $line)) {
                // separate name and value
                if (preg_match("/^([^:]*):(.*)/i", $line, $arg)) {
                    $head[trim($arg[1])] = @trim($arg[2]);
                }
            }
        }
    }

    $XEnvelopeTo = $head['X-Envelope-To'];
    $itsforcheck = decode_address($XEnvelopeTo);

    $checkfor['TickleTitle'] = trim($itsforcheck['TickleName']);
    $checkmailhere = trim(strtolower($itsforcheck['MailAddr']));
    $chaeknamehere = trim(str_replace("@tickletrain.com", "", $checkmailhere));
    //$chaeknamehere = trim(str_replace("@harpreetbedi.tk", "", $checkmailhere));
    $mysqls = mysqli_query($db->conn,"select * from tickleuser where UserName='$chaeknamehere'");
    $myrss = mysqli_fetch_assoc($mysqls);

    $allowed_compaign = $db->select_rows('Compaign', "Allowe_campaign", "where TickleID='$myrss[TickleID]'", ASSOC);
    foreach ($allowed_compaign as $allowcamp) {
        $campaignallowed = $allowcamp['Allowe_campaign'];
        $allowecampaign = $allowcamp['Allowe_campaign'];
    }

    $XOriginatingIP = $head['X-Originating-IP'];
    $XPriority = $head['X-Priority'];
    $MailHeader = MsgHeadersParse($headers);

    $savedirpath = HOME_FOLDER . 'attachment/' . str_replace("<", "", str_replace(">", "", $MailHeader['message_id'])) . '/';
    $message_body_raw = $headers . imap_body($mbox, $msg);
    $message_body_parsed = TextMsgParse($message_body_raw);

    $att_path = array();
    for ($aa = 0; $aa < count($message_body_parsed['attachments']); $aa++) {
       // if($message_body_parsed['attachments'][$aa]['content-disposition'] != 'inline'){
        $att_path[] = remove_special_characters_from_string($message_body_parsed['attachments'][$aa]['filename']);
      //}
    }
   
    //Start  Code to set time regarding to a particular Campaign . Set on 20_7_2013 
    $decode_addr = decode_address($XEnvelopeTo);
    //print_r($decode_addr);die();
   /* COde to unset date command for next campaign : added on 19th feb 2015 */
   if(isset($task_correct_date_format)){
        unset($task_correct_date_format);
   }
   // ENd of unset date command code  // 2019-10-29 01:_0 3

   $str = $decode_addr['Part2'];

    if (strpos($str,'date_') !== false) { // date_10242019_01_39_PM+quotes+sandeep@tickletrain.com
        $date_string = str_replace("date_","",$str);
        $task_month = substr($date_string,0,2);

        $arr__ =  ['N','1H','2H','3H','1D','2D','1W','2W','1M'];
        if(!in_array(strtoupper($task_month), $arr__)) {
            $task_date = substr($date_string,2,2);
            $task_year = substr($date_string,4,4);
            $hours = substr($date_string,9,2);
            $miutes = substr($date_string,12,2);
            $am = substr($date_string,15,2);
            $my_date = $task_year."-".$task_month."-".$task_date. " ".$hours.":".$miutes." ".$am;
            $task_correct_date_format = date('Y-m-d H:i:s',strtotime($my_date));
        }else{
            $time__ = get_date_and_time($task_month);
            $task_month =  strtoupper($task_month);

            if(in_array($task_month, ['N','1H','2H','3H'])) {
                $task_correct_date_format =  getlocaltime(date('Y-m-d H:i:s',$time__), $myrss['TimeZone']);
            }else{
                $task_correct_date_format = date('Y-m-d',$time__).' '.'12:00:00';
            }
        }
        //echo "task_correct_date_format = ".$task_correct_date_format; die;
    }
     /* COde to unset extension command for next campaign : added on 19th feb 2015 */
    if(isset($NextFollowupMessageId)){
    unset($NextFollowupMessageId);
    }
    if(isset($firstname_from_chrome_extension)){
    unset($firstname_from_chrome_extension);
    }
    if(isset($lastname_from_chrome_extension)){
    unset($lastname_from_chrome_extension);
    }
    if(isset($extension_mail_ticklefollow)){
        unset($extension_mail_ticklefollow);
    }
    // End of unset extension command code

    if (isset($decode_addr['Part3']) && isset($decode_addr['Part4']) && isset($decode_addr['Part5'])) {

        $time_from_extension = $decode_addr['Part5'] . ":" . $decode_addr['Part4'] . ":" . $decode_addr['Part3'];
        $NextFollowupMessageId = $decode_addr['Part9'];
        $firstname_from_chrome_extension = $decode_addr['Part7'];
        $lastname_from_chrome_extension = $decode_addr['Part6'];
        $approve_for_campaign = $decode_addr['Part8'];
        if ($approve_for_campaign == 'y') {
            $approve_for_campaign = 'Y';
        } else {
            $approve_for_campaign = 'N';
        }
        if (isset($decode_addr['Part10'])) {
            $extension_mail_ticklefollow = $decode_addr['Part10'];
            $DeleteTaskQuery = mysqli_query($db->conn,"delete from task where MailID='" . $extension_mail_ticklefollow . "'");
        }
    }

    if (isset($extension_mail_ticklefollow)) {
        $MailHeader['subject'] = trim(str_replace("Re:", "", $MailHeader['subject']));
        $MailHeader['subject'] = trim(str_replace("RE:", "", $MailHeader['subject']));
        $MailHeader['Subject'] = trim(str_replace("RE:", "", $MailHeader['Subject']));
        $MailHeader['Subject'] = trim(str_replace("Re:", "", $MailHeader['Subject']));
    }


    //End Code to set time regarding to a particular Campaign . Set on 20_7_2013       
    $data_m['TickleTitle'] = trim($decode_addr['TickleName']);
    $mailid = trim(strtolower($decode_addr['MailAddr']));
    $Tickle_Username = trim(str_replace("@tickletrain.com", "", $mailid));
    //$Tickle_Username = trim(str_replace("@harpreetbedi.tk", "", $mailid));
    $sqls = mysqli_query($db->conn,"select * from tickleuser where UserName='$Tickle_Username'");
    $rss = mysqli_fetch_assoc($sqls);

    if (!$rss) {
        debug("Tickle_Username '" . $Tickle_Username . "' not found. Email skipped");
        /* move="INBOX.Read";
          @imap_mail_move($mbox,$msg,$move);
          continue; */
        goto mailcontinue;
    }

    $userEmailId = $rss['EmailID'];
    $fromAddr = $MailHeader['sender'][0];
    $DefaultFromAddress = $MailHeader['from'][0]['email'];
    $secondary = false;
    //echo "plan".$rss['Plan'];
     //here change code
    if (strtolower($userEmailId) != strtolower($fromAddr['email']) && strtolower($userEmailId) != strtolower($DefaultFromAddress)) {
    
        //check addon status for multiple emails task 22-jan-2016   
        $secquery = mysqli_query($db->conn,"select * from secondaryEmail where EmailID='".$DefaultFromAddress."' and TickleID='".$myrss['TickleID']."'");
            $secarray = mysqli_fetch_assoc($secquery);

        $postUrl = "https://secure.tickletrain.com/get_addon_info.php";
        $postdata = array(
        'get_addon_status' => true,
        'addon_hosting_id' => $rss['addon_hosting_id']
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POST, count($postdata));
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        //print_r($response);
        //echo curl_error($ch);                         
        curl_close($ch);
        $status = json_decode($response,1);
        
       //check addon status for multiple emails task 22-jan-2016
        if($rss['Plan']==1 || $rss['email_addon']==''){ 
            debug("TickleUser email '" . $userEmailId . "' doesn't match from email " . $fromAddr['email'] . ". Email skipped");
            goto mailcontinue;
        }
        else{
            if(mysqli_num_rows($secquery==0)){
                debug("email '" . $DefaultFromAddress . "' doesn't match from email in secondatry Emails. Email skipped");
                    goto mailcontinue;
            }
            else{
                $fromAddr = $DefaultFromAddress;
                $secondary  = true;
            }
        }
    } else {
        $fromAddr = $DefaultFromAddress;
    }

    $data_m['TickleID'] = intval($rss['TickleID']);
    $TimeZone = $rss['TimeZone'];
    debug("Tickletitle: " . $data_m['TickleTitle']);
    debug("Tickle_Username: " . $Tickle_Username);
    //Mail Reply
    $checkreply = explode("-", $decode_addr['TickleName']);
    $CReply = @trim($checkreply[0]);
    $TaskID = @trim($checkreply[1]);



    //tickle
    $tickleTitle = trim(strtolower($data_m['TickleTitle']));
    $Unsubscribe = trim(strtolower($decode_addr['Unsubscribe']));
    $UnSubject = trim(preg_replace("/^(re|fwd|fw)[^:]*:/i", "", trim(strtolower($MailHeader['Subject']))));

    $Uto_emailid = (array) $MailHeader['to'];


    // die();
    //contact list
    $sql_cmail = mysqli_query($db->conn,"select * from contact_list where TickleID=" . $data_m['TickleID']);
    $contact_list = array();
    while ($row = mysqli_fetch_array($sql_cmail)) {
        $contact_list[$row['EmailID']] = $row;
    }
    mysqli_free_result($sql_cmail);

    $cIds = array(0);
    foreach ($Uto_emailid as $eml) {
        if (isset($contact_list[$eml['email']])) {
            $cIds[] = $contact_list[$eml['email']]['ContactID'];
        }
    }
    
    $isglobal = false;
    //global unsubscribe

    $globalcmd = array("unsubscribe", "resubscribe", "pause", "unpause");
    if (in_array($tickleTitle, $globalcmd) || preg_match('/^\d+$/', $tickleTitle)) {

        $isglobal = true;
        $usmail = mysqli_query($db->conn,"select * from user_mail where Subject like '%" . $UnSubject . "' and  TickleID='" . intval($rss['TickleID']) . "' and ContactID in (" . implode(",", $cIds) . ") and TickleTitleID!=''");
     
        while ($user_mail = mysqli_fetch_array($usmail)) {

            $UnMailID = $user_mail['MailID'];
            $chtask = mysqli_query($db->conn,"select task.*, tickle.TickleContact, tickle.CTickleName, tickle.TApprove from task inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) where MailID='$UnMailID' and task.Status='Y'");
            
            $rschtask = mysqli_fetch_array($chtask);
            if (@intval($rschtask['TaskID']) > 0) {
                //Unsubscribe All
                if ($tickleTitle == "resubscribe") {
                    $resub_TickleTitleID = $user_mail['TickleTitleID'];
                    $data_m['TickleTitleID'] = $resub_TickleTitleID;
                    $data_m['TickleTitle'] = trim($rschtask['CTickleName']);
                    $TickleContact = $rschtask['TickleContact'];
                }
                if ($tickleTitle == "pause") {
                    $update_task = "update task set Pause='Y' where TickleID='" . intval($rss['TickleID']) . "' and MailID='" . $UnMailID . "' and  Status='Y'";
                    mysqli_query($db->conn,$update_task);
                    continue;
                }
                if ($tickleTitle == "unpause") {
                    $update_task = "update task set Pause='N',Approve='Y' where TickleID='" . intval($rss['TickleID']) . "' and MailID='" . $UnMailID . "' and  Status='Y'";
                    mysqli_query($db->conn,$update_task);
                    continue;
                }


                if (preg_match("/^\d+$/", $tickleTitle)) {
                    // Code to fix update task command issues (command like days+username@tickletrain.com). Also update from current date with skipping weekend
                    $tickleTitle = $tickleTitle;
                    if (isset($NewTickleTitle)) {
                        unset($NewTickleTitle);
                    }
                    $GetTaskQuery = mysqli_query($db->conn,"select * from task where TickleID='" . $rss['TickleID'] . "' and MailID='" . $UnMailID . "'
                            and  Status='Y' order by TaskInitiateDate limit 1");
                    $GetTaskRow = mysqli_fetch_assoc($GetTaskQuery);
                    if (!isset($NewTickleTitle)) {
                        $CurrentDateTime = date("Y-m-d");
                        $TaskInititaeDate = date("Y-m-d", strtotime($GetTaskRow['TaskInitiateDate']));
                        $daylen = 60 * 60 * 24;
                        $DaYDifference = (strtotime($TaskInititaeDate) - strtotime($CurrentDateTime)) / $daylen;
                        $NewTickleTitle = $tickleTitle - $DaYDifference;
                    }
                    $TaskID = $GetTaskRow['TaskID'];
                    $TickleTrainID = $GetTaskRow['TickleTrainID'];
                    $MailID = $UnMailID;
                    $TickleID = $rss['TickleID'];
                    UpdateTaskFromBccCommand($TaskID, $NewTickleTitle, $MailID, $TickleID, $TickleTrainID);
                    continue;
                }
                $update_task = "update task set Status='N' where TickleID='" . intval($rss['TickleID']) . "' and MailID='" . $UnMailID . "' and  Status='Y'";
                mysqli_query($db->conn,$update_task);
            }
        }
        //die("Here ?");
        if ($UnSubject == "unsubscribe") {
            $update_task = "update task set Status='N' where  TickleID='" . intval($rss['TickleID']) . "' and  Status='Y'";
            mysqli_query($db->conn,$update_task);
        }
        if ($tickleTitle != "resubscribe") {
            goto mailcontinue;
        }
    }
    //global unsubscribe

    $sqlt = mysqli_query($db->conn,"select * from tickle where CTickleName='" . strtolower($data_m['TickleTitle']) . "' and TickleID='" . intval($rss['TickleID']) . "' and Status='Y'");
    $rst = mysqli_fetch_assoc($sqlt);

    $TickleContact = 0;
    if (!$rst) {
        //UserName
        $InvaliedTickleMessage = "";
        $InvaliedTickleMessage.= "We're sorry but the Tickle BCC address: \"$data_m[TickleTitle]+$rss[UserName]@tickletrain.com\" does not exist or is invalid. Your available Tickle addresses are below. To use, just place one of the email addresses below in the BCC field:<br/><br/>";
        $GetUserTickleQuery = mysqli_query($db->conn,"select `CTickleName` from `tickle` where TickleID='" . $rss['TickleID'] . "'") or die(mysqli_error($db->conn) . __LINE__);
        if (mysqli_num_rows($GetUserTickleQuery)) {
            while ($GetTickleUserRow = mysqli_fetch_assoc($GetUserTickleQuery)) {
                $InvaliedTickleMessage.= $GetTickleUserRow['CTickleName'] . "+" . $rss['UserName'] . "@tickletrain.com<br/>";
            }
        } else {
            $InvaliedTickleMessage.= "You currently have no Tickles. Please log into your dashboard and add at least 1 Tickle.<br/>";
        }
        $InvaliedTickleMessage.= '<br/>If you are modifying a Tickle, be sure you are using the correct format. You can get a list of "tweaks" by visiting the Support page after you <a href="https://client.tickletrain.com/login/">login</a>.';
        SendMail($userEmailId, 'noreply@tickletrain.com', 'Invalid tickle name', $InvaliedTickleMessage);

        debug("Tickle not found, email to '$userEmailId' has been sent");
        goto mailcontinue;
    }

    // $ARRRsTF = array();
    // $SqlTF = mysqli_query($db->conn,"select * from ticklefollow where TickleTrainID='" . $rst['TickleTrainID'] . "' and TickleID='" . $rst['TickleID'] . "' and Status='Y' order by FollowTickleTrainID asc");


    // while ($RsTF = mysqli_fetch_assoc($SqlTF)) {
    //     $ARRRsTF[] = $RsTF;
    // }

    //    pr($ARRRsTF); die;

    $TickleContact = @intval($rst['TickleContact']);
    $data_m['TickleTitleID'] = @trim($rst['TickleTrainID']);

    //local commands
    /* if (preg_match('/^\d+$/',$Unsubscribe)&&!$isglobal){
      debug("Local command for '".$data_m['TickleTitle']."': $Unsubscribe ($UnSubject)");
      $usmail = mysqli_query($db->conn,"select * from user_mail where trim(replace(replace(replace(replace( replace( lower( Subject ) , 're:', '' ) , 'fw:', '' ), '[', '' ), ']', '' ), 'fwd:', '' ))='" . $UnSubject . "' and  TickleID='" . intval($rss['TickleID']) . "' and ContactID in (".implode(",",$cIds).") and TickleTitleID='".$data_m['TickleTitleID']."'");
      debug("Found ".mysqli_num_rows($usmail)." mails for local command");
      while ($user_mail = mysqli_fetch_array($usmail)) {
      $UnMailID = $user_mail['MailID'];
      $update_task = "update task set TaskInitiateDate=date_add(TaskInitiateDate, interval $Unsubscribe day), TaskGMDate=date_add(TaskGMDate, interval $Unsubscribe day)  where TickleID='" . intval($rss['TickleID']) . "' and MailID='" . $UnMailID . "' and  Status='Y'";
      mysqli_query($db->conn,$update_task);
      debug($update_task);
      continue;
      }
      debug("Local command finished");
      goto mailcontinue;
      } */
      
//echo "<br>";
//echo $Unsubscribe;
    //local unsubscribe
   
    if ($Unsubscribe == "unsubscribe" && !$isglobal) {
        //pr($rst);
        //echo $Unsubscribe; die;
        //if ($UnSubject == "unsubscribe") {
            //Unsubscribe Particular Tickle
            if(empty($itsforcheck[0])) {
                $update_task = "update task set Status='N' where TickleTrainID='" . $rst['TickleTrainID'] . "' and TickleID='" . intval($rss['TickleID']) . "' and  Status='Y'";
                mysqli_query($db->conn,$update_task);
            }else{
                $update_task = "update task set Status='N' where MailID=".$itsforcheck[0];
                mysqli_query($db->conn,$update_task);
            }
            
       /* } else {
            $usmail = mysqli_query($db->conn,"select * from user_mail where trim(replace(replace(replace(replace( replace( lower( Subject ) , 're:', '' ) , 'fw:', '' ), '[', '' ), ']', '' ), 'fwd:', '' ))='" . $UnSubject . "' and  TickleID='" . intval($rss['TickleID']) . "' and ContactID in (" . implode(",", $cIds) . ") and TickleTitleID='" . $data_m['TickleTitleID'] . "'");
            debug("Found " . mysqli_num_rows($usmail) . " mails for local unsubscribe");
            while ($user_mail = mysqli_fetch_array($usmail)) {
                $UnMailID = $user_mail['MailID'];
                //Unsubscribe Particular Tickle mail
                $update_task = "update task set Status='N' where TickleTrainID='" . $rst['TickleTrainID'] . "' and MailID='" . $UnMailID . "' and TickleID='" . intval($rss['TickleID']) . "' and  Status='Y'";
                mysqli_query($db->conn,$update_task);
            }
        }*/
        debug("Local unsubscribe finished");
        goto mailcontinue;
    }
    //local unsubscribe
    //Mail Reply
    unset($decode_addr['MailAddr']);
    unset($decode_addr['TickleName']);
    asort($decode_addr);
    


    
    $data_m['TickleIntervals'] = implode(",", $decode_addr);
    $data_m['Date'] = $MailHeader['Date'];
    $data_m['Subject'] = remove_word($MailHeader['Subject'],"Re:") ;
    $data_m['message_id'] = $MailHeader['message_id'];
    $data_m['toaddress'] = $MailHeader['toaddress'];

   //02-may-2016 reply track
    //echo"<pre>";print_r($MailHeader);
    if($MailHeader['sender'][0]['host']=='outlook.com' || $MailHeader['sender'][0]['host']=='usmtgcapital.com'){
        $start = strpos($data_m['toaddress'],"<");
        $end = strpos($data_m['toaddress'],">");
        $limit = $end - $start;
        $data_m['toaddress'] = substr($data_m['toaddress'],$start,$limit+1);
        $data_m['toaddress'] = trim($data_m['toaddress']);
    }
   //02-may-2016 reply track


    $data_m['fromaddress'] = $MailHeader['fromaddress'];
    $data_m['reply_toaddress'] = $MailHeader['reply_toaddress'];
    $data_m['senderaddress'] = $MailHeader['senderaddress'];
    if($secondary==true){
        $data_m['senderaddress'] = $secarray['nickname'].' <'.$secarray['EmailID'].'>';
   }
   
    # This condition added by som on 04 aug 2016
    if(str_replace('tickletrain.com', '', $MailHeader['ccaddress'])==$MailHeader['ccaddress'])
   //if(str_replace('harpreetbedi.tk', '', $MailHeader['ccaddress'])==$MailHeader['ccaddress'])
    {        
        $data_m['ccaddress'] = $MailHeader['ccaddress'];
    }
    else{
        $data_m['ccaddress'] = '';
    }
    
    
    $data_m['Message'] = $message_body_parsed['text'];
    $data_m['MessageHtml'] = $message_body_parsed['html'];

    $data_m['MessageRaw'] = $message_body_raw;
    $data_m['attachments'] = implode(",", $att_path);
    $data_m['XEnvelopeTo'] = $XEnvelopeTo;
    $data_m['XOriginatingIP'] = $XOriginatingIP;
    $data_m['MailHeader'] = $headers;
    $data_m['XPriority'] = $XPriority;
//        
//        echo '<pre>';
//        print_r($MailHeader);
//        echo '</pre>';
//        die();
//

    if(isset($overlimitemail)){
    unset($overlimitemail); // COde added on 19th frb 2015 to fix over limits emails going to wrong users
    }

    $frommail_address = (array) ($MailHeader['sender']);
    $mytickleid = $myrss['TickleID'];
    $currentcampaign = countcurrentcampaigns($mytickleid);
    $remainingcampaigns = $allowecampaign - $currentcampaign;
    $newcampaigns = count($MailHeader['to']);
    $campaignforoverlimit = $newcampaigns - $remainingcampaigns;
    // die('bvcbvc');
    if ($campaignforoverlimit > 0) {
        $reversemailheader = array_reverse($MailHeader['to']);
        for ($z = 0; $z < $campaignforoverlimit; $z++) {
            $overlimitemail[$z] = $reversemailheader[$z]['email'];
            $overlimittickle[$z] = $data_m[TickleTitle];
            $overlimitsubject[$z] = $MailHeader['Subject'];
        }
    }

    $query = mysqli_query($db->conn,"select count(*) as 'c' from contact_list where TickleID = '" . $data_m['TickleID'] . "'");
    $row = mysqli_fetch_assoc($query);

    foreach ($frommail_address as $ind => $val) {

        if (isset($contact_list[$val['email']])) {
            $frommail_address[$ind]['contactid'] = $contact_list[$val['email']]['ContactID'];
        } else {
            mysqli_query($db->conn,"insert into contact_list (EmailID, TickleID, Status) values ('" . $val['email'] . "','" . $data_m['TickleID'] . "','Y')");
            $frommail_address[$ind]['contactid'] = mysqli_insert_id($db->conn);
        }
        if ($val['personal'] != '') {
            $arr = explode(" ", $val['personal']);
            $fname = trim(array_shift($arr));
            $lname = trim(implode(" ", $arr));

            if (strpos($fname,',') !== false) {
                     $f = $fname;
                     $fname = str_replace(",","",$lname);
                     $lname = str_replace(",","",$f);
                }

          // mysqli_query($db->conn,"update contact_list set FirstName='" . mysqli_real_escape_string($db->conn,$fname) . "', LastName='" . mysqli_real_escape_string($db->conn,$lname) . "' where ContactID=" . $frommail_address[$ind]['contactid']);
        }
        mysqli_query($db->conn,"delete from category_contact_list where ContactID=" . $frommail_address[$ind]['contactid'] . " and CategoryID=$TickleContact");
        mysqli_query($db->conn,"insert into category_contact_list (ContactID, CategoryID) values (" . $frommail_address[$ind]['contactid'] . ",$TickleContact)");
    }

    $sql_cmail = mysqli_query($db->conn,"select * from contact_list where TickleID=" . $data_m['TickleID']);
    $contact_list = array();

    while ($row = mysqli_fetch_array($sql_cmail)) {
        $contact_list[$row['EmailID']] = $row;
    }
    mysqli_free_result($sql_cmail);


    $ccmail_address = (array) ($MailHeader['cc']);
    foreach ($ccmail_address as $ind => $val) {
        if (isset($contact_list[$val['email']])) {
            $ccmail_address[$ind]['contactid'] = $contact_list[$val['email']]['ContactID'];
        } else {
            mysqli_query($db->conn,"insert into contact_list (EmailID, TickleID, Status) values ('" . $val['email'] . "','" . $data_m['TickleID'] . "','Y')");
            $ccmail_address[$ind]['contactid'] = mysqli_insert_id($db->conn);
        }
        if ($val['personal'] != '') {
            $arr = explode(" ", $val['personal']);
            $fname = trim(array_shift($arr));
            $lname = trim(implode(" ", $arr));

            if (strpos($fname,',') !== false) {
                     $f = $fname;
                     $fname = str_replace(",","",$lname);
                     $lname = str_replace(",","",$f);
                }

           // mysqli_query($db->conn,"update contact_list set FirstName='" . mysqli_real_escape_string($db->conn,$fname) . "', LastName='" . mysqli_real_escape_string($db->conn,$lname) . "' where ContactID=" . $ccmail_address[$ind]['contactid']);
        }
        mysqli_query($db->conn,"delete from category_contact_list where ContactID=" . $ccmail_address[$ind]['contactid'] . " and CategoryID=$TickleContact");
        mysqli_query($db->conn,"insert into category_contact_list (ContactID, CategoryID) values (" . $ccmail_address[$ind]['contactid'] . ",$TickleContact)");
    }

    $sql_cmail = mysqli_query($db->conn,"select * from contact_list where TickleID=" . $data_m['TickleID']);
    $contact_list = array();
    while ($row = mysqli_fetch_array($sql_cmail)) {
        $contact_list[$row['EmailID']] = $row;
    }
    mysqli_free_result($sql_cmail);

    $tomail_address = (array) ($MailHeader['to']);
    foreach ($tomail_address as $ind => $val) {
        if (isset($contact_list[$val['email']])) {
            $tomail_address[$ind]['contactid'] = $contact_list[$val['email']]['ContactID'];
        } else {
            mysqli_query($db->conn,"insert into contact_list (EmailID, TickleID, Status) values ('" . $val['email'] . "','" . $data_m['TickleID'] . "','Y')");
            $tomail_address[$ind]['contactid'] = mysqli_insert_id($db->conn);
        }
        if ($val['personal'] != $val['email']) {
            if ($val['personal'] != '') {
                $arr = explode(" ", $val['personal']);
                $fname = trim(array_shift($arr));
                $lname = trim(implode(" ", $arr));

                 if (strpos($fname,',') !== false) {
                     $f = $fname;
                     $fname = str_replace(",","",$lname);
                     $lname = str_replace(",","",$f);
                }

                //mysqli_query($db->conn,"update contact_list set FirstName='" . mysqli_real_escape_string($db->conn,$fname) . "', LastName='" . mysqli_real_escape_string($db->conn,$lname) . "' where ContactID=" . $tomail_address[$ind]['contactid']);
            }
        }
        mysqli_query($db->conn,"delete from category_contact_list where ContactID=" . $tomail_address[$ind]['contactid'] . " and CategoryID=$TickleContact");
        mysqli_query($db->conn,"insert into category_contact_list (ContactID, CategoryID) values (" . $tomail_address[$ind]['contactid'] . ",$TickleContact)");
    }


    // if (isset($firstname_from_chrome_extension) && isset($lastname_from_chrome_extension) && $firstname_from_chrome_extension != "nodataavailable" && $lastname_from_chrome_extension != "nodataavailable") {
    //     $update_contact_query = mysqli_query($db->conn,"update contact_list set FirstName='" . mysqli_real_escape_string($db->conn, ucfirst($firstname_from_chrome_extension)) . "',LastName='" . mysqli_real_escape_string($db->conn, ucfirst($lastname_from_chrome_extension)) . "' where EmailID='" . $MailHeader['to'][0]['email'] . "' and TickleID='" . $data_m['TickleID'] . "'");
    // }



    $PRmailID = 0;
    debug("Found " . count($tomail_address) . " 'to' addresses");

    for ($ind = 0; $ind < count($tomail_address); $ind++) {
    
        $currentcampaign = countcurrentcampaigns($data_m['TickleID']);
        $remainingcampaigns = $allowecampaign - $currentcampaign;
        //die('bcvbcvbcv');
        $logData = array();
        $logData['TickleID'] = $data_m['TickleID'];
        $logData['mail_data'] = $data_m;
        if ($remainingcampaigns > 0) {
        //echo"here now";
            $data_m['ContactID'] = $tomail_address[$ind]['contactid'];
            if ($ind > 0) {
                debug("Parent mail is $PRmailID");
                $data_m['ParentID'] = $PRmailID;
            }

            $insert_val = "";
    
            foreach ($data_m as $k => $v) {
                $insert_val.="$k='" . mysqli_real_escape_string($db->conn, $v) . "' ,";
            }
            $insert_val.=",";
            $insert_val = str_replace(",,", "", $insert_val);

            $sql_in = "insert into user_mail set $insert_val";
            mysqli_query($db->conn,$sql_in);
            $Rmailid = mysqli_insert_id($db->conn);
            $MailID = $Rmailid;
            $logData['MailID'] = $MailID;
            
            //Save Raw Mail
            $RelateiveMailPath = "mail/" . gmdate("Ymd") . "/";

            $Rawmail = HOME_FOLDER . $RelateiveMailPath . "$Rmailid.txt";
            $Rawmaildir = HOME_FOLDER . $RelateiveMailPath;
            if (!is_dir($Rawmaildir)) {
                @mkdir($Rawmaildir, 0777);
                @chmod($Rawmaildir, 0777, true);
            }

            imap_savebody($mbox, $Rawmail, $msg);
            $FullMessage = $data_m['MessageRaw'];
            if (!file_exists($Rawmail)) {
                file_put_contents($Rawmail, $FullMessage);
            }
            
            $logData['attachments'] = count($message_body_parsed['attachments']);
            if (count($message_body_parsed['attachments']) != 0) {
                echo ("Found " . count($message_body_parsed['attachments']) . " attachments");
                @mkdir($Rawmaildir . $Rmailid . "/", 0777);
                @chmod($Rawmaildir . $Rmailid . "/", 0777, true);
                debug("Created '" . $Rawmaildir . $Rmailid . "/" . "'");
                for ($aa = 0; $aa < count($message_body_parsed['attachments']); $aa++) {
                    file_put_contents($Rawmaildir . $Rmailid . "/" . remove_special_characters_from_string($message_body_parsed['attachments'][$aa]['filename']), $message_body_parsed['attachments'][$aa]['content']);
                }
            }
           
            $logData['images'] = count($message_body_parsed['images']);
            if (count($message_body_parsed['images']) != 0) {
                debug("Found " . count($message_body_parsed['images']) . " images");
                @mkdir($Rawmaildir . $Rmailid . "/", 0777);
                @chmod($Rawmaildir . $Rmailid . "/", 0777, true);
                debug("Created '" . $Rawmaildir . $Rmailid . "/" . "'");
                foreach ($message_body_parsed['images'] as $cid => $fl) {
                    file_put_contents($Rawmaildir . $Rmailid . "/" . $fl['filename'], $fl['content']);
                    $image_url = "/" . $RootFolder . $RelateiveMailPath . $Rmailid . "/" . $fl['filename'];
                    //add by raju
                    $firstnameQuery = mysqli_query($db->conn,"select FirstName from tickleuser where TickleID='".$data_m['TickleID']."'");
                    $firstnameRow = mysqli_fetch_assoc($firstnameQuery);
                    file_put_contents( GetHomeDir() . "/upload-files/".$firstnameRow['FirstName']."/".$fl['filename'], $fl['content']);
                    $sql_files = mysqli_query($db->conn,"INSERT INTO files (FileName,FileContext,FileParentID) VALUES('".$fl['filename']."','tickle','".$data_m['TickleID']."')");
                    //      
                            debug("update user_mail set MessageHtml=replace(MessageHtml,'cid:$cid','" . $image_url . "') where MailID='$Rmailid'");
                            mysqli_query($db->conn,"update user_mail set MessageHtml=replace(MessageHtml,'cid:$cid','" . $image_url . "') where MailID='$Rmailid'");
                }
            }

            mysqli_query($db->conn,"update user_mail set RawPath='$Rawmail', toaddress='".$tomail_address[$ind]['email']."' where MailID='$Rmailid'");
            $logData['toaddress'] = $tomail_address[$ind]['email']; 
            $logData['RawPath'] = $Rawmail; 
            // debug("Rawmail: " . $Rawmail);
            if ($ind == 0) {
                $PRmailID = $Rmailid;
            }
            
            if ($tickleTitle != "unsubscribe" && $Unsubscribe != "unsubscribe") {
                debug("Task creation for " . $data_m['TickleID']);
            
                $task['MailID'] = $MailID;
                $task['TaskCretedDate'] = date("Y-m-d H:i:s");
                $task['TickleID'] = $data_m['TickleID'];
                $task['TickleTrainID'] = $data_m['TickleTitleID'];
                $logData['TickleTitleID'] = $data_m['TickleTitleID'];
                $task['Status'] = "Y";
                $task['TimeZone'] = $TimeZone;


                if ($CReply != "reply") {

                   // $getservertz = date_default_timezone_get();
                   // date_default_timezone_set(gettimezone($TimeZone));

                    //Task Creation
                    $task_ck = $rst['QuickTickle'];                    
                    $task_RP = $rst['RecurrencePattern'];
                    $task_DailyDays = $rst['DailyDays'];
                    $task_WeeklyWeeks = $rst['WeeklyWeeks'];
                    $task_WeekDays = $rst['WeekDays'];
                    $task_EndAfter = $rst['EndAfter'];
                    //$TickleMailFollowContent=$rst['TickleMailFollowContent'];
                    $TickleTime = $rst['TickleTime'];
                    $FollowUp = $rst['FollowUp'];

                    $HasFollowup = 0;

                    $Intervals = array();

                    $Task_first['FollowTickleTrainID'] = 0;
                    $Task_first['TickleTrainID'] = $rst['TickleTrainID'];
                    $Task_first['TickleID'] = $rst['TickleID'];
                    $Task_first['Status'] = 'Y';
                    $Task_first['TickleMailFollowContent'] = $rst['TickleMailContent'];
                    $Task_first['AttachMessageFollow'] = $rst['AttachOriginalMessage'];
                    $Task_first['EmailPriorityFollow'] = $rst['EmailPriority'];
                    $Task_first['CCMeFollow'] = $rst['CCMe'];
                    $Task_first['DailyDays'] = $rst['DailyDays'];
                    $Task_first['EndAfter'] = $rst['EndAfter'];
                    $Task_first['EndAfter'] = $rst['EndAfter'];
                    //Start Code modify on 20_7_2013 (Regarding time for chrome extension)
                    $Task_first['TickleTime'] = $rst['TickleTime'];
                    //End Code modify on 20_7_2013 (Regarding time for chrome extension)
                    $Task_first['MailID'] = $MailID;
                    $Task_first['TimeZone'] = $TimeZone;
                    //skip weekend
                    $Task_first['NoWeekend'] = $rst['NoWeekend'];
                    //skip weekend

                    $ARRRsTF = array();
                   // $ARRRsTF[] = $Task_ARR;
                    $empty_key = null;


                    $wds="select * from ticklefollow where TickleTrainID='" . $rst['TickleTrainID'] . "' and TickleID='" . $rst['TickleID'] . "' and Status='Y' ";

                    $updated_follow_ = mysqli_query($db->conn,"select * from updated_follow_ids where tickle='" . $rst['TickleName'] . "' and user_id='" . $rst['TickleID'] . "' and status='unread' order by id desc");

                    if(mysqli_num_rows($updated_follow_) > 0){

                        $updated_follow = mysqli_fetch_array($updated_follow_);
                        $updated_follow_ids =  explode(',', $updated_follow['updated_follow_ids']);

                        if (($empty_key = array_search('empty', $updated_follow_ids)) !== false) {
                            unset($updated_follow_ids[$empty_key]);
                        }

                        $wds.=" ORDER BY (CASE ";
                        foreach ($updated_follow_ids as $key => $f_ids) {
                            $wds.=" WHEN FollowTickleTrainID = ".$f_ids." THEN ".$key;
                        }
                        $wds.=" END)";
                        // mysqli_query($db->conn,"delete from updated_follow_ids where id=".$updated_follow['id']);
                        mysqli_query($db->conn,"update updated_follow_ids set status='read' where id=".$updated_follow['id']);

                    }else{
                        $wds.=" order by FollowTickleTrainID asc";
                        $ARRRsTF[] = $Task_first;
                    }

                    $SqlTF = mysqli_query($db->conn,$wds);

                    while ($RsTF = mysqli_fetch_array($SqlTF)) {

                        $Task_ARR['FollowTickleTrainID'] = $RsTF['FollowTickleTrainID'];
                        $Task_ARR['TickleTrainID'] = $RsTF['TickleTrainID'];
                        $Task_ARR['TickleID'] = $RsTF['TickleID'];
                        $Task_ARR['Status'] = 'Y';
                        $Task_ARR['Pause'] = (!empty(trim($RsTF['TApprove'])) ? $RsTF['TApprove']:'N');
                        $Task_ARR['TickleMailFollowContent'] = $RsTF['TickleMailFollowContent'];
                        $Task_ARR['AttachMessageFollow'] = $RsTF['AttachMessageFollow'];
                        $Task_ARR['EmailPriorityFollow'] = $RsTF['EmailPriorityFollow'];
                        $Task_ARR['CCMeFollow'] = $RsTF['CCMeFollow'];
                        $Task_ARR['DailyDays'] = $RsTF['DailyDaysFollow'];
                        $Task_ARR['EndAfter'] = $RsTF['EndAfterFollow'];
                        //Start Code modify on 20_7_2013 (Regarding time for chrome extension)
                        $Task_ARR['TickleTime'] = $RsTF['TickleTimeFollow'];
                        // End Code modifi on 20_7_2013 (Regarding time for chrome extension)
                        //$Task_ARR['TickleTime'] = $RsTF['TickleTimeFollow'];
                        $Task_ARR['MailID'] = $MailID;
                        $Task_ARR['TimeZone'] = $TimeZone;
                        $Task_ARR['NoWeekend'] = $RsTF['NoWeekend'];
                        $ARRRsTF[] = $Task_ARR;
                    }
                   

                    if($empty_key != null && $empty_key != false ){
                        //echo 'empty =  '.$empty_key; 
                        array_splice($ARRRsTF, $empty_key, 0, [$Task_first]);
                    }


                    //Followup
                    ob_start();
                    $startday = 0;
                    if (/* $tickleTitle == "resubscribe" && */@intval($Unsubscribe) > 0) {
                        $startday = intval($Unsubscribe);
                        debug($tickleTitle . ": " . $startday);
                    }
                    //echo "calculate _days";
                    $Result = CalculateDays($ARRRsTF,$task_correct_date_format, $startday);
                  
                    // echo "<pre>";
                    // print_r($Result);
                    // die;
            
                    $total_task = 0;
                    
                    foreach ($Result as $key => $value) {
                        if (count($value['Intervals']) > 0) {
                            //Start Insert Task
                            $task['MailID'] = $value['MailID'];
                            $task['TaskCretedDate'] = date("Y-m-d H:i:s");
                            $task['TickleID'] = $value['TickleID'];
                            $task['TickleTrainID'] = $value['TickleTrainID'];
                            $task['Status'] = "Y";
                            $task['Pause'] = (isset($value['Pause']) && !empty(trim($value['Pause'])) ? $value['Pause']:'N');
                            $task['TimeZone'] = $value['TimeZone'];
                            $task['FollowTickleTrainID'] = $value['FollowTickleTrainID'];
                            $TIntervals = $value['Intervals'];
     
                            if($secondary==true){
                                $task['secondaryEmailId'] = $secarray['id'];
                            }

                            foreach ($TIntervals as $TKint => $TVint) {

                                $task['TaskInitiateDate'] = $TVint;
                                $task['TaskGMDate'] = getgmdate($task['TaskInitiateDate'], $task['TimeZone']);
                                
                                $task['DateID'] = "0";
                                $insert_tval = "";
                                foreach ($task as $k => $v) {
                                    $insert_tval.="$k='$v' ,";
                                }
                                $insert_tval.=",";
                                $insert_tval = str_replace(",,", "", $insert_tval);
                                $sql_in = "insert into task set $insert_tval";
                                mysqli_query($db->conn,$sql_in) or die(mysqli_error($db->conn) . __LINE__) ;
                                $total_task++;
                            }
                        }
                    }//foreach
                    $logData['total_task'] = $total_task;
                    $dbgmsg = ob_get_contents();
                    //debug($dbgmsg);
                    ob_end_clean();
                    date_default_timezone_set($getservertz);
                }//Task Creating if
            
            }
            
            $ttresponse = 'Campaign created successfully.';

            echo $ttresponse; 

            create_logs($logData,$ttresponse);
          
            /* Code start to set next follow-up message with new campaign created from extension  */

            $approve_for_campaign = strtoupper($approve_for_campaign);
            if (isset($NextFollowupMessageId) && $NextFollowupMessageId != "") {

                if ($approve_for_campaign == 'Y') {
                    $pause = 'Y';
                    $approve = 'Y';
                } else {
                    $approve = 'Y';
                    $pause = 'N';
                }

                $set_next_task_query = mysqli_query($db->conn,"select * from task where MailID='" . $MailID . "' and Status!='N' order by TaskInitiateDate") or die(mysqli_error($db->conn) . __LINE__);
                while ($set_next_task_row = mysqli_fetch_assoc($set_next_task_query)) {
                    
                    $TaskInitiateTime = date("H:i:s", strtotime($set_next_task_row['TaskInitiateDate']));
                    
                    if ($set_next_task_row['FollowTickleTrainID'] >= $NextFollowupMessageId) {

                        if (!isset($iqw)) {
                            $iqw = 1;
                        }
                        if ($iqw == 1) {
                            $date = date("Y-m-d");
                            $currentDate = strtotime($date);
                            $futureDate = $currentDate + (24 * $decode_addr['Part2'] * 60 * 60);
                            $formatDate = date("Y-m-d", $futureDate);
                            $taskinitialtedate = $formatDate . " " . $time_from_extension;
                            $taskgmdate = getgmdate($taskinitialtedate,$set_next_task_row['TimeZone']);

                            if ($set_next_task_row['FollowTickleTrainID'] == 0):
                            $GetTickleInfoQuery = mysqli_query($db->conn,"select NoWeekend from tickle  where TickleTrainID='$set_next_task_row[TickleTrainID]' and TickleID='$set_next_task_row[TickleID]' and Status='Y'");
                            $GetTickleInfoRow = mysqli_fetch_assoc($GetTickleInfoQuery);
                            $NoWeekEnd1 = $GetTickleInfoRow['NoWeekend'];
                       else:
                            $GetTickleFollowInfoQuery = mysqli_query($db->conn,"select NoWeekend from ticklefollow where FollowTickleTrainID='$set_next_task_row[FollowTickleTrainID]' and TickleID='$set_next_task_row[TickleID]' and Status='Y'");
                             $GetTickleFollowInfoRow = mysqli_fetch_assoc($GetTickleFollowInfoQuery);
                             $NoWeekEnd1 = $GetTickleFollowInfoRow['NoWeekend'];
                        endif;

                            if($NoWeekEnd1 == 'Y'){

                           if(date("N",  strtotime($taskgmdate)) == 7):
                            $taskgmdate = date('Y-m-d H:i:s', strtotime($taskgmdate . ' + 1 day'));
                            elseif(date("N",  strtotime($taskgmdate)) == 6):
                            $taskgmdate = date('Y-m-d H:i:s', strtotime($taskgmdate . ' + 2 day'));
                            endif;
                          }

                          $taskinitialtedate = getlocaltime($taskgmdate, $set_next_task_row['TimeZone']);
                          $formatDate = date("Y-m-d", strtotime($taskinitialtedate));
                          $update_task_query = mysqli_query($db->conn,"update task set TaskInitiateDate='" . $taskinitialtedate . "',TaskGMDate='" . $taskgmdate . "',Approve='" . $approve . "',Pause = '" . $pause . "',Status='Y' where TaskID='" . $set_next_task_row['TaskID'] . "'") or die(mysqli_error($db->conn) . __LINE__);
                          
                          debug("Update Campaign Time Query 1 update task set TaskInitiateDate='" . $taskinitialtedate . "',TaskGMDate='" . $taskgmdate . "',Approve='" . $approve . "',Pause = '" . $pause . "',Status='Y' where TaskID='" . $set_next_task_row['TaskID'] . "'");

                    } else {

                         //echo $formatDate.'<br/>';

                            if ($set_next_task_row['FollowTickleTrainID'] == 0) {
                                $tickle_query = mysqli_query($db->conn,"select DailyDays,TickleTimeFollow from tickle where TickleTrainID ='" . $set_next_task_row['TickleTrainID'] . "' and TickleID='" . $set_next_task_row['TickleID'] . "'") or die(mysqli_error($db->conn) . __LINE__);
                                $tickle_row = mysqli_fetch_assoc($tickle_query);
                                $DailyDaysFollow = $tickle_row['DailyDays'];
                                $tickletimefollow = $tickle_row['TickleTimeFollow'];
                            } else {
                                $ticklefollow_date_query = mysqli_query($db->conn,"select DailyDaysFollow,TickleTimeFollow from ticklefollow where FollowTickleTrainID = '" . $set_next_task_row['FollowTickleTrainID'] . "'") or die(mysqli_error($db->conn) . __LINE__);
                                $ticklefollow_date_query_row = mysqli_fetch_assoc($ticklefollow_date_query);
                                $DailyDaysFollow = $ticklefollow_date_query_row['DailyDaysFollow'];
                                $tickletimefollow = $ticklefollow_date_query_row['TickleTimeFollow'];
                            }
                            $date = $formatDate;
                            $currentDate = strtotime($date);
                            $futureDate = $currentDate + (24 * $DailyDaysFollow * 60 * 60);
                            $formatDate = date("Y-m-d", $futureDate);
                            $taskinitialtedate = $formatDate . " " . $TaskInitiateTime;
                            $taskgmdate = getgmdate($taskinitialtedate,$set_next_task_row['TimeZone']);

                             if ($set_next_task_row['FollowTickleTrainID'] == 0):
                            $GetTickleInfoQuery = mysqli_query($db->conn,"select NoWeekend from tickle  where TickleTrainID='$set_next_task_row[TickleTrainID]' and TickleID='$set_next_task_row[TickleID]' and Status='Y'");
                            $GetTickleInfoRow = mysqli_fetch_assoc($GetTickleInfoQuery);
                            $NoWeekEnd1 = $GetTickleInfoRow['NoWeekend'];
                       else:
                            $GetTickleFollowInfoQuery = mysqli_query($db->conn,"select NoWeekend from ticklefollow where FollowTickleTrainID='" . $set_next_task_row['FollowTickleTrainID'] . "' and TickleID='$set_next_task_row[TickleID]' and Status='Y'");
                             $GetTickleFollowInfoRow = mysqli_fetch_assoc($GetTickleFollowInfoQuery);
                             $NoWeekEnd1 = $GetTickleFollowInfoRow['NoWeekend'];
                        endif;

                            if($NoWeekEnd1 == 'Y'){
                            if(date("N",  strtotime($taskgmdate)) == 7):
                            $taskgmdate = date('Y-m-d H:i:s', strtotime($taskgmdate . ' + 1 day'));
                            elseif(date("N",  strtotime($taskgmdate)) == 6):
                            $taskgmdate = date('Y-m-d H:i:s', strtotime($taskgmdate . ' + 2 day'));
                            endif;
                          }

                            $taskinitialtedate = getlocaltime($taskgmdate, $set_next_task_row['TimeZone']);
                            $formatDate = date("Y-m-d", strtotime($taskinitialtedate));
                            
                           // echo $taskinitialtedate.'<br/>';
                          //  echo $formatDate.'<br/>';

                            $update_task_query = mysqli_query($db->conn,"update task set TaskInitiateDate='" . $taskinitialtedate . "',TaskGMDate='" . $taskgmdate . "',Status='Y' where TaskID='" . $set_next_task_row['TaskID'] . "'") or die(mysqli_error($db->conn) . __LINE__);
                            debug("Update Campaign Time Query 2 update task set TaskInitiateDate='" . $taskinitialtedate . "',TaskGMDate='" . $taskgmdate . "',Status='Y' where TaskID='" . $set_next_task_row['TaskID'] . "'");
                        }
                        $iqw++;
                    } else {
                        $CurrentDateTime = date('Y-m-d H:i:s');
                        $update_task_query = mysqli_query($db->conn,"update task set Status = 'S',TaskInitiateDate='" . $CurrentDateTime . "',TaskGMDate='" . $CurrentDateTime . "' where TaskID = '" . $set_next_task_row['TaskID'] . "'") or die(mysqli_error($db->conn) . __LINE__);
                        debug("Update Campaign Time Query 3 update task set Status = 'S',TaskInitiateDate='" . $CurrentDateTime . "',TaskGMDate='" . $CurrentDateTime . "' where TaskID = '" . $set_next_task_row['TaskID'] . "'");
                    }
                }
                unset($iqw);
             }
            // elseif (isset($firstname_from_chrome_extension) && isset($time_from_extension)) {
            //     if ($approve_for_campaign == 'Y') {
            //         $pause = 'Y';
            //         $approve = 'Y';
            //     } else {
            //         $approve = 'Y';
            //         $pause = 'N';
            //     }
            //     $update_task_approve_query = mysqli_query($db->conn,"update task set Pause='" . $pause . "',Approve='" . $approve . "' where MailID = '" . $MailID . "'") or die(mysqli_error($db->conn) . __LINE__);
            // }

            // 
            // $GetContactIdQuery = mysqli_query($db->conn,"select ContactID from user_mail where MailID='" . $MailID . "'") or die(mysqli_error($db->conn) . __LINE__);

           // $GetContactIdQuery = mysqli_query($db->conn,"select ContactID from user_mail where MailID='" . $MailID . "'");


            //$GetContactIdRow = mysqli_fetch_assoc($GetContactIdQuery);
            //$ContactID = $GetContactIdRow['ContactID'];
            // if (isset($firstname_from_chrome_extension) && isset($lastname_from_chrome_extension) && $firstname_from_chrome_extension != "nodataavailable" && $lastname_from_chrome_extension != "nodataavailable") {
            //     $mail_tmp_here = explode("+", $XEnvelopeTo);
            //     if (count($mail_tmp_here) == '11') {
            //         $FirStName = $mail_tmp_here['3'];
            //         $LaStName = $mail_tmp_here['4'];
            //     } elseif (count($mail_tmp_here) == '10') {
            //         $FirStName = $mail_tmp_here['2'];
            //         $LaStName = $mail_tmp_here['3'];
            //     }
            //     $UpdateContactInfoQuery = mysqli_query($db->conn,"update contact_list set FirstName='" . ucfirst($FirStName) . "', LastName='" . ucfirst($LaStName) . "' where ContactID='" . $ContactID . "'");
            // }

            if (isset($extension_mail_ticklefollow)) {
                $DeleteUserMail = mysqli_query($db->conn,"delete from user_mail where MailID='" . $extension_mail_ticklefollow . "'");
            }
            

            /* Code end to set next follow-up message t with new campaign created from chrome extensionm */
        }
    }
    $from = preg_replace("/[\"]/", "", $from);
    // MAKE DANISH DATE DISPLAY
    list($dayName, $day, $month, $year, $time) = explode(" ", $date);
    $time = substr($time, 0, 5);
    $date = $day . " " . $month . " " . $year . " " . $time;

    if ($bgColor == "#F0F0F0") {
        $bgColor = "#FFFFFF";
    } else {
        $bgColor = "#F0F0F0";
    }

    if (strlen($subj) > 60) {
        $subj = substr($subj, 0, 59) . "...";
    }


    if (isset($overlimitemail) && count($overlimitemail) > 0) {

        $EmailID = mysqli_query($db->conn,"select EmailID from tickleuser where TickleID='" . $mytickleid . "'");
        while ($mailcheck = mysqli_fetch_assoc($EmailID)) {
            $email = $mailcheck['EmailID'];
        }
        //die();
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
        
        $taskquery = mysqli_query($db->conn,"select TaskID from task where TickleID='" . $mytickleid . "'");
        while ($taskrow = mysqli_fetch_assoc($taskquery)) {
            $taskid = $taskrow['TaskID'];
        }
        
        $protect = protect($mytickleid . "-" . $taskid);
        
        //$TDashboardLink = "http://" . SERVER_NAME . Url_Create("home", "act=" . rawurlencode($protect));
        $TDashboardLink = "https://client.tickletrain.com" . Url_Create("home", "act=" . rawurlencode($protect));
        $Tdirectupgradelink = $TDashboardLink . '&dierctserviceid=' . $serviceid;

        $overlimitmailcontent = "I'm sorry, but the email below was not added to TickleTrain<br/><br/>";
        for ($v = 0; $v < count($overlimitemail); $v++) {
            $overlimitmailcontent.="TO:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$overlimitemail[$v]<br/>SUBJECT:&nbsp;&nbsp;&nbsp;$overlimitsubject[$v]<br/>TICKLE:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$overlimittickle[$v]<br/><br/>";
        }
        $overlimitmailcontent.= "<br/>You have reached your plan limit.";
        $logData = array();
        $logData['TickleID'] = $mytickleid;
        $logData['content'] = $overlimitmailcontent;
        $ttresponse = 'You have reached your plan limit.';
        create_logs($logData,$ttresponse);

        $overlimitmailcontent.='<div style="margin:20px auto;text-align:center"><a href="'.$Tdirectupgradelink.'" target="_blank"><button style="background:none repeat scroll 0 0 #008acd;border:1px solid #007fc2;color:#ffffff;font-family:arial;font-size:18px;min-height:30px;width:360px;margin:0;text-decoration:none;cursor: pointer;">CLICK HERE to Upgrade to the Unlimted Plan for less than $5/month</button></a><div></div></div><p style="text-align:center">Having a problem? Please <a href="https://tickletrain.com/contact-us/" target="_blank">contact us.</a></p><br/><br/><p>Thank you for using TickleTrain,</p><p>Send it. And Forget it.</p>';
            $overlimitmailcontent;
            //SendMail($myrss['EmailID'], 'noreply@tickletrain.com', 'Overlimit', "$overlimitmailcontent");
        SendMail($myrss['EmailID'], 'noreply@tickletrain.com', 'Time to upgrade', "$overlimitmailcontent");
        //debug("Campaigns reach their limit, email to '$myrss[EmailID]' has been sent");
        // goto mailcontinue;  
    }


    mailcontinue:
    debug ("mailmove - $ImapMove");
    @imap_mail_move($mbox, $msg, $ImapMove);
    echo "<br>";
    echo ("mail process end");
}
//die();
imap_expunge($mbox);
imap_close($mbox);
debug("imap_close");


//approve && pause section
$bdate = time() + 24 * 3600;
//$getservertz = date_default_timezone_get();
//date_default_timezone_set("Etc/GMT-0"); //set date
//$now = gmdate("Y-m-d H:i:s", time() - 5 * 60);
$now = gmdate("Y-m-d H:i:s", time() - (5 * 60 * 60)); #this line update by som on 12 aug 2016
//cleanDatabase();

function decode_address($mailaddr) {
    $mail_tmp = explode("+", $mailaddr);

    $mailcount = count($mail_tmp) - 1;
    $data['MailAddr'] = strtolower($mail_tmp[$mailcount]);
    $j = 0;
    for ($i = ($mailcount - 1); $i >= 0; $i--) {
        $j++;
        $data["Part$j"] = strtolower($mail_tmp[$i]);
    }
    $tickle_name = $mailcount - 1;
    $data['TickleName'] = strtolower($mail_tmp[$tickle_name]);
    $Unsb = $tickle_name - 1;
    $data['Unsubscribe'] = strtolower($mail_tmp[$Unsb]);
    for ($i = 0; $i < $Unsb; $i++) {
        $data[] = strtolower($mail_tmp[$i]);
    }
    //print_r($data);
    return $data;
}

function isValidEmail($email) {
    return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email);
}

function CalculateDays($Tasks,&$task_correct_date_format,$stday = 0) {
    $IQ = 0;
    $ARRInterval = array();
    $startday = $stday;

    foreach ($Tasks as $TKey => $TVal) {
        $task_DailyDaysFollow = intval($TVal['DailyDays']);
        if (!$IQ && $startday) {
            $startday-=$task_DailyDaysFollow;
        }
        $task_EndAfterFollow = intval($TVal['EndAfter']);
        $task_TickleTimeFollow = $TVal['TickleTime'];
        $task_followTickleTrainID = $TVal['FollowTickleTrainID'];
        //skip weekend
        $NoWeekend = @trim($TVal['NoWeekend']);
        //skip weekend
        if($TVal['DailyDays'] == 0){
            $NoWeekend = 'N';
            $now = time();
            $one_minute = $now + (1 * 60);
            $task_TickleTimeFollow = date('H:i:s', $one_minute);
        }

        $Intervals = MultiIntervalsFollow($task_DailyDaysFollow, $task_EndAfterFollow, $task_TickleTimeFollow, $startday,$task_correct_date_format,$task_followTickleTrainID, $NoWeekend);

        $ARRInterval[$IQ]['Intervals'] = $Intervals;
        foreach ($TVal as $KTsk => $VTsk) {
            $ARRInterval[$IQ][$KTsk] = $VTsk;
        }

        $IQ++;
    }
    return $ARRInterval;
}

function MultiIntervalsFollow($DailyDays, $EndAfter, $TickleTime, &$startday,&$task_correct_date_format,$task_followTickleTrainID, $noweekend = 'N') {
    //echo "MultiIntervalsFollow($DailyDays,$EndAfter,$TickleTime,$startday,$noweekend)\n";
    if ($TickleTime == "") {
        $TickleTime = "12:00:00";
    }
    $i = $startday;
    //echo $i."------";
    $specialtickle = false;
    $dates = array();
    while (count($dates) < $EndAfter) {
        // echo $DailyDays."------------".$startday.'-----------------';
       if($DailyDays == '365'){
          if($startday == '365' && $i == $startday){
              $Current_time_stamp = time();
         }else{
             $Current_time_stamp = time() + $i * 24 * 3600;
         }
         $after1yertimestamp = strtotime("+1 years",$Current_time_stamp);
         $CheckdayifferenceTimeStamp = $after1yertimestamp - $Current_time_stamp;
         $daydifferenceValue = floor($CheckdayifferenceTimeStamp/(60*60*24));
         $i+= $daydifferenceValue;
       }else{
        $i+=$DailyDays;
       }

     //  $i+=$DailyDays;https://www.youtube.com/watch?v=IdoQCk8P2l8
        $time = time() + $i * 24 * 3600;
        debug("time: $i ($time)");
        //skip weekend
        if ($noweekend == 'Y') {
            $wd = @intval(date("w", $time));
            while ($wd == 0 || $wd == 6) {
                $i++;
                $time+=24 * 3600;
                $wd = @intval(date("w", $time));
            }
        }
        //skip weekend
        $date = date('Y-m-d', $time) . ' ' . $TickleTime;
        $real_task_date = date('Y-m-d', $time);
        if( (isset($task_correct_date_format) && !empty($task_correct_date_format)) && !$specialtickle){
        $real_task_date = date('Y-m-d', time());
       // $date = $task_correct_date_format . ' ' . $TickleTime;
        $date = $task_correct_date_format;
        $specialtickle = true;
        $date1 = new DateTime($real_task_date);
        $date2 = new DateTime($task_correct_date_format);
        $interval = $date1->diff($date2);
        //echo $interval."------------------https://www.youtube.com/watch?v=IdoQCk8P2l8";
        $i =  $interval->days;
        $task_correct_date_format = "";
       }
        $dates[] = $date;
    };

    //die();
   
    $startday = $i;
   // echo $i.'<br/>';
    return $dates;
}

function cleanDatabase() {
     global $db;
    //mysqldump --user=pecan_tickle --password='T!ckLe123' tickletrain >dump_15122011.sql
    mysqli_query($db->conn,"delete from task where Status!='Y'");
    mysqli_query($db->conn,"delete from user_mail where not(MailID in (select MailID from task))");
    mysqli_query($db->conn,"delete from tickle where not(TickleID in (select TickleID from tickleuser))");
}

function debug($msg) {
    //echo $msg."\n";
    //$fname = LOGS_FOLDER . 'crontrain.log';
    //WriteFile($fname, gmdate('d.m.Y H:i:s') . " > " . $msg . "\n", "a");
}

function countcurrentcampaigns($uid) {
     global $db;
    //  include("includes/function/func.php");
    //  include("includes/class/phpmailer/class.phpmailer.php");

    $dselect = "select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$uid' and task.Status='Y'";
    $mselect = "select distinct task.MailID, task.TaskID, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$uid' and task.Status='Y'";
    $dselect.=" order by TaskDate";
    $i = 0;
    $checkquery = mysqli_query($db->conn,$dselect);
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
    $checkqueryagain = mysqli_query($db->conn,$mselect);
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
    return $currentcampaign;
}

function unread($a) {
    if ($a->seen == '0')
        return true;
    else
        return false;
}

function UpdateTaskFromBccCommand($TaskID, $NewTickleTitle, $MailID, $TickleID, $TickleTrainID) {
    global $db;
    $GetAllTasksQuery = mysqli_query($db->conn,"select TaskID,FollowTickleTrainID,TickleTrainID,TaskGMDate,TaskInitiateDate,TimeZone from task where TickleTrainID='$TickleTrainID' and TickleID='$TickleID' and MailID='$MailID' and Status='Y' order by TaskID");
    while ($GetAllTasksRow = mysqli_fetch_assoc($GetAllTasksQuery)) {
        
        $NewTaskInitiateDateTimeStamp = strtotime("$NewTickleTitle day", strtotime($GetAllTasksRow['TaskInitiateDate']));
        $TaskDate = date("Y-m-d H:i", $NewTaskInitiateDateTimeStamp);
        
        if ($GetAllTasksRow['FollowTickleTrainID'] == 0):
            $GetTickleInfoQuery = mysqli_query($db->conn,"select NoWeekend from tickle  where TickleTrainID='$TickleTrainID' and TickleID='$TickleID' and Status='Y'");
            $GetTickleInfoRow = mysqli_fetch_assoc($GetTickleInfoQuery);
            $NoWeekEnd = $GetTickleInfoRow['NoWeekend'];
        else:
            $GetTickleFollowInfoQuery = mysqli_query($db->conn,"select NoWeekend from ticklefollow where FollowTickleTrainID='" . $GetAllTasksRow['FollowTickleTrainID'] . "' and TickleID='$TickleID' and Status='Y'");
            $GetTickleFollowInfoRow = mysqli_fetch_assoc($GetTickleFollowInfoQuery);
            $NoWeekEnd = $GetTickleFollowInfoRow['NoWeekend'];
        endif;
        $getservertz = date_default_timezone_get();
        date_default_timezone_set('Etc/GMT-0');
        
        $TaskInitiateTime = date("H:i:s", strtotime($TaskDate));
        
        $TaskGmTime = date("H:i:s", strtotime($GetAllTasksRow['TaskGMDate']));
        $TaskGmDate = date('Y-m-d',  strtotime($TaskDate));
        
        if($NoWeekEnd == 'Y'){
        if(date("N",  strtotime($TaskGmDate)) == 7):
           $TaskGmDate = date('Y-m-d', strtotime($TaskGmDate . ' + 1 day')); 
        elseif(date("N",  strtotime($TaskGmDate)) == 6):
           $TaskGmDate = date('Y-m-d', strtotime($TaskGmDate . ' + 2 day'));  
        endif;
        }


        $TaskGmDateTime = $TaskGmDate.' '.$TaskGmTime;
        $TaskInitiatedateTime = $TaskGmDate.' '.$TaskInitiateTime;
        
            $nday = gmdate("Y-m-d H:i:s", strtotime($TaskGmDateTime));
            $iday = getlocaltime($nday, $GetAllTasksRow['TimeZone']).'<br/>';
            mysqli_query($db->conn,"update task set TaskInitiateDate='" . $iday . "', TaskGMDate='" . $nday . "' where TaskID=" . $GetAllTasksRow['TaskID']);
            debug("Update Campaign Time Query 7 update task set TaskInitiateDate='" . $iday . "', TaskGMDate='" . $nday . "' where TaskID=" . $GetAllTasksRow['TaskID']);
       }

       //die("Am i upto here??");
}


function remove_word($str,$word)
{
    return trim(str_replace($word, "", $str));
}



 function get_date_and_time($input)
{
    switch (strtoupper($input)) {
        case 'N':
            $date = (time()+120);
            break;
        case '1H':
            $date = (time()+(60*60));
            break;
        case '2H':
            $date = (time()+(60*60*2));
            break;
        case '3H':
            $date = (time()+(60*60*3));
            break;
        case '1D':
            $date = (time()+(60*60*24));
            break;
        case '2D':
            $date = (time()+(60*60*24*2));
            break;
        case '3D':
            $date = (time()+(60*60*24*3));
            break;
        case '1W':
            $date = (time()+(60*60*24*7));
            break;
        case '2W':
            $date = (time()+(60*60*24*14));
            break;
        case '1M':
            $date = strtotime('+1 month', time());
            break;
        default:
            $date = (time()+(120));
            break;
    }
    return $date;
}


?>