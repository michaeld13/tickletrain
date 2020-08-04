<? $facebookaccount = $_SESSION["facebookaccount"]; ?>
<script type="text/javascript" src="/<?=ROOT_FOLDER?>js/cloud-zoom.1.0.2.min.js"></script>
<!-- <script src="/<?=ROOT_FOLDER?>js/jquery.ticker.js" type="text/javascript"></script> 
 <script src="/<?=ROOT_FOLDER?>js/site.js" type="text/javascript"></script> -->
<link rel="stylesheet" href="/<?=ROOT_FOLDER?>css/cloud-zoom.css" type="text/css" media="all"/>
<link href="/<?=ROOT_FOLDER?>css/ticker_style2.css?v=2011-04-25" rel="stylesheet" type="text/css" />
<link href="/<?=ROOT_FOLDER?>css/ticker-style.css" rel="stylesheet" type="text/css" /> 

<script>
    var datesArray = <?=json_encode($dates)?>;
    function preview(TaskIDs, MailIDs, PreviewType, subj,susp) {
        var title = subj;
        if (PreviewType != 'Mail' && PreviewType != 'MailAttach') {
            title = "Preview of Tickle scheduled";
        }
        var url = "<?=Url_Create('previewmail')?>?TaskID=" + TaskIDs + "&MailID=" + MailIDs + "&Mails=" + PreviewType;
		if (susp){
			url+= "&suspended=yes";
		}
      //  alert(url);
        $("#uploadFrame").src(url, function () {
            mdialog(title, $(this).contents().find("body").html(), false, {'height':500, 'width':800});
        });
    }
    
    
    function preview1(TaskIDs, MailIDs, PreviewType, subj) {
		preview(TaskIDs, MailIDs, PreviewType, subj,true);
    }
    
    
    function ChangeTask(TaskIDs, MailIDs) {
        $.get('<?=Url_Create('edittask')?>', { TaskID:TaskIDs, MailID:MailIDs }, function (data) {
            mdialog("Adjust send time", data);
        });
        return false;
    }
    function EditContact(ContactID) {
        $.get('<?=Url_Create('contactmanager')?>', { ContactID:ContactID, action:'EditContactForm', redirect:'home'}, function (data) {
            mdialog("Contact edit", data);
        });
        return false;
    }

    function FacebookFillContact(ContactID, fbUser) {
        $.get('<?=Url_Create('contactmanager')?>', { ContactID:ContactID, action:'EditContactForm', redirect:'home', fbid:fbUser.id}
            , function (data) {
                mdialog("Contact edit", data);
            });
        return false;
    }

    function ExtendedEditContact(ContactID, email) {
        $.get('<?=Url_Create('contactmanager')?>', { ContactID:ContactID, action:'EditContactForm', redirect:'home', email:email}
            , function (data) {
                mdialog("Contact edit", data);
            });
        return false;
    }

    function DeleteConfirm(url, subval) {
        var cancel = {text:'Cancel', click:function () {
            $(this).dialog('close')
        }};
        var deleteone = {text:'Delete', click:function () {
            window.location.href = url;
        }};
        var deleteall = {text:'Delete All', click:function () {
            window.location.href = url + "&DeleteAll=Y";
        }};
        var message = "<b>Delete</b> this Tickle?<br/><b>Delete All</b> will delete this Tickle and any follow up Tickles.";
        mdialog("Delete confirmation", message, [deleteone, deleteall, cancel]);
        return false;
    }
    function ApproveConfirm(url) {

        var cancel = {text:'Cancel', click:function () {
            $(this).dialog('close')
        }};
        var approve = {text:'Approve', click:function () {
            window.location.href = url;
        }};
        var approveAll = {text:'Approve All', click:function () {
            window.location.href = url + "&ApproveAll=Y";
        }};
        var message = "<b>Approve</b> this Tickle to be sent?<br/><b>Approve All</b> will approve this and follow-up Tickles to be sent.";
        mdialog("Approve confirmation", message, [approve, approveAll, cancel]);
        return false;
    }
    function PauseConfirm(url) {

        var cancel = {text:'Cancel', click:function () {
            $(this).dialog('close')
        }};
        var pause = {text:'Pause', click:function () {
            window.location.href = url + "&PauseAll=Y";
        }};
        //var pauseAll = {text:'Pause All', click: function() {window.location.href = url+"&PauseAll=Y";}};
        var message = "<b>Pause</b> this Tickle?";
        mdialog("Pause confirmation", message, [pause, cancel]);
        return false;
    }
    function UnPauseConfirm(url) {

        var cancel = {text:'Cancel', click:function () {
            $(this).dialog('close')
        }};
        var unpause = {text:'Unpause', click:function () {
            window.location.href = url;
        }};
        var unpauseAll = {text:'Unpause All', click:function () {
            window.location.href = url + "&UnPauseAll=Y";
        }};
        var message = "<b>Unpause</b> this Tickle so it will be sent?<br/><b>Unpause All</b> will send this and all follow-up Tickles.";
        mdialog("Send it! Confirmation", message, [unpause, unpauseAll, cancel]);
        return false;
    }
    
    
    
    
    
    function UnPgradeConfirm() {
         var url = "<?php echo $alertupgrademessage; ?>";
         var upgrade = {text:'Upgrade Plan', click:function () {
            window.location.href = url;
        }};
         var donotshow = {text:'Do not show this message again', click:function () {
            setcookie()
            $(this).dialog('close')     
            
        }};
        
        var message = 'We suggest you upgrade your plan. You are approaching your limit.';
        mdialog("Upgrade Plan", message, [upgrade, donotshow]);
        return false;
    }
    
    function setcookie(){
        $.cookie("example", null);
         var tickleid = <?php echo $tickleid; ?>;
         //alert(tickleid);
        // $("#dontshow").live("click", function(){
         $.cookie("example", tickleid, { expires: 15 });
       //  parent.$.fancybox.close();
        // });
        
    }
    function fancybox_close(){
    $('#fancy_outer').hide();
    $('#fancy_overlay').hide();
    $('#fancy_title').hide();
    $('#fancy_loading').hide();
    $('#fancy_ajax').remove();
   
   }
   
