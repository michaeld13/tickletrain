<?php

include_once("../includes/data.php");
include_once("../includes/function/func.php");
//session_start();
//preg_match("/^[A-Za-z]{3}[0-9][2]$/", $subject);

set_include_path('/var/www/vhosts/client.tickletrain.com/httpdocs/google_auth2/');
require_once('Zend/Mail/Protocol/Imap.php');
require_once('Zend/Mail/Storage/Imap.php');


if (!isset($_COOKIE['LastTimseRead'])) {
    $LastTwentyDays = date("j F Y", strtotime('-20 days'));
    $yesterday = date("j F Y", strtotime('-1 days'));
    setcookie("LastTimeRead", $yesterday, time() + 3600 * 1);
}

if (isset($_GET['gmail_id'])) {
    $gmailid = base64_decode($_GET['gmail_id']);
    $gmailpassword = base64_decode($_GET['gmail_password']);
    setcookie("gmail_id", $gmailid, time() + 3600 * 60);
    setcookie("gmail_password", $gmailpassword, time() + 3600 * 60);
    echo "<script>window.close();</script>";
}

function tryImapLogin($email, $accessToken) {
    global $db;
echo $name2 = "TickleTrain";

  /**
   * Make the IMAP connection and send the auth request
   */
  // echo $email;
  $imap = new Zend_Mail_Protocol_Imap('imap.gmail.com', '993', true);
  
  $ticklelabel = "no";
  if(oauth2Authenticate($imap, $email, $accessToken)){
   // echo"authnticate";
    $storage = new Zend_Mail_Storage_Imap($imap);
    $folders = new RecursiveIteratorIterator($storage->getFolders());
     foreach ($folders as $localName => $folder) {
      // $localName = str_pad('', $folders->getDepth(), '-', STR_PAD_LEFT);
      //echo htmlspecialchars($folder);
      if(htmlspecialchars($folder)=="TickleTrain"){
          $ticklelabel = "yes";
          //$storage->selectFolder("TickleTrain");
      }
     }
     if($ticklelabel=="yes"){
        $storage->selectFolder("TickleTrain");
        if($storage->countMessages()>0){
		for ($i = 1; $i <= $storage->countMessages(); $i++ ){
			$message  = $storage->getMessage($i);
			if ($message->hasFlag(Zend_Mail_Storage::FLAG_SEEN)) {
				//$uid = $message->getUniqueId($i);
			   	$storage->removeMessage($i);
			 }
		}
        }
	
     }
     
     else{
     	$inbox = $storage->selectFolder("INBOX");
     	$storage->createFolder('TickleTrain');
     }
    
    $query = mysqli_query($db->conn,"select distinct user_mail.MailID,user_mail.Subject,user_mail.toaddress,user_mail.TickleTitleID from user_mail,task where task.MailID=user_mail.MailID and  user_mail.TickleID='" . $_POST['tickleid'] . "' and task.Status!='N'") or die(mysqli_error($db->conn) . __LINE__);

$inbox = $storage->selectFolder("INBOX");
while ($row = mysqli_fetch_assoc($query)) {
    $fromaddress = $row['toaddress'];
    $OnlyMailAddress = '';
    preg_match('/<(.*?)>/', $fromaddress, $OnlyMailAddress);
    if (count($OnlyMailAddress) > 0) {
        $fromaddress = $OnlyMailAddress[1];
    }
    if (isset($LastTwentyDays)) {
        $LastTwentyDays = date('d-M-Y', strtotime($LastTwentyDays));
	//$date1 = date("d-M-Y"); 
        $emails = $imap->search(array('SINCE "'.$LastTwentyDays.'"'));
    } else {
	//echo"1";
	$LastTwentyDays = date('d-M-Y', strtotime($_COOKIE['LastTimeRead']));
	$emails = $imap->search(array('SINCE "'.$LastTwentyDays.'"'));
	//print_r($emails);
        //$emails = imap_search($inbox, 'UNSEEN SINCE "' . $_COOKIE['LastTimeRead'] . '"');
    }
	
       if ($emails) {
       		foreach ($emails as $email_number) {
		$email_number;
		$message  = $storage->getMessage($email_number);
		if (!$message->hasFlag(Zend_Mail_Storage::FLAG_SEEN)) {
				foreach (new RecursiveIteratorIterator($message) as $part) {
				    try {
					if (strtok($part->contentType, ';') == 'text/html') {
					    echo $foundPart = quoted_printable_decode($part);
					    //break;
					}
				    } catch (Zend_Mail_Exception $e) {
					// ignore
				    }
				}
				if ($foundPart) {
				     $MessageBody =  $foundPart;
				} 
				$flags = $message->getFlags();
				unset($flags[Zend_Mail_Storage::FLAG_SEEN]);
				$storage->setFlags($email_number, $flags);
				$MessageFromAddress = $message->from;
				$MesageFromAddressMailId = '';
			   	preg_match('/<(.*?)>/', $MessageFromAddress, $MesageFromAddressMailId);
			   	if (count($MesageFromAddressMailId) > 0) {
					$FromAddressOfRealMessage = $MesageFromAddressMailId[1];
			    	}
				$SubjectOfRealMessage = $message->subject;
		    		$SubjectOfRealMessage = trim(str_replace("RE:", "", $SubjectOfRealMessage));
			    	$SubjectOfRealMessage = str_replace("'","",trim(str_replace("Re:", "", $SubjectOfRealMessage)));
			    	$Databasemessaqge = trim(str_replace("RE:", "", $row['Subject']));
			    	$Databasemessaqge = trim(str_replace("Re:", "", $Databasemessaqge));
				$Databasemessaqge = GetCustomSubjectForExtension($row['MailID'], $SubjectOfRealMessage, $Databasemessaqge, $row['TickleTitleID']);
				
				    // echo $FromAddressOfRealMessage.'-------'.$fromaddress.'-----'.$SubjectOfRealMessage.'-----'.$Databasemessaqge.'-----------End of the line-----<br/>';
				    if ($FromAddressOfRealMessage == $fromaddress && $SubjectOfRealMessage == $Databasemessaqge) {
					if (isset($_POST['deletecompaign_checkbox']) && $_POST['deletecompaign_checkbox'] != "") {
					    $deletequery = mysqli_query($db->conn,"Delete from task where MailID='" . $row['MailID'] . "' and TickleID='" . $_POST['tickleid'] . "'");
					}
					$storage->moveMessage ( $email_number, $name2 );
					//imap_mail_copy($inbox, "$email_number", "$name2") or die(imap_last_error());
				    }

				}
		}
        }
   
}

$count_campaign = countcampaign11($_POST['tickleid']);
	$storage->selectFolder("TickleTrain");
	$unread_mails = $storage->countMessages();
	//$fromaddressNewArray = array();
	//$receny_mails = $storage->recent;
	$TTFolderUnreadMail = $imap->search(array('ALL'));
	if($TTFolderUnreadMail){
		foreach ($TTFolderUnreadMail as $TTFolderUnread) {
			$message  = $storage->getMessage($TTFolderUnread);
			 $message->getFlags($TTFolderUnread);
		   if (!$message->hasFlag(Zend_Mail_Storage::FLAG_SEEN)) {
			foreach (new RecursiveIteratorIterator($message) as $part) {
			    try {
				if (strtok($part->contentType, ';') == 'text/html') {
				    $foundPart = quoted_printable_decode($part);
				    //break;
				}
			    } catch (Zend_Mail_Exception $e) {
				// ignore
			    }
			}
			if ($foundPart) {
			    $MessageBody =  $foundPart;
			}
			//$MessageBody = imap_fetch_overview($TTFolderConnection, $TTFolderUnread);
			$MessageFromAddress = $message->from;
			$MesageFromAddressMailId = '';
			preg_match('/<(.*?)>/', $MessageFromAddress, $MesageFromAddressMailId);
			if (count($MesageFromAddressMailId) > 0) {
			    $FromAddressOfRealMessage = $MesageFromAddressMailId[1];
			}
			$TTMailSubject = $message->subject;
			$subject[$row_count] = $TTMailSubject;
			$fromaddressNewArray[$row_count] = $FromAddressOfRealMessage;
			$row_count++;
	   	 }
		}
	}
	$result = array('unread_mails' => $unread_mails, 'RecentMails' => $receny_mails, 'count_campaign' => $count_campaign, 'subject' => $subject, 'fromaddress' => $fromaddressNewArray);
        die(json_encode($result));

  }
   
}

