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

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <title>Index databáze INFO35</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 500px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <?php
    PageHeader();

    $action = @$_POST["action"];
    $app_id = $_GET["id"];
    switch ($app_id) {
        case "":
            $app_id = $_POST["app_id"];
            $app_name = $_POST["app_name"];
            $app_url = $_POST["app_url"];
            $app_up = $_POST["app_up"];
            $app_del = $_POST["app_del"];
            break;
        default:
            $query49 = "SELECT nazev, url, up FROM aplikace WHERE app_id = '$app_id';";
            if ($result49 = mysqli_query($link, $query49)) {
                while ($row49 = mysqli_fetch_row($result49)) {
                    $app_name = $row49[0];
                    $app_url = $row49[1];
                    $app_up = $row49[2];
                }
            }
            break;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty(trim($app_name))) {
            $app_name_err = "Zadejte prosím název aplikace";
        }
        if (empty(trim($app_url))) {
            $app_url_err = "Zadejte prosím URL aplikace";
        }
        $query67 = "SELECT app_id FROM aplikace WHERE up = '$app_id';";
        if ($result67 = mysqli_query($link, $query67)) {
            $vazby = mysqli_num_rows($result67);
        }
        if ($app_del == "1" && $vazby > 0) {
            $app_del_err = "Aplikaci nelze smazat, protože na ni jsou závislé jiné aplikace.";
            echo "$app_del_err<br/>";
        }

        if ($app_name_err == "" && $app_url_err == "" && $app_del != "1") {
            $query77 = "UPDATE aplikace SET nazev = '$app_name', `url` = '$app_url', up = '$app_up' WHERE app_id = '$app_id';";
            $prikaz77 = mysqli_query($link, $query77);
        } elseif ($app_del == "1" && $app_del_err == "") {
            $query80 = "DELETE FROM aplikace WHERE app_id = '$app_id';";
            $prikaz80 = mysqli_query($link, $query80);
            $query82 = "DELETE FROM opravneni WHERE app_id = '$app_id';";
            $prikaz82 = mysqli_query($link, $query82);
        }
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="action" value="edit_app">
        <input type="hidden" name="app_id" value="<?php echo $app_id; ?>">
        <div class="form-group <?php echo (!empty($app_name_err)) ? 'has-error' : ''; ?>">
            <label>Název aplikace</label>
            <input type="text" name="app_name" class="form-control" value="<?php echo $app_name; ?>">
            <span class="help-block">
                <?php echo $app_name_err; ?>
            </span>
        </div>
        <div class="form-group <?php echo (!empty($app_url_err)) ? 'has-error' : ''; ?>">
            <label>URL aplikace</label>
            <input type="text" name="app_url" class="form-control" value="<?php echo $app_url; ?>">
            <span class="help-block">
                <?php echo $app_url_err; ?>
            </span>
        </div>
        <div class="form-group <?php echo (!empty($app_up_err)) ? 'has-error' : ''; ?>">
            <label for="app_up">Nadřazená aplikace:</label>
            <select class="form-control" id="app_up" name="app_up">
                <option value="">---</option>
                <?php
                $query110 = "SELECT app_id, nazev FROM aplikace ORDER BY app_id;";
                if ($result110 = mysqli_query($link, $query110)) {
                    while ($row110 = mysqli_fetch_row($result110)) {
                        $up_app_id = $row110[0];
                        $up_app_name = $row110[1];

                        echo "<option value=\"$up_app_id\"";
                        if ($up_app_id == $app_up) {
                            echo " SELECTED";
                        }
                        echo ">$up_app_name</option>\n";
                    }
                }
                ?>
            </select>
            <span class="help-block">
                <?php echo $app_up_err; ?>
            </span>
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