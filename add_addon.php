<?php
include_once("includes/data.php");
include("includes/function/func.php");

if(isset($_POST['addon_id']) && $_POST['addon_id'] != ''){	
        $userdetails = mysqli_fetch_assoc(mysqli_query($db->conn,'select * from tickleuser where EmailID = "'.$_POST['email_id'].'"'));
	$current_addonId = $userdetails['email_addon'];
	$query = mysqli_query($db->conn,"UPDATE tickleuser SET email_addon='".$_POST['addon_id']."', addon_hosting_id='".$_POST['addon_hosting_id']."' WHERE EmailID ='" . $_POST['email_id'] . "'") or die(mysqli_error($db->conn));

	if($current_addonId==2 || $current_addonId==6){
		
			if($_POST['addon_id']==1 || $_POST['addon_id']==5){
				$secondaryquery = mysqli_query($db->conn,'select * from secondaryEmail where TickleID = "'.$userdetails['TickleID'].'" ORDER BY id DESC LIMIT 1');
				$rows = mysqli_num_rows(mysqli_query($db->conn,'select * from secondaryEmail where TickleID = "'.$userdetails['TickleID'].'"'));
				$limit = $rows-2;
				if($limit>0){
					while($secData = mysqli_fetch_assoc($secondaryquery)){
						mysqli_query($db->conn,'delete from secondaryEmail where id = "'.$secData['id'].'"');
						//for preview email dashboard
						$querytask = mysqli_query($db->conn,'select * from task where secondaryEmailId = "'.$secData['id'].'"');
						while($taskData = mysqli_fetch_assoc($querytask)){
							mysqli_query($db->conn,'update user_mail set senderaddress = "'.$userdetails['FirstName'].' '.$userdetails['LastName'].' <'.$userdetails['EmailID'].'>" where MailID="'.$taskData['MailID'].'"');
						}
						//for preview email dashboard
						mysqli_query($db->conn,'update task set secondaryEmailId="" where secondaryEmailId = "'.$secData['id'].'"');
					}
				}
			}

	}
	else if($current_addonId==3 || $current_addonId==7){
		if($_POST['addon_id']==1 || $_POST['addon_id']==5){
			$rows = mysqli_num_rows(mysqli_query($db->conn,'select * from secondaryEmail where TickleID = "'.$userdetails['TickleID'].'"'));
			$limit = $rows-1;
			if($limit>0){
				$secondaryquery = mysqli_query($db->conn,'select * from secondaryEmail where TickleID = "'.$userdetails['TickleID'].'" ORDER BY id DESC LIMIT '.$limit.'');
				while($secData = mysqli_fetch_assoc($secondaryquery)){
					mysqli_query($db->conn,'delete from secondaryEmail where id = "'.$secData['id'].'"');
					//for preview email dashboard
					$querytask = mysqli_query($db->conn,'select * from task where secondaryEmailId = "'.$secData['id'].'"');
					while($taskData = mysqli_fetch_assoc($querytask)){
						mysqli_query($db->conn,'update user_mail set senderaddress = "'.$userdetails['FirstName'].' '.$userdetails['LastName'].' <'.$userdetails['EmailID'].'>" where MailID="'.$taskData['MailID'].'"');
					}
					//for preview email dashboard
					mysqli_query($db->conn,'update task set secondaryEmailId="" where secondaryEmailId = "'.$secData['id'].'"');
				}
			}
		}
		if($_POST['addon_id']==2 || $_POST['addon_id']==6){
			$rows = mysqli_num_rows(mysqli_query($db->conn,'select * from secondaryEmail where TickleID = "'.$userdetails['TickleID'].'"'));
			$limit = $rows-2;
			if($limit>0){
				$secondaryquery = mysqli_query($db->conn,'select * from secondaryEmail where TickleID = "'.$userdetails['TickleID'].'" ORDER BY id DESC LIMIT '.$limit.'');
				while($secData = mysqli_fetch_assoc($secondaryquery)){
					mysqli_query($db->conn,'delete from secondaryEmail where id = "'.$secData['id'].'"');
					//for preview email dashboard
					$querytask = mysqli_query($db->conn,'select * from task where secondaryEmailId = "'.$secData['id'].'"');
					while($taskData = mysqli_fetch_assoc($querytask)){
						mysqli_query($db->conn,'update user_mail set senderaddress = "'.$userdetails['FirstName'].' '.$userdetails['LastName'].' <'.$userdetails['EmailID'].'>" where MailID="'.$taskData['MailID'].'"');
					}
					//for preview email dashboard
					mysqli_query($db->conn,'update task set secondaryEmailId="" where secondaryEmailId = "'.$secData['id'].'"');
				}
			}
		}

	}
	
	
	die('success');
}

if(isset($_POST['remove_addons']) && $_POST['remove_addons']==true){
	$userdetails = mysqli_fetch_assoc(mysqli_query($db->conn,'select * from tickleuser where EmailID = "'.$_POST['email_id'].'"'));
	mysqli_query($db->conn,'update tickleuser set email_addon="",addon_hosting_id="" where TickleID = "'.$userdetails['TickleID'].'"');
	mysqli_query($db->conn,'update task set secondaryEmailId="" where TickleID = "'.$userdetails['TickleID'].'"');
	mysqli_query($db->conn,'delete from secondaryEmail where TickleID = "'.$userdetails['TickleID'].'"');
	die('success');
}

