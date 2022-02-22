<?php
date_default_timezone_set('Europe/Prague');
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'config.php';
include 'Converter.php';
$converter = new JTSK\Converter();

$tel_cislo     = @$_POST["tel_cislo"];
$tel_cislo_err = "";
$silnice       = @$_POST["silnice"];
$silnice_err   = "";
$kilometr      = @$_POST["kilometr"];
$kilometr_err  = "";
$smer          = @$_POST["smer"];
$format        = @$_POST["format"];
$x             = @$_POST["x"];
$x_err         = "";
$y             = @$_POST["y"];
$y_err         = "";
$override      = @$_POST["override"];
$ssud          = @$_POST["ssud"];
$ssud_err      = "";
$typ           = @$_POST["typ"];
$typ_err       = "";
//$ico           = "65993390";
//$OpID          = "777";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    switch ($format) {
        case '1':
            $x      = trim($x);
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
            break;
        case '2':
            $x      = trim($x);
            $x_pole = explode(" ", trim($x));
            $lat_dd = str_replace(",", ".", $x_pole[0]);
            $lat_mm = str_replace(",", ".", $x_pole[1]);

            if (count($x_pole) != 2 || floor($lat_dd) != $lat_dd || $lat_mm >= 60) {
                $x_err .= "Souřadnice X není ve správném formátu (DD MM.MMMM)<br/>";
            }

            $lat = $lat_dd + ($lat_mm / 60);

            if ($lat < 48.55 || $lat > 51.06) {
                $x_err .= "Souřadnice X je mimo území ČR.<br/>";
            }

            $y      = trim($y);
            $y_pole = explode(" ", trim($y));
            $lon_dd = str_replace(",", ".", $y_pole[0]);
            $lon_mm = str_replace(",", ".", $y_pole[1]);

            if (count($y_pole) != 2 || floor($lon_dd) != $lon_dd || $lon_mm >= 60) {
                $y_err .= "Souřadnice Y není ve správném formátu (DD MM.MMMM)<br/>";
            }

            $lon = $lon_dd + ($lon_mm / 60);
            if ($lon < 12.09 || $lon > 18.86) {
                $y_err .= "Souřadnice Y je mimo území ČR.<br/>";
            }
            break;
        case '3':
            $x      = trim($x);
            $x_pole = explode(" ", trim($x));
            $lat_dd = str_replace(",", ".", $x_pole[0]);
            $lat_mm = str_replace(",", ".", $x_pole[1]);
            $lat_ss = str_replace(",", ".", $x_pole[2]);

            if (count($x_pole) != 3 || floor($lat_dd) != $lat_dd || floor($lat_mm) != $lat_mm || $lat_mm >= 60 || $lat_ss >= 60) {
                $x_err .= "Souřadnice X není ve správném formátu (DD MM SS.SSSS)<br/>";
            }

            $lat = $lat_dd + ($lat_mm / 60) + ($lat_ss / 3600);

            if ($lat < 48.55 || $lat > 51.06) {
                $x_err .= "Souřadnice X je mimo území ČR.<br/>";
            }

            $y      = trim($y);
            $y_pole = explode(" ", trim($y));
            $lon_dd = str_replace(",", ".", $y_pole[0]);
            $lon_mm = str_replace(",", ".", $y_pole[1]);
            $lon_ss = str_replace(",", ".", $y_pole[2]);

            if (count($y_pole) != 3 || floor($lon_dd) != $lon_dd || floor($lon_mm) != $lon_mm || $lon_mm >= 60 || $lon_ss >= 60) {
                $y_err .= "Souřadnice Y není ve správném formátu (DD MM SS.SSSS)<br/>";
            }

            $lon = $lon_dd + ($lon_mm / 60) + ($lon_ss / 3600);
            if ($lon < 12.09 || $lon > 18.86) {
                $y_err .= "Souřadnice Y je mimo území ČR.<br/>";
            }
            break;
        case '4':
        default:
            if ($y < 431700 || $y > 904600) {
                $y_err .= "Souřadnice Y je mimo území ČR.<br/>";
            }
            if ($x < 935200 || $x > 1227300) {
                $x_err .= "Souřadnice X je mimo území ČR.<br/>";
            }
            $latlon = $converter->JTSKtoWGS84($x, $y);

            $lat = $latlon['lat'];
            $lon = $latlon['lon'];
            break;
    }

    if (empty(trim($tel_cislo))) {
        $tel_cislo_err = "Zadejte prosím telefonní číslo.";
    } else {
        $sql = "SELECT tel_cislo FROM hlasky WHERE archiv='0' AND tel_cislo = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $param_tel_cislo, $param_tel_cislo);

            $param_tel_cislo = trim($tel_cislo);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1 && $override != "1") {
                    $tel_cislo_err = "Telefonní číslo je již použito.";
                    if ($_SESSION["id" == 1]) {
                        $tel_cislo_err .= "<input type=\"checkbox\" name=\"override\" value=\"1\"> Nahradit.";
                    }
                } else {
                    $tel_cislo = trim($tel_cislo);
                }
            } else {
                echo "Něco se nepovedlo. Zkuste to prosím znovu.";
            }
        }
        mysqli_stmt_close($stmt);
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

    if (empty($tel_cislo_err) && empty($sil_err) && empty($kilometr_err) && empty($x_err) && empty($y_err) && empty($ssud_err) && empty($typ_err)) {
        $sql = "INSERT INTO hlasky (tel_cislo, silnice, kilometr, smer, latitude, longitude, ssud, typ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssssss", $param_tel_cislo, $param_silnice, $param_kilometr, $param_smer, $param_lat, $param_lon, $param_ssud, $param_typ);

            $param_tel_cislo = $tel_cislo;
            $param_silnice   = $silnice;
            $param_kilometr  = $kilometr;
            $param_smer      = $smer;
            $param_lat       = $lat;
            $param_lon       = $lon;
            $param_ssud      = $ssud;
            $param_typ       = $typ;

            if (mysqli_stmt_execute($stmt)) {
                $param_id = mysqli_insert_id($link);

                $sql = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES (?, ?, ?, ?, ?)";

                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssss", $param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);
                    $param_hlaska_id = $param_id;
                    $param_user      = htmlspecialchars($_SESSION["username"]);
                    $param_cas       = microtime(true);
                    $param_sloupec   = "tel_cislo";
                    $param_new_value = $param_tel_cislo;
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "";
                    }
                }
                $sql = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssss", $param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);
                    $param_hlaska_id = $param_id;
                    $param_user      = htmlspecialchars($_SESSION["username"]);
                    $param_cas       = microtime(true);
                    $param_sloupec   = "silnice";
                    $param_new_value = $param_silnice;
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "";
                    }
                }
                $sql = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssss", $param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);
                    $param_hlaska_id = $param_id;
                    $param_user      = htmlspecialchars($_SESSION["username"]);
                    $param_cas       = microtime(true);
                    $param_sloupec   = "kilometr";
                    $param_new_value = $param_kilometr;
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "";
                    }
                }
                $sql = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssss", $param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);
                    $param_hlaska_id = $param_id;
                    $param_user      = htmlspecialchars($_SESSION["username"]);
                    $param_cas       = microtime(true);
                    $param_sloupec   = "smer";
                    $param_new_value = $param_smer;
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "";
                    }
                }
                $sql = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssss", $param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);
                    $param_hlaska_id = $param_id;
                    $param_user      = htmlspecialchars($_SESSION["username"]);
                    $param_cas       = microtime(true);
                    $param_sloupec   = "longitude";
                    $param_new_value = $param_lon;
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "";
                    }
                }
                $sql = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssss", $param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);
                    $param_hlaska_id = $param_id;
                    $param_user      = htmlspecialchars($_SESSION["username"]);
                    $param_cas       = microtime(true);
                    $param_sloupec   = "latitude";
                    $param_new_value = $param_lat;
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "";
                    }
                }
                $sql = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssss", $param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);
                    $param_hlaska_id = $param_id;
                    $param_user      = htmlspecialchars($_SESSION["username"]);
                    $param_cas       = microtime(true);
                    $param_sloupec   = "platnost";
                    $param_new_value = "1";
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "";
                    }
                }
                $sql = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssss", $param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);
                    $param_hlaska_id = $param_id;
                    $param_user      = htmlspecialchars($_SESSION["username"]);
                    $param_cas       = microtime(true);
                    $param_sloupec   = "ssud";
                    $param_new_value = $param_ssud;
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "";
                    }
                }
                $sql = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssss", $param_hlaska_id, $param_sloupec, $param_new_value, $param_user, $param_cas);
                    $param_hlaska_id = $param_id;
                    $param_user      = htmlspecialchars($_SESSION["username"]);
                    $param_cas       = microtime(true);
                    $param_sloupec   = "typ";
                    $param_new_value = $param_typ;
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "";
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }
        header("location: index.php");
    }
}
?>

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <title>Vložení SOS hlásky</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 500px;
            padding: 20px;
        }

        tr.dark {
            background-color: #ddd;
            color: black;
        }

        tr.light {
            background-color: #fff;
            color: black;
        }

        tr.dark-strikeout {
            background-color: #ddd;
            color: red;
        }

        tr.light-strikeout {
            background-color: #fff;
            color: red;
        }
    </style>
