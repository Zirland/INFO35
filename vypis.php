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
<html lang="cs">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>INFO35 SOS hlásky</title>
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

        tr.dark-strikeout {
            background-color: #ddd;
            color: red;
        }

        tr.light-strikeout {
            background-color: #fff;
            color: red;
        }
    </style>

    <script type="text/javascript">
        var tel_cislo;
        var silnice;
        var ssud;
        var typ;

        function telcislo(num) {
            tel_cislo=num;
            filtr();
        }

        function search(str) {
            var xmlhttp;

            xmlhttp=new XMLHttpRequest();

            xmlhttp.onreadystatechange = function() {
                if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("data").innerHTML = xmlhttp.responseText;
                }
            }
            window.alert('vypis_filtr.php?'+str);
            xmlhttp.open("GET",'vypis_filtr.php?'+str, true);
            xmlhttp.send();
        }

        function filtr() {
            var qry='';
            if (tel_cislo) {
                qry += 'tel_cislo=' + tel_cislo;
            }
            if (tel_cislo && silnice) {
                qry += '&silnice=' + silnice;
            }
            else if (silnice) {
                qry += 'silnice=' + silnice;
            }
            if ((tel_cislo && ssud) || (silnice && ssud)) {
                qry += '&ssud=' + ssud;
            }
            else if (ssud) {
                qry += 'ssud=' + ssud;
            }
            if ((tel_cislo && typ) || (silnice && typ) || (ssud && typ)) {
                qry += '&typ=' + typ;
            }
            else if (typ) {
                qry += 'typ=' + typ;
            }
            search(qry);
        }
    </script>
</head>

<body onLoad="telcislo('')">
    <?php
$app_up = PageHeader();

echo "<table width=\"100%\">
<tr>
<td width=\"10\">&nbsp;</td>
<td width=\"150\"><input id=\"telcislo\" onChange=\"telcislo(this.value)\" style=\"width:140px;\"></td>
<td width=\"100\">Silnice</td>
<td width=\"100\">Kilometr</td>
<td width=\"300\">Směr</td>
<td width=\"300\">Zeměpisná šířka</td>
<td width=\"300\">Zeměpisná délka</td>
<td width=\"200\">SSÚD</td>
<td width=\"100\">Typ</td>
<td>Status</td>
<td width=\"100\">&nbsp;</td>
<td width=\"10\">&nbsp;</td>
</tr>
</table>";

echo "<div id=\"data\">";
echo "</div>";

mysqli_close($link);
?>
</body>
</html>