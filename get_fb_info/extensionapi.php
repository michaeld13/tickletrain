<?php
header('Access-Control-Allow-Origin: *');

include_once("../includes/data.php");
include ("../fb/fb_const.php");
include_once("../includes/function/func.php");
// ini_set('display_errors',1);
// error_reporting(E_ALL);
// error_reporting(E_STRICT);

if (session_id() == "") {
    session_start();
}

if(isset($_POST['RecentMails'])){
   $_POST['subject']  = trim(str_replace("Re:", "", strip_tags($_POST['subject'])));
   $_POST['subject']  = trim(str_replace("RE:", "", strip_tags($_POST['subject'])));
   $subject = $_POST['subject'];
   $query = mysqli_query($db->conn,"select task.TaskID from user_mail,task where task.Status='Y' and user_mail.Subject like '%".$subject."%' and user_mail.toaddress like '%".$_POST['toaddress']."%' and user_mail.MailID = task.MailID") or die(mysqli_error($db->conn). __LINE__);
   $rows = mysqli_num_rows($query);
  
    if($rows > 0){
         die("success");
    }else{
        die("errorhere");
    }
}

//session_start();
// $object = json_decode($_SESSION['facebookcache']);
// $array = objectToArray($object);

if(isset($_POST['gettickle']) && $_POST['gettickle']!=""){
//zzs$response = GetTickle($_POST['tickleid'],$_POST['TickleTrainID']);
die($response);
}


elseif(isset($_POST['TickleName']) && $_POST['TickleName']!=""){
    $reponse = TickleChangeDateTime($_POST['tickleid'],$_POST['TickleName']);
    header('Access-Control-Allow-Origin: *');
    header('Content-type: application/json');
    die(json_encode($reponse));
}

elseif(isset($_POST['GetBccField']) && $_POST['GetBccField'] != ""){
    $reponse = GetBccFieldString($_POST['tickl_name'],$_POST['tickleid'],$_POST['approve_tickle'],$_POST['DailyDays'],$_POST['TickleTime'],$_POST['fname'],$_POST['lname']);
      header('Access-Control-Allow-Origin: *');
      header('Content-type: application/json');
    die(json_encode($reponse));
}
    
/*----Start Code to direct whmcs login-----*/
if(isset($_GET['page']) && $_GET['page']!=""){
    $email = $_GET['emailid'];
    $autoauthkey = "abcXYZ123";
    $timestamp = time(); # Get current timestamp
    $hash = sha1($email . $timestamp . $autoauthkey); # Generate Hash
    if($_GET['page'] == 'knowledgebase' && isset($_GET['catid']) && $_GET['catid'] != ""){
    $url = 'https://secure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='. urlencode("knowledgebase.php?action=displaycat&catid=4");  
    header("location:".$url."");  
    }
    elseif($_GET['page'] == 'knowledgebase'){
    $url = 'https://secure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='. urlencode("knowledgebase.php");  
    header("location:".$url."");
    }if($_GET['page'] == 'support'){
    $url = 'https://secure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='. urlencode("submitticket.php?step=2&deptid=1");
    header("location:".$url."");
    }
	
	if($_GET['page'] == 'productdetails'){
	$url = 'https://secure.tickletrain.com/dologin.php?email='.$email.'&timestamp='.$timestamp.'&hash='.$hash.'&goto='. urlencode("clientarea.php?action=products");
    header("location:".$url."");
    }
	
}

/*----End Code to direct whmcs login-------*/

