<?php
date_default_timezone_set('Europe/Prague');
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>Editace hlásky</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
        }
    </style>

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

<?php
require_once 'config.php';
include 'Converter.php';
$converter = new JTSK\Converter();

$id = @$_GET["id"];
$up = @$_GET["up"];
if ($id == "") {
    $id = @$_POST["id"];
}

$query50 = "SELECT tel_cislo, silnice, kilometr, smer, latitude, longitude, platnost, ssud, typ, techno, archiv FROM hlasky WHERE id = $id;";
if ($result50 = mysqli_query($link, $query50)) {
    while ($row50 = mysqli_fetch_row($result50)) {
        $old_tel_cislo = $row50[0];
        $old_silnice = $row50[1];
        $old_kilometr = $row50[2];
        $old_smer = $row50[3];
        $old_latitude = $row50[4];
        $old_longitude = $row50[5];
        $old_platnost = $row50[6];
        $old_ssud = $row50[7];
        $old_typ = $row50[8];
        $old_techno = $row50[9];
        $old_archiv = $row50[10];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = @$_POST["id"];

    $silnice = @$_POST["silnice"];
    $silnice_err = "";
    $kilometr = @$_POST["kilometr"];
    $kilometr_err = "";
    $smer = @$_POST["smer"];
    $x = @substr($_POST["latitude"], 0, 14);
    $x_err = "";
    $y = @substr($_POST["longitude"], 0, 14);
    $y_err = "";
    $platnost = @$_POST["platnost"];
    $ssud = @$_POST["ssud"];
    $ssud_err = "";
    $typ = @$_POST["typ"];
    $typ_err = "";
    $tech = @$_POST["tech"];
    $arch = @$_POST["arch"];
    $up = @$_POST["up"];

    if ($tech != 1) {
        $tech = 0;
    }
    if ($arch != 1) {
        $arch = 0;
    }
    if ($platnost != 1) {
        $platnost = 0;
    }

    $x = trim($x);
    $x_pole = explode(" ", trim($x));
    if (count($x_pole) > 1) {
        $x_err .= "Souřadnice X není ve správném formátu (DD.DDDDD)<br/>";
    }
    $lat = str_replace(",", ".", $x_pole[0]);
    if ($lat < 48.55 || $lat > 51.06) {
        $x_err .= "Souřadnice X je mimo území ČR.<br/>";
    }

    $y_pole = explode(" ", trim($y));
    if (count($y_pole) > 1) {
        $y_err .= "Souřadnice Y není ve správném formátu (DD.DDDDD)<br/>";
    }
    $lon = str_replace(",", ".", $y_pole[0]);
    if ($lon < 12.09 || $lon > 18.86) {
        $y_err .= "Souřadnice Y je mimo území ČR.<br/>";
    }

    if (empty(trim($silnice))) {
        $silnice_err = "Vyberte prosím silnici.";
    }

    if (empty(trim($kilometr)) && trim($kilometr) != "0") {
        $kilometr_err = "Zadejte prosím kilometr.";
    } else {
        $kilometr = trim($kilometr);
        $kilometr = str_replace(",", ".", $kilometr);
        if ((floor($kilometr) == $kilometr && substr($kilometr, -2) != ".0")) {
            $kilometr .= ".0";
        }
        if ($kilometr == "0") {
            $kilometr = "0.0";
        }
    }

    if (empty(trim($ssud))) {
        $ssud_err = "Přiřaďte prosím hlásku příslušnému středisku SSÚD.";
    }

    if (empty(trim($typ))) {
        $typ_err = "Vyberte prosím typ hlásky.";
    }

    if (empty($silnice_err) && empty($kilometr_err) && empty($x_err) && empty($y_err) && empty($ssud_err) && empty($typ_err)) {
        $query143 = "UPDATE hlasky SET silnice = '$silnice', smer= '$smer', kilometr = '$kilometr', latitude = '$lat', longitude = '$lon', platnost = '$platnost', export = 0, edited = 1, ssud = '$ssud', typ = '$typ', techno = '$tech', archiv = '$arch' WHERE id = $id;";
        $result143 = mysqli_query($link, $query143);
        if (!$result143) {
            $error .= mysqli_error($link) . "<br/>";
        }

        if ($old_silnice != $silnice) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "silnice";
            $param_new_value = $silnice;

            $query156 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result156 = mysqli_query($link, $query156);
            if (!$result156) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_kilometr != $kilometr) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "kilometr";
            $param_new_value = $kilometr;

            $query164 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result164 = mysqli_query($link, $query164);
            if (!$result164) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_latitude != $lat) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "latitude";
            $param_new_value = $lat;

            $query182 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result182 = mysqli_query($link, $query182);
            if (!$result182) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_smer != $smer) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "smer";
            $param_new_value = $smer;

            $query190 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result190 = mysqli_query($link, $query190);
            if (!$result190) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_longitude != $lon) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "longitude";
            $param_new_value = $lon;

            $query208 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result208 = mysqli_query($link, $query208);
            if (!$result208) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_platnost != $platnost) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "platnost";
            $param_new_value = $platnost;

            $query221 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result221 = mysqli_query($link, $query221);
            if (!$result221) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_ssud != $ssud) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "ssud";
            $param_new_value = $ssud;

            $query234 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result234 = mysqli_query($link, $query234);
            if (!$result234) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        if ($old_typ != $typ) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "typ";
            $param_new_value = $typ;

            $query248 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result248 = mysqli_query($link, $query248);
            if (!$result248) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        if ($old_techno != $tech) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "techno";
            $param_new_value = $tech;

            $query262 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result262 = mysqli_query($link, $query262);
            if (!$result262) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        if ($old_archiv != $arch) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "archiv";
            $param_new_value = $arch;

            $query267 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result267 = mysqli_query($link, $query267);
            if (!$result267) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        $query283 = "SELECT url FROM aplikace WHERE app_id = '$up';";
        if ($result283 = mysqli_query($link, $query283)) {
            while ($row283 = mysqli_fetch_row($result283)) {
                $up_app = $row283[0];
            }
            if (mysqli_num_rows($result283) == 0) {
                $up_app = "index.php";
            }
        }

        Redir($up_app);
    } else {
        echo "$silnice_err $kilometr_err $x_err $y_err $ssud_err $typ_err<br/>";
    }
}

