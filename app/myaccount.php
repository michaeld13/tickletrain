<?php
use PHPMailer\PHPMailer\PHPMailer;


require_once(dirname(__DIR__)."/includes/class/PHPMailer/src/Exception.php");
require_once(dirname(__DIR__)."/includes/class/PHPMailer/src/PHPMailer.php");
require_once(dirname(__DIR__)."/includes/class/PHPMailer/src/SMTP.php");

    $mailchkSmtp = new PHPMailer(false);

$facebookaccount = $_SESSION["facebookaccount"];
$encryption = array('0' => 'None', 'ssl' => 'SSL', 'tls' => 'TLS');
$imap_port = '993';
$imap_authentication = 'ssl';
$imap_hostname = "imap.mail.yahoo.com";


$user_det = tablelist('tickleuser', '', array("WHERE UserName ='" . $_SESSION['UserName'] . "' and TickleID='" . $_SESSION['TickleID'] . "'"));
$user_details = $user_det[0];

$user_gauth = tablelist('google_auth_tokens', '', array("WHERE userid ='" . $_SESSION['TickleID'] . "'"));

if ($user_details['email_addon'] == '') {
    setcookie('sec_email', $user_details['EmailID'], time() + (86400 * 30), "/myaccount/");
}

$user_details['signature'] =  !empty(trim($user_details['signature']))?trim($user_details['signature']):'Thank you!';
$user_details['FromEmail'] =  !empty(trim($user_details['FromEmail']))?trim($user_details['FromEmail']):$user_details['UserName'] . "@tickletrain.com";


$TimeZone = $user_details['TimeZone'];

list($uregion, $uzone) = explode("/", trim($TimeZone), 2);
if (!$uregion || !$uzone) {
    $uregion = "America";
    $uzone = "Chicago";
}
$ntimezones = timezone_identifiers_list();
$timezones = array();

foreach ($ntimezones as $val) {
    list($region, $zone) = explode("/", trim($val), 2);
    if (!$zone) {
        continue;
    }
    if (!isset($timezones[$region])) {
        $timezones[$region] = array();
    }
    $timezones[$region][] = $zone;
}

//$imap_userame  = $user_details['EmailID'];

  if(!empty($_POST)){
       // echo "<pre>";
       // print_r($_POST); 
       // print_r($user_details); 
       // die;
  }

?>
<script language="javascript">
    var timezones = <?= json_encode($timezones); ?>;
    $(document).ready(function() {
        
        var timezonev = $("#TimezoneRegion").val();
        if (timezonev == 'America') {
            $('#Timezone').prepend('<option value="">---USA---</option><option value="America/New_York">Eastern</option><option value="America/Los_Angeles">Pacific</option><option value="America/Chicago">Central</option><option value="America/Denver">Mountain</option><option value="America/Anchorage">Alaska</option><option>---Other---</option>');

        }
        $("#TimezoneRegion").change(function() {
            $("#Timezone").empty();
            var region = $(this).val();
            if (region == 'America') {
                $("#Timezone").html('<option value="">---USA---</option><option value="America/New_York">Eastern</option><option value="America/Los_Angeles">Pacific</option><option value="America/Chicago">Central</option><option value="America/Denver">Mountain</option><option value="America/Anchorage">Alaska</option><option>---Other---</option>');
            }
            $.map(timezones[region], function(val) {
                var opt = $('<option>').val(region + "/" + val).text(val);
                $('#Timezone').append(opt);
            });
        });
    });
