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

    $query62 = "SELECT id, tel_cislo, silnice, kilometr, smer, longitude, latitude, platnost, export, edited, ssud, typ FROM hlasky WHERE archiv='0' ORDER BY tel_cislo";
    if ($result62 = mysqli_query($link, $query62)) {
        while ($row62 = mysqli_fetch_row($result62)) {
            $id = $row62[0];
            $tel_cislo = $row62[1];
            $silnice = $row62[2];
            $kilometr = $row62[3];
            $smer = $row62[4];
            $longitude = $row62[5];
            $latitude = $row62[6];
            $platnost = $row62[7];
            $export = $row62[8];
            $edited = $row62[9];
            $ssud = $row62[10];
            $ssud_nazev = "";
            $typ = $row62[11];
            $typ_nazev = "";

            $smer_nazev = SmerNazev($silnice, $smer, $kilometr);

            if (substr($silnice, 0, 1) != "D") {
                $silnice = "I/{$silnice}";
            }
            $kilometr = str_replace(".", ",", $kilometr);

            $query87 = "SELECT popis FROM enum_ssud WHERE id = '$ssud';";
            if ($result87 = mysqli_query($link, $query87)) {
                while ($row87 = mysqli_fetch_row($result87)) {
                    $ssud_nazev = $row87[0];
                }
            }

            $query94 = "SELECT popis FROM enum_typ WHERE id = '$typ';";
            if ($result94 = mysqli_query($link, $query94)) {
                while ($row94 = mysqli_fetch_row($result94)) {
                    $typ_nazev = $row94[0];
                }
            }

            echo "<tr class=\"";
            echo ($i % 2 == 0) ? "dark" : "light";
            if ($platnost == 0) {
                echo "-strikeout";
            }
            echo "\"><td>&nbsp;</td><td>$tel_cislo</td><td>$silnice</td><td>$kilometr</td><td>$smer_nazev</td><td>$latitude</td><td>$longitude</td><td>$ssud_nazev</td><td>";
            if ($techno == 1) {
                echo "TECHNO ";
            }
            echo "$typ_nazev</td>";

            if ($export == "0") {
                echo "<td>Připraveno k exportu</td>";
            } elseif ($edited == "1") {
                echo "<td>Čeká na schválení O2 ITS</td>";
            } else {
                echo "<td></td>";
            }

            echo "<td><a href=\"edit.php?id=$id&up=$app_up\">Edit</a></td></tr>";
            $i++;

        }
    }
    echo "</table>";

    mysqli_close($link);
    ?>