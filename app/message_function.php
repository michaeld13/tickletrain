<?php

$action=$_GET['action'];
$MID=base64_decode($_GET['MID']);
if($action=="Delete"&&$MID!="")
{
$db->delete('user_mail',array("WHERE MailID ='".$MID."'"));
$db->delete('task',array("WHERE MailID ='".$MID."'"));
}
?>