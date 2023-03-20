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

    $query36 = "SELECT hlaska_id, new_value, cas FROM log WHERE sloupec = 'platnost' AND hlaska_id IN (SELECT hlaska_id FROM log WHERE sloupec = 'platnost' AND new_value = '' AND cas < 1577836800) ORDER BY cas;";
    if ($result36 = mysqli_query($link, $query36)) {
        while ($row36 = mysqli_fetch_row($result36)) {
            $hlaska_id = $row36[0];
            $hodnota = $row36[1];
            $cas = $row36[2];

            $cas_show = date("d.m.Y H:i:s", $cas);

            echo "$hlaska_id > $hodnota > $cas_show > <a href=\"hlaska_delete.php?id=$hlaska_id\">Smazat</a><br/>";
        }
    }

    mysqli_close($link);
    ?>