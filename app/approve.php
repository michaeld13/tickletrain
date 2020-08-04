<?
$mode=intval($GLOBALS['mode']);
?>
<script>
<?if ($mode==1){?>
function ApproveConfirm(url,subval)
{
       var cancel = {text: 'Cancel', click: function() {
               // window.location.href = '<?=Url_Create("home")?>';
			   window.close();
            }};
        var unpause = {text: 'Send', click: function() {
             //  window.location.href = url+"&Approve=Y";
			   
			    $.ajax({
					url: 'https://client.tickletrain.com/'+url+"&Approve=Y",
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
    ApproveConfirm('<?=Url_Create("approve",$_SERVER['QUERY_STRING'])?>','');
    <?}?>
    <?if ($mode==4){?>
    mralert("Invalid action",'This Tickle has already been unpaused','<?=Url_Create("home")?>');
     $(".ui-dialog:first" ).css("float", "none");
        $(".ui-widget-header :first" ).css("font-size", "18px");
        $(".ui-dialog-titlebar").parent().css("left",'50%');
        $("#malert" ).css( "min-height", "" );
        $(".ui-dialog:first" ).css( "width", "340px" );
        $(".ui-dialog:first" ).css( "margin-left", "-170px" );
    <?}?>
    <?if ($mode==7){?>
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
        $(".ui-dialog:first" ).css("width", "320px");
        $(".ui-dialog:first" ).css("margin-left", "-160px" );
    <?}?>

    <?if ($mode==2){?>
    mralert("Send it! Confirmation",'All Tickles in this campaign have been unpaused','<?=Url_Create("home")?>');
    <?}?>
    <?if ($mode==3){?>
    mralert("Send it! Confirmation",'This Tickle has been unpaused.','<?=Url_Create("home")?>');
    <?}?>
    <?if ($mode==8){?>
    mralert("Invalid action",'Your Tickle settings have changed. This Tickle does not have the Approve option enabled at the moment. Please use the Dashboard to manage this Tickle.','<?=Url_Create("home")?>');
    <?}?>
});
</script>
