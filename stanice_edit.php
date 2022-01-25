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

$id_cislo              = @$_GET["cislo"];
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
    $query106  = "UPDATE stanice SET prijmeni='$prijmeni', jmeno='$jmeno', nazev_ulice='$uliceNazev', cislo_popisne='$adresaCisloDomovni', cislo_orientacni='$adresaCisloOrientacni', nazev_obce='$obecNazev', nazev_casti_obce='$castObceNazev', nazev_okresu='$okresNazev', longitude='$longitude', latitude='$latitude', kod_objektu='$objektKod', kod_adresy='$adresaKod', kod_obce='$obecKod', kod_casti_obce='$castObceKod', kod_ulice='$uliceKod' WHERE tel_cislo='$tel_cislo';";
    $prikaz106 = mysqli_query($link, $query106);
    if ($prikaz106 === false) {
        echo "CHYBA: " . mysqli_error($link);
    }

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>Úprava INFO35</title>

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

$query110 = "SELECT * FROM stanice WHERE tel_cislo = $id_cislo;";
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
    }
}
?>

<table><tr><td>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

Telefonní číslo: <input type="text" name="tel_cislo" value="<?php echo $tel_cislo ?>" readonly><br/>
Příjmení/Název: *<input type="text" name="prijmeni" value="<?php echo $prijmeni ?>">
Jméno: <input name="jmeno" value="<?php echo $jmeno ?>"><br/>
IČO: <input name="ico" value="<?php echo $ico ?>">	OpID: <input name="OpID" size="3" value="<?php echo $OpID ?>"><br/>

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

<hr/>

<?php
echo "<table>";
echo "<tr><th>Příjmení</th><th>Jméno</th><th>Telefonní číslo</th><th>IČO</th><th>Název ulice</th><th>Číslo domovní</th><th>Číslo orientační</th><th>Číslo podlaží</th><th>Číslo bytu</th><th>Název obce</th><th>Název části obce</th><th>Název okresu</th><th>Zeměpisná šířka</th><th>Zeměpisná délka</th><th>Kód objektu</th><th>Kód adresy</th><th>Kód obce</th><th>Kód části obce</th><th>Kód ulice</th><th>OpID</th></tr>";
echo "<tr";
echo "><td>$prijmeni</td><td>$jmeno</td><td>$tel_cislo</td><td>$ico</td><td>$uliceNazev</td><td>$adresaCisloDomovni</td><td>$adresaCisloOrientacni</td><td></td><td></td><td>$obecNazev</td><td>$castObceNazev</td><td>$okresNazev</td><td>$latitude</td><td>$longitude</td><td>$objektKod</td><td>$adresaKod</td><td>$obecKod</td><td>$castObceKod</td><td>$uliceKod</td><td>$OpID</td></tr>";
echo "</table>";

mysqli_close($link);
?>