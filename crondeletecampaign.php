<?php

$logDate = date("Y-m-d");
//$sevenDayBack = date("Y-m-d", strtotime('-10 days', strtotime($logDate)));
//ignore_user_abort(true); // run script in background until cron completes
ini_set('display_errors', 0);
ini_set('memory_limit', -1);
//error_reporting(E_ALL);
//error_reporting(E_STRICT);
include_once("includes/data.php");
include("includes/function/func.php");
define('ROOT_FOLDER', $RootFolder);
define('HOME_FOLDER', GetHomeDir() . "/");
define('IMAGE_BASE_FOLDER', str_replace(ROOT_FOLDER, "", HOME_FOLDER));
define('FULL_UPLOAD_FOLDER', HOME_FOLDER . "upload-files/");
define('LOGS_FOLDER', HOME_FOLDER . "_logs/");
define('SERVER_NAME', "client.tickletrain.com");
error_reporting(E_ERROR);


function recursiveRemoveDirectory($directory)
{
    chmod($directory, 0777, true);
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file)) { 
            recursiveRemoveDirectory($file);
        } else {
            echo $file.'<br>';
            chmod($file, 0777, true);
            unlink($file);
        }
    }
    rmdir($directory);
}

$mailDir = __DIR__.'/mail/';

//echo "SELECT TaskID,MailID,TaskCretedDate,TaskDeletedDate,Status,SentDate FROM task where Status ='D' ORDER BY TaskID DESC limit 0,100";
$taskQuery = mysqli_query($db->conn,"SELECT TaskID,MailID,TaskCretedDate,TaskDeletedDate,Status,SentDate FROM task where Status ='D' ORDER BY TaskID DESC limit 0,5 ");
$filesDir = array();
while($taskQueryData = mysqli_fetch_assoc($taskQuery)){    
    $TaskCretedDate = date('Ymd',  strtotime($taskQueryData['TaskCretedDate']));    
	echo $tenDayextnd = date("Y-m-d", strtotime('+10 days', strtotime($taskQueryData['TaskDeletedDate'])));
    $filesDir[] = $TaskCretedDate;   
	//echo $logDate = '2020-05-22';
    if($taskQueryData['Status']=='D' && $logDate  > $tenDayextnd)
    {   
       // echo '<pre>'; print_r($taskQueryData);
	  // echo "SELECT TaskID,MailID FROM task where Status ='D' and MailID ='".$taskQueryData['MailID']."'";
	  // echo '<br/>';
        $taskInnerQuery = mysqli_query($db->conn,"SELECT TaskID,MailID FROM task where Status ='D' and MailID ='".$taskQueryData['MailID']."' ");
        $taskInnerData = mysqli_num_rows($taskInnerQuery);        
       if($taskInnerData > '0'){             
            if (is_dir($mailDir.$TaskCretedDate.'/'.$taskQueryData['MailID'])) {
                recursiveRemoveDirectory($mailDir.$TaskCretedDate.'/'.$taskQueryData['MailID']);
            }     
            if (file_exists($mailDir.$TaskCretedDate.'/'.$taskQueryData['MailID'].'.txt')) {
                unlink($mailDir.$TaskCretedDate.'/'.$taskQueryData['MailID'].'.txt');
            }
            mysqli_query($db->conn,"DELETE FROM task where MailID ='".$taskQueryData['MailID']."' ");
            mysqli_query($db->conn,"DELETE FROM user_mail where MailID ='".$taskQueryData['MailID']."' ");
        }    
    }      
}

die('Done');

//echo '<pre>'; print_r($filesDir);
foreach(glob("{$mailDir}*") as $file)
{    
    $dirName = trim(end(explode('/', $file)));
    if(!in_array($dirName, $filesDir))
    {
        echo $file.'<br>';
        recursiveRemoveDirectory($file);
    }
}


?>
