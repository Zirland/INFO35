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

$query15 = "SELECT id, latitude, longitude FROM hlasky WHERE id >= $start AND id < $end ORDER BY id;";
if ($result15 = mysqli_query($link, $query15)) {
    while ($row15 = mysqli_fetch_row($result15)) {
        $id = $row15[0];
        $latitude = $row15[1];
        $longitude = $row15[2];

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

        $query16 = "SELECT okresNazev, obecNazev, obecKod, castObceNazev, castObceKod FROM hlasky WHERE id = $id;";
        if ($result16 = mysqli_query($link, $query16)) {
            while ($row16 = mysqli_fetch_row($result16)) {
                $old_okresNazev = $row16[0];
                $old_obecNazev = $row16[1];
                $old_obecKod = $row16[2];
                $old_castObceNazev = $row16[3];
                $old_castObceKod = $row16[4];
            }
        }

        $query27 = "UPDATE hlasky SET okresNazev = '$okresNazev', obecNazev = '$obecNazev', obecKod = '$obecKod', castObceNazev = '$castObceNazev', castObceKod = '$castObceKod', edited = 0 WHERE id = $id;";
        $result27 = mysqli_query($link, $query27);
        if (!$result27) {
            $error .= mysqli_error($link) . "<br/>";
        }

        if ($old_okresNazev != $okresNazev) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "okresNazev";
            $param_new_value = $okresNazev;

            $query33 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $error .= "$query33<br/>";
            $result33 = mysqli_query($link, $query33);
            if (!$result33) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_obecNazev != $obecNazev) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "obecNazev";
            $param_new_value = $obecNazev;

            $query33 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $error .= "$query33<br/>";
            $result33 = mysqli_query($link, $query33);
            if (!$result33) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_obecKod != $obecKod) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "obecKod";
            $param_new_value = $obecKod;

            $query33 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $error .= "$query33<br/>";
            $result33 = mysqli_query($link, $query33);
            if (!$result33) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_castObceNazev != $castObceNazev) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "castObceNazev";
            $param_new_value = $castObceNazev;

            $query33 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $error .= "$query33<br/>";
            $result33 = mysqli_query($link, $query33);
            if (!$result33) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_castObceKod != $castObceKod) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "castObceKod";
            $param_new_value = $castObceKod;

            $query33 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $error .= "$query33<br/>";
            $result33 = mysqli_query($link, $query33);
            if (!$result33) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
    }
}

echo "Done...<br/>";
echo $error;
echo "<meta http-equiv=\"refresh\" content=\"5; url=check_hlasky.php?start=$end\">";