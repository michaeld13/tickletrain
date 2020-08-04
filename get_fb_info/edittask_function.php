<?php
//$Variables['RemoveHeader'] = 1;
$err = "";
$GLOBALS['mode'] = 0;

if ($_POST['submit'] == "Update") {
    $TaskID = $_POST['TaskID'];
    $TaskDate = $_POST['TaskDate'];
    $MailID = $_POST['MailID'];
    $dt = date_create_from_format("m-d-Y h:i A",$TaskDate);
    $UpDate = date_format($dt, "Y-m-d H:i:s");
    $UPUser = $db->select_to_array('tickleuser', '', " Where TickleID='" . $_SESSION['TickleID'] . "'");
    $Tasks = $db->select_to_array('task', '', " Where MailID='$MailID' and TickleID='" . $_SESSION['TickleID'] . "' and TaskID>='$TaskID' and Status='Y' order by TaskID");
    $CTickle = $db->select_to_array('tickle', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and TickleTrainID='" . $Tasks[0]['TickleTrainID'] . "'");
    $FTickle = $db->select_to_array('ticklefollow', '', " where TickleTrainID='" . $Tasks[0]['TickleTrainID'] . "'");
    $Tfollow = array();
    foreach ($FTickle as $trow) {
        $Tfollow[$trow['FollowTickleTrainID']] = $trow;
    }
    $cnt = count($Tasks);
    $ttimezone = gettimezone($Tasks[0]['TimeZone']);
    $getservertz = date_default_timezone_get();
    date_default_timezone_set($ttimezone);
    $fdate = strtotime($Tasks[0]['TaskInitiateDate']);
    $cdate = strtotime($UpDate);
    if (date('Ymd', $fdate) == date('Ymd', $cdate)) {
        $cnt = 1;
    }
    //echo $getservertz.'<br/>';
    date_default_timezone_set($getservertz);
    
//    $date = date("Y-m-d H:i:s");
//    $timeHere = strtotime($date);
//    $timeHere = $time - (3.5 * 60);
    
    if (date('YmdHi',$cdate) >= date('YmdHi',time())) {
        //        $err = "The time you selected has already passed. Please, select a time after " . date("M-d-Y h:i A") . " to properly update the time of the day the tickle should be sent";
        //    } else {*/
        for ($j = 0; $j < $cnt; $j++) {
            $NoWeekend = @trim($CTickle[0]['NoWeekend']);
            $dailyDays = @intval($CTickle[0]['DailyDays']);
            if (intval($Tasks[$j]['FollowTickleTrainID'])) {
                $ftickle = $Tfollow[intval($Tasks[$j]['FollowTickleTrainID'])];
                $NoWeekend = @trim($ftickle['NoWeekend']);
                $dailyDays = @intval($ftickle['DailyDaysFollow']);
            }
            if ($j > 0) {
                $cdate += 3600 * 24 * $dailyDays;
                $getservertz = date_default_timezone_get();
                date_default_timezone_set('Etc/GMT-0');
                $ttime = strtotime($Tasks[$j]['TaskGMDate']);
                $cdate = mktime(intval(gmdate("H", $ttime)), intval(gmdate("i", $ttime)), intval(gmdate("s", $ttime)), intval(gmdate("m", $cdate)), intval(gmdate("d", $cdate)), intval(gmdate("Y", $cdate)));
                date_default_timezone_set($getservertz);

                /*if (intval($Tasks[$j]['FollowTickleTrainID']) != intval($Tasks[0]['FollowTickleTrainID'])) {
                    $getservertz = date_default_timezone_get();
                    date_default_timezone_set('Etc/GMT-0');
                    $ttime = strtotime($Tasks[$j]['TaskGMDate']);
                    $cdate = mktime(intval(gmdate("H", $ttime)), intval(gmdate("i", $ttime)), intval(gmdate("s", $ttime)), intval(gmdate("m", $cdate)), intval(gmdate("d", $cdate)), intval(gmdate("Y", $cdate)));
                    date_default_timezone_set($getservertz);
                }*/
                //$getservertz = date_default_timezone_get();
                //date_default_timezone_set('Etc/GMT-0');
                $dofweek = intval(gmdate('w', $cdate));
                while ($NoWeekend == 'Y' && ($dofweek == 0 || $dofweek == 6)) {
                    $cdate += 3600 * 24;
                    $dofweek = intval(gmdate('w', $cdate));
                }
            }
            $nday = gmdate("Y-m-d H:i:s", $cdate);
            //date_default_timezone_set($getservertz);

            $iday = getlocaltime($nday, $Tasks[$j]['TimeZone']);
            mysqli_query($db->conn,"update task set TaskInitiateDate='" . $iday . "', TaskGMDate='" . $nday . "' where TaskID=" . $Tasks[$j]['TaskID']);
        }
    }

	$surl = '?';
	foreach(json_decode(base64_decode($_POST['redirectUrl'])) as $key => $redirectUrl01)
	{
		if($key!='u'){ $surl .= $key.'='.$redirectUrl01.'&';} 
	}

    //redirect("home".$surl);
	header("location:https://client.tickletrain.com/home/".substr($surl,0,-1)."#".$_POST['hashtag']);
    //$msg="Date and time successfully updated";
    //echo $msg;
    exit;
    //}
}

if ($_REQUEST['act'] != "") {
    $act = unprotect(rawurldecode($_GET['act']));
    $action = explode("-", $act);
    $check_login = loginByTickle($action[0]);
    if ($check_login != 0) {
        redirect('login');
    }
    $TaskID = @intval($action[1]);
    $GLOBALS['mode'] = 1;
	
} else {
    $TaskID = @intval($_GET['TaskID']);
}
$task = $db->select_to_array('task', '', " Where TickleID='" . $_SESSION['TickleID'] . "' and Status='Y' and TaskID=" . $TaskID);
if ($_REQUEST['act'] != "" && (!is_array($task) || count($task)<1)){
    $GLOBALS['mode']=6;return;
}

$getservertz = date_default_timezone_get();
$tmzone = $_SESSION['TimeZone'];
date_default_timezone_set($tmzone);
$cdate = date("Y-m-d H:i:s");

$TickleMailID = 0;
if (count($task)) {
    $cdate = $task[0]['TaskGMDate'];
    $tmzone = $task[0]['TimeZone'];
    $TickleMailID = $task[0]['MailID'];
}
date_default_timezone_set("UTC");
$time = strtotime($cdate);
date_default_timezone_set($tmzone);
$tdiff = intval(date("Z"));

//$TickleTime = date("h:i A", $time);
$TickleDate = date("m-d-Y h:i A", $time);
$prevTask = $db->select_to_array('task', '', ' where TaskID<' . $TaskID . ' and MailID=' . $TickleMailID . ' and status="y" order by TaskID desc');
$ptime = 0;
if (count($prevTask)) {
    date_default_timezone_set("UTC");
    $ptime = strtotime($prevTask[0]['TaskGMDate']);
}

date_default_timezone_set($getservertz);
ob_start();
?>
    
	$(document).ready(function () {
		//$("#TaskDate").blur();
        $("#TaskDate").datetimepicker({dateFormat:'mm-dd-yy', timeFormat:'hh:mm TT', ampm:true});
		<?if ($GLOBALS['mode']==1){?>
			$("#TaskDate").datetimepicker("show");
		<?}?>
		//$("#TaskDate").focus();
    });

    function dateTimeStringToDate(input, settings) {
        var hours = 0;
        var minutes = 0;
        var days = 0;
        var monthes = 0;
        var years = 0;
        if (input) {
            var parts = input.split(settings.partSeparator);
            if (parts[0] != '') {
                var array = parts[0].split(settings.dateSeparator);
                monthes = parseFloat(array[0]) - 1;
                days = parseFloat(array[1]);
                years = parseFloat(array[2]);
            }
            if (parts[1] != '') {
                var array = parts[1].split(settings.separator);
                hours = parseFloat(array[0]);
                minutes = parseFloat(array[1]);

// Convert AM/PM hour to 24-hour format.
                if (!settings.show24Hours) {
                    if (hours === 12 && input.indexOf('AM') !== -1) {
                        hours = 0;
                    }
                    else if (hours !== 12 && input.indexOf('PM') !== -1) {
                        hours += 12;
                    }
                }
            }
        }

        var localTime = new Date(years, monthes, days, hours, minutes, 0);
        var taskdiff = <?= $tdiff ?>;
//alert(localTime.getTimezoneOffset()+','+taskdiff);
        var ms = localTime.getTime() - (localTime.getTimezoneOffset() * 60000) - taskdiff * 1000;
        return new Date(ms)
    }

    function checkDate() {
        $("#errMsg").html("");
        var dt = $("#TaskDate").val();//+' '+$("#TaskTime").val();
        var sdate = dateTimeStringToDate(dt, {show24Hours:false, separator:':', dateSeparator:'-', partSeparator:' '});

var cdate = new Date();

        if (cdate.getTime() > sdate.getTime()) {
            var taskdiff = <?= $tdiff ?>;
//alert(cdate.getTimezoneOffset()+','+taskdiff);
            var ms = cdate.getTime() + (cdate.getTimezoneOffset() * 60000) + taskdiff * 1000;
            cdate = new Date(ms)
//alert(cdate.toUTCString());
            $("#errMsg").html("The time you selected has already passed. Please, select a time after " + $.datepicker.formatDate('dd MM yy',cdate)+' '+$.datepicker.formatTime('hh:mm TT', {hour:cdate.getHours(),minute:cdate.getMinutes()}, {ampm:true}) + " to properly update the time of the day the Tickle should be sent.");
            return false;
        }
        if (<?= $ptime ?>*
        1000 > sdate.getTime()
    )
        {
            var pdate = new Date(<?= $ptime ?>* 1000
        )
            ;
            $("#errMsg").html("Please select a date after " + $.datepicker.formatDate('dd MM yy',pdate)+' '+$.datepicker.formatTime('hh:mm TT', {hour:pdate.getHours(),minute:pdate.getMinutes()}, {ampm:true}) + ". You cannot reschedule a Tickle ahead of another.");
            return false;
        }
        return true;
    }
<? $GLOBALS['hheader'] = ob_get_clean(); ?>
<form action="<?= Url_Create("edittask") ?>" method="post" name="TaskEdit" id="TaskEdit" onsubmit="return checkDate()">
    <input type="hidden" name="TaskID" value="<?php echo $_GET['TaskID']; ?>"/>
    <input type="hidden" name="MailID" value="<?php echo $_GET['MailID']; ?>"/>
    <input type="hidden" name="hashtag" value="<?php echo $_GET['hashtag']; ?>"/>
	<input type="hidden" name="redirectUrl" value="<?php echo base64_encode($_GET['qstr']); ?>"/>
    <fieldset>
        <div class="row">
            <span class="input_text">
                <input type="text" name="TaskDate" id="TaskDate" value="<?php echo $TickleDate; ?>"
                       onkeypress="javascript:return false;"/>
            </span>
        </div>
        <div class="submit_holder">
            <input type="submit" name="submit" value="Update"/>
        </div>
    </fieldset>
    <div class="error" id="errMsg"><?= $err ?></div>
</form>
<?
$GLOBALS['hcontent'] = ob_get_clean();
ob_end_clean();
if ($GLOBALS['mode'] == 1) {
    $GLOBALS['hcontent'] = preg_replace("/[\\r\\n]/", '', $GLOBALS['hcontent']);
} else {
    ?>
<script type="text/javascript">
        <?=$GLOBALS['hheader']?>
</script>
    <?= $GLOBALS['hcontent'] ?>
<? exit;
} ?>
