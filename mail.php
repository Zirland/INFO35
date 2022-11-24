
<?php
$to = 'Testování hlásek <hlasky@zirland.org>';

// Subject
$subject = 'Schválení termínu testu';

// Message
$message = '
<html>
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

// To send HTML mail, the Content-type header must be set
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=utf-8';

// Additional headers
$headers[] = 'From: Testování hlásek <hlasky@zirland.org>';
$headers[] = 'To: zirland@zirland.org';
$headers[] = 'Bcc: zirland@gmail.com';


// Mail it
$odeslano = mail($to, $subject, $message, implode("\r\n", $headers));
if (!$odeslano) {
  echo "FAIL";
} else {
  echo "Success";
}
?>



