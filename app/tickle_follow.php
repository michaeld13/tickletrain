<?php
session_start();
include("includes/includes.php");

$tickleId = @trim($_SESSION['TickleID']);
$TickleTrainID = @trim($_GET['TickleTrainID']);
$q = @trim($_GET['q']);
//sorting
$sort = @trim($_REQUEST['sort']);
$sfld = 1;
$sord = 0;

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
$sortfields = array("TickleTimeFollow","Schedule");

$fileds = "select ticklefollow.*, concat(tickle.CTickleName,'+',UserName,'@tickletrain.com') as BccName, concat(ticklefollow.DailyDaysFollow,' days, Repeat ',ticklefollow.EndAfterFollow,' times') as Schedule, category.CategoryName, count(files.FileID) as attaches";
$mselect = " from ticklefollow inner join tickle on (ticklefollow.TickleTrainID=tickle.TickleTrainID) inner join category on (tickle.TickleContact=CategoryID and tickle.TickleID=category.TickleID) inner join tickleuser on (tickle.TickleID=tickleuser.TickleID) left outer join files on (ticklefollow.FollowTickleTrainID=files.FileParentID and files.FileContext='ticklefollow') where tickle.TickleID=$tickleId and  ticklefollow.TickleTrainID = '$TickleTrainID'";
if ($q!=''){
    $mselect.=" and (TickleMailFollowContent like '%$q%')";
}

$cnt = intval(selectvalue("select count(*)$mselect"));
$pg=max(1,intval($_REQUEST['pg']));
$pc=intval(GetVal($_REQUEST['pc'],20));
$ps = ceil($cnt/$pc);

$mselect.=" order by ".$sortfields[$sfld]." ".$sortorders[$sord];
$mselect.=" limit ".($pg-1)*$pc.", ".$pg*$pc;

$mselect = "select ticklefollow.*, concat(ticklefollow.DailyDaysFollow,' days, Repeat ',ticklefollow.EndAfterFollow,' times') as Schedule, count(files.FileID) as attaches from ticklefollow left outer join files on (ticklefollow.FollowTickleTrainID=files.FileParentID and files.FileContext='ticklefollow') where ticklefollow.TickleID=$tickleId and  ticklefollow.TickleTrainID = '$TickleTrainID'  group by ticklefollow.FollowTickleTrainID order by ticklefollow.FollowTickleTrainID";
$follows = $db->query_to_array($db->query_to_array($fileds.$mselect));
?>
<div class="bar">
	<div class="align_left">
		<label for="num">Show</label>
		<select class="sel_num" id="num">
			<option>10</option>
			<option>20</option>
			<option>30</option>
		</select>
		<span class="txt">entries</span>
	</div>
	<div class="align_right">
		<span class="input_text"><input type="text" value="Search tickles" /></span>
		<input type="submit" value="Search" class="btn_search" />
	</div>
</div>
<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th>Mail message</th>
			<th>Time <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4"
			                                          alt="" rel="1"/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
			                                                               class="down" width="6" height="4" alt="" rel="1"/></span></th>
			<th>Schedule <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4"
						                                          alt="" rel="2"/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
						                                                               class="down" width="6" height="4" alt="" rel="2"/></span></th>
			<th>Options</th>
		</tr>
	</thead>
	<tbody>
<?foreach ($follows as $ind=>$frow) {?>
<tr<?=GetIf($ind % 2==1," class='light'","")?>>
    <td>
        <div class="txt">
            <p><?//=$row['TickleMailFollowContent']?></p>
            <?php //echo $row['TickleMailFollowContent']; ?>
            <ul class="h_txt">
                <li><a
                    href="<?=Url_Create("addtickle", "tid=" . $row['TickleTrainID'] . "&action=Edit")?>">Edit</a>
                </li>
                <li><a
                    href="<?=Url_Create("tickle", "ftid=" . $row['FollowTickleTrainID'] . "&action=DeleteFollow")?>">Delete</a>
                </li>
                <li><a href="#"
                       onclick="javascript:return PreviewEmail('<?=$row['TickleTrainID']?>', '<?=addslashes($row['TickleName'])?>');">test</a>
                </li>
            </ul>
        </div>
    </td>
    <td>
        <div class="txt">
            <?=$row['TickleTimeFollow']?>
        </div>
    </td>
    <td>
        <div class="txt">
            <?=$row['Schedule']?>
        </div>
    </td>
    <td>
        <div class="txt">
            <ul class="icons">
                <li><span class="ico01<?=GetIf($row['TApprove']=='Y',' active','')?>"></span></li>
                <li><span class="ico02<?=GetIf($row['NoWeekend']=='Y',' active','')?>"></span></li>
                <li><span class="ico03<?=GetIf($row['CCMeFollow']=='Y',' active','')?>"></span></li>
                <li><span class="ico04<?=GetIf(@intval($row['attaches'])!=0,' active','')?>"></span></li>
            </ul>
        </div>
    </td>
</tr>
    <? } ?>
    </tbody>
			</table>
<?if ($ps>1){?>
			<div class="pagination">
				<div class="holder">
					<ul>
<?for($i=1;$i<=$ps;$i++){?>
						<li<?=GetIf($i==$pg, ' class="current"','')?>><?if($i!=$ps){?><a href="./?pg=<?=$i?>"><?=$i?></a><?}?><?if($i!=$ps){?><span><?=$i?></span><?}?></li>
<?}?>
					</ul>
				</div>
			</div>
<?}?>
