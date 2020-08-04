<?php header('Access-Control-Allow-Origin: *'); ?>
<?php
include_once("includes/data.php");
include("includes/function/func.php");
ini_set('display_errors', 1);

if(isset($_POST['action']) && $_POST['action']=='GetTickle')
{	
	if(isset($_POST['order']) && $_POST['order']=='asc')
	{
		$order = 'order by tkl.TickleName asc ';
	}
	else
	{
		$order = 'order by tkl.CreatedDate desc ';
	}	
	$tQuery = mysqli_query($db->conn,"select tkl.TickleTrainID,tkl.TickleName,tkl.CreatedDate,tu.TickleID,tu.UserName,tu.FirstName,tu.LastName from tickle tkl inner join tickleuser tu on tu.TickleID=tkl.TickleID where tkl.Status='Y' ".$order);
	$tickleData = array();
	while($tResult = mysqli_fetch_assoc($tQuery))	
	{
		$tickleData[] = $tResult;
	}
	echo json_encode($tickleData);exit();
}

if(isset($_POST['action']) && $_POST['action']=='GetTickleAjax')
{	
	$user_id = $_POST['user_id'];
	if(isset($_POST['order']) && $_POST['order']=='asc')
	{
		$order = 'order by tkl.TickleName asc ';
	}
	else
	{
		$order = 'order by tkl.CreatedDate desc ';
	}	
	$tQuery = mysqli_query($db->conn,"select tkl.TickleTrainID,tkl.TickleName,tkl.CreatedDate,tu.TickleID,tu.UserName,tu.FirstName,tu.LastName from tickle tkl inner join tickleuser tu on tu.TickleID=tkl.TickleID where tkl.Status='Y' and tu.TickleID='".$user_id."' ".$order);
	$tickleData = array();
	while($tResult = mysqli_fetch_assoc($tQuery))	
	{
		$tickleData[] = $tResult;
	}
	echo json_encode($tickleData);exit();
}

if(isset($_POST['action']) && $_POST['action']=='GetUsers')
{
	$tQuery = mysqli_query($db->conn,"select TickleID,UserName,FirstName,LastName from tickleuser ORDER BY FirstName ASC");
	$tickleData = array();
	while($tResult = mysqli_fetch_assoc($tQuery))	
	{
		$tickleData[] = $tResult;
	}
	echo json_encode($tickleData);exit();
}

