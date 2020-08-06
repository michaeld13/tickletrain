<?php
header('Access-Control-Allow-Origin: *');


include_once("../config.php");
include_once("../includes/data.php");
include_once("../includes/function/func.php");
// include_once("../includes/class/phpmailer/class.phpmailer.php");
include_once("./Tickle_Validate_Var.php");
 //ini_set('display_errors', 1);
 //error_reporting(E_ALL);
//define('HOME_FOLDER', GetHomeDir() . "/");

/**
 * 
 */
class TickleTrain extends Tickle_Validate_Var {

	protected $request;

	protected $domain_name = 'client.tickletrain.com';

	protected $if_subject_empty = "(no subject)";

	protected $page_list_for_extension = [
		array('title' => 'My Contacts' , 'url' => '?' , 'id'=>'tt-mc' , 'class' => ''),
		array('title' => 'Dashboard' , 'url' => '#' , 'id'=>'tt-upc' , 'class' => ''),
	];
	
	 public function __construct()
	{
		$this->request = $this->getRequest();
		if(!method_exists($this, $this->request->method)){
            echo "method does not exist"; die;
        }
	}

	private function getRequest()
	{
		return $this->tt_object($_REQUEST);
	}

	/*
	* Function : Login 
	* Use : Login into Extension
	* Date : 20-05-19
	*/
	public function login()
	{
		# code...
		global $db;
		$input = $this->tt_filter_arr($this->request);
		$Username = $input['username'];

		if(trim($input['encryption']) == 'true'){
			$input['password'] = decryptIt($input['password']);
		}
		$Password = stripcslashes($_POST['password']);
		$query = "select * from tickleuser  WHERE ( UserName ='$Username' or  EmailID='$Username') and Password='$Password'";
        $res = mysqli_query($db->conn,$query);
        $row = mysqli_fetch_assoc($res);
		if($row){
			$short_name = '';
			$imap_setting = false;
			if(!empty($row['FirstName'])){
			  	$row['FirstName'] = str_replace("'","",$row['FirstName']);
                $row['LastName'] = str_replace("'","",$row['LastName']);
				$short_name =  $row['FirstName'][0]."".(!empty($row['LastName'])?$row['LastName'][0]:'');
			}

			// save user ip in databse
			$ip  = $this->getIp();
			$update_task = "update tickleuser set IPAddress='".$ip."' where TickleID='" . $row['TickleID'] . "'";
			mysqli_query($db->conn,$update_task);
			//

			$qu =  "select access_token, refresh_token from google_auth_tokens  where  google_auth_tokens.userid =".$row['TickleID'];
			$result = mysqli_query($db->conn,$qu);
			$gogle_tokn = mysqli_num_rows($result);
			if($gogle_tokn  > 0 ){
				$token = mysqli_fetch_assoc($result);
				if(!empty($token['access_token']) && !empty($token['refresh_token']) ){
					$imap_setting = true;
				}
			}

			if ( !empty($row['imap_host']) &&  !empty($row['imap_userame']) && !empty($row['imap_passowrd'])){
				$imap_setting = true;
			}

			$res =[
				'status' => 1,
				'userData' => [
					'UserName' => $row['UserName'],
					'UserID' => $row['TickleID'],
				    'Password' => encryptIt($row['Password']),
				    'EmailID' => $row['EmailID'],
				    'FirstName' => $row['FirstName'],
					'LastName' => $row['LastName'],
					'TimeZone' => $row['TimeZone'],
					'delete_all_campaign' => $row['delete_all_campaign'],
					'imap_setting' => $imap_setting,
					'ShortName' => $short_name,
				    'pages' =>  $this->page_list_for_extension,
				    'App_Url' => 'https://client.tickletrain.com',
				]
			];
			if(isset($input['want_html'])) {
				$res["html"] = $this->getHomeHtml($res['userData']);
			}
		}else{
			$res = ['status' => 0 , 'message' => 'Your username or password is incorrect.'];
		}
		return json_encode($res);
	}



	/*
	* Function : getHomePage 
	* Use : getHomePage for Extension
	* Date : 20-05-19
	*/
	public function getHomePage()
	{
		global $db;
		$data = [];
		$input = $this->tt_filter_arr($this->request);
		$query = "select * from tickleuser where TickleID=".$input['user_id'];
        $res = mysqli_query($db->conn,$query);
        $has_data = mysqli_num_rows($res);
		if($has_data){
			$user = mysqli_fetch_assoc($res);
			$user['imap_setting'] = false;
			$qu =  "select access_token, refresh_token from google_auth_tokens  where  google_auth_tokens.userid =".$user['TickleID'];
			$result = mysqli_query($db->conn,$qu);
			$gogle_tokn = mysqli_num_rows($result);
			if($gogle_tokn  > 0 ){
				$token = mysqli_fetch_assoc($result);
				if(!empty($token['access_token']) && !empty($token['refresh_token']) ){
					$user['imap_setting'] = true;
				}
			}
			if ( !empty($row['imap_host']) &&  !empty($row['imap_userame']) && !empty($row['imap_passowrd'])){
				$user['imap_setting'] = true;
			}

				$user['UserID'] = $user['TickleID'];

			$res = ['status' => 1 , 'html' => $this->getHomeHtml($user)];
		}else{
			$res = ['status' => 0 , 'message' => 'No user found.'];
		}
		return json_encode($res);
	}

	/*
	* Function : getProfilePage 
	* Use : display profile page for Extension
	* Date : 20-05-19
	*/
	public function getProfilePage()
	{
		global $db;
		$data = [];
		$input = $this->tt_filter_arr($this->request);
		$query = "select * from tickleuser where TickleID=".$input['user_id'];
        $res = mysqli_query($db->conn,$query);
        $row = mysqli_fetch_assoc($res);
		if($row){
			$imap_setting = false;
			$qu =  "select access_token, refresh_token from google_auth_tokens  where  google_auth_tokens.userid =".$row['TickleID'];
			$result = mysqli_query($db->conn,$qu);
			$gogle_tokn = mysqli_num_rows($result);
			if($gogle_tokn  > 0 ){
				$token = mysqli_fetch_assoc($result);
				if(!empty($token['access_token']) && !empty($token['refresh_token']) ){
					$imap_setting = true;
				}
			}
			if ( !empty($row['imap_host']) &&  !empty($row['imap_userame']) && !empty($row['imap_passowrd'])){
				$imap_setting = true;
			}
			$row['FirstName'] = str_replace("'","",$row['FirstName']);
            $row['LastName'] = str_replace("'","",$row['LastName']);

			if(!empty($row['FirstName'])){
				$short_name =  $row['FirstName'][0]."".(!empty($row['LastName'])?$row['LastName'][0]:'');
			}
			$res =[
				'status' => 1,
				'userData' => [
					'UserName' => $row['UserName'],
					'UserID' => $row['TickleID'],
				    'Password' => encryptIt($row['Password']),
				    'EmailID' => $row['EmailID'],
				    'FirstName' => $row['FirstName'],
					'LastName' => $row['LastName'],
					'ShortName' => $short_name,
					'delete_all_campaign' => $row['delete_all_campaign'],
					'TimeZone' => $row['TimeZone'],
					'imap_setting' => $imap_setting,
				    'pages' =>  $this->page_list_for_extension,
				    'App_Url' => 'https://client.tickletrain.com',
				]
			];
			if(isset($input['want_html'])) {
				$res["html"] = $this->getProfilePageHTML($res['userData']);
			}
		}else{
			$res = ['status' => 0 , 'message' => 'No user found.'];
		}
		return json_encode($res);
	}

