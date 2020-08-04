<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once(dirname(__DIR__)."/includes/class/PHPMailer/src/Exception.php");
require_once(dirname(__DIR__)."/includes/class/PHPMailer/src/PHPMailer.php");
require_once(dirname(__DIR__)."/includes/class/PHPMailer/src/SMTP.php");

// if(isset($_GET['test'])){
// 	pr($_SESSION);
// }
		$mail = new PHPMailer(true);

$newquery = tablelist('tickleuser', '', array("WHERE UserName ='" . $_SESSION['UserName'] . "' and TickleID='" . $_SESSION['TickleID'] . "'"));
$brandnewquery = $newquery[0];

$altEmailValues = '';
if(!empty($_POST['alt_email_bcc']))
{
    $altEmailValues = $_POST['alt_email_bcc'];
    unset($_POST['alt_email_bcc']);
}

//print_r($newquery);exit;
if ($_POST['submit'] == "Update") {

 //    echo "<pre>";
	// print_r($_POST);

    if ($_POST['EmailID'] != "") {
        if ($_POST['FirstName'] != "") {
            $postfields['firstname'] = $_POST['FirstName'];
        }if ($_POST['LastName'] != "") {
            $postfields['lastname'] = $_POST['LastName'];
        }
        $postfields['email'] = $_POST['EmailID'];
        $postfields['password2'] = $_POST['Password'];

        $postfields["customfields"] = base64_encode(serialize(array("2" => "$_POST[Username]", "3" => "$_POST[Password]", "7" => "$_POST[Timezone]")));
        $checkmail = whmcs_checkmail($_POST);
        $postfields['clientid'] = $checkmail['userid'];
        // print_r($postfields); 
        whmcs_updateClient($postfields);
        	//die;

    }


    //if ($_POST['Password'] != "" && $_POST['Password'] != "Password") {
      //  $check_key = array('Password', 'FirstName', 'LastName', 'Timezone'/* ,'TickleAlertTime' */, "mail_type", "signature", "enable_alt", "blueplanbarning", "alt_email", 'TimeDailyTickle');
   // } else {
      //  $check_key = array('FirstName', 'LastName', 'Timezone'/* ,'TickleAlertTime' */, "mail_type", "signature", "enable_alt", "blueplanbarning", "alt_email", 'TimeDailyTickle');
   // }
   //$Username=$_SESSION['EmailID'];
   /* if ($_POST['Password'] != "" && $_POST['Password'] != "Password") {
        $Password = $_POST['Password'];
    } else {
        unset($_POST['Password']);
    }*/

    $check_key = array('FirstName', 'LastName', 'Timezone'/* ,'TickleAlertTime' */, "mail_type", "signature", "enable_alt", "blueplanbarning", "alt_email", 'TimeDailyTickle');
	
    //$Username=trim($_POST['EmailID']);
    $FirstName = trim($_POST['FirstName']);
    $LastName = trim($_POST['LastName']);
    $Timezone = $_POST['Timezone'];
    $TickleID = $_SESSION['TickleID'];
    $mail_type = $_POST['mail_type'];
    $face_update = $_POST['face_update'];

    //$TickleTime=time12to24($_POST['TickleAlertTime']);
    /* $TickleTime_Time = preg_replace("/[^0-9:]/", '', $TickleTime);
      $TickleTime_PM = trim(preg_replace("/[^a-z]/", '', strtolower($TickleTime)));
      $TTime=explode(":",$TickleTime_Time);
      if($TickleTime_PM=="pm")
      {
      $TickleTime1=$TTime[0]+12;
      $TickleTime=$TickleTime1.":".$TTime[1].":00";
      }else
      {
      $TickleTime=$TTime[0].":".$TTime[1].":00";
      } */
    //$_POST['TickleAlertTime']=$TickleTime;

    $TickleTime = time12to24($_POST['TimeDailyTickle']);
    /* $TickleTime_Time = preg_replace("/[^0-9:]/", '', $TickleTime);
      $TickleTime_PM = trim(preg_replace("/[^a-z]/", '', strtolower($TickleTime)));
      $TTime=explode(":",$TickleTime_Time);
      if($TickleTime_PM=="pm")
      {
      $TickleTime1=$TTime[0]+12;
      $TickleTime=$TickleTime1.":".$TTime[1].":00";
      }else
      {
      $TickleTime=$TTime[0].":".$TTime[1].":00";
      } */
    $_POST['TimeDailyTickle'] = $TickleTime;

    $filter_post = filterpost($check_key, $_POST);
	//$Form->ValidField($Username,'empty','Enter Username',array('Min'=>6));
	   // $Form->ValidField($Password, 'empty', 'Enter Password', array('Min' => 6));
	//$Form->ValidField($EmailID,'email','Email Field is Empty Or Invalid');
    $Form->ValidField($FirstName, 'empty', 'Enter Your First Name');
    $Form->ValidField($LastName, 'empty', 'Enter your Last Name');


    if($_POST['secondary_id']=='primary'){
     $filter_post['DMUse'] = @intval($_POST['dmuse']);
    }
    $filter_post['DMSystem'] = @trim($_POST['dmsystem']);
    $filter_post['DMSmtp'] = @trim($_POST['dmsmtp']);
    $port = @intval($_POST['dmport']);

    $filter_post['DMPort'] = (($port > 0) ? $port : 465);
	$filter_post['DMUser'] = @trim($_POST['dmuser']);
	$filter_post['FromEmail'] = @trim($_POST['DMFrom']);
    $filter_post['DMPwd'] = @trim($_POST['dmpwd']);

    $filter_post['DMAuth'] = (isset($_POST['dmauth']) ? 1 : 0) ;
    $filter_post['DMSecure'] = @trim($_POST['dmsecure']);
	setDefaults($filter_post);
    //$filter_post['FromEmail'] = @trim($_POST['dmfromemaildef']);
    // decrypt password

    $filter_post['DMPwd'] = encryptIt($filter_post['DMPwd']);
    if ($TickleID > 0) {
        //$filter_post['RegisteredDate']=date("Y-m-d H:i:s");
        //$filter_post['IPAddress']=$_SERVER['REMOTE_ADDR'];
        //$filter_post['Status']='N';
        $_SESSION['TimeZone'] = $Timezone;
        $_SESSION['mail_type'] = $mail_type;
        $_SESSION['signature'] = $filter_post['signature'];
                //print_r($filter_post);  die;
        $ids = $db->update('tickleuser', $filter_post, array("WHERE TickleID ='$TickleID' and Status='Y'"));
        
        if(empty($_POST['enable_alt_bcc'])){
            mysqli_query($db->conn,"UPDATE tickleuser SET enable_alt_bcc='0', alt_email_bcc='' WHERE TickleID ='" . $TickleID . "'");
        }
        else {
            mysqli_query($db->conn,"UPDATE tickleuser SET enable_alt_bcc='1', alt_email_bcc='" . implode(',', $altEmailValues) . "' WHERE TickleID ='" . $TickleID . "'");
        }
        
        if(!empty($_POST['dmuser']) && !empty($_POST['dmpwd']) && !empty($_POST['dmsmtp']))
        {
        	mysqli_query($db->conn,"delete from google_auth_tokens where userid='".$TickleID."'");
        }        

        if ($Timezone != "") {
            mysqli_query($db->conn,"UPDATE task SET TimeZone='" . $Timezone . "' WHERE TickleID ='" . $TickleID . "'");
        }

        if ($_POST['update_facebook'] == "update_face") {
            mysqli_query($db->conn,"UPDATE contact_list SET FbUid='" . $_SESSION['uid'] . "',FbFname='" . $_SESSION['first_name'] . "',FbLname='" . $_SESSION['last_name'] . "',FbCheck='Y' where EmailID='" . $_SESSION['email'] . "'");

            mysqli_query($db->conn,"UPDATE tickleuser SET FbUid='" . $_SESSION['uid'] . "',FbFname='" . $_SESSION['first_name'] . "',FbLname='" . $_SESSION['last_name'] . "',FbEmail='" . $_SESSION['email'] . "',FbDetails='" . $_SESSION['userInfo'] . "' where TickleID='" . $_SESSION['TickleID'] . "' and EmailID='" . $_SESSION['email'] . "'");
        }

		//Added 15 dec 2015
		if (@intval($_POST['dmuse'])==0 && $_POST['secondary_id']=='primary') {
		     mysqli_query($db->conn,"delete from google_auth_tokens where userid='".$_SESSION['TickleID']."'");
		}
		//Added 15 dec 2015


		// Save secondary email data

		if(isset($_POST['secondary_id']) && $_POST['secondary_id']!='primary'){

			if(isset($_POST['secdmpwd'.$_POST['secondary_id']])){
		                
		            if(!empty($_POST['secsecdmuser'.$_POST['secondary_id']]) && !empty($_POST['secdmpwd'.$_POST['secondary_id']]) && !empty($_POST['secdmsmtp'.$_POST['secondary_id']]))
		            {
		                mysqli_query($db->conn,"update secondaryEmail set use_authtoken='0',authtoken='',refresh_token='', where TickleID='" . $TickleID . "' and id='" . $_POST['secondary_id'] . "'");
		            }
		            
		            
				$filter_post1 = filterpost(array(), $_POST);
				$filter_post1['DMSmtp'] = @trim($_POST['secdmsmtp'.$_POST['secondary_id']]);
				$port = @intval($_POST['secdmport'.$_POST['secondary_id']]);
				$filter_post1['DMPort'] = (($port > 0) ? $port : 25);
				$filter_post1['DMUse'] = $_POST['dmuse'];
				$filter_post1['DMUser'] = @trim($_POST['secsecdmuser'.$_POST['secondary_id']]);
				$filter_post1['DMSecure'] = @trim($_POST['secdmsecure'.$_POST['secondary_id']]);
 				$filter_post1['DMSecure'] = @trim($_POST['secdmsecure'.$_POST['secondary_id']]);
 				$filter_post1['FromEmail'] = @trim($_POST['DMFrom_'.$_POST['secondary_id']]);
				setDefaults($filter_post);
				encryptIt($filter_post1['DMPwd']);
				// decrypt password
				$filter_post1['DMPwd'] = encryptIt(@trim($_POST['secdmpwd'.$_POST['secondary_id']]));
				//print_r($filter_post1);die();
				$ids = $db->update('secondaryEmail', $filter_post1, array("WHERE TickleID ='$TickleID' and id='".$_POST['secondary_id']."'"));
			}

		}

		//add signature
		if(isset($_REQUEST['secarray'])){
				foreach($_REQUEST['secarray'] as $secemails){
					$filter_sign = array();
					$filter_sign['signature'] = @trim($_POST['signature_'.$secemails]);
					$db->update('secondaryEmail', $filter_sign, array("WHERE TickleID ='$TickleID' and id='".$secemails."'"));
				}
		}
		//add signature


	// Save secondary email data

	// imap settings rply track
		if (isset($_REQUEST['imap_host']) && isset($_REQUEST['imap_userame']) && isset($_REQUEST['imap_passowrd'])) {
				 // && $_REQUEST['imap_connection_approved'] == 'yes'
		         $imap_encrypt_pass = encryptIt($_REQUEST['imap_passowrd']);
	        $update_imap = mysqli_query($db->conn,"update tickleuser set imap_host='$_REQUEST[imap_host]',
	                       imap_userame='$_REQUEST[imap_userame]',imap_passowrd='$imap_encrypt_pass',imap_port='$_REQUEST[imap_port]',
	                       imap_secure='$_REQUEST[imap_secure]' where TickleID='" . $_SESSION['TickleID'] . "'") or die("Error Here");
	    }

		if(isset($_REQUEST['secarray'])){
			foreach($_REQUEST['secarray'] as $secemails){
				//if($_REQUEST['sec_'.$secemails.'_imap_host']!='' && $_REQUEST['sec_'.$secemails.'_imap_passowrd']!=''){
				   // mysqli_query($db->conn,'update secondaryEmail set imap_host="'.$_REQUEST['sec_'.$secemails.'_imap_host'].'",imap_passowrd="'.encryptIt($_REQUEST['sec_'.$secemails.'_imap_passowrd']).'" , imap_port="'.$_REQUEST['sec_'.$secemails.'_imap_port'].'" , imap_secure="'.$_REQUEST['sec_'.$secemails.'_imap_secure'].'", imap_userame ="'.$_REQUEST['sec_'.$secemails.'_imap_username'].'"  where id="'.$secemails.'"');
				
				
				   $dor = ($_REQUEST['sec_'.$secemails.'_delete_campaign_on_reply']=='on')?1:0;
				   $dcr = ($_REQUEST['sec_'.$secemails.'_notify_when_reply_received']=='on')?1:0;
				   $dnt = ($_REQUEST['sec_'.$secemails.'_do_not_track']=='on')?1:0;

	  			//echo $_REQUEST['sec_'.$secemails.'_delete_campaign_on_reply'];echo $dor;die();
				   $qu = 'update `secondaryEmail` set `imap_host`="'.$_REQUEST['sec_'.$secemails.'_imap_host'].'",`imap_passowrd`="'.encryptIt($_REQUEST['sec_'.$secemails.'_imap_passowrd']).'" , `imap_port`="'.$_REQUEST['sec_'.$secemails.'_imap_port'].'" , `imap_secure`="'.$_REQUEST['sec_'.$secemails.'_imap_secure'].'" , `imap_userame`="'.$_REQUEST['sec_'.$secemails.'_imap_username'].'", `delete_campaign_on_reply`= "'.$dor.'" ,  `notify_when_reply_received`="'.$dcr.'" , `do_not_track`= "'.$dnt.'" where `id`='.$secemails;

					mysqli_query($db->conn,$qu);

				//	echo $qu;
				    
				//}
			}
		}
		// imap settings rply track

   	} // Tickle id >0 
    header('location:https://client.tickletrain.com/myaccount/');
    exit();
}


//use multiple accounts

if(isset($_REQUEST['add_additional']) && $_REQUEST['add_additional']=='Save'){

	setcookie('hide_show_id', '', time() - 3600, "/myaccount");
	unset($_COOKIE['hide_show_id']);

	$sec = mysqli_query($db->conn,"select * from secondaryEmail where TickleID='" . $_SESSION['TickleID'] . "' ");
	$countSecondary = mysqli_num_rows($sec);

		if($brandnewquery['email_addon']==1 || $brandnewquery['email_addon']==5){
		       $allow_rows = 1-$countSecondary;
		}
		else if($brandnewquery['email_addon']==2 || $brandnewquery['email_addon']==6){
			$allow_rows = 2-$countSecondary;
		}
		else if($brandnewquery['email_addon']==3 || $brandnewquery['email_addon']==7){
			$allow_rows = 3-$countSecondary;
		}
		else if($brandnewquery['email_addon']==8 || $brandnewquery['email_addon']==9){
			$allow_rows = 4-$countSecondary;
		}
		else if($brandnewquery['email_addon']==10 || $brandnewquery['email_addon']==11){
			$allow_rows = 5-$countSecondary;
		}
		else if($brandnewquery['email_addon']==12 || $brandnewquery['email_addon']==13){
			$allow_rows = 6-$countSecondary;
		}
		else if($brandnewquery['email_addon']==14 || $brandnewquery['email_addon']==15){
			$allow_rows = 7-$countSecondary;
		}
		else if($brandnewquery['email_addon']==16 || $brandnewquery['email_addon']==17){
			$allow_rows = 8-$countSecondary;
		}
		else if($brandnewquery['email_addon']==18 || $brandnewquery['email_addon']==19){
			$allow_rows = 9-$countSecondary;
		}
		else if($brandnewquery['email_addon']==20 || $brandnewquery['email_addon']==21){
			$allow_rows = 10-$countSecondary;
		}
		else if($brandnewquery['email_addon']==22 || $brandnewquery['email_addon']==23){
			$allow_rows = 11-$countSecondary;
		}

	     if(isset($_POST["EmailID1"]) || isset($_POST["EmailID5"])){ 
		if(isset($_POST['EmailID5'])){
			 $email = trim($_POST['EmailID5']);
			 $nickname = trim($_POST['nickname5']);
		}
		else{
			 $email = trim($_POST['EmailID1']);
			 $nickname = trim($_POST['nickname1']);
		}
		if($allow_rows>0){
			//echo 'insert into secondaryEmail(EmailID,FromEmail,TickleID,UserName,nickname)values("'.$email.'","'.$email.'","'.$_SESSION['TickleID'].'","'.$_SESSION['UserName'].'","'.$nickname.'")'; exit;
			@mysqli_query($db->conn,'insert into secondaryEmail(EmailID,FromEmail,TickleID,UserName,nickname)values("'.$email.'","'.$email.'","'.$_SESSION['TickleID'].'","'.$_SESSION['UserName'].'","'.$nickname.'")');
			$id = mysqli_insert_id($db->conn);
			setcookie('hide_show_id', $email.'_secondary'.'_'.$id.'_0', time() + (10*24*60*60*1000), "/myaccount/");

		}
	}



	     if(isset($_POST["EmailID2"]) || isset($_POST["EmailID6"])){
		if(isset($_POST['EmailID6'])){
			 $email = trim($_POST['EmailID6']);
			 $nickname = trim($_POST['nickname6']);
		}
		else{
			 $email = trim($_POST['EmailID2']);
			 $nickname = trim($_POST['nickname2']);
		}
		if($allow_rows>0){
			mysqli_query($db->conn,'insert into secondaryEmail(EmailID,FromEmail,TickleID,UserName,nickname)values("'.$email.'","'.$email.'","'.$_SESSION['TickleID'].'","'.$_SESSION['UserName'].'","'.$nickname.'")');
			$id = mysqli_insert_id($db->conn);
			setcookie('hide_show_id', $email.'_secondary'.'_'.$id.'_0', time() + (10*24*60*60*1000), "/myaccount/");
		
		}
		
	}




		if(isset($_POST["EmailID3"]) || isset($_POST["EmailID7"])){
			if(isset($_POST['EmailID7'])){
				 $email = trim($_POST['EmailID7']);
				 $nickname = trim($_POST['nickname7']);
			}
			else{
			 	$email = trim($_POST['EmailID3']);
				$nickname = trim($_POST['nickname3']);
			}
			if($allow_rows>0){
		       	   mysqli_query($db->conn,'insert into secondaryEmail(EmailID,FromEmail,TickleID,UserName,nickname)values("'.$email.'","'.$email.'","'.$_SESSION['TickleID'].'","'.$_SESSION['UserName'].'","'.$nickname.'")');
			   $id = mysqli_insert_id($db->conn);
			   setcookie('hide_show_id', $email.'_secondary'.'_'.$id.'_0', time() + (10*24*60*60*1000), "/myaccount/");
			
			}
		}


	
		if(isset($_POST["EmailID4"]) || isset($_POST["EmailID8"])){
			if(isset($_POST['EmailID8'])){
				 $email = trim($_POST['EmailID8']);
				 $nickname = trim($_POST['nickname8']);
			}
			else{
			 	$email = trim($_POST['EmailID4']);
				$nickname = trim($_POST['nickname4']);
			}
			if($allow_rows>0){
		          mysqli_query($db->conn,'insert into secondaryEmail(EmailID,FromEmail,TickleID,UserName,nickname)values("'.$email.'","'.$email.'","'.$_SESSION['TickleID'].'","'.$_SESSION['UserName'].'","'.$nickname.'")');
			  $id = mysqli_insert_id($db->conn);
			 setcookie('hide_show_id', $email.'_secondary'.'_'.$id.'_0', time() + (10*24*60*60*1000), "/myaccount/");
			}
		}

		
		//update nickname
		if(isset($_REQUEST['secarray'])){
			foreach($_REQUEST['secarray'] as $secemails){
				$TickleID = $_SESSION['TickleID'];
				$filter_sign = array();
				$filter_sign['nickname'] = @trim($_POST['addednickname_'.$secemails]);
				$db->update('secondaryEmail', $filter_sign, array("WHERE TickleID ='$TickleID' and id='".$secemails."'"));
			}
		}
		
		//update nickname
}
//use multiple accounts


//delete Email Accounts
	if(isset($_GET['deleteSecEmail'])){
                   //Check Count from secondary table
                  //$sec = mysqli_query($db->conn,"select * from secondaryEmail where TickleID='" . $_SESSION['TickleID'] . "' and isdelete='0'");
                  //$countSecondary = mysqli_num_rows($sec);
                  //Check Count from secondary table
		// if($countSecondary>1){
		   mysqli_query($db->conn,'delete from secondaryEmail where tickleid="'.$_SESSION['TickleID'].'" and id="'.$_GET['deleteSecEmail'].'"');
		  mysqli_query($db->conn,'update task set secondaryEmailId="" where  TickleID="'.$_SESSION['TickleID'].'" and secondaryEmailId="'.$_GET['deleteSecEmail'].'"');
		  
		  setcookie('hide_show_id', '', time() - 3600, "/myaccount/");

 		  unset($_COOKIE['hide_show_id']);
		  setcookie('hide_show_id', $_SESSION['EmailID'].'_mainemail', time() + (10*24*60*60*1000), "/myaccount/");
		  // mysqli_query($db->conn,'delete from task where TickleID="'.$_SESSION['TickleID'].'" and secondaryEmailId="'.$_GET['deleteSecEmail'].'"');
		   redirect('myaccount');
		//}
		// else {
                //        echo"<script>alert('You cannot Delete all emails');</script>";
			//redirect('myaccount');
		// }
	}
//delete Email Acounts


// Update emails,username and password
if(isset($_POST['update_email']) && $_POST['update_email']=='Update'){
	//print_r($_POST);die();
	    $Form->ValidField($_POST['EmailID'], 'empty', 'Enter Your Email Id');
    	    $Form->ValidField($_POST['UserName'], 'empty', 'Enter your User Name');
	    $TickleID = $_SESSION['TickleID'];
	    if ($_POST['Password'] != "" && $_POST['Password'] != "Password") {
		$db->update('tickleuser', array('Password'=>$_POST['Password']), array("WHERE TickleID ='$TickleID' and Status='Y'"));
	    } else {
		$Password = '';
	    }
	
	$checkemail = mysqli_query($db->conn,"select * from tickleuser where EmailID='".$_POST['EmailID']."'");
	//mysqli_num_rows($checkemail);
	if(mysqli_num_rows($checkemail)==0){
		if($TickleID>0){
			$postUrl = "https://secure.tickletrain.com/get_addon_info.php";
			$postdata = array(
				'EmailID' => @trim($_SESSION['EmailID']),
				'updateEmail' => @trim($_POST['EmailID'])
			);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $postUrl);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POST, count($postdata));
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST,0);
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			echo $whmcsupdate = curl_exec($ch);                             
			curl_close($ch);
			if($whmcsupdate=='success'){
		 		$ids = $db->update('tickleuser', array('EmailID'=>@trim($_POST['EmailID'])), array("WHERE TickleID ='$TickleID' and Status='Y'"));
				$_SESSION['EmailID'] = @trim($_POST['EmailID']);
			}
		}
	}
	$checkuser = mysqli_query($db->conn,"select * from tickleuser where UserName='".$_POST['UserName']."'");
	if(mysqli_num_rows($checkuser)==0){
		
		$postUrl = "https://secure.tickletrain.com/get_addon_info.php";
		$postdata = array(
			'email_id' => @trim($_SESSION['EmailID']),
			'username' => @trim($_POST['UserName'])
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $postUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POST, count($postdata));
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$whmcsupdate = curl_exec($ch); 
		///print_r(curl_getinfo($ch));                            
		curl_close($ch);
		if($whmcsupdate=='success'){
			if($TickleID>0){
	 			$db->update('tickleuser', array('UserName'=>$_POST['UserName']), array("WHERE TickleID ='$TickleID' and Status='Y'"));
				$_SESSION['UserName'] = $_POST['UserName'];
			}
		}
	}
	redirect('myaccount');
}
// Update emails,username and password


