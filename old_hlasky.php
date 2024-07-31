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

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <title>Index databáze INFO35</title>
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

<body>
    <?php
    PageHeader();

    $query38 = "SELECT hlaska_id, new_value, cas FROM `log` WHERE sloupec = 'platnost' AND hlaska_id IN (SELECT hlaska_id FROM `log` WHERE sloupec = 'platnost' AND new_value = '0' AND cas < 1577838800) ORDER BY cas;";
    if ($result38 = mysqli_query($link, $query38)) {
        while ($row38 = mysqli_fetch_row($result38)) {
            $hlaska_id = $row38[0];
            $hodnota = $row38[1];
            $cas = $row38[2];

            $cas_show = date("d.m.Y H:i:s", $cas);

            echo "$hlaska_id > $hodnota > $cas_show > <a href=\"hlaska_delete.php?id=$hlaska_id\">Smazat</a><br/>";
        }
    }

    mysqli_close($link);
    ?>