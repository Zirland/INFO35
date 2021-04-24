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
    <title>Index databáze INFO35</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 500px; padding: 20px; }
    </style>
</head>
<body>
<?php
PageHeader();

unset($uzivatele);

echo "<table>";
echo "<tr><th></th>";
$query28 = "SELECT id, username from users ORDER BY id;";
if ($result28 = mysqli_query($link, $query28)) {
    $num_users = mysqli_num_rows($result28);
    while ($row28 = mysqli_fetch_row($result28)) {
        $userid   = $row28[0];
        $username = $row28[1];

        $uzivatele[] = $userid;
        echo "<th>$username</th>";
    }
}
echo "</tr>";

$query40 = "SELECT app_id, nazev from aplikace ORDER BY app_id;";
if ($result40 = mysqli_query($link, $query40)) {
    while ($row40 = mysqli_fetch_row($result40)) {
        $appid = $row40[0];
        $nazev = $row40[1];
        echo "<tr><td>$nazev</td>";

        foreach ($uzivatele as $user) {
            echo "<td style=\"text-align:center;\">";

            $query55 = "SELECT id FROM opravneni WHERE `user_id` = '$user' AND `app_id` = '$appid';";
            if ($result55 = mysqli_query($link, $query55)) {
                $num_users = mysqli_num_rows($result55);

                if ($num_users != 0) {
                    echo "X";
                }
            }
            echo "</td>";
        }

        echo " </tr>";
    }
}

echo "</table>";

unset($prava);
$query72 = "SELECT `user_id`, app_id FROM opravneni;";
if ($result72 = mysqli_query($link, $query72)) {
    while ($row72 = mysqli_fetch_row($result72)) {
        $usr = $row72[0];
        $app = $row72[1];
        
        $prava[] = "access_".$usr."_".$app;
    }
}

var_dump($prava);

mysqli_close($link);
?>