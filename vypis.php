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
    function telcislo(str) {
        var xmlhttp;

        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        } else {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function() {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("data").innerHTML = xmlhttp.responseText;
            }
        }

        xmlhttp.open("GET","vypis_filtr.php?telcislo="+str, true);
        xmlhttp.send();
    }
    </script>
</head>

<body onLoad="telcislo('')">
    <?php
$app_up = PageHeader();

echo "<table width=\"100%\"><tr><td width=\"10\">&nbsp;</th><td width=\"150\"><input id=\"telcislo\" onChange=\"telcislo(this.value)\" style=\"width:140px;\"></td><td width=\"100\">Silnice</td><td width=\"100\">Kilometr</td><td width=\"300\">Směr</td><td>Zeměpisná šířka</td><td>Zeměpisná délka</td><td>SSÚD</td><td>Typ</td><td>Status</td><td></td></tr></table>";

echo "<div id=\"data\">";
echo "</div>";

mysqli_close($link);
?>
</body>
</html>