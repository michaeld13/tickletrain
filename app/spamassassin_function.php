<?php
include_once ('includes/class/spamassassin/Client.php');
$emailid = 'info@tickletrain.com';
use PHPMailer\PHPMailer\PHPMailer;
$TaskID = $_POST['TaskID'];
$data = $_POST['data'];
$DataPost = array();
$Tickletid = $_POST['Tickletid'];
$Tickletid = $_POST['TickleTrainID'];
//$tickle=array();
//$tickle=$db->select_to_array('tickle',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='$Tickletid'");
//$DataPost=$tickle[0];
$DataPost = $_POST;
/*
if ($TF=$_POST['FollowTickleTrainID']) 
    $TickleFollow=$db->select_to_array('ticklefollow',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='$Tickletid' and FollowTickleTrainID='$TF'"); 
else
    $TickleFollow=$db->select_to_array('ticklefollow',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='$Tickletid' and false");    
*/
$Priority = array('1' => "1 (High)", '3' => "3 (Normal)", '5' => "5 (Low)");

// tickle 
$TickleS[0]['TickleMailContent'] = @trim($_REQUEST['TickleMailContent']);
$TickleS[0]['AttachOriginalMessage'] = @trim($_REQUEST['AttachOriginalMessage']);
$TickleS[0]['EmailPriority'] = @intval($_REQUEST['EmailPriority']);
$CCMe = @trim($_REQUEST['CCMe']);
if ($CCMe == "") {
    $CCMe = "N";
}
$TickleS[0]['CCMe'] = $CCMe;

$TickleS[0]['DailyDays'] = @intval($_REQUEST['DailyDays']);
$TickleS[0]['EndAfter'] = $_REQUEST['EndAfter'];
$TickleS[0]['TickleTime'] = str_replace(".", ":", @trim($_REQUEST['TickleTime']));
$grouplist = @trim($_REQUEST['grouplist']);

$TickleName = $DataPost['TickleName'];

$afiles = tablelist("files", '', " where FileContext='tickle' and FileParentID='$Tickletid'");
$TAttach = array();
for ($i = 0; $i < count($afiles); $i++) {
    $fname = @trim($afiles[$i]['FileName']);
    if ($fname != "" && file_exists(FULL_UPLOAD_FOLDER . $fname)) {
        $TAttach[] = FULL_UPLOAD_FOLDER . $fname;
    }
}
$TickleS[0]['Attach'] = $TAttach;


$CountRow = count($TickleFollow) - 1;
$imx = 0;
//for($ix=0;$ix<=$CountRow;$ix++)
if (isset($_POST['TickleMailFollowContent'])) {
//	if($TickleFollow[$ix]['TickleMailFollowContent']!="")
    {
        $TickleS[$imx]['TickleMailContent'] = @trim($_REQUEST['TickleMailFollowContent']);
        $TickleS[$imx]['AttachOriginalMessage'] = @trim($_REQUEST['AttachMessageFollow']);
        $TickleS[$imx]['EmailPriority'] = @trim($_REQUEST['EmailPriorityFollow']);
        $CCMe = @trim($_REQUEST['CCMeFollow']);
        if ($CCMe == "") {
            $CCMe = "N";
        }
        $TickleS[$imx]['CCMe'] = $CCMe;


        $TickleS[$imx]['DailyDays'] = @intval($_REQUEST['DailyDaysFollow']);
        $TickleS[$imx]['EndAfter'] = $_REQUEST['EndAfterFollow'];
        $TickleS[$imx]['TickleTime'] = $TickleFollow[$ix]['TickleTimeFollow'];
        $FollowTickleTrainID = $TickleFollow[$ix]['FollowTickleTrainID'];

        $afiles = tablelist("files", '', " where FileContext='ticklefollow' and FileParentID='$FollowTickleTrainID'");
        $TAttach = array();

        for ($i = 0; $i < count($afiles); $i++) {
            $fname = @trim($afiles[0]['FileName']);
            if ($fname != "" && file_exists(FULL_UPLOAD_FOLDER . $fname)) {
                $TAttach[] = FULL_UPLOAD_FOLDER . $fname;
            }
        }
        $TickleS[$imx]['Attach'] = $TAttach;
        $imx++;
    }
//var_dump($TickleS);
}

