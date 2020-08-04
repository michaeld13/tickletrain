<?php
// set facebook access_token to $_SESSION["access_token"] when facebook's logged and application's approved

session_start();
include ("fb_const.php");
$facebooktime = 3600; // facebook access_token lifetime
$selfurl = urlencode('http://' . $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"]);
$d = getdate();
if ($_SESSION["access_token_time"] && $_SESSION['access_token']) {
    if ($d[0] - $_SESSION["access_token_time"] < $facebooktime) {
        /*?>
    <script>window.parent.document.getElementById('facebookerror').innerHTML = '';</script>
    <div id="facebookflag" ok="yes">yes</div>
    <?*/
        //header('Location: ../home/');
        echo "valid";
        exit;
    }
}
$code = $_GET["code"];
//echo $code;
if ($code) // get token through code
{
    $url = "https://graph.facebook.com/oauth/access_token?client_id=$facebookaccount->app&redirect_uri=$selfurl&client_secret=$facebookaccount->secret&code=$code";
    $access_token = callFb($url,true);
    $access_token = substr($access_token, strpos($access_token, "=") + 1, strlen($access_token));
    $_SESSION["access_token"] = $access_token;
    $_SESSION["access_token_time"] = $d[0];
    echo $_SESSION["access_token"];
    echo "yes";
    /*?>
<script>window.parent.location.reload();</script>
<div id="facebookflag" ok="yes">yes</div>
<?*/
    //header('Location: ../home/');
    //        echo "token=$access_token";
    exit;
} else // get initial code through appID
{
    $url = "https://graph.facebook.com/oauth/authorize?client_id=$facebookaccount->app&redirect_uri=$selfurl&scope=offline_access,read_stream";
    header('Location: ' . $url);
}
?>
