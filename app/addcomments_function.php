<?php
use PHPMailer\PHPMailer\PHPMailer;

define('HOME_FOLDER', GetHomeDir() . "/");
define('SERVER_NAME', "client.tickletrain.com");

//echo 'post_max_size = ' . ini_get('post_max_size') . "\n";
//echo 'memory_limit = ' . ini_get('memory_limit') . "\n";


  // save form data
	if(isset($_POST['add_comment'])){
		
		$totalsizeee = $_POST['totalsizeee'];
		$Subject  = addslashes($_POST['CustomSubject']);
		$MailID  = $_POST['MailID'];
		$TickleID  = $_POST['TickleID'];
		$MessageHtml  = $_POST['TickleMailContent'];
		$TickleTrainID  = $_POST['TickleTrainID'];
		$is_owner  = $_POST['is_owner'];
		$RawPath  = $_POST['RawPath'];
		$attachments = !empty($_POST['attachments'])?explode(',', $_POST['attachments']):[];
		$deleted_attachments = !empty($_POST['deleted_attachments'])?explode(',', $_POST['deleted_attachments']):[];
		$new_img_array = [];

        // update custom subject 
		//mysqli_query($db->conn,"update tickle set custom_subject='" .$Subject. "' where TickleTrainID='" . $TickleTrainID . "' ");
 // /var/www/vhosts/tickletrain.com/httpdocs/new/mail/20120401/53.txt
		 $Rawmaildir ="/var/www/vhosts/client.tickletrain.com/httpdocs";

		// //shell_exec('ls'); 

		//    $oldmask = umask(0);//it will set the new umask and returns the old one 
		// //if (!is_dir($Rawmaildir . $RawPath)) {
         
  //           @mkdir($Rawmaildir . $RawPath, 0777);
  //           @chmod($Rawmaildir . $RawPath, 0777, true);
  //       //}else{
  //       	echo "else";
  //       //}
  //           umask($oldmask);
  //       die('die');

		// remove delete images from folders 
		if(!empty($deleted_attachments)){
			foreach ($deleted_attachments as $del_img) {
				@unlink($Rawmaildir.$RawPath.$del_img);
			}
		}

			 
			  $RelateiveMailPath = "mail/" . gmdate("Ymd") . "/";
            $Rawmaildir = HOME_FOLDER . $RelateiveMailPath;
			
			 $Rmailid = $MailID;
			 
			  if (!is_dir($Rawmaildir)) { 
                @mkdir($Rawmaildir, 0777);
                @chmod($Rawmaildir, 0777);
            } 
			
			 if (!is_dir($Rawmaildir . $Rmailid . "/")) { 
                $oldmask = umask(0);//it will set the new umask and returns the old one 
                @mkdir($Rawmaildir . $Rmailid . "/", 0777);
                @chmod($Rawmaildir . $Rmailid . "/", 0777);
                echo("Created '" . $Rawmaildir . $Rmailid . "/" . "'");
                umask($oldmask);
            }
		
			
		


		if(isset($_FILES['new_images']) && !empty($RawPath)){
		    $errors= array();
			
			//print_r($_FILES['new_images']['name']);
			
			// $uploadsize = 0 ;
			
		    foreach ($_FILES['new_images']['name'] as $key => $image) { 

		      	if(!empty($image)){  

				      $file_name = $image;
				      $file_size =$_FILES['new_images']['size'][$key];
				      $tmp_name =$_FILES['new_images']['tmp_name'][$key];
				      $type = $_FILES['new_images']['type'][$key];
					  
					
					 $totalFileSize = $totalsizeee + $file_size;			
				      $file_ext=strtolower(end(explode('.',$image)));
				     // $extensions= array("jpeg","jpg","png");
					  $extensions = array("gif", "jpeg", "jpg","png","pdf","doc","docx","txt","rtf","bmp","psd","zip","rar","ppt","pptx","cdr");
				      $allow_type= array("image/jpeg","image/jpg","image/png");
				      
				    //if(in_array($file_ext,$extensions)){ 
				      //	if(in_array($type, $allow_type)) {
				      		//$new_name = mt_rand().time().'.'. $file_ext;
							
					//$maxFileSize = 	'4800000';		
					$maxFileSize = 	'10485760';	 //10mb
					if ($totalFileSize > $maxFileSize) {
							header("location:https://client.tickletrain.com".$_SERVER['REQUEST_URI']."&filerror=limiterror");
							die;
					}
					else { 
							$imageArr=explode('.',$file_name);
							$randimg =rand(1000,9999);
							$newImageName=$imageArr[0].'_'.$randimg.'.'.$imageArr[1];
							$new_name = $newImageName;
				      		$Rawmaildir = "/var/www/vhosts/client.tickletrain.com/httpdocs".$RawPath;
						   // move_uploaded_file($tmp_name,$Rawmaildir.$new_name);
							if(move_uploaded_file($tmp_name,$Rawmaildir.$new_name)) {
							  echo "The file  has been uploaded";
							} else{
								echo "There was an error uploading the file, please try again!";
							}
					}
						    $new_img_array[] = $new_name;
					  //	}
				    //}
		      	}
			}
		}
       
	    // $newtotalsize = $uploadsize + $totalsizeee;

		$atta__ =   array_merge($attachments,$new_img_array);


        // Ppdate Email Content 
		mysqli_query($db->conn,"update user_mail set attachments='".implode(',',$atta__)."' , CustomSubject='" .$Subject. "', MessageHtml='" .$MessageHtml. "' where MailID='" . $MailID . "' ");
		$comment_by = ($is_owner == 'yes') ? 'owner' : 'contact'; 
		// add New Comment
		if(!empty($_POST['message'])){ 
			$comment  = addslashes($_POST['message']); 
			$comment  = str_replace('/upload-files','https://client.tickletrain.com/upload-files',$comment);
			echo "insert into comments (MailID,TickleTrainID,TickleID,comment_by,comment) values (".$MailID.",'".$TickleTrainID. "', '".$TickleID. "', '".$comment_by. "','".$comment. "')"; 
			mysqli_query($db->conn,"insert into comments (MailID,TickleTrainID,TickleID,comment_by,comment) values (".$MailID.",'".$TickleTrainID. "', '".$TickleID. "', '".$comment_by. "','".$comment. "')" );
		}

		header("location:https://client.tickletrain.com".$_SERVER['REQUEST_URI']);
		die;
	}
 
 // delete Comments 
    if(isset($_GET['delete_comment'])){
    	 mysqli_query($db->conn,"delete from comments where id=".$_GET['delete_comment']);
    	 echo "deleted"; die;
    } 


