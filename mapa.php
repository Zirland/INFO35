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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
	<meta content="text/html; charset=utf-8" http-equiv="content-type">
	<title>Mapa hlásek</title>

	<script type="text/javascript" src="apikey.js"></script>
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
		integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
		integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
	<style>
		#map {
			width: 1200px;
			height: 800px;
		}
	</style>
</head>

<body>
	<div id="map"></div>

	<script type="text/javascript">
		function addMarker(latlon, nazev, cislo) {
			let marker = L.marker(latlon, {
				draggable: true
			})
				.bindTooltip(cislo.toString(),
					{
						permanent: false,
						direction: 'right'
					}
				)
				.bindPopup(nazev)
				.addTo(map);
			points.push(latlon);
		}

		const init_pos = [50.08, 14.41];
		let points = [];
		const map = L.map('map').setView(init_pos, 16);
		const tileLayers = {
			'Základní': L.tileLayer(
				`https://api.mapy.cz/v1/maptiles/basic/256/{z}/{x}/{y}?apikey=${API_KEY}`,
				{
					minZoom: 0,
					maxZoom: 19,
					attribution:
						'<a href="https://api.mapy.cz/copyright" target="_blank">&copy; Seznam.cz a.s. a další</a>',
				}
			),
			'Letecká': L.tileLayer(
				`https://api.mapy.cz/v1/maptiles/aerial/256/{z}/{x}/{y}?apikey=${API_KEY}`,
				{
					minZoom: 0,
					maxZoom: 20,
					attribution:
						'<a href="https://api.mapy.cz/copyright" target="_blank">&copy; Seznam.cz a.s. a další</a>',
				}
			),
			'OpenStreetMap': L.tileLayer(
				'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
				{
					maxZoom: 19,
					attribution:
						'&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
				}
			),
		};

		tileLayers['OpenStreetMap'].addTo(map);
		L.control.layers(tileLayers).addTo(map);

		const LogoControl = L.Control.extend({
			options: {
				position: 'bottomleft',
			},

			onAdd: function (map) {
				const container = L.DomUtil.create('div');
				const link = L.DomUtil.create('a', '', container);

				link.setAttribute('href', 'http://mapy.cz/');
				link.setAttribute('target', '_blank');
				link.innerHTML =
					'<img src="https://api.mapy.cz/img/api/logo.svg" />';
				L.DomEvent.disableClickPropagation(link);

				return container;
			},
		});

		new LogoControl().addTo(map);

		<?php
		$query111 = "SELECT * FROM stanice ORDER BY tel_cislo;";
		if ($result111 = mysqli_query($link, $query111)) {
			while ($row111 = mysqli_fetch_row($result111)) {
				$prijmeni = $row111[0];
				$tel_cislo = $row111[2];
				$longitude = $row111[12];
				$latitude = $row111[13];

				echo "addMarker([$latitude, $longitude], '$prijmeni', '$tel_cislo');\n";
			}
		}
		?>
		let pointLine = L.polyline(points);
		map.fitBounds(pointLine.getBounds());
	</script>

</body>

</html>