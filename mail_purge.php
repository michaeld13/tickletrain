<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$ImapServerName = "{mail.tickletrain.com:143/imap/notls}INBOX.Read"; // For a IMAP connection    (PORT 143)
$ImapUserName = "ticklein@tickletrain.com";
$ImapPassWord = "o3Lq&93x";
$ImapMove = "INBOX.Read";
$RootFolder = "";

$since = date("D, d M Y", strtotime("-7 days"));
$mbox = imap_open($ImapServerName, $ImapUserName, $ImapPassWord) or die("imap_connection error");
$hdr = imap_check($mbox);
$emails = imap_search($mbox, 'BEFORE "'.$since.' 00:00:00 -0700 (PDT)"', SE_UID);

if ($hdr) {
	//$msgCount = $hdr->Nmsgs;;
   	$msgCount = count($emails);
    // $RecentMessage =  $hdr->Recent; // Added on 17/1/2014 (Actually it was fetching overview of all mails in the mailbox, so its for getting just recent messages)
    // $MessageIndex = $msgCount - $RecentMessage;
}
$MN = $msgCount;
if ($msgCount) {
    $overview = imap_fetch_overview($mbox, "1:$MN", 0); // Modified on 17/1/2014 to get overview of just recent messages
	//print_r($overview); 

    $size = sizeof($overview);
}


for ($i = 0; $i <500; $i++) {
    $val = $overview[$i];
    $msg = $val->msgno;
    $headers = imap_fetchheader($mbox, $msg);
    //print_r($headers);
    imap_delete($mbox, $msg);
    //debug($headers);
    
    }
imap_expunge($mbox);
imap_close($mbox);
