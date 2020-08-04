<?php
if ($_GET['UnPauseAll'] == "" && $_GET['act'] != "" && $_GET['UnPause'] == "") {
    $GLOBALS['mode']=1;
    $act = unprotect(rawurldecode($_GET['act']));
    $action = explode("-", $act);
    $check_login = loginByTickle($action[0]);
    if ($check_login!=0){
        redirect('login');
    }
    $check = tablelist('task', '', array("WHERE TickleID ='" . $action[0] . "' and TaskID='" . $action[1] . "' ORDER BY FIND_IN_SET(Status, 'Y,S,D')"));
    if (count($check)<1){
        $GLOBALS['mode']=6;
    }else{
        $DelMailid = $check[0]['MailID'];
        $Del = $db->select_to_array('task', '', array("WHERE TickleID='" . $action[0] . "' and MailID='$DelMailid' and Status='Y'"));
        if (count($Del)<1){
            $GLOBALS['mode']=6;
        }
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

    if ($_GET['UnPauseAll'] == "Y" && $ck_task['TaskID'] > 0) {
        $Upd = $db->select_to_array('task', '', " Where TickleID='".$action[0]."' and TaskID ='".$ck_task['TaskID']."'");
        $UpdMailid = $Upd[0]['MailID'];
        $db->update('task', array("Pause" => "N"), array("WHERE TickleID='".$action[0]."' and MailID='$UpdMailid'")); //TaskID ='".$_GET['TaskID']."' and
        $db->update('task', array("Approve" => "Y"), array("WHERE TickleID='".$action[0]."' and MailID='$UpdMailid'")); //TaskID ='".$_GET['TaskID']."' and
        $GLOBALS['mode']=3;
		echo '3'; exit;
    } elseif ($_GET['UnPause'] == "Y" && $ck_task['TaskID'] > 0 && $ck_task['Status'] == "Y") {
        $db->update('task', array("Pause" => "N"), array("WHERE TaskID ='".$ck_task['TaskID']."' and  TickleID='".$action[0]."'"));
        $db->update('task', array("Approve" => "Y"), array("WHERE TaskID ='".$ck_task['TaskID']."' and  TickleID='".$action[0]."'"));
       // header("location:".Url_Create("home")."");
        $GLOBALS['mode']=3;
		echo '3'; exit;
    }elseif ($ck_task['Status'] == "D") {
        $GLOBALS['mode']=4;
    } elseif ($ck_task['Status'] == "S") {
        $GLOBALS['mode']=5;
    }
}//mainif
?>
