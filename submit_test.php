<?php
date_default_timezone_set('Europe/Prague');
if (!isset($_SESSION)) {
    session_start();
}

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

$query40 = "SELECT datum, silnice, osoba, hlasky FROM testovani WHERE id = $id;";
if ($result40 = mysqli_query($link, $query40)) {
    while ($row40 = mysqli_fetch_row($result40)) {
        $datum = $row40[0];
        $silnice = $row40[1];
        $osoba = $row40[2];
        $hlasky = $row40[3];
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

    $query68 = "SELECT email FROM users WHERE id = '$logID';";
    if ($result68 = mysqli_query($link, $query68)) {
        while ($row68 = mysqli_fetch_row($result68)) {
            $koordinator = $row68[0];
        }
    }

    $to = 'Testování hlásek <hlasky@zirland.org>';
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
    $headers[] = 'Bcc: zirland@gmail.com';
    $headers[] = 'To: ' . $koordinator;
    //    mail($to, $subject, $message, implode("\r\n", $headers));

    $query97 = "UPDATE testovani SET finalni = 1, zadatel = '$logID' WHERE id = '$id';";
    if ($prikaz97 = mysqli_query($link, $query97)) {
        echo "Požadavek na schválení odeslán.";
    }
    ;
}
?>