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
    <title>Editace testu hlásek</title>
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
include 'Converter.php';
$converter = new JTSK\Converter();

$id = @$_GET["id"];
if ($id == "") {
    $id = @$_POST["id"];
}

$query69 = "SELECT datum, silnice, osoba FROM testovani WHERE id = $id;";
if ($result69 = mysqli_query($link, $query69)) {
    while ($row69 = mysqli_fetch_row($result69)) {
        $old_datum = $row69[0];
        $old_silnice = $row69[1];
        $old_osoba = $row69[2];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = @$_POST["id"];
    $action = @$_POST["action"];

    switch ($action) {
        case "hlavicka":
            $datum = @$_POST["datum"];
            $datum_err = "";
            $silnice = @$_POST["silnice"];
            $silnice_err = "";
            $osoba = @$_POST["osoba"];
            $osoba_err = "";

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
                $query104 = "UPDATE testovani SET datum = '$datum', silnice = '$silnice', osoba= '$osoba' WHERE id = $id;";
                $prikaz104 = mysqli_query($link, $query104);

                if ($silnice != $old_silnice) {
                    $query108 = "UPDATE testovani SET hlasky = '' WHERE id = $id;";
                    $prikaz108 = mysqli_query($link, $query108);
                }
            }
            break;

        case "hlasky":
            $seznam_hlasek = [];
            $pocet = $_POST['pocet'];
            for ($y = 0; $y < $pocet; $y++) {
                $arrindex = "line{$y}";
                $hlaska = $_POST[$arrindex];
                $seznam_hlasek[] = $hlaska;
            }

            $seznam_hlasek = array_filter($seznam_hlasek);

            $hlasky = implode("|", $seznam_hlasek);
            $query126 = "UPDATE testovani SET hlasky = '$hlasky' WHERE id = $id;";
            $prikaz126 = mysqli_query($link, $query126);
            Redir("testovani.php");
            break;
    }

}

$query134 = "SELECT datum, silnice, osoba, hlasky FROM testovani WHERE id = $id;";
if ($result134 = mysqli_query($link, $query134)) {
    while ($row134 = mysqli_fetch_row($result134)) {
        $old_datum = $row134[0];
        $old_silnice = $row134[1];
        $old_osoba = $row134[2];
        $old_hlasky = $row134[3];
    }
}
PageHeader();
$today = date("Y-m-d", strtotime("+ 1 day"));
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="action" value="hlavicka">
    <table width="100%" style="text-align:center;">
        <tr>
            <th>&nbsp;</th>
            <th>Datum</th>
            <th>Silnice</th>
            <th>Koordinátor</th>
            <th></th>
        </tr>
        <tr>
            <td></td>
            <td><input type="date" name="datum" min="<?php echo $today; ?>" class="form-control"
                    value="<?php echo $old_datum; ?>"></td>
            <td><select class="form-control" id="silnice" name="silnice">
                    <option value="">---</option>
                    <?php
                    $query165 = "SELECT id, nazev FROM enum_silnice ORDER BY nazev;";
                    if ($result165 = mysqli_query($link, $query165)) {
                        while ($row165 = mysqli_fetch_row($result165)) {
                            $sil_id = $row165[0];
                            $sil_name = $row165[1];

                            echo "<option value=\"$sil_id\"";
                            if ($sil_id == $old_silnice) {
                                echo " SELECTED";
                            }
                            echo ">$sil_name</option>\n";
                        }
                    }
                    ?>
                </select></td>
            <td><select class="form-control" id="osoba" name="osoba">
                    <option value="">---</option>
                    <?php
                    $query183 = "SELECT id, jmeno, tel_cislo FROM test_osoby ORDER BY jmeno;";
                    if ($result183 = mysqli_query($link, $query183)) {
                        while ($row183 = mysqli_fetch_row($result183)) {
                            $os_id = $row183[0];
                            $os_jmeno = $row183[1];
                            $os_cislo = $row183[2];

                            echo "<option value=\"$os_id\"";
                            if ($os_id == $old_osoba) {
                                echo " SELECTED";
                            }
                            echo ">$os_jmeno | $os_cislo</option>\n";
                        }
                    }
                    ?>
                </select></td>
            <td><input type="submit" value="Uložit změny v záhlaví">
</form>
</td>
</tr>
</table>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="action" value="hlasky">
    <table style="text-align:left;">
        <tr>
            <td colspan="2"><input type="submit" value="Uložit seznam hlásek">
</form>
</td>
</tr>

<?php
$z = 0;
$hlasky_array = explode("|", $old_hlasky);

$strediska = [];

$query221 = "SELECT ssud FROM hlasky WHERE silnice = '$old_silnice' AND archiv = '0' ORDER BY CAST(kilometr AS decimal), smer;";
if ($result221 = mysqli_query($link, $query221)) {
    while ($row221 = mysqli_fetch_row($result221)) {
        $strediska[] = $row221[0];
    }
}
if ($strediska) {
    $strediska = array_filter($strediska);
    $strediska = array_unique($strediska);

    echo "<tr>";
    foreach ($strediska as $stredisko) {
        $ssud_nazev = "";
        echo "<td style=\"padding:10px\"><table>";
        $query235 = "SELECT popis FROM enum_ssud WHERE id = '$stredisko';";
        if ($result235 = mysqli_query($link, $query235)) {
            while ($row235 = mysqli_fetch_row($result235)) {
                $ssud_nazev = $row235[0];
            }
        }
        echo "<tr><th colspan=\"2\">$ssud_nazev</th></tr>";
        $i = 0;
        $query243 = "SELECT id, tel_cislo, kilometr, smer, smoketest FROM hlasky WHERE silnice = '$old_silnice' AND ssud = '$stredisko'  AND archiv = '0' ORDER BY CAST(kilometr AS unsigned), smer";
        if ($result243 = mysqli_query($link, $query243)) {
            while ($row243 = mysqli_fetch_row($result243)) {
                $hl_id = $row243[0];
                $hl_telcislo = $row243[1];
                $hl_kilometr = $row243[2];
                $hl_smer = $row243[3];
                $hl_smoke = $row243[4];

                echo "<tr class=\"";
                echo ($i % 2 == 0) ? "dark" : "light";
                if ($hl_smoke == 0) {
                    echo "-smoke";
                }
                echo "\"><td><input type=\"checkbox\" name=\"line$z\" value=\"$hl_id\"";
                if (in_array($hl_id, $hlasky_array)) {
                    echo " CHECKED";
                }
                echo "></td>";
                echo "<td>$hl_telcislo | km $hl_kilometr směr $hl_smer</td></tr>\n";
                $z++;
                $i++;
            }
        }
        echo "</table></td>";
    }
    echo "</tr>";
}
?>
<tr>
    <td colspan="2"><input type="hidden" name="pocet" value="<?php echo $z; ?>"></form>
    </td>
</tr>
</table>