function constructAuthString($email, $accessToken) {
  return base64_encode("user=$email\1auth=Bearer $accessToken\1\1");
}

/**
 * Given an open IMAP connection, attempts to authenticate with OAuth2.
 *
 * $imap is an open IMAP connection.
 * $email is a Gmail address.
 * $accessToken is a valid OAuth 2.0 access token for the given email address.
 *
 * Returns true on successful authentication, false otherwise.
 */
function oauth2Authenticate($imap, $email, $accessToken) {
  $authenticateParams = array('XOAUTH2',
      constructAuthString($email, $accessToken));
  $imap->sendRequest('AUTHENTICATE', $authenticateParams);
  while (true) {
    $response = "";
    $is_plus = $imap->readLine($response, '+', true);
    if ($is_plus) {
      error_log("got an extra server challenge: $response");
      // Send empty client response.
      $imap->sendRequest('');
    } else {
     // echo $response;
      if (preg_match('/^NO /i', $response) ||
          preg_match('/^BAD /i', $response)) {
        error_log("got failure response: $response");
        return false;
      } else if (preg_match("/^OK /i", $response)) {
        return true;
      } else {
        // Some untagged response, such as CAPABILITY
      }
    }
  }
}

if($_SERVER['REMOTE_ADDR']=='202.164.47.148')
    $_POST['tickleid'] = '1176';
    $checkToken = mysqli_num_rows(mysqli_query($db->conn,"select id from google_auth_tokens where userid='".$_POST['tickleid']."' "));