</script>
<div class="main_holder">
    <div class="heading">
        <?php
        $emailType01 = explode('@', $user_details['EmailID']);

        $emailType = $emailType01[1];
        if (!empty($user_gauth['access_token'])) {
            $emailType = 'gmail.com';
        }
        unset($_SESSION['access_token']);
        $gfpath01 = str_replace('app', '', __DIR__);
        $gfpath = $gfpath01 . 'google_auth2/';
        require_once $gfpath . 'src/Google_Client.php'; // include the required calss files for google login
        require_once $gfpath . 'src/contrib/Google_PlusService.php';
        require_once $gfpath . 'src/contrib/Google_Oauth2Service.php';

        $client = new Google_Client();
        $client->setApplicationName("Asig 18 Sign in with GPlus"); // Set your applicatio name
        $client->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/plus.me', 'https://mail.google.com', 'https://www.googleapis.com/auth/gmail.send', 'https://www.googleapis.com/auth/gmail.compose', 'https://www.googleapis.com/auth/gmail.modify')); // set scope during user login
        $client->setClientId('799405691032-er3cilvjgrqgtlfreuffllvkp2ouvrjb.apps.googleusercontent.com'); // paste the client id which you get from google API console
        $client->setClientSecret('QYmRweaDw20scMLTidBR8MRB'); // set the client secret
        $client->setRedirectUri('https://client.tickletrain.com/myaccount/'); // paste the redirect URI where you given in APi console. You will get the Access Token here during login success

        $plus = new Google_PlusService($client);
        $oauth2 = new Google_Oauth2Service($client); // Call the OAuth2 class for get email address 

        if ($client->getAccessToken()) {
            $user = $oauth2->userinfo->get();
            // echo '<pre>'; print_r($user);
            $me = $plus->people->get('me');
            $optParams = array('maxResults' => 100);
            $activities = $plus->activities->listActivities('me', 'public', $optParams);
            // The access token may have been updated lazily.
            $_SESSION['access_token'] = $client->getAccessToken();
            $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL); // get the USER EMAIL ADDRESS using OAuth2
        } else {
            $authUrl = $client->createAuthUrl();
        }

        if (isset($_GET['code'])) {
            $client->authenticate(); // Authenticate
            $_SESSION['access_token'] = $client->getAccessToken(); // get the access token here         
            $getGoogleToken = $client->getAccessToken();
            $getGoogleToken02 = json_decode($getGoogleToken, true);
            $_SESSION['acctkn'] = $getGoogleToken02['access_token'];
            $user = $oauth2->userinfo->get();
            $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
            $user_details['EmailID'];
            if ((strcasecmp($email, $user_details['EmailID']) == 0 && strcasecmp($_COOKIE['sec_email'], $user_details['EmailID']) == 0) || ($email == $user_details['EmailID'] && $_COOKIE['sec_email'] == $user_details['EmailID'])) { // 30-apr-2016 case-insensitive email comparison
                if ($_SESSION['access_token'] != '') {
                    $tokenDetail = json_decode($_SESSION['access_token'], true);
                    mysqli_query($db->conn,"insert into google_auth_tokens (userid, access_token, token_type, expires_in, id_token, refresh_token,created) values ('" . $_SESSION['TickleID'] . "','" . $tokenDetail['access_token'] . "','" . $tokenDetail['token_type'] . "','" . $tokenDetail['expires_in'] . "','" . $tokenDetail['id_token'] . "','" . $tokenDetail['refresh_token'] . "','" . $tokenDetail['created'] . "')");
                    mysqli_query($db->conn,"update tickleuser set DMSmtp='',DMPort='', DMUser='', DMPwd='', imap_host='', DMSecure='', imap_userame='', imap_passowrd='', imap_port='', imap_secure='', imapOff='0', DMSmtpOff='0' where TickleID='" . $_SESSION['TickleID'] . "'");
                    redirect('myaccount');
                }
            } else if ((strcasecmp($_COOKIE['sec_email'], $email) == 0) || ($_COOKIE['sec_email'] == $email)) {      // 30-apr-2016 case-insensitive email comparison
                if ($_SESSION['access_token'] != '') {
                    $tokenDetail = json_decode($_SESSION['access_token'], true);
                   mysqli_query($db->conn,"update secondaryEmail set use_authtoken='1',authtoken='" . $tokenDetail['access_token'] . "',refresh_token='" . $tokenDetail['refresh_token'] . "', DMUse='1' where EmailID='" . $_COOKIE['sec_email'] . "' and TickleID='" . $_SESSION['TickleID'] . "'");
                    
                   mysqli_query($db->conn,"update secondaryEmail set DMSmtp='',DMPort='', DMUser='', DMPwd='',DMSecure='', imap_host='', imap_userame='', imap_passowrd='', imap_port='', imap_secure='', imapOff='0', DMSmtpOff='0' where EmailID='" . $_COOKIE['sec_email'] . "' and TickleID='" . $_SESSION['TickleID'] . "'");
                    
                }
            } else {
                echo "<font style='color:red;'>Please Authenticate as " . $_COOKIE['sec_email'] . "</font>";
            }
        }

        $checkToken = mysqli_num_rows(mysqli_query($db->conn,"select id from google_auth_tokens where userid='" . $_SESSION['TickleID'] . "' "));
        if ($checkToken == '0') {
            $auth = "smtp";
            echo"<input type='hidden' value='smtp' id='oauthcheck'>";
        } else {
            $auth = "authToken";
            echo"<input type='hidden' value='oauth' id='oauthcheck'>";
        }
        ?>
        <h1>My account</h1>
    </div>
    <div><?php echo $Form->ErrorString . $Form->ErrSufix; ?></div>
    <div style="height: 19px">&nbsp;</div>
    <div><?php echo $Form->ErrorString . $Form->ErrSufix; ?></div>
    <form action="<?= Url_Create('myaccount'); ?>" method="post" name="MyAccount" id="MyAccount">
        <fieldset>
            <div class="form_holder">
                <div class="left_side">
                    <div class="holder">
                        <div class="frame">
                            <h2>Account settings</h2>
                            <div class="row">
                                <label for="FirstName">First name <span class="req">*</span></label>
                                <span class="input_text"><input type="text" name="FirstName" id="FirstName"
                                                                value="<? echo $user_details['FirstName']; ?>"
                                                                size="32"/></span>
                            </div>
                            <div class="row">
                                <label for="LastName">Last name <span class="req">*</span></label>
                                <span class="input_text"><input type="text" name="LastName" id="LastName"
                                                                value="<? echo $user_details['LastName']; ?>"
                                                                size="32"/></span>
                            </div>            
                            <div class="row">
                                <label for="Timezone">Time Zone <span class="req">*</span></label>
                                <select name="TimezoneRegion" id="TimezoneRegion" style="width:49%">
                                <?
                                $tregions = array_keys($timezones);
                                foreach ($tregions as $region):
                                 ?>
                                        <option value="<?= $region ?>"<?= (($region == $uregion) ? " selected" : "") ?>><?= $region ?></option>
                                <? endforeach ?>
                                </select><span style="float: left">/</span>
                                <select name="Timezone" id="Timezone" style="width:49%">
                                <? foreach ($timezones[$uregion] as $zone): ?>
                                        <option value="<?= $uregion . "/" . $zone ?>"<?= (($zone == $uzone) ? " selected" : "") ?>><?= $zone ?></option>
                                <? endforeach ?>
                                </select>
                            </div>
                            <br>
                            <div class="row">
                                <label for="Username">Username <span class="req">*</span></label>
                                <span class="input_text"><input type="text" name="UserName" id="Username" value="<? echo $user_details['UserName']; ?>" size="32" onkeypress="javascript:return charnumbersonly(event);" readonly="readonly"/></span> <!-- readonly="readonly" -->
                            </div>
                            <div class="row">
                                <label for="Password">Password <span class="req">*</span></label>
                                <span class="input_text"><input type="password" name="Password" id="Password" value="Password" size="32" readonly="readonly"/></span>
                                    </div>
                                    <div class="row">
                                        <label for="EmailID">E-mail ID <span class="req">*</span></label>
                                        <span class="input_text"><input type="text" name="EmailID" id="EmailID" readonly="readonly"  value="<? echo $user_details['EmailID']; ?>" size="32" /></span><!-- readonly="readonly" -->
                                        <input type="submit" name="update_email" onclick="open_popup();return false;" value="Edit" class="btn_blue show-hide" style="margin-left:5px;margin-top:5px;background-color: #0090C7;border: none;border-radius: 2px;padding: 5px;">
                                    </div>
                                    <?php if ($user_details['Plan'] == 1) { ?>
                                        <hr>
                                        <label for="EmailID" style="color: #ff5300;margin: 0px 0px 9px;float: left;width: 100%;font: bold 16px/18px Arial, Helvetica, sans-serif;padding: 0 0 7px;">Additional email accounts
                                            <span class="ico_info">
                                                <span class="info-block info-block2">
                                                    <span class="ib-t">
                                                        <span class="info-text" style="z-index:9999;">You can use multiple email accounts with TickleTrain.  This allows you to use the same Tickle BCC addresses with other email accounts.  This option is available on paid plans only.</span>
                                                    </span>
                                                </span>
                                            </span>
                                        </label>

                                                <?php
                                                $timestamp = time();
                                                $autoauthkey = "abcXYZ123";
                                                $hash = sha1($user_details['EmailID'] . $timestamp . $autoauthkey);
                                                $acturl = urlencode("cart.php?a=add&billingcycle=annually&pid=4&aid=5");
                                                $updateurl = 'https://secure.tickletrain.com/dologin.php?email=' . $user_details['EmailID'] . '&timestamp=' . $timestamp . '&hash=' . $hash . '&goto=' . $acturl;
                                                ?>
                                        <div class="row">
                                            <a href="<?php echo $updateurl; ?>" class="btn_blue show-hide">
                                                <span>Add additional email</span>
                                            </a>
                                        </div>
                                    <?php
                                    } else {

                                        //Check Count from secondary table
                                        $sec = mysqli_query($db->conn,"select * from secondaryEmail where TickleID='" . $_SESSION['TickleID'] . "' and isdelete='0'");
                                        $countSecondary = mysqli_num_rows($sec);
                                        //Check Count from secondary table


                                        $postUrl = "https://secure.tickletrain.com/get_addon_info.php";
                                        $postdata = array(
                                            'get_addon_status' => true,
                                            'addon_hosting_id' => $user_details['addon_hosting_id']
                                        );
                                        $ch = curl_init();
                                        curl_setopt($ch, CURLOPT_URL, $postUrl);
                                        curl_setopt($ch, CURLOPT_POST, true);
                                        curl_setopt($ch, CURLOPT_POST, count($postdata));
                                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                        $response = curl_exec($ch);
                                        curl_close($ch);
                                        $status = json_decode($response, 1);
                                        //print_r($status);die();
                                        ?>
                                        <hr>
                                        <label for="EmailID" style="color: #ff5300;margin: 0px 0px 9px;float: left;width: 100%;font: bold 16px/18px Arial, Helvetica, sans-serif;padding: 0 0 7px;">Additional email accounts<span class="ico_info"><span class="info-block info-block2">
                                                    <span class="ib-t">
                                                        <span class="info-text">You can use multiple email accounts with TickleTrain.  This allows you to use the same Tickle BCC addresses with other email accounts.  This option is available on paid plans only.</span>
                                                    </span>
                                                </span></span>
                                        </label>
                                        <?php
                                        $sno = 1;
                                        if ($user_details['email_addon'] != '' && $user_details['addon_hosting_id'] != '' && ($status['status'] == 'Active' || $status['status'] == 'Pending')) {
                                            while ($secEmail = mysqli_fetch_assoc($sec)) {
                                                ?>
                                                <div class="row">
                                                    <label>Email</label>
                                                    <span class="input_text">
                                                        <input type="text" name="added_EmailID<?php echo $sno; ?>" id="EmailID<?php echo $sno; ?>" value="<? echo $secEmail['EmailID']; ?>" size="32" readonly="readonly" onblur="this.readOnly = true"/>
                                                    </span>
                                                    <label>Nickname</label>
                                                    <span class="input_text">
                                                        <input type="text" name="addednickname_<?php echo $secEmail['id']; ?>" id="addednickname_<?php echo $secEmail['id']; ?>" value="<? echo $secEmail['nickname']; ?>" size="32" readonly="readonly" onblur="this.readOnly = true"/>
                                                        <input type="hidden" name="checkmail<?php echo $sno; ?>" value="<?php echo $secEmail['id'] ?>"></span>
                                                    <button style="border:none;background-color: #EDF6FD;margin-top: 3px;">
                                                        <a href="javascript:void(0)" onclick="return edit('<?php echo $secEmail['id']; ?>')">Edit</a></button>
                                                    <button style="border:none;background-color: #EDF6FD;" onclick="return confirmDel(<?php echo $secEmail['id'] ?>);"><a href="javascript:void(0)">Delete</a></button>
                                                </div>
                                            <?php $sno++;   }
                                        } ?>
                                        <div id="addnew1">
                                        </div>
                                        <div class="row">                               
                                        <?php
                                        if ($user_details['email_addon'] == '' && $user_details['addon_hosting_id'] == '') {
                                            $timestamp = time();
                                            $autoauthkey = "abcXYZ123";
                                            $hash = sha1($user_details['EmailID'] . $timestamp . $autoauthkey);
                                            $redirecturl = 'https://secure.tickletrain.com/dologin.php?email=' . $user_details['EmailID'] . '&timestamp=' . $timestamp . '&hash=' . $hash . '&goto=' . urlencode("cart.php?gid=addons");
                                            ?>
                                                <a href="<?php echo $redirecturl; ?>" class="btn_blue show-hide">
                                                    <span>Add additional email</span>
                                                </a>
                                            <?php
                                            } else if ($user_details['email_addon'] != '' && ($status['status'] == 'Active' || $status['status'] == 'Pending')) {
                                                if ($user_details['email_addon'] == 1 || $user_details['email_addon'] == 5) {
                                                    $allow_rows = 1 - $countSecondary;
                                                    $totalAllow = 1;
                                                } else if ($user_details['email_addon'] == 2 || $user_details['email_addon'] == 6) {
                                                    $allow_rows = 2 - $countSecondary;
                                                    $totalAllow = 2;
                                                } else if ($user_details['email_addon'] == 3 || $user_details['email_addon'] == 7) {
                                                    $allow_rows = 3 - $countSecondary;
                                                    $totalAllow = 3;
                                                } else if ($user_details['email_addon'] == 8 || $user_details['email_addon'] == 9) {
                                                    $allow_rows = 4 - $countSecondary;
                                                    $totalAllow = 4;
                                                } else if ($user_details['email_addon'] == 10 || $user_details['email_addon'] == 11) {
                                                    $allow_rows = 5 - $countSecondary;
                                                    $totalAllow = 5;
                                                } else if ($user_details['email_addon'] == 12 || $user_details['email_addon'] == 13) {
                                                    $allow_rows = 6 - $countSecondary;
                                                    $totalAllow = 6;
                                                } else if ($user_details['email_addon'] == 14 || $user_details['email_addon'] == 15) {
                                                    $allow_rows = 7 - $countSecondary;
                                                    $totalAllow = 7;
                                                } else if ($user_details['email_addon'] == 16 || $user_details['email_addon'] == 17) {
                                                    $allow_rows = 8 - $countSecondary;
                                                    $totalAllow = 8;
                                                } else if ($user_details['email_addon'] == 18 || $user_details['email_addon'] == 19) {
                                                    $allow_rows = 9 - $countSecondary;
                                                    $totalAllow = 9;
                                                } else if ($user_details['email_addon'] == 20 || $user_details['email_addon'] == 21) {
                                                    $allow_rows = 10 - $countSecondary;
                                                    $totalAllow = 10;
                                                } else if ($user_details['email_addon'] == 22 || $user_details['email_addon'] == 23) {
                                                    $allow_rows = 11 - $countSecondary;
                                                    $totalAllow = 11;
                                                }
                                                ?>
                                                <a href="#" class="btn_blue show-hide" onclick="return addnewemail(<?php echo $allow_rows; ?>,<?php echo $totalAllow; ?>);">
                                                    <span>Add additional email</span>
                                                    <input type="hidden" id="numbers" val="0">
                                                </a>
                                                <input type="submit" name="add_additional" value="Save" class="btn_blue show-hide" style="margin-left:5px;background-color: #0090C7;border: none;border-radius: 2px;padding: 5px;" onclick="return validateEmail();">

                                            <?php } else { ?>
                                                <span>Service <?php echo $status['status']; ?></span>
                                            <?php } ?>
                                        </div>
                                        <?php } ?>
                                <style>
                                    .action_btn1{
                                        position:absolute;
                                        left:0;
                                        bottom:0;
                                        background-color:#D1F1FF;
                                    }   
                                    .action_btn2{
                                        left:35px;
                                    }   
                                </style>
                                <script>
                                        function confirmDel(id) {
                                            var check = confirm('Please note:  If you no longer require the capability to use TickleTrain with an additional email account(s), after deleting  the account(s), click Account at the top of the page and downgrade your plan to adjust billing.');
                                            if (check) {
                                                window.location = "?deleteSecEmail=" + id;
                                                return false;
                                            }
                                            else
                                                return false;
                                        }



                                        var i = 1, click = 1;
                                        function addnewemail(rows, allow) {
                                            if (click == 1) {
                                                no = allow - rows + 1;
                                                click = 0;
                                            }
                                            //document.getElementById("removeAccount").style.display="none";    
                                            if (rows > 0) {
                                                for (i; i <= rows; ) {
                                                    document.getElementById('addnew1').innerHTML += '<div class="row"><label>Email</label><span class="input_text"><input type="text" id="EmailID' + no + '" name="EmailID' + no + '" ><label>Nickname</label><span class="input_text"><input type="text" id="nickname' + no + '" name="nickname' + no + '" ></span></div>';
                                                    document.getElementById('numbers').value = no;
                                                    i++;
                                                    no++;
                                                    return false;
                                                }
                                            }
                                            else {
                                                var message = 'Your current plan allows ' + allow + ' additional email accounts.  To make changes to your plan, click on the Account link at the top of the page.';
                                                mcalert("Add-ons", message);
                                                return false;
                                            }

                                            return false;
                                        }

                                        function edit(id) {
                                            document.getElementById('addednickname_' + id).readOnly = false;
                                            document.getElementById('addednickname_' + id).focus();
                                            return false;
                                        }

                                        function removeemail() {
                                            document.getElementById('addnew1').innerHTML = '';
                                            document.getElementById("removeAccount").style.display = "block";
                                            i = 1;
                                            return false;
                                        }

                                        var validemail = true;
                                        function validateEmail() {
                                            var totalemails = document.getElementById('numbers').value;
                                            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                                            var nickexpression = /^[a-zA-Z0-9 ]+$/;
                                            for (var i = 1; i <= totalemails; i++) {
                                                var email = document.getElementById('EmailID' + i).value;
                                                var nickname = document.getElementById('nickname' + i).value;
                                                if (!re.test(email) || email == '') {
                                                    alert('Please enter valid email address');
                                                    validemail = false;
                                                }
                                                else if (!nickexpression.test(nickname) || nickname == '') {
                                                    alert('Please enter valid Nickname(Numbers and Characters)');
                                                    validemail = false;
                                                }
                                                else {
                                                    validemail = true;
                                                }
                                            }
                                            return validemail;
                                        }
                                </script>
                        </div>
                    </div>
                </div>


                <div class="right_side">
                    <div class="buttons">
                        <input type="submit" value="Update" class="btn_update" name="submit"/>
                    </div>
                    <? include_once "includes/mailsettings_inc.php"; ?>
                    <input type="hidden" name="secondary_id" id="secondary_id" value="primary">
                   
                    <div class="section">
                        <h2>Email settings</h2>
                            <div class="col">
                                <?php if ($user_details['Plan'] == 1 || $user_details['email_addon'] == '') { ?>
                                    <span class="input_text input-text2 DMPanelDef" style="display:<?= (intval($user_details["DMUse"]) ? "none" : "") ?>">
                                        <input type="text" name="dmfromemaildef" id="dmfromdef" value="<?= trim($user_details['EmailID']) ?>"/>
                                    </span>
                                    <span class="input_text input-text2 DMPanel show" style="display:<?= (intval($user_details["DMUse"]) ? "" : "none") ?>">
                                        <input type="text" name="dmfromemail" id="dmfrom" value="<?= trim($user_details['EmailID']) ?>" readonly="readonly"/>
                                    </span>
                                    <div style="clear:both;"></div>
                                    <div id="radioMain">
                                        <h3 class="subheading">Send Tickle campaigns using <span class="ico_info"><span class="info-block info-block2">
                                                    <span class="ib-t">
                                                        <span class="info-text">Choose the way your Tickles will be sent. We recommend using your own mail server. Either option will show the emails came from your personal email address.</span>
                                                    </span>
                                                </span></span>
                                        </h3>
                                        <ul class="radio_area">
                                            <li>
                                                <input type="hidden" name="forcepress" id="forcepress" value="0"/>
                                                <input id="dmuse0" type="radio" name="dmuse"
                                                       value="0"<?= (intval($user_details["DMUse"] || $checkToken > 0) ? "" : "checked" ) ?>
                                                       onclick="showDMPanel(this, 'true')" class="dmuse"/>
                                                <label for="dmuse0">Our mail server</label>
                                            </li>
                                            <li>
                                                <input id="dmuse1" type="radio" name="dmuse"
                                                       value="1"<?= (intval($user_details["DMUse"] || $checkToken > 0) ? "checked" : "" ) ?>  onclick="showDMPanel(this, 'true')" class="dmuse"/>
                                                <label for="dmuse1"> Your mail provider (preferred method)</label>
                                                <input type="hidden" name="dmsystem" id="dmsystem"
                                                       value="<?= trim($user_details['DMSystem']) ?>"/>
                                                <input type="hidden" name="dmtoemail" id="dmtoemail" value=""/>
                                            </li>
                                        </ul>
                                    </div>
                                <?php
                                } else {
                                    $sec = mysqli_query($db->conn,"select * from secondaryEmail where TickleID='" . $_SESSION['TickleID'] . "' and isdelete='0'");
                                ?>
                                    <input type="hidden" name="dmfromemail" id="dmfrom" value="<?= trim($user_details['EmailID']) ?>" disabled="disabled"/>
                                    <select name="alternativeemail" onchange="showhide_setting(this.value)" id="alternativeemail">
                                        <option value="<?= trim($user_details['EmailID']) ?>_mainemail"><?= trim($user_details['EmailID']) ?></option>
                                        <?php while ($secEmail = mysqli_fetch_assoc($sec)) { ?>
                                            <option value="<? echo $secEmail['EmailID']; ?>_secondary_<?php echo $secEmail['id'] ?>_<?php echo $secEmail['DMUse'] ?>"><? echo $secEmail['EmailID']; ?></option>
                                        <?php } ?>
                                    </select>
                            </div>  <!-- end col -->

                            <div id="radioMain">
                                <h3 class="subheading">Send Tickle campaigns using <span class="ico_info"><span class="info-block info-block2">
                                            <span class="ib-t">
                                                <span class="info-text">Choose the way your Tickles will be sent. We recommend using your own mail server. Either option will show the emails came from your personal email address.</span>
                                            </span>
                                        </span></span>
                                </h3>
                                <ul class="radio_area">
                                    <li>
                                        <input type="hidden" name="forcepress" id="forcepress" value="0"/>
                                        <input id="dmuse0" type="radio" name="dmuse" value="0"<?= (intval($user_details["DMUse"] || $checkToken > 0) ? "" : "checked" ) ?> onclick="showDMPanel(this, 'true')" class="dmuse"/>
                                        <label for="dmuse0">Our mail server</label>
                                    </li>
                                    <li>
                                        <input id="dmuse1" type="radio" name="dmuse" value="1" <?= (intval($user_details["DMUse"] || $checkToken > 0) ? "checked" : "" ) ?> onclick="showDMPanel(this, 'true')" class="dmuse"/>
                                        <label for="dmuse1">Your mail provider (preferred method)</label>
                                        <input type="hidden" name="dmsystem" id="dmsystem"
                                               value="<?= trim($user_details['DMSystem']) ?>"/>
                                        <input type="hidden" name="dmtoemail" id="dmtoemail" value=""/>
                                    </li>
                                </ul>
                            </div>
                    </div>



                    <div id="dmusemain" style="padding-left:30px;display:<?= (intval(($user_details["DMUse"] || $checkToken > 0)) ? "" : "none") ?>">
                        <div class="two-col">

                        <?php $sec = mysqli_query($db->conn,"select * from secondaryEmail where TickleID='" . $_SESSION['TickleID'] . "' and isdelete='0'"); ?>
                            <div id="secondary_holder">
                            <?php
                                while ($secEmail1 = mysqli_fetch_assoc($sec)) {
                                    if ($secEmail1['use_authtoken'] == '0') {
                                        $secauth = "smtp";
                                        echo"<input type='hidden' value='smtp' id='oauthcheck'>";
                                    } else {
                                        $secauth = "authToken";
                                        echo"<input type='hidden' value='oauth' id='oauthcheck'>";
                                    }
                            ?>
                                <div id="secondary_<?php echo $secEmail1['id'] ?>" style="display:none;">
                                    
                                    <div class="hide-block DMPanel" style="display:<?= (intval($user_details["DMUse"])) ?>">
                                        <?php //if ($secEmail1['use_authtoken'] == '0') { ?>
                                            <div class="row" style="display:block;padding:0;">
                                                <a href="#" class="btn_blue show-hide" id="shwbtn<?php echo $secEmail1['id'] ?>"  onclick="$('#DMDetails<?php echo $secEmail1['id'] ?>').toggle();$(this).find('em').toggle();return false">
                                                    <span>
                                                    <em class="text01 esettings1" id="text_show<?php echo $secEmail1['id'] ?>" style="display:<?= (intval($user_details["DMUse"] || $secEmail1['use_authtoken'] > 0) ? "none" : "block" ) ?>;">Show
                                                            Email Settings</em>
                                                    <em class="text02 esettings2" id="text_hide<?php echo $secEmail1['id'] ?>" style="display:<?= (intval($user_details["DMUse"] || $secEmail1['use_authtoken'] > 0) ? "block" : "none" ) ?>;">Hide Email Settings</em></span>
                                                </a>
                                            </div>    
                                        <?php //} ?>

                                        <div id="DMDetails<?php echo $secEmail1['id'] ?>" style="display:<?=(intval($user_details["DMUse"] || $secEmail1['use_authtoken']>0) ? "block" : "none" )?>">
                                            <p class="DMTitle" id="swarning<?php echo $secEmail1['id'] ?>" style="display:"> </p>

                                              <?php
                                              $client_smtp_setting = [];

                                                if(!empty($secEmail1["DMPwd"]) && empty($secEmail1["use_authtoken"])){
                                                  $client_smtp_setting = [
                                                    'dmsmtp' => $secEmail1["DMSmtp"],
                                                    'dmuser' =>$secEmail1["DMUser"],
                                                    'dmpwd' =>  decryptIt($secEmail1["DMPwd"]),
                                                    'dmport' => $secEmail1["DMPort"],
                                                    'dmsecure' => $secEmail1["DMSecure"],
                                                  ];  
                                              }
                                                  
                                                 ?>
                                                <div style="width:100%; height: auto; float: left">
                                                   <b>My email is  hosted with 
                                                    <select class="smtp_hosted_with" data-json='<?php echo  json_encode($client_smtp_setting); ?>' data-id="<?php echo $secEmail1['id'] ?>" >
                                                    <option value="gmail" <?php echo !empty($secEmail1["use_authtoken"])?'selected=selected':'' ;?>> Gmail</option>
                                                    <option value="yahoo" <?php echo (count($client_smtp_setting))?'selected=selected':'' ;?>> Yahoo</option>
                                                    <option value="other" <?php echo ($secEmail1["DMSmtp"] != 'smtp.mail.yahoo.com' && !empty($secEmail1["DMSmtp"]))?'selected=selected':'' ;?>> Other</option>
                                                   </select> </b>
                                               </div>
                                               <br>
                                            <div id="gmailSetting<?php echo $secEmail1['id'] ?>" class="gmailYesNoSetting" style="display:none">
                                                
                                            <p>Click the button below to allow TickleTrain to send your Tickles using your Gmail account</p>      

                                            <input type="hidden" id="secondaryAuth<?php echo $secEmail1['id']; ?>" value="<?php echo $secEmail1['DMUse']; ?>">
                                            <?php
                                            if ($secEmail1['use_authtoken'] == '1' && $secEmail1['authtoken'] != '') {
                                                try {
                                                    $client->refreshToken($secEmail1['refresh_token']);
                                                    $getGoogleToken = $client->getAccessToken();
                                                    $getGoogleToken02 = json_decode($getGoogleToken, true);
                                                    //print_r($getGoogleToken02);
                                                } catch (Exception $e) {
                                                    mysqli_query($db->conn,"update secondaryEmail set authtoken='',refresh_token='',use_authtoken='0',DMUse='0' where id='" . $secEmail1['id'] . "'");
                                                    redirect('myaccount');
                                                }
                                                $_SESSION['acctkn'] = $getGoogleToken02['access_token'];
                                                mysqli_query($db->conn,"update secondaryEmail set authtoken='" . $getGoogleToken02['access_token'] . "' where id='" . $secEmail1['id'] . "'");
                                                $_SESSION['access_token'] = json_encode($getGoogleToken); // get the access token here  
                                            ?>
                                                <a href="<?= Url_Create('myaccount'); ?>?qrydlt=secdeletauth&secemaiid=<?php echo $secEmail1['id']; ?>" onclick="return confirm('Are you sure delete gmail account setting ?');" id="remove_btn">
                                                    <span><img src="../images/remove_btn.png"></span>
                                                    <span class="ico_info">
                                                        <span class="info-block info-block2">
                                                            <span class="ib-t">
                                                                <span class="info-text">
                                                                    Google Gmail Authentication Access is the most secure and easiest way to allow TickleTrain to communicate with your Gmail account and properly send your Tickles through your Gmail server. If you remove Gmail Authentication you will need to manually setup your SMTP settings below.
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </span>
                                                </a>

                                                <div style="min-height: 41px;  padding-top: 10px;" id="send_Btn">
                                                    <a href="#" onclick="sendTest(document.forms['MyAccount'], '<?php echo $secauth ?>', 'secondary', '<?php echo $secEmail1['id'] ?>'); return false" class="btn_blue">
                                                        <span>Send Test Email </span>
                                                    </a>
                                                </div>

                                            <?php }else{ ?>
                                                <a href="<?php echo $authUrl ?>"><span><img src="../images/enable_gmail_access.png"></span></a>
                                                <span class="ico_info" style="margin-top: 5px;"><span class="info-block info-block2">
                                                        <span class="ib-t">
                                                            <span class="info-text">
                                                                Google's Gmail Authentication Access is the most secure and easiest way to allow TickleTrain to communicate with your Gmail account and properly send your Tickles through your Gmail server.  This also enables Reply Tracking capability so no further settings are required.  If you remove Gmail Authentication you will need to manually setup your SMTP settings.
                                                            </span>
                                                        </span>
                                                    </span>
                                                </span>
                                            <?php }
                                                $secemail01 = explode('@', $secEmail1['EmailID']);
                                                $secemail = $secemail01[1];
                                                if (!empty($secEmail1['authtoken'])) {
                                                    $secemail = 'gmail.com';
                                                }
                                            ?>
                                            </div>

                                            <div id="smtpSetting<?php echo $secEmail1['id'] ?>" class="gmailYesNoSetting" style="display:none">       
                                                <p class="yahoo-p-content">
                                                    <b>Enter your SMTP email server settings below</b><br>
                                                    If you are unsure of your outgoing email settings, try copying the settings on your local email client such as Outlook. You may also contact your email provider for the proper settings.
                                                </p>

                                                <div class="two-col" style="margin-top:10px;">
                                                    <div class="DMPanel DMTitle col single-col">
                                                        <label for="dmsmtp" class="server_title">Smtp Mail Server</label>
                                                         <?php $DMSmtp = !empty($secEmail1["DMSmtp"])?trim($secEmail1["DMSmtp"]):'smtp.mail.yahoo.com';  ?>
                                                        <span class="input_text input-text2"><input type="text" name="secdmsmtp<?php echo $secEmail1['id'] ?>" id="secdmsmtp<?php echo $secEmail1['id'] ?>" value="<?= trim($DMSmtp) ?>"/></span>
                                                    </div>
                                                    <?php if (trim($secEmail1["DMSmtp"]) != 'smtp.gmail.com' && trim($secEmail1["DMSmtp"]) != 'smtp.live.com') { ?>
                                                        <div class="DMPanel DMTitle1 col" style="display:block !important; width: 175px;">
                                                            <label for="dmuser" class="username_title">Username</label>
                                                            <span><input type="text" class="sec-dmuser"  data-id="<?= trim($secEmail1["id"]) ?>" name="secsecdmuser<?php echo $secEmail1['id'] ?>" id="secdmuser<?php echo $secEmail1['id'] ?>" value="<?= trim($secEmail1["DMUser"]) ?>"/></span>
                                                        </div>
                                                    <?php } else { ?>
                                                        <input type="hidden" name="secsecdmuser<?php echo $secEmail1['id'] ?>" id="secdmuser<?php echo $secEmail1['id'] ?>" value="<?= trim($secEmail1["DMUser"]) ?>"/>
                                                    <?php } ?>
                                                    <div class="DMPanel DMTitle col">
                                                        <label for="secdmpwd<?php echo $secEmail1['id'] ?>" class="password_title">Password</label>
                                                        <?php $pwd =  (!empty($secEmail1["DMPwd"])) ? trim(decryptIt($secEmail1["DMPwd"])) : ''; ?> 
                                                        <span><input type="password" name="secdmpwd<?php echo $secEmail1['id'] ?>" id="secdmpwd<?php echo $secEmail1['id'] ?>" value="<?php echo $pwd; ?>"/>
                                                    </div>
                                                </div>

                                                <div class="DMPanel DMTitle col single-col" style="display:none;" id="DMFrom_block_<?php echo $secEmail1['id'] ?>"  >
                                                    <label for="DMFrom" class="from_email"><?= getLabel(trim($user_details['DMFrom']), "from_email") ?></label>
                                                    <?php $DMFrom = (!empty($secEmail1["FromEmail"]))?$secEmail1["FromEmail"]:$secEmail1["EmailID"]; ?>
                                                    <span class="input_text input-text2">
                                                        <input type="text" name="DMFrom_<?php echo $secEmail1['id'] ?>"  value="<?php echo $DMFrom; ?>"/></span>
                                                </div>

                                                <div class="set-area">
                                                    <?php if (trim($secEmail1["DMSmtp"]) != 'smtp.gmail.com' && trim($secEmail1["DMSmtp"]) != 'smtp.live.com') { ?>
                                                        <div class="open-holder" id="DMAdvancedLink">
                                                            <a href="#" class="open-set" onclick="$('#DMAdvanced<?php echo $secEmail1['id'] ?>').toggle();
                                                                    $(this).find('em').toggle();
                                                                    return false"><em class="text01" id="text_show">Show advanced settings</em><em class="text02" id="text_hide">Hide
                                                                    advanced settings</em></a>
                                                        </div>
                                                    <?php } ?>
                                                </div>

                                                <div id="DMAdvanced<?php echo $secEmail1['id'] ?>" style="display:none">
                                                    <div class="two-col">
                                                        <div class="DMPanel DMTitle col" style="display:">
                                                            <label for="dmport<?php echo $secEmail1['id'] ?>" class="port_title"><?= getLabel(trim($user_details['DMSystem']), "port_title") ?></label>
                                                           <?php $DMPort = !empty($secEmail1["DMPort"])?trim($secEmail1["DMPort"]):465;  ?>
                                                            <span class="input_text input-text3"><input type="text" name="secdmport<?php echo $secEmail1['id'] ?>" id="secdmport<?php echo $secEmail1['id'] ?>" value="<?= trim($DMPort) ?>"/></span>
                                                            <input type="checkbox" name="secdmauth<?php echo $secEmail1['id'] ?>" id="secdmauth<?php echo $secEmail1['id'] ?>" class="checkbox" value="1"<?= ((@($secEmail1["DMAuth"]) != '' || 1) ? " checked" : "") ?>/>
                                                            <label for="secdmauth<?php echo $secEmail1['id'] ?>" class="inline-label auth_title"><?= getLabel(trim($secEmail1['DMSystem']), "auth_title") ?></label>
                                                        </div>

                                                        <?php $DMSecure = !empty($secEmail1["DMSecure"])?trim($secEmail1["DMSecure"]):'0';  ?>
                                                        <div class="DMPanel DMTitle col" style="display:">
                                                            <label for="secdmsecure<?php echo $secEmail1['id'] ?>" class="encryption_title"><?= getLabel(trim($user_details['DMSystem']), "encryption_title") ?></label>
                                                            <select name="secdmsecure<?php echo $secEmail1['id'] ?>" id="secdmsecure<?php echo $secEmail1['id'] ?>">
                                                                
                                                                <? foreach ($encryption as $key => $val) { ?>
                                                                        <option value="<?= $key ?>" <?= ((trim($DMSecure) == $key) ? " selected" : "") ?>><?= $val ?></option>
                                                                <? } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="min-height: 41px;  padding-top: 10px;" id="send_Btn">
                                                    <a href="#" onclick="sendTest(document.forms['MyAccount'], '<?php echo $secauth ?>', 'secondary', '<?php echo $secEmail1['id'] ?>');return false" class="btn_blue"><span>Send Test Email </span>
                                                    </a>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                           <?php } ?>
                                <script>
                                    function showhide_setting(val) {
                                        val = decodeURIComponent(val);
                                        $('#alternativeemail option:selected', this).removeAttr('selected');
                                        $('#alternativeemail1 option:selected', this).removeAttr('selected');
                                        $('#alternativeemail option[value="' + val + '"]').attr('selected', 'selected');
                                        $('#alternativeemail1 option[value="' + val + '"]').attr('selected', 'selected');
                                        $("#secondary_holder").children().hide();
                                        $("#sec_plugin_holder").children().hide();
                                        var res = val.split("_");
                                        setCookie('sec_email', res[0], 10);
                                        setCookie('hide_show_id', val, 10);

                                        setTimeout(function(){ $('.smtp_hosted_with:visible').trigger('change'); },100);

                                         

                                        if (val == '') {
                                            $("#main_sign").show();
                                        }
                                        else if (val == res[0] + '_mainemail') {
                                            $('#mainemail_setting').show();
                                            $('#secondary_id').val('primary');
                                            $("#main_sign").show();        // sign hide show
                                            $("#track_alternativeemail").val("mainemail").trigger('change'); //Change track dropdown
                                            togglesettings('primary');
                                            sendTest1(document.forms['MyAccount'], 'primary', res[2], 'true');

                                            if ($("#primarycheckbox").val() == '1') {
                                                $("#dmuse1").prop("checked", true);
                                                $("#dmuse0").prop("checked", false);
                                                showDMPanel($("#dmuse1"), "false");
                                            }
                                            else {
                                                $("#dmuse1").prop("checked", false);
                                                $("#dmuse0").prop("checked", true);
                                                showDMPanel($("#dmuse0"), "false");
                                            }

                                        }
                                        else {
                                            $('#mainemail_setting').hide();
                                            setCookie('hide_show_id', val, 10);
                                            $('#secondary_id').val(res[2]);
                                            $('#' + res[1] + '_' + res[2]).show();
                                            $("#sec_sign_" + res[2]).show();      // sign hide show
                                            $("#main_sign").hide();             // sign hide show
                                            $("#track_alternativeemail").val("track_secondary_" + res[2]).trigger('change'); //Change track dropdown
                                            togglesettings(res[2]);
                                            sendTest1(document.forms['MyAccount'], 'secondary', res[2], 'true');

                                            if ($("#secondaryAuth" + res[2]).val() == '1') {
                                                $("#dmuse1").prop("checked", true);
                                                $("#dmuse0").prop("checked", false);
                                                showDMPanel($("#dmuse1"), "false");
                                            }
                                            else {
                                                $("#dmuse1").prop("checked", false);
                                                $("#dmuse0").prop("checked", true);
                                                showDMPanel($("#dmuse0"), "false");
                                            }
                                            
                                        }

                                    }
                                    function setCookie(cname, cvalue, exdays) {
                                        var d = new Date();
                                        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                                        var expires = "expires=" + d.toUTCString();
                                        document.cookie = cname + "=" + cvalue + "; " + expires + ";path=/myaccount/";
                                    }
                                    function getCookie(cname) {
                                        var name = cname + "=";
                                        var ca = document.cookie.split(';');
                                        for (var i = 0; i < ca.length; i++) {
                                            var c = ca[i];
                                            while (c.charAt(0) == ' ')
                                                c = c.substring(1);
                                            if (c.indexOf(name) == 0)
                                                return c.substring(name.length, c.length);
                                        }
                                        return "";
                                    }
                                </script>               
                            </div>              

                            <div id="mainemail_setting"> 

                                <div class="hide-block DMPanel" style="display:<?= (intval($user_details["DMUse"] || $checkToken > 0) ? "block" : "none" ) ?>;">
                                    <div class="row" style="display:block;padding:0;">
                                        <a href="#" class="btn_blue show-hide" id="shwbtn" onclick="$('#DMDetails').toggle();
                                                $(this).find('em').toggle();
                                                return false">
                                            <span>
                                                <em class="text01 esettings1" id="text_show" style="display:none;">Show Email Settings</em>
                                                <em class="text02 esettings2" id="text_hide" style="display:block;">Hide Email Settings</em>
                                            </span>
                                        </a>
                                    </div>

                                    <div id="DMDetails" style="display:<?= (intval($user_details["DMUse"] || $checkToken > 0) ? "block" : "none" ) ?>;">
                                        <p class="DMTitle" id="swarning"
                                           style="display:<?= ((intval($user_details["DMUse"]) && isVisible(trim($user_details['DMSystem']), "swarning")) ? "" : "/*none*/") ?>">
                                        <?php
                                        if(isset($_GET['test'])){
                                            echo "<pre>";
                                            print_r($user_details);
                                            echo "</pre>";
                                        }
                                        $client_smtp_setting = [];
                                        if(!empty($user_details["DMPwd"]) && empty($checkToken) ) {
                                           $client_smtp_setting = [
                                            'dmsmtp' => $user_details["DMSmtp"],
                                            'dmuser' =>$user_details["DMUser"],
                                            'dmpwd' => decryptIt($user_details["DMPwd"]),
                                            'dmport' => $user_details["DMPort"],
                                            'dmsecure' => $user_details["DMSecure"],
                                          ]; 
                                        }

                                        if(isset($_GET['test'])){
                                            echo "<pre>";
                                            print_r($client_smtp_setting);
                                            echo "</pre>";
                                        }
                                          
                                         ?>
                                        <div style="width:100%; height: auto; float: left">
                                           <b>My email is  hosted with 
                                            <select class="smtp_hosted_with" data-json='<?php echo  json_encode($client_smtp_setting); ?>' data-id="" >
                                              
                                                    <option value="gmail" <?php echo (empty($checkToken))?'selected=selected':'' ;?>> Gmail</option>
                                                    <option value="yahoo" <?php echo (count($client_smtp_setting))?'selected=selected':'' ;?>> Yahoo</option>
                                                    <option value="other" <?php echo ($user_details["DMSmtp"] != 'smtp.mail.yahoo.com' && !empty($user_details["DMSmtp"]))?'selected=selected':'' ;?>> Other</option>
                                           </select> </b>
                                         <!--<ul id="new-setting-ul"> 
                                            <li onclick="$('#gmailSetting').show();$('#smtpSetting').hide()">Gmail</li>|
                                            <li onclick="add_yahoo_content(); $('#gmailSetting').hide();$('#smtpSetting').show();">Yahoo</li>|
                                            <li onclick="remove_yahoo_content();$('#gmailSetting').hide();$('#smtpSetting').show();">Other</li>
                                           </ul> -->
                                       </div>
                                       <br>
                                        <!--div style="width:100%; height: auto; padding: 10px 0; float: left">
                                            <a href="javascript:void(0);" onclick="$('#gmailSetting').show();
                                                    $('#smtpSetting').hide()" class="btn_blue" style="margin-right: 20px;"><span>Gmail</span></a>
                                            <a href="javascript:void(0);" onclick="$('#gmailSetting').hide();
                                                    $('#smtpSetting').show();" class="btn_blue"><span>Other Smtp</span></a>
                                        </div-->     


                                        <div id="gmailSetting" class="gmailYesNoSetting" style="display:none">        
                                            <p>Click the button below to allow TickleTrain to send your Tickles using your Gmail account</p> 
                                            <input type="hidden" id="emailtype" value="<?php echo $emailType ?>">
                                            <?php
                                            if ($checkToken != 0)
                                                $useownmail = 1;
                                            else if ($user_details['DMUse'] == '1')
                                                $useownmail = 1;
                                            else
                                                $useownmail = 0;
                                            ?>

                                            <input type="hidden" id="primarycheckbox" value="<?php echo $useownmail; ?>">
                                            <?php
                                            if ($checkToken != '0') {
                                                $getGoogleToken = mysqli_fetch_object(mysqli_query($db->conn,"select access_token,token_type,expires_in,id_token,refresh_token,created from google_auth_tokens where userid='" . $_SESSION['TickleID'] . "' "));
                                                try {
                                                    $client->refreshToken($getGoogleToken->refresh_token);
                                                    $getGoogleToken = $client->getAccessToken();
                                                    $getGoogleToken02 = json_decode($getGoogleToken, true);
                                                    //print_r($getGoogleToken02);
                                                } catch (Exception $e) {
                                                    mysqli_query($db->conn,"delete from google_auth_tokens where userid='" . $_SESSION['TickleID'] . "'");
                                                    redirect('myaccount');
                                                }
                                                $_SESSION['acctkn'] = $getGoogleToken02['access_token'];
                                                mysqli_query($db->conn,"update google_auth_tokens set access_token='" . $getGoogleToken02['access_token'] . "' , expires_in='" . $getGoogleToken02['expires_in'] . "' , created='" . $getGoogleToken02['created'] . "' where userid='" . $_SESSION['TickleID'] . "'");
                                                $_SESSION['access_token'] = json_encode($getGoogleToken); // get the access token here  
                                                ?>
                                                <a href="<?= Url_Create('myaccount'); ?>?qrydlt=deletauth" onclick="return confirm('Are you sure delete gmail account setting ?');" id="remove_btn">
                                                    <span><img src="../images/remove_btn.png"></span>

                                                    <span class="ico_info"><span class="info-block info-block2">
                                                            <span class="ib-t">
                                                                <span class="info-text">
                                                                    Google Gmail Authentication Access is the most secure and easiest way to allow TickleTrain to communicate with your Gmail account and properly send your Tickles through your Gmail server. If you remove Gmail Authentication you will need to manually setup your SMTP settings below.
                                                                </span>
                                                            </span>
                                                        </span></span>
                                                </a>

                                                <div style="min-height: 41px;  padding-top: 10px;" id="send_Btn">
                                                    <a href="#" onclick="sendTest(document.forms['MyAccount'], '<?php echo $auth ?>', 'primary', '0');
                                                            return false" class="btn_blue"><span>Send Test Email </span></a></div>
                                            <?php } else {
                                                ?>
                                                <a href="<?php echo $authUrl ?>"><span><img src="../images/enable_gmail_access.png"></span></a>
                                                <span class="ico_info" style="margin-top: 5px;"><span class="info-block info-block2">
                                                        <span class="ib-t">
                                                            <span class="info-text">
                                                                Google's Gmail Authentication Access is the most secure and easiest way to allow TickleTrain to communicate with your Gmail account and properly send your Tickles through your Gmail server.  This also enables Reply Tracking capability so no further settings are required.  If you remove Gmail Authentication you will need to manually setup your SMTP settings.
                                                            </span>
                                                        </span>
                                                    </span></span>

                                                       <?php
                                                   }
                                                   ?>
                                        </div>
                                        <!-- yahoo --> 
                                        <div id="smtpSetting" class="gmailYesNoSetting" style="display:none">              
                                              
                                            <p class="yahoo-p-content" >
                                                <b>Enter your SMTP email server settings below</b><br>
                                                If you are unsure of your outgoing email settings, try copying the settings on your local email client such as Outlook. You may also contact your email provider for the proper settings.
                                            </p>

                                                <?
                                                // print_r($server_settings);
                                                // echo  $emailType; 
                                                // echo "<pre>";
                                                // print_r($user_details);
                                                // echo "</pre>";
                                                
                                                if ($emailType != 'gmail.com' && $server_settings) {
                                                    foreach ($server_settings as $key => $val) {
                                                ?>
                                                     <input id="systembtn_<?= $key ?>" type="button" value="<?= $val ?>" class="systemselect" style="display: none" onclick="selectSettings(this, '<?= $key ?>')"<?= ((trim($user_details['DMSystem']) == $key) ? " disabled" : "") ?>/>
                                                <? }   }  ?>

                                            <div class="two-col" style="margin-top:10px;">
                                                <div class="DMPanel DMTitle col single-col">
                                                    <label for="dmsmtp" class="server_title"><?= getLabel(trim($user_details['DMSystem']), "server_title") ?></label>
                                                    <?php $DMSmtp = !empty($user_details["DMSmtp"])?trim($user_details["DMSmtp"]):'smtp.mail.yahoo.com';  ?>
                                                    <span class="input_text input-text2">
                                                        <input type="text" name="dmsmtp" id="dmsmtp" old="" value="<?= trim($DMSmtp) ?>"/></span>
                                                </div>

                                                <div class="DMPanel DMTitle col" style="dispaly:block!important; width: 175px;">
                                                    <label for="dmuser" class="username_title"><?= getLabel(trim($user_details['DMSystem']), "username_title") ?></label>
                                                    <span>
                                                        <input type="text" name="dmuser" id="dmuser" class="sec-dmuser" data-id="<?= trim($user_details["TickleID"]) ?>" value="<?= trim($user_details["DMUser"]) ?>"/>
                                                    </span>
                                                </div>
                                                <div class="DMPanel DMTitle col">
                                                    <label for="dmpwd"
                                                           class="password_title"><?= getLabel(trim($user_details['DMSystem']), "password_title") ?></label>
                                                    <span><input type="password" name="dmpwd" id="dmpwd" value="<?php if (!empty($user_details["DMPwd"])) { echo trim(decryptIt($user_details["DMPwd"]));} ?>"/></span>
                                                </div>


                                                <div class="DMPanel DMTitle col single-col" id="DMFrom_block_<?= trim($user_details["TickleID"]) ?>" style="display:none;" >
                                                    <label for="DMFrom" class="from_email"><?= getLabel(trim($user_details['DMFrom']), "from_email") ?></label>
                                                    <?php $DMFrom = (!empty($user_details["FromEmail"]))?$user_details["FromEmail"]:$user_details["EmailID"]; ?>
                                                    <span class="input_text input-text2">
                                                        <input type="text" name="DMFrom" id="DMFrom"  value="<?php echo $DMFrom; ?>"/></span>
                                                </div>

                                            </div>


                                            <div class="set-area">
                                                <div class="open-holder" id="DMAdvancedLink">
                                                    <a href="#" class="open-set" onclick="$('#DMAdvanced').toggle();$(this).find('em').toggle();return false">
                                                        <em class="text01">Show advanced settings</em>
                                                        <em class="text02">Hide advanced settings</em>
                                                    </a>
                                                </div>

                                                <!-- here-->   
                                                    
                                                <div id="DMAdvanced" style="display:none">
                                                    <div class="two-col">
                                                        <div class="DMPanel DMTitle col">
                                                            <label for="dmport"  class="port_title"><?= getLabel(trim($user_details['DMSystem']), "port_title") ?></label>
                                                                <?php $DMSmtp = !empty($user_details["DMPort"])?trim($user_details["DMPort"]):'465';  ?>
                                                            <span class="input_text input-text3">
                                                                <input type="text" name="dmport" id="dmport" value="<?= trim($DMSmtp) ?>"/></span>
                                                            <input type="checkbox" name="dmauth" id="dmauth" class="checkbox"
                                                                   value="1"<?= (($user_details["DMAuth"]) ? " checked" : "") ?>/>
                                                            <label for="dmauth" class="inline-label auth_title"><?= getLabel(trim($user_details['DMSystem']), "auth_title") ?></label>
                                                        </div>
                                                        <div class="DMPanel DMTitle col">
                                                            <label for="dmsecure" class="encryption_title"><?= getLabel(trim($user_details['DMSystem']), "encryption_title") ?></label>

                                                                    <?php $DMSecure = !empty($user_details["DMSecure"])?trim($user_details["DMSecure"]):'tls';  ?>

                                                            <select name="dmsecure" id="dmsecure"><? foreach ($encryption as $key => $val) { ?>
                                                                    <option
                                                                        value="<?= $key ?>"<?= ((trim($DMSecure) == $key) ? " selected" : "") ?>><?= $val ?></option><? } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="btn_holder DMPanel" style="display:<?= (intval($user_details["DMUse"]) ? "" : "none") ?>">
                                                    <a href="#" onclick="sendTest(document.forms['MyAccount'], '<?php echo $auth ?>', 'primary', '0');
                                                            return false" class="btn_blue" style="margin-top:<?php if ($emailType == 'gmail.com') echo '15px'; ?>"><span>Send Test Email </span></a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- yahoo -->

                


                                    </div>

                                </div>

                            </div>
                        </div>
                        <!----------  Email Setting End ------------------------------>



                        <!-----------Rply Tracking Start ----------------------------->
                        <div class="clear"></div>
                        <div id="rplytrack" style="padding-left:30px;">
                            <div style="border-top:1px solid silver;width:100%;height:2px;padding-bottom: 5px;margin-top: 5px;"></div>
                            <?php
                            $sec = mysqli_query($db->conn,"select * from secondaryEmail where TickleID='" . $_SESSION['TickleID'] . "'");
                            $sec1 = mysqli_query($db->conn,"select * from secondaryEmail where TickleID='" . $_SESSION['TickleID'] . "'");

                            $primary_type = explode('@', $user_details['EmailID']);
                         

                            $userToken = mysqli_fetch_array(mysqli_query($db->conn,"select access_token,refresh_token from google_auth_tokens where userid='" . $_SESSION['TickleID'] . "' "));
                            if (!empty($userToken['access_token'])) {
                                $primary_type[1] = 'gmail.com';
                            }

                            if (empty($userToken) && $primary_type[1] == 'gmail.com')
                                echo'<input type="hidden" value="gmail_false" id="google_auth_primary">';
                            else if (!empty($userToken))
                                echo'<input type="hidden" value="gmail_true" id="google_auth_primary">';
                            else if (empty($userToken) && $primary_type[1] != 'gmail.com')
                                echo'<input type="hidden" value="false" id="google_auth_primary">';
                            ?>

                            <?php
                            //multiple emails code
                            $get_user_detail = mysqli_query($db->conn,"select * from tickleuser where TickleID='$_SESSION[TickleID]'");
                            $user_details = mysqli_fetch_assoc($get_user_detail);
                            $postUrl = "https://secure.tickletrain.com/get_addon_info.php";
                            $postdata = array(
                                'get_addon_status' => true,
                                'addon_hosting_id' => $user_details['addon_hosting_id']
                            );
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $postUrl);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POST, count($postdata));
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $response = curl_exec($ch);
                            curl_close($ch);
                            $status = json_decode($response, 1);

                            if ($user_details['email_addon'] != '' && ($status['status'] == 'Active' || $status['status'] == 'Pending')) {
                                ?>
                                <input type="hidden" name="email_type" value="primary" id="email_type">
                                <label for="Schedule" class="subheading">Reply tracking
                                    <span class="ico_info">
                                        <span id="infoblockdetail" class="info-block info-block-m">
                                            <span id="infotopdetail" class="ib-t">
                                                <span id="infotextdetail" class="info-text">
                                                    TickleTrain helps you manage the replies you get to your Tickle campaigns.
                                                    Options  include: <br/><br/> - Delete the campaign when a reply is received. <br/> - Be notified via email that a reply was received and decide what action to take.<br/>
                                                    - Do nothing. <br/><br/> Reply tracking requires a connection to your email to enable this feature.
                                                </span>
                                            </span>
                                        </span>
                                    </span>

                                </label>

                                <select name="track_alternativeemail" onchange="showhide_setting_track(this.value);" id="track_alternativeemail" style="display:none;">
                                    <option value="mainemail"><?= trim($user_details['EmailID']) ?></option>
                                        <?php while ($secEmail = mysqli_fetch_assoc($sec)) { ?>
                                        <option value="track_secondary_<?php echo $secEmail['id'] ?>"><? echo $secEmail['EmailID']; ?></option>
                                        <?php } ?>
                                </select>

                                <div id="primary_success" style="margin-top:10px;"></div>

                                <div id="primary_reply">

                                        <?php if ($primary_type[1] != 'gmail.com') { ?>
                                        <a style="font: bold 12px/18px Arial, Helvetica, sans-serif; cursor: pointer;width: 74%;float: right;margin-top: -28px;" id="imap_settings" onclick="togglesettings('primary');
                                                return false;">
                                            <u>Settings</u></a>
                                            <?php
                                        }
                                            ?>
                                        </label>

                                        <?php if (empty($userToken)) { ?>
                                            <div class="row" id="reply_tracking" style="display:none">
                                                <?php
                                                $get_related_mail = mysqli_query($db->conn,"select EMailID from tickleuser where TickleID='$_SESSION[TickleID]'");
                                                $related_mail_row = mysqli_fetch_assoc($get_related_mail);
                                                $related_mail = $related_mail_row['EMailID'];

                                                $mail_dmsystem_array = explode('@', $related_mail);
                                                $mail_dmsystem = $mail_dmsystem_array[1];

                                                $check_imap_setting = mysqli_query($db->conn,"select * from imap_settings where imap_mailer='$mail_dmsystem'");
                                                if (mysqli_num_rows($check_imap_setting) > 0) {
                                                    $get_imap_settings = mysqli_fetch_assoc($check_imap_setting);
                                                    $imap_hostname = $get_imap_settings['server'];
                                                    $imap_port = $get_imap_settings['Port'];
                                                    $imap_authentication = $get_imap_settings['authentication'];
                                                }

                                                $get_imap_setting = mysqli_query($db->conn,"select imap_host,imap_userame,imap_passowrd,imap_port,imap_secure from tickleuser where TickleID='$_SESSION[TickleID]'");
                                                if (mysqli_num_rows($get_imap_setting) > 0) {
                                                    $get_imap_row = mysqli_fetch_assoc($get_imap_setting);
                                                    if ($get_imap_row['imap_host'] != "") {
                                                        $imap_hostname = $get_imap_row['imap_host'];
                                                    }
                                                    if ($get_imap_row['imap_userame'] != "") {
                                                        $imap_userame = $get_imap_row['imap_userame'];
                                                    }
                                                    if ($get_imap_row['imap_passowrd'] != "") {
                                                        $imap_passowrd = $get_imap_row['imap_passowrd'];
                                                    }
                                                    if ($get_imap_row['imap_port'] != "") {
                                                        $imap_port = $get_imap_row['imap_port'];
                                                    }
                                                    if ($get_imap_row['imap_secure'] != "") {
                                                        $imap_authentication = $get_imap_row['imap_secure'];
                                                    }
                                                }
                                               
                                                ?>                   
                                                <ul style="overflow: visible;width: 100%;padding: 7px 0 6px;margin: 0;list-style: none;">
                                                    <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Imap Mail Server</b>
                                                    <input type ="text" name ="imap_host" old="" value="<?php echo $imap_hostname; ?>"/></li>
                                                    <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Username</b> <input type ="text" name ="imap_userame" value="<?php echo $imap_userame; /*echo $related_mail;*/ ?>"></li>
                                                    <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Password</b> <input type ="password" name ="imap_passowrd" value="<?php if (!empty($imap_passowrd)) {echo decryptIt($imap_passowrd); } ?>"></li>
                                                </ul>
                                                <br/><br/>

                                                <div style="margin-top: 40px;">
                                                    <a id="advanced_imap_settings" onclick="showadvance('primary');
                                                            return false;" style="font: bold 12px/18px Arial, Helvetica, sans-serif; cursor: pointer; margin-left: 10px;">
                                                        Show advanced settings
                                                    </a>
                                                    <a href="#" onclick="sendTest1(document.forms['MyAccount'], 'primary', '0', 'false');
                                                            return false" class="btn_blue"><span>Test Imap Connection</span></a>
                                                    <ul id="advance_imap_setting" style="clear: both; display:none;overflow: visible;width: 100%;padding: 7px 0 6px;margin: 0;list-style: none;">
                                                        <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Imap Port</b> <input type ="text" name ="imap_port" value="<?php echo $imap_port; ?>"></li>
                                                        <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Enable Imap over SSL or TLS?</b>
                                                            <select name="imap_secure">
                                                                <option value="none" <?php if ($imap_authentication == "") { echo "selected";} ?>>none</option>
                                                                <option value="ssl" <?php if (strtoupper($imap_authentication) == "SSL") { echo "selected"; } ?>>SSL</option>
                                                                <option value="tls"<?php if (strtoupper($imap_authentication) == "TLS") { echo "selected"; } ?>>TLS</option>
                                                            </select>
                                                        </li>
                                                        <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"></li>
                                                    </ul>
                                                </div>
                                                <input type="hidden" id="imap_connection_approved" name="imap_connection_approved" value="no"/>                        
                                            </div>
                                        <?php } ?>

                                </div>

                                <div id="track_secondary_holder">
                                        <?php
                                            while ($secEmail1 = mysqli_fetch_assoc($sec1)) {
                                                $imap_hostname = !empty($secEmail1['imap_host'])?$secEmail1['imap_host']:'imap.mail.yahoo.com';
                                                $imap_passowrd = $secEmail1['imap_passowrd'];
                                                $imap_port = !empty($secEmail1['imap_port'])?$secEmail1['imap_port']:993;
                                                $imap_authentication = !empty($secEmail1['imap_secure'])?$secEmail1['imap_secure']:'ssl';
                                               
                                                $authtoken05 = $secEmail1['authtoken'];
                                                $email_type = explode('@', $secEmail1['EmailID']);
                                                if (!empty($authtoken05)) {
                                                    $email_type[1] = 'gmail.com';
                                                }
                                        ?>
                                        <div id="show_success_<?php echo $secEmail1['id'] ?>" style="margin-top: 10px;"></div>
                                        <div id="track_secondary_<?php echo $secEmail1['id'] ?>" style="display:none">

                                            <?php if ($email_type[1] != 'gmail.com') { ?>
                                                <a style="font: bold 12px/18px Arial, Helvetica, sans-serif; cursor: pointer;width: 74%;float: right;margin-top: -48px;" onclick="togglesettings('<?php echo $secEmail1['id'] ?>');return false;">
                                                    <u>Settings</u></a>
                                                <?php
                                            }

                                            if ($secEmail1['use_authtoken'] == '0' && ($email_type[1] == 'gmail.com' || !empty($authtoken05)))
                                                echo'<input type="hidden" value="gmail_false" id="google_auth_' . $secEmail1['id'] . '">';
                                            else if ($secEmail1['use_authtoken'] != '0' && ($email_type[1] == 'gmail.com' || !empty($authtoken05)))
                                                echo'<input type="hidden" value="gmail_true" id="google_auth_' . $secEmail1['id'] . '">';
                                            else if ($secEmail1['use_authtoken'] == '0' && ($email_type[1] != 'gmail.com' || empty($authtoken05)))
                                                echo'<input type="hidden" value="false" id="google_auth_' . $secEmail1['id'] . '">';

                                            if ($secEmail1['use_authtoken'] == '0') {    ?>
                                                <div class="row" id="reply_tracking_<?php echo $secEmail1['id']; ?>" style="display:none">
                                                    <ul style="overflow: visible;width: 100%;padding: 7px 0 6px;margin: 0;list-style: none;">
                                                        <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Imap Mail Server</b>
                                                            <input type="hidden" name="sec_<?php echo $secEmail1['id'] ?>_dmtoemail" id="dmtoemail" value="imap.se"/>
                                                            <input type ="text" name ="sec_<?php echo $secEmail1['id'] ?>_imap_host" value="<?php echo $imap_hostname; ?>"/></li>
                                                        <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Username </b> <input type ="text" name ="sec_<?php echo $secEmail1['id'] ?>_imap_username" value="<?php echo $secEmail1['imap_userame'];//$secEmail1['EmailID']; ?>"></li>
                                                        <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Password</b> <input type ="password" name ="sec_<?php echo $secEmail1['id'] ?>_imap_passowrd" value="<?php if (!empty($imap_passowrd)) { echo decryptIt($imap_passowrd); } ?>"></li>
                                                    </ul>
                                                    <br/><br/>

                                                    <div style="margin-top: 40px;">
                                                        <a onclick="showadvance('<?php echo $secEmail1['id'] ?>');
                                                                return false;" style="font: bold 12px/18px Arial, Helvetica, sans-serif; cursor: pointer; margin-left: 10px;">
                                                            Show advanced settings
                                                        </a>
                                                        <a href="#" onclick="sendTest1(document.forms['MyAccount'], 'secondary', '<?php echo trim($secEmail1['id']); ?>', 'false');
                                                                return false" class="btn_blue"><span>Test Imap Connection</span></a>
                                                        <ul id="advance_imap_setting_<?php echo $secEmail1['id'] ?>" style="clear: both; display:none;overflow: visible;width: 100%;padding: 7px 0 6px;margin: 0;list-style: none;">
                                                            <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Imap Port</b> <input type ="text" name ="sec_<?php echo $secEmail1['id'] ?>_imap_port" value="<?php echo $imap_port; ?>"></li>
                                                            <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Enable Imap over SSL or TLS?</b>
                                                                 <select name="sec_<?php echo $secEmail1['id'] ?>_imap_secure">
                                                                    <option value="" <?php if ($imap_authentication == "") {echo "selected";} ?>>none</option>
                                                                    <option value="ssl"<?php if (strtoupper($imap_authentication) == "SSL") {echo "selected"; } ?>>SSL</option>
                                                                    <option value="tls"<?php if (strtoupper($imap_authentication) == "TLS") { echo "selected";  } ?>>TLS</option>
                                                                </select>
                                                            </li>
                                                            <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"></li>
                                                        </ul>
                                                    </div>
                                                    <input type="hidden" id="sec_<?php echo $secEmail1['id']; ?>_imap_connection_approved" name="imap_connection_approved" value="yes"/>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <input type="hidden" name="secarray[]" value="<?php echo $secEmail1['id']; ?>">
                                        <?php } //while ?> 
                                </div>

                                <?php
                                } else {

                                    $userToken = mysqli_fetch_array(mysqli_query($db->conn,"select access_token,refresh_token from google_auth_tokens where userid='" . $_SESSION['TickleID'] . "' "));
                                    if (empty($userToken)) {     ?>

                                    <label for="Schedule" style="color: #ff5300;margin: 0px 0px 9px;float: left;width: 100%;font: bold 16px/18px Arial, Helvetica, sans-serif;padding: 0 0 7px;font-size: 15px;">Reply tracking
                                        <span class="ico_info">
                                            <span id="infoblockdetail" class="info-block info-block-m">
                                                <span id="infotopdetail" class="ib-t">
                                                    <span id="infotextdetail" class="info-text">
                                                        TickleTrain helps you manage the replies you get to your Tickle campaigns.
                                                        Options  include: <br/><br/> - Delete the campaign when a reply is received. <br/> - Be notified via email that a reply was received and decide what action to take.<br/>
                                                        - Do nothing. <br/><br/> Reply tracking requires a connection to your email to enable this feature.
                                                    </span>
                                                </span>
                                            </span>
                                        </span>

                                        <a id="imap_settings" style="font: bold 12px/18px Arial, Helvetica, sans-serif; cursor: pointer;" onclick="togglesettings('primary');
                                                return false;">
                                            <u>Settings</u></a>
                                    </label>

                                    <div id="primary_success" style="margin-top:10px;"></div>
                                    <div class="row" id="reply_tracking" style="display:none">
                                    <?php
                                        $get_related_mail = mysqli_query($db->conn,"select EMailID from tickleuser where TickleID='$_SESSION[TickleID]'");
                                        $related_mail_row = mysqli_fetch_assoc($get_related_mail);
                                        $related_mail = $related_mail_row['EMailID'];

                                        $mail_dmsystem_array = explode('@', $related_mail);
                                        $mail_dmsystem = $mail_dmsystem_array[1];

                                        $check_imap_setting = mysqli_query($db->conn,"select * from imap_settings where imap_mailer='$mail_dmsystem'");
                                        if (mysqli_num_rows($check_imap_setting) > 0) {
                                            $get_imap_settings = mysqli_fetch_assoc($check_imap_setting);
                                            $imap_hostname = $get_imap_settings['server'];
                                            $imap_port = $get_imap_settings['Port'];
                                            $imap_authentication = $get_imap_settings['authentication'];
                                        }

                                        $get_imap_setting = mysqli_query($db->conn,"select imap_host,imap_userame,imap_passowrd,imap_port,imap_secure from tickleuser where TickleID='$_SESSION[TickleID]'");
                                        if (mysqli_num_rows($get_imap_setting) > 0) {
                                            $get_imap_row = mysqli_fetch_assoc($get_imap_setting);
                                            if ($get_imap_row['imap_host'] != "") {
                                                $imap_hostname = $get_imap_row['imap_host'];
                                            }
                                            if ($get_imap_row['imap_userame'] != "") {
                                                $imap_userame = $get_imap_row['imap_userame'];
                                            }
                                            if ($get_imap_row['imap_passowrd'] != "") {
                                                $imap_passowrd = $get_imap_row['imap_passowrd'];
                                            }
                                            if ($get_imap_row['imap_port'] != "") {
                                                $imap_port = $get_imap_row['imap_port'];
                                            }
                                            if ($get_imap_row['imap_secure'] != "") {
                                                $imap_authentication = $get_imap_row['imap_secure'];
                                            }
                                        }

                                    ?>                   
                                        <ul style="overflow: visible;width: 100%;padding: 7px 0 6px;margin: 0;list-style: none;">
                                            <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Imap Mail Server</b>
                                                <input type ="text" name ="imap_host" value="<?php echo $imap_hostname; ?>"/></li>
                                            <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Username</b> <input type ="text" name ="imap_userame" value="<?php echo $imap_userame; ?>"></li>
                                            <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Password</b> <input type ="password" name ="imap_passowrd" value="<?php if (!empty($imap_passowrd)) {
                                                    echo decryptIt($imap_passowrd);
                                                } ?>">
                                            </li>
                                        </ul>
                                        <br/><br/>

                                        <div style="margin-top: 40px;">
                                            <a id="advanced_imap_settings" onclick="showadvance('primary');
                                                    return false;" style="font: bold 12px/18px Arial, Helvetica, sans-serif; cursor: pointer; margin-left: 10px;">
                                                Show advanced settings
                                            </a>
                                            <a href="#" onclick="sendTest1(document.forms['MyAccount'], 'primary', '0', 'false');
                                                    return false" class="btn_blue"><span>Test Imap Connection</span></a>
                                            <ul id="advance_imap_setting" style="clear: both; display:none;overflow: visible;width: 100%;padding: 7px 0 6px;margin: 0;list-style: none;">
                                                <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Imap Port</b> <input type ="text" name ="imap_port" value="<?php echo $imap_port; ?>"></li>
                                                <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"><b>Enable Imap over SSL or TLS?</b>
                                                    <select name="imap_secure"><option value="" <?php if ($imap_authentication == "") { echo "selected"; } ?>>none</option>
                                                        <option value="ssl"<?php if (strtoupper($imap_authentication) == "SSL") { echo "selected"; } ?>>SSL</option>
                                                        <option value="tls"<?php if (strtoupper($imap_authentication) == "TLS") { echo "selected"; } ?>>TLS</option>
                                                    </select>
                                                </li>
                                                <li style="float: left;padding: 0 20px 0 0;max-width: 213px;"></li>
                                            </ul>
                                        </div>
                                        <input type="hidden" id="imap_connection_approved" name="imap_connection_approved" value="no"/>                        
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <label for="Schedule" style="color: #ff5300;margin: 0px 0px 9px;float: left;width: 100%;font: bold 16px/18px Arial, Helvetica, sans-serif;padding: 0 0 7px;font-size: 15px;">Reply tracking 
                                        <span class="ico_info">
                                            <span id="infoblockdetail" class="info-block info-block-m">
                                                <span id="infotopdetail" class="ib-t">
                                                    <span id="infotextdetail" class="info-text">
                                                        TickleTrain helps you manage the replies you get to your Tickle campaigns.
                                                        Options  include: <br/><br/> - Delete the campaign when a reply is received. <br/> - Be notified via email that a reply was received and decide what action to take.<br/>
                                                        - Do nothing. <br/><br/> Reply tracking requires a connection to your email to enable this feature.
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                    <div id="primary_success" style="margin-top:10px;"></div>
                                <?php }
                            }
                            ?>
                        </div>      
                        <!-----------Rply Tracking End ----------------------------->

                        <div id="mailsection" style="padding-left:30px;">
                            <div class="clear"></div>
                            <div style="border-top:1px solid silver;width:100%;height:2px;padding-bottom: 5px;margin-top: 20px;"></div>
                            <div class="section">
                                <h2 style="font-size: 15px;">Mail type</h2>
                                <ul class="radio_area">
                                    <li>
                                        <input type="radio" name="mail_type" class="mail_type" value="text"
                                               id="text_type" <?php echo $user_details["mail_type"] == "text" ? "checked='checked'" : ""; ?>/>
                                        <label for="text_type">Text</label>
                                    </li>
                                    <li>
                                        <input type="radio" name="mail_type" class="mail_type" value="html"
                                               id="html_type" <?php echo ($user_details["mail_type"] == "html" || trim($user_details["mail_type"]) == "") ? "checked='checked'" : ""; ?>/>
                                        <label for="html_type">HTML</label>
                                    </li>
                                </ul>
                            </div>
                            <h2 style="font-size: 15px;">Signature <span class="ico_info"><span class="info-block info-block2">
                                        <span class="ib-t">
                                            <span class="info-text">
                                                Create a signature to be included in your Tickles.  Use the Add Signature tool in the html editor when creatng a Tickle to insert or type: [signature]
                                                If you change your signature here, all Tickles that use this feature will be updated.
                                            </span>
                                        </span>
                                    </span></span>
                            </h2>
                                    <?php
                                        $sec = mysqli_query($db->conn,"select * from secondaryEmail where TickleID='" . $_SESSION['TickleID'] . "' and isdelete='0'");
                                        if ($user_details['email_addon'] != '' && $user_details['addon_hosting_id'] != '' && ($status['status'] == 'Active' || $status['status'] == 'Pending')) {
                                            echo"<div id='sec_plugin_holder'>";
                                            while ($secEmail = mysqli_fetch_assoc($sec)) {
                                                if ($secEmail["signature"] == '')
                                                $secEmail["signature"] = 'Thank you!!';
                                    ?>
                                    <div class="plugin_holder" id="sec_sign_<?php echo $secEmail['id']; ?>" style="display:none">
                                        <textarea name="signature_<?php echo $secEmail['id']; ?>" id="signature<?php echo $secEmail['id']; ?>"
                                                  class="tinymce"><?php echo $secEmail["signature"]; ?></textarea>
                                    </div>
                                    <div class="clear"></div>
                                    <?php
                                        }
                                        echo"</div>"; /* Sec-plugin-holder */
                                    }
                                    ?>

                            <div class="plugin_holder" id="main_sign">
                                <textarea name="signature" id="signature" class="tinymce"><?php echo $user_details["signature"]; ?>
                                </textarea>
                            </div>
                            <div class="clear"></div>
                        </div>


                        <h2>Other settings</h2>
                            <?
                            $times = time24to12($user_details['TimeDailyTickle']);
                            "12:01 AM";
                            ?>
                        <div class="row" style="overflow: visible;">
                            <? /* onmouseover="$(this).find('.info-block').show()" onmouseut="$(this).find('.info-block').hide()" */ ?>
                            <label for="TimeDailyTickle">Send Today's Tickles notifications at <span
                                    class="ico_info"><span class="info-block">
                                        <span class="ib-t">
                                            <span class="info-text">
                                                Today's Tickles is an email delivered to your inbox anytime you have Tickles scheduled for that day. Set the time this email gets delivered.
                                            </span>
                                        </span>
                                    </span></span></label>

                            <div style="clear:both;"></div>
                            <input name="TimeDailyTickle" id="TimeDailyTickle" value="<?= $times ?>"/>
                        </div>
                        
                        <input id="enable_alt" type="checkbox" class="checkbox" name="enable_alt"
                                value="1" <?php echo $user_details["enable_alt"] == 1 ? "checked='checked'" : ""; ?>
                                onclick="checkAddEmail(this)"/>
                         <label for="enable_alt">Send Today's Tickles email notification to an additional email address
                            <span
                             class="ico_info">
                                <span class="info-block">
                                    <span class="ib-t">
                                         <span class="info-text">
                                            Deliver Tickle notifications to an additional email address.
                                         </span>
                                     </span>    
                                 </span>
                             </span>
                        </label>

                        <div class="clear" style="height: 19px">&nbsp;</div>

                        <div class="row" style="display:<?=(intval($user_details["enable_alt"]) ? "" : "none")?>" id="addemailrow">
                            <label for="alt_email" style="color: #ff5300;margin: 0px 0px 9px;float: left;width: 100%;font: bold 16px/18px Arial, Helvetica, sans-serif;padding: 0 0 7px;">Additional email address
                             <span class="info-block info-block2">
                            </label>

                             <div style="clear:both;"></div>
                             <input type="text" name="alt_email" id="alt_email"  value="<?php echo $user_details["alt_email"]; ?>"/>
                        </div>
                        
                        
                        <input id="enable_alt_bcc" type="checkbox" class="checkbox" name="enable_alt_bcc"
                               value="1" <?php echo $user_details["enable_alt_bcc"] == 1 ? "checked='checked'" : ""; ?>
                               onclick="checkAddEmail_bcc(this)"/>
                        <label for="enable_alt_bcc">Send a copy of your Tickle to an additional email address <span
                                class="ico_info"><span class="info-block">
                                    <span class="ib-t">
                                        <span class="info-text">
                                            When Tickle Train sends an email on your behalf, this feature will send a copy of the email to additional email addresses.
                                        </span>
                                    </span> 
                                </span></span>
                        </label>

                        <div class="clear" style="height: 1px">&nbsp;</div>

                        <div class="row" style="display:<?= (intval($user_details["enable_alt_bcc"]) ? "" : "none") ?>" id="addemailrow_bcc">
                            
                            <span class="info-block info-block2">
                                <span class="ib-t">
                                    <span class="info-text" style="z-index:9999;">You can use multiple email accounts with TickleTrain.  This allows you to use the same Tickle BCC addresses with other email accounts.  This option is available on paid plans only.</span>
                                </span>
                            </span>

                            <script>
                               function add_alt_email()
                               {
                                   var cnt = $('.altEmailRow').length;
                                   $("#altEmailRow0").clone().appendTo( "#altEmailRowsDiv" ).attr("id", "altEmailRow"+cnt);
                                   $("#altEmailRow"+cnt).find('a').show();
                                   $("#altEmailRow"+cnt).find('input').val('');
                               }
                               
                               function delete_alt_email(evnt)
                               {
                                   $(evnt).closest('.altEmailRow').remove();
                               }
                            </script>
                            <div style="clear:both;"></div>
                            <div id="altEmailRowsDiv">
                                <?php 
                                $altEmailValues = explode(',', $user_details["alt_email_bcc"]);
                                $no=0;                                
                                foreach($altEmailValues as $altEmailValue){ ?>
                                <div id="altEmailRow<?php echo $no; ?>" class="altEmailRow" style="width:100%; height: auto; padding: 10px 0 0 0; float: left;">
                                    <input type="text" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,8}$" name="alt_email_bcc[]" id="alt_email_bcc" value="<?php echo $altEmailValue; ?>"/>
                                    <a href="javascript:" onclick="delete_alt_email(this);" style="margin-left:15px; display: <?php if($no=='0'){ echo 'none'; } ?>;">Delete</a>
                                </div>
                                <?php $no++; } ?>
                            </div>    
                            <div style="width:100%; height: auto; float: left;">
                                <span style="float:left; width: 100%; padding-bottom: 5px;">(lowercase text)</span>
                                <a href="javascript:add_alt_email();">Add email</a>
                            </div>
                        </div>
                            <?php if ($user_details['Plan'] != 1) { ?>
                            <div class="clear">&nbsp;</div>
                            <input type="checkbox" id="blueplanbarning" class="checkbox" name="blueplanbarning"
                                   value="1" <?php echo $user_details["blueplanbarning"] == 1 ? "checked='checked'" : ""; ?>/>
                            <label for="blueplanbarning"> Help Promote TickleTrain
                                <span
                                    class="ico_info"><span class="info-block">
                                        <span class="ib-t">
                                            <span class="info-text">
                                                Support TickleTrain.  A small promotional message will appear at the bottom of your Tickles.  Thank you!

                                            </span>
                                        </span>
                                    </span></span></label>
                            <div class="clear">&nbsp;</div>
                            <?php } ?>
                        <?php if ($uid != "") { ?>
                            <h3>Facebook Details</h3>
                            <div class="row"><?php echo $_SESSION['first_name']; ?></div>
                            <div class="row"><?php echo $_SESSION['last_name']; ?></div>
                            <div class="row"><?php echo $_SESSION['email']; ?></div>
                            <div class="row"><img src="https://graph.facebook.com/<?php echo $_SESSION['uid']; ?>/picture?type=small"></div>
                            <div class="row">
                                <?php
                                    $faceemail = mysqli_query($db->conn,"select FbCheck from contact_list where EmailID='" . $user_details['EmailID'] . "'");
                                    $face_email = mysqli_fetch_array($faceemail);
                                    //echo $face_email['FbCheck'];
                                    if ($face_email['FbCheck'] == 'Y' && false) {
                                        echo "Facebook Details updated in Tickle Train";
                                    } else if (false) {
                                ?>
                                <input type="checkbox" name="update_facebook" id="update_facebook" value="update_face"/>
                                <label for="update_facebook">Update in Tickle Train</label>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
        </fieldset>
    </form>
</div>


<? include_once "includes/ckeditor_inc.php"; ?>
<script type="text/javascript">
    var mailservers =<?= json_encode($GLOBALS['server_settings_params']) ?>;
    var maildomains =<?= json_encode($GLOBALS['server_settings_domains']) ?>;
    var ckready = false;
    var defoult_smtp_setting = <?= json_encode($GLOBALS['server_settings_params']) ?>;

    $(document).ready(function(){

        $('.smtp_hosted_with').on('change',function(){
            $('#DMFrom_block').hide();
          var opt =  $(this).children("option:selected").val();
          var client_smtp_setting =  $(this).data().json;
          var id =  $(this).data().id;
          var prefix =  '';
          if((id != '')  && (id != null) && (typeof id != 'undefined')){
              prefix = 'sec';
          }
            if(opt == ''){
                $('#smtpSetting'+id).hide();
                $('#gmailSetting'+id).hide();
                return false;
            }
            if(opt == 'gmail'){
                $('#smtpSetting'+id).hide();
                $('#gmailSetting'+id).show();
                return false;
            }if(opt == 'yahoo'){
                if( client_smtp_setting.dmsmtp != 'smtp.mail.yahoo.com' || client_smtp_setting.dmsmtp == '' || typeof client_smtp_setting.dmsmtp == 'undefined' ){
                    $('input[name='+prefix+'dmsmtp'+id+']').val(defoult_smtp_setting[opt].server);
                    $('input[name='+prefix+'dmport'+id+']').val(defoult_smtp_setting[opt].port);
                    $('input[name='+prefix+'dmsecure'+id+'] option[value='+defoult_smtp_setting[opt].encryption+']').attr('selected','selected');
                    $('input[name='+prefix+'dmpwd'+id+']').val('');
                
                }else{

                    $('input[name='+prefix+'dmsmtp'+id+']').val(client_smtp_setting.dmsmtp);
                    $('input[name='+prefix+'dmport'+id+']').val(client_smtp_setting.dmport);
                    $('input[name='+prefix+'dmsecure'+id+'] option[value='+client_smtp_setting.dmsecure+']').attr('selected','selected');
                    $('input[name='+prefix+'dmpwd'+id+']').val(client_smtp_setting.dmpwd);

                }

            }
            else{

                if(client_smtp_setting.dmuser != ""){
                    var regEx = /^[a-zA-Z0-9._]+@[a-zA-Z]+.[a-zA-Z]{2,3}/;
                    var validEmail = regEx.test(client_smtp_setting.dmuser);
                    if (!validEmail){
                        $('#DMFrom_block_'+id).slideDown();
                    }else{
                        $('#DMFrom_block_'+id).slideUp();
                    }
                }

                if( client_smtp_setting.dmsmtp == 'smtp.mail.yahoo.com' || client_smtp_setting.dmsmtp == '' || typeof client_smtp_setting.dmsmtp == 'undefined' ){
                    $('input[name='+prefix+'dmsmtp'+id+']').val(defoult_smtp_setting[opt].server);
                    $('input[name='+prefix+'dmport'+id+']').val(defoult_smtp_setting[opt].port);
                    $('input[name='+prefix+'dmsecure'+id+'] option[value='+defoult_smtp_setting[opt].encryption+']').attr('selected','selected');
                    $('input[name='+prefix+'dmpwd'+id+']').val('');

                }else{
                    $('input[name='+prefix+'dmsmtp'+id+']').val(client_smtp_setting.dmsmtp);
                    $('input[name='+prefix+'dmport'+id+']').val(client_smtp_setting.dmport);
                    $('input[name='+prefix+'dmsecure'+id+'] option[value='+client_smtp_setting.dmsecure+']').attr('selected','selected');
                    $('input[name='+prefix+'dmpwd'+id+']').val(client_smtp_setting.dmpwd);
                }
            }

            $('#gmailSetting'+id).hide();
            $('#smtpSetting'+id).show();

            $.each(defoult_smtp_setting[opt].labels,function(inx,val){
                    $('.'+inx).html(val);
            });

            if(typeof defoult_smtp_setting[opt].help_text != 'undefined' ){
                $('.yahoo-p-content').html(defoult_smtp_setting[opt].help_text);
            }
        });

    setTimeout(function(){ $('.smtp_hosted_with:visible').trigger('change'); },1200);

    $('.dmuse').on('click',function(){
        setTimeout(function(){ $('.smtp_hosted_with:visible').trigger('change'); },100);
    });

         // $('select.smtp_hosted_with').isvisible(function() {
         //    alert("do something");
         //  });
    });


    // track rply functions
    function togglesettings(id) {
        $("#reply_tracking_" + id).toggle();
        if (id == 'primary') {
            $("#reply_tracking").toggle();
        }

    }
    function showadvance(id) {
        $("#advance_imap_setting_" + id).toggle();
        if (id == 'primary') {
            $("#advance_imap_setting").toggle();
        }
    }
    function showhide_setting_track(val) {
        $("#track_secondary_holder").children().hide();
        var res = val.split("_");
        if (val == 'mainemail') {
            $('#primary_reply').show();
            $('#email_type').val('primary');
        }
        else {
            $('#primary_reply').hide();
            $('#' + val).show();
            $('#email_type').val('secondary');
        }
    }
    
    function stopImapConnection(imapAjaxRequest,type,id)
    {    
        if (type == 'primary') {             
             var chkimap = $("#primary_success").html();
             if(chkimap=='<img src="../images/loading_circle.gif" height="50px" width="50px">')
             {    
                $('#forcepress').val('0');
                imapAjaxRequest.abort();
                var txt = "Connection failed. Verify your settings and password and try again. You may need to also check under (advanced settings) and make additional updates.\n\
                        <br><br>If you feel your password and settings are correct, please copy the error message below and check with your email provider. Your email provider can give you the proper settings to try again. Most common reason for a connection error is wrong port number under advanced settings.";
                if ($("#google_auth_primary").val() == 'false') {
                    $("#primary_success").html("<img src='../images/not-configured.png'>");
                    $("#imap_settings").show();
                }
                else if ($("#google_auth_primary").val() == 'gmail') {
                    $("#primary_success").html("<img src='../images/not-configured.png'>");
                }

                else {
                    $("#primary_success").html('');
                }
             }   
        }else{
            var chkimap = $('#show_success_' + id).html();
             if(chkimap=='<img src="../images/loading_circle.gif" height="50px" width="50px">')
             {              
               $('#forcepress').val('0');
               imapAjaxRequest.abort();
               var txt = "Connection failed. Verify your settings and password and try again. You may need to also check under (advanced settings) and make additional updates.\n\
                       <br><br>If you feel your password and settings are correct, please copy the error message below and check with your email provider. Your email provider can give you the proper settings to try again. Most common reason for a connection error is wrong port number under advanced settings";
                    if ($("#google_auth_" + id).val() == 'false') {
                        $('#show_success_' + id).show();
                        $('#show_success_' + id).html("<img src='../images/not-configured.png'>");
                        $('#track_secondary_' + id).show();
                        $("#reply_tracking_" + id).show();
                    }
                    else if ($("#google_auth_" + id).val() == 'false') {
                        $('#show_success_' + id).html("<img src='../images/not-configured.png'>");
                    }               
                }         
            }
    }

    function sendTest1(frm, type, id, onload) {
        // var plan = "<?php //echo $user_details['Plan'];  ?>";
        // var blueplanbarning = "<?php //echo $user_details['blueplanbarning'];  ?>";
        var imapAjaxRequest = '';
        if (type == 'primary') {
            if (onload == 'false') {

                if ($("#google_auth_primary").val() == 'false') {
                    if ($("input[name='imap_host']").val() === "") {
                        mcalert("Error", "Please verify that your IMAP server name, username and password are correct and try again.");
                        return false
                    }
                    if ($("input[name='imap_userame']").val() === "") {
                        mcalert("Error", "Please verify that your IMAP server name, username and password are correct and try again.");
                        return false
                    }
                    if ($("input[name='imap_passowrd']").val() === "") {
                        mcalert("Error", "Please verify that your IMAP server name, username and password are correct and try again.");
                        return false
                    }
                }
            }

            $("#primary_success").html('<img src="../images/loading_circle.gif" height="50px" width="50px">');

            $("#dmtoemail").val($("#testemail").val());
            $("#reply_tracking").hide();
            $("#imap_settings").hide();
            $(this).dialog('close');
            var sr = serialize1($(frm));//$(frm).serializeArray();//
            sr += "&testsmtp=1&email_type=primary";
            // alert(sr);
            imapAjaxRequest = $.post("<?= Url_Create('myaccount') ?>", sr, function(data) {
                if (data == "true") {
                    $('#forcepress').val('0');
                    if ($("#google_auth_primary").val() == 'false') {
                        $("#imap_connection_approved").val("yes");
                        $("#reply_tracking").hide();
                        $("#primary_success").html('<img src="../images/configured-btn.png"><span class="ico_info"><span id="infoblockdetail" class="info-block info-block-m"><span id="infotopdetail" class="ib-t"><span id="infotextdetail" class="info-text">You can now edit your Tickles to turn on Reply Tracking</span></span></span></span>');
                        $("#imap_settings").show();
                        if (onload == 'false') {
                            mcalert("Notification", "Congratulations!  We were able to connect to your mail server.  You can now select tracking options for your Tickles.  Be sure and click Update at the top of this page to save your settings.");
                        }
                    }
                    else {
                        $("#primary_success").html('');
                    }
                    //mcalert("Notification", "Congratulations!  We were able to connect to your mail server.  You can now select tracking options for your Tickles.  Be sure and click Update at the top of this page to save your settings.");
                }
                else if ($("#google_auth_primary").val() == 'gmail_true') {
                    $("#imap_connection_approved").val("yes");
                    $("#reply_tracking").hide();
                    $("#primary_success").html('<img src="../images/configured-btn.png"><span class="ico_info"><span id="infoblockdetail" class="info-block info-block-m"><span id="infotopdetail" class="ib-t"><span id="infotextdetail" class="info-text">You can now edit your Tickles to turn on Reply Tracking</span></span></span></span>');
                    $("#imap_settings").hide();
                }

                else if ($("#google_auth_primary").val() == 'gmail_false') {
                    $("#reply_tracking").hide();
                    $("#imap_settings").hide();
                    $("#primary_success").html("<img src='../images/not-configured.png'>");
                }

                else {
                    var index1 = data.indexOf("'IMAP Error:");
                    var index2 = data.indexOf("' in");
                    var org_data = data.substring(index1, index2);
                    $('#forcepress').val('0');
                    var txt = "Connection failed. Verify your settings and password and try again. You may need to also check under (advanced settings) and make additional updates.\n\
                            <br><br>If you feel your password and settings are correct, please copy the error message below and check with your email provider. Your email provider can give you the proper settings to try again. Most common reason for a connection error is wrong port number under advanced settings.<br><br>Error Message: " + data + " ";
                    //mcalert("Notification", txt);

                    if ($("#google_auth_primary").val() == 'false') {
                        $("#primary_success").html("<img src='../images/not-configured.png'>");
                        $("#imap_settings").show();
                    }
                    else if ($("#google_auth_primary").val() == 'gmail') {
                        $("#primary_success").html("<img src='../images/not-configured.png'>");
                    }

                    else {
                        $("#primary_success").html('');
                    }
                }
            });
        }

        else {
            
            if ($("#google_auth_" + id).val() == 'false') {
                if (onload == 'false') {
                    if ($("input[name='sec_" + id + "_imap_host']").val() === "") {
                        mcalert("Error", "Please verify that your IMAP server name, username and password are correct and try again.");
                        return false
                    }
                    if ($("input[name='sec_" + id + "_imap_userame']").val() === "") {
                        mcalert("Error", "Please verify that your IMAP server name, username and password are correct and try again.");
                        return false
                    }
                    if ($("input[name='sec_" + id + "_imap_passowrd']").val() === "") {
                        mcalert("Error", "Please verify that your IMAP server name, username and password are correct and try again.");
                        return false
                    }
                }
            }

            $('#show_success_' + id).show();
            $('#show_success_' + id).html('<img src="../images/loading_circle.gif" height="50px" width="50px">');
            $("#primary_success").html("");
            $('#track_secondary_' + id).hide();
            
            var sr = serialize1($(frm));//$(frm).serializeArray();//
            sr += "&testsmtp=1&email_type=secondary&id=" + id;
            
           imapAjaxRequest = $.post("<?= Url_Create('myaccount') ?>", sr, function(data) {
                //alert("#google_auth_"+id);
                if (data == "true") {
                    $('#forcepress').val('0');
                    if ($("#google_auth_" + id).val() == 'false') {
                        $('#track_secondary_' + id).show();
                        $("#reply_tracking_" + id).hide();
                        $('#show_success_' + id).html('<img src="../images/configured-btn.png"><span class="ico_info"><span id="infoblockdetail" class="info-block info-block-m"><span id="infotopdetail" class="ib-t"><span id="infotextdetail" class="info-text">You can now edit your Tickles to turn on Reply Tracking</span></span></span></span>');
                        //alert(onload);
                        if (onload == 'false') {
                            mcalert("Notification", "Congratulations!  We were able to connect to your mail server.  You can now select tracking options for your Tickles.  Be sure and click Update at the top of this page to save your settings.");
                        }
                    }
                    // mcalert("Notification", "Congratulations!  We were able to connect to your mail server.  You can now select tracking options for your Tickles.  Be sure and click Update at the top of this page to save your settings.");
                }
                else if ($("#google_auth_" + id).val() == 'gmail_true') {
                    $('#show_success_' + id).html('<img src="../images/configured-btn.png"><span class="ico_info"><span id="infoblockdetail" class="info-block info-block-m"><span id="infotopdetail" class="ib-t"><span id="infotextdetail" class="info-text">You can now edit your Tickles to turn on Reply Tracking</span></span></span></span>');
                }
                else if ($("#google_auth_" + id).val() == 'gmail_false') {
                    $('#show_success_' + id).html("<img src='../images/not-configured.png'>");
                }


                else {
                    $('#forcepress').val('0');
                    var txt = "Connection failed. Verify your settings and password and try again. You may need to also check under (advanced settings) and make additional updates.\n\
                            <br><br>If you feel your password and settings are correct, please copy the error message below and check with your email provider. Your email provider can give you the proper settings to try again. Most common reason for a connection error is wrong port number under advanced settings.<br><br>Error Message: " + data + " ";
                    if ($("#google_auth_" + id).val() == 'false') {
                        $('#show_success_' + id).show();
                        $('#show_success_' + id).html("<img src='../images/not-configured.png'>");
                        $('#track_secondary_' + id).show();
                        $("#reply_tracking_" + id).show();
                    }
                    else if ($("#google_auth_" + id).val() == 'false') {
                        $('#show_success_' + id).html("<img src='../images/not-configured.png'>");
                    }
                    //mcalert("Notification", txt);
                }
            });
                

        }      
        setTimeout(function() { stopImapConnection(imapAjaxRequest,type,id) }, 15000);
    }

    function serialize1(frm) {
        var ret = $(frm).serialize();
        ret += "&EmailID=" + $("#EmailID").val() + "&dmfromemail=" + $("#dmfrom").val();
        return ret;
    }


    // function remove_yahoo_content(){
    //     $('.two-col label').each(function(){
    //         $(this).text($(this).text().replace('Yahoo!',''));
    //     });

    //     $('.yahoo-p-content').html('');
    //     $('#dmsmtp').val($('#dmsmtp').attr('old'));
    //     var imap_input = $('input[name=imap_host]');
    //     imap_input.val(imap_input.attr('old'));
    // }

    // function add_yahoo_content(){
    //    // console.log(default_imap_setting);
    //     var i = 1;
    //     $('.two-col label').each(function(){
    //         if(i <= 3){
    //             if($(this).text().indexOf('Yahoo!') == -1){
    //                 $(this).text('Yahoo! '+$(this).text());
    //             }
    //         }
    //         i++;
    //     });

    //     $('.yahoo-p-content').html('');

    //     if($('#dmsmtp').val() != 'smtp.mail.yahoo.com'){
    //         //$('#dmsmtp').attr('old',$('#dmsmtp').val());
    //         // $('#dmsmtp').val('smtp.mail.yahoo.com');
    //     }

    //     var imap_input = $('input[name=imap_host]');
    //     // if(imap_input.val() != 'imap.mail.yahoo.com'){
    //     //     imap_input.attr('old',$('input[name=imap_host]').val());
    //     //     imap_input.val('imap.mail.yahoo.com');

    //     // }

    // }
    // track rply functions end

    $(document).ready(function() {



        $('.sec-dmuser').on('blur',function(){
            var email  =  $(this).val();
            var id = $(this).data().id;
            console.log(id);
            if(email != ""){
                var regEx = /^[a-zA-Z0-9._]+@[a-zA-Z]+.[a-zA-Z]{2,3}/;
                var validEmail = regEx.test(email);
                if (!validEmail){
                     //   console.log(validEmail);
                    $('#DMFrom_block_'+id).slideDown();
                }else{
                   // console.log(validEmail);
                    $('#DMFrom_block_'+id).slideUp();
                }
            }
        });

        

        $('#new-setting-ul li').on('click',function(){
            $('#new-setting-ul li').removeClass('active-li');
            $(this).addClass('active-li');
        })

        $('#MyAccount').submit(function() {
            var cancel = {text: 'Close', click: function() {
                    $(this).dialog('close')
                }};

            if ($('#secondary_id').val() == 'primary') {
                $('#forcepress').val(0);
                var forcepress = $('#forcepress').val();
                if (forcepress == 1) {
                    var message = '';
                    mdialog("Click Send Test Email to confirm settings.", message, [cancel]);
                    return false;
                }
                var radiobuttonvalue = $('input:radio[name=dmuse]:checked').val();
                var password = $("#dmpwd").val();
                //check oauth before submit form
                var oauthcheck = $("#oauthcheck").val();
                /*if (oauthcheck == 'smtp') {
                    if (radiobuttonvalue == 1 && password == "") {
                        //$('#forcepress').val(1);
                        var message = '';
                        mdialog("Click Send Test Email to confirm settings.", message, [cancel]);
                        return false;
                    }
                }*/
            }
        });




        //alert(<?php echo $_SESSION['TickleID']; ?>);
        $("#TimeDailyTickle").datetimepicker({timeOnly: true, timeFormat: 'hh:mm TT', ampm: true,addSliderAccess: true,
	sliderAccessArgs: { touchonly: false },controlType: 'select'});
        $("#TickleAlertTime").datetimepicker({timeOnly: true, timeFormat: 'hh:mm TT', ampm: true});
        $('.mail_type').click(function() {
            if ($(this).val() == "html") {
                $('input[name^="secarray"]').each(function() {
                    var id = $(this).val();
                    CKEDITOR.replace('signature' + id, config);
                });
                CKEDITOR.replace('signature', config);
                //$('textarea.tinymce').ckeditor(config);
                ckready = true;
            } else {
                if (ckready) {
                    $('input[name^="secarray"]').each(function() {
                        var id = $(this).val();
                        CKEDITOR.instances['signature' + id].destroy();
                    });
                    CKEDITOR.instances['signature'].destroy();
                    //$('textarea.tinymce').ckeditorGet().destroy();
                    ckready = false;
                }
            }
        });
        $(".mail_type:checked").click();
        var radiobuttonvalue = $('input:radio[name=dmuse]:checked').val();
        if (radiobuttonvalue == 1) {
            //$(".row").show();   
        } else {
            $(".dmuse:checked").click();
        }
        $("#dmauth").click(function() {
            if ($(this).get(0).checked) {
                $("#username_title").parents(".DMTitle").show();
                $("#password_title").parents(".DMTitle").show();
            } else {
                $("#username_title").parents(".DMTitle").hide();
                $("#password_title").parents(".DMTitle").hide();
            }
        });
    });

    function checkAddEmail(chk) {
        if (chk.checked) {
            $("#addemailrow").show();
        } else {
            $("#addemailrow").hide();
        }
    }
    
    function checkAddEmail_bcc(chk) {
        if (chk.checked) {
            $("#addemailrow_bcc").show();
        } else {
            $("#addemailrow_bcc").hide();
        }
    }

    function showDMPanel(chk, click) {
        if ($(chk).val() == 1) {
            if ($("#secondary_id").val() != 'primary' && click == "true") {
                $('#DMDetails' + $("#secondary_id").val()).show();
                $('#text_show' + $("#secondary_id").val()).hide();
                $('#text_hide' + $("#secondary_id").val()).show();
            }
            else if ($("#secondary_id").val() == 'primary' && click == "true") {
                $('#DMDetails').show();
                $('#text_show').hide();
                $('#text_hide').show();
            }
            $("#dmusemain").show();
            $("#dmusefalse").hide();
            $("#send_Btn").show();
            $("#remove_btn").show();
            // alert('fdfsfds');
            //  if($('#emailtype').val()!="gmail.com")
            // $("#DMDetails").show();

            $('#forcepress').val('1');
            $(".DMPanelDef").hide();
            $(".DMPanel").show();
            if ($.trim($("#dmuser").val()) == '') {
                $("#dmuser").val($("#dmfrom").val());
            }
            var eml = $("#dmfrom").val().split('@');
            var ssystem = '';
            $.each(maildomains, function(ind, vl) {
                if (eml[1].indexOf(ind) >= 0) {
                    if (vl == 'yahoo') {
                        $("#dmuser").val(eml[0]);
                    }
                    ssystem = vl;
                    return false;
                }
            });

            $("#systembtn_" + ssystem).click();
            if (mailservers[ssystem]['isdefault']) {
                $("#toolbar").hide();
                //$("#username_title").parents(".DMTitle").hide();
                //$("#DMAdvancedLink").hide();
            } else {
                $("#DMAdvancedLink").show();
                for (var key in mailservers) {
                    if (mailservers[key]['isdefault']) {
                        $("#systembtn_" + key).hide();
                    }
                }
            }
            $('#DMDetails').show();
            $('.esettings1').hide();
            $('.esettings2').show();
        } else {
            $('#forcepress').val('0');
            $(".DMPanel").hide();
            $("#dmusemain").hide();
            $("#dmusefalse").show();
            $('.DMPanelDef').show();
            $("#remove_btn").hide();
            $("#send_Btn").hide();
        }
        $('.gmailYesNoSetting').hide();

        $('.smtp_hosted_with:visible').trigger('change');

    }

    function serialize(frm) {
        var ret = $(frm).serialize();
        ret += "&EmailID=" + $("#EmailID").val() + "&dmfromemail=" + $("#dmfrom").val();
        return ret;
        /*var inputs = $(':input[name!="submit"]', frm);
         var obj = $.map(inputs, function (n, i) {
         var o = n.name + "=" + escape($(n).val());
         return o;
         });
         return obj.join("&");*/
    }

    function sendTest(frm, auth, type, secemailid) {
        // var plan = "<?php echo $user_details['Plan']; ?>";
        // var blueplanbarning = "<?php echo $user_details['blueplanbarning']; ?>";
        var cancel = {text: 'Cancel', click: function() {
                $(this).dialog('close')
            }};

        /*if(auth!='authToken'){
         if($("#dmpwd").val() === ""){
         mcalert("Error","Please enter correct smtp server name, username and password");
         return false
         }
         if($("#dmsmtp").val() === ""){
         mcalert("Error","Please enter correct smtp server name, username and password");
         return false
         }
         if($("#dmuser").val() === ""){
         mcalert("Error","Please enter correct smtp server name, username and password");
         return false
         } 
         }   */

        var send = {text: 'Send', click: function() {
                $("#dmtoemail").val($("#testemail").val());
                $(this).dialog('close');
                var sr = serialize($(frm));

                if (auth == 'authToken')
                {
                    sr += "&testsmtp1=1&authToken=yes&sendemailid=" + secemailid + "&emailaccount=" + type + "&email=" + $('#testemail').val();
                }
                else
                {
                    //$(frm).serializeArray();//                    
                    sr += "&testsmtp1=1&emailaccount=" + type + "&sendemailid=" + secemailid;
                }





                // alert(sr);
                $.post("<?= Url_Create('myaccount') ?>", sr, function(data) {
                    //alert(data);
					console.log(data);
                    if (data == "true") {
                        $('#forcepress').val('0');
                        mcalert("Notification", "Test email has been sent successfully. <br><br>Be sure to click <b>Update</b> at the top of this page to save your settings.");
                    } else {
                        var index1 = data.indexOf("'SMTP Error:");
                        var index2 = data.indexOf("' in");
                        var org_data = data.substring(index1, index2);
                        // alert(org_data);
                        //alert(index2);
                        //var newdata = data.substring(67,200);
                        //var newdata1 = newdata.replace('in /var/www/vhosts/client.tickletrain.com/httpdocs/includes/class/phpmailer/class.phpmailer.php', '');

                        $('#forcepress').val('0');
                        var txt = "The email test was unsuccessful. Please check your password and try again. <br><br>If you feel your password is correct, please copy the error message below and check with your email provider. You may need to authorize TickleTrain to send using your email account. Or this email provider does not allow SMTP sending.<br><br>Error Message: SMTP Connect() failed." + org_data + " ";
                        if ($("#server_title").is(":visible")) {
                            txt = "The email test was unsuccessful. Verify your settings and password and try again.<br><br>If you feel your password is correct, please copy the error message below and check with your email provider. You may need to authorize TickleTrain to send using your email account. Or this email provider does not allow SMTP sending.<br><br> Error Message: SMTP Connect() failed." + org_data + " ";
                        }
                        mcalert("Notification", txt);
                    }
                });
            }
        };
        var message = "<input type='text' style='' placeholder='Enter an email address' name='testemail' id='testemail'/>";
        mdialog("Send Email", message, [send, cancel]);
        $(".ui-dialog:first").css("float", "none");
        $(".ui-dialog :button:first").css("text-align", "center");
        $(".ui-widget-header :first").css("font-size", "18px");
        $(".ui-dialog:first").css("width", "300")
    }

    function selectSettings(btn, server) {
        if (mailservers[server]) {
            if (typeof (mailservers[server]['server']) != 'undefined') {
                if (mailservers[server]['server'] != '') {
                    $("#dmsmtp").val(mailservers[server]['server']);
                }
            }
            if (typeof (mailservers[server]['port']) != 'undefined' && $("#dmport").val() == "") {
                $("#dmport").val(mailservers[server]['port']);
            }
            if (typeof (mailservers[server]['encryption']) != 'undefined') {
                $("#dmsecure").val(mailservers[server]['encryption']);
            }
            // $(".DMTitle").hide();
            for (ids in mailservers[server]['labels']) {
                if (mailservers[server]['labels'][ids] != '') {
                    $("#" + ids).html(mailservers[server]['labels'][ids]);
                    $("#" + ids).parents(".DMTitle").show();
                } else {
                    $("#" + ids).show();
                }
            }
        }
        $("#dmsystem").val(server);
        $(".systemselect").removeAttr("disabled");
        $(btn).attr("disabled", "disabled");
    }
    //user info update
        function open_popup() {
            var cancel = {text: 'Close', click: function() {
                    $(this).dialog('close')
                }};
            var message = '';
            mdialog('Update Info', '<form action="<?= Url_Create("myaccount"); ?>" method="post" onsubmit="return checkpass();"><div class="left_side"><div class="row"><label for="popupuname">Username <span class="req">*</span></label><span class="input_text"><input type="text" name="UserName" id="popupuname" value="<? echo $user_details["UserName"]; ?>" size="32"onkeypress="javascript:return charnumbersonly(event);"></span> </div> <div class="row"><label for="Password">Password <span class="req">*</span></label><span class="input_text"><input type="password" name="Password" id="popuppass" value="Password" size="32"></span></div>  <div class="row"><label for="rpassword">Repeat Password <span class="req">*</span></label><span class="input_text"><input type="password" name="rpassword" id="rpassword" value="Password" size="32"></span></div> <div class="row"> <label for="EmailID">E-mail ID <span class="req">*</span></label> <span class="input_text"><input type="text" name="EmailID" id="popupemail" value="<? echo $user_details["EmailID"]; ?>" size="32"></span><!-- readonly="readonly" --><input type="submit" name="update_email" value="Update" class="btn_blue show-hide" id="update_email" style="margin-left:5px;margin-top:5px;background-color: #0090C7;border: none;border-radius: 2px;padding: 5px;"></div></div><span id="passerror" style="color:red;"></span><br><span id="emailerror" style="color:red;"></span><br><span id="unameerror" style="color:red;"></span></form>', [cancel]);

        }
        var checkemail = 'true';
        var checkuname = 'true';
        function checkpass() {
            var pass = $('#popuppass').val();
            var rppass = $('#rpassword').val();
            var uname = $('#popupuname').val();
            var email = $('#popupemail').val();
            $().html('Processing....');

            var sessionuname = '<?php echo $_SESSION["UserName"]; ?>';
            var sessionemail = '<?php echo $_SESSION["EmailID"]; ?>';
            //alert(email);
            $('#unameerror').html('');
            $('#emailerror').html('');
            $('#passerror').html('');
            if (email != sessionemail) {
                $.ajax({
                    url: "<?= Url_Create('myaccount'); ?>",
                    type: "post",
                    async: false,
                    data: {'check_email': email},
                    success: function(result) {
                        checkemail = result;
                    }});

            }
            else
                checkemail = 'true';

            if (uname != sessionuname) {
                $.ajax({
                    url: "<?= Url_Create('myaccount'); ?>",
                    type: "post",
                    async: false,
                    data: {'check_uname': uname},
                    success: function(result) {
                        checkuname = result;
                    }});
            }

            else
                checkuname = 'true';


            if (checkemail != 'true') {
                $('#emailerror').html('Email id alreay exists!!!');
                return false;
            }

            if (checkuname != 'true') {
                $('#unameerror').html('Username alreay exists!!!');
                return false;
            }

            else if (pass != rppass) {
                $('#passerror').html('Password and Repeat Password does not match');
                return false;
            }

            else {
                return true;
            }
            return false;

        }
    //user info update end her
</script>
<style>
    .buttons {
        float: right;
        width: 10%;
    }
</style>
<?php

if ($user_details['email_addon'] != '') {
    echo"<script>$(document).ready(function(){
        var setemail = getCookie('sec_email');
        $('#alternativeemail option').filter(function() {
            return this.value.indexOf( setemail ) > -1;
        })
        .prop('selected', true);
        var hide_showdiv = getCookie('hide_show_id');
        showhide_setting(hide_showdiv);
    });
    </script>";
} else if (!isset($_COOKIE['sec_email']) || $user_details['email_addon'] == '') {
    echo"<script>$(document).ready(function(){
        sendTest1(document.forms['MyAccount'],'primary','0','true');
        $('#main_sign').show();
    });
    </script>";
}


