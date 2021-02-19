<?php
date_default_timezone_set('Europe/Prague');
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'config.php';

$datum       = @$_POST["datum"];
$datum_err   = "";
$silnice     = @$_POST["silnice"];
$silnice_err = "";
$osoba       = @$_POST["osoba"];
$osoba_err   = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($datum))) {
        $datum_err = "Zadejte prosím datum testování.";
    }
    if (empty(trim($silnice))) {
        $silnice_err = "Vyberte prosím silnici.";
    }
    if (empty(trim($osoba))) {
        $osoba_err = "Vyberte prosím odpovědnou osobu.";
    }

    if (empty($datum_err) && empty($silnice_err) && empty($osoba_err)) {
        $query31  = "INSERT INTO testovani (datum, silnice, osoba) VALUES ('$datum', '$silnice', '$osoba');";
        $prikaz31 = mysqli_query($link, $query31);
    }
}
?>


<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Index databáze INFO35</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
        }
        .wrapper {
            width: 500px; padding: 20px;
        }
    </style>
</head>
<body>
    <?php
PageHeader();
$today = date("Y-m-d", strtotime("+ 1 day"));
?>
    <div class="wrapper">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

            <div class="form-group <?php echo (!empty($datum_err)) ? 'has-error' : ''; ?>">
                <label>Datum testování</label>
                <input type="date" name="datum" min="<?php echo $today; ?>" class="form-control" value="<?php echo $datum; ?>">
                <span class="help-block"><?php echo $datum_err; ?></span>
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

            <div class="form-group <?php echo (!empty($osoba_err)) ? 'has-error' : ''; ?>">
                <label for="osoba">Koordinátor testování:</label>
                <select class="form-control" id="osoba" name="osoba">
                    <option value="">---</option>
                    <?php
$sql = "SELECT id, jmeno,tel_cislo FROM test_osoby ORDER BY jmeno";

if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $os_id, $os_jmeno, $os_cislo);

        while (mysqli_stmt_fetch($stmt)) {
            echo "<option value=\"$os_id\"";
            if ($os_id == $osoba) {
                echo " SELECTED";
            }
            echo ">$os_jmeno | $os_cislo</option>\n";
        }
    }
}
mysqli_stmt_close($stmt);
?>
                </select>
                <span class="help-block"><?php echo $osoba_err; ?></span>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Vložit">
            </div>
        </form>
    </div>
    <hr>
    <?php
$today = date("Y-m-d");

echo "<h3>Dokončená testování</h3>";
echo "<table width=\"100%\">";
echo "<tr><th width=\"15\">&nbsp;</th><th width=\"10%\">Datum</th><th width=\"10%\">Silnice</th><th width=\"40%\">Koordinátor</th><th width=\"10%\">Počet hlásek</th><th width=\"20%\"></th><th></th></tr>";
$i = 0;

$query81 = "SELECT id, datum, silnice, osoba, hlasky, overeno FROM testovani WHERE schvaleno = 1 and datum >= $today ORDER BY datum, silnice;";
if ($result81 = mysqli_query($link, $query81)) {
    while ($row81 = mysqli_fetch_row($result81)) {
        $sel_id      = $row81[0];
        $sel_datum   = $row81[1];
        $sel_silnice = $row81[2];
        $sel_osoba   = $row81[3];
        $sel_hlasky  = $row81[4];
        $overeno     = $row81[5];

        $datum_format = date("d.m.Y", strtotime($sel_datum));

        $query138 = "SELECT jmeno, tel_cislo FROM test_osoby WHERE id='$sel_osoba';";
        if ($result138 = mysqli_query($link, $query138)) {
            while ($row138 = mysqli_fetch_row($result138)) {
                $jmeno     = $row138[0];
                $tel_cislo = $row138[1];
            }
        }
        $koordinator = $jmeno . " | " . $tel_cislo;

        $hlasky_arr   = explode("|", $sel_hlasky);
        $hlasky_arr   = array_filter($hlasky_arr);
        $pocet_hlasek = count($hlasky_arr);

        if ($i % 2 == 0) {
            $back_line_col = "#ddd";
        } else {
            $back_line_col = "#fff";
        }

        echo "<tr style=\"background-color:$back_line_col;\">";
        echo "<td>&nbsp;</td><td>$datum_format</td><td>$sel_silnice</td><td>$koordinator</td><td>$pocet_hlasek</td>";
        $stav_schvaleni = "Nevyhodnoceno";
        $bg_col         = $back_line_col;
        if ($overeno == 1) {
            $stav_schvaleni = "Schváleno";
            $bg_col         = "#0f0";
        }
        echo "<td style=\"background-color:$bg_col;\">";
        echo "$stav_schvaleni";
        echo "</td>";
        echo "<td><a href=\"testovani_finish.php?id=$sel_id\">Edit</a></td></tr>";
        $i = $i + 1;

    }
    if (mysqli_num_rows($result81) == 0) {
        echo "<tr><td colspan=\"6\"><i>Nebyla nalezena položka odpovídající tomuto omezení.</i></td></tr>";
    }
}

echo "</table>";
echo "<hr>";
echo "<h3>Naplánovaná testování</h3>";
echo "<table width=\"100%\">";
echo "<tr><th width=\"15\">&nbsp;</th><th width=\"10%\">Datum</th><th width=\"10%\">Silnice</th><th width=\"40%\">Koordinátor</th><th width=\"10%\">Počet hlásek</th><th width=\"20%\"></th><th></th></tr>";
$i = 0;

