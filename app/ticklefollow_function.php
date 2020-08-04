<?php
$tickleId = @trim($_SESSION['TickleID']);
$tid = @trim($_GET['tid']);
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
$Variables['sfld'] = $sfld;
$Variables['sord'] = $sord;
$sfld--;
$sord--;

$sortorders = array("asc","desc");
$sortfields = array("TickleTimeFollow","Schedule");

$fileds = "select ticklefollow.*, concat(ticklefollow.DailyDaysFollow,' days, Send ',ticklefollow.EndAfterFollow,' times') as Schedule, count(files.FileID) as attaches";
$mselect = " from ticklefollow left outer join files on (ticklefollow.FollowTickleTrainID=files.FileParentID and files.FileContext='ticklefollow') where ticklefollow.TickleID=$tickleId and  ticklefollow.TickleTrainID = '$tid'";
if ($q!=''){
    $mselect.=" and (TickleMailFollowContent like '%$q%')";
}
$mselect.=" group by ticklefollow.FollowTickleTrainID";

$pg=max(1,intval($_REQUEST['pg']));
$pc=intval(GetVal($_REQUEST['pc'],10));
if ($sort!=""){
 $mselect.=" order by ".$sortfields[$sfld]." ".$sortorders[$sord];   
}else{
 $mselect.=" order by ticklefollow.FollowTickleTrainID asc";  
}
$mselect.=" limit ".($pg-1)*$pc.", ".$pc;
//die();

$follows = $db->query_to_array($fileds.$mselect);

$sfld = $Variables['sfld'];
$sord = $Variables['sord'];
?>
<input type="hidden" id="reqstr" value="pg=<?=$pg?>&pc=<?=$pc?>&sort=<?=$sort?>&q=<?=$q?>"/>
<?/*div class="bar">
	<div class="align_left">
		<label for="pc">Show</label>
		<select class="sel_num" id="pc" name="pc" onchange="reloadFollowUps('pc',$(this).val())">
            <?for($ii=10;$ii<=30;$ii+=10){?>
            <option<?=GetIf($ii==$pc,' selected','')?>><?=$ii?></option>
            <?}?>
		</select>
		<span class="txt">entries</span>
	</div>
	<div class="align_right">
		<span class="input_text"><input type="text" value="<?=$q?>" placeholder="Search tickles" id="qsearch" /></span>
		<input type="submit" value="Search" class="btn_search" onclick="reloadFollowUps('q',$('#qsearch').val());return false;" />
	</div>
</div*/?>
<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th>Mail message</th>
			<th class="hsort<?=GetIf($sfld==1 && $sord==1," sort_up","")?><?=GetIf($sfld==1 && $sord==2," sort_down","")?>" rel="1" onclick="hsort(this)">Time <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4"
			                                          alt="" rel="1" onclick="reloadFollowUps('sort','1-1');return false;"/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
			                                                               class="down" width="6" height="4" alt="" rel="1" onclick="reloadFollowUps('sort','1-2');return false;"/></span></th>
			<th class="hsort<?=GetIf($sfld==2 && $sord==1," sort_up","")?><?=GetIf($sfld==2 && $sord==2," sort_down","")?>" rel="2" onclick="hsort(this)">Schedule <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4"
						                                          alt="" rel="2" onclick="reloadFollowUps('sort','2-1');return false;"/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
						                                                               class="down" width="6" height="4" alt="" rel="2" onclick="reloadFollowUps('sort','2-2');return false;"/></span></th>
			<th>Options</th>
		</tr>
	</thead>
	<tbody>

           


<?foreach ($follows as $ind=>$row) { ?>
<tr<?=GetIf($ind % 2==1," class='light'","")?>>
    <td>
        <div class="txt">
            <p><?=$row['TickleMailFollowContent']?></p>
            <ul class="h_txt" style="width: 100%; clear: both">
                <li><a
                    href="javascript:EditFollow(<?=$row['FollowTickleTrainID']?>);">Edit</a>
                </li>
                <li><a
                    href="javascript:DeleteFollow(<?=$row['FollowTickleTrainID']?>);" onclick="javascript:return confirm('Delete this Tickle?');">Delete</a>
                </li>
                <li><a
                    href="javascript:DuplicateFollow(<?=$row['FollowTickleTrainID']?>);">Duplicate</a>
                </li>
                <li><a
                    href="javascript:MoveupFollow(<?=$row['FollowTickleTrainID']?>);">Move up</a>    
                </li>
                <li><a
                    href="javascript:MovedownFollow(<?=$row['FollowTickleTrainID']?>);">Move down</a>    
                </li>
            </ul>
        </div>
    </td>
    <td>
        <div class="txt">
		 <?=date('h:i A',strtotime($row['TickleTimeFollow']));?>
            <?//=$row['TickleTimeFollow']?>
        </div>
    </td>
    <td>
        <div class="txt">
            <?php
                $sch = explode(' ', $row['Schedule']);
                if ($sch['3'] == '13') {
                    echo $sch['0'].' '.$sch['1'].' '.$sch['2'].' &infin; '.$sch['4'];
                } else {
                    echo $row['Schedule'];
                }
            ?>
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
    <?  } ?>
    </tbody>
			</table>
<?php
exit;
?>
