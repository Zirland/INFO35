<?php
require_once 'config.php';
?>

<!doctype html>
<html>

<head>
	<meta charset="utf-8" />
	<script type="text/javascript" src="https://api.mapy.cz/loader.js"></script>
	<script type="text/javascript">
		Loader.lang = "cs";
		Loader.load();
	</script>
</head>

<body>

	<div id="mapa" style="width:1300px; height:800px;"></div>

	<script type="text/javascript">
		function addMarker(nazev, cislo, x, y) {
			var card = new SMap.Card();
			card.getHeader().innerHTML = cislo;
			card.getBody().innerHTML = nazev;

			var options = {
				title: nazev
			};

			var pozice = SMap.Coords.fromWGS84(Number(x), Number(y));
			var marker = new SMap.Marker(pozice, cislo, options);
			marker.decorate(SMap.Marker.Feature.Card, card);
			layer.addMarker(marker);
			markers.push(pozice);
		}

		var stred = SMap.Coords.fromWGS84(14.41, 50.08);
		var mapa = new SMap(JAK.gel("mapa"));
		mapa.addDefaultLayer(SMap.DEF_BASE).enable();

		mapa.addControl(new SMap.Control.Sync());
		var mouse = new SMap.Control.Mouse(SMap.MOUSE_PAN | SMap.MOUSE_WHEEL | SMap.MOUSE_ZOOM);
		mapa.addControl(mouse);

		var layer = new SMap.Layer.Marker();
		mapa.addLayer(layer);
		layer.enable();
		var markers = [];

		<?php
		$query30 = "SELECT * FROM stanice ORDER BY tel_cislo;";
		if ($result30 = mysqli_query($link, $query30)) {
			while ($row30 = mysqli_fetch_row($result30)) {
				$prijmeni = $row30[0];
				$tel_cislo = $row30[2];
				$longitude = $row30[12];
				$latitude = $row30[13];

				echo "addMarker('$prijmeni', '$tel_cislo', $longitude, $latitude);\n";
			}
		}

		?>

		var cz = mapa.computeCenterZoom(markers);
		mapa.setCenterZoom(cz[0], cz[1]);
	</script>

</body>

</html>