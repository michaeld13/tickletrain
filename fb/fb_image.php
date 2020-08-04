<?php
// return userinfo from facebook 
// requred: actual login on facebook (login check in fb_gettoken.php)
// input $_GET['email']
// output facebook icon or info object
session_start();
include("../includes/data.php");
include ("fb_const.php");
//include ("../includes/function/func.php");
define('ROOT_FOLDER', "");
ini_set('display_errors', 0);
//$debug=true;
if (!$ret = $_GET['ret']) $ret = 'image'; // define returning result
//unset($_SESSION['access_token']);
//echo $_SESSION['access_token'];
//die();
if (isset($_SESSION['access_token'])) {
    $email = trim($_GET['email']);
    if(isset($_GET['cid'])){ $cid = $_GET['cid']; }else{ $cid = ''; }    

    if ($email != '') {

        if (!$stored_fb = $db->select_row('fb_tickleuser', '', 'Where email="' . $email . '"')) {
            $fb_url = 'https://graph.facebook.com/?access_token=' . $_SESSION["access_token"] . '&q=' . urlencode($email) . '&type=user';
            $o = json_decode(callFb($fb_url), true);
            if(isset($o['']['data'][0]['id'])){ $fb_userid = $o['']['data'][0]['id']; }else{ $fb_userid = ''; }
            if (is_numeric($fb_userid)) {
                $db->insert('fb_tickleuser', array('email' => $email, 'fbid' => $fb_userid));
            }
        } else {
            $fb_userid = $stored_fb['fbid'];
        }

        if (is_numeric($fb_userid)) {
            $facebookcache = json_decode($_SESSION["facebookcache"], true);
            if (!isset($facebookcache[$fb_userid])) {
                $url = "https://graph.facebook.com/" . $fb_userid . "?access_token=" . $_SESSION["access_token"] . "&fields=first_name,last_name,picture,gender,locale,timezone";
                $ret_json = callFb($url);
                $user = json_decode($ret_json, true);
                $facebookcache[$fb_userid] = $user;
                $_SESSION["facebookcache"] = json_encode($facebookcache);
            } else {
                $user = $facebookcache[$fb_userid];
            }
            $res = "{fbid:'$fb_userid'";
            foreach ($user as $k => $v)
                $res .= ",$k:'" . htmlspecialchars($v) . "'";
            $res .= "}";
            $facebookcache = json_decode($_SESSION["facebookcache"], true);

            switch ($ret) {
                case "object":
                    echo $res;
                    break;
                case "info":
                    ?>
                    <table cellpadding="10">
                        <tr>
                            <td><?=($user['picture']) ? "<img src='" . $user['picture'] . "'>" : ""?></td>
                            <td><?=($user['first_name']) ? $user['first_name'] : ""?> <?=($user['last_name']) ? $user['last_name'] : ""?>
                                <br>
                                <?=($user['gender']) ? $user['gender'] : ""?><?=($user['gender'] && $user['locale']) ? ", " : ""?><?=($user['locale']) ? preg_replace('/^.*\_/', '', $user['locale']) : ""?>
                                <br>
                                <?=($user['id']) ? "<a href='http://facebook.com/" . $user['id'] . "' target='_new' class='facebooklink'>View Facebook Profile</a><br><br>" : ""?>
                            </td>
                        </tr>
                    </table> <?
                    break;
                case "infomy":
                    ?>
                    <table cellpadding="10">
                        <tr>
                            <td><?=($user['picture']) ? "<img src='" . $user['picture'] . "'>" : ""?></td>
                            <td><?=($user['first_name']) ? $user['first_name'] : ""?> <?=($user['last_name']) ? $user['last_name'] : ""?>
                                <br>
                                <?=($user['gender']) ? $user['gender'] : ""?><?=($user['gender'] && $user['locale']) ? ", " : ""?><?=($user['locale']) ? preg_replace('/^.*\_/', '', $user['locale']) : ""?>
                                <br>
                                <?=($user['id']) ? "<a href='http://facebook.com/" . $user['id'] . "' target='_new' class='facebooklink'>View Facebook Profile</a><br><br>" : ""?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><a href="javascript: void(0);" onclick="updatefacebook();"
                                               class="facebooklink">Update using Facebook profile</a></td>
                        </tr>
                    </table>
                    <script>
                        function updatefacebook() {
                            $('#FirstName').val('<?=htmlspecialchars($user["first_name"])?>');
                            $('#LastName').val('<?=htmlspecialchars($user["last_name"])?>');
                        }
                    </script><?
                    break;
                case "infocmupd":
                    ?>
                    <strong class="fb_profile-row"><img src="/<?=GetRootFolder()?>images/ico-fb.gif" width="14"
                                                        height="14" alt=""/> Facebook Profile</strong>
                    <? if ($user['picture']) { ?>
                    <img src="<?=$user['picture']['data']['url']?>" width="50" height="50" alt="" class="ava"/>
                    <? } ?>
                    <? //=($user['picture'])?"<img src='".$user['picture']."'>":""?>
                    <div class="text">
                        <strong
                            class="name"><?=($user['first_name']) ? $user['first_name'] : ""?> <?=($user['last_name']) ? $user['last_name'] : ""?></strong>

                        <p><?=($user['gender']) ? $user['gender'] : ""?><?=($user['gender'] && $user['locale']) ? ", " : ""?><?=($user['locale']) ? preg_replace('/^.*\_/', '', $user['locale']) : ""?></p>

                        <p><?=($user['id']) ? "<a href='http://facebook.com/" . $user['id'] . "' target='_new'>View Facebook Profile</a>" : ""?></p>
                    </div>
                    <a href="#" onclick="updatefacebook();return false" class="btn_blue"><span>Use Facebook profile for this contact</span></a>
                    <script>
                        function updatefacebook() {
                            $('#FirstName').val('<?=htmlspecialchars($user["first_name"])?>');
                            $('#LastName').val('<?=htmlspecialchars($user["last_name"])?>');
                        }
                    </script>
                    <?break;
                case "facebook":
                    ?>
                    <a href="http://facebook.com/<?=$user['id']?>" target="_blank"><img
                        src="/<?=ROOT_FOLDER?>images/ico_fb.png"></a>
                    <?
                    break;
                default:
                    ?><a href="#" onclick="return FacebookFillContact('<?=$cid?>', <?=$res?>)"><img
                        src="/<?=ROOT_FOLDER?>images/ico_fb.png"></a>
                    <?break;
            }
        } else {
            if (isset($debug)) echo 'error=nouser';
        }
    } else {
        if (isset($debug)) echo 'error=noemail';
    }
} else {
    if (isset($debug)) echo 'error=nosession';

}
if(isset($_GET['chromeextension'])){
    echo "<script>window.close();</script>";
}
?>