</head>

<body>
    <?php
$app_up = PageHeader();
?>
    <div class="wrapper">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($tel_cislo_err)) ? 'has-error' : ''; ?>">
                <label>Telefonní číslo</label>
                <input type="text" name="tel_cislo" class="form-control" value="<?php echo $tel_cislo; ?>" autofocus>
                <span class="help-block"><?php echo $tel_cislo_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($silnice_err)) ? 'has-error' : ''; ?>">
                <label for="silnice">Název silnice:</label>
                <select class="form-control" id="silnice" name="silnice">
                    <option value="">---</option>
                    <?php
$sql = "SELECT id,nazev FROM enum_silnice ORDER BY nazev";

if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $sil_id, $sil_name);

        while (mysqli_stmt_fetch($stmt)) {
            echo "<option value=\"$sil_id\"";
            if ($sil_id == $silnice) {
                echo " SELECTED";
            }
            echo ">$sil_name</option>\n";
        }
    }
}
mysqli_stmt_close($stmt);
?>
                </select>
                <span class="help-block"><?php echo $silnice_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($kilometr_err)) ? 'has-error' : ''; ?>">
                <label>Kilometr</label>
                <input type="text" name="kilometr" class="form-control" value="<?php echo $kilometr; ?>">
                <span class="help-block"><?php echo $kilometr_err; ?></span>
            </div>

            <div class="form-group">
                <label for="smer">Směr:</label>
                <select class="form-control" id="smer" name="smer">
                    <option value="+" <?php