// complete task code
	if( isset($_GET['cptsk'])){
		$case = 1;
		$act = unprotect(rawurldecode($_GET['cptsk']));
		$action = explode("-", $act);
		$is_owner =  unprotect(rawurldecode($_GET['els']));

		if (count($action) == 2){
	    	global $db;
			$TickleID = $action[0];
			$MailID = $action[1];
		    $protected = rawurldecode(protect($TickleID . "-" . $TaskID));
		    $els = rawurldecode(protect($is_owner));

	        $TDeleteLink = "https://" . SERVER_NAME . Url_Create("unsubscribe", "act=" . rawurlencode($protect));
	        $TDashboardLink = "https://" . SERVER_NAME . Url_Create("home", "act=" . rawurlencode($protect));

			$query = "SELECT user_mail.TickleID,user_mail.MailID, user_mail.attachments,user_mail.RawPath, user_mail.toaddress,user_mail.MessageHtml,user_mail.Subject, user_mail.TickleTitleID,tickleuser.EmailID, tickleuser.FirstName, tickleuser.LastName,contact_list.FirstName as CFN , contact_list.LastName as CLN , contact_list.EmailID as CEID , user_mail.CustomSubject from task inner join user_mail ON (task.MailID=user_mail.MailID) inner join tickleuser ON (task.TickleID=tickleuser.TickleID) inner join contact_list on (contact_list.ContactID=user_mail.ContactID)  where task.Status != 'D' and task.MailID=".$MailID;

			$result = mysqli_query($db->conn,$query);
			if(mysqli_num_rows($result) > 0){
			    $flag = true;
				$task =  mysqli_fetch_assoc($result);
				$MailID = $task['MailID'];

		

			    $basepath = preg_replace("/\.txt$/i", "/", $task['RawPath']);
			    $basepath = str_replace('/var/www/vhosts/client.tickletrain.com/httpdocs', "", $basepath);
				
				$Subject = (empty($task['CustomSubject'])?$task['Subject']:$task['CustomSubject']); 
				
				$Variables['MessageHtml'] = $task['MessageHtml'];
				$Variables['OrignalSubject'] = (empty($task['Subject']) ? '(no subject)' : $task['Subject']);
				$Variables['Subject'] = $Subject ;
				$Variables['TickleID'] = $task['TickleID'];
				$Variables['MailID'] = $task['MailID'];
				$Variables['is_owner'] = $is_owner;

				$Variables['RawPath'] = $basepath;
				$Variables['attachments'] = $task['attachments'];

				$Variables['TickleTrainID'] = $task['TickleTitleID'];
				$Variables['all_comments'] = get_comments($MailID);
			

			}else{
			 	header("location:https://client.tickletrain.com");
				die;
			}
	    }else{
	    	header("location:https://client.tickletrain.com");
			die;
	    }
	}

?>