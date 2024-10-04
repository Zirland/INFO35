<?php
require_once 'dbconnect.php';

$link = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

if ($link === false) {
    die("CHYBA: Nepovedlo se připojit. " . mysqli_connect_error());
}
mysqli_set_charset($link, "utf8");

function Redir($url_aplikace)
{
    echo "<script type='text/javascript'>document.location.href='{$url_aplikace}';</script>";
    echo "<META HTTP-EQUIV=\"refresh\" content=\"0;URL={$url_aplikace}\">";
}

function PageHeader()
{
    global $link, $up, $id_user;
    echo "<table width=\"100%\">";
    echo "<tr>";

    $opravneni = [];
    $self = htmlspecialchars($_SERVER["PHP_SELF"]);
    $self = str_replace("/info35/", "", $self);

    $query27 = "SELECT app_id, up FROM aplikace WHERE url = '$self';";
    if ($result27 = mysqli_query($link, $query27)) {
        switch (mysqli_num_rows($result27)) {
            case 0:
                echo "Aplikace není registrována!!<br/>";
                break;
            default:
                while ($row27 = mysqli_fetch_row($result27)) {
                    $id_prev = $row27[0];
                    $up_app_code = $row27[1];
                }
                break;
        }
    }
    if ($up != "") {
        $up_app_code = $up;
    }
    $query44 = "SELECT url FROM aplikace WHERE app_id = '$up_app_code';";
    if ($result44 = mysqli_query($link, $query44)) {
        while ($row44 = mysqli_fetch_row($result44)) {
            $up_app = $row44[0];
        }
        if (mysqli_num_rows($result44) == 0) {
            $up_app = "index.php";
        }
    }

    $query55 = "SELECT app_id FROM opravneni WHERE user_id = $id_user AND app_id IN (SELECT app_id FROM aplikace WHERE up = $id_prev);";
    if ($result55 = mysqli_query($link, $query55)) {
        while ($row55 = mysqli_fetch_row($result55)) {
            $opravneni[] = $row55[0];
        }
    }

    echo "<td width=\"5%\"><a href=\"$up_app\" class=\"btn btn-secondry\">Návrat zpět</a></td>";

    $tlacitka = count($opravneni);

    switch ($tlacitka) {
        case "0":
            echo "<td style=\"text-align:center;\" width=\"75%\">";
            echo "</td>";
            break;
        default:
            foreach ($opravneni as $aplikace) {
                $query73 = "SELECT nazev, url FROM aplikace WHERE app_id = $aplikace;";
                if ($result73 = mysqli_query($link, $query73)) {
                    while ($row73 = mysqli_fetch_row($result73)) {
                        $nazev_aplikace = $row73[0];
                        $url_aplikace = $row73[1];
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
            break;
    }

    $userName = htmlspecialchars($_SESSION['username']);

    echo "<td width=\"15%\">Přihlášený uživatel:<br/>$userName</td>";
    echo "<td width=\"5%\"><a href=\"logout.php\" class=\"btn btn-danger\">Odhlásit se</a></td>";
    echo "</tr>";
    echo "</table>";

    return $id_prev;
}

$provozovatel = $_SESSION['provozovatel'];
$id_user = $_SESSION["id"];