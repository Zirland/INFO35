<?php
require_once 'config.php';

$OpID = '111';
$current = "";
$today = date("Ymd");

echo "Id: $OpID<br/>";

$query14 = "SELECT prijmeni, jmeno, tel_cislo, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, nazev_okresu, longitude, latitude, kod_objektu, kod_adresy, kod_ulice, kod_obce, kod_casti_obce FROM stanice WHERE OpID='$OpID';";
if ($result14 = mysqli_query($link, $query14)) {
    while ($row14 = mysqli_fetch_row($result14)) {
        $prijmeni = $row14[0];
        $jmeno = $row14[1];
        $tel_cislo = $row14[2];
        $nazev_ulice = $row14[3];
        $cislo_popisne = $row14[4];
        $cislo_orientacni = $row14[5];
        $nazev_obce = $row14[6];
        $nazev_casti_obce = $row14[7];
        $nazev_okresu = $row14[8];
        $longitude = $row14[9];
        $latitude = $row14[10];
        $kod_objektu = $row14[11];
        $kod_adresy = $row14[12];
        $kod_ulice = $row14[13];
        $kod_obce = $row14[14];
        $kod_casti_obce = $row14[15];

        $lat_deg = floor($latitude);
        $lat_rest = ($latitude - $lat_deg) * 60;
        $lat_min = floor($lat_rest);
        $lat_rest = ($lat_rest - $lat_min) * 60;

        $lat_deg = ($lat_deg < 10) ? "0" . $lat_deg : $lat_deg;
        $lat_min = ($lat_min < 10) ? "0" . $lat_min : $lat_min;
        $lat_sec = ($lat_rest < 10) ? "0" . $lat_rest : $lat_rest;
        $lat_sec = substr(($lat_sec), 0, 6);
        $latitude = "N" . $lat_deg . "°" . $lat_min . "'" . $lat_sec;

        $lon_deg = floor($longitude);
        $lon_rest = ($longitude - $lon_deg) * 60;
        $lon_min = floor($lon_rest);
        $lon_rest = ($lon_rest - $lon_min) * 60;

        $lon_deg = ($lon_deg < 10) ? "0" . $lon_deg : $lon_deg;
        $lon_min = ($lon_min < 10) ? "0" . $lon_min : $lon_min;
        $lon_sec = ($lon_rest < 10) ? "0" . $lon_rest : $lon_rest;
        $lon_sec = substr(($lon_sec), 0, 6);
        $longitude = "E" . $lon_deg . "°" . $lon_min . "'" . $lon_sec;

        $current .= "$prijmeni;$jmeno;;$tel_cislo;$OpID;$nazev_ulice;$cislo_popisne;$cislo_orientacni;$nazev_obce;$nazev_casti_obce;;$nazev_okresu;$longitude;$latitude;$kod_objektu;$kod_adresy;$kod_ulice;$kod_obce;$kod_casti_obce;\n";
    }
    mysqli_free_result($result14);
}

$file = "INFO35_FO1_" . $OpID . "_" . $today . ".csv";
file_put_contents($file, $current);

echo "Done...<br />";

echo "<a href=\"$file\" target=\"_blank\">$file</a>";

mysqli_close($link);