function tick(){
	$('#ticker li:first').animate({'opacity':0}, 500, function () { $(this).appendTo($('#ticker')).css('opacity', 1); });
}
   
    $(document).ready(function(){
          setInterval("location.reload(true)", 600000);
          setInterval(function(){ tick () }, 10000);

        var tickleid = <?php echo $tickleid; ?>; 
        var ccok = $.cookie("example");
        //alert(ccok);
        if(ccok == tickleid){
        }
        else{
            <?php if(isset ($alertupgrademessage)){ ?>
            UnPgradeConfirm();
        <?php } ?>
        
        }
        $("a.show_attach").fancybox({
                'transitionIn': 'elastic',
                'transitionOut': 'elastic',
                'speedIn': 600,
                'speedOut': 200,
                'overlayShow': true,
                'overlayColor': '#000',
                'onComplete': function (arg, cur) {
                    $('#fancybox-img').wrap(
                        $('<a>')
                        .attr('href', $(arg[cur]).attr('href'))
                        .addClass('cloud-zoom')
                        .attr('rel', "position: 'inside'")
                    );
                    $('.cloud-zoom').CloudZoom();
                }
                
        });
        
     $(".choosecompaign").click(function(){
           $('#checkbox').show();
      
     });   
    });
  
</script>
<iframe id="uploadFrame" name="uploadFrame" style="width:0px;height:0px" frameborder="0"></iframe>
  
<?php
//echo $paynowlink;
//echo '<pre>';
//print_r($subject);
//echo count($subject);
//echo '</pre>';
?>
<?php if (count($tasks) == 0 && !$search && !isset($suspendedorder)){ ?>

<div class="pages_block" style="margin-left:193px;">
    
        <div class="holder">
            <div class="num">
                <span><?=$cnt?> of <?php echo $campaignallowed; ?></span>
            </div>
            <a href="<?php echo $supportpage; ?>" class="btn_up">up</a>
        </div>
    </div>

<div class="main_holder register_area">
   
<script type="text/JavaScript" src="http://secure.tickletrain.com/modules/livehelp/scripts/jquery-latest.js"></script>

 
    
    <h1>Welcome to TickleTrain</h1>

    <div class="form">
        <div class="holder">
            <div class="frame">
                <div class="text-holder">
  <h2>It's As Easy As 1, 2, 3!!!</h2>
  <p>

<b><ol type="1">

<li>See how it works by creating a test email using your email program (Outlook, 
Gmail, etc.)<br><br></li>   
    <li>Simply copy and paste your  <a href="/<?=ROOT_FOLDER?>tickle/">Universal Tickle*</a> email address: <a href="mailto:<?php echo $mainemail;?>?bcc=universal+<?php echo $userName; ?>@tickletrain.com">universal+<?php echo $userName; ?>@tickletrain.com</a> into the bcc field before sending.<br><br></li>
	   <li>TickleTrain will do the rest!</li>
           
</ol> </b>


</p><div align="right">
<span style="color:darkblue">>> <a href="http://secure.tickletrain.com/knowledgebase.php?action=displayarticle&id=16">See Detailed Steps</a><br/><br/></span>
</div>                    <span class="txt">*This Tickle is scheduled to follow-up after 3 days. To approve the Tickle, simply “unpause” it in the Dashboard. <br><br>
You can setup your signature and email preferences in<a href ="/<?=ROOT_FOLDER?>myaccount/"> Settings</a>.</span>
					
Dashboard updates every 5 minutes.</span>
					
                   <!-- <span class="text-ty">Thank you!</span> -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    return;
}



