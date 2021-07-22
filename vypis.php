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
		var tel_cislo = '';
		var sil_nice = '';
		var ss_ud = '';
		var t_yp = '';

		function telcislo(num) {
			tel_cislo=num;
			filtr();
		}

		function silnice(sil) {
			sil_nice=sil;
			filtr();
		}

		function ssud(sud) {
			ss_ud=sud;
			filtr();
		}

		function typ(tp) {
			t_yp=tp;
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

			xmlhttp.open("GET",'vypis_filtr.php?'+str, true);
			xmlhttp.send();
		}

		function filtr() {
			var qry='';
			if (tel_cislo != '') {
				qry += 'tel_cislo=' + tel_cislo;
			}
			if (tel_cislo != '' && sil_nice != '') {
				qry += '&silnice=' + sil_nice;
			}
			else if (sil_nice != '') {
				qry += 'silnice=' + sil_nice;
			}
			if ((tel_cislo != '' && ss_ud != '') || (sil_nice != '' && ss_ud != '')) {
				qry += '&ssud=' + ss_ud;
			}
			else if (ss_ud != '') {
				qry += 'ssud=' + ss_ud;
			}
			if ((tel_cislo != '' && t_yp != '') || (sil_nice != '' && t_yp != '') || (ssud != '' && t_yp != '')) {
				qry += '&typ=' + t_yp;
			}
			else if (t_yp != '') {
				qry += 'typ=' + t_yp;
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
<td width=\"100\"><select id=\"silnice\" name=\"silnice\" onChange=\"silnice(this.value)\" style=\"width:90px;\"><option value=\"\">---</option>";

$sql = "SELECT id,nazev FROM enum_silnice ORDER BY nazev";

if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $sil_id, $sil_name);

        while (mysqli_stmt_fetch($stmt)) {
            echo "<option value=\"$sil_id\"";
            if ($sil_id == $silnice) {
                echo " SELECTED";
            }
            echo ">$sil_name</option>\n";
        }
    }
}
mysqli_stmt_close($stmt);

echo "</select></td>
<td width=\"100\"></td>
<td width=\"300\"></td>
<td width=\"300\"></td>
<td width=\"300\"></td>
<td width=\"200\"><select id=\"ssud\" name=\"ssud\" onChange=\"ssud(this.value)\" style=\"width:190px;\"><option value=\"\">---</option>";

$sql = "SELECT id,popis FROM enum_ssud ORDER BY popis";

if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $ssud_id, $ssud_name);

        while (mysqli_stmt_fetch($stmt)) {
            echo "<option value=\"$ssud_id\"";
            if ($ssud_id == $ssud) {
                echo " SELECTED";
            }
            echo ">$ssud_name</option>\n";
        }
    }
}
mysqli_stmt_close($stmt);

echo "</select></td>
<td width=\"100\"><select id=\"typ\" name=\"typ\" onChange=\"typ(this.value)\" style=\"width:90px;\"><option value=\"\">---</option>";

$sql = "SELECT id,popis FROM enum_typ ORDER BY popis";

if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $typ_id, $typ_name);

        while (mysqli_stmt_fetch($stmt)) {
            echo "<option value=\"$typ_id\"";
            if ($typ_id == $typ) {
                echo " SELECTED";
            }
            echo ">$typ_name</option>\n";
        }
    }
}
mysqli_stmt_close($stmt);

echo "</select></td>
<td></td>
<td width=\"100\"></td>
<td width=\"10\"></td>
</tr>
</table>";

echo "<div id=\"data\">";
echo "</div>";

mysqli_close($link);
?>
</body>
</html>