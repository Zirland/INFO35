<?php
require_once 'config.php';

$OpID = 777;
$current = "";
$today = date("Ymd");

echo "Id: $OpID<br/>";

$query10 = "SELECT tel_cislo, silnice, kilometr, smer, latitude, longitude, okresNazev, obecNazev, obecKod, castObceNazev, castObceKod, techno FROM hlasky WHERE platnost = '1' ORDER BY tel_cislo;";
if ($result10 = mysqli_query($link, $query10)) {
    while ($row10 = mysqli_fetch_row($result10)) {
        $tel_cislo = $row10[0];
        $silnice = $row10[1];
        $kilometr = $row10[2];
        $smer = $row10[3];
        $techno = $row10[11];

        $smer_nazev = SmerNazev($silnice, $smer, $kilometr);

        if (substr($silnice, 0, 1) != "D") {
            $silnice = "I/{$silnice}";
        }
        $kilometr = str_replace(".", ",", $kilometr);

        $uvozeni = ($techno == "1") ? "Technologická místnost" : "SOS hláska";

        $prijmeni = "$uvozeni na $silnice, km $kilometr směr $smer_nazev";

        $latitude = $row10[4];
        $longitude = $row10[5];
        $nazev_okresu = $row10[6];
        $nazev_obce = $row10[7];
        $kod_obce = $row10[8];
        $nazev_casti_obce = $row10[9];
        $kod_casti_obce = $row10[10];

        $jmeno = $smer;
        $nazev_ulice = "";
        $cislo_popisne = "";
        $cislo_orientacni = "";
        $kod_objektu = "";
        $kod_adresy = "";
        $kod_ulice = "";

        $lat_deg = floor($latitude);
        $lat_rest = ($latitude - $lat_deg) * 60;
        $lat_min = floor($lat_rest);
        $lat_rest = ($lat_rest - $lat_min) * 60;

        $lat_deg = ($lat_deg < 10) ? "0{$lat_deg}" : $lat_deg;
        $lat_min = ($lat_min < 10) ? "0{$lat_min}" : $lat_min;
        $lat_sec = ($lat_rest < 10) ? "0{$lat_rest}" : $lat_rest;
        $lat_sec = substr($lat_sec, 0, 6);
        $latitude = "N{$lat_deg}°{$lat_min}'{$lat_sec}";

        $lon_deg = floor($longitude);
        $lon_rest = ($longitude - $lon_deg) * 60;
        $lon_min = floor($lon_rest);
        $lon_rest = ($lon_rest - $lon_min) * 60;

        $lon_deg = ($lon_deg < 10) ? "0{$lon_deg}" : $lon_deg;
        $lon_min = ($lon_min < 10) ? "0{$lon_min}" : $lon_min;
        $lon_sec = ($lon_rest < 10) ? "0{$lon_rest}" : $lon_rest;
        $lon_sec = substr($lon_sec, 0, 6);
        $longitude = "E{$lon_deg}°{$lon_min}'{$lon_sec}";

        $current .= "$prijmeni;$jmeno;;$tel_cislo;$OpID;$nazev_ulice;$cislo_popisne;$cislo_orientacni;$nazev_obce;$nazev_casti_obce;;$nazev_okresu;$longitude;$latitude;$kod_objektu;$kod_adresy;$kod_ulice;$kod_obce;$kod_casti_obce;\n";
    }
    mysqli_free_result($result10);
}

$file = "INFO35_FO1_{$OpID}_{$today}.csv";
file_put_contents($file, $current);

echo "Done...<br />";

echo "<a href=\"$file\" target=\"_blank\">$file</a>";

mysqli_close($link);