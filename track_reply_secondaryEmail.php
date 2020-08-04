<!-- <title>Track reply</title> -->
<?php
//use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require("includes/class/PHPMailer/src/Exception.php");
require("includes/class/PHPMailer/src/PHPMailer.php");
require("includes/class/PHPMailer/src/SMTP.php");


include_once("includes/data.php");
include("includes/function/func.php");

    ini_set('memory_limit', '-1');
    //ini_set('display_errors', 1);
    //error_reporting(E_ALL);

define('SERVER_NAME', "client.tickletrain.com");
define('ROOT_FOLDER', "new/");
define('HOME_FOLDER', GetHomeDir() . "/");
define('IMAGE_BASE_FOLDER', str_replace(ROOT_FOLDER, "", HOME_FOLDER));

set_include_path('/var/www/vhosts/client.tickletrain.com/httpdocs/google_auth2/');
require_once('Zend/Mail/Protocol/Imap.php');
require_once('Zend/Mail/Storage/Imap.php');



$email_templete =  file_get_contents(HOME_FOLDER.'emails/reply_track.html');


$mailBcc = new PHPMailer(false);
//$mailBcc->IsMail();
// $mailBcc->isSMTP();                                      // Set mailer to use SMTP
// $mailBcc->Host = $TtSmtpHost;  // Specify main and backup SMTP servers
// $mailBcc->SMTPAuth = $TtSmtpAuth;                               // Enable SMTP authentication
// $mailBcc->Username = $TtSmtpUsername;                 // SMTP username
// $mailBcc->Password = $TtSmtpPassword;                           // SMTP password
// $mailBcc->SMTPSecure = $TtSmtpSecure;                            // Enable TLS encryption, `ssl` also accepted
// $mailBcc->Port = $TtSmtpPort;                                    // TCP port to connect to
// $mailBcc->IsHTML(true);


    $mailBcc->IsSMTP();
    $mailBcc->Mailer = "smtp";    
    $mailBcc->Host = $TtSmtpHost;  //'secureus186.sgcpanel.com';  // Specify main and backup SMTP servers
    $mailBcc->SMTPAuth = true;                               // Enable SMTP authentication
    $mailBcc->Username = $TtSmtpUsername;                 // SMTP username
    $mailBcc->Password = $TtSmtpPassword;                           // SMTP password
    $mailBcc->SMTPSecure = $TtSmtpSecure;    
    $mailBcc->SMTPAutoTLS = false;                        // Enable TLS encryption, `ssl` also accepted
    $mailBcc->Port = $TtSmtpPort;
    $mailBcc->IsHTML(true);
    $mailBcc->Timeout = 60;


    $gfpath01 = str_replace('app', '', __DIR__);
    $gfpath = $gfpath01 . '/google_auth2/';
    require_once $gfpath . 'src/Google_Client.php'; // include the required calss files for google login
    require_once $gfpath . 'src/contrib/Google_PlusService.php';
    require_once $gfpath . 'src/contrib/Google_Oauth2Service.php';
 

    $client = new Google_Client();
    $client->setClientId('799405691032-er3cilvjgrqgtlfreuffllvkp2ouvrjb.apps.googleusercontent.com'); // paste the client id which you get from google API Console
    $client->setClientSecret('QYmRweaDw20scMLTidBR8MRB'); // set the client secret

    /**
     * Builds an OAuth2 authentication string for the given email address and access
     * token.
     */
    function constructAuthString($email, $accessToken) {
        return base64_encode("user=$email\1auth=Bearer $accessToken\1\1");
    }


    function update_google_token($refresh_token,$user_id)
    {
        global $client,$db;
        # code...
        try{
         $client->refreshToken($refresh_token);
        $getGoogleToken = $client->getAccessToken();
        $responce = json_decode($getGoogleToken, true);
	
		 $createddate = date('Y-m-d H:i:s',$responce['created']);
		$exprydate = date('Y-m-d H:i:s',($responce['created'] + $responce['expires_in']));
       // echo "update secondaryEmail set authtoken='" . $responce['access_token'] . "' , token_exp_date='" . $exprydate . "'  where TickleID=".$user_id;
  
		mysqli_query($db->conn,"update secondaryEmail set authtoken='" . $responce['access_token'] . "' , token_exp_date='" . $exprydate . "'  where TickleID=".$user_id);

        return false;

        return  $responce['access_token'];
        }catch(Exception $e){
            return false;
        }
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
				//echo $response;
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

    /*
    Function : filter_email();
    Use : get user email for User name<exemple@gmail.com> to exemple@gmail.com
    Created By : Sandeep Rathour
    Date : 08-10-19 
    */
    function filter_email($MessageFromAddress) {
        $startMailTag = strpos($MessageFromAddress,"<");
        $endMailTag = strpos($MessageFromAddress,">");
        if(!empty($startMailTag) && !empty($endMailTag))
        {    
            $limit = $endMailTag - $startMailTag;        
            $MessageFromAddress = substr($MessageFromAddress,$startMailTag+1,$limit-1);
            $MessageFromAddress = trim($MessageFromAddress);
            return strtolower($MessageFromAddress);
        }  else {
            return strtolower($MessageFromAddress);
        } 
    }


    function sd_checkmailtype($subject){
        $replaceSubjectReply = array("RE:","re:", "re :", "Re:", "RE :","RE :[EXTERNAL]","RE:[EXTERNAL]","Re :[EXTERNAL]","Re:[EXTERNAL]","AW:" , "Aw:");
        $val = 0;
        foreach($replaceSubjectReply as $replaceSubjectVal)
        {
            if(str_replace($replaceSubjectVal, '', $subject) != $subject){
                $val = 1;
            }
        }   
        return $val;
    }


    function already_scaned($message_id){
        global $db;
        $q = "select * from task_track_records where message_id='" . $message_id . "' AND type='reply_receved' ORDER BY id DESC LIMIT 1";
        $task_result = mysqli_query($db->conn,$q);
        return (mysqli_num_rows($task_result) == 0);
       // $is_already_reply_on_task = mysqli_fetch_assoc($task_result);
    }


    function get_next_email_content($mail_id,$Firstname,$LastName,$signature){
        global $db;

        $query = "SELECT task.TaskID, task.TaskInitiateDate, task.FollowTickleTrainID,tickle.TickleMailContent, ticklefollow.TickleMailFollowContent FROM `task` LEFT JOIN `ticklefollow` ON  task.FollowTickleTrainID = ticklefollow.FollowTickleTrainID JOIN tickle ON tickle.TickleTrainID = task.TickleTrainID WHERE task.MailID=".$mail_id." AND task.status='Y' order by task.TaskGMDate ASC limit 1";
        $result = mysqli_query($db->conn,$query);
        if(mysqli_num_rows($result) > 0 ){
            $next_email = mysqli_fetch_assoc($result);

            $Firstname = str_replace("'", "", $FirstName);
            $Lastname = str_replace("'", "", $LastName);
            $MailContent = ($next_email['FollowTickleTrainID'] == 0) ? $next_email['TickleMailContent'] : $next_email['TickleMailFollowContent'];

            if (@trim($Firstname) != "") {
                $MailContent = preg_replace("/\[firstname\]/i", $Firstname, $MailContent);
            }else{
                $MailContent = preg_replace("/\[firstname\]/i", "", $MailContent);
            }

            if (@trim($Lastname)) {
                $MailContent = preg_replace("/\[LastName\]/i", $Lastname, $MailContent);
            }else{
                $MailContent = preg_replace("/\[LastName\]/i", "", $MailContent);
            }

           $MailContent = preg_replace("/\[signature\]/i", $signature, $MailContent);
           $MailContent = str_replace("Hi ,", 'Hi,', $MailContent);
           $timescheduled = date("m/d/y h:i a", strtotime($next_email['TaskInitiateDate']));

            return  [
                'body' =>  $MailContent,
                'replied_on' => "Your next tickle scheduled to send on " . $timescheduled
            ];

        }else{
            return false;
        }

        # code...
    }


    function delete_campaign($MailID){
            global $db;
			$delDate = date("Y-m-d H:i:s");
        // $deletequery = mysqli_query($db->conn,"Delete from task where MailID=".$MailID);
        // $deleteMailID = mysqli_query($db->conn,"delete from user_mail where MailID=".$MailID);
            mysqli_query($db->conn,"update user_mail set Status='D' where MailID='" . $MailID . "'");
            mysqli_query($db->conn,"update task set Status='D',TaskDeletedDate='".$delDate."' where Status='Y' and  MailID='" . $MailID . "'");
            mysqli_query($db->conn,"delete from task_track_records where mail_id=".$MailID);
    }


    $replaceSubjectValue=array("RE :[EXTERNAL]","RE:[EXTERNAL]","Re :[EXTERNAL]","Re:[EXTERNAL]","RE:", "Re:", "RE :", "AW:" , "Aw:" ,"Fw:", "FW:", "FW :", "fw:", "fw :", "Fw :", "Fwd:", "Fwd :", "fwd:", "FWD:");
    $replaceSubjectWithValue = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "","","");

    $sqlQuery = "SELECT secondaryEmail.EmailID as secondemail,secondaryEmail.authtoken as secondauthtoken, secondaryEmail.refresh_token as secondrefreshtoken,secondaryEmail.TickleID as secondtickleid, secondaryEmail.token_exp_date as secondtoknexp,secondaryEmail.signature as secondsign ,secondaryEmail.imap_host as secondimaphost, secondaryEmail.imapOff as secondimapoff, secondaryEmail.imap_userame as secondimapuser, secondaryEmail.imap_passowrd as secondimappass,secondaryEmail.imap_port as secondimaport,secondaryEmail.imap_secure as secondimapsecure,
	tickleuser.TickleID, tickleuser.TimeZone, tickleuser.EmailID,google_auth_tokens.access_token, google_auth_tokens.refresh_token, google_auth_tokens.created as google_token_created_date, google_auth_tokens.expires_in, tickleuser.signature , tickleuser.enable_alt , 
	tickleuser.alt_email , tickleuser.imap_host , tickleuser.imapOff, tickleuser.delete_all_campaign, tickleuser.imap_userame, tickleuser.imap_passowrd,tickleuser.imap_port,tickleuser.imap_secure FROM `tickleuser` LEFT JOIN secondaryEmail ON secondaryEmail.TickleID = tickleuser.TickleID LEFT JOIN google_auth_tokens ON google_auth_tokens.userid = tickleuser.TickleID WHERE tickleuser.imapOff != '1' AND ((google_auth_tokens.access_token != '' AND google_auth_tokens.refresh_token != '') OR (tickleuser.imap_passowrd != '' AND tickleuser.imap_userame != '' AND tickleuser.imap_host != ''))";
   
    // tickleuser.TickleID=2009  AND 
    //  4658798 , 4658799 , 4658809

    $query_res = mysqli_query($db->conn,$sqlQuery);
    $currentmonh = date('M Y');
    $oldmonth = date('M Y', strtotime('-1 month'));
    $last_three_month = date('M Y', strtotime('-2 month'));
    $last_fourth_month = date('M Y', strtotime('-3 month'));
    $total_email_sent = 0;
    $deleted_campaign = 0;


    while ($user = mysqli_fetch_assoc($query_res)){ 

        /*if(!empty($user['refresh_token']) && !empty($user['access_token'])){
            $user['google_token_created_at'] = date('Y-m-d H:i:s',$user['google_token_created_date']);
            $user['google_token_expire_at'] = date('Y-m-d H:i:s',($user['google_token_created_date']+ $user['expires_in']));
        }*/
		
	
        $user_id = $user['TickleID'];
        $signature = $user['signature'];
        $qr =  "SELECT user_mail.ContactID,contact_list.FirstName,contact_list.LastName, user_mail.MailID, user_mail.MessageHtml, user_mail.Subject, user_mail.toaddress,user_mail.XEnvelopeTo,user_mail.fromaddress,user_mail.TickleID,user_mail.Status,tickle.custom_subject,tickle.TickleTrainID,tickle.TickleID,tickle.reminder_task,tickle.TickleMailContent,tickle.delete_campaign_on_reply,tickle.notify_when_reply_received,tickle.notify_campaign_deleted FROM `user_mail` INNER JOIN  tickle  ON tickle.TickleTrainID = user_mail.TickleTitleID  LEFT JOIN contact_list ON contact_list.ContactID = user_mail.ContactID WHERE  user_mail.MailID in (select task.MailID from task where task.Status='Y' and task.TickleID =$user_id GROUP BY `MailID`) and (tickle.delete_campaign_on_reply = '1' || tickle.notify_when_reply_received='1') AND (user_mail.Date like '%" . $currentmonh . "%' OR user_mail.Date like '%" . $oldmonth . "%' OR user_mail.Date like '%" . $last_three_month . "%' OR user_mail.Date like '%" . $last_fourth_month . "%') AND tickle.reminder_task = 'N'  order by tickle.TickleID asc";

        $user_mails = $db->query_to_array($qr);
        $date =  getlocaltime(date('Y-m-d H:i:s') ,$user['TimeZone'],"d F Y");
        $date1 =  getlocaltime(date('Y-m-d H:i:s') ,$user['TimeZone'],"d-M-Y");
        
		 $secondaryemail = $user['secondemail'];
	
	
     
	//echo "<pre>";
	//print_r($user); 
     // print_r($user_mails);
	  
       if(!empty($user_mails)){
            
            $inbox_subjects = [];
            $mails = [];
            $email = $user['EmailID'];
            // If User set Google smtp server
			
			if(!empty($secondaryemail)){
			   $email = $user['secondemail'];
			   $secndtickleid = $user['secondtickleid'];				
				if(!empty($user['access_token']) && !empty($user['refresh_token'])) {
               
			    
				 
                 $accessToken = (time() > strtotime($user['secondtoknexp'])) ? update_google_token($user['secondrefreshtoken'],$user_id) : $user['secondauthtoken'];
                //echo $accessToken = $user['secondauthtoken'];
                if($accessToken){ 

                        $imap = new Zend_Mail_Protocol_Imap('imap.googlemail.com', '993', true);
                        $imap->TIMEOUT_CONNECTION = 30;

                        if(oauth2Authenticate($imap, $email, $accessToken)){ echo 'succes';
                            $storage = new Zend_Mail_Storage_Imap($imap);
                            $total_emails = $imap->search(array('SINCE "' . $date1 . '"'));
                           //  echo $email;
                           //  print_r($total_emails);
                            // echo "<br>";
                            foreach ($total_emails as $email_number):

                                $message = $storage->getMessage($email_number);

                                if( (sd_checkmailtype($message->subject)=='1') ) {

                                    $message_headers = $message->getHeaders();
                                    $messageId = $message_headers['message-id'];
                                    $mailTime = date("Y-m-d H:i:s", strtotime($message->date));
                                    $foundPart = null;
                              
                                    $parts = new RecursiveIteratorIterator($message);
                                    foreach ($parts as $part) {
                                        try {
                                            if (strtok($part->contentType, ';') == 'text/html') {
                                                $foundPart = quoted_printable_decode($part);
                                                break;
                                            }
                                        } catch (Zend_Mail_Exception $e) {
                                            // ignore
                                        }
                                    }

                                    if(empty($foundPart)){
                                        $foundPart = quoted_printable_decode($message->getContent());
                                    }

                                    $message_body_text = nl2br($foundPart);

                                    $MessageSubject=trim(str_replace($replaceSubjectValue, $replaceSubjectWithValue, $message->subject));
                                   
                                       
                                    //$max_message_id = $storage->getUniqueId($email_number);

                                    if (!$message->hasFlag(Zend_Mail_Storage::FLAG_SEEN)) {
                                        $flags = $message->getFlags();
                                        unset($flags[Zend_Mail_Storage::FLAG_SEEN]);
                                        $storage->setFlags($email_number, $flags);
                                    }
                                
                                    $MessageFromAddress = filter_email($message->from);

                                    if(!empty($MessageFromAddress)){
                                        $arr = array(
                                                    'messageId' => $messageId,
                                                    'MessageFromAddress' => $MessageFromAddress,
                                                    'subject' => $MessageSubject,
                                                    "MessageWithReply" => trim($message_body_text),
                                                    "mailTime" => $mailTime
                                                    );
                                        $mails[$MessageFromAddress][] =  $arr;
                                    }
                                }
                            endforeach;

                          $imap->logout();

                        }// oauth2Authenticate
                    
                 
                }//$accessToken 
            }
			 else{ // if User send another smtp details
					
                ob_flush();
                $inbox_subjects = [];                
                if (isset($inbox)) { @imap_close($inbox); unset($inbox);  }
                    $decryptpass = decryptIt($user['secondimappass']);
                try
                {
					
					
					//$mbox = imap_open ("{secureus186.sgcpanel.com:993/imap/ssl}INBOX", "shop@speedgraphics.net", "Graphics13!")
					//or die("can't connect: " . imap_last_error());

					//echo 'sssss'."{". $user['secondimaphost'] . ":" . $user['secondimaport'] . "/imap/" . $user['secondimapsecure'] . "}INBOX" . $user['secondimapuser']. $decryptpass;
                    $inbox = @imap_open("{". $user['secondimaphost'] . ":" . $user['secondimaport'] . "/imap/" . $user['secondimapsecure'] . "}INBOX", $user['secondimapuser'], $decryptpass) ;
					
                    if($inbox){ 

                        $total_emails = imap_search($inbox, 'SINCE "' . $date . '"');
										
                        if(!empty($total_emails)){
                            foreach ($total_emails as $email_number):
                                $MessageBody = imap_fetch_overview($inbox, $email_number);
                             
                                if(sd_checkmailtype($MessageBody[0]->subject) == '1' ){
                                    $mailTime = date("Y-m-d H:i:s", strtotime($MessageBody[0]->date));
                                    $headers = imap_fetchheader($inbox, $email_number);
                                  
                                    $messageId = $MessageBody[0]->message_id;

                                    $headers = preg_replace('/X-Original-To:(\s*[0-9]+-){0,1}/i', 'X-Envelope-To:', $headers);
                                    $message_body_raw = $headers . imap_body($inbox, $email_number, FT_PEEK);
                                    $message_body_parsed = TextMsgParse($message_body_raw);                  
                             
                                    if(isset($message_body_parsed['html']) && !empty($message_body_parsed['html']))
                                    {    
                                        $message_body_text = $message_body_parsed['html'];
                                    }
                                    else {
                                        $message_body_text = nl2br($message_body_parsed['text']);
                                    }
                                    
                                    $MessageFromAddress = filter_email($MessageBody[0]->from);
                                    
                                    $MessageSubject = trim(str_replace($replaceSubjectValue, $replaceSubjectWithValue, $MessageBody[0]->subject));
                                 
                                        $arr = array(
                                            'messageId' => $messageId,
                                            'MessageFromAddress' => $MessageFromAddress,
                                            'subject' => $MessageSubject,
                                            "MessageWithReply" => trim($message_body_text),
                                            "mailTime" => $mailTime
                                            );
                                        $mails[$MessageFromAddress][] =  $arr;
                                       
                                }
                            endforeach;
                        }

                    } //$inbox 

                }catch(Exception $e){
                  // catch code here    
				  echo ('Cannot connect: ' . imap_last_error());
                }
            }// else end
			
			}
			
			/*
            if(!empty($user['access_token']) && !empty($user['refresh_token'])) {
               
                $accessToken = (time() > strtotime($user['google_token_expire_at'])) ? update_google_token($user['refresh_token'],$user_id) : $user['access_token'];
                
                if($accessToken){

                        $imap = new Zend_Mail_Protocol_Imap('imap.gmail.com', '993', true);
                        $imap->TIMEOUT_CONNECTION = 30;

                        if(oauth2Authenticate($imap, $email, $accessToken)){
                            $storage = new Zend_Mail_Storage_Imap($imap);
                            $total_emails = $imap->search(array('SINCE "' . $date1 . '"'));
                             echo $email;
                             print_r($total_emails);
                             echo "<br>";
                            foreach ($total_emails as $email_number):

                                $message = $storage->getMessage($email_number);

                                if( (sd_checkmailtype($message->subject)=='1') ) {

                                    $message_headers = $message->getHeaders();
                                    $messageId = $message_headers['message-id'];
                                    $mailTime = date("Y-m-d H:i:s", strtotime($message->date));
                                    $foundPart = null;
                              
                                    $parts = new RecursiveIteratorIterator($message);
                                    foreach ($parts as $part) {
                                        try {
                                            if (strtok($part->contentType, ';') == 'text/html') {
                                                $foundPart = quoted_printable_decode($part);
                                                break;
                                            }
                                        } catch (Zend_Mail_Exception $e) {
                                            // ignore
                                        }
                                    }

                                    if(empty($foundPart)){
                                        $foundPart = quoted_printable_decode($message->getContent());
                                    }

                                    $message_body_text = nl2br($foundPart);

                                    $MessageSubject=trim(str_replace($replaceSubjectValue, $replaceSubjectWithValue, $message->subject));
                                   
                                       
                                    //$max_message_id = $storage->getUniqueId($email_number);

                                    if (!$message->hasFlag(Zend_Mail_Storage::FLAG_SEEN)) {
                                        $flags = $message->getFlags();
                                        unset($flags[Zend_Mail_Storage::FLAG_SEEN]);
                                        $storage->setFlags($email_number, $flags);
                                    }
                                
                                    $MessageFromAddress = filter_email($message->from);

                                    if(!empty($MessageFromAddress)){
                                        $arr = array(
                                                    'messageId' => $messageId,
                                                    'MessageFromAddress' => $MessageFromAddress,
                                                    'subject' => $MessageSubject,
                                                    "MessageWithReply" => trim($message_body_text),
                                                    "mailTime" => $mailTime
                                                    );
                                        $mails[$MessageFromAddress][] =  $arr;
                                    }
                                }
                            endforeach;

                          $imap->logout();

                        }// oauth2Authenticate
                    
                 
                }//$accessToken 
            }
            else{ // if User send another smtp details
					echo 'hererrererere';
                ob_flush();
                $inbox_subjects = [];                
                if (isset($inbox)) { @imap_close($inbox); unset($inbox);  }
                    $decryptpass = decryptIt($user['imap_passowrd']);
                try
                {
                    $inbox = @imap_open("{" . $user['imap_host'] . ":" . $user['imap_port'] . "/imap/" . $user['imap_secure'] . "}INBOX", $user['imap_userame'], $decryptpass) or $inbox = @imap_open("{" . $user['imap_host'] . ":" . $user['imap_port'] . "/imap/" . $user['imap_secure'] . "/novalidate-cert}INBOX", $user['imap_userame'], $decryptpass, OP_HALFOPEN, 1);
                   // echo ('Cannot connect: ' . print_r(imap_errors(), true));

                    if($inbox){

                        $total_emails = imap_search($inbox, 'SINCE "' . $date . '"');

                        if(!empty($total_emails)){
                            foreach ($total_emails as $email_number):
                                $MessageBody = imap_fetch_overview($inbox, $email_number);
                             
                                if(sd_checkmailtype($MessageBody[0]->subject) == '1' ){
                                    $mailTime = date("Y-m-d H:i:s", strtotime($MessageBody[0]->date));
                                    $headers = imap_fetchheader($inbox, $email_number);
                                  
                                    $messageId = $MessageBody[0]->message_id;

                                    $headers = preg_replace('/X-Original-To:(\s*[0-9]+-){0,1}/i', 'X-Envelope-To:', $headers);
                                    $message_body_raw = $headers . imap_body($inbox, $email_number, FT_PEEK);
                                    $message_body_parsed = TextMsgParse($message_body_raw);                  
                             
                                    if(isset($message_body_parsed['html']) && !empty($message_body_parsed['html']))
                                    {    
                                        $message_body_text = $message_body_parsed['html'];
                                    }
                                    else {
                                        $message_body_text = nl2br($message_body_parsed['text']);
                                    }
                                    
                                    $MessageFromAddress = filter_email($MessageBody[0]->from);
                                    
                                    $MessageSubject = trim(str_replace($replaceSubjectValue, $replaceSubjectWithValue, $MessageBody[0]->subject));
                                 
                                        $arr = array(
                                            'messageId' => $messageId,
                                            'MessageFromAddress' => $MessageFromAddress,
                                            'subject' => $MessageSubject,
                                            "MessageWithReply" => trim($message_body_text),
                                            "mailTime" => $mailTime
                                            );
                                        $mails[$MessageFromAddress][] =  $arr;
                                       
                                }
                            endforeach;
                        }

                    } //$inbox 

                }catch(Exception $e){
                  // catch code here    
                }
            }// else end
          
           */
	  
            if(count($mails)){


                foreach ($user_mails as $k => $user_mail) {
                    
                    $Subject =  (!empty($user_mail['custom_subject']))?$user_mail['custom_subject']:$user_mail['Subject'];
                    $Subject =  trim(str_replace($replaceSubjectValue, $replaceSubjectWithValue,$Subject));
                    $toaddress = strtolower($user_mail['toaddress']);

                    if(isset($mails[$toaddress])) {
                 
                        foreach ($mails[$toaddress] as $index_mail) {

                            if($index_mail['subject'] == $Subject ){
                                
                                $previous_time = 0;
                                $q = "select request_time from task_track_records where mail_id='" . $user_mail['MailID'] . "' AND type='reply_receved' ORDER BY id DESC LIMIT 1";
                                $task_result = mysqli_query($db->conn,$q);
                                $is_already_reply_on_task = mysqli_fetch_assoc($task_result);

                                // print_r($is_already_reply_on_task); die;
                         
                                if(count($is_already_reply_on_task)) {
                                    $previous_time = $is_already_reply_on_task['request_time'];
                                }

                                if(already_scaned($index_mail['messageId'])){
                                   
                                    if((strtotime($index_mail['mailTime']) > $previous_time )) {
                                   
                                        $query = "SELECT task.TaskID, task.TaskInitiateDate, task.FollowTickleTrainID,tickle.TickleMailContent, ticklefollow.TickleMailFollowContent FROM `task` LEFT JOIN `ticklefollow` ON  task.FollowTickleTrainID = ticklefollow.FollowTickleTrainID JOIN tickle ON tickle.TickleTrainID = task.TickleTrainID WHERE task.MailID=" . $user_mail['MailID'] . " AND task.status='S' order by TaskID DESC limit 1";

                                        $result = mysqli_query($db->conn,$query);

                                        $MailContent = "";
                                        $replied_on = "";

                                        if(mysqli_num_rows($result) > 0){
                                                $last_sent_task = mysqli_fetch_assoc($result);
                                                $Firstname = str_replace("'", "", $user_mail['FirstName']);
                                                $Lastname = str_replace("'", "", $user_mail['LastName']);

                                                if($user_mail['notify_when_reply_received'] == 1 ){
                                                    if(is_array($next_tickle_to_send = get_next_email_content($user_mail['MailID'],$user_mail['FirstName'],$user_mail['LastName'],$signature))) {
                                                        $MailContent  = $next_tickle_to_send['body'];
                                                        $replied_on  =  $next_tickle_to_send['replied_on'];
                                                    }
                                                }

                                                $timescheduled = date("m/d/y", strtotime($last_sent_task['TaskInitiateDate']));
                                                $TaskID =  $last_sent_task['TaskID'];
                                        }else{
                                                $query = "SELECT task.TaskID, task.TaskInitiateDate, task.FollowTickleTrainID,tickle.TickleMailContent, ticklefollow.TickleMailFollowContent FROM `task` LEFT JOIN `ticklefollow` ON  task.FollowTickleTrainID = ticklefollow.FollowTickleTrainID JOIN tickle ON tickle.TickleTrainID = task.TickleTrainID WHERE task.MailID=" . $user_mail['MailID'] . " AND task.status='Y' order by TaskID ASC limit 1";
                                                $result = mysqli_query($db->conn,$query);
                                                $last_sent_task = mysqli_fetch_assoc($result);
                                                $TaskID =  $last_sent_task['TaskID'];
                                                $MailContent =  $user_mail['MessageHtml'];
                                                $last_sent_task['TaskID'] = 0;
                                                $replied_on ="";
                                        }

                                        mysqli_query($db->conn,"insert into task_track_records (mail_id,task_id,request_time,type,email_content,message_id) values ('".$user_mail['MailID']."','" . $last_sent_task['TaskID'] . "','" .strtotime($index_mail['mailTime']). "', 'reply_receved','".addslashes($index_mail["MessageWithReply"])."','".($index_mail["messageId"])."')");

                                        $protect = protect($user_mail['TickleID'] . "-" . $TaskID);
                                        $TDeleteLink = "http://" . SERVER_NAME . Url_Create("unsubscribe", "act=" . rawurlencode($protect).'&ext=true');
                                        $TDashboardLink = "http://" . SERVER_NAME . Url_Create("home", "act=" . rawurlencode($protect));
                                        $ReceivedTime = date("m/d/Y", strtotime($index_mail['mailTime'])) . " at " . date("h:i a", strtotime($index_mail['mailTime']));

                                        $TDeleteLink = str_replace('new/', '', $TDeleteLink);
                                        $TDashboardLink = str_replace('new/', '', $TDashboardLink);

                                        $template = $email_templete;

                                        $FROM = $index_mail['MessageFromAddress'];
                                        $template =  preg_replace("/\[From\]/i", $FROM, $template);
                                        $template =  preg_replace("/\[Subject\]/i", $index_mail['subject'], $template);
                                        $template =  preg_replace("/\[Received\]/i", $ReceivedTime, $template);
                                        $template =  str_replace("[Reply_message]", $index_mail["MessageWithReply"], $template);
                                   
                                        if ($user_mail['delete_campaign_on_reply'] == 1 || $user['delete_all_campaign'] == 1) {

                                            delete_campaign($user_mail['MailID']);
                                            $deleted_campaign++;
                                            $template =  preg_replace("/\[Replied_ON\]/i", "", $template);
                                            $title = "TickleTrain has detected a reply and deleted your campaign.";
                                            $options= "";
                                        }else{

                                            $title = "TickleTrain has detected a reply to a campaign.";
                                            $options = "<b>Options:</b> <a href='" . $TDeleteLink . "' target='_blank'><B>Delete this campaign</B></a> | <a href='" . $TDashboardLink . "' target='_blank'><B>Manage</B></a>";
                                            $temp ='<tr>
                                                    <td class="innerpadding borderbottom">
                                                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                          <td class="h2">
                                                            '.$replied_on.'
                                                          </td>
                                                        </tr>
                                                        <tr>
                                                          <td class="innerpadding bodycopy">
                                                          '.$MailContent.'
                                                          </td>
                                                        </tr>
                                                      </table>
                                                    </td>
                                                  </tr>';
                                            $template =  str_replace("[Replied_ON]", $temp, $template);
                                        }
										
										//print_r($user_mail); 

                                        if($user_mail['notify_when_reply_received'] == 1 ){
											
											//echo 'track'.$TtSmtpReplyMail.'dddddddddd'.$email; 

                                            $template =  preg_replace("/\[Options\]/i", $options, $template);
                                            $template =  preg_replace("/\[title\]/i", $title, $template); // replace title
                                            $mailBcc->SetFrom($TtSmtpReplyMail, "TickleTrain");
                                            $mailBcc->Subject = "Reply Tracked: " . $index_mail['subject'];                                
                                            $mailBcc->ConfirmReadingTo = "";
                                            $template = str_replace("[TO_ADDRESS]", $email, $template);
                                            $template = str_replace("[TO_ADDRESS]", $email, $template);
                                            $mailBcc->CharSet = "UTF-8";
                                            //echo IMAGE_BASE_FOLDER; exit('');
                                           // $mailBcc->MsgHTML($body2, IMAGE_BASE_FOLDER);
                                            $mailBcc->MsgHTML($template, IMAGE_BASE_FOLDER);
                                            //$mailBcc->SMTPDebug = 2;
                                            $mailBcc->AddAddress($email);
                                            $mailBcc->AddReplyTo($TtSmtpReplyMail);
                                            $mailBcc->Send();
                                            $mailBcc->ClearAllRecipients();
                                            $mailBcc->ClearReplyTos();
                                          
                                            $ttresponse = '<br>Reply Tracked Successfully';
                                            $total_email_sent++;
                                        }

                                    } //$index_mail['mailTime']) > $previous_time

                                } //already_scaned

                            } //if subject

                        } // $mails foreach

                    } // in_array

                } // user_mails foreach
            } //$mails count
               

			   
        }// $user_mails
      
	
	}// while end

    echo "<p><b>Total Emails Sent = ".$total_email_sent."</b></p>";
    echo "<p><b>Deleted Campaign = ".$deleted_campaign."</b></p>";

?>