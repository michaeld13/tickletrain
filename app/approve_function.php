<?php
if ($_GET['ApproveAll'] == "" && $_GET['act'] != "" && $_GET['Approve'] == "") {
    $GLOBALS['mode']=1;
    $act = unprotect(rawurldecode($_GET['act']));
    $action = explode("-", $act);
    $check_login = loginByTickle($action[0]);
    if ($check_login!=0){
        redirect('login');
    }
    $check = mysqli_query($db->conn,"select task.*, tickle.TApprove from task inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) where task.TickleID ='" . $action[0] . "' and TaskID='" . $action[1] . "' ORDER BY FIND_IN_SET(task.Status, 'Y,S,D')");
    $ck_task=@mysqli_fetch_array($check);
    mysqli_free_result($check);
    if (!$ck_task){
        $GLOBALS['mode']=6;
    }elseif ($ck_task['Status'] == "D") {
        $GLOBALS['mode']=7;
    } elseif ($ck_task['Status'] == "S") {
        $GLOBALS['mode']=5;
    }elseif ($ck_task['TApprove']!="Y"){
        $GLOBALS['mode']=8;
    }
}else{
    parse_str($_SERVER['QUERY_STRING'], $protect_act);
    if ($_GET['act'] != "") {
        $act = unprotect(rawurldecode($_GET['act']));
        $action = explode("-", $act);
    }

    if (count($action) != 2) {
        $act = unprotect(rawurldecode($protect_act['act']));
        $action = explode("-", $act);
    }
    $check = tablelist('task', '', array("WHERE TickleID ='" . $action[0] . "' and TaskID='" . $action[1] . "' ORDER BY FIND_IN_SET(Status, 'Y,S,D')"));
    if (count($check) == 1)
        $ck_task = $check[0];

    if ($_GET['ApproveAll'] == "Y" && $ck_task['TaskID'] > 0) {
        $Del = $db->select_to_array('task', '', " Where TickleID='" . $action[0] . "' and TaskID ='" . $ck_task['TaskID'] . "'");

        $DelMailid = $Del[0]['MailID'];
        $db->update('task', array('Approve' => 'Y'), array("WHERE TickleID='" . $action[0] . "' and MailID='$DelMailid' and Status='Y'"));
        $GLOBALS['mode']=2;
    } elseif ($_GET['Approve'] == "Y" && $ck_task['TaskID'] > 0 && $ck_task['Status'] == "Y") {
        //$db->update('task', array('Approve' => 'Y'), array("WHERE TaskID = ?", $ck_task['TaskID']));
		  $db->update('task', array('Approve' => 'Y'), array("WHERE TaskID = '".$ck_task['TaskID']."'"));
		   $db->update('task', array('Pause' => 'N'), array("WHERE TaskID = '".$ck_task['TaskID']."'"));
       echo $GLOBALS['mode']=3;
	   exit;
       // header("location:".Url_Create("home")."");
    } elseif ($ck_task['Status'] == "D") {
        $GLOBALS['mode']=4;
    } elseif ($ck_task['Status'] == "S") {
        $GLOBALS['mode']=5;
    }
}//mainif
?>