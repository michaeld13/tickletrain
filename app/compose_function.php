<?php
//$Variables['RemoveHeader']=1;
$ToAddress = $_POST['ToAddress'];
$CcAddress = $_POST['CcAddress'];
$BccAddress = $_POST['BccAddress'];
$Subject = $_POST['Subject'];
$TickleMailContent = $_POST['TickleMailContent'];
$submit = $_POST['submit'];
if ($submit == "Send") {
    $FromEmailid = $_SESSION['EmailID'];
    $FromFirstName = $_SESSION['FirstName'];
    $FromLastName = $_SESSION['LastName'];
    $EXToAddress = extract_emails_from(addslashes($ToAddress));
    $EXCcAddress = extract_emails_from(addslashes($CcAddress));
    $EXBccAddress = extract_emails_from(addslashes($BccAddress));

    $mail = new PHPMailer(false); //New instance, with exceptions enabled
    /*
        $mail->IsSMTP();                           // tell the class to use SMTP
        $mail->SMTPAuth   = true;                  // enable SMTP authentication
        $mail->SMTPKeepAlive = true;                  // SMTP connection will not close after each email sent
        $mail->Port       = 25;                    // set the SMTP server port
        $mail->Host       = "mail.tickletrain.com"; // SMTP server
        $mail->Username   = "ticklein@tickletrain.com";     // SMTP server username
        $mail->Password   = "change88";            // SMTP server password

        $mail->IsSendmail();  // tell the class to use Sendmail
    */
    $EmailPriority = 3;
    ob_start();
    if ($ToAddress != "") {
        $body = $TickleMailContent;
        $TextMsg = trim(strip_tags($TickleMailContent));
        $mail->AddReplyTo(trim($FromEmailid), "$FromFirstName $FromLastName");
        $mail->SetFrom($FromEmailid, "$FromFirstName $FromLastName");
        $mail->Subject = $Subject;
        $mail->AltBody = $TextMsg;
        $mail->WordWrap = 80; // set word wrap
        $mail->Priority = $EmailPriority;
        $mail->MsgHTML($body);
        $mail->IsHTML(true); // send as HTML
        $mail->CharSet = "utf-8";
        foreach ($EXToAddress as $Key => $Evalue)
        {
            if ($Evalue != "")
                $mail->AddAddress($Evalue);
        }
        foreach ($EXCcAddress as $Key => $Evalue)
        {
            if ($Evalue != "")
                $mail->AddCC($Evalue);
        }
        foreach ($EXCcAddress as $Key => $Evalue)
        {
            if ($Evalue != "")
                $mail->AddBCC($Evalue);
        }
        if (!$mail->Send()) {
            $msg = "Error while sending email <br />" . "Mailer Error: " . $mail->ErrorInfo;
        } else
        {
            $msg = "Email has been sent";
        }

        $mail->ClearAddresses();
        $mail->ClearAttachments();
        $mail->ClearReplyTos();
        $mail->ClearAllRecipients();
        $mail->ClearBCCs();
        $mail->ClearCCs();
    }
    ob_end_clean();
    $Variables['MSG'] = $msg;
}//if
?>