<script type='text/javascript' src='/js/jquery-1.7.2.min.js'></script>
<?php
$tickleId = @trim($_SESSION['TickleID']);
$action = @trim($_GET['action']);
$cTickleID = $_REQUEST['TickleID'];
$baction = @trim($_REQUEST['baction']);
//print_r($action);exit;
//bulk actions
if ($baction!='' && is_array($cTickleID)){
    switch($baction){
        case 'delete':			
            $arr = array();
            foreach($cTickleID as $tId){
                if (checkTickleDelete($tId)) {
                    $arr[]=$tId;
                }
            }
            if (count($arr)){
                $db->delete('tickle', array("WHERE TickleTrainID in('".implode("','",$arr)."') and TickleID='" . $_SESSION['TickleID'] . "'"));
                $db->delete('task', array("WHERE TickleTrainID in('".implode("','",$arr)."') and TickleID='" . $_SESSION['TickleID'] . "'"));
                $db->delete('ticklefollow', array("WHERE TickleTrainID in('".implode("','",$arr)."') and TickleID='" . $_SESSION['TickleID'] . "'"));
            }
            break;
    }
}

$isAjax = @intval($_GET['ajax']);
if ($action == "Delete" && $_GET['tid'] != "") {
    if (checkTickleDelete($_GET['tid'])) {
        $db->delete('tickle', array("WHERE TickleTrainID ='" . $_GET['tid'] . "' and TickleID='" . $_SESSION['TickleID'] . "'"));
        $db->delete('task', array("WHERE TickleTrainID ='" . $_GET['tid'] . "' and TickleID='" . $_SESSION['TickleID'] . "'"));
        $db->delete('ticklefollow', array("WHERE TickleTrainID ='" . $_GET['tid'] . "' and TickleID='" . $_SESSION['TickleID'] . "'"));
    }
    if ($isAjax){
        return;
    }
	if(!isset($_GET['ajax']) && isset($_GET['redirectUrl']))
	{
		$surl = '?';
		foreach(json_decode(base64_decode($_GET['redirectUrl'])) as $key => $redirectUrl01)
		{
			if($key!='u'){ $surl .= $key.'='.$redirectUrl01.'&';} 
		}
		header("location:https://client.tickletrain.com/tickle/".substr($surl,0,-1));
	}
}
if ($action == "DeleteFollow" && $_GET['ftid'] != "") {
    if (checkTickleFollowDelete($_GET['ftid'])) {
        $db->delete('task', array("WHERE FollowTickleTrainID ='" . $_GET['ftid'] . "' and TickleID='" . $_SESSION['TickleID'] . "'"));
        $db->delete('ticklefollow', array("WHERE FollowTickleTrainID ='" . $_GET['ftid'] . "' and TickleID='" . $_SESSION['TickleID'] . "'"));
    }
    if ($isAjax){
        return;
    }
	
	if(!isset($_GET['ajax']) && isset($_GET['redirectUrl']))
	{
		$surl = '?';
		foreach(json_decode(base64_decode($_GET['redirectUrl'])) as $key => $redirectUrl01)
		{
			if($key!='u'){ $surl .= $key.'='.$redirectUrl01.'&';} 
		}
		header("location:https://client.tickletrain.com/tickle/".substr($surl,0,-1));
	}
}

$q = @trim($_GET['q']);
//sorting
$sort = @trim($_REQUEST['sort']);
$sfld = 1;
$sord = 1;

if ($sort!=""){
    list($sfld,$sord)=explode("-",$sort,2);
    if (!@intval($sfld)){
        $sfld=1;
    }else{
        $sfld = @intval($sfld);
    }
    if (!@intval($sord)){
        $sord=1;
    }else{
        $sord = @intval($sord);
    }
}
$Variables['sfld']=$sfld;
$Variables['sord'] = $sord;
$sfld--;
$sord--;

if(isset($_REQUEST['recordperpage'])){

	$perpage = $_REQUEST['recordperpage'];
	$_SESSION['tickle_per_page'] = $_REQUEST['recordperpage'];
	if($_REQUEST['pg']>1 && $perpage>10){
            redirect('tickle');
    }
}

$perpage =  isset($_SESSION['tickle_per_page'])? $_SESSION['tickle_per_page'] : 10 ;

$sortorders = array("asc","desc");
$sortfields = array("TickleName","BccName","Schedule","Schedule");

$fileds = "select tickle.*, concat(CTickleName,'+',UserName,'@tickletrain.com') as BccName, concat(DailyDays,' days, Send ',EndAfter,' times') as Schedule, category.CategoryName, count(files.FileID) as attaches";
$mselect = " from tickle inner join category on (TickleContact=CategoryID and tickle.TickleID=category.TickleID) inner join tickleuser on (tickle.TickleID=tickleuser.TickleID) left outer join files on (tickle.TickleTrainID=files.FileParentID and files.FileContext='tickle') where tickle.TickleID=$tickleId";
if ($q!=''){
    $mselect.=" and (TickleName like '%$q%' or TickleMailContent like '%$q%')";
}
$mselect.=" group by tickle.TickleTrainID";

$cnt = mysqli_num_rows(mysqli_query($db->conn, "select count(*) $mselect"));//intval(selectvalue("select count(*)$mselect"));
$pg=max(1,intval($_REQUEST['pg']));
$pc=intval(GetVal($_REQUEST['pc'],$perpage));
$ps = ceil($cnt/$pc);

$mselect.=" order by ".$sortfields[$sfld]." ".$sortorders[$sord];
$check = 10;
$mselect.=" limit ".($pg-1)*$pc.", ".$pc;
//$mselect.=" limit ".($pg-1)*$check.", ".$pc;

$list = $db->query_to_array($fileds.$mselect);	
// $tids = array();
// foreach($list as $row){
//     $tids[]=$row['TickleTrainID'];
// }
// $mselect = "select ticklefollow.*, concat(ticklefollow.DailyDaysFollow,' days, Send ',ticklefollow.EndAfterFollow,' times') as Schedule, count(files.FileID) as attaches from ticklefollow left outer join files on (ticklefollow.FollowTickleTrainID=files.FileParentID and files.FileContext='ticklefollow') where ticklefollow.TickleID=$tickleId and  ticklefollow.TickleTrainID in ('".join("','",$tids)."') group by ticklefollow.FollowTickleTrainID order by ticklefollow.TickleTrainID, ticklefollow.FollowTickleTrainID";
// $follows = $db->query_to_array($mselect);
// $flist=array();

// foreach($follows as $row){
//     if (!isset($flist[$row['TickleTrainID']])){
//         $flist[$row['TickleTrainID']]=array();
//     }
//     $flist[$row['TickleTrainID']][]=$row;
// }
$Variables['perpage'] = $perpage;
$Variables['list'] = $list;
$Variables['flist'] =  []; //$flist;
$Variables['ps']=$ps;
$Variables['pg']=$pg;
$Variables['pc']=$pc;
$Variables['cnt']=$cnt;
$Variables['search']=($q!='');

//echo "<pre>";
//print_r($Variables);
//echo "</pre>";
//die();

?>
