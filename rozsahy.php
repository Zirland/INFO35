<?php
date_default_timezone_set('Europe/Prague');
session_start();

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
</head>


<body>
<?php
PageHeader();

for ($i = 0; $i < 10; $i++) {
    $query26 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i%';";
    if ($result26 = mysqli_query($link, $query26)) {
        $radky26 = mysqli_num_rows($result26);

        if ($radky26 == 0) {
            echo "<b>$i</b><br/>";
        } else {
            $query33 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i';";
            if ($result33 = mysqli_query($link, $query33)) {
                while ($row33 = mysqli_fetch_row($result33)) {
                    $prijmeni = $row33[0];
                    echo "$i = $prijmeni<br/>";
                }
            }
            $reg     = $i . "[0-9]+";
            $query41 = "SELECT prijmeni FROM stanice WHERE tel_cislo REGEXP '$reg';";
            if ($result41 = mysqli_query($link, $query41)) {
                $radky41 = mysqli_num_rows($result41);
            }
            if ($radky41 > 0) {
                for ($j = 0; $j < 10; $j++) {
                    $query47 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j%';";
                    if ($result47 = mysqli_query($link, $query47)) {
                        $radky47 = mysqli_num_rows($result47);

                        if ($radky47 == 0) {
                            echo "$i<b>$j</b><br/>";
                        } else {
                            $query54 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j';";
                            if ($result54 = mysqli_query($link, $query54)) {

                                while ($row54 = mysqli_fetch_row($result54)) {
                                    $prijmeni = $row54[0];
                                    echo "$i$j = $prijmeni<br/>";
                                }
                            }
                            $reg     = $i . $j . "[0-9]+";
                            $query63 = "SELECT prijmeni FROM stanice WHERE tel_cislo REGEXP '$reg';";
                            if ($result63 = mysqli_query($link, $query63)) {
                                $radky63 = mysqli_num_rows($result63);
                            }
                            if ($radky63 > 0) {
                                for ($k = 0; $k < 10; $k++) {
                                    $query69 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k%';";
                                    if ($result69 = mysqli_query($link, $query69)) {
                                        $radky69 = mysqli_num_rows($result69);

                                        if ($radky69 == 0) {
                                            echo "$i$j<b>$k</b><br/>";
                                        } else {
                                            $query76 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k';";
                                            if ($result76 = mysqli_query($link, $query76)) {

                                                while ($row76 = mysqli_fetch_row($result76)) {
                                                    $prijmeni = $row76[0];
                                                    echo "$i$j$k = $prijmeni<br/>";
                                                }
                                            }
                                            $reg     = $i . $j . $k . "[0-9]+";
                                            $query85 = "SELECT prijmeni FROM stanice WHERE tel_cislo REGEXP '$reg';";
                                            if ($result85 = mysqli_query($link, $query85)) {
                                                $radky85 = mysqli_num_rows($result85);
                                            }
                                            if ($radky85 > 0) {
                                                for ($l = 0; $l < 10; $l++) {
                                                    $query91 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l%';";
                                                    if ($result91 = mysqli_query($link, $query91)) {
                                                        $radky91 = mysqli_num_rows($result91);

                                                        if ($radky91 == 0) {
                                                            echo "$i$j$k<b>$l</b><br/>";
                                                        } else {
                                                            $query98 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k$l';";
                                                            if ($result98 = mysqli_query($link, $query98)) {
                                                                while ($row98 = mysqli_fetch_row($result98)) {
                                                                    $prijmeni = $row98[0];
                                                                    echo "$i$j$k$l = $prijmeni<br/>";
                                                                }
                                                            }
                                                            $reg      = $i . $j . $k . $l . "[0-9]+";
                                                            $query106 = "SELECT prijmeni FROM stanice WHERE tel_cislo REGEXP '$reg';";
                                                            if ($result106 = mysqli_query($link, $query106)) {
                                                                $radky106 = mysqli_num_rows($result106);
                                                            }
                                                            if ($radky106 > 0) {
                                                                for ($m = 0; $m < 10; $m++) {
                                                                    $query112 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m%';";
                                                                    if ($result112 = mysqli_query($link, $query112)) {
                                                                        $radky112 = mysqli_num_rows($result112);

                                                                        if ($radky112 == 0) {
                                                                            echo "$i$j$k$l<b>$m</b><br/>";
                                                                        } else {
                                                                            $query119 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k$l$m';";
                                                                            if ($result119 = mysqli_query($link, $query119)) {
                                                                                while ($row119 = mysqli_fetch_row($result119)) {
                                                                                    $prijmeni = $row119[0];
                                                                                    echo "$i$j$k$l$m = $prijmeni<br/>";
                                                                                }
                                                                            }
                                                                            $reg      = $i . $j . $k . $l . $m . "[0-9]+";
                                                                            $query127 = "SELECT prijmeni FROM stanice WHERE tel_cislo REGEXP '$reg';";
                                                                            if ($result127 = mysqli_query($link, $query127)) {
                                                                                $radky127 = mysqli_num_rows($result127);
                                                                            }
                                                                            if ($radky127 > 0) {
                                                                                for ($n = 0; $n < 10; $n++) {
                                                                                    $query133 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n%';";
                                                                                    if ($result133 = mysqli_query($link, $query133)) {
                                                                                        $radky133 = mysqli_num_rows($result133);

                                                                                        if ($radky133 == 0) {
                                                                                            echo "$i$j$k$l$m<b>$n</b><br/>";
                                                                                        } else {
                                                                                            $query140 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n';";
                                                                                            if ($result140 = mysqli_query($link, $query140)) {
                                                                                                while ($row140 = mysqli_fetch_row($result140)) {
                                                                                                    $prijmeni = $row140[0];
                                                                                                    echo "$i$j$k$l$m$n = $prijmeni</b><br/>";
                                                                                                }
                                                                                            }
                                                                                            $reg      = $i . $j . $k . $l . $m . $n . "[0-9]+";
                                                                                            $query148 = "SELECT prijmeni FROM stanice WHERE tel_cislo REGEXP '$reg';";
                                                                                            if ($result148 = mysqli_query($link, $query148)) {
                                                                                                $radky148 = mysqli_num_rows($result148);
                                                                                            }
                                                                                            if ($radky148 > 0) {
                                                                                                for ($o = 0; $o < 10; $o++) {
                                                                                                    $query154 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n$o%';";
                                                                                                    if ($result154 = mysqli_query($link, $query154)) {
                                                                                                        $radky154 = mysqli_num_rows($result154);

                                                                                                        if ($radky154 == 0) {
                                                                                                            echo "$i$j$k$l$m$n<b>$o</b><br/>";
                                                                                                        } else {
                                                                                                            $query161 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n$o';";
                                                                                                            if ($result161 = mysqli_query($link, $query161)) {
                                                                                                                while ($row161 = mysqli_fetch_row($result161)) {
                                                                                                                    $prijmeni = $row161[0];
                                                                                                                    echo "$i$j$k$l$m$n$o = $prijmeni</b><br/>";
                                                                                                                }
                                                                                                            }
                                                                                                            $reg      = $i . $j . $k . $l . $m . $n . $o . "[0-9]+";
                                                                                                            $query169 = "SELECT prijmeni FROM stanice WHERE tel_cislo REGEXP '$reg';";
                                                                                                            if ($result169 = mysqli_query($link, $query169)) {
                                                                                                                $radky169 = mysqli_num_rows($result169);
                                                                                                            }
                                                                                                            if ($radky169 > 0) {
                                                                                                                for ($p = 0; $p < 10; $p++) {
                                                                                                                    $query175 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n$o$p%';";
                                                                                                                    if ($result175 = mysqli_query($link, $query175)) {
                                                                                                                        $radky175 = mysqli_num_rows($result175);

                                                                                                                        if ($radky175 == 0) {
                                                                                                                            echo "$i$j$k$l$m$n$o<b>$p</b><br/>";
                                                                                                                        } else {
                                                                                                                            $query182 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n$o$p';";
                                                                                                                            if ($result182 = mysqli_query($link, $query182)) {
                                                                                                                                while ($row182 = mysqli_fetch_row($result182)) {
                                                                                                                                    $prijmeni = $row182[0];
                                                                                                                                    echo "$i$j$k$l$m$n$o$p = $prijmeni<br/>";
                                                                                                                                }
                                                                                                                            }
                                                                                                                            $reg      = $i . $j . $k . $l . $m . $n . $o . $p . "[0-9]+";
                                                                                                                            $query190 = "SELECT prijmeni FROM stanice WHERE tel_cislo REGEXP '$reg';";
                                                                                                                            if ($result190 = mysqli_query($link, $query190)) {
                                                                                                                                $radky190 = mysqli_num_rows($result190);
                                                                                                                            }
                                                                                                                            if ($radky190 > 0) {
                                                                                                                                for ($q = 0; $q < 10; $q++) {
                                                                                                                                    $query196 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n$o$p$q%';";
                                                                                                                                    if ($result196 = mysqli_query($link, $query196)) {
                                                                                                                                        $radky196 = mysqli_num_rows($result196);

                                                                                                                                        if ($radky196 == 0) {
                                                                                                                                            echo "$i$j$k$l$m$n$o$p<b>$q</b><br/>";
                                                                                                                                        } else {
                                                                                                                                            $query203 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n$o$p$q';";
                                                                                                                                            if ($result203 = mysqli_query($link, $query203)) {
                                                                                                                                                while ($row203 = mysqli_fetch_row($result203)) {
                                                                                                                                                    $prijmeni = $row203[0];
                                                                                                                                                    echo "$i$j$k$l$m$n$o$p$q = $prijmeni<br/>";
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