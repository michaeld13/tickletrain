<?php
error_reporting(E_ERROR | E_PARSE);
$Variables = array();
$taskId = @intval($_GET['TaskID']);
$tickleId = @trim($_SESSION['TickleID']);//die();
$btaskId = $_REQUEST['TaskID'];
$baction = @trim($_REQUEST['baction']);
/*Start Checking Last Cron Time */
if(isset($_REQUEST['lastcron'])){
    $lastcronrow = $db->select_rows('lastcron',"*","",ASSOC);
    $lastcrontime = $lastcronrow[0]['lastcron'];
    $currenttime = date("H:i:s");
    $cronresponsearray = array('lastcrontime'=>$lastcrontime,'currentime'=>$currenttime);
    echo json_encode($cronresponsearray);
    die();
}
/*End Checking Last Cron Time */

/* Start checking campaign exists for beginners */
if(isset($_POST['taskexist'])){
    $taskexist = $db->get_count('task',"where TickleID='".$_SESSION[TickleID]."'");
    die($taskexist);
}

/* End of checking exist campaigns */

$EmailID = $db->select_rows('tickleuser',"*","where TickleID='$_SESSION[TickleID]]'",ASSOC);
/* Check for welocme or suspend Display */
$count_campaign_status_D = $db->get_count('task',"where TickleID='".$_SESSION[TickleID]."'");
if($count_campaign_status_D > 0){
    $Variables['campaign_exist'] = 'campaign_exist'; 
}
/* End of welocme or suspend Display */
$email = $EmailID['0']['EmailID'];
$firsname = $EmailID['0']['FirstName'];
$Variables['userName'] = $EmailID['0']['UserName'];
$Variables['mainemail'] = $email;
$autoauthkey = "abcXYZ123";
$timestamp = time(); # Get current timestamp
$hash = sha1($email . $timestamp . $autoauthkey); # Generate Hash
$Variables['faqlink'] = 'https://secure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='. urlencode("knowledgebase.php?action=displaycat&catid=1"); 
$Variables['kblink'] = 'https://secure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='. urlencode("knowledgebase.php?action=displayarticle&id=16"); 
$Variables['submitticket'] = 'https://secure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='. urlencode("submitticket.php?step=2&deptid=1"); 
$Variables['firstname'] = $firsname;

if(isset($_GET['dierctserviceid'])){
    header("location:https://secure.tickletrain.com/dologin.php?email=$email&timestamp=$timestamp&hash=$hash&goto=". urlencode("upgrade.php?type=package&id=$_GET[dierctserviceid]"));
}

foreach($EmailID as $email1){
    $newemail['EmailID'] = $email1['EmailID'];
}

$checkmail = whmcs_checkmail($newemail);
//print_r($checkmail);
 if(isset($checkmail) && $checkmail['userid'] != ""){
     
    $getorder = whmcs_getorder($checkmail['userid']);
    $getserviceid = whmcs_getproducdetails($getorder['userid']);

    if($getserviceid->products->product[0]->id!=""){
        $serviceid = $getserviceid->products->product[0]->id;

	// 21-mar-2016

	$acturl = urlencode("cart.php?a=add&billingcycle=annually&pid=4");

	$Variables['supportpage'] = 'https://secure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='. $acturl;
	// 21-mar-2016

      //  $Variables['supportpage'] = 'https://sbsecure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='. urlencode("upgrade.php?type=package&id=$serviceid");
	
        $Variables['makepaymet'] = 'https://secure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='. urlencode("clientarea.php?action=invoices"); 
    }
    
  $subjectarray_lastcheck = $db->select_rows('user_mail',"MailID,Subject,Message","where TickleID='$tickleId' and Status='Y'",ASSOC);  
   $last_check = count($subjectarray_lastcheck); 
//  die('dfgd');  
    
 $subjectarray = $db->select_rows('user_mail',"MailID,Subject,Message","where TickleID='$_SESSION[TickleID]]' and Status='N'",ASSOC);
  foreach($subjectarray as $subarray){
    $newsubarray[$subarray['MailID']] = $subarray['Subject'].'('.  trim(substr($subarray['Message'], 0,8)).')';
 }
    
   
    
   
  // if(isset ($getorder[invoiceid]) && $getorder[invoiceid]!=""){
       //if($getorder[invoiceid]!="" && $getorder[invoiceid]!="0"){
       $Variables['paynowlink'] = 'https://secure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='. urlencode("clientarea.php?action=invoices"); 
       //die('fhfhgf');
       
    //   }
      
  //  }
    
 }
 
 
