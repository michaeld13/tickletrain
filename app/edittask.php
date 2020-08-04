			
<?
$mode=intval($GLOBALS['mode']);
if($mode==1){?>
<script type="text/javascript">
$(document).ready(function(){
        mdialog('Adjust send time','<?=addslashes($GLOBALS['hcontent'])?>',false,{'height':150, 'width':'300px'});
});
<?=$GLOBALS['hheader'];?>
</script>
<?}?>
<?if ($mode==6){?>
<script language="javascript">
$(document).ready(function(){
        mdialog('Adjust send time','This Tickle no longer exists');
});
</script>
<?}?>