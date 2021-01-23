<?php
date_default_timezone_set('Europe/Prague');
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'config.php';
include 'Converter.php';
$converter = new JTSK\Converter();

$action                = @$_POST["action"];
$prijmeni              = @$_POST["prijmeni"];
$jmeno                 = @$_POST["jmeno"];
$tel_cislo             = @$_POST["tel_cislo"];
$ico                   = @$_POST["ico"];
$uliceNazev            = @$_POST["uliceNazev"];
$adresaCisloDomovni    = @$_POST["adresaCisloDomovni"];
$adresaCisloOrientacni = @$_POST["adresaCisloOrientacni"];

$obecNazev     = @$_POST["obecNazev"];
$castObceNazev = @$_POST["castObceNazev"];
$okresNazev    = @$_POST["okresNazev"];
$longitude     = @$_POST["longitude"];
$latitude      = @$_POST["latitude"];
$objektKod     = @$_POST["objektKod"];
$adresaKod     = @$_POST["adresaKod"];
$obecKod       = @$_POST["obecKod"];
$castObceKod   = @$_POST["castObceKod"];
$uliceKod      = @$_POST["uliceKod"];
$OpID          = @$_POST["OpID"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($tel_cislo))) {
        $tel_cislo_err = "Zadejte prosím telefonní číslo.";
    } else {
        $query39 = "SELECT tel_cislo FROM hlasky WHERE tel_cislo = '$tel_cislo';";
        if ($result39 = mysqli_query($link, $query39)) {
            $count_dupes = mysqli_num_rows($result39);
            if ($count_dupes == 1) {
                $tel_cislo_err = "Telefonní číslo je již použito.";
            } else {
                $tel_cislo = trim($tel_cislo);
            }
        } else {
            echo "Něco se nepovedlo. Zkuste to prosím znovu.";
        }
    }

    $query106 = "INSERT INTO stanice (`prijmeni`,`jmeno`,`tel_cislo`,`ico`,`nazev_ulice`,`cislo_popisne`,`cislo_orientacni`,`cislo_podlazi`,`cislo_bytu`,`nazev_obce`,`nazev_casti_obce`,`nazev_okresu`,`longitude`,`latitude`,`kod_objektu`,`kod_adresy`,`kod_obce`,`kod_casti_obce`,`kod_ulice`,`OpID`) VALUES ('$prijmeni','$jmeno','$tel_cislo','$ico','$uliceNazev','$adresaCisloDomovni','$adresaCisloOrientacni','','','$obecNazev','$castObceNazev','$okresNazev','$longitude','$latitude','$objektKod','$adresaKod','$obecKod','$castObceKod','$uliceKod','$OpID');";
//    $prikaz106 = mysqli_query($link, $query106);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>Záznam INFO35</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{font: 11px sans-serif; }
        .wrapper{ width: 500px; padding: 20px; }
        tr.strikeout td:before {
            content: " ";
            position: absolute;
            display: inline-block;
            padding: 4px 10px;
            left: 0;
            border-bottom: 1px solid #111;
            width: 100%;
        }
    </style>

    <script type="text/javascript">
    function najdi(str) {
        var xmlhttp;

        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        } else {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function() {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("data").innerHTML = xmlhttp.responseText;
            }
        }

        xmlhttp.open("GET","helper.php?opt="+str, true);
        xmlhttp.send();
    }

    function vyber(str) {
        var xmlhttp;

        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        } else {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("mistoUdal").innerHTML = xmlhttp.responseText;
            }
        }

        xmlhttp.open("GET","helper2.php?kandi="+str, true);
        xmlhttp.send();
    }
    </script>
</head>


<body>
<?php
PageHeader();
?>
<table><tr><td>
<form method="post" action="index2.php" name="generuj">
<input name="action" value="generuj" type="hidden">

Telefonní číslo: <input type="text" name="tel_cislo" value="" autofocus><br/>
Příjmení/Název: *<input type="text" name="prijmeni" value="">	Jméno: <input name="jmeno" value=""><br/>
IČO: <input name="ico" value="00007064">	OpID: <input name="OpID" size="3" value="555"><br/>

Adresa: <input onChange="najdi(this.value)">
	<select id="data" onChange="vyber(this.value)" multiple>
		<option>Select an Option...</option>
	</select>
	<br/>
	<div id="mistoUdal">
	</div>
	<br/>


<input type="submit">
</form>
</td><td><a href="mapa.php" target="_blank">Mapa</a></td></tr></table>
<hr>
<?php
echo "<table>";
echo "<tr><th>Příjmení</th><th>Jméno</th><th>Telefonní číslo</th><th>IČO</th><th>Název ulice</th><th>Číslo domovní</th><th>Číslo orientační</th><th>Číslo podlaží</th><th>Číslo bytu</th><th>Název obce</th><th>Název části obce</th><th>Název okresu</th><th>Zeměpisná délka</th><th>Zeměpisná šířka</th><th>Kód objektu</th><th>Kód adresy</th><th>Kód obce</th><th>Kód části obce</th><th>Kód ulice</th><th>OpID</th></tr>";
$i        = 0;
$query110 = "SELECT * FROM stanice ORDER BY tel_cislo;";
if ($result110 = mysqli_query($link, $query110)) {
    while ($row110 = mysqli_fetch_row($result110)) {
        $prijmeni              = $row110[0];
        $jmeno                 = $row110[1];
        $tel_cislo             = $row110[2];
        $ico                   = $row110[3];
        $uliceNazev            = $row110[4];
        $adresaCisloDomovni    = $row110[5];
        $adresaCisloOrientacni = $row110[6];

        $obecNazev     = $row110[9];
        $castObceNazev = $row110[10];
        $okresNazev    = $row110[11];
        $longitude     = $row110[12];
        $latitude      = $row110[13];
        $objektKod     = $row110[14];
        $adresaKod     = $row110[15];
        $obecKod       = $row110[16];
        $castObceKod   = $row110[17];
        $uliceKod      = $row110[18];
        $OpID          = $row110[19];

        echo "<tr";
        if ($i % 2 == 0) {
            echo " bgcolor=\"#ddd\"";
        }
        echo "><td><a href=\"stanice_edit.php?cislo=$tel_cislo\">$prijmeni</a></td><td>$jmeno</td><td>$tel_cislo</td><td>$ico</td><td>$uliceNazev</td><td>$adresaCisloDomovni</td><td>$adresaCisloOrientacni</td><td></td><td></td><td>$obecNazev</td><td>$castObceNazev</td><td>$okresNazev</td><td>$longitude</td><td>$latitude</td><td>$objektKod</td><td>$adresaKod</td><td>$obecKod</td><td>$castObceKod</td><td>$uliceKod</td><td>$OpID</td></tr>";
        $i = $i + 1;
    }
}

echo "</table>";

mysqli_close($link);
?>