
<?php
//--------------------------Set these paramaters--------------------------

// Subject of email sent to you.
$subject = 'Quote Form';

// Your email address. This is where the form information will be sent.
$emailadd = '';

// Where to redirect after form is processed.
$url = 'l';

// Where to direct file upload.
$file= '';

// Makes all fields required. If set to '1' no field can not be empty. If set to '0' any or all fields can be empty.
$req = '0';

// --------------------------Do not edit below this line--------------------------
$text = "Results from form:\
\
";
$space = ' ';
$line = '
';
foreach ($_POST as $key => $value)
{
if ($req == '1')
{
if ($value == '')
{echo "$key is empty";die;}
}
$j = strlen($key);
if ($j >= 20)
{echo "Name of form element $key cannot be longer than 20 characters";die;}
$j = 20 - $j;
for ($i = 1; $i <= $j; $i++)
{$space .= ' ';}
$value = str_replace('\
', "$line", $value);
$conc = "{$key}:$space{$value}$line";
$text .= $conc;
$space = ' ';
}
mail($emailadd, $subject, $text, 'From: '.$emailadd.'');
echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL='.$url.'">';{
}
if ((($_FILES["file"]["type"] == "application/ai")
|| ($_FILES["file"]["type"] == "application/postscript")
|| ($_FILES["file"]["type"] == "application/cdr")
|| ($_FILES["file"]["type"] == "application/dxf")
|| ($_FILES["file"]["type"] == "applicationj/eps")
|| ($_FILES["file"]["type"] == "application/pdf"))
&& ($_FILES["file"]["size"] < 900000))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    echo "Type: " . $_FILES["file"]["type"] . "<br />";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

    if (file_exists("upload/" . $_FILES["file"]["name"]))
      {
      echo $_FILES["file"]["name"] . " already exists. ";
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "upload/" . $_FILES["file"]["name"]);
      echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
      }
    }
  }
else
  {
  echo "Invalid file";
  }

exit;


echo $date1 = '2020-06-10 13:09:00';
echo $nedate = date('N', strtotime($date1));

echo date('Y-m-d', strtotime($date1. ' + 2 days'));
exit;
/*
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require_once("includes/class/PHPMailer/src/Exception.php");
require_once("includes/class/PHPMailer/src/PHPMailer.php");
require_once("includes/class/PHPMailer/src/SMTP.php");

include_once("includes/data.php");
include_once("includes/function/func.php");
ini_set('memory_limit', '-1');
ini_set('display_errors', 1);
error_reporting(E_ALL);
define('HOME_FOLDER', GetHomeDir() . "/");
define('LOGS_FOLDER', HOME_FOLDER . "_logs/");*/
//echo mkdir( GetHomeDir() . "/mail/testingfolder3/"); exit();  


  $RelateiveMailPath = "/mail/testtttt/"  ;

         $Rmailid = '19867265';
		 $root = $_SERVER["DOCUMENT_ROOT"];
            echo $Rawmaildir = $root . $RelateiveMailPath;
echo $dir = $Rawmaildir . $Rmailid;
            //    mkdir($Rawmaildir . $Rmailid . "/", 0777);
               // chmod($Rawmaildir . $Rmailid . "/", 0777, true);
				
				
				if(!file_exists($dir)){ echo 'ddd';
				$old = umask(0000);
    if (!mkdir($dir, 0777, true)) {//0755
	umask($old);
        die('Failed to create folders...');
    }else{
	 echo 'errror';
	}

}
else{
	 echo 'ssss';
}
				

				
exit;

$emoji = "🏾";

$str = str_replace('"', "", json_encode($emoji, JSON_HEX_APOS));

$myInput = $str;

echo 'dddd'.$myHexString = str_replace('\\u', '', $myInput);
echo 'sss'.$myBinString = hex2bin($myHexString);

echo 'aa'.$jj = iconv("UTF-16BE", "UTF-8", $myBinString); 


