<?
$mode=intval($GLOBALS['mode']);
?>
<script>
<?if ($mode==1){?>
function PauseConfirm(url) {

        var cancel = {text: 'Cancel', click: function() {
                // window.location.href = '<?=Url_Create("home")?>';
				window.close();
            }};
        var pause = {text: 'Pause', click: function() {
               // window.location.href = url + "&PauseAll=Y";
			   
			    $.ajax({
					url: 'https://client.tickletrain.com/'+url+ "&PauseAll=Y",
				   // dataType: 'json',
					method: 'GET',
				}).done(function (response) {
					console.log('sss'+response); 
					if(response == 3){
						alert("Send it! Confirmation,This Tickles have been paused");
						window.close();
					}
					if(response == 2){
						alert("Send it! Confirmation,All Tickles have been paused");
						window.close();
					}
				}).fail(function(){
					console.log('error');
				});
				
            }};
        //var pauseAll = {text:'Pause All', click: function() {window.location.href = url+"&PauseAll=Y";}};
        var message = "";
        mdialog("Pause this Tickle?", message, [pause, cancel]);
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
    PauseConfirm('<?=Url_Create("pause",$_SERVER['QUERY_STRING'])?>','');
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
    <?if ($mode==4){?>
    mralert("Invalid action","We're sorry. This campaign is no longer available.",'<?=Url_Create("home")?>');
        $(".ui-dialog:first" ).css("float", "none");
        $(".ui-widget-header :first" ).css("font-size", "18px");
        $(".ui-dialog-titlebar").parent().css("left",'50%');
        $("#malert" ).css( "min-height", "" );
        $(".ui-dialog:first" ).css( "width", "320px" );
        $(".ui-dialog:first" ).css( "margin-left", "-160px" );
    <?}?>

    <?if ($mode==2){?>
    window.location.href = '<?=Url_Create("home")?>';            
   // mralert("Pause confirmation",'All Tickles have been paused.','<?=Url_Create("home")?>');
    <?}?>
    <?if ($mode==3){?>
    window.location.href = '<?=Url_Create("home")?>';          
   // mralert("Pause confirmation",'This Tickle has been paused.','<?=Url_Create("home")?>');
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
});
</script>