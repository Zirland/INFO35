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
        body{ font: 14px sans-serif; }
    </style>
</head>

<?php
require_once 'config.php';
include 'Converter.php';
$converter = new JTSK\Converter();

$id = @$_GET["id"];
if ($id == "") {
    $id = @$_POST["id"];
}

$query16 = "SELECT datum, silnice, osoba FROM testovani WHERE id = $id;";
if ($result16 = mysqli_query($link, $query16)) {
    while ($row16 = mysqli_fetch_row($result16)) {
        $old_datum   = $row16[0];
        $old_silnice = $row16[1];
        $old_osoba   = $row16[2];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id     = @$_POST["id"];
    $action = @$_POST["action"];

    switch ($action) {
        case "hlavicka":
            $datum       = @$_POST["datum"];
            $datum_err   = "";
            $silnice     = @$_POST["silnice"];
            $silnice_err = "";
            $osoba       = @$_POST["osoba"];
            $osoba_err   = "";

            if (empty(trim($datum))) {
                $datum_err = "Zadejte prosím datum.";
            }

            if (empty(trim($silnice))) {
                $silnice_err = "Vyberte prosím silnici.";
            }

            if (empty(trim($osoba))) {
                $osoba_err = "Vyberte prosím odpovědnou osobu.";
            }

            if (empty($datum_err) && empty($silnice_err) && empty($osoba_err)) {
                $query79  = "UPDATE testovani SET datum = '$datum', silnice = '$silnice', osoba= '$osoba' WHERE id = $id;";
                $prikaz79 = mysqli_query($link, $query79);

                if ($silnice != $old_silnice) {
                    $query79  = "UPDATE testovani SET hlasky = '' WHERE id = $id;";
                    $prikaz79 = mysqli_query($link, $query79);
                }
            }
            break;

        case "hlasky":
            unset($seznam_hlasek);
            $pocet = $_POST['pocet'];
            for ($y = 0; $y < $pocet; $y++) {
                $$ind            = $y;
                $arrindex        = "line" . ${$ind};
                $hlaska          = $_POST[$arrindex];
                $seznam_hlasek[] = $hlaska;
            }

            $seznam_hlasek = array_filter($seznam_hlasek);

            $hlasky   = implode("|", $seznam_hlasek);
            $query93  = "UPDATE testovani SET hlasky = '$hlasky' WHERE id = $id;";
            $prikaz93 = mysqli_query($link, $query93);
            Redir("testovani.php");
            break;
    }

}

$query16 = "SELECT datum, silnice, osoba, hlasky FROM testovani WHERE id = $id;";
if ($result16 = mysqli_query($link, $query16)) {
    while ($row16 = mysqli_fetch_row($result16)) {
        $old_datum   = $row16[0];
        $old_silnice = $row16[1];
        $old_osoba   = $row16[2];
        $old_hlasky  = $row16[3];
    }
}
PageHeader();
$today = date("Y-m-d", strtotime("+ 1 day"));
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="action" value="hlavicka">
<table width="100%" style="text-align:center;">
<tr><th>&nbsp;</th><th>Datum</th><th>Silnice</th><th>Koordinátor</th><th></th></tr>
<tr><td></td>
<td><input type="date" name="datum" min="<?php echo $today; ?>" class="form-control" value="<?php echo $old_datum; ?>"></td>
<td><select class="form-control" id="silnice" name="silnice">
    <option value="">---</option>
<?php
$sql = "SELECT id,nazev FROM enum_silnice ORDER BY nazev";

if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $sil_id, $sil_name);

        while (mysqli_stmt_fetch($stmt)) {
            echo "<option value=\"$sil_id\"";
            if ($sil_id == $old_silnice) {
                echo " SELECTED";
            }
            echo ">$sil_name</option>\n";
        }
    }
}
mysqli_stmt_close($stmt);
?>
</select></td>
<td><select class="form-control" id="osoba" name="osoba">
    <option value="">---</option>
<?php
$sql = "SELECT id, jmeno,tel_cislo FROM test_osoby ORDER BY jmeno";

if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $os_id, $os_jmeno, $os_cislo);

        while (mysqli_stmt_fetch($stmt)) {
            echo "<option value=\"$os_id\"";
            if ($os_id == $old_osoba) {
                echo " SELECTED";
            }
            echo ">$os_jmeno | $os_cislo</option>\n";
        }
    }
}
mysqli_stmt_close($stmt);
?>
</select></td>
<td><input type="submit" value="Uložit změny v záhlaví"></form></td></tr></table>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="action" value="hlasky">
<table width="20%" style="text-align:left;">
<tr><td colspan="2"><input type="submit" value="Uložit seznam hlásek"></form></td></tr>

<?php
$z   = 0;
$hlasky_array = explode("|", $old_hlasky);

$sql = "SELECT id, tel_cislo, kilometr, smer FROM hlasky WHERE silnice = '$old_silnice' ORDER BY CAST(kilometr AS unsigned), smer";
if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $hl_id, $hl_telcislo, $hl_kilometr, $hl_smer);

        while (mysqli_stmt_fetch($stmt)) {
            echo "<tr><td><input type=\"checkbox\" name=\"line$z\" value=\"$hl_id\"";
            if (in_array($hl_id,$hlasky_array)) {
                echo " CHECKED";
            }
            echo "></td>";
            echo "<td>$hl_telcislo | km $hl_kilometr směr $hl_smer</td></tr>\n";
            $z = $z + 1;
        }
    }
}
mysqli_stmt_close($stmt);
?>
<tr><td colspan="2"><input type="hidden" name="pocet" value="<?php echo $z - 1; ?>"></form></td></tr>
</table>