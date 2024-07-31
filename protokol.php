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

    $query73 = "SELECT datum, silnice, hlasky, projekt, osoba FROM testovani WHERE id = $id;";
    if ($result73 = mysqli_query($link, $query73)) {
        while ($row73 = mysqli_fetch_row($result73)) {
            $datum = $row73[0];
            $silnice = $row73[1];
            $hlasky = $row73[2];
            $projekt = $row73[3];
            $zadatel = $row73[4];

            $textsilnice = (substr($silnice, 0, 1) != "D") ? "silnice I/{$silnice}" : "dálnice {$silnice}";

            $query84 = "SELECT jmeno FROM test_osoby WHERE id = '$zadatel';";
            if ($result84 = mysqli_query($link, $query84)) {
                while ($row84 = mysqli_fetch_row($result84)) {
                    $podpis = $row84[0];
                }
            }

            $query91 = "UPDATE testovani SET overeno = '1' WHERE id = $id;";
            $prikaz91 = mysqli_query($link, $query91);

            $datum_format = date("d.m.Y", strtotime($datum));

            $hlasky_array = explode("|", $hlasky);
            $hlasky_list = implode(",", $hlasky_array);

            $query99 = "SELECT min(CAST(kilometr AS DOUBLE)), max(CAST(kilometr AS DOUBLE)) FROM hlasky WHERE silnice = '$silnice' AND id IN ($hlasky_list);";
            if ($result99 = mysqli_query($link, $query99)) {
                while ($row99 = mysqli_fetch_row($result99)) {
                    $km_min = $row99[0];
                    $km_max = $row99[1];

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
                $query160 = "SELECT typ, kilometr, smer, zkouska, hovorOUT, hovorIN, lokaceSPEL, lokace112, poznamka, `status` FROM hlasky JOIN test_result ON hlasky.id = test_result.id_hlaska WHERE silnice = '$silnice' AND id_test = '$id' ORDER BY CAST(kilometr AS DOUBLE), smer DESC;";
                if ($result160 = mysqli_query($link, $query160)) {
                    while ($row160 = mysqli_fetch_row($result160)) {
                        $typ = $row160[0];
                        $kilometr = $row160[1];
                        $smer = $row160[2];
                        $zkouska = $row160[3];
                        $hovor_out = $row160[4];
                        $hovor_in = $row160[5];
                        $lokaceSPEL = $row160[6];
                        $lokace112 = $row160[7];
                        $poznamka = $row160[8];
                        $status = $row160[9];

                        $smer_nazev = SmerNazev($silnice, $smer, $kilometr);
                        $kilometr = str_replace(".", ",", $kilometr);

                        if (($r == 0) || ($r == 12) || (($r - 12) % 30 == 0)) {
                            $zarizeni .= "<table class=\"inline\">";
                            $zarizeni .= "<tr><th style=\"width:5mm;\">&nbsp;</th><th class=\"inline\">Typ</th><th class=\"inline\">Označení</th><th class=\"inline\">Směr</th><th class=\"inline\">Zkouška</th><th class=\"inline\">SOS–IZS</th><th class=\"inline\">IZS–SOS</th><th class=\"inline\">Pozice SPEL</th><th class=\"inline\">Pozice 112</th><th class=\"inline\">Poznámka</th><th style=\"width:5mm;\">&nbsp;</th></tr>";
                        }

                        $zarizeni .= "<tr><td>";
                        $zarizeni .= "</td><td class=\"inline\" style=\"text-align:center;\">";

                        $query185 = "SELECT popis FROM enum_typ WHERE id = '$typ';";
                        if ($result185 = mysqli_query($link, $query185)) {
                            while ($row185 = mysqli_fetch_row($result185)) {
                                $nazev_typu = $row185[0];
                            }
                        }

                        $zarizeni .= $nazev_typu;
                        $zarizeni .= "</td>";
                        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">$kilometr</td>";
                        $zarizeni .= "<td class=\"inline\">$smer_nazev</td>";
                        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
                        $zarizeni .= ($zkouska == "1") ? "&#9745;" : "&#9744;";
                        $zarizeni .= "</td>";

                        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
                        $zarizeni .= ($hovor_out == "1") ? "&#9745;" : "&#9744;";
                        $zarizeni .= "</td>";

                        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
                        $zarizeni .= ($hovor_in == "1") ? "&#9745;" : "&#9744;";
                        $zarizeni .= "</td>";

                        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
                        $zarizeni .= ($lokaceSPEL == "1") ? "&#9745;" : "&#9744;";
                        $zarizeni .= "</td>";

                        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
                        $zarizeni .= ($lokace112 == "1") ? "&#9745;" : "&#9744;";
                        $zarizeni .= "</td>";

                        $zarizeni .= "<td class=\"inline\">$poznamka</td>";
                        $zarizeni .= "<td></td>";
                        $zarizeni .= "</tr>";

                        if (($r == 11) || (($r - 12) % 30) == 29) {
                            $zarizeni .= "</table><p class=\"page-break\"></p)>";
                        }

                        $r++;
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
                    $query237 = "SELECT smer, typ, count(*) FROM hlasky JOIN test_result ON hlasky.id = test_result.id_hlaska WHERE silnice = '$silnice' AND id_test = '$id' GROUP BY smer, typ ORDER BY smer DESC;";
                    if ($result237 = mysqli_query($link, $query237)) {
                        while ($row237 = mysqli_fetch_row($result237)) {
                            $smer_hlasky = $row237[0];
                            $typ_hlasky = $row237[1];
                            $pocet_hlasek = $row237[2];

                            $hlavni = ($smer_hlasky == "+") ? "Hláska hlavní" : "Hláska vedlejší";

                            $query246 = "SELECT popis FROM enum_typ WHERE id = '$typ_hlasky';";
                            if ($result246 = mysqli_query($link, $query246)) {
                                while ($row246 = mysqli_fetch_row($result246)) {
                                    $nazevtypu = $row246[0];
                                }
                            }

                            $stav = 0;
                            $query254 = "SELECT typ, smer, SUM(`status`) FROM hlasky JOIN test_result ON hlasky.id = test_result.id_hlaska WHERE silnice = '$silnice' AND id_test = '$id' GROUP BY typ, smer ORDER BY typ, smer DESC;";
                            if ($result254 = mysqli_query($link, $query254)) {
                                while ($row254 = mysqli_fetch_row($result254)) {
                                    $typ_kontrola = $row254[0];
                                    $smer_kontrola = $row254[1];
                                    $status_kontrola = $row254[2];

                                    if ($typ_hlasky == $typ_kontrola && $smer_kontrola == $smer_hlasky) {
                                        $stav += $status_kontrola;
                                    }
                                }
                            }

                            echo "<tr><td></td><td class=\"inline\">$hlavni</td><td class=\"inline\" style=\"text-align:center;\">$nazevtypu</td><td class=\"inline\" style=\"text-align:center;\">$pocet_hlasek</td><td class=\"inline\" style=\"text-align:center;\">";
                            echo ($stav > 0) ? "Chyba" : "OK";
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