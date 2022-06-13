<?php
require_once 'config.php';

$tel_cislo = $_GET['tel_cislo'];
$silnice   = $_GET['silnice'];
$ssud      = $_GET['ssud'];
$typ       = $_GET['typ'];

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

$i      = 0;
$query5 = "SELECT id, tel_cislo, silnice, kilometr, smer, longitude, latitude, platnost, export, edited, ssud, typ FROM hlasky $dotaz ORDER BY tel_cislo;";
if ($result5 = mysqli_query($link, $query5)) {
    while ($row5 = mysqli_fetch_row($result5)) {
        $id         = $row5[0];
        $tel_cislo  = $row5[1];
        $silnice    = $row5[2];
        $kilometr   = $row5[3];
        $smer       = $row5[4];
        $longitude  = $row5[5];
        $latitude   = $row5[6];
        $platnost   = $row5[7];
        $export     = $row5[8];
        $edited     = $row5[9];
        $ssud       = $row5[10];
        $ssud_nazev = "";
        $typ        = $row5[11];
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

        echo "<td><a href=\"edit.php?id=$id&up=$app_up\" target=\"_blank\">Edit</a></td></tr>";
        $i = $i + 1;

    }
}
echo "</table>";