$text = 'Testtt\r\n\r\n---------- Forwarded message ---------\r\nFrom: 📋 Reminder for me ☺️ \r\nDate: Wed, Mar 25, 2020 at 11:18 AM\r\nSubject: Content Missing1 [Michael Dickman]\r\nTo: \r\n\r\n\r\nReminder Task\r\n*To:* Michael Dickman \r\n*Subject:* Content Missing1 [Michael Dickman]\r\n*Date:* Wed, 25 Mar 2020 01:48:02 -0400(EDT)\r\n*Stage:* 2 of *∞*\r\n*Remind me in:*\r\n1H\r\n\r\n2H\r\n\r\n3H\r\n\r\n1D\r\n\r\n2D\r\n\r\n3D\r\n\r\n1W\r\n\r\n2W\r\n\r\n1M\r\n\r\nMark Complete\r\n\r\nAdd\r\nComments\r\n\r\n\r\n*Reminder about this email.*\r\n\r\n\r\n*From:* Michael Dickman [michael@speedgraphics.net]\r\n*Sent:* Tue, 24 Mar 2020 18:16:43 +0530\r\n*To:*Michael Dickman \r\n*Subject:* Content Missing1\r\n\r\n\r\nTest content 2\r\n-- \r\n\r\nMichael Dickman\r\n\r\n-------------------\r\n\r\nSpeedGraphics\r\n\r\n35 Walden Dr. Unit 101\r\n\r\nArden, NC 28704\r\n\r\n-------------------\r\n\r\nwww.SpeedGraphics.net \r\n\r\nPh. (828) 771-0322\r\n\r\nFx. (828) 771-0323\r\n\r\n\r\n\r\n*Email reminders and follow-ups made easy!*\r\n\r\nTickleTrain.com \r\n\r\n© Copyright 2014 TickleTrain. All Rights Reserved.\r\n\r\n\r\n-- \r\n\r\nMichael Dickman\r\n\r\n-------------------\r\n\r\nSpeedGraphics\r\n\r\n35 Walden Dr. Unit 101\r\n\r\nArden, NC 28704\r\n\r\n-------------------\r\n\r\nwww.SpeedGraphics.net \r\n\r\nPh. (828) 771-0322\r\n\r\nFx. (828) 771-0323\r\n\r\n\r\n\r\n*Email reminders and follow-ups made easy!*\r\n\r\nTickleTrain.com \r\n';
echo '=======================<br/>';
 echo $newetst = remove_emoji($text);


function format($str) {
    $copy = false;
    $len = strlen($str);
    $res = '';

    for ($i = 0; $i < $len; ++$i) {
        $ch = $str[$i];

        if (!$copy) {
            if ($ch != '0') {
                $copy = true;
            }
            // Prevent format("0") from returning ""
            else if (($i + 1) == $len) {
                $res = '0';
            }
        }

        if ($copy) {
            $res .= $ch;
        }
    }

    return 'U+'.strtoupper($res);
}

function convert_emoji($emoji) {
    // ✊🏾 --> 0000270a0001f3fe
    $emoji = mb_convert_encoding($emoji, 'UTF-32', 'UTF-8');
    $hex = bin2hex($emoji);

    // Split the UTF-32 hex representation into chunks
    $hex_len = strlen($hex) / 8;
    $chunks = array();

    for ($i = 0; $i < $hex_len; ++$i) {
        $tmp = substr($hex, $i * 8, 8);

        // Format each chunk
        $chunks[$i] = format($tmp);
    }

    // Convert chunks array back to a string
    return implode($chunks, ' ');
}

function remove_emoji($string) {

    // Match Emoticons
    $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clear_string = preg_replace($regex_emoticons, convert_emoji($regex_emoticons), $string);

    // Match Miscellaneous Symbols and Pictographs
    $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clear_string = preg_replace($regex_symbols, convert_emoji($regex_symbols), $clear_string);

    // Match Transport And Map Symbols
    $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clear_string = preg_replace($regex_transport, convert_emoji($regex_transport), $clear_string);

    // Match Miscellaneous Symbols
    $regex_misc = '/[\x{2600}-\x{26FF}]/u';
    $clear_string = preg_replace($regex_misc, '###', $clear_string);

    // Match Dingbats
    $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
    $clear_string = preg_replace($regex_dingbats, '$$$', $clear_string);

    return $clear_string;
}



echo preg_replace('/rrr/', '/[\x{1F300}-\x{1F5FF}]/u', $newetst);
exit;

function emoji_to_unicode($emoji) {
  // $emoji = mb_convert_encoding($emoji, 'UTF-32', 'UTF-8');
   $unicode = (preg_replace("/^[0]+/","U+",bin2hex($emoji)));
   return $unicode;
}

