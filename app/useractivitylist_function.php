<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<?php
define('SERVER_NAME', "client.tickletrain.com");
if(isset($_SESSION['TickleID']))
	$tickleId = @trim($_SESSION['TickleID']);
else 
	$tickleId = base64_decode($_GET['ttuser']);
$gid = @trim($_GET['gid']);
$q = @trim($_GET['q']);
//sorting
$sort = @trim($_REQUEST['sort']);
$sfld = 1;
$sord = 1;

if ($sort!=""){
    list($sfld,$sord)=explode("-",$sort,2);
    if (!@intval($sfld)){
        $sfld=1;
    }else{
        $sfld = @intval($sfld);
    }
    if (!@intval($sord)){
        $sord=1;
    }else{
        $sord = @intval($sord);
    }
}
$Variables['sfld']=$sfld;
$Variables['sord'] = $sord;
$sfld--;
$sord--;

$sortfields = array("t.subject","comdate","toadrs","t.TickleName");
$sortorders = array("asc","desc");

// get completed data

//SELECT t.* FROM (select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate,user_mail.subject,tickle.TickleName,task.* from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) inner join category as cat on (cat.CategoryName=tickle.TickleName) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID=$tickleId and task.Status='D' AND cat.TickleID= $tickleId GROUP BY task.MailID ORDER by task.TaskCretedDate DESC limit 10) AS t WHERE ORDER BY t.TickleName DESC

 $sqlC = "select t.* FROM (select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate,tickle.TickleName,task.*,task.TaskDeletedDate as comdate,user_mail.toaddress as toadrs,user_mail.subject,user_mail.fromaddress,cat.CategoryID from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) inner join category as cat on (cat.CategoryName=tickle.TickleName)
left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID=$tickleId and task.Status='D' AND cat.TickleID= $tickleId AND task.TaskDeletedDate >= DATE_SUB(CURDATE(), INTERVAL 10 DAY)  ";
if ($gid!=""){  
    $sqlC.=" and (cat.CategoryID)=".@intval($gid);
}

if ($q!=''){
    $sqlC.=" and (tickle.TickleName like '%$q%' or user_mail.subject like '%$q%' )";
}

//GROUP BY task.MailID ORDER by task.TaskCretedDate DESC limit 10) AS t WHERE ORDER BY t.TickleName DESC


$sqlC .= ' GROUP BY task.MailID ORDER by task.TaskCretedDate DESC) AS t ';

$sqlC.= "ORDER BY ".$sortfields[$sfld]." ".$sortorders[$sord];

//echo $sqlC; 

/** pagination **/

if(isset($_REQUEST['recordperpage'])){
$perpage = $_REQUEST['recordperpage'];
$_SESSION['contact_per_page'] = $_REQUEST['recordperpage'];
?>

	<?php if($perpage=='10') { ?><script>$(document).ready(function(){
	$('#selectrecac option:nth-child(1)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='25') {  ?><script>$(document).ready(function(){
	$('#selectrecac option:nth-child(2)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='50') { ?><script>$(document).ready(function(){
	$('#selectrecac option:nth-child(3)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='100') { ?><script>$(document).ready(function(){
	$('#selectrecac option:nth-child(4)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($_REQUEST['pg']>1 && $perpage>10){
            redirect('useractivitylist');
        } ?>

<?php } 
else if(isset($_SESSION['contact_per_page'])){
        $perpage = $_SESSION['contact_per_page'];
	if($perpage=='10') { ?>
	<script>$(document).ready(function(){
	$('#selectrecac option:nth-child(1)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='25') { ?><script>$(document).ready(function(){
	$('#selectrecac option:nth-child(2)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='50') { ?><script>$(document).ready(function(){
	$('#selectrecac option:nth-child(3)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='100') { ?><script>$(document).ready(function(){
	$('#selectrecac option:nth-child(4)').attr('selected', 'selected'); });
	</script>
	<?php } 
}

else{
	$perpage = 10;
}

$cnt = mysqli_num_rows(mysqli_query($db->conn,$sqlC));
$list = mysqli_num_rows(mysqli_query($db->conn,$sqlC));

$pg=max(1,intval($_REQUEST['pg']));
$pc=intval(GetVal($_REQUEST['pc'],$perpage));
$ps = ceil($cnt/$pc);
$check = 10;

echo $sqlC.=" limit ".($pg-1)*$pc.", $perpage ";

$Clist = $db->query_to_array($sqlC);
//echo '<pre>';
//print_r($Clist);
$Variables['Clist']= $Clist;

//echo "select distinct TickleContact as CategoryID, TickleName as CategoryName from tickle where TickleID=$tickleId order by TickleContact";
$glist = $db->query_to_array("select distinct TickleContact as CategoryID, TickleName as CategoryName from tickle where TickleID=$tickleId order by CategoryName ASC");
$Variables['glist']= $glist;
$Variables['ps']=$ps;
$Variables['pg']=$pg;
$Variables['pc']=$pc;
$Variables['cnt']=$cnt;
$Variables['gid']=$gid;
$Variables['search']=($q!='');


/*** pop up window ***/

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
   // $GLOBALS['mode']=1;
    if(isset ($_REQUEST['suspended'])){
    $task=$db->select_to_array('task',''," Where TaskID='".$_SESSION['PrevTickleTaskID']."' and TickleID='".$_SESSION['TickleID']."' and Status !='S'");    
    }elseif(isset($_REQUEST['Activitylogs'])){ 
	$task=$db->select_to_array('task',''," Where TaskID='".$_SESSION['PrevTickleTaskID']."' and TickleID='".$_SESSION['TickleID']."' and (Status='D')");  
	}else{
    $task=$db->select_to_array('task',''," Where TaskID='".$_SESSION['PrevTickleTaskID']."' and TickleID='".$_SESSION['TickleID']."' and Status!='D'");
    }
    
    if (!is_array($task) || count($task)<1){
      //  $GLOBALS['mode']=6;return;
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

$comments = get_comments($_SESSION['PrevTickleMailID']);
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
			    $filext = explode('.',$attsFiles[$at]);
				$filesxttt = $filext[1]; 
				 $RawmaildirPSD = "/var/www/vhosts/client.tickletrain.com/httpdocs".$basepath;
				if($filesxttt == 'psd' || $filesxttt == 'PSD') {
					$url = "https://client.tickletrain.com/psddownload.php?filePath=".urlencode($RawmaildirPSD)."&filePSD=".urlencode($attsFiles[$at])."";
					$HTMLContent.="<br/><a href='".$url."' onclick='wopen(this);return false'>".$attsFiles[$at]."</a>";
				}
				else
					$HTMLContent.="<br/><a href='".$basepath.$attsFiles[$at]."' onclick='wopen(this);return false'>".$attsFiles[$at]."</a>";
        }
    }
}

if(!empty($comments)){
	
	$HTMLContent.="<div class='comentshrsec'><hr><ul class='comment-list'>";
	
	foreach($comments as $cmntlist) {	
		$HTMLContent.='<li>';
		$HTMLContent.='<h6>'.get_comment_user($cmntlist).'<span class="float-right pr-2 small">'.$cmntlist['created_at'].' at '.$cmntlist['created_time'].'</span></h6>';
		$HTMLContent.='<p class="small">'.$cmntlist['comment'].'</p>';
		$HTMLContent.='</li>';					
	}
	$HTMLContent.="</div></ul>";
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
   //  echo "<blockquote style='margin-left:1px'>".$AttMessageHeader."</blockquote><hr/><br />" . $TickleMailContent;
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
?>