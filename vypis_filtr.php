<?php
require_once 'config.php';

$tel_cislo = @$_GET['tel_cislo'];
$silnice = @$_GET['silnice'];
$ssud = @$_GET['ssud'];
$typ = @$_GET['typ'];

$dotaz = "WHERE archiv = '0'";
if ($tel_cislo != '') {
    $dotaz .= " AND tel_cislo LIKE '$tel_cislo%'";
}
if ($tel_cislo != '' && $silnice != '') {
    $dotaz .= " AND silnice = '$silnice'";
} else if ($silnice != '') {
    $dotaz .= " AND silnice = '$silnice'";
}
if (($tel_cislo != '' && $ssud != '') || ($silnice != '' && $ssud != '')) {
    $dotaz .= " AND ssud = '$ssud'";
} else if ($ssud != '') {
    $dotaz .= " AND ssud = '$ssud'";
}
if (($tel_cislo != '' && $typ != '') || ($silnice != '' && $typ != '') || ($ssud != '' && $typ != '')) {
    $dotaz .= " AND typ = '$typ'";
} else if ($typ != '') {
    $dotaz .= " AND typ = '$typ'";
}

$app_up = "8";
echo "<table width=\"100%\">";
echo "<tr>";
echo "<th width=\"10\">&nbsp;</th>";
echo "<th width=\"150\">Telefonní číslo</th>";
echo "<th width=\"100\">Silnice</th>";
echo "<th width=\"100\">Kilometr</th>";
echo "<th width=\"300\">Směr</th>";
echo "<th width=\"300\">Zeměpisná šířka</th>";
echo "<th width=\"300\">Zeměpisná délka</th>";
echo "<th width=\"200\">SSÚD</th>";
echo "<th width=\"150\">Typ</th>";
echo "<th>Status</th>";
echo "<th width=\"100\">&nbsp;</th>";
echo "<th width=\"10\">&nbsp;</th>";
echo "</tr>";

$i = 0;
$query47 = "SELECT id, tel_cislo, silnice, kilometr, smer, longitude, latitude, platnost, export, edited, ssud, typ, techno, hlavni FROM hlasky $dotaz ORDER BY tel_cislo;";
if ($result47 = mysqli_query($link, $query47)) {
    while ($row47 = mysqli_fetch_row($result47)) {
        $id = $row47[0];
        $tel_cislo = $row47[1];
        $silnice = $row47[2];
        $kilometr = $row47[3];
        $smer = $row47[4];
        $longitude = $row47[5];
        $latitude = $row47[6];
        $platnost = $row47[7];
        $export = $row47[8];
        $edited = $row47[9];
        $ssud = $row47[10];
        $ssud_nazev = "";
        $typ = $row47[11];
        $typ_nazev = "";
        $techno = $row47[12];
        $hlavni = $row47[13];

        $smer_nazev = SmerNazev($silnice, $smer, $kilometr);

        if (substr($silnice, 0, 1) != "D") {
            $silnice = "I/{$silnice}";
        }
        $kilometr = str_replace(".", ",", $kilometr);

        $query74 = "SELECT popis FROM enum_ssud WHERE id = '$ssud';";
        if ($result74 = mysqli_query($link, $query74)) {
            while ($row74 = mysqli_fetch_row($result74)) {
                $ssud_nazev = $row74[0];
            }
        }

        $query81 = "SELECT popis FROM enum_typ WHERE id = '$typ';";
        if ($result81 = mysqli_query($link, $query81)) {
            while ($row81 = mysqli_fetch_row($result81)) {
                $typ_nazev = $row81[0];
            }
        }

        echo "<tr class=\"";
        echo ($i % 2 == 0) ? "dark" : "light";
        if ($platnost == 0) {
            echo "-strikeout";
        }
        echo "\"><td>&nbsp;</td><td>$tel_cislo</td><td>$silnice</td><td>$kilometr</td><td>$smer_nazev</td><td>$latitude</td><td>$longitude</td><td>$ssud_nazev</td><td>";
        echo $typ_nazev;
        if ($techno == 1) {
            echo " TECHNO";
        }
        echo ($hlavni == "1") ? " hl." : " vedl.";

        if ($export == "0") {
            echo "</td><td>Připraveno k exportu</td>";
        } elseif ($edited == "1") {
            echo "</td><td>Čeká na schválení O2 ITS</td>";
        } else {
            echo "</td><td></td>";
        }

        echo "<td><a href=\"edit.php?id=$id&up=$app_up\" target=\"_blank\">Edit</a></td></tr>";
        $i++;

    }
}
echo "</table>";