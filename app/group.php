<?php
$list=mysqli_query($db->conn,"select * from category as cat,contact_list as cont where cat.CategoryID='".$_GET['q']."' and cont.CategoryID='".$_GET['q']."' and cat.TickleID='".$_SESSION['TickleID']."' and cont.TickleID='".$_SESSION['TickleID']."' and cat.CategoryID=cont.CategoryID ORDER BY FirstName ASC");
?>
<h1 class="head">Contact List</h1><form action="<?=Url_Create('category');?>" method="post" name="category"  id="category" class="niceform">
<div style="width:420px; text-align:right;"><a href="index.php?u=addcontact" class="link">Add Contact</a></div>
<table class="table" cellspacing="1px" cellpadding="5px" id="export">
<thead><tr class="head"><!--<td>Sno</td>--><td class="nohead">Group</td><td>First Name</td><td>Last Name</td><td>Email ID</td><td class="nohead">Options</td></tr></thead><tbody>
<?php
$coun=mysqli_num_rows($list);
while($getlist=mysqli_fetch_array($list))
{
$trow=1;
	?>
    <tr class="row<?php echo $trow;?>"><!--<td><b><?php //echo $i;?></b></td>--><td><b><?php echo $getlist['CategoryName'];?></b></td><td><b><?php echo $getlist['FirstName'];?></b></td><td><b><?php echo $getlist['LastName'];?></b></td><td><b><?php echo $getlist['EmailID'];?></b></td><td><a href="?u=addcontact&cid=<?php echo $getlist['ContactID'];?>&action=Edit">Edit</a> || <a href="?u=contactlist&cid=<?php echo $getlist['ContactID'];?>&action=Delete" onclick="javascript:return confirm('Are You Sure want to delete?\nEmail : <?php echo $getlist['EmailID'];?>');">Delete</a></td></tr>
    <?php

$trow++;
if($trow>=3){ 
$trow=1;
}
//print_r($b);	
}?>
</tbody></table></form>
<?php
if($coun>0)
{
?>
<form action="app/getCSV.php" method ="post" > 
<div style="width:430px; text-align:right;"><input type="hidden" name="csv_text" id="csv_text">
<input type="submit" value="Get CSV File" onclick="getCSVData()"></div>
</form>
<?php
}
exit;
?>