if(isset ($_SESSION['allowedhere']) && isset($_SESSION['downgradecheck'])) { ?>

<form method="POST" action="https://client.tickletrain.com/dashboard/">   

<div class="main_holder">

    <?php if(isset ($morethenallowed)){ ?>
       <div class="heading"><h1><?php echo  $morethenallowed;?></h1></div>
    <?php } else { ?>
     <div class="heading"><h1>Downgrade</h1></div>
     <?php } ?>
     
     <div class="bar1" style="text-align:center">
    <!--  <p style="font-size: 13px; text-align: justify; line-height: 20px;">We're sorry...but we have'nt received your payment. To resume services please visit<a href = "<?php //echo $paynowlink; ?>"> ACCOUNT&nbsp;/&nbsp;MY INVOICES</a> and view your outstanding invoice. We suggest you to signup for recurrent billing to avoid possible issue in future. You can cancel at any time.</p>
      <a href = "<?php //echo $paynowlink; ?>"><img src="http://client.tickletrain.com/app/img/make-payment.png" alt="Pay Now" title="Pay Now"/></a> -->
      <p style="font-size: 13px; text-align: justify; line-height: 20px;">Your plan has been changed successfully. Your new plan offers <b><?php echo $_SESSION['allowedhere']; ?></b> campaigns but you have <b><?php echo $_SESSION['totalcampaign']; ?></b> currently active. Please select the <b><?php echo $_SESSION['allowedhere']; ?></b> campaigns you want to keep below and click the proceed button.</p>
     <p style="color: red; font-size: 13px; text-align: justify; line-height: 20px; margin: 0px auto;">Note: All other campaigns will be removed. If you decide you want to keep all current campaigns please <a href ="<?php echo $supportpage; ?>">upgrade your plan.</a></p><br/>
     </div>
     
     <?php     
        //echo '<pre>';
        //print_r($tasks);
        //print_r($maddress);
        
    //    echo '</pre>'; 
    //    die(); ?>
     
   

     
     <input type="hidden" name="baction" value="" id="bulkact"/>
<fieldset>
<table cellpadding="0" cellspacing="0" id="maintbl">
<thead>
<tr>
    <th style="width:135px" class="hsort<?=GetIf($sfld == 1 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 1 && $sord == 2, " sort_down", "")?>"
        rel="1"><input type="checkbox" id="selectAll"/>First Name<span class="sort"><img
        src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4" alt="" rel="1"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png"
        class="down" width="6"
        height="4" alt="" rel="1"/></span>
    </th>
    <th class="hsort<?=GetIf($sfld == 2 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 2 && $sord == 2, " sort_down", "")?>"
        rel="2"<?/* class="sort_up"*/?>>Last Name <span class="sort"><img
        src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6"
        height="4" alt="" rel="2"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6" height="4" alt="" rel="2"/></span>
    </th>
    <th
        class="hsort<?=GetIf($sfld == 3 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 3 && $sord == 2, " sort_down", "")?>"
        rel="3">E-mail <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6"
                                               height="4" rel="3"
                                               alt=""/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
                                                            class="down" width="6"
                                                            height="4" alt="" rel="3"/></span></th>
   
    <th
        class="hsort<?=GetIf($sfld == 4 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 4 && $sord == 2, " sort_down", "")?>"
        rel="4"<?/* class="sort_down"*/?>>Subject <span class="sort"><img
        src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6"
        height="4" alt="" rel="4"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6" height="4" alt="" rel="4"/></span>
    </th>
    <th class="hsort<?=GetIf($sfld == 5 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 5 && $sord == 2, " sort_down", "")?>"
        rel="5">Tickle <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up"
                                                    width="6" height="4"
                                                    alt="" rel="5"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6"
        height="4" alt="" rel="5"/></span></th>
    <th class="hsort<?=GetIf($sfld == 6 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 6 && $sord == 2, " sort_down", "")?>"
        rel="6">Schedule <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6"
                                                 height="4"
                                                 alt="" rel="6"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6"
        height="4" alt="" rel="6"/></span></th>
    
</tr>
</thead>
<tbody>
<?
$i = 0;
$ix = 1;
foreach ($tasks as $MailID => $rows) {
    $is_approve = false;
    $is_pause = false;
    $i = 0;
    ksort($rows);
    foreach ($rows as $rs) {
        $TickleContact = $rs['TickleContact'];
        $TickleTrainID = $rs['TickleTrainID'];
        $FollowTickleTrainID = $rs['FollowTickleTrainID'];
        $TApprove = $rs['TTApprove'];
        $IsApproved = $rs['Approve'];
        $IsPaused = $rs['Pause'];
        $actions = "";
        if ($FollowTickleTrainID) {
            $TApprove = $rs['FollowTApprove'];
        }
        if ($TApprove == 'Y' && $IsApproved != 'Y' && !$is_approve || $IsPaused == 'Y') {
            //$actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return ApproveConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Approve=Y') . "');\" class=\"ico_play_pause\">Approve</a></li>";
            $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return UnPauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&UnPause=Y') . "');\" class=\"ico_play_pause\">UnPause</a></li>";
            //$is_approve = true;
        }
        if (($TApprove == 'N' || $IsApproved == 'Y') && $IsPaused != 'Y' && !$is_pause) {
            $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return PauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Pause=Y') . "');\" class=\"ico_play_pause pause\">Pause</a></li>";
            //$is_pause = true;
        }
        /*if ($IsPaused == 'Y' && !$is_pause) {
            $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return UnPauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&UnPause=Y') . "');\" class=\"ico_play_pause\">UnPause</a></li>";
            //$is_pause = true;
        }*/
        
        $cid = $rs['ContactID'];
        $crow = $maddress[$cid];
        $FirstName = $crow['FirstName'];
        $LastName = $crow['LastName'];
        $EmailAddr = $crow['EmailID'];
    
        

        $attFiles = GetMailAttachments($rs['RawPath'], $rs['attachments']);
        if (count($attFiles) == 0) {
            $rs['attachments'] = '';
        }
        $basepath = preg_replace("/\.txt$/i", "/", $rs['RawPath']);
        $relpath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $basepath);
        $TaskCretedDate = convert_date($rs['TaskCretedDate']);
        //$TaskInitiateDate=convert_date($rs['TaskInitiateDate']);
        $TaskInitiateDate = convert_date(getlocaltime($rs['TaskGMDate'], $rs['TimeZone']));
        $time = strtotime($rs['TaskInitiateDate']);
        $TickleTime = date("h:i A", $time);
        $TickleDate = date("m-d-y", $time);
        $message = strip_tags(trim($rs['MessageHtml']));
        if ($message == "") {
            $message = trim($rs['Message']);
        }
        if (mb_strlen($message) > 200) {
            $message = mb_substr($message, 0, 200) . "...";
        }
        $img = "/" . GetRootFolder() . "images/1.jpg";?>
    <tr class="maintr<?=(($i) ? ' light tt' . $rs['MailID']:'')?>"<?=(($i)?' style="display:none"':'')?>>
        <td>
            <div class="txt"><nobr>
            <input type="checkbox" class="listId" name="mailid[]" value="<?=$rs['MailID']?>"
                   id="task<?=$rs['TaskID']?>"/>

                <label
                    for="task<?=$rs['TaskID']?>"><?=GetVal($crow['FirstName'], '')?></label></nobr>
            </div>
        </td>
        <td>
            <div class="txt">
                <nobr><?=GetVal($crow['LastName'], '')?></nobr>
            </div>
        </td>
        <td>
            <div class="txt">
                <?=GetVal($crow['EmailID'], '-')?>
            </div>
        </td>
       
        <td>
            <div class="txt">
                <?=GetVal($rs['Subject'], '(no-subject)')?>
            </div>
            <div class="excerpt_block block" style="display:none;">
                <p><?=$message?></p>
                <!--div class="attached"><a href="#">invoice.pdf</a>, <a href="#">quote.pdf</a></div-->
            </div>
        </td>
        <td>
            <div class="txt">
                <?=GetVal($rs['TickleName'], '-')?>
            </div>
        </td>
        <td>
            <div class="txt"><nobr><?=$TickleDate?>  <?=$TickleTime?></nobr></div>
        </td>
        
    </tr>
    <!--tr class="h_txt-holder"-->
    <tr class="childtr<?=(($i) ? ' light tt' . $rs['MailID']:'')?>"<?=(($i)?' style="display:none"':'')?>>
        <td><!--
            <ul class="h_txt first">
                <li>
                    <a href="#"
                       onclick="return ExtendedEditContact('<?=$cid?>','<?=$EmailAddr?>')">Edit</a>
                </li>
            </ul> -->
        </td>
        <td><!--
            <ul class="h_txt">
                <li>
                    <a href="#"
                       onclick="return ExtendedEditContact('<?=$cid?>','<?=$EmailAddr?>')">Edit</a>
                </li>
            </ul> -->
        </td>
        <td><!--
            <ul class="h_txt">
                <li><a href="#"
                       onclick="return ExtendedEditContact('<?=$cid?>','<?=$EmailAddr?>')">Edit</a></li>
                <li><a
                    href="<?=Url_Create('compose', 'Email=' . $EmailAddr . '&TaskID=' . $rs['TaskID'])?>">Send
                    e-mail</a></li>
            </ul>
        -->
        </td>
        <td>
            <ul class="h_txt">
                <li>
               
                <nobr><a href="#"
                       onclick="preview1('<?=$rs['TaskID']?>','<?=$rs['MailID']?>', 'Mail','<?=htmlspecialchars($rs['Subject'])?>');return false;">Original email</a>
                <?if (count($attFiles)>1) { ?>
                <a href="#"
                       onclick="preview1('<?=$rs['TaskID']?>','<?=$rs['MailID']?>', 'MailAttach','<?=htmlspecialchars($rs['Subject'])?>');return false;"><img src="/images/attachment.png" border="0"/></a>
                <? }?>
                    <?if (count($attFiles)==1) {
                        $imgArr = getimagesize($basepath.$attFiles[0]);
                        if ($imgArr){?>
                            <a href="<?=$relpath.$attFiles[0]?>" class="show_attach"><img src="/images/attachment.png" border="0"/></a>
                    <?}else{?>
                        <a href="#"
                           onclick="preview1('<?=$rs['TaskID']?>','<?=$rs['MailID']?>', 'MailAttach','<?=htmlspecialchars($rs['Subject'])?>');return false;"><img src="/images/attachment.png" border="0"/></a>
                    <? }}?>
                </nobr></li>
            </ul>
        </td>
        <td>
            <ul class="h_txt">
                <li><a href="#"
                       onclick="preview1('<?=$rs['TaskID']?>','<?=$rs['MailID']?>', 'Tickle');return false;">Preview
                    tickle</a></li>
            </ul>
        </td>
       <td></td>
    </tr>

        <?
        $i++;
    }
    //foreach
}//foreach
if (count($tasks) == 0) {
    ?>
<tr>
    <td colspan="8" align="center">No appropriate campaigns found</td>
</tr>
    <? }?>
