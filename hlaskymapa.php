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
		function addMarker(cislo, x, y) {
			var card = new SMap.Card();
			card.getBody().innerHTML = cislo;

			var options = {
				title: cislo
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
		mapa.addDefaultLayer(SMap.DEF_OPHOTO);

		var layerSwitch = new SMap.Control.Layer({
			width: 65,
			items: 2,
			page: 2
		});
		layerSwitch.addDefaultLayer(SMap.DEF_BASE);
		layerSwitch.addDefaultLayer(SMap.DEF_OPHOTO);
		mapa.addControl(layerSwitch, {
			left: "8px",
			top: "9px"
		});

		mapa.addControl(new SMap.Control.Sync());
		var mouse = new SMap.Control.Mouse(SMap.MOUSE_PAN | SMap.MOUSE_WHEEL | SMap.MOUSE_ZOOM);
		mapa.addControl(mouse);

		var MujCluster = JAK.ClassMaker.makeClass({
			NAME: "MujCluster",
			VERSION: "1.0",
			EXTEND: SMap.Marker.Cluster
		});


		MujCluster.prototype.click = function(e, elm) {

			var max_zoom = 18;
			var map = this.getMap();


			if (map.getZoom() >= max_zoom) {
				var card = new SMap.Card();
				var infos = "";

				for (i = 0; i < this._markers.length; i++) {
					infos += this._markers[i]._card.getBody().innerHTML + "<br>";
				}

				card.getBody().innerHTML = infos;
				map.addCard(card, this.getCoords());

			} else {
				this.$super(e, elm);
			}

		}


		var layer = new SMap.Layer.Marker();
		var clusterer = new SMap.Marker.Clusterer(mapa, 20, MujCluster);
		layer.setClusterer(clusterer);
		mapa.addLayer(layer);
		layer.enable();
		var markers = [];

		<?php
		$query30 = "SELECT tel_cislo, latitude, longitude FROM hlasky WHERE platnost = 1 ORDER BY tel_cislo;";
		if ($result30 = mysqli_query($link, $query30)) {
			while ($row30 = mysqli_fetch_row($result30)) {
				$tel_cislo = $row30[0];
				$longitude = $row30[1];
				$latitude = $row30[2];

				echo "addMarker('$tel_cislo', $latitude, $longitude);\n";
			}
		}

		?>

		var cz = mapa.computeCenterZoom(markers);
		mapa.setCenterZoom(cz[0], cz[1]);
	</script>

</body>

</html>