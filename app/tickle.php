<?php

$pageId = $_GET['page'];
if ($pageId <= 0) $pageId = 1;
list($ticklelist, $paging, $total) = $db->select_array_slice('tickle', '*', " where TickleID='" . $_SESSION['TickleID'] . "'  order by CreatedDate ,ModifyDate desc", $pageId, PAGE_LIMIT);
$group = $db->select_to_array('category', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y'");
$category = array();
$EmailPrioritys['1'] = "High";
$EmailPrioritys['5'] = "Low";
$EmailPrioritys['3'] = "Normal";
foreach ($group as $k => $v)
{
    $category[$v['CategoryID']] = $v['CategoryName'];
}
?>
<script type="text/javascript" src="/<?=ROOT_FOLDER?>plugins/jquery.zclip.min.js"></script>
<script>
    function PreviewEmail(tid, TName) {
        var cancel = {text:'Cancel', click:function () {
            $(this).dialog('close')
        }};
        var send = {text:'Send', click:function () {
            $(this).dialog('close');
            var emailids = $("#actemail").val();
            if (emailids.replace(/^\s+|\s+$/g, '') != "") {
                $.post("<?=Url_Create('preview')?>", { emailid:emailids, Tickletid:tid }, function (data) {
                    mcalert("Confirmation", "" + data);
                });
            }
        }};
        var message = "Enter the email adddress where you'd like to send your test Tickle(s). You'll receive it immediately: <input type='text' name='actemail' id='actemail'/>";
        mdialog("Tickle Test", message, [send, cancel]);
    }
    function duplicate(tid, TName) {
        if (tid != "") {
            var cancel = {text:'Cancel', click:function () {
                $(this).dialog('close')
            }};
            var send = {text:'Create', click:function () {
                $(this).dialog('close');
                var Ticklename = jQuery.trim($("#actname").val());
                if (Ticklename != "" && Ticklename.match(/^[A-Za-z0-9]+$/ig)) {
                    $.post("<?=Url_Create('duplicatetickle')?>", { TickleName:Ticklename, Tickletid:tid, qstr: '<?php echo json_encode($_GET);?>'}, function (data) {
                        if (jQuery.trim(data) == "Exist") {
                            mcalert("Duplicate Tickle", "You cannot create a Tickle with the same name, you should not be allowed to duplicate a Tickle and have it named the same as an existing tickle/group.");
                        } else if (jQuery.trim(data) == "New") {
                            mralert("Duplicate Tickle", "New Tickle Created", window.location.href);
                        } else {
                            mcalert("Duplicate Tickle", data + "Error while creating Tickle");
                        }
                    });
                } else {
                    mcalert("Duplicate Tickle", "Please enter valid Tickle name.");
                }

            }};
            var message = "Enter the New Tickle Name  (chars and numbers only):<br/><input type='text' name='actname' id='actname'/>";
            mdialog("Duplicate Tickle", message, [send, cancel]);
        }
    }

    function duplicateFollow(ftid) {
        if (ftid != "") {
            $.post("<?=Url_Create('duplicateticklefollow')?>", { FollowTickleId:ftid}, function (data) {
                if (jQuery.trim(data) == "New") {
                    mralert("Duplicate Follow-Up Tickle", "New Follow-Up Tickle Created", window.location.href);
                } else {
                    mcalert("Duplicate Follow-Up Tickle", data + "Error while creating Tickle");
                }
            });
        }
    }

    $(document).ready(function(){
        $('a.clip_link').click(function(){
            var tid = $(this).attr("rel");
            $("#bcc"+tid).select();
            return false;
        });
        $(".listId").click(function(){
            if ($(this).attr("checked") && $(this).attr("rel")=="0"){
                malert('You cannot delete a Tickle with pending campaigns in the Dashboard.');
                return false;
            }
        });
    });
    <?if (@trim($_SESSION['ticklenew']) != '') { ?>
    $(document).ready(function () {
        mcalert("Congratulations!", "<center><p>Your Tickle Has Been Created!</p><p>Just place the following email address in your BCC field when sending an email: <a href='mailto:<?=$_SESSION['ticklenew']?>+<?=$_SESSION['UserName']?>@tickletrain.com'><?=$_SESSION['ticklenew']?>+<?=$_SESSION['UserName']?>@tickletrain.com</a></p><p>TickleTrain will do the rest. Send it. And forget it.</p></center>", {'width':'500px'});
    });
        <?
        $_SESSION['ticklenew'] = "";
        unset($_SESSION['ticklenew']);
        $_SESSION['page'] = $_GET['u'];
    }?>
</script>
<div class="main_holder tickle_list">
    <div class="heading">
        <h1>Tickles</h1>
        <a href="<?=Url_Create('addtickle')?>" class="btn_green"><span>Add Tickle</span></a>
    </div>
    <div class="bar">
        <div class="align_left">
            <form id="bulkActsApply">
                <fieldset>
                    <select id="bactionSelect">
                        <option value="">Bulk actions</option>
                        <option value="delete">Delete</option>
                    </select>
                    <input type="submit" value="Apply" class="btn_apply"/>
                </fieldset>
            </form>
        </div>
	
        <div class="align_right">
            <label>Show</label>
        	<form method="post" id="formperpage">
        		<select name='recordperpage' id="selectrec" onchange="this.form.submit();" style="width: 48px;">
        			<option value="10" <?php echo ($perpage==10)?'selected=selected':'' ?>>10</option>
        			<option value="25" <?php echo ($perpage==25)?'selected=selected':'' ?>>25</option>
        			<option value="50" <?php echo ($perpage==50)?'selected=selected':'' ?>>50</option>
        			<option value="100" <?php echo ($perpage==100)?'selected=selected':'' ?>>100</option>
        		</select>
            </form>
    	    <label>per page</label>
            <ul class="bar_buttons">
                <li><a href="#" class="expand_all">expand</a></li>
            </ul>
            <form>
                <fieldset>
                    <span class="input_text"><input type="text" name="q" value="<?=@trim($_REQUEST['q'])?>"/></span>
                    <input type="submit" value="Filter" class="btn_filter"/>
                </fieldset>
            </form>
        </div>
    </div>
    <form id="bulkActs" method="post">
        <input type="hidden" name="baction" value="" id="bulkact"/>
        <fieldset>

            <table cellpadding="0" cellspacing="0" id="maintbl">
                <thead>
                <tr>
                    <th style="width:250px" class="hsort<?=GetIf($sfld==1 && $sord==1," sort_up","")?><?=GetIf($sfld==1 && $sord==2," sort_down","")?>" rel="1"><input type="checkbox" id="selectAll"/>Tickle Name<span
                        class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4"
                                          alt="" rel="1"/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
                                                               class="down" width="6" height="4" alt="" rel="1"/></span>
                    </th>
                    <th class="hsort<?=GetIf($sfld==2 && $sord==1," sort_up","")?><?=GetIf($sfld==2 && $sord==2," sort_down","")?>" rel="2">Tickle BCC <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up"
                                                           width="6" height="4" alt="" rel="2"/><img
                        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6" height="4" alt=""
                        rel="2"/></span></th>
                    <!--th>Group <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up"
                                                      width="6" height="4" alt="" rel="3"/><img
                        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6" height="4" alt=""
                        rel="3"/></span></th-->
                    <th class="hsort<?=GetIf($sfld==3 && $sord==1," sort_up","")?><?=GetIf($sfld==3 && $sord==2," sort_down","")?>" rel="3">Schedule <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up"
                                                         width="6" height="4" alt="" rel="4"/><img
                        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6" height="4" alt=""
                        rel="4"/></span></th>
                    <th>Options</th>
                </tr>
                </thead>
                <tbody>
                <?foreach ($list as $key=>$row) { ?>
                <tr class="maintr" id="maintr<?php echo $key ?>">
                    <td>
                        <input type="checkbox" class="listId" name="TickleID[]" value="<?=$row['TickleTrainID']?>"
                               id="tickle<?=$row['TickleTrainID']?>" rel="<?=@intval(checkTickleDelete($row['TickleTrainID']))?>"/>

                        <div class="txt">
                            <a href="#" class="ico_expand" id="ex<?=$row['TickleTrainID']?>"
                                                           style="display:<?=GetIf(count($flist[$row['TickleTrainID']]), '', 'none')?>">expand/excerpt</a>&nbsp;<label for="tickle<?=$row['TickleTrainID']?>"><?=$row['TickleName']?></label>
                        </div>
                    </td>
                    <td>
                        <div class="txt">
                            <?php echo $row['BccName']; ?>
                            
                        </div>
                    </td>
                    <!--td>
                        <div class="txt">
                            <?=$row['CategoryName']?>
                        </div>
                    </td-->
                    <td>
                        <div class="txt">
                            
			   <?php  $sch = explode(' ', $row['Schedule']);
if ($sch['3'] == '13') {
    echo $sch['0'].' '.$sch['1'].' '.$sch['2'].' &infin; '.$sch['4'];
} else {
    echo $row['Schedule'];
}?>
                        </div>
                    </td>
                    <td>
                        <div class="txt">
                            <ul class="icons">
                                <li>
									<a href="javascript:void(0);" class="ico_play_pause01 <?php if($row['TApprove']=='Y'){ echo 'play'; }else{ echo 'pause'; }?>" style="cursor:default;"><?php if($row['TApprove']=='Y'){ echo 'Play'; }else{ echo 'Pause'; }?></a>
									<!--<span class="ico01<?=GetIf($row['TApprove']=='Y',' active','')?>"></span>-->									
								</li>
                                <li><span class="ico02<?=GetIf($row['NoWeekend']=='Y',' active','')?>"></span></li>
                                <li><span class="ico03<?=GetIf($row['CCMe']=='Y',' active','')?>"></span></li>
                                <li><span class="ico04<?=GetIf(@intval($row['attaches'])!=0,' active','')?>"></span></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr class="childtr">
                    <td>
                            <ul class="h_txt" style="padding-left: 27px">
                                <li style="border-left: none"><a
                                    href="<?=Url_Create("addtickle", "tid=" . $row['TickleTrainID'] . "&action=Edit&hashtag=maintr".$key."&qstr=".base64_encode(json_encode($_GET)))?>">Edit</a>
                                </li>
                                <li>
                                    <?if (checkTickleDelete($row['TickleTrainID'])){?>
                                    <a href="<?=Url_Create("tickle", "tid=" . $row['TickleTrainID'] . "&action=Delete&redirectUrl=".base64_encode(json_encode($_GET)))?>" class="delete_link">Delete</a>
                                    <?}else{?>
                                    <a href="#" onclick="javascript:malert('You cannot delete a Tickle with pending campaigns in the Dashboard.');return false">Delete</a>
                                    <?}?>
                                </li>
                                <li><a href="javascript:void(0);" onclick="javascript:return duplicate('<?php echo $row['TickleTrainID']?>','<?php echo addslashes($row['TickleName']);?>')">Duplicate</a></li>
                                <li><a href="#"
                                       onclick="javascript:return PreviewEmail('<?=$row['TickleTrainID']?>', '<?=addslashes($row['TickleName'])?>');">Test</a>
                                </li>
                            </ul>
                    </td>
                    <td>
                            <!--ul class="h_txt">
                                <li>
                                    <a href="#" class="clip_link" rel="<?=$row['TickleTrainID']?>">Select all</a>
                                </li>
                            </ul-->
                    </td>
                    <!--td>
                        <div class="txt">
                            <?=$row['CategoryName']?>
                        </div>
                    </td-->
                    <td>
                    </td>
                    <td>
                    </td>
                </tr>
                    <? if (count($flist[$row['TickleTrainID']])) { ?>
                        <? foreach ($flist[$row['TickleTrainID']] as $frow) { ?>
                        <tr class="maintr light tt<?=$frow['TickleTrainID']?>" style="display:<?php if(isset($_GET['follow_up']) && $frow['TickleTrainID']==$_GET['follow_up']){ echo ''; }else{ echo 'none'; }?>">
                            <td>
                                <div class="spacer">&nbsp;</div>
                                <div class="txt">
                                    <label for="tickle<?=$row['TickleTrainID']?>"><?=$row['TickleName']?></label>
                                </div>
                            </td>
                            <td>
                                <div class="txt">
                                    <?=$row['BccName']?>
                                </div>
                            </td>
                            <!--td>
                                <div class="txt">
                                    <?=$row['CategoryName']?>
                                </div>
                            </td-->
                            <td>
                                <div class="txt">
                                    <?=$frow['Schedule']?>
                                </div>
                            </td>
                            <td>
                                <div class="txt">
                                    <ul class="icons">
                                        <li>
										<a href="javascript:void(0);" class="ico_play_pause01 <?php if($frow['TApprove']=='Y'){ echo 'play'; }else{ echo 'pause'; }?>" style="cursor:default;"><?php if($frow['TApprove']=='Y'){ echo 'Play'; }else{ echo 'Pause'; }?></a>
										</li>
                                        <li><span class="ico02<?=GetIf($frow['NoWeekend']=='Y',' active','')?>"></span></li>
                                        <li><span class="ico03<?=GetIf($frow['CCMeFollow']=='Y',' active','')?>"></span></li>
                                        <li><span class="ico04<?=GetIf(@intval($frow['attaches'])!=0,' active','')?>"></span></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr class="childtr light tt<?=$frow['TickleTrainID']?>" style="display:<?php if(isset($_GET['follow_up']) && $frow['TickleTrainID']==$_GET['follow_up']){ echo ''; }else{ echo 'none'; }?>">
							<?php 
							   $get_var = $_GET;
							   $get_var['follow_up'] = $frow['TickleTrainID'];
							?>
                            <td>
                                    <ul class="h_txt" style="padding-left: 27px">
                                        <li style="border-left: none"><a
                                            href="<?=Url_Create("addtickle", "tid=".$row['TickleTrainID']."&ftid=" . $frow['FollowTickleTrainID'] . "&action=Edit&hashtag=maintr".$key."&qstr=".base64_encode(json_encode($get_var)))?>">Edit</a>
                                        </li>
                                        <li>
                                            <?if (checkTickleFollowDelete($frow['FollowTickleTrainID'])){?>
                                            <a href="<?=Url_Create("tickle", "ftid=" . $frow['FollowTickleTrainID'] . "&action=DeleteFollow&redirectUrl=".base64_encode(json_encode($_GET)))?>" class="delete_link">Delete</a>
                                            <?}else{?>
                                            <a href="#" onclick="javascript:malert('You cannot delete a Follow Up Tickle with pending campaigns in the Dashboard.');return false">Delete</a>
                                            <?}?>
                                        </li>
                                        <li><a href="javascript:void(0);" onclick="javascript:return duplicateFollow('<?php echo $frow['FollowTickleTrainID']?>')">Duplicate</a></li>
                                        <li><a href="#"
                                               onclick="javascript:return PreviewEmail('<?=$row['TickleTrainID']?>', '<?=addslashes($row['TickleName'])?>');">Test</a>
                                        </li>
                                    </ul>
                            </td>
                            <!--td>
                                <div class="txt">
                                    <?=$row['CategoryName']?>
                                </div>
                            </td-->
                        </tr>

                            <? } ?>
                        <? } ?>
                    <? }?>
                </tbody>
            </table>

            <? if ($ps > 1) { ?>
                <div class="pagination">
                    <div class="holder">
                        <ul>
                            <? for ($j = 1; $j <= $ps; $j++) { ?>
                                <li<?= (($j == $pg) ? ' class="current"' : '') ?>>
                                    <?= (($j == $pg) ? '<span>' : '<a href="?pg=' . $j . '&q=' . trim($_REQUEST['q']) . '&qdate=' . trim($_REQUEST['qdate']) . '&sort=' . trim($_REQUEST['sort']) . '">') ?>
                                    <?= $j ?>
                                    <?= (($j == $pg) ? '</span>' : '</a>') ?>
                                </li>
                            <? } ?>
                        </ul>
                    </div>
                </div>
            <? } ?>
        </fieldset>
    </form>
</div>
