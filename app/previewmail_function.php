<?php
define('ROOT_FOLDER', "new/");
define('HOME_FOLDER', GetHomeDir() . "/");
define('IMAGE_BASE_FOLDER',  str_replace(ROOT_FOLDER, "", HOME_FOLDER));
define('FULL_UPLOAD_FOLDER', HOME_FOLDER . "upload-files/");
define('LOGS_FOLDER', HOME_FOLDER . "_logs/");
define('SERVER_NAME', "tickletrain.com");
//die('11111');
$GLOBALS['mode']=0;

if($_REQUEST['TaskID']!=""){
    $_SESSION['PrevTickleTaskID']=$_REQUEST['TaskID'];
}

$user_signature = tablerow('tickleuser', 'signature,mail_type', array("WHERE UserName ='" . $_SESSION['UserName'] . "' and TickleID='" . $_SESSION['TickleID'] . "'"));

if($_REQUEST['MailID']!=""){
    $_SESSION['PrevTickleMailID']=$_REQUEST['MailID'];
}
if($_REQUEST['act']!=""){
    $act = unprotect(rawurldecode($_GET['act']));
    // echo "<pre>";
    // print_r($act);
    // echo "</pre>";
    $action = explode("-", $act);
    $check_login = loginByTickle($action[0]);
    if ($check_login!=0){
        redirect('login');
    }
    $_SESSION['PrevTickleTaskID']=$action[1];
    $GLOBALS['mode']=1;
    if(isset ($_REQUEST['suspended'])){
    $task=$db->select_to_array('task',''," Where TaskID='".$_SESSION['PrevTickleTaskID']."' and TickleID='".$_SESSION['TickleID']."' and Status !='S'");    
    }elseif(isset($_REQUEST['Activitylogs'])){ 
	$task=$db->select_to_array('task',''," Where TaskID='".$_SESSION['PrevTickleTaskID']."' and TickleID='".$_SESSION['TickleID']."' and (Status='D')");  
	}else{
    $task=$db->select_to_array('task',''," Where TaskID='".$_SESSION['PrevTickleTaskID']."' and TickleID='".$_SESSION['TickleID']."' and Status!='D'");
    }
    
    if (!is_array($task) || count($task)<1){
        $GLOBALS['mode']=6;return;
    }
}

if(isset ($_REQUEST['suspended'])){
$task=$db->select_to_array('task',''," Where TaskID='".$_SESSION['PrevTickleTaskID']."' and TickleID='".$_SESSION['TickleID']."' and Status='K'");
}
else{
$task=$db->select_to_array('task',''," Where TaskID='".$_SESSION['PrevTickleTaskID']."' and TickleID='".$_SESSION['TickleID']."' and (Status='Y' or Status='S')");  
}
$tickletrainid=$task[0]['TickleTrainID'];
$FollowTickleTrainID=$task[0]['FollowTickleTrainID'];

