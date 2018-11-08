<?php
if(!$_POST) exit;

    $to 	  = 'gopal@iamdesigning.com'; #Replace your email id...
	$name	  = $_POST['txtname'];
	$email    = $_POST['txtemail'];
	$phone    = $_POST['txtphone'];
	$subject  = 'Support';
    $comment  = $_POST['txtmessage'];
        
	if(get_magic_quotes_gpc()) { $comment = stripslashes($comment); }

	 $e_subject = 'You\'ve been contacted by ' . $name . '.';

	 $msg  = "You have been contacted by $name with regards to $subject.\r\n\n";
	 $msg .= "$comment\r\n\n";
	 $msg .= "You can contact $name via email, $email.\r\n\n";
	 $msg .= "-------------------------------------------------------------------------------------------\r\n";
								
	 if(@mail($to, $e_subject, $msg, "From: $email\r\nReturn-Path: $email\r\n"))
	 {
		 echo "<span class='success-msg'>Thanks for Contacting Us, We will call back to you soon.</span>";
	 }
	 else
	 {
		 echo "<span class='error-msg'>Sorry your message not sent, Try again Later.</span>";
	 }
?>