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
include 'Converter.php';
$converter = new JTSK\Converter();

$action = @$_POST["action"];
$prijmeni = @$_POST["prijmeni"];
$jmeno = @$_POST["jmeno"];
$tel_cislo = @$_POST["tel_cislo"];
$ico = @$_POST["ico"];
$uliceNazev = @$_POST["uliceNazev"];
$adresaCisloDomovni = @$_POST["adresaCisloDomovni"];
$adresaCisloOrientacni = @$_POST["adresaCisloOrientacni"];

$obecNazev = @$_POST["obecNazev"];
$castObceNazev = @$_POST["castObceNazev"];
$okresNazev = @$_POST["okresNazev"];
$longitude = @$_POST["longitude"];
$latitude = @$_POST["latitude"];
$objektKod = @$_POST["objektKod"];
$adresaKod = @$_POST["adresaKod"];
$obecKod = @$_POST["obecKod"];
$castObceKod = @$_POST["castObceKod"];
$uliceKod = @$_POST["uliceKod"];
$OpID = @$_POST["OpID"];

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
                    Příjmení/Název: *<input type="text" name="prijmeni" value="<?php echo $prijmeni; ?>"> Jméno: <input
                        name="jmeno" value=""><br />
                    IČO: <input name="ico" value="<?php echo $ico; ?>"> OpID: <input name="OpID" size="3"
                        value="<?php echo $OpID; ?>"><br />

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
            echo "><td><a href=\"stanice_edit.php?cislo=$tel_cislo\">$prijmeni</a></td><td>$jmeno</td><td>$tel_cislo</td><td>$ico</td><td>$uliceNazev</td><td>$adresaCisloDomovni</td><td>$adresaCisloOrientacni</td><td>$obecNazev</td><td>$castObceNazev</td><td>$okresNazev</td><td>$latitude</td><td>$longitude</td><td>$objektKod</td><td>$adresaKod</td><td>$obecKod</td><td>$castObceKod</td><td>$uliceKod</td><td>$OpID</td></tr>";
            $i++;
        }
    }

    echo "</table>";

    mysqli_close($link);
    ?>