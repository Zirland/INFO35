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
    PageHeader();

    echo "<table width=\"100%\">";
    echo "<tr><th>&nbsp;</th><th>Telefonní číslo</th><th>Silnice</th><th>Kilometr</th><th>Směr</th><th>Zeměpisná šířka</th><th>Zeměpisná délka</th><th>SSÚD</th><th>Status</th><th></th></tr>";
    $i = 0;

    $sql = "SELECT id, tel_cislo, silnice, kilometr, smer, longitude, latitude, platnost, export, edited, ssud FROM hlasky ORDER BY tel_cislo";

    if ($stmt = mysqli_prepare($link, $sql)) {
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $id, $tel_cislo, $silnice, $kilometr, $smer, $longitude, $latitude, $platnost, $export, $edited, $ssud);

            while (mysqli_stmt_fetch($stmt)) {
                switch ($silnice) {
                    case 'D0':
                        if ($smer == "+" && $kilometr < 30) {
                            $smer_nazev = "letiště";
                        } elseif ($smer == "+" && $kilometr < 65) {
                            $smer_nazev = "Štěrboholy";
                        } elseif ($smer == "+") {
                            $smer_nazev = "letiště";
                        } elseif ($smer == "-" && $kilometr > 65) {
                            $smer_nazev = "Brno";
                        } elseif ($smer == "-" && $kilometr > 30) {
                            $smer_nazev = "Liberec";
                        } else {
                            $smer_nazev = "Brno";
                        }
                        break;

                    case 'D1':
                        if ($smer == "+" && $kilometr < 189) {
                            $smer_nazev = "Brno";
                        } elseif ($smer == "+" && $kilometr < 273) {
                            $smer_nazev = "Hulín";
                        } elseif ($smer == "+") {
                            $smer_nazev = "Bohumín";
                        } elseif ($smer == "-" && $kilometr > 273) {
                            $smer_nazev = "Přerov";
                        } elseif ($smer == "-" && $kilometr > 203) {
                            $smer_nazev = "Brno";
                        } else {
                            $smer_nazev = "Praha";
                        }
                        break;

                    case 'D2':
                        if ($smer == "+") {
                            $smer_nazev = "Lanžhot";
                        } else {
                            $smer_nazev = "Brno";
                        }
                        break;

                    case 'D3':
                        if ($smer == "+") {
                            $smer_nazev = "České Budějovice";
                        } else {
                            $smer_nazev = "Praha";
                        }
                        break;

                    case 'D4':
                        if ($smer == "+") {
                            $smer_nazev = "Písek";
                        } else {
                            $smer_nazev = "Praha";
                        }
                        break;

                    case 'D5':
                        if ($smer == "+") {
                            $smer_nazev = "Rozvadov";
                        } else {
                            $smer_nazev = "Praha";
                        }
                        break;

                    case 'D6':
                        if ($smer == "+" && $kilometr < 112) {
                            $smer_nazev = "Karlovy Vary";
                        } elseif ($smer == "+") {
                            $smer_nazev = "Cheb";
                        } elseif ($smer == "-" && $kilometr > 112) {
                            $smer_nazev = "Karlovy Vary";
                        } else {
                            $smer_nazev = "Praha";
                        }
                        break;

                    case 'D7':
                        if ($smer == "+") {
                            $smer_nazev = "Chomutov";
                        } else {
                            $smer_nazev = "Praha";
                        }
                        break;

                    case 'D8':
                        if ($smer == "+") {
                            $smer_nazev = "Petrovice";
                        } else {
                            $smer_nazev = "Praha";
                        }
                        break;

                    case 'D11':
                        if ($smer == "+") {
                            $smer_nazev = "Hradec Králové";
                        } else {
                            $smer_nazev = "Praha";
                        }
                        break;

                    case 'D35':
                        if ($smer == "+" && $kilometr < 140) {
                            $smer_nazev = "Opatovice nad Labem";
                        } elseif ($smer == "+") {
                            $smer_nazev = "Lipník nad Bečvou";
                        } elseif ($smer == "-" && $kilometr > 220) {
                            $smer_nazev = "Mohelnice";
                        } else {
                            $smer_nazev = "Praha";
                        }
                        break;

                    case '35':
                        if ($smer == "+") {
                            $smer_nazev = "Valašské Meziříčí";
                        } else {
                            $smer_nazev = "Hranice";
                        }
                        break;

                    case 'D48':
                        if ($smer == "+") {
                            $smer_nazev = "Český Těšín";
                        } else {
                            $smer_nazev = "Bělotín";
                        }
                        break;

                    case 'D55':
                        if ($smer == "+") {
                            $smer_nazev = "Zlín";
                        } else {
                            $smer_nazev = "Kroměříž";
                        }
                        break;
                    case '57':
                        if ($smer == "+") {
                            $smer_nazev = "Vsetín";
                        } else {
                            $smer_nazev = "Valašské Meziříčí";
                        }
                        break;

                    case '58':
                        if ($smer == "+") {
                            $smer_nazev = "Ostrava";
                        } else {
                            $smer_nazev = "Rožnov pod Radhošťem";
                        }
                        break;

                    default:
                        $smer_nazev = $smer;
                        break;
                }

                if (substr($silnice, 0, 1) != "D") {
                    $silnice = "I/" . $silnice;
                }
                $kilometr = str_replace(".", ",", $kilometr);

                $query633 = "SELECT popis FROM enum_ssud WHERE id = $ssud;";
                if ($result633 = mysqli_query($link, $query633)) {
                    while ($row633 = mysqli_fetch_row($result633)) {
                        $ssud_nazev = $row633[0];
                
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
                echo "\"><td>&nbsp;</td><td>$tel_cislo</td><td>$silnice</td><td>$kilometr</td><td>$smer_nazev</td><td>$latitude</td><td>$longitude</td><td>$ssud_nazev</td>";

                if ($export == "0") {
                    echo "<td>Připraveno k exportu</td>";
                } elseif ($edited == "1") {
                    echo "<td>Čeká na schválení HZS ČR</td>";
                } else {
                    echo "<td></td>";
                }

                echo "<td><a href=\"edit.php?id=$id\">Edit</a></td></tr>";
                $i = $i + 1;
            }
        }
    }
    mysqli_stmt_close($stmt);

    echo "</table>";

    mysqli_close($link);
    ?>