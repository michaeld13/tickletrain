<?php
header('Access-Control-Allow-Origin: *');

include_once("../includes/data.php");
include_once("../includes/function/func.php");
ini_set('display_errors', 1);
session_start();
$facebookaccount->app = '205621149596063'; // facebook appID
$facebookaccount->secret = '571d44c161898e7c009da554fc551be9';  // facebook appID secret
/*End Get Fb appId And appId Secret */

$selfurl = urlencode('http://' . $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"]);
$d = getdate();
//echo $_SESSION["access_token"].'</br>';
//$_SESSION["access_token"] = "CAAC7AueUeZA8BAKrYepX91ZB5BCkybdiw7WFZAmFNlnSlu1JY1gGisHVSmSCxH92un662OZCg6RmJJSPoQfK3RvbIObBcXALRYIEDFRZCZCdE7kanh3ZCbPLHelNIJZAZCu9KOpy2JjDTdLRI405oaGZAvVLxpJx5484sZD&expires=5157610";
//$email = "amitkaushik53@gmail.com";
//session_destroy();
//$_SESSION["access_token"] = "CAAGllkm2QUkBAD5t8DeltAfbFgJwJczhVU9JvwHW2eZBaXLZBZBRLt55yHBWTP5r5ZCnVSCEJl2FV1pqEARw60Nx3dhd81RtiSbKN1AKOpUXHlfuSZAHJjpA7GqWaq4kgQhdfPjfDd31KZAnxGDBapLMHv75BDabhygjadZAmI9mQZDZD&expires=1373979664";



if(isset($_COOKIE["access_token"]) && $_COOKIE["access_token"]!="" && isset($_POST['GetFbTokenForTTExtensions'])){
die($_COOKIE["access_token"]);
}else{
$code = $_GET["code"];
if ($code) // get token through code
{
    
    $url = "https://graph.facebook.com/oauth/access_token?client_id=$facebookaccount->app&redirect_uri=$selfurl&client_secret=$facebookaccount->secret&code=$code";
    $access_token = callFb123($url);
    $access_token = substr($access_token, strpos($access_token, "=") + 1, strlen($access_token));
    die($access_token);
    $_SESSION["access_token"] = $access_token;
    
    $_SESSION["access_token_time"] = $d[0];
    setcookie("access_token",$access_token,time()+3600*24*30,"/");
   header('Location: https://mail.google.com');  
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
