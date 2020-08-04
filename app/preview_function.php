<?php
use PHPMailer\PHPMailer\PHPMailer;
define('ROOT_FOLDER', "new/");
define('HOME_FOLDER', GetHomeDir() . "/");
define('IMAGE_BASE_FOLDER', str_replace(ROOT_FOLDER, "", HOME_FOLDER));

$emailid = $_POST['emailid'];
$TaskID = $_POST['TaskID'];
$data = $_POST['data'];
$DataPost = array();
$Tickletid = $_POST['Tickletid'];
$tickle = array();
$tickle = $db->select_to_array('tickle', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and TickleTrainID='$Tickletid'");
$DataPost = $tickle[0];
$TickleFollow = $db->select_to_array('ticklefollow', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and TickleTrainID='$Tickletid'");

$Priority = array('1' => "1 (High)", '3' => "3 (Normal)", '5' => "5 (Low)");
$TickleS[0]['TickleMailContent'] = $DataPost['TickleMailContent'];
$TickleS[0]['AttachOriginalMessage'] = $DataPost['AttachOriginalMessage'];
$TickleS[0]['EmailPriority'] = $DataPost['EmailPriority'];
$TickleS[0]['CCMe'] = $DataPost['CCMe'];
$TickleS[0]['DailyDays'] = $DataPost['DailyDays'];
$TickleS[0]['EndAfter'] = $DataPost['EndAfter'];
$TickleS[0]['TickleTime'] = $DataPost['TickleTime'];

$grouplist = $DataPost['TickleContact'];
$TickleTrainID = $DataPost['TickleTrainID'];
$TickleName = $DataPost['TickleName'];

$afiles = tablelist("files", '', " where FileContext='tickle' and FileParentID='$Tickletid'");
$TAttach = array();
for ($i = 0; $i < count($afiles); $i++) {
    $fname = @trim($afiles[$i]['FileName']);
    if ($fname != "" && file_exists(FULL_UPLOAD_FOLDER . $fname)) {
        $TAttach[] = FULL_UPLOAD_FOLDER . $fname;
    }
}
$TickleS[0]['Attach'] = $TAttach;

$CountRow = count($TickleFollow) - 1;
$imx = 1;
for ($ix = 0; $ix <= $CountRow; $ix++) {
    if ($TickleFollow[$ix]['TickleMailFollowContent'] != "") {
        $TickleS[$imx]['TickleMailContent'] = $TickleFollow[$ix]['TickleMailFollowContent'];
        $TickleS[$imx]['AttachOriginalMessage'] = $TickleFollow[$ix]['AttachMessageFollow'];
        $TickleS[$imx]['EmailPriority'] = $TickleFollow[$ix]['EmailPriorityFollow'];
        $TickleS[$imx]['CCMe'] = $TickleFollow[$ix]['CCMeFollow'];
        $TickleS[$imx]['DailyDays'] = $TickleFollow[$ix]['DailyDaysFollow'];
        $TickleS[$imx]['EndAfter'] = $TickleFollow[$ix]['EndAfterFollow'];
        $TickleS[$imx]['TickleTime'] = $TickleFollow[$ix]['TickleTimeFollow'];
        $FollowTickleTrainID = $TickleFollow[$ix]['FollowTickleTrainID'];

        $afiles = tablelist("files", '', " where FileContext='ticklefollow' and FileParentID='$FollowTickleTrainID'");
        $TAttach = array();

        for ($i = 0; $i < count($afiles); $i++) {
            $fname = @trim($afiles[0]['FileName']);
            if ($fname != "" && file_exists(FULL_UPLOAD_FOLDER . $fname)) {
                $TAttach[] = FULL_UPLOAD_FOLDER . $fname;
            }
        }
        $TickleS[$imx]['Attach'] = $TAttach;

        $imx++;
    }
}

if (count($tickle) > 0) {
    if (!isValidEmail($emailid)) {
        echo "Please enter valid email id";
        exit();
    }
    $mail = new PHPMailer(true); //New instance, with exceptions enabled

    /* 	$mail->IsSMTP();                           // tell the class to use SMTP
      $mail->SMTPAuth   = true;                  // enable SMTP authentication
      $mail->SMTPKeepAlive = true;                  // SMTP connection will not close after each email sent
      $mail->Port       = 25;                    // set the SMTP server port
      $mail->Host       = "mail.tickletrain.com"; // SMTP server
      $mail->Username   = "ticklein@tickletrain.com";     // SMTP server username
      $mail->Password   = "change88";            // SMTP server password

      $mail->IsSendmail();  // tell the class to use Sendmail
     */
    if ($emailid != "") {

        foreach ($TickleS as $TKEy => $TKVal) {
		
            $TickleID = $_SESSION['TickleID'];
            $TickleTrainID = $TickleTrainID;
            $sql_user = mysqli_query($db->conn,"select tu.* , tu.Plan,tu.blueplanbarning, gat.access_token ,gat.refresh_token from tickleuser tu left join google_auth_tokens gat on gat.userid = tu.TickleID where tu.TickleID='$TickleID' and tu.Status='Y'");
            $rs_user = mysqli_fetch_assoc($sql_user);
			//echo '<pre>'; print_r($rs_user); exit();
			if(intval($rs_user['DMUse']) && $rs_user['refresh_token']!='')
			{
				ini_set('display_errors', 1);                                
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
				
					
				$client->refreshToken($rs_user['refresh_token']);
				$getGoogleToken = $client->getAccessToken();
				$getGoogleToken02 = json_decode($getGoogleToken,true);
				mysqli_query($db->conn,"update google_auth_tokens set access_token='" . $getGoogleToken02['access_token'] . "' , expires_in='" . $getGoogleToken02['expires_in'] . "' , created='" . $getGoogleToken02['created'] . "' where userid='".$row['TickleID']."' ");
				$accessToken = $getGoogleToken02['access_token'];
				$email = $rs_user['EmailID'];					
				$token = $accessToken;
				$smtpInitClientRequestEncoded = base64_encode("user=$email\1auth=Bearer $token\1\1");
				$config = array('ssl' => 'ssl',
				  'port' => '465',
				  'auth' => 'xoauth',
				  'xoauth_request' => $smtpInitClientRequestEncoded);			
				$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com',$config); 
				Zend_Mail::setDefaultTransport($transport); 
				$mail = new Zend_Mail();
				
		
				$UserSign = @trim($rs_user["signature"]);	
				$FromEmailid = $rs_user['UserName'] . '@tickletrain.com'; //$rs_user['EmailID'];	
				$FromEmailid = $rs_user['EmailID'];
				$FromFirstName = $rs_user['FirstName'];
				$FromLastName = $rs_user['LastName'];
	
				$TickleMailContent = $TKVal['TickleMailContent'];
				$EmailPriority = $TKVal['EmailPriority'];
				if ($EmailPriority <= 0) {
					$EmailPriority = 3;
				}	
				$EmailPriority = $Priority[$EmailPriority];
				$AttachOriginalMessage = $TKVal['AttachOriginalMessage'];
				$TAttach = $TKVal['Attach'];
				if (count($TAttach) != 0) {
					for ($f = 0; $f < count($TAttach); $f++) {
						//$mail->AddAttachment($TAttach[$f], basename($TAttach[$f]));
						$file = $TAttach[$f];						
						$at = new Zend_Mime_Part(file_get_contents($file));
						//$at->type        = 'image/png';
						$at->disposition = Zend_Mime::DISPOSITION_INLINE;
						$at->encoding    = Zend_Mime::ENCODING_BASE64;
						$at->filename    = basename($file);						
						/*echo file_get_contents($file);						
						$at = new Zend_Mime_Part(file_get_contents($file));
						$at->filename = basename($file);
						$at->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
						$at->encoding = Zend_Mime::ENCODING_8BIT;*/
						$mail->addAttachment($at);
					}
				}

				//
				//echo $TickleMailContent; exit();
				
				//$Subject = "Tickle Preview :" . $TickleName;
				$Subject = "Preview of \"".$TickleName."\" Tickle, Stage: ".($TKEy+1)." of ".count($TickleS);

				$TextMsg = strip_tags(str_ireplace(array("<br />", "<br>", "&nbsp;"), array("\n", "\n", " "), $TickleMailContent));
				$TextMsg = RemoveBadChar($TextMsg);	
				$HTMLContent = RemoveBadChar($TickleMailContent);
				
				$HTMLContent = str_replace('[signature]', $UserSign, $HTMLContent);
				$HTMLContent = str_replace('[FirstName]', $FromFirstName, $HTMLContent);
				$HTMLContent = str_replace('[LastName]', $FromLastName, $HTMLContent);
				$HTMLContent = str_replace('[firstname]', $FromFirstName, $HTMLContent);
				$HTMLContent = str_replace('[lastname]', $FromLastName, $HTMLContent);
							
				$HTMLContent = str_replace('/upload-files/','http://client.tickletrain.com/upload-files/',$HTMLContent);

				if ($rs_user['Plan'] == 1) {
		            $HTMLContent.= "<br/><div style=''> <a href='https://tickletrain.com/'>TickleTrain</a> - your free email assistant.</div>";
		        } elseif ($rs_user['Plan'] != "1" && $rs_user['blueplanbarning'] == 1) {
		            $HTMLContent.= "<br/><div style='color:green;'>-- Email Follow-up Made Easy! --<br/>&nbsp;&nbsp;&nbsp;&nbsp;it's free at <a href='https://tickletrain.com/'>TickleTrain</a></div>";
		        }

				///echo $HTMLContent.'<br /><br />';
				$mail->setBodyHtml($HTMLContent);
				$mail->setFrom($FromEmailid, @trim($FromFirstName . ' ' . $FromLastName));
				$mail->addTo($emailid);
				$mail->setSubject($Subject);
				//$mail->AddAddress($_POST['dmtoemail']);
				//$res = $mail->send($transport);
				//if($mail->send($transport)){
				  //echo "true";
				//}				
				
				 try{
					$mail->send($transport);
					$SendMail=1;
				 }catch(Exception $e){				 	
				  	//$errorSend = $e->getMessage();
				  	$SendMail=0;
				 } 
				
			}
			else
			{			
				if (!empty($rs_user['DMPwd'])) {
					$rs_user['DMPwd'] = @trim(decryptIt($rs_user['DMPwd']));
				}
				prepareMailer($rs_user, $mail);				
				$UserSign = @trim($rs_user["signature"]);
	
				$FromEmailid = $rs_user['UserName'] . '@tickletrain.com'; //$rs_user['EmailID'];
	
				$FromEmailid = $rs_user['EmailID'];
				$FromFirstName = $rs_user['FirstName'];
				$FromLastName = $rs_user['LastName'];
				$UserName = $rs_user['UserName'];
				$tickle_reply = $FromEmailid;
				$TickleContact = $grouplist;
	
				$TickleMailContent = $TKVal['TickleMailContent'];
				$EmailPriority = $TKVal['EmailPriority'];
				if ($EmailPriority <= 0) {
					$EmailPriority = 3;
				}
	
				$EmailPriority = $Priority[$EmailPriority];
				$AttachOriginalMessage = $TKVal['AttachOriginalMessage'];
				$TAttach = $TKVal['Attach'];
				if (count($TAttach) != 0) {
					for ($f = 0; $f < count($TAttach); $f++) {
						$mail->AddAttachment($TAttach[$f], basename($TAttach[$f]));
					}
				}
	
				//$Subject = $TickleName;
				$TextMsg = strip_tags(str_ireplace(array("<br />", "<br>", "&nbsp;"), array("\n", "\n", " "), $TickleMailContent));
				$TextMsg = RemoveBadChar($TextMsg);
	
				$HTMLContent = RemoveBadChar($TickleMailContent);
	
				$mail->AddReplyTo("noreply@tickletrain.com");
	
				$mail->SetFrom($FromEmailid, "$FromFirstName $FromLastName");
				// $mail->SetFrom("noreply@tickletrain.com", "TickleTrain");
	
				$UMessage = "";
				if ($AttachOriginalMessage == "Y") {
					$UMessage = "[Original Message Will Display Here]";
				}
				if ($AttachOriginalMessage == "A") {
					$UMessage = "[Original email message will display here and include original email attachments]";
				}
	
				if ($UMessage != "") {
					$HTMLContent.="<br/><br/>" . $UMessage;
				}
                $HTMLContent = str_replace('/upload-files/','http://client.tickletrain.com/upload-files/',$HTMLContent);
				$body = $HTMLContent;
				$body = preg_replace('/\[signature\]/i', $UserSign, $body);
				$TextMsg = preg_replace('/\[signature\]/i', $UserSign, $TextMsg);


				if ($rs_user['Plan'] == 1) {
		            $body.= "<br/><div style=''> <a href='https://tickletrain.com/'>TickleTrain</a> - your free email assistant.</div>";
		        } elseif ($rs_user['Plan'] != "1" && $rs_user['blueplanbarning'] == 1) {
		            $body.="<br/><div style='color:green;'>-- Email Follow-up Made Easy! --<br/>&nbsp;&nbsp;&nbsp;&nbsp;it's free at <a href='https://tickletrain.com/'>TickleTrain</a></div>";
		        }

	
				//$mail->Subject = "Tickle Preview :" . $Subject;
				$mail->Subject = "Preview of \"".$TickleName."\" Tickle, Stage: ".($TKEy+1)." of ".count($TickleS);

				$mail->AltBody = $TextMsg;
				$mail->WordWrap = 80; // set word wrap
				$mail->Priority = $EmailPriority;
				$mail->MsgHTML($body, IMAGE_BASE_FOLDER);
				$mail->IsHTML(true); // send as HTML
				$to_address = "";
				$mail->AddAddress($emailid);
				$errorSend = "";
				$SendMail = 1;
				//try{
				$mail->Send();
				/* }catch(Exception $e){
				  $errorSend = $e->getMessage();
				  $SendMail=0;
				  } */
				// Clear all addresses and attachments for next loop
				$mail->ClearAddresses();
				$mail->ClearAttachments();
				$mail->ClearReplyTos();
				$mail->ClearAllRecipients();
			}
			
			
			
					
			
        }//foreach array
        if ($SendMail == 1) {
            echo "Your Tickle email(s) have been sent to: " . $_POST['emailid'] . "\n";
        } else {
            echo "Error while sending mail to : " . $_POST['emailid'] . " (" . $errorSend . ")\n";
        }
    } else {
        echo "Please Enter Mail Id";
    }
}//if task

function isValidEmail($email) {
    return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email);
}

exit();
?>
