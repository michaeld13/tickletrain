<style>
#FollowUpForm{
height:auto !important;
}

.hide{
	display: none;
}
</style>
<script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/jquery.MultiFile.js"></script>
<script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/jquery.cookie.js"></script>

<script language="javascript">

 /*$(document).ready(function() {
		var sw = "<?php echo 'https://'.$_SERVER['SERVER_NAME'];?>/ckeditorn/mystyles.css";
		//console.log(sw);
        CKEDITOR.env.isCompatible = true;
            CKEDITOR.replace('TickleMailContent',{
				
				toolbar: [
			{ name: 'document', items: [ 'Print' ] },
			{ name: 'clipboard', items: [ 'Undo', 'Redo' ] },
			{ name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
			{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			{ name: 'links', items: [ 'Link', 'Unlink' ] },
			{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
			{ name: 'insert', items: [ 'Image', 'Table' ] },
			{ name: 'tools', items: [ 'Maximize' ] },
			{ name: 'editing', items: [ 'Scayt' ] }
		],

			contentsCss: [ 'https://cdn.ckeditor.com/4.8.0/full-all/contents.css',"<?php echo 'https://'.$_SERVER['SERVER_NAME'];?>/ckeditorn/mystyles.css" ],
			bodyClass: 'document-editor',
			
			});
        

		CKEDITOR.disableAutoInline = true;
		

    });*/
	
	/* $(document).ready(function() {
		  CKEDITOR.env.isCompatible = true;
	CKEDITOR.replace('TickleMailContent',{
				
				toolbar: [
			{ name: 'document', items: [ 'Print' ] },
			{ name: 'clipboard', items: [ 'Undo', 'Redo' ] },
			{ name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
			{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			{ name: 'links', items: [ 'Link', 'Unlink' ] },
			{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
			{ name: 'insert', items: [ 'Image', 'Table' ] },
			{ name: 'tools', items: [ 'Maximize' ] },
			{ name: 'editing', items: [ 'Scayt' ] }
		],
			
			});
	 }); */
	
    function togglesettings(id){
		$("#reply_tracking_"+id).toggle();

	}
    function showadvance(id){
		$("#advance_imap_setting_"+id).toggle();
	}
	function showhide_setting(val){
		$("#secondary_holder").children().hide(); 
		var res = val.split("_");
		if(val=='mainemail'){
			$('#primary_reply').show();
			$('#email_type').val('primary');
		}
		else{
			$('#primary_reply').hide();
			$('#'+val).show();
			$('#email_type').val('secondary');
		}
	}
    var timezones = <?= json_encode($timezones); ?>;
    $(document).ready(function() {        
        $(".ico_info").click(function() {
            //  alert('gfdgf');
        });

        /*$("#imap_settings").click(function(){
            $("#reply_tracking").toggle();
        })*/

        $("input[name='delete_campaign_on_reply']").click(function(){
             if ($("input[name='delete_campaign_on_reply']").prop('checked')) {
             if($("input[name='imap_host']").val() == "" || $("input[name='imap_userame']").val() == "" || $("input[name='imap_passowrd']").val() == ""){
              $("#reply_tracking").show();
              $.cookie("delete_campaign_on_reply", "delete_campaign_on_reply", {expires: 10});
              mcalert("IMAP Settings Required","To use this feature, please enter you mail server details and test your IMAP connection.");
              return false;
             }else{
                  $("input[name='do_not_track']").prop('checked', false);
               //  $("#notify_campaign_deleted").show();
             }
          }
        })

        $("input[name='do_not_track']").click(function(){
            $("input[name='delete_campaign_on_reply']").prop('checked', false);
            $("input[name='notify_when_reply_received']").prop('checked', false);
        })

        $("input[name='notify_when_reply_received']").click(function(){
            if ($("input[name='notify_when_reply_received']").prop('checked')) {
             if($("input[name='imap_host']").val() == "" || $("input[name='imap_userame']").val() == "" || $("input[name='imap_passowrd']").val() == ""){
             $("#reply_tracking").show();
             $.cookie("notify_when_reply_received", "notify_when_reply_received", {expires: 10});
             mcalert("IMAP Settings Required","To use this feature, please enter you mail server details and test your IMAP connection.");
             return false;
            }else{
                $("input[name='do_not_track']").prop('checked', false);
            }
          }
        })

        $("#advanced_imap_settings").click(function(){
            $("#advance_imap_setting").toggle();
        })
    });

