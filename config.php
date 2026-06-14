<?php
require_once 'dbconnect.php';
require_once 'db_safe.php';

$link = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
if ($link === false) {
    die("CHYBA: Nepovedlo se připojit. " . mysqli_connect_error());
}
mysqli_set_charset($link, "utf8");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
$mail = new PHPMailer(true);
$mail->SMTPDebug = SMTP::DEBUG_OFF;
$mail->isSMTP();
$mail->Host = $mail_host;
$mail->SMTPAuth = true;
$mail->Username = $mail_username;
$mail->Password = $mail_password;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port = 465;
$mail->CharSet = "UTF-8";
$mail->setFrom($mail_username, 'Testování hlásek');
$mail->addBCC($mail_bcc);
$mail->isHTML(true);

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
            // Optimization: Load all apps in single query instead of N+1 SQL
            if (!empty($opravneni)) {
                $app_ids = array_map(function($id) use ($link) { return dbEscape($link, $id); }, $opravneni);
                $query73 = "SELECT app_id, nazev, url FROM aplikace WHERE app_id IN (" . implode(",", $app_ids) . ");";
                $apps_map = array();

                if ($result73 = mysqli_query($link, $query73)) {
                    while ($row73 = mysqli_fetch_row($result73)) {
                        $apps_map[$row73[0]] = array('nazev' => $row73[1], 'url' => $row73[2]);
                    }
                }

                foreach ($opravneni as $aplikace) {
                    if (isset($apps_map[$aplikace])) {
                        $nazev_aplikace = $apps_map[$aplikace]['nazev'];
                        $url_aplikace = $apps_map[$aplikace]['url'];
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