<?php
// Load configuration from .env.local (never committed to git)
// SECURITY: .env.local is REQUIRED and should contain: DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME
if (!file_exists(__DIR__ . '/.env.local')) {
    die("FATAL ERROR: .env.local not found. Please create .env.local with database credentials.");
}

$env_lines = file(__DIR__ . '/.env.local', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($env_lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (strpos($line, '=') === false) continue;
    list($key, $value) = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}

// Fail fast if required credentials are missing
$DB_SERVER = $_ENV['DB_SERVER'] ?? null;
$DB_USERNAME = $_ENV['DB_USERNAME'] ?? null;
$DB_PASSWORD = $_ENV['DB_PASSWORD'] ?? null;
$DB_NAME = $_ENV['DB_NAME'] ?? null;

if (!$DB_SERVER || !$DB_USERNAME || !$DB_NAME) {
    die("FATAL ERROR: Missing required credentials in .env.local. Required: DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME");
}

function SmerNazev($silnice, $smer, $kilometr)
{
    switch ($silnice) {
        case 'D0':
            if ($smer == "+" && $kilometr < 30) {
                $smer_nazev = "letiště";
            } elseif ($smer == "+" && $kilometr < 65) {
                $smer_nazev = "Štěrboholy";
            } elseif ($smer == "+") {
                $smer_nazev = "letiště";
            } elseif ($smer == "-" && $kilometr > 65) {
                $smer_nazev = "Brno";
            } elseif ($smer == "-" && $kilometr > 30) {
                $smer_nazev = "Liberec";
            } else {
                $smer_nazev = "Brno";
            }
            break;

        case 'D1':
            if ($smer == "+" && $kilometr < 189) {
                $smer_nazev = "Brno";
            } elseif ($smer == "+" && $kilometr < 273) {
                $smer_nazev = "Přerov";
            } elseif ($smer == "+") {
                $smer_nazev = "Bohumín";
            } elseif ($smer == "-" && $kilometr > 283) {
                $smer_nazev = "Přerov";
            } elseif ($smer == "-" && $kilometr > 203) {
                $smer_nazev = "Brno";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case 'D2':
            $smer_nazev = ($smer == "+") ? "Lanžhot" : "Brno";
            break;

        case 'D3':
            $smer_nazev = ($smer == "+") ? "Kaplice" : "Praha";
            break;

        case 'D4':
            $smer_nazev = ($smer == "+") ? "Písek" : "Praha";
            break;

        case 'D5':
            $smer_nazev = ($smer == "+") ? "Rozvadov" : "Praha";
            break;

        case 'D6':
            if ($smer == "+" && $kilometr < 112) {
                $smer_nazev = "Karlovy Vary";
            } elseif ($smer == "+") {
                $smer_nazev = "Cheb";
            } elseif ($smer == "-" && $kilometr > 112) {
                $smer_nazev = "Karlovy Vary";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case 'D7':
            $smer_nazev = ($smer == "+") ? "Chomutov" : "Praha";
            break;

        case 'D8':
            $smer_nazev = ($smer == "+") ? "Petrovice" : "Praha";
            break;

        case 'D11':
            if ($smer == "+" && $kilometr < 90) {
                $smer_nazev = "Hradec Králové";
            } elseif ($smer == "+") {
                $smer_nazev = "Trutnov";
            } elseif ($smer == "-" && $kilometr > 96) {
                $smer_nazev = "Hradec Králové";
            } else {
                $smer_nazev = "Praha";
            }
            break;

        case '20':
            $smer_nazev = ($smer == "+") ? "České Budějovice" : "Plzeň";
            break;

        case 'D35':
            if ($smer == "+" && $kilometr < 125) {
                $smer_nazev = "Hradec Králové";
            } elseif ($smer == "+" && $kilometr < 160) {
                $smer_nazev = "Zámrsk";
            } elseif ($smer == "+" && $kilometr < 180) {
                $smer_nazev = "Litomyšl";
            } elseif ($smer == "+" && $kilometr < 220) {
                $smer_nazev = "Moravská Třebová";
            } elseif ($smer == "+") {
                $smer_nazev = "Lipník nad Bečvou";
            } elseif ($smer == "-" && $kilometr > 220) {
                $smer_nazev = "Mohelnice";
            } elseif ($smer == "-" && $kilometr > 180) {
                $smer_nazev = "Litomyšl";
            } elseif ($smer == "-" && $kilometr > 160) {
                $smer_nazev = "Zámrsk";
            } elseif ($smer == "-" && $kilometr > 125) {
                $smer_nazev = "Praha";
            } else {
                $smer_nazev = "Liberec";
            }
            break;

        case '35':
            if ($smer == "+" && $kilometr < 210) {
                $smer_nazev = "Mohelnice";
            } elseif ($smer == "+") {
                $smer_nazev = "Valašské Meziříčí";
            } elseif ($smer == "-" && $kilometr > 285) {
                $smer_nazev = "Hranice";
            } else {
                $smer_nazev = "Vysoké Mýto";
            }
            break;

        case '38':
            $smer_nazev = ($smer == "+") ? "Znojmo" : "Havlíčkův Brod";
            break;

        case 'D46':
            $smer_nazev = ($smer == "+") ? "Olomouc" : "Vyškov";
            break;

        case 'D48':
            $smer_nazev = ($smer == "+") ? "Český Těšín" : "Bělotín";
            break;

        case 'D49':
            $smer_nazev = ($smer == "+") ? "Fryšták" : "Hulín";
            break;

        case 'D52':
            $smer_nazev = ($smer == "+") ? "Mikulov" : "Brno";
            break;

        case 'D55':
            if ($smer == "+" && $kilometr < 14) {
                $smer_nazev = "Přerov";
            } elseif ($smer == "+") {
                $smer_nazev = "Břeclav";
            } elseif ($smer == "-" && $kilometr > 14) {
                $smer_nazev = "Přerov";
            } else {
                $smer_nazev = "Olomouc";
            }
            break;

        case 'D56':
            $smer_nazev = ($smer == "+") ? "Frýdek-Místek" : "Ostrava";
            break;

        case '57':
            $smer_nazev = ($smer == "+") ? "Vsetín" : "Valašské Meziříčí";
            break;

        case '58':
            $smer_nazev = ($smer == "+") ? "Ostrava" : "Rožnov pod Radhošťem";
            break;

        default:
            $smer_nazev = $smer;
            break;
    }

    return $smer_nazev;
}

// Mail credentials (must be in .env.local)
$mail_host = $_ENV['MAIL_HOST'] ?? null;
$mail_username = $_ENV['MAIL_USERNAME'] ?? null;
$mail_password = $_ENV['MAIL_PASSWORD'] ?? null;
$mail_bcc = $_ENV['MAIL_BCC'] ?? null;

if (!$mail_host || !$mail_username) {
    die("FATAL ERROR: Missing mail credentials in .env.local. Required: MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD, MAIL_BCC");
}