//
//

    function onclickcheck(id,type){
	
	 var res = id.split("_");
		if(type=='del'){
			 if($("input[name='sec_"+res[1]+"_imap_host']").val() == "" || $("input[name='sec_"+res[1]+"_imap_userame']").val() == "" || $("input[name='sec_"+res[1]+"_imap_passowrd']").val() == "")       {
			     // $("#reply_tracking").show();
			      $.cookie("sec_"+res[1]+"_delete_campaign_on_reply", "delete_campaign_on_reply", {expires: 10});
			      mcalert("IMAP Settings Required","To use this feature, please enter you mail server details and test your IMAP connection.");
			      return false;
			     }else{
			          $("input[name='sec_"+res[1]+"_do_not_track']").prop('checked', false);
				return true;
			       //  $("#notify_campaign_deleted").show();
			 }
		}
		else if(type=='notify'){
	            if($("input[name='sec_"+res[1]+"_imap_host']").val() == "" || $("input[name='sec_"+res[1]+"_imap_userame']").val() == "" || $("input[name='sec_"+res[1]+"_imap_passowrd']").val() == ""){
	             	//$("#reply_tracking").show();
		            $.cookie("sec_"+res[1]+"_notify_when_reply_received", "notify_when_reply_received", {expires: 10});
		            mcalert("IMAP Settings Required","To use this feature, please enter you mail server details and test your IMAP connection.");
		            return false;
	            }else{
	                $("input[name='sec_"+res[1]+"_do_not_track']").prop('checked', false);
					return true;
	            }
		}
		else{
		    $("input[name='sec_"+res[1]+"_delete_campaign_on_reply']").prop('checked', false);
	        $("input[name='sec_"+res[1]+"_notify_when_reply_received']").prop('checked', false);
		    return true;
		}
    }



    function sendTest1(frm,type,id) {
       // var plan = "<?php //echo $user_details['Plan']; ?>";
       // var blueplanbarning = "<?php //echo $user_details['blueplanbarning']; ?>";
        
	if(type=='primary'){
		if($("input[name='imap_host']").val() === ""){
		  mcalert("Error","Please verify that your IMAP server name, username and password are correct and try again.");
		  return false
		}
		if($("input[name='imap_userame']").val() === ""){
		  mcalert("Error","Please verify that your IMAP server name, username and password are correct and try again.");
		  return false
		}
		if($("input[name='imap_passowrd']").val() === ""){
		  mcalert("Error","Please verify that your IMAP server name, username and password are correct and try again.");
		  return false
		}

	       
		    $("#dmtoemail").val($("#testemail").val());
		    $(this).dialog('close');
		    var sr = serialize1($(frm));//$(frm).serializeArray();//
		    sr+="&testsmtp=1&email_type=primary";
		   // alert(sr);
		    $.post("<?=Url_Create('addtickle')?>", sr, function (data) {
		        if (data == "true") {
		            $('#forcepress').val('0');
		            $("#imap_connection_approved").val("yes");
		              if ($.cookie("delete_campaign_on_reply") == "delete_campaign_on_reply") {
		                $("input[name='delete_campaign_on_reply']").prop('checked', true);
		                 $("input[name='do_not_track']").prop('checked', false);
		                $.removeCookie("delete_campaign_on_reply");
		            }

		            if ($.cookie("notify_when_reply_received") == "notify_when_reply_received") {
		                $("input[name='notify_when_reply_received']").prop('checked', true);
		                $("input[name='do_not_track']").prop('checked', false);
		                $.removeCookie("notify_when_reply_received");
		            }

		          mcalert("Notification", "Congratulations!  We were able to connect to your mail server.  You can now select tracking options for your Tickles.  Be sure and click Save at the bottom of this page to save your settings.");
		           } else {
		            var index1 = data.indexOf("'IMAP Error:");
		            var index2 = data.indexOf("' in");
		            var org_data = data.substring(index1,index2);

		            if ($.cookie("delete_campaign_on_reply") == "delete_campaign_on_reply") {
		                $.removeCookie("delete_campaign_on_reply");
		            }
		           if ($.cookie("notify_when_reply_received") == "notify_when_reply_received") {
		                $.removeCookie("notify_when_reply_received");
		            }

		            $("input[name='delete_campaign_on_reply']").prop('checked', false);
		            $("input[name='notify_when_reply_received']").prop('checked', false);

		            $('#forcepress').val('0');
		            var txt = "Connection failed. Verify your settings and password and try again. You may need to also check under (advanced settings) and make additional updates.\n\
		            <br><br>If you feel your password and settings are correct, please copy the error message below and check with your email provider. Your email provider can give you the proper settings to try again. Most common reason for a connection error is wrong port number under advanced settings.<br><br>Error Message: "+data+" ";
		            mcalert("Notification", txt);
		        }
		    });
	   }

	   else{
		if($("input[name='sec_"+id+"_imap_host']").val() === ""){
		  mcalert("Error","Please verify that your IMAP server name, username and password are correct and try again.");
		  return false
		}
		if($("input[name='sec_"+id+"_imap_userame']").val() === ""){
		  mcalert("Error","Please verify that your IMAP server name, username and password are correct and try again.");
		  return false
		}
		if($("input[name='sec_"+id+"_imap_passowrd']").val() === ""){
		  mcalert("Error","Please verify that your IMAP server name, username and password are correct and try again.");
		  return false
		}


		var sr = serialize1($(frm));//$(frm).serializeArray();//
		    sr+="&testsmtp=1&email_type=secondary&id="+id;
		   // alert(sr);
		    $.post("<?=Url_Create('addtickle')?>", sr, function (data) {
		        if (data == "true") {
		            $('#forcepress').val('0');
		            $("#sec_"+id+"_imap_connection_approved").val("yes");
		              if ($.cookie("sec_"+id+"_delete_campaign_on_reply") == "delete_campaign_on_reply") {
		                $("input[name='sec_"+id+"_delete_campaign_on_reply']").prop('checked', true);
		                 $("input[name='sec_"+id+"_do_not_track']").prop('checked', false);
		                $.removeCookie("sec_"+id+"_delete_campaign_on_reply");
		            }

		            if ($.cookie("sec_"+id+"_notify_when_reply_received") == "notify_when_reply_received") {
		                $("input[name='sec_"+id+"_notify_when_reply_received']").prop('checked', true);
		                $("input[name='sec_"+id+"_do_not_track']").prop('checked', false);
		                $.removeCookie("sec_"+id+"_notify_when_reply_received");
		            }

		          mcalert("Notification", "Congratulations!  We were able to connect to your mail server.  You can now select tracking options for your Tickles.  Be sure and click Save at the bottom of this page to save your settings.");
		           } else {
		            var index1 = data.indexOf("'IMAP Error:");
		            var index2 = data.indexOf("' in");
		            var org_data = data.substring(index1,index2);

		            if ($.cookie("sec_"+id+"_delete_campaign_on_reply") == "delete_campaign_on_reply") {
		                $.removeCookie("sec_"+id+"_delete_campaign_on_reply");
		            }
		           if ($.cookie("sec_"+id+"_notify_when_reply_received") == "notify_when_reply_received") {
		                $.removeCookie("sec_"+id+"_notify_when_reply_received");
		            }

		            $("input[name='sec_"+id+"_delete_campaign_on_reply']").prop('checked', false);
		            $("input[name='sec_"+id+"_notify_when_reply_received']").prop('checked', false);

		            $('#forcepress').val('0');
		            var txt = "Connection failed. Verify your settings and password and try again. You may need to also check under (advanced settings) and make additional updates.\n\
		            <br><br>If you feel your password and settings are correct, please copy the error message below and check with your email provider. Your email provider can give you the proper settings to try again. Most common reason for a connection error is wrong port number under advanced settings.<br><br>Error Message: "+data+" ";
		            mcalert("Notification", txt);
		        } 
		    });

		
	    }





        }

      function serialize1(frm) {
        var ret = $(frm).serialize();
        ret+="&EmailID="+$("#EmailID").val()+"&dmfromemail="+$("#dmfrom").val();
        return ret;
    }
</script>
<?php
//Start Code added on 2/11/2014 to solve limit issue. Beforcode was displaying just 10 follow-up message per tickle 
$QueryForPagination = mysqli_query($db->conn,"select TickleTrainID from ticklefollow where TickleTrainID='" . $_REQUEST['tid'] . "'") or die(mysqli_error($db->conn) . __LINE__);
$RowsForPagination = mysqli_num_rows($QueryForPagination);
$pg = max(1, intval($_REQUEST['pg']));
$pc = intval(GetVal($_REQUEST['pc'], 10));
$tid = $_REQUEST['tid'];
$ps = ceil($RowsForPagination / $pc);
// End Code added on 2/11/2014 to solve limit issue. Beforcode was displaying just 10 follow-up message per tickle 

$Title = "ADD";
$tid = @trim($_GET['tid']);
$ftid = @intval($_GET['ftid']);

$ntid = $tid;
$Button = "Insert";
$mode = "AddTickle";
$CreatedDate = date("Y-m-d H:i:s");
$user_signature = tablerow('tickleuser', 'signature,mail_type', array("WHERE UserName ='" . $_SESSION['UserName'] . "' and TickleID='" . $_SESSION['TickleID'] . "'"));
$user_sign = @trim($user_signature["signature"]);
if ($user_sign != "") {
    $user_sign = str_replace(array("\r", "\n"), "", htmlspecialchars("<br><br>" . $user_sign)); //str_replace(array('"',"\n"),array('\"',''),$user_sign));
}

if ($tid == "") {
    $ntid = md5($CreatedDate . $_SESSION['TickleID']);
}
$user_sign = '<p style="text-align: left; ">
                <strong style="font-size: 12px; "><em>For example:</em></strong></p>
<p>
                <span style="font-family:verdana,geneva,sans-serif;"><span style="font-size:12px;">Hi [firstname], I wanted to follow-up on the invoice I sent you. Can you let me know if you received that or the status? Thank you.</span></span></p>
