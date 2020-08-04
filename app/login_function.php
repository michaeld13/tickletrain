<?php 
$Form=new ValidateForm();

if(isset($_POST['getpassowrd']) && $_POST['getpassowrd']=='getpass'){
    $email = $_POST['email'];
   // die($_POST['email']);
    $ticklearray = $db->select_rows('tickleuser', '*', "where EmailID='$email'", 'ASSOC');
    $tickleid = $ticklearray[0]['Password'];
    die($tickleid);
  }
  
  if(isset ($_GET['password']) && isset ($_GET['email'])){
      $_SESSION['whmcsuserid'] = $_GET['uid'];
      $_POST['submit'] = "Login";
      $_POST['Username'] = urldecode($_GET['email']);
      //$_POST['Password'] = urldecode($_GET['password']);
      $_POST['Password'] = $_GET['password'];
      // Code added on 2/1/2014 to fix login as client issue
      $GetUserStatusQuery = mysqli_query($db->conn,"SELECT `Status` FROM `tickleuser` WHERE `EmailID`='".$_GET['email']."'") or die(mysqli_error($db->conn). __LINE__);
      $GetUserStatusRow = mysqli_fetch_assoc($GetUserStatusQuery);

     if($GetUserStatusRow['Status'] == 'N'){
          header("location:https://secure.tickletrain.com/admin/clientssummary.php?userid=$_GET[uid]");
      }
  }
  

if($_POST['submit']=="Login"){
    //die('oooo');
/*
$whmcsurl = "http://secure.tickletrain.com/dologin.php";
$autoauthkey = "abcXYZ123";

$timestamp = time(); # Get current timestamp
$email = $_REQUEST['Username']; # Clients Email Address to Login
$goto = "clientarea.php";

$hash = sha1($email.$timestamp.$autoauthkey); # Generate Hash

# Generate AutoAuth URL & Redirect
$url = $whmcsurl."?email=$email&timestamp=$timestamp&hash=$hash&goto=".urlencode($goto);
header("Location: $url");
exit;
*/

global $Username;
$check_key=array('Username','Password');
$filter_post=filterpost($check_key,$_POST);
$Username=trim($_POST['Username']);
$Password=trim($_POST['Password']);

if(!isset ($_GET['password'])){
  $Form->ValidField($Username,'empty','Enter Username');
  $Form->ValidField($Password,'empty','Enter Password');
}


	if($Form->ErrorString=="")
	{
           // die('11111');
            $chek_login = login($Username,$Password);
              
            if ($chek_login==0){
                // die('valieddata');
                redirect('home');
               
            }
            if ($chek_login==1){
                //die('33333');
                $Form->ValidField($Status,'empty','Please Activate your Account<br><a href="javascript:" onclick="return SendActivation()">Resend Welcome Email</a>');
            }
            if ($chek_login==2){
                $Form->ValidField($GetUserStatusRow['Status'],'empty','Invalid Username or Password<br/>Forgot your password? Click <a href="javascript:" onclick="return SendActivation()">here</a> to restore it.');
            }
	          if ($chek_login['code']==3){
                $whmcsurl = "http://secure.tickletrain.com/dologin.php";
            		$autoauthkey = "abcXYZ123";
            		$timestamp = time(); # Get current timestamp
            		$email = $chek_login['mail']; # Clients Email Address to Login
            		$goto = "cart.php?service=noservice";

            		$hash = sha1($email.$timestamp.$autoauthkey); # Generate Hash

            		# Generate AutoAuth URL & Redirect
            		$url = $whmcsurl."?email=$email&timestamp=$timestamp&hash=$hash&goto=".urlencode($goto);
            		header("Location: $url");
            		exit;
            		//header("Location:https://secure.tickletrain.com/cart.php");
            		//exit;
                
            }
        }
}

?>
