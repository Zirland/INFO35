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
            width: 500px;
            padding: 20px;
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
        $query50 = "SELECT `user_id`, app_id FROM opravneni;";
        if ($result50 = mysqli_query($link, $query50)) {
            while ($row50 = mysqli_fetch_row($result50)) {
                $usr = $row50[0];
                $app = $row50[1];

                $prava[] = "access_$usr_$app";
            }
        }

        unset($uzivatele);
        $query61 = "SELECT id from users ORDER BY id;";
        if ($result61 = mysqli_query($link, $query61)) {
            while ($row61 = mysqli_fetch_row($result61)) {
                $userid = $row61[0];

                $uzivatele[] = $userid;
            }
        }

        unset($aplikace);
        $query71 = "SELECT app_id from aplikace ORDER BY app_id;";
        if ($result71 = mysqli_query($link, $query71)) {
            while ($row71 = mysqli_fetch_row($result71)) {
                $appid = $row71[0];

                $aplikace[] = $appid;
            }
        }

        foreach ($uzivatele as $user) {
            foreach ($aplikace as $app) {
                $index = "U{$user}A{$app}";

                $opravneni = $_POST[$index];

                if ($opravneni == "1") {
                    $new_prava[] = "access_$usr_$app";
                }
            }
        }

        $remove = array_diff($prava, $new_prava);
        foreach ($remove as $rem_item) {
            $split = explode("_", $rem_item);
            $usr = $split[1];
            $app = $split[2];

            $query98 = "DELETE FROM opravneni WHERE user_id = '$usr' AND app_id = '$app';";
            $prikaz98 = mysqli_query($link, $query98);
        }

        $add = array_diff($new_prava, $prava);
        foreach ($add as $add_item) {
            $split = explode("_", $add_item);
            $usr = $split[1];
            $app = $split[2];

            $query108 = "INSERT INTO opravneni (user_id, app_id) VALUES ('$usr', '$app');";
            $prikaz108 = mysqli_query($link, $query108);
        }
    }

    unset($uzivatele);

    echo "<form action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\" method=\"post\">";
    echo "<table>";
    echo "<tr><th></th>";
    $query118 = "SELECT id, username FROM users ORDER BY id;";
    if ($result118 = mysqli_query($link, $query118)) {
        $num_users = mysqli_num_rows($result118);
        while ($row118 = mysqli_fetch_row($result118)) {
            $userid = $row118[0];
            $username = $row118[1];

            $uzivatele[] = $userid;
            echo "<th>$username</th>";
        }
    }
    echo "</tr>";
    $i = 0;
    $query131 = "SELECT app_id, nazev FROM aplikace ORDER BY app_id;";
    if ($result131 = mysqli_query($link, $query131)) {
        while ($row131 = mysqli_fetch_row($result131)) {
            $appid = $row131[0];
            $nazev = $row131[1];
            echo "<tr class=\"";
            echo ($i % 2 == 0) ? "dark" : "light";
            echo "\"><td>$nazev</td>";

            foreach ($uzivatele as $user) {
                echo "<td style=\"text-align:center;\">";
                echo "<input type=\"checkbox\" name=\"U{$user}A{$appid}\" value=\"1\"";

                $query144 = "SELECT id FROM opravneni WHERE `user_id` = '$user' AND `app_id` = '$appid';";
                if ($result144 = mysqli_query($link, $query144)) {
                    $num_users = mysqli_num_rows($result144);

                    if ($num_users != 0) {
                        echo " CHECKED";
                    }
                }
                echo "></td>";
            }
            $i++;
            echo " </tr>";
        }
    }

    echo "</table>";
    echo "<input type=\"submit\" value=\"Uložit změny\"></form>";

    mysqli_close($link);
    ?>