$allowed_compaign = $db->select_rows('Compaign',"Allowe_campaign,warningthresold","where TickleID='$_SESSION[TickleID]'",ASSOC);
foreach($allowed_compaign as $allowcamp){
 $campaignallowed = $allowcamp['Allowe_campaign'];
 $warningthresold = $allowcamp['warningthresold'];
 $Variables['campaignallowed'] = $allowcamp['Allowe_campaign'];
 $Variables['warningthresold'] = $allowcamp['warningthresold'];
}
//echo $last_check;
//echo $getserviceid->products->product[0]->status;
//die();

if(isset($_POST['downgradeplanhere'])){
    
     $count1 = count($_POST['mailid']);
     $_SESSION['allowedhere'];
     
     if($count1 <= $_SESSION['allowedhere']){
         
      if($_POST['mailid'][0]!=""){
       
      foreach($_POST['mailid'] as $mailid){
          
          $updatetaskstatus = $db->update('task',"Status='Y'","where MailID = '$mailid' and TickleID = '$tickleId'");
          $updatemailstatus = $db->update('user_mail',"Status = 'Y'","where MailID = '$mailid' and TickleID = '$tickleId'");
         // $updateallowedcampaig = $db->update('Compaign',"Allowe_campaign=$_GET[allowcampaign],Plan=$_GET[npid]","where TickleID = '$tickleId'");
          }
          //die('6666');
          unset ($_SESSION['allowedhere']);
          unset ($_SESSION['downgradecheck']);
          unset($_SESSION['totalcampaign']);
          
        //  die('fdgdfgdf');
         // $serviceid1 = $_GET['packid'];
         // $npid = $_GET['npid'];
         // $userid = $_GET['userid'];
         // $upgrade = whmcs_upgrade_new_check($serviceid1,$userid,$npid);
        // echo '<pre>';
        // print_r($upgrade);
        //  if($upgrade == 'success'){
        //  $suspend_for_whmcs = whmcs_unsuspend($serviceid);
          //die('unsuspend');
         // if($suspend_for_whmcs == 'success'){
          header("location:https://client.tickletrain.com/registermessage/?upgrade=complete&planchange=success");
          die('788988');
        //  }else{
         //     die('error');
         // }
          }
          
     
      
     }else{
         $Variables['morethenallowed'] = "Sorry you can not choose more than $_SESSION[allowedhere] .";
     }
 }


 
 
//$countcurrentcampaign = $db->select_rows('user_mail',"Count(*) as 'c'","where TickleID='$_SESSION[TickleID]]'",ASSOC);
//$mainarray = $db->select_rows('user_mail',"MailID","where TickleID='$_SESSION[TickleID]]'",ASSOC);

$Variables['tickleid'] = $tickleId;


//$num = 1;
//foreach($mainarray as $nain){
  //   if($num > $allowed_compaign[0]['Allowe_campaign']){
         
    //    $deletetask = $db->delete('task',"where MailID = '$nain[MailID]' and TickleID = '$tickleId'");
      //  $deletemail = $db->delete('user_mail',"where MailID = '$nain[MailID]' and TickleID = '$tickleId'");
       //  $updatetaskstatus = $db->update('task',"Status='N'","where MailID = '$nain[MailID]' and TickleID = '$tickleId'");
        // $updatemailstatus = $db->update('user_mail',"Status = 'N'","where MailID = '$nain[MailID]' and TickleID = '$tickleId'");
     //}
   //  $num++;
//}

