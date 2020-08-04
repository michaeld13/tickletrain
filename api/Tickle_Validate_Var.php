<?php

/**
 * 
 */
class Tickle_Validate_Var
{


    public $imagePath =  "https://client.tickletrain.com/images/Extension";
	/*
	* Function : filter_string 
	* Use : Prevent from XSS and SQL injection
	* Date : 20-05-19
	*/
	public function tt_filter_arr($arr)
	{
		if(!empty($arr)){
			foreach ($arr as $key => $value) {
				if(!is_array($value)){
					$filtered_arr[$key] = htmlspecialchars(stripcslashes(trim($value)));
				}else{
					foreach ($value as $k => $val) {
						$filtered_arr[$key][$k] = htmlspecialchars(stripcslashes(trim($val)));
					}
				}
			}
			return $filtered_arr;
		}
		return false;
	}

    /*
	* Function : tt_object 
	* Use : Convert array to object
	* Date : 20-05-19
	*/
	public function tt_object($arr)
	{
		return json_decode(json_encode($arr), FALSE);
	}


	/*
	* Function : tt_object 
	* Use : Convert array to object
	* Date : 20-05-19
	*/
	public function getHomeHtml($userData)
	{

		$short_name ='<img src="../images/defaul_user.png" alt="user image" class="rounded-circle" />';
		if(!empty($userData['FirstName'])){
			$short_name =  '<span class="rounded-circle" >'.$userData['FirstName'][0]."".(!empty($userData['LastName'])?$userData['LastName'][0]:'').'</span>';
		}

		$checked = (($userData['delete_all_campaign'] == true )?'checked="checked"':'');
		$enable_email_traking = (!empty($userData['enable_email_traking'])?'checked="checked"':'');
		
		$act_link = rawurlencode(protect($userData['UserID']));

			return ('<div class="header-top p-2">
						<div class="row">
							<div class="col text-center font-weight-bold">
								Dashboard
								<span class="float-right small pointer close_window" > close </span>
							</div>
						</div>
					</div>
					<div class="content-body text-center mt-4">
						<div class="row">
							<div class="col">
								<div class="user-image-box">
									'.$short_name.'
								</div>
								<h5 class="tt-un mb-0">'.$userData['FirstName'].' '.$userData['LastName'].'</h5>
								<span class="small">'.$userData['EmailID'].'</span>
								<p class="small" >Account ID: '.$userData['UserID'].'</p>
								<hr>
							</div>
						</div>
						<div class="row">
							<div class="col m-0">
							<nav class="navbar">
							
								<a class="navbar-brand" target="_blank" href="https://client.tickletrain.com/useractivitylist/?act='.$act_link.'" id="getActivity">
									<span class="float-left">
										<img src=""  class="d-inline-block align-top userDefault" alt="">
									</span>
									Activity
								</a>
								
								<a class="navbar-brand" href="#" id="getProfile">
									<span class="float-left">
									<img src="'.$this->imagePath.'/defaul_user36*36.png" class="d-inline-block align-top userDefault" alt="">
									</span>
									Profile 
									<span class="float-right">
									<img src="https://client.tickletrain.com/images/Extension/arrow-point-to-right.svg"  class="d-inline-block align-top" alt="">
									</span>
								</a>

								<a class="navbar-brand" href="#" id="tt-logout">
									<span class="float-left">
										<img src="'.$this->imagePath.'/logout.svg"  class="d-inline-block align-top userDefault" alt="">
									</span>
									Log Out
								</a>

							</nav>
								<div class="tt-switch-box">
									<label class="switch">
										<input type="checkbox" id="tt-dl-cp"  '.$checked.'>
										<span class="slider round"></span>
										<span class="absolute-no">NO</span>
									</label>
									<span class="tt-switch-label">Delete Campaign On Reply
									<sup><img src="'.$this->imagePath.'/help.svg"  alt="help" data-toggle="tooltip" title="Your campaign will delete automatically when a reply is received from your contact.  Note* If you want some campaigns to delete automatically and others to remain when replied to, leave this unchecked and configure the settings for the Tickle on the website." ></sup>
									</span>
								</div>
								<div class="tt-switch-box">
									<label class="switch">
										<input type="checkbox" id="tt-tr-ee"  '.$enable_email_traking.'>
										<span class="slider round"></span>
										<span class="absolute-no">NO</span>
									</label>
									<span class="tt-switch-label">Track Email Opens/Views
									<sup><img src="'.$this->imagePath.'/help.svg"  alt="help" data-toggle="tooltip"  title="A hidden image pixel is added to your emails sent from TickleTrain.  This requires the receiver to \'view images\' when opening your email.  You can track this activity in the Dashboard."  ></sup>
									</span>
								</div>
							</div>
						</div>
					</div>') ;
	}
	
	/*
	* Function : getProfilePage 
	* Use : for display on  popup.js
	* Date : 20-05-19
	*/
	public function getProfilePageHTML($userData)
	{  

		$short_name ='<img src="../images/defaul_user.png" alt="user image" class="rounded-circle" />';
		if(!empty($userData['FirstName'])){
			$short_name =  '<span class="rounded-circle" >'.$userData['FirstName'][0]."".(!empty($userData['LastName'])?$userData['LastName'][0]:'').'</span>';
		}
		# code...
		return ('<div class="header-top p-2">
		<div class="row">
			<div class="col">
		      <a href="#" title="Back" id="backToDashboard" class ="float-left" > <img src="../images/left-arrow-white.svg"  width="20" alt="Back"></a>
			</div>
			<div class="col-8 header-title">
		      <span> Profile </span>
		    </div>
		    <div class="col ">
		      <!--a href="#" title="Log Out" id="tt-logout"  class ="float-right" > <img src="../images/logout.svg"  width="20" alt="Log Out"></a-->
		    </div>
		</div>	
    </div>
    <div class="content-body text-center mt-4">
    	<div class="row">
		    <div class="col">
		    	<div class="user-image-box">
					'.$short_name.'
		    	</div>
		      	<h5 class="tt-un mb-0">'.$userData['FirstName'].' '.$userData['LastName'].'</h5>
				<span class="small">'.$userData['EmailID'].'</span>
				<p class="small" >Account ID: '.$userData['UserID'].'</p>
				<hr>
		    </div>
		</div>
		<div class="row">
			<div class="col m-3">
				<form  class="tt-u-form" id="tt-user-form">
					<div class="form-group">
					    <label for="tt-fn">Frist Name:</label>
					    <input type="text" class="form-control private-form__control" name="FirstName" id="tt-fn" value="'.$userData['FirstName'].'">
					</div>

					<div class="form-group">
					    <label for="tt-ln">Last Name:</label>
					    <input type="text" class="form-control" id="tt-ln" name="LastName" value="'.$userData['LastName'].'">
					</div>

					<div class="form-group">
					    <label for="email">Email address:</label>
					    <input type="email" class="form-control" id="email" value="'.$userData['EmailID'].'" readonly>
					</div>
					<input type="hidden" name="user_id" value="'.$userData['UserID'].'" >
					<div class="form-group float-right">
						<button type="submit" class="btn btn-primary" id="save-profile">Save</button>
					</div>
				</form> 
			</div>
		</div>
    </div>') ;
	}



	public function get_li($task,$input, $class,$total_rows =  0 ,$stage = 0)
	{
		
		$TaskInitiateDate = strtotime($task['TaskInitiateDate']);
		$html = "";
		$P_img=""; $schedule_time = ""; $comment_icon = "";
		$attachments_icon = "";
		if($class == 'parent_li' && isset($task['has_notifications'])) {
			$P_img='<img  class="email-btn pointer opened"  data-id="'.$task['MailID'].'" src="' . $this->imagePath . '/email_open.png" />';
		}

		$NoWeekend = (isset($task['FollowNoWeekend']) && !empty($task['FollowNoWeekend']))?$task['FollowNoWeekend']:$task['NoWeekend'];

		if($class == 'parent_li') {
			$schedule_time = "schedule-time";
			$time_shotcuts = '<div class="time-holder" data-taskid="'.$task["TaskID"].'" data-mailid="'.$task["MailID"].'" data-weekend="'.$NoWeekend.'" >
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
					</div>';
			$P_img.='<img  class="del-cm-cl del-btn-all pointer"  data-id="'.$task['MailID'].'"  data-task_id="'.$task['TaskID'].'" src="' . $this->imagePath . '/delete.svg" />';

			if($this->hasComments($task['MailID'])){

				$protect = protect($$task['TickleID'] . "-" . $task['MailID']);
				$add_comment_link ="https://".$this->domain_name.Url_Create("addcomments","cptsk=".rawurlencode($protect)."&els=".rawurlencode(protect('yes')));
					
				if($new_comments = $this->hasNewComments($task['MailID'])){
					$comment_icon='<a target="_blank" href="'.$add_comment_link.'"><img  class="comnt-btn pointer" src="' . $this->imagePath . '/icon-comment.svg" /><span class="coment-count">'.$new_comments.'</span></a>';
				}else{
					$comment_icon='<a target="_blank" href="'.$add_comment_link.'"><img  class="comnt-btn pointer" src="' . $this->imagePath . '/icon-comment.svg" /></a>';
				}
			}

			$comment_icon.='<img  class="add-new-comnt-btn pointer ml-1" src="' . $this->imagePath . '/add-comment.jpeg" />';
			

		}else{
			$P_img.='<img  class="del-btn pointer"  data-task_id="'.$task['TaskID'].'" src="' . $this->imagePath . '/delete.svg" />';
		}

		$TApprove = $task['TTApprove'];
        $IsApproved = $task['Approve'];
        $IsPaused = $task['Pause'];
        $t = "main";
        if ($task['FollowTickleTrainID']) {
            $TApprove = $task['FollowTApprove'];
            $t = "Folow";
        }

        if ($TApprove == 'Y' && $IsApproved != 'Y' || $IsPaused == 'Y') {
         	$P_img.= '<img class="pause-btn pointer" data-status="Y" data-id="'.$task['TaskID'].'" src="' . $this->imagePath . '/play-button.svg" title="Unpause this tickle"/>';
        }
        
        if (($TApprove == 'N' || $IsApproved == 'Y') && ($IsPaused != 'Y') ) {
            $P_img.= '<img class="pause-btn pointer" data-status="N" data-id="'.$task['TaskID'].'" src="' . $this->imagePath . '/pause.svg" title="Pause this tickle"/>';
        }


		if(!empty($task['FirstName']) || !empty($task['LastName']) ){
			$name =  $task['FirstName'].' '.$task['LastName'];
		}else{
			$name =  $task['EmailID'];
		}

		$name = str_replace("'","",$name);
       
		if($class == 'parent_li' && $total_rows > 1 ){
			$P_img.='<img src="'.$this->imagePath.'/arrow-point-to-right.svg" class="arrow-img pointer"  data-mailid="'.$task['MailID'].'" >';
		}

		if(!empty($task['attachments'])){

			$arr =  explode(',', $task['attachments']);
			if(count($arr) > 1 ){
				$attachments_icon='<img src="'.$this->imagePath.'/attach.svg" class="arrow-img pointer orignal-email-btn" data-scroll="true" > ';
			}else{

				$basepath = preg_replace("/\.txt$/i", "/", $task['RawPath']);
       			$basepath = str_replace('/var/www/vhosts/client.tickletrain.com/httpdocs', "", $basepath);
		        $attachments_icon.='<a  href="https://client.tickletrain.com'.$basepath.$arr[0].'" ><img src="'.$this->imagePath.'/attach.svg" class="arrow-img pointer" ></a>';
			}
		
		}else{
			$attachments_icon = "";
		}

		$simbol = ($task['EndAfter'] == 13 || $task['EndAfterFollow'] == 13)?'∞':'';
		$total_rows = ($task['EndAfter'] == 13 || $task['EndAfterFollow'] == 13)?'∞':$total_rows;

		$task["Subject"] = (!empty($task["CustomSubject"] )) ? $task["CustomSubject"] : $task["Subject"];

		$subject = $this->removeChar($task["Subject"]);
		if(empty($subject)) {
			$subject = $task["Subject"];
		}

		if(empty($subject)) {
			$subject = '(no subject)';
		}
		
		$html.='<li class="list-group-item '.$class.' '.$class.'_'.$task['MailID'].'"  data-id="'.$task['TaskID'].'" data-mailid="'.$task['MailID'].'" data-weekend="'.$NoWeekend.'" >
					<div class="col-12 p-0">
						<div class="l-1">
						<label class="name pointer orignal-email-btn"  title="'.$subject.'" >'.$subject.'</label>
						  <span class="l-1-s">'.$attachments_icon.$comment_icon.'</span>
						</div>
						<div class="tickle-actions">
							<small class="opened text-muted pointer ellipsis eye-btn u-name" title="'.$task["EmailID"].'"  >'.$name.'</small>
							<small class="text-muted tt-date float-right pulse pointer text-right '.$schedule_time.'">'.date('m-d-y h:ia',$TaskInitiateDate).'</small>
						</div>

						<div class="tickle-actions">
							<small class="text-muted pointer ellipsis eye-btn"  >'.$task['TickleTitle'].'</small>
							<small class="text-muted stag-count">
								 '.($stage+1).' of '.$total_rows.'
							</small>
							<span class="tt-action-icons text-right">
								'.$P_img.'
							</span>
						</div>
					</div>
					'.$time_shotcuts.'
					<div class="timeline-box">
					</div>
					<div class="tt-cmt-box">
					  <textarea style="display:none;" class="tt-cmnt-frm" id="tt-comnt-area'.$task['TaskID'].'"  placeholder="Add a comment..."></textarea>
					</div>

				</li>';
		return $html;

		
	}


	public function delete_task($task_id)
	{
		# code...
		global $db;
		$delete_test  =  "DELETE FROM task where TaskID=".$task_id;
		$result =  mysqli_query($db->conn,$delete_test);
	}

	public function delete_all_tasks($mail_id)
	{
		# code...
		global $db;
		$delDate = date("Y-m-d H:i:s");
		$delete_query = "update task set Status='D',TaskDeletedDate='".$delDate."' where Status='Y' and MailID=".$mail_id;
		$result =  mysqli_query($db->conn,$delete_query);
	}
	
	public function delete_mail_and_tasks($mail_id)
	{
		# code...
		global $db;
		$delete_test = "update user_mail set Status='D' where MailID=".$mail_id;
		$result =  mysqli_query($db->conn,$delete_test);
					$this->delete_all_tasks($mail_id);
	}


	public function getIp() {
		
	    $ip = $_SERVER['REMOTE_ADDR'];
	 
	    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	        $ip = $_SERVER['HTTP_CLIENT_IP'];
	    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	 
	    return $ip;
	}

	public function getUserTickles($user_id)
	{
		global $db;
		$fileds = "select tickle.TickleTrainID,tickle.TickleName,tickle.reminder_task  from tickle where tickle.TickleID=".$user_id;
		$query = " group by tickle.TickleTrainID order by TickleName ASC";
		$tickles = 	 $db->query_to_array($fileds.$query);
		return $tickles;
	}

	public function getContact($user_id,$email)
	{
		global $db;
		$contact = [];
		$qry = "select *  from contact_list  where contact_list.TickleID=$user_id AND EmailID='$email'";
		$result =  mysqli_query($db->conn,$qry);
		if(mysqli_num_rows($result) > 0){
			$contact = mysqli_fetch_assoc($result);
		}
		return $contact;
	}

	public function replace_content($content,$f_name,$signature)
	{
		if(!empty($f_name)){
			$content = str_replace("[FirstName]", $f_name, $content);
            $content = str_replace("[firstname]", $f_name, $content);
		}
        return str_replace("[signature]", $signature, $content);
	}

	public function removeChar($subject){

	    $replaceSubjectValue=array("RE :[EXTERNAL]","RE:[EXTERNAL]","Re :[EXTERNAL]","Re:[EXTERNAL]","RE:", "Re:", "RE :", "Fw:", "FW:", "FW :", "fw:", "fw :", "Fw :", "Fwd:", "Fwd :", "fwd:", "FWD:");
	    $replaceSubjectWithValue = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");

        $Subject=trim(str_replace($replaceSubjectValue, $replaceSubjectWithValue, $subject));
                               
        return $Subject;
    }

    

    /// Comments Functions 
    public function hasComments($MailID)
    {
    	global $db;
	   	$query = "select * from comments where MailID='".$MailID."'";
	  	$result =  mysqli_query($db->conn,$query);
		if(mysqli_num_rows($result) > 0){
			return true;
		}
		return false;
    }

    public function hasNewComments($MailID)
    {
    	global $db;
	   	$query  = "select * from comments where status='unseen' and  MailID='".$MailID."'";
	  	$result = mysqli_query($db->conn,$query);
		return mysqli_num_rows($result);
    }

    public function mark_seen($MailID){
    	global $db;
	    $update = "update comments set status='seen' where MailID=".intval($rss['MailID']) ;
        mysqli_query($db->conn,$update);
	}
	////



// end class
}

?>