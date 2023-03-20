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

$query15 = "SELECT id, longitude, latitude FROM hlasky WHERE export = 0 ORDER BY id;";
if ($result15 = mysqli_query($link, $query15)) {
    while ($row15 = mysqli_fetch_row($result15)) {
        $id = $row15[0];

        $query24 = "UPDATE hlasky SET export = 1 WHERE id = $id;";
        $result24 = mysqli_query($link, $query24);
        if (!$result24) {
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