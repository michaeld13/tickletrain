
<div class="main_holder login_area">
	<h1>Login</h1>
	<div class="form">
		<div class="holder">
			<div class="frame">
<?
global $Username;

?>
<form action="<?=Url_Create('login');?>" method="post" name="Login"  id="Login">
    <fieldset>
        <div class="row">
            <?php echo $Form->ErrorString; ?>
                  
            <?if(@intval($_REQUEST['activation'])){?>
            Thank you for registering with TickleTrain. You may now login with your user name and password.
            <?}?>
        </div>
					<div class="row">
						<label for="Username">Username or E-mail <span class="req">*</span></label>
						<span class="input_text"><input type="text" name="Username" id="Username" value="<?=$Username?>"/></span>
					</div>
					<div class="row">
						<label for="Password">Password <span class="req">*</span></label>
						<span class="input_text"><input type="password" name="Password" id="Password"  value="" /></span>
					</div>
					<div class="submit_holder">
						<input type="submit" value="Login" class="btn_login" name="submit" id="Login" />
					</div>
				</fieldset>
</form>
</div></div></div></div>
<script language="javascript">


    function SendActivation(){
        var cancel = {text:'Cancel', click: function() {$(this).dialog('close')}};
        var send = {text:'Send', click: function() {  
            $(this).dialog('close');
            
            $.get("<?=Url_Create('Register')?>", {'act':'activationemail', 'email': $("#actemail").val()} , function(data){
                if (data=="activation"){
                    var msg='<center>You TickleTrain activation email has been resent to '+$("#actemail").val()+'! <br/><br/>Please, also check with your junk and or spam folders as the email may have been filtered into those folders depending on your spam settings. If you still do not receive the email kindly check with your email provider for more details.</center>';
                    malert(msg,{width:500});
                }
                if (data=="restore"){
                    var msg='<center>Your password has been resent to your email address.<br/><br/><nobr>Make sure to check SPAM folder and add noreply@tickletrain.com to a safe mail list.</nobr></center>';
                    mcalert('Confirmation',msg,{width:550});
                }
                if (data=="false"){
                    malert("I'm Sorry<br/>There is no account registered with that email address.");
                }
            });
            }
        };
         //if (data=="activation"){
         var message = "<input type='text' name='actemail' placeholder='Enter your email address' id='actemail'/>";
        mdialog("Enter Email",message,[send,cancel]);
        // }
        // else{
       // var message = "Enter your email address: <input type='text' name='actemail' id='actemail'/>";
       // mdialog("Restore Password",message,[send,cancel]);
       // }
    }

        <?php  
         if(($_GET['act'] == 'forget-pwd')) { ?>
            setTimeout(function(){SendActivation();},500);
        <?php } ?>
</script>
