<?php
date_default_timezone_set('Europe/Prague');
session_start();

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
    <title>Protokol z funkční zkoušky</title>
    <style type="text/css">
        table.page {
            width:18cm;
            margin-left: auto;
            margin-right: auto;
            font-family:arial;
        }
        table.inline {
            border-collapse: collapse;
        }

        td.inline {
            border: 1px solid black;
            font-size:13px;
            padding: 5px;
        }
        
        th.inline {
            border: 1px solid black;
            font-size:13px;
            vertical-align:middle;
            text-align:center;
        }

        .arial22 {
            font-size:22px;
            text-align:center;
        }
        .arial16 {
            font-size:16px;
            text-align:center;
        }
        .inline.rotated {
            writing-mode: tb-rl;
            transform: rotate(-180deg);
            justify-content:center;
            align-items:center;
        }
    </style>
</head>
<body>

<?php
$id = $_GET["id"];

$query47 = "SELECT datum, silnice, hlasky, projekt FROM testovani WHERE id = $id;";
if ($result47 = mysqli_query($link, $query47)) {
    while ($row47 = mysqli_fetch_row($result47)) {
        $datum   = $row47[0];
        $silnice = $row47[1];
        $hlasky  = $row47[2];
        $projekt = $row47[3];

        $datum_format = date("d.m.Y", strtotime($datum));

        $hlasky_array = explode("|", $hlasky);
        $hlasky_list  = implode(",", $hlasky_array);

        $query60 = "SELECT min(kilometr), max(kilometr) FROM hlasky WHERE silnice = '$silnice' AND id IN ($hlasky_list);";
        if ($result60 = mysqli_query($link, $query60)) {
            while ($row60 = mysqli_fetch_row($result60)) {
                $km_min = $row60[0];
                $km_max = $row60[1];

                $km_min = str_replace(".", ",", $km_min);
                $km_max = str_replace(".", ",", $km_max);
            }
        }
    }
}

?>
<table class="page">
<tr>
<td>
<img src="SPEL.png" style="width:18cm">
<div class="arial22">&nbsp;<br/>Protokol z funkční zkoušky<br/>&nbsp;</div>
<div class="arial16">Telefonické spojení SOS hlásek s linkou 112</div>
<div class="arial22">&nbsp;<br/>&nbsp;</div>

<table>
<tr><td style="width:15mm">&nbsp;</td>
<td style="width:2cm">Projekt:</td>
<td><?php echo $projekt; ?> ŘSD,_Přepojení_hlásek_na_dispečinky_HZS</td>
</tr>

<tr><td>&nbsp;</td>
<td>Investor:</td>
<td>Ředitelství silnic a dálnic ČR</td>
</tr>

<tr><td>&nbsp;</td>
<td>Dodavatel:</td>
<td>SPEL, a.s. Kolín<br/>Třídvorská 1402, 280 02 Kolín V</td>
</tr>

</table>
<div class="arial22">&nbsp;<br/>&nbsp;</div>

<table class="inline">
<tr><td style="width:15mm">&nbsp;</td>
<td>SPEL, a.s. provedl <?php echo $datum_format; ?> funkční zkoušku spojení.<br/>
Hlásky byly testovány na úsecích dálnice <?php echo $silnice; ?> (km <?php echo "$km_min – $km_max"; ?>)
</td>
</tr>
</table>
<div class="arial22">&nbsp;</div>
<?php
unset($radky);

