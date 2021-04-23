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
    </style>
</head>

<?php
require_once 'config.php';

$id_user = $_SESSION["id"];

$id = @$_GET["id"];
if ($id == "") {
    $id = @$_POST["id"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = @$_POST["id"];

}

$query16 = "SELECT datum, silnice, osoba, hlasky, schvaleno, odmitnuto, komentar FROM testovani WHERE id = $id;";
if ($result16 = mysqli_query($link, $query16)) {
    while ($row16 = mysqli_fetch_row($result16)) {
        $old_datum     = $row16[0];
        $old_silnice   = $row16[1];
        $old_osoba     = $row16[2];
        $old_hlasky    = $row16[3];
        $old_schvaleno = $row16[4];
        $old_odmitnuto = $row16[5];
        $old_komentar  = $row16[6];

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
</table><table>
<?php
$z            = 0;
$hlasky_array = explode("|", $old_hlasky);
$hlasky_list  = implode(",", $hlasky_array);
unset($strediska);

$query179 = "SELECT DISTINCT ssud FROM hlasky WHERE silnice = '$old_silnice' AND id IN ($hlasky_list)ORDER BY CAST(kilometr AS unsigned), smer;";
if ($result179 = mysqli_query($link, $query179)) {
    while ($row179 = mysqli_fetch_row($result179)) {
        $strediska[] = $row179[0];
    }
}
if ($strediska) {
    $strediska = array_filter($strediska);

    echo "<tr>";
    foreach ($strediska as $stredisko) {
        $ssud_nazev = "";
        echo "<td style=\"padding:10px\"><table>";
        $query237 = "SELECT popis FROM enum_ssud WHERE id = '$stredisko';";
        if ($result237 = mysqli_query($link, $query237)) {
            while ($row237 = mysqli_fetch_row($result237)) {
                $ssud_nazev = $row237[0];
            }
        }
        echo "<tr><th colspan=\"2\">$ssud_nazev</th></tr>";
        $i        = 0;
        $query193 = "SELECT id, tel_cislo, kilometr, smer, smoketest FROM hlasky WHERE silnice = '$old_silnice' AND ssud = '$stredisko' AND id IN ($hlasky_list) ORDER BY CAST(kilometr AS unsigned), smer";
        if ($result193 = mysqli_query($link, $query193)) {
            while ($row193 = mysqli_fetch_row($result193)) {
                $hl_id       = $row193[0];
                $hl_telcislo = $row193[1];
                $hl_kilometr = $row193[2];
                $hl_smer     = $row193[3];
                $hl_smoke    = $row193[4];

                echo "<tr class=\"";
                if ($i % 2 == 0) {
                    echo "dark";
                } else {
                    echo "light";
                }
                if ($hl_smoke == 0) {
                    echo "-smoke";
                }
                echo "\"><td></td>";
                echo "<td>$hl_telcislo | km $hl_kilometr směr $hl_smer</td></tr>\n";
                $z = $z + 1;
                $i = $i + 1;
            }
        }
        echo "</table></td>";
    }
    echo "</tr>";
}
?>
<tr><td colspan="2"><input type="hidden" name="pocet" value="<?php echo $z - 1; ?>"></form></td></tr>
<tr><td><input type="submit" value="Uložit změny"></form></td></tr>
</table>

<?php echo "$datum_err <br/> $komentar_err"; ?>