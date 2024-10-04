<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
  //Server settings
  $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
  $mail->isSMTP();                                            //Send using SMTP
  $mail->Host = $mail_host;                     //Set the SMTP server to send through
  $mail->SMTPAuth = true;                                   //Enable SMTP authentication
  $mail->Username = $mail_username;                     //SMTP username
  $mail->Password = $mail_password;                               //SMTP password
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
  $mail->Port = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
  $mail->CharSet = "UTF-8";

  //Recipients
  $mail->setFrom($mail_username, 'Testování hlásek');
  $mail->addAddress('zirland@zirland.org');     //Add a recipient
  $mail->addBCC('zirland@gmail.com');               //Name is optional
//    $mail->addCC('cc@example.com');

  //Attachments
//    $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments

  //Content
  $mail->isHTML(true);                                  //Set email format to HTML
  $mail->Subject = 'Schválení termínu testu';
  $mail->Body = '<html>
<head>
<title>Schválení termínu testu</title>
</head>
<body>
<p>Plánovaný termín testu byl schválen:</p>
<p><b>Datum: </b> 1.6.2022 <br/>
<b>Silnice: </b> D1 </p>

</body>
</html>
';
  //    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

  $mail->send();
  echo 'Message has been sent';
} catch (Exception $e) {
  echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>