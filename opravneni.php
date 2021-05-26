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
        body {
            font: 14px sans-serif;
        }
        .wrapper {
            width: 500px; padding: 20px;
        }
        tr.dark {
            background-color: #ddd;
            color: black;
        }
        tr.light {
            background-color: #fff;
            color: black;
        }

    </style>
</head>
<body>
<?php
PageHeader();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    unset($prava);
    $query72 = "SELECT `user_id`, app_id FROM opravneni;";
    if ($result72 = mysqli_query($link, $query72)) {
        while ($row72 = mysqli_fetch_row($result72)) {
            $usr = $row72[0];
            $app = $row72[1];

            $prava[] = "access_" . $usr . "_" . $app;
        }
    }

    unset($uzivatele);
    $query28 = "SELECT id from users ORDER BY id;";
    if ($result28 = mysqli_query($link, $query28)) {
        while ($row28 = mysqli_fetch_row($result28)) {
            $userid = $row28[0];

            $uzivatele[] = $userid;
        }
    }

    unset($aplikace);
    $query28 = "SELECT app_id from aplikace ORDER BY app_id;";
    if ($result28 = mysqli_query($link, $query28)) {
        while ($row28 = mysqli_fetch_row($result28)) {
            $appid = $row28[0];

            $aplikace[] = $appid;
        }
    }

    foreach ($uzivatele as $user) {
        foreach ($aplikace as $app) {
            $index = "U" . $user . "A" . $app;

            $opravneni = $_POST[$index];

            if ($opravneni == "1") {
                $new_prava[] = "access_" . $user . "_" . $app;
            }
        }
    }

    $remove = array_diff($prava, $new_prava);
    foreach ($remove as $rem_item) {
        $split = explode("_", $rem_item);
        $usr = $split[1];
        $app = $split[2];

        $query91 = "DELETE FROM opravneni WHERE user_id = '$usr' AND app_id = '$app';";
        $prikaz91 = mysqli_query($link, $query91);
    }

    $add = array_diff($new_prava, $prava);
    foreach ($add as $add_item) {
        $split = explode("_", $add_item);
        $usr = $split[1];
        $app = $split[2];

        $query91 = "INSERT INTO opravneni (user_id, app_id) VALUES ('$usr', '$app');";
        $prikaz91 = mysqli_query($link, $query91);
    }
}

unset($uzivatele);

echo "<form action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\" method=\"post\">";
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
$i       = 0;
$query40 = "SELECT app_id, nazev from aplikace ORDER BY app_id;";
if ($result40 = mysqli_query($link, $query40)) {
    while ($row40 = mysqli_fetch_row($result40)) {
        $appid = $row40[0];
        $nazev = $row40[1];
        echo "<tr class=\"";
        if ($i % 2 == 0) {
            echo "dark";
        } else {
            echo "light";
        }
        echo "\">";
        echo "<td>$nazev</td>";

        foreach ($uzivatele as $user) {
            echo "<td style=\"text-align:center;\">";

            echo "<input type=\"checkbox\" name=\"U";
            echo $user;
            echo "A";
            echo "$appid\" value=\"1\"";

            $query55 = "SELECT id FROM opravneni WHERE `user_id` = '$user' AND `app_id` = '$appid';";
            if ($result55 = mysqli_query($link, $query55)) {
                $num_users = mysqli_num_rows($result55);

                if ($num_users != 0) {
                    echo " CHECKED";
                }
            }
            echo "></td>";
        }
        $i = $i + 1;
        echo " </tr>";
    }
}

echo "</table>";
echo "<input type=\"submit\" value=\"Uložit změny\"></form>";

mysqli_close($link);
?>