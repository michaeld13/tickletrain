<?php

$logDate = date("Y-m-d H:i:s");
$sevenDayBack = date("Y-m-d", strtotime('-25 days', strtotime($logDate)));
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
//echo "SELECT TaskID,MailID,TaskCretedDate FROM task where SentDate <= '".$sevenDayBack."' ";
//echo '<br>';
//echo "SELECT TaskID,MailID,TaskCretedDate,Status,SentDate FROM task where SentDate <='".$sevenDayBack."' and SentDate >='2010-12-31' ";
//die();
$taskQuery = mysqli_query($db->conn,"SELECT TaskID,MailID,TaskCretedDate,Status,SentDate FROM task where SentDate <='".$sevenDayBack."' and SentDate >='2010-12-31' ORDER BY rand() limit 0,100 ");
$filesDir = array();
while($taskQueryData = mysqli_fetch_assoc($taskQuery)){    
    $TaskCretedDate = date('Ymd',  strtotime($taskQueryData['TaskCretedDate']));    
    $filesDir[] = $TaskCretedDate;    
    if($taskQueryData['Status']=='S' && $taskQueryData['SentDate'] <= $sevenDayBack)
    {   
       // echo '<pre>'; print_r($taskQueryData);
        $taskInnerQuery = mysqli_query($db->conn,"SELECT TaskID,MailID FROM task where Status !='S' and MailID ='".$taskQueryData['MailID']."' ");
        $taskInnerData = mysqli_num_rows($taskInnerQuery);        
        if($taskInnerData == '0'){                 
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
