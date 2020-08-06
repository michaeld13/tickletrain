<?php header('Access-Control-Allow-Origin: *'); ?>
<?php
$Variables['timezones'] = gettimezonenames();

//Code set to fix login issue : Developed on 2/1/2014
if (isset($_POST['EmailAddressOfCurrentUser']) && isset($_POST['CheckUserStatus'])) { //This is to change user status if he/she is already active in whmcs
    $GetUserStatusQuery = mysqli_query($db->conn,"SELECT `Status` FROM `tickleuser` WHERE `EmailID`='" . $_POST['EmailAddressOfCurrentUser'] . "'") or die(mysqli_error($db->conn) . __LINE__);
    $GetUserStatusRow = mysqli_fetch_assoc($GetUserStatusQuery);
    if ($GetUserStatusRow['Status'] == 'N') {
        $UpdateTicklTrainAccountStatus = mysqli_query($db->conn,"UPDATE `tickleuser` SET `Status`='Y' WHERE `EmailID`='" . $_POST['EmailAddressOfCurrentUser'] . "'") or die(mysqli_error($db->conn) . __LINE__);
        if ($UpdateTicklTrainAccountStatus) {
            die('success');
        } else {
            die('error');
        }
    } else {
        die('success');
    }
} elseif (isset($_POST['EmailAddressOfCurrentUser']) && isset($_POST['ChangeUserStatus'])) { // This is to change TickleTrain account status according to Whmcs
    $UpdateTicklTrainAccountStatus = mysqli_query($db->conn,"UPDATE `tickleuser` SET `Status`='" . $_POST['Staus'] . "' WHERE `EmailID`='" . $_POST['EmailAddressOfCurrentUser'] . "'") or die(mysqli_error($db->conn) . __LINE__);
    if ($UpdateTicklTrainAccountStatus) {
        die("success");
    } else {
        die("error");
    }
}


if (isset($_POST['update_tt_user_detail']) && $_POST['update_tt_user_detail'] == "yes") {

    $email = $_POST['EmailID'];
    $ticklearray = $db->select_rows('tickleuser', '*', "where EmailID='$email'", 'ASSOC');
    $tickleid = $ticklearray[0]['TickleID'];
  	$db->update('tickleuser', " FirstName='$_POST[FirstName]',LastName='$_POST[LastName]',Phone='$_POST[Phone]',Address='$_POST[Address]',City='$_POST[City]',PostCode='$_POST[PostCode]',country='$_POST[country]',State='$_POST[State]'", "where TickleID = '$tickleid'");		
    die('success');
}



if (@trim($_REQUEST['act']) == 'activationemail' && @trim($_REQUEST['email']) != '') {
    $EmailID = @trim($_REQUEST['email']);
    $row = tablerow('tickleuser', '*', array("WHERE EmailID ='$EmailID'"));
    $res = 'false';
    if ($row) {
        if ($row['Status'] == 'N') {
        //   sendActivation($row);
            $res = "activation";
        } else {

            sendRestore($row);
            $res = "restore";
        }
        //$res = 'true';
    }
    die($res);
}

if (isset($_POST['countcurrentcampaignavail']) && $_POST['countcurrentcampaignavail'] == 'countcurrentcampaignavail') {
    $dselect = "select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleid' and task.Status='Y'";
    $mselect = "select distinct task.MailID, task.TaskID, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleid' and task.Status='Y'";
    $dselect.=" order by TaskDate";

    $dates = array();
    $mArr = $db->query_to_array($dselect);

    foreach ($mArr as $row) {
        $dates[$row['TaskDate']] = 1;
    }
    $Variables['dates'] = $dates;
    $mArr = $db->query_to_array($mselect);

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
    die($currentcampaign);
}






if (isset($_POST['change_whmcs_password']) && $_POST['change_whmcs_password'] == "changing_password") {

    $email = $_POST['email'];
    $ticklearray = $db->select_rows('tickleuser', '*', "where EmailID='$email'", 'ASSOC');
    $tickleid = $ticklearray[0]['TickleID'];
    $update_password = $db->update('tickleuser', "Password='$_POST[password]'", "where TickleID = '$tickleid'");
    die($update_password);
}

if (isset($_POST['countcampaignforwhmcs'])) {
    $email = $_POST['email'];
    $ticklearray = $db->select_rows('tickleuser', '*', "where EmailID='$email'", 'ASSOC');
    $tickleid = $ticklearray[0]['TickleID'];
    $allowed_compaign = $db->select_rows('Compaign', "Allowe_campaign,warningthresold", "where TickleID='$tickleid'", ASSOC);
    foreach ($allowed_compaign as $acamp) {
        $allowcampaign = $acamp['Allowe_campaign'];
        $warningthresold = $acamp['warningthresold'];
    }


    $dselect = "select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleid' and task.Status='Y'";
    $mselect = "select distinct task.MailID, task.TaskID, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleid' and task.Status='Y'";
    $dselect.=" order by TaskDate";

    $dates = array();
    $mArr = $db->query_to_array($dselect);
    foreach ($mArr as $row) {
        $dates[$row['TaskDate']] = 1;
    }
    $Variables['dates'] = $dates;
    $mArr = $db->query_to_array($mselect);
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

    die($currentcampaign . "," . $allowcampaign . "," . $warningthresold);
}



