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

            switch ($radky34) {
                case 0:
                    echo "{$i}__ ___ ___<br/>";
                    break;
                default:
                    $query43 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i';";
                    if ($result43 = mysqli_query($link, $query43)) {
                        while ($row43 = mysqli_fetch_row($result43)) {
                            $prijmeni = $row43[0];
                            $nazev_ulice = $row43[1];
                            $cislo_popisne = $row43[2];
                            $cislo_orientacni = $row43[3];
                            $nazev_obce = $row43[4];
                            $nazev_casti_obce = $row43[5];
                            $kod_obce = $row43[6];
                            $OpID = $row43[7];

                            if ($OpID == "0") {
                                $OpID = "777";
                            }

                            $query59 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                            if ($result59 = mysqli_query($link, $query59)) {
                                while ($row59 = mysqli_fetch_row($result59)) {
                                    $orig = $row59[0];
                                }
                            }

                            $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                            $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                            echo "{$i}__ ___ ___ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                        }
                    }
                    $reg = "{$i}[0-9]+";
                    $query73 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                    if ($result73 = mysqli_query($link, $query73)) {
                        $radky73 = mysqli_num_rows($result73);
                    }
                    if ($radky73 > 0) {
                        for ($j = 0; $j < 10; $j++) {
                            $query79 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j%';";
                            if ($result79 = mysqli_query($link, $query79)) {
                                $radky79 = mysqli_num_rows($result79);

                                switch ($radky79) {
                                    case 0:
                                        echo "{$i}{$j}_ ___ ___<br/>";
                                        break;
                                    default:
                                        $query88 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j';";
                                        if ($result88 = mysqli_query($link, $query88)) {

                                            while ($row88 = mysqli_fetch_row($result88)) {
                                                $prijmeni = $row88[0];
                                                $nazev_ulice = $row88[1];
                                                $cislo_popisne = $row88[2];
                                                $cislo_orientacni = $row88[3];
                                                $nazev_obce = $row88[4];
                                                $nazev_casti_obce = $row88[5];
                                                $kod_obce = $row88[6];
                                                $OpID = $row88[7];

                                                if ($OpID == "0") {
                                                    $OpID = "777";
                                                }

                                                $query105 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                if ($result105 = mysqli_query($link, $query105)) {
                                                    while ($row105 = mysqli_fetch_row($result105)) {
                                                        $orig = $row105[0];
                                                    }
                                                }

                                                $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                echo "{$i}{$j}_ ___ ___ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                            }
                                        }
                                        $reg = "{$i}{$j}[0-9]+";
                                        $query119 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                        if ($result119 = mysqli_query($link, $query119)) {
                                            $radky119 = mysqli_num_rows($result119);
                                        }
                                        if ($radky119 > 0) {
                                            for ($k = 0; $k < 10; $k++) {
                                                $query125 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k%';";
                                                if ($result125 = mysqli_query($link, $query125)) {
                                                    $radky125 = mysqli_num_rows($result125);

                                                    switch ($radky125) {
                                                        case 0:
                                                            echo "{$i}{$j}{$k} ___ ___<br/>";
                                                            break;
                                                        default:
                                                            $query134 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k';";
                                                            if ($result134 = mysqli_query($link, $query134)) {

                                                                while ($row134 = mysqli_fetch_row($result134)) {
                                                                    $prijmeni = $row134[0];
                                                                    $nazev_ulice = $row134[1];
                                                                    $cislo_popisne = $row134[2];
                                                                    $cislo_orientacni = $row134[3];
                                                                    $nazev_obce = $row134[4];
                                                                    $nazev_casti_obce = $row134[5];
                                                                    $kod_obce = $row134[6];
                                                                    $OpID = $row134[7];

                                                                    if ($OpID == "0") {
                                                                        $OpID = "777";
                                                                    }

                                                                    $query151 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                    if ($result151 = mysqli_query($link, $query151)) {
                                                                        while ($row151 = mysqli_fetch_row($result151)) {
                                                                            $orig = $row151[0];
                                                                        }
                                                                    }

                                                                    $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                    $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                    echo "{$i}{$j}{$k} ___ ___ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                }
                                                            }
                                                            $reg = "{$i}{$j}{$k}[0-9]+";
                                                            $query165 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                                            if ($result165 = mysqli_query($link, $query165)) {
                                                                $radky165 = mysqli_num_rows($result165);
                                                            }
                                                            if ($radky165 > 0) {
                                                                for ($l = 0; $l < 10; $l++) {
                                                                    $query171 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k$l%';";
                                                                    if ($result171 = mysqli_query($link, $query171)) {
                                                                        $radky171 = mysqli_num_rows($result171);

                                                                        switch ($radky171) {
                                                                            case 0:
                                                                                echo "{$i}{$j}{$k} {$l}__ ___<br/>";
                                                                                break;
                                                                            default:
                                                                                $query180 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k$l' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k$l';";
                                                                                if ($result180 = mysqli_query($link, $query180)) {
                                                                                    while ($row180 = mysqli_fetch_row($result180)) {
                                                                                        $prijmeni = $row180[0];
                                                                                        $nazev_ulice = $row180[1];
                                                                                        $cislo_popisne = $row180[2];
                                                                                        $cislo_orientacni = $row180[3];
                                                                                        $nazev_obce = $row180[4];
                                                                                        $nazev_casti_obce = $row180[5];
                                                                                        $kod_obce = $row180[6];
                                                                                        $OpID = $row180[7];

                                                                                        if ($OpID == "0") {
                                                                                            $OpID = "777";
                                                                                        }

                                                                                        $query196 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                                        if ($result196 = mysqli_query($link, $query196)) {
                                                                                            while ($row196 = mysqli_fetch_row($result196)) {
                                                                                                $orig = $row196[0];
                                                                                            }
                                                                                        }

                                                                                        $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                                        $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                                        echo "{$i}{$j}{$k} {$l}__ ___ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                                    }
                                                                                }
                                                                                $reg = "{$i}{$j}{$k}{$l}[0-9]+";
                                                                                $query210 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                                                                if ($result210 = mysqli_query($link, $query210)) {
                                                                                    $radky210 = mysqli_num_rows($result210);
                                                                                }
                                                                                if ($radky210 > 0) {
                                                                                    for ($m = 0; $m < 10; $m++) {
                                                                                        $query216 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k$l$m%';";
                                                                                        if ($result216 = mysqli_query($link, $query216)) {
                                                                                            $radky216 = mysqli_num_rows($result216);

                                                                                            switch ($radky216) {
                                                                                                case 0:
                                                                                                    echo "{$i}{$j}{$k} {$l}{$m}_ ___<br/>";
                                                                                                    break;
                                                                                                default:
                                                                                                    $query225 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k$l$m' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k$l$m';";
                                                                                                    if ($result225 = mysqli_query($link, $query225)) {
                                                                                                        while ($row225 = mysqli_fetch_row($result225)) {
                                                                                                            $prijmeni = $row225[0];
                                                                                                            $nazev_ulice = $row225[1];
                                                                                                            $cislo_popisne = $row225[2];
                                                                                                            $cislo_orientacni = $row225[3];
                                                                                                            $nazev_obce = $row225[4];
                                                                                                            $nazev_casti_obce = $row225[5];
                                                                                                            $kod_obce = $row225[6];
                                                                                                            $OpID = $row225[7];

                                                                                                            if ($OpID == "0") {
                                                                                                                $OpID = "777";
                                                                                                            }

                                                                                                            $query241 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                                                            if ($result241 = mysqli_query($link, $query241)) {
                                                                                                                while ($row241 = mysqli_fetch_row($result241)) {
                                                                                                                    $orig = $row241[0];
                                                                                                                }
                                                                                                            }

                                                                                                            $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                                                            $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                                                            echo "{$i}{$j}{$k} {$l}{$m}_ ___ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                                                        }
                                                                                                    }
                                                                                                    $reg = "{$i}{$j}{$k}{$l}{$m}[0-9]+";
                                                                                                    $query255 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                                                                                    if ($result255 = mysqli_query($link, $query255)) {
                                                                                                        $radky255 = mysqli_num_rows($result255);
                                                                                                    }
                                                                                                    if ($radky255 > 0) {
                                                                                                        for ($n = 0; $n < 10; $n++) {
                                                                                                            $query261 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k$l$m$n%';";
                                                                                                            if ($result261 = mysqli_query($link, $query261)) {
                                                                                                                $radky261 = mysqli_num_rows($result261);

                                                                                                                switch ($radky261) {
                                                                                                                    case 0:
                                                                                                                        echo "{$i}{$j}{$k} {$l}{$m}{$n} ___<br/>";
                                                                                                                        break;
                                                                                                                    default:
                                                                                                                        $query270 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k$l$m$n';";
                                                                                                                        if ($result270 = mysqli_query($link, $query270)) {
                                                                                                                            while ($row270 = mysqli_fetch_row($result270)) {
                                                                                                                                $prijmeni = $row270[0];
                                                                                                                                $nazev_ulice = $row270[1];
                                                                                                                                $cislo_popisne = $row270[2];
                                                                                                                                $cislo_orientacni = $row270[3];
                                                                                                                                $nazev_obce = $row270[4];
                                                                                                                                $nazev_casti_obce = $row270[5];
                                                                                                                                $kod_obce = $row270[6];
                                                                                                                                $OpID = $row270[7];

                                                                                                                                if ($OpID == "0") {
                                                                                                                                    $OpID = "777";
                                                                                                                                }

                                                                                                                                $query286 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                                                                                if ($result286 = mysqli_query($link, $query286)) {
                                                                                                                                    while ($row286 = mysqli_fetch_row($result286)) {
                                                                                                                                        $orig = $row286[0];
                                                                                                                                    }
                                                                                                                                }

                                                                                                                                $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                                                                                $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                                                                                echo "{$i}{$j}{$k} {$l}{$m}{$n} ___ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                                                                            }
                                                                                                                        }
                                                                                                                        $reg = "{$i}{$j}{$k}{$l}{$m}{$n}[0-9]+";
                                                                                                                        $query300 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                                                                                                        if ($result300 = mysqli_query($link, $query300)) {
                                                                                                                            $radky300 = mysqli_num_rows($result300);
                                                                                                                        }
                                                                                                                        if ($radky300 > 0) {
                                                                                                                            for ($o = 0; $o < 10; $o++) {
                                                                                                                                $query306 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n$o%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k$l$m$n$o%';";
                                                                                                                                if ($result306 = mysqli_query($link, $query306)) {
                                                                                                                                    $radky306 = mysqli_num_rows($result306);

                                                                                                                                    switch ($radky306) {
                                                                                                                                        case 0:
                                                                                                                                            echo "{$i}{$j}{$k} {$l}{$m}{$n} {$o}__<br/>";
                                                                                                                                            break;
                                                                                                                                        default:
                                                                                                                                            $query315 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n$o' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k$l$m$n$o';";
                                                                                                                                            if ($result315 = mysqli_query($link, $query315)) {
                                                                                                                                                while ($row315 = mysqli_fetch_row($result315)) {
                                                                                                                                                    $prijmeni = $row315[0];
                                                                                                                                                    $nazev_ulice = $row315[1];
                                                                                                                                                    $cislo_popisne = $row315[2];
                                                                                                                                                    $cislo_orientacni = $row315[3];
                                                                                                                                                    $nazev_obce = $row315[4];
                                                                                                                                                    $nazev_casti_obce = $row315[5];
                                                                                                                                                    $kod_obce = $row315[6];
                                                                                                                                                    $OpID = $row315[7];

                                                                                                                                                    if ($OpID == "0") {
                                                                                                                                                        $OpID = "777";
                                                                                                                                                    }

                                                                                                                                                    $query331 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                                                                                                    if ($result331 = mysqli_query($link, $query331)) {
                                                                                                                                                        while ($row331 = mysqli_fetch_row($result331)) {
                                                                                                                                                            $orig = $row331[0];
                                                                                                                                                        }
                                                                                                                                                    }

                                                                                                                                                    $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                                                                                                    $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                                                                                                    echo "{$i}{$j}{$k} {$l}{$m}{$n} {$o}__ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                                                                                                }
                                                                                                                                            }
                                                                                                                                            $reg = "{$i}{$j}{$k}{$l}{$m}{$n}{$o}[0-9]+";
                                                                                                                                            $query345 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                                                                                                                            if ($result345 = mysqli_query($link, $query345)) {
                                                                                                                                                $radky345 = mysqli_num_rows($result345);
                                                                                                                                            }
                                                                                                                                            if ($radky345 > 0) {
                                                                                                                                                for ($p = 0; $p < 10; $p++) {
                                                                                                                                                    $query351 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n$o$p%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k$l$m$n$o$p%';";
                                                                                                                                                    if ($result351 = mysqli_query($link, $query351)) {
                                                                                                                                                        $radky351 = mysqli_num_rows($result351);

                                                                                                                                                        switch ($radky351) {
                                                                                                                                                            case 0:
                                                                                                                                                                echo "{$i}{$j}{$k} {$l}{$m}{$n} {$o}{$p}_<br/>";
                                                                                                                                                                break;
                                                                                                                                                            default:
                                                                                                                                                                $query360 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n$o$p' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k$l$m$n$o$p';";
                                                                                                                                                                if ($result360 = mysqli_query($link, $query360)) {
                                                                                                                                                                    while ($row360 = mysqli_fetch_row($result360)) {
                                                                                                                                                                        $prijmeni = $row360[0];
                                                                                                                                                                        $nazev_ulice = $row360[1];
                                                                                                                                                                        $cislo_popisne = $row360[2];
                                                                                                                                                                        $cislo_orientacni = $row360[3];
                                                                                                                                                                        $nazev_obce = $row360[4];
                                                                                                                                                                        $nazev_casti_obce = $row360[5];
                                                                                                                                                                        $kod_obce = $row360[6];
                                                                                                                                                                        $OpID = $row360[7];

                                                                                                                                                                        if ($OpID == "0") {
                                                                                                                                                                            $OpID = "777";
                                                                                                                                                                        }

                                                                                                                                                                        $query376 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                                                                                                                        if ($result376 = mysqli_query($link, $query376)) {
                                                                                                                                                                            while ($row376 = mysqli_fetch_row($result376)) {
                                                                                                                                                                                $orig = $row376[0];
                                                                                                                                                                            }
                                                                                                                                                                        }

                                                                                                                                                                        $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                                                                                                                        $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                                                                                                                        echo "{$i}{$j}{$k} {$l}{$m}{$n} {$o}{$p}_ = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                                                                                                                    }
                                                                                                                                                                }
                                                                                                                                                                $reg = "{$i}{$j}{$k}{$l}{$m}{$n}{$o}{$p}[0-9]+";
                                                                                                                                                                $query390 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo REGEXP '$reg' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo REGEXP '$reg';";
                                                                                                                                                                if ($result390 = mysqli_query($link, $query390)) {
                                                                                                                                                                    $radky390 = mysqli_num_rows($result390);
                                                                                                                                                                }
                                                                                                                                                                if ($radky390 > 0) {
                                                                                                                                                                    for ($q = 0; $q < 10; $q++) {
                                                                                                                                                                        $query396 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n$o$p$q%' UNION SELECT tel_cislo FROM hlasky WHERE archiv = '0' AND tel_cislo LIKE '$i$j$k$l$m$n$o$p$q%';";
                                                                                                                                                                        if ($result396 = mysqli_query($link, $query396)) {
                                                                                                                                                                            $radky396 = mysqli_num_rows($result396);

                                                                                                                                                                            switch ($radky396) {
                                                                                                                                                                                case 0:
                                                                                                                                                                                    echo "{$i}{$j}{$k} {$l}{$m}{$n} {$o}{$p}{$q}<br/>";
                                                                                                                                                                                    break;
                                                                                                                                                                                default:
                                                                                                                                                                                    $query405 = "SELECT prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, nazev_obce, nazev_casti_obce, kod_obce, OpID FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n$o$p$q' UNION SELECT typ, silnice, kilometr, smer, obecNazev, castObceNazev, obecKod, archiv FROM hlasky WHERE archiv = '0' AND tel_cislo = '$i$j$k$l$m$n$o$p$q';";
                                                                                                                                                                                    if ($result405 = mysqli_query($link, $query405)) {
                                                                                                                                                                                        while ($row405 = mysqli_fetch_row($result405)) {
                                                                                                                                                                                            $nazev_ulice = $row405[1];
                                                                                                                                                                                            $cislo_popisne = $row405[2];
                                                                                                                                                                                            $cislo_orientacni = $row405[3];
                                                                                                                                                                                            $nazev_obce = $row405[4];
                                                                                                                                                                                            $nazev_casti_obce = $row405[5];
                                                                                                                                                                                            $kod_obce = $row405[6];
                                                                                                                                                                                            $OpID = $row405[7];

                                                                                                                                                                                            if ($OpID == "0") {
                                                                                                                                                                                                $OpID = "777";
                                                                                                                                                                                            }

                                                                                                                                                                                            $query420 = "SELECT orig FROM obce WHERE kod = '$kod_obce';";
                                                                                                                                                                                            if ($result420 = mysqli_query($link, $query420)) {
                                                                                                                                                                                                while ($row420 = mysqli_fetch_row($result420)) {
                                                                                                                                                                                                    $orig = $row420[0];
                                                                                                                                                                                                }
                                                                                                                                                                                            }

                                                                                                                                                                                            $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
                                                                                                                                                                                            $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";

                                                                                                                                                                                            $prijmeni = $row405[0];
                                                                                                                                                                                            echo "{$i}{$j}{$k} {$l}{$m}{$n} {$o}{$p}{$q} = ($orig) [$OpID] $prijmeni, $nazev_ulice $domovni, $mesto<br/>";
                                                                                                                                                                                        }
                                                                                                                                                                                    }
                                                                                                                                                                                    break;
                                                                                                                                                                            }
                                                                                                                                                                        }
                                                                                                                                                                    }
                                                                                                                                                                }
                                                                                                                                                                break;
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                }
                                                                                                                                            }
                                                                                                                                            break;
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                        break;
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                    break;
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                                break;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            break;
                                                    }
                                                }
                                            }
                                        }
                                        break;
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }

    mysqli_close($link);
    ?>