if(isset($_POST['action']) && $_POST['action']=='AssignTickle')
{
	
	$TickleName1 = json_decode($_POST['tickle']);
	//$CTickleName = clearstr($TickleName);
	$tickletrainid1 = json_decode($_POST['tickletrainid']);
	$newUsers = json_decode($_POST['users']);	
	$oldUser = json_decode($_POST['tickleid']);
	foreach($newUsers as $newUser){	
	foreach($TickleName1 as $key=>$CTickleName){
	   $tcheck=$db->select_to_array('tickle',''," Where TickleID='".$newUser."' and Status='Y' and CTickleName='$CTickleName'");
	   $gcheck=$db->select_to_array('category',''," Where TickleID='".$newUser."' and CategoryName='$CTickleName'");
		if(count($tcheck)>0&&is_array($tcheck) || count($gcheck)>0&&is_array($gcheck))
		{
		    $result = array('result'=>'error','message'=>'Tickle already exist.');
		    echo json_encode($result);exit();
		}
	}
	
	
	for($i=0;$i<count($TickleName1);$i++){	
	      $tickletrainid = $tickletrainid1[$i];
	      $TickleName = $TickleName1[$i];	
	      $CTickleName1 = clearstr($TickleName);
	      
		$TickleNew=$db->select_to_array('tickle',''," Where TickleID='".$oldUser[$i]."' and Status='Y' and TickleTrainID='$tickletrainid'");
		$GroupNew=$db->select_to_array('category',''," Where TickleID='".$oldUser[$i]."' and CategoryID=".$TickleNew[0]['TickleContact']);
	
		$GroupNew[0]['CategoryName']=$CTickleName1;
		$GroupNew[0]['TickleID']=$newUser;
		unset($GroupNew[0]['CategoryID']);
		$ids = $db->insert('category',$GroupNew[0]);
		//mysqli_query($db->conn,"insert into category_contact_list (CategoryID, ContactID) select $ids, ContactID from category_contact_list where CategoryID=".$TickleNew[0]['TickleContact']);
	
		$CreatedDate=date("Y-m-d H:i:s");
		$ModifyDate=$CreatedDate;
		$TickleNewid=md5($CreatedDate.$newUser.$i);
		$TickleNew[0]['TickleName']=$TickleName;
		$TickleNew[0]['CTickleName']=$CTickleName1;
		$TickleNew[0]['TickleTrainID']=$TickleNewid;
		$TickleNew[0]['CreatedDate']=$CreatedDate;
		$TickleNew[0]['ModifyDate']=$ModifyDate;
		$TickleNew[0]['TickleContact']=$ids;		
		$TickleNew[0]['TickleID']=$newUser;	
		
		
		$ids = $db->insert('tickle',$TickleNew[0]);	
		$db->query("insert into files (FileName, FileContext, FileParentID) select FileName, FileContext, '".$TickleNewid."' from files where FileContext='tickle' and FileParentID='".$tickletrainid."'");
	
		$TickleFollowNew=$db->select_to_array('ticklefollow',''," Where TickleID='".$oldUser[$i]."' and Status='Y' and TickleTrainID='$tickletrainid' ORDER by FollowTickleTrainID ASC");
		//print_r($TickleFollowNew);
		if(is_array($TickleFollowNew))
		{
	
				foreach($TickleFollowNew as $K=>$V)
				{
					$FollowTickleTrainID = $V['FollowTickleTrainID'];
					unset($V['FollowTickleTrainID']);
					$V['TickleTrainID']=$TickleNewid;
					$V['TickleID']=$newUser;					
					$V['CreatedDate']=$CreatedDate;
					$V['ModifyDate']=$ModifyDate;
					$idx = $db->insert('ticklefollow',$V);
					$db->query("insert into files (FileName, FileContext, FileParentID) select FileName, FileContext, '".$idx."' from files where FileContext='ticklefollow' and FileParentID='".$FollowTickleTrainID."'");
				}
		 }//if count
		 $result = array('result'=>'success','message'=>'Tickle assign successfully.');
	  
	}
	}	
	echo json_encode($result);exit();
}

if(isset($_POST['action']) && $_POST['action']=='getTickleLogs')
{
    $logType = $_POST['logtype'];
    $offset = $_POST['offset'];
    $limit = $_POST['limit'];    
    $tickleLogData = array();
    $query = "select tl.*,tu.EmailID,tu.FirstName,tu.LastName from ticklelog tl inner join tickleuser tu on tu.TickleID=tl.TickleID Where tl.type='".$logType."' ";
    if(isset($_POST['search']) && !empty($_POST['search'])){
        $search = addslashes($_POST['search']);
        $query .= "and (tu.EmailID like '%".$search."%' || tl.ttresponse like '%".$search."%' || tl.ttrequest like '%".$search."%') ";
    }    
    $tickleLogData['totalrows'] = mysqli_num_rows(mysqli_query($db->conn,$query));
    $sqlQuery = mysqli_query($db->conn,$query . " ORDER by tl.date DESC LIMIT ".$offset.", ".$limit." ");//,tl.id
    $tickleLogData['data'] = array();
    while($result = mysqli_fetch_assoc($sqlQuery))
    {        
        $tickleLogData['data'][] = $result;
    }
    echo json_encode($tickleLogData);exit();
}
if(isset($_POST['action']) && $_POST['action']=='deleteTickleLogs')
{
    $logType = $_POST['logtype'];
    mysqli_query($db->conn,"delete from ticklelog where type='".$logType."'");
    die('success');
}

?>
