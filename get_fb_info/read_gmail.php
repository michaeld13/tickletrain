<?php
header('Access-Control-Allow-Origin: *');
include_once("../includes/data.php");
include_once("../includes/function/func.php");
error_reporting(E_ALL);
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
$name2 = "TickleTrain";

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
	$ticketfromarray = array();
	$ticklemailsubject = array();
	$ticklemailnumber = array();
     if($ticklelabel=="yes"){
        $storage->selectFolder("TickleTrain");
        if($storage->countMessages()>0){
		for ($i = 1; $i <= $storage->countMessages(); $i++ ){
			$message  = $storage->getMessage($i);
			if ($message->hasFlag(Zend_Mail_Storage::FLAG_SEEN)) {
				//$uid = $message->getUniqueId($i);
			   	//$storage->removeMessage($i);
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
				
				$MessageFromAddress = $message->from;
				$MesageFromAddressMailId = '';
			   	
				preg_match('/<(.*?)>/', $MessageFromAddress, $MesageFromAddressMailId);
			   	if (count($MesageFromAddressMailId) > 0) {
					$FromAddressOftickleMessage = $MesageFromAddressMailId[1];
			    	}
				$SubjectOftickleMessage = $message->subject;
		    		$SubjectOftickleMessage = trim(str_replace("RE:", "", $SubjectOftickleMessage));
			    	$SubjectOftickleMessage = str_replace("'","",trim(str_replace("Re:", "", $SubjectOftickleMessage)));
			    	array_push($ticklemailsubject,$SubjectOftickleMessage);
				array_push($ticketfromarray,$FromAddressOftickleMessage);
				array_push($ticklemailnumber,$i);
			 }
		}
        }
	
     }
	
     
     else{
     	$inbox = $storage->selectFolder("INBOX");
     	$storage->createFolder('TickleTrain');
     }
//print_r($ticklemailsubject);
    
    $query = mysqli_query($db->conn,"select distinct user_mail.MailID,user_mail.Subject,user_mail.toaddress,user_mail.TickleTitleID from user_mail,task where task.MailID=user_mail.MailID and  user_mail.TickleID='" . $_POST['tickleid'] . "' and task.Status!='N'") or die(mysqli_error($db->conn) . __LINE__);

$inbox = $storage->selectFolder("INBOX");
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

//print_r($emails);die();

	$subjectArray= array();
	$fromaddressarray = array();
	$emailArray = array();
       if ($emails) {
       		foreach ($emails as $email_number) {
		$email_number;
		$message  = $storage->getMessage($email_number);
		
		if ($message->hasFlag(Zend_Mail_Storage::FLAG_SEEN) == false ) {
				
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
			    	array_push($subjectArray,$SubjectOfRealMessage);
				array_push($fromaddressarray,$FromAddressOfRealMessage);
				array_push($emailArray,$email_number);
				}
		   }
        	}
	
$inbox = $storage->selectFolder("INBOX");
while ($row = mysqli_fetch_assoc($query)) {
	$inbox = $storage->selectFolder("INBOX");
   $fromaddress = $row['toaddress'];
	
    $OnlyMailAddress = '';
    preg_match('/<(.*?)>/', $fromaddress, $OnlyMailAddress);
    if (count($OnlyMailAddress) > 0) {
        $fromaddress = $OnlyMailAddress[1];
    }
                              
        $Databasemessaqge = trim(str_replace("RE:", "", $row['Subject']));
    	$Databasemessaqge = trim(str_replace("Re:", "", $Databasemessaqge));
	foreach($subjectArray as $key => $SubjectOfRealMessage){
		//print_r($SubjectOfRealMessage);

		//echo $row['TickleTitleID'];die();
		$Databasemessaqge = GetCustomSubjectForExtension($row['MailID'], $SubjectOfRealMessage, $Databasemessaqge, $row['TickleTitleID']);
	
	     //echo $fromaddressarray[$key].'-------'.$fromaddress.'-----'.$SubjectOfRealMessage.'-----'.$Databasemessaqge.'-----------End of the line-----<br/>';
	    if ($fromaddressarray[$key] == $fromaddress && $SubjectOfRealMessage == $Databasemessaqge) {

		if (isset($_POST['deletecompaign_checkbox']) && $_POST['deletecompaign_checkbox'] != "") {
		    $deletequery = mysqli_query($db->conn,"Delete from task where MailID='" . $row['MailID'] . "' and TickleID='" . $_POST['tickleid'] . "'");
		  
		}
		$storage->copyMessage ( $emailArray[$key], $name2 );
		//imap_mail_copy($inbox, "$email_number", "$name2") or die(imap_last_error());
	    }
	}

				/*$Databasemessaqge = GetCustomSubjectForExtension($row['MailID'], $SubjectOfRealMessage, $Databasemessaqge, $row['TickleTitleID']);
				
				    // echo $FromAddressOfRealMessage.'-------'.$fromaddress.'-----'.$SubjectOfRealMessage.'-----'.$Databasemessaqge.'-----------End of the line-----<br/>';
				    if ($FromAddressOfRealMessage == $fromaddress && $SubjectOfRealMessage == $Databasemessaqge) {
					if (isset($_POST['deletecompaign_checkbox']) && $_POST['deletecompaign_checkbox'] != "") {
					    $deletequery = mysqli_query($db->conn,"Delete from task where MailID='" . $row['MailID'] . "' and TickleID='" . $_POST['tickleid'] . "'");
					}
					$storage->copyMessage ( $email_number, $name2 );
					//imap_mail_copy($inbox, "$email_number", "$name2") or die(imap_last_error());
				    }*/
}