/*  Starting Conditon of Direct Login into Tickle Train from chrome extension */
if(isset($_GET['tickleid']) && $_GET['tickleid']!=""){
  $query = mysqli_query($db->conn,"select UserName,Password from tickleuser where TickleID='".$_GET['tickleid']."'") or die(mysqli_error($db->conn). __LINE__);
  if(mysqli_num_rows($query)>0){
      $row = mysqli_fetch_assoc($query);
      $username = $row['UserName'];
      $password = $row['Password'];
      $array = array('Username'=>$username,'Password'=>$password);
      $callloginfunction = loginfunction($array);
      echo "<script>window.location.href='https://client.tickletrain.com/';</script>";
      die;
  }
  }
  /*  Ending Conditon of Direct Login into Tickle Train from chrome extension */
  /* Start Condtion for fogotpassword and activation email from chrome extension */
  elseif (@trim($_REQUEST['act']) == 'activationemail' && @trim($_REQUEST['email']) != '') {
    $EmailID = @trim($_REQUEST['email']);
    $query = mysqli_query($db->conn,"select * from tickleuser where EmailID ='".$EmailID."'");
    $row = mysqli_fetch_assoc($query);
   if ($row) {
        if ($row['Status'] == 'N') {
            sendActivation($row);
            $res = "activation";
        } else {
            sendRestore($row);
            $res = "restore";
        }
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        echo json_encode('success');
        die();
        }else{
        echo json_encode('errorhere');
        die();
       }
 }
 /* End Condtion for fogotpassword and activation email from chrome extension */
 /* Start Condtion to check already login with chrome extension */
 elseif(isset($_POST['activecookie']) && $_POST['TickleId']!=""){
   $countcampaigns = countcampaign($_POST['TickleId']);
   $object = json_decode($_SESSION['facebookcache']);
   $array = objectToArray($object);
   if(isset($_SESSION['facebookcache'])){
   $responsearray = array('currentcampaign'=>$countcampaigns['currentcampaign'],'allowedcampaign'=>$countcampaigns['allowedcampaign'],'fbcontacts'=>$array);    
   }else{
   $responsearray = array('currentcampaign'=>$countcampaigns['currentcampaign'],'allowedcampaign'=>$countcampaigns['allowedcampaign']);    
   }
   header('Content-type: application/json');
   echo json_encode($responsearray);
   die();
 }
 /* End Condtion to check already login with chrome extension */
 /* Start Condtion to make login in chrome extension */
 if(isset($_POST['Username']) && isset($_POST['Password'])){

  $callloginfunction = loginfunction($_POST);

  if($callloginfunction==0){
      setcookie("email_id", $_SESSION['EmailID'], time() + 3600 * 60);
      $countcampaigns = countcampaign($_SESSION['TickleID']);
      $responsearray = array( 'TickleID'=>$_SESSION['TickleID'],
                              'EmailID'=>$_SESSION['EmailID'],
                              'currentcampaign'=>$countcampaigns['currentcampaign'],
                              'allowedcampaign'=>$countcampaigns['allowedcampaign']
                            );

      header('Content-type: application/json');
      $res =  json_encode($responsearray);
      echo $res;
      die;

  }else{        
     $responsearray = array("error"=>"Please reset your username and password and try again");
     echo json_encode($responsearray);
     die();
  }
}
/* End Condtion to make login in chrome extension */

