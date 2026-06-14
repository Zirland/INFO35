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
require_once 'db_safe.php';
require_once 'xss_safe.php';
include 'Converter.php';
$converter = new JTSK\Converter();

$start = isset($_GET["start"]) && is_numeric($_GET["start"]) ? (int)$_GET["start"] : 0;
$end = $start + 30;

$error = "";

// Function to make HTTP requests using cURL
function makeRequest($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_CAPATH, "/usr/lib/ssl/certs");
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

    // Try with default certificate first
    $response = curl_exec($ch);

    // If that fails, try without certificate verification
    if (curl_errno($ch) && strpos(curl_error($ch), 'SSL certificate problem') !== false) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($ch);
    }

    if (curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
        return false;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
        error_log("HTTP error: $httpCode");
        return false;
    }

    return $response;  // cURL handle is freed when $ch goes out of scope (PHP 8.0+)
}

$query19 = "SELECT id, latitude, longitude FROM hlasky WHERE id >= $start AND id < $end ORDER BY id;";
if ($result19 = mysqli_query($link, $query19)) {
    while ($row19 = mysqli_fetch_row($result19)) {
        $id = $row19[0];
        $latitude = $row19[1];
        $longitude = $row19[2];

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
        $response = makeRequest($url);
        if ($response === false) {
            $error .= "Failed to fetch data for ID $id<br/>";
            continue;
        }
        $vysledek = json_decode($response, $assoc = true);
        $items = $vysledek['features'][0];
        $castObceKod = $items['attributes']['kod_cast'];

        // Fetch next 3 URLs in parallel
        $url4 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/11/query?where=kod%3D$castObceKod&outFields=*&f=pjson";
        $parallel_responses = fetchUrlsParallel(array($url4));
        $response4 = isset($parallel_responses[$url4]) ? $parallel_responses[$url4] : false;
        if ($response4 === false) {
            $error .= "Failed to fetch data for castObceKod $castObceKod<br/>";
            continue;
        }
        $vysledek4 = json_decode($response4, $assoc = true);
        $items4 = $vysledek4['features'][0];
        $castObceNazev = $items4['attributes']['nazev'];
        $obecKod = $items4['attributes']['obec'];

        $url5 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/12/query?where=kod%3D$obecKod&outFields=*&f=pjson";
        $url6 = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/15/query?where=kod%3D$okresKod&outFields=*&f=pjson";
        $parallel_responses = fetchUrlsParallel(array($url5, $url6));

        $response5 = isset($parallel_responses[$url5]) ? $parallel_responses[$url5] : false;
        if ($response5 === false) {
            $error .= "Failed to fetch data for obecKod $obecKod<br/>";
            continue;
        }
        $vysledek5 = json_decode($response5, $assoc = true);
        $items5 = $vysledek5['features'][0];
        $obecNazev = $items5['attributes']['nazev'];
        $okresKod = $items5['attributes']['okres'];

        $response6 = isset($parallel_responses[$url6]) ? $parallel_responses[$url6] : false;
        if ($response6 === false) {
            $error .= "Failed to fetch data for okresKod $okresKod<br/>";
            continue;
        }
        $vysledek6 = json_decode($response6, $assoc = true);
        $items6 = $vysledek6['features'][0];
        $okresNazev = $items6['attributes']['nazev'];

        $query73 = "UPDATE hlasky SET okresNazev = '$okresNazev', obecNazev = '$obecNazev', obecKod = '$obecKod', castObceNazev = '$castObceNazev', castObceKod = '$castObceKod', edited = 0 WHERE id = $id;";
        $result73 = mysqli_query($link, $query73);
        if (!$result73) {
            $error .= mysqli_error($link) . "<br/>";
        }

        $user = htmlspecialchars($_SESSION["username"]);
        $cas = microtime(true);

        if ($old_okresNazev != $okresNazev) {
            logChange($link, $id, "okresNazev", $okresNazev, $user, $cas);
        }
        if ($old_obecNazev != $obecNazev) {
            logChange($link, $id, "obecNazev", $obecNazev, $user, $cas);
        }
        if ($old_obecKod != $obecKod) {
            logChange($link, $id, "obecKod", $obecKod, $user, $cas);
        }
        if ($old_castObceNazev != $castObceNazev) {
            logChange($link, $id, "castObceNazev", $castObceNazev, $user, $cas);
        }
        if ($old_castObceKod != $castObceKod) {
            logChange($link, $id, "castObceKod", $castObceKod, $user, $cas);
        }
    }
}

echo "Done...<br/>";
echo $error;
echo "<meta http-equiv=\"refresh\" content=\"5; url=check_hlasky.php?start=$end\">";
