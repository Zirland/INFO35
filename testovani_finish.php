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
    <title>Vyhodnocení testu hlásek</title>
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

        tr.dark-smoke {
            background-color: #8b0000;
            color: white;
        }

        tr.light-smoke {
            background-color: #dc143c;
            color: white;
        }

        tr.dark-smoke select,
        input {
            color: black;
        }

        tr.light-smoke select,
        input {
            color: black;
        }
    </style>
</head>

<?php
require_once 'config.php';

$id_user = $_SESSION["id"];
$up = $_GET["up"];

$test_id = @$_GET["id"];
if ($test_id == "") {
    $test_id = @$_POST["id"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $test_id = @$_POST["id"];

    $projekt = $_POST["projekt"];
    $query77 = "UPDATE testovani SET projekt = '$projekt' WHERE id = '$test_id';";
    $prikaz77 = mysqli_query($link, $query77);

    $query80 = "SELECT hlasky FROM testovani WHERE id = $test_id;";
    if ($result80 = mysqli_query($link, $query80)) {
        while ($row80 = mysqli_fetch_row($result80)) {
            $old_hlasky = $row80[0];
        }
    }
    $hlasky_array = explode("|", $old_hlasky);

    foreach ($hlasky_array as $hl_id) {
        $Zindex = "H{$hl_id}Z";
        $Oindex = "H{$hl_id}O";
        $Iindex = "H{$hl_id}I";
        $LSindex = "H{$hl_id}LS";
        $L1index = "H{$hl_id}L1";
        $Pindex = "H{$hl_id}P";
        $Rindex = "H{$hl_id}R";

        $newZkouska = $_POST[$Zindex];
        $newHovorOut = $_POST[$Oindex];
        $newHovorIn = $_POST[$Iindex];
        $newLokaceS = $_POST[$LSindex];
        $newLokace1 = $_POST[$L1index];
        $newPoznamka = $_POST[$Pindex];
        $newStatus = $_POST[$Rindex];

        $query105 = "UPDATE test_result SET zkouska = '$newZkouska', hovorOUT = '$newHovorOut', hovorIN = '$newHovorIn', lokaceSPEL = '$newLokaceS', lokace112 = '$newLokace1', poznamka = '$newPoznamka', `status` = '$newStatus' WHERE id_test = '$test_id' AND id_hlaska = '$hl_id';";
        $prikaz105 = mysqli_query($link, $query105);

        if ($newStatus == "0") {
            $query109 = "UPDATE hlasky SET smoketest = '1' WHERE id = '$hl_id';";
            $prikaz109 = mysqli_query($link, $query109);
        }
    }

    $time = date("H:i:s", time());
    echo "<span style=\"background-color:yellow;\">Data uložena v $time.</span>";
}

$query118 = "SELECT datum, silnice, osoba, hlasky, schvaleno, odmitnuto, komentar, projekt, archiv, overeno FROM testovani WHERE id = $test_id;";
if ($result118 = mysqli_query($link, $query118)) {
    while ($row118 = mysqli_fetch_row($result118)) {
        $old_datum = $row118[0];
        $old_silnice = $row118[1];
        $old_osoba = $row118[2];
        $old_hlasky = $row118[3];
        $old_schvaleno = $row118[4];
        $old_odmitnuto = $row118[5];
        $old_komentar = $row118[6];
        $old_projekt = $row118[7];
        $archiv = $row118[8];
        $overeno = $row118[9];
    }
}
PageHeader();
?>

<table width="100%">
    <tr>
        <th>&nbsp;</th>
        <th>Datum</th>
        <th>Silnice</th>
        <th>Koordinátor</th>
        <th>&nbsp;</th>
    </tr>
    <tr>
        <td></td>

        <?php
        $datumtestu = date("d.m.Y", strtotime($old_datum));
        echo "<td>$datumtestu</td>";

        $query151 = "SELECT nazev FROM enum_silnice WHERE id = '$old_silnice';";
        if ($result151 = mysqli_query($link, $query151)) {
            while ($row151 = mysqli_fetch_row($result151)) {
                $sil_name = $row151[0];
            }
        }

        echo "<td>$sil_name</td>";

        $query160 = "SELECT jmeno, tel_cislo FROM test_osoby WHERE id = '$old_osoba';";
        if ($result160 = mysqli_query($link, $query160)) {
            while ($row160 = mysqli_fetch_row($result160)) {
                $os_jmeno = $row160[0];
                $os_cislo = $row160[1];
            }
        }

        echo "<td>$os_jmeno | $os_cislo</td>";
        ?>
        <td></td>
    </tr>
    <?php
    $stav_schvaleni = "Čeká na schválení";
    $bg_col = "#fff";
    if ($old_schvaleno == 1) {
        $stav_schvaleni = "Schváleno";
        $bg_col = "#0f0";
    }
    if ($old_odmitnuto == 1) {
        $stav_schvaleni = "Odmítnuto";
        $bg_col = "#f00";
    }
    ?>
    <tr>
        <td></td>
        <td style="background-color:<?php echo $bg_col; ?>"> <?php echo $stav_schvaleni ?> </td>
        <td>
        </td>
        <td colspan="2">Komentář:
            <?php echo $old_komentar; ?>
        </td>
    </tr>
</table>
<br />
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $test_id; ?>">
    <table>
        <tr>
            <td>
                &nbsp; Název projektu: <input type="text" name="projekt" size="50" value="<?php echo $old_projekt; ?>">
            </td>
        </tr>
    </table>
    <br />
    <table>
        <?php
        $z = 0;
        $hlasky_array = explode("|", $old_hlasky);
        $hlasky_list = implode(",", $hlasky_array);

        echo "<tr><th></th><th style=\"padding:10px\">Typ hlásky</th><th style=\"padding:10px\">Označení</th><th style=\"padding:10px\">Směr</th><th style=\"padding:10px\">Zkouška</th><th style=\"padding:10px\">Hovor na 112</th><th style=\"padding:10px\">Zpětné volání</th><th style=\"padding:10px\">Poloha SPEL</th><th style=\"padding:10px\">Poloha 112</th><th style=\"padding:10px\">Poznámka</th></tr>";
        $i = 0;
        $query213 = "SELECT id, silnice, kilometr, smer, smoketest, typ FROM hlasky WHERE silnice = '$old_silnice' AND id IN ($hlasky_list) ORDER BY CAST(kilometr AS unsigned), smer";
        if ($result213 = mysqli_query($link, $query213)) {
            while ($row213 = mysqli_fetch_row($result213)) {
                $hl_id = $row213[0];
                $hl_silnice = $row213[1];
                $hl_kilometr = $row213[2];
                $hl_smer = $row213[3];
                $hl_smoke = $row213[4];
                $hl_typ = $row213[5];

                $smer_nazev = SmerNazev($hl_silnice, $hl_smer, $hl_kilometr);

                $query225 = "SELECT zkouska, hovorOUT, hovorIN, lokaceSPEL, lokace112, poznamka, `status` FROM test_result WHERE id_test = '$test_id' AND id_hlaska = '$hl_id';";
                if ($result225 = mysqli_query($link, $query225)) {
                    while ($row225 = mysqli_fetch_row($result225)) {
                        $zkouska = $row225[0];
                        $hovor_out = $row225[1];
                        $hovor_in = $row225[2];
                        $lokaceSPEL = $row225[3];
                        $lokace112 = $row225[4];
                        $poznamka = $row225[5];
                        $status = $row225[6];
                    }
                }
                echo "<tr class=\"";
                echo ($i % 2 == 0) ? "dark" : "light";
                if ($hl_smoke == 0) {
                    echo "-smoke";
                }
                echo "\"><td>";

                echo "<select name=\"H{$hl_id}R\">";
                echo "<option value=\"1\">Chyba</option>";
                echo "<option value=\"0\"";
                if ($status == "0") {
                    echo "SELECTED";
                }
                echo ">Stav OK</option>";
                echo "</select>";
                echo "</td><td style=\"text-align:center;\">";

                $query254 = "SELECT popis FROM enum_typ WHERE id = '$hl_typ';";
                if ($result254 = mysqli_query($link, $query254)) {
                    while ($row254 = mysqli_fetch_row($result254)) {
                        $nazev_typu = $row254[0];
                    }
                }

                echo $nazev_typu;

                echo "</td>";
                echo "<td style=\"text-align:center;\">$hl_kilometr</td>";
                echo "<td>$smer_nazev</td>";
                echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"H{$hl_id}Z\" value=\"1\"";
                if ($zkouska == "1") {
                    echo " CHECKED";
                }
                echo "></td>";

                echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"H{$hl_id}O\" value=\"1\"";
                if ($hovor_out == "1") {
                    echo " CHECKED";
                }
                echo "></td>";

                echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"H{$hl_id}I\" value=\"1\"";
                if ($hovor_in == "1") {
                    echo " CHECKED";
                }
                echo "></td>";

                echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"H{$hl_id}LS\" value=\"1\"";
                if ($lokaceSPEL == "1") {
                    echo " CHECKED";
                }
                echo "></td>";

                echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"H{$hl_id}L1\" value=\"1\"";
                if ($lokace112 == "1") {
                    echo " CHECKED";
                }
                echo "></td>";

                echo "<td><input type=\"text\" name=\"H{$hl_id}P\" value=\"$poznamka\"></td>";
                echo "</tr>";
                $z++;
                $i++;
            }
        }

        $pom = $z - 1;
        echo "<tr><td colspan=\"2\"><input type=\"hidden\" name=\"pocet\" value=\"$pom\"></td></tr>";

        if ($archiv == "0") {
            echo "<tr><td><input type=\"submit\" value=\"Uložit změny\"></form></td></tr>";
        }

        echo "</table>";
        echo "<p>&nbsp;</p>";

        echo "<a href=\"protokol.php?id=$test_id\" target=\"_blank\">Tisk prokotolu z testování</a>";

        if ($archiv == "0" && $overeno == "1") {
            echo "<p>&nbsp;</p>";
            echo "<a href=\"archivuj.php?id=$test_id\">Archivace testování</a>";
        }