if (isset($_POST['upgradepackage']) && $_POST['upgradepackage'] == 'upgrade') {
    $email = $_POST['email'];
    $ticklearray = $db->select_rows('tickleuser', '*', "where EmailID='$email'", 'ASSOC');
    $tickleid = $ticklearray[0]['TickleID'];
    $plan = $_POST['Plan'];
    $warmingthresold = $_POST['warningthresold'];
    $allowed_campaign = $_POST['allowed_campaign'];
    if ($tickleid != "") {
        $updatetickleplan = $db->update('tickleuser', "Plan=$plan", "where TickleID = '$tickleid'");
        $updatecompaignplan = $db->update('Compaign', "Plan=$plan,Allowe_campaign=$allowed_campaign,warningthresold=$warmingthresold", "where TickleID = '$tickleid'");
    }
    die('success');
}


if (isset($_POST['delete_tickle_user']) && $_POST['delete_tickle_user'] == 'delete') {
    $email = $_POST['email'];
    $ticklearray = $db->select_rows('tickleuser', '*', "where EmailID='$email'", 'ASSOC');
    $tickleid = $ticklearray[0]['TickleID'];

    if ($tickleid != "") {
        $delete1 = $db->delete('tickleuser', "where TickleID='$tickleid'");
        $delete2 = $db->delete('user_mail', "where TickleID='$tickleid'");
        $delete3 = $db->delete('ticklefollow', "where TickleID='$tickleid'");
        $delete4 = $db->delete('task', "where TickleID='$tickleid'");
        $delete5 = $db->delete('reply_mail', "where TickleID='$tickleid'");
        $delete7 = $db->delete('category', "where TickleID='$tickleid'");
        $delete8 = $db->delete('contact_list', "where TickleID='$tickleid'");
        $delete9 = $db->delete('tickle', "where TickleID='$tickleid'");
        $delete9 = $db->delete('Compaign', "where TickleID='$tickleid'");
        session_destroy();
    }
}

if (isset($_POST['suspendorder']) && $_POST['suspendorder'] == 'suspend') {
    $email = $_POST['email'];
    $ticklearray = $db->select_rows('tickleuser', 'TickleID', "where EmailID='$email'", 'ASSOC');
    $tickleid = $ticklearray[0]['TickleID'];
    if ($tickleid != "") {
        $updatetickle = $db->update('tickleuser', "Plan = '1'", "where TickleID='$tickleid'");


        $dselect = "select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleid' and task.Status='Y'";
        $mselect = "select distinct task.MailID, task.TaskID, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleid' and task.Status='Y'";
        $dselect.=" order by TaskDate";

        $dates = array();
        $mArr = $db->query_to_array($dselect);
        foreach ($mArr as $row) {
            $dates[$row['TaskDate']] = 1;
        }
        $Variables['dates'] = $dates;
        $mArr = $db->query_to_array($mselect);
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

        $maileridarray = $mails;

        
        $tQuery = "select TaskID,Pause from task where TickleID = '" . $tickleid . "'";
        $tArr = $db->query_to_array($tQuery);
        foreach($tArr as $tArrVal)
        {
            $db->update('task', "Pause='Y',temppause='".$tArrVal['Pause']."'", "where TaskID = '".$tArrVal['TaskID']."' ");
        }
       // foreach ($maileridarray as $mailerid) {
            //$updatetaskstatus = $db->update('task', "Status='K'", "where MailID = '$mailerid' and TickleID = '$tickleid'");
            //$updatemailstatus = $db->update('user_mail', "Status = 'K'", "where MailID = '$mailerid' and TickleID = '$tickleid'");
        //}

        die('success');
    }
}