if($checkToken>0){
	$name2 = "TickleTrain";
        $gfpath01 = str_replace('get_fb_info','',__DIR__);
	$gfpath = $gfpath01.'/google_auth2/';
	require_once $gfpath.'src/Google_Client.php'; // include the required calss files for google login
	require_once $gfpath.'src/contrib/Google_PlusService.php';
	require_once $gfpath.'src/contrib/Google_Oauth2Service.php';
        $getGoogleToken = mysqli_fetch_object(mysqli_query($db->conn,"select access_token,token_type,expires_in,id_token,refresh_token,created from google_auth_tokens where userid='".$_POST['tickleid']."' "));

		$client = new Google_Client();
		$client->setClientId('799405691032-er3cilvjgrqgtlfreuffllvkp2ouvrjb.apps.googleusercontent.com'); // paste the client id which you get from google API Console
	        $client->setClientSecret('QYmRweaDw20scMLTidBR8MRB'); // set the client secret
		$client->refreshToken($getGoogleToken->refresh_token);
		$getGoogleToken = $client->getAccessToken();
		$getGoogleToken02 = json_decode($getGoogleToken,true);	
		//print_r($getGoogleToken02);	
		//$_SESSION['acctkn']=$getGoogleToken02['access_token'];

$update = mysqli_query($db->conn,"update google_auth_tokens set access_token='" . $getGoogleToken02['access_token'] . "' , expires_in='" . $getGoogleToken02['expires_in'] . "' , created='" . $getGoogleToken02['created'] . "' where userid='".$_POST['tickleid']."'");

$username = $_COOKIE['gmail_id'];
if($_SERVER['REMOTE_ADDR']=='202.164.47.148')
$username = 'jaswant.shinedezign@gmail.com';
//$username = $_COOKIE['email_id'];
$accessToken = $getGoogleToken02['access_token'];
tryImapLogin($username, $accessToken);

}