if ($smer == "+") {
    echo " SELECTED";
}
?>>rostoucí</option>
                    <option value="-" <?php
if ($smer == "-") {
    echo " SELECTED";
}
?>>klesající</option>
                </select>
            </div>

            <div class="form-group">
                <label for="format">Formát souřadnice:</label>
                <select class="form-control" id="format" name="format">
                    <?php
$sql = "SELECT `id`,`name` FROM enum_srid ORDER BY `name`";

if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $srid_id, $srid_name);

        while (mysqli_stmt_fetch($stmt)) {
            echo "<option value=\"$srid_id\"";
            if ($srid_id == $format) {
                echo " SELECTED";
            }
            echo ">$srid_name</option>\n";
        }
    }
}
mysqli_stmt_close($stmt);
?>
                </select>
            </div>

            <div class="form-group <?php echo (!empty($x_err)) ? 'has-error' : ''; ?>">
                <label>Souřadnice X (JTSK cca 1 000 000 | WGS cca 49):</label>
                <input type="text" name="x" class="form-control" value="<?php echo $x; ?>">
                <span class="help-block"><?php echo $x_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($y_err)) ? 'has-error' : ''; ?>">
                <label>Souřadnice Y (JTSK cca 700 000 | WGS cca 15):</label>
                <input type="text" name="y" class="form-control" value="<?php echo $y; ?>">
                <span class="help-block"><?php echo $y_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($ssud_err)) ? 'has-error' : ''; ?>">
                <label for="ssud">Středisko SSÚD:</label>
                <select class="form-control" id="ssud" name="ssud">
                    <option value="">---</option>
                    <?php
