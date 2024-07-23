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
    <script type="text/javascript" src="https://api.mapy.cz/loader.js"></script>
    <script type="text/javascript">
        Loader.lang = "cs";
        Loader.load(null, {
            poi: true
        });
    </script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
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

$query45 = "SELECT tel_cislo, silnice, kilometr, smer, latitude, longitude, platnost, ssud, typ, techno, archiv FROM hlasky WHERE id = $id;";
if ($result45 = mysqli_query($link, $query45)) {
    while ($row45 = mysqli_fetch_row($result45)) {
        $old_tel_cislo = $row45[0];
        $old_silnice = $row45[1];
        $old_kilometr = $row45[2];
        $old_smer = $row45[3];
        $old_latitude = $row45[4];
        $old_longitude = $row45[5];
        $old_platnost = $row45[6];
        $old_ssud = $row45[7];
        $old_typ = $row45[8];
        $old_techno = $row45[9];
        $old_archiv = $row45[10];
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
        $query138 = "UPDATE hlasky SET silnice = '$silnice', smer= '$smer', kilometr = '$kilometr', latitude = '$lat', longitude = '$lon', platnost = '$platnost', export = 0, edited = 1, ssud = '$ssud', typ = '$typ', techno = '$tech', archiv = '$arch' WHERE id = $id;";
        $result138 = mysqli_query($link, $query138);
        if (!$result138) {
            $error .= mysqli_error($link) . "<br/>";
        }

        if ($old_silnice != $silnice) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "silnice";
            $param_new_value = $silnice;

            $query151 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result151 = mysqli_query($link, $query151);
            if (!$result151) {
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

            $query177 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result177 = mysqli_query($link, $query177);
            if (!$result177) {
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

            $query203 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result203 = mysqli_query($link, $query203);
            if (!$result203) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_platnost != $platnost) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "platnost";
            $param_new_value = $platnost;

            $query216 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result216 = mysqli_query($link, $query216);
            if (!$result216) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }
        if ($old_ssud != $ssud) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "ssud";
            $param_new_value = $ssud;

            $query229 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result229 = mysqli_query($link, $query229);
            if (!$result229) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        if ($old_typ != $typ) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "typ";
            $param_new_value = $typ;

            $query243 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result243 = mysqli_query($link, $query243);
            if (!$result243) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        if ($old_techno != $tech) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "techno";
            $param_new_value = $tech;

            $query257 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result257 = mysqli_query($link, $query257);
            if (!$result257) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        if ($old_archiv != $arch) {
            $param_hlaska_id = $id;
            $param_user = htmlspecialchars($_SESSION["username"]);
            $param_cas = microtime(true);
            $param_sloupec = "archiv";
            $param_new_value = $arch;

            $query271 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
            $result271 = mysqli_query($link, $query271);
            if (!$result271) {
                $error .= mysqli_error($link) . "<br/>";
            }
        }

        $query278 = "SELECT url FROM aplikace WHERE app_id = '$up';";
        if ($result278 = mysqli_query($link, $query278)) {
            while ($row278 = mysqli_fetch_row($result278)) {
                $up_app = $row278[0];
            }
            if (mysqli_num_rows($result278) == 0) {
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
                    $query330 = "SELECT id, nazev FROM enum_silnice ORDER BY nazev;";
                    if ($result330 = mysqli_query($link, $query330)) {
                        while ($row330 = mysqli_fetch_row($result330)) {
                            $sil_id = $row330[0];
                            $sil_name = $row330[1];

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
                    $query368 = "SELECT id,popis FROM enum_ssud ORDER BY popis;";
                    if ($result368 = mysqli_query($link, $query368)) {
                        while ($row368 = mysqli_fetch_row($result368)) {
                            $ssud_id = $row368[0];
                            $ssud_name = $row368[1];

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
                    $query385 = "SELECT id, popis FROM enum_typ ORDER BY popis;";
                    if ($result385 = mysqli_query($link, $query385)) {
                        while ($row385 = mysqli_fetch_row($result385)) {
                            $typ_id = $row385[0];
                            $typ_name = $row385[1];

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
            } ?>></td>
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

<div id="mapa" style="width:1200px; height:800px;"></div>

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
    function SelectElement(id, valueToSelect) {
        var element = document.getElementById(id);
        element.value = valueToSelect;
    }

    function start(e) {
        var node = e.target.getContainer();
        node[SMap.LAYER_MARKER].style.cursor = "pointer";
    }

    function stop(e) {
        var node = e.target.getContainer();
        node[SMap.LAYER_MARKER].style.cursor = "";
        var coords = e.target.getCoords();
        var souradnice = coords.toString().split(",");
        var souradnice_x = souradnice[0].replace(/\(/g, "");
        var souradnice_y = souradnice[1].replace(/\)/g, "");

        document.getElementById("latitude").value = souradnice_y;
        document.getElementById("longitude").value = souradnice_x;

        var pozice = SMap.Coords.fromWGS84(souradnice_x, souradnice_y);
        mapa.setCenter(pozice);
    }

    <?php
    if (isset($old_latitude) && isset($old_longitude)) {
        echo "var stred = SMap.Coords.fromWGS84($old_longitude, $old_latitude);\n";
    } else {
        echo "var stred = SMap.Coords.fromWGS84(14.41, 50.08);\n";
    }
    ?>
    var mapa = new SMap(document.querySelector("#mapa"), stred, 20);

    mapa.addDefaultLayer(SMap.DEF_OPHOTO).enable();
    mapa.addDefaultLayer(SMap.DEF_BASE);

    var layerSwitch = new SMap.Control.Layer({
        width: 65,
        items: 2,
        page: 2
    });
    layerSwitch.addDefaultLayer(SMap.DEF_BASE);
    layerSwitch.addDefaultLayer(SMap.DEF_OPHOTO);
    mapa.addControl(layerSwitch, { left: "8px", top: "9px" });

    mapa.addControl(new SMap.Control.Sync());
    var mouse = new SMap.Control.Mouse(SMap.MOUSE_PAN | SMap.MOUSE_WHEEL | SMap.MOUSE_ZOOM);
    mapa.addControl(mouse);

    var layer = new SMap.Layer.Marker();
    mapa.addLayer(layer);
    layer.enable();

    var options = {
        title: ""
    };
    var marker = new SMap.Marker(stred, "myMarker", options);
    marker.decorate(SMap.Marker.Feature.Draggable);
    layer.addMarker(marker);

    var layer2 = new SMap.Layer.Marker(undefined, {
        poiTooltip: true
    });
    mapa.addLayer(layer2).enable();

    var dataProvider = mapa.createDefaultDataProvider();
    dataProvider.setOwner(mapa);
    dataProvider.addLayer(layer2);
    dataProvider.setMapSet(SMap.MAPSET_BASE);
    // dataProvider.enable();

    var signals = mapa.getSignals();
    signals.addListener(window, "marker-drag-stop", stop);
    signals.addListener(window, "marker-drag-start", start);


</script>