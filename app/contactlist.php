<?php
$_SESSION['page'] = $_GET['u'];
?>
<style>
    .pagination {
        overflow: inherit !important;
    }
</style>
<div class="main_holder">
    <div class="heading">
        <h1>Contacts</h1>
        <a href="#" class="btn_blue" onclick="return ExportContacts(this)"><span>Export contacts</span></a>
        <?/* if (!$_SESSION['access_token'] && $_SESSION['TickleID'] != "") { ?>
            <div class="fb_block">
                <div id="fb-root"></div>
                <div id="facebookerror" style="margin-left: 5px; display: inline-block">Get more information about your
                    contacts by <a
                        href="/<?=ROOT_FOLDER?>fb/settoken/">logging into</a> Facebook.
                </div>
            </div>
        <? } */?>
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
	<?php //if($_SERVER['REMOTE_ADDR']=='202.164.47.148'){ ?>
        <label>Show</label>
	<form method="post" id="formperpage">
		<select name='recordperpage' id="selectrec" onchange="this.form.submit();" style="width: 48px;">
			<option value="10">10</option>
			<option value="25">25</option>
			<option value="50">50</option>
			<option value="100">100</option>
		</select>
        </form>
	<label>per page</label>
        <?php //} ?>
            <form id="perpageform">
                <fieldset>
                    <select name="gid" style="width:130px" onchange="this.form.submit();">
                        <option value="">View all contacts</option>
                        <?php /*<option value="0"<?=getIf($gid=="0",' selected','')?>>Unassigned</option> */?>
                        <?foreach($glist as $grow):?>
                        <option value="<?=$grow['CategoryID']?>"<?=getIf($gid==$grow['CategoryID'],' selected','')?>><?=$grow['CategoryName']?></option>
                        <?endforeach?>
                    </select>
                    <span class="input_text"><input type="text" name="q" value="<?=@trim($_REQUEST['q'])?>" id="filterList"/></span>
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
                    <th class="hsort<?=GetIf($sfld==1 && $sord==1," sort_up","")?><?=GetIf($sfld==1 && $sord==2," sort_down","")?>" rel="1"><input type="checkbox" id="selectAll"/><!--<label for="/*selectAll*/">-->First Name<!--</label>--><span
                        class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4"
                                          alt="" rel="1"/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
                                                               class="down" width="6" height="4" alt="" rel="1"/></span>
                    </th>
                    <th class="hsort<?=GetIf($sfld==2 && $sord==1," sort_up","")?><?=GetIf($sfld==2 && $sord==2," sort_down","")?>" rel="2">Last Name<span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up"
                                                         width="6" height="4" alt="" rel="2"/><img
                        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6" height="4" alt=""
                        rel="2"/></span></th>
                    <th class="hsort<?=GetIf($sfld==3 && $sord==1," sort_up","")?><?=GetIf($sfld==3 && $sord==2," sort_down","")?>" rel="3">E-mail<span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up"
                                                      width="6" height="4" alt="" rel="3"/><img
                        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6" height="4" alt=""
                        rel="3"/></span></th>
                    <th>Tickle Name <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up"
                                                      width="6" height="4" alt="" rel="4"/><img
                        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6" height="4" alt=""
                        rel="4"/></span></th>
                    <th>Social</th>
                </tr>
                </thead>
                <tbody>
                <?
                $ind=0;
                $key=0;
                foreach ($list as $lKey=>$row) {
                $ind++;
                //echo "select ccat.CategoryID,tickle.TickleTrainID,tickle.TickleName category_contact_list ccat inner join tickle on ccat.CategoryID=tickle.TickleContact where ccat.ContactID='".$row['ContactID']."'";
                $catData = $db->query_to_array("select ccat.CategoryID,tickle.TickleTrainID,tickle.TickleName from category_contact_list ccat inner join tickle on ccat.CategoryID=tickle.TickleContact where ccat.ContactID='".$row['ContactID']."'");                
                //echo '<pre>'; print_r($catData);
                foreach($catData as $catRow){
                ?>
                <tr class="maintr" id="maintr<?php echo $key ?>">
                    <td>
                        <input type="checkbox" class="listId" name="CContactID[]" value="<?=$row['ContactID']?>"
                               id="contact<?=$row['CContactID']?>" rel="<?=intval(checkContactDelete($row['ContactID'],$catRow['CategoryID']))?>">

                        <div class="txt">
                            <label for="contact<?=$row['CContactID']?>"><?=str_replace("'","",$row['FirstName'])?></label>
                        </div>
                    </td>
                    <td>
                        <div class="txt">
                            <?=str_replace("'","",$row['LastName'])?>
                        </div>
                    </td>
                    <td>
                        <div class="txt">
                            <?=$row['EmailID']?>
                        </div>
                    </td>
                    <td>
                        <div class="txt">
                            <?=$catRow['TickleName']?>
                        </div>
                    </td>
                    <td>
                        <div class="txt">
                            <ul class="social_nw">
                                <li id="fb-<?=$ind?>" style="display:none"></li>
                                <?php /*
                                <script>$('#fb-<?=$ind?>').load('<?=Url_Create("fb/image", "email=" . $row['EmailID'] . "&cid=" . $row['ContactID']."&ret=facebook")?>', function (data) {
                                    if (data != '') {
                                        $(this).show()
                                    }
                                })</script>
                                <li><img src="/<?=GetRootFolder()?>images/ico_linked_in.png" width="13" height="13"
                                         alt=""/></li*/?>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr class="childtr">
                    <td>
                            <ul class="h_txt" style="padding-left: 27px">
                                <li style="border-left: none">
                                    <a href="#" onclick="return ExtendedEditContact('<?=$row['ContactID']?>','<?=$row['EmailID']?>','maintr<?php echo $key ?>')">Edit</a>
                                </li>
                            </ul>
                    </td>
                    <td>
                            <ul class="h_txt">
                                <li>
                                    <a href="#" onclick="return ExtendedEditContact('<?=$row['ContactID']?>','<?=$row['EmailID']?>','maintr<?php echo $key ?>')">Edit</a>
                                </li>
                            </ul>
                    </td>
                    <td>
                            <ul class="h_txt">
                                <li><a href="#" onclick="return ExtendedEditContact('<?=$row['ContactID']?>','<?=$row['EmailID']?>','maintr<?php echo $key ?>')">Edit</a></li>
                                <li><a href="mailto:<?=$row['EmailID']?>">Send e-mail</a></li>
                            </ul>
                    </td>
                    <td>
                            <?if ($catRow['TickleTrainID']!=""){?>
                                <ul class="h_txt">
                                    <li><a
                                        href="<?=Url_Create("addtickle", "tid=" . $catRow['TickleTrainID'] . "&action=Edit". "&hashtag=maintr$key")?>">Edit</a>
                                    </li>
                                </ul>
                            <?}?>
                    </td>
                    <td>
                    </td>
                </tr>
                <? $key++;}}?>
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
        </fieldset>
    </form>
</div>

<script type="text/javascript" charset="utf-8">

    function ExportContacts(elm) {
        var url = '<?=Url_Create("contactmanager", "action=ExportContacts")."&CategoryID=".$_GET['gid']?>';
        if ($("#filterList").val() != "") {
            url += "&search=" + $("#filterList").val();
        }
        $(elm).attr("href", url);
        return true;
    }
    function ExtendedEditContact(ContactID, email ,hashtag) {
        $.get('<?=Url_Create('contactmanager')?>', { ContactID:ContactID, action:'EditContactForm', hashtag: hashtag, redirect:'contactlist', email:email , qstr: '<?php echo json_encode($_GET);?>'}
            , function (data) {
                mdialog("Contact edit", data);
            });
        return false;
    }

    $(document).ready(function(){
        $(".listId").click(function(){
            if ($(this).attr("checked") && $(this).attr("rel")=="0"){
                malert('You cannot delete a contact with pending campaigns in the Dashboard.');
                return false;
            }
        });
    });

</script>