</tbody>

</table>
<?if ($ps > 1) { ?>
<div class="pagination">
    <div class="holder">
        <ul>
            <?for ($j = 1; $j <= $ps; $j++) { ?>
            <li<?=(($j == $pg) ? ' class="current"' : '')?>><?=(($j == $pg) ? '<span>' : '<a href="?pg=' . $j . '&q=' . trim($_REQUEST['q']). '&qdate=' . trim($_REQUEST['qdate']). '&sort=' . trim($_REQUEST['sort']) . '">')?><?=$j?><?=(($j == $pg) ? '</span>' : '</a>')?></li>
            <? }?>
        </ul>
    </div>
</div>
    <? }  ?>
</fieldset>
     
  
     
     

     
     
     
     <!--
     
     <div class="form">
        <div class="holder">
            <div class="frame">
                <div class="text-holder">
                    <p>Your order has been suspended. And because of that you are not able to see your current compaign. We are suggesting you to either Pay or select five any five compaign from given below <br/> Note : Dowgrade button will downgrade your Plan to Basic <br/><ul>
                     <li><a href="#" class="choosecompaign">Choose Any Five</a></li><?php if(isset($paynowlink)) { ?><li><a href="<?php echo $paynowlink; ?>" class="pay">Pay Now</a></li><?php } ?>
                     </ul></p>
                     
                     
                     <div id ="checkbox" style="display:none; margin-top: 100px">
                         <form method="POST" action="" style="margin: -90px 0 0">
                             <ul>
                            <?php foreach($subject as $key=>$value) { ?>
                             <li style="width:200px;"><input type="checkbox" name="mailid[]"  value="<?php echo $key; ?>"/><?php echo trim($value); ?></li>
                            <?php } ?>
                             </ul>
                             <input type = "submit" name="choosecompaign" value="Save"/>
                         </form> 
                     </div>
                     
                    <span class="txt">*The Dashboard updates approximately every 5 minutes.</span>
                    <span class="text-ty">Thank you!</span>
                </div>
            </div>
        </div>
    </div> -->
</div>
    <div style="text-align:center"> 
    <input type="submit" name="downgradeplanhere" value="" class="btn_downgrade1"/>
    
    </div>
    </form>   
<?php 
return;
}