foreach($ticklemailsubject as $key => $ticklemailsubjects){
			 $Dbquery = mysqli_query($db->conn,"select Subject,TickleTitleID,MailID from user_mail where Subject='".mysqli_real_escape_string($db->conn,$ticklemailsubjects)."'") or die(mysqli_error($db->conn) . __LINE__);
			    if (mysqli_num_rows($Dbquery) == 1) {
					$data = mysqli_fetch_array($Dbquery);
					$mailIDdb = $data['MailID'];
					 $Dbquery1 = mysqli_query($db->conn,"select * from task where MailID='".$mailIDdb."'") or die(mysqli_error($db->conn) . __LINE__);
				if (mysqli_num_rows($Dbquery1) == 0) {
					 $storage->selectFolder("TickleTrain");
	                                 $storage->removeMessage($ticklemailnumber[$key]);
				}
			
			    }
	}



	$count_campaign = countcampaign11($_POST['tickleid']);

	$storage->selectFolder("TickleTrain");
	$fromaddressNewArray = array();
	//$receny_mails = $storage->recent;
	$TTFolderUnreadMail = $imap->search(array('ALL'));
	//$unread_mails = $storage->countMessages();
	$unread_mails = 0;
	if($TTFolderUnreadMail){
		$row_count = 0;
		$subject = array();
		foreach ($TTFolderUnreadMail as $TTFolderUnread) {
			$message  = $storage->getMessage($TTFolderUnread);
			 $message->getFlags($TTFolderUnread);
		   if ($message->hasFlag(Zend_Mail_Storage::FLAG_SEEN) == false) {
			$unread_mails ++;
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
			$flags = $message->getFlags();
			unset($flags[Zend_Mail_Storage::FLAG_SEEN]);
			$storage->setFlags($TTFolderUnread, $flags);
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
	if(!isset($receny_mails)){	$receny_mails = '';	}
	if(!isset($subject)){	$subject = '';	}
	$result = array('unread_mails' => $unread_mails, 'RecentMails' => $receny_mails, 'count_campaign' => $count_campaign, 'subject' => $subject, 'fromaddress' => $fromaddressNewArray);
	header('Access-Control-Allow-Origin: *');
    header('Content-type: application/json');
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


$checkToken = mysqli_num_rows(mysqli_query($db->conn,"select id from google_auth_tokens where userid='".$_POST['tickleid']."' "));
if($checkToken>0){
    
      $subject = array();
      $fromaddressNewArray = array();       
	
	$query = mysqli_query($db->conn,"select Subject,toaddress from user_mail where TickleID = '".$_POST['tickleid']."'");
   
    $ttMailidSubjects = array();
	
	while($row = mysqli_fetch_assoc($query)){

            $subject[] = $row['Subject'];
            $fromaddressNewArray[] = $row['toaddress'];
            $ttMailidSubjects[] = array('subject' => $row['Subject'],'toaddress' => $row['toaddress']);
	}
	
	
	if(isset($_POST['subjectsMails']) && !empty($_POST['subjectsMails']))// && ($_POST['tickleid']=='1843' || $_POST['tickleid']=='1925')
	{
		$fromaddressarray = array();
		$subjectArray = array();
		$subjectsMails = explode(',,,,',$_POST['subjectsMails']);
		foreach($subjectsMails as $key => $subjectsMail){
			if($key!='0'){
				$subjectsMail02 = explode('____',$subjectsMail);
				$SubjectOftickleMessage = $subjectsMail02[1];
				$SubjectOftickleMessage = trim(str_replace("RE:", "", $SubjectOftickleMessage));
				$SubjectOftickleMessage = str_replace("'","",trim(str_replace("Re:", "", $SubjectOftickleMessage)));
				array_push($subjectArray,$SubjectOftickleMessage);
				array_push($fromaddressarray,$subjectsMail02[0]);			
			}
		}
		$query = mysqli_query($db->conn,"select distinct user_mail.MailID,user_mail.Subject,user_mail.toaddress,user_mail.TickleTitleID from user_mail,task where task.MailID=user_mail.MailID and  user_mail.TickleID='" . $_POST['tickleid'] . "' and task.Status!='N'") or die(mysqli_error($db->conn) . __LINE__);		
		while ($row = mysqli_fetch_assoc($query))
		{
			$fromaddress = $row['toaddress'];	
			$OnlyMailAddress = '';
			preg_match('/<(.*?)>/', $fromaddress, $OnlyMailAddress);
			if (count($OnlyMailAddress) > 0) {
				$fromaddress = $OnlyMailAddress[1];
			}		
			
			$Databasemessaqge = trim(str_replace("RE:", "", $row['Subject']));
			$Databasemessaqge = trim(str_replace("Re:", "", $Databasemessaqge));
    	
			foreach($subjectArray as $key => $SubjectOfRealMessage){
				$Databasemessaqge = GetCustomSubjectForExtension($row['MailID'], $SubjectOfRealMessage, $Databasemessaqge, $row['TickleTitleID']);
//                                echo '<pre>';
//                                echo $fromaddressarray[$key].' == '.$fromaddress.'<br>';
//                                echo $SubjectOfRealMessage.' == '.$Databasemessaqge.'<br>';
//                                print_r($Databasemessaqge);
//                                print_r($SubjectOfRealMessage); 
				if ($fromaddressarray[$key] == $fromaddress && str_replace(' ','',$SubjectOfRealMessage) == str_replace(' ','',$Databasemessaqge)){
					if (isset($_POST['deletecompaign_checkbox']) && $_POST['deletecompaign_checkbox'] != "") {
					$deletequery = mysqli_query($db->conn,"Delete from task where MailID='" . $row['MailID'] . "' and TickleID='" . $_POST['tickleid'] . "'");
					}
				}
			}
		}	
	}
	$count_campaign = countcampaign11($_POST['tickleid']);
	$result = array('unread_mails' => '', 'RecentMails' => '', 'count_campaign' => $count_campaign, 'subject' => $subject, 'fromaddress' => $fromaddressNewArray,'ttMailidSubjects'=>$ttMailidSubjects);
    die(json_encode($result));
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
header('Access-Control-Allow-Origin: *');
      header('Content-type: application/json');
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
    $newcheckarray = array();
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
    $newcheckarrayagain = array();
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
    if (isset($sfld) && $sfld == 5 && !$sord) {
        asort($sMails);
    }
    if (isset($sfld) && $sfld == 5 && $sord) {
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
    $GetTickleCustomSubjectQuery = mysqli_query($db->conn,"select custom_subject from tickle where TickleTrainID='".$TickleTitlteId."' and custom_subject='".mysqli_real_escape_string($db->conn,$MailSubject)."'") or die(mysqli_error($db->conn) . __LINE__);
    if (mysqli_num_rows($GetTickleCustomSubjectQuery) > 0) {
        $GetTickleCustomSubjectRow = mysqli_fetch_assoc($GetTickleCustomSubjectQuery);
        $CustomSubjectForTodayTickleMail = $GetTickleCustomSubjectRow['custom_subject'];
    } else {
        $GetTickleFollowCustomSubjectQuery = mysqli_query($db->conn,"select custom_subject from ticklefollow where TickleTrainID='".$TickleTitlteId."' and custom_subject='".mysqli_real_escape_string($db->conn,$MailSubject)."'") or die(mysqli_error($db->conn) . __LINE__);
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
