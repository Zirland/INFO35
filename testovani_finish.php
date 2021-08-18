<?php
date_default_timezone_set('Europe/Prague');
session_start();

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

        tr.dark-smoke input{
            color: black;
        }

        tr.light-smoke input{
            color: black;
        }

    </style>
</head>

<?php
require_once 'config.php';

$id_user = $_SESSION["id"];

$test_id = @$_GET["id"];
if ($test_id == "") {
    $test_id = @$_POST["id"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $test_id = @$_POST["id"];

    $projekt  = $_POST["projekt"];
    $query63  = "UPDATE testovani SET projekt = '$projekt' WHERE id = '$test_id';";
    $prikaz63 = mysqli_query($link, $query63);

    $query66 = "SELECT hlasky FROM testovani WHERE id = $test_id;";
    if ($result66 = mysqli_query($link, $query66)) {
        while ($row66 = mysqli_fetch_row($result66)) {
            $old_hlasky = $row66[0];
        }
    }
    $hlasky_array = explode("|", $old_hlasky);

    foreach ($hlasky_array as $hl_id) {
        $Zindex  = "H" . $hl_id . "Z";
        $Oindex  = "H" . $hl_id . "O";
        $Iindex  = "H" . $hl_id . "I";
        $LSindex = "H" . $hl_id . "LS";
        $L1index = "H" . $hl_id . "L1";
        $Pindex  = "H" . $hl_id . "P";
        $Rindex  = "H" . $hl_id . "R";

        $newZkouska  = $_POST[$Zindex];
        $newHovorOut = $_POST[$Oindex];
        $newHovorIn  = $_POST[$Iindex];
        $newLokaceS  = $_POST[$LSindex];
        $newLokace1  = $_POST[$L1index];
        $newPoznamka = $_POST[$Pindex];
        $newStatus   = $_POST[$Rindex];

        $query81  = "UPDATE test_result SET zkouska = '$newZkouska', hovorOUT = '$newHovorOut', hovorIN = '$newHovorIn', lokaceSPEL = '$newLokaceS', lokace112 = '$newLokace1', poznamka = '$newPoznamka', `status` = '$newStatus' WHERE id_test = '$test_id' AND id_hlaska = '$hl_id';";
        $prikaz81 = mysqli_query($link, $query81);

        if ($newStatus == "0") {
            $query81  = "UPDATE hlasky SET smoketest = '1' WHERE id = '$hl_id';";
            $prikaz81 = mysqli_query($link, $query81);
        }
    }

    $time = date("H:i:s", time());
    echo "<span style=\"background-color:yellow;\">Data uložena v $time.</span>";
}

$query16 = "SELECT datum, silnice, osoba, hlasky, schvaleno, odmitnuto, komentar, projekt FROM testovani WHERE id = $test_id;";
if ($result16 = mysqli_query($link, $query16)) {
    while ($row16 = mysqli_fetch_row($result16)) {
        $old_datum     = $row16[0];
        $old_silnice   = $row16[1];
        $old_osoba     = $row16[2];
        $old_hlasky    = $row16[3];
        $old_schvaleno = $row16[4];
        $old_odmitnuto = $row16[5];
        $old_komentar  = $row16[6];
        $old_projekt   = $row16[7];
    }
}
PageHeader();
?>

<table width="100%">
<tr><th>&nbsp;</th><th>Datum</th><th>Silnice</th><th>Koordinátor</th><th>&nbsp;</th></tr>
<tr>
<td></td>

<?php
$datumtestu = date("d.m.Y", strtotime($old_datum));
echo "<td>$datumtestu</td>";

$query89 = "SELECT nazev FROM enum_silnice WHERE id = '$old_silnice';";
if ($result89 = mysqli_query($link, $query89)) {
    while ($row89 = mysqli_fetch_row($result89)) {
        $sil_name = $row89[0];
    }
}

echo "<td>$sil_name</td>";

$query98 = "SELECT jmeno, tel_cislo FROM test_osoby WHERE id = '$old_osoba';";
if ($result98 = mysqli_query($link, $query98)) {
    while ($row98 = mysqli_fetch_row($result98)) {
        $os_jmeno = $row98[0];
        $os_cislo = $row98[1];
    }
}

echo "<td>$os_jmeno | $os_cislo</td>";
?>
<td></td></tr>
<?php
$stav_schvaleni = "Čeká na schválení";
$bg_col         = "#fff";
if ($old_schvaleno == 1) {
    $stav_schvaleni = "Schváleno";
    $bg_col         = "#0f0";
}
if ($old_odmitnuto == 1) {
    $stav_schvaleni = "Odmítnuto";
    $bg_col         = "#f00";
}
?>
<tr>
<td></td>
<td style="background-color:<?php echo $bg_col; ?>"> <?php echo $stav_schvaleni ?> </td>
<td>
</td>
<td colspan="2">Komentář: <?php echo $old_komentar; ?></td>
</tr>
</table>
<br/>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<input type="hidden" name="id" value="<?php echo $test_id; ?>">
<table>
<tr>
<td>
&nbsp; Název projektu: <input type="text" name="projekt" size="50" value="<?php echo $old_projekt; ?>">
</td>
</tr>
</table>
<br/>
<table>
<?php
$z            = 0;
$hlasky_array = explode("|", $old_hlasky);
$hlasky_list  = implode(",", $hlasky_array);

echo "<tr><th></th><th style=\"padding:10px\">Typ hlásky</th><th style=\"padding:10px\">Označení</th><th style=\"padding:10px\">Směr</th><th style=\"padding:10px\">Zkouška</th><th style=\"padding:10px\">Hovor na 112</th><th style=\"padding:10px\">Zpětné volání</th><th style=\"padding:10px\">Lokalizace</th><th style=\"padding:10px\">Poznámka</th></tr>";
$i        = 0;
$query193 = "SELECT id, silnice, kilometr, smer, smoketest, typ FROM hlasky WHERE silnice = '$old_silnice' AND id IN ($hlasky_list) ORDER BY CAST(kilometr AS unsigned), smer";
if ($result193 = mysqli_query($link, $query193)) {
    while ($row193 = mysqli_fetch_row($result193)) {
        $hl_id       = $row193[0];
        $hl_silnice  = $row193[1];
        $hl_kilometr = $row193[2];
        $hl_smer     = $row193[3];
        $hl_smoke    = $row193[4];
        $hl_typ      = $row193[5];

        $smer_nazev = SmerNazev($hl_silnice, $hl_smer, $hl_kilometr);

        $query174 = "SELECT zkouska, hovorOUT, hovorIN, lokaceSPEL, lokace112, poznamka, `status` FROM test_result WHERE id_test = '$test_id' AND id_hlaska = '$hl_id';";
        if ($result174 = mysqli_query($link, $query174)) {
            while ($row174 = mysqli_fetch_row($result174)) {
                $zkouska    = $row174[0];
                $hovor_out  = $row174[1];
                $hovor_in   = $row174[2];
                $lokaceSPEL = $row174[3];
                $lokace112  = $row174[4];
                $poznamka   = $row174[5];
                $status     = $row174[6];
            }
        }
        echo "<tr class=\"";
        if ($i % 2 == 0) {
            echo "dark";
        } else {
            echo "light";
        }
        if ($hl_smoke == 0) {
            echo "-smoke";
        }
        echo "\"><td>";

        if ($status == "0") {
            echo "Stav OK";
        } else {
            echo "Chyba";
        }

// combobox - H _id_ R

        echo "</td><td style=\"text-align:center;\">";

        $query146 = "SELECT popis FROM enum_typ WHERE id = '$hl_typ';";
        if ($result146 = mysqli_query($link, $query146)) {
            while ($row146 = mysqli_fetch_row($result146)) {
                $nazev_typu = $row146[0];
            }
        }

        echo $nazev_typu;

        echo "</td>";
        echo "<td style=\"text-align:center;\">$hl_kilometr</td>";
        echo "<td>$smer_nazev</td>";
        echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"H";
        echo $hl_id;
        echo "Z\" value=\"1\"";
        if ($zkouska == "1") {
            echo " CHECKED";
        }
        echo "></td>";

        echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"H";
        echo $hl_id;
        echo "O\" value=\"1\"";
        if ($hovor_out == "1") {
            echo " CHECKED";
        }
        echo "></td>";

        echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"H";
        echo $hl_id;
        echo "I\" value=\"1\"";
        if ($hovor_in == "1") {
            echo " CHECKED";
        }
        echo "></td>";

        echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"H";
        echo $hl_id;
        echo "LS\" value=\"1\"";
        if ($lokaceSPEL == "1") {
            echo " CHECKED";
        }
        echo "></td>";

        echo "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"H";
        echo $hl_id;
        echo "L1\" value=\"1\"";
        if ($lokace112 == "1") {
            echo " CHECKED";
        }
        echo "></td>";

        echo "<td><input type=\"text\" name=\"H";
        echo $hl_id;
        echo "P\" value=\"$poznamka\"></td>";
        echo "</tr>";
        $z = $z + 1;
        $i = $i + 1;
    }
}
?>
<tr><td colspan="2"><input type="hidden" name="pocet" value="<?php echo $z - 1; ?>"></td></tr>
<tr><td><input type="submit" value="Uložit změny"></form></td></tr>
</table>

<p>&nbsp;</p>
<?php
echo "$datum_err <br/> $komentar_err";
echo "<p>&nbsp;</p>";
echo "<a href=\"protokol.php?id=$test_id\" target=\"_blank\">Tisk prokotolu z testování</a>";
echo "<p>&nbsp;</p>";
//echo "<a href=\"archiv.php?id=$test_id\">Archivace testování</a>";
