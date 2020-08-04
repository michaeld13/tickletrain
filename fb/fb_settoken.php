<?php
// set facebook access_token to $_SESSION["access_token"] when user click login facebook

session_start();
include ("fb_const.php");
$curl = @intval($_GET['curl']);

$facebooktime = 3600; // facebook access_token lifetime
if(isset($_GET['fromechrome_extension'])){
$selfurl = urlencode('http://' . $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"].'?fromechrome_extension=yes');
}else{
$selfurl = urlencode('http://' . $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"]);    
}
//$selfurl = urlencode((($_SERVER['HTTPS']=='on')?"https":"http")."://".$_SERVER['SERVER_NAME']."/fb/settoken/");
//echo GetRootFolder();exit;
$d = getdate();
if ($_SESSION["access_token_time"] && $_SESSION['access_token']) {
    
    if ((($d[0] - $_SESSION["access_token_time"]) < $facebooktime) && !$curl) {
        header('Location: ../home/');
    }
    exit;
}

$code = $_GET["code"];
//echo $code;
if ($code) // get token through code
{
    $url = "https://graph.facebook.com/oauth/access_token?client_id=$facebookaccount->app&redirect_uri=$selfurl&client_secret=$facebookaccount->secret&code=$code";
    $access_token = callFb($url);
    $access_token = substr($access_token, strpos($access_token, "=") + 1, strlen($access_token));
    $_SESSION["access_token"] = $access_token;
    $_SESSION["access_token_time"] = $d[0];
    //echo $_SESSION["access_token"];
    //die();
    setcookie("access_token",$access_token,time()+3600*24*30,"/");
    if (!$curl){
       // if(isset($_GET['fromechrome_extension'])){
       // echo "<script>window.close();</script>";    
       // }else{
        header('Location: ../home/');    
       // }
        
    }
    //        echo "token=$access_token";
    exit;
} else // get initial code through appID
{
    
    $url = "https://graph.facebook.com/oauth/authorize?client_id=$facebookaccount->app&redirect_uri=$selfurl&scope=offline_access,read_stream";
    header('Location: ' . $url);
    
}
?>
