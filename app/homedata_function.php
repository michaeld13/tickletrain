<?php
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	 $JoinTickleID="TickleID";
	 if($_SESSION['page']=="home1")
	 {
	$aColumns = array( 'FirstName','LastName', 'toaddress', 'Subject', 'CTickleName' ,'TaskInitiateDate','Options');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "FirstName";
	
	/* DB table to use */
	$sTable = "user_mail";
	 }
	 	 
	 
	 
	
	
	
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysqli_real_escape_string($db->conn, $_GET['iDisplayStart'] ).", ".
			mysqli_real_escape_string($db->conn, $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysqli_real_escape_string($db->conn, $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			if($_SESSION['page']=="message")
			{
			$sOrder = "ORDER BY MailID desc";
			}
			if($_SESSION['page']=="contactlist")
			{
			$sOrder = "ORDER BY cat.CategoryName desc";
			}
		}
	}
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	 
	 
	$sWhere = "";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($db->conn, $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */

	/*
	 * Output
	 */
	 
	$iFilteredTotal=0;
	$output = array("aaData" => array(),"subdata" => array());

	//tickle
	$task_order=$db->select_to_array('task','DISTINCT MailID, UNIX_TIMESTAMP( TaskInitiateDate )'," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' order by UNIX_TIMESTAMP(TaskInitiateDate) asc");// LIMIT ".$startlim.",".$_GET['iDisplayLength']."

if(is_array($task_order))
{
foreach($task_order as $k_ord=>$rs1_ord)
{
$keys[]=$rs1_ord['MailID'];
}
}
//print_r($keys);
//$tasks=$db->select_to_array('task',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' group by MailID order by TaskInitiateDate asc");

$i=0;
$ix=1;
if(is_array($keys)>0)
{
$tasks=array_unique($keys);
$pagect=0;
$iFilteredTotal=count($tasks);
$pagecte=$_GET['iDisplayStart']+$_GET['iDisplayLength'];
foreach($tasks as $k=>$rs1)
{
if($pagect>=$_GET['iDisplayStart']&&$pagect<$pagecte)
{

	$task=$db->select_to_array('task',''," Where TickleID='".$_SESSION['TickleID']."' and MailID='".$rs1."' and Status='Y' order by TaskInitiateDate asc");
	foreach($task as $k=>$rs)
	{
	$i++;
	$user_mail=$db->select_to_array('user_mail',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and MailID='".$rs['MailID']."'");
	$tickletrainid=$user_mail[0]['TickleTitleID'];
	
	$tickle=$db->select_to_array('tickle',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='".$tickletrainid."'");
	$TickleContact=$tickle[0]['TickleContact'];
	$toaddress=extract_emails_from($user_mail[0]['toaddress']);
	$contact_list=$db->select_to_array('contact_list',''," Where EmailID='".$toaddress[0]."' and TickleID='".$_SESSION['TickleID']."' and Status='Y' and CategoryID='".$TickleContact."'");
	
	$fromaddress=imap_rfc822_parse_adrlist($user_mail[0]['fromaddress'],"");
	$cid=$contact_list[0]['ContactID'];
	
	$EmailAddr=$fromaddress[0]->mailbox."@".$fromaddress[0]->host;
	
	$Personal=explode(" ",$fromaddress[0]->personal);
	
	$TaskCretedDate=convert_date($rs['TaskCretedDate']);
	$TaskInitiateDate=convert_date($rs['TaskInitiateDate']);
	$time=strtotime($rs['TaskInitiateDate']);
	$TickleTime=date("h:i A",$time);
	$TickleDate=date("m-d-Y",$time);
	$FirstName=$contact_list[0]['FirstName'];
	if($FirstName=="") $FirstName='<a href="index.php?u=addcontact&cid='.$cid.'&action=Edit">Add Name</a>';
	$LastName=$contact_list[0]['LastName'];
	if($LastName=="") $LastName='<a href="index.php?u=addcontact&cid='.$cid.'&action=Edit">Add Name</a>';
	$display="";
	$class='sub ClassMin'.$rs['MailID'];
	$Views='';
	
	$USubject=$user_mail[0]['Subject'];
	if($USubject=="")
	$USubject="(no-subject)";
	
	

		
	
	if($TMiD!=$rs['MailID'])
	{
	$TMiD=$rs['MailID'];
	$taskNo=$db->select_value('task','count(*) as c'," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and  MailID='".$TMiD."'");
	$display="";
	$class='Main';
	
	if($taskNo>1)
	{
	$view='<img src="app/img/details_open.png">';
	}
	else
	{
	$view='';
	}
	
		$row = array();
/*		$row[]=$FirstName;
		$row[]=$LastName;
		$row[]=$toaddress[0];
		$row[]=$USubject;
		$row[]=$tickle[0]['CTickleName'];
		$row[]=$TickleTime;
		$row[]=$TickleDate;*/
		
		$row[]=$FirstName.'<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$(".popup").colorbox({width:"50%", height:"50%", iframe:true});
	});
</script>';
		$row[]=$LastName;
		$row[]='<a href="index.php?u=compose&Email='.$toaddress[0].'&TaskID='.$rs['TaskID'].'">'.$toaddress[0].'';
		$row[]='<a href="index.php?u=previewmail&TaskID='.$rs['TaskID'].'&MailID='.$rs['MailID'].'&Mails=Mail&keepThis=true&TB_iframe=true&height=300&width=600" title="Tickle Mail Preview"  onclick="javascript:preview(\''.$rs['TaskID'].','.$rs['MailID'].'\');" class="popup">'.$USubject.'</a>';
		$row[]='<a href="index.php?u=previewmail&TaskID='.$rs['TaskID'].'&MailID='.$rs['MailID'].'&Mails=Tickle&keepThis=true&TB_iframe=true&height=300&width=600" title="Tickle Preview"  onclick="javascript:preview(\''.$rs['TaskID'].','.$rs['MailID'].'\');" class="popup">'.$tickle[0]['CTickleName'].'</a>';
		$row[]='<a href="index.php?u=edittask&TaskID='.$rs['TaskID'].'&MailID='.$rs['MailID'].'&keepThis=true&height=100&width=600" title="Change Send Date" class="popup">'.$TickleTime.'</a>';
		$row[]='<a href="index.php?u=edittask&TaskID='.$rs['TaskID'].'&MailID='.$rs['MailID'].'&keepThis=true&height=100&width=600" title="Change Send Date" class="popup">'.$TickleDate.'</a>';
		$row[]='<a href="javascript:void(0);" onclick="javascript:return DeleteConfirm(\'index.php?u=home&TaskID='.$rs['TaskID'].'&Delete=Y,'.$class.'\');"><img src="app/img/Delete-icon.png" width="17" height="17" border="0"></a>'.$view.'';
	
	if($taskNo>1)
	{
		if ( $_GET['sSearch'] != "" )
			{
		$search=implode(",",$row);
		//preg_match("/(".$_GET['sSearch'].")*/iu", $search,$matches);
		$matches=substr_count($search,$_GET['sSearch']);
		if($matches>0)
		{
		$output['aaData'][] = $row;
		$ix++;
		}
			}else
			{
			$output['aaData'][] = $row;
			$ix++;
			}
	}else
			{
			$output['aaData'][] = $row;
			$ix++;
			}
	
	}
	else
	{
		$row_follow = array();
		$row_follow[]=$FirstName;
		$row_follow[]=$LastName;
		$row_follow[]='<a href="index.php?u=compose&Email='.$toaddress[0].'&TaskID='.$rs['TaskID'].'">'.$toaddress[0].'';
		$row_follow[]='<a href="index.php?u=previewmail&TaskID='.$rs['TaskID'].'&MailID='.$rs['MailID'].'&Mails=Mail&keepThis=true&TB_iframe=true&height=300&width=600" title="Tickle Mail Preview"  onclick="javascript:preview(\''.$rs['TaskID'].','.$rs['MailID'].'\');" class="thickbox">'.$USubject.'</a>';
		$row_follow[]='<a href="index.php?u=previewmail&TaskID='.$rs['TaskID'].'&MailID='.$rs['MailID'].'&Mails=Tickle&keepThis=true&TB_iframe=true&height=300&width=600" title="Tickle Preview"  onclick="javascript:preview(\''.$rs['TaskID'].','.$rs['MailID'].'\');" class="thickbox">'.$tickle[0]['CTickleName'].'</a>';
		$row_follow[]='<a href="index.php?u=edittask&TaskID='.$rs['TaskID'].'&MailID='.$rs['MailID'].'&keepThis=true&height=100&width=600" title="Change Send Date" class="thickbox">'.$TickleTime.'</a>';
		$row_follow[]='<a href="index.php?u=edittask&TaskID='.$rs['TaskID'].'&MailID='.$rs['MailID'].'&keepThis=true&height=100&width=600" title="Change Send Date" class="thickbox">'.$TickleDate.'</a>';
		$row_follow[]='<a href="javascript:void(0);" onclick="javascript:return DeleteConfirm(\'index.php?u=home&TaskID='.$rs['TaskID'].'&Delete=Y,'.$class.'\');">Delete</a>';
	$output['subdata'][] = $row_follow;	
	}
		
		
			
			
			
	//if($ix==3) $ix=1;
	}//foreach
}//ifpage
$pagect++;
	}//foreach
	}//if
	//print_r($output['aaData']);
	//print_r($output['aaData1']);
$iTotal=$iFilteredTotal;
	$output["sEcho"] =  intval($_GET['sEcho']);
	$output["iTotalRecords"] =  $iTotal;
	$output["iTotalDisplayRecords"] = $iFilteredTotal;


		
	
	echo json_encode( $output );
	exit();
	
?>