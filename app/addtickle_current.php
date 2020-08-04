<script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/jquery.MultiFile.js"></script>
<script language="javascript">
    var timezones = <?= json_encode($timezones); ?>;
    $(document).ready(function() {

        $(".ico_info").click(function() {
            //  alert('gfdgf');  
        });

    });
</script>
<?php
$Title = "ADD";
$tid = @trim($_GET['tid']);
$ftid = @intval($_GET['ftid']);
$ntid = $tid;
$Button = "Insert";
$mode = "AddTickle";
$CreatedDate = date("Y-m-d H:i:s");
$user_signature = tablerow('tickleuser', 'signature', array("WHERE UserName ='" . $_SESSION['UserName'] . "' and TickleID='" . $_SESSION['TickleID'] . "'"));
$user_sign = @trim($user_signature["signature"]);
if ($user_sign != "") {
    $user_sign = str_replace(array("\r", "\n"), "", htmlspecialchars("<br><br>" . $user_sign)); //str_replace(array('"',"\n"),array('\"',''),$user_sign));
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

if ($tid == "") {
    $ntid = md5($CreatedDate . $_SESSION['TickleID']);
}

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
            CKEDITOR.replace('TickleMailContent', config);
            //$('textarea.tinymce').ckeditor(config);
        });
    </script>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.MultiFile').MultiFile({accept: '', max: 10});

        $("#TickleTime").datetimepicker({timeOnly: true, timeFormat: 'hh:mm TT', ampm: true});

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
                    ckeditorrequired: true
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
                $.ajax({url: '<?= Url_Create("addtickle", "") ?>', type: 'get', data: {"action": "CheckTickleGroup", "TickleName": $("#TickleName").val(), "TickleTrainID": '<?= (($tid != "") ? $tid : $ntid) ?>'}, success: function(data) {
                        var catId = parseInt(data);
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
            }
        });
        $("#TickleTimeFollow").datetimepicker({timeOnly: true, timeFormat: 'hh:mm TT', ampm: true});

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
    function EditFollow(ftid) {
        ClearForm();
        //$("#spamassassinfollow").attr("style", "display:none");
        if (ftid > 0) {
            $("#FollowUpForm").attr("title", "Update Follow-Up Tickle");
            $.getJSON('/<?= ROOT_FOLDER ?>addtickle/?action=LoadTickleFollow&tid=<?= $ntid ?>&ftid=' + ftid,
                    function(data) {
                        PopulateForm("EditTickleFollow", data);
                    });
        } else {
            $("#FollowUpForm").attr("title", "Add Follow-Up Tickle");
        }
        $(window).scrollTop(0);
        $("#FollowUpForm").dialog({width: 1000, position: 'top', modal: true,
            open: function() {
                CKEDITOR.replace('TickleMailFollowContent', config);
            },
            close: function() {
                CKEDITOR.instances['TickleMailFollowContent'].destroy();
            }
        });
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
        $('input[type="checkbox"]').bind('click', function()
        {
            if ($(this).is(":checked") == true) {
                $('#EndAfter').attr('disabled', 'disabled');
            } else {
                $('#EndAfter').removeAttr('disabled');
            }
        });
    });
</script>

