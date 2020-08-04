<?php
use PHPMailer\PHPMailer\PHPMailer;
$case = 1;
$flag = false;
define('HOME_FOLDER', GetHomeDir() . "/");
define('SERVER_NAME', "client.tickletrain.com");

		if(isset($_GET['taskk']) == 'restore'){
			
			$act = unprotect(rawurldecode($_GET['cptsk']));
			$is_owner =  unprotect(rawurldecode($_GET['els']));
			$action = explode("-", $act);
			if (count($action) == 2){
		    	global $db;

				$TickleID = $action[0];
				$TaskID = $action[1];
				$protected = rawurldecode(protect($TickleID . "-" . $TaskID));
		   		$els = rawurldecode(protect($is_owner));

				$query = "SELECT task.*,user_mail.toaddress,user_mail.Subject,user_mail.Date,user_mail.Message,user_mail.MessageHtml,tickleuser.FirstName, tickleuser.LastName from task inner join user_mail ON (task.MailID=user_mail.MailID) inner join tickleuser ON (task.TickleID=tickleuser.TickleID) where TaskID=".$TaskID;
				$result = mysqli_query($db->conn,$query);
				if(mysqli_num_rows($result) > 0){

					$task =  mysqli_fetch_assoc($result);
					$MailID =  $task['MailID'];
					//echo "update task set Status='Y' where Status='D' and  MailID='" . $MailID . "'";
					mysqli_query($db->conn,"update user_mail set Status='Y' where MailID='" . $MailID . "'");
			        mysqli_query($db->conn,"update task set Status='Y' where Status='D' and  MailID='" . $MailID . "'");
			        $case = 3;
					$flag = true;
					echo $case; exit;
				}
			}
		}
		if(isset($_GET['cptsk']) && isset($_GET['undo'])){
			$act = unprotect(rawurldecode($_GET['cptsk']));
			$is_owner =  unprotect(rawurldecode($_GET['els']));
			$action = explode("-", $act);
			if (count($action) == 2){
		    	global $db;

				$TickleID = $action[0];
				$TaskID = $action[1];
				$protected = rawurldecode(protect($TickleID . "-" . $TaskID));
		   		$els = rawurldecode(protect($is_owner));

				$query = "SELECT task.*,user_mail.toaddress,user_mail.Subject,user_mail.Date,user_mail.Message,user_mail.MessageHtml,tickleuser.FirstName, tickleuser.LastName from task inner join user_mail ON (task.MailID=user_mail.MailID) inner join tickleuser ON (task.TickleID=tickleuser.TickleID) where TaskID=".$TaskID;
				$result = mysqli_query($db->conn,$query);
				if(mysqli_num_rows($result) > 0){

					$task =  mysqli_fetch_assoc($result);
					$MailID =  $task['MailID'];
					//echo "update task set Status='Y' where Status='D' and  MailID='" . $MailID . "'";
					mysqli_query($db->conn,"update user_mail set Status='Y' where MailID='" . $MailID . "'");
			        mysqli_query($db->conn,"update task set Status='Y' where Status='D' and  MailID='" . $MailID . "'");
			        $case = 3;
					$flag = true;
					//echo $case; exit;
				}
			}
		} 
	     

     // Task send date update code 
		if (isset($_GET['tskup']) &&  isset($_GET['val']) ){
		    $act = unprotect(rawurldecode($_GET['tskup']));
		    $val = unprotect(rawurldecode($_GET['val']));
		    $weekend = unprotect(rawurldecode($_GET['w']));
		    $action = explode("-", $act);
		    if (count($action) == 2){
		    	global $db;
				$TaskID = $action[1];
				$query = "SELECT * from task inner join user_mail ON (task.MailID=user_mail.MailID) where task.TaskID=".$TaskID;
				$result = mysqli_query($db->conn,$query);
				$flag = true;
				if(mysqli_num_rows($result) > 0){

					$task =  mysqli_fetch_assoc($result);
			    	$input = [
			    		'd' => $val,
			    		'weekend' => $weekend,
			    	];
			    	$res = updateSchedule($input,$task);
			    	$iday = $res['iday'];
				}else{
				  $flag = false; 
				}
		    }
		}
		
		function updateSchedule($input,$task)
		{
			global $db;
			$MailID =  $task['MailID'];

			$input['d'] = strtoupper($input['d']);

			switch ($input['d']) {
				case 'N':
					$date = (time()+60);
					break;
				case '1H':
					$date = (time()+(60*60));
					break;
				case '2H':
					$date = (time()+(60*60*2));
					break;
				case '3H':
					$date = (time()+(60*60*3));
					break;
				case '1D':
					$date = strtotime('+1 days', time());
					break;
				case '2D':
					$date = strtotime('+2 days', time());
					break;
				case '3D':
					$date =  strtotime('+3 days', time());
					break;
				case '1W':
					$date = strtotime('+7 days', time());
					break;
				case '2W':
					$date = strtotime('+14 days', time());
					break;
				case '1M':
					$date = strtotime('+1 month', time());
					break;
				default:
					# code...
					$date = (time()+(60));
					break;
			}

			if(in_array($input['d'], ['N','1H','2H','3H'])){
				$d = date("Y-m-d H:i:s", $date);
			    $iday = getlocaltime($d, $task['TimeZone']);
			}else{
				$d = date("Y-m-d H:i:s", $date);
			    $iday = getlocaltime($d, $task['TimeZone'],'Y-m-d');
	      		$iday = $iday." ".date('H:i:s',strtotime($task['TaskInitiateDate']));
			}

			if(isset($input['weekend']) && $input['weekend'] == 'Y' ){
			    switch (date('N', strtotime($iday))) {
			    	case '6':
			    		$iday = date("Y-m-d H:i:s",strtotime('+2 days', strtotime($iday)));
			    		break;
			    	case '7':
			    		$iday = date("Y-m-d H:i:s",strtotime('+1 days', strtotime($iday)));
			    		break;
			    	default:
			    		# code...
			    		break;
			    }
			}

			$nday  = getgmdate($iday , $task['TimeZone']);
			$update_task="update task set Status='Y', TaskGMDate='".$nday."' , TaskInitiateDate='".$iday."'  where TaskID='" . $task['TaskID'] . "'";
			mysqli_query($db->conn,$update_task);
			$responce =  ['status'=> 1 , 'message' => 'updated' ,'iday' => $iday]; 

	            // update other tickles too 
			$sel = "SELECT task.TaskID,task.TickleID,task.TimeZone,tickle.NoWeekend,task.TaskInitiateDate,task.TaskGMDate,tickle.TickleName,tickle.DailyDays,ticklefollow.DailyDaysFollow,ticklefollow.NoWeekend as FollowNoWeekend from task inner join user_mail on (task.MailID=user_mail.MailID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TaskInitiateDate > '".$task['TaskInitiateDate']."' and task.Status='Y' and task.TaskID!=".$task['TaskID']." and task.MailID=".$MailID;
					$sel.=" ORDER BY TaskInitiateDate ASC,TaskID ASC";
			$tasks = $db->query_to_array($sel);

				foreach ($tasks as $tsk) {

					$days = (!empty($tsk['DailyDays'])) ? $tsk['DailyDays'] :  $tsk['DailyDaysFollow'] ;
					$iday = date("Y-m-d H:i:s",strtotime('+'.$days.' days', strtotime($iday)));
					$iday  = getlocaltime($iday, $tsk['TimeZone'],'Y-m-d');
					$iday = $iday.' '.date('H:i:s',strtotime($tsk['TaskInitiateDate']));

					$NoWeekend = (isset($tsk['FollowNoWeekend']) && !empty($tsk['FollowNoWeekend']))?$tsk['FollowNoWeekend']:$tsk['NoWeekend'];
					
					if($NoWeekend == 'Y' ){
					    switch (date('N', strtotime($iday))) {
					    	case '6':
					    		$iday = date("Y-m-d H:i:s",strtotime('+2 days', strtotime($iday)));
					    		break;
					    	case '7':
					    		$iday = date("Y-m-d H:i:s",strtotime('+1 days', strtotime($iday)));
					    		break;
					    	default:
					    		# code...
					    		break;
					    }
					}

					$nday  = getgmdate($iday , $tsk['TimeZone']);
					$update_task = "update task set TaskGMDate='".$nday."' , TaskInitiateDate='".$iday."'  where TaskID='" . $tsk['TaskID'] . "'";
					mysqli_query($db->conn,$update_task);
				}

			return $responce ;
		}
	///



   // complete task code 
	if( isset($_GET['cptsk']) && !isset($_GET['undo']) ){
		$case = 2;
		$act = unprotect(rawurldecode($_GET['cptsk']));
		$action = explode("-", $act);
		$is_owner =  unprotect(rawurldecode($_GET['els']));
		if (count($action) == 2){
	    	global $db;
			$TickleID = $action[0];
			$TaskID = $action[1];
		    $protected = rawurldecode(protect($TickleID . "-" . $TaskID));
		    $els = rawurldecode(protect($is_owner));


		        $TDeleteLink = "https://" . SERVER_NAME . Url_Create("unsubscribe", "act=" . rawurlencode($protect));
		        $TDashboardLink = "https://" . SERVER_NAME . Url_Create("home", "act=" . rawurlencode($protect));

			$query = "SELECT task.*,user_mail.toaddress,user_mail.Subject,user_mail.Date,user_mail.Message,user_mail.MessageHtml,tickleuser.EmailID, tickleuser.FirstName, tickleuser.LastName,contact_list.FirstName as CFN , contact_list.LastName as CLN , contact_list.EmailID as CEID from task inner join user_mail ON (task.MailID=user_mail.MailID) inner join tickleuser ON (task.TickleID=tickleuser.TickleID) inner join contact_list on (contact_list.ContactID=user_mail.ContactID)  where task.Status != 'D' and user_mail.Status != 'D' and task.TaskID=".$TaskID;
			$result = mysqli_query($db->conn,$query);
			if(mysqli_num_rows($result) > 0){
			    $flag = true;
				$task =  mysqli_fetch_assoc($result);
				//print_r($task); exit;
				$MailID = $task['MailID'];

				// if not a owner then send email to owner
				if($task && $is_owner == 'no'){
					$mail = new PHPMailer(false);
					$mail->IsSMTP();    // tell the class to use SMTP
		            $mail->Mailer = "smtp";                       
		            $mail->Host = "mail.tickletrain.com"; // SMTP server
		            $mail->Port = '25'; // set the SMTP server port
		            $mail->SMTPKeepAlive = false;                  // SMTP connection will not close after each email sent     
		            $mail->Username = "ticklein@tickletrain.com";     // SMTP server username
		            $mail->Password = 'change88q1w2e3r4';     // SMTP server password
		            $mail->SMTPAuth = true;                  // enable SMTP authentication        
		            $mail->SMTPSecure = '';
		            $mail->SMTPAutoTLS = false;                        // Enable TLS encryption, `ssl` also accepted
		           // $mail->SMTPDebug = 2;
		            $FromEmailid = "ticklein@tickletrain.com";
		            $FromEmailid_name = 'Task Complete' ;
		            $mail->setFrom($FromEmailid , trim($FromEmailid_name));
		            $mail->Sender = $FromEmailid;
		            $mail->Subject = $task['Subject'].' [Complete]';
		            $mail->isHTML(true); // send as HTML
		            $to  = $task['toaddress'];
					$mail->CharSet = "utf-8";

	            	$body  =  get_confirm_body($task);
	            	$stage =  unprotect(rawurldecode($_GET['stg']));   
					$body =  preg_replace("/\[Stage\]/i", $stage, $body);
	            	$mail->MsgHTML($body);
	            	$mail->AddAddress($task['EmailID']);
	       			$ret = $mail->Send();
				}elseif($task && $is_owner == 'yes'){
					$delDate = date("Y-m-d H:i:s");
					mysqli_query($db->conn,"update user_mail set Status='D' where MailID='" . $MailID . "'");
			        mysqli_query($db->conn,"update task set Status='D',TaskDeletedDate='".$delDate."' where Status='Y' and  MailID='" . $MailID . "'");
				}

			}else{
			  $flag = false; 
			  $task_not_found_to_user = true;
			}
	    }
	}

	function get_body($task){
		$email_templete =  file_get_contents(HOME_FOLDER.'emails/complete_task.html');
		$full_name  =  $task['FirstName']." ".$task['LastName'];

	    $body =  preg_replace("/\[Name\]/i", $full_name, $email_templete);
	    $body =  preg_replace("/\[To\]/i", $task['toaddress'], $body);
	    $body =  preg_replace("/\[Subject\]/i", $task['Subject'], $body);
	    $body =  preg_replace("/\[Date\]/i", date("D, j M Y H:i:s O(T)"), $body);

	    return $body;
	}

	function get_confirm_body($task){
		$email_templete =  file_get_contents(HOME_FOLDER.'emails/confirm_complete_task.html');


	    $protect = protect($task['TickleID'] . "-" . $task['TaskID']);
		$TaskComplete = "https://".SERVER_NAME.Url_Create("test","cptsk=".rawurlencode($protect)."&els=".rawurlencode(protect('yes'))); //Link for complete the task .. for owner

		$full_name  =  $task['FirstName']." ".$task['LastName'];
		/** source content **/
		$Attsourcemsg = "<div style='border:none;border-top:solid #B5C4DF 1.0pt;padding:3.0pt 0in 0in 0in'>
		<b>From:</b> ".$task['FirstName']." ".$task['LastName']. " [" . $task['EmailID'] . "] <br /> 
		<b>Sent:</b> " . $task['Date'] . " <br /> 
		<b>To:</b>" . $task['ToAddress'] . " <br />
		<b>Subject:</b> " . $task['Subject']  . "<br /><br />" .$task['MessageHtml']."<br/></div>" ;
        
		/** end **/
		$name =  (!empty($task['CFN']) || !empty($task['CLN']))? $task['CFN'].' '.$task['CLN'] : $task['CEID'];
	    $body =  preg_replace("/\[Name\]/i", $full_name, $email_templete);
	    $body =  preg_replace("/\[SomeOne\]/i", $name, $body);
	    $body =  preg_replace("/\[To\]/i", $task['toaddress'], $body);
	    $body =  preg_replace("/\[Subject\]/i", $task['Subject'], $body);
	    $body =  preg_replace("/\[Date\]/i", date("D, j M Y H:i:s O(T)"), $body);
	    $body =  preg_replace("/\[TaskComplete\]/i", $TaskComplete, $body);
		$body =  preg_replace("/\[message_body\]/i", $Attsourcemsg, $body);

	    return $body ;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/vnd.microsoft.icon" href="/<?= ROOT_FOLDER ?>favicon.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>css/all.css" type="text/css" media="all"/>
    <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>css/jquery-confirm.min.css" type="text/css" media="all"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
	<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script>
	function quitwindow(){
	//	var win = window.open(location, "_self");
//win.close();

 var  s=window.opener;
s.close();
	
	}
	
	function restorecam(url){
			$.ajax({
					url: 'https://client.tickletrain.com/'+url+'&taskk=restore',
				   // dataType: 'json',
					method: 'GET',
				}).done(function (response) {
					console.log(response); 
					if(response == 3){
						//alert("Task has been restored successfully");
						$("#success-msg").hide();
						$("#Restoretask").fadeIn('1000');
					//	window.close();
						return false;
					}
				}).fail(function(){
					console.log('error');
				});
			
             //  window.location.href = url;
           
	}
	</script>
	
	
</head>
<style type="text/css">
	body{
		background:#efefef;
	}
	.row.custom{
	color: #343a40 !important;
	display: block;
	position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    -webkit-transform: translate(-50%, -50%); 
	-moz-transform: translate(-50%, -50%); 
	-ms-transform:translate(-50%, -50%); 
	-o-transform: translate(-50%, -50%);
	text-align: center; 
	margin: 0;
	width: 100%;
	}
	.row.custom img{
		max-width: 160px;
	}
	.row.custom h4{
		margin-top: 15px;
		font-size: 20px;
	}
	.btn-success.animated{
		min-width: 70px;
		font-weight: bold;
	}
	@media(max-width:767px){
		.row.custom img{
			max-width: 86px;
		}
		h4{
			font-size: 18px;
		}
	}
</style>
<body>
	<div class="container">

		<?php if($flag){    ?>

		<?php if($case == 1){  ?>
			<div class="row custom">
				<img class="animated fadeIn delay-1s" src="<?= ROOT_FOLDER ?>/images/success-png-icon.png" width="200" />
				<h4 class="animated fadeIn delay-2s"> Task Updated Successfully</h4>
				<p class="animated fadeIn delay-2s" style="font-size: 16px;">
					Your task schedule is updated. This task will be sent on  
					<b><?php echo date('M d, Y',strtotime($iday)); ?>
					at
					<?php echo date('h:i A',strtotime($iday)); ?>
					</b>
				</p>
				<p>
					<button class="btn btn-success animated fadeIn delay-2s" onclick="window.close();"> Ok </button>
					
				</p>
			</div>
		<?php } ?>

		<?php if($case == 2){  ?>
			<div class="row custom" id="success-msg">
				<img class="animated fadeIn delay-1s" src="<?= ROOT_FOLDER ?>/images/success-png-icon.png" width="200" />
				<h4 class="animated fadeIn delay-1s"> This Task has been Marked Complete!</h4>
				<?php if($is_owner == 'yes' || isset($_GET['redo']) ){ ?>
					<!--p class="animated fadeIn delay-1s" style="font-size: 16px;">
						To undo this action and restore this task <a href="?undo=true&cptsk=<?php echo $protected; ?>&els=<?php echo $els; ?>" id="undo"> click here </a>
					</p-->
					
					<p class="animated fadeIn delay-1s" style="font-size: 16px;">
						To undo this action and restore this task <a style="color:blue;cursor:pointer;" onclick="restorecam('test/?undo=true&cptsk=<?php echo $protected; ?>&els=<?php echo $els; ?>')"> click here </a>
					</p>
					
					
					<!---button onclick="windowOpen('?undo=true&cptsk=<?php //echo $protected; ?>&els=<?php //echo $els; ?>')"> 
					  Open GeeksforGeeks 
					</button-->
				<?php }  ?>
				<p>
					<button class="btn btn-success animated fadeIn delay-2s" onclick="window.close();"> Ok </button>
				</p>
			</div>
		<?php } ?>

		<?php if($case == 3){  ?>
			<div class="row custom" >
				<img class="animated fadeIn" src="<?= ROOT_FOLDER ?>/images/success-png-icon.png" width="200" />
				<h4 class="animated fadeIn"> Task has been Restored!</h4>
				<?php if($is_owner == 'yes' || isset($_GET['undo']) ){ ?>
					<p class="animated fadeIn delay-1s" style="font-size: 16px;">
						To undo this action and mark complete <a  href="?redo=true&cptsk=<?php echo $protected; ?>&els=<?php echo $els; ?>"> click here </a>
					</p>
					<p>
						<button class="btn btn-success animated fadeIn" onclick="window.close();"> Ok </button>
						
					</p>
				<?php }  ?>
			</div>
		<?php } ?>
		
		<div id="Restoretask" style="display:none;">
		<div class="row custom" >
				<img class="animated fadeIn" src="<?= ROOT_FOLDER ?>/images/success-png-icon.png" width="200" />
				<h4 class="animated fadeIn"> Task has been Restored!</h4>
				<?php if($is_owner == 'yes' || isset($_GET['undo']) ){ ?>
					<p class="animated fadeIn delay-1s" style="font-size: 16px;">
						To undo this action and mark complete <a  href="?redo=true&cptsk=<?php echo $protected; ?>&els=<?php echo $els; ?>"> click here </a>
					</p>
					<p>
						<button class="btn btn-success animated fadeIn" onclick="window.close();"> Ok </button>
						
					</p>
				<?php }  ?>
			</div>
        </div>

		<?php } ?>

		<?php if(!$flag){  ?>
			<div class="row custom">
				<img class="animated fadeIn delay-1s" src="<?= ROOT_FOLDER ?>/images/success-png-icon.png" width="200" />
				<h4 class="animated fadeIn delay-1s">This task has already been marked complete!</h4>
				<p>
					<button class="btn btn-success animated fadeIn delay-1s" onclick="window.close();"> Ok </button>
				</p>
			</div>
		<?php } ?>

	</div>
</body>
</html>
<?php die; ?>