$query110 = "SELECT typ, kilometr, smer, zkouska, hovorOUT, hovorIN, lokace, poznamka, `status` FROM hlasky JOIN test_result ON hlasky.id = test_result.id_hlaska WHERE silnice = '$silnice' AND id_test = '$id' ORDER BY kilometr, smer DESC;";
if ($result110 = mysqli_query($link, $query110)) {
    while ($row110 = mysqli_fetch_row($result110)) {
        $typ       = $row110[0];
        $kilometr  = $row110[1];
        $smer      = $row110[2];
        $zkouska   = $row110[3];
        $hovor_out = $row110[4];
        $hovor_in  = $row110[5];
        $lokace    = $row110[6];
        $poznamka  = $row110[7];
        $status    = $row110[8];

        $smer_nazev = SmerNazev($silnice, $smer, $kilometr);

        $zarizeni .= "<tr><td>";
        $zarizeni .= "</td><td class=\"inline\" style=\"text-align:center;\">";
        $zarizeni .= $typ;
        $zarizeni .= "</td>";
        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">$kilometr</td>";
        $zarizeni .= "<td class=\"inline\">$smer_nazev</td>";
        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
        if ($zkouska == "1") {
            $zarizeni .= "&#9745;";
        } else {
            $zarizeni .= "&#9744;";
        }
        $zarizeni .= "</td>";

        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
        if ($hovor_out == "1") {
            $zarizeni .= "&#9745;";
        } else {
            $zarizeni .= "&#9744;";
        }
        $zarizeni .= "</td>";

        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
        if ($hovor_in == "1") {
            $zarizeni .= "&#9745;";
        } else {
            $zarizeni .= "&#9744;";
        }
        $zarizeni .= "</td>";

        $zarizeni .= "<td class=\"inline\" style=\"text-align:center;\">";
        if ($lokace == "1") {
            $zarizeni .= "&#9745;";
        } else {
            $zarizeni .= "&#9744;";
        }
        $zarizeni .= "</td>";

        $zarizeni .= "<td class=\"inline\">$poznamka</td>";
        $zarizeni .= "<td></td>";
        $zarizeni .= "</tr>";
    }
}
?>
<table class="inline">
<tr><th style="width:15mm;">&nbsp;</th>
<th class="inline">Zařízení</th><th class="inline">Typ</th><th class="inline">Počet</th><th class="inline">Stav</th></tr>
<?php
$query180 = "SELECT smer, typ, count(*) FROM hlasky JOIN test_result ON hlasky.id = test_result.id_hlaska WHERE silnice = '$silnice' AND id_test = '$id' GROUP BY smer, typ ORDER BY smer DESC;";
if ($result180 = mysqli_query($link, $query180)) {
    while ($row180 = mysqli_fetch_row($result180)) {
        $smer_hlasky  = $row180[0];
        $typ_hlasky   = $row180[1];
        $pocet_hlasek = $row180[2];

        if ($smer_hlasky == "+") {
            $hlavni = "Hláska hlavní";
        } else {
            $hlavni = "Hláska hlavní";
        }

        $stav     = 0;
        $query187 = "SELECT typ, smer, SUM(`status`) FROM hlasky JOIN test_result ON hlasky.id = test_result.id_hlaska WHERE silnice = '$silnice' AND id_test = '$id' GROUP BY typ, smer ORDER BY typ, smer DESC;";
        if ($result187 = mysqli_query($link, $query187)) {
            while ($row187 = mysqli_fetch_row($result187)) {
                $typ_kontrola    = $row187[0];
                $smer_kontrola   = $row187[1];
                $status_kontrola = $row187[2];

                if ($typ_hlasky == $typ_kontrola && $smer_kontrola == $smer_hlasky) {
                    $stav = $stav + $status_kontrola;
                }
            }
        }

        echo "<tr><td></td><td class=\"inline\">$hlavni</td><td class=\"inline\">$typ_hlasky</td><td class=\"inline\">$pocet_hlasek</td><td class=\"inline\">";
        if ($stav > 0) {
            echo "Chyba";
        } else {
            echo "OK";
        }
        echo "</td></tr>";
    }
}
?>
</table>
<p></p>
<table class="inline">
<tr><th style="width:15mm;">&nbsp;</th>
<th class="inline">Typ</th><th class="inline rotated">Označení<br/>&nbsp;</th><th class="inline">Směr</th><th class="inline rotated">Zkouška</th><th class="inline rotated">SOS–IZS</th><th class="inline rotated">IZS–SOS</th><th class="inline rotated">Lokalizace</th><th class="inline">Poznámka</th><thstyle="width:15mm;">&nbsp;</th></tr>
<?php
echo $zarizeni;
?>
</tr>
</table>

<?php
print_r($radky);
echo "<br/>";
print_r($typy);
echo "<br/>";
print_r($smery);
echo "<br/>";
print_r($uspech);
echo "<br/>";
?>

</td>
</tr>
</table>
</body>
</html>

<?php
mysqli_close($link);
?>