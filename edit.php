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

    <script type="text/javascript" src="get-api-key.php"></script>
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
require_once 'db_safe.php';
require_once 'xss_safe.php';
include 'Converter.php';
$converter = new JTSK\Converter();

$id = @$_GET["id"];
$up = @$_GET["up"];
if ($id == "") {
    $id = @$_POST["id"];
}

$query50 = "SELECT tel_cislo, silnice, kilometr, smer, latitude, longitude, platnost, ssud, typ, techno, archiv, hlavni FROM hlasky WHERE id = $id;";
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
        $old_hlavni = $row50[11];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escapuj všechny POST proměnné
    $id = isset($_POST["id"]) ? dbEscape($link, trim($_POST["id"])) : "";

    $silnice = isset($_POST["silnice"]) ? dbEscape($link, trim($_POST["silnice"])) : "";
    $silnice_err = "";
    $kilometr = isset($_POST["kilometr"]) ? dbEscape($link, trim($_POST["kilometr"])) : "";
    $kilometr_err = "";
    $smer = isset($_POST["smer"]) ? dbEscape($link, trim($_POST["smer"])) : "";
    $x = isset($_POST["latitude"]) ? dbEscape($link, substr(trim($_POST["latitude"]), 0, 14)) : "";
    $x_err = "";
    $y = isset($_POST["longitude"]) ? dbEscape($link, substr(trim($_POST["longitude"]), 0, 14)) : "";
    $y_err = "";
    $platnost = isset($_POST["platnost"]) ? dbEscape($link, trim($_POST["platnost"])) : "";
    $ssud = isset($_POST["ssud"]) ? dbEscape($link, trim($_POST["ssud"])) : "";
    $ssud_err = "";
    $typ = isset($_POST["typ"]) ? dbEscape($link, trim($_POST["typ"])) : "";
    $typ_err = "";
    $tech = isset($_POST["tech"]) ? dbEscape($link, trim($_POST["tech"])) : "";
    $arch = isset($_POST["arch"]) ? dbEscape($link, trim($_POST["arch"])) : "";
    $hlav = isset($_POST["hlav"]) ? dbEscape($link, trim($_POST["hlav"])) : "";
    $up = isset($_POST["up"]) ? dbEscape($link, trim($_POST["up"])) : "";

    if ($tech != 1) {
        $tech = 0;
    }
    if ($arch != 1) {
        $arch = 0;
    }
    if ($hlav != 1) {
        $hlav = 0;
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
        $query148 = "UPDATE hlasky SET silnice = '$silnice', smer= '$smer', kilometr = '$kilometr', latitude = '$lat', longitude = '$lon', platnost = '$platnost', export = 0, edited = 1, ssud = '$ssud', typ = '$typ', techno = '$tech', archiv = '$arch', hlavni = '$hlav' WHERE id = $id;";
        $result148 = mysqli_query($link, $query148);
        if (!$result148) {
            $error .= mysqli_error($link) . "<br/>";
        }

        $param_hlaska_id = $id;
        $param_user = htmlspecialchars($_SESSION["username"]);
        $param_cas = microtime(true);

        if ($old_silnice != $silnice) {
            $param_sloupec = "silnice";
            $param_new_value = $silnice;

            $query162 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result162 = mysqli_query($link, $query162);
            if (!$result162) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_kilometr != $kilometr) {
            $param_sloupec = "kilometr";
            $param_new_value = $kilometr;

            $query172 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result172 = mysqli_query($link, $query172);
            if (!$result172) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_latitude != $lat) {
            $param_sloupec = "latitude";
            $param_new_value = $lat;

            $query182 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result182 = mysqli_query($link, $query182);
            if (!$result182) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_smer != $smer) {
            $param_sloupec = "smer";
            $param_new_value = $smer;

            $query192 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result192 = mysqli_query($link, $query192);
            if (!$result192) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_longitude != $lon) {
            $param_sloupec = "longitude";
            $param_new_value = $lon;

            $query202 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result202 = mysqli_query($link, $query202);
            if (!$result202) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_platnost != $platnost) {
            $param_sloupec = "platnost";
            $param_new_value = $platnost;

            $query212 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result212 = mysqli_query($link, $query212);
            if (!$result212) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_ssud != $ssud) {
            $param_sloupec = "ssud";
            $param_new_value = $ssud;

            $query222 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result222 = mysqli_query($link, $query222);
            if (!$result222) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        if ($old_typ != $typ) {
            $param_sloupec = "typ";
            $param_new_value = $typ;

            $query233 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result233 = mysqli_query($link, $query233);
            if (!$result233) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        if ($old_techno != $tech) {
            $param_sloupec = "techno";
            $param_new_value = $tech;

            $query244 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result244 = mysqli_query($link, $query244);
            if (!$result244) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        if ($old_archiv != $arch) {
            $param_sloupec = "archiv";
            $param_new_value = $arch;

            $query255 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result255 = mysqli_query($link, $query255);
            if (!$result255) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        if ($old_hlavni != $hlav) {
            $param_sloupec = "hlavni";
            $param_new_value = $hlav;

            $query266 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result266 = mysqli_query($link, $query266);
            if (!$result266) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        $query273 = "SELECT url FROM aplikace WHERE app_id = '$up';";
        if ($result273 = mysqli_query($link, $query273)) {
            while ($row273 = mysqli_fetch_row($result273)) {
                $up_app = $row273[0];
            }
            if (mysqli_num_rows($result273) == 0) {
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
    <input type="hidden" name="id" value="<?php echo xss($id); ?>">
    <input type="hidden" name="up" value="<?php echo xss($up); ?>">
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
            <th>Hlavní hláska</th>
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
                <?php echo xss($old_tel_cislo); ?>
            </td>
            <td><select class="form-control" id="silnice" name="silnice">
                    <option value="">---</option>
                    <?php
                    $query326 = "SELECT id, nazev FROM enum_silnice ORDER BY nazev;";
                    if ($result326 = mysqli_query($link, $query326)) {
                        while ($row326 = mysqli_fetch_row($result326)) {
                            $sil_id = $row326[0];
                            $sil_name = $row326[1];

                            echo "<option value=\"$sil_id\"";
                            if ($sil_id == $old_silnice) {
                                echo " SELECTED";
                            }
                            echo ">$sil_name</option>\n";
                        }
                    }
                    ?>
                </select></td>
            <td><input type="text" name="kilometr" value="<?php echo xss($old_kilometr); ?>"></td>
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
            <td><input type="checkbox" name="hlav" value="1" <?php if ($old_hlavni == 1) {
                echo " CHECKED";
            } ?>></td>
            <td><input type="text" name="latitude" id="latitude" value="<?php echo xss($old_latitude); ?>"></td>
            <td><input type="text" name="longitude" id="longitude" value="<?php echo xss($old_longitude); ?>"></td>
            <td><select class="form-control" id="ssud" name="ssud">
                    <option value="">---</option>
                    <?php
                    $query363 = "SELECT id,popis FROM enum_ssud ORDER BY popis;";
                    if ($result363 = mysqli_query($link, $query363)) {
                        while ($row363 = mysqli_fetch_row($result363)) {
                            $ssud_id = $row363[0];
                            $ssud_name = $row363[1];

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
                    $query384 = "SELECT id, popis FROM enum_typ ORDER BY popis;";
                    if ($result384 = mysqli_query($link, $query384)) {
                        while ($row384 = mysqli_fetch_row($result384)) {
                            $typ_id = $row384[0];
                            $typ_name = $row384[1];

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
$query427 = "SELECT sloupec, new_value, user, cas FROM log WHERE hlaska_id = $id;";
if ($result427 = mysqli_query($link, $query427)) {
    while ($row427 = mysqli_fetch_row($result427)) {
        $log_sloupec = $row427[0];
        $log_new_value = $row427[1];
        $log_user = $row427[2];
        $log_cas = $row427[3];

        $log_cas_format = date("d.m.Y H:i:s", $log_cas);

        if ($log_sloupec == "ssud") {
            $query438 = "SELECT popis FROM enum_ssud WHERE id = $log_new_value;";
            if ($result438 = mysqli_query($link, $query438)) {
                while ($row438 = mysqli_fetch_row($result438)) {
                    $log_new_value = $row438[0];

                }
            }
        }

        if ($log_sloupec == "typ") {
            $query448 = "SELECT popis FROM enum_typ WHERE id = $log_new_value;";
            if ($result448 = mysqli_query($link, $query448)) {
                while ($row448 = mysqli_fetch_row($result448)) {
                    $log_new_value = $row448[0];

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

        document.getElementById("latitude").value = souradnice_x;
        document.getElementById("longitude").value = souradnice_y;

        map.panTo([souradnice_x, souradnice_y]);
    }

    <?php
    if (isset($old_latitude) && isset($old_longitude)) {
        echo "const init_pos = [" . $old_latitude . ", " . $old_longitude . "];";
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