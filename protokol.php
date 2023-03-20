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
    <title>Protokol z funkční zkoušky</title>
    <style type="text/css">
        table.page {
            width: 19cm;
            margin-left: auto;
            margin-right: auto;
            font-family: arial;
        }

        table.inline {
            border-collapse: collapse;
        }

        td.inline {
            border: 1px solid black;
            font-size: 13px;
            padding: 5px;
        }

        th.inline {
            border: 1px solid black;
            font-size: 13px;
            vertical-align: middle;
            text-align: center;
        }

        .arial22 {
            font-size: 22px;
            text-align: center;
        }

        .arial16 {
            font-size: 16px;
            text-align: center;
        }

        @media print {
            div {
                page-break-inside: avoid;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>

    <?php
    $id = $_GET["id"];

    $query47 = "SELECT datum, silnice, hlasky, projekt, osoba FROM testovani WHERE id = $id;";
    if ($result47 = mysqli_query($link, $query47)) {
        while ($row47 = mysqli_fetch_row($result47)) {
            $datum = $row47[0];
            $silnice = $row47[1];
            $hlasky = $row47[2];
            $projekt = $row47[3];
            $zadatel = $row47[4];

            if (substr($silnice, 0, 1) != "D") {
                $textsilnice = "silnice I/" . $silnice;
            } else {
                $textsilnice = "dálnice " . $silnice;
            }

            $query72 = "SELECT jmeno FROM test_osoby WHERE id = '$zadatel';";
            if ($result72 = mysqli_query($link, $query72)) {
                while ($row72 = mysqli_fetch_row($result72)) {
                    $podpis = $row72[0];
                }
            }

            $query79 = "UPDATE testovani SET overeno = '1' WHERE id = $id;";
            $prikaz79 = mysqli_query($link, $query79);

            $datum_format = date("d.m.Y", strtotime($datum));

            $hlasky_array = explode("|", $hlasky);
            $hlasky_list = implode(",", $hlasky_array);

            $query60 = "SELECT min(CAST(kilometr AS DOUBLE)), max(CAST(kilometr AS DOUBLE)) FROM hlasky WHERE silnice = '$silnice' AND id IN ($hlasky_list);";
            if ($result60 = mysqli_query($link, $query60)) {
                while ($row60 = mysqli_fetch_row($result60)) {
                    $km_min = $row60[0];
                    $km_max = $row60[1];

                    $km_min = str_replace(".", ",", $km_min);
                    $km_max = str_replace(".", ",", $km_max);
                }
            }
        }
    }

    ?>
    <table class="page">
        <tr>
            <td>
                <img src="SPEL.png" style="width:18cm">
                <div class="arial22">&nbsp;<br />Protokol z funkční zkoušky<br />&nbsp;</div>
                <div class="arial16">Telefonické spojení SOS hlásek s linkou 112</div>
                <div class="arial22">&nbsp;<br />&nbsp;</div>

                <table>
                    <tr>
                        <td style="width:5mm">&nbsp;</td>
                        <td style="width:2cm">Projekt:</td>
                        <td>
                            <?php echo $projekt; ?>
                        </td>
                    </tr>

                    <tr>
                        <td>&nbsp;</td>
                        <td>Investor:</td>
                        <td>Ředitelství silnic a dálnic ČR</td>
                    </tr>

                    <tr>
                        <td>&nbsp;</td>
                        <td>Dodavatel:<br />&nbsp;</td>
                        <td>SPEL, a.s. Kolín<br />Třídvorská 1402, 280 02 Kolín V</td>
                    </tr>

                </table>
                <div class="arial22">&nbsp;<br />&nbsp;</div>

                <table class="inline">
                    <tr>
                        <td style="width:5mm">&nbsp;</td>
                        <td>SPEL, a.s. provedl
                            <?php echo $datum_format; ?> funkční zkoušku spojení.<br />
                            Hlásky byly testovány na úsecích
                            <?php echo $textsilnice; ?> (km
                            <?php echo "$km_min – $km_max"; ?>)
                        </td>
                    </tr>
                </table>
                <div class="arial22">&nbsp;</div>
                <?php
                $r = 0;
                $zarizeni = "";
                $query110 = "SELECT typ, kilometr, smer, zkouska, hovorOUT, hovorIN, lokaceSPEL, lokace112, poznamka, `status` FROM hlasky JOIN test_result ON hlasky.id = test_result.id_hlaska WHERE silnice = '$silnice' AND id_test = '$id' ORDER BY CAST(kilometr AS DOUBLE), smer DESC;";
                if ($result110 = mysqli_query($link, $query110)) {
                    while ($row110 = mysqli_fetch_row($result110)) {
                        $typ = $row110[0];
                        $kilometr = $row110[1];
                        $smer = $row110[2];
                        $zkouska = $row110[3];
                        $hovor_out = $row110[4];
                        $hovor_in = $row110[5];
                        $lokaceSPEL = $row110[6];
                        $lokace112 = $row110[7];
                        $poznamka = $row110[8];
                        $status = $row110[9];

                        $smer_nazev = SmerNazev($silnice, $smer, $kilometr);
                        $kilometr = str_replace(".", ",", $kilometr);

                        if (($r == 0) || ($r == 12) || (($r - 12) % 30 == 0)) {
                            $zarizeni .= "<table class=\"inline\">";
                            $zarizeni .= "<tr><th style=\"width:5mm;\">&nbsp;</th><th class=\"inline\">Typ</th><th class=\"inline\">Označení</th><th class=\"inline\">Směr</th><th class=\"inline\">Zkouška</th><th class=\"inline\">SOS–IZS</th><th class=\"inline\">IZS–SOS</th><th class=\"inline\">Pozice SPEL</th><th class=\"inline\">Pozice 112</th><th class=\"inline\">Poznámka</th><th style=\"width:5mm;\">&nbsp;</th></tr>";
                        }

                        $zarizeni .= "<tr><td>";
                        $zarizeni .= "</td><td class=\"inline\" style=\"text-align:center;\">";

                        $query146 = "SELECT popis FROM enum_typ WHERE id = '$typ';";
                        if ($result146 = mysqli_query($link, $query146)) {
                            while ($row146 = mysqli_fetch_row($result146)) {
                                $nazev_typu = $row146[0];
                            }
                        }

                        $zarizeni .= $nazev_typu;
                        $zarizeni .= "</td>";
                        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">$kilometr</td>";
                        $zarizeni .= "<td class=\"inline\">$smer_nazev</td>";
                        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
                        if ($zkouska == "1") {
                            $zarizeni .= "&#9745;";
                        } else {
                            $zarizeni .= "&#9744;";
                        }
                        $zarizeni .= "</td>";

                        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
                        if ($hovor_out == "1") {
                            $zarizeni .= "&#9745;";
                        } else {
                            $zarizeni .= "&#9744;";
                        }
                        $zarizeni .= "</td>";

                        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
                        if ($hovor_in == "1") {
                            $zarizeni .= "&#9745;";
                        } else {
                            $zarizeni .= "&#9744;";
                        }
                        $zarizeni .= "</td>";

                        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
                        if ($lokaceSPEL == "1") {
                            $zarizeni .= "&#9745;";
                        } else {
                            $zarizeni .= "&#9744;";
                        }
                        $zarizeni .= "</td>";

                        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
                        if ($lokace112 == "1") {
                            $zarizeni .= "&#9745;";
                        } else {
                            $zarizeni .= "&#9744;";
                        }
                        $zarizeni .= "</td>";

                        $zarizeni .= "<td class=\"inline\">$poznamka</td>";
                        $zarizeni .= "<td></td>";
                        $zarizeni .= "</tr>";

                        if (($r == 11) || (($r - 12) % 30) == 29) {
                            $zarizeni .= "</table><p class=\"page-break\"></p)>";
                        }

                        $r = $r + 1;
                    }
                }
                ?>
                <table class="inline">
                    <tr>
                        <th style="width:5mm;">&nbsp;</th>
                        <th class="inline">Zařízení</th>
                        <th class="inline">Typ</th>
                        <th class="inline">Počet</th>
                        <th class="inline">Stav</th>
                    </tr>
                    <?php
                    $query180 = "SELECT smer, typ, count(*) FROM hlasky JOIN test_result ON hlasky.id = test_result.id_hlaska WHERE silnice = '$silnice' AND id_test = '$id' GROUP BY smer, typ ORDER BY smer DESC;";
                    if ($result180 = mysqli_query($link, $query180)) {
                        while ($row180 = mysqli_fetch_row($result180)) {
                            $smer_hlasky = $row180[0];
                            $typ_hlasky = $row180[1];
                            $pocet_hlasek = $row180[2];

                            if ($smer_hlasky == "+") {
                                $hlavni = "Hláska hlavní";
                            } else {
                                $hlavni = "Hláska vedlejší";
                            }

                            $query211 = "SELECT popis FROM enum_typ WHERE id = '$typ_hlasky';";
                            if ($result211 = mysqli_query($link, $query211)) {
                                while ($row211 = mysqli_fetch_row($result211)) {
                                    $nazevtypu = $row211[0];
                                }
                            }

                            $stav = 0;
                            $query187 = "SELECT typ, smer, SUM(`status`) FROM hlasky JOIN test_result ON hlasky.id = test_result.id_hlaska WHERE silnice = '$silnice' AND id_test = '$id' GROUP BY typ, smer ORDER BY typ, smer DESC;";
                            if ($result187 = mysqli_query($link, $query187)) {
                                while ($row187 = mysqli_fetch_row($result187)) {
                                    $typ_kontrola = $row187[0];
                                    $smer_kontrola = $row187[1];
                                    $status_kontrola = $row187[2];

                                    if ($typ_hlasky == $typ_kontrola && $smer_kontrola == $smer_hlasky) {
                                        $stav = $stav + $status_kontrola;
                                    }
                                }
                            }

                            echo "<tr><td></td><td class=\"inline\">$hlavni</td><td class=\"inline\" style=\"text-align:center;\">$nazevtypu</td><td class=\"inline\" style=\"text-align:center;\">$pocet_hlasek</td><td class=\"inline\" style=\"text-align:center;\">";
                            if ($stav > 0) {
                                echo "Chyba";
                            } else {
                                echo "OK";
                            }
                            echo "</td></tr>";
                        }
                    }
                    ?>
                </table>
                <p>&nbsp;</p>
                <table>
                    <tr>
                        <td style="width:5mm;">&nbsp;</td>
                        <td>Funkční zkouška systému:</td>
                    </tr>
                </table>
                <?php
                echo $zarizeni;
                ?>
                <table>
                    <tr>
                        <td style="width:5mm;">&nbsp;</td>
                        <td style="font-size:13px;"><i>Zkouška spojení – test volání ze SOS hlásky do veřejné
                                telekomunikační sítě.</i><br />
                            <i>SOS–IZS – test volání ze SOS hlásky na telefonní linku 112.</i><br />
                            <i>IZS–SOS – test volání z telefonní linky 112 na SOS hlásku.</i><br />
                            <i>Pozice SPEL – kontrola údajů evidovaných u dodavatele.</i><br />
                            <i>Pozice 112 – kontrola údajů zobrazených na lince 112.</i><br />
                        </td>
                    </tr>
                </table>
                <p>&nbsp;</p>
                <div>
                    <table>
                        <tr>
                            <td style="width:5mm;">&nbsp;</td>
                            <td>Funkční zkoušky provedli:</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width:5mm;">&nbsp;</td>
                            <td>Za dodavatele<br />SPEL, a.s.</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width:5mm;">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width:5mm;">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>
                                <?php echo $podpis; ?>
                            </td>
                            <td style="text-align:center;">&nbsp;&nbsp;……………………<br />podpis</td>
                        </tr>
        </tr>
        <tr>
            <td style="width:5mm;">&nbsp;</td>
            <td>Za TCTV 112<br />&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td style="width:5mm;">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td style="width:5mm;">&nbsp;</td>
            <td>&nbsp;</td>
            <td>Ing. Bessa Urbánek Jan</td>
            <td style="text-align:center;">&nbsp;&nbsp;……………………<br />podpis</td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <table>
        <tr>
            <td style="width:5mm;">&nbsp;</td>
            <td>V Kolíně dne
                <?php
                $dnes_datum = date("d.m.Y", time());
                echo $dnes_datum; ?>
            </td>
        </tr>
    </table>
    </div>
    </td>
    </tr>
    </table>
</body>

</html>

<?php
mysqli_close($link);
?>