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
    $query27 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i%';";
    if ($result27 = mysqli_query($link, $query27)) {
        $radky27 = mysqli_num_rows($result27);

        if ($radky27 == 0) {
            echo "<b>$i</b><br/>";
        } else {
            $query34 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i';";
            if ($result34 = mysqli_query($link, $query34)) {
//                $radky36 = mysqli_num_rows($result34);
                while ($row34 = mysqli_fetch_row($result34)) {
                    $prijmeni = $row34[0];
                    echo "$i = $prijmeni<br/>";
                }
            }
            for ($j = 0; $j < 10; $j++) {
                $query42 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j%';";
                if ($result42 = mysqli_query($link, $query42)) {
                    $radky42 = mysqli_num_rows($result42);

                    if ($radky42 == 0) {
                        echo "$i<b>$j</b><br/>";
                    } else {
                        $query49 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j';";
                        if ($result49 = mysqli_query($link, $query49)) {
//                            $radky53 = mysqli_num_rows($result49);
                            while ($row49 = mysqli_fetch_row($result49)) {
                                $prijmeni = $row49[0];
                                echo "$i$j = $prijmeni<br/>";
                            }
                        }
                        for ($k = 0; $k < 10; $k++) {
                            $query57 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k%';";
                            if ($result57 = mysqli_query($link, $query57)) {
                                $radky57 = mysqli_num_rows($result57);

                                if ($radky57 == 0) {
                                    echo "$i$j<b>$k</b><br/>";
                                } else {
                                    $query64 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k';";
                                    if ($result64 = mysqli_query($link, $query64)) {
//                                        $radky70 = mysqli_num_rows($result64);
                                        while ($row64 = mysqli_fetch_row($result64)) {
                                            $prijmeni = $row64[0];
                                            echo "$i$j$k = $prijmeni<br/>";
                                        }
                                    }
                                    for ($l = 0; $l < 10; $l++) {
                                        $query72 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l%';";
                                        if ($result72 = mysqli_query($link, $query72)) {
                                            $radky72 = mysqli_num_rows($result72);

                                            if ($radky72 == 0) {
                                                echo "$i$j$k<b>$l</b><br/>";
                                            } else {
                                                $query79 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k$l';";
                                                if ($result79 = mysqli_query($link, $query79)) {
//                                                    $radky87a = mysqli_num_rows($result79);
                                                    while ($row79 = mysqli_fetch_row($result79)) {
                                                        $prijmeni = $row79[0];
                                                        echo "$i$j$k$l = $prijmeni<br/>";
                                                    }
                                                }
                                                for ($m = 0; $m < 10; $m++) {
                                                    $query87 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m%';";
                                                    if ($result87 = mysqli_query($link, $query87)) {
                                                        $radky87 = mysqli_num_rows($result87);

                                                        if ($radky87 == 0) {
                                                            echo "$i$j$k$l<b>$m</b><br/>";
                                                        } else {
                                                            $query94 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k$l$m';";
                                                            if ($result94 = mysqli_query($link, $query94)) {
//                                                                $radky104 = mysqli_num_rows($result94);
                                                                while ($row94 = mysqli_fetch_row($result94)) {
                                                                    $prijmeni = $row94[0];
                                                                    echo "$i$j$k$l$m = $prijmeni<br/>";
                                                                }
                                                            }
                                                            for ($n = 0; $n < 10; $n++) {
                                                                $query102 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n%';";
                                                                if ($result102 = mysqli_query($link, $query102)) {
                                                                    $radky102 = mysqli_num_rows($result102);

                                                                    if ($radky102 == 0) {
                                                                        echo "$i$j$k$l$mb>$n</b><br/>";
                                                                    } else {
                                                                        $query109 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n';";
                                                                        if ($result109 = mysqli_query($link, $query109)) {
//                                                                            $radky121 = mysqli_num_rows($result109);
                                                                            while ($row109 = mysqli_fetch_row($result109)) {
                                                                                $prijmeni = $row109[0];
                                                                                echo "$i$j$k$l$mn = $prijmeni</b><br/>";
                                                                            }
                                                                        }
                                                                        for ($o = 0; $o < 10; $o++) {
                                                                            $query117 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n$o%';";
                                                                            if ($result117 = mysqli_query($link, $query117)) {
                                                                                $radky117 = mysqli_num_rows($result117);

                                                                                if ($radky117 == 0) {
                                                                                    echo "$i$j$k$l$m$n<b>$o</b><br/>";
                                                                                } else {
                                                                                    $query124 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n$o';";
                                                                                    if ($result124 = mysqli_query($link, $query124)) {
//                                                                                        $radky138 = mysqli_num_rows($result124);
                                                                                        while ($row124 = mysqli_fetch_row($result124)) {
                                                                                            $prijmeni = $row124[0];
                                                                                            echo "$i$j$k$l$m$n$o = $prijmeni</b><br/>";
                                                                                        }
                                                                                    }
                                                                                    for ($p = 0; $p < 10; $p++) {
                                                                                        $query132 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n$o$p%';";
                                                                                        if ($result132 = mysqli_query($link, $query132)) {
                                                                                            $radky132 = mysqli_num_rows($result132);

                                                                                            if ($radky132 == 0) {
                                                                                                echo "$i$j$k$l$m$n$o<b>$p</b><br/>";
                                                                                            } else {
                                                                                                $query139 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n$o$p';";
                                                                                                if ($result139 = mysqli_query($link, $query139)) {
//                                                                                                    $radky155 = mysqli_num_rows($result139);
                                                                                                    while ($row139 = mysqli_fetch_row($result139)) {
                                                                                                        $prijmeni = $row139[0];
                                                                                                        echo "$i$j$k$l$m$n$o$p = $prijmeni<br/>";
                                                                                                    }
                                                                                                }
                                                                                                for ($q = 0; $q < 10; $q++) {
                                                                                                    $query147 = "SELECT tel_cislo FROM stanice WHERE tel_cislo LIKE '$i$j$k$l$m$n$o$p$q%';";
                                                                                                    if ($result147 = mysqli_query($link, $query147)) {
                                                                                                        $radky147 = mysqli_num_rows($result147);

                                                                                                        if ($radky147 == 0) {
                                                                                                            echo "$i$j$k$l$m$n$o$p<b>$q</b><br/>";
                                                                                                        } else {
                                                                                                            $query154 = "SELECT prijmeni FROM stanice WHERE tel_cislo = '$i$j$k$l$m$n$o$p$q';";
                                                                                                            if ($result154 = mysqli_query($link, $query154)) {
                                                                                                                while ($row154 = mysqli_fetch_row($result154)) {
                                                                                                                    $prijmeni = $row154[0];
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

mysqli_close($link);
?>