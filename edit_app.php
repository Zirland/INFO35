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

$action = @$_POST["action"];
$app_id = $_GET["id"];
if ($app_id == "") {
    $app_id   = $_POST["app_id"];
    $app_name = $_POST["app_name"];
    $app_url  = $_POST["app_url"];
    $app_up   = $_POST["app_up"];
    $app_del  = $_POST["app_del"];
} else {
    $query42 = "SELECT nazev, url, up FROM aplikace WHERE app_id = '$app_id';";
    if ($result42 = mysqli_query($link, $query42)) {
        while ($row42 = mysqli_fetch_row($result42)) {
            $app_name = $row42[0];
            $app_url  = $row42[1];
            $app_up   = $row42[2];
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($app_name))) {
        $app_name_err = "Zadejte prosím název aplikace";
    }
    if (empty(trim($app_url))) {
        $app_url_err = "Zadejte prosím URL aplikace";
    }
    $query60 = "SELECT app_id FROM aplikace WHERE up = '$app_id';";
    if ($result60 = mysqli_query($link, $query60)) {
        $vazby = mysqli_num_rows($result60);
    }
    if ($app_del == "1" && $vazby > 0) {
        $app_del_err = "Aplikaci nelze smazat, protože na ni jsou závislé jiné aplikace.";
        echo "$app_del_err<br/>";
    }

    if ($app_name_err == "" && $app_url_err == "" && $app_del != "1") {
        $query66  = "UPDATE aplikace SET nazev = '$app_name', `url` = '$app_url', up = '$app_up' WHERE app_id = '$app_id';";
        $prikaz66 = mysqli_query($link, $query66);
    } elseif ($app_del == "1" && $app_del_err == "") {
        $query70  = "DELETE FROM aplikace WHERE app_id = '$app_id';";
        $prikaz70 = mysqli_query($link, $query70);
        $query72  = "DELETE FROM opravneni WHERE app_id = '$app_id';";
        $prikaz72 = mysqli_query($link, $query72);
    }
}
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="hidden" name="action" value="edit_app">
    <input type="hidden" name="app_id" value="<?php echo $app_id; ?>">
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
            if ($up_app_id == $app_up) {
                echo " SELECTED";
            }
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
        <label>Smazat</label>
        <input type="checkbox" name="app_del" value="1">
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Uložit">
    </div>
</form>

<?php
mysqli_close($link);
?>