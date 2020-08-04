<?php
ini_set('display_errors', 1);
include_once("includes/data.php");
include_once("includes/function/func.php");
define('HOME_FOLDER', GetHomeDir() . "/");
define('LOGS_FOLDER', HOME_FOLDER . "_logs/");
$mbox = imap_open($ImapServerName, $ImapUserName, $ImapPassWord) or 
        die(imap_errors());
$LastSevenDays = date("j F Y", strtotime('-7 days'));
$emails = imap_search($mbox,"BEFORE \"$LastSevenDays\" SEEN UNDELETED");
if ($emails) {
     foreach($emails as $email){
         imap_delete($mbox, $email) or die(imap_errors());
     }
     imap_expunge($mbox) or die(imap_errors());
 }
 ?>

