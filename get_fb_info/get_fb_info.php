<?php
header('Access-Control-Allow-Origin: *');

include_once("../includes/data.php");
include_once("../includes/function/func.php");
session_start();
$facebookaccount->app = '205621149596063'; // facebook appID
$facebookaccount->secret = '571d44c161898e7c009da554fc551be9';  // facebook appID secret
/*End Get Fb appId And appId Secret */

$selfurl = urlencode('http://' . $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"]);
$d = getdate();
$email = "amitkaushik53@gmail.com";
//echo $_SESSION["access_token"].'</br>';
//$_SESSION["access_token"] = "CAAC7AueUeZA8BAKrYepX91ZB5BCkybdiw7WFZAmFNlnSlu1JY1gGisHVSmSCxH92un662OZCg6RmJJSPoQfK3RvbIObBcXALRYIEDFRZCZCdE7kanh3ZCbPLHelNIJZAZCu9KOpy2JjDTdLRI405oaGZAvVLxpJx5484sZD&expires=5157610";
//$email = "amitkaushik53@gmail.com";
//session_destroy();
$_SESSION["access_token"] = "CAAGllkm2QUkBABVZBYSfg8633BSUc6NqtpJfFJJfjoXYr42oBbLeFH3IrJCG1N8L2oaFjlZAw9LiG3H0RTeZCzvSArODZCVyLi8BjC7uTSoScqeGpdx017ryxKCcIp5ywYJgtcZBDLRxvbTkSFxYP1qqT4KvxiFIV5srwGf70ZBfR4QbcpNbRsxhY8kdo31GIZD";
if(isset($_SESSION["access_token"]) && $_SESSION["access_token"]!=""){
if ($email != '') {
    $fb_url = 'https://graph.facebook.com/?access_token=' . $_SESSION["access_token"] . '&q=' . urlencode($email) . '&type=user';
    $o = json_decode(callFb123($fb_url), true);
    echo "<pre>";
    print_r($o);
    die('1111');
    $fb_userid = $o['']['data'][0]['id'];
    if (is_numeric($fb_userid)) {
        $url = "https://graph.facebook.com/" . $fb_userid . "?access_token=" . $_SESSION["access_token"] . "&fields=first_name,last_name,picture,gender,locale,timezone";
        $ret_json = callFb123($url);
        echo '<pre>';
        print_r($ret_json);
        echo '</pre>';
        $user = json_decode($ret_json, true);
        //die(json_encode($user));
       // header('Location: https://mail.google.com');
    }else{
       // header('Location: https://mail.google.com');
    }
}
}else{
//die('cbcbvc');    
$code = $_GET["code"];
if ($code) // get token through code
{
    
    $url = "https://graph.facebook.com/oauth/access_token?client_id=$facebookaccount->app&redirect_uri=$selfurl&client_secret=$facebookaccount->secret&code=$code";
    $access_token = callFb123($url);
    $access_token = substr($access_token, strpos($access_token, "=") + 1, strlen($access_token));
    $_SESSION["access_token"] = $access_token;
    $_SESSION["access_token_time"] = $d[0];
     $fb_url = 'https://graph.facebook.com/?access_token=' . $_SESSION["access_token"] . '&q=' . urlencode($email) . '&type=user';
    $o = json_decode(callFb123($fb_url), true);
    echo '<pre>';
    print_r($o);
    echo '</pre>';
    die();
    setcookie("access_token",$access_token,time()+3600*24*30,"/");
   
    //header('Location: https://mail.google.com');  
   } else // get initial code through appID
{
    $url = "https://graph.facebook.com/oauth/authorize?client_id=$facebookaccount->app&redirect_uri=$selfurl&scope=offline_access,read_stream";
    header('Location: ' . $url);
}
}

function callFb123($url) {
     $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true
    ));

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
?>