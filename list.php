<?php
date_default_timezone_set('Europe/Prague');
session_start();

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
    <title>Záznam INFO35 - SOS hlásky</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 500px;
            padding: 20px;
        }

        tr.dark {
            background-color: #ddd;
            color: black;
        }

        tr.light {
            background-color: #fff;
            color: black;
        }

        tr.dark-strikeout {
            background-color: #ddd;
            color: red;
        }

        tr.light-strikeout {
            background-color: #fff;
            color: red;
        }
    </style>
</head>

<body>
    <?php
$app_up = PageHeader();

echo "<table width=\"100%\">";
echo "<tr><th>&nbsp;</th><th>Telefonní číslo</th><th>Silnice</th><th>Kilometr</th><th>Směr</th><th>Zeměpisná šířka</th><th>Zeměpisná délka</th><th>SSÚD</th><th>Typ</th><th>Status</th><th></th></tr>";
$i = 0;

$query60 = "SELECT id, tel_cislo, silnice, kilometr, smer, longitude, latitude, platnost, export, edited, ssud, typ FROM hlasky WHERE archiv='0' ORDER BY tel_cislo";
if ($result60 = mysqli_query($link, $query60)) {
    while ($row60 = mysqli_fetch_row($result60)) {
        $id         = $row60[0];
        $tel_cislo  = $row60[1];
        $silnice    = $row60[2];
        $kilometr   = $row60[3];
        $smer       = $row60[4];
        $longitude  = $row60[5];
        $latitude   = $row60[6];
        $platnost   = $row60[7];
        $export     = $row60[8];
        $edited     = $row60[9];
        $ssud       = $row60[10];
        $ssud_nazev = "";
        $typ        = $row60[11];
        $typ_nazev  = "";

        $smer_nazev = SmerNazev($silnice, $smer, $kilometr);

        if (substr($silnice, 0, 1) != "D") {
            $silnice = "I/" . $silnice;
        }
        $kilometr = str_replace(".", ",", $kilometr);

        $query237 = "SELECT popis FROM enum_ssud WHERE id = '$ssud';";
        if ($result237 = mysqli_query($link, $query237)) {
            while ($row237 = mysqli_fetch_row($result237)) {
                $ssud_nazev = $row237[0];
            }
        }

        $query237 = "SELECT popis FROM enum_typ WHERE id = '$typ';";
        if ($result237 = mysqli_query($link, $query237)) {
            while ($row237 = mysqli_fetch_row($result237)) {
                $typ_nazev = $row237[0];
            }
        }

        echo "<tr class=\"";
        if ($i % 2 == 0) {
            echo "dark";
        } else {
            echo "light";
        }
        if ($platnost == 0) {
            echo "-strikeout";
        }
        echo "\"><td>&nbsp;</td><td>$tel_cislo</td><td>$silnice</td><td>$kilometr</td><td>$smer_nazev</td><td>$latitude</td><td>$longitude</td><td>$ssud_nazev</td><td>$typ_nazev</td>";

        if ($export == "0") {
            echo "<td>Připraveno k exportu</td>";
        } elseif ($edited == "1") {
            echo "<td>Čeká na schválení O2 ITS</td>";
        } else {
            echo "<td></td>";
        }

        echo "<td><a href=\"edit.php?id=$id&up=$app_up\">Edit</a></td></tr>";
        $i = $i + 1;

    }
}
echo "</table>";

mysqli_close($link);
?>