if(!empty($_POST['dmsmtp']) || !empty($_POST['dmuser']) || !empty($_POST['dmpwd'])){


    $mailchkSmtp->IsSMTP();                                      // Set mailer to use SMTP
    $mailchkSmtp->Host = $_POST['dmsmtp'];                 // Specify main and backup server
    $mailchkSmtp->Port = $_POST['dmport'];                                    // Set the SMTP port
    $mailchkSmtp->SMTPAuth = (isset($_POST['dmauth']) ? $_POST['dmauth'] : false ); // Enable SMTP authentication
    $mailchkSmtp->Username = $_POST['dmuser'];                // SMTP username
    $mailchkSmtp->Password = $_POST['dmpwd'];                  // SMTP password
    $mailchkSmtp->SMTPSecure = $_POST['dmsecure'];                            // Enable encryption, 'ssl' also accepted
    $mailchkSmtp->From = $_POST['dmuser'];        
    $mailchkSmtp->AddAddress('tickletraincron@gmail.com', '');  // Add a recipient//shine@123        
    $mailchkSmtp->IsHTML(true);                                  // Set email format to HTML
    $mailchkSmtp->Subject = 'Test SMTP Connection';
    $mailchkSmtp->Body    = 'Test SMTP Connection';
    $mailchkSmtp->AltBody = 'Test SMTP Connection';
    try {
        $SendMail22 = $mailchkSmtp->Send();
    } catch (Exception $e) {

    }
    if ($SendMail22) {
        mysqli_query($db->conn,"update tickleuser set DMSmtpOff='0' where TickleID='" . $_SESSION['TickleID'] . "'");
    }
    $mailchkSmtp->ClearAllRecipients();
    $mailchkSmtp->ClearReplyTos();  
}

