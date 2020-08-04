<?php
$TickleName=trim($_REQUEST['TickleName']);
$CTickleName=clearstr($TickleName);
$Tickletid=$_REQUEST['Tickletid'];

$tcheck=$db->select_to_array('tickle',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and CTickleName='$CTickleName'");
$gcheck=$db->select_to_array('category',''," Where TickleID='".$_SESSION['TickleID']."' and CategoryName='$CTickleName'");
if(count($tcheck)>0&&is_array($tcheck) || count($gcheck)>0&&is_array($gcheck))
{
    echo "Exist";
}else{
    echo "New";
    $TickleNew=$db->select_to_array('tickle',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='$Tickletid'");
	$GroupNew=$db->select_to_array('category',''," Where TickleID='".$_SESSION['TickleID']."' and CategoryID=".$TickleNew[0]['TickleContact']);

	$GroupNew[0]['CategoryName']=$CTickleName;
	unset($GroupNew[0]['CategoryID']);
	$ids = $db->insert('category',$GroupNew[0]);    

    $CreatedDate=date("Y-m-d H:i:s");
    $ModifyDate=$CreatedDate;
    $TickleNewid=md5($CreatedDate.$_SESSION['TickleID']);
    $TickleNew[0]['TickleName']=$TickleName;
    $TickleNew[0]['CTickleName']=$CTickleName;
    $TickleNew[0]['TickleTrainID']=$TickleNewid;
    $TickleNew[0]['CreatedDate']=$CreatedDate;
    $TickleNew[0]['ModifyDate']=$ModifyDate;
	$TickleNew[0]['TickleContact']=$ids;

    $ids = $db->insert('tickle',$TickleNew[0]);

    $db->query("insert into files (FileName, FileContext, FileParentID) select FileName, FileContext, '".$TickleNewid."' from files where FileContext='tickle' and FileParentID='".$Tickletid."'");

    $TickleFollowNew=$db->select_to_array('ticklefollow',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='$Tickletid' ORDER by FollowTickleTrainID ASC");
    //print_r($TickleFollowNew);
    if(is_array($TickleFollowNew))
    {

            foreach($TickleFollowNew as $K=>$V)
            {
                $FollowTickleTrainID = $V['FollowTickleTrainID'];
                unset($V['FollowTickleTrainID']);
                $V['TickleTrainID']=$TickleNewid;
                $V['CreatedDate']=$CreatedDate;
                $V['ModifyDate']=$ModifyDate;
                $idx = $db->insert('ticklefollow',$V);
                $db->query("insert into files (FileName, FileContext, FileParentID) select FileName, FileContext, '".$idx."' from files where FileContext='ticklefollow' and FileParentID='".$FollowTickleTrainID."'");            }
    }//if count
}
exit;
?>