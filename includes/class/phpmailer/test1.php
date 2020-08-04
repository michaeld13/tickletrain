<?php


require("./class.phpmailer.php");

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    //$mail->IsSMTP();                                      // Set mailer to use SMTP
    // $mail->Host = 'smtp.mail.yahoo.com';  // Specify main and backup SMTP servers
    // $mail->SMTPAuth = true;                               // Enable SMTP authentication
    // $mail->Username = 'sandeepr.shinedezign';                 // SMTP username
    // $mail->Password = 'vclsfxmdvylwlsvx';                           // SMTP password
    // $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    // $mail->Port = 465;  


    $mail->Hostname = 'tickletrain.com';  // Specify main and backup SMTP servers
    $mail->Host = 'secureus186.sgcpanel.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;    
  //  $mail->Mailer = "mail";                          // Enable SMTP authentication
    $mail->Username = 'sandeep@speedgraphics.net';                 // SMTP username
    $mail->Password = 'shine123!';                           // SMTP password
    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 465;
                                      // TCP port to connect to


    //Recipients
    $mail->setFrom('sandeep@speedgraphics.net', 'sandeep speedgraphic',1);
    //$mail->AddReplyTo('sandeep@speedgraphics.net', 'Sandeep');
    $mail->AddAddress('sandeepr.shinedezign@yahoo.com');     // Add a recipient

    //$mail->AddCustomHeader('In-Reply-To',$mail->getLastMessageID()); // so we get threading on gmail (needed as to and from are the same address)

    $body = 'This is Threading <b>here</b>';
    $mail->IsHTML(true);                                // Set email format to HTML
    $mail->Subject = 'RE: Thread Issue';
    $mail->AltBody = $body;
    $mail->CharSet = "utf-8";
    $mail->MsgHTML($body);


    $mail->send();
    $mail->ClearAllRecipients();
    $mail->ClearReplyTos();
    $mail->ClearAttachments();
    $mail->ClearCCs();
    // echo "<pre>";
    // print_r($mail);
    //save_mail($mail); 

   // $mail_string=$mail->get_mail_string();
    //imap_append($ImapStream, $folder, $mail_string, "\\Seen");

    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}


function save_mail($mail)
{
    $imap_host = 'secureus186.sgcpanel.com';//imap.one.com
    $imap_userame = 'sandeep@speedgraphics.net';                 // SMTP username
    $decryptpass = 'shine123!';

    $inbox = imap_open("{" . $imap_host . ":993/imap/ssl}INBOX.Sent", $imap_userame, $decryptpass) or $inbox = @imap_open("{" . $imap_host. ":993/imap/ssl/novalidate-cert}INBOX.Sent",$imap_userame, $decryptpass, OP_HALFOPEN, 1);
    $path = "{" . $imap_host . ":993/imap/ssl}INBOX.Sent";

    $result = imap_append($inbox, $path, $mail->getSentMIMEMessage());
    imap_close($inbox);
}