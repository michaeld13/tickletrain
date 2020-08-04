<?php
if($ContactID>0)
{
    $Title="Edit";
    $Button="Update";
    $contact=$db->select_row('contact_list',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and ContactID='$ContactID'");
}
$fbid=$_GET['fbid'];
$linkarr=explode('/',$_SERVER["HTTP_REFERER"]);
$fbref=$linkarr[count($linkarr)-2];
$email4fb=$_GET['email'];
?>
<script language="javascript">
	$(document).ready(function(){
		$("#ContactForm").validate({
			rules: {
				// FirstName: {
				// 	required:true
				// 	//charspaceonly:true
				// },
				// LastName: {
				// 	required:true
				// 	//charspaceonly:true
				// },
				// EmailID: {
				// 	required:true,
				// 	email:true,
    			//remote:{url:'<?= Url_Create("contactmanager", "")?>',type:'get',data:{"action":"CheckContactEmail","ContactID":'<?=$ContactID?>'}}
				// }
			},
			messages: {
				// FirstName: {
				// 	required: "Required field",
				// },
				// LastName: {
				// 	required: "Required field",
				// },
				// EmailID: {
				// 	required: "Required field",
				// 	email: "Invalid email",
    			//remote: "Email exists"
				// }
			}
		});
                
	});
</script>
<div class="lightbox lb_holder" id="lightbox_holder">
<div class="fb_profile" id='facebookinfo'></div>
<script>
    $('#facebookinfo').load('<?=Url_Create("fb/image","email=".$contact['EmailID']."&ret=infocmupd")?>',function (data) {if (data!=''){$("#lightbox_holder").width("500px");$("#lightbox_holder").parents(".ui-dialog").width('550px');}});
</script>
<form action="<?=Url_Create('contactmanager');?>" class="lb_form" method="post" name="ContactForm"  id="ContactForm">
    <input type="hidden" name="action" value="EditContact"/>
    <input type="hidden" name="redirect" value="<?=$redirect?>"/>
    <input type="hidden" name="hashtag" value="<?php echo $_GET['hashtag'] ?>"/>
    <input type="hidden" name="ContactID" value="<?=$ContactID?>"/>
	<input type="hidden" name="redirectUrl" value="<?php if($redirect=='home'){ echo $_GET['qstr']; }else{ echo base64_encode($_GET['qstr']); } ?>"/>
	<fieldset>
		<div class="row">
			<label for="FirstName">First Name:</label>
			<span class="input_text"><input class=" contactname" type="text" name="FirstName" id="FirstName" value="<? if ($fbFirstName) echo $fbFirstName; else echo str_replace("'","",$contact['FirstName']); ?>"/></span>
		</div>
		<div class="row">
			<label for="LastName">Last Name:</label>
			<span class="input_text"><input class="contactname" type="text" name="LastName" id="LastName" value="<? if ($fbLastName) echo $fbLastName; else echo str_replace("'","",$contact['LastName']);?>"/></span>
		</div>
		<!-- <div class="row">
			<label for="EmailID">Email ID:</label>
			<span class="input_text"><input type="text" name="EmailID" id="EmailID" value="<? echo $contact['EmailID'];?>"/></span>
		</div> -->
		<div class="row">
			<input type="submit" value="Update" class="btn_update" />
		</div>
	</fieldset>
</form>
</div>