$tickle=$db->select_to_array('tickle',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='".$tickletrainid."'");
if(isset ($_REQUEST['suspended'])){
    $user_mail=$db->select_to_array('user_mail',''," Where TickleID='".$_SESSION['TickleID']."' and Status='K' and MailID='".$_SESSION['PrevTickleMailID']."'");
}elseif(isset($_REQUEST['Activitylogs'])){ 
	 $user_mail=$db->select_to_array('user_mail',''," Where TickleID='".$_SESSION['TickleID']."' and Status='D' and MailID='".$_SESSION['PrevTickleMailID']."'");   
}
else{
 $user_mail=$db->select_to_array('user_mail',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and MailID='".$_SESSION['PrevTickleMailID']."'");   
}

$HasFollowup=$task[0]['HasFollowup'];
$TickleContact=$tickle[0]['TickleContact'];
$contact_list=$db->select_to_array('contact_list',''," Where ContactID='".$user_mail[0]['ContactID']."' and TickleID='".$_SESSION['TickleID']."'");
$FirstName=@trim($contact_list[0]['FirstName']);
$FirstName = str_replace("'","",$FirstName);
$LastName=@trim($contact_list[0]['LastName']);
$LastName= str_replace("'","",$LastName);

$sql_files = mysqli_query($db->conn,"select * from files where FileContext='tickle' and FileParentID='".$tickle[0]['TickleTrainID']."'");
$TAttach = array();
while ($rs_files = mysqli_fetch_array($sql_files)) {
$fname=$FirstName;
	//echo FULL_UPLOAD_FOLDER,$fname;
    $fname = @trim($rs_files['FileName']);
    if ($fname != "" && file_exists(FULL_UPLOAD_FOLDER . $fname)) {
        $TAttach[] = FULL_UPLOAD_FOLDER . $fname;
    }
}
mysqli_free_result($sql_files);

$AttachOriginalMessage = $tickle[0]['AttachOriginalMessage'];
$TickleMailContent=$tickle[0]['TickleMailContent'];

if($FollowTickleTrainID>0)
{
    $ticklefollow=$db->select_to_array('ticklefollow',''," Where TickleID='".$_SESSION['TickleID']."' and FollowTickleTrainID='".$FollowTickleTrainID."'");
    $TickleMailContent=$ticklefollow[0]['TickleMailFollowContent'];
    $sql_files = mysqli_query($db->conn,"select * from files where FileContext='ticklefollow' and FileParentID='" . $FollowTickleTrainID . "'");
    $TAttach = array();
    while ($rs_files = mysqli_fetch_array($sql_files)) {
        $fname = @trim($rs_files['FileName']);
        if ($fname != "" && file_exists(FULL_UPLOAD_FOLDER . $fname)) {
            $TAttach[] = FULL_UPLOAD_FOLDER . $fname;
        }
    }
    mysqli_free_result($sql_files);
    $AttachOriginalMessage = $tickle[0]['AttachOriginalMessage'];
}
if ($FirstName!=""){
    $TickleMailContent= preg_replace("/\[FirstName\]/i",$FirstName,$TickleMailContent);
}
if ($LastName!=""){
    $TickleMailContent= preg_replace("/\[LastName\]/i", $LastName,$TickleMailContent);
}

$TickleMailContent = preg_replace('/\[signature\]/i', $_SESSION['signature'], $TickleMailContent);
$TickleMailContent = preg_replace('/\[FirstName\]/i', "", $TickleMailContent);
$TickleMailContent = preg_replace('/\[LastName\]/i', "", $TickleMailContent);
//$TickleMailContent = trim(trim(preg_replace('/[\s]+(\s?,\s?,?)+[\s]+/i', ", ", $TickleMailContent)),",");
if($user_signature['mail_type']!='text'){
    $TickleMailContent = preg_replace('/((&nbsp;)*|\s*)*,((&nbsp;)*|\s*)*/i', ", ", $TickleMailContent);
}


//echo $TickleMailContent;
//die();

//$TickleMailContent = urlencode($TickleMailContent);

$UMessage=$user_mail[0]['Message'];
$USubject = $user_mail[0]['Subject'];
$UMessageHtml=$user_mail[0]['MessageHtml'];
$UMessageRaw=$user_mail[0]['MessageRaw'];
$MailHeader=ReadHeader($user_mail[0]['MailHeader']);
if($UMessageHtml=="")
{
    //$UMessageHtml = "<pre style='white-space:normal;'>$UMessage</pre>";
    $UMessageHtml = preText($UMessage);
}

$UMessageHtml= preg_replace("/\[FirstName\]/i",$FirstName,$UMessageHtml);
$UMessageHtml= preg_replace("/\[LastName\]/i", $LastName,$UMessageHtml);
$UMessageHtml = str_replace($_SERVER['DOCUMENT_ROOT'], "", $UMessageHtml);

$UMessageHtml = str_replace("/mail/","https://client.tickletrain.com/mail/",$UMessageHtml);
$UMessageHtml = preg_replace('/<base[^>]+\>/i', '', $UMessageHtml);
$send_date = $user_mail[0]['Date'];
if ($_REQUEST['Mails']!="Mail"){
	$send_date = date("r", strtotime($task[0]['TaskInitiateDate']));
}
if($_REQUEST['Mails'] == "Tickle"){
	 $AttMessageHeader .="<div style='clear:left'><label style='width:55px;float:left'>From:</label>".htmlspecialchars($user_mail[0]['senderaddress'])."</div>";
}
else{
	$AttMessageHeader .="<div style='clear:left'><label style='width:55px;float:left'>From:</label>".htmlspecialchars($user_mail[0]['fromaddress'])."</div>";
}
$AttMessageHeader .="
<div style='clear:left'><label style='width:55px;float:left'>Sent:</label>".$send_date."</div>
<div style='clear:left'><label style='width:55px;float:left'>To:</label>".htmlspecialchars($contact_list[0]['EmailID'])."</div>";
if($user_mail[0]['ccaddress']!="")
{
	$AttMessageHeader.=" <div style='clear:left'><label style='width:55px;float:left'>Cc:</label>".htmlspecialchars($user_mail[0]['ccaddress'])."</div>";
}
if(isset($_REQUEST['TaskID']) && $_REQUEST['TaskID']!="" && $_REQUEST['Mails'] == "Tickle"){
$GetCustomSubject = GetCustomSubject($_REQUEST['TaskID']);    
}       
if($GetCustomSubject && $GetCustomSubject != ""){
      $AttMessageHeader.=" <div style='clear:left'><label style='width:55px;float:left'>Subject:</label>".$GetCustomSubject."</div>";  
     }
     else{
        $AttMessageHeader.=" <div style='clear:left'><label style='width:55px;float:left'>Subject:</label>".$user_mail[0]['Subject']."</div>";
   }
   
   
   
//   echo "<pre>";
//   echo $UMessageHtml;
//   print_r($_REQUEST);
//   print_r($_SESSION);
//   echo "</pre>";
//   die();

//$HTMLContent=$HTMLContent."<br /><blockquote type='cite'>".nl2br($AttMessageHeader)."<br />".$UMessageHtml."</blockquote>";
//$HTMLContent=$HTMLContent.nl2br($AttMessageHeader)."<br />".$UMessageHtml;//."</blockquote

if($UMessageHtml != strip_tags($UMessageHtml)){
  $UMessageHtml = $UMessageHtml;
}else{
  $UMessageHtml = nl2br($UMessageHtml);
}
   
$HTMLContent=$AttMessageHeader."<br/>".$HTMLContent.$UMessageHtml;//."</blockquote
if (@trim($user_mail[0]['attachments'])!=""){
    $attsFiles = GetMailAttachments($user_mail[0]['RawPath'],$user_mail[0]['attachments']);
    if (count($attsFiles)!=0){
        if ($_REQUEST['Mails']=='MailAttach'){
            $HTMLContent="";
        }
        $basepath = preg_replace("/\.txt$/i", "/", $user_mail[0]['RawPath']);
        $HTMLContent.="<hr/><b>Attachments:</b>";//.preg_replace("/[,]/","<br/>",@trim($user_mail[0]['attachments']));
        $basepath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $basepath);
        for ($at=0;$at<count($attsFiles);$at++){
                $HTMLContent.="<br/><a href='".$basepath.$attsFiles[$at]."' onclick='wopen(this);return false'>".$attsFiles[$at]."</a>";
        }
    }
}

if ($AttachOriginalMessage != "N") {
  //die("@What about Here ???");  
  //$TickleMailContent = $TickleMailContent . "<br /><br /><blockquote type='cite'>" . $HTMLContent . "</blockquote>";
}
if (count($TAttach)!=0){
    $TickleMailContent.="<hr/><b>Attachments:</b>";//.preg_replace("/[,]/","<br/>",@trim($user_mail[0]['attachments']));

    for ($at=0;$at<count($TAttach);$at++){
            $fname=utf8_basename($TAttach[$at]);
            $basepath = str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname($TAttach[$at]));
            $TickleMailContent.="<br/><a href='".$basepath."/".$fname."' onclick='wopen(this);return false'>".$fname."</a>";
    }
}

