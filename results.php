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

$id_user = $_SESSION["id"];

$id = @$_GET["id"];

$query18 = "SELECT datum, osoba, silnice, hlasky, zadatel FROM testovani WHERE id = $id;";
if ($result18 = mysqli_query($link, $query18)) {
    while ($row18 = mysqli_fetch_row($result18)) {
        $old_hlasky = $row18[3];
    }
}

$hlasky_array = explode("|", $old_hlasky);
foreach ($hlasky_array as $id_hlaska) {
    $query27 = "INSERT INTO test_result (id_test, id_hlaska) VALUES ('$id','$id_hlaska');";
    $prikaz27 = mysqli_query($link, $query27);
}