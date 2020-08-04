<?php
$tid=@trim($_GET['tid']);
$ftid=@intval($_GET['ftid']);
$Button="Add";
$mode = "AddTickleFollow";
if($ftid>0)
{
	$Button="Update";
	$mode="EditTickleFollow";
	$Taskcheck=$db->select_to_array('task',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and TickleTrainID='$tid'");
	$tickle=$db->select_to_array('ticklefollow',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and FollowTickleTrainID='$ftid'");
    $Files = $db->select_to_array('files',''," where FileContext='ticklefollow' and FileParentID='".$ftid."' ORDER by FileID ASC");
}
?>
<form action="<?=Url_Create('addtickle');?>" method="post"
	name="TickleFormFollow" id="TickleFormFollow" enctype="multipart/form-data" target="uploadFrame">
	<input type="hidden" name="TickleTrainID" value="<?php echo $tid;?>" />
	<input type="hidden" name="FollowTickleTrainID" value="<?php echo $ftid;?>" />
	<input type="hidden" name="action" value="<?php echo $mode;?>" />	
<table cellpadding="0" cellspacing="0" class="tickletable">
	<tr>
		<td class="title">Mail Content<span class="error">*</span></td>
		<td colspan="2"><textarea name="TickleMailFollowContent"
			id="TickleMailFollowContent" rows="40" cols="600" style="width: 90%"
			class="tinymce"><? echo $tickle[0]['TickleMailFollowContent'];?>
			<?if ($mode=="AddTickleFollow"){
				$user_sign=tablelist('tickleuser','signature',array("WHERE UserName ='".$_SESSION['UserName']."' and TickleID='".$_SESSION['TickleID']."'"));
				$user_signature=$user_sign[0];
				$_SESSION['sign']=$user_signature["signature"];
				if($_SESSION['sign']!="")
				{
					echo "<br><br>".$_SESSION['sign']; } } ?></textarea><br>
		<span class="errortext" id="err_TickleMailFollowContent"></span></td>
	</tr>
	<tr>
		<td class="title">Attached Files</td>
		<td colspan="2"><?if (count($Files)!=0){?>
                    <span id="loaded_files">
                        <?for ($i=0;$i<count($Files);$i++){
                            $frow = $Files[$i];
                            $fname = @trim($frow['FileName']);
                            if($fname=="" || !file_exists(FULL_UPLOAD_FOLDER.$fname)){
                                continue;
                            }?>
                        <div id="loadedFile<?=$frow['FileID']?>"><a href="javascript:void();" onclick="DeleteFile(<?=$frow['FileID']?>);return false;">x</a>&nbsp;<a href="<?=UPLOAD_FOLDER.$fname?>"><span class="file" title="File loaded: <?=$fname?>"><?=$fname?></span></a></div>
                        <?}?>
                    </span>
                    <?}?>
		<input type="file" name="TAttachFollow[]"
                       id="TAttachFollow" class="MultiFile"/><br>
		<span class="errortext" id="err_TAttachFollow"></span></td>
	</tr>
	<tr>
		<td class="title">Attach Original Message</td>
		<td colspan="2"><? if($tickle[0]['AttachMessageFollow']=="Y") $AttachMessageFollow=" checked";?>
		<input type="checkbox" name="AttachMessageFollow"
			id="AttachMessageFollow" value="Y"
			<?php echo $AttachOriginalMessage;?> /><br>
		<span class="errortext" id="err_AttachMessageFollow"></span></td>
	</tr>
	<tr>
		<td class="title">Email Priority</td>
		<td colspan="2"><?php $EmailPriorityFollow=$tickle[0]['EmailPriorityFollow'];
		$EPselected[$EmailPriorityFollow]="Selected";
		?> <select name="EmailPriorityFollow" id="EmailPriorityFollow">
			<option value="3" <?php echo $EPselected['3'];?>>Normal</option>
			<option value="5" <?php echo $EPselected['5'];?>>Low</option>
			<option value="1" <?php echo $EPselected['1'];?>>High</option>
		</select></td>
	</tr>
	<tr>
		<td class="title">Receipt confirmation</td>
		<td colspan="2"><? if($tickle[0]['TReceiptConfirm']=="Y") $TReceiptConfirm=" checked";?> <input
			type="checkbox" name="TReceiptConfirm" id="TReceiptConfirm" value="Y"<?php echo $TReceiptConfirm;?> /><br>
		<span class="errortext" id="err_TReceiptConfirm"></span></td>
	</tr>
	<tr>
		<td class="title">Do not send tickles on weekends</td>
		<td colspan="2"><? if($tickle[0]['NoWeekend']=="Y") $NoWeekend=" checked";?> <input
			type="checkbox" name="NoWeekend" id="NoWeekend" value="Y"<?php echo $NoWeekend;?> /><br>
		<span class="errortext" id="err_NoWeekend"></span></td>
	</tr>
	<tr>
		<td class="title">Approve before sending</td>
		<td colspan="2"><? if($tickle[0]['TApprove']=="Y") $TApprove=" checked";?> <input
			type="checkbox" name="TApprove" id="TApprove" value="Y"<?php echo $TApprove;?> />
			Your tickle emails will be on hold until approved from your Today's Tickles email.<br>
		<span class="errortext" id="err_TApprove"></span></td>
	</tr>
	<tr>
		<td class="title">BCC me on this tickle</td>
		<td colspan="2"><? if($tickle[0]['CCMeFollow']=="Y") $CCMe=" checked";?> <input
			type="checkbox" name="CCMeFollow" id="CCMeFollow" value="Y"<?php echo $CCMe;?> />
		When checked, well email you when your Tickles are sent.<br>
		<span class="errortext" id="err_CCMeFollow"></span></td>
	</tr>
	<tr>
		<td class="title">Tickle Intervals<br /></td>
		<td colspan="2">
		<fieldset style=""><legend><b>Schedule</b></legend>
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr style="vertical-align: top;">
				<td>
				<div id="Daily">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td><?php 
						$DailyDays=$tickle[0]['DailyDaysFollow'];
						$EndAfter=$tickle[0]['EndAfterFollow'];
						?> Send this tickle email after &nbsp;</td>
						<td><select name="DailyDaysFollow">
						<?php
						for($ix=1;$ix<=60;$ix++)
						{
							$Dsel="";
							if($DailyDays==$ix)
							{
								$Dsel="selected";
							}
							echo '<option value="'.$ix.'" '.$Dsel.'>'.$ix.'</option>';
						}
						for($ix=90;$ix<=180;$ix+=30)
						{
							$Dsel="";
							if($DailyDays==$ix)
							{
								$Dsel="selected";
							}
							echo '<option value="'.$ix.'" '.$Dsel.'>'.$ix.'</option>';
						}
						?>

						</select></td>
						<td>&nbsp; days. Repeat &nbsp;</td>
						<td><select name="EndAfterFollow">
						<?php
						for($ix=0;$ix<=11;$ix++)
						{
							$Dsel="";
							$EndAfters=$EndAfter-1;
							if($EndAfters==$ix)
							{
								$Dsel="selected";
							}
							$ixv=$ix+1;
							echo '<option value="'.$ixv.'" '.$Dsel.'>'.$ix.'</option>';
						}
						?>
						</select></td>
						<td>&nbsp; times</td>
					</tr>
				</table>
				</div>
				</td>
			</tr>
		</table>
		</div>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td class="title">Time of day</td>
		<td colspan="2">
		<table>
			<tr>
				<td><?php  

				$timeformat=explode(":",$tickle[0]['TickleTimeFollow']);
				$TickleTime=date("h:i A",strtotime($tickle[0]['TickleTimeFollow']));
				$time=$TickleTime;
				if($tickle[0]['TickleTimeFollow']=="")
				$time="12:00 PM";

				?> <input id='TickleTimeFollow' name="TickleTimeFollow" type='text'
					value='<?php echo $time;?>' size=8 maxlength=8
					onkeypress="javascript:return false;"></td>
				<td></td>
			</tr>
		</table>
</td>
	</tr>
	<tr>
		<td></td>
		<td>
		<div class="buttons">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td><input type="submit" name="SubmitButton" value="Save Changes" class="Buttons" /></td>				
			</tr>
		</table>
		</div>
		</td>
	</tr>
</table>
</form>
<?php exit;?>