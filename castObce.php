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
include 'Converter.php';
$converter = new JTSK\Converter();

$query23 = "SELECT id, latitude, longitude FROM hlasky WHERE edited = 1 ORDER BY id;";
if ($result23 = mysqli_query($link, $query23)) {
    while ($row23 = mysqli_fetch_row($result23)) {
        $id = $row23[0];
        $latitude = $row23[1];
        $longitude = $row23[2];

        $coord = $converter->WGS84toJTSK($latitude, $longitude);

        $x = -1 * $coord["x"];
        $y = -1 * $coord["y"];

        $url = "https://gis.izscr.cz/arcgis/rest/services/terinos_sluzby/cast_obce/MapServer/0/query?where=&text=&objectIds=&time=&geometry=%7B%22spatialReference%22%3A%7B%22wkid%22%3A102067%7D%2C%22x%22%3A$y%2C%22y%22%3A$x%7D&geometryType=esriGeometryPoint&inSR=102067&spatialRel=esriSpatialRelIntersects&relationParam=&outFields=naz_okres%2Cnaz_obec%2Ckod_obec%2Cnaz_cast%2Ckod_cast&returnGeometry=false&returnTrueCurves=false&maxAllowableOffset=&geometryPrecision=&outSR=&having=&returnIdsOnly=false&returnCountOnly=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&returnZ=false&returnM=false&gdbVersion=&historicMoment=&returnDistinctValues=false&resultOffset=&resultRecordCount=&queryByDistance=&returnExtentOnly=false&datumTransformation=&parameterValues=&rangeValues=&quantizationParameters=&f=json";
        $response = file_get_contents($url, false, stream_context_create($arrContextOptions));
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

        $query66 = "SELECT okresNazev, obecNazev, obecKod, castObceNazev, castObceKod FROM hlasky WHERE id = $id;";
        if ($result66 = mysqli_query($link, $query66)) {
            while ($row66 = mysqli_fetch_row($result66)) {
                $old_okresNazev = $row66[0];
                $old_obecNazev = $row66[1];
                $old_obecKod = $row66[2];
                $old_castObceNazev = $row66[3];
                $old_castObceKod = $row66[4];
            }
        }

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
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "obecKod";
            $param_new_value = $obecKod;

            $query116 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result116 = mysqli_query($link, $query116);
            if (!$result116) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_castObceNazev != $castObceNazev) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "castObceNazev";
            $param_new_value = $castObceNazev;

            $query131 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result131 = mysqli_query($link, $query131);
            if (!$result131) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_castObceKod != $castObceKod) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "castObceKod";
            $param_new_value = $castObceKod;

            $query142 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
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