$statusarray = $db->select_rows('task',"MailID,Status","where TickleID='$_SESSION[TickleID]]'",ASSOC);

//echo '<pre>';
//print_r($statusarray);
//echo '</pre>';
//die();
if($getserviceid->products->product[0]->status == 'Suspended'){
  $inactivestatusarray = 'inactive';  
} 


if(isset($_POST['choosecompaign'])){
    $_POST['mailid'] = array_unique($_POST['mailid']);
    $count = count($_POST['mailid']);
     if($count<10){
      if($_POST['mailid'][0]!=""){
     foreach($_POST['mailid'] as $mailid){
          $updatetaskstatus = $db->update('task',"Status='Y'","where MailID = '$mailid' and TickleID = '$tickleId'");
          $updatemailstatus = $db->update('user_mail',"Status = 'Y'","where MailID = '$mailid' and TickleID = '$tickleId'");
          $updateallowedcampaig = $db->update('Compaign',"Allowe_campaign = '10',Plan='1'","where TickleID = '$tickleId'");
          }

          $upgrade = whmcs_upgrade($serviceid,$checkmail['userid']);
         
          if($upgrade == 'success'){
          $suspend_for_whmcs = whmcs_unsuspend($serviceid);
          //die('unsuspend');
          if($suspend_for_whmcs == 'success'){
          header("location:https://client.tickletrain.com/dashboard/");
          }else{
              die('error');
          }
          }
      }
     }else{
         $Variables['morethenfive'] = "Sorry you can not choose more than ten .";
     }
 }

//bulk actions
if ($baction!='' && is_array($btaskId)){
    switch($baction){
        case 'delete':
            $db->delete('task', array("WHERE TickleID='$tickleId' and TaskID in (".join(",",$btaskId).") and Status='Y'"));
            break;
        case 'approve':
            $db->update('task', array("Approve" => "Y"), array("WHERE TaskID in (".join(",",$btaskId).") and  TickleID='$tickleId'"));
            break;
        case 'unapprove':
            $db->update('task', array("Approve" => "N"), array("WHERE TaskID in (".join(",",$btaskId).") and  TickleID='$tickleId'"));
            break;
        case 'pause':
            $db->query("update task as t inner join tickle as tt on (t.TickleTrainID=tt.TickleTrainID) set t.Pause='Y' where t.TaskID in (".join(",",$btaskId).") and ifnull(t.FollowTickleTrainID,0)=0 and t.Pause='N' and (t.Approve='Y' or tt.TApprove='N') and t.TickleID='$tickleId'");
            $db->query("update task as t inner join ticklefollow as tt on (t.FollowTickleTrainID=tt.FollowTickleTrainID) set t.Pause='Y' where t.TaskID in (".join(",",$btaskId).") and ifnull(t.FollowTickleTrainID,0)>0 and t.Pause='N' and (t.Approve='Y' or tt.TApprove='N') and t.TickleID='$tickleId'");
            //$db->update('task', array("Pause" => "Y"), array("WHERE TaskID in (".join(",",$btaskId).") and  Pause='N' and (Approve='Y' or TApprove='N') and TickleID='$tickleId'"));
            break;
        case 'unpause':
            $db->update('task', array("Pause" => "N"), array("WHERE TaskID in (".join(",",$btaskId).") and  TickleID='$tickleId'"));
            $db->update('task', array("Approve" => "Y"), array("WHERE TaskID in (".join(",",$btaskId).") and  TickleID='$tickleId'"));
            break;
    }
}




$action = $_GET['Delete'];

if ($action == "Y" && $taskId) {

    if ($_GET['DeleteAll'] == "Y") {
        $Del = $db->select_to_array('task', '', " Where TickleID='$tickleId' and TaskID ='$taskId'");
        $DelMailid = $Del[0]['MailID'];
        $db->delete('task', array("WHERE TickleID='$tickleId' and MailID='$DelMailid' and Status='Y'")); //TaskID ='".$_GET['TaskID']."' and
        $db->delete('user_mail', array("WHERE MailID='$DelMailid'"));
    } else {
        $db->delete('task', array("WHERE TaskID ='$taskId' and  TickleID='$tickleId'"));
    }
	$surl = '?';
	foreach(json_decode(base64_decode($_GET['redirectUrl'])) as $key => $redirectUrl01)
	{
		if($key!='u'){ $surl .= $key.'='.$redirectUrl01.'&';} 
	}			
	header("location:https://client.tickletrain.com/home/".substr($surl,0,-1)."#".$_GET['hashtag']);
	
}

