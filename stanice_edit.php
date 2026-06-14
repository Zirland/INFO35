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
require_once 'db_safe.php';
require_once 'xss_safe.php';
include 'Converter.php';
$converter = new JTSK\Converter();

$id_cislo = isset($_GET["cislo"]) ? dbEscape($link, trim($_GET["cislo"])) : "";
$delete_flag = isset($_GET["delete"]) ? dbEscape($link, trim($_GET["delete"])) : "";

// Escapuj POST proměnné
$action = isset($_POST["action"]) ? dbEscape($link, trim($_POST["action"])) : "";
$prijmeni = isset($_POST["prijmeni"]) ? dbEscape($link, trim($_POST["prijmeni"])) : "";
$jmeno = isset($_POST["jmeno"]) ? dbEscape($link, trim($_POST["jmeno"])) : "";
$tel_cislo = isset($_POST["tel_cislo"]) ? dbEscape($link, trim($_POST["tel_cislo"])) : "";
$ico = isset($_POST["ico"]) ? dbEscape($link, trim($_POST["ico"])) : "";
$uliceNazev = isset($_POST["uliceNazev"]) ? dbEscape($link, trim($_POST["uliceNazev"])) : "";
$adresaCisloDomovni = isset($_POST["adresaCisloDomovni"]) ? dbEscape($link, trim($_POST["adresaCisloDomovni"])) : "";
$adresaCisloOrientacni = isset($_POST["adresaCisloOrientacni"]) ? dbEscape($link, trim($_POST["adresaCisloOrientacni"])) : "";

$obecNazev = isset($_POST["obecNazev"]) ? dbEscape($link, trim($_POST["obecNazev"])) : "";
$castObceNazev = isset($_POST["castObceNazev"]) ? dbEscape($link, trim($_POST["castObceNazev"])) : "";
$okresNazev = isset($_POST["okresNazev"]) ? dbEscape($link, trim($_POST["okresNazev"])) : "";
$longitude = $_POST["longitude"] ?? '';
$latitude = $_POST["latitude"] ?? '';
$objektKod = $_POST["objektKod"] ?? '';
$adresaKod = $_POST["adresaKod"] ?? '';
$obecKod = $_POST["obecKod"] ?? '';
$castObceKod = $_POST["castObceKod"] ?? '';
$uliceKod = $_POST["uliceKod"] ?? '';
$OpID = $_POST["OpID"] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $longitude = round($longitude, 7);
    $latitude = round($latitude, 7);
    $query39 = "UPDATE stanice SET prijmeni='$prijmeni', jmeno='$jmeno', nazev_ulice='$uliceNazev', cislo_popisne='$adresaCisloDomovni', cislo_orientacni='$adresaCisloOrientacni', nazev_obce='$obecNazev', nazev_casti_obce='$castObceNazev', nazev_okresu='$okresNazev', longitude='$longitude', latitude='$latitude', kod_objektu='$objektKod', kod_adresy='$adresaKod', kod_obce='$obecKod', kod_casti_obce='$castObceKod', kod_ulice='$uliceKod' WHERE tel_cislo='$tel_cislo';";
    $prikaz39 = mysqli_query($link, $query39);
    if ($prikaz39 === false) {
        echo "CHYBA: " . mysqli_error($link);
    }
}

if ($delete_flag == "yes") {
    $query40 = "DELETE FROM stanice WHERE tel_cislo='$id_cislo';";
    $prikaz40 = mysqli_query($link, $query40);
    if ($prikaz40 === false) {
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
        body {
            font: 11px sans-serif;
        }

        .wrapper {
            width: 500px;
            padding: 20px;
        }

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
                xmlhttp = new XMLHttpRequest();
            } else {// code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("data").innerHTML = xmlhttp.responseText;
                }
            }

            xmlhttp.open("GET", "helper.php?opt=" + str, true);
            xmlhttp.send();
        }

        function vyber(str) {
            var xmlhttp;

            if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {// code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("mistoUdal").innerHTML = xmlhttp.responseText;
                }
            }

            xmlhttp.open("GET", "helper2.php?kandi=" + str, true);
            xmlhttp.send();
        }
    </script>
</head>

<body>
    <?php
    PageHeader();

    $query123 = "SELECT * FROM stanice WHERE tel_cislo = $id_cislo;";
    if ($result123 = mysqli_query($link, $query123)) {
        while ($row123 = mysqli_fetch_row($result123)) {
            $prijmeni = $row123[0];
            $jmeno = $row123[1];
            $tel_cislo = $row123[2];
            $ico = $row123[3];
            $uliceNazev = $row123[4];
            $adresaCisloDomovni = $row123[5];
            $adresaCisloOrientacni = $row123[6];

            $obecNazev = $row123[9];
            $castObceNazev = $row123[10];
            $okresNazev = $row123[11];
            $longitude = $row123[12];
            $latitude = $row123[13];
            $objektKod = $row123[14];
            $adresaKod = $row123[15];
            $obecKod = $row123[16];
            $castObceKod = $row123[17];
            $uliceKod = $row123[18];
            $OpID = $row123[19];
        }
    }
    ?>

    <table>
        <tr>
            <td>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                    Telefonní číslo: <input type="text" name="tel_cislo" value="<?php echo xss($tel_cislo); ?>"
                        readonly><br />
                    Příjmení/Název: *<input type="text" name="prijmeni" value="<?php echo xss($prijmeni); ?>">
                    Jméno: <input name="jmeno" value="<?php echo xss($jmeno); ?>"><br />
                    IČO: <input name="ico" value="<?php echo xss($ico); ?>"> OpID: <input name="OpID" size="3"
                        value="<?php echo xss($OpID); ?>"><br />

                    Adresa: <input onChange="najdi(this.value)">
                    <select id="data" onChange="vyber(this.value)" multiple>
                        <option>Select an Option...</option>
                    </select>
                    <br />
                    <div id="mistoUdal">
                    </div>
                </form>

                <hr />

                <?php
                echo "<table>";
                echo "<tr><th>Příjmení</th><th>Jméno</th><th>Telefonní číslo</th><th>IČO</th><th>Název ulice</th><th>Číslo domovní</th><th>Číslo orientační</th><th>Číslo podlaží</th><th>Číslo bytu</th><th>Název obce</th><th>Název části obce</th><th>Název okresu</th><th>Zeměpisná šířka</th><th>Zeměpisná délka</th><th>Kód objektu</th><th>Kód adresy</th><th>Kód obce</th><th>Kód části obce</th><th>Kód ulice</th><th>OpID</th><th>Vymazat</th></tr>";
                echo "<tr><td>" . xss($prijmeni) . "</td><td>" . xss($jmeno) . "</td><td>" . xss($tel_cislo) . "</td><td>" . xss($ico) . "</td><td>" . xss($uliceNazev) . "</td><td>" . xss($adresaCisloDomovni) . "</td><td>" . xss($adresaCisloOrientacni) . "</td><td></td><td></td><td>" . xss($obecNazev) . "</td><td>" . xss($castObceNazev) . "</td><td>" . xss($okresNazev) . "</td><td>" . xss($latitude) . "</td><td>" . xss($longitude) . "</td><td>" . xss($objektKod) . "</td><td>" . xss($adresaKod) . "</td><td>" . xss($obecKod) . "</td><td>" . xss($castObceKod) . "</td><td>" . xss($uliceKod) . "</td><td>" . xss($OpID) . "</td><td><a href='stanice_edit.php?delete=yes&cislo=" . xss($id_cislo) . "'>Vymazat</a></td></tr>";
                echo "</table>";

                mysqli_close($link);
                ?>