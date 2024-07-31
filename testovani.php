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

$datum = @$_POST["datum"];
$datum_err = "";
$silnice = @$_POST["silnice"];
$silnice_err = "";
$osoba = @$_POST["osoba"];
$osoba_err = "";

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
        $query33 = "INSERT INTO testovani (datum, silnice, osoba, zadatel) VALUES ('$datum', '$silnice', '$osoba', '');";
        $prikaz33 = mysqli_query($link, $query33);
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
            width: 500px;
            padding: 20px;
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
                <input type="date" name="datum" min="<?php echo $today; ?>" class="form-control"
                    value="<?php echo $datum; ?>">
                <span class="help-block">
                    <?php echo $datum_err; ?>
                </span>
            </div>

            <div class="form-group <?php echo (!empty($silnice_err)) ? 'has-error' : ''; ?>">
                <label for="silnice">Název silnice:</label>
                <select class="form-control" id="silnice" name="silnice">
                    <option value="">---</option>
                    <?php
                    $query81 = "SELECT id, nazev FROM enum_silnice ORDER BY nazev;";
                    if ($result81 = mysqli_query($link, $query81)) {
                        while ($row81 = mysqli_fetch_row($result81)) {
                            $sil_id = $row81[0];
                            $sil_name = $row81[1];

                            echo "<option value=\"$sil_id\"";
                            if ($sil_id == $silnice) {
                                echo " SELECTED";
                            }
                            echo ">$sil_name</option>\n";
                        }
                    }
                    ?>
                </select>
                <span class="help-block">
                    <?php echo $silnice_err; ?>
                </span>
            </div>

            <div class="form-group <?php echo (!empty($osoba_err)) ? 'has-error' : ''; ?>">
                <label for="osoba">Koordinátor testování:</label>
                <select class="form-control" id="osoba" name="osoba">
                    <option value="">---</option>
                    <?php
                    $query106 = "SELECT id, jmeno, tel_cislo FROM test_osoby ORDER BY jmeno;";
                    if ($result106 = mysqli_query($link, $query106)) {
                        while ($row106 = mysqli_fetch_row($result106)) {
                            $os_id = $row106[0];
                            $os_jmeno = $row106[1];
                            $os_cislo = $row106[2];

                            echo "<option value=\"$os_id\"";
                            if ($os_id == $osoba) {
                                echo " SELECTED";
                            }
                            echo ">$os_jmeno | $os_cislo</option>\n";
                        }
                    }
                    ?>
                </select>
                <span class="help-block">
                    <?php echo $osoba_err; ?>
                </span>
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

    $query141 = "SELECT id, datum, silnice, osoba, hlasky, overeno FROM testovani WHERE schvaleno = 1 and odmitnuto = 0 and archiv = 0 and datum <= '$today' ORDER BY datum, silnice;";
    if ($result141 = mysqli_query($link, $query141)) {
        while ($row141 = mysqli_fetch_row($result141)) {
            $sel_id = $row141[0];
            $sel_datum = $row141[1];
            $sel_silnice = $row141[2];
            $sel_osoba = $row141[3];
            $sel_hlasky = $row141[4];
            $overeno = $row141[5];

            $datum_format = date("d.m.Y", strtotime($sel_datum));

            $query153 = "SELECT jmeno, tel_cislo FROM test_osoby WHERE id='$sel_osoba';";
            if ($result153 = mysqli_query($link, $query153)) {
                while ($row153 = mysqli_fetch_row($result153)) {
                    $jmeno = $row153[0];
                    $tel_cislo = $row153[1];
                }
            }
            $koordinator = $jmeno . " | " . $tel_cislo;

            $hlasky_arr = explode("|", $sel_hlasky);
            $hlasky_arr = array_filter($hlasky_arr);
            $pocet_hlasek = count($hlasky_arr);

            $back_line_col = ($i % 2 == 0) ? "#ddd" : "#fff";

            echo "<tr style=\"background-color:$back_line_col;\">";
            echo "<td>&nbsp;</td><td>$datum_format</td><td>$sel_silnice</td><td>$koordinator</td><td>$pocet_hlasek</td>";
            $stav_schvaleni = "Nevyhodnoceno";
            $bg_col = $back_line_col;
            if ($overeno == 1) {
                $stav_schvaleni = "Provedeno vyhodnocení";
                $bg_col = "#0f0";
            }
            echo "<td style=\"background-color:$bg_col;\">";
            echo $stav_schvaleni;
            echo "</td>";
            echo "<td><a href=\"testovani_finish.php?id=$sel_id\">Edit</a></td></tr>";
            $i++;

        }
        if (mysqli_num_rows($result141) == 0) {
            echo "<tr><td colspan=\"6\">&nbsp; <i>Nebyla nalezena položka odpovídající tomuto omezení.</i></td></tr>";
        }
    }

    echo "</table>";
    echo "<hr>";
    echo "<h3>&nbsp; Naplánovaná testování</h3>";
    echo "<table width=\"100%\">";
    echo "<tr><th width=\"15\">&nbsp;</th><th width=\"10%\">Datum</th><th width=\"10%\">Silnice</th><th width=\"40%\">Koordinátor</th><th width=\"10%\">Počet hlásek</th><th width=\"20%\"></th><th></th></tr>";
    $i = 0;

    $query195 = "SELECT id, datum, silnice, osoba, hlasky, schvaleno, odmitnuto, komentar FROM testovani WHERE (finalni = 1 AND datum > '$today') OR (finalni = 1 AND schvaleno = 0 AND odmitnuto = 0) ORDER BY datum, silnice;";
    if ($result195 = mysqli_query($link, $query195)) {
        while ($row195 = mysqli_fetch_row($result195)) {
            $sel_id = $row195[0];
            $sel_datum = $row195[1];
            $sel_silnice = $row195[2];
            $sel_osoba = $row195[3];
            $sel_hlasky = $row195[4];
            $schvaleno = $row195[5];
            $odmitnuto = $row195[6];
            $komentar = $row195[7];

            $datum_format = date("d.m.Y", strtotime($sel_datum));

            $query209 = "SELECT jmeno, tel_cislo FROM test_osoby WHERE id='$sel_osoba';";
            if ($result209 = mysqli_query($link, $query209)) {
                while ($row209 = mysqli_fetch_row($result209)) {
                    $jmeno = $row209[0];
                    $tel_cislo = $row209[1];
                }
            }
            $koordinator = $jmeno . " | " . $tel_cislo;

            $hlasky_arr = explode("|", $sel_hlasky);
            $hlasky_arr = array_filter($hlasky_arr);
            $pocet_hlasek = count($hlasky_arr);

            $back_line_col = ($i % 2 == 0) ? "#ddd" : "#fff";

            echo "<tr style=\"background-color:$back_line_col;\">";
            echo "<td>&nbsp;</td><td>$datum_format</td><td>$sel_silnice</td><td>$koordinator</td><td>$pocet_hlasek</td>";
            $stav_schvaleni = "Čeká na schválení";
            $bg_col = $back_line_col;
            if ($schvaleno == 1) {
                $stav_schvaleni = "Schváleno";
                $bg_col = "#0f0";
            }
            if ($odmitnuto == 1) {
                $stav_schvaleni = "Odmítnuto";
                $bg_col = "#f00";
            }
            echo "<td style=\"background-color:$bg_col;\">";
            if ($komentar != "") {
                echo "<span title=\"$komentar\" style=\"border-bottom: 1px dotted black;\">";
            }
            echo $stav_schvaleni;
            if ($komentar != "") {
                echo "</span>";
            }
            echo "</td>";
            echo "<td><a href=\"testovani_zmena.php?id=$sel_id\">Edit</a></td></tr>";
            $i++;

        }
        if (mysqli_num_rows($result195) == 0) {
            echo "<tr><td colspan=\"6\">&nbsp; <i>Nebyla nalezena položka odpovídající tomuto omezení.</i></td></tr>";
        }
    }

    echo "</table>";
    echo "<hr>";
    echo "<h3>&nbsp; Připravovaná testování</h3>";
    echo "<table width=\"100%\">";
    echo "<tr><th width=\"15\">&nbsp;</th><th width=\"10%\">Datum</th><th width=\"10%\">Silnice</th><th width=\"40%\">Koordinátor</th><th width=\"10%\">Počet hlásek</th><th width=\"20%\"></th><th></th></tr>";
    $i = 0;

    $query261 = "SELECT id, datum, silnice, osoba, hlasky FROM testovani WHERE finalni = 0 ORDER BY datum, silnice;";
    if ($result261 = mysqli_query($link, $query261)) {
        while ($row261 = mysqli_fetch_row($result261)) {
            $sel_id = $row261[0];
            $sel_datum = $row261[1];
            $sel_silnice = $row261[2];
            $sel_osoba = $row261[3];
            $sel_hlasky = $row261[4];

            $datum_format = date("d.m.Y", strtotime($sel_datum));

            $query272 = "SELECT jmeno, tel_cislo FROM test_osoby WHERE id='$sel_osoba';";
            if ($result272 = mysqli_query($link, $query272)) {
                while ($row272 = mysqli_fetch_row($result272)) {
                    $jmeno = $row272[0];
                    $tel_cislo = $row272[1];
                }
            }
            $koordinator = $jmeno . " | " . $tel_cislo;

            $hlasky_arr = explode("|", $sel_hlasky);
            $hlasky_arr = array_filter($hlasky_arr);
            $pocet_hlasek = count($hlasky_arr);

            echo "<tr style=\"";
            echo ($i % 2 == 0) ? "background-color:#ddd;" : "background-color:#fff;";
            echo "\"><td>&nbsp;</td><td>$datum_format</td><td>$sel_silnice</td><td>$koordinator</td><td>$pocet_hlasek</td>";
            echo "<td><a href=\"submit_test.php?id=$sel_id\">Požádat o schválení</a></td>";
            echo "<td><a href=\"testovani_edit.php?id=$sel_id\">Edit</a></td></tr>";
            $i++;

        }
        if (mysqli_num_rows($result261) == 0) {
            echo "<tr><td colspan=\"6\">&nbsp; <i>Nebyla nalezena položka odpovídající tomuto omezení.</i></td></tr>";
        }
    }

    echo "</table>";
    echo "<hr>";
    mysqli_close($link);
    ?>