/*$action = $_GET['Approve'];

if ($action == "Y" && $taskId) {
    if ($_GET['ApproveAll'] == 'Y') {
        $Upd = $db->select_to_array('task', '', " Where TickleID='$tickleId' and TaskID ='$taskId'");
        $UpdMailid = $Upd[0]['MailID'];
        $db->update('task', array("Approve" => "Y"), array("WHERE TickleID='$tickleId' and MailID='$UpdMailid'")); //TaskID ='".$_GET['TaskID']."' and
    } else {
        $db->update('task', array("Approve" => "Y"), array("WHERE TaskID ='$taskId' and  TickleID='$tickleId'"));
    }

    redirect("home");
}

if ($action == "N" && $taskId) {
    $db->update('task', array("Approve" => "N"), array("WHERE TaskID ='$taskId' and  TickleID='$tickleId'"));
    redirect("home");
}
*/
$action = $_GET['Pause'];
if ($action == "Y" && $taskId) {
    if ($_GET['PauseAll'] == 'Y') {
        $Upd = $db->select_to_array('task', '', " Where TickleID='$tickleId' and TaskID ='$taskId'");
        $UpdMailid = $Upd[0]['MailID'];
        $db->update('task', array("Pause" => "Y"), array("WHERE TickleID='$tickleId' and MailID='$UpdMailid'")); //TaskID ='".$_GET['TaskID']."' and
    } else {
        $db->update('task', array("Pause" => "Y"), array("WHERE TaskID ='$taskId' and  TickleID='$tickleId'"));
    }

   // redirect("home");
   $surl = '?';
	foreach(json_decode(base64_decode($_GET['redirectUrl'])) as $key => $redirectUrl01)
	{
		if($key!='u'){ $surl .= $key.'='.$redirectUrl01.'&';} 
	}			
	header("location:http://client.tickletrain.com/home/".substr($surl,0,-1)."#".$_GET['hashtag']);
}

$action = $_GET['UnPause'];
if ($action == "Y" && $taskId) {
    if ($_GET['UnPauseAll'] == 'Y') {
        $Upd = $db->select_to_array('task', '', " Where TickleID='$tickleId' and TaskID ='$taskId'");
        $UpdMailid = $Upd[0]['MailID'];
        $db->update('task', array("Pause" => "N"), array("WHERE TickleID='$tickleId' and MailID='$UpdMailid'")); //TaskID ='".$_GET['TaskID']."' and
        $db->update('task', array("Approve" => "Y"), array("WHERE TickleID='$tickleId' and MailID='$UpdMailid'")); //TaskID ='".$_GET['TaskID']."' and
    } else {
        $db->update('task', array("Pause" => "N"), array("WHERE TaskID ='$taskId' and  TickleID='$tickleId'"));
        $db->update('task', array("Approve" => "Y"), array("WHERE TaskID ='$taskId' and  TickleID='$tickleId'"));
    }
	
	$surl = '?';
	foreach(json_decode(base64_decode($_GET['redirectUrl'])) as $key => $redirectUrl01)
	{
		if($key!='u'){ $surl .= $key.'='.$redirectUrl01.'&';} 
	}			
	header("location:https://client.tickletrain.com/home/".substr($surl,0,-1)."#".$_GET['hashtag']);
	
    //redirect("home");
}
if (!isset($_SESSION['access_token']) && $_SESSION['TickleID'] != "" && isset($_COOKIE['access_token'])){
    $_SESSION['access_token'] = $_COOKIE['access_token'];
    $d = getdate();
    $_SESSION['access_token_time']=$d[0];
    //$url = "http://".$_SERVER['SERVER_NAME']."/".ROOT_FOLDER."fb/gettoken/";
    //$_SESSION['FBCHECK']=1;
    //oredirect($url);
    //echo $url;exit;
    //phpinfo();exit;
    //echo callFb($url,true);exit;
    //session_start();
}
$q = @trim($_GET['q']);
$qdate=@trim($_GET['qdate']);
$fltdate = 0;
if ($qdate!=''){
    switch($qdate){
        case "1": $fltdate=time();break;
        case "2": $fltdate=strtotime("+3 days");break;
        case "3": $fltdate=strtotime("+7 days");break;
        case "4": $fltdate=strtotime("+14 days");break;
        case "5": $fltdate=strtotime("+1 month");break;
    }
}