$sql = "SELECT id,popis FROM enum_ssud ORDER BY popis";

if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $ssud_id, $ssud_name);

        while (mysqli_stmt_fetch($stmt)) {
            echo "<option value=\"$ssud_id\"";
            if ($ssud_id == $ssud) {
                echo " SELECTED";
            }
            echo ">$ssud_name</option>\n";
        }
    }
}
mysqli_stmt_close($stmt);
?>
                </select>
                <span class="help-block"><?php echo $ssud_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($typ_err)) ? 'has-error' : ''; ?>">
                <label for="typ">Typ hlásky:</label>
                <select class="form-control" id="typ" name="typ">
                    <option value="">---</option>
                    <?php
$sql = "SELECT id,popis FROM enum_typ ORDER BY popis";

if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $typ_id, $typ_name);

        while (mysqli_stmt_fetch($stmt)) {
            echo "<option value=\"$typ_id\"";
            if ($typ_id == $typ) {
                echo " SELECTED";
            }
            echo ">$typ_name</option>\n";
        }
    }
}
mysqli_stmt_close($stmt);
?>
                </select>
                <span class="help-block"><?php echo $typ_err; ?></span>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Vložit">
            </div>
        </form>
    </div>

    <hr>
    <?php
echo "<table width=\"100%\">";
echo "<tr><th>&nbsp;</th><th>Telefonní číslo</th><th>Silnice</th><th>Kilometr</th><th>Směr</th><th>Zeměpisná šířka</th><th>Zeměpisná délka</th><th>SSÚD</th><th>Typ</th><th></th></tr>";
$i = 0;

$query60 = "SELECT id, tel_cislo, silnice, kilometr, smer, longitude, latitude, platnost, ssud, typ FROM hlasky WHERE export = 0 ORDER BY tel_cislo";
if ($result60 = mysqli_query($link, $query60)) {
    while ($row60 = mysqli_fetch_row($result60)) {
        $id         = $row60[0];
        $tel_cislo  = $row60[1];
        $silnice    = $row60[2];
        $kilometr   = $row60[3];
        $smer       = $row60[4];
        $longitude  = $row60[5];
        $latitude   = $row60[6];
        $platnost   = $row60[7];
        $ssud       = $row60[8];
        $ssud_nazev = "";
        $typ        = $row60[9];
        $typ_nazev  = "";

        $smer_nazev = SmerNazev($silnice, $smer, $kilometr);

        if (substr($silnice, 0, 1) != "D") {
            $silnice = "I/" . $silnice;
        }
        $kilometr = str_replace(".", ",", $kilometr);

        $query237 = "SELECT popis FROM enum_ssud WHERE id = '$ssud';";
        if ($result237 = mysqli_query($link, $query237)) {
            while ($row237 = mysqli_fetch_row($result237)) {
                $ssud_nazev = $row237[0];
            }
        }

        $query237 = "SELECT popis FROM enum_typ WHERE id = '$typ';";
        if ($result237 = mysqli_query($link, $query237)) {
            while ($row237 = mysqli_fetch_row($result237)) {
                $typ_nazev = $row237[0];
            }
        }

        echo "<tr class=\"";
        if ($i % 2 == 0) {
            echo "dark";
        } else {
            echo "light";
        }
        if ($platnost == 0) {
            echo "-strikeout";
        }
        echo "\"><td>&nbsp;</td><td>$tel_cislo</td><td>$silnice</td><td>$kilometr</td><td>$smer_nazev</td><td>$latitude</td><td>$longitude</td><td>$ssud_nazev</td><td>$typ_nazev</td>";
        echo "<td><a href=\"edit.php?id=$id&up=$app_up\">Edit</a></td></tr>";
        $i = $i + 1;

    }
}
echo "</table>";

mysqli_close($link);
?>