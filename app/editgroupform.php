<?php
$Title="ADD";
$CategoryID=$_GET['CategoryID'];
$Button="Insert";
if($CategoryID>0 && checkGroupDelete($CategoryID))
{
    $Title="Edit";
    $Button="Update";
    $category=$db->select_row('category',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and CategoryID='$CategoryID'");
}else{
    $CategoryID=0;
}
?>
<h1 class="head"><?php echo $Title;?> Group</h1>
<script language="javascript">
	$(document).ready(function(){
		$("#CategoryForm<?=$CategoryID?>").validate({
			rules: {
				CategoryName: {
					required:true,
					charnumberonly:true<?if ($CategoryID<=0){?>,
                    remote:{url:'<?= Url_Create("contactmanager", "")?>',type:'get',data:{"action":"CheckCategory","CategoryID":'<?=$CategoryID?>'}}
					<?}?>
				}
			},
			messages: {
				CategoryName: {
					required: "Required field",
					charnumberonly: "Invalid name"<?if ($CategoryID<=0){?>,
                   	remote: "It is not possible to name a Contact Group if the name is associated with an existing Tickle. Please use another name that is not associated with a Tickle or rename the existing Tickle to some other name."
					<?}?>
				}
			}<?if ($CategoryID>0){?>,
            submitHandler: function(frm) {
				var pData = $(frm).serializeObject();
                $.ajax({url:'<?= Url_Create("contactmanager", "")?>',type:'get',data:{"action":"CheckCategory","CategoryID":'<?=$CategoryID?>',"CategoryName":$("#CategoryName").val()}, success: function( data ) {
                    var catId = parseInt(data);
                    if (catId>0){
                        var msg = 'A Group already exists with that name. Would you like to merge this Group with the existing one? Click YES to have Groups merged. Click NO to cancel and choose a different name.';
                        var cancel = {text:'No', click: function() {$(this).dialog('close')}};
                        var ok = {text:'Yes', click: function(){$(this).dialog('close');pData['MergeCategoryID']=catId;submitData(pData);}};
                        mdialog("Confirmation", msg,[ok,cancel]);
                    }else{
						submitData(pData);
					}
                }});
               //form.submit();
            }
			<?}?>	
		});
	});
	function submitData(pData){
		$.ajax({url:'<?= Url_Create("contactmanager", "")?>',type:'post',data:pData, success: function( data ) {
			window.location.reload();
		}});
	}
</script>
<form action="<?=Url_Create('contactmanager');?>" method="post" name="CategoryForm"  id="CategoryForm<?=$CategoryID?>" class="niceform">
<input type="hidden" name="action" value="EditCategory"/>
<input type="hidden" name="CategoryID" value="<?=$CategoryID?>"/>
<input type="hidden" name="MergeCategoryID" value="0" id="MergeCategoryID"/>

<table cellpadding="0" cellspacing="0">
<tr><td class="title">Group Name<span class="error">*</span></td><td><input type="text" name="CategoryName" id="CategoryName" value="<? echo $category['CategoryName'];?>" size="32"/><br><span class="errortext" id="err_CategoryName"></span>
</td></tr>
<tr><td></td><td><input type="submit" name="submit" value="<?php echo $Button;?>" /></td></tr>
</table>
</form>