//rply track code

if (isset($_POST['testsmtp'])) {
	if($_REQUEST['email_type']=='primary'){
	    if ($_REQUEST['imap_secure'] == 'none') {
		$_REQUEST['imap_secure'] = '';
	    }    
            
            imap_timeout(IMAP_OPENTIMEOUT, 5);
            imap_timeout(IMAP_READTIMEOUT, 5);
            imap_timeout(IMAP_WRITETIMEOUT, 5);
            imap_timeout(IMAP_CLOSETIMEOUT, 5);
            
	    $imap_decrypt_pass=$_REQUEST['imap_passowrd'];

	    $connection = @imap_open("{" . $_REQUEST['imap_host'] . ":" . $_REQUEST['imap_port'] . "/imap/" . $_REQUEST['imap_secure'] . "/novalidate-cert}INBOX", $_REQUEST['imap_userame'], $imap_decrypt_pass, OP_HALFOPEN, 1);


	    if ($connection) {
			$chkInbox = (string) $connection;
           $chkInbox = substr(strtolower(trim($chkInbox)),0,11);
           if($chkInbox=='resource id'){
				echo "true";
				mysqli_query($db->conn,"update tickleuser set imap_host='$_REQUEST[imap_host]',
                           imap_userame='$_REQUEST[imap_userame]',imap_passowrd='".encryptIt($imap_decrypt_pass)."',imap_port='$_REQUEST[imap_port]',
                           imap_secure='$_REQUEST[imap_secure]' where TickleID='" . $_SESSION['TickleID'] . "'") or die("Error Here");

                mysqli_query($db->conn,"update tickleuser set imapOff='0' where TickleID='" . $_SESSION['TickleID'] . "'");
           }     
	    } else {
		echo imap_last_error();
                //mysqli_query($db->conn,"update tickleuser set imapOff='1' where TickleID='" . $_SESSION['TickleID'] . "'");
		die();
	    }
	   }
	   else{
		//echo $_REQUEST['sec_'.$_REQUEST['id'].'_imap_host'];
		 if ($_REQUEST['sec_'.$_REQUEST['id'].'_imap_secure'] == 'none') {
			$_REQUEST['sec_'.$_REQUEST['id'].'_imap_secure'] = '';
		    }  
		
		  $imap_decrypt_pass=$_REQUEST['sec_'.$_REQUEST['id'].'_imap_passowrd'];
	            imap_timeout(IMAP_OPENTIMEOUT, 5);
	            imap_timeout(IMAP_READTIMEOUT, 5);
	            imap_timeout(IMAP_WRITETIMEOUT, 5);
	            imap_timeout(IMAP_CLOSETIMEOUT, 5);
		    $connection = imap_open("{" . $_REQUEST['sec_'.$_REQUEST['id'].'_imap_host'] . ":" . $_REQUEST['sec_'.$_REQUEST['id'].'_imap_port'] . "/imap/" . $_REQUEST['sec_'.$_REQUEST['id'].'_imap_secure'] . "/novalidate-cert}INBOX", $_REQUEST['sec_'.$_REQUEST['id'].'_imap_username'], $imap_decrypt_pass, OP_HALFOPEN, 1);
		    if ($connection) {
				$chkInbox = (string) $connection;
	           $chkInbox = substr(strtolower(trim($chkInbox)),0,11);
	           if($chkInbox=='resource id'){
					echo "true";
					mysqli_query($db->conn,'update secondaryEmail set imapOff="0" ,imap_host="'.$_REQUEST['sec_'.$_REQUEST['id'].'_imap_host'].'",imap_passowrd="'.encryptIt($imap_decrypt_pass).'" , imap_port="'.$_REQUEST['sec_'.$_REQUEST['id'].'_imap_port'].'" , imap_secure="'.$_REQUEST['sec_'.$_REQUEST['id'].'_imap_secure'].'", imap_userame ="'.$_REQUEST['sec_'.$_REQUEST['id'].'_imap_username'].'"  where id="'.$_REQUEST['id'].'"');
				}			

		    } else {
			echo imap_last_error();
	               // mysqli_query($db->conn,"update secondaryEmail set imapOff='1' where id='" . $_REQUEST['id'] . "'");
			die();
		    }
	   }
    exit;
}

//rply track code




if ($_GET['qrydlt'] == "deletauth") {
	mysqli_query($db->conn,"delete from google_auth_tokens where userid='".$_SESSION['TickleID']."'");
	mysqli_query($db->conn,"update tickleuser set dmuse='0' where TickleID='".$_SESSION['TickleID']."'");
	redirect('myaccount');
}

if ($_GET['qrydlt'] == "secdeletauth" && $_GET['secemaiid']!='') {
    mysqli_query($db->conn,"update secondaryEmail set authtoken='',refresh_token='',use_authtoken='0',DMUse='0' where id='".$_GET['secemaiid']."'");
	redirect('myaccount');
}

function constructAuthString($email,$accessToken) {
  return base64_encode("user=$email\1auth=Bearer $accessToken\1\1");
}

if (isset($_POST['testsmtp1'])) {


	if($_POST['authToken']=='yes'){
			$TickleID = $_SESSION['TickleID'];
	        ini_set('display_errors', 1);
			if($_POST['emailaccount']=='secondary'){
				$sql_user_sec = mysqli_query($db->conn,"select * from secondaryEmail where id='".$_POST['sendemailid']."'");
	            $rs_user_sec = mysqli_fetch_array($sql_user_sec); 
				
			}
		        $sql_user = mysqli_query($db->conn,"select tu.* , gat.access_token ,gat.refresh_token from tickleuser tu left join google_auth_tokens gat on gat.userid = tu.TickleID where tu.TickleID='$TickleID' and tu.Status='Y'");
	            $rs_user = mysqli_fetch_array($sql_user);           
	            
				//set_include_path('/var/www/vhosts/tickletrain.com/site2/google_auth2/');
	            set_include_path('/var/www/vhosts/client.tickletrain.com/httpdocs/google_auth2/');
				// require_once('Zend/Loader/Autoloader.php');						   
				require_once 'Zend/Mail/Transport/Smtp.php';
				require_once 'Zend/Mail.php';
				
				
				//$gfpath01 = str_replace('app','',__DIR__);
				//$gfpath = $gfpath01.'/google_auth2/';
				require_once 'src/Google_Client.php'; // include the required calss files for google login
				require_once 'src/contrib/Google_PlusService.php';
				require_once 'src/contrib/Google_Oauth2Service.php';
				
				$client = new Google_Client();
				$client->setClientId('799405691032-er3cilvjgrqgtlfreuffllvkp2ouvrjb.apps.googleusercontent.com'); // paste the client id which you get from google API Console
				$client->setClientSecret('QYmRweaDw20scMLTidBR8MRB'); // set the client secret			
				if($_POST['emailaccount']=='secondary'){
				     $client->refreshToken($rs_user_sec['refresh_token']);
				     
				}
				else{
				     $client->refreshToken($rs_user['refresh_token']);
				}
				$getGoogleToken = $client->getAccessToken();
				$getGoogleToken02 = json_decode($getGoogleToken,true);
				mysqli_query($db->conn,"update google_auth_tokens set access_token='" . $getGoogleToken02['access_token'] . "' , expires_in='" . $getGoogleToken02['expires_in'] . "' , created='" . $getGoogleToken02['created'] . "' where userid='".$TickleID."' ");
				$accessToken = $getGoogleToken02['access_token'];
				
	                if($_POST['emailaccount']=='secondary'){
				     $email = @trim($rs_user_sec['EmailID']);
				     $rs_user['FirstName'] = $rs_user_sec['nickname'];
				     $rs_user['LastName'] = '';
				     mysqli_query($db->conn,"update secondaryEmail set authtoken='" . $getGoogleToken02['access_token'] . "' where where id='".$_POST['sendemailid']."'");
			}
			else{
			        $email = @trim($rs_user['EmailID']);
			}
			$token = trim($accessToken);		
			$smtpInitClientRequestEncoded = constructAuthString($email, $token);
			$config = array('ssl' => 'ssl',
			  'port' => '465',
			  'auth' => 'xoauth',
			  'xoauth_request' => $smtpInitClientRequestEncoded);

			$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com',$config); 
			Zend_Mail::setDefaultTransport($transport); 
			$mail = new Zend_Mail();
			$mail->setBodyHtml('Congratulations!<br/><br/>The receipt of this email indicates that your new mail settings are working for this account.');
			$mail->setFrom($rs_user['EmailID'], @trim($rs_user['FirstName'] . ' ' . $rs_user['LastName']));// 
			$mail->addTo($_POST['email']);
			$mail->setSubject("Tickle Train SMTP Test");
			//$mail->AddAddress($_POST['dmtoemail']);
			$res = $mail->send($transport);
			if($mail->send($transport)){
			  echo "true";
			}
			die();
	}else{


		if($_POST['emailaccount']=='secondary'){
			// echo "<pre>";
			// print_r($_POST);
		
			$email_id  = $_POST['secsecdmuser'.$_POST['secondary_id']];
			$pwd = $_POST['secdmpwd'.$_POST['secondary_id']];
			$sql_user_sec = mysqli_query($db->conn,"select * from secondaryEmail where id='".$_POST['sendemailid']."'");
			$rs_user_sec = mysqli_fetch_array($sql_user_sec); 
			$filter_post['DMSmtp'] = @trim($_POST['secdmsmtp'.$_POST['secondary_id']]);
            $_POST['secdmsmtpmailid'.$_POST['secondary_id']] = $rs_user_sec['EmailID'];
                        
			$port = @intval($_POST['secdmport'.$_POST['secondary_id']]);
			$filter_post['DMFrom'] = $_POST['DMFrom_'.$_POST['secondary_id']];
			$filter_post['DMUse'] = @intval($_POST['dmuse']);
			$filter_post['DMPort'] = (($port > 0) ? $port : 25);
			$filter_post['DMAuth'] = (isset($_POST['dmauth']) ? true : false) ;
			$filter_post['DMUser'] = @trim($_POST['secsecdmuser'.$_POST['secondary_id']]);
			$filter_post['DMPwd'] = @trim($_POST['secdmpwd'.$_POST['secondary_id']]);
			$filter_post['DMSecure'] = @trim($_POST['secdmsecure'.$_POST['secondary_id']]);
			$filter_post['EmailID'] = @trim($email_id);
			setDefaults($filter_post);
			$FirstName = $rs_user_sec['nickname'];
			$LastName =  '';//$_POST['LastName'];
			//print_r($filter_post);
		    prepareMailer($filter_post, $mail);
			$update_query = "update secondaryEmail set DMSmtpOff='0' where id='".$_POST['secondary_id']."'";
		}
		else{

			//echo "else" ; die;
			$update_query = "update tickleuser set DMSmtpOff='0' where TickleID='".$_SESSION['TickleID']."'";
			$filter_post['DMFrom'] = $_POST['DMFrom'];
			$filter_post['DMUse'] = @intval($_POST['dmuse']);
			$filter_post['DMSystem'] = @trim($_POST['dmsystem']);
			$filter_post['DMSmtp'] = @trim($_POST['dmsmtp']);
			$port = @intval($_POST['dmport']);
			$filter_post['DMPort'] = (($port > 0) ? $port : 25);
			$filter_post['DMAuth'] = (isset($_POST['dmauth']) ? true : false) ;
			$filter_post['DMUser'] = @trim($_POST['dmuser']);
			$filter_post['DMPwd'] = @trim($_POST['dmpwd']);
			$filter_post['DMSecure'] = @trim($_POST['dmsecure']);
			$filter_post['EmailID'] = @trim($_POST['EmailID']);
			//print_r($filter_post);
			setDefaults($filter_post);
			$FirstName = $_POST['FirstName'];
			$LastName = $_POST['LastName'];
			$mail = new PHPMailer(true);
		       //prepareMailer($filter_post, $mail);
		}
		
		if(empty($filter_post['DMUser']) || empty($filter_post['DMPwd']) || empty($filter_post['DMSmtp'])){
                    die('error');
        }	
	
		$from = (filter_var($filter_post['DMUser'], FILTER_VALIDATE_EMAIL))? $filter_post['DMUser']: $filter_post['DMFrom'];


		$mail->Host = trim($filter_post['DMSmtp']); // SMTP server
        $mail->Port = intval($filter_post['DMPort']); // set the SMTP server port
        $mail->SMTPKeepAlive = false;                  // SMTP connection will not close after each email sent
        $mail->SMTPAuth = $filter_post['DMAuth'];                  // enable SMTP authentication
        $mail->Username = trim($filter_post['DMUser']);     // SMTP server username
        $mail->Password = trim($filter_post['DMPwd']);     // SMTP server password
        $mail->SMTPSecure = trim($filter_post['DMSecure']);
		$mail->Subject = "Tickle Train SMTP Test";
		$mail->Body = "Congratulations!<br/><br/>The receipt of this email indicates that your new mail settings are working for this account.";
		$mail->setFrom(@trim($from), @trim($FirstName . ' ' . $LastName));
		//$mail->Sender = @trim($from);
		$mail->IsHTML(true); // send as HTML
		$mail->AddAddress($_POST['dmtoemail']);
		$mail->Timeout = 60;
		try {
			$SendMail = $mail->Send();
		} catch (Exception $e) {

			if ($e instanceof CustomException) {
				echo $e;
			} else if ($e instanceof OtherException) {
				echo $e;
			} else {
				echo $e;
			}
		}

		if ($SendMail) {
			//echo @trim(decryptIt($rs_user_sec['DMPwd']));
			echo "true";
			mysqli_query($db->conn,$update_query);

		} else {
			die();
		}
		exit;
	}
}


//check email avail
if(isset($_POST['check_email']) && $_POST['check_email']!=''){
	$checkemail = mysqli_query($db->conn,"select * from tickleuser where EmailID='".$_POST['check_email']."'");
	if(mysqli_num_rows($checkemail)==0){
		echo"true";
	}
	else{
		echo"false";
	}
	die();
}


//check email avail
//check Username avail
if(isset($_POST['check_uname']) && $_POST['check_uname']!=''){
	$checkemail = mysqli_query($db->conn,"select * from tickleuser where UserName='".$_POST['check_uname']."'");
	if(mysqli_num_rows($checkemail)==0){
		echo"true";
	}
	else{
		echo"false";
	}
	die();
}


//check email avail

function setDefaults(&$filter_post) {
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


// if(!empty($_POST['dmsmtp']) || !empty($_POST['dmuser']) || !empty($_POST['dmpwd'])){

// 	print_r($_POST); die;

//         $mailchkSmtp = new PHPMailer;
//         $mailchkSmtp->IsSMTP();                                      // Set mailer to use SMTP
//         $mailchkSmtp->Host = $_POST['dmsmtp'];                 // Specify main and backup server
//         $mailchkSmtp->Port = $_POST['dmport'];                                    // Set the SMTP port
//         $mailchkSmtp->SMTPAuth = (isset($_POST['dmauth']) ? $_POST['dmauth'] : false ); // Enable SMTP authentication
//         $mailchkSmtp->Username = $_POST['dmuser'];                // SMTP username
//         $mailchkSmtp->Password = $_POST['dmpwd'];                  // SMTP password
//         $mailchkSmtp->SMTPSecure = $_POST['dmsecure'];                            // Enable encryption, 'ssl' also accepted
//         $mailchkSmtp->From = $_POST['dmuser'];        
//         //$mailchkSmtp->AddAddress('tickletraincron@gmail.com', '');  // Add a recipient//shine@123        
//         $mailchkSmtp->IsHTML(true);                                  // Set email format to HTML
//         $mailchkSmtp->Subject = 'Test SMTP Connection';
//         $mailchkSmtp->Body    = 'Test SMTP Connection';
//         $mailchkSmtp->AltBody = 'Test SMTP Connection';
//         try {
//             $SendMail22 = $mailchkSmtp->Send();
//             //echo "sending mail"; 

//         } catch (Exception $e) {
//             //echo "error in sending mail"; 
//         }
//         if ($SendMail22) {
//             mysqli_query($db->conn,"update tickleuser set DMSmtpOff='0' where TickleID='" . $_SESSION['TickleID'] . "'");
//         }
//         $mailchkSmtp->ClearAllRecipients();
//         $mailchkSmtp->ClearReplyTos();  
// } 

 



?>


