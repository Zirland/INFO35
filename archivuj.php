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

$test_id = @$_GET["id"];
if ($test_id == "") {
    $test_id = @$_POST["id"];
}

$query27 = "UPDATE testovani SET archiv='1' WHERE id ='$test_id';";
$result27 = mysqli_query($link, $query27);
if (!$result27) {
    $error .= mysqli_error($link) . "<br/>";
}

echo "Pracuji...<br/>";
echo $error;
if ($error == "") {
    Redir("testovani.php");
}