<?php
require_once 'dbconnect.php';

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("CHYBA: Nepovedlo se připojit. " . mysqli_connect_error());
}
mysqli_set_charset($link, "utf8");

function Redir($url_aplikace)
{
    echo "<script type='text/javascript'>document.location.href='{$url_aplikace}';</script>";
    echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $url_aplikace . '">';
}

function PageHeader()
{
    global $link, $up;
    echo "<table width=\"100%\">";
    echo "<tr>";

    $opravneni = [];
    $self = htmlspecialchars($_SERVER["PHP_SELF"]);
    $self = str_replace("/info35/", "", $self);

    $query33 = "SELECT app_id, up FROM aplikace WHERE url = '$self';";
    if ($result33 = mysqli_query($link, $query33)) {
        if (mysqli_num_rows($result33) == 0) {
            echo "Aplikace není registrována!!<br/>";
        } else {
            while ($row33 = mysqli_fetch_row($result33)) {
                $id_prev = $row33[0];
                $up_app_code = $row33[1];
            }
        }
    }
    if ($up != "") {
        $up_app_code = $up;
    }
    $query39 = "SELECT url FROM aplikace WHERE app_id = '$up_app_code';";
    if ($result39 = mysqli_query($link, $query39)) {
        while ($row39 = mysqli_fetch_row($result39)) {
            $up_app = $row39[0];
        }
        if (mysqli_num_rows($result39) == 0) {
            $up_app = "index.php";
        }
    }

    $id_user = $_SESSION["id"];
    $query30 = "SELECT app_id FROM opravneni WHERE user_id = $id_user AND app_id IN (SELECT app_id FROM aplikace WHERE up = $id_prev);";
    if ($result30 = mysqli_query($link, $query30)) {
        while ($row30 = mysqli_fetch_row($result30)) {
            $opravneni[] = $row30[0];
        }
    }

    echo "<td width=\"5%\"><a href=\"$up_app\" class=\"btn btn-secondry\">Návrat zpět</a></td>";

    $tlacitka = count($opravneni);

    if ($tlacitka == "0") {
        echo "<td style=\"text-align:center;\" width=\"75%\">";
        echo "</td>";
    } else {
        foreach ($opravneni as $aplikace) {
            $query52 = "SELECT nazev, url FROM aplikace WHERE app_id = $aplikace;";
            if ($result52 = mysqli_query($link, $query52)) {
                while ($row52 = mysqli_fetch_row($result52)) {
                    $nazev_aplikace = $row52[0];
                    $url_aplikace = $row52[1];
                    $newTarget = 0;

                    if ($tlacitka == "1" && $id_prev == "1") {
                        Redir($url_aplikace);
                    }

                    if (substr($url_aplikace, 0, 1) == "#") {
                        $url_aplikace = substr($url_aplikace, 1);
                        $newTarget = 1;
                    }

                    echo "<td style=\"text-align:center;\" width=\"" . 75 / $tlacitka . "%\">";
                    echo "<a href=\"$url_aplikace\" class=\"btn btn-primary\"";
                    if ($newTarget == 1) {
                        echo " target=\"_blank\"";
                    }
                    echo ">$nazev_aplikace</a>";
                    echo "</td>";
                }
            }
        }
    }

    $userName = htmlspecialchars($_SESSION['username']);

    echo "<td width=\"15%\">Přihlášený uživatel:<br/>$userName</td>";
    echo "<td width=\"5%\"><a href=\"logout.php\" class=\"btn btn-danger\">Odhlásit se</a></td>";
    echo "</tr>";
    echo "</table>";

    return $id_prev;
}

