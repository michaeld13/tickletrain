<?php
include_once ('includes/class/spamassassin/Client.php');
$emailid='info@tickletrain.com';

$TaskID=$_POST['TaskID'];
$data=$_POST['data'];
$DataPost=array();
$Tickletid=$_POST['Tickletid'];
//$Tickletid=$_POST['TickleTrainID'];
$tickle=array();
$tickle=$db->select_to_array('tickle',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='$Tickletid'");
$DataPost=$tickle[0];

if ($TF=$_POST['FollowTickleTrainID']) 
    $TickleFollow=$db->select_to_array('ticklefollow',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='$Tickletid' and FollowTickleTrainID='$TF'"); 
else
    $TickleFollow=$db->select_to_array('ticklefollow',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='$Tickletid' and false");    

$Priority = array('1'=>"1 (High)", '3'=>"3 (Normal)", '5'=>"5 (Low)");
$TickleS[0]['TickleMailContent']=$DataPost['TickleMailContent'];
$TickleS[0]['AttachOriginalMessage']=$DataPost['AttachOriginalMessage'];
$TickleS[0]['EmailPriority']=$DataPost['EmailPriority'];
$TickleS[0]['CCMe']=$DataPost['CCMe'];
$TickleS[0]['DailyDays']=$DataPost['DailyDays'];
$TickleS[0]['EndAfter']=$DataPost['EndAfter'];
$TickleS[0]['TickleTime']=$DataPost['TickleTime'];

$grouplist=$DataPost['TickleContact'];
$TickleTrainID=$DataPost['TickleTrainID'];
$TickleName=$DataPost['TickleName'];

$afiles=tablelist("files",''," where FileContext='tickle' and FileParentID='$Tickletid'");
$TAttach=array();
for($i=0; $i<count($afiles);$i++){
    $fname = @trim($afiles[$i]['FileName']);
    if ($fname!="" && file_exists(FULL_UPLOAD_FOLDER.$fname)){
       $TAttach[]=FULL_UPLOAD_FOLDER.$fname;
    }
}
$TickleS[0]['Attach']=$TAttach;



$CountRow=count($TickleFollow)-1;
$imx=0;
for($ix=0;$ix<=$CountRow;$ix++)
{
	if($TickleFollow[$ix]['TickleMailFollowContent']!="")
	{
	$TickleS[$imx]['TickleMailContent']=$TickleFollow[$ix]['TickleMailFollowContent'];
	$TickleS[$imx]['AttachOriginalMessage']=$TickleFollow[$ix]['AttachMessageFollow'];
	$TickleS[$imx]['EmailPriority']=$TickleFollow[$ix]['EmailPriorityFollow'];
	$TickleS[$imx]['CCMe']=$TickleFollow[$ix]['CCMeFollow'];
	$TickleS[$imx]['DailyDays']=$TickleFollow[$ix]['DailyDaysFollow'];
	$TickleS[$imx]['EndAfter']=$TickleFollow[$ix]['EndAfterFollow'];
	$TickleS[$imx]['TickleTime']=$TickleFollow[$ix]['TickleTimeFollow'];
        $FollowTickleTrainID = $TickleFollow[$ix]['FollowTickleTrainID'];

        $afiles=tablelist("files",''," where FileContext='ticklefollow' and FileParentID='$FollowTickleTrainID'");
        $TAttach=array();

        for($i=0; $i<count($afiles);$i++){
            $fname = @trim($afiles[0]['FileName']);
            if ($fname!="" && file_exists(FULL_UPLOAD_FOLDER.$fname)){
               $TAttach[]=FULL_UPLOAD_FOLDER.$fname;
            }
        }
        $TickleS[$imx]['Attach']=$TAttach;
	$imx++;
	}
}


echo '<h1 class="head">Spam check report</h1>';

if(count($tickle)>0)
{

$mail = new PHPMailer(true); //New instance, with exceptions enabled

$mail->IsSMTP();                           // tell the class to use SMTP
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->SMTPKeepAlive = true;                  // SMTP connection will not close after each email sent
	$mail->Port       = 25;                    // set the SMTP server port
	$mail->Host       = "mail.tickletrain.com"; // SMTP server
	$mail->Username   = "ticklein@tickletrain.com";     // SMTP server username
	$mail->Password   = "change88";            // SMTP server password
	
	$mail->IsSpamAssassin();  // tell the class to use SpamAssassin
if($emailid!="")
{

foreach($TickleS as $TKEy=>$TKVal)
{
//    $TNKey=$TickleS[0];
			$TickleID=$_SESSION['TickleID'];
			$TickleTrainID=$TickleTrainID;
			$sql_user=mysqli_query($db->conn,"select * from tickleuser where TickleID='$TickleID' and Status='Y'");
			$rs_user=mysqli_fetch_array($sql_user);
			$FromEmailid=$rs_user['EmailID'];
			$FromFirstName=$rs_user['FirstName'];
			$FromLastName=$rs_user['LastName'];
			$UserName=$rs_user['UserName'];
$tickle_reply=$FromEmailid;
			$TickleContact=$grouplist;
			
			$TickleMailContent=$TKVal['TickleMailContent'];
			$EmailPriority=$TKVal['EmailPriority'];
			if($EmailPriority<=0){
                            $EmailPriority=3;
                        }
			
                        $EmailPriority=$Priority[$EmailPriority];
			$AttachOriginalMessage=$TKVal['AttachOriginalMessage'];
                        $TAttach = $TKVal['Attach'];
                        if (count($TAttach)!=0){
                                for($f=0;$f<count($TAttach);$f++){
                                    $mail->AddAttachment($TAttach[$f], basename($TAttach[$f]));
                                }
                        }
                       
			
			$Cuser=array();
			/*if($TickleContact>0)
			{
				$sql_cuser=mysqli_query($db->conn,"select * from contact_list where TickleID='$TickleID' and Status='Y' and CategoryID='$TickleContact'");
				while($rs_cuser=mysqli_fetch_array($sql_cuser))
				{
				$Cuser[]=$rs_cuser['EmailID'];
				}
			}*/
		
			$toaddress=$Cuser;
			$toaddress=array_filter($toaddress);
			$toaddress=array_unique($toaddress);
			$Subject=$TickleName;
			$TextMsg=strip_tags(str_ireplace(array("<br />","<br>","&nbsp;"),array("\n","\n"," "),$TickleMailContent));
			$TextMsg=RemoveBadChar($TextMsg);
			
			$HTMLContent=RemoveBadChar($TickleMailContent);
			$mail->AddReplyTo(trim($tickle_reply), "$FromFirstName $FromLastName");
			$mail->SetFrom($FromEmailid, "$FromFirstName $FromLastName");
			$AttMessageHeader="
<div style='border:none;border-top:solid #B5C4DF 1.0pt;padding:3.0pt 0in 0in 0in'>
From: $FromFirstName $FromLastName [".$FromEmailid."]
Sent: [Tickle Mail Date]
To: [Tickle T0 Mail Address]
Cc: [Tickle Cc Mail Address]
Subject: [Tickle Mail Subject]
</div>
";

			if(count($toaddress)>0)
			{
			//\n ".implode(",",$toaddress)."
			
			}
			$UMessage="[Attached Original Message Will Display Here]";
			$AttMessage=strip_tags($AttMessageHeader)." \n".$UMessage;
			
				if($AttachOriginalMessage=="Y")
				{
				$Subject=$Subject;//." : ".$USubject;
				$TextMsg=$TextMsg."\n\n".str_replace("\n","\n> ",$AttMessage);
				if(trim($UMessageHtml)=="")
				{
				$UMessageHtml=nl2br($UMessage);
				}
				$HTMLContent=$HTMLContent."<br /><br /><blockquote type='cite'>".nl2br($AttMessageHeader)."<br />".$UMessageHtml."</blockquote>";
				}
				$body=$HTMLContent;
				$mail->Subject = $Subject;
				$mail->AltBody    = $Note.$TextMsg;
				$mail->WordWrap   = 80; // set word wrap
				$mail->Priority =$EmailPriority;
				$mail->MsgHTML($body);
				$mail->IsHTML(true); // send as HTML
			$to_address="";
			$mail->AddAddress($emailid);
			$mailtext=$mail->Send();
                        
                        $spamreport[]=CheckOnSpam($mailtext);
                        
			$SendMail=1;
			 // Clear all addresses and attachments for next loop
			  $mail->ClearAddresses();
				$mail->ClearAttachments();
			  $mail->ClearReplyTos();
			  $mail->ClearAllRecipients();  
		}//foreach array
		if($SendMail==1)
		{
                    ShowSpamReport($spamreport);
		}
	}
        
}//if task
//echo '<center><a href="javascript: void(0)" onclick="$(\'#spamassassin\').attr(\'style\',\'display:none\')">Close</a></center>';

function isValidEmail($email){
	return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email);
}	
exit();

function CheckOnSpam($message) {
    $params = array(
        "hostname" => "localhost",
        "port" => "783",
        "user" => "ppadron",
    );

    $sa = new SpamAssassin_Client($params);

    $m=$sa->getShortSpamReport($message);
    return $m;
}

function ShowSpamReport($report) {
    foreach ($report as $z) {
        $sum=0;
        $res='';
        $res.= '<table border=0 width=194>';
        foreach ($z as $v){
            $s=preg_split('/ /', $v, 2);
            if ((float)$s[0]!=0) {
                $s10=10*(float)$s[0];
                $res.= '<tr><td align="right" valign=top>'.$s10.'</td><td>'.substr($s[1],strpos($s[1],' ')).'</td></tr>';
                $sum+=$s10;
            }
        }
//        $res.= '<tr><td colspan=2>Total='.$sum.'</td></tr>';
        $res.= '</table>';
        ShowSpamReportGraph($sum);
        echo $res;
    }
}

function ShowSpamReportGraph($sum) {
    $result=array('0'=>'excellent','15'=>'good','30'=>'satisfactory','40'=>'poor');
    $x=(int)(143-$sum*3.2);
    $img='sa_center.png';
    if ($x<30) { $img='sa_left.png'; $x+=22; }
        elseif ($x>100) { $img='sa_right.png'; $x-=30; }
    if ($x<5) $x=5;
    
    foreach ($result as $k => $v) {
        if ($sum>=$k) $res=$v;
    }
    ?>
    <table border="0" width="194" cellpadding="0" cellspacing="0">
        <tr>
            <td width="7"></td>
            <td width="180" align="center" valign="middle"><div style="font-size: 32px;"><?=$sum?></div></td>
            <td width="7"></td>
        </tr>
        <tr>
            <td colspan="3"><div style="height: 34px; width: 80px; font-size: 12px; text-align: center; padding-top: 0px; margin-left: <?=$x?>px; background-image: url(../images/<?=$img?>)"><?=$res?></div></td>
        </tr>
        <tr>
            <td></td>
            <td><img src="../images/gradient.png"></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    
        <?
}
?>