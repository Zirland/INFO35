<?php
date_default_timezone_set('Europe/Prague');
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Index databáze INFO35</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 500px; padding: 20px; }
    </style>
</head>
<body>
<?php
PageHeader();

$action   = @$_POST["action"];
$app_id   = $_POST["app_id"];
$app_name = $_POST["app_name"];
$app_url  = $_POST["app_url"];
$app_up   = $_POST["app_up"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($app_name))) {
        $app_name_err = "Zadejte prosím název aplikace";
    }
    if (empty(trim($app_url))) {
        $app_url_err = "Zadejte prosím URL aplikace";
    }

    if ($app_name_err == "" || $app_url_err == "") {
        $query44  = "INSERT INTO aplikace (nazev, url, up) VALUES ('$app_name','$app_url', '$app_up');";
        $prikaz44 = mysqli_query($link, $query44);
        header("location: preferences.php");
    }
}
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="hidden" name="action" value="add_app">
    <div class="form-group <?php echo (!empty($app_name_err)) ? 'has-error' : ''; ?>">
        <label>Název aplikace</label>
        <input type="text" name="app_name" class="form-control" value="<?php echo $app_name; ?>">
        <span class="help-block"><?php echo $app_name_err; ?></span>
    </div>
    <div class="form-group <?php echo (!empty($app_url_err)) ? 'has-error' : ''; ?>">
        <label>URL aplikace</label>
        <input type="text" name="app_url" class="form-control" value="<?php echo $app_url; ?>">
        <span class="help-block"><?php echo $app_url_err; ?></span>
    </div>
    <div class="form-group <?php echo (!empty($app_up_err)) ? 'has-error' : ''; ?>">
        <label for="app_up">Nadřazená aplikace:</label>
        <select class="form-control" id="app_up" name="app_up">
            <option value="">---</option>
<?php
$sql = "SELECT app_id,nazev FROM aplikace ORDER BY app_id";

if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $up_app_id, $up_app_name);

        while (mysqli_stmt_fetch($stmt)) {
            echo "<option value=\"$up_app_id\"";
            echo ">$up_app_name</option>\n";
        }
    }
}
mysqli_stmt_close($stmt);
?>
        </select>
        <span class="help-block"><?php echo $app_up_err; ?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Vložit">
    </div>
</form>

<?php
echo "<table width=\"50%\" align=\"center\">";
$query79 = "SELECT app_id, nazev, url, up FROM aplikace ORDER BY app_id;";
if ($result79 = mysqli_query($link, $query79)) {
    while ($row79 = mysqli_fetch_row($result79)) {
        $apl_id    = $row79[0];
        $apl_nazev = $row79[1];
        $apl_url   = $row79[2];
        $apl_up    = $row79[3];

        echo "<tr>";
        echo "<td>$apl_id</td>";
        echo "<td>$apl_nazev</td>";
        echo "<td>$apl_url</td>";
        echo "<td>$apl_up</td>";
        echo "<td><a href=\"edit_app.php?id=$apl_id\" target=\"_blank\">Editovat</a></td>";
        echo "</tr>";
    }
}
echo "</table>";
?>
<?php
mysqli_close($link);
?>