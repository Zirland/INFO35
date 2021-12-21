<?php
$dotaz = $_GET['opt'];

$query = urlencode($dotaz);

$url = "http://ags.cuzk.cz/arcgis/rest/services/RUIAN/Vyhledavaci_sluzba_nad_daty_RUIAN/MapServer/exts/GeocodeSOE/findAddressCandidates?SingleLine=$query&f=pjson";
$response = file_get_contents($url);
$vysledek=json_decode($response,$assoc = TRUE);
$pocKandid = count($vysledek['candidates']);

$kandidati = $vysledek['candidates'];

switch($pocKandid) {
	case 0:
		echo "<option>Vyhledejte adresu...</option>";
	break;
	default:
		foreach($kandidati as $items) {
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
