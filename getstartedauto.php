<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require("includes/class/PHPMailer/src/Exception.php");
require("includes/class/PHPMailer/src/PHPMailer.php");
require("includes/class/PHPMailer/src/SMTP.php");

include_once("includes/data.php");
include_once("includes/function/func.php");

$current_date = date("Y-m-d h:i:s");
$before_24hours_date = date('Y-m-d h:i:s', strtotime($current_date) - 60 * 60 * 24);
$before_48hours_date = date('Y-m-d h:i:s', strtotime($current_date) - 60 * 60 * 110);

$query = mysql_query("select UserName,TickleID,EmailID,FirstName,LastName from tickleuser where RegisteredDate <='$before_24hours_date' and RegisteredDate >='$before_48hours_date'") or die(mysql_error(). __LINE__);
while($row = mysql_fetch_assoc($query)){

    $TickleID = $row['TickleID'];
    $checkMailTasks = mysql_query("select MailID from user_mail where TickleID='$TickleID'") or die("xcvbcbcvbc");

     if(mysql_num_rows($checkMailTasks) < 1){

     $to = "$row[FirstName] $row[LastName] <$row[EmailID]>";
     $subject = "Getting Started with TickleTrain";
     $message = "Welcome to TickleTrain!  We have scheduled a training Tickle for you that you'll receive shortly.  These emails provide useful tips for getting the most from our service.  Thank you for signing up!  Productivity boost around the corner!

     <br/><br/>TickleTrain.com

     <br/>Send it.  And Forget it.";
     // Always set content-type when sending HTML email
   //  $headers = "MIME-Version: 1.0" . "\r\n";
    // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

     // More headers
  //   $headers .= "From: $row[FirstName] $row[LastName] <$row[EmailID]>" . "\r\n";
     //$headers .= "Bcc: getstartedauto+$row[UserName]@tickletrain.com" . "\r\n";
  //   mail($to,$subject,$message,$headers);
     
     }
}

?>