if (isset($_POST['unsuspendorder']) && $_POST['unsuspendorder'] == 'unsuspend') {

    $email = $_POST['email'];
    $ticklearray = $db->select_rows('tickleuser', 'TickleID', "where EmailID='$email'", 'ASSOC');
    $tickleid = $ticklearray[0]['TickleID'];
    if ($tickleid != "") {
        $updatetickle = $db->update('tickleuser', "Plan = '$_POST[plan]'", "where TickleID='$tickleid'");

        $dselect = "select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleid' and task.Status='K'";
        $mselect = "select distinct task.MailID, task.TaskID, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleid' and task.Status='K'";
        $dselect.=" order by TaskDate";

        $dates = array();
        $mArr = $db->query_to_array($dselect);
        foreach ($mArr as $row) {
            $dates[$row['TaskDate']] = 1;
        }
        $Variables['dates'] = $dates;
        $mArr = $db->query_to_array($mselect);
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


        $maileridarray = $mails;

        $dselect = "select date_format(task.TaskInitiateDate,'%Y-%m-%d') as TaskDate from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleid' and task.Status='Y'";
        $mselect = "select distinct task.MailID, task.TaskID, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleid' and task.Status='Y'";
        $dselect.=" order by TaskDate";

        $dates = array();
        $mArr = $db->query_to_array($dselect);
        foreach ($mArr as $row) {
            $dates[$row['TaskDate']] = 1;
        }
        $Variables['dates'] = $dates;
        $mArr = $db->query_to_array($mselect);
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

        $taskstatus = count($mails);

        if ($taskstatus == '0') {

            $allowed_compaign = $db->select_rows('Compaign', "Allowe_campaign", "where TickleID='$tickleid'", ASSOC);
            foreach ($allowed_compaign as $acamp) {
                $allowcampaign = $acamp['Allowe_campaign'];
            }
            //$allowcampaign;

            $sl = 0;
            foreach ($maileridarray as $mailidhere) {
                if ($sl < $allowcampaign)
                   // $updatetaskstatus = $db->update('task', "Status='Y'", "where MailID = '" . $mailidhere . "' and TickleID = '$tickleid'");
               // $updatemailstatus = $db->update('user_mail', "Status = 'Y'", "where MailID = '" . $mailidhere . "' and TickleID = '$tickleid'");

                $sl++;
            }
        }
        
        $tQuery = "select TaskID,temppause from task where TickleID = '" . $tickleid . "'";
        $tArr = $db->query_to_array($tQuery);
        foreach($tArr as $tArrVal)
        {
            if(!empty($tArrVal['temppause']))
            {    
                //$db->update('task', "Pause='".$tArrVal['temppause']."',temppause=''", "where TaskID = '".$tArrVal['TaskID']."' ");
            }    
        }
        
        
        die('success');
    }
}



if (isset($_POST['terminateorder']) && $_POST['terminateorder'] == 'terminate') {
    $email = $_POST['email'];
    $ticklearray = $db->select_rows('tickleuser', 'TickleID', "where EmailID='$email'", 'ASSOC');
    $tickleid = $ticklearray[0]['TickleID'];
    if ($tickleid != "") {
        $updatetickle = $db->update('tickleuser', "Plan = '1'", "where TickleID='$tickleid'");
        
        $tQuery = "select TaskID,Pause from task where TickleID = '" . $tickleid . "'";
        $tArr = $db->query_to_array($tQuery);
        foreach($tArr as $tArrVal)
        {
            $db->update('task', "Pause='Y',temppause='".$tArrVal['Pause']."'", "where TaskID = '".$tArrVal['TaskID']."' ");
        }
        
        /*$compaign = $db->get_count('user_mail', "where TickleID='$tickleid'");
        if ($compaign > 5) {
            $maileridarray = $db->select_rows('user_mail', 'MailID,Status', "where TickleID='$tickleid'", 'ASSOC');
            $i = 1;
            foreach ($maileridarray as $mailerid) {
                if ($i > 5) {
                    $deletetask = $db->delete('task', "where MailID = '$mailerid[MailID]' and TickleID = '$tickleid'");
                    $deletemail = $db->delete('user_mail', "where MailID = '$mailerid[MailID]' and TickleID = '$tickleid'");
                }
                $i++;
            }
        }*/
        
        
        die('success');
    }
}

if(isset($_POST['checkuser'])){
        $username = $_POST['username'];
	$qry = mysqli_query($db->conn,'select * from tickleuser where UserName="'.$username.'"');
	echo $rows = mysqli_num_rows($qry);
	die();
}

if (isset($_POST['checkusernameemail']) && $_POST['checkusernameemail'] == 'checkusernameemail') {
	//die("dd");
    $Username = trim($_POST['username']);
    $EmailID = trim($_POST['emailid']);
    $arrays=whmcs_check_user(array('email'=>$_POST['emailid'])); // change on 12/10/2014	
    if (is_array($arrays) && ($arrays['error']!='')) {
        echo 'error';
    } else {
        echo 'success';
    }

    die('');
}