$contact_list = $db->select_to_array('contact_list', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' order by ContactID");
$maddress = array();
for ($ee = 0; $ee < count($contact_list); $ee++) {
    $eml = $contact_list[$ee]['ContactID'];
    if (!isset($maddress[$eml])) {
        $maddress[$eml] = $contact_list[$ee];
    }
}
$Variables['maddress'] = $maddress;


//sorting
$sort = @trim($_REQUEST['sort']);
$sfld = 6;
$sord = 0;

if ($sort!=""){
    list($sfld,$sord)=explode("-",$sort,2);
    if (!@intval($sfld)){
        $sfld=6;
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
// commented by sandeep
// $sfld--;
// $sord--;

$sortorders = array("asc","desc");
//$sortfields = array("contact_list.FirstName","contact_list.LastName","contact_list.EmailID","Subject","TickleName","if(date_format(task.TaskInitiateDate,'%Y-%m-%d')<>date_format(FROM_UNIXTIME(unix_timestamp(now())+24*3600),'%Y-%m-%d'),'',date_format(task.TaskInitiateDate,'%H:%i:%s'))");
//$sortfields = array("contact_list.FirstName","contact_list.FirstName","contact_list.LastName","Subject","TickleName","task.MailID", "TaskDateTime");
$sortfields = array("TaskDateTime","contact_list.FirstName","contact_list.LastName","contact_list.EmailID","Subject","tickle.TickleName","TaskDateTime", "TaskDateTime");

//if(count($activestatusarray)=='0' && count($inactivestatusarray)!='0'){
if(isset($inactivestatusarray)){
 $dselect = "select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='Y'"; // task.Status='K' last changes 03-jun-16
  $mselect = "select distinct task.MailID, task.TaskID, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='Y'";  // task.Status='K' last changes 03-jun-16
}else{
$dselect = "select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='Y'";
$mselect = "select distinct task.MailID, task.TaskID, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='Y'";
}
if ($q!=""){
    $q = addslashes(urldecode($q));
    $mselect.=" and (FirstName like '%$q%' or LastName like '%$q%' or EmailID like '%$q%' or TickleName like '%$q%' or TickleMailContent like '%$q%' or Subject like '%$q%' or Message like '%$q%' or MessageHtml like '%$q%' or ifnull(ticklefollow.TickleMailFollowContent,'') like '%$q%')";
}
if ($fltdate){
    $mselect.=" and date_format(task.TaskInitiateDate,'%Y-%m-%d')<='".date("Y-m-d",$fltdate)."'";
}
$mselect.=" order by ".$sortfields[$sfld];


if ($sfld!=5){
    $mselect.=" ".$sortorders[$sord];
}

$dselect.=" order by TaskInitiateDate ASC";



$dates = array();
$mArr = $db->query_to_array($dselect);
foreach($mArr as $row){
    $dates[$row['TaskDate']]=1;
}
$Variables['dates'] = $dates;
$mArr = $db->query_to_array($mselect);
$mails=array();
$sMails=array();
foreach($mArr as $row){
    if (!isset($sMails[$row['MailID']])){
        $sMails[$row['MailID']]=$row['TaskDateTime'];
    }
}
if ($sfld==5 && !$sord){
    asort($sMails);
}
if ($sfld==5 && $sord){
    arsort($sMails);
}

foreach ($sMails as $mid=>$val){
    $mails[]=$mid;
}

//echo '<pre>';
//print_r($mails);
//echo '</pre>';
//echo $campaignallowed;
//echo $tickleId;
//die();
/*
$mails_for_deleting = $mails;

$num = 0;
foreach($mails_for_deleting as $ml){
    if($num >= $campaignallowed && !isset($_GET['npid']) && count($activestatusarray)!='0'){
      $check_deleted = 'deleted';
      $updatetaskstatus = $db->update('task',"Status='K'","where MailID = '$ml' and TickleID = '$tickleId'");
      $updatemailstatus = $db->update('user_mail',"Status = 'K'","where MailID = '$ml' and TickleID = '$tickleId'");
     // $deletetask = $db->delete('task',"where MailID = '$ml' and TickleID = '$tickleId'");
     // $deletemail = $db->delete('user_mail',"where MailID = '$ml' and TickleID = '$tickleId'");
    }
    $num++;
}
if(isset($check_deleted)){
    header("location:http://client.tickletrain.com/dashboard/");
}
*/

if(isset($_REQUEST['recordperpage'])){
$perpage = $_REQUEST['recordperpage'];
$_SESSION['dashboard_per_page'] = $_REQUEST['recordperpage'];
?>

	<?php if($perpage=='10') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(1)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='25') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(2)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='50') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(3)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='100') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(4)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($_REQUEST['pg']>1 && $perpage>10){
            redirect('home');
        } ?>

<?php } 