echo $emj = emoji_to_unicode("📋");//returns U+1F4B5

echo 'ennnnnnnnnnn'.$tct = json_decode('U+1F4CB');
exit; 

    if(isset($_GET['test']))
    {
        echo  getgmdate('2019-12-22 01:00:00', 'America/New_York');
        echo "<br>";
        echo date_default_timezone_get();
        echo "<br>";
        die();
    }

function create_logs($logData,$ttresponse){
    global $db;
    $logDate = date("Y-m-d H:i:s");//gmdate("Y-m-d H:i:s");
    $logDate01 = date("Y-m-d");
    $logDate01 = $logDate01.' 00:00:00';
    if(file_exists(__DIR__ . '/ticklelog/'.date('d-m-Y').'/crontrain.json'))
    {        
        $logJsonData = file_get_contents(__DIR__ . '/ticklelog/'.date('d-m-Y').'/crontrain.json');
        $todayLogData = json_decode($logJsonData,true);    
    }else {
        $todayLogData = array();
    }
    
    $data = array('TickleID'=>$logData['TickleID'],'MailID'=>$logData['MailID'],'ttrequest'=>'','type'=>'createcampaign','date'=>$logDate,'ttresponse'=>$ttresponse);
    
    $todayLogData[] = $data;
    if(!is_dir(__DIR__ . '/ticklelog/'.date('d-m-Y'))){
        mkdir(__DIR__ . '/ticklelog/'.date('d-m-Y'));    
    }
    $fp = fopen(__DIR__ . '/ticklelog/'.date('d-m-Y').'/crontrain.json', 'w');
    fwrite($fp, json_encode($todayLogData));
    fclose($fp);
}


function remove_special_characters_from_string($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
}

error_reporting(E_ERROR);





	   
$logDate = date("Y-m-d H:i:s");
$logDate02 = date("Y-m-d H:i:s", strtotime('-1 hour', strtotime($logDate)));
$threeDayBack = date("Y-m-d", strtotime('-7 days', strtotime($logDate)));
$tomorrowDate = date("Y-m-d", strtotime('+1 days', strtotime($logDate)));
//ignore_user_abort(true); // run script in background until cron completes
ini_set('display_errors', 1);
ini_set('memory_limit', -1);
//error_reporting(E_ALL);
//error_reporting(E_STRICT);
include_once("includes/data.php");
include("includes/function/func.php");
include("includes/class/phpmailer/class.phpmailer.php");
define('ROOT_FOLDER', $RootFolder);
define('HOME_FOLDER', GetHomeDir() . "/");
define('IMAGE_BASE_FOLDER', str_replace(ROOT_FOLDER, "", HOME_FOLDER));
define('FULL_UPLOAD_FOLDER', HOME_FOLDER . "upload-files/");
define('LOGS_FOLDER', HOME_FOLDER . "_logs/");
define('SERVER_NAME', "client.tickletrain.com");
error_reporting(E_ERROR);


//Google Auth and Zend Mailer Files
set_include_path('/var/www/vhosts/client.tickletrain.com/httpdocs/google_auth2/');

// require_once('Zend/Loader/Autoloader.php');						   
require_once 'Zend/Mail/Transport/Smtp.php';
require_once 'Zend/Mail.php';
require_once 'Zend/Mime.php';

//$gfpath01 = str_replace('app','',__DIR__);
//$gfpath = $gfpath01.'/google_auth2/';
require_once 'src/Google_Client.php'; // include the required calss files for google login
require_once 'src/contrib/Google_PlusService.php';
require_once 'src/contrib/Google_Oauth2Service.php';


$result =mysqli_query($db->conn,"SELECT TickleID,TimeZone,TimeDailyTickle,today_report_date from tickleuser where `Status`='Y'");

print_r($result);



//$dom = new DOMDocument('1.0', 'UTF-8');
$strWithEmoji = "😀"; 


echo $sql_in = "INSERT INTO anjmessages (message_id, project_id, sender_id, receiver_id, message, timestamp, is_read)
VALUES ('501','322','77','188', $strWithEmoji ,'1473413606','x');";
       mysqli_query($db->conn,$sql_in);
	   
	   if (mysqli_query($db->conn, $sql_in)) {
               echo "New record created successfully";
            } else {
               echo "Error: " . $sql_in . "" . mysqli_error($db->conn);
            }
	   
	   
	   
	   
?>