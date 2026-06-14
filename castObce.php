<?php
date_default_timezone_set('Europe/Prague');
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$arrContextOptions = [
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ],
];

require_once 'config.php';
require_once 'db_safe.php';
require_once 'xss_safe.php';
include 'Converter.php';
$converter = new JTSK\Converter();

$query23 = "SELECT id, latitude, longitude FROM hlasky WHERE edited = 1 ORDER BY id;";
if ($result23 = mysqli_query($link, $query23)) {
    while ($row23 = mysqli_fetch_row($result23)) {
        $id = $row23[0];
        $latitude = $row23[1];
        $longitude = $row23[2];

        // Load old values at start for logging (avoid redundant SELECT later)
        $query_old = "SELECT okresNazev, obecNazev, obecKod, castObceNazev, castObceKod FROM hlasky WHERE id = $id;";
        $result_old = mysqli_query($link, $query_old);
        if ($row_old = mysqli_fetch_row($result_old)) {
            $old_okresNazev = $row_old[0];
            $old_obecNazev = $row_old[1];
            $old_obecKod = $row_old[2];
            $old_castObceNazev = $row_old[3];
            $old_castObceKod = $row_old[4];
        }

        $coord = $converter->WGS84toJTSK($latitude, $longitude);

        $x = -1 * $coord["x"];
        $y = -1 * $coord["y"];

        // Optimization: Fetch first URL to get codes, then batch remaining URLs
        $url = "https://gis.izscr.cz/arcgis/rest/services/terinos_sluzby/cast_obce/MapServer/0/query?where=&text=&objectIds=&time=&geometry=%7B%22spatialReference%22%3A%7B%22wkid%22%3A102067%7D%2C%22x%22%3A$y%2C%22y%22%3A$x%7D&geometryType=esriGeometryPoint&inSR=102067&spatialRel=esriSpatialRelIntersects&relationParam=&outFields=naz_okres%2Cnaz_obec%2Ckod_obec%2Cnaz_cast%2Ckod_cast&returnGeometry=false&returnTrueCurves=false&maxAllowableOffset=&geometryPrecision=&outSR=&having=&returnIdsOnly=false&returnCountOnly=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&returnZ=false&returnM=false&gdbVersion=&historicMoment=&returnDistinctValues=false&resultOffset=&resultRecordCount=&queryByDistance=&returnExtentOnly=false&datumTransformation=&parameterValues=&rangeValues=&quantizationParameters=&f=json";
        $response = file_get_contents($url, false, stream_context_create($arrContextOptions));
        $vysledek = json_decode($response, $assoc = true);
        $items = $vysledek['features'][0];
        $castObceKod = $items['attributes']['kod_cast'];

        // Fetch remaining 3 URLs in parallel
        $url4 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/11/query?where=kod%3D$castObceKod&outFields=*&f=pjson";
        $urls = array($url4);
        $parallel_responses = fetchUrlsParallel($urls);

        $response4 = $parallel_responses[$url4];
        $vysledek4 = json_decode($response4, $assoc = true);
        $items4 = $vysledek4['features'][0];
        $castObceNazev = $items4['attributes']['nazev'];
        $obecKod = $items4['attributes']['obec'];

        // Fetch next URLs in parallel
        $url5 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/12/query?where=kod%3D$obecKod&outFields=*&f=pjson";
        $url6 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/15/query?where=kod%3D$okresKod&outFields=*&f=pjson";
        $parallel_responses = fetchUrlsParallel(array($url5));

        $response5 = $parallel_responses[$url5];
        $vysledek5 = json_decode($response5, $assoc = true);
        $items5 = $vysledek5['features'][0];
        $obecNazev = $items5['attributes']['nazev'];
        $okresKod = $items5['attributes']['okres'];

        $parallel_responses = fetchUrlsParallel(array($url6));
        $response6 = $parallel_responses[$url6];
        $vysledek6 = json_decode($response6, $assoc = true);
        $items6 = $vysledek6['features'][0];
        $okresNazev = $items6['attributes']['nazev'];

        $query77 = "UPDATE hlasky SET okresNazev = '$okresNazev', obecNazev = '$obecNazev', obecKod = '$obecKod', castObceNazev = '$castObceNazev', castObceKod = '$castObceKod', edited = 0 WHERE id = $id;";
        $result77 = mysqli_query($link, $query77);
        if (!$result77) {
            $error .= mysqli_error($link) . "<br/>";
        }

        if ($old_okresNazev != $okresNazev) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "okresNazev";
            $param_new_value = $okresNazev;

            $query90 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result90 = mysqli_query($link, $query90);
            if (!$result90) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_obecNazev != $obecNazev) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "obecNazev";
            $param_new_value = $obecNazev;

            $query103 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result103 = mysqli_query($link, $query103);
            if (!$result103) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_obecKod != $obecKod) {
            logChange($link, $id, "obecKod", $obecKod, $_SESSION["username"], microtime(true));
        }
        if ($old_castObceNazev != $castObceNazev) {
            logChange($link, $id, "castObceNazev", $castObceNazev, $_SESSION["username"], microtime(true));
        }
        if ($old_castObceKod != $castObceKod) {
            logChange($link, $id, "castObceKod", $castObceKod, $_SESSION["username"], microtime(true));
            $result142 = mysqli_query($link, $query142);
            if (!$result142) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
    }
}

echo "Done...<br/>";
echo $error;
if ($error == "") {
    Redir("index.php");
}