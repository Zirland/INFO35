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

$query16 = "SELECT datum, osoba, silnice, hlasky, zadatel FROM testovani WHERE id = $id;";
if ($result16 = mysqli_query($link, $query16)) {
    while ($row16 = mysqli_fetch_row($result16)) {
        $old_hlasky = $row16[3];
    }
}

$hlasky_array = explode("|", $old_hlasky);
foreach ($hlasky_array as $id_hlaska) {
    $query147 = "INSERT INTO test_result (id_test, id_hlaska) VALUES ('$id','$id_hlaska');";
    $prikaz147 = mysqli_query($link, $query147);
}