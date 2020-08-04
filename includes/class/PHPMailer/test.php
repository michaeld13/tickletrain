<?php

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    // $mail->Host = 'smtp.mail.yahoo.com';  // Specify main and backup SMTP servers
    // $mail->SMTPAuth = true;                               // Enable SMTP authentication
    // $mail->Username = 'sandeepr.shinedezign';                 // SMTP username
    // $mail->Password = 'vclsfxmdvylwlsvx';                           // SMTP password
    // $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    // $mail->Port = 465;  


    //$mail->Hostname = 'localhost.localdomain';  // Specify main and backup SMTP servers
    $mail->Host = 'secureus186.sgcpanel.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;    
    $mail->Mailer = "mail";                          // Enable SMTP authentication
    $mail->Username = 'sandeep@speedgraphics.net';                 // SMTP username
    $mail->Password = 'shine123!';                           // SMTP password
    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 465;
                                      // TCP port to connect to



    //Recipients
    $mail->setFrom('sandeep@speedgraphics.net', 'Sandeep');
    $mail->AddReplyTo('sandeep@speedgraphics.net', 'Sandeep');
    $mail->addAddress('sandeepr.shinedezign@yahoo.com');     // Add a recipient


    //$mail->AddCustomHeader('In-Reply-To',$mail->getLastMessageID()); // so we get threading on gmail (needed as to and from are the same address)

    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
   // print_r(expression)
    save_mail($mail); 

   // $mail_string=$mail->get_mail_string();
    //imap_append($ImapStream, $folder, $mail_string, "\\Seen");

    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}


function save_mail($mail)
{
    $providerMail = 'Gmail';
    $providerMailSentFolder = 'Sent Mail';//You can change 'Sent Mail' to any folder
    $imap_host = 'secureus186.sgcpanel.com';//imap.one.com
    $imap_userame = 'sandeep@speedgraphics.net';                 // SMTP username
    $decryptpass = 'shine123!';

    $inbox = imap_open("{" . $imap_host . ":993/imap/ssl}INBOX.Sent", $imap_userame, $decryptpass) or $inbox = @imap_open("{" . $imap_host. ":993/imap/ssl/novalidate-cert}INBOX.Sent",$imap_userame, $decryptpass, OP_HALFOPEN, 1);
    $path = "{" . $imap_host . ":993/imap/ssl}INBOX.Sent";

    $result = imap_append($inbox, $path, $mail->getSentMIMEMessage());
    imap_close($inbox);

}