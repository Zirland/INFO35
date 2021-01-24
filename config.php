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
    global $link;
    echo "<table width=\"100%\">";
    echo "<tr>";

    unset($opravneni);
    $self = htmlspecialchars($_SERVER["PHP_SELF"]);
    $self = str_replace("/info35/", "", $self);

    $query33 = "SELECT app_id, up FROM aplikace WHERE url = '$self';";
    if ($result33 = mysqli_query($link, $query33)) {
        if (mysqli_num_rows($result33) == 0) {
            echo "Aplikace není registrována!!<br/>";
        } else {
            while ($row33 = mysqli_fetch_row($result33)) {
                $id_prev     = $row33[0];
                $up_app_code = $row33[1];
            }
        }
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

    if (!$opravneni) {
        $opravneni = [];
    }
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
                    $url_aplikace   = $row52[1];
                    $newTarget      = 0;

                    if ($tlacitka == "1" && $id_prev == "1") {
                        Redir($url_aplikace);
                    }

                    if (substr($url_aplikace, 0, 1) == "#") {
                        $url_aplikace = substr($url_aplikace, 1);
                        $newTarget    = 1;
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
}
