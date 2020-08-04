<?
$mode=intval($GLOBALS['mode']);
?>
<script>
<?if ($mode==1){?>
function UnPauseConfirm(url) {
        //alert(url);
        //alert(<?=Url_Create("home")?>);
        var cancel = {text: 'Cancel', click: function() {
              //  window.location.href = '<?=Url_Create("home")?>';
			  window.close();
            }};
        var unpause = {text: 'Send', click: function() {
				 $.ajax({
					url: 'https://client.tickletrain.com/'+url,
				   // dataType: 'json',
					method: 'GET',
				}).done(function (response) {
					console.log('sss'+response); 
					if(response == 3){
						alert("Send it! Confirmation,This Tickle has been unpaused");
						window.close();
					}
				}).fail(function(){
					console.log('error');
				});
			
             //  window.location.href = url;
            }};
        var message = "";
        mdialog("Send this Tickle?", message, [unpause, cancel]);
      $(".ui-dialog:first" ).css("float", "none");
        $(".ui-widget-header :first" ).css("font-size", "18px");
        $(".ui-dialog-titlebar").parent().css("left",'50%');
        $("#malert" ).css( "min-height", "" );
        $(".ui-dialog:first" ).css( "width", "210px" );
        $(".ui-dialog:first" ).css( "margin-left", "-105px" );
        return false;
    }
<?}?>
$(document).ready(function(){
    <?if ($mode==1){?>
    UnPauseConfirm('<?=Url_Create("unpause",$_SERVER['QUERY_STRING'])?>&UnPauseAll=Y','');
    <?}
    /*if ($mode==2 || $mode==3){
        redirect('home');
    }*/if ($mode==4){?>
        mralert("Invalid action","We're sorry. This campaign is no longer available.",'<?=Url_Create("home")?>');
        $(".ui-dialog:first" ).css("float", "none");
        $(".ui-widget-header :first" ).css("font-size", "18px");
        $(".ui-dialog-titlebar").parent().css("left",'50%');
        $("#malert" ).css( "min-height", "" );
        $(".ui-dialog:first" ).css( "width", "320px" );
        $(".ui-dialog:first" ).css( "margin-left", "-160px" );
    <?}?>
    <?if ($mode==5){?>
        mralert("Invalid action","We're sorry. This campaign is no longer available.",'<?=Url_Create("home")?>');
        $(".ui-dialog:first" ).css("float", "none");
        $(".ui-widget-header :first" ).css("font-size", "18px");
        $(".ui-dialog-titlebar").parent().css("left",'50%');
        $("#malert" ).css( "min-height", "" );
        $(".ui-dialog:first" ).css( "width", "320px" );
        $(".ui-dialog:first" ).css( "margin-left", "-160px" );
    <?}?>
    <?if ($mode==6){?>
        mralert("Invalid action","We're sorry. This campaign is no longer available.",'<?=Url_Create("home")?>');
        $(".ui-dialog:first" ).css("float", "none");
        $(".ui-widget-header :first" ).css("font-size", "18px");
        $(".ui-dialog-titlebar").parent().css("left",'50%');
        $("#malert" ).css( "min-height", "" );
        $(".ui-dialog:first" ).css( "width", "320px" );
        $(".ui-dialog:first" ).css( "margin-left", "-160px" );
    <?}?>

    <?if ($mode==2){?>
    mralert("Send it! Confirmation",'All Tickles have been paused','<?=Url_Create("home")?>');
    <?}?>
    <?if ($mode==3){?>
    mralert("Send it! Confirmation",'This Tickle has been unpaused.','<?=Url_Create("home")?>');
    <?}?>
});
</script>