if (count($tickle) > 0 || true) {
    $mail = new PHPMailer(false); //New instance, with exceptions enabled

    $mail->IsSMTP(); // tell the class to use SMTP
    $mail->SMTPAuth = true; // enable SMTP authentication
    $mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent
    $mail->Port = 25; // set the SMTP server port
    $mail->Host = "mail.tickletrain.com"; // SMTP server
    $mail->Username = "ticklein@tickletrain.com"; // SMTP server username
    $mail->Password = "change88"; // SMTP server password
    //$mail->IsSpamAssassin(); // tell the class to use SpamAssassin
    $mail->Mailer = 'spamassassin';

    if ($emailid != "") {

        foreach ($TickleS as $TKEy => $TKVal) {
//    $TNKey=$TickleS[0];
            $TickleID = $_SESSION['TickleID'];
            $TickleTrainID = $TickleTrainID;
            $sql_user = mysqli_query($db->conn,"select * from tickleuser where TickleID='$TickleID' and Status='Y'");
            $rs_user = mysqli_fetch_array($sql_user);
            $FromEmailid = $rs_user['EmailID'];
            $FromFirstName = $rs_user['FirstName'];
            $FromLastName = $rs_user['LastName'];
            $UserName = $rs_user['UserName'];
            $tickle_reply = $FromEmailid;
            $TickleContact = $grouplist;

            $TickleMailContent = $TKVal['TickleMailContent'];
            $EmailPriority = $TKVal['EmailPriority'];
            if ($EmailPriority <= 0) {
                $EmailPriority = 3;
            }

            $EmailPriority = $Priority[$EmailPriority];
            $AttachOriginalMessage = $TKVal['AttachOriginalMessage'];
            $TAttach = $TKVal['Attach'];
            if (count($TAttach) != 0) {
                for ($f = 0; $f < count($TAttach); $f++) {
                    $mail->AddAttachment($TAttach[$f], basename($TAttach[$f]));
                }
            }


            $Cuser = array();            

            $toaddress = $Cuser;
            $toaddress = array_filter($toaddress);
            $toaddress = array_unique($toaddress);
            $Subject = $TickleName;
            $TextMsg = strip_tags(str_ireplace(array("<br />", "<br>", "&nbsp;"), array("\n", "\n", " "), $TickleMailContent));
            $TextMsg = RemoveBadChar($TextMsg);

            $HTMLContent = RemoveBadChar($TickleMailContent);
            //$mail->AddReplyTo(trim($tickle_reply), "$FromFirstName $FromLastName");
            $mail->AddReplyTo("noreply@tickletrain.com");
            //$mail->SetFrom($FromEmailid, "$FromFirstName $FromLastName");
            $mail->SetFrom("noreply@tickletrain.com");
            $AttMessageHeader = "<div style='border:none;border-top:solid #B5C4DF 1.0pt;padding:3.0pt 0in 0in 0in'>
                                    From: $FromFirstName $FromLastName [" . $FromEmailid . "]
                                    Sent: [Tickle Mail Date]
                                    To: [Tickle T0 Mail Address]
                                    Cc: [Tickle Cc Mail Address]
                                    Subject: [Tickle Mail Subject]
                                    </div>
                                ";

            if (count($toaddress) > 0) {
                //\n ".implode(",",$toaddress)."

            }
            $UMessage = "[Attached Original Message Will Display Here]";
            $AttMessage = strip_tags($AttMessageHeader) . " \n" . $UMessage;

            if ($AttachOriginalMessage == "Y") {
                $Subject = $Subject; //." : ".$USubject;
                $TextMsg = $TextMsg . "\n\n" . str_replace("\n", "\n> ", $AttMessage);
                if (trim($UMessageHtml) == "") {
                    $UMessageHtml = nl2br($UMessage);
                }
                $HTMLContent = $HTMLContent . "<br /><br /><blockquote type='cite'>" . nl2br($AttMessageHeader) . "<br />" . $UMessageHtml . "</blockquote>";
            }
            $body = $HTMLContent;
            $mail->Subject = $Subject;
            $mail->AltBody = $Note . $TextMsg;
            $mail->WordWrap = 80; // set word wrap
            $mail->Priority = $EmailPriority;
            $mail->MsgHTML($body);
            $mail->IsHTML(true); // send as HTML
            $to_address = "";
            $mail->AddAddress($emailid);
            $mailtext = $mail->Send();

            $spamreport[] = CheckOnSpam($mailtext);

            $SendMail = 1;
            // Clear all addresses and attachments for next loop
            $mail->ClearAddresses();
            $mail->ClearAttachments();
            $mail->ClearReplyTos();
            $mail->ClearAllRecipients();
        }
        //foreach array
        if ($SendMail == 1) {
            ShowSpamReport($spamreport);
        }
    }

}
//if task
//echo '<center><a href="javascript: void(0)" onclick="$(\'#spamassassin\').attr(\'style\',\'display:none\')">Close</a></center>';