$query81 = "SELECT id, datum, silnice, osoba, hlasky, schvaleno, odmitnuto, komentar FROM testovani WHERE finalni = 1 AND datum < $today ORDER BY datum, silnice;";
if ($result81 = mysqli_query($link, $query81)) {
    while ($row81 = mysqli_fetch_row($result81)) {
        $sel_id      = $row81[0];
        $sel_datum   = $row81[1];
        $sel_silnice = $row81[2];
        $sel_osoba   = $row81[3];
        $sel_hlasky  = $row81[4];
        $schvaleno   = $row81[5];
        $odmitnuto   = $row81[6];
        $komentar    = $row81[7];

        $datum_format = date("d.m.Y", strtotime($sel_datum));

        $query138 = "SELECT jmeno, tel_cislo FROM test_osoby WHERE id='$sel_osoba';";
        if ($result138 = mysqli_query($link, $query138)) {
            while ($row138 = mysqli_fetch_row($result138)) {
                $jmeno     = $row138[0];
                $tel_cislo = $row138[1];
            }
        }
        $koordinator = $jmeno . " | " . $tel_cislo;

        $hlasky_arr   = explode("|", $sel_hlasky);
        $hlasky_arr   = array_filter($hlasky_arr);
        $pocet_hlasek = count($hlasky_arr);

        if ($i % 2 == 0) {
            $back_line_col = "#ddd";
        } else {
            $back_line_col = "#fff";
        }

        echo "<tr style=\"background-color:$back_line_col;\">";
        echo "<td>&nbsp;</td><td>$datum_format</td><td>$sel_silnice</td><td>$koordinator</td><td>$pocet_hlasek</td>";
        $stav_schvaleni = "Čeká na schválení";
        $bg_col         = $back_line_col;
        if ($schvaleno == 1) {
            $stav_schvaleni = "Schváleno";
            $bg_col         = "#0f0";
        }
        if ($odmitnuto == 1) {
            $stav_schvaleni = "Odmítnuto";
            $bg_col         = "#f00";
        }
        echo "<td style=\"background-color:$bg_col;\">";
        if ($komentar != "") {
            echo "<span title=\"$komentar\" style=\"border-bottom: 1px dotted black;\">";
        }
        echo "$stav_schvaleni";
        if ($komentar != "") {
            echo "</span>";
        }
        echo "</td>";
        echo "<td><a href=\"testovani_zmena.php?id=$sel_id\">Edit</a></td></tr>";
        $i = $i + 1;

    }
    if (mysqli_num_rows($result81) == 0) {
        echo "<tr><td colspan=\"6\"><i>Nebyla nalezena položka odpovídající tomuto omezení.</i></td></tr>";
    }
}

echo "</table>";
echo "<hr>";
echo "<h3>Připravovaná testování</h3>";
echo "<table width=\"100%\">";
echo "<tr><th width=\"15\">&nbsp;</th><th width=\"10%\">Datum</th><th width=\"10%\">Silnice</th><th width=\"40%\">Koordinátor</th><th width=\"10%\">Počet hlásek</th><th width=\"20%\"></th><th></th></tr>";
$i = 0;

$query81 = "SELECT id, datum, silnice, osoba, hlasky FROM testovani WHERE finalni = 0 ORDER BY datum, silnice;";
if ($result81 = mysqli_query($link, $query81)) {
    while ($row81 = mysqli_fetch_row($result81)) {
        $sel_id      = $row81[0];
        $sel_datum   = $row81[1];
        $sel_silnice = $row81[2];
        $sel_osoba   = $row81[3];
        $sel_hlasky  = $row81[4];

        $datum_format = date("d.m.Y", strtotime($sel_datum));

        $query138 = "SELECT jmeno, tel_cislo FROM test_osoby WHERE id='$sel_osoba';";
        if ($result138 = mysqli_query($link, $query138)) {
            while ($row138 = mysqli_fetch_row($result138)) {
                $jmeno     = $row138[0];
                $tel_cislo = $row138[1];
            }
        }
        $koordinator = $jmeno . " | " . $tel_cislo;

        $hlasky_arr   = explode("|", $sel_hlasky);
        $hlasky_arr   = array_filter($hlasky_arr);
        $pocet_hlasek = count($hlasky_arr);

        echo "<tr style=\"";
        if ($i % 2 == 0) {
            echo "background-color:#ddd;";
        } else {
            echo "background-color:#fff;";
        }
        echo "\"><td>&nbsp;</td><td>$datum_format</td><td>$sel_silnice</td><td>$koordinator</td><td>$pocet_hlasek</td>";
        echo "<td><a href=\"submit_test.php?id=$sel_id\">Požádat o schválení</a></td>";
        echo "<td><a href=\"testovani_edit.php?id=$sel_id\">Edit</a></td></tr>";
        $i = $i + 1;

    }
    if (mysqli_num_rows($result81) == 0) {
        echo "<tr><td colspan=\"6\"><i>Nebyla nalezena položka odpovídající tomuto omezení.</i></td></tr>";
    }
}

echo "</table>";
echo "<hr>";
mysqli_close($link);
?>