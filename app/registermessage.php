<?php
if(!isset($_GET['planchange'])){
$tickleId = @trim($_SESSION['TickleID']);
$EmailID = $db->select_rows('tickleuser',"EmailID","where TickleID='$_SESSION[TickleID]]'",ASSOC);
$email = $EmailID['0']['EmailID'];
//die();
$params['EmailID'] = $email;
$checkmail = whmcs_checkmail($params);

 if(isset($checkmail) && $checkmail['userid'] != ""){
     $userid = $checkmail['userid'];
    $getorder = whmcs_getorder($checkmail['userid']);
    $getserviceid = whmcs_getproducdetails($getorder['userid']);
    
    
     if($getserviceid->products->product[0]->id!=""){
        $serviceid = $getserviceid->products->product[0]->id;
        $productid = $getserviceid->products->product[0]->pid;
        if($getserviceid->products->product[0]->status == 'Suspended'){
            $suspend_for_whmcs = whmcs_unsuspend($serviceid);
        }
    }

 }
 
 $connect = mysqli_connect('localhost','admin_whmcs','GjutD$%C*b','admin_whmcs');
 //$select = mysqli_select_db($connect,'admin_whmcs');
 $whmcsquery = mysqli_query($connect,"select configoption1 from tblproducts where id ='".$productid."'");
 $row = mysqli_fetch_assoc($whmcsquery);
 $allowcampaign = $row['configoption1'];
 //die('fghgfh');
 mysqli_close($connect);
 
 
    $dselect = "select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='Y'";
    $mselect = "select distinct task.MailID, task.TaskID, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='Y'";
    $dselect.=" order by TaskDate";

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
  $currentcampaign = count($mails);
 
  $remainingcampaign = $allowcampaign-$currentcampaign;
 
 
 if($currentcampaign > $allowcampaign){
  header("location:https://client.tickletrain.com/dashboard/?npid=".$productid."&packid=".$serviceid."&allowcampaign=".$allowcampaign."&userid=".$userid."");
 }else{
     
    $dselect = "select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='K'";
    $mselect = "select distinct task.MailID, task.TaskID, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='K'";
    $dselect.=" order by TaskDate";

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
     $maileridarray11 = $mails;
     if($remainingcampaign <= count($maileridarray11)){
     for($al=0; $al<$remainingcampaign; $al++){
          $updatetaskstatus = $db->update('task',"Status='Y'","where MailID = '".$maileridarray11[$al]."' and TickleID = '$tickleId'");
          $updatemailstatus = $db->update('user_mail',"Status = 'Y'","where MailID = '".$maileridarray11[$al]."' and TickleID = '$tickleId'");
      }
     }else{
      foreach($maileridarray11 as $mid){
       $updatetaskstatus = $db->update('task',"Status='Y'","where MailID = '".$mid."' and TickleID = '$tickleId'");
       $updatemailstatus = $db->update('user_mail',"Status = 'Y'","where MailID = '".$mid."' and TickleID = '$tickleId'");   
      }   
     }
    unset ($_SESSION['allowedhere']);
    unset ($_SESSION['downgradecheck']);
    unset($_SESSION['totalcampaign']);
    
 }
}

if(isset($_GET['upgrade']) && $_GET['upgrade']=='complete'){  ?>
<div class="main_holder register_area">
			<h1>Change Successful!</h1>
			<div class="form">
				<div class="holder">
					<div class="frame">
						<div class="text-holder">
							<p>Thank you for using TickleTrain!</p>
							<!--<span class="txt">Your campaign count has been updated.</span>-->
							<span class="text-ty">Thank you!</span>
						</div>
					</div>
				</div>
			</div>
</div>


    
<?php }else{
	//print_r($_SESSION);
  if(isset($_SESSION['cusername'])){
    $check_login =$db->select_rows("tickleuser","*", "WHERE (UserName ='".$_SESSION['cusername']."' or EmailID='".$_SESSION['cusername']."') and Password='".$_SESSION['cpassword']."'",ASSOC);
    if (count($check_login) == 1) {
        $ck_login = $check_login[0];
        if ($ck_login['TickleID'] > 0) {
            if ($ck_login['Status'] == 'Y') {
                $_SESSION['TickleID'] = $ck_login['TickleID'];
                $_SESSION['UserName'] = $ck_login['UserName'];
                $_SESSION['EmailID'] = $ck_login['EmailID'];
                $_SESSION['FirstName'] = $ck_login['FirstName'];
                $_SESSION['LastName'] = $ck_login['LastName'];
                $_SESSION['TimeZone'] = $ck_login['TimeZone'];
                $_SESSION['mail_type'] = $ck_login['mail_type'];
                $_SESSION['signature'] = $ck_login['signature'];
		header("location:https://client.tickletrain.com/dashboard/"); 
		exit();	
            }
        }
    }


  }
?>
<div class="main_holder register_area">
			<h1>Thank you for registering!</h1>
			<div class="form">
				<div class="holder">
					<div class="frame">
						<div class="text-holder">
							<p style="font-size: 17px;text-align: left;padding: 20px 0 0 50px;letter-spacing: 1px;font-weight: bold;"> Simply login to TickleTrain <br>above and we'll show you <br>how to use it! </p>
							<span class="text-ty">Thank you!</span>
						</div>
					</div>
				</div>
			</div>
</div>
<?php }
?>