elseif(isset ($suspendedorder)) { ?>

<form method="POST" action="">   

<div class="main_holder">

    <?php if(isset ($morethenfive)){ ?>
       <div class="heading"><h1><?php echo  $morethenfive;?></h1></div>
    <?php } else { ?>
     <div class="heading"><h1>Order Suspended</h1></div>
     <?php } ?>
     
     <div class="bar1" style="text-align:center">
      <p style="font-size: 13px; text-align: justify; line-height: 20px;">We're sorry...but we have'nt received your payment. To resume services please visit<a href = "<?php echo $paynowlink; ?>"> ACCOUNT&nbsp;/&nbsp;MY INVOICES</a> and view your outstanding invoice. We suggest you to signup for recurrent billing to avoid possible issue in future. You can cancel at any time.</p>
      <a href = "<?php echo $supportpage; ?>"><img src="http://client.tickletrain.com/app/img/make-payment.png" alt="Pay Now" title="Pay Now"/></a>
      <p style="font-size: 13px; text-align: justify; line-height: 20px;">If you are unable to pay at this time,we can offer our free blue line plan,and you can continue to use tickle train. It is limited to 5 campaigns so any other follow-ups will be permanently deleted. If you wish to downgrade,select any 5 below and click downgrade. If you are canceling for another reason,we really appreciate your feedback. thanks for using tickle train!</p>
     </div>
     
     <?php     
        //echo '<pre>';
        //print_r($tasks);
        //print_r($maddress);
        
    //    echo '</pre>'; 
    //    die(); ?>
     
   

     
     <input type="hidden" name="baction" value="" id="bulkact"/>
<fieldset>
<table cellpadding="0" cellspacing="0" id="maintbl">
<thead>
<tr>
    <th style="width:135px" class="hsort<?=GetIf($sfld == 1 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 1 && $sord == 2, " sort_down", "")?>"
        rel="1"><input type="checkbox" id="selectAll"/>First Name<span class="sort"><img
        src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4" alt="" rel="1"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png"
        class="down" width="6"
        height="4" alt="" rel="1"/></span>
    </th>
    <th class="hsort<?=GetIf($sfld == 2 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 2 && $sord == 2, " sort_down", "")?>"
        rel="2"<?/* class="sort_up"*/?>>Last Name <span class="sort"><img
        src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6"
        height="4" alt="" rel="2"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6" height="4" alt="" rel="2"/></span>
    </th>
    <th
        class="hsort<?=GetIf($sfld == 3 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 3 && $sord == 2, " sort_down", "")?>"
        rel="3">E-mail <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6"
                                               height="4" rel="3"
                                               alt=""/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
                                                            class="down" width="6"
                                                            height="4" alt="" rel="3"/></span></th>
   
    <th
        class="hsort<?=GetIf($sfld == 4 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 4 && $sord == 2, " sort_down", "")?>"
        rel="4"<?/* class="sort_down"*/?>>Subject <span class="sort"><img
        src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6"
        height="4" alt="" rel="4"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6" height="4" alt="" rel="4"/></span>
    </th>
    <th class="hsort<?=GetIf($sfld == 5 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 5 && $sord == 2, " sort_down", "")?>"
        rel="5">Tickle <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up"
                                                    width="6" height="4"
                                                    alt="" rel="5"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6"
        height="4" alt="" rel="5"/></span></th>
    <th class="hsort<?=GetIf($sfld == 6 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 6 && $sord == 2, " sort_down", "")?>"
        rel="6">Schedule <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6"
                                                 height="4"
                                                 alt="" rel="6"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6"
        height="4" alt="" rel="6"/></span></th>
    
</tr>
</thead>
<tbody>
<?
$i = 0;
$ix = 1;
foreach ($tasks as $MailID => $rows) {
    $is_approve = false;
    $is_pause = false;
    $i = 0;
    ksort($rows);
    foreach ($rows as $rs) {
        $TickleContact = $rs['TickleContact'];
        $TickleTrainID = $rs['TickleTrainID'];
        $FollowTickleTrainID = $rs['FollowTickleTrainID'];
        $TApprove = $rs['TTApprove'];
        $IsApproved = $rs['Approve'];
        $IsPaused = $rs['Pause'];
        $actions = "";
        if ($FollowTickleTrainID) {
            $TApprove = $rs['FollowTApprove'];
        }
        if ($TApprove == 'Y' && $IsApproved != 'Y' && !$is_approve || $IsPaused == 'Y') {
            //$actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return ApproveConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Approve=Y') . "');\" class=\"ico_play_pause\">Approve</a></li>";
            $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return UnPauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&UnPause=Y') . "');\" class=\"ico_play_pause\">UnPause</a></li>";
            //$is_approve = true;
        }
        if (($TApprove == 'N' || $IsApproved == 'Y') && $IsPaused != 'Y' && !$is_pause) {
            $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return PauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Pause=Y') . "');\" class=\"ico_play_pause pause\">Pause</a></li>";
            //$is_pause = true;
        }
        /*if ($IsPaused == 'Y' && !$is_pause) {
            $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return UnPauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&UnPause=Y') . "');\" class=\"ico_play_pause\">UnPause</a></li>";
            //$is_pause = true;
        }*/
        
        $cid = $rs['ContactID'];
        $crow = $maddress[$cid];
        $FirstName = $crow['FirstName'];
        $LastName = $crow['LastName'];
        $EmailAddr = $crow['EmailID'];
    
        

        $attFiles = GetMailAttachments($rs['RawPath'], $rs['attachments']);
        if (count($attFiles) == 0) {
            $rs['attachments'] = '';
        }
        $basepath = preg_replace("/\.txt$/i", "/", $rs['RawPath']);
        $relpath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $basepath);
        $TaskCretedDate = convert_date($rs['TaskCretedDate']);
        //$TaskInitiateDate=convert_date($rs['TaskInitiateDate']);
        $TaskInitiateDate = convert_date(getlocaltime($rs['TaskGMDate'], $rs['TimeZone']));
        $time = strtotime($rs['TaskInitiateDate']);
        $TickleTime = date("h:i A", $time);
        $TickleDate = date("m-d-y", $time);
        $message = strip_tags(trim($rs['MessageHtml']));
        if ($message == "") {
            $message = trim($rs['Message']);
        }
        if (mb_strlen($message) > 200) {
            $message = mb_substr($message, 0, 200) . "...";
        }
        $img = "/" . GetRootFolder() . "images/1.jpg";?>
    <tr class="maintr<?=(($i) ? ' light tt' . $rs['MailID']:'')?>"<?=(($i)?' style="display:none"':'')?>>
        <td>
            <div class="txt"><nobr>
            <input type="checkbox" class="listId" name="mailid[]" value="<?=$rs['MailID']?>"
                   id="task<?=$rs['TaskID']?>"/>

                <label
                    for="task<?=$rs['TaskID']?>"><?=GetVal($crow['FirstName'], '')?></label></nobr>
            </div>
        </td>
        <td>
            <div class="txt">
                <nobr><?=GetVal($crow['LastName'], '')?></nobr>
            </div>
        </td>
        <td>
            <div class="txt">
                <?=GetVal($crow['EmailID'], '-')?>
            </div>
        </td>
       
        <td>
            <div class="txt">
                <?=GetVal($rs['Subject'], '(no-subject)')?>
            </div>
            <div class="excerpt_block block" style="display:none;">
                <p><?=$message?></p>
                <!--div class="attached"><a href="#">invoice.pdf</a>, <a href="#">quote.pdf</a></div-->
            </div>
        </td>
        <td>
            <div class="txt">
                <?=GetVal($rs['TickleName'], '-')?>
            </div>
        </td>
        <td>
            <div class="txt"><nobr><?=$TickleDate?>  <?=$TickleTime?></nobr></div>
        </td>
        
    </tr>
    <!--tr class="h_txt-holder"-->
    <tr class="childtr<?=(($i) ? ' light tt' . $rs['MailID']:'')?>"<?=(($i)?' style="display:none"':'')?>>
        <td><!--
            <ul class="h_txt first">
                <li>
                    <a href="#"
                       onclick="return ExtendedEditContact('<?=$cid?>','<?=$EmailAddr?>')">Edit</a>
                </li>
            </ul> -->
        </td>
        <td><!--
            <ul class="h_txt">
                <li>
                    <a href="#"
                       onclick="return ExtendedEditContact('<?=$cid?>','<?=$EmailAddr?>')">Edit</a>
                </li>
            </ul> -->
        </td>
        <td><!--
            <ul class="h_txt">
                <li><a href="#"
                       onclick="return ExtendedEditContact('<?=$cid?>','<?=$EmailAddr?>')">Edit</a></li>
                <li><a
                    href="<?=Url_Create('compose', 'Email=' . $EmailAddr . '&TaskID=' . $rs['TaskID'])?>">Send
                    e-mail</a></li>
            </ul>
        -->
        </td>
        <td>
            <ul class="h_txt">
                <li>
               
                <nobr><a href="#"
                       onclick="preview1('<?=$rs['TaskID']?>','<?=$rs['MailID']?>', 'Mail','<?=htmlspecialchars($rs['Subject'])?>');return false;">Original email</a>
                <?if (count($attFiles)>1) { ?>
                <a href="#"
                       onclick="preview1('<?=$rs['TaskID']?>','<?=$rs['MailID']?>', 'MailAttach','<?=htmlspecialchars($rs['Subject'])?>');return false;"><img src="/images/attachment.png" border="0"/></a>
                <? }?>
                    <?if (count($attFiles)==1) {
                        $imgArr = getimagesize($basepath.$attFiles[0]);
                        if ($imgArr){?>
                            <a href="<?=$relpath.$attFiles[0]?>" class="show_attach"><img src="/images/attachment.png" border="0"/></a>
                    <?}else{?>
                        <a href="#"
                           onclick="preview1('<?=$rs['TaskID']?>','<?=$rs['MailID']?>', 'MailAttach','<?=htmlspecialchars($rs['Subject'])?>');return false;"><img src="/images/attachment.png" border="0"/></a>
                    <? }}?>
                </nobr></li>
            </ul>
        </td>
        <td>
            <ul class="h_txt">
                <li><a href="#"
                       onclick="preview1('<?=$rs['TaskID']?>','<?=$rs['MailID']?>', 'Tickle');return false;">Preview
                    tickle</a></li>
            </ul>
        </td>
       <td></td>
    </tr>

        <?
        $i++;
    }
    //foreach
}//foreach
if (count($tasks) == 0) {
    ?>
<tr>
    <td colspan="8" align="center">No appropriate campaigns found</td>
</tr>
    <? }?>
