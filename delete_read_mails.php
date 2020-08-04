<?php
//ignore_user_abort(true); // run script in background until cron completes
ini_set('display_errors', 1);
//error_reporting(E_ALL);
//error_reporting(E_STRICT);
include_once("includes/data.php");
include("includes/function/func.php");
define('ROOT_FOLDER', $RootFolder);
define('HOME_FOLDER', GetHomeDir() . "/");
define('SERVER_NAME', "client.tickletrain.com");


$mbox = imap_open($ImapServerName.'.Read', $ImapUserName, $ImapPassWord) or die('Imap Error in establish conection.');
$hdr = imap_check($mbox);

$size = 0;
$last_week= date('Y-m-d',strtotime("-7 days"));

if (isset($hdr->Nmsgs) && ($hdr->Nmsgs > 0 ) ) {
    $MN = $hdr->Nmsgs;
    $overview = imap_fetch_overview($mbox, "1:$MN", 0); // Modified on 17/1/2014 to get overview of just recent messages

    foreach ($overview as $key => $mail) {
         $mail_date  =  date('Y-m-d',strtotime($mail->date));
        if($mail_date < $last_week){
           $uid = $mail->uid; 
           imap_delete($mbox, $uid, FT_UID); // delete all mails that older then i week
        }else{
           //echo "new= ".$mail_date.'<br>';
        }
    }

    imap_expunge($mbox);
    imap_close($mbox);

    echo "Mails succesfully deleted";
}else{
    echo "No mail found";

}

?>