	/*
	* Function : getActivitiesPage 
	* Use : display profile page for Extension
	* Date : 28-06-19
	*/
	public function getActivitiesPage()
	{
		global $db;
		$data = [];
		$input = $this->tt_filter_arr($this->request);
		$TickleID = $input['user_id'];

		$q = "select TimeZone from tickleuser where TickleID=".$input['user_id'];
		$user_res = mysqli_query($db->conn,$q);
		$user = mysqli_fetch_assoc($user_res);
		if(!empty($user['TimeZone'])){
			date_default_timezone_set($user['TimeZone']);
		}

		$query = "select task.MailID,contact_list.FirstName, contact_list.LastName , contact_list.EmailID as ContactEmail, user_mail.Subject ,task_track_records.id,task_id,request_time,task_track_records.type, user_mail.ContactID from task_track_records 
					JOIN task ON (task.TaskID = task_track_records.task_id and task.TickleID = $TickleID )
					JOIN user_mail ON (user_mail.MailID = task.MailID)
					JOIN contact_list ON (contact_list.ContactID = user_mail.ContactID)
					GROUP BY task.MailID
					ORDER BY task_track_records.id DESC LIMIT 20";


		$result = mysqli_query($db->conn,$query);
		$list_html = "";
		if(mysqli_num_rows($result) > 0){

			while($row = mysqli_fetch_assoc($result)){

				$name = $row['ContactEmail'] ;
				$short_name ='<img src="../images/defaul_user36*36.png" alt="user image" class="rounded-circle mx-auto d-block img-fluid" />';
				if(!empty($row['FirstName'])){
					$row['FirstName'] = str_replace("'","",$row['FirstName']);
               		$row['LastName'] = str_replace("'","",$row['LastName']);
					$short_name =  '<span class="rounded-circle" >'.$row['FirstName'][0]."".(!empty($row['LastName'])?$row['LastName'][0]:'').'</span>';
				    $name = $row['FirstName']." ".$row['LastName'];
				}

				$list_html.='<li class="list-group-item">
								<div class="row">
									<div class="col-2 col-sm-6 col-md-3 px-0">
										'.$short_name.'
									</div>
									<div class="col-10 col-sm-6 col-md-9 p-1">
										<small class="text-muted float-right pulse">'.date("M d h:s a", $row['request_time']).'</small>
										<label class="name">'.$name.'</label>
										<small class="opened text-muted pointer" id="'.$row['MailID'].'">'.$row['Subject'].'<img src="../images/arrow-point-to-right.svg" class="arrow-img"> <span><img  class="del-btn-activity pointer"  width="15" height="15" data-id="'.$row['task_id'].'" src="' . $this->imagePath . '/delete.svg" /></span> </small>
									</div>
									<div class="col-12" style="padding:0px;">
										<div class="timeline-box">
										</div>
									</div>
								</div>
							</li>';
			}

		}
		return  $this->getActivitiesPageHTML($list_html);
	}

	
	/*
	* Function : view_open_email 
	* Use : display the detail of Activity
	* Date : 29-06-19
	*/
	public function view_open_email()
	{
		global $db;
		$data = []; // FollowTickleTrainID
		$input = $this->tt_filter_arr($this->request);
		$trackID = $input['trackID'];
		$TickleID  =  $input['user_id'];
		$bodytxt = "";

		if(!empty($trackID)){

			$q =  "select * from task_track_records where id=" . $trackID;
			$res = mysqli_query($db->conn,$q);

			if(mysqli_num_rows($res) > 0){
				$track_records = mysqli_fetch_assoc($res);
				$query = "SELECT task.FollowTickleTrainID,task.SentDate,tickle.TickleMailContent,ticklefollow.TickleMailFollowContent,user_mail.toaddress,user_mail.ccaddress,user_mail.fromaddress,user_mail.Subject,user_mail.CustomSubject, contact_list.FirstName, contact_list.LastName , tickleuser.signature FROM `task` LEFT JOIN `ticklefollow` ON  task.FollowTickleTrainID = ticklefollow.FollowTickleTrainID  JOIN user_mail ON user_mail.MailID = task.MailID JOIN contact_list ON contact_list.ContactID = user_mail.ContactID JOIN tickleuser ON  tickleuser.TickleID = user_mail.TickleID JOIN tickle ON tickle.TickleTrainID = task.TickleTrainID  WHERE TaskID=".$track_records['task_id']." AND task.TickleID=".$TickleID;

				$result = mysqli_query($db->conn,$query);

				if(mysqli_num_rows($result) > 0){
						$arr = mysqli_fetch_assoc($result);
						$arr['TickleMailFollowContent'] = ($arr['FollowTickleTrainID'] == 0 )?$arr['TickleMailContent']:$arr['TickleMailFollowContent'];
						$name = "";
						$arr['FirstName'] = str_replace("'","",$arr['FirstName']);
			            $arr['LastName'] = str_replace("'","",$arr['LastName']);
						$firstname =  (!empty($arr['FirstName']))?$arr['FirstName']:$arr['LastName'];

						$signature =  $arr['signature'];
						$arr['Subject'] = (!empty($arr['CustomSubject'])) ? $arr['CustomSubject'] : $arr['Subject'] ;
						$arr['Subject'] = (!empty($arr['Subject'])?$arr['Subject']: $this->if_subject_empty);

						// $bodytxt.="<div class='email-header'><div style='clear:left'><label><b>To:</b></label>".htmlspecialchars($arr['toaddress'])."</div>";
						// $bodytxt.="<div style='clear:left'><label><b>From:</b></label>".htmlspecialchars($arr['fromaddress'])."</div>";
						// $bodytxt.="<div style='clear:left'><label><b>Subject:</b></label>".htmlspecialchars($arr['Subject'])."</div>";
						// $bodytxt.="<div style='clear:left'><label> <b>Sent:</b></label>".$arr['SentDate']."</div><hr></div>";


						$bodytxt.="<div class='email-header'>
								<div class='row'>
									<div class='col-sm-2'><label>From:</label></div>
		  							<div class='col-sm-10'><p class='h-detail'>".htmlspecialchars($arr['fromaddress'])."</p></div>
								</div>";
						$bodytxt.="<div class='row'>
											<div class='col-sm-2'><label>To:</label></div>
				  							<div class='col-sm-10'><p class='h-detail'>".htmlspecialchars($arr['toaddress'])."</p></div>
										</div>";
						if(!empty($arr['ccaddress'])){
						$bodytxt.="<div class='row'>
											<div class='col-sm-2'><label>Cc:</label></div>
				  							<div class='col-sm-10'><p class='h-detail'>".htmlspecialchars($arr['ccaddress'])."</p></div>
										</div>";
						}
						$bodytxt.="<div class='row'>
											<div class='col-sm-2'><label>Subject:</label></div>
				  							<div class='col-sm-10'><p class='h-detail'>".htmlspecialchars($arr['Subject'])."</p></div>
										</div>
										<div class='row'>
											<div class='col-sm-2'><label>Sent:</label></div>
				  							<div class='col-sm-10'><p class='h-detail'>".$arr['SentDate']."</p></div>
										</div>
									</div><hr>";




						$bodytxt.= '<div class="orignal-email-body" style="line-height: 22px; margin-top: 15px;">'.str_replace("[FirstName]", $firstname, $arr['TickleMailFollowContent']).'</div>';
			            $bodytxt = str_replace("[firstname]", $firstname, $bodytxt);
			            $bodytxt = str_replace("[signature]", $signature, $bodytxt);
			        }
				}
   			}
		return '<div class="viewEmail">'.$bodytxt.'</div>';
	}

		/*
	* Function : viewReplied 
	* Use : display reply email message on tickle 
	* Date : 29-06-19
	*/
	public function viewReplied()
	{
		global $db;
		$data = []; // FollowTickleTrainID
		$input = $this->tt_filter_arr($this->request);
		$trackID = $input['trackID'];

		$bodytxt = "";
		if(!empty($trackID) ){
			$query =  "select * from task_track_records where id=" . $trackID;
			$result = mysqli_query($db->conn,$query);
			if(mysqli_num_rows($result) > 0){
				$arr = mysqli_fetch_assoc($result);
	            $bodytxt = $arr['email_content'];
	        }
		}
		return '<div class="viewEmail">'.$bodytxt.'</div>';
	}

	/*
	* Function : viewEmail 
	* Use : display the detail of Activity
	* Date : 29-06-19
	*/
	public function viewOrignalEmail()
	{
		global $db;
		$data = []; 
		$add_comment = '';
		$input = $this->tt_filter_arr($this->request);
		$MailID = $input['MailID'];

		$query = "SELECT tickle.reminder_task, user_mail.Subject,user_mail.CustomSubject,user_mail.fromaddress,user_mail.toaddress,user_mail.ccaddress,user_mail.attachments,user_mail.RawPath,user_mail.MessageHtml,user_mail.Date,contact_list.FirstName, contact_list.LastName , contact_list.EmailID as Contact_EmailID,tickleuser.signature FROM `user_mail` JOIN contact_list ON contact_list.ContactID = user_mail.ContactID JOIN tickleuser ON  tickleuser.TickleID = user_mail.TickleID inner join tickle on (user_mail.TickleTitleID=tickle.TickleTrainID)  WHERE `MailID`=".$MailID;

		$result = mysqli_query($db->conn,$query);

		if(mysqli_num_rows($result) > 0){
			$arr = mysqli_fetch_assoc($result);

			$protect = protect($TickleID . "-" . $MailID);

			if($arr['reminder_task'] == 'Y'){
				$add_comment ="https://".$this->domain_name.Url_Create("addcomments","cptsk=".rawurlencode($protect)."&els=".rawurlencode(protect('yes')));
			}

			
			$comments = get_comments($MailID);
		//	$commentsss = str_replace("/upload-files/","https://client.tickletrain.com/upload-files/",$comments['comment']);
		//	echo '<pre>===';
		//	print_r($comments);
            $arr['Subject'] = (!empty($arr['CustomSubject'])) ? $arr['CustomSubject'] : $arr['Subject'] ;
			$arr['Subject'] = (!empty($arr['Subject'])?$arr['Subject']: $this->if_subject_empty);

			$Message_Header="<div class='email-header'>
								<div class='row'>
									<div class='col-sm-2'><label>From:</label></div>
		  							<div class='col-sm-10'><p class='h-detail'>".htmlspecialchars($arr['fromaddress'])."</p></div>
								</div>";
			$Message_Header.="<div class='row'>
								<div class='col-sm-2'><label>To:</label></div>
	  							<div class='col-sm-10'><p class='h-detail'>".htmlspecialchars($arr['toaddress'])."</p></div>
							</div>";
			if(!empty($arr['ccaddress'])){
			$Message_Header.="<div class='row'>
								<div class='col-sm-2'><label>Cc:</label></div>
	  							<div class='col-sm-10'><p class='h-detail'>".htmlspecialchars($arr['ccaddress'])."</p></div>
							</div>";
			}

			$Message_Header.="<div class='row'>
								<div class='col-sm-2'><label>Subject:</label></div>
	  							<div class='col-sm-10'><p class='h-detail'>".htmlspecialchars($arr['Subject'])."</p></div>
							</div>
							<div class='row'>
								<div class='col-sm-2'><label>Sent:</label></div>
	  							<div class='col-sm-10'><p class='h-detail'>".$arr['Date']."</p></div>
							</div><hr>";

			$Message_Header.= '<div class="orignal-email-body">'.$arr['MessageHtml'].'</div>';

			$Message_Header = str_replace("/mail/","https://client.tickletrain.com/mail/",$Message_Header);
			


			if(!empty($arr['attachments'])) {

				$Message_Header.="<div class='attachments' ><hr/><b>Attachments:</b>";
				$images = explode(',', $arr['attachments']);
				$basepath = preg_replace("/\.txt$/i", "/", $arr['RawPath']);
       			$basepath = str_replace('/var/www/vhosts/client.tickletrain.com/httpdocs', "", $basepath);
       			$Message_Header.="<ul class='thumbnail-ul' >";
		        foreach ($images as $img) {

		        	$file_ext=strtolower(end(explode('.',$img)));
				    $extensions= array("jpeg","jpg","png","gif");
					//$extensions= array("jpeg","jpg","png","gif","doc","docx","gif","odt","rtf","tex","txt","wpd");

		        	if(in_array($file_ext, $extensions)){
		        		$Message_Header.="<li><a href='https://client.tickletrain.com".$basepath.$img."'><img src='https://client.tickletrain.com".$basepath.$img."' /></a><span class='imag_nme'>".$img."</span></li>";
		        	}elseif($file_ext == 'pdf'){
						$Message_Header.="<li><a href='https://client.tickletrain.com".$basepath.$img."'><img src='https://client.tickletrain.com/images/Extension/icon-pdf.png' /></a><span class='imag_nme'>".$img."</span></li>";
		        	}elseif(($file_ext == 'doc') || ($file_ext == 'docx')){
						$Message_Header.="<li><a href='https://client.tickletrain.com".$basepath.$img."'><img src='https://client.tickletrain.com/images/Extension/icon-doc.png' /></a><span class='imag_nme'>".$img."</span></li>";
		        	}elseif($file_ext == 'txt'){
						$Message_Header.="<li><a href='https://client.tickletrain.com".$basepath.$img."'><img src='https://client.tickletrain.com/images/Extension/icon-txt.png' /></a><span class='imag_nme'>".$img."</span></li>";
		        	}elseif($file_ext == 'psd'){
						$RawmaildirPSD = "/var/www/vhosts/client.tickletrain.com/httpdocs".$basepath;
						//$Message_Header.="<li><a href='https://client.tickletrain.com".$basepath.$img."'><img src='https://client.tickletrain.com/images/Extension/icon-psd.png' /></a><span class='imag_nme'>".$img."</span></li>";
						$Message_Header.="<li><a href='https://client.tickletrain.com/psddownload.php?filePath=".urlencode($RawmaildirPSD)."&filePSD=".urlencode($img)."'><img src='https://client.tickletrain.com/images/Extension/icon-psd.png' /></a><span class='imag_nme'>".$img."</span></li>";
					}else{
		        		$Message_Header.="<li><a href='https://client.tickletrain.com".$basepath.$img."'><img src='https://client.tickletrain.com/images/Extension/icon-empty.png' /></a><span class='imag_nme'>".$img."</span></li>";
		        	}

		        }
		        $Message_Header.="</ul>";
		        $Message_Header.="<div class='comentshrsec'>";
			}

			return  json_encode(['body_content' => $Message_Header , 'title'=>$arr['Subject'] , 'comments' => $comments ,'add_comment' => $add_comment ]);

		}else{
			return   json_encode(['body_content' => 'Something went wrong' , 'title'=>'Error' ]);
		}
	}

