<?php
include("class/class-db.php");
# define the connection string
define('DB_CONNECTIONSTRING','mysqlidb://tickletrain_5;localhost;tikletrain5_user;tickle;');
$db = db::getInstance(DB_CONNECTIONSTRING);
# put on the devel mode (3 will print all queries and errors)
$db->beverbose = 1;
mysqli_query($db->conn,"SET NAMES utf8");

$ImapServerName = "{mail.tickletrain.com:143/imap/notls}INBOX"; // For a IMAP connection    (PORT 143)
$ImapUserName = "ticklein@tickletrain.com";
$ImapPassWord = "o3Lq&93x";

//$ImapServerName = "{206.123.73.168:143/imap/notls}INBOX"; // For a IMAP connection    (PORT 143)
//$ImapUserName = "info@harpreetbedi.tk";
//$ImapPassWord = "kXjm&291";

$ImapMove = "INBOX.Read";
$RootFolder = "";


$TtSmtpHost = 'mail.tickletrain.com';//'mail.tickletrain.com';  // Specify main and backup SMTP servers
$TtSmtpAuth = true;                               // Enable SMTP authentication
$TtSmtpUsername = 'ticklein@tickletrain.com';                 // SMTP username
$TtSmtpPassword = 'o3Lq&93x';                           // SMTP password
$TtSmtpSecure = '';                            // Enable TLS encryption, `ssl` also accepted
$TtSmtpPort = 25;  
$TtSmtpReplyMail = 'noreply@tickletrain.com';


?>
