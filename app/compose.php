<?php
if ($_GET['TaskID'] > 0) {
    $task = $db->select_to_array('task', '', " Where TaskID='" . $_GET['TaskID'] . "' and TickleID='" . $_SESSION['TickleID'] . "' and Status='Y'");
    $user_mail = $db->select_to_array('user_mail', '', " Where MailID='" . $task[0]['MailID'] . "' and TickleID='" . $_SESSION['TickleID'] . "' and Status='Y'");
    $toaddress = extract_emails_from($user_mail[0]['toaddress']);
    $ccaddress = extract_emails_from($user_mail[0]['ccaddress']);
    $ToAddress = implode(",", $toaddress);
    $CcAddress = implode(",", $ccaddress);
}
include_once "includes/ckeditor_inc.php";
?>
<!-- Load TinyMCE -->
<script type="text/javascript">
    $(document).ready(function () {
        $('textarea.tinymce').ckeditor(config);
    });

    function validcompose(frm) {
        var re = /^[a-z\.\-_0-9]+@([0-9a-z_\-]+\.)+[a-z]{2,4}$/ig;
	var re1 = /^[a-z\+\.\-_0-9]+@([0-9a-z_\-]+\.)+[a-z]{2,4}$/ig;
        var val = $.trim(frm.ToAddress.value);
        if ( val == "" || !val.match(re)) {
            alert("Please enter to valid email address");
            frm.ToAddress.focus();
            return false;
        }
        var val = $.trim(frm.CcAddress.value);
        if ( val != "" && !val.match(re)) {
            alert("Please enter to valid cc address");
            frm.CcAddress.focus();
            return false;
        }
        var val = $.trim(frm.BccAddress.value);
        if ( val != "" && !val.match(re1)) {
            alert("Please enter to valid bcc address");
            frm.BccAddress.focus();
            return false;
        }

        var val = $.trim(frm.Subject.value);
        if (val == "") {
            alert("Please enter Subject");
            frm.Subject.focus();
            return false;
        }
        var TickleMailContent = CKEDITOR.instances['TickleMailContent'].getData();
        if ($.trim(TickleMailContent) == "") {
            alert("Please enter message");
            $("TickleMailContent").focus();
            return false;
        }
    }
</script>
<div class="main_holder edit_page">
    <div class="heading">
        <h1 class="head">Compose Mail</h1>
    </div>
    <form name="composemail" action="" method="post" onsubmit="javascript:return validcompose(this);">
        <div class="error"><?php echo $MSG?></div>
        <div class="form_holder">
            <fieldset>
                <div class="left_side">
                    <div class="row">
                        <label for="ToAddress">To</label>
            <span class="input_text">
                <input type="text" name="ToAddress" id="ToAddress" value="<?=$ToAddress;?>" size="50"/>
            </span>
                    </div>
                    <div class="row">
                        <label for="CcAddress">Cc</label>
            <span class="input_text">
                <input type="text" id="CcAddress" name="CcAddress" value="<?=$CcAddress;?>" size="50"/>
            </span>
                    </div>
                    <div class="row">
                        <label for="BccAddress">Bcc</label>
            <span class="input_text">
                <input type="text" name="BccAddress" id="BccAddress" value="" size="50"/>
            </span>
                    </div>
                    <div class="row">
                        <label for="Subject">Subject</label>
            <span class="input_text">
                <input type="text" name="Subject" id="Subject" value="" size="50"/>
            </span>
                    </div>
                </div>
                <div class="right_side">
                    <h3>Message</h3>

                    <div class="plugin_holder">
                        <textarea name="TickleMailContent" id="TickleMailContent" rows="40" cols="600"
                                  style="width: 90%"
                                  class="tinymce"><? echo $tickle[0]['TickleMailContent'];?></textarea>
                    </div>
                    <div class="submit_holder">
                        <input type="submit" name="submit" value="Send"/>
                    </div>
                </div>
            </fieldset>
        </div>
    </form>
</div>
