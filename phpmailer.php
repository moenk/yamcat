<?php
// 
//	file: phpmailer.php
//
// 	coder: moenk
//
//	purpose: wrapper for the PHPMailer class, replaces the mail() function that doesn't work on any webserver
// 			and to avoid mails getting marked as spam for missing A/MX records
// 

function phpmail($email,$name,$subject,$body) {
	include "conf/config.php";
	date_default_timezone_set('Europe/Berlin');
	require_once('external/PHPMailer/class.phpmailer.php');
	$mail = new PHPMailer();
	$mail->ContentType = 'text/plain'; 
    $mail->IsHTML(false);
	$mail->IsSMTP(); 							// telling the class to use SMTP
	$mail->SMTPDebug  = 1;              		// debug information (for testing), 1 = errors and messages, 2 = messages only
	if ($mailsecure!="") {
		$mail->SMTPAuth   = true;           	// enable SMTP authentication
	} else { 
		$mail->SMTPAuth   = false;           	// disable SMTP authentication
	}
	$mail->SMTPSecure = $mailsecure;
	$mail->Host       = $mailhost;
	$mail->Port       = $mailport;
	$mail->Username   = $mailusername;
	$mail->Password   = $mailpassword;
	$mail->SetFrom($mailfrom, $subtitle);
	$mail->Subject    = $subject;
	$mail->Body = $body;
	// $mail->MsgHTML($body);
	$mail->AddAddress($email,$name);
	if(!$mail->Send()) {
		return "Mailer Error: " . $mail->ErrorInfo;
	} else {
		return "Message sent!";
	}
}
?>
