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

    $query81 = "SELECT id, datum, silnice, osoba, hlasky, overeno FROM testovani WHERE archiv = 1 ORDER BY datum, silnice;";
    if ($result81 = mysqli_query($link, $query81)) {
        while ($row81 = mysqli_fetch_row($result81)) {
            $sel_id = $row81[0];
            $sel_datum = $row81[1];
            $sel_silnice = $row81[2];
            $sel_osoba = $row81[3];
            $sel_hlasky = $row81[4];
            $overeno = $row81[5];

            $datum_format = date("d.m.Y", strtotime($sel_datum));

            $query138 = "SELECT jmeno, tel_cislo FROM test_osoby WHERE id='$sel_osoba';";
            if ($result138 = mysqli_query($link, $query138)) {
                while ($row138 = mysqli_fetch_row($result138)) {
                    $jmeno = $row138[0];
                    $tel_cislo = $row138[1];
                }
            }
            $koordinator = $jmeno . " | " . $tel_cislo;

            $hlasky_arr = explode("|", $sel_hlasky);
            $hlasky_arr = array_filter($hlasky_arr);
            $pocet_hlasek = count($hlasky_arr);

            if ($i % 2 == 0) {
                $back_line_col = "#ddd";
            } else {
                $back_line_col = "#fff";
            }

            echo "<tr style=\"background-color:$back_line_col;\">";
            echo "<td>&nbsp;</td><td>$datum_format</td><td>$sel_silnice</td><td>$koordinator</td><td>$pocet_hlasek</td>";
            $stav_schvaleni = "Nevyhodnoceno";
            $bg_col = $back_line_col;
            if ($overeno == 1) {
                $stav_schvaleni = "Provedeno vyhodnocení";
                $bg_col = "#0f0";
            }
            echo "<td style=\"background-color:$bg_col;\">";
            echo "$stav_schvaleni";
            echo "</td>";
            echo "<td><a href=\"testovani_finish.php?id=$sel_id&up=24\">Edit</a></td></tr>";
            $i = $i + 1;

        }
        if (mysqli_num_rows($result81) == 0) {
            echo "<tr><td colspan=\"6\">&nbsp; <i>Nebyla nalezena položka odpovídající tomuto omezení.</i></td></tr>";
        }
    }

    mysqli_close($link);
    ?>