	/*
	* Function : viewEmail 
	* Use : display the detail of Activity
	* Date : 29-06-19
	*/
	public function deleteActivity()
	{
		global $db;
		$input = $this->tt_filter_arr($this->request);
		$MailId =  $input['MailID'];
		$str = "";

		$query = "select MailID from task where TaskID=$MailId";
		$result = mysqli_query($db->conn,$query);
		
		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);
			$str =" OR task_id IN(select TaskID from task where MailID=".$row['MailID'].")";
		}
		
		$delete_test = "DELETE FROM task_track_records WHERE task_id IN(select TaskID from task where MailID=$MailId) OR task_id =$MailId ".$str ;
		$result =  mysqli_query($db->conn,$delete_test);
	}


    /*
	* Function : printLabels 
	* Use : Print tickletrain lables on emails 
	* Date : 03-06-19
	*/
	public function printLabels()
	{
		global $db;
		$data = [];
		$input = $this->tt_filter_arr($this->request);

		$query = "select distinct user_mail.MailID,user_mail.Subject,user_mail.toaddress from user_mail , task where task.MailID=user_mail.MailID and  user_mail.TickleID='" . $input['user_id'] . "' and task.Status='Y' and task.Approve='N' GROUP BY user_mail.Subject";
		$result =  mysqli_query($db->conn,$query);
		while ($row = mysqli_fetch_assoc($result))
		{
			$data[] = $row;
		}

		if(count($data)){
			$res = ['status' => 1 , 'data' =>  $data];
		}else{
			$res = ['status' => 0 , 'data' =>  [] ];
		}

		return json_encode($res);

	}

    
    /*
	* Function : update_profile 
	* Use : Updating users profile data 
	* Date : 06-06-19
	*/
	public function update_profile()
	{
		# code...
		global $db;
		$data = [];
		$error = false;
		$input = $this->tt_filter_arr($this->request);
		//parse_str($input['fromData'], $data);
		if(!empty($input['user_id'])){

			if(empty($input['FirstName'])) {
				$res['errors']['FirstName'] =  'FirstName is required.'; 
				$error =  true ; 
			}
			if(empty($input['LastName'])) {
				$res['errors']['LastName'] = 'LastName is required.'; 
				$error =  true ; 
			}

			if(!$error){
				$update_task = "update tickleuser set FirstName='".$input['FirstName']."' , LastName='".$input['LastName']."' where TickleID='" . $input['user_id'] . "'";
				mysqli_query($db->conn,$update_task);

				$d = [
					'FirstName' => $input['FirstName'],
					'LastName' => $input['LastName']
				];

				$res = ['status' => 1 , 'message' => 'Profile successfully updated.' , 'data' => $d ];
			}else{
				$res['status'] = 0;
			}

		}
		return json_encode($res);
	}



	/*
	* Function : replySettingUpdate 
	* Use : Updating user's delete campaign reply Setting 
	* Date : 02-07-19
	*/
	public function replySettingUpdate()
	{
		# code...
		global $db;
		$data = [];
		$error = false;
		$status = 0;
		$input = $this->tt_filter_arr($this->request);
		$colum = $input['filed'];
		if($input['value'] == 'true' &&  $colum == 'delete_all_campaign'){
			$status = 1;
		}
		if(!empty($input['value']) && $input['value'] != 'false' &&  $colum == 'enable_email_traking'){
			$status = 1;
		}
		//parse_str($input['fromData'], $data);
		if(!empty($input['user_id'])){
			$update_task = "update `tickleuser` set `$colum`='".$status."' where TickleID=" . $input['user_id'];
			mysqli_query($db->conn,$update_task);
		}
		return true;
	}

	/*
	* Function : getTickles 
	* Use : show dropdownd in Compose view gmail extension 
	* Date : 08-06-19
	*/
	public function getTickles()
	{
		global $db;
		$input = $this->tt_filter_arr($this->request);
		
		$tickles = 	 $this->getUserTickles($input['user_id']);

		$query = "select UserName from tickleuser  WHERE  TickleID=".$input['user_id'];
        $res = mysqli_query($db->conn,$query);
        $user = mysqli_fetch_assoc($res);

		$html = '';

		if(isset($input['MailID'])  && !empty($input['MailID']) && $input['view'] == 'thread' ) {
		 	$MailID = $input['MailID'];
			$sent= []; $TickleTitle = ""; $tickle_html="";
			$select = "SELECT task.TaskID,task.MailID, task.status ,user_mail.TickleTitle, task.TickleID, task.Pause, task.Approve , tickle.TApprove , ifnull(tickle.TApprove,'') as TTApprove, user_mail.Subject from task inner join user_mail on (task.MailID=user_mail.MailID) LEFT JOIN `ticklefollow` ON  task.FollowTickleTrainID = ticklefollow.FollowTickleTrainID inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) where task.MailID='$MailID' ORDER BY TaskInitiateDate ASC";  
			$tasks = $db->query_to_array($select);

			if(is_array($tasks) && count($tasks) > 0 ){
				
				foreach ($tasks as $task) {

					if($task['status'] ==  'S'){
						$sent[] = $task;  
					}
					$TickleTitle = $task['TickleTitle'];
				}



				foreach ($tasks as $task) {

						$TApprove = $task['TTApprove'];
				        $IsApproved = $task['Approve'];
				        $IsPaused = $task['Pause'];

					if($task['status'] !=  'S'){

						//print_r($task);

						 if (($TApprove == 'N' || $IsApproved == 'Y') && ($IsPaused != 'Y') ) {
				         	$status = 'Active';
				        }
						break;
					}
				}
				$delidate_tickles_opt = [];


				if(is_array($tickles) && count($tickles) > 0 ){

					$bcc =  $MailID.'+unsubscribe+'.$TickleTitle.'+'.$user['UserName'].'@tickletrain.com';

					$tickle_html.='<ul class="tickle-list-tt-in-thread_" >';
					foreach ($tickles as $tickle) {

						if($tickle['TickleName'] != $TickleTitle){
							if($tickle['reminder_task'] == 'N') {
								$tickle_html.='<li data-name="'.$tickle['TickleName'].'+'.$user['UserName'].'@tickletrain.com" data-bcc="'.$bcc.'">'.$tickle['TickleName'].' <img  class="pointer expend_tickle" id="'.$tickle['TickleTrainID'].'" src="https://client.tickletrain.com/images/Extension/maximize.svg"  width="15" /> </li>';
							}else{
								$delidate_tickles_opt[] =  array(
									'TickleTrainID' => $tickle['TickleTrainID'],
									'TickleName' => $tickle['TickleName'],
								);
							}
						}

					}

					if(!empty($delidate_tickles_opt)){
						foreach ($delidate_tickles_opt as $em) {
							$tickle_html.='<li  class="c-green" data-name="'.$em['TickleName'].'+'.$user['UserName'].'@tickletrain.com" data-bcc="'.$bcc.'">'.$em['TickleName'].' <img  class="pointer expend_tickle" id="'.$em['TickleTrainID'].'" src="https://client.tickletrain.com/images/Extension/maximize.svg"  width="15" /> </li>';
						}
					}

					$tickle_html.='</ul>';
				}

				$html = '<div class="tt-reply-compose-box aX">
							<div class="container" >
								<div class="row">
								<table>
									<tr>
										<th><span>Switch To:</span></th>  <td><div class="tt-switch-tickle">'.$tickle_html.'</div></td>
									</tr>
									<tr>
									    <th><span>Active:</span></th>
									    <td>
										    <span style="background: cadetblue; color: white;"> 
												<a class="tt-btn" >'.$TickleTitle.' </a>
											</span>
											<span class="float-left blue pointer mt-1"> 
												<a class="tt-pause-btn pointer"  data-status="'.(($status == "Active")?"N":"Y").'" data-id="'.$task['MailID'].'" >'.(($status == "Active")?"Pause":"Unpause").' </a> |
												<a class="tt-delete-btn pointer" data-id="'.$task['MailID'].'" >Delete</a> |
												<a class="tt-restart-btn pointer" data-id="'.$task['MailID'].'" data-name="resubscribe+'.$user['UserName'].'@tickletrain.com" >Restart</a>
											</span>
										</td>
									</tr>
								</table>
								</div>
							</div>
						</div>';
				$res = ['status' => 2 , 'data' => $tickles , 'html' => $html ];
				return json_encode($res);
			}
		}

		if(is_array($tickles) && count($tickles) > 0 ){

			$html.='<ul class="tickle-list-tt-in-compose__ aX" >';
			foreach ($tickles as $tickle) {

				if($tickle['reminder_task'] == 'N') {
					$html.='<li data-name="'.$tickle['TickleName'].'+'.$user['UserName'].'@tickletrain.com">'.$tickle['TickleName'].' <img  class="pointer expend_tickle" id="'.$tickle['TickleTrainID'].'" src="https://client.tickletrain.com/images/Extension/maximize.svg"  width="15" /></li>';
				}else{
					$delidate_tickles_opt[] =  array(
						'TickleTrainID' => $tickle['TickleTrainID'],
						'TickleName' => $tickle['TickleName'],
					);
				}

			}

			if(!empty($delidate_tickles_opt)){
				foreach ($delidate_tickles_opt as $em) {
					$html.='<li  class="c-green" data-name="'.$em['TickleName'].'+'.$user['UserName'].'@tickletrain.com" data-bcc="'.$bcc.'">'.$em['TickleName'].' <img  class="pointer expend_tickle" id="'.$em['TickleTrainID'].'" src="https://client.tickletrain.com/images/Extension/maximize.svg"  width="15" /> </li>';
				}
			}
					
			$html.='</ul>';


			$res = ['status' => 1 , 'data' => $tickles , 'html' => $html ];
		}else{
			$res = ['status' => 0 , 'message' => 'No Tickles Found.' ];
		}
		return json_encode($res);
	}

	/*
	* Function : getTickleFollow 
	* Use : get all follow tickles and display in gmail extension's popup  
	* Date : 08-06-19
	*/
	public function getTickleFollow()
	{
		# code...
		global $db;
		$input = $this->tt_filter_arr($this->request);
		$mselect = "select ticklefollow.*, concat(ticklefollow.DailyDaysFollow,' days, Send ',ticklefollow.EndAfterFollow,' times') as Schedule, count(files.FileID) as attaches from ticklefollow left outer join files on (ticklefollow.FollowTickleTrainID=files.FileParentID and files.FileContext='ticklefollow') where ticklefollow.TickleID='".$input['user_id']."' and  ticklefollow.TickleTrainID = '".$input['tickle_id']."' group by ticklefollow.FollowTickleTrainID order by ticklefollow.TickleTrainID, ticklefollow.FollowTickleTrainID";
		$follows = $db->query_to_array($mselect);
		$html= '';

		$query = "select  TickleTrainID ,TApprove, TickleName , concat(DailyDays,' days, Send ',EndAfter,' times') as Schedule from tickle where TickleTrainID='".$input['tickle_id']."'";
		$result =  mysqli_query($db->conn,$query);
		$tickle_exist = mysqli_fetch_assoc($result);

		if(is_array($tickle_exist)){

			if($tickle_exist['TApprove'] == 'Y'  ){
				$button = '<a href="#" class="pause-tickle" data-id="'.$tickle_exist['TickleTrainID'].'" data-type="tickle" title="UnPause this tickle"  > <img src="https://client.tickletrain.com/images/Extension/play.svg"  width="20" /> </a>';
			}else{
				$button = '<a href="#" class="unpause-tickle" data-id="'.$tickle_exist['TickleTrainID'].'" data-type="tickle" title="Pause this tickle"  > <img src="https://client.tickletrain.com/images/Extension/pause.svg"  width="20" /> </a>';
			}

			$html.='<tr>
						<td>#</td>
						<td> '.$tickle_exist['TickleName'].'</td>
						<td class="tt-bcc">'.$tickle_exist['TickleName'].'+'.$input['UserName'].'@tickletrain.com</td>
						<td  >'.$tickle_exist['Schedule'].'</td>
						<td class="jconfirm-buttons" style="float:none;">
						<a href="#" class="view-tickle" data-type="tickle" id="'.$tickle_exist['TickleTrainID'].'" title="View this tickle"> <img src="https://client.tickletrain.com/images/Extension/view.svg"  width="25" /> </a>
						<a href="#" class="select-tickle" data-id="'.$tickle_exist['TickleTrainID'].'" data-type="tickle" title="Edit this tickle"  > <img src="https://client.tickletrain.com/images/Extension/edit.svg"  width="20" /> </a>
						'.$button.'
						</td>
					</tr>';
		
			if(is_array($follows) && count($follows) > 0 ){

				foreach ($follows as $follow) {

					if($follow['TApprove'] == 'Y' ){
						$button = '<a href="#" class="pause-tickle" data-type="ticklefollow" data-id="'.$follow['FollowTickleTrainID'].'" title="UnPause this tickle" > <img src="https://client.tickletrain.com/images/Extension/play.svg"  width="20" /> </a>';				
					}else{
						$button = '<a href="#" class="unpause-tickle" data-type="ticklefollow" data-id="'.$follow['FollowTickleTrainID'].'" title="Pause this tickle" > <img src="https://client.tickletrain.com/images/Extension/pause.svg"  width="20" /> </a>';
					}

					$html.='<tr>
								<td>#</td>
								<td> '.$tickle_exist['TickleName'].'</td>
								<td class="tt-bcc" >'.$tickle_exist['TickleName'].'+'.$input['UserName'].'@tickletrain.com</td>
								<td>'.$follow['Schedule'].'</td>
								<td class="jconfirm-buttons" style="float:none;">
								<a href="#" class="view-tickle" data-type="ticklefollow" id="'.$follow['FollowTickleTrainID'].'" title="View this tickle" > <img src="https://client.tickletrain.com/images/Extension/view.svg"  width="25" /> </a>
								<a href="#" class="select-tickle" data-type="ticklefollow" data-id="'.$follow['FollowTickleTrainID'].'" title="Edit this tickle" > <img src="https://client.tickletrain.com/images/Extension/edit.svg"  width="20" /> </a>
								'.$button.'
								</td>
							</tr>';
				}
			}

			$res = ['status' => 1 , 'html' => $html ];

		}else{
				$res = ['status' => 0 , 'message' => 'No Tickles Found.' ];
		}

		return json_encode($res);
	}

	/*
	* Function : reviewEmail 
	* Use : display preview of single email   
	* Date : 10-06-19
	*/
	public function reviewEmail()
	{
		# code...
		global $db;
		$input = $this->tt_filter_arr($this->request);
		$html= '';
		
		if($input['tickle_type'] == 'tickle'){
		  $query = "select TickleMailContent from tickle where TickleTrainID='".$input['tickle_id']."'";
		}else{
		  $query = "select TickleMailFollowContent AS TickleMailContent  from ticklefollow  where FollowTickleTrainID='".$input['tickle_id']."'";
		}

		$result =  mysqli_query($db->conn,$query);
		$tickle_exist = mysqli_fetch_assoc($result);

		if(is_array($tickle_exist)){
					
			$res = ['status' => 1 , 'html' => $tickle_exist['TickleMailContent'] ];

		}else{
				$res = ['status' => 0 , 'html' => '<p>No Tickles Found.<p>' ];
		}
		return json_encode($res);

	}
	/*
	* Function : selectTikle 
	* Use : get tickle content and append to gmail Compose body  
	* Date : 10-06-19
	*/
	public function selectTikle()
	{
		# code...
		global $db;
		$input = $this->tt_filter_arr($this->request);
		$html= '';


		if($input['tickle_type'] == 'tickle'){
		  $query = "select TickleMailContent from tickle where TickleTrainID='".$input['tickle_id']."'";
		}else{
		  $query = "select TickleMailFollowContent AS TickleMailContent  from ticklefollow  where FollowTickleTrainID='".$input['tickle_id']."'";
		}
		$result =  mysqli_query($db->conn,$query);
		$tickle_exist = mysqli_fetch_assoc($result);
		if(is_array($tickle_exist)){
			$res = ['status' => 1 , 'html' => $tickle_exist['TickleMailContent'] ];

		}else{
				$res = ['status' => 0 , 'html' => '<p>No Tickles Found.<p>' ];
		}
		return json_encode($res);
	}

	/*
	* Function : saveContact() 
	* Use : save contact while mail sent from Gmail  
	* Date : 25-06-19
	*/
	public function saveContact()
	{
		global $db;
		$input = $this->tt_filter_arr($this->request);
		$html= '';

            //'BCC' : tickleLocalState.BCC,
            //'composeWindow' : tickleLocalState.composeWindow,
            //'MailID' : tickleLocalState.MailID,

		if(!empty($input['BCC']) && (!empty($input['composeWindow']) && $input['composeWindow'] == 'thread') && !empty($input['MailID']) ){
			$this->deleteCampaign();
		}

		if(!empty($input['user_id']) &&  !empty($input['EmailID']) && !empty($input['tickle']) ){

			$user_id = $input['user_id'];
			$name = $input['name'];
			$f_name = $name ;
			$l_name =  "";
			if(!empty($input['FirstName']) ||  !empty($input['LastName']) ){
					$f_name =  $input['FirstName'];
					$l_name =  $input['LastName'];
			}else{
				if(!empty($name)){
					$arr = explode(' ', $name);
					$f_name =  (isset($arr[0]))?$arr[0]:$name;
					$l_name =  (isset($arr[1]))?$arr[1]:'';
				}
			}

			$contact_email = strtolower($input['EmailID']);

			$tickle = $input['tickle'];

			$qur =  "select contact_list.*  from contact_list where contact_list.TickleID='$user_id' and contact_list.EmailID='$contact_email'";
			$result =  mysqli_query($db->conn,$qur);


			if(!empty($input['updated_follow_ids'])) {
				$updated_follow_ids = implode(',', $input['updated_follow_ids']);
				$query = "INSERT INTO `updated_follow_ids` (`updated_follow_ids`, `user_id`, `tickle`) VALUES ('$updated_follow_ids', '$user_id', '$tickle')";
				mysqli_query($db->conn,$query);
			}

			if(mysqli_num_rows($result) ==  0 ){
				$query = "INSERT INTO `contact_list` (`FirstName`,`LastName` , `EmailID`, `Status`, `TickleID`) VALUES ('$f_name', '$l_name', '$contact_email', 'Y', '$user_id')";
				mysqli_query($db->conn,$query);
				echo "contect saved ";
			}else{

				if(!empty($input['FirstName']) || !empty($input['LastName'])  ){
					$contact = mysqli_fetch_assoc($result);
					$query = "update contact_list set FirstName='".$f_name."' , LastName='".$l_name."'  where ContactID=".$contact['ContactID'];
					mysqli_query($db->conn,$query);
					echo "updated ";
				}else{
					echo "nothing to updated ";
				}
			}
	
		}
	}

	/*
	* Function : getUpcommingTickles() 
	* Use : get Upcomming Tickles fro extension page  
	* Date : 03-07-19
	*/
	public function getDashboard()
	{

		global $db;
		$input = $this->tt_filter_arr($this->request);
		$html= '';
		$tickleId =  $input['user_id'];
		$search_str = (isset($input['str']))?$input['str']:'';
 
        $limit =  25;
		$page =  (isset($input['page']))?$input['page']:0;
		$start =  $page*$limit;
	
	
	//echo '<pre>';
	//print_r($input);
	date_default_timezone_set($input['TimeZone']);

		$dates =  [
			'today' =>  date('Y-m-d'),
			'tomorrow' => date("Y-m-d", strtotime('tomorrow')),
			'week' =>  date("Y-m-d", strtotime("+7 day")),
			'anytime' =>  date("Y-m-d", strtotime("+1 year")),
		];
		
		$input['date'] = (isset($input['date']))?$input['date']:'today';
		$date = (isset($dates[$input['date']]))?$dates[$input['date']] : date('Y-m-d');

		$todaydate = date('Y-m-d');
		$tomorrowdate = date("Y-m-d", strtotime('tomorrow'));
		$weekdate = date("Y-m-d", strtotime("+7 day"));
		$today =  $tomorrow = $week = $anytime = '';

				switch($input['date']) {
				  case 'tomorrow':
				    // code block
				    $tomorrow = 'active';
				    break;
				  case 'week':
				    $week = 'active';
				 break;
				  case 'anytime':
				    $anytime = 'active';
				 break;
				  default:
				 	 $today = 'active';
				 // code block
				}

		// $fileds = "select * ";
		// $query = " from task where TickleID=".$input['user_id'];
		// $query.=" where TaskGMDate >= '$date' AND TaskGMDate <= '$date'";
		// if(isset($input['TimeZone']) ){
		// 	date_default_timezone_set('Asia/Kolkata');
		// }

		$tickle_id =  isset($input['tickle_id'])?$input['tickle_id']:'';
		$tickles =  $this->getUserTickles($tickleId);
		$delidate_tickles_opt = [];
		if(!isset($input['load_more'])) {

			$html = '<div class="tt-filter-2">
						<select class="ml-1 up-tickles" ><option value="" >Show all Tickles</option>';
						if(!empty($tickles)) {
							foreach ($tickles as $key => $tickle) {

								if($tickle['reminder_task'] == 'N'){
									$selected = ($tickle_id == $tickle['TickleTrainID']) ? 'selected="selected"' : '';
									$html.='<option value="'.$tickle['TickleTrainID'].'"  '.$selected.'>'.$tickle['TickleName'].'</option>';
								}else{
								   $delidate_tickles_opt[] =  array( 
								   								'TickleTrainID' => $tickle['TickleTrainID'],
								   								'TickleName' => $tickle['TickleName'],
								   							);
								}
							}

							if(!empty($delidate_tickles_opt)) {
								$html.='<optgroup label="Task Reminders">';
								foreach ($delidate_tickles_opt as $em) {
									$selected = ($tickle_id == $em['TickleTrainID']) ? 'selected="selected"' : '';
									$html.='<option value="'.$em['TickleTrainID'].'"  '.$selected.'>'.$em['TickleName'].'</option>';
								}
								$html.='</optgroup>';
							}

						}		
			$html.='</select> 
	         		<div class="search_input"><img src="https://client.tickletrain.com/images/Extension/searchicon.png"  width="15" > </div>
					</div>';

			$html.= '<div class="top-flders">
						<span data-tag="today" class="'.$today.'" >Today</span>
						<span data-tag="tomorrow" class="'.$tomorrow.'" > Tomorrow </span>
						<span class="'.$week.'" data-tag="week">This Week</span>
						<span class="'.$anytime.'" data-tag="anytime" >Anytime</span>
					</div>';
			$display = "display:none;";
		}
					
		$q_str = ""; 
		if (!empty($search_str)) {
			$display = "";
		    $search_str = addslashes(urldecode($search_str));
			$search_str2 = htmlspecialchars_decode($search_str);
		    $q_str.=" AND (FirstName like '%$search_str%' or LastName like '%$search_str%' or EmailID like '%$search_str%' or TickleName like '%$search_str%' or TickleMailContent like '%$search_str%' or Subject like '%$search_str%' or Subject like '%$search_str2%' or Message like '%$search_str%' or Message like '%$search_str2%')  ";
		}


				switch($input['date']) {
				  case 'tomorrow':
				    // code block
				    $select = "SELECT task.MailID,task.TaskID FROM task  INNER JOIN user_mail on (task.MailID=user_mail.MailID) INNER JOIN contact_list on (user_mail.ContactID=contact_list.ContactID) INNER JOIN tickle ON (task.TickleTrainID=tickle.TickleTrainID) WHERE task.TickleID='$tickleId' AND task.Status='Y' AND DATE(TaskInitiateDate) = '$date'  ".$q_str;
				    break;
				  case 'week':
				    $select = "SELECT task.MailID,task.TaskID FROM task  INNER JOIN user_mail on (task.MailID=user_mail.MailID) INNER JOIN contact_list on (user_mail.ContactID=contact_list.ContactID) INNER JOIN tickle ON (task.TickleTrainID=tickle.TickleTrainID) WHERE task.TickleID='$tickleId' AND task.Status='Y'  AND DATE(TaskInitiateDate) <= '$date' ".$q_str;
				    break;
				  case 'anytime':
				    $select = "SELECT task.MailID,task.TaskID FROM task  INNER JOIN user_mail on (task.MailID=user_mail.MailID) INNER JOIN contact_list on (user_mail.ContactID=contact_list.ContactID) INNER JOIN tickle ON (task.TickleTrainID=tickle.TickleTrainID) WHERE task.TickleID='$tickleId' AND task.Status='Y'  AND DATE(TaskInitiateDate) <= '$date'  ".$q_str;
				    break;
				  default:
				 	 $select = "SELECT task.MailID,task.TaskID FROM task  INNER JOIN user_mail on (task.MailID=user_mail.MailID) INNER JOIN contact_list on (user_mail.ContactID=contact_list.ContactID) INNER JOIN tickle ON (task.TickleTrainID=tickle.TickleTrainID) WHERE task.TickleID='$tickleId' AND task.Status='Y' AND DATE(TaskInitiateDate) = '$date'  ".$q_str;
				    // code block
				} 
				
		//$select = "SELECT task.MailID FROM task  INNER JOIN user_mail on (task.MailID=user_mail.MailID) INNER JOIN contact_list on (user_mail.ContactID=contact_list.ContactID) INNER JOIN tickle ON (task.TickleTrainID=tickle.TickleTrainID) WHERE task.TickleID='$tickleId' AND task.Status='Y' AND DATE(TaskGMDate) <= '$date'  ".$q_str;
	
		

		if(!empty($input['tickle_id']) ){
			$select.=" AND task.TickleTrainID='".$input['tickle_id']."'";
		}
		$select.=" GROUP BY MailID ORDER BY MIN(TaskGMDate) ASC ";

        
        $has_more_data = false;
        $total_q = mysqli_query($db->conn,$select);
        $tt =  mysqli_num_rows($total_q);

		if(mysqli_num_rows($total_q) > ($start+$limit)) {
			$has_more_data = true;
		}


		$select.="LIMIT ".$start.','.$limit;
	//echo $select; exit;
		$mails =$db->query_to_array($select);

		$already_has =  [];

		if(is_array($mails) && count($mails) > 0 ){

			$percentage = ((count($mails)*100)/$tt);

			if(!isset($input['load_more'])){

				$html.='<div class="progress" style="height:5px">
							    <div class="progress-bar"  id="progress-bar" style="width:'.$percentage.'%;height:10px;"></div>
							</div>';
				$html.='<div class="tt-uct-container" style="overflow: scroll !important;">
							<div class="input-group mb-1" style="'.$display.'" id="seach_input_holder">
							  <input type="text" id="search_input" class="form-control" name="search" autocomplete="off" placeholder="Search..." value="'.$search_str.'" >
							  <button id="clear_search" >X</button>
							</div>';
				$html.='<div class="scrl scroll2"><ul class="uct-ul mb-0">';
			}

			foreach ($mails as $mail) {
				 
				 if($input['date'] == 'today' || $input['date'] == 'tomorrow'){
				   $sel = "SELECT task.TaskID,tickle.EndAfter,tickle.NoWeekend, ticklefollow.EndAfterFollow, ticklefollow.NoWeekend as FollowNoWeekend ,task.FollowTickleTrainID,task.TaskInitiateDate ,task.MailID, task.TickleID, task.Pause, task.Approve , tickle.TApprove , task.TaskGMDate, user_mail.ContactID,user_mail.attachments,user_mail.RawPath, user_mail.Subject, user_mail.CustomSubject,user_mail.TickleTitle,contact_list.FirstName,contact_list.LastName, contact_list.EmailID, tickle.TickleName, ifnull(tickle.TApprove,'') as TTApprove, ifnull(ticklefollow.TApprove,'') as FollowTApprove, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='Y' AND DATE(TaskInitiateDate) >= '$date' and  task.MailID=".$mail['MailID'];
				 }else{
					$sel = "SELECT task.TaskID,tickle.EndAfter,tickle.NoWeekend, ticklefollow.EndAfterFollow, ticklefollow.NoWeekend as FollowNoWeekend ,task.FollowTickleTrainID,task.TaskInitiateDate ,task.MailID, task.TickleID, task.Pause, task.Approve , tickle.TApprove , task.TaskGMDate, user_mail.ContactID,user_mail.attachments,user_mail.RawPath, user_mail.Subject, user_mail.CustomSubject,user_mail.TickleTitle,contact_list.FirstName,contact_list.LastName, contact_list.EmailID, tickle.TickleName, ifnull(tickle.TApprove,'') as TTApprove, ifnull(ticklefollow.TApprove,'') as FollowTApprove, date_format(task.TaskInitiateDate,'%Y-%m-%d %H:%i:%s') as TaskDateTime from task inner join user_mail on (task.MailID=user_mail.MailID) inner join contact_list on (user_mail.ContactID=contact_list.ContactID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left outer join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TickleID='$tickleId' and task.Status='Y' and task.MailID=".$mail['MailID'];
				 }
					$sel.=" ORDER BY TaskInitiateDate ASC,TaskID ASC";
					$tasks = $db->query_to_array($sel);
					
					//echo $sel;
					// echo '<pre>'; print_r($task);
					
					$qr= "SELECT * FROM task WHERE Status!='Y' AND MailID=".$mail['MailID'];
					$nr = mysqli_query($db->conn,$qr);
					$sent_emails = mysqli_num_rows($nr);

					$total =  (count($tasks)+$sent_emails);

					foreach ($tasks as $k => $task) {
						if(in_array($task['MailID'], $already_has)){
							$child = 'child_li' ;
						}else{
							$already_has[] =  $task['MailID'];
							$child = 'parent_li';

							$qr = "select * from task_track_records where mail_id= '".$task['MailID']."' ";
							$n =  mysqli_query($db->conn,$qr);
							if(mysqli_num_rows($n) >  0 ){
								$task['has_notifications'] = true; 
							}
	 						mysqli_free_result($n);

						}
						// echo '<pre>'; print_r($task);
						$stage =  ($k+$sent_emails);
						$html.=$this->get_li($task,$input,$child,$total,$stage);
					}
			}

			if(!isset($input['load_more'])){
				$html.='</ul>';

					if($has_more_data){
						
						$html.='<div class="text-center"> <a href="#" class="badge badge-warning load_more" data-page="'.($page+1).'">Load More</a> </div>';
					}
				$html.='</div></div>';
			}

			$res = ['status' => 1 ,'query' => $select, 'html' => $html , 'page'=> ($page+1) , 'has_more_data' => $has_more_data , 'percentage' => $percentage ];

		}else{
		$html.= '<div class="tt-uct-container" style="overflow: scroll !important;">
							<div class="autocomplete input-group mb-1" style="border-bottom: 1px solid #dad9d9;" id="seach_input_holder">
							  <input type="text" class="form-control" id="search_input" autocomplete="off" name="search" placeholder="Search..." value="'.$search_str.'" >
							  <button id="clear_search" >X</button>
							</div>
					<div class="text-center">
						<p> <b>No Tickle Found </b></p>
					</div>
				</div>';
				$res = ['status' => 0 , 'html' => $html ];
		}

		return json_encode($res);
	}

	/*
	* Function : campaignList() 
	* Use : list of all active campaign(main_script.js)
	* Date : 04-03-2020
	*/
	public function campaignList(){
		global $db;
		$input = $this->tt_filter_arr($this->request);
		$TickleID =  $input['user_id'];
		$newtask = array();
		if(!empty($TickleID)){
			$select = "SELECT user_mail.Subject , user_mail.CustomSubject FROM task  INNER JOIN user_mail on (task.MailID=user_mail.MailID) INNER JOIN contact_list on (user_mail.ContactID=contact_list.ContactID) INNER JOIN tickle ON (task.TickleTrainID=tickle.TickleTrainID) WHERE task.TickleID='$TickleID' AND task.Status='Y' GROUP BY task.MailID ORDER BY Subject ASC";
			$tasks = $db->query_to_array($select);
			foreach($tasks as $newlist){
				$subject = $this->removeChar($newlist['Subject']);
				$custmsubject = $this->removeChar($newlist['CustomSubject']);
				$newtask[] = array('Subject'=>$subject,
									'CustomSubject'=>$custmsubject);
			}
			return json_encode(['status'=> 1 , 'data' => $newtask]);

		}else{
			return json_encode(['status'=> 0 , 'message' => 'user not found']);
		}
	}


	/*
	* Function : getCampaignDetail() 
	* Use : get Campaign detail for showing click on Email lable
	* Date : 16-07-19
	*/
	public function getCampaignDetail()
	{
		global $db;
		$input = $this->tt_filter_arr($this->request);
		$MailID = $input['MailID'];
		$sent= []; $TickleTitle = "";
		$status =  'Paused';
		$select = "SELECT task.TaskID,task.MailID,task.TaskInitiateDate, task.status ,user_mail.TickleTitle, task.TickleID, task.Pause, task.Approve , tickle.TApprove , ifnull(tickle.TApprove,'') as TTApprove, user_mail.Subject from task inner join user_mail on (task.MailID=user_mail.MailID) LEFT JOIN `ticklefollow` ON  task.FollowTickleTrainID = ticklefollow.FollowTickleTrainID inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) where task.MailID='$MailID' ORDER BY TaskInitiateDate ASC, TaskID ASC";  
		
		$tasks = $db->query_to_array($select);

		if(is_array($tasks) && count($tasks) > 0 ){

			foreach ($tasks as $task) {

				if($task['status'] ==  'S'){
					$sent[] = $task;  
				}
				$TickleTitle = $task['TickleTitle'];
			}
		

			foreach ($tasks as $task) {

				$TApprove = $task['TTApprove'];
		        $IsApproved = $task['Approve'];
		        $IsPaused = $task['Pause'];

				if($task['status'] !=  'S'){

					if (($TApprove == 'N' || $IsApproved == 'Y') && ($IsPaused != 'Y') ) {
				         	$status = 'Active';
				    }
					break;
				}
			}


				
			$html = '<div class="tt-capm-detail-box">
						<div class="row"><span>Tickle : </span><span class="ml-3">'.$TickleTitle.'</span></div>
						<div class="row"><span>Status :</span><span class="ml-3">'.$status.'</span></div>
						<div class="row"><span>Stage  :</span><span class="ml-3">'. count($sent).' of '.count($tasks).'</span></div>
						<div class="row">
							<span> 
								<a class="tt-btn tt-pause-btn"  data-status="'.(($status == "Active")?"N":"Y").'" data-id="'.$task['MailID'].'" >'.(($status == "Active")?"Pause":"Unpause").' </a>
							</span>
							<span>
								<a class="tt-btn tt-delete-btn" data-id="'.$task['MailID'].'" >Delete</a>
							</span>
							<!--span>
								<a class="tt-btn tt-restart-btn" data-id="'.$task['MailID'].'" >Restart</a>
							</span-->
						</div>
					</div>';
			return $html;
		}else{
			return "<div class='error-box'> Unable to find the details. Please refresh the page. </div>";
		}
	}

	/*
	* Function : deleteCampaign() 
	* Use : deleteing  Campaign from extension
	* Date : 16-07-19
	*/
	public function deleteCampaign()
	{
		global $db;
		$input = $this->tt_filter_arr($this->request);
		$MailID = $input['MailID'];
		$user_id = $input['user_id'];

		if(isset($input['MailID'])){
			$this->delete_mail_and_tasks($input['MailID']);
		}else if($input['TaskID']){
			$this->delete_task($input['TaskID']);
		}
		return true;
	}


	/*
	* Function : updateCampaign() 
	* Use : update  Campaign status etc. from extension
	* Date : 16-07-19
	*/
	public function updateCampaign(Type $var = null)
	{
		global $db;
		$input = $this->tt_filter_arr($this->request);
		if(isset($input['MailID'])){
			$str = " and MailID=".$input['MailID'] ." and status!='S' ORDER BY TaskGMDate ASC LIMIT 1";
		}
		if(isset($input['TaskID'])){
			$str = " and TaskID=".$input['TaskID'];
		}

		$user_id = $input['user_id'];
		$coulmns = $input['fileds'];
		$values = $input['values'];
		$set_string = "" ;
		foreach ($coulmns as $key => $coulmn) {
			$set_string[$key]="`$coulmn`='".$values[$key]."'";
		}

		if(!empty($input['user_id'])){
			 $update_task = "update `task` set ".implode(',', $set_string)."  where TickleID=" . $user_id . " ".$str;
			mysqli_query($db->conn,$update_task);
		}
		return true;
		# code...
	}


	/*
	* Function : getMailactivity() 
	* Use : showing activity detail like when open and when client reply on the  Campaign.
	* Date : 15-08-19
	*/
	public function getMailactivity()
	{
		global $db;
		$input = $this->tt_filter_arr($this->request);
		$MailID =  $input['MailID'];

		$query = "select task_track_records.id,tickleuser.TimeZone ,TaskGMDate ,request_count ,created_at,task_id,mail_id,request_time,task_track_records.type,task.TaskID,task.Status from task_track_records LEFT JOIN task ON (task.TaskID = task_track_records.task_id) JOIN user_mail ON (user_mail.MailID = task_track_records.mail_id) JOIN tickleuser ON tickleuser.TickleID = user_mail.TickleID where task_track_records.mail_id =$MailID ORDER BY task_track_records.created_at DESC , task_track_records.task_id ASC";

		$result = mysqli_query($db->conn,$query);
		$index = 0;
		$Total = 0;

		$taskids = [];
		$arr = [];
		$q = "select TaskID,Status,TaskGMDate from task where MailID=".$MailID. " ORDER BY TaskInitiateDate ASC";
		$r = mysqli_query($db->conn,$q);
		while($row_ = mysqli_fetch_assoc($r)){
			$taskids[] = $row_;
			if($row_['Status'] == 'S'){
				$Total++;
			}
			$all_ids[] = $row_['TaskID'];
		}
		//echo $Total;

		// echo "<pre>";
		// print_r($all_ids);
		// echo "</pre>";


		$i = 0;
		if(mysqli_num_rows($result) > 0){

			if(isset($input['user_id'])){
				$list_html='<ul class="timeline">';
			}else{
				$list_html='<ul class="notification-ul">';
			}

			$pre = 0 ;

				while($row = mysqli_fetch_assoc($result)){

					$link = "";
					$count = "";
					$class =  "tt-view-email";
					if($row['type'] == 'reply_receved' ){
					  $tyle = "Replied";
					  $class =  "tt-view-reply-email";
					  $date = getlocaltime(date('Y-m-d h:i a',($row['request_time']+(60*2))), $row['TimeZone']);
					  $date = date('D m/d/y h:i a',strtotime($date));
					}else{
					  $tyle = "Opened";
					  $date = getlocaltime($row['created_at'], $row['TimeZone']);
					  $date = date('D m/d/y h:i a',strtotime($date));
					}
					
					if($row['Status'] == 'S'){

						if($row['TaskID'] != $pre ){
							$pre = $row['TaskID'];
							$Total--;
						}
						
						// echo $row['task_id']." -";
						$inx = array_search($row['task_id'], $all_ids);
						// echo "<br>";

						if($inx >= 0){
							$inx =  $inx+1;
						}else{
							$inx = 0;
						}


						$list_html.='<li>
									<div class="pr-1 text-left pointer '.$class.'" data-id="'.$row['id'].'" >'.$tyle.' '.$count.' '.$link.'</div>
									<small class="text-muted">'.$date.'  <span class="float-right pr-1"> Stage '.$inx.'</span> </small>
								</li>';

					}else{
						$list_html.='<li>
									<div class="pr-1 text-left pointer '.$class.'" data-id="'.$row['id'].'" >'.$tyle.' '.$count.' '.$link.'</div>
									<small class="text-muted">'.$date.'  <span class="float-right pr-1"> Stage 0 </span> </small></li>';
					}

				}
		$list_html.="</ul>";
		}
		return  $list_html;
	}


	/*
	* Function : updateSchedule() 
	* Use : updating schedule of perticular email From Dashboard.
	* Date : 15-08-19
	*/
	public function updateSchedule()
	{
		global $db;
		$input = $this->tt_filter_arr($this->request);
		$TaskID =  $input['TaskID'];
		$query = "select * from task where status='Y' and TaskID=".$TaskID;
		$result = mysqli_query($db->conn,$query);

		if(mysqli_num_rows($result) > 0){

				$task =  mysqli_fetch_assoc($result);
				$MailID =  $task['MailID'];

			// update contact name 
				if(isset($input['f_name']) && isset($input['l_name'])){
					$f_name= $input['f_name'];
		      		$l_name= $input['l_name'];
					$qu= "select ContactID from user_mail where MailID=".$MailID ;
					$re = mysqli_query($db->conn,$qu);
					$mail =  mysqli_fetch_assoc($re);
					if(!empty($mail)){
						$que = "update contact_list set FirstName='".$f_name."' , LastName='".$l_name."'  where ContactID=".$mail['ContactID'];
						mysqli_query($db->conn,$que);
					}
				}
			////


			if(isset($input['d'])){

				$input['d'] = strtoupper($input['d']);

				switch (strtoupper($input['d'])) {
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
				    $iday = (date_default_timezone_get() != $task['TimeZone'] ) ? getlocaltime($d, $task['TimeZone']) : $d;
				}else{
					$d = date("Y-m-d H:i:s", $date);
				    $iday = (date_default_timezone_get() != $task['TimeZone'] ) ? getlocaltime($d, $task['TimeZone']) : $d;
				    $arr = explode(' ', $iday);
              		$iday = $arr[0]." ".date('H:i:s',strtotime($task['TaskInitiateDate']));
				}

			}else{

				$date_arr =  explode(' ', $input['date']);
				$d_ = explode('-',$date_arr[0]);
				$t_ = $date_arr[1];
				$am_pm = $date_arr[2];
				$iday =  $d_[2]."-".$d_[0]."-".$d_[1]." ".$t_.' '.$am_pm;
				$iday = date("Y-m-d H:i:s", strtotime($iday));
	
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
			echo $update_task = "update task set TaskGMDate='".$nday."' , TaskInitiateDate='".$iday."'  where TaskID='" . $task['TaskID'] . "'";
			mysqli_query($db->conn,$update_task);

            // update other tickles too 
			$sel = "SELECT task.TaskID,task.TickleID,task.TimeZone,task.FollowTickleTrainID,tickle.NoWeekend,task.TaskInitiateDate,task.TaskGMDate,tickle.TickleName,tickle.DailyDays,ticklefollow.DailyDaysFollow,ticklefollow.NoWeekend as FollowNoWeekend from task inner join user_mail on (task.MailID=user_mail.MailID) inner join tickle on (task.TickleTrainID=tickle.TickleTrainID) left join ticklefollow on (task.FollowTickleTrainID=ticklefollow.FollowTickleTrainID) where task.TaskInitiateDate > '".$task['TaskInitiateDate']."' and task.Status='Y' and task.TaskID!=".$task['TaskID']." and task.MailID=".$MailID;
				$sel.=" ORDER BY TaskInitiateDate ASC,TaskID ASC";
			$tasks = $db->query_to_array($sel);
		
			foreach ($tasks as $tsk) {
				// echo "<br>";
				// echo $tsk['TaskID'];
				// echo "<br>";
				
				
			   // $days = (!empty($tsk['DailyDays'])) ? $tsk['DailyDays'] :  $tsk['DailyDaysFollow'];
			   
			   if($tsk['FollowTickleTrainID'] == 0 && !empty($tsk['DailyDays']))
					$days = $tsk['DailyDays'];
				else
					$days = $tsk['DailyDaysFollow'];
				
				
				$iday = date("Y-m-d",strtotime('+'.$days.' days', strtotime($iday)));
				//echo $iday  = getlocaltime($iday, $tsk['TimeZone'],'Y-m-d');
				//echo "<br>";
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
					
					//echo "update task set TaskGMDate='".$nday."' , TaskInitiateDate='".$iday."'  where TaskID='" . $tsk['TaskID'] . "'";
				
				}

				$nday  = getgmdate($iday , $tsk['TimeZone']);
				$update_task = "update task set TaskGMDate='".$nday."' , TaskInitiateDate='".$iday."'  where TaskID='" . $tsk['TaskID'] . "'";
				mysqli_query($db->conn,$update_task);
			}

			return json_encode(['status'=> 1 , 'message' => 'updated']);
		}else{
			return json_encode(['status'=> 0 , 'message' => 'not found']);
		}
	}


	/*
	* Function : viewAllEmailPreview() 
	* Use : preview all emails in poupup slider .
	* Date : 15-08-19
	*/

	public function viewAllEmailPreview()
	{
		global $db;
		$input = $this->tt_filter_arr($this->request);
		$TaskID =  $input['TaskID'];
		$MailID =  $input['MailID'];
		$bodytxt = "";
		$query = "SELECT task.FollowTickleTrainID, task.TaskID, task.TaskInitiateDate,  task.Pause, task.Approve , tickle.TApprove , ifnull(tickle.TApprove,'') as TTApprove, tickle.TickleMailContent,tickle.reminder_task,tickle.NoWeekend,ticklefollow.TickleMailFollowContent, contact_list.FirstName, contact_list.LastName, contact_list.EmailID as Contact_EmailID , tickleuser.signature, user_mail.Subject, user_mail.fromaddress,user_mail.ccaddress,user_mail.Date FROM `task` LEFT JOIN `ticklefollow` ON  task.FollowTickleTrainID = ticklefollow.FollowTickleTrainID  JOIN user_mail ON user_mail.MailID = task.MailID JOIN contact_list ON contact_list.ContactID = user_mail.ContactID JOIN tickleuser ON  tickleuser.TickleID = user_mail.TickleID JOIN tickle ON tickle.TickleTrainID = task.TickleTrainID  WHERE  task.status='Y' and task.MailID=".$MailID." ORDER BY task.TaskInitiateDate ASC";

		$result = mysqli_query($db->conn,$query);
	
		if(mysqli_num_rows($result) > 0){

			$tasks = $db->query_to_array($query);
			$total =  count($tasks);

			$bodytxt.='<div class="preview-items">' ;
			$i = 0;
			$initialSlide = 0;
			$firstname = "";
			$lastname = "";
			$minDate =  "";
            $body='<div class="slider-preview">';
			foreach ($tasks as $key =>  $task) {  
			

				if($TaskID == $task['TaskID']){

					$initialSlide =  $i;
					$stag_txt =  'Stage ' .($i+1). ' of '.$total;
					$firstname = $task['FirstName'];
					$lastname = $task['LastName'];
					$crunt_date =  '<span id="s_date"  > '.date('l m/d/y, h:i a',strtotime($task['TaskInitiateDate'])) . '</span><input type="text" value="'.date('m-d-Y h:i A',strtotime($task['TaskInitiateDate'])).'" id="tt-date" class="form-control" style="display:none;" readonly> <img id="date_edit_icon" src="https://client.tickletrain.com/images/Extension/edit.svg"  width="15" > <img id="check-mark" src="https://client.tickletrain.com/images/Extension/clear.svg" style="display:none"  width="15" >';

					if($key != 0){
						$minDate = date('F d, Y H:i:s',strtotime($tasks[$key-1]['TaskInitiateDate']));
					}
					
				}
				
				$TApprove = $task['TTApprove'];
				$IsApproved = $task['Approve'];
				$IsPaused = $task['Pause'];

				$txt= ($task['FollowTickleTrainID'] == 0 )?$task['TickleMailContent']:$task['TickleMailFollowContent'];
			
				$name = "";
				$task['FirstName'] = str_replace("'","",$task['FirstName']);
            	$task['LastName'] = str_replace("'","",$task['LastName']);
				$firstname =  (!empty($task['FirstName']))?$task['FirstName']:$task['LastName'];
				$signature =  '<div class="signature">'.$task['signature'].'</div>';
				$txt = str_replace("[FirstName]", $firstname, $txt);
	            $txt = str_replace("[firstname]", $firstname, $txt);
	            $txt = str_replace("Hi ,",'Hi,', $txt);
	            $txt = str_replace("[signature]", $signature, $txt);
	            $txt = str_replace("/upload-files", "https://client.tickletrain.com/upload-files", $txt);

	            $task['Subject'] = (!empty($task['Subject'])?$task['Subject']: $this->if_subject_empty);
	            
				$Message_Header="<div class='email-header'>
									<div class='row'>
										<div class='col-sm-2'><label>From:</label></div>
			  							<div class='col-sm-10'><p class='h-detail'>".htmlspecialchars($task['fromaddress'])."</p></div>
									</div>";

				$Message_Header.="<div class='row'>
									<div class='col-sm-2'><label>To:</label></div>
		  							<div class='col-sm-10'><p class='h-detail'>".htmlspecialchars($task['Contact_EmailID'])."</p></div>
								</div>";

				if(!empty($task['ccaddress']) && ($task['reminder_task'] != 'Y')){
				$Message_Header.="<div class='row'>
									<div class='col-sm-2'><label>Cc:</label></div>
		  							<div class='col-sm-10'><p class='h-detail'>".htmlspecialchars($task['ccaddress'])."</p></div>
								</div>";
				}

				$Message_Header.="<div class='row'>
									<div class='col-sm-2'><label>Subject:</label></div>
		  							<div class='col-sm-10'><p class='h-detail'>".htmlspecialchars($task['Subject'])."</p></div>
								</div>
								<div class='row'>
									<div class='col-sm-2'><label>Sent:</label></div>
		  							<div class='col-sm-10'><p class='h-detail'>".$task['TaskInitiateDate']."</p></div>
								</div>
							</div><hr>";


				$body.='<div class="p-item">'.$Message_Header.trim($txt).'</div>';
				$i++;
				$dates[] = [
							'task_id' => $task['TaskID'], 
							'TaskInitiateDate' =>  date('l m/d/y, h:i a',strtotime($task['TaskInitiateDate'])),
							'date' =>  date('m-d-Y h:i A',strtotime($task['TaskInitiateDate'])),
							'date2' => date('F d, Y H:i:s',strtotime($task['TaskInitiateDate']))
						];
			}
			$body.='</div>';

				//$bodytxt.= '<textarea class="p-txtarea" >'.$txt.'</textarea>';
				$bodytxt.= '<div class="email-body scroll2">'.$body.'</div>';
				$bodytxt.='<div class="reshchedule">
				            <p class="mb-0" > <button class="prev" > <span>&#10140;</span> </button>  <button class="next" > <span>&#10140;</span> </button>  </p>
							<h5> Settings   <small class="float-right text-medium stage-txt">'.$stag_txt.'</small></h5>
								<table>
								<tr> 
									<td style="width: 148px;"> <span>Stage: </span> </td> 
									<td class="stages">
									</td>
								</tr>
								<tr> 
									<td> Send Date: </td> 
									<td>'.$crunt_date.'</td> 
								</tr>
							
							   
							   </tr>
							   
								<tr>
									<td>FirstName: </td> 
									<td><input type="text" class="form-control" value="'.$firstname.'" id="f_name" placeholder="Please Enter FirstName" > </td> 
								</tr>
								<tr>
									<td>LastName: </td> 
									<td><input type="text" value="'.$lastname.'" class="form-control" id="l_name" placeholder="Please Enter LastName" >   </td> 
								</tr>

								</table>
							</div>';
			$bodytxt.'</div>';

			$res =  ['status' => 1 , 'data' => $bodytxt , 'initialSlide' => ($initialSlide) ,  'dates' => $dates ,'minDate' => $minDate ];
		}else{
			$res =  ['status' => 0 , 'data' => 'not found'];
		}
		return json_encode($res);
	}


	/*
	* Function : ticklePreview() 
	* Use : preview all emails in poupup slider .
	* Date : 15-08-19
	*/
	public function ticklePreview()
	{
		global $db;
		$input = $this->tt_filter_arr($this->request);
		
		$MailID = $input['MailID'];
		
		$TickleTrainID =  $input['TickleTrainID'];
		$user_id =  $input['UserId'];
		
		$query = "SELECT tickleuser.signature,tickle.DailyDays,tickle.TickleTime,tickle.TApprove,tickle.TickleTrainID,tickle.TickleName,tickle.TickleMailContent FROM tickle JOIN tickleuser ON  tickleuser.TickleID = tickle.TickleID where tickle.TickleID=".$user_id . " AND tickle.TickleTrainID='".$TickleTrainID."' group by TickleTrainID";
		$result =  mysqli_query($db->conn,$query);
	
		$f_name = "";
		$l_name = "";

		if(isset($input['contact_emailAddress']) && !empty($input['contact_emailAddress'])){
			$contact = $this->getContact($user_id,$input['contact_emailAddress']);
			if(count($contact)){
				$f_name = str_replace("'","",$contact['FirstName']);
				$l_name = str_replace("'","",$contact['LastName']);
			}elseif(isset($input['contact_name']) && !empty($input['contact_name'])){
				$f_name = $input['contact_name'];
				$getnameall = explode(' ',$f_name);
				$f_name = $getnameall[0];
				$l_name = end($getnameall);
			}
		}

		if(mysqli_num_rows($result) > 0){
			$tickle =  mysqli_fetch_assoc($result);

			$mselect = "select ticklefollow.*, concat(ticklefollow.DailyDaysFollow,' days, Send ',ticklefollow.EndAfterFollow,' times') as Schedule, count(files.FileID) as attaches from ticklefollow left outer join files on (ticklefollow.FollowTickleTrainID=files.FileParentID and files.FileContext='ticklefollow') where ticklefollow.TickleID=$user_id and  ticklefollow.TickleTrainID='$TickleTrainID' group by ticklefollow.FollowTickleTrainID order by ticklefollow.TickleTrainID, ticklefollow.FollowTickleTrainID";
			$follows = $db->query_to_array($mselect);


			$follow_ids = ['empty'];
			$bodytxt='<div class="preview-items">' ;
			$total = count($follows)+1;
			$stag_txt =  'Stage 1 of '.$total;
			$list_html = "";
			for ($j=1; $j <= $total ; $j++) { 
				$list_html.= "<span>".$j."</span>";
			}

			$signature =  $tickle['signature'];
			$tickle['TickleMailContent'] = $this->replace_content($tickle['TickleMailContent'],$f_name,$signature);

			if($tickle['DailyDays'] == 0){
	            // $NoWeekend = 'N';
	            // $now = time();
	            // $one_minute = $now + (1 * 60);
	            // $task_TickleTimeFollow = date('H:i:s', $one_minute);
	            $start_time_txt =  "Now";
	        }else{
	            $start_time_txt =  "After ".$tickle['DailyDays'].' days at '.date('h:s a',strtotime($tickle['TickleTime']));
	        }


            $body='<div class="tickle-preview">';
			$body.='<div class="p-item">'.trim($tickle['TickleMailContent']).'</div>';

			foreach ($follows as $follow) {
				$follow_ids[]= $follow['FollowTickleTrainID'];
				$txt = $follow['TickleMailFollowContent'];
				$txt = $this->replace_content($txt,$f_name,$signature);
				$body.='<div class="p-item">'.trim($txt).'</div>';
				
				if($tickle['TApprove'] == 'N' ){
						$btnpause = '<span class="pusebtntext"><a href="javascript:void;" id="tnplusss" style="cursor: pointer;text-decoration: underline !important;"  class="tt-btn1 tt-pause-btn-new"  data-status="N" data-id="'.$MailID.'" > Unpaused <img src="https://client.tickletrain.com/images/Extension/pause.svg"  width="20" /> </a></span>';				
					}else{
						$btnpause = '<span class="pusebtntext"><a href="javascript:void;" id="tnplusss" style="cursor: pointer;text-decoration: underline !important;" class="tt-btn1 tt-pause-btn-new"  data-status="Y" data-id="'.$MailID.'" > Paused <img src="https://client.tickletrain.com/images/Extension/play.svg"  width="20" /> </a></span>';
					}
					
			}
			$body.='</div>';


				
				
				
				//$bodytxt.= '<textarea class="p-txtarea" >'.$txt.'</textarea>';
				$bodytxt.= '<div class="email-body scroll2">'.$body.'</div>';
				$bodytxt.='<div class="reshchedule">
				            <p class="mb-0" > <button class="prev" > <span>&#10140;</span> </button>  <button class="next" > <span>&#10140;</span>  </button>  </p>
							<h5> Settings  <small class="float-right text-medium stage-txt">'.$stag_txt.'</small></h5>
								<table id="previewtableee">
								<tr>
									<td> <span>Drag and drop to reorder:</span> </td> 
									<td class="stages">
									'.$list_html.'
									</td>
								</tr>
								<tr>
								 <td><span> Start Date: </span></td>
								 <td><span class="time-msg pr-1">'.$start_time_txt.'</span><sup><a href="#" class="change-shchedule"><img src="'.$this->imagePath.'/edit.svg"  width="15" /></a><a href="#" class="reset-shchedule" data-txt="'.$start_time_txt.'" >reset</a></sup>
								<div class="tickle-stage">
									<span>N</span>
									<span>1H</span>
									<span>2H</span>
									<span>3H</span>
									<span>1D</span>
									<span>2D</span>
									<span>3D</span>
									<span>1W</span>
									<span>2W</span>
									<span>1M</span>
									<i><img src="'.$this->imagePath.'/calendar.svg" class="pointer" width="20"  > </i>
								</div>
								 <span> <input type="text"  id="st-date" readonly> </span></td>
								</tr>
								
								
								
								<tr>
									<td>Deliver Status:</td>
									<td>'.$btnpause.'
									<input type="hidden" id="getdeliverstatus" value="">
									</td>
	
								<tr>
									<td>FirstName: </td> 
									<td><input type="text" id="f_name" class="form-control" placeholder="Please Enter FirstName" value="'.$f_name.'" > </td> 
								</tr>
								<tr>
									<td>LastName: </td> 
									<td><input type="text" value="'.$l_name.'" id="l_name" placeholder="Please Enter LastName" class="form-control"  >  </td> 
								</tr>
								</table>
							</div>';
			$bodytxt.'</div>';

			$res =  ['status' => 1 , 'data' => $bodytxt , 'follow_ids' => $follow_ids ];
		}
		return json_encode($res);
		# code...
	}

	public function isWeekend($date) {
	    return (date('N', strtotime($date)) >= 6);
	}
	


	public function save_comment()
	{
		global $db;
		$input = $this->tt_filter_arr($this->request);
		
		if(!empty($input['TaskID']) && !empty($input['TickleID'])){

			$query = "select task.*,user_mail.attachments,user_mail.RawPath from task inner join user_mail ON (task.MailID=user_mail.MailID) where task.TaskID=".$input['TaskID'];

			$result = mysqli_query($db->conn,$query);
			$list_html = "";
			if(mysqli_num_rows($result) > 0){

					$row = mysqli_fetch_assoc($result);

					if(!empty($row)){

						if(isset($_FILES['files']) && !empty($row['RawPath']) ){

						    $errors= array();

						    $Rawmaildir="/var/www/vhosts/client.tickletrain.com/httpdocs/";

						    $RelateiveMailPath = "mail/" . gmdate("Ymd") . "/";
				            $Rawmaildir = $Rawmaildir . $RelateiveMailPath;
							$new_img_array = [];
							$Rmailid = $row['MailID'];
							$MailID = $row['MailID'];
							$attachments = !empty($row['attachments'])?explode(',', $row['attachments']):[];
							 
							if (!@is_dir($Rawmaildir)) {
				                @mkdir($Rawmaildir, 0777);
				                @chmod($Rawmaildir, 0777);
				            }
							
							if (!@is_dir($Rawmaildir . $Rmailid . "/")) { 
				                $oldmask = umask(0);//it will set the new umask and returns the old one 
				                @mkdir($Rawmaildir . $Rmailid . "/", 0777);
				                @chmod($Rawmaildir . $Rmailid . "/", 0777);
				                //echo("Created '" . $Rawmaildir . $Rmailid . "/" . "'");
				                umask($oldmask);
				            }
							
							$uploadsize = 0 ;
							
						    foreach ($_FILES['files']['name'] as $key => $image) { 

						       	if(!empty($image)){

						       		$basepath = preg_replace("/\.txt$/i", "/", $row['RawPath']);



						       		$usedSpace = $this->getAvailableSpace($basepath,$attachments);
						       		
						       		// echo round($usedSpace/1000000);
						       		// echo "<br>";
						       		// echo round((100000000 - $usedSpace)/1000000);

						       		// die;
						       		$t = $usedSpace+$input['files_size'];

						       		if( ($usedSpace >= 10000000) || ($t >= 10000000)){
						       			$res =  ['status' => 0,'message'=>"We're sorry, your files are over the maximum file size allowed."];
										return json_encode($res);
						       		}


						   	      	$file_name = $image;
							 	    $file_size =$_FILES['files']['size'][$key];
							 	    $tmp_name =$_FILES['files']['tmp_name'][$key];
							        $type = $_FILES['files']['type'][$key];
									  
							 	    $file_ext=strtolower(end(explode('.',$image)));
								   
							 		$extensions = array("gif", "jpeg", "jpg","png","pdf","doc","docx","txt","rtf","bmp","psd","zip","rar","ppt","pptx","cdr");
							 	    $allow_type= array("image/jpeg","image/jpg","image/png");
								      								
							 		// $imageArr=explode('.',$file_name);
							 		// $randimg =rand(1000,9999);
							 		// $new_name=time().$randimg.'.'.$imageArr[1];
															 		
									if(move_uploaded_file($tmp_name,$basepath.$file_name)) {
									}
									
							 		$new_img_array[] = $file_name;
									 
						       	}
							}


							$atta__ =   array_merge($attachments,$new_img_array);
							
					        // Ppdate Email Content 
							mysqli_query($db->conn,"update user_mail set attachments='".implode(',',$atta__)."' where MailID='" . $MailID . "' ");
						}


						if(!empty(trim($_POST['comment']))){
							$query = "INSERT INTO `comments`(`MailID`, `TickleTrainID`, `TickleID`, `comment_by`, `comment`) VALUES (".$row['MailID'].",'".$row['TickleTrainID']."',".$input['TickleID'].",'owner','".$_POST['comment']."')";
							mysqli_query($db->conn,$query);

						}

					}

					$res =  ['status' => 1,'message'=>"Comment successfully added."];
			}else{
					$res =  ['status' => 0,'message'=>"Task not found."];
			}

		}else{
			$res =  ['status' => 0,'message'=>"Something went wrong."];
		}

		return json_encode($res);
		# code...
	}



	public function getAvailableSpace($path,$images){

		$totalsize = 0;

		if(!empty($images)){

			foreach ($images as $key => $image){
				$filename = $path.$image;
				$sizesss = @filesize($filename);
				$totalsize = $totalsize + $sizesss;
			}
		}

		return $totalsize;
	}
	


// end class 
}

###########################################################################################################################
if(isset($_REQUEST['method']) && !empty($_REQUEST['method'])){
	$method = trim($_REQUEST['method']);
    $TickleTrain = new TickleTrain();
    echo $TickleTrain->$method(); die;
}else{
	echo 'please provide a method name'; die;
}


