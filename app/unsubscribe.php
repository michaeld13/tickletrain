<?
$mode=intval($GLOBALS['mode']);
?>
<script>
<?if ($mode==1){?>
function DeleteConfirm(url, subval) {
        //alert(url);
        var cancel = {text: 'Cancel', click: function() {
                //window.location.href = '<?=Url_Create("home")?>';
				window.close();
            }};
        var deleteone = {text: 'Delete', click: function() {
               //  window.location.href = url + "&DeleteAll=Y";
			   
			   $.ajax({
					url: 'https://client.tickletrain.com/'+url+ "&DeleteAll=Y",
				   // dataType: 'json',
					method: 'GET',
				}).done(function (response) {
					if(response == 2){
						alert("The campaign is deleted successfully");
						window.close();
					}
				}).fail(function(){
					console.log('error');
				});
				
            }};
        var message = "";
        mdialog("Delete this Campaign?", message, [deleteone,cancel]);
        $(".ui-dialog:first" ).css("float", "none");
        $(".ui-widget-header :first" ).css("font-size", "18px");
        $(".ui-dialog-titlebar").parent().css("left",'50%');
        $("#malert" ).css( "min-height", "" );
        $(".ui-dialog:first" ).css( "width", "250px" );
        $(".ui-dialog:first" ).css( "margin-left", "-125px" );
        return false;
    }
<?}?>
$(document).ready(function(){
    <?if ($mode==1){?>
    DeleteConfirm('<?=Url_Create("approve",$_SERVER['QUERY_STRING'])?>','');
    <?}
    if ($mode==4){?>
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
    window.location.href = '<?=Url_Create("home")?>';            
    <?}?>
});
</script>
