<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

require_once "config.php";

$username     = $password     = "";
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
        $query29 = "SELECT id, username, password FROM users WHERE username = '$username';";
        if ($result29 = mysqli_query($link, $query29)) {
            while ($row29 = mysqli_fetch_row($result29)) {
                $id              = $row29[0];
                $username        = $row29[1];
                $hashed_password = $row29[2];

                if (mysqli_num_rows($result29) == 1) {
                    if (password_verify($password, $hashed_password)) {
                        session_start();

                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"]       = $id;
                        $_SESSION["username"] = $username;

                        header("location: index.php");
                    } else {
                        $password_err = "Zadané heslo není správné.";
                    }
                } else {
                    $username_err = "Zadaný uživatel neexistuje.";
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
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
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
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Heslo</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Přihlásit">
            </div>
            <p>Nemáte účet? <a href="register.php">Zaregistrujte se</a>.</p>
        </form>
    </div>
</body>
</html>