if($_REQUEST['Mails']!="Mail" && $_REQUEST['Mails']!="MailAttach"){
    if($user_signature['mail_type']=='text'){
          $TickleMailContent = nl2br($TickleMailContent);
      }
     echo "<blockquote style='margin-left:1px'>".$AttMessageHeader."</blockquote><hr/><br />" . $TickleMailContent;
}

/*
$hm = GetHomeDir();
$rt = GetRootFolder();
$HTMLContent = str_replace($hm,$rt,$HTMLContent);
$hm = str_replace("/tickletrain.com","/server.tickletrain.com",$hm);
$HTMLContent = str_replace($hm,$rt,$HTMLContent);
*/
if ($GLOBALS['mode']==1){
    $str = str_replace("$",urlencode("$"),$HTMLContent);
    $GLOBALS['hcontent']=preg_replace("/[\\r\\n]+/",'',$str);
    $GLOBALS['subject']=$USubject;
    return;
}
if($_REQUEST['Mails']=="Mail" || $_REQUEST['Mails']=="MailAttach")
{   
	if($user_signature['mail_type']=='text'){
          $HTMLContent = nl2br($HTMLContent);
        }
	echo $HTMLContent;
}
if($_REQUEST['TaskID']=="")
{
	$_SESSION['PrevTickleTaskID']="";
	$_SESSION['PrevTickleMailID']="";
}

exit;
?>