else if(isset($_SESSION['dashboard_per_page'])){
        $perpage = $_SESSION['dashboard_per_page'];
	if($perpage=='10') { ?>
	<script>$(document).ready(function(){
	$('#selectrec option:nth-child(1)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='25') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(2)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='50') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(3)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='100') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(4)').attr('selected', 'selected'); });
	</script>
	<?php } 
}

else{
	$perpage = 10;
}


$pg=max(1,intval($_REQUEST['pg']));
$pc=intval(GetVal($_REQUEST['pc'],$perpage));
$ps = ceil(count($mails)/$pc);
$tasks=array();
$flt = array(0);
for($i=($pg-1)*$pc;$i<$pg*$pc && $i<count($mails);$i++){
    $flt[]=$mails[$i];
    $tasks[$mails[$i]]=array();
}

//if(count($activestatusarray)=='0' && count($inactivestatusarray)!='0'){
if(isset($inactivestatusarray)){
$select = "select tickle.TickleName as tname,task.*, user_mail.*, tickle.*, ticklefollow.*, ifnull(tickle.TApprove,'') as TTApprove, ifnull(ticklefollow.TApprove,'') as FollowTApprove, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='Y'";// task.Status='K' last changes 03-jun-16
//$select = "select t.* FROM(select tickle.TickleName as tname,task.*, user_mail.*, tickle.*, ticklefollow.*, ifnull(tickle.TApprove,'') as TTApprove, ifnull(ticklefollow.TApprove,'') as FollowTApprove, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='Y'";// task.Status='K' last changes 03-jun-16

}else{
$select = "select tickle.TickleName as tname,task.*, user_mail.*, tickle.*, ticklefollow.*, ifnull(tickle.TApprove,'') as TTApprove, ifnull(ticklefollow.TApprove,'') as FollowTApprove, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='Y'";    
//$select = "select t.* FROM(select tickle.TickleName as tname,task.*, user_mail.*, tickle.*, ticklefollow.*, ifnull(tickle.TApprove,'') as TTApprove, ifnull(ticklefollow.TApprove,'') as FollowTApprove, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='Y'";    

}
/*if ($q!=""){
    $select.=" and (TickleName like '%$q%' or TickleMailContent like '%$q%' or Subject like '%$q%' or Message like '%$q%' or MessageHtml like '%$q%' or ifnull(ticklefollow.TickleMailFollowContent,'') like '%$q%')";
}*/
$select.=" and task.MailID in (".join(",",$flt).")";
//$select.=" and task.MailID in (".join(",",$flt).")) as t";
$select.=" order by ".$sortfields[$sfld]." ".$sortorders[$sord];
//$select.=" ORDER BY TaskDateTime ASC;