$up_app = PageHeader();
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="up" value="<?php echo $up; ?>">
    <table width="100%" style="text-align:center;">
        <tr>
            <td colspan="8">Editace hlásky</td>
        </tr>
        <tr>
            <th>&nbsp;</th>
            <th>Telefonní číslo</th>
            <th>Silnice</th>
            <th>Kilometr</th>
            <th>Směr</th>
            <th>Zeměpisná šířka</th>
            <th>Zeměpisná délka</th>
            <th>SSÚD</th>
            <th>Techno</th>
            <th>Typ</th>
            <th>Platnost</th>
            <?php
            if ($old_platnost == "0") {
                echo "<th>Archiv</th>";
            }
            ?>
        </tr>
        <tr>
            <td></td>
            <td>
                <?php echo $old_tel_cislo; ?>
            </td>
            <td><select class="form-control" id="silnice" name="silnice">
                    <option value="">---</option>
                    <?php
                    $query335 = "SELECT id, nazev FROM enum_silnice ORDER BY nazev;";
                    if ($result335 = mysqli_query($link, $query335)) {
                        while ($row335 = mysqli_fetch_row($result335)) {
                            $sil_id = $row335[0];
                            $sil_name = $row335[1];

                            echo "<option value=\"$sil_id\"";
                            if ($sil_id == $old_silnice) {
                                echo " SELECTED";
                            }
                            echo ">$sil_name</option>\n";
                        }
                    }
                    ?>
                </select></td>
            <td><input type="text" name="kilometr" value="<?php echo $old_kilometr; ?>"></td>
            <td><select id="smer" name="smer">
                    <option value="+" <?php
                    if ($old_smer == "+") {
                        echo " SELECTED";
                    }
                    ?>>rostoucí</option>
                    <option value="-" <?php
                    if ($old_smer == "-") {
                        echo " SELECTED";
                    }
                    ?>>klesající</option>
                </select>
            </td>
            <td><input type="text" name="latitude" id="latitude" value="<?php echo $old_latitude; ?>"></td>
            <td><input type="text" name="longitude" id="longitude" value="<?php echo $old_longitude; ?>"></td>
            <td><select class="form-control" id="ssud" name="ssud">
                    <option value="">---</option>
                    <?php
                    $query369 = "SELECT id,popis FROM enum_ssud ORDER BY popis;";
                    if ($result369 = mysqli_query($link, $query369)) {
                        while ($row369 = mysqli_fetch_row($result369)) {
                            $ssud_id = $row369[0];
                            $ssud_name = $row369[1];

                            echo "<option value=\"$ssud_id\"";
                            if ($ssud_id == $old_ssud) {
                                echo " SELECTED";
                            }
                            echo ">$ssud_name</option>\n";
                        }
                    }
                    ?>
                </select></td>
            <td><input type="checkbox" name="tech" value="1" <?php if ($old_techno == 1) {
                echo " CHECKED";
            } ?>></td>
            <td><select class="form-control" id="typ" name="typ">
                    <option value="">---</option>
                    <?php
                    $query390 = "SELECT id, popis FROM enum_typ ORDER BY popis;";
                    if ($result390 = mysqli_query($link, $query390)) {
                        while ($row390 = mysqli_fetch_row($result390)) {
                            $typ_id = $row390[0];
                            $typ_name = $row390[1];

                            echo "<option value=\"$typ_id\"";
                            if ($typ_id == $old_typ) {
                                echo " SELECTED";
                            }
                            echo ">$typ_name</option>\n";
                        }
                    }
                    ?>
                </select></td>
            <td><input type="checkbox" name="platnost" value="1" <?php if ($old_platnost == 1) {
                echo " CHECKED";
            } ?>>
            </td>
            <?php
            if ($old_platnost == "0") {
                echo "<td><input type=\"checkbox\" name=\"arch\" value=\"1\"";
                if ($old_archiv == 1) {
                    echo " CHECKED";
                }
                echo "></td>";
            }
            ?>

        </tr>
        <tr>
            <td colspan="3"></td>
            <td colspan="5"><input type="submit" value="Uložit změny">
