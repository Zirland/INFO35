<?php
date_default_timezone_set('Europe/Prague');
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'config.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="content-type">
	<title>Editace testu hlásek</title>
	<script type="text/javascript" src="https://api.mapy.cz/loader.js"></script>
	<script type="text/javascript">
		Loader.lang = "cs";
		Loader.load(null, {
			poi: true
		});
	</script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 500px;
            padding: 20px;
        }
    </style>
</head>

<?php
$id = @$_GET["id"];
if ($id == "") {
    $id = @$_POST["id"];
}

$query16 = "SELECT datum, silnice, osoba, hlasky FROM testovani WHERE id = $id;";
if ($result16 = mysqli_query($link, $query16)) {
    while ($row16 = mysqli_fetch_row($result16)) {
        $datum   = $row16[0];
        $silnice = $row16[1];
        $osoba   = $row16[2];
        $hlasky  = $row16[3];
    }
}

PageHeader();
$today = date("Y-m-d", strtotime("+ 1 day"));
$error = 0;

if ($datum < $today) {
    echo "Testování je možno schválit nejpozději den před jeho uskutečněním.";
    $error = 1;
}

if ($hlasky == "") {
    echo "Nelze schválit testování bez hlásek.";
    $error = 1;
}

if ($error == 0) {

    $datumformat = date("d.m.Y", strtotime($datum));
    $logID = $_SESSION["id"];

    $query72 = "SELECT email FROM users WHERE id = '$logID';";
    if ($result72 = mysqli_query($link, $query72)) {
        while ($row72 = mysqli_fetch_row($result72)) {
            $koordinator   = $row72[0];
        }
    }

    $to      = 'Testování hlásek <hlasky@zirland.org>';
    $subject = 'Požadavek na schválení testu';
    $message = '
<html>
<head>
  <title>Požadavek na schválení testu</title>
</head>
<body>
<p>Byl zadán požadavek na schválení testu hlásek:</p>
<p><b>Datum: </b>' . $datumformat . '<br/>
<b>Silnice: </b>' . $silnice . '</p>
</body>
</html>
';
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=utf-8';
    $headers[] = 'From: Testování hlásek <hlasky@zirland.org>';
    $headers[] = 'To: '. $koordinator;
    mail($to, $subject, $message, implode("\r\n", $headers));

    $query96  = "UPDATE testovani SET finalni = 1, zadatel = '$logID' WHERE id = '$id';";
    $prikaz96 = mysqli_query($link, $query96);

    echo $message;
}
?>