</tbody>

</table>
<?if ($ps > 1) { ?>
<div class="pagination">
    <div class="holder">
        <ul>
            <?for ($j = 1; $j <= $ps; $j++) { ?>
            <li<?=(($j == $pg) ? ' class="current"' : '')?>><?=(($j == $pg) ? '<span>' : '<a href="?pg=' . $j . '&q=' . trim($_REQUEST['q']). '&qdate=' . trim($_REQUEST['qdate']). '&sort=' . trim($_REQUEST['sort']) . '">')?><?=$j?><?=(($j == $pg) ? '</span>' : '</a>')?></li>
            <? }?>
        </ul>
    </div>
</div>
    <? }  ?>
</fieldset>
     
  
     
     

     
     
     
     <!--
     
     <div class="form">
        <div class="holder">
            <div class="frame">
                <div class="text-holder">
                    <p>Your order has been suspended. And because of that you are not able to see your current compaign. We are suggesting you to either Pay or select five any five compaign from given below <br/> Note : Dowgrade button will downgrade your Plan to Basic <br/><ul>
                     <li><a href="#" class="choosecompaign">Choose Any Five</a></li><?php if(isset($paynowlink)) { ?><li><a href="<?php echo $paynowlink; ?>" class="pay">Pay Now</a></li><?php } ?>
                     </ul></p>
                     
                     
                     <div id ="checkbox" style="display:none; margin-top: 100px">
                         <form method="POST" action="" style="margin: -90px 0 0">
                             <ul>
                            <?php foreach($subject as $key=>$value) { ?>
                             <li style="width:200px;"><input type="checkbox" name="mailid[]"  value="<?php echo $key; ?>"/><?php echo trim($value); ?></li>
                            <?php } ?>
                             </ul>
                             <input type = "submit" name="choosecompaign" value="Save"/>
                         </form> 
                     </div>
                     
                    <span class="txt">*The Dashboard updates approximately every 5 minutes.</span>
                    <span class="text-ty">Thank you!</span>
                </div>
            </div>
        </div>
    </div> -->
</div>
    <div style="text-align:center"> 
    <input type="submit" name="choosecompaign" value="" class="btn_downgrade"/>
    <div style="color: red; text-transform: lowercase; font-size: 11px; margin: 0px auto;">NOTE: DOWNGRADE BUTTON WILL DOWNGRADE YOUR PLAN TO BASIC AND ALL CAMPAIGNS OTHER THEN THE 5 SELECTED ABOVE WILL BE PERMANENTLY DELETED</div>
    </div>
    </form>   
<?php 
return;
} ?>
<div class="main_holder">
<div class="heading"><h1>Dashboard</h1>
<?php 
 
 $percentahecampaign1 = ($campaignallowed/100)*$warningthresold;
 $percentahecampaign = floor($campaignallowed-$percentahecampaign1);
 if($cnt >= $percentahecampaign){ ?>
    
    <div class="pages_block2">
        <div class="holder">
            <div class="num">
                <span><?=$cnt?> of <?php echo $campaignallowed; ?></span>
            </div>
            <a href="<?php echo $supportpage; ?>" class="btn_up2">up</a>
        </div>
    </div>
    
  <?php } else { ?>
    <div class="pages_block">
        <div class="holder">
            <div class="num">
                <span><?=$cnt?> of <?php echo $campaignallowed; ?></span>
            </div>
            <a href="<?php echo $supportpage; ?>" class="btn_up">up</a>
        </div>
    </div>
 <?php } ?>   
    <? if (!isset($_SESSION['access_token']) && $_SESSION['TickleID'] != "") { ?>
        <div class="fb_block">
            <div id="fb-root"></div>
            <div id="facebookerror" style="margin-left: 5px; display: inline-block">Get more information about your
                contacts by <a
                    href="/<?=ROOT_FOLDER?>fb/settoken/">logging into</a> Facebook.
                    <?php if(isset ($alertupgrademessage)){ ?>
                   <!-- Your campaign limit is either complete or near about complete. So we suggest you to <a href = "<?php echo $alertupgrademessage; ?>">Upgrade product</a> -->
                    <?php } ?>
            </div>
        </div>
    <? } ?>

