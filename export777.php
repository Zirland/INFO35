<?php
require_once 'config.php';

$OpID    = 777;
$current = "";
$today   = date("Ymd");

echo "Id: $OpID<br/>";

$query14 = "SELECT tel_cislo, silnice, kilometr, smer, latitude, longitude, okresNazev, obecNazev, obecKod, castObceNazev, castObceKod FROM hlasky WHERE platnost = '1' ORDER BY tel_cislo;";
if ($result14 = mysqli_query($link, $query14)) {
    while ($row14 = mysqli_fetch_row($result14)) {
        $tel_cislo = $row14[0];
        $silnice   = $row14[1];
        $kilometr  = $row14[2];
        $smer      = $row14[3];

        $smer_nazev = SmerNazev($silnice, $kilometr);

        if (substr($silnice, 0, 1) != "D") {
            $silnice = "I/" . $silnice;
        }
        $kilometr = str_replace(".", ",", $kilometr);

        switch ($tel_cislo) {
            case '311690000':
            case '321610160':
            case '321610161':
            case '352352796':
            case '352352797':
            case '352352798':
            case '353434086':
            case '353434087':
            case '353434088':
            case '371529259':
            case '371529260':
            case '381201204':
            case '381201262':
            case '381201277':
            case '382201079':
            case '415735455':
            case '558405170':
            case '558416206':
            case '595150460':
            case '595150461':
                $uvozeni = "Technologická místnost";
                break;

            default:
                $uvozeni = "SOS hláska";
                $break;
        }

        $prijmeni = $uvozeni . " na " . $silnice . ", km " . $kilometr . " směr " . $smer_nazev;

        $latitude         = $row14[4];
        $longitude        = $row14[5];
        $nazev_okresu     = $row14[6];
        $nazev_obce       = $row14[7];
        $kod_obce         = $row14[8];
        $nazev_casti_obce = $row14[9];
        $kod_casti_obce   = $row14[10];

        $jmeno            = $smer;
        $nazev_ulice      = "";
        $cislo_popisne    = "";
        $cislo_orientacni = "";
        $kod_objektu      = "";
        $kod_adresy       = "";
        $kod_ulice        = "";

        $lat_deg  = floor($latitude);
        $lat_rest = ($latitude - $lat_deg) * 60;
        $lat_min  = floor($lat_rest);
        $lat_rest = ($lat_rest - $lat_min) * 60;

        $lat_deg  = ($lat_deg < 10) ? "0" . $lat_deg : $lat_deg;
        $lat_min  = ($lat_min < 10) ? "0" . $lat_min : $lat_min;
        $lat_sec  = ($lat_rest < 10) ? "0" . $lat_rest : $lat_rest;
        $lat_sec  = substr(($lat_sec), 0, 6);
        $latitude = "N" . $lat_deg . "°" . $lat_min . "'" . $lat_sec;

        $lon_deg  = floor($longitude);
        $lon_rest = ($longitude - $lon_deg) * 60;
        $lon_min  = floor($lon_rest);
        $lon_rest = ($lon_rest - $lon_min) * 60;

        $lon_deg   = ($lon_deg < 10) ? "0" . $lon_deg : $lon_deg;
        $lon_min   = ($lon_min < 10) ? "0" . $lon_min : $lon_min;
        $lon_sec   = ($lon_rest < 10) ? "0" . $lon_rest : $lon_rest;
        $lon_sec   = substr(($lon_sec), 0, 6);
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
