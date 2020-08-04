<?
$server_settings = array(''=>'SMTP Server', 'google'=>'GMail','yahoo'=>'Yahoo','exchange'=>'Microsoft Exchange', 'hotmail'=>'Hotmail');
$GLOBALS['server_settings_domains']=array('gmail'=>'google','yahoo'=>'yahoo','hotmail'=>'hotmail','msn'=>'hotmail','ymail'=>'yahoo', 'rocketmail'=>'yahoo');

$GLOBALS['server_settings_params'] = array(
	'google'=>array(
		'isdefault'=>true,
		'server'=>'smtp.gmail.com',
		'port'=>'465',
		'encryption'=>'ssl',
		'labels'=>array(
			'username_title'=>'Gmail Email Address',
			'password_title'=>'Gmail Password'
		)
	),
	'yahoo'=>array(
		'isdefault'=>true, 
		'server'=>'smtp.mail.yahoo.com',
		'port'=>'465',
		'encryption'=>'ssl',
		'labels'=>array(
	 		'server_title'=>'Yahoo! SMTP Mail Server',
			'username_title'=>'Yahoo! Mail ID',
			'password_title'=>'Yahoo! Mail Password',
			'port_title'=>'Server Port ',
			'encryption_title'=>'Enable SMTP over SSL or TLS?',
			'auth_title'=>'Use SMTP Authentication?',
		),
		'help_text' =>'<b>Enter your Yahoo SMTP email server settings below</b><br>Yahoo requires a "two-step verification" process to work with apps such as TickleTrain. And it\'s easy to do! We have prefilled Yahoo\'s email settings for you. We just need a special password now. To get this, login to your Yahoo Mail account and click on <i>Account Info </i> and then <i>Account Security</i> link. You\'ll see the "Two Step Verification" area there. Yahoo will generate a special password for you that you will enter into TickleTrain\'s settings below. After doing so, use the "Send Test Email" button below to make sure your new settings work and click "Update" to save your new settings. Here is a <a href="https://help.yahoo.com/kb/SLN15241.html" target="_blank"> link </a> for more info from Yahoo!',
	),
	'exchange'=>array(
		'server'=>'',
		'port'=>'465',
		'encryption'=>'ssl',
		'labels'=>array(
			'server_title'=>'Exchange Server',
			'username_title'=>'Exchange Username',
			'password_title'=>'Exchange Password',
			'port_title'=>'Exchange Server Port ',
			'encryption_title'=>'Enable SMTP over SSL or TLS?',
			'auth_title'=>'Use SMTP Authentication?',
			'swarning'=>''
		)
	),
	'hotmail'=>array(
		'isdefault'=>true, 
		'server'=>'smtp.live.com',
		'port'=>'25',
		'encryption'=>'tls',
		'labels'=>array(
			'username_title'=>'Hotmail Email Address',
			'password_title'=>'Hotmail Password'
		)
	),
	// ''=>array(
	// 	'server'=>'',
	// 	'port'=>'25',
	// 	'labels'=>array(
	// 		'server_title'=>'SMTP Mail Server',
	// 		'port_title'=>'SMTP Port',
	// 		'username_title'=>'Username',
	// 		'password_title'=>'Password',
	// 		'encryption_title'=>'Enable SMTP over SSL or TLS?',
	// 		'auth_title'=>'Use SMTP Authentication?',
	// 		'swarning'=>''
	// 	)
	// ),
	'other'=>array(
		'server'=>'',
		'port'=>'25',
		'encryption'=>'ssl',
		'labels'=>array(
	 		'server_title'=>'SMTP Mail Server',
	 		'from_email'=>'From Email',
			'username_title'=>'Mail ID',
			'password_title'=>'Mail Password',
			'port_title'=>'Server Port ',
			'encryption_title'=>'Enable SMTP over SSL or TLS?',
			'auth_title'=>'Use SMTP Authentication?',
		),
		'help_text' =>'<b>Enter your SMTP email server settings below</b><br> If you are unsure of your outgoing email settings, try copying the settings on your local email client such as Outlook. You may also contact your email provider for the proper settings.',
	)
);

function isVisible($server,$label){
    $labels = $GLOBALS['server_settings_params'][$server]['labels'];
    return isset($labels[$label]);
}
function getLabel($server,$label){
    $labels = $GLOBALS['server_settings_params'][$server]['labels'];
    return @trim($labels[$label]);
}
?>