function isValidEmail($email)
{
    return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email);
}

exit();

function CheckOnSpam($message)
{
    $params = array(
        "hostname" => "localhost",
        "port" => "783",
        "user" => "ppadron",
    );

    $sa = new SpamAssassin_Client($params);

    $m = $sa->getShortSpamReport($message);
    return $m;
}

function ShowSpamReport($report)
{
    $res = "<h3>Spam check report</h3>";
    $comment = array(
        '0' => 'Your Tickle message is nice and clean. Mail servers should have no issues accepting your emails, no changes are required.',
        '4.6' => 'There is a small chance some mail servers may mark your Tickle message as spam. Do your best to clean up the easy-to-understand issues (above).',
        '7.1' => 'There is a moderate chance some mail servers may mark your Tickle message as spam. Do your best to clean up the easy-to-understand issues (above).',
        '10.1' => 'There is a good chance most mail servers will mark your Tickle message as spam. Do your best to clean up the easy-to-understand issues (above).',
        '13.1' => 'There are major problems with your Tickle message. A complete message overhaul needed. Do your best to clean up all the easy-to-understand issues (above).'
    );
    $check_text = "";
    foreach ($report as $z) {
        $sum = 0;
        $res = '';
        $check_text .= '<div class="check_text">';
        foreach ($z as $v) {
            $s = preg_split('/ /', $v, 2);
            if ((float)$s[0] != 0) {
                //$s10 = 10 * (float)$s[0];   //02-march-2016
		$s10 =  (float)$s[0];
                $s06 = $s10 * 0.6;
                $check_text .= '<p>' . $s06 . ' ' . substr($s[1], strpos($s[1], ' ')) . '</p>';
                $sum += $s10;
            }
        }
        if ($sum > 0) {
            $check_text .= '<span class="ico_question" onclick="ShowSpamHelp();return false" style="cursor:pointer">?</span>';
        }
        $check_text .= '</div>';
        foreach ($comment as $k => $v) {
            if ($sum >= $k / 0.6) $ps = $v;
        }
        $s = round($sum * 0.6, 1);
        $res .= ShowSpamReportGraph($sum);
        $res .= $check_text;
        $res .= "<h4>Score: $s</h4><p>$ps</p>";
        echo $res;
    }
}

function ShowSpamReportGraph($sum)
{
    $result = array('0' => 'excellent', '4.6' => 'good', '7.1' => 'fair', '10.1' => 'poor', '13.1' => 'SPAM');
    //14-0
    //0-150
    $sm = min(13,$sum*0.6);
    $x = ceil(150-$sm*(150/13));
    foreach ($result as $k => $v) {
        if ($sum * 0.6 >= $k) $res = $v;
    }
    $ret = '<div class="check_block">'.
    '<ul class="numbers">'.
   		'<li>15</li>'.
        '<li>10</li>'.
   		'<li>5</li>'.
   		'<li>0</li>'.
   	'</ul>';
    $ret .= '<div class="tooltip" style="left:' . $x . 'px"><span>' . $res . '</span></div>';
    $ret .= "</div>";
    return $ret;
}
?>