function SmerNazev($silnice, $smer, $kilometr)
{
    switch ($silnice) {
        case 'D0':
            if ($smer == "+" && $kilometr < 30) {
                $smer_nazev = "letiště";
            } elseif ($smer == "+" && $kilometr < 65) {
                $smer_nazev = "Štěrboholy";
            } elseif ($smer == "+") {
                $smer_nazev = "letiště";
            } elseif ($smer == "-" && $kilometr > 65) {
                $smer_nazev = "Brno";
            } elseif ($smer == "-" && $kilometr > 30) {
                $smer_nazev = "Liberec";
            } else {
                $smer_nazev = "Brno";
            }
            break;

        case 'D1':
            if ($smer == "+" && $kilometr < 189) {
                $smer_nazev = "Brno";
            } elseif ($smer == "+" && $kilometr < 273) {
                $smer_nazev = "Hulín";
            } elseif ($smer == "+") {
                $smer_nazev = "Bohumín";
            } elseif ($smer == "-" && $kilometr > 273) {
                $smer_nazev = "Přerov";
            } elseif ($smer == "-" && $kilometr > 203) {
                $smer_nazev = "Brno";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case 'D2':
            if ($smer == "+") {
                $smer_nazev = "Lanžhot";
            } else {
                $smer_nazev = "Brno";
            }
            break;

        case 'D3':
            if ($smer == "+") {
                $smer_nazev = "České Budějovice";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case 'D4':
            if ($smer == "+") {
                $smer_nazev = "Písek";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case 'D5':
            if ($smer == "+") {
                $smer_nazev = "Rozvadov";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case 'D6':
            if ($smer == "+" && $kilometr < 112) {
                $smer_nazev = "Karlovy Vary";
            } elseif ($smer == "+") {
                $smer_nazev = "Cheb";
            } elseif ($smer == "-" && $kilometr > 112) {
                $smer_nazev = "Karlovy Vary";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case 'D7':
            if ($smer == "+") {
                $smer_nazev = "Chomutov";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case 'D8':
            if ($smer == "+") {
                $smer_nazev = "Petrovice";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case 'D11':
            if ($smer == "+") {
                $smer_nazev = "Jaroměř";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case '20':
            if ($smer == "+") {
                $smer_nazev = "České Budějovice";
            } else {
                $smer_nazev = "Plzeň";
            }
            break;

        case 'D35':
            if ($smer == "+" && $kilometr < 160) {
                $smer_nazev = "Vysoké Mýto";
            } elseif ($smer == "+") {
                $smer_nazev = "Lipník nad Bečvou";
            } elseif ($smer == "-" && $kilometr > 220) {
                $smer_nazev = "Mohelnice";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case '35':
            if ($smer == "+" && $kilometr < 210) {
                $smer_nazev = "Mohelnice";
            } elseif ($smer == "+") {
                $smer_nazev = "Valašské Meziříčí";
            } elseif ($smer == "-" && $kilometr > 285) {
                $smer_nazev = "Hranice";
            } else {
                $smer_nazev = "Vysoké Mýto";
            }
            break;

        case '38':
            if ($smer == "+") {
                $smer_nazev = "Znojmo";
            } else {
                $smer_nazev = "Havlíčkův Brod";
            }
            break;

        case 'D48':
            if ($smer == "+") {
                $smer_nazev = "Český Těšín";
            } else {
                $smer_nazev = "Bělotín";
            }
            break;

        case 'D49':
            if ($smer == "+") {
                $smer_nazev = "Fryšták";
            } else {
                $smer_nazev = "Hulín";
            }
            break;

        case 'D52':
            if ($smer == "+") {
                $smer_nazev = "Mikulov";
            } else {
                $smer_nazev = "Brno";
            }
            break;

        case 'D55':
            if ($smer == "+") {
                $smer_nazev = "Uherské Hradiště";
            } else {
                $smer_nazev = "Kroměříž";
            }
            break;

        case 'D56':
            if ($smer == "+") {
                $smer_nazev = "Frýdek-Místek";
            } else {
                $smer_nazev = "Ostrava";
            }
            break;

        case '57':
            if ($smer == "+") {
                $smer_nazev = "Vsetín";
            } else {
                $smer_nazev = "Valašské Meziříčí";
            }
            break;

        case '58':
            if ($smer == "+") {
                $smer_nazev = "Ostrava";
            } else {
                $smer_nazev = "Rožnov pod Radhošťem";
            }
            break;

        default:
            $smer_nazev = $smer;
            break;
    }

    return $smer_nazev;
}