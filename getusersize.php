<?php

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
include("includes/class/phpmailer/class.smtp.php");
define('ROOT_FOLDER', $RootFolder);
define('HOME_FOLDER', GetHomeDir() . "/");
define('IMAGE_BASE_FOLDER', str_replace(ROOT_FOLDER, "", HOME_FOLDER));
define('FULL_UPLOAD_FOLDER', HOME_FOLDER . "upload-files/");
define('LOGS_FOLDER', HOME_FOLDER . "_logs/");
define('SERVER_NAME', "client.tickletrain.com");
//error_reporting(E_ERROR);

$result =mysqli_query($db->conn,"SELECT TickleID,TimeZone,TimeDailyTickle,today_report_date from tickleuser where `Status`='Y'");
while ($row = mysqli_fetch_assoc($result)) {
	
	$userid = $row['TickleID'];
	$resulttask =mysqli_query($db->conn,"SELECT MailID,TaskID from task WHERE TickleID =4658825 ");
	
	while ($rowres = mysqli_fetch_assoc($resulttask)) {
		//echo '<pre>';
		// print_r($rowres);
		 
		 
		 $dir = "/var/www/vhosts/client.tickletrain.com/httpdocs/mail/";

			// Sort in ascending order - this is default
			$a = scandir($dir);
			 foreach($a as $dr){
				 
				if($dr>0){
					  $nedr = "/var/www/vhosts/client.tickletrain.com/httpdocs/mail/".$dr."/";
					$andr = scandir($nedr);
					//	echo '<pre>----';
					//print_r($andr);
					
				/*	foreach($andr as $finldr){
						
					$mailid = $rowres['MailID'];
					echo  $dir1 = "/var/www/vhosts/client.tickletrain.com/httpdocs/mail/".$finldr."/".$mailid."/";
						// if ( is_dir( $dir1 ) ) {
							// echo 'exist'.$dir1;
						// }
					}  */
				}
				
			}
 
		 
		
	}
}




