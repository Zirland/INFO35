<?php
date_default_timezone_set('Europe/Prague');
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'config.php';

$id = @$_GET["id"];
if ($id == "") {
    $id = @$_POST["id"];
}

$query17 = "SELECT jmeno, tel_cislo FROM test_osoby WHERE id = '$id';";
if ($result17 = mysqli_query($link, $query17)) {
    while ($row17 = mysqli_fetch_row($result17)) {
        $old_jmeno     = $row17[0];
        $old_tel_cislo = $row17[1];
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id            = @$_POST["id"];
    $jmeno         = @$_POST["jmeno"];
    $jmeno_err     = "";
    $tel_cislo     = @$_POST["tel_cislo"];
    $tel_cislo_err = "";

    if (empty(trim($jmeno))) {
        $jmeno_err = "Zadejte prosím jméno.";
    }
    if (empty(trim($tel_cislo))) {
        $tel_cislo_err = "Zadejte prosím telefonní číslo.";
    }

    if (empty($jmeno_err) && empty($tel_cislo_err)) {
        $query79  = "UPDATE test_osoby SET jmeno = '$jmeno', tel_cislo = '$tel_cislo' WHERE id = $id;";
        $result79 = mysqli_query($link, $query79);
        Redir("index.php");
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Testování - edit osoby</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 500px; padding: 20px; }
    </style>
</head>
<body>
    <?php
PageHeader();
?>
    <div class="wrapper">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group <?php echo (!empty($jmeno_err)) ? 'has-error' : ''; ?>">
                <label>Příjmení a jméno koordinátora testování</label>
                <input type="text" name="jmeno" class="form-control" value="<?php echo $old_jmeno; ?>" autofocus>
                <span class="help-block"><?php echo $jmeno_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($tel_cislo_err)) ? 'has-error' : ''; ?>">
                <label>Telefonní číslo</label>
                <input type="text" name="tel_cislo" class="form-control" value="<?php echo $old_tel_cislo; ?>" autofocus>
                <span class="help-block"><?php echo $tel_cislo_err; ?></span>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Vložit">
            </div>
        </form>
    </div>
    <hr>
    <?php
echo "<table width=\"50%\">";
echo "<tr><th>&nbsp;</th><th>Jméno</th><th>Telefonní číslo</th><th></th></tr>";
$i = 0;

$query81 = "SELECT id, jmeno, tel_cislo FROM test_osoby ORDER BY jmeno;";
if ($result81 = mysqli_query($link, $query81)) {
    while ($row81 = mysqli_fetch_row($result81)) {
        $sel_id        = $row81[0];
        $sel_jmeno     = $row81[1];
        $sel_tel_cislo = $row81[2];

        echo "<tr style=\"";
        if ($i % 2 == 0) {
            echo "background-color:#ddd;";
        } else {
            echo "background-color:#fff;";
        }
        echo "\"><td>&nbsp;</td><td>$sel_jmeno</td><td>$sel_tel_cislo</td>";
        echo "<td><a href=\"test_osoba_edit.php?id=$sel_id\">Edit</a></td></tr>";
        $i = $i + 1;

    }
}

echo "</table>";

mysqli_close($link);
?>