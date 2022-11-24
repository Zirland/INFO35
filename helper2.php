<?php
include 'Converter.php';
$converter = new JTSK\Converter();

$input = $_GET['kandi'];
echo "$input<br/>";
$dotaz = explode('|', $input);

$classId   = $dotaz[0];
$itemId    = $dotaz[1];
$itemQuery = urlencode($itemId);

switch ($classId) {
    case "AdresniMisto": // Adresa
        $url      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/1/query?where=adresa+%3D+%27$itemQuery%27&outFields=*&f=pjson";
        $response = file_get_contents($url);
        $vysledek = json_decode($response, $assoc = true);
        $items    = $vysledek['features'][0];

        $adresaKod               = $items['attributes']['kod'];
        $adresaCisloDomovni      = $items['attributes']['cislodomovni'];
        $adresaOrientacniCislo   = $items['attributes']['cisloorientacni'];
        $adresaOrientacniPismeno = $items['attributes']['cisloorientacnipismeno'];
        $adresaCisloOrientacni   = $adresaOrientacniCislo . $adresaOrientacniPismeno;
        $objektKod               = $items['attributes']['stavebniobjekt'];
        $uliceKod                = $items['attributes']['ulice'];

        $sour_X = $items['geometry']['x'] * -1;
        $x = $items['geometry']['x'];
        $sour_Y = $items['geometry']['y'] * -1;
        $y = $items['geometry']['y'];

        $url2      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/4/query?where=kod%3D$uliceKod&outFields=*&f=pjson";
        $response2 = file_get_contents($url2);
        $vysledek2 = json_decode($response2, $assoc = true);
        $items2    = $vysledek2['features'][0];

        $uliceNazev = $items2['attributes']['nazev'];

        $url3      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/3/query?where=kod%3D$objektKod&outFields=*&f=pjson";
        $response3 = file_get_contents($url3);
        $vysledek3 = json_decode($response3, $assoc = true);
        $items3    = $vysledek3['features'][0];

        $castObceKod = $items3['attributes']['castobce'];

        $url4      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/11/query?where=kod%3D$castObceKod&outFields=*&f=pjson";
        $response4 = file_get_contents($url4);
        $vysledek4 = json_decode($response4, $assoc = true);
        $items4    = $vysledek4['features'][0];

        $castObceNazev = $items4['attributes']['nazev'];
        $obecKod       = $items4['attributes']['obec'];

        $url5      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/12/query?where=kod%3D$obecKod&outFields=*&f=pjson";
        $response5 = file_get_contents($url5);
        $vysledek5 = json_decode($response5, $assoc = true);
        $items5    = $vysledek5['features'][0];

        $obecNazev = $items5['attributes']['nazev'];
        $okresKod  = $items5['attributes']['okres'];

        $url6      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/15/query?where=kod%3D$okresKod&outFields=*&f=pjson";
        $response6 = file_get_contents($url6);
        $vysledek6 = json_decode($response6, $assoc = true);
        $items6    = $vysledek6['features'][0];

        $okresNazev = $items6['attributes']['nazev'];

        echo "Část: $castObceKod<br/>";
        if ($castObceKod == '') {
            $url = "https://gis.izscr.cz/arcgis/rest/services/terinos_sluzby/cast_obce/MapServer/0/query?where=&text=&objectIds=&time=&geometry=%7B%22spatialReference%22%3A%7B%22wkid%22%3A102067%7D%2C%22x%22%3A$x%2C%22y%22%3A$y%7D&geometryType=esriGeometryPoint&inSR=102067&spatialRel=esriSpatialRelIntersects&relationParam=&outFields=naz_okres%2Cnaz_obec%2Ckod_obec%2Cnaz_cast%2Ckod_cast&returnGeometry=false&returnTrueCurves=false&maxAllowableOffset=&geometryPrecision=&outSR=&having=&returnIdsOnly=false&returnCountOnly=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&returnZ=false&returnM=false&gdbVersion=&historicMoment=&returnDistinctValues=false&resultOffset=&resultRecordCount=&queryByDistance=&returnExtentOnly=false&datumTransformation=&parameterValues=&rangeValues=&quantizationParameters=&f=json";

            $response = file_get_contents($url);
            $vysledek = json_decode($response, $assoc = true);

            $items = $vysledek['features'][0];

            $okresNazev    = $items['attributes']['naz_okres'];
            $obecNazev     = $items['attributes']['naz_obec'];
            $obecKod       = $items['attributes']['kod_obec'];
            $castObceNazev = $items['attributes']['naz_cast'];
            $castObceKod   = $items['attributes']['kod_cast'];
        }
        break;

    case "Ulice": // Ulice
        $url      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/4/query?where=ulice+%3D+%27$itemQuery%27&outFields=*&f=pjson";
        $response = file_get_contents($url);
        $vysledek = json_decode($response, $assoc = true);
        $items    = $vysledek['features'][0];
        echo "$url<br/>";
        print "<pre>";
        print_r($vysledek);
        print "</pre>";

        $uliceKod   = $items['attributes']['kod'];
        $uliceNazev = $items['attributes']['nazev'];
        $obecKod    = $items['attributes']['obec'];

        $url5      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/12/query?where=kod%3D$obecKod&outFields=*&f=pjson";
        $response5 = file_get_contents($url5);
        $vysledek5 = json_decode($response5, $assoc = true);
        $items5    = $vysledek5['features'][0];

        $obecNazev = $items5['attributes']['nazev'];
        $okresKod  = $items5['attributes']['okres'];

        $url6      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/15/query?where=kod%3D$okresKod&outFields=*&f=pjson";
        $response6 = file_get_contents($url6);
        $vysledek6 = json_decode($response6, $assoc = true);
        $items6    = $vysledek6['features'][0];

        $okresNazev = $items6['attributes']['nazev'];

        break;

    case "CastObce": // CastObce
        $url      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/11/query?where=nazev+%3D+%27$itemQuery%27&outFields=*&f=pjson";
        $response = file_get_contents($url);
        $vysledek = json_decode($response, $assoc = true);
        $items    = $vysledek['features'][0];

        echo "$url<br/>";
        print "<pre>";
        print_r($vysledek);
        print "</pre>";

        $castObceKod = $items3['attributes']['castobce'];

        $url4      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/11/query?where=kod%3D$castObceKod&outFields=*&f=pjson";
        $response4 = file_get_contents($url4);
        $vysledek4 = json_decode($response4, $assoc = true);
        $items4    = $vysledek4['features'][0];

        $castObceNazev = $items4['attributes']['nazev'];
        $obecKod       = $items4['attributes']['obec'];

        $url5      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/12/query?where=kod%3D$obecKod&outFields=*&f=pjson";
        $response5 = file_get_contents($url5);
        $vysledek5 = json_decode($response5, $assoc = true);
        $items5    = $vysledek5['features'][0];

        $obecNazev = $items5['attributes']['nazev'];
        $okresKod  = $items5['attributes']['okres'];

        $url6      = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/15/query?where=kod%3D$okresKod&outFields=*&f=pjson";
        $response6 = file_get_contents($url6);
        $vysledek6 = json_decode($response6, $assoc = true);
        $items6    = $vysledek6['features'][0];

        $okresNazev = $items6['attributes']['nazev'];

        break;
        $url = "";
    default:
        break;
}