</form>
</td>
</tr>
</table>

<div id="map"></div>

<?php
echo "Log změn:<br/>";
echo "<table width=\"50%\"><tr><th></th><th></th><th></th><th></th></tr>";
$query433 = "SELECT sloupec, new_value, user, cas FROM log WHERE hlaska_id = $id;";
if ($result433 = mysqli_query($link, $query433)) {
    while ($row433 = mysqli_fetch_row($result433)) {
        $log_sloupec = $row433[0];
        $log_new_value = $row433[1];
        $log_user = $row433[2];
        $log_cas = $row433[3];

        $log_cas_format = date("d.m.Y H:i:s", $log_cas);

        if ($log_sloupec == "ssud") {
            $query444 = "SELECT popis FROM enum_ssud WHERE id = $log_new_value;";
            if ($result444 = mysqli_query($link, $query444)) {
                while ($row444 = mysqli_fetch_row($result444)) {
                    $log_new_value = $row444[0];

                }
            }
        }

        if ($log_sloupec == "typ") {
            $query454 = "SELECT popis FROM enum_typ WHERE id = $log_new_value;";
            if ($result454 = mysqli_query($link, $query454)) {
                while ($row454 = mysqli_fetch_row($result454)) {
                    $log_new_value = $row454[0];

                }
            }
        }

        echo "<tr><td>$log_sloupec</td><td>$log_new_value</td><td>$log_user</td><td>$log_cas_format</td></tr>";
    }
}
echo "</table>";
?>

<script type="text/javascript">
    function moveMarker(e) {
        let coords = e.target.getLatLng();
        let souradnice = coords.toString().split(', ');
        let souradnice_x = souradnice[0].replace(/LatLng\(/g, '');
        let souradnice_y = souradnice[1].replace(/\)/g, '');

        var node = e.target.getContainer();
        node[SMap.LAYER_MARKER].style.cursor = "pointer";

        document.getElementById("latitude").value = souradnice_x;
        document.getElementById("longitude").value = souradnice_y;

        map.panTo([souradnice_x, souradnice_y]);
    }

    <?php
    if (isset($old_latitude) && isset($old_longitude)) {
        echo "const init_pos = [$old_latitude, $old_longitude];";
    } else {
        echo "const init_pos = [50.08, 14.41];";
    }
    ?>
    const map = L.map('map').setView(init_pos, 19);
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

    let marker = L.marker(init_pos, {
        draggable: true,
    }).addTo(map);

    marker.on('dragend', moveMarker);

</script>