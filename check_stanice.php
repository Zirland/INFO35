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
include 'Converter.php';
$converter = new JTSK\Converter();

$query14 = "SELECT tel_cislo FROM check_st;";
if ($result14 = mysqli_query($link, $query14)) {
    $rows = mysqli_num_rows($result14);
    echo "Zbývá zkontrolovat $rows záznamů<br/>";
    $row14 = mysqli_fetch_row($result14);
    $check_id = $row14[0];
}

echo "$check_id<br/>";
$error = 0;

$query15 = "SELECT nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, nazev_okresu, longitude, latitude, kod_objektu, kod_adresy, kod_obce, kod_casti_obce, kod_ulice FROM stanice WHERE tel_cislo = '$check_id';";
echo "$query15<br/>";
if ($result15 = mysqli_query($link, $query15)) {
    while ($row15 = mysqli_fetch_row($result15)) {
        $nazev_ulice = $row15[0];
        $cislo_popisne = $row15[1];
        $cislo_orientacni = $row15[2];
        $nazev_obce = $row15[3];
        $nazev_casti_obce = $row15[4];
        $nazev_okresu = $row15[5];
        $longitude = $row15[6];
        $latitude = $row15[7];
        $kod_objektu = $row15[8];
        $kod_adresy = $row15[9];
        $kod_obce = $row15[10];
        $kod_casti_obce = $row15[11];
        $kod_ulice = $row15[12];

        if ($kod_adresy != '') {
            $url = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/1/query?where=kod%3D$kod_adresy&outFields=*&f=pjson";
            $response = file_get_contents($url);
            $vysledek = json_decode($response, $assoc = true);
            $items = $vysledek['features'][0];

            $new_cislo_popisne = $items['attributes']['cislodomovni'];
            $adresaOrientacniCislo = $items['attributes']['cisloorientacni'];
            $adresaOrientacniPismeno = $items['attributes']['cisloorientacnipismeno'];
            $new_cislo_orientacni = $adresaOrientacniCislo . $adresaOrientacniPismeno;
            $new_kod_objektu = $items['attributes']['stavebniobjekt'];
            $new_kod_ulice = $items['attributes']['ulice'];

            $sour_X = $items['geometry']['x'] * -1;
            $x = $items['geometry']['x'];
            $sour_Y = $items['geometry']['y'] * -1;
            $y = $items['geometry']['y'];

            $url2 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/4/query?where=kod%3D$new_kod_ulice&outFields=*&f=pjson";
            $response2 = file_get_contents($url2);
            $vysledek2 = json_decode($response2, $assoc = true);
            $items2 = $vysledek2['features'][0];

            $new_nazev_ulice = $items2['attributes']['nazev'];

            $url3 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/3/query?where=kod%3D$new_kod_objektu&outFields=*&f=pjson";
            $response3 = file_get_contents($url3);
            $vysledek3 = json_decode($response3, $assoc = true);
            $items3 = $vysledek3['features'][0];

            $new_kod_casti_obce = $items3['attributes']['castobce'];

            $url4 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/11/query?where=kod%3D$new_kod_casti_obce&outFields=*&f=pjson";
            $response4 = file_get_contents($url4);
            $vysledek4 = json_decode($response4, $assoc = true);
            $items4 = $vysledek4['features'][0];

            $new_nazev_casti_obce = $items4['attributes']['nazev'];
            $new_kod_obce = $items4['attributes']['obec'];

            $url5 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/12/query?where=kod%3D$new_kod_obce&outFields=*&f=pjson";
            $response5 = file_get_contents($url5);
            $vysledek5 = json_decode($response5, $assoc = true);
            $items5 = $vysledek5['features'][0];

            $new_nazev_obce = $items5['attributes']['nazev'];
            $new_kod_okresu = $items5['attributes']['okres'];

            $url6 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/15/query?where=kod%3D$new_kod_okresu&outFields=*&f=pjson";
            $response6 = file_get_contents($url6);
            $vysledek6 = json_decode($response6, $assoc = true);
            $items6 = $vysledek6['features'][0];

            $new_nazev_okresu = $items6['attributes']['nazev'];

            $latlon = $converter->JTSKtoWGS84($sour_Y, $sour_X); // returns array ['lat', 'lon']

            $new_latitude = $latlon['lat'];
            $new_longitude = $latlon['lon'];

            if ($new_kod_casti_obce == "") {
                echo "Chyba v záznamu čísla $check_id<br/>";

                echo "Ulice: $nazev_ulice = $new_nazev_ulice<br/>";
                echo "Popisne: $cislo_popisne = $new_cislo_popisne<br/>";
                echo "Orientacni: $cislo_orientacni = $new_cislo_orientacni<br/>";
                echo "Obec: $nazev_obce = $new_nazev_obce<br/>";
                echo "Cast: $nazev_casti_obce = $new_nazev_casti_obce<br/>";
                echo "Okres: $nazev_okresu = $new_nazev_okresu<br/>";
                echo "LON: $longitude = $new_longitude<br/>";
                echo "LAT: $latitude = $new_latitude<br/>";
                echo "OBJ: $kod_objektu = $new_kod_objektu<br/>";
                echo "Kod obce: $kod_obce = $new_kod_obce<br/>";
                echo "Kod casti: $kod_casti_obce = $new_kod_casti_obce<br/>";
                echo "Kod ulice: $kod_ulice = $new_kod_ulice<br/>";

                $error = 1;
                $url7 = "https://gis.izscr.cz/arcgis/rest/services/terinos_sluzby/cast_obce/MapServer/0/query?where=&text=&objectIds=&time=&geometry=%7B%22spatialReference%22%3A%7B%22wkid%22%3A102067%7D%2C%22x%22%3A$x%2C%22y%22%3A$y%7D&geometryType=esriGeometryPoint&inSR=102067&spatialRel=esriSpatialRelIntersects&relationParam=&outFields=naz_okres%2Cnaz_obec%2Ckod_obec%2Cnaz_cast%2Ckod_cast&returnGeometry=false&returnTrueCurves=false&maxAllowableOffset=&geometryPrecision=&outSR=&having=&returnIdsOnly=false&returnCountOnly=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&returnZ=false&returnM=false&gdbVersion=&historicMoment=&returnDistinctValues=false&resultOffset=&resultRecordCount=&queryByDistance=&returnExtentOnly=false&datumTransformation=&parameterValues=&rangeValues=&quantizationParameters=&f=json";

                $response7 = file_get_contents($url7);
                $vysledek7 = json_decode($response7, $assoc = true);

                $items7 = $vysledek7['features'][0];

                $new_nazev_okresu = $items7['attributes']['naz_okres'];
                $new_nazev_obce = $items7['attributes']['naz_obec'];
                $new_kod_obce = $items7['attributes']['kod_obec'];
                $new_nazev_casti_obce = $items7['attributes']['naz_cast'];
                $new_kod_casti_obce = $items7['attributes']['kod_cast'];

                echo "Ulice: $nazev_ulice = $new_nazev_ulice<br/>";
                echo "Popisne: $cislo_popisne = $new_cislo_popisne<br/>";
                echo "Orientacni: $cislo_orientacni = $new_cislo_orientacni<br/>";
                echo "Obec: $nazev_obce = $new_nazev_obce<br/>";
                echo "Cast: $nazev_casti_obce = $new_nazev_casti_obce<br/>";
                echo "Okres: $nazev_okresu = $new_nazev_okresu<br/>";
                echo "LON: $longitude = $new_longitude<br/>";
                echo "LAT: $latitude = $new_latitude<br/>";
                echo "OBJ: $kod_objektu = $new_kod_objektu<br/>";
                echo "Kod obce: $kod_obce = $new_kod_obce<br/>";
                echo "Kod casti: $kod_casti_obce = $new_kod_casti_obce<br/>";
                echo "Kod ulice: $kod_ulice = $new_kod_ulice<br/>";

            }
        } else {
            echo "Chybějící adresa u čísla $check_id<br/>";
            $error = 1;
        }

        if ($error == 0) {
            if ($nazev_ulice != $new_nazev_ulice) {
                echo "$nazev_ulice = $new_nazev_ulice<br/>";
                $update = "UPDATE stanice SET nazev_ulice = '$new_nazev_ulice' WHERE tel_cislo = '$check_id';";
                $prikaz_up = mysqli_query($link, $update);
            }
            if ($cislo_popisne != $new_cislo_popisne) {
                echo "$cislo_popisne = $new_cislo_popisne<br/>";
                $update = "UPDATE stanice SET cislo_popisne = '$new_cislo_popisne' WHERE tel_cislo = '$check_id';";
                $prikaz_up = mysqli_query($link, $update);
            }
            if ($cislo_orientacni != $new_cislo_orientacni) {
                echo "$cislo_orientacni = $new_cislo_orientacni<br/>";
                $update = "UPDATE stanice SET cislo_orientacni = '$new_cislo_orientacni' WHERE tel_cislo = '$check_id';";
                $prikaz_up = mysqli_query($link, $update);
            }
            if ($nazev_obce != $new_nazev_obce) {
                echo "$nazev_obce = $new_nazev_obce<br/>";
                $update = "UPDATE stanice SET nazev_obce = '$new_nazev_obce' WHERE tel_cislo = '$check_id';";
                $prikaz_up = mysqli_query($link, $update);
            }
            if ($nazev_casti_obce != $new_nazev_casti_obce) {
                echo "$nazev_casti_obce = $new_nazev_casti_obce<br/>";
                $update = "UPDATE stanice SET nazev_casti_obce = '$new_nazev_casti_obce' WHERE tel_cislo = '$check_id';";
                $prikaz_up = mysqli_query($link, $update);
            }
            if ($nazev_okresu != $new_nazev_okresu) {
                echo "$nazev_okresu = $new_nazev_okresu<br/>";
                $update = "UPDATE stanice SET nazev_okresu = '$new_nazev_okresu' WHERE tel_cislo = '$check_id';";
                $prikaz_up = mysqli_query($link, $update);
            }
            if ($longitude != $new_longitude) {
                echo "$longitude = $new_longitude<br/>";
                $update = "UPDATE stanice SET longitude = '$new_longitude' WHERE tel_cislo = '$check_id';";
                $prikaz_up = mysqli_query($link, $update);
            }
            if ($latitude != $new_latitude) {
                echo "$latitude = $new_latitude<br/>";
                $update = "UPDATE stanice SET latitude = '$new_latitude' WHERE tel_cislo = '$check_id';";
                $prikaz_up = mysqli_query($link, $update);
            }
            if ($kod_objektu != $new_kod_objektu) {
                echo "$kod_objektu = $new_kod_objektu<br/>";
                $update = "UPDATE stanice SET kod_objektu = '$new_kod_objektu' WHERE tel_cislo = '$check_id';";
                $prikaz_up = mysqli_query($link, $update);
            }
            if ($kod_obce != $new_kod_obce) {
                echo "$kod_obce = $new_kod_obce<br/>";
                $update = "UPDATE stanice SET kod_obce = '$new_kod_obce' WHERE tel_cislo = '$check_id';";
                $prikaz_up = mysqli_query($link, $update);
            }
            if ($kod_casti_obce != $new_kod_casti_obce) {
                echo "$kod_casti_obce = $new_kod_casti_obce<br/>";
                $update = "UPDATE stanice SET kod_casti_obce = '$new_kod_casti_obce' WHERE tel_cislo = '$check_id';";
                $prikaz_up = mysqli_query($link, $update);
            }
            if ($kod_ulice != $new_kod_ulice) {
                echo "$kod_ulice = $new_kod_ulice<br/>";
                $update = "UPDATE stanice SET kod_ulice = '$new_kod_ulice' WHERE tel_cislo = '$check_id';";
                $prikaz_up = mysqli_query($link, $update);
            }

            $del_check = "DELETE FROM check_st WHERE tel_cislo = '$check_id';";
            echo "$del_check<br/>";
            $prikaz_del = mysqli_query($link, $del_check);

            echo "<meta http-equiv=\"refresh\" content=\"1\">";
        }
    }
}