<?php
require_once "config.php";

$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["username"]))) {
        $username_err = "Zadejte prosím uživatelské jméno.";
    } else {
        $input = trim($_POST["username"]);
        $query13 = "SELECT id FROM users WHERE username = '$input';";
        if ($result13 = mysqli_query($link, $query13)) {
            switch (mysqli_num_rows($result13)) {
                case 1:
                    $username_err = "Uživatelské jméno je již obsazeno.";
                    break;
                default:
                    $username = trim($_POST["username"]);
                    break;
            }
        } else {
            echo "Něco se nepovedlo. Zkuste to prosím znovu.";
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Zadejte prosím heslo.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Heslo musí být dlouhé alespoň 6 znaků.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Zopakujte prosím heslo pro potvrzení.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Hesla se neshodují.";
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Zadejte prosím e-mailovou adresu.";
    } else {
        if (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $email_err = "Zadaná adresa není platná.";
        } else {
            $email = trim($_POST["email"]);
        }
    }

    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)) {
        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_DEFAULT);
        $param_email = $email;

        $query60 = "INSERT INTO users (username, password, email) VALUES ('$param_username', '$param_password', '$param_email')";
        if ($prikaz60 = mysqli_query($link, $query60)) {
            header("location: login.php");
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
    <title>Registrace</title>
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
        <h2>Registrace</h2>
        <p>Vytvořte si uživatelský účet vyplněním tohoto formuláře.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Uživatelské jméno</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block">
                    <?php echo $username_err; ?>
                </span>
            </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>E-mail</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block">
                    <?php echo $email_err; ?>
                </span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Heslo</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block">
                    <?php echo $password_err; ?>
                </span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Potvrzení hesla</label>
                <input type="password" name="confirm_password" class="form-control"
                    value="<?php echo $confirm_password; ?>">
                <span class="help-block">
                    <?php echo $confirm_password_err; ?>
                </span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Potvrdit">
                <input type="reset" class="btn btn-default" value="Storno">
            </div>
            <p>Jste již registrován? <a href="login.php">Přihlaste se zde</a>.</p>
        </form>
    </div>
</body>

</html>