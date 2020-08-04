<?php
$upage=$_GET['u'];
$act = @trim($_GET['act']);
if ($act!="" && $upage!="activation"){
    $act = unprotect(rawurldecode($act));
    $action = explode("-", $act);
    if (count($action)==2){
        $check_login = loginByTickle($action[0]);
        if ($check_login!=0){
            redirect('login');
        }
    }
}
//&&$upage!="approve"&&$upage!="unsubscribe"&&$upage!="previewmail"&&$upage!="edittask"

if($_SESSION['TickleID']<=0&&$upage!="Register"&&$upage!="registermessage"&&$upage!="login"&&$upage!="activation"&&$upage!="test"&&$upage!="useractivitylist"&&$upage!="addcomments")
{
$upage="login";
}

include_once('class/class.FormValidation.php');

$app_function=$system['App_Path'].$upage."_function.php";
$FunctionVar=func($app_function);
$content['FunctionVar']=$FunctionVar;
$app_page=$system['App_Path'].$upage.".php";
$content['Content']=Block_Create($app_page,$content);
$template_file;
include_once($template_file);
?>