<p>
                <span style="font-family:verdana,geneva,sans-serif;"><span style="font-size:12px;">[signature]</span></span></p>
<hr />
<p>
                <span style="font-size: 12px; ">Tip! &nbsp;Use the </span><img alt="" src="/' . ROOT_FOLDER . 'images/fn.PNG" style="font-size: 12px; border:1px solid black " /><span style="font-size: 12px; ">&nbsp;First Name and&nbsp;</span><img alt="" src="/' . ROOT_FOLDER . 'images/sig.PNG" style="font-size: 12px; border:1px solid black " /><span style="font-size: 12px; ">&nbsp;Signature shortcuts to insert these fields. &nbsp;</span></p>
<p>
                <span style="font-size:12px;">Go ahead and delete all of this text and type your own message. The delivery options are below.</span></p>';
if ($tid != "") {
    $Title = "Edit";
    $Button = "Update";
    $mode = "EditTickle";
    $tickle = $db->select_to_array('tickle', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and TickleTrainID='$tid'");
    $ticklefollow = $db->select_to_array('ticklefollow', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and TickleTrainID='$tid'");
    $Taskcheck = $db->select_to_array('task', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and TickleTrainID='$tid'");
    $Files = $db->select_to_array('files', '', " where FileContext='tickle' and FileParentID='" . $tid . "' ORDER by FileID ASC");
}
if ($_SESSION['mail_type'] != "text") {
    $addtickle = true;
    include_once "includes/ckeditor_inc.php";
    ?>
<script type="text/javascript">
    $(document).ready(function() {
    	CKEDITOR.env.isCompatible = true;
        CKEDITOR.replace('TickleMailContent',config);
        //$('textarea.tinymce').ckeditor(config);
    });
</script>
<?php } ?>
<script type="text/javascript">

    function SetTickleShedule(instant){
        if(instant == 'Instantly'){
            $("#DailyDays").hide()
            $("#TickleTimehideshow").hide()
            $("#TickleTime").parent().hide();
            $("#IsRepeat").hide();
            $("label[for='EndAfter']").hide();
            $("#unlimited_label").hide();
            $("#EndAfter").hide();
            $("#unlimited").hide();
            $("#TApprove").removeAttr("checked");
            $("#TApprove").attr("disabled","disabled");
        }else{
            $("#DailyDays").show()
            $("#TickleTimehideshow").show();
            $("#TickleTime").parent().show();
            $("#IsRepeat").show();
            $("label[for='EndAfter']").show();
            $("#unlimited_label").show();
            $("#EndAfter").show();
            $("#unlimited").show();
            $("#TApprove").removeAttr("disabled");
<?php if ($tickle[0]['TApprove'] == "Y" || $mode == 'AddTickle') { ?>
                            $("#TApprove").attr("checked","checked");
<?php } else { ?>
                            $("#TApprove").removeAttr("checked");
<?php } ?>
                        }
                    }






                    $(document).ready(function() {
                    
                    $("#TickleFormFollowtext").submit(function() {
                        
                        
                        if(typeof FormData == "undefined"){
                             var form_data = [];
                             form_data.push("files[]", $("#TickleFormFollowtext")[0]);
                         }else{
                             var form_data = new FormData($("#TickleFormFollowtext")[0]);
                         }

                         var filename = window.location.pathname;
                         var path = "https://client.tickletrain.com"+filename+"?tid=<?php echo $_GET[tid]; ?>";
                         $.ajax({
                             url: path,
                             type: 'post',
                             data: form_data,
                             processData: false,
                             contentType: false,
                             success: function(data) {
                                 reloadFollowUps();
                                 $("#FollowUpForm").dialog("close");

                             }
                         });
                         return false;
                        
                        
                    });
                    
                   <?php if($tickle[0]['DailyDays'] == 0 && $mode=="EditTickle") { ?>
                   SetTickleShedule('Instantly')
                   <?php } ?>

                 $("input[name='CustomSubjectCheckbox']").click(function() {
                     if ($("input[name='CustomSubjectCheckbox']").is(':checked')) {
                         $("#CustomSubject").show();
                     } else {
                         $("#CustomSubject").hide();
                     }
                 });

                 $("#TickleSendOutTime").change(function(){
                     var instant;
                     if($("#TickleSendOutTime").val() == 'Instantly'){
                         instant = "Instantly";
                     }else{
                         instant = "After";
                     }
                     SetTickleShedule(instant);
                 })

                 $('.MultiFile').MultiFile({accept: '', max: 10});
                 $("#TickleTime").datetimepicker({timeOnly: true, timeFormat: 'hh:mm TT', ampm: true,addSliderAccess: true,
	sliderAccessArgs: { touchonly: false },controlType: 'select'});
                 $.validator.methods.ckeditorrequired = function(value, element) {
                     var val = CKEDITOR.instances[element.id].getData();
                     element.value = $.trim(val);
                     return element.value != "";
                 };
                 $("#TickleForm").validate({
                     rules: {
                         TickleName: {
                             required: true,
                             charnumberonly: true,
                             notspecialwords: true,
                             remote: {url: '<?= Url_Create("addtickle", "") ?>', type: 'get', data: {"action": "CheckTickle", "TickleTrainID": '<?= (($tid != "") ? $tid : $ntid) ?>'}}
                         },
                         TickleMailContent: {
                             //ckeditorrequired: true
                             required: true,
                         }
                     },
                     messages: {
                         TickleName: {
                             required: "Please Enter Tickle Name",
                             charnumberonly: "Invalid name",
                             notspecialwords: "Invalid name",
                             remote: "Tickle name already exists. Please use a different name."
                         },
                         TickleMailContent: {
                             ckeditorrequired: "Please Enter Mail Content"
                         }
                     },
                     submitHandler: function(form) {
                         //alert("dd");
                         $.ajax({
                             url: '<?= Url_Create("addtickle", "") ?>',
                             type: 'get',
                             data: {"action": "CheckTickleGroup", "TickleName": $("#TickleName").val(), "TickleTrainID": '<?= (($tid != "") ? $tid : $ntid) ?>'},
                             success: function(data) {
                                 var catId = parseInt(data);
                                 if ($("input[name='CustomSubjectCheckbox']").is(':checked')) {
                                     var CustomSubject = $("#TextAreaCustomSubject").val();
                                     if (!CustomSubject || CustomSubject == "") {
                                         var subject_validation_message = 'Please eneter your custom subject first';
                                         var ok = {text: 'OK', click: function() {
                                                 $(this).dialog('close');
                                             }};
                                         mdialog("Confirmation", subject_validation_message, [ok]);
                                         return false;
                                     }
                                 }

                                 if (catId > 0) {
                                     var msg = 'A Contact Group already exists with that name. Would you like to merge new contacts with existing ones? Click YES to have new contacts added to this Group. Click NO to cancel and choose a different name for your Tickle or use Contact Manager to rename the existing Contact Group.';
                                     var cancel = {text: 'No', click: function() {
                                             $(this).dialog('close')
                                         }};
                                     var ok = {text: 'Yes', click: function() {
                                             $(this).dialog('close');
                                             $("#ticklecontact_field").val(catId);
                                             form.submit()
                                         }};
                                     mdialog("Confirmation", msg, [ok, cancel]);
                                 } else {
                                     form.submit();
                                 }
                             }});
                         //form.submit();
                     }
                 });

                 $("#TickleFormFollow").validate({
                     rules: {
                         TickleMailFollowContent: {
                             ckeditorrequired: true
                         }
                     },
                     messages: {
                         TickleMailFollowContent: {
                             ckeditorrequired: "Please Enter Mail Content"
                         }
                     },
                     submitHandler: function(form) {
                         if(typeof FormData == "undefined"){
                             var form_data = [];
                             form_data.push("files[]", $("#TickleFormFollow")[0]);
                         }else{
                             var form_data = new FormData($("#TickleFormFollow")[0]);
                         }

                         var filename = window.location.pathname;
                         var path = "https://client.tickletrain.com"+filename+"?tid=<?php echo $_GET[tid]; ?>";
                         $.ajax({
                             url: path,
                             type: 'post',
                             data: form_data,
                             processData: false,
                             contentType: false,
                             success: function(data) {
                                 reloadFollowUps();
                                 $("#FollowUpForm").dialog("close");

                             }
                         });
                         return false;
                     }
                 });
                 $("#TickleTimeFollow").datetimepicker({timeOnly: true, timeFormat: 'hh:mm TT', ampm: true,addSliderAccess: true,
	sliderAccessArgs: { touchonly: false },controlType: 'select'});

<? if ($ftid) { ?>
        EditFollow(<?= $ftid ?>);
<? } ?>
    });

    function CheckAttach(elm) {
        var nm = $(elm).attr("name");
        if (elm.checked) {
            $("#" + nm + "FilesContainer").show();
        } else {
            $("#" + nm + "FilesContainer").hide();
        }
    }

    function ChildLoad(frm) {
        var curl = frm.contentWindow.location.href;
        if (curl.indexOf('blank') < 0) {
            $("#FollowUpForm").dialog("close");
            $("#reload_field").val(1);
            $("#TickleForm").submit();
            //reloadFollowUps();
        }
    }
    function EditFollow(ftid, counter) {
        //	alert(ftid);
        var ftid = ftid;
        $("#TickleMailFollowContent").html('');
        $.getJSON('/<?= ROOT_FOLDER ?>addtickle/?action=LoadTickleFollow&tid=<?= $ntid ?>&ftid=' + ftid,
        function(data) {
            $("#TickleMailFollowContent").html(data['TickleMailFollowContent']);
       
            if ($("#unlimited").is(":checked") == true) {
	            var tid = "<?php echo $_GET['tid']; ?>";
	            var unpauseall = {text: 'OK', click: function() {
	                    $(this).dialog('close')
	                }};
	            var message = 'In order to set "Continuous" you can not add any additional follow-up Tickles.';
	            mdialog("Continuous", message, [unpauseall]);
	            return false;
	        }

            if (ftid > 0) {
	            $("#FollowUpForm").attr("title", "Update Follow-Up Tickle");
	            PopulateForm("EditTickleFollow", data);
	        }else{
	            ClearForm();
	            PopulateForm("AddTickleFollow", data);
	        }


	            $("#FollowUpForm").attr("title", "Update Follow-Up Tickle");
		        $(window).scrollTop(0);
		        $("#FollowUpForm").dialog({width: 1000, position: 'top', modal: true,
		            open: function() {
		                //alert(CKEDITOR.instances["TickleMailFollowContent"]);
		                var chkcontenttype = "<?php echo $user_signature['mail_type']; ?>";
		                if(chkcontenttype!='text')
		                {    
		                    if (CKEDITOR.instances["TickleMailFollowContent"]){
		                        CKEDITOR.instances['TickleMailFollowContent'].destroy();
		                        CKEDITOR.replace('TickleMailFollowContent', config);
		                    }else{
		                        CKEDITOR.instances["TickleMailFollowContent"];
		                        CKEDITOR.replace('TickleMailFollowContent', config);
		                    }
		                } 

		                if($('input[name=reminder_task]').prop('checked')==true){
		                	$('.reminder_li').hide();
							$(".rate_sec_tickle").css("display", "none");
		                }else{
		                	$('.reminder_li').show();
							$(".rate_sec_tickle").css("display", "show");
		                }
		            },
		            close: function() {
		                ClearForm();
		                if (CKEDITOR.instances["TickleMailFollowContent"]) {
		                    CKEDITOR.instances['TickleMailFollowContent'].destroy();
		                }
		            }
		        });


        });

        
        
        //if (ftid > 0) {
            // $("#FollowUpForm").attr("title", "Update Follow-Up Tickle");
            // $.getJSON('/<?= ROOT_FOLDER ?>addtickle/?action=LoadTickleFollow&tid=<?= $ntid ?>&ftid=' + ftid,
            // function(data) {
            //     //console.log(data);
            //     PopulateForm("EditTickleFollow", data);
                
            // });



    }

    function DeleteFollow(ftid) {
        $.get('/<?= ROOT_FOLDER ?>addtickle/?action=DeleteFollow&tid=<?= $ntid ?>&ftid=' + ftid,
        function(data) {
            reloadFollowUps();
        });
    }

    function DuplicateFollow(ftid) {
        if (ftid != "") {
            $.post("<?= Url_Create('duplicateticklefollow') ?>", {FollowTickleId: ftid}, function(data) {
                reloadFollowUps();
            });
        }
    }

    function MoveupFollow(ftid) {
        if (ftid != "") {
            var tid = "<?php echo $_GET['tid']; ?>";
            $.post("<?= Url_Create('duplicateticklefollow') ?>", {FollowTickleId: ftid, "Moveupfollow": "Moveupfollow", "tid": tid}, function(data) {
                reloadFollowUps();
            });
        }
    }

    function MovedownFollow(ftid) {
        if (ftid != "") {
            var tid = "<?php echo $_GET['tid']; ?>";
            $.post("<?= Url_Create('duplicateticklefollow') ?>", {FollowTickleId: ftid, "MovedownFollow": "MovedownFollow", "tid": tid}, function(data) {
                reloadFollowUps();
            });
        }
    }

    function reloadFollowUps(sfield, sval) {
        var reqstr = "";
        if ($("#reqstr").length) {
            reqstr = $("#reqstr").val();
        }
        var url = '<?= Url_Create('ticklefollow', "tid=$tid") ?>';
        if (sfield && sval && reqstr != "") {
            reqstr = reqstr.replace(new RegExp(sfield + "=[^&$]*", "g"), sfield + "=" + sval);
            url += "&" + reqstr;
        }
        var pg = "<?php echo $_REQUEST['pg']; ?>";
        if (pg) {
            url += "&pg=" + pg;
        }

        $('#followcontainer').load(url);
    }

    function readmore(id) {
        $("#" + id).dialog();
        return false;
    }

    function DeleteFile(fid) {
        $.get('/<?= ROOT_FOLDER ?>deletefile.php?fid=' + fid, function(data) {
            $("#loadedFile" + fid).remove()
        });
    }

    function SpamCheck() {
        var str = $("#TickleForm").serialize();
        $.post("<?= Url_Create('spamassassin'); ?>", str, function(data) {
            $("#spamassassin").html(data);

            //$("#spamassassin").attr("style", "display:block; width:200px; background-color:#FFFFFF; border: solid 1px #808080; margin: 20px, 20px, 20px, 20px");
            window.location = '#toptickle';
        });
        return false;
    }
    function ShowSpamHelp() {
        $("#spamhelp").dialog({width: 500, position: 'top'});
        return false;
    }
    $(document).ready(function() {

        if ($('#EndAfter').val() == '13') {
            $('#unlimited').prop('checked', true);
        }

        $('#EndAfter').change(function() {
            if ($('#EndAfter').val() != '13') {
                $('#unlimited').prop('checked', false);
                $("#EndAfter option[value='13']").remove();
            }
        });

        $('#unlimited').bind('click', function()
        {
            if ($(this).is(":checked") == true) {
                var tid = "<?php echo $_GET['tid']; ?>"
                $.ajax({
                    url: "",
                    data: {"tid": tid, "checkfollowup": "checkfollowup", 'ticklemessage': 'ticklemessage'},
                    type: "post",
                    async: false,
                    success: function(response) {
                        if (response == "NotAvailable") {
                            $('#unlimited').prop('checked', false);
                            var unpauseall = {text: 'OK', click: function() {
                                    $(this).dialog('close')
                                }};
                            var message = "In order to set “Continuous” please remove any additional follow-up Tickles first.";
                            mdialog("Continuous", message, [unpauseall]);
                        } else {
                            $('#EndAfter').prepend('<option value="13" selected>&infin;</option>');
                        }
                    }
                });
            } else {
                $("#EndAfter option[value='13']").remove();
                // $('#EndAfter').prop('disabled', false);
            }
        });
        $('#changetickle').change(function() {
            if($(this).val()=='now'){
                $("#DailyDays").hide();
                $("#TickleTimehideshow").hide();
            }else{
                $("#DailyDays").show();
                $("#TickleTimehideshow").show();
            }
        });
		
	    $('#TApprove').click(function() {	
			if($(this).prop('checked')==true)
			{ 
				$('.ico_play_pause01').removeClass('pause');  $('.ico_play_pause01').addClass('play');
			}else
			{
				$('.ico_play_pause01').removeClass('play');  $('.ico_play_pause01').addClass('pause');
			}
		});	

		$('input[name=reminder_task]').click(function() { 
			if($(this).prop('checked')==true)
			{ 
				$('.reminder_li').slideUp();
				$('.reminder').slideDown();

				$("input[name='CustomSubjectCheckbox']").prop('checked',false);
				$(".rate_sec_tickle").slideUp();
				$(".rate_sec_tickle").css("display", "none");
				
			}else
			{
				$('.reminder_li').slideDown();
				$('.reminder').slideUp();
			    $(".rate_sec_tickle").slideDown();
				$(".rate_sec_tickle").css("display", "block");
			}
		});	
		
		if($('input[name=reminder_task]').prop('checked')==true){
			$(".rate_sec_tickle").css("display", "none");
		}else{
			$(".rate_sec_tickle").css("display", "show");
		}
				
    });
	
</script>

<div class="main_holder edit_page">
    <div class="heading">
        <h1><?= $Title ?> Tickle</h1>
    </div>
    <form action="<?= Url_Create('addtickle'); ?>" method="post" name="TickleForm" id="TickleForm" enctype="multipart/form-data">
        <input type="hidden" name="TickleTrainID" value="<?php echo $tid; ?>"/>
        <input type="hidden" name="NTickleTrainID" value="<?php echo $ntid; ?>"/>
        <input type="hidden" name="action" value="<?php echo $mode; ?>"/>
        <input type="hidden" name="reload" value="0" id="reload_field"/>
        <input type="hidden" name="qstr" value="<?php echo $_GET['qstr'] ?>">
		<input type="hidden" name="hashtag" value="<?php echo $_GET['hashtag'] ?>">
        <input type="hidden" name="spamcheck" value="no" id="spamcheck">
        <input type="hidden" name="TickleContact" value="0" id="ticklecontact_field">
        <fieldset>

            <?php if (!isset($pg) || $pg == "1") { ?>
            <div class="twocolumns">
                <div class="left_col">
                    <div class="row">
                        <label for="TickleName">Tickle name <span class="req">*</span></label>
                        <span class="input_text" style="width:200px;">
                        	<input name="TickleName" id="TickleName" value="<?= $tickle[0]['TickleName'] ?>" maxlength="50" type="text" style="width:100%;"/>
                        </span>

                        <span style="margin-left: 50px;">
                        	<input type="checkbox" name="reminder_task" <?php if ($tickle[0]['reminder_task'] == 'Y') { ?> checked="checked" <?php } ?>  /> <?php echo $reminder_task; ?> Use this Tickle to turn emails into tasks or reminders

                        	<span class="ico_info">
                        		<span class="info-block">
                                    <span class="ib-t">
                                        <span class="info-text">
                                            Enable this option and we will send email reminders to *you* when your Tickle is used in the TO: or BCC: fields.  If, however, there is an email address in the CC: field, the reminders will be sent to that email address.  The CC: field is useful when replying to a contact and delegating a task at the same time.
                                        </span>
                                    </span>
                                </span>
                            </span>
                        </span>

                    </div>

                        <?php

                        if(isset($CustomSubject)) {
                            unset($CustomSubject);
                        }

                        $query = mysqli_query($db->conn,"select custom_subject from  tickle where TickleTrainID= '" . $_GET['tid'] . "'") or die(mysqli_error($db->conn) . __LINE__);
                        if (mysqli_num_rows($query) > 0) {
                            $row = mysqli_fetch_assoc($query);
                            if($row['custom_subject'] != "") {
                                $CustomSubject = $row['custom_subject'];
                            }
                        }
                        ?>
                    <div class="row  reminder <?php if ($tickle[0]['reminder_task'] != 'Y') { ?> hide <?php } ?>" >
                        <label for="Subject">Name for your task/reminder:</label>
                        <span class="input_text" style="width:200px;">
                        	<input type="text" name="reminder_task_name"  value="<?= utf8_decode($tickle[0]['reminder_task_name']) ?>"  placeholder="ex. Delegated task" /> 
                    	</span>
                    </div>

                    <div class="row reminder_li <?php if ($tickle[0]['reminder_task'] == 'Y') { ?> hide <?php } ?>">
                        <label for="Subject">Subject<span class="req">*</span></label>
                        <input type="checkbox" name="CustomSubjectCheckbox" value="CustomSubjectCheckbox" <?php if (isset($CustomSubject) && $CustomSubject != '') { ?> checked="checked" <?php } ?>/> Edit Subject
                        <span class="ico_info">
                        	<span class="info-block info-block-m">
                                <span class="ib-t">
                                    <span class="info-text">
                                        Leave this unchecked if you want to use the original subject line when sending your Tickles. It will use a prefix "RE: {subject}" Or you can use your own subject by clicking the check box.
                                    </span>
                                </span>
                            </span>
                        </span>
                    </div>

                    <div class="row reminder_li <?php if ($tickle[0]['reminder_task'] == 'Y') { ?> hide <?php } ?>" id="CustomSubject" style="<?php if (isset($CustomSubject) && $CustomSubject != '') { ?>display: block;<?php } else { ?> display: none <?php } ?>">
                        <label for="Custom Subject">Custom Subject</label>
                        <div style="clear:both;"></div>
                        <span class="input_text" style="width:360px;background-position:0 -44px;">
                        	<input name="TextAreaCustomSubject" id="TextAreaCustomSubject" style="width:327px;"  value="<?php echo $CustomSubject; ?>"/>
                        </span>
                    </div>

                    <div class="row">
                        <label for="TickleMailContent">Email message <span class="req">*</span></label><div style="clear: both;"></div>
                        <div class="plugin_holder">
                            <textarea name="TickleMailContent" id="TickleMailContent" <?php if($user_signature['mail_type']=='text'){?> rows="10" style="width: 700px;" <?php }else{ ?> rows="40" cols="600" <?php } ?>
                                                             class="tinymce"><?php if($user_signature['mail_type']=='text'){ echo @trim($tickle[0]['TickleMailContent']); }else{?><?= @trim($tickle[0]['TickleMailContent']) ?><?= (($mode != "EditTickle") ? $user_sign : "") ?><?php } ?></textarea>
                        </div>
                    </div>
                    <!--  <h2>Schedule</h2> -->
                    <label for="Schedule" style="color: #ff5300;margin: 0px 0px 9px;float: left;width: 100%;font: bold 16px/18px Arial, Helvetica, sans-serif;padding: 0 0 7px;">Schedule
	                    <span class="ico_info">
	                    	<span class="info-block info-block-m">
	                            <span class="ib-t">
	                                <span class="info-text">
	                                    Setting here made determine how long to wait before your Tickle is sent. "Instantly" will send your Tickle right away. The "continuous" checkbox will repeat your Tickle email indefinitely.
	                                </span>
	                            </span>
	                        </span>
	                    </span>
	                </label>

                        <?
                        $QuickTickle = $tickle[0]['QuickTickle'];
                        if ($QuickTickle == "A") {
                            $tickle['Advanced'] = "checked";
                        } else {
                            $tickle['Quick'] = "checked";
                        }
                        $RecurrencePattern = $tickle[0]['RecurrencePattern'];
                        if ($RecurrencePattern != "") {
                            $RP[$RecurrencePattern] = "checked";
                        } else {
                            $RP['D'] = "checked";
                        }
                        ?> <input type="hidden" value="Q" name="QuickTickle"/>
                    <input type="hidden" value="D" name="RecurrencePattern"/>
                        <?
                        $DailyDays = $tickle[0]['DailyDays'];
                        $EndAfter = $tickle[0]['EndAfter'];
                        ?>
                    <div class="row2">
                        <label for="DailyDays">Send this Tickle</label>
                        <select name="tsot" id="TickleSendOutTime">
                                <?php if($DailyDays == 0 && $mode=="EditTickle") {
                                    echo '<option value="After">After</option>';
                                    echo '<option value="Instantly" selected>Instantly</option>';
                                }else {
                                    echo '<option value="After" selected>After</option>';
                                    echo '<option value="Instantly">Instantly</option>';
                                } ?>
                        </select>
                        <select name="auto" id="changetickle" style="display:none;">
                            <option value="after">After</option>
                            <option value="now">Immediately</option>

                        </select>
                        <select name="DailyDays" id="DailyDays">
                                <?php
                                for ($ix = 1; $ix <= 60; $ix++) {
                                    $Dsel = "";
                                    if ($DailyDays == $ix) {
                                        $Dsel = "selected";
                                    }
                                    echo '<option value="' . $ix . '" ' . $Dsel . '>' . $ix . '</option>';
                                }
                                for ($ix = 90; $ix <= 180; $ix += 30) {
                                    $Dsel = "";
                                    if ($DailyDays == $ix) {
                                        $Dsel = "selected";
                                    }
                                    echo '<option value="' . $ix . '" ' . $Dsel . '>' . $ix . '</option>';
                                }
								$Dsel = "";
                                if ($DailyDays == '365') { 
                                        $Dsel = "selected";
                                }
                                    echo '<option value="365" ' . $Dsel . '>365</option>';
                                ?>

                        </select> <label id="TickleTimehideshow" for="TickleTime">days at</label>
                        <span class="input_text input_time">
                                <?php
                                $timeformat = explode(":", $tickle[0]['TickleTime']);
                                $TickleTime = date("h:i A", strtotime($tickle[0]['TickleTime']));
                                $time = $TickleTime;
                                if ($tickle[0]['TickleTime'] == "")
                                    $time = "12:00 PM";
                                ?> <input id='TickleTime' name="TickleTime" type='text'
                                   value='<?php echo $time; ?>' maxlength=8
                                   onkeypress="javascript:return false;"></span>
                        <span class="txt" id="IsRepeat">. Repeat</span>
                        <select name="EndAfter" id="EndAfter">
                                <?php
                                for ($ix = 0; $ix <= 11; $ix++) {
                                    $Dsel = "";
                                    $EndAfters = $EndAfter - 1;
                                    if ($EndAfters == $ix) {
                                        $Dsel = "selected";
                                    }

                                    if ($EndAfter == '13' && !isset($counterhere)) {
                                        $counterhere = '13';
                                        echo '<option value="13" selected>&infin;</option>';
                                    }

                                    $ixv = $ix + 1;
                                    echo '<option value="' . $ixv . '" ' . $Dsel . '>' . $ix . '</option>
                                                             ';
                                }
                                ?>
                        </select> <label for="EndAfter">times.</label>
                        <!--<span class="txt"></span>-->
                        <label id="unlimited_label" for="Unlimited">Continuous</label>
                        <input type="checkbox"  id="unlimited" value="1" name="unlimited"/>

                    </div>

                    <h2>Delivery options</h2>

                    <div class="row2">
                        <ul class="check_area">
                            <li>
                                <?
                                if ($tickle[0]['TApprove'] == "Y" || $mode == 'AddTickle')
                                    $TApprove = " checked";
                                ?>
                                <input type="checkbox" name="TApprove" id="TApprove" value="Y"<?php echo $TApprove; ?> />
                                <label for="TApprove">Approve before<span class="ico_info"><span class="info-block info-block-m">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Pauses your Tickles until you approve them. They can be approved from the Dashboard or via the Today's Tickles email notification.
                                                </span>
                                            </span>
                                        </span></span></label>

                                <div class="inner_text">
								<font style="float:left; padding-right:5px;">sending</font>
								<a href="javascript:void(0);" class="ico_play_pause01 <?php if($tickle[0]['TApprove'] == "Y" || $mode == 'AddTickle'){ echo 'play'; }else{ echo 'pause'; }?>" style="cursor:default;"><?php if($tickle[0]['TApprove'] == "Y" || $mode == 'AddTickle'){ echo 'Play'; }else{ echo 'Pause'; }?></a>
								<!--<font style="float:left">(Pause mode)</font>-->								
									<!--<span class="ico01 active"></span>-->
								</div>
                            </li>

                            <li>
                                <?
                                if ($tickle[0]['NoWeekend'] == "Y")
                                    $NoWeekend = " checked";
                                ?>
                                <input type="checkbox" name="NoWeekend" id="NoWeekend" value="Y"<?php echo $NoWeekend; ?> />
                                <label for="NoWeekend">Do not send Tickles on 
                                	<span class="ico_info">
                                		<span class="info-block">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Tickles will not be sent on Saturday and Sunday.
                                                </span>
                                            </span>
                                        </span>
                                    </span>
                                </label>

                                <div class="inner_text">weekends <span class="ico02 active"></span></div>
                            </li>

                            <li>
                                <?
                                if ($tickle[0]['CCMe'] == "Y")
                                        $CCMe = " checked";
                                ?> 
                                <input type="checkbox" name="CCMe" id="CCMe" value="Y"<?php echo $CCMe; ?> />
	                                <label for="CCMe">BCC me <span class="ico_info"><span class="info-block">
	                                            <span class="ib-t">
	                                                <span class="info-text">
	                                                    Receive a copy of your Tickle when it's sent to the recipient.
	                                                </span>
	                                            </span>
	                                        </span></span>
	                                </label>
                                <div class="inner_text"><span class="ico03 active"></span></div>
                            </li>


                            <li class="reminder_li <?php if ($tickle[0]['reminder_task'] == 'Y') { ?> hide <?php } ?>">
                                <?php
                                    if ($tickle[0]['AttachOriginalMessage'] != "N")
                                        $AttachOriginalMessage = " checked";
                                ?>
                                <input type="checkbox" name="AttachOriginalMessage"
                                       id="AttachOriginalMessage" value="Y"
                                           <?php echo $AttachOriginalMessage; ?> onclick="CheckAttach(this)"/>
                                <label for="AttachOriginalMessage">Include original message text 
                                	<span class="ico_info">
                                		<span class="info-block">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Includes original message text in the body of your outgoing Tickle.
                                                </span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                                
                                <div id="AttachOriginalMessageFilesContainer" class="inner_text"
                                     style="display:<?= (($tickle[0]['AttachOriginalMessage'] != "N") ? "" : "none") ?>">
                                    <input type="checkbox" name="AttachOriginalMessageFiles" id="AttachOriginalMessageFiles"
                                           value="Y" <?= (($tickle[0]['AttachOriginalMessage'] == 'Y') ? "checked" : "") ?> />
                                    <label for="AttachOriginalMessageFiles">Check this box to have original e-mail file attachments resent along
                                        with your outgoing Tickle.</label>
                                    <span class="ico_info">
                                    	<span class="info-block">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Original email file attachments will be resent with your Tickle.
                                                </span>
                                            </span>
                                        </span>
                                    </span>
                                </div>
                            </li>

                        </ul>


                        <div class="files_holder">
                            <label for="TAttach">Attached files<span class="ico_info"><span class="info-block">
                                        <span class="ib-t">
                                            <span class="info-text">
                                                Include files to be sent with your Tickle.
                                            </span>
                                        </span>
                                    </span></span></label>
                                <? if (count($Files) != 0) { ?>
                            <span id="loaded_files">
                                        <?
                                        for ($i = 0; $i < count($Files); $i++) {
                                            $frow = $Files[$i];
                                            $fname = @trim($frow['FileName']);
                                         //   if ($fname == "" || !file_exists(FULL_UPLOAD_FOLDER . $fname)) {
                                         //       continue;
                                         //   }
                                            ?>
                                <div id="loadedFile<?= $frow['FileID'] ?>"><a href="javascript:void();"
                                                                              onclick="DeleteFile(<?= $frow['FileID'] ?>);
                                                                                  return false;">x</a>&nbsp;<a
                                                                              href="<?= UPLOAD_FOLDER . rawurldecode($fname) ?>" onclick="wopen(this);
                                                                                  return false">
                                                                                  <?php
                                                                                    if(preg_match('/__/',$fname))
                                                                                     {
 											$file_attach = explode("__",$fname);
 											$fname = $file_attach[1];
                                                                                     }
                                                                                   ?>
                                                                                  <span class="file"
                                            title="File loaded: <?= $fname ?>"><?= $fname ?></span></a>
                                </div>
                                        <? } ?>
                            </span>
                                <? } ?>
                            <div class="file">
                                <input type="file" name="TAttach[]"
                                       id="TAttach" class="MultiFile"<? // file-input-area" style="opacity:0"      ?>/>

                                    <? /* span class="input_text"><input class="text file-input-value" type="text"/></span>
                                      <a href="#" class="button">Browse...</a */ ?>
                            </div>
                        </div>
                    </div>
               


			<label class="reminder_li <?php if ($tickle[0]['reminder_task'] == 'Y') { ?> hide <?php } ?>" for="Schedule" style="color: #ff5300;margin: 0px 0px 9px;float: left;width: 100%;font: bold 16px/18px Arial, Helvetica, sans-serif;padding: 0 0 7px;">Reply tracking
                    <span class="ico_info">
                        <span id="infoblockdetail" class="info-block info-block-m">
                            <span id="infotopdetail" class="ib-t">
                                <span id="infotextdetail" class="info-text">
                                    TickleTrain helps you manage the replies you get to your Tickle campaigns.
                                    Options  include: <br/><br/> - Delete the campaign when a reply is received. <br/> - Be notified via email that a reply was received and decide what action to take.<br/>
                                    - Do nothing. <br/><br/> Reply tracking requires a connection to your email to enable this feature.
                                </span>
                            </span>
                        </span></span>
				<a style="font: bold 12px/18px Arial, Helvetica, sans-serif; cursor: pointer; margin-left: 23px;" id="imap_settings" href="<?=Url_Create('myaccount')?>">
                         <u>Settings</u></a>
            </label>


			<div id="primary_reply" class="reminder_li <?php if ($tickle[0]['reminder_task'] == 'Y') { ?> hide <?php } ?>">
			
                    <div class="row2">
                        <ul class="check_area">
                            <?
                                    if ($tickle[0]['delete_campaign_on_reply'] == "1")
                                        $delete_campaign_on_reply = "checked";
                                    ?>
                            <li><input type="checkbox" name="delete_campaign_on_reply"<?php echo $delete_campaign_on_reply; ?>>Delete Campaign on reply
                               <?
                                if ($tickle[0]['notify_campaign_deleted'] == "1")
                                        $notify_campaign_deleted = "checked";
                                    ?>
                                <?php if(isset($notify_campaign_deleted)) { ?>
                                 <div class="inner_text" style="display : none" id="notify_campaign_deleted">
                                <input type="checkbox" name ="notify_campaign_deleted"<?php echo $notify_campaign_deleted; ?>>
                                Notify me when campaigns are deleted.
                                <?php } else { ?>
                                <div class="inner_text" style="display : none" id="notify_campaign_deleted">
                                <input type="checkbox" name ="notify_campaign_deleted"<?php echo $notify_campaign_deleted; ?>>
                                 Notify me when campaigns are deleted.
                                  <?php } ?>
                                </div></li>
                                <?
                                 if ($tickle[0]['notify_when_reply_received'] == "1")
                                        $notify_when_reply_received = " checked";
                                    ?>
                            <li><input type="checkbox" name="notify_when_reply_received"<?php echo $notify_when_reply_received; ?>>Notify me when a reply is received</li>
                               <?
                                 if ($tickle[0]['do_not_track'] == "1" || !isset($tickle[0]['do_not_track']))
                                        $do_not_track = " checked";
                                    ?>
                            <li><input type="checkbox" name="do_not_track"<?php echo $do_not_track; ?>>Do not track</li>
                        </ul>
                    </div>

			</div>



                  <h2>E-mail options</h2>
                    <div class="row">
                        <div class="row_section">
                            <label for="EmailPriority">E-mail priority</label>
                                <?php
                                $EmailPriority = $tickle[0]['EmailPriority'];
                                $EPselected[$EmailPriority] = "Selected";
                                ?> <select name="EmailPriority" id="EmailPriority">
                                <option value="3" <?php echo $EPselected['3']; ?>>Normal</option>
                                <option value="5" <?php echo $EPselected['5']; ?>>Low</option>
                                <option value="1" <?php echo $EPselected['1']; ?>>High</option>
                            </select>
                            <span class="ico_info"><span class="info-block">
                                    <span class="ib-t">
                                        <span class="info-text">
                                            Sets email priority for your Tickle.
                                        </span>
                                    </span>
                                </span></span>
                        </div>
                        <div class="row_section">
                                <?
                                if ($tickle[0]['TReceiptConfirm'] == "Y")
                                    $TReceiptConfirm = " checked";
                                ?> <input
                                type="checkbox" name="TReceiptConfirm" id="TReceiptConfirm" value="Y"<?php echo $TReceiptConfirm; ?> />
                            <label for="TReceiptConfirm">Receipt confirmation</label>
                            <span class="ico_info"><span class="info-block">
                                    <span class="ib-t">
                                        <span class="info-text">
                                            Enable a read receipt notification for your Tickle.
                                        </span>
                                    </span>
                                </span></span>
                        </div>
                    </div>
                </div>
                <div class="right_col">
					<div class="rate_sec_tickle">
                    <div class="box">
                        <div class="holder">
                            <div class="frame" id="spamassassin">
                                <h3>Spam check report</h3>
                                <h4>Press the button below to get SPAM rating for your email</h4>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn_blue btn_blue_green" onclick="SpamCheck();
                        return false;"><span>Rate my email</span></a>
					</div>
                </div>
            </div>
            <?php } else { ?>
            <div style="display:none">
                <textarea name="TickleMailContent" style="display:none"></textarea>
            </div>
            <?php }

            if(isset($_GET['tid'])) {
                echo '<div id="followcontainer"></div>';
            }

            ?>

            <? if ($ntid != "" && count($ticklefollow) != 0) { ?>


            <!--  Start Code added on 2e /11/2014 to solve limit issue. Beforcode was displaying just 10 follow-up message per tickle -->

                <?php
                if (isset($_REQUEST['tid'])) {
                    if ($ps > 1) {
                        ?>

            <!-- <h2 style="margin: 0 0 -17px;">Follow-up Tickles</h2> -->
            <div class="pagination">
                <div class="holder">
                    <ul>
                                    <? for ($j = 1; $j <= $ps; $j++) { ?>
                        <li<?= (($j == $pg) ? ' class="current"' : '') ?>>
                                            <?= (($j == $pg) ? '<span>' : '<a href="?tid=' . $tid . '&pg=' . $j . '">') ?>
                                            <?= $j ?>
                                            <?= (($j == $pg) ? '</span>' : '</a>') ?>
                        </li>
                                    <? } ?>
                    </ul>
                </div>
            </div>
                    <? }
                } else {
                    ?>
            <h2>Follow-up Tickles</h2>
                <? } ?>

            <!--End Code added on 2e /11/2014 to solve limit issue. Beforcode was displaying just 10 follow-up message per tickle -->

            <script type="text/javascript" charset="utf-8">
                $(document).ready(function() {
                    reloadFollowUps();
                });
            </script>

            <? } ?>
            <div class="buttons">
                <input type="submit" value="Save" class="btn_save"/>
                <a href="#FollowUpForm" class="btn_green" onclick="EditFollow(0);
                    return false;"><span>Add follow-up Tickle</span></a>
                <span class="ico_info"><span class="info-block">
                        <span class="ib-t">
                            <span class="info-text">
                                Schedule another Tickle after this one is sent. A different email message encourages a reply.
                            </span>
                        </span>
                    </span></span>

                <!--  Start Code added on 2e /11/2014 to solve limit issue. Beforcode was displaying just 10 follow-up message per tickle -->



                <!--End Code added on 2e /11/2014 to solve limit issue. Beforcode was displaying just 10 follow-up message per tickle -->


            </div>
        </fieldset>
    </form>
</div>
<div id="spamhelp" style="display:none" title="Top 10 Most Common Spam Filter Triggers">
    <!--h3>Top 10 Most Common Spam Filter Triggers</h3-->
    <!--p>The most common reason TickleTrain customers may have been flagged by spam filters when sending their campaign emails is too many images, not enough text. This is a very common mistake.</p-->
    <p>There is very little difference between a Tickle email and an email you send normally. However, it's a good idea
        to remember basic rules when sending any emails. Most importantly, the more images, the better chance an email
        will be blocked by spam filtering.</p>

    <p>Here's the top 10 list of spam filter warnings TickleTrain users may see when scoring their Tickles:</p>
    <ol>
        <li>BODY: HTML has a low ratio of text to image area</li>
        <li>HTML is very short with a linked image</li>
        <li>BODY: HTML has a low ratio of text to image area</li>
        <li>BODY: HTML and text parts are different</li>
        <li>BODY: HTML: images with 2400-2800 bytes of words</li>
        <li>BODY: HTML: images with 2000-2400 bytes of words</li>
        <li>BODY: HTML: images with 1200-1600 bytes of words</li>
        <li>BODY: HTML: images with 1600-2000 bytes of words</li>
        <li>BODY: HTML: images with 1200-1600 bytes of words</li>
        <li>BODY: HTML: images with 800-1200 bytes of words</li>
    </ol>
    <!--p>The definitions on this list basically mean that the tickle message has too many images compared to readable text.</p>
    <p>Spam filters can't read images. Spammers know that, so they often send spam that's nothing but a big, ginormous image. Spam filters know this, so they in turn block email that they can't read.<b>A good rule is the less images in your Tickle messages the better.</b></p-->
    <p>We have found that if your messages are in the 'Good' and above score you will have little if no issues with spam
        filters. To improve your score either add more text or remove some images.</p>
</div>
<div class="lightbox" id="FollowUpForm" style='display:none;' title="Follow-Up Tickle">
    <? include_once 'addticklefollow_form.php'; ?>
</div>

<? if ($_REQUEST['spamcheck'] == 'yes') { ?>

<script>
    var emailids = "info@tickletrain.com";
    $.post("<?= Url_Create('spamassassin') ?>", {emailid: emailids, Tickletid: "<?= $tid ?>"}, function(data) {
        $("#spamassassin").html(data);
        $("#spamassassin").attr("style", "display:block; width:200px; background-color:#FFFFFF; border: solid 1px #808080; margin: 20px, 20px, 20px, 20px");
    });
</script>
<?
};
//what the popup should say if they atempt the same the wording is a little different thann trying to cadd continouous if a follow-up tickle exists
?>
