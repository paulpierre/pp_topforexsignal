<?php

global $controllerFunction,$controllerObject,$controllerID;

    switch($controllerFunction)
    {
        case 'subscribed':
            break;

        case 'support':
            $userEmail = (isset($_POST['userEmail']))?$_POST['userEmail']:false;
            $userName = (isset($_POST['userName']))?$_POST['userName']:false;
            $userMessage = (isset($_POST['userMessage']))?$_POST['userMessage']:false;

            //print '<pre>' . print_r($_POST,true) .'</pre>';

            if($userEmail && $userName && $userMessage)
            {
                $emailBody = '<b>Name:</b>' . $userName . '<br>' . '<b>Email:</b> ' . $userEmail . '<br>' . '<b>Message:</b><br>' . $userMessage;
                $emailSubject = 'Support email';
                $emailTo = 'support@topforexsignal.com';

                $emailHeader = "From: $emailTo\n" . "MIME-Version: 1.0\n" . "Content-type: text/html; charset=utf-8\n";


                //if(send_email($emailTo,$emailSubject,$emailBody))
                if(mail($emailTo, $emailSubject, $emailBody, $emailHeader,"-f$emailTo"))
                 {
                     print ' <div class="main-title" style="padding-top:120px;padding-bottom:40px;"><div class="container"><h2 style="color:#fff;">Message Sent!</h2></div></div><div class="margin30 header-padding"></div>';
                     print '<div class="container"><h2>Email sent!</h2><p>Great, we have sent you email off to our support staff. We will respond within 24-28 hours of recieving your message. </p></div><div class="margin300"></div>';
                 } else {
                     print ' <div class="main-title" style="padding-top:120px;padding-bottom:40px;"><div class="container"><h2 style="color:#fff;">Error!</h2></div></div><div class="margin30 header-padding"></div>';
                     print '<div class="container"><h2>Error sending email</h2><p>It looks like there was an issue sending your message to support using our web form. Please send an email to support@topforexsignal.com if you run into this problem again.</p></div><div class="margin300"></div>';
                 }

            } else {
                print ' <div class="main-title" style="padding-top:120px;padding-bottom:40px;"><div class="container"><h2 style="color:#fff;">Error!</h2></div></div><div class="margin30 header-padding"></div>';
                print '<div class="container"><h2>Error, no data provided.</h2><p> Please send an email to support@topforexsignal.com if you run into this problem again.</p></div><div class="margin300"></div>';
            }

            break;
    }



/*
function send_email($user_email, $user_subject, $user_message)
{
    $mail             = new PHPMailer();

    //$body            = file_get_contents('contents.html');
    $body 			  = $user_message;
    $mail->IsSMTP(); // telling the class to use SMTP
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
    $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
    $mail->Port       = 465;                   // set the SMTP port for the GMAIL server
    $mail->Username   = SUPPORT_EMAIL;  // GMAIL username
    $mail->Password   = SUPPORT_EMAIL_PASSWORD;            // GMAIL password
    $mail->SetFrom(SUPPORT_EMAIL, SUPPORT_EMAIL_NAME);
    $mail->AddReplyTo(SUPPORT_EMAIL,SUPPORT_EMAIL_NAME);
    $mail->Subject    = $user_subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($user_email,'AppRewarder User');

    if(!$mail->Send()) {
        return false;//echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        return true; //echo "Message sent!";
    }
}*/