if(isset($_POST['secarray']) && !empty($_POST['secarray'])){
    foreach($_POST['secarray'] as $secarrayVal){

        if(!empty($_POST['secdmsmtp'.$secarrayVal]) || !empty($_POST['secsecdmuser'.$secarrayVal]) || !empty($_POST['secdmpwd'.$secarrayVal]))
        {
            $mailchkSmtp = new PHPMailer;
            $mailchkSmtp->IsSMTP();                                      // Set mailer to use SMTP
            $mailchkSmtp->Host = $_POST['secdmsmtp'.$secarrayVal];                 // Specify main and backup server
            $mailchkSmtp->Port = $_POST['secdmport'.$secarrayVal];                                    // Set the SMTP port
            $mailchkSmtp->SMTPAuth = true;                               // Enable SMTP authentication
            $mailchkSmtp->Username = $_POST['secsecdmuser'.$secarrayVal];                // SMTP username
            $mailchkSmtp->Password = $_POST['secdmpwd'.$secarrayVal];                  // SMTP password
            $mailchkSmtp->SMTPSecure = $_POST['secdmsecure'.$secarrayVal];                            // Enable encryption, 'ssl' also accepted
            $mailchkSmtp->From = $_POST['secsecdmuser'.$secarrayVal];        
            $mailchkSmtp->AddAddress('tickletraincron@gmail.com', '');  // Add a recipient//shine@123        
            $mailchkSmtp->IsHTML(true);                                  // Set email format to HTML
            $mailchkSmtp->Subject = 'Test SMTP Connection';
            $mailchkSmtp->Body    = 'Test SMTP Connection';
            $mailchkSmtp->AltBody = 'Test SMTP Connection';
            try {
                $SendMail22 = $mailchkSmtp->Send();
            } catch (Exception $e) {

            }
            if ($SendMail22) {                
                mysqli_query($db->conn,"update secondaryEmail set DMSmtpOff='0' where id='" . $secarrayVal . "'");
            }
            $mailchkSmtp->ClearAllRecipients();
            $mailchkSmtp->ClearReplyTos();  
        }

    }        
}


?>
