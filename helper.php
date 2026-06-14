<?php
$dotaz = $_GET['opt'];

$query = urlencode($dotaz);

$url = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/exts/GeocodeSOE/findAddressCandidates?SingleLine=$query&f=pjson";
$response = file_get_contents($url);
$vysledek = json_decode($response, $assoc = TRUE);
$pocKandid = count($vysledek['candidates']);

$kandidati = $vysledek['candidates'];

switch ($pocKandid) {
	case 0:
		echo "<option>Vyhledejte adresu...</option>";
		break;
	default:
		foreach ($kandidati as $items) {
			echo "<option value=\"";
			echo $items['attributes']['Type'];
			echo "|";
			echo $items['address'];
			echo "\">";
			echo $items['address'];
			echo "</option>";
		}
		break;
}
?>
/**
 * Centralized function to log changes to database records
 */
function logChange($link, $hlaska_id, $sloupec, $new_value, $user, $cas) {
    $hlaska_id = dbEscape($link, $hlaska_id);
    $sloupec = dbEscape($link, $sloupec);
    $new_value = dbEscape($link, $new_value);
    $user = dbEscape($link, $user);
    
    $query = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$hlaska_id', '$sloupec', '$new_value', '$user', '$cas')";
    return mysqli_query($link, $query);
}

/**
 * Fetch multiple URLs in parallel using cURL multi-handle
 * @param array $urls - URLs to fetch
 * @return array - Response data keyed by URL
 */
function fetchUrlsParallel($urls) {
    $responses = array();
    $mh = curl_multi_init();
    $handles = array();
    
    // Initialize all curl handles
    foreach ($urls as $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_multi_add_handle($mh, $ch);
        $handles[$url] = $ch;
    }
    
    // Execute all requests in parallel
    $active = null;
    do {
        $status = curl_multi_exec($mh, $active);
    } while ($status === CURLM_CALL_MULTI_PERFORM || $active);
    
    // Get responses
    foreach ($handles as $url => $ch) {
        $responses[$url] = curl_multi_getcontent($ch);
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }
    
    curl_multi_close($mh);
    return $responses;
}
