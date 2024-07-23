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

    echo "<h3>Archivovaná testování</h3>";
    echo "<table width=\"100%\">";
    echo "<tr><th width=\"15\">&nbsp;</th><th width=\"10%\">Datum</th><th width=\"10%\">Silnice</th><th width=\"40%\">Koordinátor</th><th width=\"10%\">Počet hlásek</th><th width=\"20%\"></th><th></th></tr>";
    $i = 0;

    $query43 = "SELECT id, datum, silnice, osoba, hlasky, overeno FROM testovani WHERE archiv = 1 ORDER BY datum, silnice;";
    if ($result43 = mysqli_query($link, $query43)) {
        while ($row43 = mysqli_fetch_row($result43)) {
            $sel_id = $row43[0];
            $sel_datum = $row43[1];
            $sel_silnice = $row43[2];
            $sel_osoba = $row43[3];
            $sel_hlasky = $row43[4];
            $overeno = $row43[5];

            $datum_format = date("d.m.Y", strtotime($sel_datum));

            $query55 = "SELECT jmeno, tel_cislo FROM test_osoby WHERE id='$sel_osoba';";
            if ($result55 = mysqli_query($link, $query55)) {
                while ($row55 = mysqli_fetch_row($result55)) {
                    $jmeno = $row55[0];
                    $tel_cislo = $row55[1];
                }
            }
            $koordinator = "$jmeno  | $tel_cislo";

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
            echo (string) $stav_schvaleni;
            echo "</td>";
            echo "<td><a href=\"testovani_finish.php?id=$sel_id&up=24\">Edit</a></td></tr>";
            $i++;
        }

        if (mysqli_num_rows($result43) == 0) {
            echo "<tr><td colspan=\"6\">&nbsp; <i>Nebyla nalezena položka odpovídající tomuto omezení.</i></td></tr>";
        }
    }

    mysqli_close($link);
    ?>