else{
//print_r($_COOKIE);
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = $_COOKIE['gmail_id'];
//$username = $_COOKIE['email_id'];
$password = $_COOKIE['gmail_password'];
//$_POST['tickleid'] = '600';

$name2 = "TickleTrain";
$unread_box = imap_open("{imap.gmail.com:993/imap/ssl}$name2", $username, $password);
$status_unread = @imap_status($unread_box, "{imap.gmail.com}$name2", SA_ALL) or $unread_box_open_error = "error_for_unread_box";
if (isset($unread_box_open_error) && $unread_box_open_error == "error_for_unread_box") {
    imap_close($unread_box);
    $inbox = imap_open($hostname, "$username", "$password") or die('Cannot connect to Gmail: ' . imap_last_error());
    imap_createmailbox($inbox, imap_utf7_encode("{imap.gmail.com}$name2")) or die('Issue is: ' . imap_last_error());
} else {
    $unread_mails_here = imap_search($unread_box, 'SEEN');
    if ($unread_mails_here) {
        foreach ($unread_mails_here as $unred_email_number) {
            imap_delete($unread_box, $unred_email_number) or die(imap_last_error());
            imap_expunge($unread_box);
        }
    }
    imap_close($unread_box);
}
if (!isset($inbox)) {
    $inbox = imap_open($hostname, $username, "$password") or die('Cannot connect to Gmail: ' . imap_last_error());
}
$query = mysqli_query($db->conn,"select distinct user_mail.MailID,user_mail.Subject,user_mail.toaddress,user_mail.TickleTitleID from user_mail,task where task.MailID=user_mail.MailID and  user_mail.TickleID='" . $_POST['tickleid'] . "' and task.Status!='N'") or die(mysqli_error($db->conn) . __LINE__);
while ($row = mysqli_fetch_assoc($query)) {
    $fromaddress = $row['toaddress'];
    $OnlyMailAddress = '';
    preg_match('/<(.*?)>/', $fromaddress, $OnlyMailAddress);
    if (count($OnlyMailAddress) > 0) {
        $fromaddress = $OnlyMailAddress[1];
    }
    if (isset($LastTwentyDays)) {
        $emails = imap_search($inbox, 'UNSEEN SINCE "' . $LastTwentyDays . '"');
    } else {
        $emails = imap_search($inbox, 'UNSEEN SINCE "' . $_COOKIE['LastTimeRead'] . '"');
    }
    if ($emails) {
        foreach ($emails as $email_number) {
	    $email_number;
            $MessageBody = imap_fetch_overview($inbox, $email_number);
            $MessageFromAddress = $MessageBody[0]->from;
            $MesageFromAddressMailId = '';
            preg_match('/<(.*?)>/', $MessageFromAddress, $MesageFromAddressMailId);
            if (count($MesageFromAddressMailId) > 0) {
                $FromAddressOfRealMessage = $MesageFromAddressMailId[1];
            }
            $SubjectOfRealMessage = $MessageBody[0]->subject;
            $SubjectOfRealMessage = trim(str_replace("RE:", "", $SubjectOfRealMessage));
            $SubjectOfRealMessage = str_replace("'","",trim(str_replace("Re:", "", $SubjectOfRealMessage)));
            $Databasemessaqge = trim(str_replace("RE:", "", $row['Subject']));
            $Databasemessaqge = trim(str_replace("Re:", "", $Databasemessaqge));
            $Databasemessaqge = GetCustomSubjectForExtension($row['MailID'], $SubjectOfRealMessage, $Databasemessaqge, $row['TickleTitleID']);
            // echo $FromAddressOfRealMessage.'-------'.$fromaddress.'-----'.$SubjectOfRealMessage.'-----'.$Databasemessaqge.'-----------End of the line-----<br/>';
            if ($FromAddressOfRealMessage == $fromaddress && $SubjectOfRealMessage == $Databasemessaqge) {
                if (isset($_POST['deletecompaign_checkbox']) && $_POST['deletecompaign_checkbox'] != "") {
                    $deletequery = mysqli_query($db->conn,"Delete from task where MailID='" . $row['MailID'] . "' and TickleID='" . $_POST['tickleid'] . "'");
                }
                imap_mail_copy($inbox, "$email_number", "$name2") or die(imap_last_error());
            }
        }
    }
}

$status_unread = @imap_status($inbox, "{imap.gmail.com}$name2", SA_ALL);
$unread_mails = $status_unread->messages;
$receny_mails = $status_unread->recent;
$count_campaign = countcampaign11($_POST['tickleid']);
imap_close($inbox);

$TTFolderConnection = imap_open("{imap.gmail.com:993/imap/ssl}$name2", $username, $password);
$TTFolderUnreadMail = imap_search($TTFolderConnection, 'UNSEEN');
if ($TTFolderUnreadMail) {
    $row_count = 0;
    foreach ($TTFolderUnreadMail as $TTFolderUnread) {
        $MessageBody = imap_fetch_overview($TTFolderConnection, $TTFolderUnread);
        $MessageFromAddress = $MessageBody[0]->from;
        $MesageFromAddressMailId = '';
        preg_match('/<(.*?)>/', $MessageFromAddress, $MesageFromAddressMailId);
        if (count($MesageFromAddressMailId) > 0) {
            $FromAddressOfRealMessage = $MesageFromAddressMailId[1];
        }
        $TTMailSubject = $MessageBody[0]->subject;
        $subject[$row_count] = $TTMailSubject;
        $fromaddressNewArray[$row_count] = $FromAddressOfRealMessage;
        $row_count++;
    }
}
imap_close($TTFolderConnection);

$result = array('unread_mails' => $unread_mails, 'RecentMails' => $receny_mails, 'count_campaign' => $count_campaign, 'subject' => $subject, 'fromaddress' => $fromaddressNewArray);
die(json_encode($result));

}

