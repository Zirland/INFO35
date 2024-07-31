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

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>Rozsahy INFO35</title>
    <style>
        body {
            font-family: monospace;
        }
    </style>
</head>


<body>
    <?php
    PageHeader();

    for ($i = 1; $i < 10; $i++) {
        $query34 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i%';";
        if ($result34 = mysqli_query($link, $query34)) {
            $radky34 = mysqli_num_rows($result34);

            if ($radky34 == 0) {
                echo "{$i}__ ___ ___<br/>";
            } else {
                $query41 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i';";
                if ($result41 = mysqli_query($link, $query41)) {
                    while ($row41 = mysqli_fetch_row($result41)) {
                        $prijmeni = $row41[0];
                        $nazev_ulice = $row41[1];
                        $cislo_popisne = $row41[2];
                        $cislo_orientacni = $row41[3];
                        $nazev_obce = $row41[4];
                        $nazev_casti_obce = $row41[5];
                        $kod_obce = $row41[6];
                        $OpID = $row41[7];

                        if ($OpID == "0") {
                            $OpID = "777";
                        }

                        $query57 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                        if ($result57 = mysqli_query($link, $query57)) {
                            while ($row57 = mysqli_fetch_row($result57)) {
                                $orig = $row57[0];
                            }
                        }

                        $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                        $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                        echo "{$i}__ ___ ___ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                    }
                }
                $reg = "{$i}[0-9]+";
                $query71 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                if ($result71 = mysqli_query($link, $query71)) {
                    $radky71 = mysqli_num_rows($result71);
                }
                if ($radky71 > 0) {
                    for ($j = 0; $j < 10; $j++) {
                        $query77 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j%';";
                        if ($result77 = mysqli_query($link, $query77)) {
                            $radky77 = mysqli_num_rows($result77);

                            if ($radky77 == 0) {
                                echo "{$i}{$j}_ ___ ___<br/>";
                            } else {
                                $query84 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j';";
                                if ($result84 = mysqli_query($link, $query84)) {

                                    while ($row84 = mysqli_fetch_row($result84)) {
                                        $prijmeni = $row84[0];
                                        $nazev_ulice = $row84[1];
                                        $cislo_popisne = $row84[2];
                                        $cislo_orientacni = $row84[3];
                                        $nazev_obce = $row84[4];
                                        $nazev_casti_obce = $row84[5];
                                        $kod_obce = $row84[6];
                                        $OpID = $row84[7];

                                        if ($OpID == "0") {
                                            $OpID = "777";
                                        }

                                        $query101 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                        if ($result101 = mysqli_query($link, $query101)) {
                                            while ($row101 = mysqli_fetch_row($result101)) {
                                                $orig = $row101[0];
                                            }
                                        }

                                        $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                        $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                        echo "{$i}{$j}_ ___ ___ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                    }
                                }
                                $reg = "{$i}{$j}[0-9]+";
                                $query115 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                if ($result115 = mysqli_query($link, $query115)) {
                                    $radky115 = mysqli_num_rows($result115);
                                }
                                if ($radky115 > 0) {
                                    for ($k = 0; $k < 10; $k++) {
                                        $query121 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k%';";
                                        if ($result121 = mysqli_query($link, $query121)) {
                                            $radky121 = mysqli_num_rows($result121);

                                            if ($radky121 == 0) {
                                                echo "{$i}{$j}{$k} ___ ___<br/>";
                                            } else {
                                                $query128 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k';";
                                                if ($result128 = mysqli_query($link, $query128)) {

                                                    while ($row128 = mysqli_fetch_row($result128)) {
                                                        $prijmeni = $row128[0];
                                                        $nazev_ulice = $row128[1];
                                                        $cislo_popisne = $row128[2];
                                                        $cislo_orientacni = $row128[3];
                                                        $nazev_obce = $row128[4];
                                                        $nazev_casti_obce = $row128[5];
                                                        $kod_obce = $row128[6];
                                                        $OpID = $row128[7];

                                                        if ($OpID == "0") {
                                                            $OpID = "777";
                                                        }

                                                        $query145 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                        if ($result145 = mysqli_query($link, $query145)) {
                                                            while ($row145 = mysqli_fetch_row($result145)) {
                                                                $orig = $row145[0];
                                                            }
                                                        }

                                                        $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                        $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                        echo "{$i}{$j}{$k} ___ ___ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                    }
                                                }
                                                $reg = "{$i}{$j}{$k}[0-9]+";
                                                $query159 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                                if ($result159 = mysqli_query($link, $query159)) {
                                                    $radky159 = mysqli_num_rows($result159);
                                                }
                                                if ($radky159 > 0) {
                                                    for ($l = 0; $l < 10; $l++) {
                                                        $query165 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k$l%';";
                                                        if ($result165 = mysqli_query($link, $query165)) {
                                                            $radky165 = mysqli_num_rows($result165);

                                                            if ($radky165 == 0) {
                                                                echo "{$i}{$j}{$k} {$l}__ ___<br/>";
                                                            } else {
                                                                $query172 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k$l' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k$l';";
                                                                if ($result172 = mysqli_query($link, $query172)) {
                                                                    while ($row172 = mysqli_fetch_row($result172)) {
                                                                        $prijmeni = $row172[0];
                                                                        $nazev_ulice = $row172[1];
                                                                        $cislo_popisne = $row172[2];
                                                                        $cislo_orientacni = $row172[3];
                                                                        $nazev_obce = $row172[4];
                                                                        $nazev_casti_obce = $row172[5];
                                                                        $kod_obce = $row172[6];
                                                                        $OpID = $row172[7];

                                                                        if ($OpID == "0") {
                                                                            $OpID = "777";
                                                                        }

                                                                        $query188 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                        if ($result188 = mysqli_query($link, $query188)) {
                                                                            while ($row188 = mysqli_fetch_row($result188)) {
                                                                                $orig = $row188[0];
                                                                            }
                                                                        }

                                                                        $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                        $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                        echo "{$i}{$j}{$k} {$l}__ ___ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                    }
                                                                }
                                                                $reg = "{$i}{$j}{$k}{$l}[0-9]+";
                                                                $query202 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                                                if ($result202 = mysqli_query($link, $query202)) {
                                                                    $radky202 = mysqli_num_rows($result202);
                                                                }
                                                                if ($radky202 > 0) {
                                                                    for ($m = 0; $m < 10; $m++) {
                                                                        $query208 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k$l$m%';";
                                                                        if ($result208 = mysqli_query($link, $query208)) {
                                                                            $radky208 = mysqli_num_rows($result208);

                                                                            if ($radky208 == 0) {
                                                                                echo "{$i}{$j}{$k} {$l}{$m}_ ___<br/>";
                                                                            } else {
                                                                                $query215 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k$l$m' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k$l$m';";
                                                                                if ($result215 = mysqli_query($link, $query215)) {
                                                                                    while ($row215 = mysqli_fetch_row($result215)) {
                                                                                        $prijmeni = $row215[0];
                                                                                        $nazev_ulice = $row215[1];
                                                                                        $cislo_popisne = $row215[2];
                                                                                        $cislo_orientacni = $row215[3];
                                                                                        $nazev_obce = $row215[4];
                                                                                        $nazev_casti_obce = $row215[5];
                                                                                        $kod_obce = $row215[6];
                                                                                        $OpID = $row215[7];

                                                                                        if ($OpID == "0") {
                                                                                            $OpID = "777";
                                                                                        }

                                                                                        $query231 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                                        if ($result231 = mysqli_query($link, $query231)) {
                                                                                            while ($row231 = mysqli_fetch_row($result231)) {
                                                                                                $orig = $row231[0];
                                                                                            }
                                                                                        }

                                                                                        $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                                        $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                                        echo "{$i}{$j}{$k} {$l}{$m}_ ___ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                                    }
                                                                                }
                                                                                $reg = "{$i}{$j}{$k}{$l}{$m}[0-9]+";
                                                                                $query245 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                                                                if ($result245 = mysqli_query($link, $query245)) {
                                                                                    $radky245 = mysqli_num_rows($result245);
                                                                                }
                                                                                if ($radky245 > 0) {
                                                                                    for ($n = 0; $n < 10; $n++) {
                                                                                        $query251 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k$l$m$n%';";
                                                                                        if ($result251 = mysqli_query($link, $query251)) {
                                                                                            $radky251 = mysqli_num_rows($result251);

                                                                                            if ($radky251 == 0) {
                                                                                                echo "{$i}{$j}{$k} {$l}{$m}{$n} ___<br/>";
                                                                                            } else {
                                                                                                $query258 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k$l$m$n';";
                                                                                                if ($result258 = mysqli_query($link, $query258)) {
                                                                                                    while ($row258 = mysqli_fetch_row($result258)) {
                                                                                                        $prijmeni = $row258[0];
                                                                                                        $nazev_ulice = $row258[1];
                                                                                                        $cislo_popisne = $row258[2];
                                                                                                        $cislo_orientacni = $row258[3];
                                                                                                        $nazev_obce = $row258[4];
                                                                                                        $nazev_casti_obce = $row258[5];
                                                                                                        $kod_obce = $row258[6];
                                                                                                        $OpID = $row258[7];

                                                                                                        if ($OpID == "0") {
                                                                                                            $OpID = "777";
                                                                                                        }

                                                                                                        $query274 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                                                        if ($result274 = mysqli_query($link, $query274)) {
                                                                                                            while ($row274 = mysqli_fetch_row($result274)) {
                                                                                                                $orig = $row274[0];
                                                                                                            }
                                                                                                        }

                                                                                                        $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                                                        $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                                                        echo "{$i}{$j}{$k} {$l}{$m}{$n} ___ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                                                    }
                                                                                                }
                                                                                                $reg = "{$i}{$j}{$k}{$l}{$m}{$n}[0-9]+";
                                                                                                $query288 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                                                                                if ($result288 = mysqli_query($link, $query288)) {
                                                                                                    $radky288 = mysqli_num_rows($result288);
                                                                                                }
                                                                                                if ($radky288 > 0) {
                                                                                                    for ($o = 0; $o < 10; $o++) {
                                                                                                        $query294 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n$o%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k$l$m$n$o%';";
                                                                                                        if ($result294 = mysqli_query($link, $query294)) {
                                                                                                            $radky294 = mysqli_num_rows($result294);

                                                                                                            if ($radky294 == 0) {
                                                                                                                echo "{$i}{$j}{$k} {$l}{$m}{$n} {$o}__<br/>";
                                                                                                            } else {
                                                                                                                $query301 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n$o' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k$l$m$n$o';";
                                                                                                                if ($result301 = mysqli_query($link, $query301)) {
                                                                                                                    while ($row301 = mysqli_fetch_row($result301)) {
                                                                                                                        $prijmeni = $row301[0];
                                                                                                                        $nazev_ulice = $row301[1];
                                                                                                                        $cislo_popisne = $row301[2];
                                                                                                                        $cislo_orientacni = $row301[3];
                                                                                                                        $nazev_obce = $row301[4];
                                                                                                                        $nazev_casti_obce = $row301[5];
                                                                                                                        $kod_obce = $row301[6];
                                                                                                                        $OpID = $row301[7];

                                                                                                                        if ($OpID == "0") {
                                                                                                                            $OpID = "777";
                                                                                                                        }

                                                                                                                        $query317 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                                                                        if ($result317 = mysqli_query($link, $query317)) {
                                                                                                                            while ($row317 = mysqli_fetch_row($result317)) {
                                                                                                                                $orig = $row317[0];
                                                                                                                            }
                                                                                                                        }

                                                                                                                        $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                                                                        $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                                                                        echo "{$i}{$j}{$k} {$l}{$m}{$n} {$o}__ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                                                                    }
                                                                                                                }
                                                                                                                $reg = "{$i}{$j}{$k}{$l}{$m}{$n}{$o}[0-9]+";
                                                                                                                $query331 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                                                                                                if ($result331 = mysqli_query($link, $query331)) {
                                                                                                                    $radky331 = mysqli_num_rows($result331);
                                                                                                                }
                                                                                                                if ($radky331 > 0) {
                                                                                                                    for ($p = 0; $p < 10; $p++) {
                                                                                                                        $query337 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n$o$p%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k$l$m$n$o$p%';";
                                                                                                                        if ($result337 = mysqli_query($link, $query337)) {
                                                                                                                            $radky337 = mysqli_num_rows($result337);

                                                                                                                            if ($radky337 == 0) {
                                                                                                                                echo "{$i}{$j}{$k} {$l}{$m}{$n} {$o}{$p}_<br/>";
                                                                                                                            } else {
                                                                                                                                $query344 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n$o$p' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k$l$m$n$o$p';";
                                                                                                                                if ($result344 = mysqli_query($link, $query344)) {
                                                                                                                                    while ($row344 = mysqli_fetch_row($result344)) {
                                                                                                                                        $prijmeni = $row344[0];
                                                                                                                                        $nazev_ulice = $row344[1];
                                                                                                                                        $cislo_popisne = $row344[2];
                                                                                                                                        $cislo_orientacni = $row344[3];
                                                                                                                                        $nazev_obce = $row344[4];
                                                                                                                                        $nazev_casti_obce = $row344[5];
                                                                                                                                        $kod_obce = $row344[6];
                                                                                                                                        $OpID = $row344[7];

                                                                                                                                        if ($OpID == "0") {
                                                                                                                                            $OpID = "777";
                                                                                                                                        }

                                                                                                                                        $query360 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                                                                                        if ($result360 = mysqli_query($link, $query360)) {
                                                                                                                                            while ($row360 = mysqli_fetch_row($result360)) {
                                                                                                                                                $orig = $row360[0];
                                                                                                                                            }
                                                                                                                                        }

                                                                                                                                        $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                                                                                        $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                                                                                        echo "{$i}{$j}{$k} {$l}{$m}{$n} {$o}{$p}_ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                                                                                    }
                                                                                                                                }
                                                                                                                                $reg = "{$i}{$j}{$k}{$l}{$m}{$n}{$o}{$p}[0-9]+";
                                                                                                                                $query374 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                                                                                                                if ($result374 = mysqli_query($link, $query374)) {
                                                                                                                                    $radky374 = mysqli_num_rows($result374);
                                                                                                                                }
                                                                                                                                if ($radky374 > 0) {
                                                                                                                                    for ($q = 0; $q < 10; $q++) {
                                                                                                                                        $query380 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n$o$p$q%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k$l$m$n$o$p$q%';";
                                                                                                                                        if ($result380 = mysqli_query($link, $query380)) {
                                                                                                                                            $radky380 = mysqli_num_rows($result380);

                                                                                                                                            if ($radky380 == 0) {
                                                                                                                                                echo "{$i}{$j}{$k} {$l}{$m}{$n} {$o}{$p}{$q}<br/>";
                                                                                                                                            } else {
                                                                                                                                                $query387 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n$o$p$q' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k$l$m$n$o$p$q';";
                                                                                                                                                if ($result387 = mysqli_query($link, $query387)) {
                                                                                                                                                    while ($row387 = mysqli_fetch_row($result387)) {
                                                                                                                                                        $nazev_ulice = $row387[1];
                                                                                                                                                        $cislo_popisne = $row387[2];
                                                                                                                                                        $cislo_orientacni = $row387[3];
                                                                                                                                                        $nazev_obce = $row387[4];
                                                                                                                                                        $nazev_casti_obce = $row387[5];
                                                                                                                                                        $kod_obce = $row387[6];
                                                                                                                                                        $OpID = $row387[7];

                                                                                                                                                        if ($OpID == "0") {
                                                                                                                                                            $OpID = "777";
                                                                                                                                                        }

                                                                                                                                                        $query402 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                                                                                                        if ($result402 = mysqli_query($link, $query402)) {
                                                                                                                                                            while ($row402 = mysqli_fetch_row($result402)) {
                                                                                                                                                                $orig = $row402[0];
                                                                                                                                                            }
                                                                                                                                                        }

                                                                                                                                                        $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                                                                                                        $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                                                                                                        $prijmeni = $row387[0];
                                                                                                                                                        echo "{$i}{$j}{$k} {$l}{$m}{$n} {$o}{$p}{$q} = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                                                                                                    }
                                                                                                                                                }
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    mysqli_close($link);
    ?>