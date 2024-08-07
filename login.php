<?php
date_default_timezone_set('Europe/Prague');
if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

require_once "config.php";

$username = $password = "";
$username_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Zadejte prosím uživatelské jméno.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Zadejte prosím heslo.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {
        $query31 = "SELECT id, username, `password` FROM users WHERE username = '$username';";
        if ($result31 = mysqli_query($link, $query31)) {
            while ($row31 = mysqli_fetch_row($result31)) {
                $id = $row31[0];
                $username = $row31[1];
                $hashed_password = $row31[2];

                switch (true) {
                    case (mysqli_num_rows($result31) == 1 && password_verify($password, $hashed_password)):
                        if (!isset($_SESSION)) {
                            session_start();
                        }

                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;

                        header("location: index.php");
                        break;

                    case mysqli_num_rows($result29) != 1:
                        $username_err = "Zadaný uživatel neexistuje.";
                        break;

                    default:
                        $password_err = "Zadané heslo není správné.";
                }
            }
        } else {
            echo "Něco se nepovedlo. Zkuste to prosím znovu.";
        }
    }

    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <title>Přihlášení</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 350px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Přihlášení</h2>
        <p>Přihlaste se vyplněním tohoto formuláře.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Uživatelské jméno</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block">
                    <?php echo $username_err; ?>
                </span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Heslo</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block">
                    <?php echo $password_err; ?>
                </span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Přihlásit">
            </div>
            <p>Nemáte účet? <a href="register.php">Zaregistrujte se</a>.</p>
        </form>
    </div>
</body>

</html>