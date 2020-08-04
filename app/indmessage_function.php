<?php
$FollowTickleTrainID=$_GET['FollowID'];
$tid=$_GET['tid'];

$TickleFollow=$db->select_to_array('ticklefollow',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='$tid' and FollowTickleTrainID='".$FollowTickleTrainID."'");
$TickleMailFollowContent=$TickleFollow[0]['TickleMailFollowContent'];
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
<link rel="stylesheet" href="js/tiny_mce/themes/advanced/skins/default/content.css" type="text/css"/>


</head>

<body>
<div id="someID"><?php echo RemoveBadChar($TickleMailFollowContent);?></div>
</body>
</html><?php exit;?>