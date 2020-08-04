<script type='text/javascript' src='/js/jquery-1.7.2.min.js'></script>
<?php
$tickleId = @trim($_SESSION['TickleID']);
$action=$_GET['action'];
$baction = @trim($_REQUEST['baction']);
$cContactID = $_REQUEST['CContactID'];
$baction = @trim($_REQUEST['baction']);


//echo $baction;
//print_r($cContactID);exit;
//bulk actions
if ($baction!='' && is_array($cContactID)){
    switch($baction){
        case 'delete':
            $db->delete('category_contact_list',array("WHERE ContactID in('".implode("','",$cContactID)."')"));
            $db->delete('contact_list',array("WHERE ContactID in ('".implode("','",$cContactID)."')"));
/*            $db->delete('category_contact_list',array("WHERE concat(ContactID,'_',CategoryID) in('".implode("','",$cContactID)."')"));
            $db->delete('contact_list',array("WHERE concat(ContactID,'_0') in('".implode("','",$cContactID)."')"));*/
            break;
    }
}

if($action=="Delete"&&$_GET['cid']>0)
{
    if (checkContactDelete($_GET['cid'])){
        $db->delete('contact_list',array("WHERE ContactID ='".$_GET['cid']."'"));
    }
}
$q = @trim($_GET['q']);
$gid = @trim($_GET['gid']);
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

$sortorders = array("asc","desc");
$sortfields = array("FirstName","LastName","EmailID");

//$fileds = "select cont.ContactID, ccat.CategoryID, tickle.TickleTrainID, TickleName, FirstName, LastName, EmailID, concat(cont.ContactID,'_',ifnull(ccat.CategoryID,0)) as CContactID";
//$mselect = " from contact_list as cont left outer join (category_contact_list ccat inner join tickle on (ccat.CategoryID=tickle.TickleContact)) on (cont.ContactID=ccat.ContactID) WHERE cont.TickleID='$tickleId'";
$fileds = "select cont.ContactID, FirstName, LastName, EmailID, concat(cont.ContactID,'_',ifnull(ccat.CategoryID,0)) as CContactID";
$mselect = " from contact_list as cont inner join category_contact_list ccat on cont.ContactID=ccat.ContactID WHERE cont.TickleID='$tickleId'";

if ($q!=''){
    $mselect.=" and (FirstName like '%$q%' or LastName like '%$q%' or EmailID like '%$q%')";
}
if ($gid!=""){
    $mselect.=" and ifnull(ccat.CategoryID,0)=".@intval($gid);
}

if(isset($_REQUEST['recordperpage'])){
$perpage = $_REQUEST['recordperpage'];
$_SESSION['contact_per_page'] = $_REQUEST['recordperpage'];
?>

	<?php if($perpage=='10') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(1)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='25') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(2)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='50') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(3)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='100') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(4)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($_REQUEST['pg']>1 && $perpage>10){
            redirect('contactlist');
        } ?>

<?php } 
else if(isset($_SESSION['contact_per_page'])){
        $perpage = $_SESSION['contact_per_page'];
	if($perpage=='10') { ?>
	<script>$(document).ready(function(){
	$('#selectrec option:nth-child(1)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='25') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(2)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='50') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(3)').attr('selected', 'selected'); });
	</script>
	<?php } ?>
	<?php if($perpage=='100') { ?><script>$(document).ready(function(){
	$('#selectrec option:nth-child(4)').attr('selected', 'selected'); });
	</script>
	<?php } 
}

else{
	$perpage = 10;
}

$condition = '';
if ($q!=''){
    $condition.=" and (FirstName like '%$q%' or LastName like '%$q%' or EmailID like '%$q%')";
}
if ($gid!=""){
    $condition.=" and ifnull(CategoryID,0)=".@intval($gid);
}

//$cnt = mysqli_num_rows(mysqli_query($db->conn,"select TickleID from contact_list WHERE TickleID='".$tickleId."' ".$condition));
$cnt = mysqli_num_rows(mysqli_query($db->conn,"select EmailID ".$mselect));

//$cnt = mysqli_num_rows(mysqli_query($db->conn,"select cont.TickleID $mselect"));

//$cnt = intval(selectvalue("select count(*)$mselect"));
$pg=max(1,intval($_REQUEST['pg']));
$pc=intval(GetVal($_REQUEST['pc'],$perpage));
$ps = ceil($cnt/$pc);
$check = 10;
$mselect.=" group by cont.ContactID ";
$mselect.=" order by ".$sortfields[$sfld]." ".$sortorders[$sord];
//$mselect.=" limit ".($pg-1)*$pc.", ".$pg*$pc;
$mselect.=" limit ".($pg-1)*$pc.", $perpage ";
// $mselect.=" limit ".($pg-1)*$check.", $perpage ";
//echo $fileds.$mselect;

$list = $db->query_to_array($fileds.$mselect);
$glist = $db->query_to_array("select distinct TickleContact as CategoryID, TickleName as CategoryName from tickle where TickleID=$tickleId order by TickleContact");
$Variables['list'] = $list;
$Variables['glist']= $glist;
$Variables['ps']=$ps;
$Variables['pg']=$pg;
$Variables['pc']=$pc;
$Variables['cnt']=$cnt;
$Variables['search']=($q!='');
$Variables['gid']=$gid;
?>
