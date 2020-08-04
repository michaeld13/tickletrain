<?php


$pageId=$_GET['page'];
if($pageId<=0) $pageId=1;
$MID=base64_decode($_GET['MID']);
if($MID>0) {
$sqladd=" and MailID ='".$MID."'";
}
list($tasklist,$paging,$total) = $db->select_array_slice('task','*'," where TickleID='".$_SESSION['TickleID']."' and Status='Y' $sqladd  order by TaskInitiateDate asc",$pageId,PAGE_LIMIT);
$group=$db->select_to_array('category',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y'");
//print_r($group);
$category=array();
foreach($group as $k=>$v)
{
$category[$v['CategoryID']]=$v['CategoryName'];
}
?>
<script>
function preview(taskid)
{
var emailids=prompt("Please enter your Email ID","");
$.post("<?=Url_Create('preview')?>", { emailid: emailids,TaskID: taskid} , function(data){
     alert("Tickle Msg: " + data);
   });

}
</script>
<?php /* ?><h1 class="head">Task List</h1><form action="<?=Url_Create('task');?>" method="post" name="tickle"  id="tickle" class="niceform">
<table  class="table" cellspacing="1px" cellpadding="5px">
<tr class="head"><td>Sno</td><td>Mail Subject</td><td>Tickle Name</td><td>Contact Group</td><td>Created on</td><td>Wait Until</td><td>Options</td></tr> <?php */ ?>
<?php
/*$i=0;
$trow=1;
$category[0]="As in Tickle Mail";
if(count($tasklist)>0)
{
	foreach($tasklist as $k=>$rs)
	{
	$i++;
	$check="";
$user_mail=$db->select_to_array('user_mail',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and MailID='".$rs['MailID']."'");
$tickletrainid=$user_mail[0]['TickleTitleID'];
$tickle=$db->select_to_array('tickle',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='".$tickletrainid."'");
$TaskCretedDate=convert_date($rs['TaskCretedDate']);
$TaskInitiateDate=convert_date($rs['TaskInitiateDate']);*/
/** <a href="javascript:void();" onclick="javascript:return preview('<?php echo $rs['TaskID'];?>');">Preview</a> || ***/
	/*?>
	<tr class="row<?php echo $trow;?>"><td><b><?php echo $i;?></b></td><td><b><?php echo $user_mail[0]['Subject'];?></b></td><td><b><?php echo $user_mail[0]['TickleTitle'];?></b></td><td><b><?php echo $category[$tickle[0]['TickleContact']];?></b></td><td><b><?php echo $TaskCretedDate;?></b></td><td><b><?php echo $TaskInitiateDate;?></b></td><td> <a href="?u=<?php echo $_GET['u']; ?>&tid=<?php echo $rs['TaskID'];?>&action=Delete" onclick="javascript:return confirm('Are You Sure want to delete?\nTask on : <?php echo $TaskInitiateDate;?>');">Delete</a></td></tr>
	<?php
	$trow++;
	if($trow>=3){ 
	$trow=1;
	 }
	}
}
if($i>0)
{
echo "<tr><td colspan=7><strong>Page : </strong> ".$paging."</td></tr>";
}else
{
echo "<tr><td colspan=7><strong>No Tasks</strong></td></tr>";
}*/
?>
<!--</table>-->

<?php
$_SESSION['page']=$_GET['u'];
?><div align='left'><h1 class="head">Task List</h1></div><script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				oTable = $('#example').dataTable( {
					"bProcessing": true,
					"bServerSide": true,
					"bJQueryUI": true,
					"aaSorting": [[ 0, "desc" ]],
					"aoColumns": [null,null,null,null,null,{ "bSortable": false }],
					"sPaginationType": "full_numbers",
					"sAjaxSource": "<?=Url_Create('data')?>"
				} );
			} );
		</script>
		<div id="container">
			
			
			<div id="dynamic">
<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
	<thead>
		<tr>
		<th width="10%">Mail Subject</th>
			<th width="15%">Tickle Name</th>
			<th width="15%">Contact Group</th>
			<th width="25%">Created on</th>
			<th width="25%">Wait Until</th>
            <th width="10%">Options</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="6" class="dataTables_empty">Loading data from server</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
		<th>Mail Subject</th>
			<th>Tickle Name</th>
			<th>Contact Group</th>
			<th>Created on</th>
			<th>Wait Until</th>
            <th>Options</th>
		</tr>
	</tfoot>
</table>
			</div>
			<div class="spacer"></div>
			
			
		
		</div>