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

$start = $_GET["start"];
$end = $start + 30;

$query19 = "SELECT id, latitude, longitude FROM hlasky WHERE id >= $start AND id < $end ORDER BY id;";
if ($result19 = mysqli_query($link, $query19)) {
    while ($row19 = mysqli_fetch_row($result19)) {
        $id = $row19[0];
        $latitude = $row19[1];
        $longitude = $row19[2];

        $coord = $converter->WGS84toJTSK($latitude, $longitude);

        $x = -1 * $coord["x"];
        $y = -1 * $coord["y"];

        $url = "https://gis.izscr.cz/arcgis/rest/services/terinos_sluzby/cast_obce/MapServer/0/query?where=&text=&objectIds=&time=&geometry=%7B%22spatialReference%22%3A%7B%22wkid%22%3A102067%7D%2C%22x%22%3A$y%2C%22y%22%3A$x%7D&geometryType=esriGeometryPoint&inSR=102067&spatialRel=esriSpatialRelIntersects&relationParam=&outFields=naz_okres%2Cnaz_obec%2Ckod_obec%2Cnaz_cast%2Ckod_cast&returnGeometry=false&returnTrueCurves=false&maxAllowableOffset=&geometryPrecision=&outSR=&having=&returnIdsOnly=false&returnCountOnly=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&returnZ=false&returnM=false&gdbVersion=&historicMoment=&returnDistinctValues=false&resultOffset=&resultRecordCount=&queryByDistance=&returnExtentOnly=false&datumTransformation=&parameterValues=&rangeValues=&quantizationParameters=&f=json";
        $response = file_get_contents($url);
        $vysledek = json_decode($response, $assoc = true);

        $items = $vysledek['features'][0];

        $castObceKod = $items['attributes']['kod_cast'];

        $url4 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/11/query?where=kod%3D$castObceKod&outFields=*&f=pjson";
        $response4 = file_get_contents($url4);
        $vysledek4 = json_decode($response4, $assoc = true);
        $items4 = $vysledek4['features'][0];

        $castObceNazev = $items4['attributes']['nazev'];
        $obecKod = $items4['attributes']['obec'];

        $url5 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/12/query?where=kod%3D$obecKod&outFields=*&f=pjson";
        $response5 = file_get_contents($url5);
        $vysledek5 = json_decode($response5, $assoc = true);
        $items5 = $vysledek5['features'][0];

        $obecNazev = $items5['attributes']['nazev'];
        $okresKod = $items5['attributes']['okres'];

        $url6 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/15/query?where=kod%3D$okresKod&outFields=*&f=pjson";
        $response6 = file_get_contents($url6);
        $vysledek6 = json_decode($response6, $assoc = true);
        $items6 = $vysledek6['features'][0];

        $okresNazev = $items6['attributes']['nazev'];

        $query62 = "SELECT okresNazev, obecNazev, obecKod, castObceNazev, castObceKod FROM hlasky WHERE id = $id;";
        if ($result62 = mysqli_query($link, $query62)) {
            while ($row62 = mysqli_fetch_row($result62)) {
                $old_okresNazev = $row62[0];
                $old_obecNazev = $row62[1];
                $old_obecKod = $row62[2];
                $old_castObceNazev = $row62[3];
                $old_castObceKod = $row62[4];
            }
        }

        $query73 = "UPDATE hlasky SET okresNazev = '$okresNazev', obecNazev = '$obecNazev', obecKod = '$obecKod', castObceNazev = '$castObceNazev', castObceKod = '$castObceKod', edited = 0 WHERE id = $id;";
        $result73 = mysqli_query($link, $query73);
        if (!$result73) {
            $error .= mysqli_error($link) . "<br/>";
        }

        $param_hlaska_id = $id;
        $param_user = htmlspecialchars($_SESSION["username"]);
        $param_cas = microtime(true);

        if ($old_okresNazev != $okresNazev) {
            $param_sloupec = "okresNazev";
            $param_new_value = $okresNazev;

            $query87 = "INSERT INTO `log` (hlaska_id, sloupec, new_value, user, cas) VALUES ($param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);";
            $error .= "$query87<br/>";
            $prikaz87 = mysqli_query($link, $query87);
            if (!$result87) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        if ($old_obecNazev != $obecNazev) {
            $param_sloupec = "obecNazev";
            $param_new_value = $obecNazev;

            $query99 = "INSERT INTO `log` (hlaska_id, sloupec, new_value, user, cas) VALUES ($param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);";
            $error .= "$query99<br/>";
            $prikaz99 = mysqli_query($link, $query99);
            if (!$result99) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_obecKod != $obecKod) {
            $param_sloupec = "obecKod";
            $param_new_value = $obecKod;

            $query110 = "INSERT INTO `log` (hlaska_id, sloupec, new_value, user, cas) VALUES ($param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);";
            $error .= "$query110<br/>";
            $prikaz110 = mysqli_query($link, $query110);
            if (!$result110) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_castObceNazev != $castObceNazev) {
            $param_sloupec = "castObceNazev";
            $param_new_value = $castObceNazev;

            $query121 = "INSERT INTO `log` (hlaska_id, sloupec, new_value, user, cas) VALUES ($param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);";
            $error .= "$query121<br/>";
            $prikaz121 = mysqli_query($link, $query121);
            if (!$result121) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_castObceKod != $castObceKod) {
            $param_sloupec = "castObceKod";
            $param_new_value = $castObceKod;

            $query132 = "INSERT INTO `log` (hlaska_id, sloupec, new_value, user, cas) VALUES ($param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);";
            $error .= "$query132<br/>";
            $prikaz132 = mysqli_query($link, $query132);
            if (!$result132) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
    }
}

echo "Done...<br/>";
echo $error;
echo "<meta http-equiv=\"refresh\" content=\"5; url=check_hlasky.php?start=$end\">";