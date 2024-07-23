<?php
require_once 'config.php';

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
}

if ($id == "") {
    echo "<form action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\" method=\"post\">";
    echo "Enter ID: <input type=\"text\" name=\"id\" />";
    echo "001 Geis<br/>";
    echo "002 Kooperativa<br/>";
    echo "111 Komerční banka<br/>";
    echo "222 Fio<br/>";
    echo "444 Česká pošta<br/>";
    echo "555 HZS ČR<br/>";
    echo "<input type=\"submit\" value=\"Submit\" />";
    echo "</form>";
    exit();
}

$current = "";
$today = date("Ymd");

echo "Id: $OpID<br/>";

$query29 = "SELECT prijmeni, jmeno, tel_cislo, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, nazev_okresu, longitude, latitude, kod_objektu, kod_adresy, kod_ulice, kod_obce, kod_casti_obce FROM stanice WHERE OpID='$OpID';";
if ($result29 = mysqli_query($link, $query29)) {
    while ($row29 = mysqli_fetch_row($result29)) {
        $prijmeni = $row29[0];
        $jmeno = $row29[1];
        $tel_cislo = $row29[2];
        $nazev_ulice = $row29[3];
        $cislo_popisne = $row29[4];
        $cislo_orientacni = $row29[5];
        $nazev_obce = $row29[6];
        $nazev_casti_obce = $row29[7];
        $nazev_okresu = $row29[8];
        $longitude = $row29[9];
        $latitude = $row29[10];
        $kod_objektu = $row29[11];
        $kod_adresy = $row29[12];
        $kod_ulice = $row29[13];
        $kod_obce = $row29[29];
        $kod_casti_obce = $row29[15];

        $lat_deg = floor($latitude);
        $lat_rest = ($latitude - $lat_deg) * 60;
        $lat_min = floor($lat_rest);
        $lat_rest = ($lat_rest - $lat_min) * 60;

        $lat_deg = ($lat_deg < 10) ? "0{$lat_deg}" : $lat_deg;
        $lat_min = ($lat_min < 10) ? "0{$lat_min}" : $lat_min;
        $lat_sec = ($lat_rest < 10) ? "0{$lat_rest}" : $lat_rest;
        $lat_sec = substr(($lat_sec), 0, 6);
        $latitude = "N{$lat_deg}°{$lat_min}'{$lat_sec}";

        $lon_deg = floor($longitude);
        $lon_rest = ($longitude - $lon_deg) * 60;
        $lon_min = floor($lon_rest);
        $lon_rest = ($lon_rest - $lon_min) * 60;

        $lon_deg = ($lon_deg < 10) ? "0{$lon_deg}" : $lon_deg;
        $lon_min = ($lon_min < 10) ? "0{$lon_min}" : $lon_min;
        $lon_sec = ($lon_rest < 10) ? "0{$lon_rest}" : $lon_rest;
        $lon_sec = substr(($lon_sec), 0, 6);
        $longitude = "E{$lon_deg}°{$lon_min}'{$lon_sec}";

        $current .= "$prijmeni;$jmeno;;$tel_cislo;$OpID;$nazev_ulice;$cislo_popisne;$cislo_orientacni;$nazev_obce;$nazev_casti_obce;;$nazev_okresu;$longitude;$latitude;$kod_objektu;$kod_adresy;$kod_ulice;$kod_obce;$kod_casti_obce;\n";
    }
    mysqli_free_result($result14);
}

$file = "INFO35_FO1_{$OpID}_{$today}.csv";
file_put_contents($file, $current);

echo "Done...<br />";

echo "<a href=\"$file\" target=\"_blank\">$file</a>";

mysqli_close($link);