<?php
if (!isset($_REQUEST['Moveupfollow']) && !isset($_REQUEST['MovedownFollow'])) {
    $FollowTickleTrainID = $_REQUEST['FollowTickleId'];
    $TickleFollowNew = $db->select_to_array('ticklefollow', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and FollowTickleTrainID='$FollowTickleTrainID' ORDER by FollowTickleTrainID ASC");
//print_r($TickleFollowNew);
    if (is_array($TickleFollowNew)) {
        $CreatedDate = date("Y-m-d H:i:s");
        $ModifyDate = $CreatedDate;
        foreach ($TickleFollowNew as $K => $V) {
            $FollowTickleTrainID = $V['FollowTickleTrainID'];
            unset($V['FollowTickleTrainID']);
            $V['CreatedDate'] = $CreatedDate;
            $V['ModifyDate'] = $ModifyDate;
            $idx = $db->insert('ticklefollow', $V);
            $db->query("insert into files (FileName, FileContext, FileParentID) select FileName, FileContext, '" . $idx . "' from files where FileContext='ticklefollow' and FileParentID='" . $FollowTickleTrainID . "'");
        }
    }//if count
} elseif (isset($_REQUEST['Moveupfollow']) && !isset($_REQUEST['MovedownFollow'])) {
    $FollowTickleTrainID = $_REQUEST['FollowTickleId'];
    $TickleFollowNew = $db->select_to_array('ticklefollow', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and FollowTickleTrainID<'$FollowTickleTrainID' and TickleTrainID='" . $_REQUEST['tid'] . "' ORDER by FollowTickleTrainID DESC limit 1");
    $HoldArray = $TickleFollowNew[0]['FollowTickleTrainID'];
    $UpdateQuery = mysqli_query($db->conn,"update ticklefollow set FollowTickleTrainID='9999999999' where FollowTickleTrainID='" . $HoldArray . "'") or die(mysqli_error($db->conn) . __LINE__);
    $NewUpdateQuery = mysqli_query($db->conn,"update ticklefollow set FollowTickleTrainID='" . $HoldArray . "' where FollowTickleTrainID='" . $FollowTickleTrainID . "'") or die(mysqli_error($db->conn) . __LINE__);
    $OneMoreUpdateQuery = mysqli_query($db->conn,"update ticklefollow set FollowTickleTrainID='" . $FollowTickleTrainID . "' where FollowTickleTrainID='9999999999'") or die(mysqli_error($db->conn) . __LINE__);
}

 elseif (!isset($_REQUEST['Moveupfollow']) && isset($_REQUEST['MovedownFollow'])) {
    $FollowTickleTrainID = $_REQUEST['FollowTickleId'];
    $TickleFollowNew = $db->select_to_array('ticklefollow', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and FollowTickleTrainID>'$FollowTickleTrainID' and TickleTrainID='" . $_REQUEST['tid'] . "' ORDER by FollowTickleTrainID ASC limit 1");
    $HoldArray = $TickleFollowNew[0]['FollowTickleTrainID'];
    $UpdateQuery = mysqli_query($db->conn,"update ticklefollow set FollowTickleTrainID='9999999999' where FollowTickleTrainID='" . $HoldArray . "'") or die(mysqli_error($db->conn) . __LINE__);
    $NewUpdateQuery = mysqli_query($db->conn,"update ticklefollow set FollowTickleTrainID='" . $HoldArray . "' where FollowTickleTrainID='" . $FollowTickleTrainID . "'") or die(mysqli_error($db->conn) . __LINE__);
    $OneMoreUpdateQuery = mysqli_query($db->conn,"update ticklefollow set FollowTickleTrainID='" . $FollowTickleTrainID . "' where FollowTickleTrainID='9999999999'") or die(mysqli_error($db->conn) . __LINE__);
}

echo "New";
exit;
?>