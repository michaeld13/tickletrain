<?php

$act = $_GET['act'];
$check = tablelist('tickleuser', '', array("WHERE md5(TickleID) ='$act' and Status='N'"));
if (count($check) == 1)
    $ck_login = $check[0];

if ($ck_login['TickleID'] > 0) {
    $db->update('tickleuser', array('Status' => 'Y'), array("WHERE TickleID = ?", $ck_login['TickleID']));
    $postfields['action'] = 'getclientsdetails';
    $postfields['email'] = $ck_login['EmailID'];
    $apiResponse = whmcs_callAPI($postfields);
    if ($apiResponse->result == 'success') {
        $postfields['status'] = 'active';
        $postfields['clientid'] = $apiResponse->userid;
        whmcs_updateClient($postfields);
    }
}
redirect('login', 'activation=1');
?>