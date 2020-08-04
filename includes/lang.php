<?php
$system['root']=str_replace("includes","",dirname(__FILE__));
$system['templates']=$system['root']."tpl/";
$system['folder']=str_replace("index.php","",$_SERVER['PHP_SELF']);

$system['default_theme_template']=$system['templates']."index.php";
$theme_path=str_replace($system['root'],"",$system['default_theme']);
$template_file=$system['default_theme_template'];
$content['Theme_Path']=$theme_path;
$system['App_Path']=$system['root']."app/";

$content['Title']="Tickle Train";
$content['Head']='';
$content['Header']="<h1>Tickle Train</h1>";

if($_SESSION['TickleID']>0)
{
$content['Head'].='
<script type="text/javascript" src="/'.ROOT_FOLDER.'js/member.js"></script>
';
}
?>
