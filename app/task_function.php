<?php

$action=$_GET['action'];
if($action=="Delete"&&$_GET['tid']!="")
{
$db->delete('task',array("WHERE TaskID ='".$_GET['tid']."'"));
}
?>