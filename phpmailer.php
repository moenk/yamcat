<?php
// 
// wrapper for the PHPMailer class
// 
// download it from: http://code.google.com/a/apache-extras.org/p/phpmailer/
// 
// replaces the mail() function that doesn't work on any webserver
// and to avoid mails getting marked as spam for missing A/MX records
// 

function phpmail($email,$subject,$body) {
  include "conf/config.php";
  date_default_timezone_set('Europe/Berlin');
  require_once('external/PHPMailer/class.phpmailer.php');
  $mail             = new PHPMailer();
  $mail->IsSMTP(); 							// telling the class to use SMTP
  $mail->SMTPDebug  = 1;                    // enables SMTP debug information (for testing)
											// 1 = errors and messages
											// 2 = messages only
  $mail->SMTPAuth   = true;                 // enable SMTP authentication
  $mail->SMTPSecure = $mailsecure;
  $mail->Host       = $mailhost;
  $mail->Port       = $mailport;
  $mail->Username   = $mailusername;
  $mail->Password   = $mailpassword;
  $mail->SetFrom($mailfrom, $subtitle);
  $mail->Subject    = $subject;
  // $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
  $mail->MsgHTML($body);
  $mail->AddAddress($email,$email);
  if(!$mail->Send()) {
    return "Mailer Error: " . $mail->ErrorInfo;
  } else {
    return "Message sent!";
  }
}
?>