function countcampaign11($uid) {
    global $db;
    $allowed_compaign = mysqli_query($db->conn,"select Allowe_campaign from Compaign where TickleID='$uid'");
    while ($allowcamp = mysqli_fetch_assoc($allowed_compaign)) {
        $allowcampaign = $allowcamp['Allowe_campaign'];
    }
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
    $returnarray = array("currentcampaign" => $currentcampaign, "allowedcampaign" => $allowcampaign);
    return $returnarray;
}



function GetCustomSubjectForExtension($MailID, $MailSubject, $Databasemessaqge, $TickleTitlteId) {
   global $db;
    $GetTickleCustomSubjectQuery = mysqli_query($db->conn,"select custom_subject from tickle where TickleTrainID='".$TickleTitlteId."' and custom_subject='".mysqli_real_escape_string($db->conn, $MailSubject)."'") or die(mysqli_error($db->conn) . __LINE__);
    if (mysqli_num_rows($GetTickleCustomSubjectQuery) > 0) {
        $GetTickleCustomSubjectRow = mysqli_fetch_assoc($GetTickleCustomSubjectQuery);
        $CustomSubjectForTodayTickleMail = $GetTickleCustomSubjectRow['custom_subject'];
    } else {
        $GetTickleFollowCustomSubjectQuery = mysqli_query($db->conn,"select custom_subject from ticklefollow where TickleTrainID='".$TickleTitlteId."' and custom_subject='".mysqli_real_escape_string($db->conn, $MailSubject)."'") or die(mysqli_error($db->conn) . __LINE__);
        if (mysqli_num_rows($GetTickleFollowCustomSubjectQuery) > 0) {
            $GetTickleFollowCustomSubjectRow = mysqli_fetch_assoc($GetTickleFollowCustomSubjectQuery);
            $CustomSubjectForTodayTickleMail = $GetTickleFollowCustomSubjectRow['custom_subject'];
        }
    }
    if (!isset($CustomSubjectForTodayTickleMail) || $CustomSubjectForTodayTickleMail == "") {
        $CustomSubjectForTodayTickleMail = $Databasemessaqge;
    }
    
     $CustomSubjectForTodayTickleMail;
    
    //  return mysqli_num_rows($GetTickleCustomSubjectQuery);
    return $CustomSubjectForTodayTickleMail;
}

?>
