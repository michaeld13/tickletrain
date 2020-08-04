<?

//echo "<pre>";
//print_r($GLOBALS);
//echo "</pre>";
//die();

$mode=intval($GLOBALS['mode']);
?>
<?if($mode==1){?>
<script language="javascript">
$(document).ready(function(){
        mdialog1('<?=addslashes($GLOBALS['subject'])?>',unescape('<?=addslashes($GLOBALS['hcontent'])?>'),false,{'height':500, 'width':800});
});
</script>
<?}?>
<?if ($mode==6){?>
<script language="javascript">
$(document).ready(function(){
        mdialog1('Subject Email','This campaign no longer exists');
});
</script>
<?}?>        
