<?php
//echo "<pre>";
//print_r($_REQUEST);

if ( isset($_GET['ext']) && $_GET['act'] != "") {
    $act = unprotect(rawurldecode($_GET['act']));
    $action = explode("-", $act);
    $check_login = loginByTickle($action[0]);
    if ($check_login!=0){
        redirect('login');
    }

    $task = $db->select_to_array('task', '', " Where TickleID='" . $action[0] . "' and TaskID ='" . $action[1] . "'");
    //print_r($task); 
    $GLOBALS['mode'] = 6 ;
    if($task){
        $check = tablelist('task', '', array("WHERE TickleID ='" . $task[0]['TickleID'] . "' and MailID='" . $task[0]['MailID'] . "' and Status='Y' OR Status='S' "));
        if($check){
            $GLOBALS['mode']=1;
        }
    }

}



if (!isset($_GET['ext']) &&   $_GET['DeleteAll'] == "" && $_GET['act'] != "" && $_GET['Delete'] == "") {

    $GLOBALS['mode']=1;
    $act = unprotect(rawurldecode($_GET['act']));
    $action = explode("-", $act);
    $check_login = loginByTickle($action[0]);
    if ($check_login!=0){
        redirect('login');
    }
    $check = tablelist('task', '', array("WHERE TickleID ='" . $action[0] . "' and TaskID='" . $action[1] . "' ORDER BY FIND_IN_SET(Status, 'Y,S,D')"));

    $ck_task=@$check[0];
    if (count($check)<1){
        $GLOBALS['mode']=6;
    }elseif ($ck_task['Status'] == "D") {
        $GLOBALS['mode']=4;
    }/*elseif ($ck_task['Status'] == "S") {
        $GLOBALS['mode']=5;
    }*/
}else if( isset($_GET['DeleteAll']) && $_GET['act'] != "" && $_GET['Delete'] == ""){
    parse_str($_SERVER['QUERY_STRING'], $protect_act);
    if ($_GET['act']!=''){
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

    if ($_GET['DeleteAll'] == "Y" && $ck_task['TaskID'] > 0) {
        $Del = $db->select_to_array('task', '', " Where TickleID='" . $action[0] . "' and TaskID ='" . $ck_task['TaskID'] . "'");
        $DelMailid = $Del[0]['MailID'];
        $db->update('task', array('Status' => 'D'), array("WHERE TickleID='" . $action[0] . "' and MailID='$DelMailid' and Status='Y'"));
        $GLOBALS['mode']=2;
		echo '2'; exit;
    }elseif ($_GET['Delete'] == "Y" && $ck_task['TaskID'] > 0 && $ck_task['Status'] == "Y") {
        $db->update('task', array('Status' => 'D'), array("WHERE TaskID = ?", $ck_task['TaskID']));
        $GLOBALS['mode']=3;
    } elseif ($ck_task['Status'] == "D") {
        $GLOBALS['mode']=4;
    } elseif ($ck_task['Status'] == "S") {
        $GLOBALS['mode']=5;
    }
}
?>