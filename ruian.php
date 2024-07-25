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

$error = "";

$query16 = "SELECT id, longitude, latitude FROM hlasky WHERE export = 0 ORDER BY id;";
if ($result16 = mysqli_query($link, $query16)) {
    while ($row16 = mysqli_fetch_row($result16)) {
        $id = $row16[0];

        $query21 = "UPDATE hlasky SET export = 1 WHERE id = $id;";
        $result21 = mysqli_query($link, $query21);
        if (!$result21) {
            $error .= mysqli_error($link) . "<br/>";
        }

        $param_hlaska_id = $id;
        $param_user = htmlspecialchars($_SESSION["username"]);
        $param_cas = microtime(true);
        $param_sloupec = "export";
        $param_new_value = 1;

        $query33 = "INSERT INTO log (hlaska_id, sloupec, new_value, user, cas) VALUES ('$param_hlaska_id', '$param_sloupec', '$param_new_value', '$param_user', '$param_cas');";
        $result33 = mysqli_query($link, $query33);
        if (!$result33) {
            $error .= mysqli_error($link) . "<br/>";
        }
    }
}

echo "Done...<br/>";
echo $error;
if ($error == "") {
    Redir("index.php");
}

mysqli_close($link);