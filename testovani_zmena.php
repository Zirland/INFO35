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
	<title>Editace testu hlásek</title>
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

$query16 = "SELECT datum, osoba, silnice, hlasky, zadatel FROM testovani WHERE id = $id;";
if ($result16 = mysqli_query($link, $query16)) {
    while ($row16 = mysqli_fetch_row($result16)) {
        $old_datum   = $row16[0];
        $old_osoba   = $row16[1];
        $old_silnice = $row16[2];
        $old_hlasky  = $row16[3];
        $zadatel     = $row16[4];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = @$_POST["id"];

    $datum        = @$_POST["datum"];
    $datum_err    = "";
    $osoba        = @$_POST["osoba"];
    $odvolat      = @$_POST["odvolat"];
    $schvalit     = @$_POST["schvalit"];
    $komentar     = @$_POST["komentar"];
    $komentar_err = "";

    if (empty(trim($datum))) {
        $datum_err = "Zadejte prosím datum.";
    }

    if ($odvolat == "1" && empty(trim($komentar))) {
        $komentar_err = "Je nutno vyplnit komentář";
    }

    if (empty($datum_err) && empty($osoba_err) && empty($komentar_err)) {
        $query72 = "SELECT email FROM users WHERE id = '$zadatel';";
        if ($result72 = mysqli_query($link, $query72)) {
            while ($row72 = mysqli_fetch_row($result72)) {
                $koordinator = $row72[0];
            }
        }

        if ($odvolat == "1") {
            $query88  = "UPDATE testovani SET odmitnuto = '1' WHERE id = $id;";
            $prikaz88 = mysqli_query($link, $query88);

            $datumformat = date("d.m.Y", strtotime($datum));
            $to          = 'Testování hlásek <hlasky@zirland.org>';
            $subject     = 'Zrušení termínu testu';
            $message     = '
<html>
<head>
<title>Zrušení termínu testu</title>
</head>
<body>
<p>Plánovaný termín testu byl zrušen:</p>
<p><b>Datum: </b>' . $datumformat . '<br/>
<b>Silnice: </b>' . $old_silnice . '<br/>
<b>Komentář: </b>' . $komentar . '</p>

</body>
</html>
';
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=utf-8';
            $headers[] = 'From: Testování hlásek <hlasky@zirland.org>';
            $headers[] = 'Bcc: zirland@gmail.com';
            $headers[] = 'To: ' . $koordinator;
            mail($to, $subject, $message, implode("\r\n", $headers));
        }

        if ($schvalit == "1") {
            $query93  = "UPDATE testovani SET schvaleno = '1' WHERE id = $id;";
            $prikaz93 = mysqli_query($link, $query93);

            $datumformat = date("d.m.Y", strtotime($datum));
            $to          = 'Testování hlásek <hlasky@zirland.org>';
            $subject     = 'Schválení termínu testu';
            $message     = '
<html>
<head>
<title>Schválení termínu testu</title>
</head>
<body>
<p>Plánovaný termín testu byl schválen:</p>
<p><b>Datum: </b>' . $datumformat . '<br/>
<b>Silnice: </b>' . $old_silnice . '</p>

</body>
</html>
';
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=utf-8';
            $headers[] = 'From: Testování hlásek <hlasky@zirland.org>';
            $headers[] = 'Bcc: zirland@gmail.com';
            $headers[] = 'To: ' . $koordinator;
            mail($to, $subject, $message, implode("\r\n", $headers));

            $hlasky_array = explode("|", $old_hlasky);
            foreach ($hlasky_array as $id_hlaska) {
                $query147 = "INSERT INTO test_result (id_test, id_hlaska) VALUES ('$id','$id_hlaska');";
                $prikaz147 = mysqli_query($link, $query147);
            }
        }

        $query79  = "UPDATE testovani SET datum = '$datum', osoba = '$osoba', komentar = '$komentar' WHERE id = $id;";
        $prikaz79 = mysqli_query($link, $query79);

        if ($old_datum != $datum) {
            $olddatumformat = date("d.m.Y", strtotime($old_datum));
            $datumformat    = date("d.m.Y", strtotime($datum));

            $to      = 'Testování hlásek <hlasky@zirland.org>';
            $subject = 'Změna data testu';
            $message = '
<html>
<head>
<title>Změna data testu</title>
</head>
<body>
<p>Plánovaný termín testu byl změněn:</p>
<p><b>Původní datum: </b>' . $olddatumformat . '<br/>
<b>Nové datum: </b>' . $datumformat . '<br/>
<b>Silnice: </b>' . $old_silnice . '</p>

</body>
</html>
';
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=utf-8';
            $headers[] = 'From: Testování hlásek <hlasky@zirland.org>';
            $headers[] = 'Bcc: zirland@gmail.com';
            $headers[] = 'To: ' . $koordinator;
            mail($to, $subject, $message, implode("\r\n", $headers));
        }

        Redir("testovani.php");
    }
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
$today = date("Y-m-d", strtotime("+ 1 day"));
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<table width="100%" style="text-align:center;">
<tr><th>Datum</th><th>Silnice</th><th>Koordinátor</th><th>&nbsp;</th></tr>
<tr>
<td><input type="date" name="datum" min="<?php echo $today; ?>" class="form-control" value="<?php echo $old_datum; ?>"></td>
<td><select class="form-control" id="silnice" name="silnice" disabled>
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
echo "<tr colspan=\"5\">";
echo "<td style=\"background-color:$bg_col;\">$stav_schvaleni</td>";
echo "<td>";
if ($id_user == "1" && $old_schvaleno == 0) {
    echo "<input type=\"checkbox\" name=\"schvalit\" value=\"1\"> Schválit termín testu<br/>";
}
if ($old_odmitnuto == 0) {
    echo "<input type=\"checkbox\" name=\"odvolat\" value=\"1\"> Zrušit (odvolat) termín testu</td>";
}
echo "<td colspan=\"2\">Komentář: <input type=\"text\" size=\"100\" name=\"komentar\" value=\"$old_komentar\"></td>";
echo "</tr>";

$z            = 0;
$hlasky_array = explode("|", $old_hlasky);
$hlasky_list  = implode(",", $hlasky_array);
unset($strediska);

$query179 = "SELECT DISTINCT ssud FROM hlasky WHERE silnice = '$old_silnice' AND id IN ($hlasky_list) ORDER BY CAST(kilometr AS unsigned), smer;";
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