</div>
<div class="bar">
    <div class="align_left">
        <form id="bulkActsApply">
            <fieldset>
                <select id="bactionSelect">
                    <option value="">Bulk actions</option>
                    <option value="delete">Delete</option>
                    <!--option value="approve">Approve</option-->
                    <option value="pause">Pause</option>
                    <option value="unpause">Unpause</option>
                </select>
                <input type="submit" value="Apply" class="btn_apply"/>
            </fieldset>
        </form>
    </div>
    <div class="align_right">
        <ul class="bar_buttons">
            <li><a href="#" class="excerpt_all">excerpt</a></li>
            <li><a href="#" class="expand_all">expand</a></li>
        </ul>
        <form>
            <fieldset>
                <select id="dateFilter" name="qdate" onchange="this.form.submit()">
                    <option value="">Show all dates</option>
                    <?foreach ($tfilter as $ind => $frow): ?>
                    <option
                        value="<?=$ind?>"<?=getIf(@trim($_REQUEST['qdate']) == $ind, ' selected', '')?>><?=$frow?></option>
                    <? endforeach?>
                </select>
                <?/*span class="input_text"><input type="text" id="dateFilter" name="qdate"
                                                value="<?=@trim($_REQUEST['qdate'])?>"
                                                placeholder="Show all dates"/></span*/?>
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
    <th style="width:135px" class="hsort<?=GetIf($sfld == 1 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 1 && $sord == 2, " sort_down", "")?>"
        rel="1"><input type="checkbox" id="selectAll"/>First Name<span class="sort"><img
        src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6" height="4" alt="" rel="1"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png"
        class="down" width="6"
        height="4" alt="" rel="1"/></span>
    </th>
    <th class="hsort<?=GetIf($sfld == 2 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 2 && $sord == 2, " sort_down", "")?>"
        rel="2"<?/* class="sort_up"*/?>>Last Name <span class="sort"><img
        src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6"
        height="4" alt="" rel="2"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6" height="4" alt="" rel="2"/></span>
    </th>
    <th
        class="hsort<?=GetIf($sfld == 3 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 3 && $sord == 2, " sort_down", "")?>"
        rel="3">E-mail <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6"
                                               height="4" rel="3"
                                               alt=""/><img src="/<?=GetRootFolder()?>images/arrow_down.png"
                                                            class="down" width="6"
                                                            height="4" alt="" rel="3"/></span></th>
    <th>Social</th>
    <th
        class="hsort<?=GetIf($sfld == 4 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 4 && $sord == 2, " sort_down", "")?>"
        rel="4"<?/* class="sort_down"*/?>>Subject <span class="sort"><img
        src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6"
        height="4" alt="" rel="4"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6" height="4" alt="" rel="4"/></span>
    </th>
    <th class="hsort<?=GetIf($sfld == 5 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 5 && $sord == 2, " sort_down", "")?>"
        rel="5">Tickle <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up"
                                                    width="6" height="4"
                                                    alt="" rel="5"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6"
        height="4" alt="" rel="5"/></span></th>
    <th class="hsort<?=GetIf($sfld == 6 && $sord == 1, " sort_up", "")?><?=GetIf($sfld == 6 && $sord == 2, " sort_down", "")?>"
        rel="6">Schedule <span class="sort"><img src="/<?=GetRootFolder()?>images/arrow_up.png" class="up" width="6"
                                                 height="4"
                                                 alt="" rel="6"/><img
        src="/<?=GetRootFolder()?>images/arrow_down.png" class="down" width="6"
        height="4" alt="" rel="6"/></span></th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?
