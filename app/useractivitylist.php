<?php
if(isset($_SESSION['TickleID']))
	$tickleId = @trim($_SESSION['TickleID']);
else 
	$tickleId = base64_decode($_GET['ttuser']);

$mode=intval($GLOBALS['mode']);
//echo '<pre>';
//print_r($GLOBALS);
$_SESSION['page'] = $_GET['u'];
?>
<?if($mode==1){?>
<script language="javascript">
$(document).ready(function(){
        mdialog('<?=addslashes($GLOBALS['subject'])?>',unescape('<?=addslashes($GLOBALS['hcontent'])?>'),false,{'height':500, 'width':800});
});
</script>
<?}?>
<?if ($mode==6){?>
<script language="javascript">
$(document).ready(function(){
        mdialog('Subject Email','This campaign no longer exists');
});


	
</script>
<?}
?>

<style>
    .pagination {
        overflow: inherit !important;
    }
</style>

<script>

 function preview1(TaskIDs, MailIDs, PreviewType, subj) {
        preview(TaskIDs, MailIDs, PreviewType, subj, true);
    }
	
function preview(TaskIDs, MailIDs, PreviewType, subj, susp) {
        var title = subj;
        if (PreviewType != 'Mail' && PreviewType != 'MailAttach') {
            title = "Preview of Tickle scheduled";
        }
	//	var url =  "https://" . SERVER_NAME . Url_Create("useractivitylist", "act=" . rawurlencode($protect) . "&MailID=" . $list['MailID'] . "&Mails=Mail&Activitylogs=completed&gid=".trim($_REQUEST['gid']));
					
        var url = "<?= Url_Create('previewmail') ?>?TaskID=" + TaskIDs + "&MailID=" + MailIDs + "&Mails=" + PreviewType +"&Activitylogs=completed";
        if (susp) {
            url += "&suspended=yes";
        }
        //alert(url);
        $("#uploadFrame").src(url, function() {
            mdialogNew(title, $(this).contents().find("body").html(), false, {'height': 500, 'width': 800});
        });
    }
</script>

<div class="main_holder">
  <div class="heading">
	<h1>Completed Activities</h1>
  </div>
    
	
	
	 <div class="summary_sec">
	 <hr>
	 
	 <div class="bar2">
		<div class="align_left">
			<h3>Recently Completed in last 10 days</h3>
		</div>
		<div class="align_right activty_filter">
			 <label>Show per page</label>
				<form method="post" id="formperpage">
					<select name='recordperpage' id="selectrecac" onchange="this.form.submit();" style="width: 48px;">
						<option value="10">10</option>
						<option value="25">25</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select>
				</form>
			
            <form id="perpageform">
                <fieldset>
                    <select name="gid" style="width:130px" onchange="this.form.submit();">
                        <option value="">Show All Tickles</option>
                        <?php /*<option value="0"<?=getIf($gid=="0",' selected','')?>>Unassigned</option> */?>
                        <?foreach($glist as $grow): ?>
                        <option value="<?=$grow['CategoryID']?>"<?=GetIf($gid==$grow['CategoryID'],' selected','')?>><?=$grow['CategoryName']?></option>
                        <?endforeach?>
                    </select>
                    <span class="input_text"><input type="text" name="q" value="<?=@trim($_REQUEST['q'])?>" id="filterList"/></span>
                    <input type="submit" value="Filter" class="btn_filter"/>
                </fieldset>
            </form>
        </div>
	</div>
		
	 
	    <?php if(!empty($Clist)) { ?>
		<table cellpadding="0" cellspacing="0" id="maintb_useractivity">
                <thead>
					<tr>
						  <th class="hsort<?=GetIf($sfld==1 && $sord==1," sort_up","")?><?=GetIf($sfld==1 && $sord==2," sort_down","")?>" rel="1">Subject<span
                        class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4"
                                          alt="" rel="1"/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
                                                               class="down" width="6" height="4" alt="" rel="1"/></span>
                    </th>
					
					<th class="hsort<?=GetIf($sfld==2 && $sord==1," sort_up","")?><?=GetIf($sfld==2 && $sord==2," sort_down","")?>" rel="2">Date Completed<span
                        class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4"
                                          alt="" rel="1"/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
                                                               class="down" width="6" height="4" alt="" rel="2"/></span>
                    </th>
					<th class="hsort<?=GetIf($sfld==3 && $sord==1," sort_up","")?><?=GetIf($sfld==3 && $sord==2," sort_down","")?>" rel="3">Recipient<span
                        class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4"
                                          alt="" rel="1"/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
                                                               class="down" width="6" height="4" alt="" rel="3"/></span>
                    </th>
					<th class="hsort<?=GetIf($sfld==4 && $sord==1," sort_up","")?><?=GetIf($sfld==4 && $sord==2," sort_down","")?>" rel="4">Tickle<span
                        class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4"
                                          alt="" rel="1"/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
                                                               class="down" width="6" height="4" alt="" rel="4"/></span>
                    </th>
					<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php 
				 foreach($Clist as $list) {
					 $protect = protect($list['TickleID'] . "-" . $list['TaskID']);
					  $hreflink = "https://" . SERVER_NAME . Url_Create("previewmail", "act=" . rawurlencode($protect) . "&MailID=" . $list['MailID'] . "&Mails=Mail&Activitylogs=completed");
					$hreflink1 = "https://" . SERVER_NAME . Url_Create("useractivitylist", "act=" . rawurlencode($protect) . "&MailID=" . $list['MailID'] . "&Mails=Mail&Activitylogs=completed&gid=".trim($_REQUEST['gid']));
					
					// htmlspecialchars($list['fromaddress']);
					 $fromaddress=imap_rfc822_parse_adrlist($list['fromaddress'],"");
					 $EmailAddr=$fromaddress[0]->mailbox."@".$fromaddress[0]->host;
					?>
					<tr>
						<td><?php echo $list['subject'];?></td>
						<td><?php echo date('m-d-y',strtotime($list['comdate']));?></td>
						<td><?php echo $list['toadrs'];?></td>
						<td><?php echo $list['TickleName'];?></td>
						<td><!--a href="<?= $hreflink1 ;?>" target="_self">View</a-->
						<a href="#" onclick="preview('<?= $list["TaskID"] ?>', '<?= $list["MailID"] ?>', 'Mail','<?= $list['subject'] ?>');return false;">View</a>
						
						</td>
					</tr>
				<?php } ?>
				</tbody>
	   
	 </table>
		<?if ($ps > 1) { ?>
            <div class="pagination">
                <div class="holder">
                    <ul>
                        <?for ($j = 1; $j <= $ps; $j++) { ?>
                        <li<?=(($j == $pg) ? ' class="current"' : '')?>><?=(($j == $pg) ? '<span>' : '<a href="?pg=' . $j . '&q=' . trim($_REQUEST['q']). '&gid=' . trim($_REQUEST['gid']). '&sort=' . trim($_REQUEST['sort']) . '">')?><?=$j?><?=(($j == $pg) ? '</span>' : '</a>')?></li>
                        <? }?>
                    </ul>
                </div>
            </div>
                <? }?>
				
		<?php } ?>
	 </div>
</div>
<iframe id="uploadFrame" name="uploadFrame" style="width:0px;height:0px" frameborder="0"></iframe>