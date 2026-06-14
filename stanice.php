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
$longitude = isset($_POST["longitude"]) ? trim($_POST["longitude"]) : "";
$latitude = isset($_POST["latitude"]) ? trim($_POST["latitude"]) : "";
$objektKod = isset($_POST["objektKod"]) ? trim($_POST["objektKod"]) : "";
$adresaKod = isset($_POST["adresaKod"]) ? trim($_POST["adresaKod"]) : "";
$obecKod = isset($_POST["obecKod"]) ? trim($_POST["obecKod"]) : "";
$castObceKod = isset($_POST["castObceKod"]) ? trim($_POST["castObceKod"]) : "";
$uliceKod = isset($_POST["uliceKod"]) ? trim($_POST["uliceKod"]) : "";
$OpID = isset($_POST["OpID"]) ? trim($_POST["OpID"]) : "";

// Filtry – buď z aktuálního požadavku, nebo z cookies
$reset_filters = isset($_GET['reset_filters']);

if ($reset_filters) {
    // Vymazání filtrů a cookies
    setcookie('stanice_filter_opid', '', time() - 3600, "/");
    setcookie('stanice_filter_long_coords', '', time() - 3600, "/");
    $filter_opid = '';
    $filter_long_coords = false;
} elseif (isset($_GET['opid']) || isset($_GET['long_coords'])) {
    // Nové nastavení filtrů z GET, uložit do cookies
    $filter_opid = isset($_GET['opid']) ? trim((string) $_GET['opid']) : '';
    $filter_long_coords = isset($_GET['long_coords']);

    setcookie('stanice_filter_opid', $filter_opid, time() + (86400 * 365), "/"); // 1 rok
    setcookie('stanice_filter_long_coords', $filter_long_coords ? '1' : '0', time() + (86400 * 365), "/");
} else {
    // Výchozí hodnoty z cookies, pokud existují
    $filter_opid = isset($_COOKIE['stanice_filter_opid']) ? trim((string) $_COOKIE['stanice_filter_opid']) : '';
    $filter_long_coords = isset($_COOKIE['stanice_filter_long_coords']) && $_COOKIE['stanice_filter_long_coords'] === '1';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($tel_cislo))) {
        $tel_cislo_err = "Zadejte prosím telefonní číslo.";
    } else {
        $query41 = "SELECT tel_cislo FROM stanice WHERE tel_cislo = '$tel_cislo';";
        if ($result41 = mysqli_query($link, $query41)) {
            $count_dupes = mysqli_num_rows($result41);
            switch ($count_dupes == 1) {
                case true:
                    $tel_cislo_err = "Telefonní číslo je již použito.";
                    break;
                default:
                    $tel_cislo = trim($tel_cislo);
                    break;
            }
        } else {
            echo "Něco se nepovedlo. Zkuste to prosím znovu.";
        }
    }
    $latitude = round($latitude, 7);
    $longitude = round($longitude, 7);

    $query57 = "INSERT INTO stanice (`prijmeni`,`jmeno`,`tel_cislo`,`ico`,`nazev_ulice`,`cislo_popisne`,`cislo_orientacni`,`cislo_podlazi`,`cislo_bytu`,`nazev_obce`,`nazev_casti_obce`,`nazev_okresu`,`longitude`,`latitude`,`kod_objektu`,`kod_adresy`,`kod_obce`,`kod_casti_obce`,`kod_ulice`,`OpID`) VALUES ('$prijmeni','$jmeno','$tel_cislo','$ico','$uliceNazev','$adresaCisloDomovni','$adresaCisloOrientacni','','','$obecNazev','$castObceNazev','$okresNazev','$longitude','$latitude','$objektKod','$adresaKod','$obecKod','$castObceKod','$uliceKod','$OpID');";
    $prikaz57 = mysqli_query($link, $query57);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>Lokalizace telefonních stanic</title>

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

            if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("data").innerHTML = xmlhttp.responseText;
                }
            }

            xmlhttp.open("GET", "helper.php?opt=" + str, true);
            xmlhttp.send();
        }

        function vyber(str) {
            var xmlhttp;

            if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function() {
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
    ?>
    <table>
        <tr>
            <td>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input name="action" value="generuj" type="hidden">

                    Telefonní číslo: <input type="text" name="tel_cislo" value="" autofocus><br />
                    Příjmení/Název: *<input type="text" name="prijmeni" value="<?php echo xss($prijmeni); ?>"> Jméno: <input
                        name="jmeno" value=""><br />
                    IČO: <input name="ico" value="<?php echo xss($ico); ?>"> OpID: <input name="OpID" size="3"
                        value="<?php echo xss($OpID); ?>"><br />

                    Adresa: <input onChange="najdi(this.value)">
                    <select id="data" onChange="vyber(this.value)" multiple>
                        <option>Vyhledejte adresu...</option>
                    </select>
                    <br />
                    <div id="mistoUdal">
                    </div>
                </form>
            </td>
            <td>
                <?php
                if ($id_user == '1') {
                    echo "<a href=\"mapa.php\" target=\"_blank\">Mapa</a>";
                }
                ?>
            </td>
        </tr>
    </table>
    <hr>
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="margin-bottom: 1em;">
        <label>Filtrovat dle OpID:</label>
        <input type="text" name="opid" value="<?php echo htmlspecialchars($filter_opid); ?>" placeholder="vše" size="6">
        <label style="margin-left: 1em;">
            <input type="checkbox" name="long_coords" value="1" <?php echo $filter_long_coords ? ' checked' : ''; ?>>
            Pouze zem. šířka/délka delší než 10 znaků
        </label>
        <button type="submit">Filtrovat</button>
        <?php if ($filter_opid !== '' || $filter_long_coords) { ?>
            <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?reset_filters=1">Zrušit filtr</a>
        <?php } ?>
    </form>
    <?php
    echo "<table width=\"100%\">";
    echo "<tr><th>Příjmení</th><th>Jméno</th><th>Telefonní číslo</th><th>IČO</th><th>Název ulice</th><th>Číslo domovní</th><th>Číslo orientační</th><th>Název obce</th><th>Název části obce</th><th>Název okresu</th><th>Zeměpisná šířka</th><th>Zeměpisná délka</th><th>Kód objektu</th><th>Kód adresy</th><th>Kód obce</th><th>Kód části obce</th><th>Kód ulice</th><th>OpID</th></tr>";
    $i = 0;

    $opid_esc = mysqli_real_escape_string($link, $filter_opid);
    $where = [];
    if ($filter_opid !== '') {
        $where[] = "OpID = '$opid_esc'";
    }
    if ($filter_long_coords) {
        $where[] = "(LENGTH(CAST(latitude AS CHAR)) > 10 OR LENGTH(CAST(longitude AS CHAR)) > 10)";
    }
    $where_sql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';
    $query177 = "SELECT * FROM stanice$where_sql ORDER BY tel_cislo;";
    if ($result177 = mysqli_query($link, $query177)) {
        while ($row177 = mysqli_fetch_row($result177)) {
            $prijmeni = $row177[0];
            $jmeno = $row177[1];
            $tel_cislo = $row177[2];
            $ico = $row177[3];
            $uliceNazev = $row177[4];
            $adresaCisloDomovni = $row177[5];
            $adresaCisloOrientacni = $row177[6];

            $obecNazev = $row177[9];
            $castObceNazev = $row177[10];
            $okresNazev = $row177[11];
            $longitude = $row177[12];
            $latitude = $row177[13];
            $objektKod = $row177[14];
            $adresaKod = $row177[15];
            $obecKod = $row177[16];
            $castObceKod = $row177[17];
            $uliceKod = $row177[18];
            $OpID = $row177[19];

            echo "<tr";
            if ($i % 2 == 0) {
                echo " bgcolor=\"#ddd\"";
            }
            echo "><td><a href=\"stanice_edit.php?cislo=" . xss($tel_cislo) . "\">" . xss($prijmeni) . "</a></td><td>" . xss($jmeno) . "</td><td>" . xss($tel_cislo) . "</td><td>" . xss($ico) . "</td><td>" . xss($uliceNazev) . "</td><td>" . xss($adresaCisloDomovni) . "</td><td>" . xss($adresaCisloOrientacni) . "</td><td>" . xss($obecNazev) . "</td><td>" . xss($castObceNazev) . "</td><td>" . xss($okresNazev) . "</td><td>" . xss($latitude) . "</td><td>" . xss($longitude) . "</td><td>" . xss($objektKod) . "</td><td>" . xss($adresaKod) . "</td><td>" . xss($obecKod) . "</td><td>" . xss($castObceKod) . "</td><td>" . xss($uliceKod) . "</td><td>" . xss($OpID) . "</td></tr>";
            $i++;
        }
    }

    echo "</table>";

    mysqli_close($link);
    ?>