<div class="main_holder edit_page">
    <div class="heading">
        <h1><?= $Title ?> Tickle</h1>
    </div>
    <form action="<?= Url_Create('addtickle'); ?>" method="post"
          name="TickleForm" id="TickleForm" enctype="multipart/form-data">
        <input type="hidden" name="TickleTrainID" value="<?php echo $tid; ?>"/>
        <input type="hidden" name="NTickleTrainID" value="<?php echo $ntid; ?>"/>
        <input type="hidden" name="action" value="<?php echo $mode; ?>"/>
        <input type="hidden" name="reload" value="0" id="reload_field"/>
        <input type="hidden" name="spamcheck" value="no" id="spamcheck">
        <input type="hidden" name="TickleContact" value="0" id="ticklecontact_field">
        <fieldset>
            <div class="twocolumns">
                <div class="left_col">
                    <div class="row">
                        <label for="TickleName">Tickle name <span class="req">*</span></label>
                        <span class="input_text" style="width:340px;background-position:0 -44px;"><input name="TickleName" id="TickleName" value="<?= $tickle[0]['TickleName'] ?>"
                                                                                                         maxlength="50" type="text" style="width:330px"/></span>
                    </div>
                    <div class="row">
                        <label for="TickleMailContent">Email message <span class="req">*</span></label><div style="clear: both;"></div>
                        <div class="plugin_holder"><textarea name="TickleMailContent"
                                                             id="TickleMailContent" rows="40" cols="600"
                                                             class="tinymce"><?= @trim($tickle[0]['TickleMailContent']) ?><?= (($mode != "EditTickle") ? $user_sign : "") ?></textarea>
                        </div>
                    </div>
                    <h2>Schedule</h2>
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
                        <label for="DailyDays">Send this Tickle after</label>
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
                            ?>

                        </select> <label for="TickleTime">days at</label>
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
                        <span class="txt">. Repeat</span>
                        <select name="EndAfter" id="EndAfter">
                            <?php
                            for ($ix = 0; $ix <= 11; $ix++) {
                                $Dsel = "";
                                $EndAfters = $EndAfter - 1;
                                if ($EndAfters == $ix) {
                                    $Dsel = "selected";
                                }
                                $ixv = $ix + 1;
                                echo '<option value="' . $ixv . '" ' . $Dsel . '>' . $ix . '</option>
                                                             ';
                            }
                            ?>
                        </select> <label for="EndAfter">times.</label>
                        <span class="txt">.</span>
                        <input type="checkbox"  id="unlimited" value="1" name="unlimited"/>
                        <label for="Unlimited">Unlimited.</label>
                    </div>
                    <h2>Delivery options</h2>

                    <div class="row2">
                        <ul class="check_area">
                            <li>
                                <?
                                if ($tickle[0]['TApprove'] == "Y" || $mode == 'AddTickle')
                                    $TApprove = " checked";
                                ?> <input
                                    type="checkbox" name="TApprove" id="TApprove" value="Y"<?php echo $TApprove; ?> />
                                <label for="TApprove">Approve before<span class="ico_info"><span class="info-block info-block-m">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Pauses your Tickles until you approve them. They can be approved from the Dashboard or via the Today's Tickles email notification.
                                                </span>
                                            </span>
                                        </span></span></label>

                                <div class="inner_text">sending<br>(Pause mode)<span class="ico01 active"></span></div>
                            </li>
                            <li>
                                <?
                                if ($tickle[0]['NoWeekend'] == "Y")
                                    $NoWeekend = " checked";
                                ?> <input
                                    type="checkbox" name="NoWeekend" id="NoWeekend" value="Y"<?php echo $NoWeekend; ?> />
                                <label for="NoWeekend">Do not send Tickles on <span class="ico_info"><span class="info-block">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Tickles will not be sent on Saturday and Sunday.
                                                </span>
                                            </span>
                                        </span></span></label>

                                <div class="inner_text">weekends <span class="ico02 active"></span></div>
                            </li>
                            <li>
                                <?
                                if ($tickle[0]['CCMe'] == "Y")
                                    $CCMe = " checked";
                                ?> <input
                                    type="checkbox" name="CCMe" id="CCMe" value="Y"<?php echo $CCMe; ?> />
                                <label for="CCMe">BCC me <span class="ico_info"><span class="info-block">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Receive a copy of your Tickle when it's sent to the recipient.
                                                </span>
                                            </span>
                                        </span></span></label>

                                <div class="inner_text"><span class="ico03 active"></span></div>
                            </li>
                            <li>
                                <?
                                if ($tickle[0]['AttachOriginalMessage'] != "N")
                                    $AttachOriginalMessage = " checked";
                                ?>
                                <input type="checkbox" name="AttachOriginalMessage"
                                       id="AttachOriginalMessage" value="Y"
                                       <?php echo $AttachOriginalMessage; ?> onclick="CheckAttach(this)"/>
                                <label for="AttachOriginalMessage">Include original message text <span class="ico_info"><span class="info-block">
                                            <span class="ib-t">
                                                <span class="info-text">
                                                    Includes original message text in the body of your outgoing Tickle.
                                                </span>
                                            </span>
                                        </span></span></label>

                                <div id="AttachOriginalMessageFilesContainer" class="inner_text"
                                     style="display:<?= (($tickle[0]['AttachOriginalMessage'] != "N") ? "" : "none") ?>">
                                    <input type="checkbox" name="AttachOriginalMessageFiles" id="AttachOriginalMessageFiles"
                                           value="Y"<?= (($tickle[0]['AttachOriginalMessage'] == 'A') ? " checked" : "") ?> />
                                    <label for="AttachOriginalMessageFiles">Check this box to have original e-mail file attachments resent along
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
                                        if ($fname == "" || !file_exists(FULL_UPLOAD_FOLDER . $fname)) {
                                            continue;
                                        }
                                        ?>
                                        <div id="loadedFile<?= $frow['FileID'] ?>"><a href="javascript:void();"
                                                                                      onclick="DeleteFile(<?= $frow['FileID'] ?>);
                                                                                                      return false;">x</a>&nbsp;<a
                                                                                      href="<?= UPLOAD_FOLDER . rawurldecode($fname) ?>" onclick="wopen(this);
                                                                                                      return false"><span class="file"
                                                    title="File loaded: <?= $fname ?>"><?= $fname ?></span></a>
                                        </div>
                                    <? } ?>
                                </span>
                            <? } ?>
                            <div class="file">
                                <input type="file" name="TAttach[]"
                                       id="TAttach" class="MultiFile"<? // file-input-area" style="opacity:0"    ?>/>

                                <? /* span class="input_text"><input class="text file-input-value" type="text"/></span>
                                  <a href="#" class="button">Browse...</a */ ?>
                            </div>
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
                    <div class="box">
                        <div class="holder">
                            <div class="frame" id="spamassassin">
                                <h3>Spam check report</h3>
                                <h4>Press the button below to get SPAM rating for your email</h4>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn_blue btn_blue_green" onclick="SpamCheck();
                            return false;"><span>Rate my reply</span></a>
                </div>
            </div>
            <? if ($ntid != "" && count($ticklefollow) != 0) { ?>
                <h2>Follow-up Tickles</h2>
                <script type="text/javascript" charset="utf-8">
                    $(document).ready(function() {
                        reloadFollowUps();
                    });
                </script>
                <div id="followcontainer">
                </div>
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
?>