$latlon = $converter->JTSKtoWGS84($sour_Y, $sour_X); // returns array ['lat', 'lon']

$latitude  = $latlon['lat'];
$longitude = $latlon['lon'];

echo "<br />Okres název: <input type=\"text\" name=\"okresNazev\" value=\"$okresNazev\">";
echo "<br/>Obec kód: <input type=\"text\" name=\"obecKod\" value=\"$obecKod\" readonly> Obec název: <input type=\"text\" name=\"obecNazev\" value=\"$obecNazev\" readonly>";
echo "<br/>Část obce kód: <input type=\"text\" name=\"castObceKod\" value=\"$castObceKod\" readonly> Část obce název: <input type=\"text\" name=\"castObceNazev\" value=\"$castObceNazev\" readonly> ";
echo "<br/>Ulice kód: <input type=\"text\" name=\"uliceKod\" value=\"$uliceKod\"> Ulice název: <input type=\"text\" name=\"uliceNazev\" value=\"$uliceNazev\"> ";
echo "<br/>Kód objektu: <input type=\"text\" name=\"objektKod\" value=\"$objektKod\">";
echo "Domovní: <input type=\"text\" name=\"adresaCisloDomovni\" size=\"4\" value=\"$adresaCisloDomovni\">";
echo "<br/>Kód adresy: <input type=\"text\" name=\"adresaKod\" value=\"$adresaKod\">";
echo "Orientační: <input type=\"text\" name=\"adresaCisloOrientacni\" size=\"4\" value=\"$adresaCisloOrientacni\">";
echo "<br/><input type=\"text\" name=\"latitude\" value=\"$latitude\">";
echo "<input type=\"text\" name=\"longitude\" value=\"$longitude\">";