/*-----Function Start to convert an object into array------*/
function objectToArray($object)
{
if(!is_object($object) && !is_array($object))
return $object;

$array=array();
foreach($object as $member=>$data)
{
$array[$member]=objectToArray($data);
}
return $array;
}
/*-----Function End to convert an object into array------*/
/*-----Starting of Login Fucntion to make login in Tickle Train-------*/
function loginfunction($POST){
$check_key=array('Username','Password');
$filter_post=filterpost($check_key,$POST);
$Username=trim($POST['Username']);
$Password=trim($POST['Password']);
if($Form->ErrorString=="")
	{
           $chek_login = login($Username,$Password);
           if ($chek_login==0){
               return "0";
            }else{
                return 1;
            }
        }
}
/*-----Ending of Login Fucntion to make login in Tickle Train-------*/
/* ----Starting of Count Campaign fucntion to count user campaign ----*/
function countcampaign($uid){
    global $db;
    $allowed_compaign = mysqli_query($db->conn,"select Allowe_campaign from Compaign where TickleID=$uid");
    while($allowcamp = mysqli_fetch_assoc($allowed_compaign)){
    $allowcampaign = $allowcamp['Allowe_campaign'];
    }

    // comment by sandeep on 23-04-19 Reasion No use of this code .. This is extra code i found .

    // $dselect = "select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$uid' and task.Status='Y'";
    $mselect = "select distinct task.MailID, task.TaskID, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$uid' and task.Status='Y'";
    // comment by sandeep on 23-04-19
    // $dselect.=" order by TaskDate";
    // $i = 0;
    // $checkquery = mysqli_query($db->conn,$dselect);
    // while($checrow = mysqli_fetch_assoc($checkquery)){
    //     $newcheckarray[$i] = $checrow;
    //     $i++;
    // }
    // $dates = array();
    // $mArr = $newcheckarray;
    // foreach($mArr as $row){
    //     $dates[$row['TaskDate']]=1;
    // }

    // $Variables['dates'] = $dates;

$j=0;
$checkqueryagain = mysqli_query($db->conn,$mselect);
    while($checrowagain = mysqli_fetch_assoc($checkqueryagain)){
        $newcheckarrayagain[$j] = $checrowagain;
        $j++;
    }
$mArr = $newcheckarrayagain;

$mails=array();
$sMails=array();

if(count($mArr)){
  foreach($mArr as $row){
      if (!isset($sMails[$row['MailID']])){
          $sMails[$row['MailID']]=$row['TaskDateTime'];
      }
  }
}
  // comment by sandeep on 23-04-19
  // if ($sfld==5 && !$sord){  comment  by sandeep 
  //     asort($sMails);
  // }
  // if ($sfld==5 && $sord){
  //     arsort($sMails);
  // }

foreach ($sMails as $mid=>$val){
    $mails[]=$mid;
}

  $currentcampaign = count($mails);

  $returnarray = array("currentcampaign"=>$currentcampaign,"allowedcampaign"=>$allowcampaign);
  return $returnarray;
}
/* ----Ending of Count Campaign fucntion to count user campaign ----*/
/* ----Starting of fucntion to Resend Activation Email ----*/
function sendActivation($row) {
    $url_activation = "http://" . $_SERVER['HTTP_HOST'] . Url_Create('activation', 'act=' . md5($row['TickleID']));
     $mail_content = "<p>" . $row['FirstName'] . ", thank you for creating a TickleTrain account!</p>
    <p>Please <a href='" . $url_activation . "'>click</a> the link below to authenticate your account.<br/>
    <a href='" . $url_activation . "'>" . $url_activation . "</a></p>
    <br/>
    <p><b>Username: " . $row['UserName'] . "</b><br/>
    <b>Password: ********</b></p>
    <p>Once you have logged in, watch our <i>Getting Started video</i> and you'll be up and running in minutes. Be sure and visit your Settings page to setup your email account and signature. We look forward to putting your emails to work for you.</p>
    <p>Send it and forget it!<br><a href=\"www.tickletrain.com\">www.tickletrain.com</a></p>";    
    return SendMail($row['EmailID'], "noreply@tickletrain.com", "Welcome to TickleTrain", $mail_content, 'mail', true, array('sales@ourinternet.us', 'michael@speedgraphics.net'));
}
/* ----Ending of fucntion to Resend Activation Email ----*/
/* ----Starting of fucntion to Resend Password ----*/
function sendRestore($row) {
    $mail_content = "<p>" . $row['FirstName'] . ", your password is: <b>" . $row['Password'] . "</b></p>
    <p>We look forward to putting your emails to work for you.</p>
    <p>Send it and forget it!<br><a href=\"www.tickletrain.com\">www.tickletrain.com</a></p>";    
    return SendMail($row['EmailID'], "noreply@tickletrain.com", "Restore TickleTrain account", $mail_content, 'mail', true);
}
/* ----Ending of fucntion to Resend Password ----*/

