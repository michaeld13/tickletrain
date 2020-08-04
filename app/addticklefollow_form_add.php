
<?php
$ntid=@trim($_GET['tid']);
$mode = "EditTickleFollow";
$fuser_sign = '';
?>
<script>
$(document).ready(function(){
		$("input[name='CustomSubjectCheckboxForFollowUp']").prop('checked', true);
		$("#CustomSubjectForFollowup1").show();
		$("input[name='TextAreaCustomSubjectForFollowup']").val(data['custom_subject']);
});
</script>
<div class="lb_holder">
    <form action="<?= Url_Create('addtickle'); ?>" method="post"
          name="TickleFormFollow" id="TickleFormFollow" enctype="multipart/form-data">
        <input type="hidden" name="TickleTrainID" value="<?php echo $ntid; ?>" />
        <input type="hidden" id="FollowTickleTrainID" name="FollowTickleTrainID" value="0" />
        <input type="hidden" id="action" name="action" value="<?php echo $mode; ?>" />
        <fieldset>
            <div class="twocolumns">
                <div class="left_col">
                    
                     
                        
                        <div class="row">
                            <label for="Subject">Subject<span class="req">*</span></label>
                            <input type="checkbox" name="CustomSubjectCheckboxForFollowUp" value="CustomSubjectCheckboxForFollowUp"/> Edit Subject
                            <span class="ico_info"><span class="info-block info-block-m">
                                                <span class="ib-t">
                                                    <span class="info-text">
                                                        Leave this unchecked if you want to use the original subject line when sending your Tickles. It will use a prefix "RE: {subject}" Or you can use your own subject by clicking the check box.
                                                    </span>
                                                </span>
                                            </span></span>
                        </div>
                        
           <div class="row" id="CustomSubjectForFollowup1" style="display: none">
           <label for="Custom Subject">Custom Subject</label>
           <div style="clear:both;"></div>
           <span class="input_text" style="width:360px;background-position:0px -44px;"><input name="TextAreaCustomSubjectForFollowup" id="TextAreaCustomSubjectForFollowup" style="width:335px; outline: none; text-align: left;"
                   value=""/></span>
           </div>
                      
                    
                    <div class="row">
                        <label for="TickleMailFollowContent">Email message <span class="req">*</span></label><div style="clear: both;"></div>
                        <div class="plugin_holder"><textarea name="TickleMailFollowContent"
                                                             id="TickleMailFollowContent1" rows="40" cols="600"
                                                             class="tinymceDialog"><?= $fuser_sign ?></textarea>
                        </div>
                    </div>
                    
                    <h2>Schedule</h2>
                    <div class="row2">
                        <label for="DailyDaysFollow">Send this Tickle</label>
                        <select name="DailyDaysFollow" id="DailyDaysFollow">
                            <?php
                            for ($ix = 1; $ix <= 60; $ix++) {
                                echo '<option value="' . $ix . '">' . $ix . '</option>';
                            }
                            for ($ix = 90; $ix <= 180; $ix += 30) {
                                echo '<option value="' . $ix . '">' . $ix . '</option>';
                            }
                            ?>

                        </select> <label for="TickleTimeFollow">days after the previous at</label>
                        <span class="input_text input_time">
                            <input id='TickleTimeFollow' name="TickleTimeFollow" type='text'
                                   value='12:00PM' maxlength=8
                                   onkeypress="javascript:return false;"></span>
                        <span class="txt">. Repeat</span>
                        <?php
                        
                        $GetFollowUpMessageQuery = mysqli_query($db->conn,"select `EndAfterFollow` from `ticklefollow` where `TickleTrainID`='".$_GET['tid']."' order by `FollowTickleTrainID`")  or die(mysqli_error($db->conn). __LINE__);
                            if(mysqli_num_rows($GetFollowUpMessageQuery) > 0){
                             while($GetFollowUpMessageRow = mysqli_fetch_assoc($GetFollowUpMessageQuery)){
                              $EndAfterFollw = $GetFollowUpMessageRow['EndAfterFollow'];
                             }
                            }
                            //echo $EndAfterFollw;
                         ?>
                        <select name="EndAfterFollow" id="EndAfterFollow">
                            <?php
                            if($EndAfterFollw == '13'){
                              echo '<option value="13" selected>&infin;</option>';  
                            }
                            for ($ix = 0; $ix <= 11; $ix++) {
                                $ixv = $ix + 1;
                                echo '<option value="' . $ixv . '">' . $ix . '</option>
                                                                                 ';
                            }
                            ?>
                        </select> <label for="EndAfterFollow">times.</label>

                        <input type="checkbox"  id="unlimited_1" value="1" name="unlimited_1"/>
                        <label for="Unlimited">Continuous</label>
                    </div>
                    <h2>Delivery options</h2>

                    <div class="row2">
                        <ul class="check_area">
                            <li>
                                <input
                                    type="checkbox" name="TApprove" id="TApproveFollow" value="Y" />
                                <label for="TApproveFollow">Approve before <span class="ico_info"><span class="info-block info-block-m">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Pauses your Tickles until you approve them. They can be approved from the Dashboard or via the Today's Tickles email notification.
                                                </span>
                                            </span>
                                        </span></span></label>

                                <div class="inner_text">sending<br>(Pause mode)<span class="ico01 active"></span></div>
                            </li>
                            <li>
                                <input
                                    type="checkbox" name="NoWeekend" id="NoWeekendFollow" value="Y" />
                                <label for="NoWeekendFollow">Do not send Tickles on <span class="ico_info"><span class="info-block">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Tickles will not be sent on Saturday and Sunday.
                                                </span>
                                            </span>
                                        </span></span></label>

                                <div class="inner_text">weekends <span class="ico02 active"></span></div>
                            </li>
                            <li>
                                <input type="checkbox" name="CCMeFollow" id="CCMeFollow" value="Y" />
                                <label for="CCMeFollow">BCC me <span class="ico_info"><span class="info-block">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Receive a copy of your Tickle when it's sent to the recipient.
                                                </span>
                                            </span>
                                        </span></span></label>

                                <div class="inner_text"><span class="ico03 active"></span></div>
                            </li>
                            <li>

                                <input type="checkbox" name="AttachMessageFollow"
                                       id="AttachMessageFollow" value="Y"
                                       onclick="CheckAttach(this)"/>&nbsp;<label for="AttachMessageFollow">Include original message text&nbsp;<span class="ico_info"><span class="info-block">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Includes original message text in the body of your outgoing Tickle.
                                                </span>
                                            </span>
                                        </span></span></label>

                                <div id="AttachMessageFollowFilesContainer" class="inner_text"
                                     style="display:none">
                                    <input type="checkbox" name="AttachMessageFollowFiles"
                                           value="Y" id="AttachMessageFollowFiles"/>
                                    <label for="AttachMessageFollowFiles">Check this box to have original e-mail file attachments resent along
                                        with your outgoing Tickle.</label>
                                    <span class="ico_info"><span class="info-block">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Original email file attachments will be resent with your Tickle.
                                                </span>
                                            </span>
                                        </span></span>
                                </div>
                            </li>
                        </ul>
                        <div class="files_holder">
                            <label for="TAttachFollow">Attached files
                                <span class="ico_info"><span class="info-block info-block-m">
                                        <span class="ib-t">
                                            <span class="info-text">
                                                Include files to be sent with your Tickle.
                                            </span>
                                        </span>
                                    </span></span></label>
                            <span id="loadedfollow_files"></span>
                            <div class="file">
                                <input type="file" name="TAttachFollow[]"
                                       id="TAttachFollow" class="MultiFile"<? // file-input-area" style="opacity:0" ?>/>

                                <? /* span class="input_text"><input class="text file-input-value" type="text"/></span>
                                  <a href="#" class="button">Browse...</a */ ?>
                            </div>
                        </div>
                    </div>
                    <h2>E-mail options</h2>

                    <div class="row">
                        <div class="row_section">
                            <label for="EmailPriorityFollow">E-mail priority</label>
                            <select name="EmailPriorityFollow" id="EmailPriorityFollow">
                                <option value="3">Normal</option>
                                <option value="5">Low</option>
                                <option value="1">High</option>
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
                            <input
                                type="checkbox" name="TReceiptConfirm" id="TReceiptConfirm" value="Y"/>
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
                    <div class="box">
                        <div class="holder">
                            <div class="frame" id="spamassassinfollow">
                                <h3>Spam check report</h3>
                                <h4>Press the button below to get SPAM rating for your email</h4>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn_blue btn_blue_green" onclick="SpamCheckFollow();
                                    return false;"><span>Rate my reply</span></a>
                </div>
            </div>
            <div class="buttons">
                <input type="submit" value="SAVE" class="btn_save" />
            </div>
        </fieldset>
    </form>
</div>
<script>
    function SpamCheckFollow() {
        var str = $("#TickleFormFollow").serialize();
        var tid = $('input:[name="TickleTrainID"]').val();
        var ftid = $('input:[name="FollowTickleTrainID"]').val();
        $.post("<?= Url_Create('spamassassin'); ?>", str, function(data) {
            $("#spamassassinfollow").html(data);
            //$("#spamassassinfollow").attr("style", "display:block; width:200px; background-color:#FFFFFF; border: solid 1px #808080; margin: 20px, 20px, 20px, 20px");
            //$("#spamassassinfollow").attr("style", "display:block; width:200px; background-color:#FFFFFF; border: solid 1px #808080; margin: 20px, 20px, 20px, 20px");
            window.location = '#topfollow';
        });
        return false;
    }
</script>