if (isset($_POST['EmailID'])) {
	//print_r($_POST);die();
	
    $check_key = array('Username', 'Password', 'EmailID', 'FirstName', 'LastName', 'Phone', 'Address', 'City', 'PostCode',
        'country', 'State', 'Plan', 'Timezone');
    $_POST['Username'] = trim($_POST['Username']);   // Remove Space from username
    $filter_post = filterpost($check_key, $_POST);
    $Username = trim($_POST['Username']);
    $Password = trim($_POST['Password']);
    $RPassword = trim($_POST['RPassword']);
    $EmailID = trim($_POST['EmailID']);
    $REmailID = trim($_POST['REmailID']);
    $FirstName = trim($_POST['FirstName']);
    $LastName = trim($_POST['LastName']);
    $Plan = trim($_POST['Plan']);
    $Timezone = trim($_POST['Timezone']);
    $Phone = trim($_POST['Phone']);
    $Address = trim($_POST['Address']);
    $City = trim($_POST['City']);
    $PostCode = trim($_POST['PostCode']);
    $country = trim($_POST['country']);
    $State = trim($_POST['State']);
     // for automatic login
	$_SESSION['cusername']=$_POST['Username'];
	$_SESSION['cpassword']=$_POST['Password'];

    $Form->ValidField($Username, 'charnumberonly', 'Enter valid Username', array('Min' => 6));
    $Form->ValidField($Password, 'empty', 'Enter Password', array('Min' => 6));
    $Form->ValidField($RPassword, 'empty', 'Enter Repeat Password', array('Min' => 6));
    $Form->ValidField($EmailID, 'email', 'Email Field is Empty Or Invalid');
    $Form->ValidField($REmailID, 'email', 'Repeat Email Field is Empty Or Invalid');

    $Form->ValidField(($Password == $RPassword), "bool", "Paswords does not match");
    $Form->ValidField(($EmailID == $REmailID), "bool", "Emails does not match");
    $Form->ValidField($FirstName, 'empty', 'Enter Your First Name');
    $Form->ValidField($LastName, 'empty', 'Enter your Last Name');
    if ($Plan != '1') {
        $Form->ValidField($Phone, 'phonenumber', 'Phone Number is empty or invalid.  Please try again.', array('Max' => 16));
        $Form->ValidField($Address, 'empty', 'Adreess field is empty or not valied');
        $Form->ValidField($City, 'empty', 'City field is empty or not valied');
        $Form->ValidField($PostCode, 'empty', 'Post Code is empty or not valied', array('Max' => 12));
        $Form->ValidField($country, 'empty', 'Country field is empty or not valied');
        $Form->ValidField($State, 'empty', 'State field is empty or not valied');
    }
	/* multiple emails upgrade plan 18-feb-2016 */
        if (isset($_POST['whmcsmodulepost']) && $_POST['whmcsmodulepost'] == 'whmcspost') {
		 if (CheckUser($Username, $EmailID)) {
			die('success');
		 }
	}
	/* multiple emails upgrade plan */
    if (strlen($Form->ErrorString) == '0') {
         if (CheckUser($Username, $EmailID)) {
            $Form->ValidField($EmailID_Registered, 'empty', 'Email ID Already Registered. Click here to <a href=https://tickletrain.com/login/>login</a> or request login information to be <a id=onclick href=javascript:;>resent</a>.');
            header("location:https://tickletrain.com/email-register?errorstring=" . urlencode($Form->ErrorString)."&plan=".base64_encode($_POST['Plan']).'/'.base64_encode($EmailID).'/'.base64_encode($_POST['FirstName']).'/'.base64_encode($_POST['LastName']).'/'.base64_encode($_POST['Username']).'/'.base64_encode($_POST['TimezoneRegion']).'/'.base64_encode($_POST['Timezone']));
        } else {
             if (isset($_POST['whmcsmodulepost']) && $_POST['whmcsmodulepost'] == 'whmcspost') {
                $filter_post['RegisteredDate'] = date("Y-m-d H:i:s");
                $filter_post['IPAddress'] = $_SERVER['REMOTE_ADDR'];
                $filter_post['Status'] = 'Y'; // change 06-10-2014
                $filter_post['mail_type'] = 'html';

                $filter_post['TickleID'] = $db->insert('tickleuser', $filter_post);
                $row = tablerow('tickleuser', '*', array("WHERE EmailID ='$EmailID' and Status='N'"));
                if (isset($_POST['SpecailTickle']) && $_POST['SpecailTickle'] == "exists"):
                    $TickleIdForCannedTickle = '579';
                else:
                    $TickleIdForCannedTickle = '798';
                endif;
                $SetCategoryQuery = mysqli_query($db->conn,"select * from `category` where `TickleID`='$TickleIdForCannedTickle' order by
                    `CategoryID`") or die(mysqli_error($db->conn) . __LINE__);
                while ($Cat_trow = mysqli_fetch_assoc($SetCategoryQuery)) {
                    $Cat_trow['TickleID'] = $filter_post['TickleID'];
                    $RealCategoryId = $Cat_trow['CategoryID'];
                    unset($Cat_trow['CategoryID']);
                    $cid = $db->insert("category", $Cat_trow);
                    
                     $SetTickleQuery = mysqli_query($db->conn,"select * from `tickle` where `TickleID`='$TickleIdForCannedTickle'
                     and TickleContact = '".$RealCategoryId."' order by `TickleContact`") or die(mysqli_error($db->conn) . __LINE__);
                     
                     while ($trow = mysqli_fetch_assoc($SetTickleQuery)) {
                     sleep(1);
                    $tid = md5(date("Y-m-d H:i:s") . $filter_post['TickleID']);
                    $OrgTickle = $trow['TickleTrainID'];
                    $trow['TickleTrainID'] = $tid;
                    $trow['TickleContact'] = $cid;
                    $trow['TickleID'] = $filter_post['TickleID'];
                   // $trow['TApprove'] = 'Y';
                    $db->insert("tickle", $trow);
                    AddFiles("tickle",$OrgTickle,$tid);

                    $TickleFollowQuery = mysqli_query($db->conn,"select * from `ticklefollow` where `TickleID`='$TickleIdForCannedTickle'
                        and `TickleTrainID` = '".$OrgTickle."' order by `FollowTickleTrainID`") or die(mysqli_error($db->conn) . __LINE__);
                    while ($TickleFollowRow = mysqli_fetch_assoc($TickleFollowQuery)) {
                        $TickleFollowRow['TickleTrainID'] = $tid;
                        $TickleFollowRow['TickleID'] = $filter_post['TickleID'];
                        $old_ticklefollw_id = $TickleFollowRow['FollowTickleTrainID'];
                        unset($TickleFollowRow['FollowTickleTrainID']);
                        $new_ticklefollw_id = $db->insert("ticklefollow", $TickleFollowRow);
                        AddFiles("ticklefollow",$old_ticklefollw_id,$new_ticklefollw_id);
                    }
                  }
               }
               
//                $categoryid = 0;
//                $SetTickleQuery = mysqli_query($db->conn,"select * from `tickle` where `TickleID`='$TickleIdForCannedTickle'
//                    order by `TickleContact`") or die(mysqli_error($db->conn) . __LINE__);
//                while ($trow = mysqli_fetch_assoc($SetTickleQuery)) {
//                    sleep(1);
//
//                    $tid = md5(date("Y-m-d H:i:s") . $filter_post['TickleID']);
//                    $TickleFollowQuery = mysqli_query($db->conn,"select * from `ticklefollow` where `TickleID`='$TickleIdForCannedTickle'
//                        and `TickleTrainID` = '" . $trow['TickleTrainID'] . "' order by `FollowTickleTrainID`") or die(mysqli_error($db->conn) . __LINE__);
//                    while ($TickleFollowRow = mysqli_fetch_assoc($TickleFollowQuery)) {
//                        $TickleFollowRow['TickleTrainID'] = $tid;
//                        $TickleFollowRow['TickleID'] = $filter_post['TickleID'];
//                        $db->insert("ticklefollow", $TickleFollowRow);
//                    }
//                    $trow['TickleTrainID'] = $tid;
//                    //$TickleTrainIdHere[] = $tid;
//                    $trow['TickleContact'] = $cid[$categoryid];
//                    $trow['TickleID'] = $filter_post['TickleID'];
//                    $trow['TApprove'] = 'Y';
//                    $db->insert("tickle", $trow);
//                    $categoryid++;
//                }

//                $TickleIdHere = 0;
//                $TickleArray = array();
//                
//                $TickleFollowQuery = mysqli_query($db->conn,"select * from `ticklefollow` where `TickleID`='$TickleIdForCannedTickle' order by `FollowTickleTrainID`") or die(mysqli_error($db->conn) . __LINE__);
//                while ($trow = mysqli_fetch_assoc($TickleFollowQuery)) {
//                    if (!in_array($trow['TickleTrainID'], $TickleArray)) {
//                        $TickleArray[] = $trow['TickleTrainID'];
//                        $TickleIdHere++;
//                    }
//                    $CurrnetTickleIdHere = $TickleIdHere - 1;
//                    $trow['TickleTrainID'] = $TickleTrainIdHere[$CurrnetTickleIdHere];
//                    $trow['TickleID'] = $filter_post['TickleID'];
//                    $trow['Pause'] = 'Y';
//                    $trow['Approve'] = 'Y';
                //  unset($trow['FollowTickleTrainID']);
                //     unset($_SESSION['nutrient']);
                //  setcookie("nutrient", "isagenix", time() - 6000);
                //   $db->insert("ticklefollow", $trow);
                // Code to set mutiple tickles with a new accoubn. Written on 8/11/2013
                //  $trow = tablerow("category", "*", array("where TickleID=0"));
                // $trow['TickleID'] = $filter_post['TickleID'];
                // unset($trow['CategoryID']);
                //  $cid = $db->insert("category", $trow);
                // $tid = md5(date("Y-m-d H:i:s") . $filter_post['TickleID']);
                // $trow = tablerow("tickle", "*", array("where TickleID=0"));
                // $trow['TickleTrainID'] = $tid;
                // $trow['TickleContact'] = $cid;
                // $trow['TickleID'] = $filter_post['TickleID'];
                // $db->insert("tickle", $trow);
                // $trow = tablerow("ticklefollow", "*", array("where TickleID=0"));
                // $trow['TickleTrainID'] = $tid;
                // $trow['TickleID'] = $filter_post['TickleID'];
                // unset($trow['FollowTickleTrainID']);
                // $db->insert("ticklefollow", $trow);
                $compaign['Allowe_campaign'] = $_POST['allowed_compaign'];
		$compaign['warningthresold'] = $_POST['warningthresolds'];
                $compaign['TickleID'] = $filter_post['TickleID'];
                $compaign['Plan'] = $_POST['Plan'];
                $db->insert("Compaign", $compaign);
                //copy universal Tickle
            //   sendActivation($row) or die("MailNotSent"); //comment on 06-10-2014 by raju

                die('success');


                //Register User in whmcs with status Inactive
                //   redirect('registermessage');
            } else {
                if(isset($_SESSION['facebookaccount']->nutrient) && $_SESSION['facebookaccount']->nutrient != ""){
                    $_POST['nutrient'] = 'isagenix';
                    unset($_SESSION['facebookaccount']->nutrient);
                }
                $array = whmcs_register($_POST);

                if ($array == '1') {
                    $whmcsurl = "https://secure.tickletrain.com/dologin.php";
                    $autoauthkey = "abcXYZ123";
                    $timestamp = time(); # Get current timestamp
                    $email = $EmailID; # Clients Email Address to Login
                    $hash = sha1($email . $timestamp . $autoauthkey); # Generate Hash
                    
			if($_POST['use_addon']=='on'){
				$useaddon = true;
				  if($_POST['Plan']=='6' && $_POST['addon_val']=='one')
				       $addon_id = 5;
				  else if($_POST['Plan']=='4' && $_POST['addon_val']=='one')
					$addon_id = 1;
				  if($_POST['Plan']=='6' && $_POST['addon_val']=='two')
				       $addon_id = 6;
				  else if($_POST['Plan']=='4' && $_POST['addon_val']=='two')
					$addon_id = 2;
				  if($_POST['Plan']=='6' && $_POST['addon_val']=='three')
				       $addon_id = 7;
				  else if($_POST['Plan']=='4' && $_POST['addon_val']=='three')
					$addon_id = 3;
				  if($_POST['Plan']=='6' && $_POST['addon_val']=='four')
				       $addon_id = 8;
				  else if($_POST['Plan']=='4' && $_POST['addon_val']=='four')
					$addon_id = 4;
			}
		//echo $addon_id;die();
                    if($_POST['Plan']=='6')
                    {
				$acturl = urlencode("cart.php?a=add&billingcycle=annually&pid=$_POST[Plan]&aid=$addon_id");
			}
			else
			{
				$acturl = urlencode("cart.php?a=add&pid=$_POST[Plan]&aid=$addon_id");
			}
			
                    header("location:$whmcsurl?email=$email&timestamp=$timestamp&hash=$hash&goto=" .$acturl );
	
                } else {
                    $checkmail = whmcs_checkmail($_POST);
                    if (isset($checkmail) && $checkmail['userid'] != "") {
                        $getorder = whmcs_getorder($checkmail['userid']);
                        if (isset($getorder['invoiceid']) && $getorder['amount'] != "") {
                            // $whmcsurl = "whmcs?ccce=dologin";
                            $autoauthkey = "abcXYZ123";
                            $timestamp = time(); # Get current timestamp
                            $email = $EmailID; # Clients Email Address to Login
                            $hash = sha1($email . $timestamp . $autoauthkey); # Generate Hash
                            if (isset($getorder[invoiceid]) && $getorder[invoiceid] != "") {
                                $errorstring = 'There is an existing order using this email address. Please check your email and click on the registration link.  A payment is also pending. You can ' . '<a href = https://secure.tickletrain.com/dologin.php?email=' . $email . '&timestamp=' . $timestamp . '&hash=' . $hash . '&goto=' . urlencode("viewinvoice.php?id=$getorder[invoiceid]") . '>Pay Now.</a>';
                                header("location:https://dev.tickletrain.com/email-register?errorstring=" . urlencode($errorstring));
                            }
                        } else {
                            $postfiled['clientid'] = $checkmail['userid'];
                            $postfiled['email'] = $_POST['EmailID'];
                            $postfiled['address1'] = $_POST['Address'];
                            $postfiled['city'] = $_POST['City'];
                            $postfiled['state'] = $_POST['State'];
                            $postfiled['postcode'] = $_POST['PostCode'];
                            $postfiled['country '] = $_POST['country'];
                            $postfiled['phonenumber'] = $_POST['Phone'];
                            $postfiled['firstname'] = $_POST['FirstName'];
                            $postfiled['lastname'] = $_POST['LastName'];
                            $postfiled["customfields"] = base64_encode(serialize(array("2" => "$_POST[Username]", "3" => "$_POST[Password]", "4" => "$_POST[Timezone]", "5" => "$_POST[Plan]")));
                            $apiResponse = whmcs_updateClient($postfiled);
                            if (!$apiResponse['error']) {
                                $whmcsurl = "https://secure.tickletrain.com/dologin.php";
                                $autoauthkey = "abcXYZ123";
                                $timestamp = time(); # Get current timestamp
                                $email = $EmailID; # Clients Email Address to Login
                                $hash = sha1($email . $timestamp . $autoauthkey); # Generate Hash
                                
                                if($_POST['Plan']=='6')
				{
					$acturl = urlencode("cart.php?a=add&billingcycle=annually&pid=$_POST[Plan]");
				}
				else
				{
					$acturl = urlencode("cart.php?a=add&pid=$_POST[Plan]");
				}
                                
                                header("location:$whmcsurl?email=$email&timestamp=$timestamp&hash=$hash&goto=" . urlencode("cart.php?a=add&pid=$_POST[Plan]"));
                            }
                        }
                    }
                   // die('fdsfd');
                }
            }
        }
    } else {
        if (isset($_POST['checkrequestroot']) && $_POST['checkrequestroot'] == 'maintickle') {
            
            echo "<form  id = 'ReturnPostedArray' method = 'POST' action = 'https://tickletrain.com/email-register?errorstring=".urlencode($Form->ErrorString)."'>";
            $PostedArray = $_POST;
            foreach($PostedArray as $PostAraayKey=>$PostAraayValue){
              echo "<input type='hidden' name='$PostAraayKey' value='$PostAraayValue'>";
            }
            echo "</form>";
            //die();
          echo "<script type='text/javascript'>setTimeout(\"document.getElementById(\'ReturnPostedArray\').submit();\", 100);</script>";
           // header("location:https://tickletrain.com/register?errorstring=" . urlencode($Form->ErrorString));
        }
    }
}

function checkSelection($key, $val, $isdef = false) {
    return GetIf(!strlen($val) && $isdef || $key == $val, " selected", "");
}

function sendActivation($row) {
    $url_activation = "https://" . $_SERVER['HTTP_HOST'] . Url_Create('activation', 'act=' . md5($row['TickleID']));
    $mail_content = "<p>" . $row['FirstName'] . ", thank you for creating a TickleTrain account!</p>
    <p>Please <a href='" . $url_activation . "'>click</a> the link below to authenticate your account.<br/>
    <a href='" . $url_activation . "'>" . $url_activation . "</a></p>
    <br/>
    <p><b>Username: " . $row['UserName'] . "</b><br/>
    <b>Password: ********</b></p>
    <p>Once you have logged in, watch our <i>Getting Started video</i> and you'll be up and running in minutes. Be sure and visit your Settings page to setup your email account and signature. We look forward to putting your emails to work for you.</p>
    <p>Send it and forget it!<br><a href=\"www.tickletrain.com\">www.tickletrain.com</a></p>";
    return SendMail($row['EmailID'], "noreply@tickletrain.com", "Welcome to TickleTrain", $mail_content, 'mail', true, array('jdickman@tickletrain.com', 'michael@speedgraphics.net'));
}

function sendRestore($row) {
    $mail_content = "<p>" . $row['FirstName'] . ", your password is: <b>" . $row['Password'] . "</b></p>
    <p>We look forward to putting your emails to work for you.</p>
    <p>Send it and forget it!<br><a href=\"www.tickletrain.com\">www.tickletrain.com</a></p>";
    return SendMail($row['EmailID'], "noreply@tickletrain.com", "Restore TickleTrain account", $mail_content, 'mail', true);
}

if(!empty($_POST['checkuserbyemail']) && $_POST['checkuserbyemail']=='checkuserbyemail'){
	//print_r($_POST);
	 $EmailID = trim($_POST['emailid']); 
	 if($_POST['plan']=="RedLine"){
		$plan_id=4;
	 }else{
		$plan_id=1;
	 } 
 	if($_POST['setup']=="isagenix"){
		$isagenix='&setup='.$_POST['setup'];
	  }else{
          	$isagenix="";
	  }
	$checkuser=whmcs_check_user(array('email'=>$EmailID));	
       if (is_array($checkuser) && ($checkuser['error']!='')) {   
    
	  echo 'error';
	
       } else {
	 sendEmailconfirmation_custom($EmailID,$plan_id,$isagenix);
	echo 'success';
       }
    unset($_POST['checkuserbyemail']);
    die();
}
if(!empty($_POST['checkuserbyemailresend']) && $_POST['checkuserbyemailresend']=='checkuserbyemailresend'){
	 $EmailID = trim($_POST['emailid']);
	
	 if($_POST['plan']=="RedLine"){
		$plan_id=4;
	 }else{
		$plan_id=1;
	 } 
	 if($_POST['setup']=="isagenix"){
		$isagenix='&setup='.$_POST['setup'];
	  }else{
          	$isagenix="";
	  }
	 sendEmailconfirmation_custom($EmailID,$plan_id,$isagenix);
	echo 'success';
	unset($_POST['checkuserbyemailresend']);
	exit();
}
if(!empty($_POST['checkwhmcresend']) && $_POST['checkwhmcresend']=='checkwhmcresend'){
	 $EmailID = trim($_POST['emailid']); 

	 if($_POST['plan']=="RedLine"){
		$plan_id=4;
	 }else{
		$plan_id=1;
	 } 
	
	 if($_POST['setup']=="isagenix"){
		$isagenix='&setup='.$_POST['setup'];
	  }else{
          	$isagenix="";
	  }
	 sendEmailconfirmation_custom($EmailID,$plan_id,$isagenix);
	echo 'success';
	//unset($_POST['checkuserbyemailresend']);
	exit();
}
function sendEmailconfirmation_custom($email,$plan_id,$isagenix){
//		print_r($_POST); exit();
    
    $url_activation = "https://tickletrain.com/email-register/?plan=".base64_encode($plan_id).'/'.base64_encode($email).$isagenix; 
    $logo="https://client.tickletrain.com/images/logo.png";	
    $mail_content = "<div style=\"margin:0px auto;padding:0px;width:100%;\">
    <div style=\"margin:0px;padding:5px 0px;font-family:Arial, Helvetica, sans-serif;font-size:28px;color:#2a2a2a;\">Thank you for your interest in TickleTrain! You're only a click away.</div><br>
    <div style=\"margin:0px;padding:0px;font-family:Arial, Helvetica, sans-serif;font-size:16px;color:#2a2a2a;line-height:24px;\">
     Free up time and be more productive by eliminating the need to follow up on emails when you don&#39;t get a reply and stay on top of important emails by turning them into tasks - simply type a unique email address in the \"BCC\" field of your email and let TickleTrain do the rest. <br><br>
There are a few more bits of information we need from you to activate your account. To compete your registration on our website, click the Verify Email icon below. We look forward to working with you.<br><br>
    </div>
     <div style=\"margin:0px;padding:0px;font-family:Arial, Helvetica, sans-serif;font-size:18px;color:#2a2a2a;line-height:24px;\">Before starting, we need you to <strong>verify your email address</strong>. Click the link below to continue.</div>
      <div style=\"margin:20px auto; text-align:center;\"><a href='".$url_activation."' style=\"background: none repeat scroll 0 0 #008acd;border: 1px solid #007fc2;color: #ffffff;     font-family: arial;font-size: 18px;height: 30px;line-height: 30px;margin: 0;padding: 8px 15px;text-decoration: none;text-transform: uppercase;\">Verify Email</a><div style=\"text-align=center;margin:5px 0;padding:0px;\"></div></div><p style=\"text-align:center;\">Having a problem? Please <a href=\"https://tickletrain.com/contact-us/\">contact us.</a></p>
  </div>
";
    // By doing so you agree to our <a style=\"color:#000;\" href=\"http://tickletrain.com/terms-of-use/\">Terms of Use</a> and <a style=\"color:#000;\" href='http://tickletrain.com/privacy-policy/'>Privacy Policy</a>.
    
    return SendMail($email, "noreply@tickletrain.com", "Please verify your email address", $mail_content, 'smtp', true, array('jdickman@tickletrain.com', 'michael@speedgraphics.net'));
}


function AddFiles($filecontext,$old_partner_id,$new_partner_id){
    global $db;
   $GetFiles_query = mysqli_query($db->conn,"select * from files where FileContext='".$filecontext."' and FileParentID='".$old_partner_id."'") or die(mysqli_error($db->conn). __LINE__);
   while($GetFiles = mysqli_fetch_assoc($GetFiles_query)){
      $AddNewePartnerFiles = mysqli_query($db->conn,"insert into files (FileName,FileContext,FileParentID) values ('".$GetFiles['FileName']."',
       '".$filecontext."','".$new_partner_id."')") or die(mysqli_error($db->conn). __LINE__);
   }
}

?>