if($getserviceid->products->product[0]->status == 'Active' && $getserviceid->products->product[0]->pid == 1 ){
  //echo "<h2>free plan </h2>";
  //$select.=" Limit 10";
}

//echo $select;

$taskArr = $db->query_to_array($select);


 if($getserviceid->products->product[0]->status == 'Active' && $getserviceid->products->product[0]->pid == 1 ){
    //$free_plan_limit = 10;
 }


//echo '<pre>';
//print_r($taskArr);


/*
foreach($taskArr as $row){
 
    if (!isset($tasks[$row['MailID']])){
        $tasks[$row['MailID']]=array();
    }
	
    $tasks[$row['MailID']][$row['TaskID']]=$row;
	

}
*/


foreach($taskArr as $row){
 
    if (!isset($tasks[$row['MailID']."=key"])){
        $tasks[$row['MailID']."=key"]=array();
    }
	
    $tasks[$row['MailID']."=key"][$row['TaskID']."=key"]=$row;
	

} 


//array_multisort($tasks, SORT_DESC, $taskArr);

$Variables['tfilter']=array(1=>"Show today", 2=>"Show 3 days", 3=>"Show 1 week", 4=>"Show 2 weeks", 5=>"Show 1 month");

// if(isset($_GET['test'])){
//     echo "<pre>";
//     print_r(($tasks[187322]) );
//     echo "</pre>";
// }


$Variables['tasks'] = $tasks;
$Variables['ps']=$ps;
$Variables['pg']=$pg;
$Variables['pc']=$pc;
$Variables['cnt']=count($mails);

//echo $last_check;
//die('cbvcbvc');
if(isset($_GET['npid']) && $_GET['npid']!= "" && $getserviceid->products->product[0]->status=='Active'){
$_SESSION['downgradecheck'] = 'yesdowngrade';
//$Variables['downgradecheck'] = 'yesdowngrade';
 $maileridarray = $db->select_rows('task', 'MailID,Status', "where TickleID='$tickleId' and Status = 'Y'", 'ASSOC');
 //$usermailidarray = $db->select_rows('user_mail', 'MailID,Status', "where TickleID='$tickleId' and Status = 'Y'", 'ASSOC');
 //$_SESSION['totalcampaign'] = count($usermailidarray);
 if(count($maileridarray)!=0){
  foreach($maileridarray as $mailerid){
         
          
          $updatetaskstatus = $db->update('task',"Status='K'","where MailID = '$mailerid[MailID]' and TickleID = '$tickleId' and Status = 'Y'");
          $updatemailstatus = $db->update('user_mail',"Status = 'K'","where MailID = '$mailerid[MailID]' and TickleID = '$tickleId' and Status = 'Y'");
       
    }
 }
      // $usermailidarray = $db->select_rows('user_mail', 'MailID,Status', "where TickleID='$tickleId' and Status = 'S'", 'ASSOC');
       $_SESSION['totalcampaign'] = count($mails);
       $_SESSION['allowedhere'] = $campaignallowed;
      // $Variables['allowedhere'] = $_GET['allowcampaign'];
}