/* Start code to get tickle info */

     
     
     
     
     
     
//     $query = mysqli_query($db->conn,"select TickleName,TickleTime,DailyDays from tickle where TickleID='".$tickleid."'")  or die(mysqli_error($db->conn). __LINE__);
//     $html = "<div class='pop_up' style='display:none'><select name='TickleName' class='select_input'><option value='Select Tickle To Use'>Select Tickle To Use </option>";
//     while($row = mysqli_fetch_assoc($query)){
//       $html.= "<option value='".$row['TickleName']."'>".$row['TickleName']."</option>";
//       $result[] = $row;
//     }
//     $html.="</select><div class='send_div'><p> Send after </p><select name='DailyDays' class='DailyDays'><option value='3'>3</option>";
//     for($i=1; $i<=60; $i++){
//       $html.= "<option value='".$i."'>$i</option>";  
//     }
//     $html.= '</select>
//	  </div>
//	  <div class="send_div" style="float:right;">
//	  <p>days at</p>
//	 <input type="text" class="input_b" id="TickleTime" value="12:00" name="TickleTime">
//	  </div>
//	  <div class="Approve">
//	   <p>Approve before sending</p>
//	   <input type="checkbox" name="chk_approve" value="" />
//
//	    </div>
//	<div class="first_name">
//	<p>First Name :</p>
//	 <input type="text" class="input_b" value="" name="fname">
//	</div>
//	<div class="first_name">
//	<p>Last Name :</p>
//	 <input type="text" class="input_b" value="" name="lname">
//	</div>';
//        if($_POST['tokenid'] != ""){
//	$html.= '<div class="facebook_div" style="clear:both;">
//	<img src="http://client.tickletrain.com/images/ico_fb.png" alt="" />
//	<h1>Facebook Profile</h1>
//	</div>
//	<div class="Alpha_diallo" style="clear:both; width:220px">
//	<img src="img_2.png" alt="" class="fb_profile_image" />
//	<h1 class="fb_profile_name">Alpha Diallo</h1>
//	<h2 class="fb_profile_gender">male,US</h2>
//	<h3 style="cursor:pointer" class="facebook_profile_url"><span id="update_contact_acc_fb" cursor="pointer">Use Facebook profile for this contact</span></h3>
//	</div>';
//        }
//	$html.= '<div class="update">update</div>
//	</div>';
//     return $html;
 
 /* End code to get tickle info*/
 /* change tickle default time and date */
 function TickleChangeDateTime($tickleid,$ticklename){
     $query = mysqli_query($db->conn,"select TickleTime,DailyDays from tickle where TickleID='".$tickleid."' and TickleName='".$ticklename."'");
     $row = mysqli_fetch_assoc($query);
     $tickletime = substr($row['TickleTime'],0,5);
     $retrun_array = array("TickleTime"=>$tickletime,'DailyDays'=>$row['DailyDays']);
     return $retrun_array;
 }
 /* End of the function */
 function GetBccFieldString($ticklename,$tickleid,$approve,$dailydays,$tickletime,$firstname,$lastname){
 if($approve == "" || $approve=="undefined"){
    $approve = "N"; 
 }else{
    $approve = "Y";   
 }
 $tickletime_new_format = str_replace(":","+","$tickletime");
 $GetUserNameQuery = mysqli_query($db->conn,"select UserName from tickleuser where TickleID='".$tickleid."'")  or die(mysqli_error($db->conn). __LINE__);
 $GetUserNameResult = mysqli_fetch_assoc($GetUserNameQuery);
 $ticklestring = $approve.'+'.$firstname.'+'.$lastname.'+'.$tickletime_new_format.'+'.$dailydays.'+'.$ticklename.'+'.$GetUserNameResult['UserName'].'@tickletrain.com';
 $response_array = array("TickleBccString"=>$ticklestring,'updated'=>'updated');
 return $response_array;
 }
 /* Start function  */
?>