$i = 0;
$ix = 1;
foreach ($tasks as $MailID => $rows) {
    $is_approve = false;
    $is_pause = false;
    $i = 0;
    ksort($rows);
    foreach ($rows as $rs) {
        
       // echo '<pre>';
       // print_r($rs);
       // echo '</pre>';
       //echo $rs['TimeZone']; 
       //echo date_default_timezone_get(); 
        
        $TickleContact = $rs['TickleContact'];
        $TickleTrainID = $rs['TickleTrainID'];
        $FollowTickleTrainID = $rs['FollowTickleTrainID'];
        $TApprove = $rs['TTApprove'];
        $IsApproved = $rs['Approve'];
        $IsPaused = $rs['Pause'];
        $actions = "";
        if ($FollowTickleTrainID) {
            $TApprove = $rs['FollowTApprove'];
        }
        if ($TApprove == 'Y' && $IsApproved != 'Y' && !$is_approve || $IsPaused == 'Y') {
            //$actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return ApproveConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Approve=Y') . "');\" class=\"ico_play_pause\">Approve</a></li>";
            $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return UnPauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&UnPause=Y') . "');\" class=\"ico_play_pause\">UnPause</a></li>";
            //$is_approve = true;
        }
        if (($TApprove == 'N' || $IsApproved == 'Y') && $IsPaused != 'Y' && !$is_pause) {
            $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return PauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Pause=Y') . "');\" class=\"ico_play_pause pause\">Pause</a></li>";
            //$is_pause = true;
        }
        /*if ($IsPaused == 'Y' && !$is_pause) {
            $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return UnPauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&UnPause=Y') . "');\" class=\"ico_play_pause\">UnPause</a></li>";
            //$is_pause = true;
        }*/
        $cid = $rs['ContactID'];
        $crow = $maddress[$cid];
        $FirstName = $crow['FirstName'];
        $LastName = $crow['LastName'];
        $EmailAddr = $crow['EmailID'];

        $attFiles = GetMailAttachments($rs['RawPath'], $rs['attachments']);
        if (count($attFiles) == 0) {
            $rs['attachments'] = '';
        }
        $basepath = preg_replace("/\.txt$/i", "/", $rs['RawPath']);
        $relpath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $basepath);
        $TaskCretedDate = convert_date($rs['TaskCretedDate']);
        //$TaskInitiateDate=convert_date($rs['TaskInitiateDate']);
        $TaskInitiateDate = convert_date(getlocaltime($rs['TaskGMDate'], $rs['TimeZone']));
        $time = strtotime($rs['TaskInitiateDate']);
        $TickleTime = date("h:i A", $time);
        $TickleDate = date("m-d-y", $time);
        $message = strip_tags(trim($rs['MessageHtml']));
        if ($message == "") {
            $message = trim($rs['Message']);
        }
        if (mb_strlen($message) > 200) {
            $message = mb_substr($message, 0, 200) . "...";
        }
        $img = "/" . GetRootFolder() . "images/1.jpg";?>
    <tr class="maintr<?=(($i) ? ' light tt' . $rs['MailID']:'')?>"<?=(($i)?' style="display:none"':'')?>>
        <td>
            <div class="txt"><nobr>
            <input type="checkbox" class="listId" name="TaskID[]" value="<?=$rs['TaskID']?>"
                   id="task<?=$rs['TaskID']?>"/>

                <label
                    for="task<?=$rs['TaskID']?>"><?=GetVal($crow['FirstName'], '')?></label></nobr>
            </div>
        </td>
        <td>
            <div class="txt">
                <nobr><?=GetVal($crow['LastName'], '')?></nobr>
            </div>
        </td>
        <td>
            <div class="txt">
                <?=GetVal($crow['EmailID'], '-')?>
            </div>
        </td>
        <td>
            <div class="txt">
                <ul class="social_nw">
                    <? if (isset($_SESSION['access_token'])) { ?>
                    <li id="fb-<?=$rs['TaskID']?>" style="display:none"></li>
                    <script>$('#fb-<?=$rs['TaskID']?>').load('<?=Url_Create("fb/image", "email=" . $EmailAddr . "&cid=" . $cid)?>', function (data) {
                        if (data != '') {
                            $(this).show()
                        }
                    })</script>
                    <?}?>
                    <?/*li><img src="/<?=GetRootFolder()?>images/ico_linked_in.png" width="13" height="13"
                                         alt=""/></li*/?>
                </ul>
            </div>
        </td>
        <td>
            <div class="txt">
                <?=GetVal($rs['Subject'], '(no-subject)')?>
            </div>
            <div class="excerpt_block block" style="display:none;">
                <p><?=$message?></p>
                <!--div class="attached"><a href="#">invoice.pdf</a>, <a href="#">quote.pdf</a></div-->
            </div>
        </td>
        <td>
            <div class="txt">
                <?=GetVal($rs['TickleName'], '-')?>
            </div>
        </td>
        <td>
            <div class="txt"><nobr><?=$TickleDate?>  <?=$TickleTime?></nobr></div>
        </td>
        <td>
            <div class="txt">
                <ul class="icons">
                    <li><a href="javascript:void(0);"
                           onclick="javascript:return DeleteConfirm('<?=Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Delete=Y')?>','<?php echo $class?>');"
                           class="ico_basket">Delete</a></li>
                    <?=$actions?><?if (!$i && count($tasks[$rs['MailID']]) > 1) { ?>
                    <li><a href="#" class="ico_expand" id="ex<?=$rs['MailID']?>">expand/exerpt</a>
                    </li><? }?></ul>
            </div>
        </td>
    </tr>
    <!--tr class="h_txt-holder"-->
    <tr class="childtr<?=(($i) ? ' light tt' . $rs['MailID']:'')?>"<?=(($i)?' style="display:none"':'')?>>
        <td>
           <ul class="h_txt first">
                <li>
                    <a href="#"
                       onclick="return ExtendedEditContact('<?=$cid?>','<?=$EmailAddr?>')">Edit</a>
                </li>
            </ul>
        </td>
        <td>
           <ul class="h_txt">
                <li>
                    <a href="#"
                       onclick="return ExtendedEditContact('<?=$cid?>','<?=$EmailAddr?>')">Edit</a>
                </li>
            </ul>
        </td>
        <td>
           <ul class="h_txt">
                <li><a href="#"
                       onclick="return ExtendedEditContact('<?=$cid?>','<?=$EmailAddr?>')">Edit</a></li>
                <li><a
                    href="<?=Url_Create('compose', 'Email=' . $EmailAddr . '&TaskID=' . $rs['TaskID'])?>">Send
                    e-mail</a></li>
            </ul>
        </td>
        <td>
        </td>
        <td>
            <ul class="h_txt">
                <li><nobr><a href="#"
                       onclick="preview('<?=$rs['TaskID']?>','<?=$rs['MailID']?>', 'Mail','<?=htmlspecialchars($rs['Subject'])?>');return false;">Original email</a>
                <?if (count($attFiles)>1) { ?>
                <a href="#"
                       onclick="preview('<?=$rs['TaskID']?>','<?=$rs['MailID']?>', 'MailAttach','<?=htmlspecialchars($rs['Subject'])?>');return false;"><img src="/images/attachment.png" border="0"/></a>
                <? }?>
                    <?if (count($attFiles)==1) {
                        $imgArr = getimagesize($basepath.$attFiles[0]);
                        if ($imgArr){?>
                            <a href="<?=$relpath.$attFiles[0]?>" class="show_attach"><img src="/images/attachment.png" border="0"/></a>
                    <?}else{?>
                        <a href="#"
                           onclick="preview('<?=$rs['TaskID']?>','<?=$rs['MailID']?>', 'MailAttach','<?=htmlspecialchars($rs['Subject'])?>');return false;"><img src="/images/attachment.png" border="0"/></a>
                    <? }}?>
                </nobr></li>
            </ul>
        </td>
        <td>
            <ul class="h_txt">
                <li><a href="#"
                       onclick="preview('<?=$rs['TaskID']?>','<?=$rs['MailID']?>', 'Tickle');return false;">Preview
                    tickle</a></li>
            </ul>
        </td>
        <td>
          <ul class="h_txt">
                <li><a href="#" onclick="return ChangeTask('<?=$rs['TaskID']?>','<?=$rs['MailID']?>');">Edit</a>
                </li>
            </ul>
        </td>
        <td>
        </td>
    </tr>

        <?
        $i++;
    }
    //foreach
}//foreach
if (count($tasks) == 0) {
    ?>
<tr>
    <td colspan="8" align="center">No appropriate campaigns found</td>
</tr>
    <? }?>
</tbody>

</table>
<?if ($ps > 1) { ?>
<div class="pagination">
    <div class="holder">
        <ul>
            <?for ($j = 1; $j <= $ps; $j++) { ?>
            <li<?=(($j == $pg) ? ' class="current"' : '')?>><?=(($j == $pg) ? '<span>' : '<a href="?pg=' . $j . '&q=' . trim($_REQUEST['q']). '&qdate=' . trim($_REQUEST['qdate']). '&sort=' . trim($_REQUEST['sort']) . '">')?><?=$j?><?=(($j == $pg) ? '</span>' : '</a>')?></li>
            <? }?>
        </ul>
    </div>
</div>
    <? }?>
</fieldset>
</form>
    </div>
 <?php 
$email = $mainemail;
$autoauthkey = "abcXYZ123";
$timestamp = time(); # Get current timestamp
$hash = sha1($email . $timestamp . $autoauthkey);
 ?>   
  <!--   <div style="margin-left:180px;">    
 <ul id="js-news" class="js-hidden">
          
                
		<li class="news-item"></li>
               
             
 </ul>
      </div> -->

    
 
  
  
<?/*if (!isset($_SESSION['facebookalert'])) {
    $_SESSION['facebookalert'] = 'yes';
?>
<iframe id="fblogin" width="0" height="0" frameborder="0" src="/<?=ROOT_FOLDER?>fb/gettoken/" onload="fberr()"></iframe>
<script type="text/javascript">
    function fberr() {
        try {
            var str = $("#fblogin").contents().find("div").attr('id');
        }
        catch (e) {
            var str = 'error';
        }
        if (str == 'facebookflag') {
            window.location.reload();
        }
    }
</script>
    <? }*/