elseif($last_check=='0' && $getserviceid->products->product[0]->status=='Active'){
    //echo $last_check;
    //die();
    //die('fhgfh');
   
//$Variables['downgradecheck'] = 'yesdowngrade';
 if($campaignallowed < count($mails)){
 $maileridarray = $db->select_rows('task', 'MailID,Status', "where TickleID='$tickleId' and Status = 'K' and Status!='Y'", 'ASSOC');

 if(count($maileridarray)!=0){
     $_SESSION['downgradecheck'] = 'yesdowngrade';
 /* foreach($maileridarray as $mailerid){
         
          
          $updatetaskstatus = $db->update('task',"Status='K'","where MailID = '$mailerid[MailID]' and TickleID = '$tickleId' and Status = 'Y'");
          $updatemailstatus = $db->update('user_mail',"Status = 'K'","where MailID = '$mailerid[MailID]' and TickleID = '$tickleId' and Status = 'Y'");
       
    } */
   $_SESSION['totalcampaign'] = count($mails);
   $_SESSION['allowedhere'] = $campaignallowed; 
 }
   //$usermailidarray = $db->select_rows('user_mail', 'MailID,Status', "where TickleID='$tickleId' and Status = 'S'", 'ASSOC');
 } 
}
elseif($getserviceid->products->product[0]->status!='Active'){
  unset ($_SESSION['allowedhere']);
  unset ($_SESSION['downgradecheck']);
  unset($_SESSION['totalcampaign']);   
}

 //echo $warningthresold;
       $percentahecampaign1 = ($campaignallowed*$warningthresold)/100; 
       $percentahecampaign = floor($campaignallowed-$percentahecampaign1); 

// echo $percentahecampaign; 

//if((count($mails) >= $percentahecampaign) && count($mails)!='0' && count($mails)!='' ){
if($campaignallowed<=10){
     $acturl = urlencode("cart.php?a=add&billingcycle=annually&pid=4"); //for multiple emails addon
    // $Variables['alertupgrademessage'] = 'https://sbsecure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='. urlencode("upgrade.php?type=package&id=$serviceid");
    $Variables['alertupgrademessage'] = 'https://secure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='.$acturl;
}

if(isset($_POST['countcampaignforwhmcs'])){
    die(count($mails).",".$campaignallowed);
}

$Variables['search']=($q!='' || $fltdate);
//if(count($activestatusarray)=='0' && count($inactivestatusarray)!='0'){
if(isset($inactivestatusarray)){
//$Variables['subject'] = $newsubarray;
//$Variables['mailid'] = $mailidarray;
$Variables['suspendedorder'] = 'suspended';
}


$newsarray = whmcs_getannouncements();
//if(!empty($newsarray)){
//foreach ($newsarray->announcements->announcement as $ann){
//$Variables['announcement'][$ann->id] = trim(strip_tags($ann->announcement));
//}
//}else{
//    return false;
//}

if(isset($_POST['changeEmail']) && $_POST['changeEmail']=='true'){
if($_POST['value']=='primary'){
	$mailid = mysqli_fetch_assoc(mysqli_query($db->conn,'select * from task where TaskID="'.$_POST['taskid'].'"'));
	$mailid = $mailid['MailID'];
	mysqli_query($db->conn,'update task set secondaryEmailId="0" where MailID="'.$mailid.'"');

	$userdetails = mysqli_fetch_assoc(mysqli_query($db->conn,'select * from tickleuser where TickleID="'.$_SESSION['TickleID'].'"'));

        mysqli_query($db->conn,'update user_mail set senderaddress = "'.$userdetails['FirstName'].' '.$userdetails['LastName'].' <'.$userdetails['EmailID'].'>" where MailID="'.$mailid.'"');
}
else{
	$mailid = mysqli_fetch_assoc(mysqli_query($db->conn,'select * from task where TaskID="'.$_POST['taskid'].'"'));
	$mailid = $mailid['MailID'];
	mysqli_query($db->conn,'update task set secondaryEmailId="'.$_POST['value'].'" where MailID="'.$mailid.'"');
        $secData = mysqli_fetch_assoc(mysqli_query($db->conn,'select * from secondaryEmail where id="'.$_POST['value'].'"'));
	mysqli_query($db->conn,'update user_mail set senderaddress = "'.$secData['nickname'].' <'.$secData['EmailID'].'>" where MailID="'.$mailid.'"');
}
die();
}


?>
<script type='text/javascript' src='/js/jquery-1.7.2.min.js'></script>
<script>
function changeEmail(val,taskid){
//alert(taskid);
var sr = "&changeEmail=true&value="+val+"&taskid="+taskid;
//alert(val);
$.post("<?=Url_Create('home')?>", sr, function (data) { 

//alert(data);

});

}

</script>
